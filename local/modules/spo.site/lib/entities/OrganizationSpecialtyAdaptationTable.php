<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

/**
 * @Table(name="spo_organization_specialty_adaptation")
 * @Entity
 */
class OrganizationSpecialtyAdaptationTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_organization_specialty_adaptation';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_ADAPTATION_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'organization_specialty_adaptation_id'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_ID',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_id'
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION_SPECIALTY',
                'Spo\Site\Entities\OrganizationSpecialtyTable',
                array(
                    '=this.ORGANIZATION_SPECIALTY_ID' => 'ref.ORGANIZATION_SPECIALTY_ID'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_ADAPTATION_TYPE',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_adaptation_type'
                )
            )
        );
    }
}
