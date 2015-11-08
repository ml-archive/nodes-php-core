<?php
namespace Nodes\Exception;

/**
 * Class HelperDirectoryNotFoundException
 *
 * @package Nodes\Exception
 */
class HelperDirectoryNotFoundException extends Exception
{
    /**
     * Constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string  $message       Error message
     * @param  integer $statusCode    Status code
     * @param  string  $statusMessage Status code message
     * @param  boolean $report        Wether or not exception should be reported
     */
    public function __construct($message = 'Helper directory does not exist.', $statusCode = 500, $statusMessage = 'Helper directory does not exist', $report = false)
    {
        parent::__construct($message, $statusCode, $statusMessage, $report);
    }
}