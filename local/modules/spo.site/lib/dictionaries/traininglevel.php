<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class TrainingLevel extends SPODictionary
{
	const BASE = 1;
	const EXTENDED = 2;
	const BASEPAD = 3;
	const START = 4;
	const EXTENDEDPAD = 4;

	protected static $values = array(
		self::BASE 			=>	'Базовый уровень',
		self::EXTENDED 		=>	'Повышенный уровень',
		self::BASEPAD		=>	'Базовая подготовка',
		self::START 		=>	'Начальное',
		self::EXTENDEDPAD	=>	'Углубленная подготовка',
	);

}