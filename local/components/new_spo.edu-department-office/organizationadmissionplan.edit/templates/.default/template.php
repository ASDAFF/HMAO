<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Helpers\EduDepartmentOfficeUrlHelper as Url;
use Spo\Site\Dictionaries\AdmissionPlanStatus;

$data = $arResult['data'];

?>
<div class="department-dashboard">

    <p>Организация: <strong><?=$data['organization']['fullName']?></strong></p>
    <p>
        Программа обучения: <strong><?=$data['specialty']['title']?> (<?=$data['specialty']['code']?>)</strong>
        <ul>
            <li>Форма обучения: <?=StudyMode::getValue($data['organizationSpecialty']['studyMode'])?></li>
            <li>Базовое образование: <?=BaseEducation::getValue($data['organizationSpecialty']['baseEducation'])?></li>
            <li>Уровень подготовки: <?=TrainingLevel::getValue($data['organizationSpecialty']['trainingLevel'])?></li>
        </ul>
    </p>

    <form class="form-horizontal">
        <fieldset>

            <?php
            $status = $data['admissionPlan']['status'];
            if ($status == AdmissionPlanStatus::ACCEPTED)
                $class = 'success';
            elseif ($status == AdmissionPlanStatus::CREATED)
                $class = 'info';
            else
                $class = 'danger';
            ?>
            <div class="form-group">
                <label class="col-md-3 control-label">Статус плана приёма:</label>
                <div class="col-md-9">
                    <p class="text-<?=$class?>">
                        Статус плана приёма: <?=AdmissionPlanStatus::getValue($data['admissionPlan']['status'])?>
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label">Начало приёма заявок</label>
                <div class="col-md-9">
                    <input class="form-control input-md" type="text" value="<?=$data['admissionPlan']['startDate']?>" disabled>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label">Окончание приёма заявок</label>
                <div class="col-md-9">
                    <input class="form-control input-md" type="text" value="<?=$data['admissionPlan']['endDate']?>" disabled>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label">Количество бюджетных мест</label>
                <div class="col-md-9">
                    <input  class="form-control input-md" type="text" value="<?=$data['admissionPlan']['grantStudentsNumber']?>" disabled>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label">Количество коммерческих мест</label>
                <div class="col-md-9">
                    <input class="form-control input-md" type="text" value="<?=$data['admissionPlan']['tuitionStudentsNumber']?>" disabled>
                </div>
            </div>
        </fieldset>

        <div class="pull-right">
            <?php if ($status != AdmissionPlanStatus::ACCEPTED) {?>
            <button type="button" id="btn-accept" class="btn btn-info">
                <i class="fa fa-check"></i> Одобрить
            </button>
            <?php }?>

            <?php if ($status != AdmissionPlanStatus::DECLINED) {?>
            <button type="button" id="btn-decline" class="btn btn-danger" data-target=".dialog-form-container" data-toggle="modal">
                <i class="fa fa-remove"></i> Отклонить
            </button>
            <?php }?>
        </div>

    </form>
</div>


<div class="container">
    <div class="page-header">
        <h1 id="timeline">История изменений</h1>
    </div>
    <ul class="timeline">
        <?php
        foreach($data['admissionPlan']['events'] as $event) {
            if ($event['status'] == AdmissionPlanStatus::CREATED) {
                $class = 'info';
                $icon = 'question';
                $inverted = true;
            }
            elseif ($event['status'] == AdmissionPlanStatus::ACCEPTED) {
                $inverted = false;
                $icon = 'check';
                $class = 'success';
            } else {
                $icon = 'remove';
                $inverted = false;
                $class = 'danger';
            }
        ?>
            <li class="<?= ($inverted) ? 'timeline-inverted' : ''?>">
                <div class="timeline-badge <?=$class?>"><i class="fa fa-<?=$icon?>"></i></div>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4 class="timeline-title">
                            <?=AdmissionPlanStatus::getValue($event['status'])?>
                        </h4>
                        <p>
                            <small class="text-muted"><i class="fa fa-calendar"></i> <?=$event['date']?></small>
                        </p>
                    </div>
                    <div class="timeline-body">
                        <p><?=$event['comment']?></p>
                    </div>
                </div>
            </li>
        <?php } ?>

    </ul>
</div>


<div class="modal fade dialog-form-container" id="admission-plan-editor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="changeStatusForm" class="form-horizontal" method="post" action="<?=Url::toAdmissionPlanEdit($data['admissionPlan']['id'])?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <fieldset>
                        <input id="inputAdmissionPlanStatus" name="admissionPlan[status]" hidden="" type="text" value="">

                        <div class="form-group">
                            <label class="col-md-4 control-label">Укажите причину отклонения</label>
                            <div class="col-md-8">
                                <textarea id="inputReason" name="admissionPlan[reason]" class="form-control input-md" type="text"></textarea>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-info"><i class="fa fa-check"></i> Сохранить</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var statusAccepted = <?= AdmissionPlanStatus::ACCEPTED?>;
    var statusDeclined = <?= AdmissionPlanStatus::DECLINED?>;
</script>