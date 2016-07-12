<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<div class="container">
	<div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">

		<div class="panel panel-info" >
			<div class="panel-heading">
				<div class="panel-title">Изменение пароля</div>
			</div>

			<div style="padding-top:30px" class="panel-body" >

				<?php if ($arResult['ERROR']):?>
					<div class="alert alert-danger alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<?=ShowMessage($arResult['ERROR_MESSAGE'])?>
					</div>
				<?php endif;?>

				<?php if (!empty($arParams["~AUTH_RESULT"])):?>
					<div class="alert alert-danger" role="alert">
						<?=ShowMessage($arParams["~AUTH_RESULT"])?>
					</div>
				<?php endif;?>

				<form method="post" target="_top" action="<?=$arResult["AUTH_FORM"]?>" name="bform"class="form-horizontal">

					<?if($arResult["BACKURL"] <> ''):?>
						<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
					<?endif?>

					<input type="hidden" name="AUTH_FORM" value="Y" />
					<input type="hidden" name="TYPE" value="CHANGE_PWD">



					<div class="form-group">
						<div class="col-md-12 controls">
							<label class="control-label">Логин </label>
							<input type="text" class="form-control" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" size="17" placeholder="Логин">
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12 controls">
							<label class="control-label">Контрольная строка </label>
							<input type="text" class="form-control" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>">
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12 controls">
							<label class="control-label">Новый пароль </label>
							<input type="password" class="form-control" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>">
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12 controls">
							<label class="control-label">Повтор пароля </label>
							<input type="password" class="form-control" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>">
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12 controls">
							<button type="submit" name="change_pwd" class="btn btn-info btn-block">Изменить пароль</button>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>