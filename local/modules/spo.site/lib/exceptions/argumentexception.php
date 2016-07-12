<?php

namespace Spo\Site\Exceptions;
use Exception;

class ArgumentException extends SpoException
{
    const ARGUMENT_MISSING = 1;
    const ARGUMENT_INCORRECT = 2;

    public static function argumentMissing($argumentName = '')
    {
        $msg = 'Не указан обязательный входной параметр компонента ' . $argumentName;
        return new static($msg, self::ARGUMENT_MISSING);
    }

    public static function argumentIncorrect($argumentName = '')
    {
        $msg = 'Неверное значение параметра ' . $argumentName;
        return new static($msg, self::ARGUMENT_INCORRECT);
    }
}