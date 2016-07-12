<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php

use Spo\Site\Dictionaries\BaseEducation;
?>

<div class="page-header">
    <h3>Информация о количестве поданных заявлений</h3>
</div>
<?
    $statisticApplication = $arResult['statisticApplication'];
?>
<table width="100%" class="table table-bordered">
    <thead>
    <tr>
        <td rowspan="3">№ п.п.</td>
        <td rowspan="3">Код</td>
        <td rowspan="3">Специальность (профессия)</td>
        <td rowspan="3">Образование</td>
        <td colspan="6">Очная форма обучения</td>
    </tr>
    <tr>
        <td colspan="3">Бюджет</td>
        <td colspan="3">Контракт</td>
    </tr>
    <tr>
        <td>План приёма</td>
        <td>Кол-во поданных заявлений</td>
        <td>Кол-во человек на место</td>
        <td>План приёма</td>
        <td>Кол-во поданных заявлений</td>
        <td>Кол-во человек на место</td>
    </tr>
    </thead>
    <tbody>
    <? foreach ($statisticApplication as $item) {
        $stringNumber = $stringNumber + 1;

        if ($item['grantGroupsNumber'] != 0)
        {
            $GrantCompetition = (int)$item['grantStudentsNumber'] / (int)$item['grantGroupsNumber'];
            $GrantCompetition = round($GrantCompetition, 2);
        }
        else
            $GrantCompetition = 0;


        if ($item['tuitionGroupsNumber'] != 0)
        {
            $tuitionCompetition = (int)$item['tuitionStudentsNumber'] / (int)$item['tuitionGroupsNumber'];
            $tuitionCompetition = round($tuitionCompetition, 2);
        }
        else
            $tuitionCompetition = 0;
    ?>
        <tr>
            <td><?=$stringNumber?></td>
            <td><?=$item['specialtyCode']?></td>
            <td><?=$item['specialtyTitle']?></td>
            <td><?=BaseEducation::getValue($item['baseEducation'])?></td>
            <td><?=$item['grantStudentsNumber']?></td>
            <td><?=$item['grantGroupsNumber']?></td>
            <td><?=$GrantCompetition?></td>
            <td><?=$item['tuitionStudentsNumber']?></td>
            <td><?=$item['tuitionGroupsNumber']?></td>
            <td><?=$tuitionCompetition?></td>
        </tr>
    <?
    }
    ?>

    </tbody>
</table>