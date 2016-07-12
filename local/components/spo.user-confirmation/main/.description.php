<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => 'Сайтовая часть - подтверждение регистрационных данных',
	"DESCRIPTION" => 'Компонент подтверждения регистрационных данных пользователя',
	"ICON" => '/images/icon.gif',
	"SORT" => 10,
	"PATH" => array(
		"ID" => 'SPO',
		"NAME" => 'СПО',
		"SORT" => 10,
		// Раздел CHILD можно использовать, чтобы логически сгруппировать компоненты по разделам в админке.
		"CHILD" => array(
			"ID" => 'somesection6',
			"NAME" => 'Сайтовая часть - подтверждение регистрационных данных',
			"SORT" => 10
		)
	),
	'COMPLEX' => 'Y',
);

?>