<?php

namespace Nodes\Exceptions;

/**
 * Class BadRequestException
 *
 * @package Nodes\Exceptions
 */
class BadRequestException extends Exception
{
    /**
     * BadRequestException constructor
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 400);
        $this->dontReport();
        $this->setStatusCode(400);
    }
}
