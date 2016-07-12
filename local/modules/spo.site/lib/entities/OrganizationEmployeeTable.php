<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class OrganizationEmployeeTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_organization_employee';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ORGANIZATION_EMPLOYEE_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'organization_employee_id',
                    'unique' => true
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_ID',
                array(
                    'required' => true,
                    'column_name' => 'organization_id'
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION',
                'Spo\Site\Entities\OrganizationTable',
                array(
                    '=this.ORGANIZATION_ID' => 'ref.ORGANIZATION_ID'
                )
            ),
            new Entity\IntegerField(
                'USER_ID',
                array(
                    'required' => true,
                    'column_name' => 'user_id'
                )
            ),
            new Entity\IntegerField(
                'USER_MODERATOR',
                array(
                    'required' => false,
                    'column_name' => 'user_moderator'
                )
            ),
            new Entity\ReferenceField(
                'USER',
                'Bitrix\Main\UserTable',
                array(
                    '=this.USER_ID' => 'ref.ID'
                )
            )
        );
    }
}
