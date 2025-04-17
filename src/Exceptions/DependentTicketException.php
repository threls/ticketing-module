<?php

namespace Threls\ThrelsTicketingModule\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DependentTicketException extends HttpException
{
    public function __construct($message = null)
    {
        parent::__construct(Response::HTTP_NOT_FOUND, $message);
    }
}
