<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Информация об организации");
?><?$APPLICATION->IncludeComponent(
	"spo.organization-info:main",
	"",
	Array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/organization-info/",
		"VARIABLE_ALIASES" => Array(),
		"VARIABLE_ALIASES" => Array(
		)
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>