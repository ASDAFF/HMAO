<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => 'Личный кабинет сотрудника департамента образования',
	"DESCRIPTION" => 'Личный кабинет сотрудника департамента образования',
	"ICON" => '/images/icon.gif',
	"SORT" => 10,
//	"PATH" => array(
//		"ID" => 'SPO',
//		"NAME" => 'СПО',
//		"SORT" => 10,
//		// Раздел CHILD можно использовать, чтобы логически сгруппировать компоненты по разделам в админке.
//		"CHILD" => array(
//			"ID" => 'spo_organization_cabinet',
//			"NAME" => Loc::getMessage('STANDARD_ELEMENTS_DESCRIPTION_DIR1'),
//			"SORT" => 10
//		)
//	),
	'COMPLEX' => 'Y',
);

?>