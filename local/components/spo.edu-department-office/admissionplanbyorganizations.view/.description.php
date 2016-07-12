<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => 'Контрольные цифры приёма по организациям',
	"DESCRIPTION" => 'Компонент для вывода контрольных цифр приёма по организациям',
	"ICON" => '/images/icon.gif',
	"SORT" => 10,
    // чтобы компонент был виден в списке компонентов, необходимо добавить описание PATH
);

?>