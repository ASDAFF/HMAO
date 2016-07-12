<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class UserValidDataTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_user_valid_data';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'USER_VALID_DATA_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'user_valid_data_id',
                )
            ),
            new Entity\DateField(
                'USER_VALID_DATA_EMAIL_CONFIRM_DATE',
                array(
                    'required' => false,
                    'column_name' => 'user_valid_data_email_confirm_date',
                )
            ),
            new Entity\StringField(
                'USER_VALID_DATA_EMAIL',
                array(
                    'size' => 64,
                    'required' => false,
                    'column_name' => 'user_valid_data_email',
                )
            ),
            new Entity\StringField(
                'USER_VALID_DATA_EMAIL_CONFIRM_CODE',
                array(
                    'size' => 64,
                    'required' => false,
                    'column_name' => 'user_valid_data_email_confirm_code',
                )
            ),
            new Entity\DateField(
                'USER_VALID_DATA_PHONE_CONFIRM_DATE',
                array(
                    'required' => false,
                    'column_name' => 'user_valid_data_phone_confirm_date',
                )
            ),
            new Entity\StringField(
                'USER_VALID_DATA_PHONE',
                array(
                    'size' => 12,
                    'required' => false,
                    'column_name' => 'user_valid_data_phone',
                )
            ),
            new Entity\StringField(
                'USER_VALID_DATA_PHONE_CONFIRM_CODE',
                array(
                    'size' => 16,
                    'required' => false,
                    'column_name' => 'user_valid_data_phone_confirm_code',
                )
            ),
            new Entity\BooleanField(
                'USER_VALID_DATA_IS_ACTIVE',
                array(
                    'values' => array(0, 1),
                    'column_name' => 'user_valid_data_is_active',
                )
            ),
            new Entity\DateField(
                'USER_VALID_DATA_PHONE_CONFIRM_CODE_REQUEST_DATE',
                array(
                    'required' => false,
                    'column_name' => 'user_valid_data_phone_confirm_code_request_date',
                )
            ),
            new Entity\IntegerField(
                'USER_ID',
                array(
                    'column_name' => 'user_id'
                )
            ),
            new Entity\ReferenceField(
                'USER',
                'Bitrix\Main\UserTable',
                array(
                    '=this.USER_ID' => 'ref.ID'
                )
            ),
            new Entity\ReferenceField(
                'ABITURIENT_PROFILE',
                'Spo\Site\Entities\AbiturientProfileTable',
                array(
                    '=this.USER_ID' => 'ref.USER_ID'
                )
            ),
            'FIO' => new Entity\ExpressionField(
                'FIO',
                'CONCAT_WS(" ", %s, %s, %s)',
                array('USER.LAST_NAME', 'USER.NAME', 'USER.SECOND_NAME')
            ),

        );
    }
}
