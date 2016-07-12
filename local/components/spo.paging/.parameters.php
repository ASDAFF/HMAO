<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "ShownPages" => array(
            "PARENT" => "BASE",
            "NAME" => 'Количество показываемых страниц пейджинга',
            "TYPE" => "STRING",
            "DEFAULT" => "11",
        ),
        "ShowTotalCount" => array(
            "PARENT" => "BASE",
            "NAME" => 'Показывать количество записей',
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "NavClass" => array(
            "PARENT" => "BASE",
            "NAME" => 'Класс элемента nav',
            "TYPE" => "STRING",
            "DEFAULT" => "paging",
        ),
        "PageCount" => array(
            "PARENT" => "BASE",
            "NAME" => 'Количество показываемых элементов (динамический параметр)',
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
        "TotalCount" => array(
            "PARENT" => "BASE",
            "NAME" => 'Количество записей всего (динамический параметр)',
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
        "CurrentPage" => array(
            "PARENT" => "BASE",
            "NAME" => 'Текущая страница (динамический параметр)',
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
    )
);