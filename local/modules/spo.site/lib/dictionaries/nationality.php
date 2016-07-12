<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class Nationality extends SPODictionary
{
	const RU = 1;
	const RU_FOREIGN = 2;
	const FOREIGN = 3;
	const NONE = 4;

	protected static $values = array(
		self::RU => 'Гражданин РФ',
		self::RU_FOREIGN => 'Гражданин РФ и иностранного государства (двойное гражданство)',
		self::FOREIGN => 'Гражданин иностранного государства',
		self::NONE => 'Лицо без гражданства',
	);

}