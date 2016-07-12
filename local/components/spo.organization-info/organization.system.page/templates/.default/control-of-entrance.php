<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php

use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Helpers\DateFormatHelper;

$data = $arResult['admissionPlanWithRequestNumber'] ?>

<div class="page-header">
	<h3>Контрольные цифры приёма</h3>
</div>

<table width="100%" class="table table-bordered">
    <tr>
        <th rowspan="2">№ п.п.</th>
        <th rowspan="2">Код</th>
        <th rowspan="2">Специальность (профессия)</th>
        <th rowspan="2">Программа подготовки  </th>
        <th rowspan="2">Срок обучения </th>
        <th rowspan="2">Форма обучения </th>
        <th rowspan="2">Базовое образование </th>
        <th colspan="2">План приёма</th>
    </tr>
    <tr>
        <th colspan="1">Бюджетное</th>
        <th colspan="1">Контракт</th>
    </tr>
    <!--tr>
        <th>план</th>
        <th>подано</th>
        <th>чел./место <br></th>
        <th>план</th>
        <th>подано</th>
        <th>чел. <br />/место</th>
    </tr-->
    <?php

    $t=0;
    $g=0;
    $i=0;
    foreach($data as $d) {
        $t+=$d['tuitionStudentsNumber'];
        $g+=$d['grantStudentsNumber'];
        $i++;
        ?>
        <tr>
            <td><?=$i?></td>
            <td><?=$d['specialtyCode']?></td>
            <td><?=$d['specialtyTitle']?></td>
            <td><?=TrainingLevel::getValue($d['trainingLevel'])?></td>
            <td><?=DateFormatHelper::months2YearsMonths($d['studyPeriod'])?></td>
            <td><?=StudyMode::getValue($d['studyMode'])?></td>
            <td><?=BaseEducation::getValue($d['baseEducation'])?></td>
            <td><?=$d['grantStudentsNumber']?></td>

            <td><?=$d['tuitionStudentsNumber']?></td>

        </tr>
    <?php } ?>


<tr>
    <td> </td>
    <td> <b> ИТОГО </b></td>
    <td> </td>
    <td> </td>
    <td> </td>
    <td> </td>
    <td> </td>
    <td> <b><?=$g?></b></td>

    <td><b><?=$t?></b> </td>

</tr>

</table>


