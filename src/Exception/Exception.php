<?php
/**
 * This file should be extended by all Nodes exceptions
 */
namespace Nodes\Exception;

/**
 * Class Exception
 *
 * @package Nodes\Exception
 */
class Exception extends \Exception
{
    /**
     * Validation failed
     * @const
     */
    const VALIDATION_FAILED = 412;

    /**
     * Invalid "Accept" header
     * @const
     */
    const INVALID_ACCEPT_HEADER = 440;

    /**
     * Missing "Authorization" token
     * @const
     */
    const AUTH_MISSING_TOKEN = 441;

    /**
     * Invalid "Authorization" token
     * @const
     */
    const AUTH_INVALID_TOKEN = 442;

    /**
     * Expired "Authorization" token
     * @const
     */
    const AUTH_TOKEN_EXPIRED = 443;

    /**
     * Invalid 3rd party token (e.g. from OAuth)
     * @const
     */
    const INVALID_THIRD_PARTY_TOKEN = 444;

    /**
     * Database entity not found
     * @const
     */
    const ENTITY_NOT_FOUND = 445;

    /**
     * Status code
     * @var integer
     */
    protected $statusCode;

    /**
     * Status code message
     * @var string
     */
    protected $statusCodeMessage;

    /**
     * Report exception
     * @var boolean
     */
    protected $report = true;

    /**
     * Constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string  $message            Error message
     * @param  integer $statusCode         Status code
     * @param  string  $statusCodeMessage  Status code message
     * @param  boolean $report             Wether or not exception should be reported
     */
    public function __construct($message, $statusCode = 500, $statusCodeMessage = null, $report = true)
    {
        // Set status code and status message if provided
        $this->statusCode = (int) $statusCode;
        $this->statusCodeMessage = $statusCodeMessage;

        // Set message
        $this->message = $message;

        // Set report state
        $this->report = (bool) $report;
    }

    /**
     * Retrieve status code
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Retrieve status code message
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getStatusCodeMessage()
    {
        return $this->statusCodeMessage;
    }

    /**
     * Wether or not to report exception
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function getReport()
    {
        return (bool) $this->report;
    }
}