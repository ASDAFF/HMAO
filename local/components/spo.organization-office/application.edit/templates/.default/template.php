<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?
use \Bitrix\Main\Localization\Loc as Loc;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Dictionaries\ApplicationFundingType;
use Spo\Site\Helpers\AbiturientOfficeUrlHelper;
use Spo\Site\Dictionaries\ApplicationEventReason;
use Spo\Site\Helpers\OrganizationOfficeUrlHelper as Url;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\BaseEducation;

Loc::loadMessages(__FILE__);

$application = $arResult['application'];
$events = $arResult['application']['applicationEvents'];
$valid = $arResult['valid'];

switch ($application['status']) {
    case ApplicationStatus::CREATED: $class = 'primary'; break;
    case ApplicationStatus::ACCEPTED: $class = 'success'; break;
    case ApplicationStatus::RETURNED: $class = 'warning'; break;
    case ApplicationStatus::DECLINED: $class = 'danger'; break;
    case ApplicationStatus::DELETED: $class = 'danger'; break;
    case ApplicationStatus::IMPORT: $class = 'paper-plane-o'; break;
}
?>

<h4><? if ($valid)
        $validInfo = "Профиль пользователя проверен";
    else
        $validInfo = "Профиль пользователя не проверен "."<a href=".Url::toAbiturientProfile($application['abiturient']['userId']).">(проверить)</a>";
    echo $validInfo;
    ?></h4>

<h3 xmlns="http://www.w3.org/1999/html">Заявка № <?= $application['id']?></h3>

<div class="col-md-6"><p class="text-<?=$class?>">Статус: <?= ApplicationStatus::getValue($application['status']) ?></p></div>
<div class="col-md-6">
    <div class="pull-right"><a href="<?=Url::toApplicationList()?>">Вернуться к списку заявок</a></div>
</div>


<table class="table">
    <tr>
        <td>Абитуриент</td>
        <td>
            <a href="<?=Url::toAbiturientProfile($application['abiturient']['userId'])?>">
                <?= $application['abiturient']['fullname'] ?>
            </a>
        </td>
    </tr>
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
        <td><?= StudyMode::getValue($application['studyMode']) ?></td>
    </tr>
    <tr>
        <td>Базовое образование</td>
        <td><?= BaseEducation::getValue($application['baseEducation']) ?></td>
    </tr>

    <tr>
        <td>Вид финансирования</td>
        <td><?=ApplicationFundingType::getValue(ApplicationFundingType::GRANT)?></td>
    </tr>

    <tr>
        <td>Требуется общежитие</td>
        <td><?=$application['needHostel'] ? 'Да' : 'Нет';?></td>
    </tr>
</table>

<?
    if ($application['status'] == '1' and $valid)
    {
        ?>
        <div class="">
            <button type="button" id="btn-accept" class="btn btn-success" data-target=".dialog-form-container" data-toggle="modal">
                Принять
            </button>
            <!--<button type="button" id="btn-return" class="btn btn-warning" data-target=".dialog-form-container" data-toggle="modal">
                Отправить на доработку
            </button>
            <button type="button" id="btn-decline" class="btn btn-danger" data-target=".dialog-form-container" data-toggle="modal">
                Отклонить
            </button>-->
        </div>
        <?
    }
?>


    <div class="modal fade dialog-form-container" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel" style="margin:0;">Изменение статуса заявки</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" method="post" action="<?=Url::toApplicationEdit($application['id'])?>">
                        <input id="inputApplicationStatus" type="text" name="application[status]" hidden value="">

                        <div class="form-group" id="reasonFormGroup" hidden="">
                            <label class="col-md-4 control-label" for="radios">Причина</label>
                            <div class="col-md-8">
                                <div class="radio">
                                    <label for="radios-0">
                                        <input id="radioReasonIsEmpty" name="application[applicationEventReason]" value="<?=ApplicationEventReason::NONE?>" checked="checked" type="radio">
                                        Не указано
                                    </label>
                                </div>
                                <div class="radio">
                                    <label for="radios-1">
                                        <input class="reasonCheckBox" name="application[applicationEventReason]" value="<?=ApplicationEventReason::INCORRECT_APPLICATION?>" type="radio">
                                        <?=ApplicationEventReason::getValue(ApplicationEventReason::INCORRECT_APPLICATION)?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label for="radios-1">
                                        <input class="reasonCheckBox" name="application[applicationEventReason]" value="<?=ApplicationEventReason::DOCUMENTS_NOT_PROVIDED?>" type="radio">
                                        <?=ApplicationEventReason::getValue(ApplicationEventReason::DOCUMENTS_NOT_PROVIDED)?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label for="radios-1">
                                        <input class="reasonCheckBox" name="application[applicationEventReason]" value="<?=ApplicationEventReason::EXAM_NON_APPEARANCE?>" type="radio">
                                        <?=ApplicationEventReason::getValue(ApplicationEventReason::EXAM_NON_APPEARANCE)?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label for="radios-1">
                                        <input class="reasonCheckBox" name="application[applicationEventReason]" value="<?=ApplicationEventReason::SELECTION_FAIL?>" type="radio">
                                        <?=ApplicationEventReason::getValue(ApplicationEventReason::SELECTION_FAIL)?>
                                    </label>
                                </div>
                            </div>
                        </div>

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
                </form>
            </div>
        </div>
    </div>



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
                $icon = 'paper-plane-o';
            }
            else {
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
                            <small class="text-muted"><i class="fa fa-calendar"></i> <?=substr($event['date'], 0, 10);?></small>
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

    $(function(){

        $('#btn-accept').on('click', function(event){
            $('#inputApplicationStatus').attr('value', "<?=ApplicationStatus::ACCEPTED?>");
            setEmptyReason();
            $('#reasonFormGroup').hide();
        });

        $('#btn-return').on('click', function(event){
            $('#inputApplicationStatus').attr('value', "<?=ApplicationStatus::RETURNED?>");
            setEmptyReason();
            $('#reasonFormGroup').hide();
        });

        $('#btn-decline').on('click', function(event){
            $('#inputApplicationStatus').attr('value', "<?=ApplicationStatus::DECLINED?>");
            $('#reasonFormGroup').show();
        });

        function setEmptyReason() {
            $('.reasonCheckBox').each(function(){ this.checked = false; });
            $('#radioReasonIsEmpty').prop('checked', true);
        }

    });

</script>
