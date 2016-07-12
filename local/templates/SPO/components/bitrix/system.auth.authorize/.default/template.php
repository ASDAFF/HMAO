<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?//
//ShowMessage($arParams["~AUTH_RESULT"]);
//ShowMessage($arResult['ERROR_MESSAGE']);
//?>
<div class="container">
    <div class="row">
	    <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">

			<div class="panel panel-info" >

				<div class="panel-heading">
                    <div style="float:right; font-size: 12px;">
                        <a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow">Забыли пароль?</a>
                    </div>
					<div class="panel-title">Вход в систему</div>
				</div>

				<div style="padding-top:30px" class="panel-body" >
					<form name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" class="form-horizontal">

						<?php if (!empty($arParams["~AUTH_RESULT"])):?>
							<div class="alert alert-danger alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
								<?=ShowMessage($arParams["~AUTH_RESULT"]);?>
							</div>
						<?php endif;?>

						<input type="hidden" name="AUTH_FORM" value="Y" />
						<input type="hidden" name="TYPE" value="AUTH" />
						<?if (strlen($arResult["BACKURL"]) > 0):?>
							<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
						<?endif?>
						<?foreach ($arResult["POST"] as $key => $value):?>
							<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
						<?endforeach?>

						<div style="margin-bottom: 25px" class="input-group">
							<span class="input-group-addon"><i class="fa fa-user"></i></span>
							<input type="text" class="form-control" name="USER_LOGIN" maxlength="255" value="<?=$arResult["USER_LOGIN"]?>" placeholder="Логин">
						</div>

						<div style="margin-bottom: 25px" class="input-group">
							<span class="input-group-addon"><i class="fa fa-lock"></i></span>
							<input type="password" class="form-control" name="USER_PASSWORD" maxlength="255" placeholder="Пароль">
						</div>

						<?if($arResult["CAPTCHA_CODE"]):?>
							<tr>
								<td></td>
								<td><input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
									<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></td>
							</tr>
							<tr>
								<td class="bx-auth-label"><?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:</td>
								<td><input class="bx-auth-input" type="text" name="captcha_word" maxlength="50" value="" size="15" /></td>
							</tr>
						<?endif;?>

						<div class="form-group">
							<div class="col-md-12 controls">
								<button type="submit" class="btn btn-info btn-block" name="Login">Вход</button>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6">
								<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
									<div class="checkbox">
										<label>
											<input id="USER_REMEMBER" name="USER_REMEMBER" value="Y" type="checkbox"> Запомнить
										</label>
									</div>
								<?endif?>
							</div>
							<div class="col-md-6 control">
								<a href="<?=$arResult["AUTH_REGISTER_URL"]?>" class="pull-right">
									Регистрация
								</a>
							</div>
						</div>
					</form>
				</div>

			</div>

        </div>
	</div>
</div>

