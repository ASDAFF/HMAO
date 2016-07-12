<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php use Spo\Site\Helpers\AbiturientOfficeUrlHelper; ?>

<p><?= $arResult['message']?></p>

<p>
    <a href="<?= AbiturientOfficeUrlHelper::getApplicationListUrl() ?>" class="btn btn-info"><i class="fa fa-list-alt" style="margin-right: 5px;"></i> Вернуться к списку заявок</a>
</p>
