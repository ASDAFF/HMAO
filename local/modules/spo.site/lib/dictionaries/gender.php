<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class Gender extends SPODictionary
{
	const MALE = 1;
	const FEMALE = 2;
	// Ещё варианты?

	protected static $values = array(
		self::MALE => 'Мужской',
		self::FEMALE => 'Женский',
	);

}