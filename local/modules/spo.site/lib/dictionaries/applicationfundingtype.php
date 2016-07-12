<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class ApplicationFundingType extends SPODictionary
{
	const GRANT = 1;
	const PAID = 2;

	protected static $values = array(
		self::GRANT => 'Бюджетная форма обучения',
		self::PAID => 'Контрактная форма обучения',
	);
}