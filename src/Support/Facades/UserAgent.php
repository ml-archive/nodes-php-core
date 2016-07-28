<?php

namespace Nodes\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class UserAgent.
 */
class UserAgent extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'nodes.useragent';
    }
}
