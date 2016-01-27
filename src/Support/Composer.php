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
     * Packages to install
     *
     * @var array
     */
    protected $packages = [];

    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $laravel;

    /**
     * Artisan application
     *
     * @var \Illuminate\Foundation\Console\Kernel
     */
    protected $artisan;

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
    }

    /**
     * Bootstrap Laravel application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return \Illuminate\Foundation\Application
     */
    protected function bootstrapLaravel()
    {
        // Autoload required bootstrap files
        require __DIR__ . '/../../../../../bootstrap/autoload.php';

        // Bootstrap Laravel application
        $this->laravel = $laravel = require_once __DIR__ . '/../../../../../bootstrap/app.php';

        // Bootstrap Artisan application
        $this->artisan = $laravel->make(\Illuminate\Contracts\Console\Kernel::class);

        return $laravel;
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
        // Bootstap Laravel application and console kernel
        $this->bootstrapLaravel();

        foreach ($this->packages as $package => $packagePath) {
            $this->runInstallPackageCommand($package);
        }

        $this->getArtisan()->terminate(null, null);

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
        return $this->getArtisan()->handle(
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
     * Retrieve Laravel application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Illuminate\Foundation\Application
     */
    public function getLaravel()
    {
        return $this->laravel;
    }

    /**
     * Retrieve Artisan instance
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Illuminate\Foundation\Console\Kernel
     */
    public function getArtisan()
    {
        return $this->artisan;
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