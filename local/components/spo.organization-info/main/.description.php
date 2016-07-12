<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => 'Сайтовая часть - информация об организации',
	"DESCRIPTION" => 'Компонент сайтовой части регионального портала СПО - вывод информации об организации',
	"ICON" => '/images/icon.gif',
	"SORT" => 10,
	"PATH" => array(
		"ID" => 'SPO',
		"NAME" => 'СПО',
		"SORT" => 10,
		// Раздел CHILD можно использовать, чтобы логически сгруппировать компоненты по разделам в админке.
		"CHILD" => array(
			"ID" => 'somesection4',
			"NAME" => 'Сайтовая часть - информация об организации',
			"SORT" => 10
		)
	),
	'COMPLEX' => 'Y',
);

?>