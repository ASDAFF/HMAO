<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

/**
 * @Table(name="spo_qualification")
 * @Entity
 */
class QualificationTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_qualification';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'QUALIFICATION_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'required' => true,
                    'column_name' => 'qualification_id',
                )
            ),
            new Entity\StringField(
                'QUALIFICATION_TITLE',
                array(
                    'required' => false,
                    'column_name' => 'qualification_title',
                    'size' => 255
                )
            ),
        );
    }
}
