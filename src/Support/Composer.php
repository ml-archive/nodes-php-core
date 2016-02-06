<?php
namespace Nodes\Support;

use Composer\Composer as ComposerInstance;
use Composer\EventDispatcher\EventSubscriberInterface as ComposerEventSubscriberContract;
use Composer\Installer\PackageEvent as ComposerPackageEvent;
use Composer\Installer\PackageEvents as ComposerPackageEvents;
use Composer\IO\IOInterface as ComposerIOContract;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event as ComposerScriptEvent;
use Composer\Script\ScriptEvents as ComposerScriptEvents;
use Nodes\Support\InstallPackage as NodesInstaller;

/**
 * Class Composer
 *
 * @package Nodes\Support
 */
class Composer implements PluginInterface, ComposerEventSubscriberContract
{
    /**
     * Composer instance
     *
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * Composer config
     *
     * @var array
     */
    protected $config;

    /**
     * Composer IO instance
     *
     * @var \Composer\IO\IOInterface
     */
    protected $io;

    /**
     * Nodes Installer instance
     *
     * @var \Nodes\Support\InstallPackage
     */
    protected $nodesInstaller;

    /**
     * Packages to install
     *
     * @var array
     */
    protected $packages = [];

    /**
     * Activate is called after the plugin is loaded
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Composer\Composer       $composer
     * @param  \Composer\IO\IOInterface $io
     * @return void
     */
    public function activate(ComposerInstance $composer, ComposerIOContract $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->nodesInstaller = new NodesInstaller;
    }

    /**
     * Add package name to array of packages to install
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Composer\Installer\PackageEvent $event
     * @return void
     */
    public function addPackage(ComposerPackageEvent $event)
    {
        // Retrieve package
        $package = $event->getOperation()->getPackage();

        // Retrieve install path of package
        $packagePath = $event->getComposer()->getInstallationManager()->getInstallPath($package) . '/';

        // If package contains a service provider,
        // add package to array of packages to install
        if ($this->packageHasServiceProvider($packagePath)) {
            $this->packages[$package->getName()] = $packagePath;
        }
    }

    /**
     * Install packages
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Composer\Script\Event $event
     * @return void
     */
    public function installPackages(ComposerScriptEvent $event)
    {
        // Only continue if there is packages to install
        if (empty($this->packages)) {
            $event->stopPropagation();
            return;
        }

        // Make sure Nodes core service provider
        // has been installed before bootstraping Artisan
        $this->nodesInstaller->addNodesServiceProvider();

        // Bootstap Laravel and Artisan application
        $this->nodesInstaller->bootstrapLaravelArtisan();

        // Install found packages
        foreach ($this->packages as $package => $packagePath) {
            $this->runInstallPackageCommand($package);
        }

        // Terminate Artisan instance
        $this->nodesInstaller->getArtisan()->terminate(null, null);

        // Stop propagation
        $event->stopPropagation();
    }

    /**
     * Run Artisan install package command
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $package
     * @return integer
     */
    public function runInstallPackageCommand($package)
    {
        return $this->nodesInstaller->getArtisan()->handle(
            new \Symfony\Component\Console\Input\ArgvInput(['NodesComposer', 'nodes:package:install', $package]),
            new \Symfony\Component\Console\Output\ConsoleOutput
        );
    }

    /**
     * Check if package has a service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $packagePath
     * @return boolean
     */
    protected function packageHasServiceProvider($packagePath)
    {
        return file_exists($packagePath . 'src/ServiceProvider.php');
    }

    /**
     * Generate base path from package path
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $packagePath
     * @return string
     */
    protected function generateBasePathFromPackagePath($packagePath)
    {
        return substr($packagePath, 0, strrpos($packagePath, '/vendor')+1);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @access public
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ComposerScriptEvents::POST_INSTALL_CMD => [
                ['installPackages', 0]
            ],
            ComposerScriptEvents::POST_UPDATE_CMD => [
                ['installPackages', 0]
            ],
            ComposerPackageEvents::POST_PACKAGE_INSTALL => [
                ['addPackage', 0]
            ]
        ];
    }
}