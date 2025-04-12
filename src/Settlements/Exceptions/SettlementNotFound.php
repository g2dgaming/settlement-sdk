<?php

namespace ApnaPayment\Settlements\Exceptions;

use Exception;


class SettlementNotFound extends ServerException
{
    protected $message = 'Settlement not found';
    protected $code = 404;
}
