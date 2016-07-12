<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;
use Spo\Site\Dictionaries\OrganizationStatus;

class OrganizationTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_organization';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ORGANIZATION_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'organization_id',
                    'unique' => true
                )
            ),
            new Entity\StringField(
                'ORGANIZATION_NAME',
                array(
                    'required' => false,
                    'column_name' => 'organization_name',
                    'size' => 255
                )
            ),
            new Entity\StringField(
                'ORGANIZATION_FULL_NAME',
                array(
                    'required' => false,
                    'column_name' => 'organization_full_name',
                    'size' => 100
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_FOUNDATION_YEAR',
                array(
                    'required' => false,
                    'column_name' => 'organization_foundation_year',
                )
            ),
            new Entity\StringField(
                'ORGANIZATION_ADDRESS',
                array(
                    'required' => false,
                    'column_name' => 'organization_address',
                    'size' => 1000
                )
            ),
            new Entity\StringField(
                'ORGANIZATION_EMAIL',
                array(
                    'required' => false,
                    'column_name' => 'organization_email',
                    'size' => 255
                )
            ),
            new Entity\StringField(
                'ORGANIZATION_PHONE',
                array(
                    'required' => false,
                    'column_name' => 'organization_phone',
                    'size' => 255
                )
            ),
            new Entity\StringField(
                'ORGANIZATION_SITE',
                array(
                    'required' => false,
                    'column_name' => 'organization_site',
                    'size' => 255
                )
            ),
            new Entity\FloatField(
                'ORGANIZATION_COORDINATE_X',
                array(
                    'column_name' => 'organization_coordinate_x',
                    'default_value' => 0
                )
            ),
            new Entity\FloatField(
                'ORGANIZATION_COORDINATE_Y',
                array(
                    'column_name' => 'organization_coordinate_y',
                    'default_value' => 0
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_STATUS',
                array(
                    'column_name' => 'organization_status',
                    'default_value' => OrganizationStatus::DISABLED
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_HOSTEL',
                array(
                    'column_name' => 'organization_hostel'
                )
            ),
            new Entity\IntegerField(
                'REGION_AREA_ID',
                array(
                    'required' => false,
                    'column_name' => 'region_area_id',
                )
            ),
            new Entity\IntegerField(
                'INN',
                array(                    
                    'column_name' => 'INN',
                )
            ),
            new Entity\ReferenceField(
                'REGION_AREA',
                'Spo\Site\Entities\RegionAreaTable',
                array(
                    '=this.REGION_AREA_ID' => 'ref.REGION_AREA_ID'
                )
            ),
            new Entity\IntegerField(
                'CITY_ID',
                array(
                    'required' => false,
                    'column_name' => 'city_id',
                )
            ),
            new Entity\ReferenceField(
                'CITY',
                'Spo\Site\Entities\CityTable',
                array(
                    '=this.CITY_ID' => 'ref.CITY_ID'
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION_SPECIALTY',
                'Spo\Site\Entities\OrganizationSpecialtyTable',
                array(
                    '=this.ORGANIZATION_ID' => 'ref.ORGANIZATION_ID'
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION_EMPLOYEE',
                'Spo\Site\Entities\OrganizationEmployeeTable',
                array(
                    '=this.ORGANIZATION_ID' => 'ref.ORGANIZATION_ID'
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION_PAGE',
                'Spo\Site\Entities\OrganizationPageTable',
                array(
                    '=this.ORGANIZATION_ID' => 'ref.ORGANIZATION_ID'
                )
            ),

        );
    }
}
