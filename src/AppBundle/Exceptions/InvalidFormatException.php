<?php


namespace AppBundle\Exceptions;


class InvalidFormatException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}