<?php
namespace Spo\Site\Adapters;

use Spo\Site\Doctrine\Entities\OrganizationSpecialtyAdaptation;
use Spo\Site\Domains\SpecialtyDomain;
use Spo\Site\Doctrine\Entities\Specialty;
use Spo\Site\Doctrine\Entities\OrganizationSpecialty;
use Spo\Site\Domains\OrganizationSpecialtyDomain;
use Spo\Site\Util\CVarDumper;
use Spo\Site\Util\Methodarguments\OrganizationSpecialtyLoadArguments;
use Spo\Site\Doctrine\Entities\Qualification;
use Spo\Site\Doctrine\Entities\OrganizationSpecialtyExam;

class OrganizationSpecialtyDomainAdapter
{
	public static function getOrganizationSpecialtiesListWithSpecialties(OrganizationSpecialtyDomain $domain)
	{
        $result = array();
		$orgSpecialties = $domain->getEntityCollection();

		foreach ($orgSpecialties as $orgSpecialty) {
			/* @var OrganizationSpecialty $orgSpecialty */
            $specialty = $orgSpecialty->getSpecialty();

            $orgSpecialtyAttributes = array(
                'organizationSpecialtyId'            => $orgSpecialty->getId(),
                'organizationSpecialtyBaseEducation' => $orgSpecialty->getBaseEducation(),
                'organizationSpecialtyStudyMode'     => $orgSpecialty->getStudyMode(),
                'organizationSpecialtyStatus'        => $orgSpecialty->getStatus(),
                'specialty' => array(
                    'specialtyId'    => $specialty->getId(),
                    'specialtyCode'  => $specialty->getCode(),
                    'specialtyTitle' => $specialty->getTitle(),
                )
            );

			$result[] = $orgSpecialtyAttributes;
		}

		return $result;
	}

    public static function getOrganizationSpecialtiesListWithTotalCount(OrganizationSpecialtyDomain $domain)
    {
        return array(
            'list'       => self::getOrganizationSpecialtiesListWithSpecialties($domain),
            'totalCount' => $domain->getTotalCount()
        );
    }

    public static function getOrganizationSpecialty(
        OrganizationSpecialtyDomain $domain,
        OrganizationSpecialtyLoadArguments $args
    ){
        /* @var OrganizationSpecialty $os */
        $os = $domain->getModel();

        $result = array(
            'id'                      => $os->getId(),
            'specialty'               => $os->getSpecialty()->getId(),
            'baseEducation'           => $os->getBaseEducation(),
            'trainingLevel'           => $os->getTrainingLevel(),
            'trainingType'            => $os->getTrainingType(),
            'studyMode'               => $os->getStudyMode(),
            'studyPeriod'             => $os->getStudyPeriod(),
            'plannedAbiturientsCount' => $os->getPlannedAbiturientsCount(),
            'plannedGroupsCount'      => $os->getPlannedGroupsCount(),
            'adapted' => false,
            'adaptationTypes' => array(),

        );

        if($args->isWithExams())
        {
            $list = $os->getOrganizationSpecialtyExams();
            $result['exams'] = array();

            /* @var OrganizationSpecialtyExam $exam */
            foreach($list as $exam){
                $result['exams'][] = array(
                    'disciplineId' => $exam->getDiscipline(),
                    'id'           => $exam->getId(),
                    'type'         => $exam->getType(),
                );
            }
        }
//var_dump($args->isWithQualifications());exit;
        if($args->isWithQualifications())
        {
            $list = $os->getOrganizationSpecialtyQualifications();
            $result['qualifications'] = array();

            /* @var Qualification $qualification */
            foreach($list as $qualification){
                $result['qualifications'][] = array(
                    'id'    => $qualification->getId(),
                    'title' => $qualification->getTitle(),
                );
            }
        }

        $organizationSpecialtyAdaptationTypes = $os->getOrganizationSpecialtyAdaptationTypes();
        if (count($organizationSpecialtyAdaptationTypes)) {
            $result['adapted'] = true;

            foreach ($organizationSpecialtyAdaptationTypes as $t)
                /** @var OrganizationSpecialtyAdaptation $t */
                $result['adaptationTypes'][] = $t->getType();
        }


        return $result;
    }

}