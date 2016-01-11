<?php
namespace Nodes;

/**
 * Class ServiceProvider
 *
 * @package Nodes\Core
 */
class ServiceProvider extends AbstractServiceProvider
{
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

        // Register Artisan commands
        $this->commands($this->commands);
    }
}