<?php
namespace Spo\Site\Domains;

use Spo\Site\Core\SPODomain;
use Bitrix\Main;
use Spo\Site\Dictionaries\EducationDocumentType;
//use Spo\Site\Doctrine\Entities\AbiturientProfile;
use D;
use CIBlockElement;
use CUser;
use CFile;
//use Spo\Site\Doctrine\Entities\UserValidData;
//use Spo\Site\Doctrine\Repositories\AbiturientProfileRepository;
use Spo\Site\Exceptions\ArgumentException;
use Spo\Site\Util\CVarDumper;
use CModule;
//use Spo\Site\Doctrine\Entities\BitrixUser;
use Spo\Site\Helpers\UserDataConfirmationHelper;
use Symfony\Component\Validator\ConstraintViolation;
use Spo\Site\Entities\AbiturientProfileTable;
use Spo\Site\Entities\UserValidDataTable;
use Spo\Site\Entities\ParentTable;
use Spo\Site\Dictionaries;


class UserDomain extends SPODomain
{
	// todo возможно, стоит ориентироваться на символьные коды групп пользователей и получать id групп динамически,
	// todo нужен отдельный класс. Зависит от дальнейшей судьбы проекта, метода его установки, и т.д.

	// @var BitrixUser
	protected $entity;

	const ABITURIENTS_GROUP_ID = 5;
	const UNCONFIRMED_ORGANIZER_GROUP_ID = 6;
	const ORGANIZER_GROUP_ID = 7;
	const EDUCATIONAL_DEPARTMENT_EMPLOYEE = 8;

	const PROFILE_IS_NOT_CORRECT = 1;
	const PROFILE_IS_CORRECT = 2;

    const CONFIRMATION_CODE_TYPE_EMAIL = 'email';
    const CONFIRMATION_CODE_TYPE_PHONE = 'phone';

	private static function checkUserBelongsToGroups($groupId, $userGroups)
	{
		foreach ($userGroups as $group) {
			if ($group['GROUP_ID'] == $groupId) {
				return true;
			}
		}

		return false;
	}

	 //Проверяет, есть ли среди идентификаторов в переданом массиве идентификатор, соответствующий группе "Абитуриенты"
	 //@param array $groupsIds
	 //@return bool
	public static function checkIsAbiturient(array $groupsIds)
	{
		return self::checkUserBelongsToGroups(self::ABITURIENTS_GROUP_ID, $groupsIds);
	}

	public static function createAbiturientProfile($userId)
	{
		$userDomain = UserDomain::loadByUserId($userId);

		if ($userDomain === null)
			throw new Main\SystemException('Not Found Exception');


		return $userDomain;
	}


     //Проверяет, есть ли среди идентификаторов в переданом массиве идентификатор, соответствующий группе "Организация"
     //@param CUser $bxUser
     //@return bool

    public static function checkIsOrganizationEmployee($bxUser)
    {
		global $USER;
		$arGroups = $USER->GetUserGroupArray();
        return self::checkUserBelongsToGroups(self::ORGANIZER_GROUP_ID, $arGroups);
    }

	public static function loadByUserId($userId)
	{

		return new UserDomain();
	}



	public function addAbiturientProfileParent($profileFormData,$userId,$type)
	{
		/*определения опикуна*/
		$i=0;
		//var_dump($profileFormData);
		while (!empty($profileFormData['fio'][$i]))
		{
			$res['ID_ABITURIENT']=$userId;
			$res['TYPEPARENT']=$i+1;
			$res['FIO']=$profileFormData['fio'][$i];
			$res['BIRTHDATE']=new \Bitrix\Main\Type\Date(date("d.m.Y",strtotime($profileFormData['birthdate'][$i])));
			$res['SNILS']=preg_replace('/[^0-9]/', '', $profileFormData['snils'][$i]);
			$res['CITIZENSHIP']=(int)$profileFormData['citizenship'][$i];
			$res['DOCTYPEPERS']=1;
			$res['DOCSERPERS']=preg_replace('/[^0-9]/', '', $profileFormData['docserpers'][$i]);
			$res['DOCNUMPERS']=(int)$profileFormData['docnumpers'][$i];
			$res['DOCISSUEDPERS']=$profileFormData['docissuedpers'][$i];
			$res['DOCDATEPERS']=UserDomain::DateBitrix($profileFormData['docdatepers'][$i]);
			$res['PHONE']=$profileFormData['Phone'][$i];
			if($type==2)
			{
				$res['TYPEPARENT'] = 3;
				$res['DOBDOCUMENT']=$profileFormData['dobdocument'][0];
			}
			if (!empty($profileFormData['idparent'][$i]))
			{
				$IdParent=ParentTable::update((int) $profileFormData['idparent'][$i],$res);
			}
			else
			{
				$IdParent=ParentTable::add($res);
			}

			if (!$IdParent->isSuccess())
			{
				$mas=implode(' Error:',$IdParent->getErrorMessages());
				var_dump($mas);
			}
			else
			{
				var_dump("Профиль сохранился с id:".$IdParent->getId());
			}
			$i++;
		}
	}

