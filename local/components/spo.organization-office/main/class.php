<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Domains\UserDomain;
use Spo\Site\Core\SPOComponent;
use Spo\Site\Helpers\OrganizationOfficeUrlHelper as Url;
use Spo\Site\Exceptions\AccessException;

class OrganizationOfficeComponent extends SPOComponent
{
	// Для задания путей "по-умолчанию" для работы в ЧПУ режиме
//	protected $arDefaultUrlTemplates404 = array(
//		'cabinetIndex'     => 'index.php',
//        'organizationEdit' => OfficeUrl::toOrganizationInfoEdit(false)
//	);

	protected $arAllowedComponentVariables = array('APPLICATION_ID', 'ORGANIZATION_ID', 'year');
	protected $componentPage = 'cabinetIndex';

	protected function getResult()
	{
	}

    protected function checkParams()
    {
    }

    public function onBeforeExecuteComponent()
    {
        $this->arDefaultUrlTemplates404 = array(
            'cabinetIndex'     => 'index.php',
            'organizationEdit' => Url::toOrganizationInfoEdit(true),
            'applicationList'  => Url::toApplicationList(true),
            'applicationArchiveList'  => Url::toApplicationArchiveList(true),
            'applicationEdit' => Url::toApplicationEdit(0, true),
            'specialtyList'    => Url::toSpecialtyList(true),
            'specialtyAjax'    => Url::toSpecialtyAjax(true),
            'abiturientProfile'=> Url::toAbiturientProfile(0, true),
            'staticPageEdit'   => Url::toStaticPageEdit(0, true),
            'admissionPlan' => Url::toAdmissionPlanEdit(0, true),
        );
    }
	/**
	 * @throws AccessException
	 */
	protected function checkUserAccess()
	{
		global $USER;
		$currentUserId = $USER->GetID();
		if (empty($currentUserId)){
			throw AccessException::isNotOrganizationEmployee();
        }
		if (!UserDomain::checkIsOrganizationEmployee($USER)){
			throw AccessException::isNotOrganizationEmployee();
        }
		if (OrganizationDomain::checkIfUserIsEmployeeOfAnyOrganization($USER->GetID())==false){
			throw AccessException::isNotOrganizationEmployee();
        }

	}
}
?>