<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class ExamDiscipline extends SPODictionary
{
	const RUSSIAN_LANGUAGE = 1;
	const MATHEMATICS = 2;
    const HISTORY = 3;
    const BIOLOGY = 4;
    const GEOGRAPHY = 5;
    const FOREIGN_LANGUAGE = 6;
    const CHEMISTRY = 7;
    const LITERATURE = 8;
    const PHYSICS = 9;
    const INFORMATICS = 10;

	protected static $values = array(
        self::RUSSIAN_LANGUAGE => 'Русский язык',
        self::MATHEMATICS => 'Математика',
        self::HISTORY => 'История',
        self::BIOLOGY => 'Биология',
        self::GEOGRAPHY => 'География',
        self::FOREIGN_LANGUAGE => 'Иностранный язык',
        self::CHEMISTRY => 'Химия',
        self::LITERATURE => 'Литература',
        self::PHYSICS => 'Физика',
        self::INFORMATICS => 'Информатика',
	);

}