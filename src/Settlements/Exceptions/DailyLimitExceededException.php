<?php
namespace ApnaPayment\Settlements\Exceptions;

use Exception;


class DailyLimitExceededException extends ServerException
{
    protected $message = 'Daily limit exceeded. You cannot make this settlement.';
    protected $code = 403;
}
