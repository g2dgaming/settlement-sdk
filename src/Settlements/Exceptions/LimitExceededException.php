<?php
namespace ApnaPayment\Settlements\Exceptions;

class LimitExceededException extends ServerException
{
    protected $message = 'Your limits have exceeded. You cannot make this settlement.';
    protected $code = 403;
}
