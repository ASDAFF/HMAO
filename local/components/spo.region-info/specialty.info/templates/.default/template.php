<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php
use Spo\Site\Helpers\RegionInfoUrlHelper;

$specialty = $arResult['specialtyInfo'];
?>


<div class="specialty-page">

    <h3><?=$specialty['specialtyTitle']?></h3>

    <p class="sp-info">
        Группа специальностей (ФГОС): <strong><?=$specialty['specialtyGroupTitle']?></strong><br/>
        Код специальности (ФГОС): <strong><?=$specialty['specialtyCode']?></strong><br/>
    </p>

    <?php if (!empty($specialty['specialtyQualifications'])):?>
        <p>
        Квалификации:
        <ul>
            <?php foreach ($specialty['specialtyQualifications'] as $qualification):
                if (!empty($qualification['title'])) {
                ?>
                <li><?=$qualification['title']?></li>
            <?php } endforeach;?>
        </ul>
        </p>
    <?php endif;?>

    <div class="sp-description">
        <?=$specialty['specialtyDescription']?>
    </div>

    <?php if ($arResult['cityWithSelectedSpecialty'][0]['organizationCount']>0):?>
        <hr>
        <h4>Города с учебными заведениями по специальности &laquo;<?=$specialty['specialtyTitle']?>&raquo;</h4>
        <ul>
            <?php foreach($arResult['cityWithSelectedSpecialty'] as $city):?>
            <li>
                <a href="<?=RegionInfoUrlHelper::getOrganizationListUrl(array('organizationFilter[specialty][]' => $specialty['specialtyId'], 'organizationFilter[city]' => $city['id']))?>">
                    <?=$city['name']?>
                </a>
            </li>
            <?php endforeach;?>
        </ul>
    <?php endif;?>

</div>

