<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>

<?$APPLICATION->IncludeComponent(
	"spo.abiturient-office:application.edit",
	"",
	Array(
		"applicationId" => $arResult['VARIABLES']['applicationId'],
	),
	$component
);?>