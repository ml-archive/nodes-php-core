<?php
namespace Nodes\Support;

use Illuminate\Console\Command as IlluminateConsoleCommand;
use Nodes\Exceptions\InstallPackageException;

/**
 * Class InstallPackage
 *
 * @package Nodes\Support
 */
class InstallPackage
{
    /**
     * Base path of application
     *
     * @var string
     */
    protected $basePath;

    /**
     * Vendor name
     *
     * @var string
     */
    protected $vendor;

    /**
     * Package name
     *
     * @var string
     */
    protected $packageName;

    /**
     * Package namespace
     *
     * @var string
     */
    protected $packageNamespace;

    /**
     * Path to package
     *
     * @var string
     */
    protected $packagePath;

    /**
     * Service provider class name
     *
     * @var string
     */
    protected $serviceProvider;

    /**
     * Path of service provider file
     *
     * @var string
     */
    protected $serviceProviderFilePath;

    /**
     * Composer's PSR-4 array
     *
     * @var array
     */
    protected $composerPsr4;

    /**
     * Application config
     *
     * @var array
     */
    protected $config = [];

    /**
     * Installer instance
     *
     * @var \Illuminate\Console\Command
     */
    protected $installer;

    /**
     * Prepare package by locating package path
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return $this
     * @throws \Nodes\Exceptions\InstallPackageException
     */
    protected function preparePackage()
    {
        // Validate required package information
        if (empty($this->getVendorName()) || empty($this->getPackageName())) {
            throw new InstallPackageException(sprintf('Vendor [%s] or Package Name [%s] is not set.', $this->getVendorName(), $this->getPackageName()), 400);
        }

        // Generate path to package
        $this->packagePath = $packagePath = $this->getVendorPath($this->getVendorPackageName());

        // Check if package exists in the vendor folder
        if (!file_exists($packagePath)) {
            throw new InstallPackageException(sprintf('[%s] was not be found in the vendor folder [%s].', $this->getPackageName(), $this->getVendorPath()), 400);
        }

        return $this;
    }

    /**
     * Prepare package to work on/with the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return $this
     * @throws \Nodes\Exceptions\InstallPackageException
     */
    protected function prepareServiceProvider()
    {
        // Generate path to service provider file
        $this->serviceProviderFilePath = $serviceProviderFilePath = $this->getPackageServiceProviderFilePath();
        if (!file_exists($serviceProviderFilePath)) {
            throw new InstallPackageException(sprintf('[ServiceProvider.php] was not be found in the package folder [%s].', $this->getPackagePath()), 500);
        }

        // Namespace of package
        $this->packageNamespace = $packageNamespace = $this->locatePackageNamespaceFromComposer();
        if (empty($packageNamespace)) {
            throw new InstallPackageException(sprintf('Could not locate namespace of package [%s] in Composers list of PSR-4 registed packages. Run "composer dump-autoload" and try again.', $this->getVendorPackageName()), 500);
        }

        // Set namespace of package's service provider
        $this->serviceProvider = sprintf('%sServiceProvider', $this->packageNamespace);

        return $this;
    }

    /**
     * Check if package is installed
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     * @throws \Nodes\Exceptions\InstallPackageException
     */
    public function isPackageInstalled()
    {
        // Prepare package and service provider
        $this->preparePackage()->prepareServiceProvider();

        // Look for package service provider
        $checkIfServiceProviderIsAlreadyInstalled = $this->searchApplicationConfig(sprintf('%s::class', $this->escapeNamespace($this->serviceProvider)));

        // If service provider is found,
        // we can conclude the package is already installed
        return !empty($checkIfServiceProviderIsAlreadyInstalled[0]) ? true : false;
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
        // Locate "Nodes Core Service Provider" position in providers array
        $locateNodesCoreProviderPosition = $this->searchApplicationConfig(sprintf('%s::class', $this->escapeNamespace($this->getCoreServiceProviderNamespace())));
        if (!empty($locateNodesCoreProviderPosition[0])) {
            return true;
        }

        $locateNodesCoreProviderArrayPosition = $this->searchApplicationConfig('\'providers\' =>');
        for($i = $locateNodesCoreProviderArrayPosition[0]; $i < count($this->getApplicationConfig()); $i++) {
            // Trim current config line
            $value = trim($this->getApplicationConfig()[$i]);

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
            $this->addToApplicationConfig($i, $serviceProviderSnippet);
            break;
        }

        // Update application config
        $this->updateApplicationConfig();

        return true;
    }

