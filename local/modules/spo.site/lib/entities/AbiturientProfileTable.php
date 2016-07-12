<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;
use Spo\Site\Dictionaries;

class AbiturientProfileTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'spo_abiturient_profile';
	}
	/*public function findByINN($INN)
	{
		$this->queryBuilder = $this->createQueryBuilder('AbiturientProfile')
			->select('AbiturientProfile')
			->andWhere('AbiturientProfile.abiturientProfileINN = :INN')
			->setParameter('INN', $INN);

		return $this;
	}*/
	public static function findByINN($INN)
	{
		$ArrayRezult=AbiturientProfileTable::getList(array(
			'filter' => array('INN' => $INN),
			'select' => array('*')
		))->fetchAll();
		return $ArrayRezult;
	}
	/*public function findBySNILS($SNILS)
	{
		$this->queryBuilder = $this->createQueryBuilder('AbiturientProfile')
			->select('AbiturientProfile')
			->andWhere('AbiturientProfile.abiturientProfileSNILS = :SNILS')
			->setParameter('SNILS', $SNILS);

		return $this;
	}*/
	public static function findBySNILS()
	{
		$ArrayRezult=AbiturientProfileTable::getList(array(
			'filter' => array('SNILS' => $SNILS),
			'select' => array('*')
		))->fetchAll();
		return $ArrayRezult;
	}
	/*public function exceptUserId($exceptUserId)
	{
		$this->queryBuilder->andWhere('AbiturientProfile.userId != :userId')->setParameter('userId', $exceptUserId);
		return $this;
	}*/
	public static function exceptUserId($exceptUserId)
	{
		$ArrayRezult=AbiturientProfileTable::getList(array(
			'filter' => array('!=USER_ID' => $exceptUserId),
			'select' => array('*')
		))->fetchAll();
		return $ArrayRezult;
	}

	public static function UserIds($UserId)
	{
		$ArrayRezult=AbiturientProfileTable::getList(array(
			'filter' => array('=USER_ID' => $UserId),
			'select' => array('*')
		))->fetch();
		return $ArrayRezult;
	}

	public static function getMap()
	{
		return array(
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_ID',
				array(
					'primary' => true,
					'autocomplete' => true,
					'column_name' => 'spo_abiturient_profile_id'
				)
			),
			new	Entity\IntegerField(
				'USER_ID',
				array(
					'required' => true,
					'column_name' => 'user_id'
				)
			),
			
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_GENDER',
				array(
					//'default_value' => Dictionaries\Gender::MALE,
					'column_name' => 'spo_abiturient_profile_gender'
				)
			),
			new	Entity\DateTimeField(
				'SPO_ABITURIENT_PROFILE_BIRTHDAY',
				array(
					'column_name' => 'spo_abiturient_profile_birthday'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_BIRTHPLACE',
				array(
					'size' => 1000,

					'column_name' => 'spo_abiturient_profile_birthplace'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_INN',
				array(

					'column_name' => 'spo_abiturient_profile_inn'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_SNILS',
				array(
					'column_name' => 'spo_abiturient_profile_snils'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_CAS',
				array(
					'column_name' => 'spo_abiturient_profile_cas'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_EDUCATION',
				array(
					'default_value' => Dictionaries\BaseEducation::BASIC,

					'column_name' => 'spo_abiturient_profile_education'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_ADDITIONAL_LANGUAGE',
				array(
					'default_value' => Dictionaries\AdditionalLanguage::NONE,

					'column_name' => 'spo_abiturient_profile_additional_language'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_NATIONALITY',
				array(
					'column_name' => 'spo_abiturient_profile_nationality'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_NATIONALITY_COUNTRY',
				array(
					'column_name' => 'spo_abiturient_profile_nationality_country'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_IS_CORRECT',
				array(
					'column_name' => 'spo_abiturient_profile_is_correct'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_TYPE',
				array(
					'column_name' => 'spo_abiturient_profile_identity_document_type'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_SERIES',
				array(
					'column_name' => 'spo_abiturient_profile_identity_document_series'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_NUMBER',
				array(
					'column_name' => 'spo_abiturient_profile_identity_document_number'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_BY',
				array(
					'column_name' => 'spo_abiturient_profile_identity_document_issued_by'
				)
			),
			new	Entity\DateTimeField(
				'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_DATE',
				array(
					'column_name' => 'spo_abiturient_profile_identity_document_issued_date'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_CODE',
				array(
					'column_name' => 'spo_abiturient_profile_identity_document_issued_code'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_INSURANCE_COMPANY_NAME',
				array(
					'column_name' => 'spo_abiturient_profile_insurance_company_name'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_INSURANCE_NUMBER',
				array(
					'column_name' => 'spo_abiturient_profile_insurance_number'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_INSURANCE_SERIES',
				array(
					'column_name' => 'spo_abiturient_profile_insurance_series'
				)
			),
			new	Entity\DateTimeField(
				'SPO_ABITURIENT_PROFILE_INSURANCE_DATE',
				array(
					'column_name' => 'spo_abiturient_profile_insurance_date'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_REGISTRATION_ADDRESS',
				array(
					'column_name' => 'spo_abiturient_profile_registration_address'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_TYPE',
				array(
					'column_name' => 'spo_abiturient_profile_education_organization_type'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_NUMBER',
				array(
					'column_name' => 'spo_abiturient_profile_education_organization_number'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_CITY',
				array(
					'column_name' => 'spo_abiturient_profile_education_organization_city'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_EDUCATION_ORGANIZATION_NAME',
				array(
					'column_name' => 'spo_abiturient_profile_education_organization_name'
				)
			),
			new	Entity\DateTimeField(
				'SPO_ABITURIENT_PROFILE_EDUCATION_COMPLETION_DATE',
				array(
					'column_name' => 'spo_abiturient_profile_education_completion_date'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_TYPE',
				array(
					'column_name' => 'spo_abiturient_profile_education_document_type'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_NUMBER',
				array(
					'column_name' => 'spo_abiturient_profile_education_document_number'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_SERIES',
				array(
					'column_name' => 'spo_abiturient_profile_education_document_series'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_IS_RESERVIST',
				array(
					'column_name' => 'spo_abiturient_profile_is_reservist'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_SERIES',
				array(
					'column_name' => 'spo_abiturient_profile_military_document_series'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_NUMBER',
				array(
					'column_name' => 'spo_abiturient_profile_military_document_number'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_REGION',
				array(
					'column_name' => 'spo_abiturient_profile_military_document_region'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_ADDITIONAL_DATA',
				array(
					'column_name' => 'spo_abiturient_profile_additional_data'
				)
			),
			new	Entity\IntegerField(
				'USER_ID',
				array(
					'required' => true,
					'column_name' => 'user_id'
				)
			),
			new Entity\ReferenceField(
				'USER',
				'Bitrix\Main\UserTable',
				array(
					'=this.USER_ID' => 'ref.ID'
				)
			),
			'FIO' => new Entity\ExpressionField(
				'FIO',
				'CONCAT_WS(" ", %s, %s, %s)',
				array('USER.LAST_NAME', 'USER.NAME', 'USER.SECOND_NAME')
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_SCAN_FILE',
				array(
					'column_name' => 'spo_abiturient_profile_identity_document_scan_file'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_REGISTRATION_SCAN_FILE',
				array(
					'column_name' => 'spo_abiturient_profile_identity_document_registration_scan_file'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_SCAN_FILE',
				array(
					'column_name' => 'spo_abiturient_profile_education_document_scan_file'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_INN_SCAN_FILE',
				array(
					'column_name' => 'spo_abiturient_profile_INN_scan_file'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_SNILS_SCAN_FILE',
				array(
					'column_name' => 'spo_abiturient_profile_SNILS_scan_file'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_GRADUATED_WITH_HONOURS',
				array(
					'column_name' => 'spo_abiturient_profile_graduated_with_honours'
				)
			),
			new	Entity\IntegerField(
				'SPO_ABITURIENT_PROFILE_FIRST_TIME_ENROLMENT',
				array(
					'column_name' => 'spo_abiturient_profile_first_time_enrolment'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_OLYMPIAD_WINNER',
				array(
					'column_name' => 'spo_abiturient_profile_olympiad_winner'
				)
			),
			new Entity\TextField(
				'SPO_ABITURIENT_PROFILE_OLYMPIAD_STRING',
				array(
					'column_name' => 'spo_abiturient_profile_olympiad_string'
				)
			),
			new	Entity\StringField(
				'SPO_ABITURIENT_PROFILE_SENIORITY',
				array(
					'column_name' => 'spo_abiturient_profile_seniority'
				)
			),
			new	Entity\IntegerField(
				'TYPERESIDENCE',
				array(
					'column_name' => 'typeresidence'
				)
			),
			new	Entity\IntegerField(
				'RECEIVEDEDUCATION',
				array(
					'column_name' => 'receivededucation'
				)
			),
			new	Entity\TextField(
				'ADDRESSRESIDENCE',
				array(
					'column_name' => 'addressresidence'
				)
			),
			new	Entity\IntegerField(
				'VALIDITY',
				array(
					'column_name' => 'validity'
				)
			),
			new	Entity\IntegerField(
				'USER_VALID_ID',
				array(
					'column_name' => 'user_valid_id'
				)
			),
			new Entity\IntegerField(
				'DOCORIGIN',
				array(
					'column_name' => 'docorigin'
				)
			),
		);
	}
}
