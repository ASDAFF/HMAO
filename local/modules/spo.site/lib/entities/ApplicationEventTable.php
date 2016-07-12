<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

/**
 * @Entity(repositoryClass="Spo\Site\Doctrine\Repositories\ApplicationEventRepository")
 * @Table(name="spo_application_event")
 *
 * Class ApplicationEvent
 */
class ApplicationEventTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_application_event';
    }

    public static function getMap()
    {
        return array(
            new	Entity\IntegerField(
                'APPLICATION_EVENT_ID',
                array(
                    'primary'       => true,
                    'autocomplete'  => true,
                    'column_name'   => 'application_event_id'
                )
            ),
            new	Entity\IntegerField(
                'APPLICATION_ID',
                array(
                    'required' => false,
                    'column_name' => 'application_id'
                )
            ),
            new Entity\ReferenceField(
                'APPLICATION',
                'Spo\Site\Entities\ApplicationTable',
                array(
                    '=this.APPLICATION_ID' => 'ref.APPLICATION_ID',
                )
            ),
            new Entity\IntegerField(
                'USER_ID',
                array(
                    'required' => true,
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
            new	Entity\DatetimeField(
                'APPLICATION_EVENT_DATE',
                array(
                    'required' => true,
                    'column_name' => 'application_event_date'
                )
            ),
            new	Entity\IntegerField(
                'APPLICATION_EVENT_STATUS',
                array(
                    'required' => true,
                    'column_name' => 'application_event_status'
                )
            ),
            new	Entity\IntegerField(
                'APPLICATION_EVENT_REASON',
                array(
                    'required' => true,
                    'column_name' => 'application_event_reason'
                )
            ),
            new	Entity\TextField(
                'APPLICATION_EVENT_COMMENT',
                array(
                    'required' => false,
                    'column_name' => 'application_event_comment'
                )
            )

        );
    }
}
