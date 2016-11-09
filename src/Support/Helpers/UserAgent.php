<?php

if ( ! function_exists('nodes_user_agent')) {
    /**
     * Retrieve Nodes user agent.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return \Nodes\Support\UserAgent\Agents\Nodes|null
     * @deprecated
     */
    function nodes_user_agent()
    {
        $nodesUserAgent = app('nodes.useragent')->getNodesUserAgent();

        return ($nodesUserAgent && $nodesUserAgent->success()) ? $nodesUserAgent : null;
    }
}

if ( ! function_exists('nodes_meta')) {
    /**
     * nodes_meta
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return Nodes\Support\UserAgent\Agents\Meta|null
     */
    function nodes_meta()
    {
        return app('nodes.useragent')->getNodesMeta();
    }
}

if ( ! function_exists('user_agent')) {
    /**
     * Retrieve original user agent.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return \Nodes\Support\UserAgent\Agents\Original|null
     */
    function user_agent()
    {
        $userAgent = app('nodes.useragent')->getOriginalUserAgent();

        return ($userAgent && $userAgent->success()) ? $userAgent : null;
    }
}
