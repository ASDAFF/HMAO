<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class RegionTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'spo_region';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField(
				'REGION_ID',
				array(
					'primary' => true,
					'autocomplete' => true,
					'required' => true,
					'column_name' => 'region_id'
				)
			),
			new Entity\StringField(
				'REGION_NAME',
				array(
					'size' => 255,
					'required' => true,
					'column_name' => 'region_name'
				)
			),
			new Entity\ReferenceField(
				'REGION_AREA',
				'Spo\Site\Entities\RegionAreaTable',
				array(
					'=this.REGION_ID' => 'ref.REGION_ID'
				)
			)
		);
	}
}
