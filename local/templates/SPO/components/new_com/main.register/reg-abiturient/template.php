<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div class="system-form bx-auth-reg">

<?if($USER->IsAuthorized()):?>

    <p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>

<?else:?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">

            <?if (count($arResult["ERRORS"]) > 0):

                foreach ($arResult["ERRORS"] as $key => $error)

                    if (intval($key) == 0 && $key !== 0)
                        $arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

                ShowError(implode("<br />", $arResult["ERRORS"]));

            elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):?>

                <p><?echo GetMessage("REGISTER_EMAIL_WILL_BE_SENT")?></p>

            <?endif?>


            <div class="panel panel-info" >

                <div class="panel-heading">
                    <div class="panel-title">Регистрация абитуриента</div>
                </div>

                <div class="panel-body">

                    <form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data" class="form-horizontal">

                        <?if($arResult["BACKURL"] <> ''):?>
                            <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
                        <?endif;?>

                        <?php
                        // Признак того, что регистрируется абитуриент. Необходим при дальнейшей обработке регистрационных данных
                        // пользователя - создания соответствующих инфоблоков
                        ?>
                        <input type="hidden" name="userRole" value="abiturient" />

                        <!--p><span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?></p-->

                        <fieldset>
                            <!--legend><?=GetMessage("AUTH_REGISTER")?></legend-->

                            <? /* пример поля
                            <div class="form-group">
                                <label class="col-lg-6 control-label">Website</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="website">
                                </div>
                            </div>
                            */ ?>

                            <?foreach ($arResult["SHOW_FIELDS"] as $FIELD):?>
                                <?if($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true):?>

                                    <div class="form-group">
                                        <label class="col-lg-6 control-label">
                                            <?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><i class="fa fa-asterisk"></i><?endif?>
                                            <?echo GetMessage("main_profile_time_zones_auto")?>
                                        </label>
                                        <div class="col-lg-6">
                                            <select name="REGISTER[AUTO_TIME_ZONE]" onchange="this.form.elements['REGISTER[TIME_ZONE]'].disabled=(this.value != 'N')" class="form-control">
                                                <option value=""><?echo GetMessage("main_profile_time_zones_auto_def")?></option>
                                                <option value="Y"<?=$arResult["VALUES"][$FIELD] == "Y" ? " selected=\"selected\"" : ""?>><?echo GetMessage("main_profile_time_zones_auto_yes")?></option>
                                                <option value="N"<?=$arResult["VALUES"][$FIELD] == "N" ? " selected=\"selected\"" : ""?>><?echo GetMessage("main_profile_time_zones_auto_no")?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-lg-6 control-label">
                                            <?echo GetMessage("main_profile_time_zones_zones")?>
                                        </label>
                                        <div class="col-lg-6">
                                            <select name="REGISTER[TIME_ZONE]"<?if(!isset($_REQUEST["REGISTER"]["TIME_ZONE"])) echo 'disabled="disabled"'?> class="form-control">
                                            <?foreach($arResult["TIME_ZONE_LIST"] as $tz=>$tz_name):?>
                                                <option value="<?=htmlspecialcharsbx($tz)?>"<?=$arResult["VALUES"]["TIME_ZONE"] == $tz ? " selected=\"selected\"" : ""?>><?=htmlspecialcharsbx($tz_name)?></option>
                                            <?endforeach?>
                                            </select>
                                        </div>
                                    </div>

                                <?else:?>

                                    <div class="form-group">
                                        <label class="col-lg-6 control-label">
                                            <?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><i class="fa fa-asterisk"></i><?endif?>
                                            <?=GetMessage("REGISTER_FIELD_".$FIELD)?>:
                                        </label>
                                        <div class="col-lg-6">
                                            <?switch ($FIELD)
                                                {
                                                    case "PASSWORD":
                                                ?>

                                                        <input size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="bx-auth-input form-control" />

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

                                                        <div class="help-block"><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></div>

                                                <?
                                                    break;
                                                    case "CONFIRM_PASSWORD":
                                                ?>
                                                        <input size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="form-control" />
                                                <?
                                                    break;
                                                    case "PERSONAL_GENDER":
                                                ?>
                                                        <select name="REGISTER[<?=$FIELD?>]" class="form-control">
                                                            <option value=""><?=GetMessage("USER_DONT_KNOW")?></option>
                                                            <option value="M"<?=$arResult["VALUES"][$FIELD] == "M" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_MALE")?></option>
                                                            <option value="F"<?=$arResult["VALUES"][$FIELD] == "F" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_FEMALE")?></option>
                                                        </select>
                                                <?
                                                    break;
                                                    case "PERSONAL_COUNTRY":
                                                    case "WORK_COUNTRY":
                                                ?>
                                                        <select name="REGISTER[<?=$FIELD?>]" class="form-control">
                                                            <?
                                                                foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value)
                                                                {?>
                                                                    <option value="<?=$value?>"<?if ($value == $arResult["VALUES"][$FIELD]):?> selected="selected"<?endif?>><?=$arResult["COUNTRIES"]["reference"][$key]?></option>
                                                                <?}
                                                            ?>
                                                        </select>
                                                <?
                                                    break;
                                                    case "PERSONAL_PHOTO":
                                                    case "WORK_LOGO":
                                                ?>
                                                        <input size="30" type="file" name="REGISTER_FILES_<?=$FIELD?>" class="form-control" />
                                                <?
                                                    break;
                                                    case "PERSONAL_NOTES":
                                                    case "WORK_NOTES":
                                                ?>
                                                        <textarea cols="30" rows="5" name="REGISTER[<?=$FIELD?>]" class="form-control"><?=$arResult["VALUES"][$FIELD]?></textarea>
                                                <?
                                                    break;

                                                    default:
                                                        if ($FIELD == "PERSONAL_BIRTHDAY"):?><small><?=$arResult["DATE_FORMAT"]?></small><br /><?endif;?>
                                                        <input size="30" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" class="form-control" />
                                                        <?if ($FIELD == "PERSONAL_BIRTHDAY")
                                                            $APPLICATION->IncludeComponent(
                                                                'bitrix:main.calendar',
                                                                '',
                                                                array(
                                                                    'SHOW_INPUT' => 'N',
                                                                    'FORM_NAME' => 'regform',
                                                                    'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
                                                                    'SHOW_TIME' => 'N'
                                                                ),
                                                                null,
                                                                array("HIDE_ICONS"=>"Y")
                                                            );
                                                        ?>
                                                <?
                                                }
                                            ?>
                                        </div>
                                    </div>
                                <?endif?>
                            <?endforeach?>

                        </fieldset>

                        <?// ********************* User properties ***************************************************?>
                        <?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
                            <fieldset>
                                <legend>
                                    <?=strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?>
                                </legend>
                                <?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
                                    <div class="form-group">
                                        <label class="col-lg-6 control-label">
                                            <?if ($arUserField["MANDATORY"]=="Y"):?><i class="fa fa-asterisk"></i><?endif;?>
                                            <?=$arUserField["EDIT_FORM_LABEL"]?>:
                                        </label>
                                        <div class="col-lg-6">
                                            <?$APPLICATION->IncludeComponent(
                                                "bitrix:system.field.edit",
                                                $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                                                array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "regform"), null, array("HIDE_ICONS"=>"Y"));
                                            ?>
                                        </div>
                                    </div>
                                <?endforeach;?>
                            </fieldset>
                        <?endif;?>
                        <?// ******************** /User properties ***************************************************?>

                        <? /* CAPTCHA */
                            if ($arResult["USE_CAPTCHA"] == "Y") {?>
                                <fieldset>
                                    <legend><?=GetMessage("REGISTER_CAPTCHA_TITLE")?></legend>

                                    <div class="form-group">
                                        <label class="col-lg-6 control-label">
                                            <i class="fa fa-asterisk"></i>
                                            <?=GetMessage("REGISTER_CAPTCHA_PROMT")?>:
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
                                            <div><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></div>
                                            <input type="text" name="captcha_word" maxlength="50" value="" class="form-control" style="width:100px; margin-top: 5px;" />
                                        </div>
                                    </div>

                                </fieldset>
                            <?}
                        /* !CAPTCHA */ ?>

                        <div class="form-group">
                            <div class="col-lg-6 col-lg-offset-6">
                                <input type="submit" name="register_submit_button" class="btn btn-info" value="<?=GetMessage("AUTH_REGISTER")?>" />
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

<?endif?>

</div>