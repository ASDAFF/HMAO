<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class StudyMode extends SPODictionary
{
	const INTRAMURAL = 1;
	const EXTRAMURAL = 2;
	const MIXED = 3;

	protected static $values = array(
		self::INTRAMURAL => 'Очная форма обучения',
		self::EXTRAMURAL => 'Заочная форма обучения',
		self::MIXED => 'Очно-заочная форма обучения',
	);

    protected static $shortValues = array(
        self::INTRAMURAL => 'Очная',
        self::EXTRAMURAL => 'Заочная',
        self::MIXED => 'Очно-заочная',
    );

    public static function getShortValue($code)
    {
        if (array_key_exists($code, static::$shortValues))
            return static::$shortValues[$code];

        return null;
    }
}