<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Техникумы и колледжи");
?><?$APPLICATION->IncludeComponent(
	"spo.region-info:main",
	"",
	Array(
    "SEF_MODE" => "Y",
    "SEF_FOLDER" => "/region-info/",
	)
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>