<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    use Spo\Site\Helpers\PagingHelper;
    use Spo\Site\Helpers\OrganizationOfficeUrlHelper as Url;
    use Spo\Site\Dictionaries\ApplicationStatus as AppStatus;
    use Spo\Site\Dictionaries\TrainingLevel;

    /**
     * @var $APPLICATION
     * @var $arResult
     * @var $paging PagingHelper
     */

    $applicationList        =   $arResult['applications']['list'];
    $totalCount             =   $arResult['applications']['totalCount'];
    $applicationStatus      =   $arResult['applicationStatus'];
    $applicationFundingType =   $arResult['applicationFundingType'];
    $paging                 =   $arResult['paging'];
    $limit                  =   $paging->getLimit();
    $currentPage            =   $paging->getCurrentPage();
    $pageCount              =   $paging->getPageCountByTotalRecordCount($totalCount);
?>
<form action="#" method="get" id="FormSerch">
<div class="application-list-page">
    <h2 class="page-header">Список заявок на поступление (Архив)</h2>

    <div>
        <p class="text-muted">
            В данном разделе вы можете просматривать заявки, поступившие от абитуриентов.<br/>
            Для удобства просмотра доступен ряд фильтров.<br/>
            Существует возможность просмотра профиля абитуриента.<br/>

            Вы можете изменить статус заявок. При изменении статуса абитуриенту отправляется письмо.
        </p>
    </div>

    <div class="row">

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

            <div class="panel panel-info">
                <div class="panel-heading">Фильтр</div>
                <div class="panel-body form-horizontal">
                    <? $DataYer=date("Y");$DataYer=$DataYer-2015+1;?>
                    <div class="form-group filter-year" role="group">
                        <label class="col-lg-5 control-label">Год подачи заявки</label>
                        <div class="col-lg-7">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="filter-value">Любой</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a data-value="" href="#">Любой</a></li>
                                <? for($i=0;$DataYer>$i;$i=$i+1){?>
                                <li><a data-value="<?=2015+$i?>" href="#"><?=2015+$i?></a></li>
                                <? }?>
                            </ul>
                        </div>
                    </div>

                        <!--Поиск по дате1-->
                    <div class="form-group filter-data" role="group">
                        <label class="col-lg-5 control-label">Дата</label>
                        <div class="col-lg-7">     
                            <div class="form-group" role="group">
                                <label class="col-lg-2 control-label">с</label>
                                <div class="col-lg-10">
                                    <input
                                        name="Serch[Data1]"
                                        type="date" min="2015-01-01"
                                        class="form-control inputControl"
                                        onkeydown="NewPoicKlic.onclic(event.keyCode);"
                                        style="/*width:23px;*/display:inline-block;"
                                        value="<?=$_GET['Serch']['Data1']?>"
                                        onchange="$('#FormSerch').submit()"
                                    >
                                </div>
                            </div>
                            <div class="form-group" role="group">
                                <label class="col-lg-2 control-label">по</label>
                                <div class="col-lg-10">
                                    <input
                                        name="Serch[Data2]"
                                        type="date" min="2015-01-01"
                                        class="form-control inputControl"
                                        onkeydown="NewPoicKlic.onclic(event.keyCode);"
                                        style="/*width:204px;*/display:inline-block;"
                                        placeholder="01.01.2015"
                                        pattern="[0-9]{2}\.[0-9]{2}\.[0-9]{4}"
                                        value="<?=$_GET['Serch']['Data2']?>"
                                        onchange="$('#FormSerch').submit()"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                        <!--Поиск ID-->
                    <div class="form-group filter-status" role="group">
                        <label class="col-lg-5 control-label">ID</label>
                        <div class="col-lg-7">
                            <input type="text"
                                   name="Serch[ID]"
                                   class="form-control inputControl"
                                   onkeydown="NewPoicKlic.onclickk(event.keyCode);"
                                   value="<?=$_GET['Serch']['ID']?>">
                        </div>
                    </div>
                        <!--Поиск Имени Фамилия Отчество-->
                        <div class="form-group filter-status" role="group">
                            <label class="col-lg-5 control-label">Абитуриент</label>
                            <div class="col-lg-7">
                                <input type="text" 
                                       name="Serch[Name]" 
                                       class="form-control inputControl"
                                       onkeydown="NewPoicKlic.onclickk(event.keyCode);"
                                       value="<?=$_GET['Serch']['Name']?>"
                                >
                                <input type="hidden" name="speciality" class="inputControl" value="<?=$_GET['speciality']?>">
                                <input type="hidden" name="studymode" class="inputControl" value="<?=$_GET['studymode']?>">
                                <input type="hidden" name="fundingtype" class="inputControl" value="<?=$_GET['fundingtype']?>">
                                <input type="hidden" name="needhostel" class="inputControl" value="<?=$_GET['needhostel']?>">
                                <input type="hidden" name="baseeducation" class="inputControl" value="<?=$_GET['baseeducation']?>">
                                <input type="hidden" name="status" class="inputControl" value="<?=$_GET['status']?>">
                                <input type="hidden" name="limit" class="inputControl" value="<?=$_GET['limit']?>">
                            </div>
                        </div>
                    </form>
                    <div class="form-group filter-cpicaliti" role="group">
                        <label class="col-lg-5 control-label">Специальность</label>
                        <div class="col-lg-7">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="filter-value">Любая</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a data-value="" href="#">Любая</a></li>
                                <?foreach($arResult['GetListSpiciality'] as $item){?>
                                    <li><a data-value="<?=$item['id']?>" href="#"><?=$item['specialtyTitle']?></a></li>
                                <?}?>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group filter-StudyMode" role="group">
                        <label class="col-lg-5 control-label">Форма обучения</label>
                        <div class="col-lg-7">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="filter-value">Любая</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a data-value="" href="#">Любая</a></li>
                                <?foreach($arResult['GetListStudyMode'] as $item){?>
                                    <li><a data-value="<?=$item['ID']?>" href="#"><?=$item['studyMode']?></a></li>
                                <?}?>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group filter-FundingType" role="group">
                        <label class="col-lg-5 control-label">Вид финансирования</label>
                        <div class="col-lg-7">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="filter-value">Любая</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a data-value="" href="#">Любая</a></li>
                                <?foreach($arResult['GetListFundingType'] as $item){?>
                                    <li><a data-value="<?=$item['ID']?>" href="#"><?=$item['FundingType']?></a></li>
                                <?}?>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group filter-NeedHostel" role="group">
                        <label class="col-lg-5 control-label">Потребность в общежитии</label>
                        <div class="col-lg-7">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="filter-value">Любая</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a data-value="" href="#">Любая</a></li>
                                <?foreach($arResult['GetListNeedHostel'] as $item){?>
                                    <li><a data-value="<?=$item['ID']?>" href="#"><?=$item['needHostel']?></a></li>
                                <?}?>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group filter-baseEducation" role="group">
                        <label class="col-lg-5 control-label">Уровень подготовки</label>
                        <div class="col-lg-7">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="filter-value">Любая</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a data-value="" href="#">Любая</a></li>
                                <?foreach($arResult['GetListBaseEducation'] as $item){?>
                                    <li><a data-value="<?=$item['ID']?>" href="#"><?=$item['BaseEducation']?></a></li>
                                <?}?>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group filter-userValid" role="group">
                        <label class="col-lg-5 control-label">Профиль пользователя</label>
                        <div class="col-lg-7">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="filter-value">Любой</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a data-value="" href="#">Любой</a></li>
                                <li><a data-value="1" href="#">Верифицирован</a></li>
                                <li><a data-value="0" href="#">Не верифицирован</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group filter-status" role="group">
                        <label class="col-lg-5 control-label">Статус</label>
                        <div class="col-lg-7">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="filter-value">Любой</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a data-value="" href="#">Любой</a></li>
                                <?
                                foreach($applicationStatus as $status => $value)
                                {
                                    if (in_array($status, array(1, 2, 3, 9)))
                                        {?>
                                            <li><a data-value="<?=$status?>" href="#"><?=$value?></a></li><?
                                        }?><?
                                }?>
                                <!--<li><a data-value="2" href="#">Принято</a></li>
                                <li><a data-value="3" href="#">Отклонено</a></li>-->
                            </ul>
                        </div>
                    </div>
                    <div class="form-group filter-pagingLimit" role="group">
                        <label class="col-lg-5 control-label">Количества записей</label>
                        <div class="col-lg-7">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="filter-value">10</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a data-value="" href="#">10</a></li>
                                <li><a data-value="20" href="#">20</a></li>
                                <li><a data-value="50" href="#">50</a></li>

                            </ul>
                        </div>
                    </div>
                    <!--div class="form-group filter-funding-type" role="group">
                        <label class="col-lg-5 control-label">Вид финансирования</label>
                        <div class="col-lg-7">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="filter-value">Любой</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a data-value="" href="#">Любой</a></li>
                                <?foreach($applicationFundingType as $fType=>$value){?>
                                    <li><a data-value="<?=$fType?>" href="#"><?=$value?></a></li>
                                <?}?>
                            </ul>
                        </div>
                    </div-->

                    <div class="form-group">
                        <div class="col-lg-7 col-lg-offset-5">
                            <button id="reset-filter-btn" type="button" class="btn btn-success">Сбросить фильтр</button>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

            <div class="panel panel-info">
                <div class="panel-heading">Сортировка</div>
                <div class="panel-body form-horizontal">

                    <div class="form-group filter-order-field" role="group">
                        <label class="col-lg-6 control-label">Сортировать по свойству</label>
                        <div class="col-lg-6">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="filter-value">Выбрать</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <!--li><a data-value="" href="#">*не сортировать*</a></li-->
                                <li><a data-value="applicationCreateDate" href="#">Дата подачи</a></li>
                                <li><a data-value="applicationId" href="#">ID</a></li>
                                <li><a data-value="userLastname" href="#">Абитуриент</a></li>
                                <!--li><a data-value="userName" href="#">Абитуриент. Имя</a></li-->
                                <!--li><a data-value="userSecondname" href="#">Абитуриент. Отчество</a></li-->

                            </ul>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-6 control-label">Направление</label>
                        <div class="col-lg-6">
                            <!--div class="btn-group filter-order-by" role="group">
                                <button type="button" data-value="ASC"  class="btn btn-default">По возрастанию</button>
                                <button type="button" data-value="DESC" class="btn btn-default">По убыванию</button>
                            </div-->
                            <div class="filter-order-by">
                                <div><span data-value="ASC"><i class="fa"></i> По возрастанию</span></div>
                                <div><span data-value="DESC"><i class="fa"></i> По убыванию</span></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

            <div class="panel panel-info">
                <div class="panel-heading">Поля</div>
                <div class="panel-body form-horizontal">

                    <div class="form-group filter-order-field" role="group" style="text-align: center;">
                            <select name="Shapka[]" size="6" multiple style="width: 90%;margin: 0px 5%;">
                                <? foreach($arResult['MassivTitel'] as $kei=>$item){?>
                                 <option value="<?=$kei?>" <?if($arResult['selecttid'][$kei] == 1){?>selected<?}?>><?=$item?></option>
                                <?}?>
                            </select>
                            <input type="submit" value="Сохранить" style="
                            color: #fff;
                            background-color: #76bbad;
                            border:0px;
                            padding: 6px 15px;
                            margin-top: 15px;
                            margin-bottom: 0px;
                            ">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
