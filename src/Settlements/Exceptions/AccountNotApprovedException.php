<?php
namespace ApnaPayment\Settlements\Exceptions;

class AccountNotApprovedException extends ServerException
{
    protected $message = 'This account cannot be used as it is not yet approved.';
    protected $code = 406;
}