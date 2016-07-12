<?php
namespace Spo\Site\Adapters;

use Spo\Site\Domains\UserDomain;
use Spo\Site\Doctrine\Entities\BitrixUser;
use Spo\Site\Doctrine\Entities\AbiturientProfile;
use Spo\Site\Util\CVarDumper;
use CFile;
use Spo\Site\Entities\AbiturientProfileTable;
use Spo\Site\Entities\ParentTable;
use Spo\Site\Entities\UserValidDataTable;
use Spo\Site\Entities\OrganizationEmployeeTable;

class UserDomainAdapter
{
	public static function getAbiturientProfile(/*UserDomain $domain,*/$userId)
	{
		$params = array(
			'filter' => array(
				'=USER_ID' => $userId,
			),
			'select' => array(
				'abiturientProfileGender' => 'SPO_ABITURIENT_PROFILE_GENDER',
				'abiturientProfileBirthday' => 'SPO_ABITURIENT_PROFILE_BIRTHDAY',
				'abiturientProfileBirthplace' => 'SPO_ABITURIENT_PROFILE_BIRTHPLACE',
				'abiturientProfileAdditionalLanguage' => 'SPO_ABITURIENT_PROFILE_ADDITIONAL_LANGUAGE',
				'abiturientProfileCAS' => 'SPO_ABITURIENT_PROFILE_CAS',
				'abiturientProfileEducation' => 'SPO_ABITURIENT_PROFILE_EDUCATION',
				'abiturientProfileINN' => 'SPO_ABITURIENT_PROFILE_INN',
				'abiturientProfileSNILS' => 'SPO_ABITURIENT_PROFILE_SNILS',
				'abiturientProfileNationality' => 'SPO_ABITURIENT_PROFILE_NATIONALITY',
				'abiturientProfileNationalityCountry' => 'SPO_ABITURIENT_PROFILE_NATIONALITY_COUNTRY',

				'abiturientProfileIdentityDocumentType' => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_TYPE',
				'abiturientProfileIdentityDocumentSeries' => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_SERIES',
				'abiturientProfileIdentityDocumentNumber' => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_NUMBER',
				'abiturientProfileIdentityDocumentIssuedBy' => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_BY',
				'abiturientProfileIdentityDocumentIssuedDate' => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_DATE',
				'abiturientProfileIdentityDocumentIssuedCode' => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_CODE',

				'abiturientProfileInsuranceCompanyName' => 'SPO_ABITURIENT_PROFILE_INSURANCE_COMPANY_NAME',
				'abiturientProfileInsuranceNumber' => 'SPO_ABITURIENT_PROFILE_INSURANCE_NUMBER',
				'abiturientProfileInsuranceSeries' => 'SPO_ABITURIENT_PROFILE_INSURANCE_SERIES',
				'abiturientProfileInsuranceDate' => 'SPO_ABITURIENT_PROFILE_INSURANCE_DATE',

				'abiturientProfileRegistrationAddress' => 'SPO_ABITURIENT_PROFILE_REGISTRATION_ADDRESS',

				'abiturientProfileEducationOrganizationType' => 'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_TYPE',
				'abiturientProfileEducationOrganizationNumber' => 'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_NUMBER',
				'abiturientProfileEducationOrganizationCity' => 'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_CITY',
				'abiturientProfileEducationOrganizationName' => 'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_NAME',
				'abiturientProfileEducationCompletionDate' => 'SPO_ABITURIENT_PROFILE_EDUCATION_COMPLETION_DATE',
				'abiturientProfileEducationDocumentType' => 'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_TYPE',
				'abiturientProfileEducationDocumentNumber' => 'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_NUMBER',
				'abiturientProfileEducationDocumentSeries' => 'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_SERIES',

				'abiturientProfileMilitaryDocumentSeries' => 'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_SERIES',
				'abiturientProfileMilitaryDocumentNumber' => 'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_NUMBER',
				'abiturientProfileMilitaryDocumentRegion' => 'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_REGION',
				'abiturientProfileIsReservist' => 'SPO_ABITURIENT_PROFILE_IS_RESERVIST',
				'abiturientProfileAdditionalData' => 'SPO_ABITURIENT_PROFILE_ADDITIONAL_DATA',
				'abiturientProfileGraduatedWithHonours' => 'SPO_ABITURIENT_PROFILE_GRADUATED_WITH_HONOURS',
				'abiturientProfileFirstTimeEnrolment' => 'SPO_ABITURIENT_PROFILE_FIRST_TIME_ENROLMENT',
				'abiturientProfileOlympiadWinner' => 'SPO_ABITURIENT_PROFILE_OLYMPIAD_WINNER',
				'abiturientProfileOlympiadWinnerString'=> 'SPO_ABITURIENT_PROFILE_OLYMPIAD_STRING',
				'abiturientProfileSeniority' => 'SPO_ABITURIENT_PROFILE_SENIORITY',
				'AddressResidence' => 'ADDRESSRESIDENCE',
				'abiturientProfileIdentityDocumentScanFileLink' => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_SCAN_FILE',
				'abiturientProfileIdentityDocumentRegistrationScanFileLink' => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_REGISTRATION_SCAN_FILE',
				'abiturientProfileINNScanFile' => 'SPO_ABITURIENT_PROFILE_INN_SCAN_FILE',
				'abiturientProfileSNILSScanFile' => 'SPO_ABITURIENT_PROFILE_SNILS_SCAN_FILE',
				'abiturientProfileEducationDocumentScanFile' => 'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_SCAN_FILE',
				'UserVer' => 'USER_VALID_ID',
				'userId'	=>	'USER_ID'
			)
		);
		$resultDb = AbiturientProfileTable::getList($params)->fetch();
		
		if (!$resultDb)
			$resultDb=array();		

		/*вывод ФИО ТЕЛЕФОН email*/
		$resUserDate=UserValidDataTable::getList(array(
			'filter' 	=>	array(
				'=USER_ID' => $userId,
			),
			'select'	=>	array(
				'abiturientProfileEmail'		=>	'USER_VALID_DATA_EMAIL',
				'abiturientProfileFIO'			=> 	'FIO',
				'abiturientProfilePhone'		=>	'USER_VALID_DATA_PHONE',
			)
			))->fetch();
		//if($resultDb)
		$resultDb=array_merge($resultDb,$resUserDate);
		/*вывод сведеньей что пользователь проверен*/
		//var_dump($resultDb);
		if($resultDb['UserVer'])
		{
			$UserVer=OrganizationEmployeeTable::getList(array(
				'filter'=>array('=ORGANIZATION_ID'=>$resultDb['UserVer']),
				'select'=>array('NameOrgVer'=>'ORGANIZATION.ORGANIZATION_NAME')
			))->fetch();
			$resultDb['NameOrgVer']=$UserVer['NameOrgVer'];
		}
		//var_dump($resultDb);
		/*получение данных о родителях*/
		$resultParent=ParentTable::getList(array(
			'filter'	=>	array('=ID_ABITURIENT'=>$userId),
			'select'	=>  array(
				'id'			=>	'ID_PARENT',
				'fio'			=>  'FIO',
				'typeparent'	=> 	'TYPEPARENT',
				'birthdate'		=>	'BIRTHDATE',
				'snils'			=>  'SNILS',
				'citizenship'	=>	'CITIZENSHIP',
				'doctypepers'	=>	'DOCTYPEPERS',
				'docserpers'	=> 	'DOCSERPERS',
				'docnumpers'	=>	'DOCNUMPERS',
				'docissuedpers' =>  'DOCISSUEDPERS',
				'docdatepers'	=>	'DOCDATEPERS',
				'dobdocument'	=>	'DOBDOCUMENT',
				'phone'			=>	'PHONE',
			)
		));
		$parent=array();
		while ($item = $resultParent->fetch())
		{
			unset($item['BIRTHDATE']);
			unset($item['DOCDATEPERS']);
			$parent[$item['typeparent']]=$item;
		}
		$resultDb['abiturientProfileBirthday']= mb_split('-',mb_split(" ", $resultDb['abiturientProfileBirthday'])[0]);
		$resultDb['abiturientProfileBirthday']=$resultDb['abiturientProfileBirthday'][2].'-'.$resultDb['abiturientProfileBirthday'][1].'-'.$resultDb['abiturientProfileBirthday'][0];
		$resultDb['abiturientProfileIdentityDocumentIssuedDate']= mb_split('-',mb_split(" ", $resultDb['abiturientProfileIdentityDocumentIssuedDate'])[0]);
		$resultDb['abiturientProfileIdentityDocumentIssuedDate']=$resultDb['abiturientProfileIdentityDocumentIssuedDate'][2].'-'.$resultDb['abiturientProfileIdentityDocumentIssuedDate'][1].'-'.$resultDb['abiturientProfileIdentityDocumentIssuedDate'][0];
		$resultDb['abiturientProfileInsuranceDate']= mb_split('-',mb_split(" ", $resultDb['abiturientProfileInsuranceDate'])[0]);
		$resultDb['abiturientProfileInsuranceDate']=$resultDb['abiturientProfileInsuranceDate'][2].'-'.$resultDb['abiturientProfileInsuranceDate'][1].'-'.$resultDb['abiturientProfileInsuranceDate'][0];
		$resultDb['abiturientProfileEducationCompletionDate']= mb_split('-',mb_split(" ", $resultDb['abiturientProfileEducationCompletionDate'])[0]);
		$resultDb['abiturientProfileEducationCompletionDate']=$resultDb['abiturientProfileEducationCompletionDate'][2].'-'.$resultDb['abiturientProfileEducationCompletionDate'][1].'-'.$resultDb['abiturientProfileEducationCompletionDate'][0];
		$resultDb['abiturientProfileIdentityDocumentScanFileLink']=CFile::GetPath($resultDb['abiturientProfileIdentityDocumentScanFileLink']);
		$resultDb['abiturientProfileIdentityDocumentRegistrationScanFileLink']=CFile::GetPath($resultDb['abiturientProfileIdentityDocumentRegistrationScanFileLink']);
		$resultDb['abiturientProfileINNScanFile']=CFile::GetPath($resultDb['abiturientProfileINNScanFile']);
		$resultDb['abiturientProfileSNILSScanFile']=CFile::GetPath($resultDb['abiturientProfileSNILSScanFile']);
		$resultDb['abiturientProfileEducationDocumentScanFile']=CFile::GetPath($resultDb['abiturientProfileEducationDocumentScanFile']);
		$resultDb['abiturientProfileParent']=$parent;
		unset($resultDb['ABITURIENTPROFILEBIRTHDAY']);
		unset($resultDb['ABITURIENTPROFILEIDENTITYDOCUMENTISSUEDDATE']);
		unset($resultDb['ABITURIENTPROFILEINSURANCEDATE']);
		unset($resultDb['ABITURIENTPROFILEEDUCATIONCOMPLETIONDATE']);
		$data=$resultDb;
		//var_dump($resultDb);
		return $data;
	}
	
