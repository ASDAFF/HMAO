<?php

namespace Spo\Site\Util;
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 26.05.15
 * Time: 15:58
 */

class UiMessage
{
    const TYPE_COMMON  = 'success';
    const TYPE_ERROR   = 'danger';
    const TYPE_WARNING = 'warning';

    static $sessKey = 'spo.ui.message';
    static $types   = array(
        self::TYPE_COMMON,
        self::TYPE_ERROR,
        self::TYPE_WARNING,
    );

    // вероятно может не работать если сессия не инициализирована
    public static function addMessage($message, $type = self::TYPE_COMMON)
    {
        $validType = self::getValidType($type);

        if(!is_array($_SESSION[self::$sessKey]))
        {
            $_SESSION[self::$sessKey] = array();
        }

        if(!is_array($_SESSION[self::$sessKey][$validType]))
        {
            $_SESSION[self::$sessKey][$validType] = array();
        }

        $_SESSION[self::$sessKey][$validType][] = $message;
    }

    public static function getMessages($clean = true)
    {
        if(isset($_SESSION[self::$sessKey]))
        {
            $messages = $_SESSION[self::$sessKey];
            if($clean)
            {
                unset($_SESSION[self::$sessKey]);
            }
            return $messages;
        }
        else
        {
            return array();
        }
    }

    protected static function getValidType($type)
    {
        if(in_array($type, self::$types))
        {
            return $type;
        }
        else
        {
            return self::TYPE_COMMON;
        }
    }
}