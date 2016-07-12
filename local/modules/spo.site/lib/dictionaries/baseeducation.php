<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class BaseEducation extends SPODictionary
{
	const OLDBASIC = 1;
	const OLDSECONDARY = 2;
	const BASIC = 3;
	const SECONDARY = 4;
	const SECONPROF = 5;

	protected static $values = array(
		self::OLDBASIC => 'Основное общее образование (9 классов)',
		self::OLDSECONDARY => 'Среднее общее образование (11 классов)',
		self::BASIC => 'Основное общее образование (9 классов)',
		self::SECONDARY => 'Среднее общее образование (11 классов)',
		self::SECONPROF => 'Среднее профессиональное образование',
	);

    protected static $shortValues = array(
		self::OLDBASIC 		=> '9 классов',
		self::OLDSECONDARY 	=> '11 классов',
		self::BASIC     	=> '9 классов',
		self::SECONDARY 	=> '11 классов',
	);

    public static function getShortValue($code)
    {
        if (array_key_exists($code, static::$shortValues))
            return static::$shortValues[$code];
        return null;
    }

}