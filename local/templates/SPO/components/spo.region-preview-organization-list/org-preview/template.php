<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php
use Spo\Site\Helpers\RegionInfoUrlHelper;
use Spo\Site\Helpers\OrganizationInfoUrlHelper;
?>

<div class="organizations-list-index">
    <h3>Образовательные организации региона <small><a href="<?=RegionInfoUrlHelper::getOrganizationListUrl()?>">Перейти к общему списку</a></small></h3>

    <ul class="views">
    <?php foreach($arResult['organizationsList'] as $organization):?>
		<?if ($organization['name'] != '') :?>
        <li><a href="<?=OrganizationInfoUrlHelper::getOrganizationMainPageUrl($organization['id'])?>"><i class="fa fa-graduation-cap"></i><?=$organization['name']?></a></li>
		<?endif;?>
    <?php endforeach;?>
    </ul>
</div>