<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class OrganizationSpecialtyExamTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_organization_specialty_exam';
    }

    public static function exem($idOrg)
    {
        return OrganizationSpecialtyExamTable::getlist(array(
            'filter'        =>  array(
                '=ORGANIZATION_SPECIALTY_ID'  =>$idOrg
            ),
            'select'        =>  array(
                '*'
            )
        ))->fetch();
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_EXAM_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'organization_specialty_exam_id'
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
                'ORGANIZATION_SPECIALTY_EXAM_DISCIPLINE',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_exam_discipline'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_EXAM_TYPE',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_exam_type'
                )
            ),
            new Entity\TextField(
                'ADRES',
                array(
                    //'required' => true,
                    'column_name' => 'adres'
                )
            ),
            new Entity\DateField(
                'DATE',
                array(
                    //'required' => true,
                    'column_name' => 'date'
                )
            ),
        );
    }
}
