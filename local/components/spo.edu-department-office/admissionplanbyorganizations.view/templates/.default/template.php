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
            <th>Специальность (код) </th>
            <th>Уровень подготовки </th>
            <th>Срок обучения </th>
            <th>Форма обучения </th>
            <th>Базовое образование </th>
            <th colspan="2">План приёма </th>
            <th></th>
        </tr>
        </thead>

        <?php
        $grantSum = 0;
        $tuitionSum = 0;
        ?>

        <?php foreach($planData as $p) { ?>
        <tr>

            <td><?=$p['specialtyTitle']?> (<?=$p['specialtyCode']?>)</td>
            <td><?=TrainingLevel::getValue($p['trainingLevel'])?></td>
            <td><?=DateFormatHelper::months2YearsMonths($p['studyPeriod'])?></td>
            <td><?=StudyMode::getValue($p['studyMode'])?></td>
            <td><?=BaseEducation::getValue($p['baseEducation'])?></td>
            <td>Бюджет</td>
            <td>Контракт</td>
        </tr>

            <?php
                $orgGrantSum = 0;
                $orgTuitionSum = 0;
            ?>

            <?php foreach($p['organizations'] as $organizationId => $organization) { ?>
            <?php
                $orgGrantSum += $organization['grantStudentsNumber'];
                $orgTuitionSum += $organization['tuitionStudentsNumber'];
            ?>
            <tr>
                <td colspan="5" style="text-align: right">
                    <a href="<?=OrganizationInfoUrlHelper::getOrganizationMainPageUrl($organizationId)?>">
                        <?=$organization['name']?>
                    </a>
                </td>
                <?php if ($organization['admissionPlanStatus'] == AdmissionPlanStatus::CREATED) { ?>
                    <td class="info"><?=$organization['grantStudentsNumber']?></td>
                    <td class="info"><?=$organization['tuitionStudentsNumber']?></td>
                <?php } elseif($organization['admissionPlanStatus'] == AdmissionPlanStatus::ACCEPTED) { ?>
                    <td class="success"><?=$organization['grantStudentsNumber']?></td>
                    <td class="success"><?=$organization['tuitionStudentsNumber']?></td>
                <?php } elseif($organization['admissionPlanStatus'] == AdmissionPlanStatus::DECLINED) { ?>
                    <td class="danger"><?=$organization['grantStudentsNumber']?></td>
                    <td class="danger"><?=$organization['tuitionStudentsNumber']?></td>
                <?php }?>
                <td>
                    <a href="<?=Url::toAdmissionPlanEdit($organization['admissionPlanId'])?>">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                </td>
            </tr>
            <?php } ?>

            <tr style="font-weight: bold" >
                <td colspan="5" style="text-align: right">Итого по специальности</td>
                <td><?=$orgGrantSum?></td>
                <td><?=$orgTuitionSum?></td>
            </tr>

            <?php
                $grantSum += $orgGrantSum;
                $tuitionSum += $orgTuitionSum;
            ?>
        <?php } ?>

        <tr style="font-weight: bold" >
            <td colspan="5" style="text-align: right">Итого</td>
            <td><?=$grantSum?></td>
            <td><?=$tuitionSum?></td>
        </tr>

    </table>
</div>