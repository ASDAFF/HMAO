<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<? $CurrPage = $APPLICATION->GetCurPage(); ?>

<?php foreach($arResult['menu'] as $menuItems):?>
<div class="sidebar-menu">
	<ul>
		<?php foreach($menuItems as $label => $link):?>
			<li<? if ($CurrPage == $link) echo ' class="highlight"'; ?>>
				<a href="<?=$link?>"><?= $label ?></a>
			</li>
		<?php endforeach;?>
	</ul>
</div>
<?php endforeach;?>
