<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Spo\Site\Core\SPOComponent;
use Spo\Site\Exceptions\ArgumentException;
use Spo\Site\Exceptions\AccessException;
use Spo\Site\Domains\UserDomain;
use Spo\Site\Adapters\UserDomainAdapter;

class Confirmation extends UserConfirmation
{
    protected $componentPage = '';

    protected function checkParams()
    {
    }

	protected function getResult()
	{
        global $USER;
        $userId = $USER->GetID();

        $userDomain = UserDomain::loadByUserId($userId);

        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();
        $confirmationFormSubmitted = $request->get('ConfirmationForm');
        if ($confirmationFormSubmitted && !$USER->IsAuthorized()) {
            if (!empty($confirmationFormSubmitted['emailCode'])) {
                $userDomain->confirmUserEmail($confirmationFormSubmitted['emailCode'],$userId,$USER);
                /*$USER->Authorize($userId);*/
                LocalRedirect($_SERVER['REQUEST_URI']);

            }
            if (!$userDomain->validate())
                $this->arResult['errors'] = $userDomain->getErrors();           
        }

        $this->arResult['userId'] = $userId;
        $this->arResult['bitrixUserEmail'] = $USER->GetEmail();
		//$this->arResult['bitrixUserEmail'] = $userDomain->getUserEmail();
        $rsUser = CUser::GetList($by="PERSONAL_PHONE", $order="desc", array("ID"=>$userId),array("SELECT"=>array("PERSONAL_PHONE")));
        $arUser = $rsUser->Fetch();
		//$this->arResult['bitrixUserPersonalPhone'] = $userDomain->getUserPersonalPhone();
        $this->arResult['bitrixUserPersonalPhone'] = $arUser['PERSONAL_PHONE'];
        //print_r($arUser['PERSONAL_PHONE']);
        $this->arResult['userValidData'] = UserDomainAdapter::isUserDataValid(/*$userDomain,*/$userId,$arUser['PERSONAL_PHONE'],$USER->GetEmail());
	}

}
?>