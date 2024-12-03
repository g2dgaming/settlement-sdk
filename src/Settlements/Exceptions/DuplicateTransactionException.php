<?php

namespace ApnaPayment\Settlements\Exceptions;

use Exception;


class DuplicateTransactionException extends ServerException
{
    protected $message = 'This settlement transaction has already been processed.';
    protected $code = 409;
}