    public function isAbiturientProfileCorrect($userId)
    {
		$params = array(
			'filter' => array(
				'=USER_ID' => $userId,
			),
			'select' => array(
				'SPO_ABITURIENT_PROFILE_ID',
			)
		);
		$resultDb = AbiturientProfileTable::getList($params)->fetchAll();
		//while ($result = $resultDb->fetch()) {
			//$result['creationDate'] = $result['APPLICATION_CREATION_DATE']->format('d.m.Y');
			//$resultList[] = $result;
		//}

		if (empty($resultDb[0]['SPO_ABITURIENT_PROFILE_ID'])) {
			return 0;
		}
		else {
			return 1;
		}
		//$returnn=$this->entity->getAbiturientProfile()->isCorrect();
        //return $returnn;
    }
	public function DateBitrix($datate){
		if (preg_match("/([0-2]\d|3[01])\-(0\d|1[012])\-(\d{4})/" , $datate)!=NULL) {
			$datate = mb_ereg_replace('-', '.', $datate);
			$datate = new \Bitrix\Main\Type\DateTime($datate . " 00:00:00");
		}
		else {
			$datate = "";
		}
		return $datate;
	}
	public function FileBitrixZagruz($proArrial = array()){
		foreach($_FILES['AbiturientProfile'] as $key => $item) {
			if($key != 'error'){
				if (!empty($item['abiturientProfilePhoto'])) {
					$Files['abiturientProfilePhoto'][$key] = $item['abiturientProfilePhoto'];
				}
				if (!empty($item['identityDocumentScanFile'])) {
					$Files['identityDocumentScanFile'][$key] = $item['identityDocumentScanFile'];
				}
				if (!empty($item['identityDocumentRegistrationScanFile'])) {
					$Files['identityDocumentRegistrationScanFile'][$key] = $item['identityDocumentRegistrationScanFile'];
				}
				if (!empty($item['INNScanFile'])) {
					$Files['INNScanFile'][$key] = $item['INNScanFile'];
				}
				if (!empty($item['SNILSScanFile'])) {
					$Files['SNILSScanFile'][$key] = $item['SNILSScanFile'];
				}
				if (!empty($item['educationDocumentScanFile'])) {
					$Files['educationDocumentScanFile'][$key] = $item['educationDocumentScanFile'];
				}
			}
		}
		foreach($Files as $key => $item){
			if(!empty($proArrial[$key])) {
			  	CFile::Delete($proArrial[$key]);
			}
			$fileId[$key] = CFile::SaveFile($item, 'abiturient');
		}
		return $fileId;
	}
	public function isAbiturientProfileAdd($profileFormData,$userId)
	{
		$profileFormData['abiturientProfileBirthday']=UserDomain::DateBitrix($profileFormData['abiturientProfileBirthday']);
		$profileFormData['abiturientProfileIdentityDocumentIssuedDate']=UserDomain::DateBitrix($profileFormData['abiturientProfileIdentityDocumentIssuedDate']);
		$profileFormData['abiturientProfileInsuranceDate']=UserDomain::DateBitrix($profileFormData['abiturientProfileInsuranceDate']);
		$profileFormData['abiturientProfileEducationCompletionDate']=UserDomain::DateBitrix($profileFormData['abiturientProfileEducationCompletionDate']);
		$Fails=UserDomain::FileBitrixZagruz();
		$profileFormData['abiturientProfileIdentityDocumentScanFileLink']=$Fails['identityDocumentScanFile'];
		$profileFormData['abiturientProfileIdentityDocumentRegistrationScanFileLink']=$Fails['identityDocumentRegistrationScanFile'];
		$profileFormData['abiturientProfileINNScanFile']=$Fails['INNScanFile'];
		$profileFormData['abiturientProfileSNILSScanFile']=$Fails['SNILSScanFile'];
		$profileFormData['abiturientProfileEducationDocumentScanFile']=$Fails['educationDocumentScanFile'];
		if(empty($profileFormData['abiturientProfileAdditionalLanguage'])) {
			$profileFormData['abiturientProfileAdditionalLanguage'] = 2;
		}
		if(empty($profileFormData['abiturientProfileEducation'])) {
			$profileFormData['abiturientProfileEducation'] = Dictionaries\BaseEducation::BASIC;
		}

		$in=array(
			'USER_ID' =>$userId,
			'SPO_ABITURIENT_PROFILE_GENDER' =>$profileFormData['abiturientProfileGender'],
			'SPO_ABITURIENT_PROFILE_BIRTHDAY'=>$profileFormData['abiturientProfileBirthday'],
			'SPO_ABITURIENT_PROFILE_BIRTHPLACE'=>$profileFormData['abiturientProfileBirthplace'],
			'SPO_ABITURIENT_PROFILE_ADDITIONAL_LANGUAGE'=>$profileFormData['abiturientProfileAdditionalLanguage'],
			'SPO_ABITURIENT_PROFILE_CAS'=>$profileFormData['abiturientProfileCAS'],
			'SPO_ABITURIENT_PROFILE_EDUCATION'=>$profileFormData['abiturientProfileEducation'],
			'SPO_ABITURIENT_PROFILE_INN'=>$profileFormData['abiturientProfileINN'],
			'SPO_ABITURIENT_PROFILE_SNILS'=> preg_replace('/[^0-9]/', '', $profileFormData['abiturientProfileSNILS']),
			'SPO_ABITURIENT_PROFILE_NATIONALITY'=>$profileFormData['abiturientProfileNationality'],
			'SPO_ABITURIENT_PROFILE_NATIONALITY_COUNTRY'=>$profileFormData['abiturientProfileNationalityCountry'],

			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_TYPE'=>$profileFormData['abiturientProfileIdentityDocumentType'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_SERIES'=>$profileFormData['abiturientProfileIdentityDocumentSeries'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_NUMBER'=>$profileFormData['abiturientProfileIdentityDocumentNumber'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_BY'=>$profileFormData['abiturientProfileIdentityDocumentIssuedBy'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_DATE'=>$profileFormData['abiturientProfileIdentityDocumentIssuedDate'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_CODE'=>$profileFormData['abiturientProfileIdentityDocumentIssuedCode'],

			'SPO_ABITURIENT_PROFILE_INSURANCE_COMPANY_NAME'=>$profileFormData['abiturientProfileInsuranceCompanyName'],
			'SPO_ABITURIENT_PROFILE_INSURANCE_NUMBER'=>$profileFormData['abiturientProfileInsuranceNumber'],
			'SPO_ABITURIENT_PROFILE_INSURANCE_SERIES'=>$profileFormData['abiturientProfileInsuranceSeries'],
			'SPO_ABITURIENT_PROFILE_INSURANCE_DATE'=>$profileFormData['abiturientProfileInsuranceDate'],

			'SPO_ABITURIENT_PROFILE_REGISTRATION_ADDRESS'=>$profileFormData['abiturientProfileRegistrationAddress'],

			'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_TYPE'=>$profileFormData['abiturientProfileEducationOrganizationType'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_NUMBER'=>$profileFormData['abiturientProfileEducationOrganizationNumber'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_CITY'=>$profileFormData['abiturientProfileEducationOrganizationCity'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_NAME'=>$profileFormData['abiturientProfileEducationOrganizationName'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_COMPLETION_DATE'=>$profileFormData['abiturientProfileEducationCompletionDate'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_TYPE'=>$profileFormData['abiturientProfileEducationDocumentType'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_NUMBER'=>$profileFormData['abiturientProfileEducationDocumentNumber'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_SERIES'=>$profileFormData['abiturientProfileEducationDocumentSeries'],

			'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_SERIES'=>$profileFormData['abiturientProfileMilitaryDocumentSeries'],
			'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_NUMBER'=>$profileFormData['abiturientProfileMilitaryDocumentNumber'],
			'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_REGION'=>$profileFormData['abiturientProfileMilitaryDocumentRegion'],
			'SPO_ABITURIENT_PROFILE_IS_RESERVIST'=>$profileFormData['abiturientProfileIsReservist'],
			'SPO_ABITURIENT_PROFILE_ADDITIONAL_DATA'=>$profileFormData['abiturientProfileAdditionalData'],
			'SPO_ABITURIENT_PROFILE_GRADUATED_WITH_HONOURS'=>$profileFormData['abiturientProfileGraduatedWithHonours'],
			'SPO_ABITURIENT_PROFILE_FIRST_TIME_ENROLMENT'=>(int)$profileFormData['abiturientProfileFirstTimeEnrolment'],
			'SPO_ABITURIENT_PROFILE_OLYMPIAD_WINNER'=>$profileFormData['abiturientProfileOlympiadWinner'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_SCAN_FILE'=>$profileFormData['abiturientProfileIdentityDocumentScanFileLink'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_REGISTRATION_SCAN_FILE'=>$profileFormData['abiturientProfileIdentityDocumentRegistrationScanFileLink'],
			'SPO_ABITURIENT_PROFILE_INN_SCAN_FILE'=>$profileFormData['abiturientProfileINNScanFile'],
			'SPO_ABITURIENT_PROFILE_SNILS_SCAN_FILE'=>$profileFormData['abiturientProfileSNILSScanFile'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_SCAN_FILE'=>$profileFormData['abiturientProfileEducationDocumentScanFile'],
			'SPO_ABITURIENT_PROFILE_SENIORITY'=>$profileFormData['abiturientProfileSeniorityString'],
			'SPO_ABITURIENT_PROFILE_IS_CORRECT'=>1,
			'ADDRESSRESIDENCE'=>$profileFormData['AddressResidence'],
			'SPO_ABITURIENT_PROFILE_OLYMPIAD_STRING'=>$profileFormData['abiturientProfileOlympiadWinnerString'],
            'VALIDITY' => $profileFormData['VALIDITY'],
            'USER_VALID_ID' => $profileFormData['USER_VALID_ID'],

		);
		$result = AbiturientProfileTable::add($in);
		/*изменение данных ФИО телефон и мыло*/
		if(!empty($profileFormData['abiturientProfileFIO']))
		{
			$FIO=explode(' ',$profileFormData['abiturientProfileFIO']);
			$user = new CUser;
			$fields = Array(
				"NAME"              => $FIO[0],
				"LAST_NAME"         => $FIO[1],
				"SECOND_NAME"       => $FIO[2],
			);
			$user->Update($userId, $fields);
		}
		if(!empty($profileFormData['abiturientProfilePhone']) || !empty($profileFormData['abiturientProfileEmail']))
		{
			$resultDb = UserValidDataTable::getList(array(
				'filter'=>array('=USER_ID'=>$userId)
			))->fetch();
			if($resultDb['USER_VALID_DATA_PHONE']!=$profileFormData['abiturientProfilePhone'])
			{
				UserValidDataTable::update($resultDb['USER_VALID_DATA_ID'],array('USER_VALID_DATA_PHONE'=> preg_replace('/[^0-9]/', '', $profileFormData['abiturientProfilePhone'])));
				$user = new CUser;
				$fields = Array(
					"PERSONAL_PHONE"             => $profileFormData['abiturientProfilePhone']
				);
				$user->Update($userId, $fields);
			}
			if($resultDb['USER_VALID_DATA_EMAIL']!=$profileFormData['abiturientProfileEmail'])
			{
				UserValidDataTable::update($resultDb['USER_VALID_DATA_ID'],array('USER_VALID_DATA_EMAIL'=>$profileFormData['abiturientProfileEmail']));
				$user = new CUser;
				$fields = Array(
					"EMAIL"             => $profileFormData['abiturientProfileEmail']
				);
				$user->Update($userId, $fields);
			}
		}

		/*$result = AbiturientProfileTable::add($in);
		if (!$result->isSuccess())
		{
			$mas=implode(' Error:',$result->getErrorMessages());
			var_dump($mas);
		}
		else
		{
			var_dump("Профиль сохранился с id:".$result->getId());
		}*/
	}

	public function updateAbiturientProfile($profileFormData, $profileFiles, $userId)
	{
		$profileFormData['abiturientProfileBirthday']=UserDomain::DateBitrix($profileFormData['abiturientProfileBirthday']);
		$profileFormData['abiturientProfileIdentityDocumentIssuedDate']=UserDomain::DateBitrix($profileFormData['abiturientProfileIdentityDocumentIssuedDate']);
		$profileFormData['abiturientProfileInsuranceDate']=UserDomain::DateBitrix($profileFormData['abiturientProfileInsuranceDate']);
		$profileFormData['abiturientProfileEducationCompletionDate']=UserDomain::DateBitrix($profileFormData['abiturientProfileEducationCompletionDate']);
		$params = array(
			'filter' => array(
				'=USER_ID' => $userId,
			),
			'select' => array(
				'identityDocumentScanFile' => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_SCAN_FILE',
				'INNScanFile' => 'SPO_ABITURIENT_PROFILE_INN_SCAN_FILE',
				'SNILSScanFile' => 'SPO_ABITURIENT_PROFILE_SNILS_SCAN_FILE',
				'educationDocumentScanFile' => 'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_SCAN_FILE',
				'SPO_ABITURIENT_PROFILE_ID'
			)
		);
		$resultDb = AbiturientProfileTable::getList($params)->fetchAll();
		if(empty($profileFormData['abiturientProfileAdditionalLanguage'])) {
			$profileFormData['abiturientProfileAdditionalLanguage'] = 2;
		}
		if(empty($profileFormData['abiturientProfileEducation'])) {
			$profileFormData['abiturientProfileEducation'] = Dictionaries\BaseEducation::BASIC;
		}
		$Fails=UserDomain::FileBitrixZagruz($resultDb[0]);

		$in=array(
			'SPO_ABITURIENT_PROFILE_GENDER' =>$profileFormData['abiturientProfileGender'],
			'SPO_ABITURIENT_PROFILE_BIRTHDAY'=>$profileFormData['abiturientProfileBirthday'],
			'SPO_ABITURIENT_PROFILE_BIRTHPLACE'=>$profileFormData['abiturientProfileBirthplace'],
			'SPO_ABITURIENT_PROFILE_ADDITIONAL_LANGUAGE'=>$profileFormData['abiturientProfileAdditionalLanguage'],
			'SPO_ABITURIENT_PROFILE_CAS'=>$profileFormData['abiturientProfileCAS'],
			'SPO_ABITURIENT_PROFILE_EDUCATION'=>$profileFormData['abiturientProfileEducation'],
			'SPO_ABITURIENT_PROFILE_INN'=>$profileFormData['abiturientProfileINN'],
			'SPO_ABITURIENT_PROFILE_SNILS'=> preg_replace('/[^0-9]/', '', $profileFormData['abiturientProfileSNILS']),
			'SPO_ABITURIENT_PROFILE_NATIONALITY'=>$profileFormData['abiturientProfileNationality'],
			'SPO_ABITURIENT_PROFILE_NATIONALITY_COUNTRY'=>$profileFormData['abiturientProfileNationalityCountry'],

			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_TYPE'=>$profileFormData['abiturientProfileIdentityDocumentType'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_SERIES'=>$profileFormData['abiturientProfileIdentityDocumentSeries'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_NUMBER'=>$profileFormData['abiturientProfileIdentityDocumentNumber'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_BY'=>$profileFormData['abiturientProfileIdentityDocumentIssuedBy'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_DATE'=>$profileFormData['abiturientProfileIdentityDocumentIssuedDate'],
			'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_CODE'=>$profileFormData['abiturientProfileIdentityDocumentIssuedCode'],

			'SPO_ABITURIENT_PROFILE_INSURANCE_COMPANY_NAME'=>$profileFormData['abiturientProfileInsuranceCompanyName'],
			'SPO_ABITURIENT_PROFILE_INSURANCE_NUMBER'=>$profileFormData['abiturientProfileInsuranceNumber'],
			'SPO_ABITURIENT_PROFILE_INSURANCE_SERIES'=>$profileFormData['abiturientProfileInsuranceSeries'],
			'SPO_ABITURIENT_PROFILE_INSURANCE_DATE'=>$profileFormData['abiturientProfileInsuranceDate'],

			'SPO_ABITURIENT_PROFILE_REGISTRATION_ADDRESS'=>$profileFormData['abiturientProfileRegistrationAddress'],

			'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_TYPE'=>$profileFormData['abiturientProfileEducationOrganizationType'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_NUMBER'=>$profileFormData['abiturientProfileEducationOrganizationNumber'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_CITY'=>$profileFormData['abiturientProfileEducationOrganizationCity'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_NAME'=>$profileFormData['abiturientProfileEducationOrganizationName'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_COMPLETION_DATE'=>$profileFormData['abiturientProfileEducationCompletionDate'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_TYPE'=>$profileFormData['abiturientProfileEducationDocumentType'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_NUMBER'=>$profileFormData['abiturientProfileEducationDocumentNumber'],
			'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_SERIES'=>$profileFormData['abiturientProfileEducationDocumentSeries'],

			'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_SERIES'=>$profileFormData['abiturientProfileMilitaryDocumentSeries'],
			'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_NUMBER'=>$profileFormData['abiturientProfileMilitaryDocumentNumber'],
			'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_REGION'=>$profileFormData['abiturientProfileMilitaryDocumentRegion'],
			'SPO_ABITURIENT_PROFILE_IS_RESERVIST'=>$profileFormData['abiturientProfileIsReservist'],
			'SPO_ABITURIENT_PROFILE_ADDITIONAL_DATA'=>$profileFormData['abiturientProfileAdditionalData'],
			'SPO_ABITURIENT_PROFILE_GRADUATED_WITH_HONOURS'=>$profileFormData['abiturientProfileGraduatedWithHonours'],
			'SPO_ABITURIENT_PROFILE_FIRST_TIME_ENROLMENT'=>(int)$profileFormData['abiturientProfileFirstTimeEnrolment'],
			'SPO_ABITURIENT_PROFILE_OLYMPIAD_WINNER'=>$profileFormData['abiturientProfileOlympiadWinner'],
			'SPO_ABITURIENT_PROFILE_SENIORITY'=>$profileFormData['abiturientProfileSeniorityString'],
			'SPO_ABITURIENT_PROFILE_IS_CORRECT'=>1,
			'ADDRESSRESIDENCE'=>$profileFormData['AddressResidence'],
			'SPO_ABITURIENT_PROFILE_OLYMPIAD_STRING'=>$profileFormData['abiturientProfileOlympiadWinnerString'],
            'VALIDITY' => $profileFormData['VALIDITY'],
            'USER_VALID_ID' => $profileFormData['USER_VALID_ID'],
		);
		if(!empty($Fails['identityDocumentScanFile'])) {
			$in['SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_SCAN_FILE'] = $Fails['identityDocumentScanFile'];
		}
		if(!empty($Fails['identityDocumentRegistrationScanFile'])) {
			$in['SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_REGISTRATION_SCAN_FILE'] = $Fails['identityDocumentRegistrationScanFile'];
		}
		if(!empty($Fails['INNScanFile'])) {
			$in['SPO_ABITURIENT_PROFILE_INN_SCAN_FILE']= $Fails['INNScanFile'];
		}
		if(!empty($Fails['SNILSScanFile'])) {
			$in['SPO_ABITURIENT_PROFILE_SNILS_SCAN_FILE']= $Fails['SNILSScanFile'];
		}
		if(!empty($Fails['educationDocumentScanFile'])) {
			$in['SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_SCAN_FILE'] = $Fails['educationDocumentScanFile'];
		}
		$result = AbiturientProfileTable::update($resultDb[0]['SPO_ABITURIENT_PROFILE_ID'],$in);
		/*изменение данных ФИО телефон и мыло | ФИО обязательное поэтому загрузка фото тут */
		if(!empty($profileFormData['abiturientProfileFIO']))
		{
			$FIO=explode(' ',$profileFormData['abiturientProfileFIO']);
			$user = new CUser;
			$fields = Array(
				"NAME"              => $FIO[0],
				"LAST_NAME"         => $FIO[1],
				"SECOND_NAME"       => $FIO[2],
			);

			if(!empty($profileFiles['size']['abiturientProfilePhoto'])){
				$photo = array();
				foreach($profileFiles as $key => $item) {
					if($key != 'error'){
						if (!empty($item['abiturientProfilePhoto'])) {
							$photo[$key] = $item['abiturientProfilePhoto'];
						}
					}
				}
				$fields['PERSONAL_PHOTO'] = $photo;
			}

			$user->Update($userId, $fields);
		}
		if(!empty($profileFormData['abiturientProfilePhone']) || !empty($profileFormData['abiturientProfileEmail']))
		{
			$resultDb = UserValidDataTable::getList(array(
				'filter'=>array('=USER_ID'=>$userId)
			))->fetch();

			if($resultDb['USER_VALID_DATA_PHONE']!=$profileFormData['abiturientProfilePhone'])
			{

				UserValidDataTable::update($resultDb['USER_VALID_DATA_ID'],array('USER_VALID_DATA_PHONE'=> preg_replace('/[^0-9]/', '', $profileFormData['abiturientProfilePhone'])));
				$user = new CUser;
				$fields = Array(
					"PERSONAL_PHONE"             => $profileFormData['abiturientProfilePhone']
				);
				$user->Update($userId, $fields);
			}
			if($resultDb['USER_VALID_DATA_EMAIL']!=$profileFormData['abiturientProfileEmail'])
			{
				UserValidDataTable::update($resultDb['USER_VALID_DATA_ID'],array('USER_VALID_DATA_EMAIL'=>$profileFormData['abiturientProfileEmail']));
				$user = new CUser;
				$fields = Array(
					"EMAIL"             => $profileFormData['abiturientProfileEmail']
				);
				$user->Update($userId, $fields);
			}
		}



		if (!$result->isSuccess())
		{
			$mas=implode(' Error:',$result->getErrorMessages());
			var_dump($mas);
		}
		else
		{
			var_dump("Профиль сохранился с id:".$result->getId());
		}

	}
	public function getUserEmail()
	{
		return $this->entity->getEmail();
	}

    public function getUserEmailConfirmationCode()
    {
        if ($this->entity->getUserValidData() == null)
            return null;
        return $this->entity->getUserValidData()->getUserValidDataEmailConfirmCode();
    }

    public function getUserPhoneConfirmationCode()
    {
        if ($this->entity->getUserValidData() == null)
            return null;

        return $this->entity->getUserValidData()->getUserValidDataPhoneConfirmCode();
    }

    public function getUserPersonalPhone()
    {
        return $this->entity->getPersonalPhone();
    }

	public function getUserFullName()
	{
		return $this->entity->getFullName();
	}

    public function getUserId()
    {
        return $this->entity->getId();
    }

    public function isUserDataConfirmed()
    {
        /*$userValidData = $this->entity->getUserValidData();
        if (empty($userValidData))
            return false;*/
		GLOBAL $USER;
		$params = array(
			'filter' => array(
				'=USER_ID' => $USER->GetID(),
			),
			'select' => array(
				'activ'=>'USER_VALID_DATA_IS_ACTIVE',
			)
		);
		$resultDb = UserValidDataTable::getList($params)->fetchAll();
		$resultDb[0]['activ'];
        //return $userValidData->getStatus();
		return $resultDb[0]['activ'];
    }

    public function getUserPasswordHash()
    {
        return $this->entity->getPasswordHash();
    }

    /**
     * @param $codeType
     * @return string сгенерированный код подтверждения
     * @throws \Spo\Site\Exceptions\ArgumentException
     */
    public function generateNewConfirmationCode($codeType)
    {
        if ($codeType != self::CONFIRMATION_CODE_TYPE_EMAIL && $codeType != self::CONFIRMATION_CODE_TYPE_PHONE)
            throw ArgumentException::argumentIncorrect('codeType');
		GLOBAL $USER;
		$params = array(
			'filter' => array(
				'=USER_ID' => $USER->GetID(),
			),
			'select' => array(
				'USER_VALID_DATA_EMAIL_CONFIRM_CODE',
				'USER_VALID_DATA_ID',
			)
		);
		$resultDb = UserValidDataTable::getList($params)->fetchAll();
		
        //$userValidData = $this->entity->getUserValidData();
        if (count($resultDb)==0) {
			$code = UserDataConfirmationHelper::generateConfirmationCode();
			/*$userValidData = new UserValidData();
			$userValidData->setUser($this->entity);
			$userValidData->setStatus(false);*/
			$result = UserValidDataTable::add(array(
				'USER_VALID_DATA_EMAIL_CONFIRM_CODE' => $code,
				'USER_ID'=>$USER->GetID(),
				'USER_VALID_DATA_IS_ACTIVE'=>0,
			));
        }
		else{
			if (empty($resultDb[0]['USER_VALID_DATA_EMAIL_CONFIRM_CODE'])) {
				$code = UserDataConfirmationHelper::generateConfirmationCode();
				/*$userValidData = new UserValidData();
                $userValidData->setUser($this->entity);
                $userValidData->setStatus(false);*/
				$result = UserValidDataTable::update($resultDb[0]['USER_VALID_DATA_ID'],array(
					'USER_VALID_DATA_EMAIL_CONFIRM_CODE' => $code,
					'USER_ID'=>$USER->GetID(),
					'USER_VALID_DATA_IS_ACTIVE'=>0,
				));
			}
		}
/*
        $code = UserDataConfirmationHelper::generateConfirmationCode();

        if ($codeType == self::CONFIRMATION_CODE_TYPE_EMAIL) {
            $userValidData->setUserValidDataEmailConfirmCode($code);
        } 
*/
		/*elseif ($codeType == self::CONFIRMATION_CODE_TYPE_PHONE) {

            $userPersonalPhone = $this->entity->getPersonalPhone();
            if (empty($userPersonalPhone)) {
                $this->addError('Необходимо указать ваш номер телефона');
                return false;
            }

            /*if ($userValidData->getPhoneConfirmCodeRequestDate() != null) {
                $lastRequestDate = $userValidData->getPhoneConfirmCodeRequestDate();
                $now = new \DateTime();

                if ($now->diff($lastRequestDate)->format('%i') < 1) {
                    $this->addError(
                        'Запрос на получение кода подтверждения по смс можно отправлять не чаще, чем раз в минуту'
                    );
                    return false;
                }
            }*/

		    //$userValidData->setUserValidDataPhoneConfirmCode($code);
            //$userValidData->setPhoneConfirmCodeRequestDate(new \DateTime());
        //}


        //$this->entity->setUserValidData($userValidData);
        //$this->persistEntity($this->entity->getUserValidData());
        return true;
    }

	public function confirmUserEmail($code,$userId,$USER)
    {
		//echo $this->entity->getUserValidData()->getUserValidDataEmailConfirmCode();
		$params = array(
			'filter' => array(
				'=USER_VALID_DATA_EMAIL_CONFIRM_CODE' => $code,
			),
			'select' => array(
				'getUserValidDataEmailConfirmCode' => 'USER_VALID_DATA_EMAIL_CONFIRM_CODE',
				'user_valid_data_id'=>'USER_VALID_DATA_ID',
				'user_id'=>'USER_ID',
			)
		);
		$resultDb = UserValidDataTable::getList($params)->fetchAll();
		$cUser = new CUser;
		$fields = Array(
			"ACTIVE"            => "Y",
		);
		$cUser->Update($resultDb[0]['user_id'], $fields);
		$cUser->Authorize($resultDb[0]['user_id']);
		if (/*$this->entity->getUserValidData()->getUserValidDataEmailConfirmCode()*/$resultDb[0]['getUserValidDataEmailConfirmCode'] != $code) {
			$this->addError('Неправильный код подтверждения', 'ConfirmationForm', 'emailCode');
			return false;
		}

		$date = new \Bitrix\Main\Type\DateTime(date('d.m.Y H:i:s'));
		UserValidDataTable::update($resultDb[0]['user_valid_data_id']
			,array(
				'USER_VALID_DATA_EMAIL'=>$USER->GetEmail(),
				'USER_VALID_DATA_EMAIL_CONFIRM_DATE'=>$date,
				'USER_VALID_DATA_IS_ACTIVE'=>1,
				'USER_VALID_DATA_EMAIL_CONFIRM_CODE'=>$resultDb[0]['getUserValidDataEmailConfirmCode'],
			)
		);
		//header('Location: https://www.profhmao.ru/');
        /*$this->entity->getUserValidData()->setUserValidDataEmail($this->entity->getEmail());
        $this->entity->getUserValidData()->setUserValidDataEmailConfirmDate(new \DateTime());
        $this->persistEntity($this->entity->getUserValidData());*/
    }

    /*public function confirmUserPhone($code)
    {
        if ($this->entity->getUserValidData()->getUserValidDataPhoneConfirmCode() != $code) {
            $this->addError('Неправильный код подтверждения', 'ConfirmationForm', 'phoneCode');
            return;
        }

        $this->entity->getUserValidData()->setUserValidDataPhone($this->entity->getPersonalPhone());
        $this->entity->getUserValidData()->setUserValidDataPhoneConfirmDate(new \DateTime());
        $this->persistEntity($this->entity->getUserValidData());
    }*/

    public function refreshUserValidDataStatus()
    {
        //$currentUserValidData = $this->entity->getUserValidData();
        /*if (empty($currentUserValidData))
            return;

        $status = true;

        if ($this->entity->getEmail() != $currentUserValidData->getUserValidDataEmail()) {
            $currentUserValidData->setUserValidDataEmailConfirmCode(null);
            $status = false;
        }

        if ($this->entity->getPersonalPhone() != $currentUserValidData->getUserValidDataPhone()) {
            $currentUserValidData->setUserValidDataPhoneConfirmCode(null);
            $status = false;
        }

        $currentUserValidData->setStatus($status);
        $this->entity->setUserValidData($currentUserValidData);
        $this->persistEntity($this->entity->getUserValidData());*/
		return;

    }


     // Проверяет, есть ли среди идентификаторов в переданом массиве идентификатор,
     // соответствующий группе "Сотрудник департамента образования"
     //
     // @param CUser $bxUser
     // @return bool

    public static function checkIsEduDepartmentEmployee(CUser $bxUser)
    {
        return self::checkUserBelongsToGroups(self::EDUCATIONAL_DEPARTMENT_EMPLOYEE, $bxUser->GetUserGroupArray());
    }
}
?>