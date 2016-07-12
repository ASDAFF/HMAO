<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;
use Spo\Site\Core\SPOComponent;

class SpoPaging extends SPOComponent
{
    protected $componentPage = 'template';
    protected function checkUserAccess(){}
    protected function checkParams(){}
    public function onPrepareComponentParams($arParams){
        if(!isset($arParams['ShownPages'])){
            $arParams['ShownPages'] = 11;
        }
        if(!isset($arParams['ShownTotalCount'])){
            $arParams['ShownTotalCount'] = 'Y';
        }
        if(!isset($arParams['NavClass'])){
            $arParams['NavClass'] = 'paging';
        }

        return $arParams;
    }
	protected function getResult()
	{
        //$this->onPrepareComponentParams()
        //var_dump($this->arParams);exit;
        //var_dump($this->arResult);exit;
	}
}
?>