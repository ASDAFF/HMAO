<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Helpers\OrganizationInfoUrlHelper;
?>

<table class="table">
	<thead>
		<th>&nbsp;</th>
		<th>Адрес</th>
		<th>Телефон</th>
		<th>Сайт</th>
	</thead>
	<?php foreach ($arResult['organizations'] as $organization): ?>
	<tbody>
		<tr>
			<td><a href="<?= OrganizationInfoUrlHelper::getOrganizationMainPageUrl($organization['id'])?>"><?= $organization['name']?></a></td>
			<td><?= 'г. ' . $organization['city'] . ', ' . $organization['address'] ?></td>
			<td><?= $organization['phone']?></td>
			<?php if (!empty($organization['site']))
				$organization['site'] = '<a href="' . $organization['site'] . '">' . $organization['site'] . '</a>';
			?>
			<td><?= $organization['site']?></td>
		</tr>
	</tbody>
	<?php endforeach; ?>
</table>



