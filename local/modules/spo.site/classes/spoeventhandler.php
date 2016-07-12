<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 21.05.15
 * Time: 11:55
 */

class SpoEventHandler
{
    public function onBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {
        global $USER;
        if(!$USER->IsAdmin()){return;}

        $aGlobalMenu['global_menu_spo_site'] = array(
            'menu_id'      => 'spo_site',
            'page_icon'    => 'settings_title_icon',
            'index_icon'   => 'settings_page_icon',
            'text'         => 'Настройки СПО',
            'title'        => 'Настройки СПО',
            'url'          => 'spo_site_settings_index.php?lang='.LANGUAGE_ID,
            'sort'         =>  1000,
            //'icon'       => 'button_settings',
            'items_id'     => 'global_menu_spo_site',
            'help_section' => 'spo_site',
            'items'      => array(
//                array(
//                    "text" => 'test',
//                    "url"  => "spo_site_settings_index.php=".LANG,
////                    "more_url" =>   Array(
////                        "iblock_edit.php?type=".$ibtype,
////                        "iblock_section_edit.php?type=".$ibtype,
////                        $urlSectionAdminPage."?type=".$ibtype,
////                        "iblock_element_edit.php?type=".$ibtype,
////                        $urlElementAdminPage."?type=".$ibtype,
////                        "iblock_history_list.php?type=".$ibtype,
////                        "iblock_history_view.php?type=".$ibtype,
////                        "iblock_admin.php?type=".$ibtype."&#9001;=".LANG
////                    ),
//                    "title"       => 'test title',
//                    "parent_menu" => "global_menu_spo_site",
//                    "sort"        =>  200,
//                    "icon"        => "iblock_menu_icon_types",
//                    "page_icon"   => "iblock_page_icon_types",
//                    "items_id"    => "menu_iblock_type_",
//                    //"items"       => $iblock
//                )
            ),

        );

    }
}