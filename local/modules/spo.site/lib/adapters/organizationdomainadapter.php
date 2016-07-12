<?php
namespace Spo\Site\Adapters;

use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\OrganizationStatus;
use Spo\Site\Entities\OrganizationTable;
//use Spo\Site\Doctrine\Entities\Organization;
//use Spo\Site\Doctrine\Entities\OrganizationPage;
//use Spo\Site\Doctrine\Entities\OrganizationSpecialty;
//use Spo\Site\Doctrine\Entities\OrganizationSpecialtyExam;
//use Spo\Site\Doctrine\Entities\Qualification;
//use Spo\Site\Doctrine\Entities\AdmissionPlan;
//use Spo\Site\Doctrine\Entities\OrganizationSpecialtyAdaptation;

class OrganizationDomainAdapter
{
    public static function getOrganizationInformation($IDORGID/*OrganizationDomain $domain*/)
    {
        /*$data = array();

		// @var Organization $model 
        $model = $domain->getModel();
        $city  = $model->getCity();
        $regionArea = $model->getRegionArea();

        $data = array(
            'organizationId' => $model->getOrganizationId(),
            'organizationName' => $model->getOrganizationName(),
            'organizationFullName' => $model->getOrganizationFullName(),
            'organizationFoundationYear' => $model->getOrganizationFoundationYear(),
            'organizationAddress' => $model->getOrganizationAddress(),
            'organizationEmail' => $model->getOrganizationEmail(),
            'organizationPhone' => $model->getOrganizationPhone(),
            'organizationSite' => $model->getOrganizationSite(),
            'organizationCoordinateX' => $model->getOrganizationCoordinateX(),
            'organizationCoordinateY' => $model->getOrganizationCoordinateY(),
            'city' => ($city === null) ? null : $city->getCityName(),
            'cityId' => ($city === null) ? null : $city->getCityId(),
            'regionArea' => ($regionArea === null) ? null : $regionArea->getRegionAreaId(),
            'regionAreaName' => ($regionArea === null) ? null : $regionArea->getRegionAreaName(),
        );*/
        $organizationModel = OrganizationTable::getList(array(
            'filter' => array('ORGANIZATION_ID'=>$IDORGID),
            'select' => array(
                'organizationId'=>'ORGANIZATION_ID',
                'organizationName'=>'ORGANIZATION_NAME',
                'organizationFullName'=>'ORGANIZATION_FULL_NAME',
                'organizationFoundationYear'=>'ORGANIZATION_FOUNDATION_YEAR',
                'organizationAddress'=>'ORGANIZATION_ADDRESS',
                'organizationEmail'=>'ORGANIZATION_EMAIL',
                'organizationPhone'=>'ORGANIZATION_PHONE',
                'organizationSite'=>'ORGANIZATION_SITE',
                'organizationCoordinateX'=>'ORGANIZATION_COORDINATE_X',
                'organizationCoordinateY'=>'ORGANIZATION_COORDINATE_Y',
                'city'=>'CITY.CITY_NAME',
                'cityId'=>'CITY_ID',
                'regionArea'=>'REGION_AREA_ID',
                'regionAreaName'=>'REGION_AREA.REGION_AREA_NAME',
                'hostel'=>'ORGANIZATION_HOSTEL',
            )
        ))->fetchAll();
        $data=$organizationModel[0];
        return $data;
    }
    public static function listOrganizations(){
	/*public static function listOrganizations(OrganizationDomain $domain)
	{
		$data = array();
		$organizationEntities = $domain->getEntityCollection();
		if (empty($organizationEntities))
			return array();

		foreach ($organizationEntities as $organizationEntity) {
			// @var Organization $organizationEntity
            $cityName = $organizationEntity->getCity() ? $organizationEntity->getCity()->getCityName() : '';

			$organizationData = array();
			$organizationData['id'] = $organizationEntity->getOrganizationId();
			$organizationData['city'] = $cityName;
			$organizationData['name'] = $organizationEntity->getName();
			$organizationData['phone'] = $organizationEntity->getOrganizationPhone();
			$organizationData['site'] = $organizationEntity->getOrganizationSite();
			$organizationData['address'] = $organizationEntity->getOrganizationAddress();

			$data[] = $organizationData;
		}*/
        $ArrayResult = OrganizationTable::getList(array(
            'filter' => array(
                'ORGANIZATION_STATUS'=>OrganizationStatus::ENABLED
            ),
            'select' => array(
                'id'=>'ORGANIZATION_ID',
                'city'=>'CITY.CITY_NAME',
                'name'=>'ORGANIZATION_NAME',
                'phone'=>'ORGANIZATION_PHONE',
                'site'=>'ORGANIZATION_SITE',
                'address'=>'ORGANIZATION_ADDRESS',
            )
        ))->fetchAll();
		//return $data;
        return $ArrayResult;
	}

