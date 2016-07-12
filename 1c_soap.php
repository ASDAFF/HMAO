<?
define("NEED_AUTH", false);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("ws_addnews");
?><?$APPLICATION->IncludeComponent(
	"govpay:webservice.1c_soap",
	"",
	Array(
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>