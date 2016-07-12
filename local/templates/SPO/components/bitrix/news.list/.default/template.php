<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="news-list">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>

<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="news-item blog" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <div class="blog-body">

            <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
                <? $icon_file = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], array('width'=>150, 'height'=>100), BX_RESIZE_IMAGE_EXACT, true); ?>

                <?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                        <img src="<?=$icon_file["src"]?>" width="<?=$icon_file["width"]?>" height="<?=$icon_file["height"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>" />
                    </a>
                <?else:?>
                    <img src="<?=$icon_file["src"]?>" width="<?=$icon_file["width"]?>" height="<?=$icon_file["height"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>" />
                <?endif;?>
            <?endif?>

            <?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
                <h5>
                    <?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
                        <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a>
                    <?else:?>
                        <?echo $arItem["NAME"]?>
                    <?endif;?>
                </h5>
            <?endif;?>

            <?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
                <div class="news-date-time"><?=$arItem["DISPLAY_ACTIVE_FROM"];?></div>
            <?endif?>

            <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
                <p><?echo $arItem["PREVIEW_TEXT"];?></p>
            <?endif;?>

            <?foreach($arItem["FIELDS"] as $code=>$value):?>
                <small>
                <?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
                </small><br />
            <?endforeach;?>

            <?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
                <small>
                <?=$arProperty["NAME"]?>:&nbsp;
                <?if(is_array($arProperty["DISPLAY_VALUE"])):?>
                    <?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
                <?else:?>
                    <?=$arProperty["DISPLAY_VALUE"];?>
                <?endif?>
                </small><br />
            <?endforeach;?>

        </div>
	</div>
<?endforeach;?>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