    /**
     * Install package's service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string|boolean  Returns true if service provider is already installed
     * @throws \Nodes\Exceptions\InstallPackageException
     */
    public function installServiceProvider()
    {
        // Check if service provider is already installed
        if ($this->isPackageInstalled()) {
            return true;
        }

        // Make sure to load service provider
        // if it for some reason isn't already
        if (!class_exists($this->serviceProvider)) {
            require_once($this->getPackageServiceProviderFilePath());
        }

        // Make sure package service provider isn't already installed
        $checkIfServiceProviderIsAlreadyInstalled = $this->searchApplicationConfig(sprintf('%s::class', $this->escapeNamespace($this->serviceProvider)));
        if (!empty($checkIfServiceProviderIsAlreadyInstalled[0])) {
            return true;
        }

        // Locate position of Nodes (Core) service provider in config file
        $locateNodesCoreProviderPosition = $this->searchApplicationConfig(sprintf('%s::class', $this->escapeNamespace($this->getCoreServiceProviderNamespace())));
        if (empty($locateNodesCoreProviderPosition[0])) {
            // Nodes Core service provider is missing from config file.
            // We'll start by adding that and then try again.
            $this->addNodesServiceProvider();

            // Lets' try and locate the position again.
            $locateNodesCoreProviderPosition = $this->searchApplicationConfig(sprintf('%s::class', $this->escapeNamespace($this->getCoreServiceProviderNamespace())));
        }

        // Service Provider namespace
        $serviceProviderNamespace = explode('\\', sprintf('%s::class', $this->serviceProvider));

        // Add package service provider to Laravel's app config
        for($i = $locateNodesCoreProviderPosition[0]+1; $i < count($this->getApplicationConfig()); $i++) {
            // Get value of next item in providers array
            $value = trim($this->getApplicationConfig()[$i]);

            // If we're on a line where there's already a service provider,
            // we'll take the namespace and match it up against our own.
            //
            // If our service provider doesn't fit here, we'll move on to next line.
            if (!empty($value) && $value != '],') {
                // Current item namespace
                $currentNamespace = explode('\\', $value);

                // Comparison state
                $shouldBeInsertedHere = false;

                // Determine if current item's namespace, comes before or after
                // our own service providers namespace, if sorted alphabetically
                foreach ($currentNamespace as $key => $namespacePart) {
                    // Compare current namespace parts
                    $comparison = strnatcmp($namespacePart, $serviceProviderNamespace[$key]);

                    // Namespace parts are identical
                    // move on to next part
                    if ($comparison == 0) {
                        continue;
                    }

                    // Difference found
                    $shouldBeInsertedHere = $comparison > 0 ? true : false;
                    break;
                }

                // After comparing we can conclude our service provider
                // should NOT be inserted at current line. And we're
                // therefore moving on to next line.
                if (!$shouldBeInsertedHere) {
                    continue;
                }
            }

            // Success!
            // Insert service provider at current line
            $this->addToApplicationConfig($i, [
                str_repeat(' ', 8) . sprintf('%s::class,', $this->serviceProvider) . "\n"
            ]);
            break;
        }

        // Update existing config
        $this->updateApplicationConfig();

        return $this->serviceProvider;
    }

    /**
     * Install facades belonging to package
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $facades
     * @return boolean|null
     */
    public function installFacades($facades)
    {
        // Validate received data
        if (empty($facades) || !is_array($facades)) {
            return false;
        }

        // Locate beginning of $aliases array
        $locateAliasesArray = $this->searchApplicationConfig('\'aliases\' => \[');
        if (empty($locateAliasesArray[0])) {
            return null;
        }

        foreach ($facades as $facadeName => $facadeNamespace) {
            // If facade is already installed,
            // we'll just skip it and move along.
            $checkIfFacadesIsAlreadyInstalled = $this->searchApplicationConfig(sprintf('%s::class', $this->escapeNamespace($facadeNamespace)));
            if (!empty($checkIfFacadesIsAlreadyInstalled[0])) {
                continue;
            }

            for ($i = $locateAliasesArray[0]+1; $i < count($this->getApplicationConfig()); $i++) {
                // Get value of next line
                $value = trim($this->getApplicationConfig()[$i]);

                // If we're on an empty line, we'll continue to next one
                //
                // If we're on a line where there's already a facade,
                // we'll take the facade and match it up against our
                // current one. If our facade doesn't fit here,
                // we'll continue on to next line.
                if (empty($value)) {
                    continue;
                } elseif (!empty($value) && $value != '],') {
                    // Retrieve current facade name from line
                    $currentFacadeName = trim(explode('=>', $value)[0]);

                    // Remove single quotes wrapping facade name
                    $currentFacadeName = substr($currentFacadeName, 1, strlen($currentFacadeName)-1);

                    // Compare the two facades names.
                    // If current facade name comes before our own facade name
                    // we'll move on to next line
                    if (strnatcmp($currentFacadeName, $facadeName) != 1) {
                        continue;
                    }
                }

                // Success!
                // We're inserting our facade at the current line.
                $this->addToApplicationConfig($i, [
                    str_repeat(' ', 8) . sprintf('\'%s\' => %s::class,', $facadeName, $facadeNamespace) . "\n"
                ]);
                break;
            }
        }

        // Update existing config
        $this->updateApplicationConfig();

        return true;
    }

