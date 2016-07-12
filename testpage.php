<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тестовая страница - макет главной страницы");
?>

    <div class="row">
	<div class="col-md-7">
		<h3>Оформление услуги на сайте</h3>
		<hr>
		<p style="text-align: justify">
            <strong>ВНИМАНИЕ!</strong>
			1 октября 2014 года завершен прием и регистрация заявлений на поступление в колледжи и техникумы Якутии через государственный портал Специальное Профессиональное Образование. Срок приема заявлений на обучение в ПОО по специальностям/профессиям, требующим у поступающего определенных творческих способностей, физических и (или) психологических качеств продлен до 15 сентября 2014 года.
		</p>
        <style>.cursor-pointer {cursor: pointer}</style>
		<div class="panel-group" id="accordion">
			<div class="panel panel-default" >
				<div class="panel-heading">
					<h4 class="panel-title"><a data-toggle="collapse" data-target="#collapseOne" class="collapsed cursor-pointer">
					Кто может обратиться за услугой </a> </h4>
				</div>
				<div id="collapseOne" class="panel-collapse collapse cursor-pointer">
					<div class="panel-body">
						 Поступающие, имеющие основное общее или среднее образование, достигшие 14 летнего возраста и имеющие паспорт.
					</div>
				</div>
			</div>
			<div class="panel panel-default" id="panel2">
				<div class="panel-heading">
					<h4 class="panel-title"> <a data-toggle="collapse" data-target="#collapseTwo" class="collapsed cursor-pointer">
					Стоимость услуги и порядок оплаты </a> </h4>
				</div>
				<div id="collapseTwo" class="panel-collapse collapse cursor-pointer">
					<div class="panel-body">
						 Бесплатно
					</div>
				</div>
			</div>
			<div class="panel panel-default" id="panel3">
				<div class="panel-heading">
					<h4 class="panel-title"> <a data-toggle="collapse" data-target="#collapseThree" class="collapsed cursor-pointer">
					Перечень необходимых документов </a> </h4>
				</div>
				<div id="collapseThree" class="panel-collapse collapse">
					<div class="panel-body">
						<ul>
							<li>Оригинал документов, удостоверяющих личность поступающего;</li>
							<li>Документ, подтверждающий место жительства в городе Москве (при отсутствии отметки о регистрации в документе, удостоверяющем личность);</li>
							<li>Оригинал документа об образовании и (или) квалификации;</li>
							<li>Медицинская справка;</li>
							<li>4 фотографии 3х4;</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="panel panel-default" id="panel4">
				<div class="panel-heading">
					<h4 class="panel-title"> <a data-toggle="collapse" data-target="#collapseFour" class="collapsed cursor-pointer">
					Сроки предоставления услуги </a> </h4>
				</div>
				<div id="collapseFour" class="panel-collapse collapse">
					<div class="panel-body">
						 Услуга доступна с 20 июня по 15 августа. В случае подачи заявления поступающим на обучение по образовательным программам по специальностям (профессиям), требующим у поступающего определенных творческих способностей, физических и (или) психологических качеств услуга будет доступна с 20 июня по 1 августа.
					</div>
				</div>
			</div>
			<div class="panel panel-default" id="panel5">
				<div class="panel-heading">
					<h4 class="panel-title"> <a data-toggle="collapse" data-target="#collapseFive" class="collapsed cursor-pointer">
					Результат оказания услуги </a> </h4>
				</div>
				<div id="collapseFive" class="panel-collapse collapse">
					<div class="panel-body">
						 Регистрация заявления на портале с последующим зачислением в ПОО
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-5">
		<h3>Новости региона</h3>
		<hr>
		 <?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"main-page-news",
	Array(
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "2",
		"NEWS_COUNT" => "4",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(0=>"",1=>"undefined",2=>"",),
		"PROPERTY_CODE" => array(0=>"",1=>"undefined",2=>"",),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "/news/#ELEMENT_ID#/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	)
);?>
	</div>
	<div class="row">
		<div class="col-md-12">
            <?$APPLICATION->IncludeComponent(
                "spo.region-preview-organization-list",
                "",
                Array(
                    'organizationCount' => 10,
                )
            );?>
		</div>
	</div>

</div>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>