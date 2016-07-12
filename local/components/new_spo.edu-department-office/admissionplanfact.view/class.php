<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Type;

use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Adapters\OrganizationDomainAdapter;
use Spo\Site\Domains\SpecialtyDomain;
use Spo\Site\Adapters\SpecialtyDomainAdapter;
use Spo\Site\Domains\RegionDomain;
use Spo\Site\Adapters\RegionDomainAdapter;
use Spo\Site\Util\SpoConfig;
use Spo\Site\Domains\AdmissionPlanStatDomain;
use Spo\Site\Adapters\AdmissionPlanStatDomainAdapter;
use Spo\Site\Dictionaries\OrganizationStatus;
use Spo\Site\Entities\RegionTable;
use Spo\Site\Entities\SpecialtyTable;
use Spo\Site\Entities\OrganizationTable;

class AdmissionPlanFactComponent extends EduDepartmentOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('План-фактный анализ количества поданных заявлений к плану набора' => '');
    protected $pageTitle = 'План-фактный анализ';

	protected function getResult()
	{
        $filter = $this->getAdmissionPlanFilter();
        $availableSpecialtiesWithQualifications = $this->getSpecialtiesList();
        $admissionPlan = AdmissionPlanStatDomain::getGeneralStatData($filter);
        $applicationsNumber = AdmissionPlanStatDomain::getGeneralApplicationsNumber($filter);
        $this->arResult['organizationsList'] = $this->getOrganizationsList();
        $this->arResult['regionAreasList'] = $this->getRegionAreasList();
        $this->arResult['specialtiesList'] = $availableSpecialtiesWithQualifications;
        $this->arResult['existingStudyPeriods'] = AdmissionPlanStatDomain::getExistingStudyPeriods();
        AdmissionPlanStatDomain::getExistingStudyPeriods();
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
        $ArrayResult = OrganizationTable::getList(array(
            'filter' => array('ORGANIZATION_STATUS'=>OrganizationStatus::ENABLED),
            'select' => array(
                'id'=>'ORGANIZATION_ID',
                'city'=>'CITY.CITY_NAME',
                'name'=>'ORGANIZATION_NAME',
                'phone'=>'ORGANIZATION_PHONE',
                'sity'=>'ORGANIZATION_SITE',
                'address'=>'ORGANIZATION_ADDRESS',
            )
        ))->fetchAll();
        //return OrganizationDomainAdapter::listOrganizations(OrganizationDomain::getEnabledOrganizations());
        return $ArrayResult;
    }

    private function getRegionAreasList()
    {
        $ArrayResult = RegionTable::getList(array(
            'filter' => array('REGION_ID'=>SpoConfig::getSiteRegionId()),
            'select' => array(
                'regionAreaId'=>'REGION_AREA.REGION_AREA_ID',
                'regionAreaName'=>'REGION_AREA.REGION_AREA_NAME',
            )
        ))->fetchAll();
        //return RegionDomainAdapter::listRegionAreas(RegionDomain::getById(SpoConfig::getSiteRegionId()));
        return $ArrayResult;
    }

    /**
     * @return array
     * @throws Main\ArgumentException
     */

    private function getSpecialtiesList()
    {
        $ArrayResult = SpecialtyTable::getList(array(
            'filter' => array('!SPECIALTY_ID'=>'','!SPECIALTY_TITLE'=>'','!SPECIALTY_CODE'=>'','!SPECIALTY_GROUP_ID'=>'','!ORGANIZATION_SPECIALTY.SPECIALTY_ID'=>''),
            'group'   => array('SPECIALTY_ID','SPECIALTY_TITLE','SPECIALTY_CODE'),
            'order'  => array('SPECIALTY_CODE' => 'ASC'),
            'select' => array(
                '*',
                'QUALIFICATION_ID'=>'QUALIFICATIONS.QUALIFICATION.QUALIFICATION_ID',
                'QUALIFICATION_TITLE'=>'QUALIFICATIONS.QUALIFICATION.QUALIFICATION_TITLE',
            )
        ))->fetchAll();
        $y=0;
        for($i=0;count($ArrayResult)>$i;$i=$i+1){
            $j=$i+1;
            if(count($ArrayResult)>=$j) {
                if ($ArrayResult[$i]['SPECIALTY_ID'] == $ArrayResult[$j]['SPECIALTY_ID']) {
                    $qualification['id'] = $ArrayResult[$i]['QUALIFICATION_ID'];
                    $qualification['title'] = $ArrayResult[$i]['QUALIFICATION_TITLE'];
                    $qualifications[] = $qualification;
                } else {
                    $ArrayResultNew['id'] = $ArrayResult[$i]['SPECIALTY_ID'];
                    $ArrayResultNew['code'] = $ArrayResult[$i]['SPECIALTY_CODE'];
                    $ArrayResultNew['title'] = $ArrayResult[$i]['SPECIALTY_TITLE'];
                    $qualification['id'] = $ArrayResult[$i]['QUALIFICATION_ID'];
                    $qualification['title'] = $ArrayResult[$i]['QUALIFICATION_TITLE'];
                    $qualifications[] = $qualification;
                    $ArrayResultNew['qualifications'] = $qualifications;
                    $ResultNew[] = $ArrayResultNew;
                    $qualifications = array();
                }
            }
        }
        $ResultNew['totalCount']=0;
        //return SpecialtyDomainAdapter::getSpecialtyList(SpecialtyDomain::getSpecialtiesList(true, true));
        return $ResultNew;
    }

}