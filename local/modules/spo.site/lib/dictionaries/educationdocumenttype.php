<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class EducationDocumentType extends SPODictionary
{
	const BASIC_CERTIFICATE = 1;
	const SECONDARY_CERTIFICATE = 2;

	protected static $values = array(
		self::BASIC_CERTIFICATE => 'Аттестат об основном общем образовании (9 классов)',
		self::SECONDARY_CERTIFICATE => 'Аттестат о среднем общем образовании (11 классов)',
	);

}