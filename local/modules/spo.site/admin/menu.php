<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 21.05.15
 * Time: 11:15
 */
//var_dump(__DIR__, $_SERVER['DOCUMENT_ROOT']);exit;
//todo включить, когда появится необходимость в меню в админке
/*$aMenu = array(
    'parent_menu' => 'global_menu_spo_site', // поместим в раздел 'Сервис'
    'sort'        => 100,                    // вес пункта меню
    //'url'         => 'form_list.php?lang='.LANGUAGE_ID,  // ссылка на пункте меню
    //'url'         => 'spo_site_settings_index.php?lang='.LANGUAGE_ID,
    'text'        => 'Общие параметры СПО',       // текст пункта меню
    'title'       => 'Общие параметры СПО', // текст всплывающей подсказки
    'icon'        => 'form_menu_icon', // малая иконка
    'page_icon'   => 'form_page_icon', // большая иконка
    'items_id'    => 'spi_site_test',  // идентификатор ветви
    'module_id'   => 'spo_site',
    'items'       => array(
        array(         // остальные уровни меню сформируем ниже.
            'text'     => 'Административный раздел СПО',
            'url'      => '/local/modules/spo.site/admin/?lang='.LANGUAGE_ID,
            'more_url' => array('/local/modules/spo.site/admin/'),
            //'items_id'    => 'spi_site_test_1',  // идентификатор ветви
            //'module_id'   => 'spo_site',
        ),
    ),
);*/

return $aMenu;