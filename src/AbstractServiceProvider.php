<?php
namespace Nodes;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider
 *
 * @abstract
 * @package Nodes\Core
 */
abstract class AbstractServiceProvider extends IlluminateServiceProvider
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
    }
}