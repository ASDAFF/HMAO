<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>

<? $page = $arResult['page'];?>

<div class="page-header">
	<h3><?=$page['pageTypeStr']?></h3>
</div>

<div class="organization-page-content">

    <?= $page['pageContent']?>

    <? if (!empty($page['files'])) :?>
        <table class="table table-bordered organization-page-files">
            <? foreach($page['files'] as $file):?>
                <tr>
                    <td><?=$file['organization_page_file_title']?></td>
                    <td><a href="<?=$file['href']?>" class="badge badge-info">Скачать</a></td>
                </tr>
            <? endforeach;?>
        </table>
    <? endif; ?>

</div>
