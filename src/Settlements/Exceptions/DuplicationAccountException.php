<?php
namespace ApnaPayment\Settlements\Exceptions;

class DuplicationAccountException extends ServerException
{
    protected $message = 'An account with same credentials exist.';
    protected $code = 422;
}
