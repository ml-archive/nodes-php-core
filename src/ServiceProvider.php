<?php

namespace Nodes;

use BrowscapPHP\Browscap;
use BrowscapPHP\BrowscapUpdater;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Nodes\Support\UserAgent\Parser as NodesUserAgentParser;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    public function boot()
    {
        $this->publishGroups();
        $this->autoloadFilesAndDirectories();
    }

    /**
     * Register the service provider.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    public function register()
    {
        $this->registerBrowscap();
        $this->registerUserAgentParser();
    }

    /**
     * Register publish groups.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function publishGroups()
    {
        // Config files
        $this->publishes([
            __DIR__.'/../config/autoload.php' => config_path('nodes/autoload.php'),
            __DIR__.'/../config/project.php'  => config_path('nodes/project.php'),
            __DIR__.'/../config/meta.php'     => config_path('nodes/meta.php'),
        ], 'config');
    }

    /**
     * Register browscap.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function registerBrowscap()
    {
        if (!config('nodes.project.browsecap', false)) {
            return;
        }

        $this->app->singleton(Browscap::class, function ($app) {
            $cacheDir = storage_path('framework/browscap4');
            $fileCache = new \Doctrine\Common\Cache\FilesystemCache($cacheDir);
            $cache = new \Roave\DoctrineSimpleCache\SimpleCacheAdapter($fileCache);
            $logger = new \Monolog\Logger('logger');


            $browscap = new \BrowscapPHP\Browscap($cache, $logger);

            $updater = new \BrowscapPHP\BrowscapUpdater($cache, $logger);
            $updater->update(\BrowscapPHP\Helper\IniLoader::PHP_INI);


            return $browscap;
        });
    }

    /**
     * Register user agent parser.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function registerUserAgentParser()
    {
        $this->app->bind('nodes.useragent', function ($app) {
            return new NodesUserAgentParser($app['request']);
        });

        $this->app->singleton(NodesUserAgentParser::class, function ($app) {
            return $app['nodes.useragent'];
        });
    }

    /**
     * Autoload files and directories.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
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
