<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class IdentityDocumentType extends SPODictionary
{
	const PASSPORT = 1;
	const BIRTH_CERTIFICATE = 2;

	protected static $values = array(
		self::PASSPORT => 'Паспорт',
		self::BIRTH_CERTIFICATE => 'Свидетельство о рождении',
	);

}