<?php
/*echo "<pre>";
print_r($arResult['selecttid']['fundingType']);
echo "</pre>";*/
?>

    <div class="org-application-list-full">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Абитуриент</th>
                    <th>Документы</th>
                    <?if($arResult['selecttid']['specialtyTitle']==1 or count($arResult['selecttid'])==0){?>
                     <th>Специальность</th>
                    <?}?>
                    <?if($arResult['selecttid']['studyMode']==1 or count($arResult['selecttid'])==0){?>
                     <th>Форма обучения</th>
                    <?}?>
                    <?if($arResult['selecttid']['fundingType']==1 or count($arResult['selecttid'])==0){?>
                     <th>Вид финансирования</th>
                    <?}?>
                    <?if($arResult['selecttid']['needHostel']==1 or count($arResult['selecttid'])==0){?>
                     <th>Потребность в&nbsp;общежитии</th>
                    <?}?>
                    <?if($arResult['selecttid']['trainingLevel']==1 or count($arResult['selecttid'])==0){?>
                     <th>Уровень подготовки</th>
                    <?}?>
                    <?if($arResult['selecttid']['creationDate']==1 or count($arResult['selecttid'])==0){?>
                     <th>Дата подачи</th>
                    <?}?>
                    <?if($arResult['selecttid']['status']==1 or count($arResult['selecttid'])==0){?>
                     <th>Статус</th>
                    <?}?>
                </tr>
            </thead>
            <tbody>
            <?foreach($applicationList as $application){
                $user = $application['user'];
                $status = $application['statusCode'];
                $changeStatuses = AppStatus::getAvailableStatusChanges($status);
                $availableStatuses = array();
                foreach($changeStatuses as $changeStatus){
                    $availableStatuses[$changeStatus] = AppStatus::getValue($changeStatus);
                }
                ?>
                <tr>
                    <td><?=$application['id']?></td>
                    <td>
                        <a target="_blank" href="<?=Url::toAbiturientProfile($user['userId'])?>">
                            <?=$user['userFullName'] . ' (' . $user['userEmail'] . ')'?>
                        </a>
                    </td>
                    <?if(!empty($application['docorigin'])){?>
                     <td>Оригинал</td>
                    <?} else {?>
                     <td>Копия</td>
                    <?}?>
                    <?if($arResult['selecttid']['specialtyTitle']==1 or count($arResult['selecttid'])==0){?>
                      <td><?=$application['specialtyTitle']?></td>
                    <?}?>
                    <?if($arResult['selecttid']['studyMode']==1 or count($arResult['selecttid'])==0){?>
                     <td><?=$application['studyMode']?></td>
                    <?}?>
                    <?if($arResult['selecttid']['fundingType']==1 or count($arResult['selecttid'])==0){?>
                     <td><?=$application['fundingType']?></td>
                    <?}?>
                    <?if($arResult['selecttid']['needHostel']==1 or count($arResult['selecttid'])==0){?>
                     <td><span class="fa <?=$application['needHostel'] ? 'fa-check' : 'fa-minus';?>"></span></td>
                    <?}?>
                    <?if($arResult['selecttid']['trainingLevel']==1 or count($arResult['selecttid'])==0){?>
                     <td><?=TrainingLevel::getValue($application['baseEducation'])?></td>
                    <?}?>
                    <?if($arResult['selecttid']['creationDate']==1 or count($arResult['selecttid'])==0){?>
                     <td><?=$application['creationDate']?></td>
                    <?}?>
                    <?if($arResult['selecttid']['status']==1 or count($arResult['selecttid'])==0){?>
                    <td>
                        <div class="btn-group">
<!--                            --><?//if(count($availableStatuses) > 0){?>
<!--                                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">-->
<!--                                    --><?//=$application['status']?><!-- <span class="caret"></span>-->
<!--                                </button>-->
<!--                                <ul class="dropdown-menu" role="menu">-->
<!--                                    <li class="disabled">-->
<!--                                        <span>Изменить статус на:</span>-->
<!--                                    </li>-->
<!--                                    <li class="divider"></li>-->
<!--                                    --><?//foreach($availableStatuses as $code=>$label){
//                                        if($code === $status){continue;}?>
<!--                                        <li>-->
<!--                                            <a href="--><?//=Url::toApplicationStatusChange($application['id'], $code)?><!--">-->
<!--                                                --><?//=$label?>
<!--                                            </a>-->
<!--                                        </li>-->
<!--                                    --><?//}?>
<!--                                </ul>-->
<!--                            --><?//}else{?>
                                    <?=AppStatus::getValue($application['status'])                                  ?>
<!--                            --><?//}?>
                        </div>
                    </td>
                    <?}?>
                    <td>
                        <a href="<?=Url::toApplicationEdit($application['id'])?>">
                            <i class="fa fa-pencil-square-o"></i>
                        </a>
                    </td>
                </tr>
            <?}?>
            </tbody>
        </table>
    </div>
    <?
    $APPLICATION->IncludeComponent("spo.paging", "", array(
        'PageCount'     =>  $pageCount,
        'TotalCount'    =>  $totalCount,
        'CurrentPage'   =>  $currentPage,
        'Limit'         =>  $limit,
    ));?>
