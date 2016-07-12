<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php
use Bitrix\Main\Type\DateTime;
use Spo\Site\Helpers\AbiturientOfficeUrlHelper;
use Spo\Site\Dictionaries\ApplicationFundingType;
use Spo\Site\Dictionaries\ExamDiscipline;
use Spo\Site\Dictionaries\ExamType;
use Spo\Site\Dictionaries\TrainingType;
use Spo\Site\Helpers\DateFormatHelper;
use Spo\Site\Dictionaries\AdaptationType;
use Spo\Site\Dictionaries\ApplicationPriority;

$organizationData = $arResult['organizationWithAvailableSpecialties'];
$hostel = $arResult['OldHostel'];
$count = $arResult['counts'];
$abiturientProfileEducation = $arResult['USER_PROFILE_EDUCATION'];
?>
<div class="row">
    <div class="col-md-12">
    <?php if($arResult['success']):?>

        <p></p>
        <p class="text-success"><?= $arResult['success'] ?></p>
        <a class="btn btn-info" href="<?=AbiturientOfficeUrlHelper::getApplicationListUrl()?>">Список моих заявок</a>

    <?php else: ?>
        <label class="col-md-4 control-label">Образовательная организация</label>
        <div class="col-md-8">
            <p class="form-control-static"><?= $arResult['organizationWithAvailableSpecialties']['name'] ?></p>
        </div>

        <label class="col-md-4 control-label">Дата подачи заявления</label>
        <div class="col-md-8">
            <p class="form-control-static"><?= date('Y-m-d');?></p>
        </div>
        <?if( $arResult['organizationWithAvailableSpecialties']['hostel']) {?>
            <label class="col-md-4 control-label">Требуется место в общежитии:</label>
            <div class="col-md-8">
                <p class="form-control-static"><input type="checkbox" onclick="" id="hostel" <?if($hostel) echo "checked";?>></p>
            </div>
        <?}else{?>
            <label class="col-md-12 control-label">Учреждение не предоставляет место в общежитии</label>
        <?}?>


        <? foreach($organizationData['specialties'] as $s) : ?>
        <form action="<?=AbiturientOfficeUrlHelper::getApplicationCreateUrl($organizationData['id'])?>" method="post" onsubmit="AllHostel()">
            <div class="well col-md-12">

                <input type="text" name="applicationData[organizationSpecialtyId]" value="<?=$s['id']?>" hidden>
                <input type="text" name="applicationData[admissionPlanId]" value="<?=$s['actualAdmissionPlan']['id']?>" hidden>

                <input type="text" name="applicationData[Oldhostel]" value="<?=$hostel?>" hidden>

                <?php
                //var_dump($s);
                $strt='';
                $now = new DateTime();
                $applicationReceptionIsAvailable = true;
                    if ($s['apply']) {
                        $applicationReceptionIsAvailable = false;
                        $reason = 'Вы уже подавали заявку на данную специальность в данном учебном заведении';
                    } elseif ($count >= 3) {
                        $applicationReceptionIsAvailable = false;
                        $reason = 'Вы не можете подавать более трех заявлений';
                    } elseif (empty($s['actualAdmissionPlan'])) {
                        $applicationReceptionIsAvailable = false;
                        $reason = 'Приём заявлений на данную специальность в настоящий момент не проводится';
                    } elseif ($now < $s['actualAdmissionPlan']['startDate']) {
                        $applicationReceptionIsAvailable = false;
                        $reason = 'Приём заявлений на данную специальность будет открыт с ' . $s['actualAdmissionPlan']['startDate']->format('d-m-Y');
                    } elseif ($s['actualAdmissionPlan']['endDate'] < $now) {
                        $applicationReceptionIsAvailable = false;
                        $reason = 'Приём заявлений на данную специальность проводился до ' . $s['actualAdmissionPlan']['endDate']->format('d-m-Y');
                    } elseif ($abiturientProfileEducation == 1 || $abiturientProfileEducation == 3){
                        if ($s['baseEducationNumber'] == 2 || $s['baseEducationNumber'] == 4 || $s['baseEducationNumber'] == 5){
                            $applicationReceptionIsAvailable = false;
                            $reason = 'Вы не можете подать заявку на данную учебную программу';
                        }
                    }
                ?>

                <?php if ($applicationReceptionIsAvailable) {?>
                    <button type="submit" class="btn btn-info" style="width: 100%">Выбрать</button><br>
                    <input type="hidden" name="applicationData[applicationPriority]" value="<?=$count+1;?>" >
                <?php } else { ?>
                    <p class="text-warning"><?=$reason?></p>
                <?php } ?>

                <hr>

                <div class="col-md-8">
                    Специальность: <strong><?=$s['title']?> (<?=$s['code']?>)<br></strong>
                    Форма обучения: <strong><?=$s['studyMode']?></strong><br>
                    Срок обучения: <strong><?= DateFormatHelper::months2YearsMonths($s['studyPeriod'])?></strong><br>
                    Базовое образование: <strong><?=$s['baseEducation']?></strong><br>
                    Уровень подготовки: <strong><?=$s['trainingLevel']?></strong><br>
                    <? if($s['GRANT']==0 || $s['TUITION']==0)
                    {
                       if($s['TUITION']==0) {
                           $strt="Бюджетное финансирование";
                           echo '<input type="hidden" name="applicationData[fundingType]" value="'.ApplicationFundingType::GRANT.'" >';
                       }
                       if($s['GRANT']==0) {
                           $strt="Контратное финансирование";
                           echo '<input type="hidden" name="applicationData[fundingType]" value="'.ApplicationFundingType::PAID.'" >';
                       }
                       echo "Вид финансирования: <strong>".$strt."</strong> <br/>";
                    }?>
                    Программа обучения: <strong><?=TrainingType::getValue($s['trainingType'])?></strong><br>

                    <?php if($applicationReceptionIsAvailable) { ?>
                    Приём заявлений: <strong>с <?=$s['actualAdmissionPlan']['startDate']->format('d-m-Y')?> по <?=$s['actualAdmissionPlan']['endDate']->format('d-m-Y')?></strong><br>
                    <?php } ?>
                    <p>
                        Вступительные экзамены:
                        <?php if(!empty($s['exams'])) {?>
                            <ul>
                                <?php foreach ($s['exams'] as $exam):?>
                                    <li>
                                        <?=ExamDiscipline::getValue($exam['discipline'])?> (<?=ExamType::getValue($exam['type'])?>) <br/>
                                        Дата провкедения: <?=date("d.m.Y",strtotime($exam['date']))?>
                                        Место: <?=$exam['adres']?>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        <?php } else {?>
                            нет
                        <?php }?>
                    </p>

                    <?php if (!empty($s['adaptationTypes'])) { ?>
                    <p>
                        Программа адаптированна для:
                        <ul>
                        <?php foreach($s['adaptationTypes'] as $adaptationType) { ?>
                            <li><?=AdaptationType::getValue($adaptationType)?></li>
                        <?php } ?>
                        </ul>
                    </p>
                    <?php }?>


                </div>
                <div class="col-md-4">
                    <?php if ($applicationReceptionIsAvailable) {?>
                        <p><input type="checkbox" name="applicationData[needHostel]" hidden> </p>
                        <? if(!empty($strt)){ ?>
                            <? if($s['actualAdmissionPlan']['grantStudentsNumber']!=0){?>
                              <div class="radio">
                                <label>
                                     <input type="radio" name="applicationData[fundingType]" value="<?=ApplicationFundingType::GRANT?>" checked>
                                    Бюджетное финансирование
                                </label>
                              </div>
                            <? }?>
                            <? if($s['actualAdmissionPlan']['tuitionStudentsNumber']!=0){?>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="applicationData[fundingType]" value="<?=ApplicationFundingType::PAID?>" <? if($s['actualAdmissionPlan']['grantStudentsNumber']==0){?>checked<?}?>>
                                    Коммерческое финансирование
                                </label>
                            </div>
                            <?}?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </form>
        <?php endforeach;?>

    <?php endif; ?>
    </div>
</div>