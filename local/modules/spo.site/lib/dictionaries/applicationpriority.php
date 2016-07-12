<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class ApplicationPriority extends SPODictionary
{
	const HIGH = 1;
	const MIDDLE = 2;
	const LOW = 3;

	protected static $values = array(
		self::HIGH 		=> 'Высокий (1)',
		self::MIDDLE 	=> 'Средний (2)',
		self::LOW 		=> 'Низкий (3)',
	);
}