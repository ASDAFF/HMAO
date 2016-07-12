<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class BitrixFileTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'b_file';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'required' => true,
                )
            ),
            new Entity\DateField(
                'TIMESTAMP_X',
                array(
                    'required' => true,
                    'default_value' => new Type\Date
                )
            ),
            new Entity\StringField(
                'MODULE_ID',
                array(
                    'size' => 60
                )
            ),
            new Entity\IntegerField('HEIGHT'),
            new Entity\IntegerField('WIDTH'),
            new Entity\IntegerField('FILE_SIZE'),
            new Entity\StringField(
                'CONTENT_TYPE',
                array(
                    'size' => 255,
                    'default_value' => 'IMAGE'
                )
            ),
            new Entity\StringField(
                'SUBDIR',
                array(
                    'size' => 255
                )
            ),
            new Entity\StringField(
                'FILE_NAME',
                array(
                    'required' => true,
                    'size' => 255
                )
            ),
            new Entity\StringField(
                'ORIGINAL_NAME',
                array(
                    'size' => 255
                )
            ),
            new Entity\StringField(
                'DESCRIPTION',
                array(
                    'size' => 255
                )
            ),
            new Entity\StringField(
                'HANDLER_ID',
                array(
                    'size' => 50
                )
            ),
            new Entity\StringField(
                'EXTERNAL_ID',
                array(
                    'size' => 50
                )
            )
        );
    }
}
