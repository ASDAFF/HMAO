<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use \Bitrix\Main;
use Spo\Site\Core\SPOComponent;
use Spo\Site\Util\UiMessage;

class SpoUiMessage extends SPOComponent
{
    protected $componentPage = 'template';
    protected function checkUserAccess(){}
    protected function checkParams()
    {
        if(empty($this->arParams['container-selector'])){
            $this->arParams['container-selector'] = 'body';
        }
    }
    //public function onPrepareComponentParams($arParams){}
	protected function getResult()
	{
        //UiMessage::addMessage('Всё зашибись');
        //\Spo\Site\Util\CVarDumper::dump($_SESSION[UiMessage::$sessKey]);
        $this->arResult['messages'] = UiMessage::getMessages();
        $this->arResult['selector'] = $this->arParams['container-selector'];
        //\Spo\Site\Util\CVarDumper::dump($this->arResult['messages']);
        //\Spo\Site\Util\CVarDumper::dump($_SESSION[UiMessage::$sessKey]);
        //$this->onPrepareComponentParams()
        //var_dump($this->arParams);exit;
        //var_dump($this->arResult);exit;
	}
}
?>