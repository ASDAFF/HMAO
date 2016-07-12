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
use Spo\Site\Domains\AdmissionPlanDomain;


class AdmissionPlanViewComponent extends EduDepartmentOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('Количество поданных заявлений' => '');
    protected $pageTitle = 'Количество поданных заявлений';

	protected function getResult()
	{
        $filter = $this->getAdmissionPlanFilter();

        // Если нужно обновить статус какого-либо плана приёма - обновляем
        //Пока под вопросом
        $this->changeAdmissionPlanStatus();
        //***********************
        $this->arResult['admissionPlan'] = AdmissionPlanStatDomainAdapter::getAdmissionPlanByOrganizations(
            AdmissionPlanStatDomain::getAdmissionPlanByOrganizations($filter)
        );
        $this->arResult['organizationsList'] = $this->getOrganizationsList();
        $this->arResult['regionAreasList'] = $this->getRegionAreasList();
        $this->arResult['specialtiesList'] = $this->getSpecialtiesList();

        // TODO Временный вспомогательный метод. Скорее всего, варианты периодов обучения будут предопределены
        // TODO пока что же выбираем из базы все существующие, чтобы можно ыло сделать фильтр
        $this->arResult['existingStudyPeriods'] = AdmissionPlanStatDomain::getExistingStudyPeriods();

        $this->arResult['filter'] = $filter;

        $this->pageTitle .= ' ' . $filter['year'];
	}

    private function changeAdmissionPlanStatus()
    {
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();

        $changedAdmissionPlan = $request->get('changedAdmissionPlan');

        if (empty($changedAdmissionPlan) || empty($changedAdmissionPlan['id']) || empty($changedAdmissionPlan['status']))
            return null;
        $admissionPlanDomain = AdmissionPlanDomain::changeStatus(
            $changedAdmissionPlan['id'], $changedAdmissionPlan['status']
        );

        if (!$admissionPlanDomain->save())
            throw new Main\DB\Exception('Ошибка при сохранении данных');

        return true;
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
        //return OrganizationDomainAdapter::listOrganizations(OrganizationDomain::getEnabledOrganizations());
    }

    private function getRegionAreasList()
    {
        /*return RegionDomainAdapter::listRegionAreas1(
            RegionDomain::getById(
                SpoConfig::getSiteRegionId()
            )
        );*/
        return RegionDomainAdapter::listRegionAreas(SpoConfig::getSiteRegionId());
    }

    private function getSpecialtiesList()
    {
        return SpecialtyDomainAdapter::getSpecialtyList();
        //return SpecialtyDomainAdapter::getSpecialtyList(SpecialtyDomain::getSpecialtiesList(true, true));
    }

}