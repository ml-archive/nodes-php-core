<?php

namespace Nodes\Support\UserAgent;

use Illuminate\Http\Request;
use Nodes\Support\UserAgent\Agents\Meta as NodesMeta;
use Nodes\Support\UserAgent\Agents\Nodes as NodesUserAgent;
use Nodes\Support\UserAgent\Agents\Original as OriginalUserAgent;

/**
 * Class Parser.
 */
class Parser
{
    /**
     * User agent received from request header.
     *
     * @var string
     */
    protected $userAgent;

    /**
     * Original user agent.
     *
     * @var string
     */
    protected $originalUserAgent;

    /**
     * Nodes user agent.
     *
     * @var string
     */
    protected $nodesUserAgent;

    /**
     * @var \Nodes\Support\UserAgent\Agents\Meta|null
     */
    protected $meta;

    const NODES_META_HEADER = 'N-Meta';

    /**
     * UserAgent constructor.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        // Retrieve user agent from request header
        // X-User-Agent is supported since some browsers / platforms override User-Agent header
        $this->userAgent = $userAgent = $request->header('X-User-Agent') ?: $request->header('User-Agent');

        // Set nodes meta
        if ($request->header(self::NODES_META_HEADER)) {
            $this->meta = new NodesMeta($request->header(self::NODES_META_HEADER));
        }

        // Parse received user agent
        $this->parse($userAgent);
    }

    /**
     * Parse received user agent.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param string $userAgent
     *
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
            $originalUserAgent = substr($userAgent, 0, $nodesUAPosition - 1);
        }

        $this->parseOriginalUserAgent($originalUserAgent);
    }

    /**
     * Parse Nodes user agent.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param string $nodesUserAgent
     *
     * @return \Nodes\Support\UserAgent\Agents\Nodes|bool
     */
    protected function parseNodesUserAgent($nodesUserAgent)
    {
        return $this->nodesUserAgent = new NodesUserAgent($nodesUserAgent);
    }

    /**
     * Parse original user agent.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param string $originalUserAgent
     *
     * @return \Nodes\Support\UserAgent\Agents\Original
     */
    protected function parseOriginalUserAgent($originalUserAgent)
    {
        return $this->originalUserAgent = new OriginalUserAgent($originalUserAgent);
    }

    /**
     * Retrieve Nodes user agent.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function getNodesUserAgent()
    {
        return $this->nodesUserAgent;
    }

    /**
     * Retrieve original user agent.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function getOriginalUserAgent()
    {
        return $this->originalUserAgent;
    }

    /**
     * getNodesMeta.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return \Nodes\Support\Agents\Meta|null
     */
    public function getNodesMeta()
    {
        return $this->meta;
    }
}
