<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>

<?$APPLICATION->IncludeComponent(
	"spo.abiturient-office:application.create",
	"",
	Array(
		"organizationId" => $arResult['VARIABLES']['organizationId'],
	),
	$component
);?>