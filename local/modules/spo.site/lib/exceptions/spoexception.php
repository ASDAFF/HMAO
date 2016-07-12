<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 19.05.15
 * Time: 13:47
 */
namespace Spo\Site\Exceptions;

use Exception;

class SpoException extends Exception
{
    protected static $defaultMessage = 'Ошибка обработки данных';
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        if($message === '')
        {
            $message = static::$defaultMessage;
        }

        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        if(defined('SPO_DEV_MODE') && SPO_DEV_MODE)
        {
            $str = 'Исключение: ' . __CLASS__ . '<br>' .
                'Код: ' . $this->code . '<br/>'.
                'Сообщение: ' . $this->message . '<br/>'.
                '<pre>' . $this->getTraceAsString() . '</pre>';
            return $str;
        }
        else
        {
            return $this->message . ' (Код: ' . $this->code . ')';
        }
    }
}