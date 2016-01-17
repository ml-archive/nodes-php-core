<?php
namespace Nodes\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class UserAgent
 *
 * @package Nodes\Support\Facades
 */
class UserAgent extends Facade
{
    /**
     * Retrieve original user agent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @access public
     * @return \Nodes\Support\UserAgent\Agents\Original
     */
    public static function original()
    {
        return self::$app['nodes.useragent']->getOriginalUserAgent();
    }

    /**
     * Retrieve Nodes user agent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @access public
     * @return \Nodes\Support\UserAgent\Agents\Nodes
     */
    public static function nodes()
    {
        return self::$app['nodes.useragent']->getNodesUserAgent();
    }

    /**
     * Get the registered name of the component
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'nodes.useragent';
    }
}