<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use Spo\Site\Domains\UserDomain;
use Spo\Site\Core\SPOComponent;

class AbiturientOffice extends SPOComponent
{
	public $componentRootUrl = '/abiturient-office/';
    protected $pageTitle = 'Личный кабинет абитуриента';

	public $arDefaultUrlTemplates404 = array(
		'applicationList' => 'index.php',
		'applicationEdit' => 'edit/#applicationId#/',
		'applicationDelete' => 'delete/#applicationId#/',
		'applicationCreate' => 'create/#organizationId#/',
		'profileUpdate' => 'profile/',
	);

	// Список всех переменных, которые может принимать компонент из параметров GET запроса
	protected $arAllowedComponentVariables = array('applicationId', 'organizationId');
	// Имя шаблона, который будет использоваться (можно сразу задать значение по умолчанию)
	protected $componentPage = 'applicationList';

	protected function getResult()
	{
	}

    protected function checkParams()
    {
    }

	protected function checkUserAccess()
	{
		global $USER;
		$currentUserId = $USER->GetID();

		if (empty($currentUserId) || !UserDomain::checkIsAbiturient($USER->GetUserGroupArray()))
			LocalRedirect('/auth/need-abiturient-registration.php');

	}

}
?>