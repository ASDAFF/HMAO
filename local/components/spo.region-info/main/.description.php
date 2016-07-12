<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => 'Сайтовая часть - информация о регионе',
	"DESCRIPTION" => 'Компонент сайтовой части регионального портала СПО',
	"ICON" => '/images/icon.gif',
	"SORT" => 10,
	"PATH" => array(
		"ID" => 'SPO',
		"NAME" => 'СПО',
		"SORT" => 10,
		// Раздел CHILD можно использовать, чтобы логически сгруппировать компоненты по разделам в админке.
		"CHILD" => array(
			"ID" => 'somesection3',
			"NAME" => 'Сайтовая часть - информация о регионе',
			"SORT" => 10
		)
	),
	'COMPLEX' => 'Y',
);

?>