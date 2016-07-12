<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?
use \Bitrix\Main\Localization\Loc as Loc;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Dictionaries\ApplicationFundingType;
use Spo\Site\Helpers\AbiturientOfficeUrlHelper;
use Spo\Site\Dictionaries\ApplicationEventReason;

Loc::loadMessages(__FILE__);

$application = $arResult['application'];
$events = $arResult['application']['applicationEvents'];

switch ($application['status']) {
    case ApplicationStatus::CREATED: $class = 'primary'; break;
    case ApplicationStatus::ACCEPTED: $class = 'success'; break;
    case ApplicationStatus::RETURNED: $class = 'warning'; break;
    case ApplicationStatus::DECLINED: $class = 'danger'; break;
    case ApplicationStatus::DELETED: $class = 'danger'; break;
    case ApplicationStatus::PRIOR: $class = 'danger'; break;
}

?>


<h3 xmlns="http://www.w3.org/1999/html">Заявка № <?= $application['id']?></h3>

<p class="text-<?=$class?>">Статус: <?= ApplicationStatus::getValue($application['status']) ?></p>

<table class="table">
    <tr>
        <td>Дата подачи заявления</td>
        <td><?= $application['creationDate'] ?></td>
    </tr>
    <tr>
        <td>Специальность</td>
        <td><?= $application['specialtyTitle'] ?> (<?= $application['specialtyCode'] ?>)</td>
    </tr>
    <tr>
        <td>Форма обучения</td>
        <td><?= $application['studyMode'] ?></td>
    </tr>
    <tr>
        <td>Базовое образование</td>
        <td><?= $application['baseEducation'] ?></td>
    </tr>
</table>

<form class="form-horizontal" method="post" action="<?=AbiturientOfficeUrlHelper::getApplicationEditUrl($application['id'])?>">
    <fieldset>
        <div class="form-group">
            <label class="col-md-2 control-label" for="radios">Вид финансирования</label>
            <div class="col-md-6">
                <?if($application['GRANT']!=0){?>
                <div class="radio">
                    <label for="radios-0">
                        <input name="application[fundingType]" value="<?=ApplicationFundingType::GRANT?>" <?=($application['fundingType'] == ApplicationFundingType::GRANT) ? 'checked' : ''?> type="radio">
                        <?=ApplicationFundingType::getValue(ApplicationFundingType::GRANT)?>
                    </label>
                </div>
                <? }?>
                <?if($application['TUITION']!=0){?>
                <div class="radio">
                    <label for="radios-1">
                        <input name="application[fundingType]" value="<?=ApplicationFundingType::PAID?>" <?=($application['fundingType'] == ApplicationFundingType::PAID) ? 'checked' : ''?> type="radio">
                        <?=ApplicationFundingType::getValue(ApplicationFundingType::PAID)?>
                    </label>
                </div>
                <? }?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label" for="checkboxes">Требуется общежитие</label>
            <div class="col-md-6">
                <div class="checkbox">
                    <label for="checkboxes-0">
                        <input name="application[needHostel]" type="checkbox" <?= ($application['needHostel']) ? 'checked' : ''?>>
                    </label>
                </div>
            </div>
        </div>
        <label class="col-md-12 control-label"><i class="fa fa-asterisk"></i>При изменении заявки необходимо ввести комментарий</label>
    </fieldset>

    <?if($application['status']!=7):?>
    <div class="">
        <a href="<?= AbiturientOfficeUrlHelper::getApplicationDeleteUrl($application['id'])?>" class="btn btn-danger"><i class="fa fa-times"></i> Отменить заявку на поступление</a>
        <button type="button" id="btn-save" class="btn btn-info" data-target=".dialog-form-container" data-toggle="modal">
            Сохранить
        </button>
    </div>
    <?endif;?>

    <div class="modal fade dialog-form-container" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel" style="margin:0;">Подача заявления</h4>
                    </div>

                    <div class="modal-body">
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Комментарий (если требуется)</label>
                                <div class="col-md-8">
                                    <textarea name="application[applicationEventComment]" class="form-control input-md" type="text"></textarea>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info">  Сохранить</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"> Отмена</button>
                    </div>
            </div>
        </div>
    </div>

</form>

<?php if (!empty($events)) {?>
<div class="page-header">
    <h1 id="timeline">История изменений</h1>
</div>
<ul class="timeline">
    <?php
    foreach($events as $event) {
        $inverted = ($event['status'] == ApplicationStatus::CREATED) ? true : false;
        if ($event['status'] == ApplicationStatus::CREATED) {
            $icon = 'question';
            $class = 'primary';
        } elseif ($event['status'] == ApplicationStatus::RETURNED) {
            $class = 'warning';
            $icon = 'refresh';
        } elseif ($event['status'] == ApplicationStatus::ACCEPTED) {
            $class = 'success';
            $icon = 'check';
        } elseif ($event['status'] == ApplicationStatus::IMPORT) {
            $class = 'success';
            $icon = 'paper-plane';
        } elseif ($event['status'] == ApplicationStatus::PRIOR)
        {
            $class = 'success';
            $icon = 'sort-amount-asc';
        } else {
            $class = 'danger';
            $icon = 'remove';
        }
    ?>
    <li <?= ($inverted) ? 'class="timeline-inverted"' : ''?>>
        <div class="timeline-badge <?=$class?>"><i class="fa fa-<?=$icon?>"></i></div>
        <div class="timeline-panel">
            <div class="timeline-heading">
                <h4 class="timeline-title">
                    Статус заявки: <?= ApplicationStatus::getValue($event['status'])?>
                </h4>
                <p>
                    <small class="text-muted"><i class="fa fa-calendar"></i> <?=$event['date']?></small>
                </p>
            </div>

            <div class="timeline-body">
                <p><?=$event['comment']?></p>
                <?php if (!empty($event['reason']) && $event['reason'] != ApplicationEventReason::NONE) {?>
                <p>Примечание: <?= ApplicationEventReason::getValue($event['reason'])?></p>
                <?php } ?>
            </div>
        </div>
    </li>
    <?php } ?>
</ul>
<?php } ?>
<script>
    <? if($application['status']==7):?>
        $('input').prop('disabled', true);
        $('select').prop('disabled', true);
    <?endif;?>
</script>




