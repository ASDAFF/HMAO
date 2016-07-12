<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    global $USER;
    /* @var $APPLICATION */
    $pageUrl     = $APPLICATION->GetCurPage(true);
    $isIndexPage = $pageUrl !== SITE_DIR."index.php";

    //todo Поменять идентификаторы групп на константы каким-то образом
    $isAbiturient            = CSite::InGroup(array(5));
    $isOrganizationEmployee  = CSite::InGroup(array(7));
    $isEduDepartmentEmployee = CSite::InGroup(array(8));
    $pluginMode             = ''; // '.min'
?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            <?$APPLICATION->ShowTitle();?>
        </title>
        <link rel="shortcut icon" type="image/png" href="<?=SITE_TEMPLATE_PATH?>/images/favicon.png" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/vendors/jquery.loadmask.css">
        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/vendors/jquery.datetimepicker.css">
        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/vendors/font-awesome.min.css">
        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/vendors/everest.css">
        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/styles.css">
        <script type="application/javascript" src="//code.jquery.com/jquery-1.11.3<?=$pluginMode?>.js"></script>
        <script type="application/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap<?=$pluginMode?>.js"></script>
        <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/vendors/jquery.loadmask/jquery.loadmask<?=$pluginMode?>.js"></script>
        <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/vendors/jquery.datetimepicker/jquery.datetimepicker.js"></script>
        <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/vendors/jquery.validate/jquery.validate<?=$pluginMode?>.js"></script>
        <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/vendors/jquery.validate/additional-methods<?=$pluginMode?>.js"></script>
        <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/vendors/jquery.validate/localization/messages_ru<?=$pluginMode?>.js"></script>
        <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/vendors/jquery.validate/jquery.inputmask.js"></script>
        <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/vendors/jquery.validate/jquery.inputmask.date.extensions.js"></script>
        <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/spo_index.js"></script>
        <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/sortable.js"></script>
        <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/form-ui.js"></script>
        <?if($pageUrl=='/abiturient-office/profile/index.php' || $pageUrl=='/organization-office/abiturient/profile'):?>
            <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-ui-1.10.2.custom.min.js"></script>

            <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.primepix.kladr.js"></script>
            <script type="application/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/example4.js"></script>
            <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/jquery-ui-1.10.2.custom.min.css">
        <?endif;?>
        <?$APPLICATION->ShowHead();?>
    </head>
    <body>
        <div id="panel">
            <?$APPLICATION->ShowPanel();?>
        </div>

        <div class="page-wrapper">
            <div class="spo-topline">
                <div class="container">
                    <div class="user-menu">
                        <?if($USER->IsAuthorized()){?>
                            <?if($isAbiturient){?>
                                <a href="/abiturient-office/profile/"><i class="fa fa-user"></i> <?= $USER->GetLogin()?></a>
                                <div class="dropdown dropdown-link" id="switch-boards">
                                    <a id="board-title" class="dropdown-toggle" data-toggle="dropdown" data-target="#switch-boards">Кабинет абитуриента <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/abiturient-office/">Список моих заявок</a></li>
                                        <li><a href="/abiturient-office/profile/">Профиль абитуриента</a></li>
                                    </ul>
                                </div>
                            <?}elseif($isOrganizationEmployee){?>
                                <a href="/organization-office/"><i class="fa fa-user"></i> <?= $USER->GetLogin()?></a>
                                <a href="/organization-office/">Кабинет организации</a>
                            <?}elseif($isEduDepartmentEmployee){?>
                                <a href="/edu-department-office/"><i class="fa fa-user"></i> <?= $USER->GetLogin()?></a>
                                <a href="/edu-department-office/">Кабинет сотрудника департамента образования</a>
                            <?}?>
                            <a href="/auth/?logout=YES">Выход</a>
                        <?}else{?>
                            <a href="/auth">Вход</a>
                            <a href="/auth/abiturient-registration.php">Регистрация</a>
                        <?}?>
                    </div>
                </div>
            </div>
            <div class="spo-page-header">
                <div class="container">
                    <div class="row">
                        <div class="title col-md-9">
                            <div class="spo-title"><a href="/">Портал профессионального образования</a></div>
                            <div class="region-title">
                                <a href="/">
                                    <? $APPLICATION->IncludeFile(SITE_DIR."local/includes/region-name.php", Array(), Array("MODE"=>"text")); ?>
                                </a>
                            </div>
                        </div>

                        <!--div class="col-md-3"><div class="logo"><a href="/"><img src="<?=SITE_TEMPLATE_PATH?>/images/spo-logo.png" alt="Логотип СПО" /></a></div></div-->
                    </div>
                </div>
            </div>
            <div class="header-nav">
                <div class="container">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "spo.mainmenu",
                            array(
                                "ROOT_MENU_TYPE" => "top",
                                "MAX_LEVEL" => "2",
                                "CHILD_MENU_TYPE" => "left",
                                "USE_EXT" => "Y",
                                "DELAY" => "N",
                                "ALLOW_MULTI_SELECT" => "N",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "MENU_CACHE_GET_VARS" => array()
                            ),
                            false
                        );?>
                </div>
            </div>

            <div class="page-content">
                <div class="container">

                    <div id="ui-message-panel"></div>

                    <?if($isIndexPage){?>

                        <?$APPLICATION->IncludeComponent(
	"bitrix:breadcrumb", 
	"breadcrumbs", 
	array(
		"START_FROM" => "1",
		"PATH" => "",
		"SITE_ID" => "s1"
	),
	false
);?>

                        <h1 class="page-title"><?$APPLICATION->ShowTitle(false)?></h1>

                        <div class="text-content">
                    <?}?>
