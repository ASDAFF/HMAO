<?
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)	die();?>

<div class="system-form bx-auth-profile">

<?ShowError($arResult["strProfileError"]);?>

<?if ($arResult['DATA_SAVED'] == 'Y') ShowNote(GetMessage('PROFILE_DATA_SAVED')); ?>

    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">

            <div class="blog">

                <div class="blog-body">

                    <form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data" class="form-horizontal">

                        <?=$arResult["BX_SESSION_CHECK"]?>

                        <input type="hidden" name="lang" value="<?=LANG?>" />
                        <input type="hidden" name="ID" value=<?=$arResult["ID"]?> />

                        <? /* пример поля
                        <div class="form-group">
                            <label class="col-lg-6 control-label">Website</label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="website">
                            </div>
                        </div>
                        */ ?>

                        <?/*if($arResult["ID"]>0) { ?>
                            <?if (strlen($arResult["arUser"]["TIMESTAMP_X"])>0) { ?>
                                <div class="form-group">
                                    <label class="col-lg-6 control-label"><?=GetMessage('LAST_UPDATE')?></label>
                                    <div class="col-lg-6"><?=$arResult["arUser"]["TIMESTAMP_X"]?></div>
                                </div>
                            <? } ?>
                            <?if (strlen($arResult["arUser"]["LAST_LOGIN"])>0) { ?>
                                <div class="form-group">
                                    <label class="col-lg-6 control-label"><?=GetMessage('LAST_LOGIN')?></label>
                                    <div class="col-lg-6"><?=$arResult["arUser"]["LAST_LOGIN"]?></div>
                                </div>
                            <? } ?>
                        <? }*/ ?>

                        <? /*
                        <div class="form-group">
                            <label class="col-lg-6 control-label"><?echo GetMessage("main_profile_title")?></label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="TITLE" value="<?=$arResult["arUser"]["TITLE"]?>" />
                            </div>
                        </div>
                        */ ?>

                        <fieldset>
                            <legend>Персональные данные</legend>

                            <div class="form-group">
                                <label class="col-lg-6 control-label">
                                    <i class="fa fa-asterisk"></i>
                                    <?=GetMessage('LOGIN')?>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="LOGIN" maxlength="50" value="<?=$arResult["arUser"]["LOGIN"]?>" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-6 control-label">
                                    <i class="fa fa-asterisk"></i>
                                    <?=GetMessage('NAME')?>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="NAME" maxlength="50" value="<?/*=$arResult["arUser"]["NAME"]*/?>" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-6 control-label">
                                    <i class="fa fa-asterisk"></i>
                                    <?=GetMessage('LAST_NAME')?>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"]?>" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-6 control-label"><?=GetMessage('SECOND_NAME')?></label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="SECOND_NAME" maxlength="50" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-6 control-label">
                                    <?if($arResult["EMAIL_REQUIRED"]):?><i class="fa fa-asterisk"></i><?endif?>
                                    <?=GetMessage('EMAIL')?>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="EMAIL" maxlength="50" value="<?=$arResult["arUser"]["EMAIL"]?>" readonly/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-6 control-label">
                                    <i class="fa fa-asterisk"></i>
                                    <?=GetMessage('USER_PHONE')?>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="PERSONAL_PHONE" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_PHONE"]?>" />
                                </div>
                            </div>

                            <?if($arResult["IS_ADMIN"]):?>
                                <div class="form-group">
                                    <label class="col-lg-6 control-label">
                                        <?=GetMessage("USER_ADMIN_NOTES")?>:
                                    </label>
                                    <div class="col-lg-6">
                                        <textarea rows="5" name="ADMIN_NOTES" class="form-control"><?=$arResult["arUser"]["ADMIN_NOTES"]?></textarea>
                                    </div>
                                </div>
                            <?endif;?>

                        </fieldset>

                        <?if($arResult["arUser"]["EXTERNAL_AUTH_ID"] == ''):?>
                            <fieldset>
                                <legend>Пароль</legend>

                                <div class="form-group">
                                    <label class="col-lg-6 control-label"><?=GetMessage('NEW_PASSWORD_REQ')?></label>
                                    <div class="col-lg-6">
                                        <input type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" class="form-control bx-auth-input" />
                                        <span class="help-block"><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></span>

                                        <?if($arResult["SECURE_AUTH"]):?>
                                            <span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
                                            <div class="bx-auth-secure-icon"></div>
                                        </span>
                                            <noscript>
                                            <span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
                                                <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                                            </span>
                                            </noscript>
                                            <script type="text/javascript">
                                                document.getElementById('bx_auth_secure').style.display = 'inline-block';
                                            </script>
                                        <?endif?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-6 control-label"><?=GetMessage('NEW_PASSWORD_CONFIRM')?></label>
                                    <div class="col-lg-6">
                                        <input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" class="form-control bx-auth-input" />
                                    </div>
                                </div>

                            </fieldset>
                        <?endif?>

                        <?if($arResult["TIME_ZONE_ENABLED"] == true):?>
                            <fieldset>
                                <legend><?echo GetMessage("main_profile_time_zones")?></legend>


                                <div class="form-group">
                                    <label class="col-lg-6 control-label"><?echo GetMessage("main_profile_time_zones_auto")?></label>
                                    <div class="col-lg-6">
                                        <select name="AUTO_TIME_ZONE" onchange="this.form.TIME_ZONE.disabled=(this.value != 'N')" class="form-control">
                                            <option value=""><?echo GetMessage("main_profile_time_zones_auto_def")?></option>
                                            <option value="Y"<?=($arResult["arUser"]["AUTO_TIME_ZONE"] == "Y"? ' SELECTED="SELECTED"' : '')?>><?echo GetMessage("main_profile_time_zones_auto_yes")?></option>
                                            <option value="N"<?=($arResult["arUser"]["AUTO_TIME_ZONE"] == "N"? ' SELECTED="SELECTED"' : '')?>><?echo GetMessage("main_profile_time_zones_auto_no")?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-6 control-label"><?echo GetMessage("main_profile_time_zones_zones")?></label>
                                    <div class="col-lg-6">
                                        <select name="TIME_ZONE"<?if($arResult["arUser"]["AUTO_TIME_ZONE"] <> "N") echo ' disabled="disabled"'?> class="form-control">
                                            <?foreach($arResult["TIME_ZONE_LIST"] as $tz=>$tz_name):?>
                                                <option value="<?=htmlspecialcharsbx($tz)?>"<?=($arResult["arUser"]["TIME_ZONE"] == $tz? ' SELECTED="SELECTED"' : '')?>><?=htmlspecialcharsbx($tz_name)?></option>
                                            <?endforeach?>
                                        </select>
                                    </div>
                                </div>

                            </fieldset>
                        <?endif?>

                        <div class="form-group">
                            <div class="col-lg-6 col-lg-offset-6">
                                <input type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?>" class="btn btn-info" />
                                <!--input type="reset" value="<?=GetMessage('MAIN_RESET');?>" class="btn" /-->
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>