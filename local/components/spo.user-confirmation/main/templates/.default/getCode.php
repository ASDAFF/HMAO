<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?$APPLICATION->IncludeComponent(
    "spo.user-confirmation:getcode.ajax",
    "",
    Array(
        'codeType' => $arResult['VARIABLES']['codeType'],
    ),
    $component
);?>
