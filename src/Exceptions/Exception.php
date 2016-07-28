<?php

namespace Nodes\Exceptions;

use Exception as CoreException;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class Exception.
 */
class Exception extends CoreException implements HttpExceptionInterface
{
    /**
     * Validation failed.
     *
     * @const integer
     */
    const VALIDATION_FAILED = 412;

    /**
     * Invalid "Accept" header.
     *
     * @const integer
     */
    const INVALID_ACCEPT_HEADER = 440;

    /**
     * Missing "Authorization" token.
     *
     * @const integer
     */
    const AUTH_MISSING_TOKEN = 441;

    /**
     * Invalid "Authorization" token.
     *
     * @const integer
     */
    const AUTH_INVALID_TOKEN = 442;

    /**
     * Expired "Authorization" token.
     *
     * @const integer
     */
    const AUTH_TOKEN_EXPIRED = 443;

    /**
     * Invalid 3rd party token (e.g. from OAuth).
     *
     * @const integer
     */
    const INVALID_THIRD_PARTY_TOKEN = 444;

    /**
     * Database entity not found.
     *
     * @const integer
     */
    const ENTITY_NOT_FOUND = 550;

    /**
     * Status code.
     *
     * @var int
     */
    protected $statusCode = 500;

    /**
     * Status code message.
     *
     * @var string
     */
    protected $statusMessage = null;

    /**
     * headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Report exception.
     *
     * @var bool
     */
    protected $report = true;

    /**
     * Meta data.
     *
     * @var array
     */
    protected $meta = [];

    /**
     * Exception type.
     *
     * @var string
     */
    protected $type = null;

    /**
     * Exception context.
     *
     * @var string
     */
    protected $context = null;

    /**
     * Severity of exception.
     *
     * @var string
     */
    protected $severity = 'error';

    /**
     * Message bag of errors.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * Exception constructor.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string  $message   Error message
     * @param  int $code      Error code
     * @param  array   $headers   List of headers
     * @param  bool $report    Wether or not exception should be reported
     * @param  string  $severity  Options: "fatal", "error", "warning", "info"
     */
    public function __construct($message, $code, $headers = [], $report = true, $severity = 'error')
    {
        // Set message
        $this->message = $message;

        // Set code
        $this->setCode($code);

        // Set headers
        $this->setHeaders($headers);

        // Set report state
        $this->setReport($report);

        // Set serverity
        $this->setSeverity($severity);

        // Set an empty message bag
        $this->setErrors(new MessageBag);
    }

    /**
     * Set exception code.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string|int $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        // Add status message to meta array
        $this->addMeta(['code' => $code]);

        return $this;
    }

    /**
     * Set status code.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  int $statusCode
     * @param  string  $message
     * @return $this
     */
    public function setStatusCode($statusCode, $message = null)
    {
        $this->statusCode = (int) $statusCode;

        // Add status message to meta array
        $this->addMeta(['status' => ['code' => (int) $statusCode]]);

        // Set optional status message if present
        if (! empty($message)) {
            $this->setStatusMessage($message);
        }

        return $this;
    }

    /**
     * Retrieve status code.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return int
     */
    public function getStatusCode()
    {
        return (int) $this->statusCode;
    }

    /**
     * Set status code message.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $message
     * @return $this
     */
    public function setStatusMessage($message)
    {
        $this->statusMessage = $message;

        // Add status message to meta array
        $this->addMeta(['status' => ['message' => $message]]);

        return $this;
    }

    /**
     * Retrieve status code message.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * Set headers.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Retrieve headers.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return array
     */
    public function getHeaders()
    {
        return (array) $this->headers;
    }

    /**
     * Set report flag to true.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return $this
     */
    public function report()
    {
        $this->report = true;

        return $this;
    }

    /**
     * Set report flag to false.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return $this
     */
    public function dontReport()
    {
        $this->report = false;

        return $this;
    }

    /**
     * Set whether or not to report exception.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  bool $report
     * @return $this
     */
    public function setReport($report)
    {
        $this->report = (bool) $report;

        return $this;
    }

    /**
     * Retrieve whether or not to report exception.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return bool
     */
    public function getReport()
    {
        return (bool) $this->report;
    }

    /**
     * Add data to existing meta data.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  array $meta
     * @return $this
     */
    public function addMeta(array $meta)
    {
        $this->meta = array_merge_recursive((array) $this->meta, $meta);

        return $this;
    }

    /**
     * Retrieve meta data.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return array
     */
    public function getMeta()
    {
        return (array) $this->meta;
    }

    /**
     * Set exception type.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Retrieve exception type.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set exception context.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Retrieve exception context.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set severity of exception.
     *
     * Options: "fatal", "error", "waring", "info"
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $severity
     * @return $this
     */
    public function setSeverity($severity)
    {
        if (in_array($severity, ['fatal', 'error', 'warning', 'info'])) {
            $this->severity = $severity;
        }

        return $this;
    }

    /**
     * Retrieve severity of exception.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * Set a message bag of errors.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  \Illuminate\Support\MessageBag $errors
     * @return $this
     */
    public function setErrors(MessageBag $errors)
    {
        $this->errors = $errors;

        // Add status message to meta array
        if (! $errors->isEmpty()) {
            $this->addMeta(['errors' => $errors->all()]);
        }

        return $this;
    }

    /**
     * Retrieve message bag of errors.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Check if exception has any errors.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return bool
     */
    public function hasErrors()
    {
        return ! $this->errors->isEmpty();
    }
}
