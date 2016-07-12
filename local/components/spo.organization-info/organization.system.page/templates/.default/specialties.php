<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php

use Spo\Site\Dictionaries\TrainingType;

$organizationSpecialties = $arResult['organization']['specialties'] ?>

<div class="page-header">
	<h3>Специальности подготовки</h3>
</div>

<div class="alert alert-info alert-white rounded alert-org-specialty">
    <div class="icon"><i class="fa fa-info"></i></div>
    <span>Актуальную информацию о направлениях подготовки и специальностях необходимо уточнять в приемной комиссии выбранного учебного заведения</span>
</div>

<? if (!empty($organizationSpecialties)) : ?>
<table class="table">
	<thead>
		<th>Форма обучения</th>
		<th>Код специальности</th>
		<th>Наименование специальности</th>
		<th>Квалификация</th>
		<th>Базовое образование</th>
		<th>Программа обучения</th>
	</thead>
	<tbody>
	<? foreach($organizationSpecialties as $organizationSpecialty):?>
		<tr>
			<td><?=$organizationSpecialty['studyMode']?></td>
			<td><?=$organizationSpecialty['code']?></td>
			<td><?=$organizationSpecialty['title']?></td>
			<td>
                <?php foreach($organizationSpecialty['qualifications'] as $q) {
                    echo $q['title'] . '</br>';
                }?>
            </td>
			<td><?=$organizationSpecialty['baseEducation']?></td>
			<td><?=TrainingType::getValue($organizationSpecialty['trainingType'])?></td>
		</tr>
	<? endforeach;?>
	</tbody>
</table>
<? endif; ?>
