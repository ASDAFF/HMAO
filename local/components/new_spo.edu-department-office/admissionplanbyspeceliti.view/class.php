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
use Spo\Site\Entities\AdmissionPlanTable;


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

        $this->arResult['admissionPlan'] = $this->getAdmissionPlanByOrganizations($filter);
        $this->arResult['organizationsList'] = $this->getOrganizationsList();
        $this->arResult['regionAreasList'] = $this->getRegionAreasList();
        $this->arResult['specialtiesList'] = $this->getSpecialtiesList();

        // TODO Временный вспомогательный метод. Скорее всего, варианты периодов обучения будут предопределены
        // TODO пока что же выбираем из базы все существующие, чтобы можно ыло сделать фильтр
        $this->arResult['existingStudyPeriods'] = AdmissionPlanStatDomain::getExistingStudyPeriods();

        $this->arResult['filter'] = $filter;

        $this->pageTitle .= ' ' . $filter['year'];
	}
    public static function getAdmissionPlanByOrganizations($filter)
    {
        if (!isset($filter['year']))
            $filter['year'] = date('Y');

        /*$repository = AdmissionPlanStatRepository::create()
            ->getAdmissionPlanByOrganization()
            ->filterByAdmissionPlanYear($filter['year']);

        $repository->applyFilter($filter);

        $result = $repository->executeQuery();*/
        if(!empty($filter['admissionPlanStatus'])){
            $FILTER['ADMISSION_PLAN_STATUS']=$filter['admissionPlanStatus'];
        }
        if(!empty($filter['organization'])){
            $FILTER['ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_ID']=$filter['organization'];
        }
        if(!empty($filter['regionArea'])){
            $FILTER['ORGANIZATION_SPECIALTY.ORGANIZATION.REGION_AREA_ID']=$filter['regionArea'];
        }
        if(!empty($filter['specialties'])){
            $FILTER['ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID']=$filter['specialties'];
        }
        if(!empty($filter['studyPeriod'])){
            $FILTER['ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD']=$filter['studyPeriod'];
        }
        if(!empty($filter['studyMode'])){
            $FILTER['ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE']=$filter['studyMode'];
        }
        if(!empty($filter['baseEducation'])){
            $FILTER['ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION']=$filter['baseEducation'];
        }
        if(!empty($filter['trainingLevel'])){
            $FILTER['ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_TYPE']=$filter['trainingLevel'];
        }
        $date = new \Bitrix\Main\Type\DateTime("01.01.".$filter['year']." 00:00:00");
        $date2 = $filter['year']+1;
        $date2 = new \Bitrix\Main\Type\DateTime("01.01.".$date2." 00:00:00");
        $FILTER['<=ADMISSION_PLAN_START_DATE'] = $date2;
        $FILTER['>=ADMISSION_PLAN_START_DATE'] = $date;
        $ArrayResult = AdmissionPlanTable::getList(array(
            'filter' => $FILTER,
            'order' => array(
                'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID'=>'ASC',
                'ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_ID'=>'ASC',
                'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_TYPE'=>'ASC',
                ),
            'select' => array(
                'grantStudentsNumber'=>'ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
                'tuitionStudentsNumber'=>'ADMISSION_PLAN_TUITION_STUDENTS_NUMBER',
                'grantGroupsStudentsNumber'=>'ADMISSION_PLAN_GRANT_GROUPS_NUMBER',
                'tuitionGroupsStudentsNumber'=>'ADMISSION_PLAN_TUITION_GROUPS_NUMBER',
                //'admissionPlanStatus'=>'ADMISSION_PLAN_STATUS',
                //'admissionPlanId'=>'ADMISSION_PLAN_ID',
                'organizationId'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_ID',
                'organizationName'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_NAME',
                //'organizationRegionAreaId'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.REGION_AREA_ID',
                'trainingLevel'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_TYPE',
                //'studyPeriod'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                //'studyMode'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
                //'baseEducation'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'specialtyTitle'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'specialtyCode'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'specialtyId'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID',
                'aplicationFundingType'=>'ORGANIZATION_SPECIALTY.APPLICATION.APPLICATION_FUNDING_TYPE',
            )
        ))->fetchAll();
        $result = array();
        $Budget=0;
        $Platno=0;
        $ArrayResultNew['trainingLevels'][1]['budget'] = 0;
        $ArrayResultNew['trainingLevels'][1]['platno'] = 0;
        $ArrayResultNew['trainingLevels'][2]['budget'] = 0;
        $ArrayResultNew['trainingLevels'][2]['platno'] = 0;
        for($i=0;count($ArrayResult)>$i;$i=$i+1){
          if($ArrayResult[$i]['organizationId']==$ArrayResult[$i+1]['organizationId'] and
              $ArrayResult[$i]['specialtyId']==$ArrayResult[$i+1]['specialtyId'] and
              $ArrayResult[$i]['trainingLevel']==$ArrayResult[$i+1]['trainingLevel']
          ){
              if($ArrayResult[$i]['aplicationFundingType']==1) $Budget = $Budget + 1;
              if($ArrayResult[$i]['aplicationFundingType']==2) $Platno = $Platno + 1;
          }
          else{
              if($ArrayResult[$i]['aplicationFundingType']==1) $Budget = $Budget + 1;
              if($ArrayResult[$i]['aplicationFundingType']==2) $Platno = $Platno + 1;
              //$ArrayResultNew['grantStudentsNumber']=$ArrayResult[$i]['grantStudentsNumber'];
              //$ArrayResultNew['tuitionStudentsNumber']=$ArrayResult[$i]['tuitionStudentsNumber'];
              $ArrayResultNew['trainingLevel']=$ArrayResult[$i]['trainingLevel'];
              $ArrayResultNew['organizationId']=$ArrayResult[$i]['organizationId'];
              $ArrayResultNew['organizationName']=$ArrayResult[$i]['organizationName'];
              $ArrayResultNew['specialtyTitle']=$ArrayResult[$i]['specialtyTitle'];
              $ArrayResultNew['specialtyCode']=$ArrayResult[$i]['specialtyCode'];
              $ArrayResultNew['specialtyId']=$ArrayResult[$i]['specialtyId'];
              $ArrayResultNew['trainingLevels'][1]['grantStudentsNumber']=0;
              $ArrayResultNew['trainingLevels'][1]['tuitionStudentsNumber']=0;
              $ArrayResultNew['trainingLevels'][2]['grantStudentsNumber']=0;
              $ArrayResultNew['trainingLevels'][2]['tuitionStudentsNumber']=0;
              if($ArrayResultNew['trainingLevel']==1) {
                  $ArrayResultNew['trainingLevels'][1]['grantStudentsNumber']=$ArrayResult[$i]['grantStudentsNumber'];
                  $ArrayResultNew['trainingLevels'][1]['tuitionStudentsNumber']=$ArrayResult[$i]['tuitionStudentsNumber'];
                  $ArrayResultNew['trainingLevels'][1]['budget'] = $Budget + (int)$ArrayResult[$i]['grantGroupsStudentsNumber'];
                  $ArrayResultNew['trainingLevels'][1]['platno'] = $Platno + (int)$ArrayResult[$i]['tuitionGroupsStudentsNumber'];
              }
              if($ArrayResultNew['trainingLevel']==2) {
                  $ArrayResultNew['trainingLevels'][2]['grantStudentsNumber']=$ArrayResult[$i]['grantStudentsNumber'];
                  $ArrayResultNew['trainingLevels'][2]['tuitionStudentsNumber']=$ArrayResult[$i]['tuitionStudentsNumber'];
                  $ArrayResultNew['trainingLevels'][2]['budget'] = $Budget + (int)$ArrayResult[$i]['grantGroupsStudentsNumber'];
                  $ArrayResultNew['trainingLevels'][2]['platno'] = $Platno + (int)$ArrayResult[$i]['tuitionGroupsStudentsNumber'];
              }
              $ArrayRes[]=$ArrayResultNew;
              $ArrayResultNew['trainingLevels'][1]['budget'] = 0;
              $ArrayResultNew['trainingLevels'][1]['platno'] = 0;
              $ArrayResultNew['trainingLevels'][2]['budget'] = 0;
              $ArrayResultNew['trainingLevels'][2]['platno'] = 0;
              $Budget=0;
              $Platno=0;
          }
        }
        $ArrayResultNew=array();
        $ArrayResult=array();
        for($i=0;count($ArrayRes)>$i;$i=$i+1){
            if($ArrayRes[$i]['specialtyId']==$ArrayRes[$i+1]['specialtyId']){
                //$ArrayResultNew['grantStudentsNumber']=$ArrayRes[$i]['grantStudentsNumber'];
                //$ArrayResultNew['tuitionStudentsNumber']=$ArrayRes[$i]['tuitionStudentsNumber'];
                $ArrayResultNew['organizationId']=$ArrayRes[$i]['organizationId'];
                $ArrayResultNew['organizationName']=$ArrayRes[$i]['organizationName'];
                $ArrayResultNew['trainingLevels']=$ArrayRes[$i]['trainingLevels'];
                $ArrayResultNew['budget']=$ArrayRes[$i]['budget'];
                $ArrayResultNew['platno']=$ArrayRes[$i]['platno'];
                $ArrayOrganiz[]=$ArrayResultNew;
            }
            else{
                //$ArrayResultNew['grantStudentsNumber']=$ArrayRes[$i]['grantStudentsNumber'];
                //$ArrayResultNew['tuitionStudentsNumber']=$ArrayRes[$i]['tuitionStudentsNumber'];
                $ArrayResultNew['organizationId']=$ArrayRes[$i]['organizationId'];
                $ArrayResultNew['trainingLevels']=$ArrayRes[$i]['trainingLevels'];
                $ArrayResultNew['organizationName']=$ArrayRes[$i]['organizationName'];
                $ArrayResultNew['budget']=$ArrayRes[$i]['budget'];
                $ArrayResultNew['platno']=$ArrayRes[$i]['platno'];
                $ArrayOrganiz[]=$ArrayResultNew;
                $ArrayResultCel['specialtyTitle']=$ArrayRes[$i]['specialtyTitle'];
                $ArrayResultCel['specialtyCode']=$ArrayRes[$i]['specialtyCode'];
                $ArrayResultCel['specialtyId']=$ArrayRes[$i]['specialtyId'];
                $ArrayResultCel['organization']=$ArrayOrganiz;
                $ArrayResult[]=$ArrayResultCel;
                $ArrayOrganiz=array();
            }
        }
        //return $result;
        return $ArrayResult;
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