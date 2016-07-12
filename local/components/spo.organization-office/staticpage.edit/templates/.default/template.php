<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Spo\Site\Helpers\OrganizationOfficeUrlHelper as Url;

/* @var $arResult */
$organizationPage      = $arResult['organizationPageData'];
$organizationPageFiles = $organizationPage['files'];
?>
<style>
.files .input-group{
    width: 100%;
}
</style>
<?php if ($arResult['success']):?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $arResult['success'] ?>
    </div>
<?php endif; ?>

<h2 class="page-header">Редактирование страницы: <?=$organizationPage['pageTypeStr']?></h2>

<form method="post" enctype="multipart/form-data" action="<?=Url::toStaticPageEdit($organizationPage['pageId'])?>">

    <div class="panel panel-primary visual-editor-block">
        <div class="panel-heading">
            Визуальный редактор
        </div>
        <div class="panel-body">
            <?$APPLICATION->IncludeComponent(
                "bitrix:fileman.light_editor",
                "",
                Array(
                    "CONTENT" => $organizationPage['pageContent'],
                    "INPUT_NAME" => "OrganizationPage[organizationPageContent]",
                    "INPUT_ID" => "",
                    "WIDTH" => "100%",
                    "HEIGHT" => "300px",
                    "RESIZABLE" => "Y",
                    "AUTO_RESIZE" => "Y",
                    "VIDEO_ALLOW_VIDEO" => "N",
                    //        "VIDEO_MAX_WIDTH" => "640",
                    //        "VIDEO_MAX_HEIGHT" => "480",
                    //        "VIDEO_BUFFER" => "20",
                    //        "VIDEO_LOGO" => "",
                    //        "VIDEO_WMODE" => "transparent",
                    //        "VIDEO_WINDOWLESS" => "Y",
                    //        "VIDEO_SKIN" => "/bitrix/components/bitrix/player/mediaplayer/skins/bitrix.swf",
                    "USE_FILE_DIALOGS" => "N",
                    "ID" => "",
                    "JS_OBJ_NAME" => ""
                )
            );?>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">Загруженные файлы</div>

        <?if(empty($organizationPageFiles)):?>

            <div class="panel-body">
                <p style="margin: 0;">Нет загруженных файлов.</p>
            </div>

        <?else:?>

            <!-- Table -->
            <table class="table table-hover org-uploaded-files">
                <thead>
                    <tr>
                        <th class="id">ID</th>
                        <th class="title">Название файла</th>
                        <th>&nbsp;</th>
                        <th class="delete">Удалить</th>
                    </tr>
                </thead>
                <tbody>
                <?foreach($organizationPageFiles as $fileData){?>
                    <tr>
                        <td class="id"><?=$fileData['organization_page_file_id']?></td>
                        <td class="title">
                            <input type="text" class="form-control"
                                name="OrganizationPage[organizationPageFileTitle][<?=$fileData['organization_page_file_id']?>]"
                                value="<?=$fileData['organization_page_file_title']?>"
                            >
                        </td>
                        <td class="download">
                            <a href="<?=$fileData['href']?>" target="_blank"><i class="fa fa-download"></i> Скачать</a>
                        </td>
                        <td class="delete">
                            <div class="squared-check">
                                <input type="checkbox" name="OrganizationPage[deletableFiles][]" value="<?=$fileData['organization_page_file_id']?>" id="delete_<?=$fileData['organization_page_file_id']?>" />
                                <label for="delete_<?=$fileData['organization_page_file_id']?>"></label>
                            </div>
                        </td>
                    </tr>
                <?}?>
                </tbody>
            </table>

        <? endif; ?>

    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">Загрузить файлы</div>

        <div class="panel-body">

            <div class="org-new-files form-horizontal">

                <div class="blog new-file-item">
                    <div class="blog-body">

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Название</label>
                            <div class="col-lg-6"><input type="text" class="form-control" name="OrganizationPageFile[]" class="from-control" /></div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Файл</label>
                            <div class="col-lg-6"><input type="file" name="OrganizationPageFile[]" /></div>
                        </div>

                        <button class="btn btn-danger btn-rounded btn-sm remove-file" type="submit"><i class="fa fa-times"></i> Удалить</button>

                    </div>
                </div>

            </div>

            <button class="btn btn-success add-file"><i class="fa fa-plus"></i> Ещё ...</button>

        </div>

    </div>


    <div class="form-group">
        <div class="">
            <button class="btn btn-info" type="submit"><i class="fa fa-check"></i> Сохранить</button>
            <a class="btn btn-default" href="/organization-office/">Отмена</a>
        </div>
    </div>

<form>
<script>
    $(function(){

        $uploadableFileRow = $('.new-file-item').clone();

        $('.add-file').on('click', function(event){
            event.preventDefault();
            $('.org-new-files').append($uploadableFileRow.clone());
            return false;
        });

        $('.org-new-files').on('click', '.remove-file', function(event){
            event.preventDefault();
//            console.log('asd');
            $(this).parents('.new-file-item').remove();
            if($('.org-new-files .new-file-item').length === 0){
                $('.org-new-files').append($uploadableFileRow.clone());
            }
            return false;
        });

    });
</script>