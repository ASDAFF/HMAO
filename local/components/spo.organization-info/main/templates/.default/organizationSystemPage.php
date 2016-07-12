<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php
use Spo\Site\Helpers\OrganizationInfoUrlHelper;
use Spo\Site\Helpers\AbiturientOfficeUrlHelper;

$organizationId = $arResult['VARIABLES']['organizationId'];
?>

<!--h2><?=$arResult['organizationName'];?></h2-->

<div class="apply-button">
    <? if($arResult['EroreUser']!=1){?>
        <a href="<?=AbiturientOfficeUrlHelper::getApplicationCreateUrl($organizationId)?>" class="btn btn-info"><i class="fa fa-file-text"></i> Подать заявку</a>
    <?}else{?>
        <a href="?erroUser=1" class="btn btn-info"><i class="fa fa-file-text"></i> Подать заявку</a>
    <? }?>
</div>

<div class="row">
    <div class="col-md-3">

        <?$APPLICATION->IncludeComponent(
            "spo.organization-menu",
            "",
            Array(
                'organizationId' => $organizationId,
            )
        );?>

    </div>
    <div class="col-md-9">

        <?$APPLICATION->IncludeComponent(
            "spo.organization-info:organization.system.page",
            "",
            Array(
                'organizationId' => $organizationId,
                'section' => $arResult['VARIABLES']['section'],
            ),
            $component
        );?>

    </div>
</div>