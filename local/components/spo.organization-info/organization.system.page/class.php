<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Spo\Site\Adapters\OrganizationDomainAdapter;
use Bitrix\Main;
use Spo\Site\Exceptions\ArgumentException;
use Spo\Site\Adapters\AdmissionPlanStatDomainAdapter;
use Spo\Site\Domains\AdmissionPlanStatDomain;
use Spo\Site\Entities\OrganizationTable;
use Spo\Site\Entities\AbiturientExamTable;
use Spo\Site\Entities\OrganizationSpecialtyTable;
use Spo\Site\Entities\OrganizationSpecialtyExamTable;
use Spo\Site\Entities\EnrollmentTable;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Helpers\DateFormatHelper;

class OrganizationInfoSystemPageComponent extends OrganizationInfo
{
	private $organizationSection = '';
	protected $componentPage = '';

	protected function checkParams()
	{
		if (empty($this->arParams['organizationId']))
			throw ArgumentException::argumentMissing('organizationId');

		if (!empty($this->arParams['section']))
			$this->organizationSection = $this->arParams['section'];
	}

	protected function getResult()
	{
		switch ($this->organizationSection) {
			case 'specialties' :
				$this->getOrganizationSpecialtyResult();
				$this->componentPage = 'specialties';
				break;
            case 'control-of-entrance' :
                $this->getOrganizationControlOfEntranceResult();
                $this->componentPage = 'control-of-entrance';
                break;
			case 'renrollment-count' :
				$this->getRenrollmentCountResult();
				$this->componentPage = 'renrollment-count';
				break;
            case 'statistic-application' :
                $this->getOrganizationStatisticApplicationResult();
                $this->componentPage = 'statistic-application';
                break;
			case 'entry-exams-schedule' :
				$this->getEntryExamsSchedule();
				$this->componentPage = 'entry-exams-schedule';
				break;
			case 'entry-exams-result' :
				$this->getEntryExamsResult();
				$this->componentPage = 'entry-exams-result';
				break;
			default:
				$this->getOrganizationInfoResult();
				$this->componentPage = '';
		}
	}


	// TODO Запрос и view практически аналогичены getOrganizationControlOfEntranceResult, возможно, стоит объединить
    private function getOrganizationStatisticApplicationResult()
    {
		$filter = array('year' => date('Y'), 'organization' => array($this->arParams['organizationId']));
		$admissionPlan = AdmissionPlanStatDomain::getGeneralStatData($filter);

        $this->arResult['statisticApplication'] = $admissionPlan;

   }

	private function getRenrollmentCountResult()
	{
		/*получение списка профессий у данной организации*/
		$date1=date('Y')+1;
		$spec=OrganizationSpecialtyTable::getlist(array(
			'filter'	=>	array(
				'=ORGANIZATION_ID'	=>	$this->arParams['organizationId'],
				'>=ADMISSION_PLAN.ADMISSION_PLAN_START_DATE'	=> new \Bitrix\Main\Type\DateTime("01.01.".date('Y')." 00:00:00"),
				'<=ADMISSION_PLAN.ADMISSION_PLAN_END_DATE'		=> new \Bitrix\Main\Type\DateTime("01.01.".$date1." 00:00:00")
				),
			'select'	=>	array(
				'name'			=>	'SPECIALITY.SPECIALTY_TITLE',
				'Idspec'		=>	'ORGANIZATION_SPECIALTY_ID',
				'baseEducation'	=>	'ORGANIZATION_SPECIALTY_BASE_EDUCATION',
				'Grant'			=>	'ADMISSION_PLAN.ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
				'Tution'		=>	'ADMISSION_PLAN.ADMISSION_PLAN_TUITION_STUDENTS_NUMBER'),
		))->fetchAll();
		/*echo "<pre>";
		print_r($spec);
		echo "</pre>";
		die;*/

		/*получение списка данных по рекомендованых к зачеслению*/
		if(!empty($_GET['spec']))
		{
			$res=explode('_',$_GET['spec']);

			$Renrollment=EnrollmentTable::getlist(array(
					'filter'	=>	array(
						'=ORGANIZATION_ID'							=>	$this->arParams['organizationId'],
						'=ADMISSION_PLAN.ORGANIZATION_SPECIALTY_ID'	=>	(int)$res[0],
						'=ENROLLMENT_FINANCE'						=>	(int)$res[1],
					),
					'order' 	=>	array(
						'ENROLLMENT' 		=> 'DESC',
						'ENROLLMENT_PRIORY' => 'DESC',
						'ENROLLMENT_PRIORY' => 'ASC'),
					'select'	=>	array(
						'*',
						'NameSpec'		=>	'ADMISSION_PLAN.ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
						'Month'			=>	'ADMISSION_PLAN.ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
						'baseEducation'	=>	'ADMISSION_PLAN.ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
						)
				))->fetchAll();
			if (!empty($Renrollment))
			{
				if($res[1]==1)
				{
					$final='Контрактное';
				}
				else
				{
					$final='Бюджетное';
				}
				$info=$Renrollment[0]['NameSpec'].' '.BaseEducation::getshortValues($Renrollment[0]['baseEducation']).' '.mb_strtolower($final).' '.DateFormatHelper::months2YearsMonths($Renrollment[0]['Month']);
				$this->arResult['infos']=$info;
			}
			$this->arResult['Renrollment'] = $Renrollment;
		}
		$this->arResult['spec']	= $spec;


	}


