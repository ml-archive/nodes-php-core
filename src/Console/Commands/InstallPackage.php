<?php
namespace Nodes\Console\Commands;

use Illuminate\Console\Command;
use Nodes\AbstractServiceProvider as NodesAbstractServiceProvider;
use Nodes\Exceptions\InstallNodesPackageException;
use Nodes\Exceptions\InstallPackageException;

/**
 * Class InstallPackage
 *
 * @package Nodes\Console\Commands
 */
class InstallPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nodes:package:install
                            {package : Name of package (e.g. "nodes/core")}
                            {--file : Filename of Service Provider located in package\'s "src/" folder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a Nodes package into your project';

    /**
     * Install package's service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     * @throws \Nodes\Exceptions\InstallPackageException
     */
    public function handle()
    {
        // Retrieve package name
        $package = $this->argument('package');

        // Validate package name
        if (!$this->validatePackageName($package)) {
            throw new InstallPackageException(sprintf('Invalid package name [%s]', $package), 400);
        }

        // Split package into vendor name and package name
        list($vendor, $packageName) = explode('/', $package);

        // Service Provider filename
        $serviceProviderFileName = $this->option('file') ?: 'ServiceProvider.php';

        // Check if package is already installed.
        // If it is, we'll abort and do nothing.
        if (nodes_is_package_installed($vendor, $packageName, $serviceProviderFileName)) {
            return;
        }

        // Make user confirm installation of package
        if (!$this->confirm(sprintf('Do you wish to install package [%s] into your application?', $package), true)) {
            $this->output->block(sprintf('See README.md for instructions to manually install package [%s].', $package), 'TIP!', 'fg=white;bg=black', ' ', true);
            return;
        }

        // Install service provider for package
        $serviceProviderClass = nodes_install_service_provider($vendor, $packageName, $serviceProviderFileName);
        if ($serviceProviderClass === true) {
            return;
        }

        // Ask a series of installation questions
        // such as to copy config files, views etc.
        $serviceProvider = app($serviceProviderClass, [$this->getLaravel()]);
        if ($serviceProvider instanceof NodesAbstractServiceProvider) {
            // Set Console Outputter
            $serviceProvider->setOutput($this->getOutput());

            // Install package facades
            if (is_null(nodes_install_facades($vendor, $packageName, $serviceProvider))) {
                $this->error('Could not localte aliases array in [config/app.php]');
            }

            // Run package install sequence
            $serviceProvider->install();
        }

        // Successfully installed package
        $this->info(sprintf('Service Provider for package <comment>[%s]</comment> was successfully installed.', sprintf('%s/%s', $vendor, $packageName)));
    }

    /**
     * Validate package name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $package
     * @return boolean
     */
    protected function validatePackageName($package)
    {
        $package = explode('/', $package);
        return count($package) == 2;
    }
}