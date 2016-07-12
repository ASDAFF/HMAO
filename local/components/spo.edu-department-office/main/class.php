<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Domains\UserDomain;
use Spo\Site\Core\SPOComponent;
use Spo\Site\Helpers\EduDepartmentOfficeUrlHelper as Url;
use Spo\Site\Exceptions\AccessException;

class EduDepartmentOfficeComponent extends SPOComponent
{
	protected $arAllowedComponentVariables = array();
	protected $componentPage = 'eduDepartmentIndex';

    public $componentRootUrl = '/edu-department-office/';

	protected function getResult()
	{
	}

    protected function checkParams()
    {
    }

    public function onBeforeExecuteComponent()
    {
		echo 1111;
		die;
        $this->arDefaultUrlTemplates404 = array(
            'eduDepartmentIndex' => 'index.php',
            // Сводная информация по всем организациям
            'admissionPlanView' => Url::toAdmissionPlanView('', true),
            // Планы приёма с выводом по организациям
            'admissionPlanByOrganizationsView' => Url::toAdmissionPlanByOrganizationsView(array(), true),
            // Соотношение мест и поданых заявок
            'admissionPlanFactView' => Url::toAdmissionPlanFactView(array(), true),
            // Страница редактирования плана приёма
            'admissionPlanEdit' => Url::toAdmissionPlanEdit('', true),
        );
    }

	/**
	 * @throws AccessException
	 */
	protected function checkUserAccess()
	{
		global $USER;
		$currentUserId = $USER->GetID();

		if (empty($currentUserId))
        {
			throw AccessException::isNotEducationDepartmentEmployee();
        }

		if (!UserDomain::checkIsEduDepartmentEmployee($USER))
        {
			throw AccessException::isNotEducationDepartmentEmployee();
        }
	}
}
?>