    public static function getOrganizationSpecialtiesIds(OrganizationDomain $organizationDomain)
    {
        /** @var Organization $organization */
        $organization = $organizationDomain->getModel();
        $organizationSpecialties = $organization->getSpecialties();

        $organizationSpecialtiesIds = array();
        foreach ($organizationSpecialties as $organizationSpecialty) {
            /** @var OrganizationSpecialty $organizationSpecialty */
            $organizationSpecialtiesIds[] = $organizationSpecialty->getId();
        }

        return $organizationSpecialtiesIds;
    }

    public static function getOrganizationControlOfEntrance(OrganizationDomain $organizationDomain)
    {
        $organization = $organizationDomain->getModel();
        /** @var Organization $organization */
        $result = array(
            'id' => $organization->getId(),
            'name' => $organization->getFullName(),
        );

        // Массив для группировка всех образовательных программ по форме обучения (очное, заочное, ...) и требуемому
        // базовому образованию (9 классов, 10 классов, ...)
        $specialtyGroups = array();
        foreach (StudyMode::getKeysArray() as $studyMode) {
            $baseEducationArray = array();
            foreach (BaseEducation::getKeysArray() as $baseEducation) {
                $baseEducationArray[$baseEducation] = array();
            }
            $specialtyGroups[$studyMode] = $baseEducationArray;
        }


        $specialty = $organization->getSpecialties();
        foreach ($specialty as $s) {
            /** @var OrganizationSpecialty $s */
            $specialtyData = array();
            $specialtyData['code'] = $s->getSpecialty()->getCode();
            $specialtyData['id'] = $s->getId();
            $specialtyData['title'] = $s->getSpecialty()->getTitle();
            $specialtyData['studyMode'] = StudyMode::getValue($s->getStudyMode());
            $specialtyData['baseEducation'] = BaseEducation::getValue($s->getBaseEducation());
            $specialtyData['studyPeriod'] = $s->getStudyPeriod();
            $specialtyData['trainingLevel'] = TrainingLevel::getValue($s->getTrainingLevel());
            $specialtyData['trainingType'] = $s->getTrainingLevel();

            $specialtyData['plannedAbiturientsCount'] = $s->getPlannedAbiturientsCount();
            $specialtyData['plannedGroupsCount'] = $s->getPlannedGroupsCount();

            $specialtyData['qualifications'] = array();
            foreach ($s->getOrganizationSpecialtyQualifications() as $qualification) {
                /** @var Qualification $qualification */
                $specialtyData['qualifications'][] = array(
                    'id' => $qualification->getId(),
                    'title' => $qualification->getTitle(),
                );
            }

            $specialtyGroups[intval($s->getStudyMode())][intval($s->getBaseEducation())][] = $specialtyData;

        }

        $result['specialtiesGroups'] = $specialtyGroups;

        return $result;
    }

