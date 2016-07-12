<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Spo\Site\Helpers\OrganizationOfficeUrlHelper as Url;

/**
 * @var $APPLICATION
 * @var $arResult
 */
$organization = $arResult['organizationModel'];
$applicationCurrentList = $arResult['applicationList']['currentList'];
$applicationArchiveList = $arResult['applicationList']['archiveList'];
$pagesList = $arResult['pages']['list'];
?>

<div class="organisation-dashboard">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <a href="<?=Url::toApplicationList()?>" class="btn btn-sm btn-default"><i class="fa fa-edit"></i> Перейти к полному списку</a>
            <div class="panel-title">Последние необработанные заявки</div>
        </div>
        <table class="table table-hover org-application-list">
            <?foreach($applicationCurrentList as $currentApplication){
                $creation_date = ParseDateTime($currentApplication['creationDate'],"DD-MM-YYYY");
                $creation_date_display = $creation_date["DD"]." ".ToLower(GetMessage("MONTH_".intval($creation_date["MM"])."_S"))." ".$creation_date["YYYY"];
                /**
                 * @var DateTime $createdDate
                 */
                //$createdDate = $application['applicationCreationDate'];<td>$createdDate->format('Y-m-d H:i:s')</td>
                $user = $currentApplication['user'];
                ?>
                <tr>
                    <td class="date"><?=$creation_date_display;?></td>
                    <td>
                        <strong><?=$user['userFullName'];?></strong>
                    </td>
                    <td>
                        <? if(!empty($user['user_valid_id'])):?>
                            <small>Профиль обработан</small>
                        <? endif; ?>
                    </td>
                    <td>
                        <small><?=$user['userEmail'];?></small>
                    </td>
                    <td class="id"><?=$currentApplication['id']?></td>
                </tr>
            <?}?>
        </table>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <a href="<?=Url::toApplicationArchiveList()?>" class="btn btn-sm btn-default"><i class="fa fa-edit"></i> Перейти к полному списку</a>
            <div class="panel-title">Архив обработанных заявок</div>
        </div>
        <table class="table table-hover org-application-list">
            <?foreach($applicationArchiveList as $archiveApplication){
                $creation_date = ParseDateTime($archiveApplication['creationDate'],"DD-MM-YYYY");
                $creation_date_display = $creation_date["DD"]." ".ToLower(GetMessage("MONTH_".intval($creation_date["MM"])."_S"))." ".$creation_date["YYYY"];
                $user = $archiveApplication['user'];
                ?>
                <tr>
                    <td class="date"><?=$creation_date_display;?></td>
                    <td>
                        <strong><?=$user['userFullName'];?></strong>
                    </td>
                    <td>
                        <? if(!empty($user['user_valid_id'])):?>
                            <small>Профиль обработан</small>
                        <? endif; ?>
                    </td>
                    <td>
                        <small><?=$user['userEmail'];?></small>
                    </td>
                    <td class="id"><?=$archiveApplication['id']?></td>
                </tr>
            <?}?>
        </table>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <? if($arResult['NeModerator']==1){?>
            <a href="<?=Url::toOrganizationInfoEdit()?>" class="btn btn-sm btn-default"><i class="fa fa-edit"></i> Редактировать</a>
            <? }?>
            <div class="panel-title">Информация</div>
        </div>

        <table class="table brief-info">
            <tr>
                <th>ID</th>
                <td><?=$organization['organizationId']?></td>
            </tr>
            <tr>
                <th>Название</th>
                <td><?=$organization['organizationName']?></td>
            </tr>
            <tr>
                <th>Полное название</th>
                <td><?=$organization['organizationFullName']?></td>
            </tr>
            <tr>
                <th>Год основания</th>
                <td><?=$organization['organizationFoundationYear']?></td>
            </tr>
            <tr>
                <th>Адрес</th>
                <td><?=$organization['organizationAddress']?></td>
            </tr>
            <tr>
                <th>E-mail</th>
                <td><?=$organization['organizationEmail']?></td>
            </tr>
            <tr>
                <th>Телефон</th>
                <td><?=$organization['organizationPhone']?></td>
            </tr>
            <tr>
                <th>Сайт</th>
                <td><a target="_blank" href="<?=$organization['organizationSite']?>"><?=$organization['organizationSite']?></a></td>
            </tr>
        </table>

    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">Страницы</div>
        <table class="table table-hover pages-list">
            <?foreach($pagesList as $page){?>
                <tr>
                    <th><?=$page['pageId']?></th>
                    <td><?=$page['pageTypeStr']?></td>
                    <td style="text-align: right">
                        <? if($arResult['NeModerator']==1){?>
                        <a href="<?=Url::toStaticPageEdit($page['pageId'])?>" class="btn btn-default btn-xs"><i class="fa fa-edit"></i> &nbsp;Редактировать</a>
                        <? }?>
                    </td>
                </tr>
            <?}?>
        </table>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <a href="<?=Url::toSpecialtyList()?>" class="btn btn-sm btn-default"><i class="fa fa-edit"></i> Перейти к списку</a>
            <div class="panel-title">Образовательные программы организации</div>
        </div>
    </div>


    <div class="panel panel-primary">
        <div class="panel-heading">
            <a href="<?=Url::toAdmissionPlanEdit()?>" class="btn btn-sm btn-default"><i class="fa fa-edit"></i> Перейти к плану приёма</a>
            <div class="panel-title">План приёма</div>
        </div>
    </div>
</div>