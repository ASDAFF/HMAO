<?php
namespace Spo\Site\Adapters;

use Spo\Site\Domains\AdmissionPlanDomain;
use Spo\Site\Domains\SpecialtyDomain;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Doctrine\Entities\Specialty;
use Spo\Site\Doctrine\Entities\OrganizationSpecialty;
use Spo\Site\Doctrine\Entities\Organization;
use Spo\Site\Doctrine\Entities\AdmissionPlan;
use Spo\Site\Doctrine\Entities\AdmissionPlanEvent;
use Spo\Site\Util\CVarDumper;
use Spo\Site\Entities\AdmissionPlanTable;

class AdmissionPlanDomainAdapter
{

    public static function getAdmissionPlanInfo(/*AdmissionPlanDomain $admissionPlanDomain*/$admissionPlanDomain)
    {
        /** @var AdmissionPlan $admissionPlan */
        //$admissionPlan = $admissionPlanDomain->getModel();
        //$organizationSpecialty = $admissionPlan->getOrganizationSpecialty();
        //$specialty = $organizationSpecialty->getSpecialty();
        //$organization = $organizationSpecialty->getOrganization();
        $ArrayResult = AdmissionPlanTable::getList(array(
            'filter' => array('ADMISSION_PLAN_ID'=>$admissionPlanDomain),
            'select' => array(
                'id'=>'ADMISSION_PLAN_ID',
                'startDate'=>'ADMISSION_PLAN_START_DATE',
                'endDate' => 'ADMISSION_PLAN_END_DATE',
                'grantStudentsNumber' => 'ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
                'tuitionStudentsNumber' => 'ADMISSION_PLAN_TUITION_STUDENTS_NUMBER',
                'status' => 'ADMISSION_PLAN_STATUS',
                'studyMode' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
                'studyPeriod' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                'trainingLevel' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_LEVEL',
                'baseEducation' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'code' => 'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'title' => 'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'fullName' => 'ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_FULL_NAME',
                'address' => 'ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_ADDRESS',
            )
        ))->fetchAll();
        foreach ($ArrayResult as $res) {
            $result = array(
                'admissionPlan' => array(
                    'id' => $res['id'],
                    'startDate' => date("Y-m-d",strtotime($res['startDate'])),
                    'endDate' => date("Y-m-d",strtotime($res['endDate'])),
                    'grantStudentsNumber' => $res['grantStudentsNumber'],
                    'tuitionStudentsNumber' => $res['tuitionStudentsNumber'],
                    'status' => $res['status'],
                    'events' => array(),
                ),
                'organizationSpecialty' => array(
                    'studyMode' => $res['studyMode'],
                    'studyPeriod' => $res['studyPeriod'],
                    'trainingLevel' => $res['trainingLevel'],
                    'baseEducation' => $res['baseEducation'],
                ),
                'specialty' => array(
                    'code' => $res['code'],
                    'title' => $res['title'],
                ),
                'organization' => array(
                    'fullName' => $res['fullName'],
                    'address' => $res['address'],
                ),);
            }
            /*$result = array(
                'admissionPlan' => array(
                    'id' => $admissionPlan->getId(),
                    'startDate' => $admissionPlan->getStartDate()->format('Y-m-d'),
                    'endDate' => $admissionPlan->getStartDate()->format('Y-m-d'),
                    'grantStudentsNumber' => $admissionPlan->getGrantStudentsNumber(),
                    'tuitionStudentsNumber' => $admissionPlan->getTuitionStudentsNumber(),
                    'status' => $admissionPlan->getStatus(),
                    'events' => array(),
                ),
                'organizationSpecialty' => array(
                    'studyMode' => $organizationSpecialty->getStudyMode(),
                    'studyPeriod' => $organizationSpecialty->getStudyPeriod(),
                    'trainingLevel' => $organizationSpecialty->getTrainingLevel(),
                    'baseEducation' => $organizationSpecialty->getBaseEducation(),
                ),
                'specialty' => array(
                    'code' => $specialty->getCode(),
                    'title' => $specialty->getTitle(),
                ),
                'organization' => array(
                    'fullName' => $organization->getFullName(),
                    'address' => $organization->getOrganizationAddress(),
                ),
            );*/

        /*$events = $admissionPlanDomain->admissionPlanEvents;
        if (!empty($events))
         foreach ($events as $event)
             // @var AdmissionPlanEvent $event
             $result['admissionPlan']['events'][] = array(
                 'status' => $event->getStatus(),
                 'date' => $event->getDate()->format('Y-m-d'),
                 'comment' => $event->getComment(),
             );*/

        return $result;
        //echo "OK";

    }

    /*public static function formAdmissionPlan(OrganizationDomain $organizationDomain, AdmissionPlanDomain $admissionPlanDomain)
    {
        // @var Organization $organization
        $organization = $organizationDomain->getModel();
        $inactiveSpecialties = array();
        $activeSpecialties = array();

        foreach ($organization->getOrganizationSpecialties() as $organizationSpecialty)
        {
            // @var OrganizationSpecialty $organizationSpecialty
            // @var Specialty $specialty
            $specialty = $organizationSpecialty->getSpecialty();

            $result = array(
                'specialtyId' => $specialty->getId(),
                'specialtyCode' => $specialty->getCode(),
                'specialtyTitle' => $specialty->getTitle(),
                'organizationSpecialtyId' => $organizationSpecialty->getId(),
                'organizationSpecialtyBaseEducation' => $organizationSpecialty->getBaseEducation(),
                'organizationSpecialtyStudyMode' => $organizationSpecialty->getStudyMode(),
                'admissionPlan' => array(),
            );

            if (!$organizationSpecialty->getStatus()) {
                $inactiveSpecialties[] = $result;
                continue;
            }

            foreach ($admissionPlanDomain->getEntityCollection() as $admissionPlan) {
                /// @var AdmissionPlan $admissionPlan
                if ($admissionPlan->getOrganizationSpecialtyId() == $organizationSpecialty->getId()) {
                    $result['admissionPlan']['grantStudentsNumber'] = $admissionPlan->getGrantStudentsNumber();
                    $result['admissionPlan']['grantGroupsNumber'] = $admissionPlan->getGrantGroupsNumber();
                    $result['admissionPlan']['tuitionStudentsNumber'] = $admissionPlan->getTuitionStudentsNumber();
                    $result['admissionPlan']['tuitionGroupsNumber'] = $admissionPlan->getTuitionGroupsNumber();
                    $result['admissionPlan']['startDate'] = $admissionPlan->getStartDate()->format('d-m-Y');
                    $result['admissionPlan']['endDate'] = $admissionPlan->getEndDate()->format('d-m-Y');
                    $result['admissionPlan']['status'] = $admissionPlan->getStatus();
                    $result['admissionPlan']['lastEvent'] = array();

                    // @var AdmissionPlanEvent $lastEvent 
                    $lastEvent = $admissionPlan->getEvents()->last();
                    if (!empty($lastEvent))
                        $result['admissionPlan']['lastEvent'] = array(
                            'date' => $lastEvent->getDate()->format('Y-m-d'),
                            'status' => $lastEvent->getStatus(),
                            'comment' => $lastEvent->getComment(),
                        );
                }
            }

            $activeSpecialties[] = $result;
        }

        return array('activeSpecialties' => $activeSpecialties, 'inactiveSpecialties' => $inactiveSpecialties);
    }*/


}