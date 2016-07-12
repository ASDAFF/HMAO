<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

class ApplicationTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_application';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'APPLICATION_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'application_id'
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
                'ABITURIENT_USER_ID',
                'Spo\Site\Entities\AbiturientProfileTable',
                array(
                    '=this.USER_ID' => 'ref.USER_ID'
                )
            ),
            new Entity\ReferenceField(
                'USER',
                'Bitrix\Main\UserTable',
                array(
                    '=this.USER_ID' => 'ref.ID'
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
            new Entity\DatetimeField(
                'APPLICATION_CREATION_DATE',
                array(
                    'column_name' => 'application_creation_date'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_ID',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_id'
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION_SPECIALTY',
                'Spo\Site\Entities\OrganizationSpecialtyTable',
                array(
                    '=this.ORGANIZATION_SPECIALTY_ID' => 'ref.ORGANIZATION_SPECIALTY_ID'
                )
            ),
            new Entity\IntegerField(
                'APPLICATION_FUNDING_TYPE',
                array(
                    'required' => true,
                    'column_name' => 'application_funding_type'
                )
            ),
            new Entity\IntegerField(
                'APPLICATION_STATUS',
                array(
                    'required' => true,
                    'column_name' => 'application_status'
                )
            ),
            new Entity\BooleanField(
                'SPO_APPLICATION_NEED_HOSTEL',
                array(
                    'value' => array(0, 1),
                    'column_name' => 'spo_application_need_hostel'
                )
            ),
            new Entity\IntegerField(
                'APPLICATION_PRIORITY',
                array(
                    'required' => true,
                    'column_name' => 'application_priority'
                )
            ),
            new Entity\IntegerField(
                'IMPORT_TO_C',
                array(
                    'column_name'   => 'import_to_c',
                )
            ),
            new Entity\IntegerField(
                'ADMISSION_PLAN_ID',
                array(
                    'required' => true,
                    'column_name' => 'admission_plan_id'
                )
            ),
            new Entity\ReferenceField(
                'ADMISSION_PLAN',
                'Spo\Site\Entities\AdmissionPlanTable',
                array(
                    '=this.ADMISSION_PLAN_ID' => 'ref.ADMISSION_PLAN_ID'
                )
            ),
            new Entity\ReferenceField(
                'APPLICATION_EVENT',
                'Spo\Site\Entities\ApplicationEventTable',
                array(
                    '=this.APPLICATION_ID' => 'ref.APPLICATION_ID'
                )
            ),
            new Entity\ReferenceField(
                'ABITURIENT',
                'Spo\Site\Entities\AbiturientProfileTable',
                array(
                    '=this.USER_ID' => 'ref.USER_ID'
                )
            ),
        );
    }
}
