<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;

/**
 * Class EnrollmentTable
 *
 * Fields:
 * <ul>
 * <li> enrollment_id int mandatory
 * <li> admission_plan_id int mandatory
 * <li> organization_id int mandatory
 * <li> enrollment_finance int optional
 * <li> enrollment_fio string(255) optional
 * <li> enrollment_copy int optional
 * <li> enrollment_ball double optional
 * <li> enrollment_priory int optional
 * </ul>
 *
 * @package Bitrix\Enrollment
 **/

class EnrollmentTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'spo_enrollment';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ENROLLMENT_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'enrollment_id'
                )
            ),
            new Entity\IntegerField(
                'ADMISSION_PLAN_ID',
                array(
                    'column_name' => 'admission_plan_id',
                    'required' => true,
                )
            ),
            new Entity\ReferenceField(
                'ADMISSION_PLAN',
                'Spo\Site\Entities\AdmissionPlanTable',
                array(
                    '=this.ADMISSION_PLAN_ID' => 'ref.ADMISSION_PLAN_ID'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_ID',
                array(
                    'column_name' => 'organization_id',
                    'required' => true,
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION',
                'Spo\Site\Entities\OrganizationSpecialtyTable',
                array(
                    '=this.ORGANIZATION_ID' => 'ref.ORGANIZATION_SPECIALTY_ID'
                )
            ),
            new Entity\IntegerField(
                'ENROLLMENT_FINANCE',
                array(
                    'required' => true,
                    'column_name' => 'enrollment_finance'
                )
            ),
            new Entity\StringField(
                'ENROLLMENT_FIO',
                array(
                    'size' => 255,
                    'required' => true,
                    'column_name' => 'enrollment_fio'
                )
            ),
            new Entity\IntegerField(
                'ENROLLMENT_COPY',
                array(
                    'column_name' => 'enrollment_copy'
                )
            ),
            new Entity\StringField(
                'ENROLLMENT_BALL',
                array(
                    'column_name' => 'enrollment_ball'
                )
            ),
            new Entity\IntegerField(
                'ENROLLMENT_PRIORY',
                array(
                    'column_name' => 'enrollment_priory'
                )
            ),
            new Entity\IntegerField(
                'ENROLLMENT',
                array(
                    'column_name' => 'enrollment'
                )
            ),
        );
    }

    /*Удаление рекомендованых к зачислению по организации*/
    public static function deleteEnroll($Organ)
    {
        global $DB;
        $sql="DELETE FROM spo_enrollment WHERE organization_id=".(int)$Organ;
        $res = $DB->Query($sql);        
        return $res;
    }
}
?>