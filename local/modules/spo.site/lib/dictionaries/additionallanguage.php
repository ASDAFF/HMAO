<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class AdditionalLanguage extends SPODictionary
{
	const NONE = 1;
	const EN = 2;
	const DE = 3;
	const FI = 4;
	const OTHER = 99;

	protected static $values = array(
		self::NONE => 'Нет',
		self::EN => 'Английский',
		self::DE => 'Немецкий',
		self::FI => 'Финский',
		self::OTHER => 'Другое',
	);

}