<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class OrganizationStatus extends SPODictionary
{
	const DISABLED = 1;
	const ENABLED = 2;
	const BANNED = 3;

	protected static $values = array(
		self::DISABLED => 'Не активна',
		self::ENABLED => 'Активна',
		self::BANNED => 'Заблокирована',
	);

}