<?php
namespace ApnaPayment\Settlements\Exceptions;

use Exception;


class ServerException extends Exception
{
    protected $message = 'Daily limit exceeded. You cannot make this settlement.';
    protected $code = 500;
}
