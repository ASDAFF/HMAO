<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * @var $APPLICATION
 */
?>
<?$APPLICATION->IncludeComponent(
    "new_spo.edu-department-office:admissionplan.view",
    "",
    Array(
        "SEF_MODE" => "Y",
    ),
    $component
);?>