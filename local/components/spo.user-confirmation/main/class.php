<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use Spo\Site\Core\SPOComponent;
use Spo\Site\Exceptions\AccessException;
use Spo\Site\Domains\UserDomain;
use Spo\Site\Util\UiMessage;

class UserConfirmation extends SPOComponent
{

    protected function checkParams()
    {
    }

    protected function checkUserAccess()
    {
        global $USER;
        /*if (!$USER->IsAuthorized())
            throw AccessException::isNotAuthorized();*/
    }

	public function onIncludeComponentLang()
	{
		Loc::loadMessages(__DIR__ . '../../../../messages.php');
	}

	public $componentRootUrl = '/user-confirmation/';

	public $arDefaultUrlTemplates404 = array(
		'confirm' => 'confirm/#userId#/#userConfirmationHash#/',
		'getCode' => 'getCode/#codeType#/',
	);

	protected $arAllowedComponentVariables = array('userId', 'codeType');
	protected $componentPage = 'confirm';

	protected function getResult()
	{
	}


}
?>