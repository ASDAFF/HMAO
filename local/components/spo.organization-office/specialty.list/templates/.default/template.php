<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    use Spo\Site\Helpers\PagingHelper;
    use Spo\Site\Helpers\OrganizationOfficeUrlHelper as Url;
    use Spo\Site\Dictionaries\StudyMode;
    use Spo\Site\Dictionaries\BaseEducation;
    use Spo\Site\Dictionaries\AdaptationType;

    /**
     * @var $APPLICATION
     * @var $arResult
     * @var $paging PagingHelper
     */

    $specialtiesList   = $arResult['specialtiesList']['list'];
    $freeSpecialtyList = $arResult['freeSpecialtyList']['list'];
    $organizationId    = $arResult['organizationId'];
    $baseEducationList = $arResult['baseEducationList'];
    $studyModeList     = $arResult['studyModeList'];
    $applicationCount  = $arResult['applicationCount'];
    $disciplineList    = $arResult['disciplineList'];
    $examTypeList      = $arResult['examTypeList'];
    $trainingLevelList = $arResult['trainingLevelList'];
    $trainingTypeList  = $arResult['trainingTypeList'];
    $adress             = $arResult['adress'];

    //$paging          = $arResult['paging'];

    //$currentPage = $paging->getCurrentPage();
    //$pageCount   = $paging->getPageCountByTotalRecordCount($totalCount);
    //\Spo\Site\Util\CVarDumper::dump($freeSpecialtyList);exit;
?>
<div class="specialty-list">

    <div class="error-place"></div>

    <h2 class="page-header">Образовательные программы организации</h2>

    <p class="text-muted">
        В данном разделе вы можете управлять образовательными программами организациями, набор на которые осуществляется в образовательной организации.<br/>
        Выбрав программу, абитуриенты смогут подать заявки.
    </p>

    <div class="modal fade dialog-form-container" id="organization-specialty-editor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

        <div class="modal-dialog"  style="width: 633px;">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel" style="margin:0;">Добавить образовательную программу</h4>
                </div>

                <div class="modal-body">
                    <div class="error-list">

                    </div>
                    <form>
                        <div class="form-group">
                            <label>Специальность</label>
                            <select style="width: 300px;" class="specialty-id-fld form-control">
                                <?foreach($freeSpecialtyList as $specialty){?>
                                    <option value="<?=$specialty['id']?>" data-qualifications='<?=json_encode($specialty['qualifications'])?>'>
                                        <?='(' . $specialty['code'] . ') ' . $specialty['title']?>
                                    </option>
                                <?}?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Квалификации</label>
                            <div class="qualification-list">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Базовое образование</label> <!--  для подачи заявки на данную специальность -->
                            <select class="specialty-base-education-fld form-control">
                                <?foreach($baseEducationList as $id=>$val){?>
                                    <option value="<?=$id?>">
                                        <?=$val?>
                                    </option>
                                <?}?>
                            </select>
                            <span class="help-block">Образование, необходимое для подачи заявки на данную специальность</span>
                        </div>

                        <div class="form-group">
                            <label>Уровень обучения</label>
                            <select class="training-level-fld form-control">
                                <?foreach($trainingLevelList as $id=>$val){?>
                                    <option value="<?=$id?>">
                                        <?=$val?>
                                    </option>
                                <?}?>
                            </select>
                            <span class="help-block"></span>
                        </div>

                        <div class="form-group">
                            <label>Программа подготовки</label>
                            <select class="training-type-fld form-control">
                                <?foreach($trainingTypeList as $id=>$val){?>
                                    <option value="<?=$id?>">
                                        <?=$val?>
                                    </option>
                                <?}?>
                            </select>
                            <span class="help-block"></span>
                        </div>

                        <div class="form-group">
                            <label>Форма обучения</label>
                            <select class="specialty-study-mode-fld form-control">
                                <?foreach($studyModeList as $id=>$val){?>
                                    <option value="<?=$id?>">
                                        <?=$val?>
                                    </option>
                                <?}?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Период обучения (месяцев)</label>
                            <input type="text" class="form-control study-period-fld" />
                        </div>

