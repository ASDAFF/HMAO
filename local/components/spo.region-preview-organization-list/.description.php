<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => 'Краткий список образовательных организаций',
	"DESCRIPTION" => 'Компонент, выводящий краткий список организаций',
	"ICON" => '/images/icon.gif',
	"SORT" => 10,
	"PATH" => array(
		"ID" => 'SPO',
		"NAME" => 'СПО',
		"SORT" => 10,
		// Раздел CHILD можно использовать, чтобы логически сгруппировать компоненты по разделам в админке.
		"CHILD" => array(
			"ID" => 'somesection8',
			"NAME" => 'Сайтовая часть - список организаций',
			"SORT" => 10
		)
	),
	'COMPLEX' => 'N',
);

?>