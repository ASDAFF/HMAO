<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

/**
 * OrganizationPageFile
 *
 * @Table(name="spo_organization_page_file", indexes={@Index(name="file_id", columns={"file_id"}), @Index(name="organization_page_file_id", columns={"organization_page_id"})})
 * @Entity
 */
class OrganizationPageFileTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_organization_page_file';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ORGANIZATION_PAGE_FILE_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    //'required' => true,
                    'column_name' => 'organization_page_file_id'
                )
            ),
            new Entity\StringField(
                'ORGANIZATION_PAGE_FILE_TITLE',
                array(
                    'required' => true,
                    'size' => 4098,
                    'column_name' => 'organization_page_file_title'
                )
            ),
            new Entity\IntegerField(
                'FILE_ID',
                array(
                    'column_name' => 'file_id'
                )
            ),
            new Entity\ReferenceField(
                'FILE',
                'Spo\Site\Entities\BitrixFileTable',
                array(
                    '=this.FILE_ID' => 'ref.ID'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_PAGE_ID',
                array(
                    'column_name' => 'organization_page_id'
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION_PAGE',
                'Spo\Site\Entities\OrganizationPageTable',
                array(
                    '=this.ORGANIZATION_PAGE_ID' => 'ref.ID'
                )
            )
        );
    }
}
