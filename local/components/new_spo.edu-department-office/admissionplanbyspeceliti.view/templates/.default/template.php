<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Helpers\DateFormatHelper;
use Spo\Site\Helpers\OrganizationInfoUrlHelper;
use Spo\Site\Dictionaries\AdmissionPlanStatus;
use Spo\Site\Helpers\EduDepartmentOfficeUrlHelper as Url;

/**
 * @var $APPLICATION
 * @var $arResult
 */
$planData = $arResult['admissionPlan'];
$filter = $arResult['filter'];

?>

<div class="blog organisation-list-search-form">
    <div class="blog-header" data-toggle="collapse" href="#filterFormContainer">
        <strong>
            <i class="fa fa-search"></i> Фильтр
        </strong>
    </div>
    <div class="blog-body collapse" id="filterFormContainer">

        <form class="form-horizontal" id="filterForm">
            <fieldset>

                <div class="form-group">
                    <label class="col-md-4 control-label">Статус плана приёма</label>
                    <div class="col-md-8">
                        <select name="filter[admissionPlanStatus]" class="form-control">
                            <option value="">Любой</option>
                            <?php foreach(AdmissionPlanStatus::getValuesArray() as $key => $value):?>
                                <option value="<?=$key?>" <?=($key == $filter['admissionPlanStatus']) ? 'selected' : '';?>>
                                    <?=$value?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="filter[year]">Год</label>
                    <div class="col-md-8">
                        <select name="filter[year]" class="form-control">
                            <?php $currentYear = (integer) date('Y');?>
                            <?php for ($i = 2000; $i < $currentYear + 5; $i++) { ?>
                                <option value="<?=$i?>" <?= ($i == $filter['year']) ? 'selected' : ''?>><?=$i?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="organization[]">Образовательные организации</label>
                    <div class="col-md-8">
                        <select id="organization[]" name="filter[organization][]" class="form-control" multiple="multiple">
                            <?php foreach($arResult['organizationsList'] as $organization):?>
                                <option value="<?=$organization['id']?>" <?= (in_array($organization['id'], $filter['organization'])) ? 'selected' : ''; ?>>
                                    <?=$organization['name']?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="selectbasic">Район</label>
                    <div class="col-md-8">
                        <select name="filter[regionArea]" class="form-control">
                            <option value="">Любой</option>
                            <?php foreach($arResult['regionAreasList'] as $regionArea):?>
                                <option value="<?=$regionArea['regionAreaId']?>"  <?= ($regionArea['regionAreaId'] == $filter['regionArea']) ? 'selected' : ''; ?>>
                                    <?=$regionArea['regionAreaName']?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="specialties">Специальности подготовки (ФГОС)</label>
                    <div class="col-md-8">
                        <select id="specialties[]" name="filter[specialties][]" class="form-control" multiple="multiple">
                            <?php foreach($arResult['specialtiesList']['list'] as $specialty):?>
                                <option value="<?=$specialty['id']?>" <?= (in_array($specialty['id'], $filter['specialties'])) ? 'selected' : ''; ?>>
                                    <?=$specialty['code']?> - <?=$specialty['title']?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label">Срок обучения</label>
                    <div class="col-md-8">
                        <select name="filter[studyPeriod]" class="form-control">
                            <option value="">Любой</option>
                            <?php foreach($arResult['existingStudyPeriods'] as $studyPeriod):?>
                                <option value="<?=$studyPeriod?>" <?= ($filter['studyPeriod'] == $studyPeriod) ? 'selected' : '';?>>
                                    <?=DateFormatHelper::months2YearsMonths($studyPeriod)?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label">Форма обучения</label>
                    <div class="col-md-8">
                        <select name="filter[studyMode]" class="form-control">
                            <option value="">Любая</option>
                            <?php foreach(StudyMode::getValuesArray() as $key => $value):?>
                                <option value="<?=$key?>" <?=($key == $filter['studyMode']) ? 'selected' : '';?>>
                                    <?=$value?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label">Программы подготовки на базе</label>
                    <div class="col-md-8">
                        <select name="filter[baseEducation]" class="form-control">
                            <option value="">Любая</option>
                            <?php foreach(BaseEducation::getValuesArray() as $key => $value):?>
                                <option value="<?=$key?>" <?=($key == $filter['baseEducation']) ? 'selected' : '';?>>
                                    <?=$value?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="selectbasic">Программа подготовки</label>
                    <div class="col-md-8">
                        <select name="filter[trainingLevel]" class="form-control">
                            <option value="">Любая</option>
                            <?php foreach(TrainingLevel::getValuesArray() as $key => $value):?>
                                <option value="<?=$key?>" <?=($key == $filter['trainingLevel']) ? 'selected' : '';?>>
                                    <?=$value?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 pull-right">
                        <button class="btn btn-info" type="submit">Поиск</button>
                        <button class="btn btn-default" type="reset">Сбросить</button>
                    </div>
                </div>

            </fieldset>
        </form>

    </div>
</div>
<div class="department-dashboard">

    <table width="100%" class="table table-bordered">
        <thead>
        <tr>
            <th></th>
            <th colspan="6" style="text-align: center;">На базе основного общего образования
                (9 классов)</th>
            <th colspan="6" style="text-align: center;">На базе среднего (полного) общего образования
                (11 классов)</th>
        </tr>
        <tr>
            <th></th>
            <th colspan="3" style="text-align: center;">Бюджет</th>
            <th colspan="3" style="text-align: center;">Контракт</th>
            <th colspan="3" style="text-align: center;">Бюджет</th>
            <th colspan="3" style='text-align: center;'>Контракт</th>
        </tr>
        <tr>
            <th>Организация </th>
            <th>План приёма </th>
            <th>Кол-во поданных заявлений</th>
            <th>Кол-во человек на место </th>
            <th>План приёма </th>
            <th>Кол-во поданных заявлений</th>
            <th>Кол-во человек на место </th>
            <th>План приёма </th>
            <th>Кол-во поданных заявлений</th>
            <th>Кол-во человек на место </th>
            <th>План приёма </th>
            <th>Кол-во поданных заявлений</th>
            <th>Кол-во человек на место </th>
        </tr>
        </thead>

        <?php
        $grantSum = 0;
        $tuitionSum = 0;
        ?>

        <?php foreach($planData as $p) { ?>
            <tr>
                <th colspan="13"><?=$p['specialtyTitle']." (".$p['specialtyCode'].")"?></th>
            </tr>
            <?php for($i=0;count($p['organization'])>$i;$i=$i+1){?>
                <tr>
                    <td><?=$p['organization'][$i]['organizationName']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][1]['grantStudentsNumber']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][1]['budget']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][1]['budget']/$p['organization'][$i]['trainingLevels'][1]['grantStudentsNumber']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][1]['tuitionStudentsNumber']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][1]['platno']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][1]['platno']/$p['organization'][$i]['trainingLevels'][1]['tuitionStudentsNumber']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][2]['grantStudentsNumber']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][2]['budget']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][2]['budget']/$p['organization'][$i]['trainingLevels'][2]['grantStudentsNumber']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][2]['tuitionStudentsNumber']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][2]['platno']?></td>
                    <td><?=$p['organization'][$i]['trainingLevels'][2]['platno']/$p['organization'][$i]['trainingLevels'][2]['tuitionStudentsNumber']?></td>
                    <?php
                     $grantStudentsNumber1=$p['organization'][$i]['trainingLevels'][1]['grantStudentsNumber']+$grantStudentsNumber1;
                     $budget1=$p['organization'][$i]['trainingLevels'][1]['budget']+$budget1;
                     $itogbudget1=$p['organization'][$i]['trainingLevels'][1]['budget']/$p['organization'][$i]['trainingLevels'][1]['grantStudentsNumber']+$itogbudget1;
                     $tuitionStudentsNumber1=$p['organization'][$i]['trainingLevels'][1]['tuitionStudentsNumber']+$tuitionStudentsNumber1;
                     $platno1=$p['organization'][$i]['trainingLevels'][1]['platno']+$platno1;
                     $itogplatno1=$p['organization'][$i]['trainingLevels'][1]['platno']/$p['organization'][$i]['trainingLevels'][1]['tuitionStudentsNumber']+$itogplatno1;

                    $grantStudentsNumber2=$p['organization'][$i]['trainingLevels'][2]['grantStudentsNumber']+$grantStudentsNumber2;
                    $budget2=$p['organization'][$i]['trainingLevels'][2]['budget']+$budget2;
                    $itogbudget2=$p['organization'][$i]['trainingLevels'][2]['budget']/$p['organization'][$i]['trainingLevels'][2]['grantStudentsNumber']+$itogbudget2;
                    $tuitionStudentsNumber2=$p['organization'][$i]['trainingLevels'][2]['tuitionStudentsNumber']+$tuitionStudentsNumber2;
                    $platno2=$p['organization'][$i]['trainingLevels'][2]['platno']+$platno2;
                    $itogplatno2=$p['organization'][$i]['trainingLevels'][2]['platno']/$p['organization'][$i]['trainingLevels'][2]['tuitionStudentsNumber']+$itogplatno2;
                    ?>
                </tr>
            <? }?>
        <?php } ?>

        <tr style="font-weight: bold" >
            <td style="text-align: right">Итого</td>
            <td><?=$grantStudentsNumber1?></td>
            <td><?=$budget1?></td>
            <td><?=$itogbudget1?></td>
            <td><?=$tuitionStudentsNumber1?></td>
            <td><?=$platno1?></td>
            <td><?=$itogplatno1?></td>
            <td><?=$grantStudentsNumber2?></td>
            <td><?=$budget2?></td>
            <td><?=$itogbudget2?></td>
            <td><?=$tuitionStudentsNumber2?></td>
            <td><?=$platno2?></td>
            <td><?=$itogplatno2?></td>
        </tr>

    </table>
</div>