<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация абитуриента");
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:main.register", 
	"reg-abiturient", 
	array(
//		"SHOW_FIELDS" => array(
//			0 => "NAME",
//			1 => "SECOND_NAME",
//			2 => "LAST_NAME",
//			3 => "PERSONAL_PHONE",
//		),
//		"REQUIRED_FIELDS" => array(
//			0 => "NAME",
//			1 => "LAST_NAME",
//			2 => "PERSONAL_PHONE",
//		),
		"AUTH" => "N",
		"USE_BACKURL" => "Y",
		"SUCCESS_PAGE" => "/",
		"SET_TITLE" => "Y",
		"USER_PROPERTY" => array(
		),
		"USER_PROPERTY_NAME" => ""
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>