	private function getOrganizationControlOfEntranceResult()
    {

		$filter = array('year' => date('Y'), 'organization' => array($this->arParams['organizationId']));
        $admissionPlan = AdmissionPlanStatDomain::getGeneralStatData($filter);
        $applicationsNumber = AdmissionPlanStatDomain::getGeneralApplicationsNumber($filter);

        $this->arResult['admissionPlanWithRequestNumber'] = AdmissionPlanStatDomainAdapter::getAdmissionPlanWithApplicationsNumber(
            $admissionPlan, $applicationsNumber
        );

    }

	private function getOrganizationSpecialtyResult()
	{
		/*$organizationDomain = OrganizationDomain::getOrganizationWithSpecialties($this->arParams['organizationId'], true);
		$this->arResult['organization'] = OrganizationDomainAdapter::listOrganizationSpecialties($organizationDomain);*/
		$ArrayResult = OrganizationTable::getList(array(
			'filter' => array(
				'ORGANIZATION_ID'=>$this->arParams['organizationId'],
			),
			//'group'   => array('ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE'),
			'order'   => array('ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE'=>'ASC',
				               'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_EXAM.ORGANIZATION_SPECIALTY_EXAM_DISCIPLINE'=>'ASC',
				               'ORGANIZATION_SPECIALTY.SPECIALITY.QUALIFICATIONS.QUALIFICATION.QUALIFICATION_ID'=>'ASC',
				),
			'select' => array(
				'id'=>'ORGANIZATION_ID',
				'name'=>'ORGANIZATION_FULL_NAME',
				'code'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
				'specialty_id'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_ID',
				'title'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
				'studyMode'=>'ORGANIZATION_SPECIALTY.SPECIALITY.ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
				'baseEducation'=>'ORGANIZATION_SPECIALTY.SPECIALITY.ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
				'studyPeriod'=>'ORGANIZATION_SPECIALTY.SPECIALITY.ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
				'trainingLevel'=>'ORGANIZATION_SPECIALTY.SPECIALITY.ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_LEVEL',
				'trainingType'=>'ORGANIZATION_SPECIALTY.SPECIALITY.ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_TYPE',
				'exams_discipline'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_EXAM.ORGANIZATION_SPECIALTY_EXAM_DISCIPLINE',
				'exams_type'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_EXAM.ORGANIZATION_SPECIALTY_EXAM_TYPE',
				'qualifications_id'=>'ORGANIZATION_SPECIALTY.QUALIFICATION2ORGANIZATIONSPECIALTYTABLE.QUALIFICATION.QUALIFICATION_ID',
				'qualifications_title'=>'ORGANIZATION_SPECIALTY.QUALIFICATION2ORGANIZATIONSPECIALTYTABLE.QUALIFICATION.QUALIFICATION_TITLE',
				'actualAdmissionPlan_startDate'=>'ORGANIZATION_SPECIALTY.ADMISSION_PLAN.ADMISSION_PLAN_START_DATE',
				'actualAdmissionPlan_endDate'=>'ORGANIZATION_SPECIALTY.ADMISSION_PLAN.ADMISSION_PLAN_END_DATE',
				'actualAdmissionPlan_grantStudentsNumber'=>'ORGANIZATION_SPECIALTY.ADMISSION_PLAN.ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
				'actualAdmissionPlan_tuitionStudentsNumber'=>'ORGANIZATION_SPECIALTY.ADMISSION_PLAN.ADMISSION_PLAN_TUITION_STUDENTS_NUMBER',
				'adaptationType'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_ADAPTATION.ORGANIZATION_SPECIALTY_ADAPTATION_TYPE',
			)
		))->fetchAll();
		for($i=0; count($ArrayResult)>$i; $i=$i+1){
			if(
				$ArrayResult[$i]['code']!=$ArrayResult[$i+1]['code'] or
				$ArrayResult[$i]['exams_discipline']!=$ArrayResult[$i+1]['exams_discipline'] or
				$ArrayResult[$i]['exams_type']!=$ArrayResult[$i+1]['exams_type'] or
				$ArrayResult[$i]['qualifications_title']!=$ArrayResult[$i+1]['qualifications_title'] or
				$ArrayResult[$i]['adaptationType']!=$ArrayResult[$i+1]['adaptationType']
			) {
				$ArrayResultNew[]=$ArrayResult[$i];
			}
		}
		$ArrayResult=array();
		$ArrayResult['id']=$ArrayResultNew[0]['id'];
		$ArrayResult['name']=$ArrayResultNew[0]['name'];
		for($i=0; count($ArrayResultNew)>$i; $i=$i+1){
			unset($ArrayResultNew[$i]['name']);
			unset($ArrayResultNew[$i]['id']);
			$ArrayResultNew[$i]['studyMode']=StudyMode::getValue($ArrayResultNew[$i]['studyMode']);
			$ArrayResultNew[$i]['baseEducation']=BaseEducation::getValue($ArrayResultNew[$i]['baseEducation']);
			$ArrayResultNew[$i]['trainingLevel']=TrainingLevel::getValue($ArrayResultNew[$i]['trainingLevel']);
			$ArrayResult['specialties'][]=$ArrayResultNew[$i];
		}
		$ArrayResultNew=array();
		$j=0;
		for($i=0; count($ArrayResult['specialties'])>$i; $i=$i+1){
			if($ArrayResult['specialties'][$i]['code']==$ArrayResult['specialties'][$i+1]['code']){
				if($ArrayResult['specialties'][$i]['exams_discipline']!='' and $ArrayResult['specialties'][$i]['exams_discipline']!=$ArrayResult['specialties'][$i+1]['exams_discipline']) {
					$exams[] = array(
						'discipline' => $ArrayResult['specialties'][$i]['exams_discipline'],
						'type' => $ArrayResult['specialties'][$i]['exams_type']
					);
				}
				if($ArrayResult['specialties'][$i]['qualifications_id']!='' and $ArrayResult['specialties'][$i]['qualifications_id']!=$ArrayResult['specialties'][$i+1]['qualifications_id']){
					$qualifications[] = array(
						'id' => $ArrayResult['specialties'][$i]['qualifications_id'],
						'title' => $ArrayResult['specialties'][$i]['qualifications_title']
					);
				}
				if($ArrayResult['specialties'][$i]['actualAdmissionPlan_startDate']!=''
					and $ArrayResult['specialties'][$i]['actualAdmissionPlan_startDate']!=$ArrayResult['specialties'][$i+1]['actualAdmissionPlan_startDate']
					and $ArrayResult['specialties'][$i]['actualAdmissionPlan_endDate']!=$ArrayResult['specialties'][$i+1]['actualAdmissionPlan_endDate']
					and $ArrayResult['specialties'][$i]['actualAdmissionPlan_grantStudentsNumber']!=$ArrayResult['specialties'][$i+1]['actualAdmissionPlan_grantStudentsNumber']
					and $ArrayResult['specialties'][$i]['actualAdmissionPlan_tuitionStudentsNumber']!=$ArrayResult['specialties'][$i+1]['actualAdmissionPlan_tuitionStudentsNumber']
				){
					if(strtotime("now") < strtotime($ArrayResult['specialties'][$i]['actualAdmissionPlan_endDate'])){
					   $actualAdmissionPlan[] = array(
						'startDate' => $ArrayResult['specialties'][$i]['actualAdmissionPlan_startDate'],
						'endDate' => $ArrayResult['specialties'][$i]['actualAdmissionPlan_endDate'],
						'grantStudentsNumber' => $ArrayResult['specialties'][$i]['actualAdmissionPlan_grantStudentsNumber'],
						'tuitionStudentsNumber' => $ArrayResult['specialties'][$i]['actualAdmissionPlan_tuitionStudentsNumber'],
					   );
					}
				}
				if($ArrayResult['specialties'][$i]['adaptationType']!='' and $ArrayResult['specialties'][$i]['adaptationType']!=$ArrayResult['specialties'][$i+1]['adaptationType']){
					$adaptationTypes[] = $ArrayResult['specialties'][$i]['adaptationType'];
				}
				unset($ArrayResult[$i]);
			}
			else{
				if($ArrayResult['specialties'][$i]['exams_discipline']!='' and $ArrayResult['specialties'][$i]['exams_discipline']!=$ArrayResult['specialties'][$i+1]['exams_discipline']) {
					$exams[] = array(
						'discipline' => $ArrayResult['specialties'][$i]['exams_discipline'],
						'type' => $ArrayResult['specialties'][$i]['exams_type']
					);
				}
				if($ArrayResult['specialties'][$i]['qualifications_id']!='' and $ArrayResult['specialties'][$i]['qualifications_id']!=$ArrayResult['specialties'][$i+1]['qualifications_id']){
					$qualifications[] = array(
						'id' => $ArrayResult['specialties'][$i]['qualifications_id'],
						'title' => $ArrayResult['specialties'][$i]['qualifications_title']
					);
				}
				if($ArrayResult['specialties'][$i]['actualAdmissionPlan_startDate']!=''
					and $ArrayResult['specialties'][$i]['actualAdmissionPlan_startDate']!=$ArrayResult['specialties'][$i+1]['actualAdmissionPlan_startDate']
					and $ArrayResult['specialties'][$i]['actualAdmissionPlan_endDate']!=$ArrayResult['specialties'][$i+1]['actualAdmissionPlan_endDate']
					and $ArrayResult['specialties'][$i]['actualAdmissionPlan_grantStudentsNumber']!=$ArrayResult['specialties'][$i+1]['actualAdmissionPlan_grantStudentsNumber']
					and $ArrayResult['specialties'][$i]['actualAdmissionPlan_tuitionStudentsNumber']!=$ArrayResult['specialties'][$i+1]['actualAdmissionPlan_tuitionStudentsNumber']
				){
					if(strtotime("now") < strtotime($ArrayResult['specialties'][$i]['actualAdmissionPlan_endDate'])){
						$actualAdmissionPlan[] = array(
							'startDate' => $ArrayResult['specialties'][$i]['actualAdmissionPlan_startDate'],
							'endDate' => $ArrayResult['specialties'][$i]['actualAdmissionPlan_endDate'],
							'grantStudentsNumber' => $ArrayResult['specialties'][$i]['actualAdmissionPlan_grantStudentsNumber'],
							'tuitionStudentsNumber' => $ArrayResult['specialties'][$i]['actualAdmissionPlan_tuitionStudentsNumber'],
						);
					}
				}
				if($ArrayResult['specialties'][$i]['adaptationType']!='' and $ArrayResult['specialties'][$i]['adaptationType']!=$ArrayResult['specialties'][$i+1]['adaptationType']){
					$adaptationTypes[] = $ArrayResult['specialties'][$i]['adaptationType'];
				}
				$ArrayResultNew['specialties'][$j]['code']=$ArrayResult['specialties'][$i]['code'];
				$ArrayResultNew['specialties'][$j]['id']=$ArrayResult['specialties'][$i]['id'];
				$ArrayResultNew['specialties'][$j]['title']=$ArrayResult['specialties'][$i]['title'];
				$ArrayResultNew['specialties'][$j]['studyMode']=$ArrayResult['specialties'][$i]['studyMode'];
				$ArrayResultNew['specialties'][$j]['baseEducation']=$ArrayResult['specialties'][$i]['baseEducation'];
				$ArrayResultNew['specialties'][$j]['studyPeriod']=$ArrayResult['specialties'][$i]['studyPeriod'];
				$ArrayResultNew['specialties'][$j]['trainingLevel']=$ArrayResult['specialties'][$i]['trainingLevel'];
				$ArrayResultNew['specialties'][$j]['trainingType']=$ArrayResult['specialties'][$i]['trainingType'];
				$ArrayResultNew['specialties'][$j]['exams']=$exams;
				$qualifications= array_map("unserialize", array_unique( array_map("serialize", $qualifications) ));
				$ArrayResultNew['specialties'][$j]['qualifications']=$qualifications;
				$ArrayResultNew['specialties'][$j]['actualAdmissionPlan']=$actualAdmissionPlan;
				$adaptationTypes= array_map("unserialize", array_unique( array_map("serialize", $adaptationTypes) ));
				$ArrayResultNew['specialties'][$j]['adaptationTypes']=$adaptationTypes;
				$exams=array();
				$qualifications=array();
				$actualAdmissionPlan=array();
				$adaptationTypes=array();
				$j=$j+1;
			}
		}

		$this->arResult['organization']=$ArrayResultNew;
	}

