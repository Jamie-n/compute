<?php

namespace App\Exceptions;

use Exception;

class InvalidOrderException extends Exception
{
    public static function genericMessage(): InvalidOrderException
    {
        return new self('An Error Occurred Whilst Placing Your Order - If this issue persists please speak to support.');
    }
}
