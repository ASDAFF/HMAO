<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use Spo\Site\Core\SPOComponent;

class RegionInfo extends SPOComponent
{

    protected function checkParams()
    {
    }

    protected function checkUserAccess()
    {
    }

	public function onIncludeComponentLang()
	{
		Loc::loadMessages(__DIR__ . '../../../../messages.php');
	}

	public $componentRootUrl = '/region-info/';

	public $arDefaultUrlTemplates404 = array(
		'organizationList' => 'edu-organizations/',
		'specialtiesList' => 'specialties/',
        'specialtyInfo' => 'specialty/#specialtyId#/'
	);

	protected $arAllowedComponentVariables = array('organizationId', 'specialtyId');
	protected $componentPage = 'organizationList';

	protected function getResult()
	{
	}


}
?>