<?php

namespace Nodes\Exceptions;

/**
 * Class BadRequestException.
 */
class BadRequestException extends Exception
{
    /**
     * BadRequestException constructor.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
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
