<?php
if (!function_exists('nodes_user_agent')) {
    /**
     * Retrieve Nodes user agent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return \Nodes\Support\UserAgent\Agents\Nodes|null
     */
    function nodes_user_agent()
    {
        $nodesUserAgent = app('nodes.useragent')->getNodesUserAgent();
        return ($nodesUserAgent && $nodesUserAgent->success()) ? $nodesUserAgent : null;
    }
}

if (!function_exists('user_agent')) {
    /**
     * Retrieve original user agent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return \Nodes\Support\UserAgent\Agents\Original|null
     */
    function user_agent()
    {
        $userAgent = app('nodes.useragent')->getOriginalUserAgent();
        return ($userAgent && $userAgent->success()) ? $userAgent : null;
    }
}