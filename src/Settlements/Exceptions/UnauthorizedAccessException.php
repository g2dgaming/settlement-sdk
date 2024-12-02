<?php
namespace ApnaPayment\Settlements\Exceptions;

use Exception;


class UnauthorizedAccessException extends Exception
{
    protected $message = 'Unauthorized access to the settlement account.';
    protected $code = 403;
}
