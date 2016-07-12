<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => 'Подробное описание специальности СПО',
	"DESCRIPTION" => 'Компонент для отображения подробного описания специальности',
	"ICON" => '/images/icon.gif',
	"SORT" => 20,
//	"PATH" => array(
//		"ID" => 'SPO',
//		"NAME" => 'СПО',
//		"SORT" => 10,
//		"CHILD" => array(
//			"ID" => 'somesection',
//			"NAME" => Loc::getMessage('STANDARD_ELEMENTS_LIST_DESCRIPTION_DIR'),
//			"SORT" => 10
//		)
//	),
);

?>