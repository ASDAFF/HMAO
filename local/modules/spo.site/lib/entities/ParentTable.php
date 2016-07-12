<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;
use Spo\Site\Dictionaries\OrganizationStatus;

class ParentTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_parent';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ID_PARENT',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'id_parent',
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
            new Entity\ReferenceField(
                'ABITURIENT',
                'Spo\Site\Entities\AbiturientProfileTable',
                array(
                    '=this.ID_ABITURIENT' => 'ref.SPO_ABITURIENT_PROFILE_ID'
                )
            ),
            new Entity\IntegerField(
                'TYPEPARENT',
                array(
                    'required' => true,
                    'column_name' => 'typeparent',
                )
            ),
            new Entity\TextField(
                'FIO',
                array(
                    'required' => true,
                    'column_name' => 'fio',
                )
            ),
            new Entity\DateField(
                'BIRTHDATE',
                array(
                    'required' => true,
                    'column_name' => 'birthdate',
                )
            ),
            new Entity\StringField(
                'SNILS',
                array(
                    'required' => false,
                    'column_name' => 'snils',
                )
            ),
            new Entity\StringField(
                'PHONE',
                array(
                    'required' => false,
                    'column_name' => 'phone',
                )
            ),
            new Entity\StringField(
                'CITIZENSHIP',
                array(
                    'required' => false,
                    'column_name' => 'citizenship',
                )
            ),
            new Entity\IntegerField(
                'DOCTYPEPERS',
                array(
                    'required' => false,
                    'column_name' => 'doctypepers',
                    'default_value' => 1,
                )
            ),
            new Entity\IntegerField(
                'DOCSERPERS',
                array(
                    'required' => true,
                    'column_name' => 'docserpers',
                )
            ),
            new Entity\IntegerField(
                'DOCNUMPERS',
                array(
                    'required'      => true,
                    'column_name' => 'docnumpers',
                    'default_value' => 0
                )
            ),
            new Entity\TextField(
                'DOCISSUEDPERS',
                array(
                    'required' => true,
                    'column_name' => 'docissuedpers',
                )
            ),
            new Entity\DateField(
                'DOCDATEPERS',
                array(
                    'required' => false,
                    'column_name' => 'docdatepers',
                )
            ),
            new Entity\TextField(
                'DOBDOCUMENT',
                array(
                    'required' => false,
                    'column_name' => 'dobdocument',
                )
            ),
        );
    }
}
