<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет организации");
//\Bitrix\Main\ModuleManager::registerModule('spo.site');
//\Bitrix\Main\Loader::includeModule('test.module');
?><?$APPLICATION->IncludeComponent(
	"spo.organization-office:main",
	"",
	Array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/organization-office/",
		"VARIABLE_ALIASES" => Array(
		)
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>