<?php
namespace ApnaPayment\Settlements\Exceptions;

use Exception;

class InsufficientAccountBalanceException extends Exception
{
    protected $message = 'Insufficient balance to complete the settlement.';
    protected $code = 402;
}
