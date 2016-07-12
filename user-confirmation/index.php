<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подтверждение регистрационных данных");
?><?$APPLICATION->IncludeComponent(
	"spo.user-confirmation:main",
	"",
	Array(
            "SEF_MODE" => "Y",
            "SEF_FOLDER" => "/user-confirmation/"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>