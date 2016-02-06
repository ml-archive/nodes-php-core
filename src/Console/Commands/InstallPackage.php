<?php
namespace Nodes\Console\Commands;

use Illuminate\Console\Command;
use Nodes\AbstractServiceProvider as NodesAbstractServiceProvider;
use Nodes\Exceptions\InstallNodesPackageException;
use Nodes\Exceptions\InstallPackageException;
use Nodes\Support\InstallPackage as NodesInstaller;

/**
 * Class InstallPackage
 *
 * @package Nodes\Console\Commands
 */
class InstallPackage extends Command
{
    /**
     * The name and signature of the console command
     *
     * @var string
     */
    protected $signature = 'nodes:package:install
                            {package : Name of package (e.g. "nodes/core")}';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Install a Nodes package into your project';

    /**
     * Nodes installer
     *
     * @var \Nodes\Support\InstallPackage
     */
    protected $nodesInstaller;

    /**
     * Array of packages that should be force installed
     *
     * @var array
     */
    private $forceInstallPackage = ['nodes/core'];

    /**
     * Install package's service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Nodes\Support\InstallPackage $nodesInstaller
     * @return void
     * @throws \Nodes\Exceptions\InstallPackageException
     */
    public function handle(NodesInstaller $nodesInstaller)
    {
        // Bootstrap Nodes Installer
        $nodesInstaller->bootstrapLaravelArtisan();

        // Retrieve package name
        $package = $this->argument('package');

        // Validate package name
        if (!$this->validatePackageName($package)) {
            throw new InstallPackageException(sprintf('Invalid package name [%s]', $package), 400);
        }

        // Set vendor and package name
        $nodesInstaller->setVendorPackageName($package);

        // Check if we should force run package installation
        $forcePackageInstall = in_array($package, $this->forceInstallPackage);

        // Check if package is already installed
        if (!$forcePackageInstall && $nodesInstaller->isPackageInstalled($package)) {
            return;
        }

        // Make user confirm installation of package
        if (!$this->confirm(sprintf('Do you wish to install package <comment>[%s]</comment> into your application?', $package), true)) {
            $this->output->block(sprintf('Run "php artisan nodes:package:install %s" when you\'re ready to install the package [%s].', $package, $package), 'TIP!', 'fg=white;bg=black', ' ', true);
            return;
        }

        // Install service provider for package
        $serviceProviderClass = $nodesInstaller->installServiceProvider($package);

        // Ask a series of installation questions
        // such as to copy config files, views etc.
        $serviceProvider = app($serviceProviderClass, [$this->getLaravel()]);
        if ($serviceProvider instanceof NodesAbstractServiceProvider) {
            // Execute package install sequence
            $serviceProvider->setInstaller($nodesInstaller)->setCommand($this)->install();
        }

        // Successfully installed package
        $this->info(sprintf('Package <comment>[%s]</comment> was successfully installed.', $package));
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