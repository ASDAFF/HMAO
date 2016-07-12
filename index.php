<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Главная");
?> 
<div class="spo-index-page"> 
  <div class="row region-info"> 
    <div class="col-md-8"> 
      <h4>Образовательные организации региона на карте:</h4>
     <?$APPLICATION->IncludeComponent(
	"spo.region-map",
	"",
	Array(
	)
);?> </div>
   
    <div class="col-md-4 text-content"> 
      <h2>О регионе</h2>
     			<? $APPLICATION->IncludeFile(SITE_DIR."local/includes/region-info.php", Array(), Array("MODE"=>"html")); ?> </div>
   </div>
 
  <div class="row"> 
    <div class="col-md-8 text-content index-text"> 
      <h3>Оформление услуги на сайте</h3>
     
      <p><b>ВНИМАНИЕ! Если вы не зарегистрированы на Портале, Вам необходимо зарегистрироваться.</b></p>
     
      <p>Если вы уже зарегистрированы на Портале, Вам необходимо авторизоваться путём перехода по ссылке отправленной на электронную почту.</p>
     
      <p>25 августа 2016 года завершается приём заявлений на поступление в колледжи Ханты-Мансийского автономного округа-Югры через Портал профессионального образования.</p>
     
      <div class="panel-group" id="accordion"> 
        <div class="panel panel-primary"> 
          <div class="panel-heading"> 
            <h4 class="panel-title"><a >Кто может обратиться за услугой</a></h4>
           </div>
         
          <div id="collapseOne" class="panel-collapse collapse in"> 
            <div class="panel-body"> Поступающие, имеющие основное общее или среднее образование, достигшие 14 летнего возраста и имеющие паспорт. </div>
           </div>
         </div>
       
        <div class="panel panel-primary" id="panel2"> 
          <div class="panel-heading"> 
            <h4 class="panel-title"><a >Стоимость услуги и порядок оплаты</a></h4>
           </div>
         
          <div id="collapseTwo" class="panel-collapse collapse in"> 
            <div class="panel-body"> Бесплатно </div>
           </div>
         </div>
       
        <div class="panel panel-primary" id="panel3"> 
          <div class="panel-heading"> 
            <h4 class="panel-title"><a >Перечень необходимых документов</a></h4>
           </div>
         
          <div id="collapseTwo" class="panel-collapse collapse in"> 
            <div class="panel-body"> 
              <p><span style="font-family: Arial, helvetica, sans-serif; font-size: 14px; line-height: 20px; font-weight: normal;"> - оригинал или ксерокопию документов, удостоверяющих его личность, гражданство;  
                  <br />
                 </span><span style="line-height: 20px;"> - оригинал или ксерокопию документа  об образовании;  
                  <br />
                 </span><span style="line-height: 20px;"> - 6 фотографий; 
                  <br />
                 </span><span style="line-height: 20px;"> - результаты обязательного предварительного медицинского осмотра (с обязательным указание по какой профессии/специальности абитуриент годен);  
                  <br />
                 </span><span style="line-height: 20px;"> - СНИЛС (копия), ИНН (копия) поступающего;  
                  <br />
                 </span><span style="line-height: 20px;"> - ксерокопия паспорта родителей  (законных представителей).</span></p>
             </div>
           </div>
         
          <div class="panel panel-primary" id="panel4"> 
            <div class="panel-heading"> 
              <h4 class="panel-title"><a >Сроки предоставления услуги</a></h4>
             </div>
           
            <div id="collapseFour" class="panel-collapse collapse in"> 
              <div class="panel-body"> Услуга доступна с 20 июня по 15 августа. В случае подачи заявления поступающим на обучение по образовательным программам по специальностям (профессиям), требующим у поступающего определенных творческих способностей, физических и (или) психологических качеств услуга будет доступна с 20 июня по 1 августа. </div>
             </div>
           </div>
                    </div>

          <div class="panel panel-primary" id="panel5"> 
            <div class="panel-heading"> 
              <h4 class="panel-title"><a >Результат оказания услуги</a></h4>
             </div>
           
            <div id="collapseFive" class="panel-collapse collapse in"> 
              <div class="panel-body"> Регистрация заявления на портале с последующим зачислением в ПОО </div>
             </div>
           </div>
         </div>
       </div>
     
      <div class="col-md-4 spo-news-index"> 
        <h3>Новости региона</h3>
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
);?> </div>
     </div>
   </div>
 <?$APPLICATION->IncludeComponent(
	"spo.region-preview-organization-list",
	"org-preview",
	Array(
		"organizationCount" => "10"
	)
);?> </div>
 <?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>