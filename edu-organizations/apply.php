<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подать заявление");
?>Страница с формой подачи заявления от абитуриента.<br><br>Если переходим из списка ОО (указав конкретное ОО), то на форме в списке выбора должно быть выбрано это ОО. Если переходим по ссылке на данную страницу напрямую - не должно быть выбрано ОО, нужно выбирать руками. Возможно, оставим только первый вариант - переход на форму подачи заявки возможен только при указании конкретной ОО в списке ОО.<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>