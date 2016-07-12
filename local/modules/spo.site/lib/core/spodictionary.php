<?php

namespace Spo\Site\Core;

use ReflectionClass;
use Spo\Site\Util\CVarDumper;

class SPODictionary
{

	protected static $values = array();

	public static function getValue($code)
	{
		if (array_key_exists($code, static::$values))
			return static::$values[$code];
		return $code;
	}

	public static function getshortValues($code)
	{
		if (array_key_exists($code, static::$shortValues))
			return static::$shortValues[$code];
		return $code;
	}

	public static function getValuesArray()
	{
		return static::$values;
	}

	public static function getClassArray()
	{
		return static::$class;
	}

    public static function getKeysArray()
    {
        $keys = array();
        foreach (static::$values as $key => $value) {
            $keys[] = $key;
        }

        return $keys;
    }

	protected static function getClassConstants() {
		$oClass = new ReflectionClass(get_called_class());
		return $oClass->getConstants();
	}

	public static function isDefined($code)
	{
		if (in_array(intval($code), static::getClassConstants()))
			return true;

		return false;
	}

}