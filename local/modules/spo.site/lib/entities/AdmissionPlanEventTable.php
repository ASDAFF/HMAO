<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;


/**
 * @Entity(repositoryClass="Spo\Site\Doctrine\Repositories\AdmissionPlanEventRepository")
 * @Table(name="spo_admission_plan_event")
 *
 * Class AdmissionPlanEvent
 */
class AdmissionPlanEventTable extends Entity\DataManager
{

    public static function getTableName()
    {
        return 'spo_admission_plan_event';
    }
/*
    public function getAdmissionPlanEvents($admissionPlanId)
    {
        $this->queryBuilder = $this->createQueryBuilder('AdmissionPlanEvent')
            ->select('AdmissionPlanEvent')
            ->andWhere('AdmissionPlanEvent.admissionPlanId = :planId')
            ->setParameter('planId', $admissionPlanId)
            ->addOrderBy('AdmissionPlanEvent.date', 'DESC');

        return $this;
    }
 */
    public static function getAdmissionPlanEvents($admissionPlanId)
    {
        $ArrayRezult=AdmissionPlanEventTable::getList(array(
            'order' => array('DATE' => 'DESC'),
            'filter' => array('PLAN_ID' => $admissionPlanId),
            'select' => array('*')
        ))->fetchAll();
        return $ArrayRezult;
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ADMISSION_PLAN_EVENT_ID',
                array(
                    'primary'       => true,
                    'autocomplete'  => true,
                    'column_name'   => 'admission_plan_event_id',
                    'unique'        => true
                )
            ),
            new	Entity\IntegerField(
                'ADMISSION_PLAN_ID',
                array(
                    'required'      => true,
                    'column_name'   => 'admission_plan_id'
                )
            ),
            new Entity\ReferenceField(
                'PLAN',
                'Spo\Site\Entities\AdmissionPlanTable',
                array(
                    '=this.ADMISSION_PLAN_ID' => 'ref.ADMISSION_PLAN_ID'
                )
            ),
            new	Entity\DatetimeField(
                'ADMISSION_PLAN_EVENT_DATE',
                array(
                    'required'      => false,
                    'column_name'   => 'admission_plan_event_date'
                )
            ),
            new	Entity\IntegerField(
                'ADMISSION_PLAN_EVENT_STATUS',
                array(
                    'required'      => true,
                    'column_name'   => 'admission_plan_event_status',
                )
            ),
            new Entity\TextField(
                'ADMISSION_PLAN_EVENT_COMMENT',
                array(
                    'required'      => false,
                    'column_name'   => 'admission_plan_event_comment',
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
            )
        );
    }

}