<!--                        <div class="form-group">-->
<!--                            <label>План приема</label>-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-4">-->
<!--                                    Кол-во человек-->
<!--                                    <input type="text"  class="form-control planned-abiturients-count-fld" />-->
<!--                                </div>-->
<!--                                <div class="col-md-4">-->
<!--                                    Кол-во групп-->
<!--                                    <input type="text"  class="form-control planned-groups-count-fld" />-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->

                        <br />

                        <?php // TODO Устанавливать видимость в зависимости от пришедших данных ?>
                        <div class="form-group">
                            <label class="control-label" for="radios">Программа адаптирована для лиц с ОВЗ</label>
                            <div class="">
                                <label class="radio-inline" for="checkboxNotAdapted">
                                    <input name="adapted" id="checkboxNotAdapted" value="0" checked="checked" type="radio">
                                    Нет
                                </label>
                                <label class="radio-inline" for="checkboxAdapted">
                                    <input name="adapted" id="checkboxAdapted" value="1" type="radio">
                                    Да
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="adaptation-type-select-box" hidden="hidden">
                            <label class="control-label" for="checkboxes">Тип адаптации</label>
                            <div>
                                <?php foreach(AdaptationType::getValuesArray() as $key => $value) {?>
                                    <div class="checkbox">
                                        <label for="checkboxes-0">
                                            <input class="adaptation-type-checkbox" name="adaptationTypes[]" value="<?= $key ?>" type="checkbox">
                                            <?= $value ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <br />
                        <br />
                        <input type="hidden" id="adress" value="<?=$adress;?>">
                        <div class="form-group">
                            <label>Вступительные экзамены</label> <button type="button" class="add-exam btn btn-success btn-xs">Добавить</button>
                            <table id="exam" style="display:none;">
                                <th>
                                    Дисциплина
                                </th>
                                <th>
                                    Тип экзамена
                                </th>
                                <th>
                                    Дата проведения экзамена
                                </th>
                                <th>
                                    Место проведения
                                </th>
                                <th>
                                </th>
                                <tbody class="exam-list">
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-info bind-specialty-request-btn" data-url="<?=Url::toAddEducationalProgram($organizationId)?>" data-update-url="<?=Url::toUpdateEducationalProgram()?>"><i class="fa fa-check"></i> Сохранить программу</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                </div>

            </div>
        </div>
    </div>

    <p>
        <button type="button" style="display: none" class="btn-add-specialty btn btn-info" data-toggle="modal" data-target=".dialog-form-container"><i class="fa fa-plus"></i> &nbsp;Добавить образовательную программу</button>
    </p>

    <table id="specialty-list" class="table">
        <thead>
            <tr>
                <th>ID спец.</th>
                <th>Код</th>
                <th>Специальность</th>
                <th>ID прогр.</th>
                <th>Базовое образование</th>
                <th>Форма обучения</th>
                <th style="text-align: center">Подано заявок</th>
                <?/*<th>Статус</th>*/?>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <?foreach($specialtiesList as $specialty){
            $orgSpecialties = $specialty['organizationSpecialties'];
            $rowspan = count($orgSpecialties);

            foreach($orgSpecialties as $index => $orgSpecialty){?>
                <tr>
                    <?if($index === 0){?>
                        <td rowspan="<?=$rowspan?>"><?=$specialty['specialtyId']?></td>
                        <td rowspan="<?=$rowspan?>"><?=$specialty['specialtyCode']?></td>
                        <td rowspan="<?=$rowspan?>"><?=$specialty['specialtyTitle']?></td>
                    <?}?>
                    <td><?=$orgSpecialty['organizationSpecialtyId']?></td>
                    <td><?=BaseEducation::getValue($orgSpecialty['organizationSpecialtyBaseEducation'])?></td>
                    <td><?=StudyMode::getValue($orgSpecialty['organizationSpecialtyStudyMode'])?></td>
                    <td style="text-align: center"><?=$applicationCount[$orgSpecialty['organizationSpecialtyId']]?></td>
                    <?/*<td><?=$orgSpecialty['organizationSpecialtyStatus']?></td>*/?>
                    <? if($arResult['NeModerator']==1){?>
                    <td>
                        <a class="edit-program-btn btn btn-xs" href="<?=Url::toLoadEducationalProgram($orgSpecialty['organizationSpecialtyId'])?>"><i class="fa fa-times"></i> Изменить</a>
                    </td>
                    <td>
                        <a class="delete-program-btn btn btn-danger btn-xs" href="<?=Url::toDeleteEducationalProgram($orgSpecialty['organizationSpecialtyId'], $organizationId)?>"><i class="fa fa-times"></i> Удалить</a>
                    </td>
                    <? }?>
                </tr>
            <?}?>
        <?}?>
        </tbody>
    </table>

    <script>
        var disciplineList = <?=json_encode($disciplineList)?>;
        var examTypeList   = <?=json_encode($examTypeList)?>;
    </script>
</div>