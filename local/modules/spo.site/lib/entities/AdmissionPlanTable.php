<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;
use Spo\Site\Entities\OrganizationSpecialtyTable;

/**
 * @Entity(repositoryClass="Spo\Site\Doctrine\Repositories\AdmissionPlanRepository")
 * @Table(name="spo_admission_plan")
 *
 * Class AdmissionPlan - план приёма организации по специальности на год
 */
class AdmissionPlanTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_admission_plan';
    }


   
    public static function loadAdmissionPlanInfoById($admissionPlanId)
    {
        $ArrayResult = AdmissionPlanTable::getList(array(
            'filter' => array('=ID' => $admissionPlanId),
            'select' => array(
                '*',
                'ORG_SPECIALTY_' => 'ORGANIZATION_SPECIALTY.*',
                'ORG_SPECIALTY_ORGANIZATION_' => 'ORGANIZATION_SPECIALTY.ORGANIZATION.*',
                'ORG_SPECIALTY_SPECIALTY_' => 'ORGANIZATION_SPECIALTY.*',
            )
        ))->fetchAll();
        return $ArrayResult;
    }
  
    public static function getActualAdmissionPlanByOrganizationSpecialtyId($organizationSpecialtyId)
    {
        $ArrayRezult=AdmissionPlanTable::getList(array(
            'filter' => array('=ORGANIZATION_SPECIALTY.ID' => $organizationSpecialtyId,'=PLAN_START_DATE'=>date('Y')),
            'select' => array(
                 '*',
                 'ORG_SPECIALTY_' => 'ORGANIZATION_SPECIALTY.*'
                )
        ))->fetchAll();
        return $ArrayRezult;
    }
  
    public static function filterByOrganizationSpecialtyId($organizationSpecialtyId)
    {
        $ArrayRezult=AdmissionPlanTable::getList(array(
            'filter' => array('=ORGANIZATION_SPECIALTY.ID' => $organizationSpecialtyId),
            'select' => array(
                '*',
                'ORG_SPECIALTY_' => 'ORGANIZATION_SPECIALTY.*'
            )
        ))->fetchAll();
        return $ArrayRezult;
    }
  
    public static function filterByOrganizationId($organizationId)
    {
        $ArrayRezult=AdmissionPlanTable::getList(array(
            'filter' => array('=ORGANIZATION_SPECIALTY.ORGANIZATION_ID' => $organizationId),
            'select' => array(
                '*',
                'ORG_SPECIALTY_' => 'ORGANIZATION_SPECIALTY.*'
            )
        ))->fetchAll();
        return $ArrayRezult;
    }
   
    //нада спросить
    public static function filterByYear($year)
    {
        $ArrayRezult=AdmissionPlanTable::getList(array(
            'filter' => array('ORGANIZATION_SPECIALTY.START_DATE' => $year),
            'select' => array('*')
        ))->fetchAll();
        return $ArrayRezult;
    }

    
    public static function getMap()
    {
        return array(
            new	Entity\IntegerField(
                'ADMISSION_PLAN_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'admission_plan_id'
                )
            ),
            new	Entity\DateTimeField(
                'ADMISSION_PLAN_START_DATE',
                array(
                    'required' => false,
                    'column_name' => 'admission_plan_start_date'
                )
            ),
            new	Entity\DateTimeField(
                'ADMISSION_PLAN_END_DATE',
                array(
                    'required' => false,
                    'column_name' => 'admission_plan_end_date'
                )
            ),
            new	Entity\IntegerField(
                'ADMISSION_PLAN_GRANT_GROUPS_NUMBER',
                array(
                    'required' => false,
                    'column_name' => 'admission_plan_grant_groups_number',
                    'default_value' => 0,
                )
            ),
            new	Entity\IntegerField(
                'ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
                array(
                    'required' => false,
                    'column_name' => 'admission_plan_grant_students_number',
                    'default_value' => 0,
                )
            ),
            new	Entity\IntegerField(
                'ADMISSION_PLAN_TUITION_STUDENTS_NUMBER',
                array(
                    'required' => false,
                    'column_name' => 'admission_plan_tuition_students_number',
                    'default_value' => 0,
                )
            ),
            new	Entity\IntegerField(
                'ADMISSION_PLAN_TUITION_GROUPS_NUMBER',
                array(
                    'required' => false,
                    'column_name' => 'admission_plan_tuition_groups_number',
                    'default_value' => 0,
                )
            ),
            new	Entity\IntegerField(
                'ADMISSION_PLAN_STATUS',
                array(
                    'required' => false,
                    'column_name' => 'admission_plan_status'
                )
            ),
            new	Entity\IntegerField(
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
                    '=this.ORGANIZATION_SPECIALTY_ID' => 'ref.ORGANIZATION_SPECIALTY_ID',
                )
            ),
        );
    }
}
