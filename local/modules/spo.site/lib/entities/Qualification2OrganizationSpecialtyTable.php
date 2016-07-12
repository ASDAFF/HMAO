<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class Qualification2OrganizationSpecialtyTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_qualification2organization_specialty';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'QUALIFICATION_ID',
                array(
                    'primary' => true,
                    'column_name' => 'qualification_id',
                )
            ),
            new Entity\ReferenceField(
                'QUALIFICATION',
                'Spo\Site\Entities\QualificationTable',
                array(
                    '=this.QUALIFICATION_ID' => 'ref.QUALIFICATION_ID'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_ID',
                array(
                    'primary' => true,
                    'column_name' => 'organization_specialty_id',
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION_SPECIALTY',
                'Spo\Site\Entities\OrganizationSpecialtyTable',
                array(
                    '=this.ORGANIZATION_SPECIALTY_ID' => 'ref.ORGANIZATION_SPECIALTY_ID'
                )
            ),
        );
    }
}