    public static function isUserDataValid(/*UserDomain $domain,*/$userId,$Phone,$Email)
    {
        /** @var BitrixUser $userEntity */
        //$userEntity = $domain->getModel();
        //$userValidDataEntity = $userEntity->getUserValidData();
		$params = array(
			'filter' => array(
				'=USER_ID' => $userId,
			),
			'select' => array(
				'getUserValidDataEmail' => 'USER_VALID_DATA_EMAIL',
				'getUserValidDataPhone' => 'USER_VALID_DATA_PHONE',
				'getStatus' => 'USER_VALID_DATA_IS_ACTIVE',
			)
		);
		$resultDb = UserValidDataTable::getList($params)->fetchAll();
        $emailIsConfirmed = true;
        $phoneIsConfirmed = true;
        $dataIsConfirmed = true;
        if (count($resultDb) === 0) {
            $emailIsConfirmed = false;
            $phoneIsConfirmed = false;
            $dataIsConfirmed = false;
        } else{
			if ($resultDb[0]['getUserValidDataEmail'] != $Email)
				$emailIsConfirmed = false;

			if ($resultDb[0]['getUserValidDataPhone']  != $Phone)
				$phoneIsConfirmed = false;

			if (!$resultDb[0]['getStatus'])
				$dataIsConfirmed = false;

            /*if ($userValidDataEntity->getUserValidDataEmail() != $userEntity->getEmail())
                $emailIsConfirmed = false;

            if ($userValidDataEntity->getUserValidDataPhone() != $userEntity->getPersonalPhone())
                $phoneIsConfirmed = false;

            if (!$userValidDataEntity->getStatus())
                $dataIsConfirmed = false;*/
        }

        return array(
            'dataIsConfirmed' => $dataIsConfirmed,
            'emailIsConfirmed' => $emailIsConfirmed,
            'phoneIsConfirmed' => $phoneIsConfirmed,
        );


    }
}