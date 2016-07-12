<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * @var $APPLICATION
 */
?>
<?$APPLICATION->IncludeComponent(
    "spo.organization-office:application.list",
    "archive",
    Array(
        "SEF_MODE" => "Y",
        "ARCHIVE" => true
    ),
    $component
);?>