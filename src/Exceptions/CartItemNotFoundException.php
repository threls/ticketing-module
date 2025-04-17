<?php

namespace Threls\ThrelsTicketingModule\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CartItemNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(Response::HTTP_NOT_FOUND, 'Cart item not found.');
    }

}
