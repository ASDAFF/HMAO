<?php
namespace Spo\Site\Domains;

//use D;
//use Spo\Site\Doctrine\Repositories\AdmissionPlanStatRepository;
//use Bitrix\Main\Entity\ExpressionField;
//use Doctrine\ORM\Query\Expr\Join;
use Spo\Site\Core\SPODomain;
use Spo\Site\Util\CVarDumper;
use Bitrix\Main\Type;
use Spo\Site\Dictionaries\ApplicationFundingType;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Entities\OrganizationSpecialtyTable;
use Spo\Site\Entities\RegionTable;
use Spo\Site\Entities\AdmissionPlanTable;
use Spo\Site\Entities\ApplicationTable;


class AdmissionPlanStatDomain extends SPODomain {

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
            'select' => array(
                'grantStudentsNumber'=>'ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
                'tuitionStudentsNumber'=>'ADMISSION_PLAN_TUITION_STUDENTS_NUMBER',
                'admissionPlanStatus'=>'ADMISSION_PLAN_STATUS',
                'admissionPlanId'=>'ADMISSION_PLAN_ID',
                'organizationId'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_ID',
                'organizationName'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_NAME',
                'organizationRegionAreaId'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.REGION_AREA_ID',
                'trainingLevel'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_TYPE',
                'studyPeriod'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                'studyMode'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
                'baseEducation'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'specialtyTitle'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'specialtyCode'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'specialtyId'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID',
            )
        ))->fetchAll();
        //return $result;
        return $ArrayResult;
    }

    public static function getApplicationsWithSpecialtiesByYear($year = null)
    {
        if (!$year)
            $year = date('Y');
        $date = new \Bitrix\Main\Type\DateTime("01.01.".$year." 00:00:00");
        $date2 = $year+1;
        $date2 = new \Bitrix\Main\Type\DateTime("01.01.".$date2." 00:00:00");
        $ArrayResult = ApplicationTable::getList(array(
            'filter' => array(
                '<ADMISSION_PLAN.ADMISSION_PLAN_START_DATE'=>$date2,
                '>ADMISSION_PLAN.ADMISSION_PLAN_START_DATE'=>$date,
                '!APPLICATION_STATUS'=>ApplicationStatus::DELETED,
            ),
            'select' => array(
                'applicationId'=>'APPLICATION_ID',
                'applicationFundingType'=>'APPLICATION_FUNDING_TYPE',
                'applicationCreationDate'=>'APPLICATION_CREATION_DATE',
                'admissionPlanId'=>'ADMISSION_PLAN.ADMISSION_PLAN_ID',
                'studyMode'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
                'specialtyTitle'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'specialtyCode'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'specialtyId'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID',
            )
        ))->fetchAll();
        global $DB;
        foreach($ArrayResult as $kiy=>$item) {
            $ArrayResult[$kiy]['applicationCreationDate']=new \DateTime($item['applicationCreationDate']);
        }
        //return AdmissionPlanStatRepository::create()->getApplicationsWithSpecialtiesByYear($year)->executeQuery();
        return $ArrayResult;
    }

    public static function getGeneralApplicationsNumber($filter)
    {
        if (!isset($filter['year']))
            $filter['year'] = date('Y');
        /*$repository = AdmissionPlanStatRepository::create()
            ->getGeneralApplicationsNumber()
            ->filterByAdmissionPlanYear($filter['year']);
        $repository->applyFilter($filter);
        $result = $repository->executeQuery();*/
        $date = new \Bitrix\Main\Type\DateTime("01.01.".$filter['year']." 00:00:00");
        $date2 = $filter['year']+1;
        $date2 = new \Bitrix\Main\Type\DateTime("01.01.".$date2." 00:00:00");
        $FILTER = array(
            array(
                '>=ADMISSION_PLAN.ADMISSION_PLAN_START_DATE'=>$date,
                '<=ADMISSION_PLAN.ADMISSION_PLAN_END_DATE'=>$date2,
            ),
            array(
                'LOGIC' => 'OR',
                '!ADMISSION_PLAN.ADMISSION_PLAN_TUITION_STUDENTS_NUMBER'=>'',
                '!ADMISSION_PLAN.ADMISSION_PLAN_GRANT_STUDENTS_NUMBER'=>'',
            ),
        );
        if(!empty($filter['organization'])){
            $FILTER['=ORGANIZATION_ID']=$filter['organization'][0];
        }

        $result = OrganizationSpecialtyTable::getList(array(
            'filter' => $FILTER,
            //'group'   => array('SPECIALITY.ID'),
            'order'  => array('SPECIALITY.SPECIALTY_ID' => 'ASC','APPLICATION.APPLICATION_FUNDING_TYPE'=>'ASC'),
            'select' => array(
                'ApplicationsNumber'=>'APPLICATION.APPLICATION_FUNDING_TYPE',
                //'START_DATE'=>'ADMISSION_PLAN.START_DATE',
                'trainingLevel'=>'ORGANIZATION_SPECIALTY_TRAINING_LEVEL',
                'studyPeriod'=>'ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                'studyMode'=>'ORGANIZATION_SPECIALTY_STUDY_MODE',
                'baseEducation'=>'ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'specialtyCode'=>'SPECIALITY.SPECIALTY_CODE',
                'specialtyId'=>'SPECIALITY.SPECIALTY_ID',
                //new ExpressionField('MAX_AGE', 'SUM(%s)', array('ADMISSION_PLAN.TUITION_STUDENTS_NUMBER')),
            )
        ))->fetchAll();

        $grantApplicationsNumber=0;
        $paidApplicationsNumber=0;
        for($i=0;count($result)>$i;$i=$i+1){
            $j=$i+1;
            if(count($result)>=$j) {
                if (
                    $result[$i]['specialtyId'] == $result[$j]['specialtyId']
                ){
                    if($result[$i]['ApplicationsNumber']==ApplicationFundingType::GRANT){
                        $grantApplicationsNumber=$grantApplicationsNumber+1;
                    }
                    if($result[$i]['ApplicationsNumber']==ApplicationFundingType::PAID){
                        $paidApplicationsNumber=$paidApplicationsNumber+1;
                    }
                } else {
                    $ResultNew['grantApplicationsNumber'] = $grantApplicationsNumber+1;
                    $ResultNew['paidApplicationsNumber'] = $paidApplicationsNumber+1;
                    $ResultNew['trainingLevel'] = $result[$i]['trainingLevel'];
                    $ResultNew['studyPeriod'] = $result[$i]['studyPeriod'];
                    $ResultNew['studyMode'] = $result[$i]['studyMode'];
                    $ResultNew['baseEducation'] = $result[$i]['baseEducation'];
                    $ResultNew['specialtyCode'] = $result[$i]['specialtyCode'];
                    $ResultNew['specialtyId'] = $result[$i]['specialtyId'];
                    $ArrResultNew[]=$ResultNew;
                    $grantApplicationsNumber=0;
                    $paidApplicationsNumber=0;
                }
            }
        }
        //Чета надо сделать с этим
        return $result;
    }

    public static function getAllAdmissionPlans($year = null)
    {
        /*$admissionPlans = AdmissionPlanStatRepository::create()->getCommonQuery();

        if ($year)
            $admissionPlans->filterByAdmissionPlanYear($year);
        $rezult=$admissionPlans->executeQuery();*/

        $date = new \Bitrix\Main\Type\DateTime("01.01.".$year." 00:00:00");
        $date2 = $year+1;
        $date2 = new \Bitrix\Main\Type\DateTime("01.01.".$date2." 00:00:00");
        $ArrayResult = AdmissionPlanTable::getList(array(
            'filter' => array(
                '<=ADMISSION_PLAN_START_DATE'=>$date2,
                '>=ADMISSION_PLAN_START_DATE'=>$date,
            ),
            'select' => array(
                'grantStudentsNumber'=>'ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
                'tuitionStudentsNumber'=>'ADMISSION_PLAN_TUITION_STUDENTS_NUMBER',
                'admissionPlanStatus'=>'ADMISSION_PLAN_STATUS',
                'admissionPlanId'=>'ADMISSION_PLAN_ID',
                'organizationId'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_ID',
                'organizationName'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_NAME',
                'organizationRegionAreaId'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.REGION_AREA_ID',
                'trainingLevel'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_TYPE',
                'studyPeriod'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                'studyMode'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
                'baseEducation'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'specialtyTitle'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'specialtyCode'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'specialtyId'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID',
            )
        ))->fetchAll();
        //return $result;
        return $ArrayResult;
    }

    public static function getGeneralStatData(array $filter)
    {
        if (!isset($filter['year']))
            $filter['year'] = date('Y');

        /*$repository = AdmissionPlanStatRepository::create()
            ->getAdmissionPlanGeneralStat();
            ->filterByAdmissionPlanYear($filter['year']);

        $repository->applyFilter($filter);
        $result = $repository->executeQuery();*/
        /*foreach($result as $key=>$itemRezult) {
    $ID = AdmissionPlanTable::getList(array(
        'filter' => array('ORGANIZATION_SPECIALTY_ID'=>$itemRezult['ID']),
        'select' => array(
            new ExpressionField('grantStudentsNumber', 'SUM(%s)', array('GRANT_STUDENTS_NUMBER')),
            new ExpressionField('tuitionStudentsNumber', 'SUM(%s)', array('TUITION_STUDENTS_NUMBER')),
        )
    ))->fetchAll();
    $result[$key]['grantStudentsNumber']=$ID[0]['grantStudentsNumber'];
    $result[$key]['tuitionStudentsNumber']=$ID[0]['tuitionStudentsNumber'];
}*/
        $date = new \Bitrix\Main\Type\DateTime("01.01.".$filter['year']." 00:00:00");
        $date2 = $filter['year']+1;
        $date2 = new \Bitrix\Main\Type\DateTime("01.01.".$date2." 00:00:00");
        $FILTER = array(
            '>=START_DATE'=>$date,
            '<=ADMISSION_PLAN.ADMISSION_PLAN_END_DATE'=>$date2,
            /* array(
             //    'LOGIC' => 'AND',

             ),*/
            array(
                'LOGIC' => 'OR',
                '!ADMISSION_PLAN.ADMISSION_PLAN_TUITION_STUDENTS_NUMBER'=>0,
                '!ADMISSION_PLAN.ADMISSION_PLAN_GRANT_STUDENTS_NUMBER'=>0,
            ),
        );

        if(!empty($filter['organization'])){
            $FILTER['=ORGANIZATION_ID']=$filter['organization'][0];
        }
        //var_dump($FILTER);
        $result = OrganizationSpecialtyTable::getList(array(
            'filter' => $FILTER,
            'group'   => array(
                'ORGANIZATION_ID',
                'ORGANIZATION_SPECIALTY_TRAINING_LEVEL',
                'ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                'ORGANIZATION_SPECIALTY_STUDY_MODE',
                'ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'SPECIALITY.SPECIALTY_TITLE',
                'SPECIALITY.SPECIALTY_CODE',
                'SPECIALITY.SPECIALTY_ID'
            ),
            'order'  => array('SPECIALITY.SPECIALTY_ID' => 'ASC','ORGANIZATION_SPECIALTY_BASE_EDUCATION'=>'ASC'),
            'select' => array(
                'grantStudentsNumber'=>'ADMISSION_PLAN.ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
                'tuitionStudentsNumber'=>'ADMISSION_PLAN.ADMISSION_PLAN_TUITION_STUDENTS_NUMBER',
                'grantGroupsNumber' => 'ADMISSION_PLAN.ADMISSION_PLAN_GRANT_GROUPS_NUMBER',
                'tuitionGroupsNumber' => 'ADMISSION_PLAN.ADMISSION_PLAN_TUITION_GROUPS_NUMBER',
                'START_DATE'=>'ADMISSION_PLAN.ADMISSION_PLAN_START_DATE',
                'ID'=>'ADMISSION_PLAN.ADMISSION_PLAN_ID',
                //'START_DATE'=>'ADMISSION_PLAN.START_DATE',
                'trainingLevel'=>'ORGANIZATION_SPECIALTY_TRAINING_LEVEL',
                'studyPeriod'=>'ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                'studyMode'=>'ORGANIZATION_SPECIALTY_STUDY_MODE',
                'baseEducation'=>'ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'specialtyTitle'=>'SPECIALITY.SPECIALTY_TITLE',
                'specialtyCode'=>'SPECIALITY.SPECIALTY_CODE',
                'specialtyId'=>'SPECIALITY.SPECIALTY_ID',
            )
        ))->fetchAll();

        // var_dump($result);
        $tuition=0;
        $grant=0;
        for($i=0;count($result)>$i;$i=$i+1){
            $j=$i+1;
            if(count($result)>=$j) {
                if (
                    $result[$i]['trainingLevel'] == $result[$j]['trainingLevel']
                    && $result[$i]['studyPeriod'] == $result[$j]['studyPeriod']
                    && $result[$i]['studyMode'] == $result[$j]['studyMode']
                    && $result[$i]['baseEducation'] == $result[$j]['baseEducation']
                    && $result[$i]['specialtyTitle'] == $result[$j]['specialtyTitle']
                    && $result[$i]['specialtyCode'] == $result[$j]['specialtyCode']
                    && $result[$i]['specialtyId'] == $result[$j]['specialtyId']
                ){
                    $qualification['grantStudentsNumber'] = $qualification['grantStudentsNumber']+$result[$i]['grantStudentsNumber'];
                    $qualification['tuitionStudentsNumber'] = $qualification['tuitionStudentsNumber']+$result[$i]['tuitionStudentsNumber'];
                    $qualification['grantGroupsNumber'] = $qualification['tuitionStudentsNumber']+$result[$i]['grantGroupsNumber'];
                    $qualification['tuitionGroupsNumber'] = $qualification['tuitionStudentsNumber']+$result[$i]['tuitionGroupsNumber'];
                } else {
                    $ResultNew['grantStudentsNumber'] = $qualification['grantStudentsNumber']+$result[$i]['grantStudentsNumber'];
                    $ResultNew['tuitionStudentsNumber'] = $qualification['tuitionStudentsNumber']+$result[$i]['tuitionStudentsNumber'];
                    $ResultNew['grantGroupsNumber'] = $qualification['tuitionStudentsNumber']+$result[$i]['grantGroupsNumber'];
                    $ResultNew['tuitionGroupsNumber'] = $qualification['tuitionStudentsNumber']+$result[$i]['tuitionGroupsNumber'];
                    $ResultNew['trainingLevel'] = $result[$i]['trainingLevel'];
                    $ResultNew['studyPeriod'] = $result[$i]['studyPeriod'];
                    $ResultNew['studyMode'] = $result[$i]['studyMode'];
                    $ResultNew['baseEducation'] = $result[$i]['baseEducation'];
                    $ResultNew['specialtyTitle'] = $result[$i]['specialtyTitle'];
                    $ResultNew['specialtyCode'] = $result[$i]['specialtyCode'];
                    $ResultNew['specialtyId'] = $result[$i]['specialtyId'];

                    $ArrResultNew[]=$ResultNew;
                    $qualification['grantStudentsNumber']="";
                }
            }
        }
        return $ArrResultNew;
    }

    // TODO Временный вспомогательный метод. Скорее всего, варианты периодов обучения будут предопределены
    // TODO пока что же выбираем из базы все существующие, чтобы можно ыло сделать фильтр
    public static function getExistingStudyPeriods()
    {
        /*$queryBuilder = D::$em->createQueryBuilder();

        $organizationSpecialties = $queryBuilder
            ->select('DISTINCT OrganizationSpecialty.studyPeriod')
            ->from('Spo\Site\Doctrine\Entities\OrganizationSpecialty', 'OrganizationSpecialty')
            ->getQuery()
            ->execute();
        $result = array();*/

        $organizationSpecialties = OrganizationSpecialtyTable::getList(array(
            'group'   => array('ORGANIZATION_SPECIALTY_STUDY_PERIOD'),
            'order'  => array('ORGANIZATION_SPECIALTY_STUDY_PERIOD' => 'DESC'),
            'select' => array(
                'studyPeriod'=>'ORGANIZATION_SPECIALTY_STUDY_PERIOD'
            )
        ))->fetchAll();
        foreach ($organizationSpecialties as $organizationSpecialty)
            $result[] = $organizationSpecialty['studyPeriod'];
        return $result;

    }

}