	public static function listOrganizationSpecialties(OrganizationDomain $organizationDomain)
	{
		$organization = $organizationDomain->getModel();

		/** @var Organization $organization */
		$result = array(
			'id' => $organization->getId(),
			'name' => $organization->getFullName(),
			'specialties' => array(),
		);

		$specialty = $organization->getSpecialties();
		foreach ($specialty as $s) {
			/** @var OrganizationSpecialty $s */
			$specialtyData = array();
			$specialtyData['code'] = $s->getSpecialty()->getCode();
			$specialtyData['id'] = $s->getId();
			$specialtyData['title'] = $s->getSpecialty()->getTitle();
			$specialtyData['studyMode'] = StudyMode::getValue($s->getStudyMode());
			$specialtyData['baseEducation'] = BaseEducation::getValue($s->getBaseEducation());
			$specialtyData['studyPeriod'] = $s->getStudyPeriod();
			$specialtyData['trainingLevel'] = TrainingLevel::getValue($s->getTrainingLevel());
			$specialtyData['trainingType'] = $s->getTrainingLevel();

            $specialtyData['exams'] = array();
            foreach ($s->getOrganizationSpecialtyExams() as $exam) {
                /** @var OrganizationSpecialtyExam $exam */
                $examData = array();
                $examData['discipline'] = $exam->getDiscipline();
                $examData['type'] = $exam->getType();
                $specialtyData['exams'][] = $examData;
            }

            $specialtyData['qualifications'] = array();
            foreach ($s->getOrganizationSpecialtyQualifications() as $qualification) {
                /** @var Qualification $qualification */
                $specialtyData['qualifications'][] = array(
                    'id' => $qualification->getId(),
                    'title' => $qualification->getTitle(),
                );
            }

            $specialtyData['actualAdmissionPlan'] = array();
            $admissionPlans = $s->getAdmissionPlans();
            $actualAdmissionPlan = $admissionPlans[0];

            if (!empty($actualAdmissionPlan)) {
                /** @var AdmissionPlan $actualAdmissionPlan */
                $specialtyData['actualAdmissionPlan']['startDate'] = $actualAdmissionPlan->getStartDate();
                $specialtyData['actualAdmissionPlan']['endDate'] = $actualAdmissionPlan->getEndDate();
                $specialtyData['actualAdmissionPlan']['grantStudentsNumber'] = $actualAdmissionPlan->getGrantStudentsNumber();
                $specialtyData['actualAdmissionPlan']['tuitionStudentsNumber'] = $actualAdmissionPlan->getTuitionStudentsNumber();
            }

            $specialtyData['adaptationTypes'] = array();
            foreach ($s->getOrganizationSpecialtyAdaptationTypes() as $adaptationType) {
                /** @var OrganizationSpecialtyAdaptation $adaptationType */
                $specialtyData['adaptationTypes'][] = $adaptationType->getType();
            }

            $result['specialties'][] = $specialtyData;

		}

		return $result;
	}

	/**
	 * @param OrganizationDomain $organizationDomain
	 * @return array
	 */
	public static function listOrganizationSpecialtiesInfoGroupedBySpecialty(OrganizationDomain $organizationDomain)
	{
		$organization = $organizationDomain->getModel();
		/** @var Organization $organization */
		$result = array(
			'id' => $organization->getId(),
			'name' => $organization->getFullName(),
			'specialties' => array(),
		);

		$specialty = $organization->getSpecialties();
		foreach ($specialty as $s) {
			/** @var OrganizationSpecialty $s */

			if (!isset($result['specialties'][$s->getSpecialty()->getId()])) {
				$result['specialties'][$s->getSpecialty()->getId()] = array(
					'title' => $s->getSpecialty()->getTitle(),
					'code' => $s->getSpecialty()->getCode(),
					'baseEducation' => array($s->getBaseEducation() => BaseEducation::getValue($s->getBaseEducation())),
					'studyMode' => array($s->getStudyMode() => StudyMode::getValue($s->getStudyMode())),
				);
			} else {
				$result['specialties'][$s->getSpecialty()->getId()]['baseEducation'][$s->getBaseEducation()] = BaseEducation::getValue($s->getBaseEducation());
				$result['specialties'][$s->getSpecialty()->getId()]['studyMode'][$s->getStudyMode()] = StudyMode::getValue($s->getStudyMode());
			}
		}

		return $result;
	}
}