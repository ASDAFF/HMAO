<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;
use Spo\Site\Dictionaries\OrganizationStatus;

class AbiturientExamTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_abiturient_exam';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ID_ABITURIENT_EXAM',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'id_abiturient_exam',
                    'unique' => true
                )
            ),
            new Entity\IntegerField(
                'ID_ABITURIENT',
                array(
                    'required' => true,
                    'column_name' => 'id_abiturient',
                )
            ),
            new Entity\IntegerField(
                'ID_ORGANIZATION',
                array(
                    'required' => true,
                    'column_name' => 'id_organization',
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION',
                'Spo\Site\Entities\OrganizationTable',
                array(
                    '=this.ID_ORGANIZATION' => 'ref.ORGANIZATION_ID'
                )
            ),
            new Entity\StringField(
                'ID_ORGANIZATION_SPECIALTY',
                array(
                    'required' => true,
                    'column_name' => 'id_organization_specialty',
                )
            ),
            new Entity\StringField(
                'TEST',
                array(
                    'required' => true,
                    'column_name' => 'test',
                )
            ),
            new Entity\StringField(
                'BALL',
                array(
                    'required' => true,
                    'column_name' => 'ball',
                )
            ),
            new Entity\StringField(
                'FROM_EXEM',
                array(
                    'required' => false,
                    'column_name' => 'from_exem',
                )
            ),
            new Entity\IntegerField(
                'APPEAR',
                array(
                    'required' => false,
                    'column_name' => 'appear',
                )
            ),
            new Entity\DateField(
                'DATE',
                array(
                    'required' => true,
                    'column_name' => 'date',
                )
            ),
        );
    }
}
