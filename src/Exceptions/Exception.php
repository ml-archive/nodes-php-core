<?php
namespace Nodes\Exception;

use Exception as CoreException;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class Exception
 *
 * @package Nodes\Exception
 */
class Exception extends CoreException implements HttpExceptionInterface
{
    /**
     * Validation failed
     *
     * @const integer
     */
    const VALIDATION_FAILED = 412;

    /**
     * Invalid "Accept" header
     *
     * @const integer
     */
    const INVALID_ACCEPT_HEADER = 440;

    /**
     * Missing "Authorization" token
     *
     * @const integer
     */
    const AUTH_MISSING_TOKEN = 441;

    /**
     * Invalid "Authorization" token
     *
     * @const integer
     */
    const AUTH_INVALID_TOKEN = 442;

    /**
     * Expired "Authorization" token
     *
     * @const integer
     */
    const AUTH_TOKEN_EXPIRED = 443;

    /**
     * Invalid 3rd party token (e.g. from OAuth)
     *
     * @const integer
     */
    const INVALID_THIRD_PARTY_TOKEN = 444;

    /**
     * Database entity not found
     *
     * @const integer
     */
    const ENTITY_NOT_FOUND = 445;

    /**
     * Status code
     *
     * @var integer
     */
    protected $statusCode;

    /**
     * Status code message
     *
     * @var string
     */
    protected $statusMessage;

    /**
     * Report exception
     *
     * @var boolean
     */
    protected $report = true;

    /**
     * Message bag of errors
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * Exception constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string  $message        Error message
     * @param  integer $statusCode     Status code
     * @param  string  $statusMessage  Status code message
     * @param  array   $headers        List of headers
     * @param  boolean $report         Wether or not exception should be reported
     */
    public function __construct($message, $statusCode = 500, $statusMessage = null, $headers = [], $report = true)
    {
        // Set status code and status message if provided
        $this->statusCode = (int) $statusCode;
        $this->statusMessage = $statusMessage;

        // Set message
        $this->message = $message;

        // Set headers
        $this->headers = $headers;

        // Set report state
        $this->report = (bool) $report;

        // Set an empty message bag
        $this->errors = new MessageBag;
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
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * Retrieve headers
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
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

    /**
     * Set a message bag of errors
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Illuminate\Support\MessageBag $errors
     * @return \Nodes\Exception\Exception
     */
    public function setErrors(MessageBag $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Retrieve message bag of errors
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Check if exception has any errors
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function hasErrors()
    {
        return !$this->errors->isEmpty();
    }
}