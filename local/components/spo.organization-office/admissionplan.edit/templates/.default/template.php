<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    use Spo\Site\Helpers\PagingHelper;
    use Spo\Site\Helpers\OrganizationOfficeUrlHelper as Url;
    use Spo\Site\Dictionaries\StudyMode;
    use Spo\Site\Dictionaries\BaseEducation;
    use Spo\Site\Dictionaries\AdmissionPlanStatus;

    /**
     * @var $APPLICATION
     * @var $arResult
     * @var $paging PagingHelper
     */

    $year = $arResult['year'];
    $admissionPlan = $arResult['admissionPlan'];
?>
<?php if (isset($arResult['errors'])) {?>
<div class="alert alert-danger">
    Не удалось установить план приёма для выбранной специальности:
    <ul>
    <?php foreach($arResult['errors'] as $error) {?>
        <li><?=$error['message']?></li>
    <?php } ?>
    </ul>
</div>
<?php } ?>

<h2 class="page-header">План приёма на <?=$year?> год</h2>

<form id="formSelectorForm" class="form-inline" method="get" action="">
    <fieldset>
        <div class="form-group group-lg">
            <label class="col-md-1 control-label" for="selectbasic">Год </label>
            <div class="col-md-3">
                <select id="yearSelector" name="year" class="form-control">
                    <?php $currentYear = (integer) date('Y');?>
                    <?php for ($i = 2000; $i < $currentYear + 5; $i++) { ?>
                        <option value="<?=$i?>" <?= ($i == $year) ? 'selected' : ''?>><?=$i?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </fieldset>
</form>
<br />

<?php foreach ($admissionPlan['activeSpecialties'] as $p) {?>

<div class="well">
    <div class="row">
        <div class="col-md-4">
            <p><strong><?= $p['specialtyTitle'] ?> (<?= $p['specialtyCode'] ?>)</strong></p>
            <p><?= BaseEducation::getValue($p['organizationSpecialtyBaseEducation'])?></p>
            <p><?= StudyMode::getValue($p['organizationSpecialtyStudyMode'])?></p>
        </div>
        <div class="col-md-4">
            <?php if (empty($p['admissionPlan'])) { ?>
            План приёма не определён
            (<a href="" class="edit-admission-plan-link" data-organization-specialty-id="<?=$p['organizationSpecialtyId']?>" data-toggle="modal" data-target=".dialog-form-container">создать</a>)

            <?php } else { ?>
            <?php if ($p['admissionPlan']['status'] != AdmissionPlanStatus::ACCEPTED) {?>
                    <? if($arResult['NeModerator']==1){?>
                    <a href="" class="edit-admission-plan-link" data-toggle="modal" data-organization-specialty-id="<?=$p['organizationSpecialtyId']?>" data-target=".dialog-form-container">
                        Изменить
                    </a>
                    <? }?>
            <?php }?>
                <div id="admissionPlan<?=$p['organizationSpecialtyId']?>">
                    Количество бюджетных мест: <span class="grantStudentsNumber"><?= $p['admissionPlan']['grantStudentsNumber'] ?></span><br>
                    Количество коммерческих мест: <span class="tuitionStudentsNumber"><?= $p['admissionPlan']['tuitionStudentsNumber'] ?></span><br>
                    Дата начала приёма: <span class="startDate"><?= $p['admissionPlan']['startDate'] ?></span><br>
                    Дата завершения приёма: <span class="endDate"><?= $p['admissionPlan']['endDate'] ?></span><br>
                    <?php if ($p['admissionPlan']['status'] == AdmissionPlanStatus::CREATED) {?>
                    <p class="text-warning">
                    <?php } elseif ($p['admissionPlan']['status'] == AdmissionPlanStatus::ACCEPTED) {?>
                    <p class="text-success">
                    <?php } else { ?>
                    <p class="text-danger">
                    <?php }?>
                    Статус: <?= AdmissionPlanStatus::getValue($p['admissionPlan']['status']);?>
                    </p>
                </div>
            <?php } ?>
        </div>
        <div class="col-md-4">
            Последние изменения: <br />
            <?php
            $lastEvent = $p['admissionPlan']['lastEvent'];
            if (!empty($lastEvent)) {
            ?>

            Дата: <?= $lastEvent['date'] ?><br />
            Статус: <?= AdmissionPlanStatus::getValue($lastEvent['status']) ?><br />
            Комментарий: <?= $lastEvent['comment'] ?><br />

            <?php } else {?>
            -
            <?php }?>
        </div>
    </div>
</div>
<?php } ?>

<hr>
<p class="text-muted">
    В данном разделе вы можете управлять планом приёма по программам обучения.
</p>

<div class="modal fade dialog-form-container" id="admission-plan-editor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" method="post" action="<?=Url::toAdmissionPlanEdit($year)?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel" style="margin:0;">План приёма</h4>
                </div>

                <div class="modal-body">
                    <div class="error-list"></div>
                    <fieldset>
                        <input id="inputOrganizationSpecialtyId" name="admissionPlan[organizationSpecialtyId]" hidden="" type="text" value="">
                        <input name="admissionPlan[year]" hidden="" type="text" value="<?=$year?>">

                        <div class="form-group">
                            <label class="col-md-4 control-label">Начало приёма заявок</label>
                            <div class="col-md-8">
                                <input id="inputStartDate" name="admissionPlan[startDate]" class="form-control input-md" type="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Окончание приёма заявок</label>
                            <div class="col-md-8">
                                <input id="inputEndDate" name="admissionPlan[endDate]" class="form-control input-md" type="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Количество бюджетных мест</label>
                            <div class="col-md-8">
                                <input id="inputGrantStudentsNumber" name="admissionPlan[grantStudentsNumber]" class="form-control input-md" type="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Количество коммерческих мест</label>
                            <div class="col-md-8">
                                <input id="inputTuitionStudentsNumber" name="admissionPlan[tuitionStudentsNumber]" class="form-control input-md" type="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Комментарий</label>
                            <div class="col-md-8">
                                <textarea  name="admissionPlan[reason]" class="form-control input-md"></textarea>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-info" data-url="<?=Url::toAddEducationalProgram($organizationId)?>"><i class="fa fa-check"></i> Сохранить</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var selectedYear = '<?=$year?>';

    $('#yearSelector').on('change', function(){
        $(location).attr('href', '<?=Url::toAdmissionPlanEdit()?>' + '?year=' + this.value);
    });

</script>
