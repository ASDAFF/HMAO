<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 21.05.15
 * Time: 12:43
 */
/* @var $APPLICATION; */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin.php");
//CModule::IncludeModule("iblock");
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->IncludeComponent(
    "spo.admin:main",
    "",
    Array(
        "SEF_MODE" => "Y",
    ),
    $component
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");