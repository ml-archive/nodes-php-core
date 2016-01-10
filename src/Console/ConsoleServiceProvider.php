<?php
namespace Nodes\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ConsoleServiceProvider
 *
 * @package Nodes\Console
 */
class ConsoleServiceProvider extends IlluminateServiceProvider
{
    /**
     * Nodes commands to register
     *
     * @var array
     */
    protected $nodesCommands = [
        \Nodes\Console\Commands\InstallPackage::class
    ];

    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton('Illuminate\Contracts\Console\Kernel', Kernel::class);
    }
}