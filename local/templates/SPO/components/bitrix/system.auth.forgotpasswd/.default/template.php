<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="container">
	<div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
		<div class="panel panel-info" >
			<div class="panel-heading">
				<div class="panel-title">Восстановление пароля</div>
			</div>

			<div style="padding-top:30px" class="panel-body" >

				<?php if (!empty($arParams["~AUTH_RESULT"])):?>
				<div class="alert alert-danger" role="alert">
					<?=ShowMessage($arParams["~AUTH_RESULT"])?>
				</div>
				<?php endif;?>

				<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">

					<?if($arResult["BACKURL"] <> ''):?>
						<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
					<?endif?>

					<input type="hidden" name="AUTH_FORM" value="Y" />
					<input type="hidden" name="TYPE" value="SEND_PWD">


					<label class="control-label">Введите логин пользователя:</label>
					<div style="margin-bottom: 25px" class="input-group">
						<span class="input-group-addon"><i class="fa fa-user"></i></span>
						<input id="login-username" type="text" class="form-control" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" size="17" placeholder="Логин">
					</div>

					<label class="control-label">...или e-mail:</label>
					<div style="margin-bottom: 25px" class="input-group">
						<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
						<input id="login-password" type="text" class="form-control" name="USER_EMAIL" maxlength="255" size="17" placeholder="E-mail">
					</div>

					<div class="form-group">
						<div class="controls">
							<button type="submit" name="send_account_info" class="btn btn-info btn-block">Выслать</button>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>

