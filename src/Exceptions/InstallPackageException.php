<?php
namespace Nodes\Exceptions;

/**
 * Class InstallPackageException
 *
 * @package Nodes\Exceptions
 */
class InstallPackageException extends Exception
{
    /**
     * InstallPackageException constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string  $message
     * @param  integer $code
     * @param  array   $headers
     * @param  boolean $report
     * @param  string  $severity
     */
    public function __construct($message, $code = 500, $headers = [], $report = false, $severity = 'error')
    {
        parent::__construct($message, $code, $headers, $report, $severity);
    }
}