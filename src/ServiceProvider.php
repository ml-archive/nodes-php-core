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
     * Register the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function register()
    {
        // Load helpers
        $this->loadHelpers();
    }
}