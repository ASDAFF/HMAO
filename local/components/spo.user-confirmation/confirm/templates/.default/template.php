<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?php
// todo сделать два ajax запроса на отправку кодов и убрать это...

$phoneIncorrectCode = false;
$emailIncorrectCode = false;

foreach ($arResult['errors'] as $error) {
    if ($error['property'] == 'phoneCode') {
        $phoneIncorrectCode = $error['message'];
    } elseif ($error['property'] == 'emailCode') {
        $emailIncorrectCode = $emailIncorrectCode = $error['message'];
    }
}
?>

<div class="well">
    <legend>Вы успешно авторизованны.</legend>
    <!--form id="formx" class="form-horizontal" method="post">
        <fieldset>
            <input id="userId" name="userId" hidden value="<?=$arResult['userId']?>" type="text"-->



            <!--<div class="form-group">
                <label class="col-md-4 control-label" for="phone">Номер телефона</label>
                <div class="col-md-3">
                    <input class="form-control input-md" type="text" value="<?=$arResult['bitrixUserPersonalPhone']?>" readonly>
                    <span class="help-block">Ваш номер телефона, указанный в профиле</span>
                    <div id="phoneCodeInput" <?= $phoneIncorrectCode ? '' : 'hidden'?>>
                        <input name="ConfirmationForm[phoneCode]" id="phoneCode" class="form-control input-md" type="text">
                        <span class="help-block">Введите код подтверждения из СМС</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <?php if (!$arResult['userValidData']['phoneIsConfirmed']):?>
                    <button class="getConfirmationCode btn btn-info" name="phoneCode">
                        Отправить СМС с кодом подтверждения
                    </button>
                    <?php else:?>
                        <p class="text-success">Телефон подтверждён</p>
                    <?php endif;?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="textinput">E-mail</label>
                <div class="col-md-3">
                    <input class="form-control input-md" type="text" value="<?=$arResult['bitrixUserEmail']?>" readonly>
                    <span class="help-block">Ваш адрес электронной почты, указанный в профиле</span>
                </div>
                <div class="col-md-3">
                    <?php if (!$arResult['userValidData']['emailIsConfirmed']):?>
                    <button class="getConfirmationCode btn btn-info" name="emailCode">
                        Отправить E-Mail с кодом подтверждения
                    </button>
                    <?php else:?>
                        <p class="text-success">E-mail подтверждён</p>
                    <?php endif;?>
                </div>
                <div class="row">
                    <div class="col-md-offset-4 col-md-3">
                        <div id="emailCodeInput" <?= $emailIncorrectCode ? '' : 'hidden'?>>
                            <input name="ConfirmationForm[emailCode]" class="form-control input-md" type="text">
                            <span class="help-block">Введите код подтверждения из e-mail</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <?= $emailIncorrectCode ?>
                    </div>
                </div>
            </div>

            <div id="submitBtnDiv" class="form-group" <?= $emailIncorrectCode || $phoneIncorrectCode ? '' : 'hidden'?>>
                <div class="col-md-offset-4 col-md-4">
                    <button class="btn btn-success" type="submit">Подтвердить</button>
                </div>
            </div>

        </fieldset>
    </form-->
</div>

<script>


    $(function(){
        $('.getConfirmationCode').on('click', function(e){
            var url = '/user-confirmation/getCode/';
            var codeType = e.target.name;

            if (codeType == 'phoneCode') {
                url = url + 'phone/';
            } else {
                url = url + 'email/';
            }

            var urlParams = new SpoUrl(url);
            urlParams.addParam('nolayout', 1);

            SpoAjax({
                url: urlParams.toString(),
                successCallback: function(){
                    if (codeType == 'phoneCode') {
                        $('#phoneCodeInput').show();
                        $('#submitBtnDiv').show();
                    } else if (codeType == 'emailCode') {
                        $('#emailCodeInput').show();
                        $('#submitBtnDiv').show();
                    }
                },
                failureCallback: function(errors){
                    //alert(errors);
                },
                alwaysCallback: function() {

                }
            });

            return false;
        });
    });
</script>

