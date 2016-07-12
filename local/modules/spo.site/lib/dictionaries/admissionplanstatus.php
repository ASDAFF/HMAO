<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class AdmissionPlanStatus extends SPODictionary
{
	const CREATED = 1;
	const ACCEPTED = 2;
	const DECLINED = 3;

	protected static $values = array(
		self::CREATED => 'На рассмотрении',
		self::ACCEPTED => 'Одобрено',
		self::DECLINED => 'Не одобрено',
	);
}