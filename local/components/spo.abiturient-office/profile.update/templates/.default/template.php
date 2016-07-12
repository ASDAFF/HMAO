<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
use \Bitrix\Main\Localization\Loc as Loc;
use Spo\Site\Helpers\AbiturientOfficeUrlHelper;
use Spo\Site\Dictionaries\Nationality;
use Spo\Site\Dictionaries\Gender;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\AdditionalLanguage;
use Spo\Site\Dictionaries\IdentityDocumentType;
use Spo\Site\Dictionaries\IdentityRegisterType;
use Spo\Site\Dictionaries\EducationDocumentType;
use Spo\Site\Dictionaries\Parents;
use Spo\Site\Dictionaries\ParentsType;

Loc::loadMessages(__FILE__);
$profileData = $arResult['profile'];
$userData = $arResult['user'];
?>

<!--p>
    <a class="btn btn-info" href="<?= AbiturientOfficeUrlHelper::getApplicationListUrl() ?>">Список поданных заявлений</a>
</p-->


<div class="row">
    <div class="col-md-12">
        <?php if ($arResult['success']): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <?= $arResult['success'] ?>
            </div>
        <?php endif; ?>

        <div role="tabpanel">

            <form id="abiturient-profile-form" class="form-horizontal" method="post" enctype="multipart/form-data" onsubmit="regHome()">
                <div class="error-place"></div>
                <!--fieldset-->
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-main" role="tab" data-toggle="tab" data-value="#main">Основные данные</a></li>
                    <li><a href="#tab-education" data-toggle="tab" data-value="#education">Образование</a></li>
                    <li><a href="#tab-insurance" data-toggle="tab" data-value="#insurance">Страхование</a></li>
                    <li><a href="#tab-additional" data-toggle="tab" data-value="#additional">Дополнительно</a></li>
                    <li><a href="#tab-files" data-toggle="tab" data-value="#files">Скан-копии документов</a></li>
                    <li><a href="#tab-parents" data-toggle="tab" data-value="#parents">Данные о родителях</a></li>
                </ul>

                <div class="tab-content" style=" padding-top: 20px;">

                    <div class="tab-pane active" id="tab-main">
                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Фото</label>
                            <div class="col-md-8">
                                <? if(!empty($userData['PERSONAL_PHOTO'])) {
                                    echo CFile::ShowImage($userData['PERSONAL_PHOTO'], 250, 250, 'class="profile-photo"');
                                } ?>
                                <input type="file" name="AbiturientProfile[abiturientProfilePhoto]" class="photoimg">
                                <input class="photoview" type="hidden" value="<?= (!empty($userData['PERSONAL_PHOTO'])) ? 1 : 0;?>">

                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>ФИО</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileFIO]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileFIO'] ?>">

                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Телефон</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfilePhone]"
                                       class="form-control input-md phone" type="tel"
                                       value="<?= $profileData['abiturientProfilePhone'] ?>">

                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>E-mail</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileEmail]"
                                       class="form-control input-md email" type="email"
                                       value="<?= $profileData['abiturientProfileEmail'] ?>"
                                        readonly>

                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Пол</label>
                            <div class="col-md-8">
                                <?php foreach (Gender::getValuesArray() as $key => $value): ?>
                                    <label class="radio-inline">
                                        <input name="AbiturientProfile[abiturientProfileGender]"
                                               value="<?= $key ?>" <?php if ($key == $profileData['abiturientProfileGender']) echo 'checked' ?>
                                               type="radio">
                                        <?= $value ?>
                                    </label>
                                <? endforeach; ?>
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Гражданство</label>
                            <div class="col-md-8">
                                <select id="nationalityCountrySelect"
                                        name="AbiturientProfile[abiturientProfileNationality]" class="form-control" onchange="gohome();">
                                    <?php foreach (Nationality::getValuesArray() as $key => $value): ?>
                                        <option
                                            value="<?= $key ?>" <?php if ($key == $profileData['abiturientProfileNationality']) echo 'selected' ?>><?= $value ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="NationalityCountry">
                            <label class="col-md-4 control-label">Гражданство (укажите страну) </label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileNationalityCountry]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileNationalityCountry'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Дата рождения</label>
                            <div class="col-md-8">
                                <input id="abiturientProfileBirthday"
                                       name="AbiturientProfile[abiturientProfileBirthday]" class="form-control input-md birthday"
                                       type="text" value="<?= $profileData['abiturientProfileBirthday'] ?>">
                                <span class="error-list label label-danger" id="SpanAbiturientProfileBirthday" style="display: none">Это поле необходимо заполнить</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Место рождения</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileBirthplace]" class="form-control input-md" type="text" value="<?= $profileData['abiturientProfileBirthplace'] ?>">
                                <span class="error-list label label-danger" id="SpanabiturientProfileBirthplace" style="display: none">Это поле необходимо заполнить</span>
                            </div>
                        </div>
                        <!--div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Тип регистрации</label>
                            <div class="col-md-8">
                                <select name="AbiturientProfile[IdentityRegisterType]"
                                        class="form-control">
                                    <?php foreach (IdentityRegisterType::getValuesArray() as $key => $value): ?>
                                        <option
                                            value="<?= $key ?>" <?php if ($key == $profileData['IdentityRegisterType']) echo 'selected' ?>><?= $value ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>
                        </di-->
                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Адрес прописки </label>
                            <div class="col-md-8">
                                <div id="klad">
                                    <? $val=implode(" ",$profileData['abiturientProfileRegistrationAddress']); ?>
                                    <input name="cityr" type="text" value="<?=$val[0]?>" placeholder="Город" class="form-control" autocomplete="off" style="float:left; width:37%">
                                    <input name="streetr" type="text" value="<?=$val[1]?>" placeholder="Улица" class="form-control" autocomplete="off" style="float:left; width:33%">
                                    <input name="buildingr" type="text" value="<?=$val[2]?>" placeholder="Дом" class="form-control" autocomplete="off" style="float:left; width:10%">
                                    <input name="building-addr" type="text" value="<?=$val[3]?>" placeholder="Корпус" class="form-control" autocomplete="off" style="float:left; width:10%">
                                    <input name="apartment" type="text" value="<?=$val[4]?>" placeholder="Кв" class="form-control" autocomplete="off" style="float:left; width:10%">
                                </div>
                                <input name="AbiturientProfile[abiturientProfileRegistrationAddress]"
                                       class="form-control input-md" type="hidden"
                                       value="<?= $profileData['abiturientProfileRegistrationAddress'] ?>" id="Registration" readonly>
                                <span class="form-control input-md" id="Registration2" style="display: -webkit-inline-box;    height: 50px;"> <?= $profileData['abiturientProfileRegistrationAddress'] ?> </span>
                                <span class="error-list label label-danger" id="SpanRegistration" style="display: none">Это поле необходимо заполнить</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Адрес проживания по факту</label>
                            <div class="col-md-8">
                                <div id="klad">
                                    <input name="cityp" type="text" value="" placeholder="Город" class="form-control" autocomplete="off" style="float:left; width:37%">
                                    <input name="streetp" type="text" value="" placeholder="Улица" class="form-control" autocomplete="off" style="float:left; width:33%">
                                    <input name="buildingp" type="text" value="" placeholder="Дом" class="form-control" autocomplete="off" style="float:left; width:10%">
                                    <input name="building-addp" type="text" value="" placeholder="Корпус" class="form-control" autocomplete="off" style="float:left; width:10%">
                                    <input name="apartmenp" type="text" value="" placeholder="Кв" class="form-control" autocomplete="off" style="float:left; width:10%">
                                </div>
                                <input name="AbiturientProfile[AddressResidence]"
                                       class="form-control input-md" type="hidden"
                                       value="<?= $profileData['AddressResidence'] ?>" id="Residence" readonly>
                                <span class="form-control input-md" id="Residence2" style="display: -webkit-inline-box;    height: 50px;"> <?= $profileData['AddressResidence'] ?> </span>
                                <span class="error-list label label-danger" id="SpanResidence" style="display: none">Это поле необходимо заполнить</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Адрес прописки совпадает с адресом проживания по факту</label>
                            <div class="col-md-8">
                                <div class="checkbox">
                                    <label for="checkboxes">
                                        <input
                                            id="AddressProp"
                                            type="checkbox"
                                            onchange="CopyAddress();"
                                            <?if($profileData['abiturientProfileRegistrationAddress']== $profileData['AddressResidence']) echo 'checked'?>
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">ИНН</label>
                            <div class="col-md-8">
                                <input <? if($arResult['EroorInn']==1){?>style="border-color:#d9534f"<?}?> name="AbiturientProfile[abiturientProfileINN]" class="form-control input-md inn"
                                       type="text" value="<? if($profileData['abiturientProfileINN']) echo $profileData['abiturientProfileINN']?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group" id="SNILS">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Номер СНИЛС</label>
                            <div class="col-md-8">
                                <input <? if($arResult['EroorSnils']==1){?>style="border-color:#d9534f"<?}?> name="AbiturientProfile[abiturientProfileSNILS]" class="form-control input-md"
                                       type="text" value="<?=$profileData['abiturientProfileSNILS'];?>"
                                       id="abiturientProfileSNILS">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">СНИЛС отсутствует</label>
                            <div class="col-md-8">
                                <div class="checkbox">
                                    <label for="checkboxes">
                                        <input
                                            id="AcceptSNILS"
                                            name="AcceptSNILS"
                                            type="checkbox"
                                            onclick="avaliable($('#AcceptSNILS'),$('#SNILS')); snilsrequired()"
                                            <?if(isset($profileData['abiturientProfileSNILS']) && empty($profileData['abiturientProfileSNILS'])) echo 'checked'?>
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Тип документа, удостоверяющего личность</label>
                            <div class="col-md-8">
                                <select name="AbiturientProfile[abiturientProfileIdentityDocumentType]"
                                        class="form-control" onblur="mask()">
                                    <?php foreach (IdentityDocumentType::getValuesArray() as $key => $value): ?>
                                        <option
                                            value="<?= $key ?>" <?php if ($key == $profileData['abiturientProfileIdentityDocumentType']) echo 'selected' ?>><?= $value ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Серия документа, удостоверяющего личность</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileIdentityDocumentSeries]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileIdentityDocumentSeries'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Номер документа, удостоверяющего личность</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileIdentityDocumentNumber]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileIdentityDocumentNumber'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Документ выдан</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileIdentityDocumentIssuedBy]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileIdentityDocumentIssuedBy'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Дата выдачи документа</label>
                            <div class="col-md-8">
                                <input id="abiturientProfileIdentityDocumentIssuedDate"
                                       name="AbiturientProfile[abiturientProfileIdentityDocumentIssuedDate]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileIdentityDocumentIssuedDate'] ?>">
                                <span class="error-list label label-danger" id="SpanAbiturientProfileIdentityDocumentIssuedDate" style="display: none">Это поле необходимо заполнить</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Код подразделения, выдавшего документ</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileIdentityDocumentIssuedCode]"
                                       class="form-control input-md kodpod" type="text"
                                       value="<?= $profileData['abiturientProfileIdentityDocumentIssuedCode'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab-education">

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Вид образовательной организации</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileEducationOrganizationType]"
                                       id="typeOrg"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileEducationOrganizationType'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Город образовательной организации</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileEducationOrganizationCity]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileEducationOrganizationCity'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Название образовательной организации</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileEducationOrganizationName]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileEducationOrganizationName'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Номер образовательной организации</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileEducationOrganizationNumber]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileEducationOrganizationNumber'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Дата окончания обучения</label>
                            <div class="col-md-8">
                                <input id="abiturientProfileEducationCompletionDate"
                                       name="AbiturientProfile[abiturientProfileEducationCompletionDate]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileEducationCompletionDate'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Вид документа об образовании</label>
                            <div class="col-md-8">
                                <select name="AbiturientProfile[abiturientProfileEducationDocumentType]"
                                        class="form-control">
                                    <?php foreach (EducationDocumentType::getValuesArray() as $key => $value): ?>
                                        <option
                                            value="<?= $key ?>" <?php if ($key == $profileData['abiturientProfileEducationDocumentType']) echo 'selected' ?>><?= $value ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Серия документа об образовании</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileEducationDocumentSeries]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileEducationDocumentSeries'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Номер документа об образовании</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileEducationDocumentNumber]"
                                       class="form-control input-md obrnum" type="text"
                                       value="<?= $profileData['abiturientProfileEducationDocumentNumber'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Средний балл аттестата</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileCAS]" class="form-control input-md ballat"
                                       type="text" value="<?= $profileData['abiturientProfileCAS'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Окончил школу с медалью</label>
                            <div class="col-md-8">
                                <div class="checkbox">
                                    <label for="checkboxes">
                                        <input
                                            name="AbiturientProfile[abiturientProfileGraduatedWithHonours]"
                                            <?php if ($profileData['abiturientProfileGraduatedWithHonours']) echo 'checked' ?>
                                            value="1"
                                            type="checkbox"
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Призёр олимпиады</label>
                            <div class="col-md-8">
                                <div class="checkbox">
                                    <label for="checkboxes">
                                        <input
                                            name="AbiturientProfile[abiturientProfileOlympiadWinner]"
                                            <?php if ($profileData['abiturientProfileOlympiadWinner']) echo 'checked' ?>
                                            value="1"
                                            type="checkbox"
                                            id="isOlympiadWinnerCheckbox"
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="abiturientProfileOlympiadWinnerString">
                            <label class="col-md-4 control-label">Название олимпиады </label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileOlympiadWinnerString]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileOlympiadWinnerString'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Образование</label>
                            <div class="col-md-8">
                                <select name="AbiturientProfile[abiturientProfileEducation]"
                                        id="ProfileEducation"
                                        class="form-control"
                                        aria-invalid="false"
                                        onchange="ProfileEdu($(this).val())">
                                    <?php foreach (BaseEducation::getValuesArray() as $key => $value): ?>
                                        <option value="<?=$key?>" <?php if ($key == $profileData['abiturientProfileEducation']) echo 'selected="selected"';?> ><?=$value?></option>
                                    <? endforeach; ?>
                                </select>

                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Ранее не получал среднее профессиональное
                                образование</label>
                            <div class="col-md-8">
                                <div class="checkbox">
                                    <label for="checkboxes">
                                        <input
                                            name="AbiturientProfile[abiturientProfileFirstTimeEnrolment]"
                                            id="TimeEnrolment"                                            
                                            <?php if ($profileData['abiturientProfileFirstTimeEnrolment']) echo 'checked="checked"' ?>
                                            value="1"
                                            type="checkbox"
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Изучаемый язык</label>
                            <div class="col-md-8">
                                <?php foreach (AdditionalLanguage::getValuesArray() as $key => $value): ?>
                                    <label class="radio-inline">
                                        <input name="AbiturientProfile[abiturientProfileAdditionalLanguage]"
                                               value="<?= $key ?>" <?php if ($key == $profileData['abiturientProfileAdditionalLanguage']) echo 'checked' ?>
                                               type="radio">
                                        <?= $value ?>
                                    </label>
                                <? endforeach; ?>
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab-insurance">

                        <div class="form-group">
                            <label class="col-md-4 control-label">Наименование медицинского страхователя</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileInsuranceCompanyName]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileInsuranceCompanyName'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Номер медицинской страховки</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileInsuranceNumber]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileInsuranceNumber'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Серия медицинской страховки</label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileInsuranceSeries]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileInsuranceSeries'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Дата окончания действия медицинской страховки</label>
                            <div class="col-md-8">
                                <input id="abiturientProfileInsuranceDate"
                                       name="AbiturientProfile[abiturientProfileInsuranceDate]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileInsuranceDate'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>

                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab-additional">

                        <div class="form-group">
                            <label class="col-md-4 control-label">Состою на воинском учёте</label>
                            <div class="col-md-8">
                                <div class="checkbox">
                                    <label for="checkboxes">
                                        <input
                                            name="AbiturientProfile[abiturientProfileIsReservist]"
                                            id="isReservistCheckbox" <?php if ($profileData['abiturientProfileIsReservist']) echo 'checked' ?>
                                            value="1"
                                            type="checkbox"
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="militaryDocumentAttributes">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Серия приписного свидетельства</label>
                                <div class="col-md-8">
                                    <input name="AbiturientProfile[abiturientProfileMilitaryDocumentSeries]"
                                           class="form-control input-md" type="text"
                                           value="<?= $profileData['abiturientProfileMilitaryDocumentSeries'] ?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Номер приписного свидетельства</label>
                                <div class="col-md-8">
                                    <input name="AbiturientProfile[abiturientProfileMilitaryDocumentNumber]"
                                           class="form-control input-md" type="text"
                                           value="<?= $profileData['abiturientProfileMilitaryDocumentNumber'] ?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Район приписки</label>
                                <div class="col-md-8">
                                    <input name="AbiturientProfile[abiturientProfileMilitaryDocumentRegion]"
                                           class="form-control input-md" type="text"
                                           value="<?= $profileData['abiturientProfileMilitaryDocumentRegion'] ?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Имеется трудовой стаж</label>
                            <div class="col-md-8">
                                <div class="checkbox">
                                    <label for="checkboxes">
                                        <input
                                            name="AbiturientProfile[abiturientProfileSeniority]"
                                            <?php if ($profileData['abiturientProfileSeniority']) echo 'checked' ?>
                                            value="1"
                                            type="checkbox"
                                            id="hasSeniorityCheckbox"
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="abiturientProfileSeniorityString">
                            <label class="col-md-4 control-label">Место работы и должность </label>
                            <div class="col-md-8">
                                <input name="AbiturientProfile[abiturientProfileSeniorityString]"
                                       class="form-control input-md" type="text"
                                       value="<?= $profileData['abiturientProfileSeniority'] ?>">
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>




                        <div class="form-group">
                            <label class="col-md-4 control-label">Дополнительная информация</label>
                            <div class="col-md-8">
                                <textarea name="AbiturientProfile[abiturientProfileAdditionalData]" class="form-control"
                                          rows="5"><?= $profileData['abiturientProfileAdditionalData'] ?></textarea>
                                <span class="error-list label label-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab-files">

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="exampleInputFile">Паспорт</label>
                            <div class="col-md-8">
                                <input type="file" name="AbiturientProfile[identityDocumentScanFile]" class="passportimg">

                                <span class="help-block">
                                    Скан-копия паспорта (первый разворот)
                                    <?php if (!empty($profileData['abiturientProfileIdentityDocumentScanFileLink'])): ?>
                                        <a href="#" data-toggle="modal" data-target="#myIdentityDocument">Просмотреть
                                            загруженный файл </a>
                                    <?php endif; ?>
                                    <div class="modal fade" id="myIdentityDocument" aria-labelledby="myModalLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">Копия паспорта/свидетельства</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Титульная страница паспорта/свидетельства</p>
                                                    <img
                                                        src="<?= $profileData['abiturientProfileIdentityDocumentScanFileLink'] ?>"
                                                        alt="" width="100%">
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div>
                                    <input class="passportview" type="hidden" value="<?php if (!empty($profileData['abiturientProfileIdentityDocumentScanFileLink'])) echo 1; else echo 0;?>">
                                    <span class="error-list label label-danger"></span>
                                </span>

                                <input type="file" name="AbiturientProfile[identityDocumentRegistrationScanFile]" class="passportregimg">

                                <span class="help-block">
                                    Скан-копия паспорта (прописка)
                                    <?php if (!empty($profileData['abiturientProfileIdentityDocumentRegistrationScanFileLink'])): ?>
                                        <a href="#" data-toggle="modal" data-target="#myIdentityDocumentRegistration">Просмотреть
                                            загруженный файл </a>
                                    <?php endif; ?>
                                    <div class="modal fade" id="myIdentityDocumentRegistration" aria-labelledby="myModalLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">Копия паспорта/свидетельства</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Cтраница регистрации паспорта/свидетельства</p>
                                                    <img
                                                        src="<?= $profileData['abiturientProfileIdentityDocumentRegistrationScanFileLink'] ?>"
                                                        alt="" width="100%">
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div>
                                    <input class="passportregview" type="hidden" value="<?php if (!empty($profileData['abiturientProfileIdentityDocumentRegistrationScanFileLink'])) echo 1; else echo 0;?>">
                                    <span class="error-list label label-danger"></span>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="exampleInputFile">ИНН</label>
                            <div class="col-md-8">
                                <input type="file" name="AbiturientProfile[INNScanFile]" class="innimg">
                                <span class="help-block">
                                    Скан-копия ИНН (лицевая сторона)
                                    <?php if (!empty($profileData['abiturientProfileINNScanFile'])): ?>
                                        <a href="#" data-toggle="modal" data-target="#myINN">Просмотреть загруженный
                                            файл </a>
                                    <?php endif; ?>
                                    <div class="modal fade" id="myINN" aria-labelledby="myModalLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">Копия ИНН</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <p>ИНН</p>
                                                    <img src="<?= $profileData['abiturientProfileINNScanFile'] ?>"
                                                         alt="" width="100%">
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div>
                                    <input class="innview" type="hidden" value="<?php if (!empty($profileData['abiturientProfileINNScanFile'])) echo 1; else echo 0;?>">
                                    <span class="error-list label label-danger"></span>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="exampleInputFile">СНИЛС</label>
                            <div class="col-md-8">
                                <input type="file" name="AbiturientProfile[SNILSScanFile]" class="snilsimg">
                                <span class="help-block">
                                    Скан-копия СНИЛС (лицевая сторона)
                                    <?php if (!empty($profileData['abiturientProfileSNILSScanFile'])): ?>

                                        <a href="#" data-toggle="modal" data-target="#mySNILSS">Просмотреть загруженный
                                            файл </a>
                                    <?php endif; ?>
                                    <div class="modal fade" id="mySNILSS" aria-labelledby="myModalLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">Копия СНИЛС</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <p>СНИЛС</p>
                                                    <img src="<?= $profileData['abiturientProfileSNILSScanFile'] ?>"
                                                         alt="" width="100%">
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div>
                                     <input class="snilsview" type="hidden" value="<?php if (!empty($profileData['abiturientProfileSNILSScanFile'])) echo 1; else echo 0; ?>">
                                    <span class="error-list label label-danger"></span>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="exampleInputFile">Аттестат</label>
                            <div class="col-md-8">
                                <input type="file" name="AbiturientProfile[educationDocumentScanFile]" class="atistatimg">
                                <span class="help-block">
                                    Скан-копия аттестата о начальном образовании
                                    <?php if (!empty($profileData['abiturientProfileEducationDocumentScanFile'])): ?>

                                        <a href="#" data-toggle="modal" data-target="#myAttestat">Просмотреть
                                            загруженный файл </a>
                                    <?php endif; ?>
                                    <div class="modal fade" id="myAttestat" aria-labelledby="myModalLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">Копия аттестата</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Титульная страница аттестата</p>
                                                    <img
                                                        src="<?= $profileData['abiturientProfileEducationDocumentScanFile'] ?>"
                                                        alt="" width="100%">
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div>
                                     <input class="atistatview" type="hidden" value="<?php if (!empty($profileData['abiturientProfileEducationDocumentScanFile'])) echo 1; else echo 0; ?>">
                                    <span class="error-list label label-danger"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?
                        if(isset($profileData['abiturientProfileParent'][3])) $n=3;
                        if(isset($profileData['abiturientProfileParent'][1])) $n=1;
                    ?>
                    
                    <div role="tabpanel" class="tab-pane" id="tab-parents">
                        <div class="form-group">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i></label>
                            <div class="col-md-8">
                                <?php foreach (Parents::getValuesArray() as $key => $value): ?>
                                    <label class="radio-inline <?=Parents::getClassArray()[$key]?>">
                                        <input name="AbiturientProfile[abiturientParents]"
                                               class="<?=Parents::getClassArray()[$key]?> parents ppp<?= $key ?>"
                                               value="<?= $key ?>" <?php if ($key == $n) echo 'checked' ?>
                                               type="radio" onclick="parenttypechange()">
                                        <?= $value ?>
                                        <span class="error-list label label-danger"></span>
                                    </label>
                                <? endforeach; ?>

                            </div>
                        </div>
                        <div class="form-group" id="parenttype">
                            <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i></label>
                            <div class="col-md-8">
                                <div class="checkbox parent">
                                    <?php foreach (ParentsType::getValuesArray() as $key => $value): ?>
                                        <label for="checkboxes">
                                            <input name="AbiturientProfile[abiturientParentsType]"
                                                   value="<?= $key ?>" <?php if (isset($profileData['abiturientProfileParent'][$key])) echo 'checked' ?>
                                                   type="checkbox"
                                                   class="parentdel"
                                                   id="parent<?= $key ?>"
                                            >
                                            <?= $value ?>
                                            <span class="error-list label label-danger"></span>
                                        </label>
                                    <? endforeach; ?>

                                </div>
                            </div>
                        </div>
                         
                        <div id="firstparent">
                            <input type="hidden" name="AbiturientProfileParent[idparent][0]" value="<?=$profileData['abiturientProfileParent'][$n]['id'];?>">
                            <div class="form-group">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>ФИО <scan id="NameParent">матери</scan></label>
                                <div class="col-md-8">
                                    <input class="form-control input-md maza" type="text" size="40" name="AbiturientProfileParent[fio][0]" value="<?=$profileData['abiturientProfileParent'][$n]['fio'];?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Гражданство</label>
                                <div class="col-md-8">
                                    <select id="nationalityCountrySelect"
                                            name="AbiturientProfileParent[citizenship][0]" class="form-control">
                                        <?php foreach (Nationality::getValuesArray() as $key => $value): ?>
                                            <option
                                                value="<?= $key ?>" <?php if ($key == $profileData['abiturientProfileParent'][$n]['citizenship']) echo 'selected' ?>><?= $value ?></option>
                                        <? endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Дата рождения</label>
                                <div class="col-md-8">
                                    <input id="abiturientProfileBirthdayParentMama"
                                           name="AbiturientProfileParent[birthdate][0]" class="form-control input-md birthday birthdate1"
                                           type="text" value="<?if(!empty($profileData['abiturientProfileParent'][$n]['birthdate'])) echo date("d-m-Y",strtotime($profileData['abiturientProfileParent'][$n]['birthdate']))?>">
                                    <span class="error-list label label-danger" id="SpanabiturientProfileBirthdayParentMama" style="display: none">Это поле необходимо заполнить</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Номер телефона</label>
                                <div class="col-md-8">
                                    <input class="form-control input-md phone" type="text" name="AbiturientProfileParent[Phone][0]" value="<?=$profileData['abiturientProfileParent'][$n]['phone']?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group" id="ParentSNILS1">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Номер СНИЛС</label>
                                <div class="col-md-8">
                                    <input name="AbiturientProfileParent[snils][0]" class="form-control input-md snils msnils"
                                           type="text" value="<?=$profileData['abiturientProfileParent'][$n]['snils']?>"
                                           id="abiturientProfileSNILSMAMA">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">СНИЛС отсутствует</label>
                                <div class="col-md-8">
                                    <div class="checkbox">
                                        <label for="checkboxes">
                                            <input
                                                id="AcceptParentSNILS1"
                                                name="AcceptSNILS"
                                                type="checkbox"
                                                <? if(isset($profileData['abiturientProfileParent'][$n]['snils']) &&  empty($profileData['abiturientProfileParent'][$n]['snils']))  echo 'checked'?>
                                                onclick="avaliable($('#AcceptParentSNILS1'), $('#ParentSNILS1')); $('#abiturientProfileSNILSMAMA').val('')"
                                            >
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Серия паспорта</label>
                                <div class="col-md-8">
                                    <input name="AbiturientProfileParent[docserpers][0]"
                                           class="form-control input-md seriesdoc" type="text"
                                           value="<?=$profileData['abiturientProfileParent'][$n]['docserpers']?>"
                                           >
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Номер паспорта</label>
                                <div class="col-md-8">
                                    <input name="AbiturientProfileParent[docnumpers][0]"
                                           class="form-control input-md nomdoc" type="text"
                                           value="<?=$profileData['abiturientProfileParent'][$n]['docnumpers']?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Документ выдан</label>
                                <div class="col-md-8">
                                    <input id="docissuedpersParent1"
                                           name="AbiturientProfileParent[docissuedpers][0]"
                                           class="form-control input-md docissuedpers" type="text"
                                           value="<?=$profileData['abiturientProfileParent'][$n]['docissuedpers']?>">
                                    <span class="error-list label label-danger" id="SpandocissuedpersParent1" style="display: none">Это поле необходимо заполнить</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Дата выдачи документа</label>
                                <div class="col-md-8">
                                    <input id="abiturientProfileParentIdentityDocumentIssuedDateMama"
                                           name="AbiturientProfileParent[docdatepers][0]"
                                           class="form-control input-md docdatepers" type="text"
                                           value="<?if(!empty($profileData['abiturientProfileParent'][$n]['docdatepers'])) echo date("d-m-Y",strtotime($profileData['abiturientProfileParent'][$n]['docdatepers']))?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label guard"><i class="fa fa-asterisk"></i>Документ, удостоверяющий положение законного представителя по отношению к ребенку</label>
                                <div class="col-md-8">
                                    <input name="AbiturientProfileParent[dobdocument][0]"
                                           class="form-control input-md guard dobdocument" type="text"
                                           value="<?=$profileData['abiturientProfileParent'][$n]['dobdocument']?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                        </div>
                        <?
                            $n=2;
                        ?>
                        <div id="secondparent">
                            <input type="hidden" name="AbiturientProfileParent[idparent][1]" value="<?=$profileData['abiturientProfileParent'][$n]['id'];?>">
                            <div class="form-group">
                                <label class="col-md-4 control-label">ФИО отца</label>
                                <div class="col-md-8">
                                    <input class="form-control input-md faza" type="text" size="40" name="AbiturientProfileParent[fio][1]" value="<?=$profileData['abiturientProfileParent'][$n]['fio'];?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Гражданство</label>
                                <div class="col-md-8">
                                    <select id="nationalityCountrySelect"
                                            name="AbiturientProfileParent[citizenship][1]" class="form-control">
                                        <?php foreach (Nationality::getValuesArray() as $key => $value): ?>
                                            <option
                                                value="<?= $key ?>" <?php if ($key == $profileData['abiturientProfileParent'][$n]['citizenship']) echo 'selected' ?>><?= $value ?></option>
                                        <? endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Дата рождения</label>
                                <div class="col-md-8">
                                    <input id="abiturientProfileBirthdayParentPapa"
                                           name="AbiturientProfileParent[birthdate][1]" class="form-control input-md birthdate2 birthday"
                                           type="text" value="<?if(!empty($profileData['abiturientProfileParent'][$n]['birthdate'])) echo date("d-m-Y",strtotime($profileData['abiturientProfileParent'][$n]['birthdate']))?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Номер телефона</label>
                                <div class="col-md-8">
                                    <input class="form-control input-md phone" type="text" name="AbiturientProfileParent[Phone][1]" value="<?=$profileData['abiturientProfileParent'][$n]['phone']?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group" id="ParentSNILS2">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Номер СНИЛС</label>
                                <div class="col-md-8">
                                    <input name="AbiturientProfileParent[snils][1]" class="form-control input-md snils fsnils"
                                           type="text" value="<?=$profileData['abiturientProfileParent'][$n]['snils']?>"
                                           id="abiturientProfileParentSNILSPAPA">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">СНИЛС отсутствует</label>
                                <div class="col-md-8">
                                    <div class="checkbox">
                                        <label for="checkboxes">
                                            <input
                                                id="AcceptParentSNILS2"
                                                name="AcceptParentSNILS2"
                                                type="checkbox"
                                                <?if(isset($profileData['abiturientProfileParent'][$n]['snils']) &&  empty($profileData['abiturientProfileParent'][$n]['snils']))  echo 'checked'?>
                                                onclick="avaliable($('#AcceptParentSNILS2'), $('#ParentSNILS2')); $('#abiturientProfileParentSNILSPAPA').val('')"
                                            >
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Серия паспорта</label>
                                <div class="col-md-8">
                                    <input name="AbiturientProfileParent[docserpers][1]"
                                           class="form-control input-md seriesdoc2" type="text"
                                           value="<?=$profileData['abiturientProfileParent'][$n]['docserpers']?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Номер паспорта</label>
                                <div class="col-md-8">
                                    <input name="AbiturientProfileParent[docnumpers][1]"
                                           class="form-control input-md nomdoc2" type="text"
                                           value="<?=$profileData['abiturientProfileParent'][$n]['docnumpers']?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Документ выдан</label>
                                <div class="col-md-8">
                                    <input name="AbiturientProfileParent[docissuedpers][1]"
                                           class="form-control input-md docissuedpers2" type="text"
                                           value="<?=$profileData['abiturientProfileParent'][$n]['docissuedpers']?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Дата выдачи документа</label>
                                <div class="col-md-8">
                                    <input id="abiturientProfileParentIdentityDocumentIssuedDatePapa"
                                           name="AbiturientProfileParent[docdatepers][1]"
                                           class="form-control input-md docdatepers2" type="text"
                                           value="<?if(!empty($profileData['abiturientProfileParent'][$n]['docdatepers'])) echo date("d-m-Y",strtotime($profileData['abiturientProfileParent'][$n]['docdatepers']))?>">
                                    <span class="error-list label label-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?if(empty($profileData['NameOrgVer'])){?>
                <div class="form-group">
                    <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i><a href="/abiturient-office/agreement.php" target="_blank">Согласие на обработку персональных данных</a></label>
                    <div class="col-md-8">
                        <div class="checkbox">
                            <label for="checkboxes">
                                <input
                                    name="Accept"
                                    type="checkbox" onclick="persdan()"
                                    <?php if (!empty($profileData['abiturientProfileIdentityDocumentSeries'])) echo 'checked' ?>
                                >
                                <span class="error-list label label-danger"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <?}
                else
                {?>
                <div class="form-group">
                    <label class="col-md-4 control-label"><i class="fa fa-asterisk"></i>Профиль проверен</label>
                    <div class="col-md-8">
                        <?if(empty($profileData['NameOrgVer'])){?>
                            <div class="checkbox">
                                <label for="checkboxes">
                                    <input type="checkbox" name="verification">
                                    <span class="error-list label label-danger"></span>
                                </label>
                            </div>
                        <?}else{?>
                            <div class="col-md-8 control-label">
                                <label> <?=$profileData['NameOrgVer'];?> </label>
                            </div>
                        <?}?>
                    </div>
                </div>
                <?}?>
                <div class="col-md-offset-4 col-md-8">
                    <input type="hidden" name="AbiturientProfileParent[NameOrgVer]" value="<?=$profileData['NameOrgVer']?>">
                    <button id="form-submit-btn" class="btn btn-info" type="submit" disabled onclick="validatebitch()"><i class="fa fa-check"></i>Сохранить профиль</button>
                </div>
                <div class="col-md-offset-4 col-md-8">
                    Если у вас возникли проблемы с заполнением анкеты, мы готовы вам помочь! Телефон для связи: <tel>777 77 77</tel>, <a href="mailto:sham@gkomega.ru?subject=Помощь по заполнению профиля">наша почта</a>
                </div>
                <!--/fieldset-->
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    <? if(!empty($profileData['NameOrgVer'])):?>
        $('input').prop('disabled', true);
        $('select').prop('disabled', true);
        $('button').prop('disabled', true);
    <?endif;?>
    errorList = <?=json_encode(isset($arResult['errors']) ? $arResult['errors'] : array());?>;
    nationalityNONE = <?= Nationality::NONE?>;
    nationalityRU = <?= Nationality::RU?>;
    <?if (is_array($profileData['abiturientProfileParent'])):?>
        parenttypechange();
    <?endif;?>
    fullparent();
    function gohome() {
        var val =$('#nationalityCountrySelect').val();
        if (val!=1)
        {
            alert('Подача заявления иностранцем осуществляется через учебную организацию.');
            $('input').prop('disabled', true);
            $('select').prop('disabled', true);
            $('button').prop('disabled', true);
        }
    }
    <?php if (!empty($profileData['abiturientProfileIdentityDocumentSeries'])) echo 'persdan();
    ' ?>
    $(document).ready(function() {
        avaliable($('#AcceptParentSNILS2'), $('#ParentSNILS2'));
        <?if(empty($profileData['abiturientProfileSNILS'])) echo "snilsrequired();
        avaliable($('#AcceptSNILS'),$('#SNILS'));
        "?>
        <?if(empty($profileData['abiturientProfileParent'][0]['snils'])) echo "
        
        avaliable($('#AcceptParentSNILS1'), $('#ParentSNILS1'));
        "?>
        initFormValidation();
//Здесь функция function DleTrackDownload

    });
</script>