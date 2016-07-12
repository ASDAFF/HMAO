<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class CityTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_city';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'CITY_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'required' => true,
                    'column_name' => 'city_id'
                )
            ),
            new Entity\StringField(
                'CITY_NAME',
                array(
                    'size' => 255,
                    'required' => true,
                    'column_name' => 'city_name'
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
                    '=this.REGION_ID' => 'ref.ID'
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION',
                'Spo\Site\Entities\OrganizationTable',
                array(
                    '=this.ID' => 'ref.CITY_ID'
                )
            )
        );
    }
}