	private function getOrganizationInfoResult(){
		$this->arResult['organization'] = OrganizationDomainAdapter::getOrganizationInformation(
			$this->arParams['organizationId']
		);
	}

	private function getEntryExamsSchedule()
	{
		$organizationSpecialityIds = $organizationSpecialities = $organizationSpecialitiesExams = array();

		/* Выборка специальностей */
		$organizationSpecialitiesDb = OrganizationSpecialtyTable::getlist(array(
			'filter' =>	array(
				'=ORGANIZATION_ID' => $this->arParams['organizationId'],
			),
			'select' =>	array(
				'ORGANIZATION_SPECIALTY_ID',
				'IDPROGRAM',
				'NAME' => 'SPECIALITY.SPECIALTY_TITLE',
				'CODE' => 'SPECIALITY.SPECIALTY_CODE',
				'BASE_EDUCATION' => 'ORGANIZATION_SPECIALTY_BASE_EDUCATION',
				'STUDY_MODE' => 'ORGANIZATION_SPECIALTY_STUDY_MODE',
			)
		));

		while ($speciality = $organizationSpecialitiesDb->fetch()) {
			$organizationSpecialityIds[] = $speciality['ORGANIZATION_SPECIALTY_ID'];
			$organizationSpecialities[$speciality['ORGANIZATION_SPECIALTY_ID']] = $speciality;
		}

		/* Выборка экзаменов по специальностям */
		$organizationSpecialityExamsDb = OrganizationSpecialtyExamTable::getlist(array(
			'filter' =>	array(
				'=ORGANIZATION_SPECIALTY_ID' => $organizationSpecialityIds
			),
			'select' =>	array(
				'*'
			)
		));
		while ($exam = $organizationSpecialityExamsDb->fetch()) {
			$organizationSpecialitiesExams[$exam['ORGANIZATION_SPECIALTY_ID']][] = $exam;
		}

		$this->arResult = array(
			'specialities' => $organizationSpecialities,
			'exams' => $organizationSpecialitiesExams,
		);
	}

	private function getEntryExamsResult()
	{
		$users = array();
		$abiturientExams = AbiturientExamTable::getList(array(
			'filter' => array(
				'=ID_ORGANIZATION' => $this->arParams['organizationId'],
			),
			'select' => array(
				'ID_ABITURIENT', 'ID_ORGANIZATION_SPECIALTY', 'TEST', 'BALL', 'FROM_EXEM', 'APPEAR', 'DATE'
			),
			'order' => array('DATE' => 'ASC')
		))->fetchAll();

		$userIds = array_column($abiturientExams, 'ID_ABITURIENT');

		$cUser = new CUser();
		$userDb = $cUser->GetList(($by = 'ID'), ($order = 'asc'), array("ID" => implode('|', $userIds)),
				array('FIELDS' => array('ID', 'NAME', 'LAST_NAME', 'SECOND_NAME')));
		while ($user = $userDb->Fetch()) {
			$users[$user['ID']] = $user['LAST_NAME'].' '.$user['NAME'].' '.$user['SECOND_NAME'];
		}

		$this->arResult = array(
			'abiturientExams' => $abiturientExams,
			'users' => $users
		);
	}
}
?>