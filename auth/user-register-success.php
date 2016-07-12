<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Успешная регистрация пользователя");
?>
<div class="container">
	<div class="row">
		<div class="cpl-md-12">
			<p></p>
			<p class="text-success">Вы успешно зарегистрировались в системе. <a href="/auth/">Войти в систему</a> </p>
		</div>
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>