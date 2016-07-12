<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class TrainingType extends SPODictionary
{
    // TODO узнать, чем это отличается trainingLevel (базовый - углублённый)
	const MID_LEVEL = 1;
	const SKILLED = 2;

	protected static $values = array(
		self::MID_LEVEL => 'Программа подготовки специалистов среднего звена',
		self::SKILLED => 'Программа подготовки квалифицированных рабочих, служащих',
	);

}