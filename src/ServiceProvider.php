<?php
namespace Nodes;

use Nodes\Support\UserAgent\Parser as NodesUserAgentParser;
use phpbrowscap\Browscap;

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
     * Register Artisan commands
     *
     * @var array
     */
    protected $commands = [
        \Nodes\Console\Commands\InstallPackage::class
    ];

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
            $browscap = new Browscap(storage_path('framework/cache'));
            $browscap->remoteIniUrl = 'http://browscap.org/stream?q=PHP_BrowsCapINI';
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
}