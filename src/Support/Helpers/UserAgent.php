<?php
if (!function_exists('nodes_user_agent')) {
    /**
     * Retrieve Nodes user agent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return \Nodes\Support\UserAgent\Agents\Nodes
     */
    function nodes_user_agent()
    {
        return \NodesUserAgent::nodes();
    }
}

if (!function_exists('user_agent')) {
    /**
     * Retrieve original user agent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return \Nodes\Support\UserAgent\Agents\Original
     */
    function user_agent()
    {
        return \NodesUserAgent::original();
    }
}