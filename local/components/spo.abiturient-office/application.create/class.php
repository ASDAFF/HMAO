<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Dictionaries\ApplicationPriority;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Dictionaries\AdmissionPlanStatus;
use Spo\Site\Entities\UserValidDataTable;
use Spo\Site\Entities\ApplicationTable;
use Spo\Site\Entities\OrganizationTable;
use Spo\Site\Entities\OrganizationSpecialtyTable;
//use Spo\Site\Domains\ApplicationDomain;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Adapters\OrganizationDomainAdapter;
use Spo\Site\Domains\UserDomain;
use Spo\Site\Helpers\AbiturientOfficeUrlHelper;
use Spo\Site\Util\Notification\Notifier;
use Spo\Site\Entities\HostelTable;
use Spo\Site\Entities\AbiturientProfileTable;
use Spo\Site\Helpers\DateFormatHelper;




class ApplicationCreateComponent extends AbiturientOffice
{
	protected $componentPage = '';
	protected $pageTitle = 'Подача заявки на поступление';

	protected function checkParams()
	{
		if (empty($this->arParams['organizationId']))
			throw new Main\ArgumentNullException('organizationId');
	}

	protected function getResult()
	{
		global $USER;
		$userId = $USER->GetID();

		// Перед подачей заявки должны быть подтверждены регистрационные данные пользователя
		$userDomain = UserDomain::loadByUserId($userId);
		if ($userDomain->isUserDataConfirmed()==0) {
			$this->componentPage = 'need-confirm-data-before-application-creation';
			return;
		}
		$apUser=ApplicationTable::getlist(array(
			'filter' => array(
				'=USER_ID' 				=> $userId,
				'=ORGANIZATION_ID' 		=> $this->arParams['organizationId'],
				'!APPLICATION_STATUS'	=> 4,
			),
			'select' =>array('*'),
			))->fetchAll();
		$this->arResult['counts']=count($apUser);

		$organizationDomain = OrganizationDomain::getOrganizationWithSpecialties($this->arParams['organizationId']);
		$organization = $this->listOrganizationSpecialties($this->arParams['organizationId'],$apUser);

		/*получение сведеньей о ранее веденом общежитии*/
		$hostel=HostelTable::UserId($userId,$this->arParams['organizationId']);
		if(!$hostel)
		{
			$OldHostel=0;
		}
		else
		{
			$OldHostel=$hostel['ID_HOSTEL'];
		}


		if ($organizationDomain==0) {
			$this->componentPage = 'application-creation-not-available';
			return;
		}
		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		$applicationData = $request->get('applicationData');

		// Если отправлена форма, то создам заявку
		if ($applicationData) {
			$applicationDataSave = array(
				'APPLICATION_PRIORITY' => $applicationData['applicationPriority'],
				'ORGANIZATION_SPECIALTY_ID' => $applicationData['organizationSpecialtyId'],
				'APPLICATION_CREATION_DATE' => new Bitrix\Main\Type\DateTime(),
				'ORGANIZATION_ID' => $this->arParams['organizationId'],
				'USER_ID' => $userId,
				'APPLICATION_STATUS' => ApplicationStatus::CREATED,
				'ADMISSION_PLAN_ID' => $applicationData['admissionPlanId'],
				'APPLICATION_FUNDING_TYPE' => $applicationData['fundingType'],
				/*'SPO_APPLICATION_NEED_HOSTEL' => $applicationData['needHostel'] != '' ? true : false,*/
				'IMPORT_TO_C' => 0,
			);
			/*заполнение/изминение общежитие*/
			$host=false;
			if($applicationData['needHostel'] != '')
			{
				$host=true;
			}

			if ($OldHostel>0 && !$host)
			{
				$res=HostelTable::delete($OldHostel);
				if (!$res->isSuccess()) {
					$this->arResult['errors'] = $res->getErrorMessages();
					/*var_dump($res->getErrorMessages());*/
				} else {
					$this->arResult['success'] = 'Место в общежитии зарезервировано';
					/*var_dump('Место в общежитии зарезервировано');*/
				}

			}
			if ($host && $OldHostel==0)
			{
				$res=HostelTable::add(
					array(
						'ID_USER'			=>	$userId,
						'ID_ORGANIZATION'	=>	$this->arParams['organizationId']
					));
				if (!$res->isSuccess()) {
					$this->arResult['errors'] = $res->getErrorMessages();
					/*var_dump($res->getErrorMessages());*/
				} else {
					$this->arResult['success'] = 'Место в общежитии зарезервировано';
					/*var_dump('Место в общежитии зарезервировано');*/
				}
			}
			//HostelTable
			$resultUpdate = ApplicationTable::add($applicationDataSave);
			if (!$resultUpdate->isSuccess()) {
				$this->arResult['errors'] = $resultUpdate->getErrorMessages();
			} else {
				$this->arResult['success'] = 'Заявление успешно поданно';
				/*$notifier = new Notifier();
                $notifier->applicationStatusChanged($applicationDomain->lastApplicationEvent);*/
			}

			/*отправка почтового события*/
			$SPECIALTY=OrganizationSpecialtyTable::getList(array(
				'filter'	=>	array(
					'ORGANIZATION_SPECIALTY_ID'	=> $applicationData['organizationSpecialtyId']
				),
				'select'	=>	array(
					'Code'			=>	'SPECIALITY.SPECIALTY_CODE',
					'NameSpec'		=>	'SPECIALITY.SPECIALTY_TITLE',
					'CodeCiliza'	=>	'IDSPECIALIZATION',
					'NameCiliza'	=>	'NAMESPECIALIZATION',
					'BaseEducation'	=>	'ORGANIZATION_SPECIALTY_BASE_EDUCATION',
					'Period'		=>	'ORGANIZATION_SPECIALTY_STUDY_PERIOD'
				)
			))->fetch();

			if($applicationData['fundingType']==1)
			{
				$final='Контрактная форма обучения';
			}
			else
			{
				$final='Бюджетная форма обучения';
			}
			$Ciliza='';
			if(!empty($SPECIALTY['CodeCiliza'])) $Ciliza.=$SPECIALTY['CodeCiliza'].' '.$SPECIALTY['NameCiliza'].', ';
			$APPLICATION_SPECIALTY=$SPECIALTY['Code'].' '.$SPECIALTY['NameSpec'].', '.$Ciliza.BaseEducation::getshortValues($SPECIALTY['BaseEducation']).', '.mb_strtolower($final).', '.DateFormatHelper::months2YearsMonths($SPECIALTY['Period']);
			/*полученние данных пользователя */
			$arInfoUser=AbiturientProfileTable::getlist(array(
				'filter'	=>	array('USER_ID'=>$userId),
				'select'	=>	array(
					'ABITURIENT_EMAIL'			=>	'Spo\Site\Entities\UserValidDataTable:ABITURIENT_PROFILE.USER_VALID_DATA_EMAIL',
					'ABITURIENT_FIO'			=>	'FIO',
					'*')
			))->fetch();
			/*получение данных организации*/
			$arInfoOrgan=OrganizationTable::getlist(array(
				'filter'	=>	array(
					'ORGANIZATION_ID'	=>	$this->arParams['organizationId'],
				),
				'select'	=>	array(
					'ORGANIZATION_NAME',
					'ORGANIZATION_INFO_LINK'	=>	'ORGANIZATION_SITE'
				)
			))->fetch();
			$arEventFields = array(
				'APPLICATION_SPECIALTY'		=>		$APPLICATION_SPECIALTY,
				'ABITURIENT_EMAIL'			=>		$arInfoUser['ABITURIENT_EMAIL'],
				'ABITURIENT_FIO'			=>		$arInfoUser['ABITURIENT_FIO'],
				'ORGANIZATION_NAME'			=>		$arInfoOrgan['ORGANIZATION_NAME'],
				'ORGANIZATION_INFO_LINK'	=>		$arInfoOrgan['ORGANIZATION_INFO_LINK'],
			);
			//print_r(CAdvContract::GetSiteArray(1));
			CEvent::SendImmediate("APPLICATION_CREATED", 's1', $arEventFields);

		}
		$this->arResult['organizationWithAvailableSpecialties'] = $organization;
		$this->arResult['OldHostel'] = $OldHostel;

        //Получаем информацию об образовании пользователя и отправляем в шаблон
        global $USER;
        $userFilterParams = array(
            'filter' => array(
                '=USER_ID' => $USER -> GetID(),
            ),
            'select' => array(
                'EDUCATION' => 'SPO_ABITURIENT_PROFILE_EDUCATION',
            )
        );

        $abiturientProfileEducation = AbiturientProfileTable::getList($userFilterParams)->fetch();
        $this->arResult['USER_PROFILE_EDUCATION'] = $abiturientProfileEducation['EDUCATION'];

    }
	/**
	 * @param $organizationId
	 * @param $apUser
	 * @return array
	 */
	protected function listOrganizationSpecialties($organizationId,$apUser) {
		$params = array(
			'filter' => array(
				'=ORGANIZATION_ID' => $organizationId,
			),
			'select' => array(
				'ID' => 'ORGANIZATION_ID',
				'NAME' => 'ORGANIZATION_NAME',
				'ORG_SPEC_' => 'ORGANIZATION_SPECIALTY.*',
				'SPECIALTY_' => 'ORGANIZATION_SPECIALTY.SPECIALITY.*',
				"GRANT" =>'ORGANIZATION_SPECIALTY.ADMISSION_PLAN.ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
				'TUITION' => 'ORGANIZATION_SPECIALTY.ADMISSION_PLAN.ADMISSION_PLAN_TUITION_STUDENTS_NUMBER',
				'ORGANIZATION_HOSTEL'
			)
		);

        $resultDB = OrganizationTable::getList($params);

		$organization = array();
		$specialtyIds = array();

		while ($result = $resultDB -> fetch()) {
			//var_dump($result['ORG_SPEC_ORGANIZATION_SPECIALTY_BASE_EDUCATION']);
			$key=array_search($result['ORG_SPEC_ORGANIZATION_SPECIALTY_ID'],array_column($apUser,'ORGANIZATION_SPECIALTY_ID'));
			if ($key===false)
				$bul=false;
			else
				$bul=true;
			$organization['id'] = $result['ID'];
			$organization['name'] = $result['NAME'];
			$organization['hostel'] = $result['ORGANIZATION_HOSTEL'];
			$specialtyData = array(
				'id' 			=> $result['ORG_SPEC_ORGANIZATION_SPECIALTY_ID'],
				'code' 			=> $result['SPECIALTY_SPECIALTY_CODE'],
				'title' 		=> $result['SPECIALTY_SPECIALTY_TITLE'],
				'studyMode' 	=> StudyMode::getValue($result['ORG_SPEC_ORGANIZATION_SPECIALTY_STUDY_MODE']),
				'baseEducation' => BaseEducation::getValue($result['ORG_SPEC_ORGANIZATION_SPECIALTY_BASE_EDUCATION']),
				'baseEducationNumber' => $result['ORG_SPEC_ORGANIZATION_SPECIALTY_BASE_EDUCATION'],
				'studyPeriod' 	=> $result['ORG_SPEC_ORGANIZATION_SPECIALTY_STUDY_PERIOD'],
				'trainingLevel' => TrainingLevel::getValue($result['ORG_SPEC_ORGANIZATION_SPECIALTY_TRAINING_LEVEL']),
				'trainingType' 	=> $result['ORG_SPEC_ORGANIZATION_SPECIALTY_TRAINING_LEVEL'],
				'apply'			=> $bul,
			);
			$specialtyIds[] = $result['ORG_SPEC_ORGANIZATION_SPECIALTY_ID'];
			$organization['specialties'][$result['ORG_SPEC_ORGANIZATION_SPECIALTY_ID']] = $specialtyData;
		}
		$d=date('Y')+1;
		$orgSpecialtyParams = array(
			'filter' => array(
				'=ORGANIZATION_SPECIALTY_ID' => $specialtyIds,
				'=ADMISSION_PLAN_ADMISSION_PLAN_STATUS' => AdmissionPlanStatus::ACCEPTED,
				'>=ADMISSION_PLAN_ADMISSION_PLAN_START_DATE' => new \Bitrix\Main\Type\DateTime("01.01.".date('Y')." 00:00:00"),
				'<=ADMISSION_PLAN_ADMISSION_PLAN_END_DATE' => new \Bitrix\Main\Type\DateTime("01.01.".$d." 00:00:00"),
			),
			'select' => array(
				'ID' => 'ORGANIZATION_SPECIALTY_ID',
				'EXAM_' => 'ORGANIZATION_SPECIALTY_EXAM.*',
				'EXAM_dates' => 'ORGANIZATION_SPECIALTY_EXAM.DATE',
				'QUALIFICATIONS_' => 'Spo\Site\Entities\Qualification2OrganizationSpecialtyTable:ORGANIZATION_SPECIALTY.QUALIFICATION.*',
				'ADAPTATION_' => 'ORGANIZATION_SPECIALTY_ADAPTATION.*',
				'ADMISSION_PLAN_' => 'ADMISSION_PLAN.*'
			)
		);
		$orgSpecialtyResultDb = OrganizationSpecialtyTable::getList($orgSpecialtyParams);



		while ($result = $orgSpecialtyResultDb->fetch()) {
			/*echo "<pre>";
			print_r($result);
			echo "</pre>";*/
			if ($result['EXAM_ORGANIZATION_SPECIALTY_EXAM_ID'] != null) {
				$organization['specialties'][$result['ID']]['exams'][$result['EXAM_ID']] = array(
					'discipline' => $result['EXAM_ORGANIZATION_SPECIALTY_EXAM_DISCIPLINE'],
					'type' => $result['EXAM_ORGANIZATION_SPECIALTY_EXAM_TYPE'],
					'date' => $result['EXAM_dates'],
					'adres' => $result['EXAM_ADRES'],
				);
			}

			if ($result['QUALIFICATIONS_QUALIFICATION_ID'] != null) {
				$organization['specialties'][$result['ID']]['qualifications'][$result['QUALIFICATIONS_QUALIFICATION_ID']] = array(
					'id' => $result['QUALIFICATIONS_QUALIFICATION_ID'],
					'title' => $result['QUALIFICATIONS_QUALIFICATION_TITLE'],
				);
			}

			if ($result['ADAPTATION_ORGANIZATION_SPECIALTY_ADAPTATION_ID'] != null) {
				$organization['specialties'][$result['ID']]['adaptationTypes'][$result['ADAPTATION_ORGANIZATION_SPECIALTY_ADAPTATION_ID']] = $result['ADAPTATION_ORGANIZATION_SPECIALTY_ADAPTATION_TYPE'];
			}

			if ($result['ADMISSION_PLAN_ADMISSION_PLAN_STATUS']==AdmissionPlanStatus::ACCEPTED) {
				$organization['specialties'][$result['ID']]['actualAdmissionPlan'] = array(
					'id' => $result['ADMISSION_PLAN_ADMISSION_PLAN_ID'],
					'startDate' => $result['ADMISSION_PLAN_ADMISSION_PLAN_START_DATE'],
					'endDate' => $result['ADMISSION_PLAN_ADMISSION_PLAN_END_DATE'],
					'grantStudentsNumber' => $result['ADMISSION_PLAN_ADMISSION_PLAN_GRANT_STUDENTS_NUMBER'],
					'tuitionStudentsNumber' => $result['ADMISSION_PLAN_ADMISSION_PLAN_TUITION_STUDENTS_NUMBER']
				);
			}
		}
		//var_dump($organization);
		return $organization;
	}
}
?>