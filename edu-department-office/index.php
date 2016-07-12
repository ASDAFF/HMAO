<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Главная");
?><?$APPLICATION->IncludeComponent(
    "new_spo.edu-department-office:main",
    "",
    Array(
        "SEF_FOLDER" => "/edu-department-office/",
        "SEF_MODE" => "Y",
        "VARIABLE_ALIASES" => Array()
    )
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>