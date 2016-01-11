<?php
namespace Nodes\Support;

use Nodes\Exceptions\InstallPackageException;

/**
 * Class InstallPackage
 *
 * @package Nodes\Support
 */
class InstallPackage
{
    /**
     * Vendor name
     *
     * @var string
     */
    protected $vendor = 'nodes';

    /**
     * Package name
     *
     * @var string
     */
    protected $packageName;

    /**
     * Composer's class map
     *
     * @var array
     */
    protected $composerClassMap;

    /**
     * Service provider class name
     *
     * @var string
     */
    protected $serviceProvider;

    /**
     * InstallPackage constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     */
    public function __construct()
    {
        // Composers class map
        $this->composerClassMap = require(base_path('vendor/composer/autoload_classmap.php'));
    }

    /**
     * Add Nodes Service provider if missing
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function addNodesServiceProvider()
    {
        // Load Laravel's "app config" into an array
        $config = file(config_path('app.php'));

        // Locate "Nodes Core Service Provider" position in providers array
        $locateNodesCoreProviderPosition = array_keys(preg_grep('|Nodes\\\\ServiceProvider::class|', $config));
        if (!empty($locateNodesCoreProviderPosition[0])) {
            return true;
        }

        $locateNodesCoreProviderArrayPosition = array_keys(preg_grep("|'providers' =>|", $config));
        for($i = $locateNodesCoreProviderArrayPosition[0]; $i < count($config); $i++) {
            // Trim current config line
            $value = trim($config[$i]);

            // If line is not the end of the "providers" array
            // continue onwards to next line
            if ($value != '],') {
                continue;
            }

            // Generate service provider content
            $serviceProviderSnippet  = '' . "\n";
            $serviceProviderSnippet .= str_repeat("\t", 2) . '/**' . "\n";
            $serviceProviderSnippet .= str_repeat("\t", 2) . ' * Nodes Service Providers' . "\n";
            $serviceProviderSnippet .= str_repeat("\t", 2) . ' */' . "\n";
            $serviceProviderSnippet .= str_repeat("\t", 2) . 'Nodes\ServiceProvider::class,' . "\n";

            // Add service provider snippet to config array
            array_splice($config, $i, 0, $serviceProviderSnippet);
            break;
        }

        // Update service provider config
        file_put_contents(config_path('app.php'), implode('', $config));

        return true;
    }

    /**
     * Install package's service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string  $serviceProviderFilename
     * @return string  Returns namespace of service provider
     * @throws \Nodes\Exceptions\InstallPackageException
     */
    public function installServiceProvider($serviceProviderFilename = 'ServiceProvider.php')
    {
        // Validate required package information
        if (empty($this->vendor) || empty($this->packageName)) {
            throw new InstallPackageException(sprintf('Vendor [%s] or Package Name [%s] is not set.', $this->vendor, $this->packageName), 400);
        }

        // Generate path to package
        // $vendorPath = base_path(sprintf('vendor/%s/%s', $name[0], $name[1]));
        $vendorPath = base_path(sprintf('%s/%s', $this->vendor, $this->packageName));

        // Check if package exists in the vendor folder
        if (!file_exists($vendorPath)) {
            throw new InstallPackageException(sprintf('[%s] was not be found in the vendor folder [%s].', $this->packageName, base_path('vendor/')), 400);
        }

        // Generate path to service provider file
        $serviceProviderFilenamePath = sprintf('%s/src/%s', $vendorPath, $serviceProviderFilename);
        if (!file_exists($serviceProviderFilenamePath)) {
            throw new InstallPackageException(sprintf('[%s] was not be found in the package folder [%s].', $serviceProviderFilename, $vendorPath), 400);
        }

        // Look for service provider file in Composers class map
        //
        // If not found, it means the package hasn't been properly
        // registered with Composers autoloader
        $this->serviceProvider = $serviceProvider = array_search($serviceProviderFilenamePath, $this->composerClassMap);
        if (!$serviceProvider) {
            throw new InstallPackageException(sprintf('Service Provider [%s] for package [%s] was not found in Composers class map.', $serviceProvider, sprintf('%s/%s', $this->vendor, $this->packageName)), 400);
        }

        // Make sure to load service provider
        // if it for some reason isn't already
        if (!class_exists($serviceProvider)) {
            require_once($serviceProviderFilename);
        }

        // Load Laravel's "app config" into an array
        $config = file(config_path('app.php'));

        // Locate "Nodes Core Service Provider" position in providers array
        $locateNodesCoreProviderPosition = array_keys(preg_grep('|Nodes\\\\ServiceProvider::class|', $config))[0];

        for($i = $locateNodesCoreProviderPosition+1; $i < count($config); $i++) {
            // Get value of next item in providers array
            $value = trim($config[$i]);

            // Check if we should insert the service provider
            // at the current position. Else skip to next item.
            if (!$this->shouldInsertHere($value)) {
                continue;
            }

            // Insert service provider at current position
            array_splice($config, $i, 0, [
                str_repeat("\t", 2) . sprintf('%s::class', $serviceProvider)
            ]);
            break;
        }

        // Update existing config
        file_put_contents(config_path('app.php'), implode('', $config));

        return $serviceProvider;
    }

    /**
     * Check if we should insert item at current position
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $value
     * @return boolean
     */
    protected function shouldInsertHere($value)
    {
        // If value is either:
        // Empty = We're on an empty line in the providers array
        // "]," = We're at the last item of the providers array)
        //
        // Then we'll always want to insert item here.
        if (empty($value) || $value == '],') {
            return true;
        }

        // Current item namespace
        $itemNamespace = explode('\\', $value);

        // Service Provider namespace
        $serviceProviderNamespace = explode('\\', sprintf('%s::class', $this->serviceProvider));

        // Determine if current item's namespace, comes before or after
        // the service providers namespace, if sorted alphabetically
        foreach ($itemNamespace as $key => $namespacePart) {
            // Compare current namespace parts
            $comparison = strnatcmp($namespacePart, $serviceProviderNamespace[$key]);

            // Namespace parts are identical,
            // move on to the next part.
            if ($comparison == 0) {
                continue;
            }

            // Difference found
            return $comparison > 0 ? true : false;
        }

        return false;
    }

    /**
     * Set vendor name of package to install
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $vendorName
     * @return $this
     */
    public function setVendorName($vendorName)
    {
        $this->vendor = $vendorName;
        return $this;
    }

    /**
     * Set package name to install
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $packageName
     * @return $this
     */
    public function setPackageName($packageName)
    {
        $this->packageName = $packageName;
        return $this;
    }
}