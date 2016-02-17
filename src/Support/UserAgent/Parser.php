<?php
namespace Nodes\Support\UserAgent;

use Illuminate\Http\Request;
use Nodes\Support\UserAgent\Agents\Nodes as NodesUserAgent;
use Nodes\Support\UserAgent\Agents\Original as OriginalUserAgent;

/**
 * Class Parser
 *
 * @package Nodes\Support\UserAgent
 */
class Parser
{
    /**
     * User agent received from request header
     *
     * @var string
     */
    protected $userAgent;

    /**
     * Original user agent
     *
     * @var string
     */
    protected $originalUserAgent;

    /**
     * Nodes user agent
     *
     * @var string
     */
    protected $nodesUserAgent;

    /**
     * UserAgent constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        // Retrieve user agent from request header
        // X-User-Agent is supported since some browsers / platforms override User-Agent header
        if($request->header('X-User-Agent')) {
            $this->userAgent = $userAgent = $request->header('X-User-Agent');
        } else {
            $this->userAgent = $userAgent = $request->header('User-Agent');   
        }

        // Parse received user agent
        $this->parse($userAgent);
    }

    /**
     * Parse received user agent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $userAgent
     * @return void
     */
    protected function parse($userAgent)
    {
        // By default we're assuming that we're
        // NOT dealing with a Nodes user agent
        $originalUserAgent = $userAgent;

        // If we're dealing with a Nodes user agent
        // we'll have to split the received user agent
        // into two different one. An original one and a Nodes one
        $nodesUAPosition = strrpos($userAgent, 'Nodes/');
        if ($nodesUAPosition !== false) {
            // Extract Nodes user agent from
            // the receive user agent and parse it
            $nodesUserAgent = substr($userAgent, $nodesUAPosition);
            $this->parseNodesUserAgent($nodesUserAgent);

            // Chop off the Nodes user agent from the received user agent
            $originalUserAgent = substr($userAgent, 0, $nodesUAPosition-1);
        }

        $this->parseOriginalUserAgent($originalUserAgent);
    }

    /**
     * Parse Nodes user agent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $nodesUserAgent
     * @return \Nodes\Support\UserAgent\Agents\Nodes|boolean
     */
    protected function parseNodesUserAgent($nodesUserAgent)
    {
        return $this->nodesUserAgent = new NodesUserAgent($nodesUserAgent);
    }

    /**
     * Parse original user agent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $originalUserAgent
     * @return \Nodes\Support\UserAgent\Agents\Original
     */
    protected function parseOriginalUserAgent($originalUserAgent)
    {
        return $this->originalUserAgent = new OriginalUserAgent($originalUserAgent);
    }

    /**
     * Retrieve Nodes user agent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getNodesUserAgent()
    {
        return $this->nodesUserAgent;
    }

    /**
     * Retrieve original user agent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getOriginalUserAgent()
    {
        return $this->originalUserAgent;
    }
}
