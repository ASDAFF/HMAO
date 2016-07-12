<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация образовательного учреждения");
?>Регистрация организации<br>
	<br>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.register", 
	"reg-organization", 
	array(
		"SHOW_FIELDS" => array(
			0 => "NAME",
			1 => "SECOND_NAME",
			2 => "LAST_NAME",
		),
		"REQUIRED_FIELDS" => array(
		),
		"AUTH" => "N",
		"USE_BACKURL" => "Y",
		"SUCCESS_PAGE" => "/auth/user-register-success.php",
		"SET_TITLE" => "Y",
		"USER_PROPERTY" => array(
			0 => "UF_EDUORG",
		),
		"USER_PROPERTY_NAME" => ""
	),
	false
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>