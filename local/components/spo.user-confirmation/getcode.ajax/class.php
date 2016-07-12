<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Spo\Site\Core\SPOComponent;
use Spo\Site\Domains\UserDomain;
use Spo\Site\Util\JsonResponse;
use Spo\Site\Util\Notification\Notifier;

class getCode extends UserConfirmation
{
    protected $componentPage = '';

    protected function checkParams()
    {

    }

	protected function getResult()
	{
        global $USER;

        $userDomain = UserDomain::loadByUserId($USER->GetID());

        $response = new JsonResponse();
        $userDomain->generateNewConfirmationCode($this->arParams['codeType']);
        if (!$userDomain->validate()) {
            // todo. Нам нужна нормальная обработка ошибок.
            $domainErrors = $userDomain->getErrors();
            $errorsArray = array();
            foreach ($domainErrors as $domainError) {
                $errorsArray[] = $domainError['message'];
            }
            $response->setErrors($errorsArray);
        } else {
            //$userDomain->save();
            $notifier = new Notifier();
            if ($this->arParams['codeType'] == UserDomain::CONFIRMATION_CODE_TYPE_EMAIL) {
                if (!$notifier->emailConfirmationCodeGenerated($USER->GetID()))
                {
                    $response->setErrors('Не удалось отправить письмо, попробуйте позже');
                }
            }
            /*elseif ($this->arParams['codeType'] == UserDomain::CONFIRMATION_CODE_TYPE_PHONE) {
                if (!$notifier->phoneConfirmationCodeGenerated($userDomain->getUserId()))
                    $response->setErrors('Не удалось отправить СМС. Попробуйте позже');
            }*/
        }
        $this->arResult['response'] = $response;
	}

}
?>