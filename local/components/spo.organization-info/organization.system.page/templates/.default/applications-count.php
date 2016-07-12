<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php

use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\TrainingType;

$specialtiesGroups = $arResult['organization']['specialtiesGroups'];
$applicationsStat = $arResult['applicationsCount'];
?>

<div class="page-header">
	<h3>Контрольные цифры приёма</h3>
</div>
    <?php foreach($specialtiesGroups as $studyModeCode => $studyModeGroup): ?>
        <?php foreach($studyModeGroup as $baseEducationCode => $baseEducationGroup): ?>
            <?php if (!empty($baseEducationGroup)) :?>
                <?php $allAbiturientsCount = 0; $allGroupsCount = 0; $applicationsCount = 0;?>
                <strong><?= StudyMode::getValue($studyModeCode) . ' - ' . BaseEducation::getValue($baseEducationCode)?></strong>
                <hr>
                <table class="table">
                    <thead>
                        <th>Код</th>
                        <th>Специальность</th>
                        <th>План приёма на бюджет</th>
                        <th>Количество групп</th>
                        <th>Подано заявлений</th>
                        <th>Человек на место</th>
                    </thead>
                    <tbody>
                    <?php foreach($baseEducationGroup as $organizationSpecialty):?>
                        <?php
                            $allAbiturientsCount += $organizationSpecialty['plannedAbiturientsCount'];
                            $allGroupsCount += $organizationSpecialty['plannedGroupsCount'];
                            $applicationsCount += $applicationsStat[$organizationSpecialty['id']];
                        ?>
                        <tr>
                            <td><?=$organizationSpecialty['code']?></td>
                            <td><?=$organizationSpecialty['title']?></td>
                            <td><?= $organizationSpecialty['plannedAbiturientsCount']?></td>
                            <td><?= $organizationSpecialty['plannedGroupsCount']?></td>
                            <td>
                                <?= $applicationsStat[$organizationSpecialty['id']]?>
                            </td>
                            <td>
                                <?= $applicationsStat[$organizationSpecialty['id']] / $organizationSpecialty['plannedAbiturientsCount'] ?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                        <tr>
                            <td colspan="2"><strong class="pull-right">Итого: </strong></td>
                            <td><strong><?=$allAbiturientsCount?></strong></td>
                            <td><strong><?=$allGroupsCount?></strong></td>
                            <td><strong><?=$applicationsCount?></strong></td>
                        </tr>
                    </tbody>
                </table>
            <?php endif;?>
        <?php endforeach;?>
    <?php endforeach;?>


