<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class HostelTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_hostel';
    }

    public static function UserId($exceptUserId,$org)
    {
        $ArrayRezult=HostelTable::getList(array(
            'filter' => array(
                '=ID_USER'          =>  $exceptUserId,
                '=ID_ORGANIZATION'  =>  $org
                ),
            'select' => array('*')
        ))->fetch();
        if(empty($ArrayRezult))
        {
            return false;
        }
        else
        {
            return $ArrayRezult;
        }

    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ID_HOSTEL',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'id_hostel'
                )
            ),
            new Entity\IntegerField(
                'ID_USER',
                array(
                    'required' => true,
                    'column_name' => 'id_user'
                )
            ),
            new Entity\IntegerField(
                'ID_ORGANIZATION',
                array(
                    'required' => true,
                    'column_name' => 'id_organization'
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION',
                'Spo\Site\Entities\OrganizationTable',
                array(
                    '=this.ID_ORGANIZATION' => 'ref.ORGANIZATION_ID'
                )
            ),
            new Entity\ReferenceField(
                'USER',
                'Bitrix\Main\UserTable',
                array(
                    '=this.USER_ID' => 'ref.ID'
                )
            ),
        );
    }
}
