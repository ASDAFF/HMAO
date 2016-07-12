<?php
namespace Spo\Site\Adapters;

use Spo\Site\Dictionaries\TrainingLevel;
//use Spo\Site\Doctrine\Entities\Application;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Domains\ApplicationDomain;
use Spo\Site\Dictionaries\ApplicationFundingType;
//use Spo\Site\Doctrine\Entities\ApplicationEvent;
use Spo\Site\Util\CVarDumper;

class ApplicationDomainAdapter
{
	public static function listUserApplications($applications, $includeOwners = false)
	{
		$result = array();

		/** @var Application $application */
		foreach ($applications as $application) {

            $appResult = array(
				'id' => $application->getId(),
				'creationDate' => $application->getCreationDate(),
				'specialtyTitle' => $application->getOrganizationSpecialty()->getSpecialty()->getTitle(),
				'specialtyCode' => $application->getOrganizationSpecialty()->getSpecialty()->getCode(),
				'studyPeriod' => $application->getOrganizationSpecialty()->getStudyPeriod(),
				'studyMode' => StudyMode::getValue($application->getOrganizationSpecialty()->getStudyMode()),
				'trainingLevel' => $application->getOrganizationSpecialty()->getTrainingLevel(),
				'baseEducation' => BaseEducation::getValue($application->getOrganizationSpecialty()->getBaseEducation()),
				'status' => ApplicationStatus::getValue($application->getStatus()),
				'statusCode' => intval($application->getStatus()),
				'applicationFundingType' => ApplicationFundingType::getValue($application->getFundingType()),
				'needHostel' => $application->getNeedHostel(),
				'organizationName' => $application->getOrganization()->getName(),
				'organizationId' => $application->getOrganization()->getId(),
			);
            if($includeOwners){
                $user = $application->getUser();
                $appResult['user'] = array(
                    'userFullName' => $user->getFullName(),
                    'userEmail' => $user->getEmail(),
                    'userId' => $user->getId()
                );
            }
            $result[] = $appResult;
		}
		return $result;
	}

	public static function getUserApplication(ApplicationDomain $applicationDomain)
	{
        $application = $applicationDomain->getModel();
        $applicationEvents = $applicationDomain->applicationEvents;

		/** @var Application $application */
		$result = array(
			'id' => $application->getId(),
			'creationDate' => $application->getCreationDate(),
			'specialtyTitle' => $application->getOrganizationSpecialty()->getSpecialty()->getTitle(),
			'specialtyCode' => $application->getOrganizationSpecialty()->getSpecialty()->getCode(),
			'studyMode' => StudyMode::getValue($application->getOrganizationSpecialty()->getStudyMode()),
			'baseEducation' => BaseEducation::getValue($application->getOrganizationSpecialty()->getBaseEducation()),
			'status' => $application->getStatus(),
            'fundingType' => $application->getFundingType(),
            'needHostel' => $application->getNeedHostel(),
            'applicationEvents' => array(),
		);

        if (!empty($applicationEvents))
            foreach ($applicationEvents as $event)
                /** @var ApplicationEvent $event */
                $result['applicationEvents'][] = array(
                    'date' => $event->getDate(),
                    'comment' => $event->getComment(),
                    'status' => $event->getStatus(),
                    'reason' => $event->getReason(),
                );

		return $result;
	}

    public static function getOrganizationApplication(ApplicationDomain $applicationDomain)
    {
        /** @var Application $application */
        $application = $applicationDomain->getModel();
        $applicationEvents = $applicationDomain->applicationEvents;
        $abiturient = $application->getUser();

        $result = array(
            'id' => $application->getId(),
            'creationDate' => $application->getCreationDate(),
            'specialtyTitle' => $application->getOrganizationSpecialty()->getSpecialty()->getTitle(),
            'specialtyCode' => $application->getOrganizationSpecialty()->getSpecialty()->getCode(),
            'studyMode' => $application->getOrganizationSpecialty()->getStudyMode(),
            'baseEducation' => $application->getOrganizationSpecialty()->getBaseEducation(),
            'status' => $application->getStatus(),
            'fundingType' => $application->getFundingType(),
            'needHostel' => $application->getNeedHostel(),
            'applicationEvents' => array(),
            'abiturient' => array(
                'fullname' => $abiturient->getFullName(),
                'userId' => $abiturient->getId(),
            ),
        );

        if (!empty($applicationEvents))
            foreach ($applicationEvents as $event)
                /** @var ApplicationEvent $event */
                $result['applicationEvents'][] = array(
                    'date' => $event->getDate(),
                    'comment' => $event->getComment(),
                    'status' => $event->getStatus(),
                    'reason' => $event->getReason(),
                );
        return $result;
    }

    public static function getApplicationList(ApplicationDomain $domain)
    {
		$Arrrai=array(
			'list' =>self::listUserApplications($domain->getEntityCollection(), true),
			'totalCount' => $domain->getTotalCount()
		);
        return $Arrrai;
    }
}