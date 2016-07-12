<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
//use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Domains\OrganizationSpecialtyDomain;
//use Spo\Site\Domains\SpecialtyDomain;
//use Spo\Site\Adapters\OrganizationSpecialtyDomainAdapter;
use Spo\Site\Adapters\SpecialtyDomainAdapter;
//use Spo\Site\Util\CVarDumper;
use Spo\Site\Helpers\PagingHelper;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\ExamDiscipline;
use Spo\Site\Dictionaries\ExamType;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Dictionaries\TrainingType;
use Spo\Site\Entities\OrganizationTable;
use Spo\Site\Entities\OrganizationSpecialtyTable;
use Spo\Site\Entities\OrganizationEmployeeTable;

class SpecialtyListComponent extends OrganizationOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('Образовательные программы организации' => '');
	protected function getResult()
	{
		global $USER;
        $paging = new PagingHelper(false);
        $ArrayResul = OrganizationEmployeeTable::getList(array(
            'filter' => array(
                'USER_MODERATOR'=>$USER->GetID(),
            ),
            'select' => array(
                'USER_MODERATOR',
            )
        ))->fetchAll();

        if(count($ArrayResul)>0 and $ArrayResul[0]['USER_MODERATOR']!='' and $ArrayResul[0]['USER_MODERATOR']!=0){
            $this->arResult['NeModerator']=0;
        }
        else{
            $this->arResult['NeModerator']=1;
        }
        /*$organization    = OrganizationDomain::loadByEmployeeUserId($USER->GetID());
        $specialtyDomain =
            SpecialtyDomain::getSpecialtiesListWithOrganizationSpecialtiesByOrganizationId(
                $organization->getOrganizationId(),
                $paging
            );*/
        $ArrayResult = OrganizationTable::getList(array(
            'filter' => array(
                'LOGIC' => 'OR',
                'ORGANIZATION_EMPLOYEE.USER_ID'=>$USER->GetID(),
                'ORGANIZATION_EMPLOYEE.USER_MODERATOR'=>$USER->GetID(),
            ),
            'group'   => array('ORGANIZATION_SPECIALTY.SPECIALTY_ID'),
            'order'   => array('ORGANIZATION_SPECIALTY.SPECIALTY_ID'=>'ASC'),
            'select' => array(
                'specialtyId'=>'ORGANIZATION_SPECIALTY.SPECIALTY_ID',
                'specialtyCode'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'specialtyTitle'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'organization_id'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_ID',
                'organizationSpecialtyId'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_ID',
                'organizationSpecialtyBaseEducation'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'organizationSpecialtyStudyMode'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
                'organizationSpecialtyStatus'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STATUS',
                'organization_address'=>'ORGANIZATION_ADDRESS',
            )
        ))->fetchAll();
        $getOrganizationId=$ArrayResult[0]['organization_id'];
        $getAdress=$ArrayResult[0]['organization_address'];
        for($i=0;count($ArrayResult)>$i;$i=$i+1){
            $j=$i+1;
            if(count($ArrayResult)>=$j) {
                if ($ArrayResult[$i]['specialtyId'] == $ArrayResult[$j]['specialtyId']) {
                    $orgSpecel['organizationSpecialtyId'] = $ArrayResult[$i]['organizationSpecialtyId'];
                    $orgSpecel['organizationSpecialtyBaseEducation'] = $ArrayResult[$i]['organizationSpecialtyBaseEducation'];
                    $orgSpecel['organizationSpecialtyStudyMode'] = $ArrayResult[$i]['organizationSpecialtyStudyMode'];
                    $orgSpecel['organizationSpecialtyStatus'] = $ArrayResult[$i]['organizationSpecialtyStatus'];
                    $organizationSpecialties[] = $orgSpecel;
                } else {
                    $orgSpecel['organizationSpecialtyId'] = $ArrayResult[$i]['organizationSpecialtyId'];
                    $orgSpecel['organizationSpecialtyBaseEducation'] = $ArrayResult[$i]['organizationSpecialtyBaseEducation'];
                    $orgSpecel['organizationSpecialtyStudyMode'] = $ArrayResult[$i]['organizationSpecialtyStudyMode'];
                    $orgSpecel['organizationSpecialtyStatus'] = $ArrayResult[$i]['organizationSpecialtyStatus'];
                    $organizationSpecialties[] = $orgSpecel;
                    $ArrayResultNew['specialtyId'] = $ArrayResult[$i]['specialtyId'];
                    $ArrayResultNew['specialtyCode'] = $ArrayResult[$i]['specialtyCode'];
                    $ArrayResultNew['specialtyTitle'] = $ArrayResult[$i]['specialtyTitle'];
                    $ArrayResultNew['organizationSpecialties'] = $organizationSpecialties;
                    $specialtiesList[] = $ArrayResultNew;
                    $organizationSpecialties = array();
                }
            }
        }
        $specialtiesList=array('list'=>$specialtiesList,'totalCount'=>count($specialtiesList));
        //$organizationSpecialtyDomain =
        //    OrganizationSpecialtyDomain::getSpecialtiesListByOrganizationId($organization->getOrganizationId(), $paging);
        //$specialtyIdList = $specialtyDomain->getSpecialtiesIdList();
        //$freeSpecialtyDomain = SpecialtyDomain::getSpecialtyListExceptIds($specialtyIdList, $paging);
        //$organizationSpecialtyList =
        //    OrganizationSpecialtyDomainAdapter::getOrganizationSpecialtiesListWithTotalCount($organizationSpecialtyDomain);
        //$this->arResult['organizationSpecialtiesList'] = $organizationSpecialtyList;

        //$freeSpecialtyDomain = SpecialtyDomain::getSpecialtiesList(true);

        // объединяем все связанные OrganizationSpecialty в домен
        //$organizationSpecialtyDomain = $specialtyDomain->getOrganizationSpecialtyDomain();
        /*$specialtiesList     =
            SpecialtyDomainAdapter::getSpecialtiesListWithOrganizationSpecialtiesAndTotalCount($specialtyDomain);*/
        $freeSpecialtyList   = SpecialtyDomainAdapter::getSpecialtyList(/*$freeSpecialtyDomain*/);
        $this->arResult['adress']            = $getAdress;
        $this->arResult['specialtiesList']   = $specialtiesList;
        $this->arResult['applicationCount']  = OrganizationSpecialtyDomain::getOrganizationSpecialtiesApplicationCount();
        $this->arResult['freeSpecialtyList'] = $freeSpecialtyList;
        $this->arResult['paging']            = $paging;
        //$this->arResult['organizationId']    = $organization->getOrganizationId();
        $this->arResult['organizationId']    = $getOrganizationId;
        $this->arResult['baseEducationList'] = BaseEducation::getValuesArray();
        $this->arResult['studyModeList']     = StudyMode::getValuesArray();
        $this->arResult['disciplineList']    = ExamDiscipline::getValuesArray();
        $this->arResult['examTypeList']      = ExamType::getValuesArray();
        $this->arResult['trainingLevelList'] = TrainingLevel::getValuesArray();
        $this->arResult['trainingTypeList']  = TrainingType::getValuesArray();
	}
}
?>