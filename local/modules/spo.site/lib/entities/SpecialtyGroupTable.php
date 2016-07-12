<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class SpecialtyGroupTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'spo_specialty_group';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField(
				'SPECIALTY_GROUP_ID',
				array(
					'primary' => true,
					'autocomplete' => true,
					'required' => true,
					'column_name' => 'specialty_group_id',
				)
			),
			new Entity\StringField(
				'SPECIALTY_GROUP_TITLE',
				array(
					'required' => true,
					'size' => 255,
					'column_name' => 'specialty_group_title',
				)
			),
			new Entity\StringField(
				'SPECIALTY_GROUP_CODE',
				array(
					'required' => true,
					'size' => 10,
					'column_name' => 'specialty_group_code',
				)
			),
			new Entity\ReferenceField(
				'SPECIALITY',
				'Spo\Site\Entities\SpecialtyTable',
				array(
					'=this.SPECIALTY_GROUP_ID' => 'ref.SPECIALTY_GROUP_ID'
				)
			),
		);
	}
}