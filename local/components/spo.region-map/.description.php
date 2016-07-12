<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//use \Bitrix\Main\Localization\Loc as Loc;
//Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => 'Компонент карты СПО',
	"DESCRIPTION" => 'Компонент карты СПО',
	"ICON" => '/images/icon.gif',
	"SORT" => 10,
	"PATH" => array(
		"ID" => 'SPO',
		"NAME" => 'СПО',
		"SORT" => 10,
//		"CHILD" => array(
//			"ID" => 'region-map',
//			"NAME" => 'Карта региона',
//			"SORT" => 10
//		)
	),
);
?>