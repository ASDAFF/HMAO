<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class Qualification2SpecialtyTable extends Entity\DataManager
{

    public static function getTableName()
    {
        return 'spo_qualification2specialty';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'SPECIALITY_ID',
                array(
                    'primary' => true,
                    'column_name' => 'specialty_id',
                )
            ),
            new Entity\ReferenceField(
                'SPECIALITY',
                'Spo\Site\Entities\SpecialtyTable',
                array(
                    '=this.SPECIALITY_ID' => 'ref.ID'
                )
            ),
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
            )
        );
    }
}
