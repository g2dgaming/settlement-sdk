<?php
namespace ApnaPayment\Settlements\Exceptions;

use Exception;



class InvalidAccountException extends ServerException
{
    protected $message = 'The provided settlement account is invalid or does not exist.';
    protected $code = 400;
}