    /**
     * Escape namespace
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $namespace
     * @return string
     */
    protected function escapeNamespace($namespace)
    {
        return str_replace('\\', '\\\\', $namespace);
    }

    /**
     * Retrieve application config
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getApplicationConfig()
    {
        if (!empty($this->config)) {
            return $this->config;
        }

        return $this->config = file($this->getConfigPath('app.php'));
    }

    /**
     * Add to application config
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $line
     * @param  string $content
     * @return array
     */
    public function addToApplicationConfig($line, $content)
    {
        return array_splice($this->config, $line, 0, $content);
    }

    /**
     * Write to application config
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function updateApplicationConfig()
    {
        return (bool) file_put_contents($this->getConfigPath('app.php'), implode('', $this->getApplicationConfig()));
    }

    /**
     * Search application config
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $pattern   Regular expression pattern
     * @param  string $modifiers Regular expression modifiers
     * @return array
     */
    public function searchApplicationConfig($pattern, $modifiers = '')
    {
        return array_keys(preg_grep(sprintf('|%s|%s', $pattern, $modifiers), $this->getApplicationConfig()));
    }

    /**
     * Set base path
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        return $this;
    }

    /**
     * Retrieve base path of application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $append
     * @return string
     */
    public function getBasePath($append = null)
    {
        if (empty($this->basePath)) {
            $this->basePath = dirname(__FILE__, 6) . '/';
        }

        return !empty($append) ? $this->basePath . $append : $this->basePath;
    }

    /**
     * Retrieve config path of application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $append
     * @return string
     */
    public function getConfigPath($append = null)
    {
        $configPath = $this->getBasePath('config/');
        return !empty($append) ? $configPath . $append : $configPath;
    }

    /**
     * Retrieve vendor path of application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $append
     * @return string
     */
    public function getVendorPath($append = null)
    {
        $vendorPath = $this->getBasePath('vendor/');
        return !empty($append) ? $vendorPath . $append : $vendorPath;
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
     * Retrieve vendor name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getVendorName()
    {
        return $this->vendor;
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

    /**
     * Retrieve package name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName;
    }

    /**
     * Set package path
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $packagePath
     * @return $this
     */
    public function setPackagePath($packagePath)
    {
        $this->packagePath = $packagePath;
        return $this;
    }

    /**
     * Retrieve package path
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getPackagePath()
    {
        return $this->packagePath;
    }

    /**
     * Retrieve package service provider file path
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getPackageServiceProviderFilePath()
    {
        return sprintf('%s/src/ServiceProvider.php', $this->getPackagePath());
    }

    /**
     * Set vendor and package name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $package
     * @return $this
     */
    public function setVendorPackageName($package)
    {
        // Split package into vendor name and package name
        list($vendorName, $packageName) = explode('/', $package);

        // Set vendor name and package name
        return $this->setVendorName($vendorName)->setPackageName($packageName);
    }

    /**
     * Retrieve vendor/package name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getVendorPackageName()
    {
        return sprintf('%s/%s', $this->getVendorName(), $this->getPackageName());
    }

    /**
     * Locate package namespace from Composer's array
     * of PSR-4 registered packages
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string|null
     */
    public function locatePackageNamespaceFromComposer()
    {
        // Load Composer's array of PSR-4 registered packages
        $composerPsr4 = (array) require($this->getBasePath('vendor/composer/autoload_psr4.php'));
        if (empty($composerPsr4)) {
            return null;
        }

        // Generate our package path
        $packagePath = sprintf('%s/src', $this->getPackagePath());

        // Loop through all PSR-4 registered packages
        // and look for our package path.
        //
        // If found return the namespace connected to
        // our package path
        foreach ($composerPsr4 as $packageNamespace => $path) {
            // Incorrect package path. Move on.
            if ($path[0] != $packagePath) {
                continue;
            }

            // Return found package namespace
            return $packageNamespace;
        }

        return null;
    }

    /**
     * Retrieve namespace of core service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getCoreServiceProviderNamespace()
    {
        return 'Nodes\\ServiceProvider';
    }

    /**
     * Set installer instance
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @final
     * @access public
     * @param  \Illuminate\Console\Command $installer
     * @return $this
     */
    public function setInstaller(IlluminateConsoleCommand $installer)
    {
        $this->installer = $installer;
        return $this;
    }

    /**
     * Retrieve installer instance
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Illuminate\Console\Command
     */
    public function getInstaller()
    {
        return $this->installer;
    }
}