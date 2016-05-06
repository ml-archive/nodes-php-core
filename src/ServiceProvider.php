<?php
namespace Nodes;

use BrowscapPHP\Cache\BrowscapCache;
use Nodes\Support\UserAgent\Parser as NodesUserAgentParser;
use BrowscapPHP\Browscap;
use WurflCache\Adapter\File as CacheFile;

/**
 * Class ServiceProvider
 *
 * @package Nodes\Core
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * Name of package
     *
     * @var string
     */
    protected $package = 'core';

    /**
     * Array of configs to copy
     *
     * @var array
     */
    protected $configs = [
        'config/autoload.php' => 'config/nodes/autoload.php',
        'config/project.php' => 'config/nodes/project.php'
    ];

    /**
     * Register Artisan commands
     *
     * @var array
     */
    protected $commands = [
        \Nodes\Console\Commands\InstallPackage::class
    ];

    /**
     * Facades to install
     *
     * @var array
     */
    protected $facades = [
        'NodesUserAgent' => \Nodes\Support\Facades\UserAgent::class
    ];

    /**
     * Boot the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->autoloadFilesAndDirectories();
    }

    /**
     * Register the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerBrowscap();
        $this->registerUserAgentParser();
    }

    /**
     * Register browscap
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    protected function registerBrowscap()
    {
        $this->app->singleton(Browscap::class, function($app) {
            $browscap = new Browscap;

            // Cache for 90 days
            $browscap->setCache(new BrowscapCache(
                new CacheFile([
                    CacheFile::DIR => storage_path('framework/browscap')
                ])
            ), 7776000);

            // Automatically check for updates
            $browscap->update();

            return $browscap;
        });
    }

    /**
     * Register user agent parser
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function registerUserAgentParser()
    {
        $this->app->bind('nodes.useragent', function($app) {
            return new NodesUserAgentParser($app['request']);
        });

        $this->app->singleton(NodesUserAgentParser::class, function($app) {
            return $app['nodes.useragent'];
        });
    }

    /**
     * Autoload files and directories
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function autoloadFilesAndDirectories()
    {
        // Load files/directories from config file
        $autoload = config('nodes.autoload', null);
        if (empty($autoload)) {
            return;
        }

        foreach ($autoload as $item) {
            // Retrieve full path of item
            $itemPath = base_path($item);

            // If item doesn't exist, we'll skip it.
            if (!file_exists($itemPath)) {
                continue;
            }

            // If item is a file, we'll load it.
            //
            // If item is a directory, we'll recursively
            // go through it and load all the files we find.
            if (is_file($itemPath)) {
                include $itemPath;
            } else {
                load_directory($itemPath);
            }
        }
    }
}