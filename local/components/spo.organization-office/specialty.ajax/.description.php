<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => 'Ajax компонент специальностей',
	"DESCRIPTION" => 'Ajax компонент специальностей',
	"ICON" => '/images/icon.gif',
	"SORT" => 10,
    // чтобы компонент был виден в списке компонентов, необходимо добавить описание PATH
);

?>