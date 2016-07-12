<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Helpers\DateFormatHelper;
use Spo\Site\Helpers\EduDepartmentOfficeUrlHelper as Url;
/**
 * @var $APPLICATION
 * @var $arResult
 */
$planData = $arResult['admissionPlanWithRequestNumber'];
$filter = $arResult['filter'];

$grantStudentsSumBasic = 0;
$tuitionStudentsSumBasic = 0;
$grantStudentsSumSecondary = 0;
$tuitionStudentsSumSecondary = 0;
?>

<div class="blog organisation-list-search-form">
    <div class="blog-header" data-toggle="collapse" href="#filterForm">
        <strong>
            <i class="fa fa-search"></i> Фильтр
        </strong>
    </div>
    <div class="blog-body collapse" id="filterForm">

        <form class="form-horizontal">
            <fieldset>

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
        <tr>
            <th rowspan="3">Специальность (код) </th>
            <th rowspan="3">Уровень подготовки </th>
            <th rowspan="3">Срок обучения </th>
            <th rowspan="3">Форма обучения </th>
            <th rowspan="3">Базовое образование </th>
            <th colspan="6">План приёма</th>
        </tr>
        <tr>
            <th colspan="3">бюджетное</th>
            <th colspan="3">коммерческое</th>
        </tr>
        <tr>
            <th>план</th>
            <th>подано</th>
            <th>чел./место <br></th>
            <th>план</th>
            <th>подано</th>
            <th>чел./место</th>
        </tr>
        <?php foreach($planData as $p) { ?>
        <tr>
            <td><?=$p['specialtyTitle']?> (<?=$p['specialtyCode']?>)</td>
            <td><?=TrainingLevel::getValue($p['trainingLevel'])?></td>
            <td><?=DateFormatHelper::months2YearsMonths($p['studyPeriod'])?></td>
            <td><?=StudyMode::getValue($p['studyMode'])?></td>
            <td><?=BaseEducation::getValue($p['baseEducation'])?></td>
            <td><?=$p['grantStudentsNumber']?></td>
            <td><?=$p['grantApplicationsNumber']?></td>
            <td><?=$p['grantDemand']?></td>
            <td><?=$p['tuitionStudentsNumber']?></td>
            <td><?=$p['paidApplicationsNumber']?></td>
            <td><?=$p['paidDemand']?></td>
        </tr>
        <?php } ?>
    </table>


</div>