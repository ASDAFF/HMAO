<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("abiturient-office");
?><?$APPLICATION->IncludeComponent(
	"spo.abiturient-office:main",
	"",
	Array(
		"SEF_FOLDER" => "/abiturient-office/",
"SEF_MODE" => "Y",
		"VARIABLE_ALIASES" => Array()
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>