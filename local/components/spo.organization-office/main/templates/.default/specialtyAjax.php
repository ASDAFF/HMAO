<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @var $APPLICATION
 */
$APPLICATION->IncludeComponent(
    "spo.organization-office:specialty.ajax",
    "",
    Array(
        "SEF_MODE" => "Y",
    ),
    $component
);?>