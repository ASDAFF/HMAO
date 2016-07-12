<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use Bitrix\Main;
use Spo\Site\Core\SPOComponent;
use Spo\Site\Exceptions\AccessException;

class SPOAdminComponent extends SPOComponent
{
	protected $arAllowedComponentVariables = array('APPLICATION_ID', 'ORGANIZATION_ID');
	protected $componentPage = 'cabinetIndex';

	protected function getResult(){}

    protected function checkParams(){}

    public function onBeforeExecuteComponent()
    {
        $this->arDefaultUrlTemplates404 = array(
            'adminIndex'     => 'index.php',
//            'organizationEdit' => Url::toOrganizationInfoEdit(true),
//            'applicationList'  => Url::toApplicationList(true),
//            'specialtyList'    => Url::toSpecialtyList(true),
//            'specialtyAjax'    => Url::toSpecialtyAjax(true),
//            'abiturientProfile'=> Url::toAbiturientProfile(0, true),
//            'staticPageEdit'   => Url::toStaticPageEdit(0, true),
        );
    }
	/**
	 * @throws AccessException
	 */
	protected function checkUserAccess()
	{
		global $USER;

		if(!$USER->IsAdmin())
        {
			throw AccessException::isNotAdmin();
        }
	}
}
?>