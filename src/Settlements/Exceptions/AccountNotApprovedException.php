<?php
namespace ApnaPayment\Settlements\Exceptions;

class AccountNotApprovedException extends ServerException
{
    protected $message = 'An account with same credentials exist.';
    protected $code = 406;
}