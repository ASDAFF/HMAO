<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class ExamType extends SPODictionary
{
	const UGE = 1;
	const WRITTEN = 2;
    const VERBAL = 3;
    const DICTATION = 4;
    const OTHER = 100;


	protected static $values = array(
        self::UGE => 'ЕГЭ',
        self::WRITTEN => 'Письменно',
        self::VERBAL => 'Устно',
        self::DICTATION => 'Диктант',
        self::OTHER => 'Другое',
	);

}