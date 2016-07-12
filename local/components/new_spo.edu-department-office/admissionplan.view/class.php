<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;

use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Adapters\OrganizationDomainAdapter;
use Spo\Site\Domains\SpecialtyDomain;
use Spo\Site\Adapters\SpecialtyDomainAdapter;
use Spo\Site\Domains\RegionDomain;
use Spo\Site\Adapters\RegionDomainAdapter;
use Spo\Site\Util\SpoConfig;
use Spo\Site\Domains\AdmissionPlanStatDomain;
use Spo\Site\Adapters\AdmissionPlanStatDomainAdapter;


class AdmissionPlanViewComponent extends EduDepartmentOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('Контрольные цифры приёма' => '');
    protected $pageTitle = 'Контрольные цифры приёма';

	protected function getResult()
	{
        $filter = $this->getAdmissionPlanFilter();
        $availableSpecialtiesWithQualifications = $this->getSpecialtiesList();
        $this->arResult['admissionPlan'] = AdmissionPlanStatDomainAdapter::getGeneralStatData(
            AdmissionPlanStatDomain::getGeneralStatData($filter), $availableSpecialtiesWithQualifications
        );
        $this->arResult['organizationsList'] = $this->getOrganizationsList();
        $this->arResult['regionAreasList'] = $this->getRegionAreasList();
        $this->arResult['specialtiesList'] = $availableSpecialtiesWithQualifications;

        // TODO Временный вспомогательный метод. Скорее всего, варианты периодов обучения будут предопределены
        // TODO пока что же выбираем из базы все существующие, чтобы можно ыло сделать фильтр
        $this->arResult['existingStudyPeriods'] = AdmissionPlanStatDomain::getExistingStudyPeriods();

        $this->arResult['filter'] = $filter;

        $this->pageTitle .= ' ' . $filter['year'];
	}

    private function getAdmissionPlanFilter()
    {
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();
        $filter = $request->get('filter');
        if (empty($filter) || !is_array($filter))
            $filter = array();

        if (!isset($filter['year']))
            $filter['year'] = date('Y');

        return $filter;
    }

    private function getOrganizationsList()
    {
        return OrganizationDomainAdapter::listOrganizations();
    }

    private function getRegionAreasList()
    {
        return RegionDomainAdapter::listRegionAreas(
            //RegionDomain::getById(
                SpoConfig::getSiteRegionId()
            //)
        );
    }

    private function getSpecialtiesList()
    {
        return SpecialtyDomainAdapter::getSpecialtyList();
    }

}