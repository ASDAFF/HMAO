<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class OrganizationPageTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_organization_page';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ORGANIZATION_PAGE_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'organization_page_id'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_PAGE_TYPE',
                array(
                    'required' => false,
                    'column_name' => 'organization_page_type'
                )
            ),
            new Entity\TextField(
                'ORGANIZATION_PAGE_CONTENT',
                array(
                    'required' => false,
                    'column_name' => 'organization_page_content'
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
            new Entity\ReferenceField(
                'ORGANIZATION_PAGE_FILE',
                'Spo\Site\Entities\OrganizationPageFileTable',
                array(
                    '=this.ORGANIZATION_PAGE_ID' => 'ref.ORGANIZATION_PAGE_ID'
                )
            ),
        );
    }
}
