<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class RegionAreaTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'spo_region_area';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField(
				'REGION_AREA_ID',
				array(
					'primary' => true,
					'autocomplete' => true,
					'required' => true,
					'column_name' => 'region_area_id'
				)
			),
			new Entity\StringField(
				'REGION_AREA_NAME',
				array(
					'size' => 255,
					'required' => true,
					'column_name' => 'region_area_name'
				)
			),
			new Entity\IntegerField(
				'REGION_ID',
				array(
					'column_name' => 'region_id'
				)
			),
			new Entity\ReferenceField(
				'REGION',
				'Spo\Site\Entities\RegionTable',
				array(
					'=this.REGION_ID' => 'ref.REGION_ID'
				)
			),
			new Entity\ReferenceField(
				'ORGANIZATION',
				'Spo\Site\Entities\OrganizationTable',
				array(
					'=this.REGION_AREA_ID' => 'ref.REGION_AREA_ID'
				)
			)
		);
	}
}
