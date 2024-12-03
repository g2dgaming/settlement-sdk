<?php
namespace ApnaPayment\Settlements\Exceptions;

use Exception;


class UnauthorizedAccessException extends ServerException
{
    protected $message = 'Unauthorized access to the settlement account.';
    protected $code = 401;
}