<?php
 $Ogranichenie="";
 foreach ($_GET['Shapka'] as $item){
     $Ogranichenie=$Ogranichenie.'&Shapka[]='.$item;
 }
?>
    <script>
        var filterParams = {
            orderBy: {
                name: 'orderBy',
                def:  'DESC'
            },
            orderField: {
                name: 'orderField',
                def:   false
            },
            year: {
                name: 'year',
                def:   false
            },
            status: {
                name: 'status',
                def:   false
            },
            funding: {
                name: 'funding',
                def:   false
            },
            speciality: {
                name: 'speciality',
                def:   false
            },
            studymode: {
                name: 'studymode',
                def:   false
            },
            fundingtype: {
                name: 'fundingtype',
                def:   false
            },
            needhostel: {
                name: 'needhostel',
                def:   false
            },
            baseeducation: {
                name: 'baseeducation',
                def:   false
            },
            limit: {
                name: 'limit',
                def: false
            },
            valid:{
                name: 'valid',
                def: false
            }
        };

        $(function(){
            $filterOrderBy = $('.filter-order-by');
            $filterYear = $('.filter-year');
            $filterStatus = $('.filter-status');
            $filterPagingLimit = $('.filter-pagingLimit');
            $filterUserValid = $('.filter-userValid');
            $filterSpeciality = $('.filter-cpicaliti');
            $filterStudyMode = $('.filter-StudyMode');
            $filterFundingType = $('.filter-FundingType');
            $filterNeedHostel = $('.filter-NeedHostel');
            $filterBaseEducation = $('.filter-baseEducation');
            //$filterFundingType = $('.filter-funding-type');
            $filterOrderField = $('.filter-order-field');
            $resetFilterBtn = $('#reset-filter-btn');

            $resetFilterBtn.on('click', function(){
                var name;
                for(name in filterParams){
                    spoUrl.removeParam(filterParams[name]['name']);
                }
                $('.inputControl').val('');
                var Ogranich='<?=$Ogranichenie?>';
                window.location='/organization-office/application/list/archive?'+Ogranich;
            });
            //Фильтр статуса
            $filterOrderBy
                .find('span')
                .on('click', function(event){
                    $btn = $(this);
                    spoUrl.changeParam(filterParams.orderBy.name, $btn.data('value')).removePaging().changeLocation();
                })
                .filter('[data-value="' + (
                    spoUrl.hasParam(filterParams.orderBy.name) ?
                    spoUrl.getParam(filterParams.orderBy.name) :
                    filterParams.orderBy.def
                    ) + '"]'
                )
                .addClass('active');
            //Фильтр года
            $filterYear
                .find('.dropdown-menu a')
                .on('click', function(event){
                    $link = $(event.target);
                    value = $link.data('value');
                    if(value.length === 0){
                        spoUrl.removeParam(filterParams.year.name).removePaging().changeLocation();
                    }else{
                        spoUrl.changeParam(filterParams.year.name, value).removePaging().changeLocation();
                    }
                    return false;
                });
            if(spoUrl.hasParam(filterParams.year.name)){
                $filterYear.find('button').addClass('active');
                $filterYear.find('.filter-value').html(spoUrl.getParam(filterParams.year.name));
            }
            //Фильтр статуса
            $filterStatus
                .find('.dropdown-menu a')
                .on('click', function(event){
                    $link = $(event.target);
                    value = $link.data('value');
                    if(value.length === 0){
                        spoUrl.removeParam(filterParams.status.name).removePaging().changeLocation();
                    }else{
                        spoUrl.changeParam(filterParams.status.name, value).removePaging().changeLocation();
                    }
                    return false;
                });
            if(spoUrl.hasParam(filterParams.status.name)){
                $filterStatus.find('button').addClass('active');
                $filterStatus.find('.filter-value').html(
                    $filterStatus.find('ul li a[data-value="' + spoUrl.getParam(filterParams.status.name) + '"]').html()
                );
            }
            //Фильтр по валидации
            $filterUserValid
                .find('.dropdown-menu a')
                .on('click', function(event){
                    $link = $(event.target);
                    value = $link.data('value');
                    if(value.length === 0){
                        spoUrl.removeParam(filterParams.valid.name).removePaging().changeLocation();
                    }else{
                        spoUrl.changeParam(filterParams.valid.name, value).removePaging().changeLocation();
                    }
                    return false;
                });
            if(spoUrl.hasParam(filterParams.valid.name)){
                $filterUserValid.find('button').addClass('active');
                $filterUserValid.find('.filter-value').html(
                    $filterUserValid.find('ul li a[data-value="' + spoUrl.getParam(filterParams.valid.name) + '"]').html()
                );
            }
            //Количество записей
            $filterPagingLimit
                .find('.dropdown-menu a')
                .on('click', function(event){
                    $link = $(event.target);
                    value = $link.data('value');
                    if(value.length === 0){
                        spoUrl.removeParam(filterParams.limit.name).removePaging().changeLocation();
                    }else{
                        spoUrl.changeParam(filterParams.limit.name, value).removePaging().changeLocation();
                    }
                    return false;
                });
            if(spoUrl.hasParam(filterParams.limit.name)){
                $filterPagingLimit.find('button').addClass('active');
                $filterPagingLimit.find('.filter-value').html(
                    $filterPagingLimit.find('ul li a[data-value="' + spoUrl.getParam(filterParams.limit.name) + '"]').html()
                );
            }
            //Фильтр специальности
            $filterSpeciality
                .find('.dropdown-menu a')
                .on('click', function(event){
                    $link = $(event.target);
                    value = $link.data('value');
                    if(value.length === 0){
                        spoUrl.removeParam(filterParams.speciality.name).removePaging().changeLocation();
                    }else{
                        spoUrl.changeParam(filterParams.speciality.name, value).removePaging().changeLocation();
                    }
                    return false;
                });
            if(spoUrl.hasParam(filterParams.speciality.name)){
                $filterSpeciality.find('button').addClass('active');
                $filterSpeciality.find('.filter-value').html(
                    $filterSpeciality.find('ul li a[data-value="' + spoUrl.getParam(filterParams.speciality.name) + '"]').html()
                );
            }
            //Фильтр Форма обучения
            $filterStudyMode
                .find('.dropdown-menu a')
                .on('click', function(event){
                    $link = $(event.target);
                    value = $link.data('value');
                    if(value.length === 0){
                        spoUrl.removeParam(filterParams.studymode.name).removePaging().changeLocation();
                    }else{
                        spoUrl.changeParam(filterParams.studymode.name, value).removePaging().changeLocation();
                    }
                    return false;
                });
            if(spoUrl.hasParam(filterParams.studymode.name)){
                $filterStudyMode.find('button').addClass('active');
                $filterStudyMode.find('.filter-value').html(
                    $filterStudyMode.find('ul li a[data-value="' + spoUrl.getParam(filterParams.studymode.name) + '"]').html()
                );
            }
            //Фильтр Вид финансирования
            $filterFundingType
                .find('.dropdown-menu a')
                .on('click', function(event){
                    $link = $(event.target);
                    value = $link.data('value');
                    if(value.length === 0){
                        spoUrl.removeParam(filterParams.fundingtype.name).removePaging().changeLocation();
                    }else{
                        spoUrl.changeParam(filterParams.fundingtype.name, value).removePaging().changeLocation();
                    }
                    return false;
                });
            if(spoUrl.hasParam(filterParams.fundingtype.name)){
                $filterFundingType.find('button').addClass('active');
                $filterFundingType.find('.filter-value').html(
                    $filterFundingType.find('ul li a[data-value="' + spoUrl.getParam(filterParams.fundingtype.name) + '"]').html()
                );
            }
            //Фильтр Потребность в общежитии
            $filterNeedHostel
                .find('.dropdown-menu a')
                .on('click', function(event){
                    $link = $(event.target);
                    value = $link.data('value');
                    if(value.length === 0){
                        spoUrl.removeParam(filterParams.needhostel.name).removePaging().changeLocation();
                    }else{
                        spoUrl.changeParam(filterParams.needhostel.name, value).removePaging().changeLocation();
                    }
                    return false;
                });
            if(spoUrl.hasParam(filterParams.needhostel.name)){
                $filterNeedHostel.find('button').addClass('active');
                $filterNeedHostel.find('.filter-value').html(
                    $filterNeedHostel.find('ul li a[data-value="' + spoUrl.getParam(filterParams.needhostel.name) + '"]').html()
                );
            }
            //Фильтр Уровень подготовки
            $filterBaseEducation
                .find('.dropdown-menu a')
                .on('click', function(event){
                    $link = $(event.target);
                    value = $link.data('value');
                    if(value.length === 0){
                        spoUrl.removeParam(filterParams.baseeducation.name).removePaging().changeLocation();
                    }else{
                        spoUrl.changeParam(filterParams.baseeducation.name, value).removePaging().changeLocation();
                    }
                    return false;
                });
            if(spoUrl.hasParam(filterParams.baseeducation.name)){
                $filterBaseEducation.find('button').addClass('active');
                $filterBaseEducation.find('.filter-value').html(
                    $filterBaseEducation.find('ul li a[data-value="' + spoUrl.getParam(filterParams.baseeducation.name) + '"]').html()
                );
            }
//            $filterFundingType
//                .find('.dropdown-menu a')
//                .on('click', function(event){
//                    $link = $(event.target);
//                    value = $link.data('value');
//                    if(value.length === 0){
//                        spoUrl.removeParam(filterParams.funding.name).removePaging().changeLocation();
//                    }else{
//                        spoUrl.changeParam(filterParams.funding.name, value).removePaging().changeLocation();
//                    }
//                    return false;
//                });
//            if(spoUrl.hasParam(filterParams.funding.name)){
//                $filterFundingType.find('button').addClass('active');
//                $filterFundingType.find('.filter-value').html(
//                    $filterFundingType.find('ul li a[data-value="' + spoUrl.getParam(filterParams.funding.name) + '"]').html()
//                );
//            }

            $filterOrderField
                .find('.dropdown-menu a')
                .on('click', function(event){
                    $link = $(event.target);
                    value = $link.data('value');
                    if(value.length === 0){
                        spoUrl.removeParam(filterParams.orderField.name).removePaging().changeLocation();
                    }else{
                        spoUrl.changeParam(filterParams.orderField.name, value).removePaging().changeLocation();
                    }
                    return false;
                });
            var orderParam = spoUrl.hasParam(filterParams.orderField.name) ? spoUrl.getParam(filterParams.orderField.name) : 'applicationCreateDate';
            var value = $filterOrderField
                .find('.dropdown-menu a[data-value=' + orderParam + ']').html();
            $filterOrderField.find('button').addClass('active');
            $filterOrderField.find('.filter-value').html(value);
        });
        function PoicKlic(){
            this.onclickk=function(key){
                if(key==13){
                    $("#FormSerch").submit();
                }
            }
        }
        var NewPoicKlic=new PoicKlic();
    </script>
</div>