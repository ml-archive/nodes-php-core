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
     * Array of configs to copy
     *
     * @var array
     */
    protected $configs = [];

    /**
     * Array of views to copy
     *
     * @var array
     */
    protected $views = [];

    /**
     * Array of assets to copy
     *
     * @var array
     */
    protected $assets = [];

    /**
     * Install service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @final
     * @access public
     * @return void
     */
    final public function install()
    {
        $this->installConfigs();
        $this->installViews();
        $this->installAssets();
        $this->installCustom();
    }

    /**
     * Register the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function register() {}

    /**
     * Copy configs to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installConfigs()
    {

    }

    /**
     * Copy views to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installViews()
    {

    }

    /**
     * Copy assets to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installAssets()
    {

    }

    /**
     * Install scaffolding
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @abstract
     * @access protected
     * @return void
     */
    abstract protected function installScaffolding();

    /**
     * Install custom stuff
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @abstract
     * @access protected
     * @return void
     */
    abstract protected function installCustom();
}