<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

/**
 * @Entity(repositoryClass="Spo\Site\Doctrine\Repositories\SpecialtyRepository")
 * @Table(name="spo_specialty")
 *
 * Class Specialty - специальности подготовки
 *
 */
class SpecialtyTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_specialty';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'SPECIALTY_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'specialty_id',
                )
            ),
            new Entity\IntegerField(
                'SPECIALTY_GROUP_ID',
                array(
                    'required' => true,
                    'column_name' => 'specialty_group_id',
                )
            ),
            new Entity\ReferenceField(
                'GROUP',
                'Spo\Site\Entities\SpecialtyGroupTable',
                array(
                    '=this.SPECIALTY_GROUP_ID' => 'ref.SPECIALTY_GROUP_ID'
                )
            ),
            new Entity\StringField(
                'SPECIALTY_TITLE',
                array(
                    'size' => 255,
                    'required' => true,
                    'column_name' => 'specialty_title',
                )
            ),
            new Entity\StringField(
                'SPECIALTY_CODE',
                array(
                    'size' => 255,
                    'required' => true,
                    'unique' => true,
                    'column_name' => 'specialty_code',
                )
            ),
            new Entity\StringField(
                'SPECIALTY_DESCRIPTION',
                array(
                    'size' => 10000,
                    'required' => true,
                    'column_name' => 'specialty_description',
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION_SPECIALTY',
                'Spo\Site\Entities\OrganizationSpecialtyTable',
                array(
                    '=this.SPECIALTY_ID' => 'ref.SPECIALTY_ID'
                )
            ),
            new Entity\ReferenceField(
                'QUALIFICATIONS',
                'Spo\Site\Entities\Qualification2SpecialtyTable',
                array(
                    '=this.SPECIALTY_ID' => 'ref.SPECIALITY_ID'
                )
            ),
        );
    }
}
