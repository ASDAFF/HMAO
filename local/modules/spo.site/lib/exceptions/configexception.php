<?php

namespace Spo\Site\Exceptions;
use Exception;

class ConfigException extends SpoException
{
    const OPTION_UNDEFINED = 1;

    public static function optionUndefined($optionName = '', $moduleName = '')
    {
        $msg = 'В таблице b_option не найден параметр "' . $optionName . '"  для модуля "' . $moduleName . '". '
            . 'После установки параметра необходимо очистить кэш CMS';

        return new static($msg, self::OPTION_UNDEFINED);
    }
}