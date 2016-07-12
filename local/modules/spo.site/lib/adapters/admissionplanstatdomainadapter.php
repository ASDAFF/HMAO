<?php
namespace Spo\Site\Adapters;


use Spo\Site\Dictionaries\AdmissionPlanStatus;
use Spo\Site\Dictionaries\ApplicationFundingType;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Util\CVarDumper;

class AdmissionPlanStatDomainAdapter
{

    public static function getAdmissionPlansStatForMainPage(array $admissionPlans)
    {
        $specialties = array();
        $organizations = array();
        $createdAdmissionPlansNumber = 0;
        $declinedAdmissionPlansNumber = 0;
        $intramuralGrantStudentsNumber = 0;
        $intramuralTuitionStudentsNumber = 0;
        $extramuralGrantStudentsNumber = 0;
        $extramuralTuitionStudentsNumber = 0;

        foreach ($admissionPlans as $plan) {
            $organizations[$plan['organizationId']] = true;
            $specialties[$plan['specialtyId']] = true;

            if (StudyMode::EXTRAMURAL == $plan['studyMode']) {
                $intramuralGrantStudentsNumber += $plan['grantStudentsNumber'];
                $intramuralTuitionStudentsNumber += $plan['tuitionStudentsNumber'];
            } elseif (StudyMode::INTRAMURAL == $plan['studyMode']) {
                $extramuralGrantStudentsNumber += $plan['grantStudentsNumber'];
                $extramuralTuitionStudentsNumber += $plan['tuitionStudentsNumber'];
            }

            if (AdmissionPlanStatus::CREATED== $plan['admissionPlanStatus'])
                $createdAdmissionPlansNumber++;

            if (AdmissionPlanStatus::DECLINED == $plan['admissionPlanStatus'])
                $declinedAdmissionPlansNumber++;
        }

        $admissionPlanData = array(
            'specialtiesNumber' => count($specialties),
            'organizationsNumber' => count($organizations),
            'createdAdmissionPlansNumber' => $createdAdmissionPlansNumber,
            'declinedAdmissionPlansNumber' => $declinedAdmissionPlansNumber,
            'intramuralGrantStudentsNumber' => $intramuralGrantStudentsNumber,
            'intramuralTuitionStudentsNumber' => $intramuralTuitionStudentsNumber,
            'extramuralGrantStudentsNumber' => $extramuralGrantStudentsNumber,
            'extramuralTuitionStudentsNumber' => $extramuralTuitionStudentsNumber,
        );

        return $admissionPlanData;
    }

    public static function getApplicationsStatForMainPage(array $applications)
    {

        $applicationsNumberBySpecialties = array();
        $allApplicationsNumber = count($applications);
        $specialties = array();

        foreach ($applications as $application) {
            if (!isset($applicationsNumberBySpecialties[$application['specialtyId']]))
                $applicationsNumberBySpecialties[$application['specialtyId']] = 0;

            $applicationsNumberBySpecialties[$application['specialtyId']]++;
            $specialties[$application['specialtyId']] = $application['specialtyTitle'];
        }

        arsort($applicationsNumberBySpecialties);
        $top5specialties = array();
        $i = 0;

        foreach ($applicationsNumberBySpecialties as $specialtyId => $applicationsNumber) {
            $top5specialties[$specialties[$specialtyId]] = $applicationsNumber;

            $i++;
            if ($i == 5)
                break;
        }

        $monthsNames = array(
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь',
        );

        $applicationsByDate = array();

        foreach ($applications as $a) {
            /** @var \DateTime $creationDate */
            $creationDate = $a['applicationCreationDate'];
            $month = $monthsNames[(integer) $creationDate->format('m')];
            $day = ((integer) $creationDate->format('d') > 15) ? '16-31' : '01-15';
            $dateKey = $month . ' ' . $day;

            if (!isset($applicationsByDate[$dateKey]))
                $applicationsByDate[$dateKey] = array(
                    'intramuralGrantApplicationsNumber' => 0,
                    'intramuralTuitionApplicationsNumber' => 0,
                    'extramuralGrantApplicationsNumber' => 0,
                    'extramuralTuitionApplicationsNumber' => 0,
                );

            $applicationType = ($a['applicationStudyMode'] == StudyMode::INTRAMURAL) ? 'intramural' : 'extramural';
            $applicationType .= ($a['applicationFundingType'] == ApplicationFundingType::GRANT) ? 'Grant' : 'Tuition';
            $applicationType .= 'ApplicationsNumber';

            $applicationsByDate[$dateKey][$applicationType]++;
        }

        return array(
            'allApplicationsNumber' => $allApplicationsNumber,
            'top5Specialties' => $top5specialties,
            'applicationsByDate' => $applicationsByDate,
        );
    }

    public static function getAdmissionPlanByOrganizations($admissionPlanByOrganizations)
    {
        $result = array();

        // Необходимо сгруппировать данные по программе образования, и к каждой прицепить список организаций

        foreach ($admissionPlanByOrganizations as $plan) {
            $planKey = self::getFullEducationProgramHash($plan);

            if (!isset($result[$planKey])) {
                $result[$planKey] = array(
                    'trainingLevel' => $plan['trainingLevel'],
                    'studyPeriod' => $plan['studyPeriod'],
                    'studyMode' => $plan['studyMode'],
                    'specialtyId' => $plan['specialtyId'],
                    'specialtyTitle' => $plan['specialtyTitle'],
                    'specialtyCode' => $plan['specialtyCode'],
                    'baseEducation' => $plan['baseEducation'],
                    'organizations' => array(),
                );
            }

            $result[$planKey]['organizations'][$plan['organizationId']] = array(
                'name' => $plan['organizationName'],
                'regionAreaId' => $plan['regionAreaId'],
                'grantStudentsNumber' => $plan['grantStudentsNumber'],
                'tuitionStudentsNumber' => $plan['tuitionStudentsNumber'],
                'admissionPlanStatus' => $plan['admissionPlanStatus'],
                'admissionPlanId' => $plan['admissionPlanId'],
            );
        }

        return $result;

    }

    public static function getAdmissionPlanWithApplicationsNumber($admissionPlan, $applicationsStat)
    {
        $result = array();
        $applicationsStatIndexed = array();

        // Проиндексируем оба массива по образовательной программе для последующего склеивания
        foreach ($applicationsStat as $applications)
        {
            $programKey = self::getFullEducationProgramHash($applications);
            $applicationsStatIndexed[$programKey] = $applications;
        }

        foreach ($admissionPlan as $plan) {
            $programKey = self::getFullEducationProgramHash($plan);

            $result[$programKey] = array(
                'trainingLevel' => $plan['trainingLevel'],
                'studyPeriod' => $plan['studyPeriod'],
                'studyMode' => $plan['studyMode'],
                'specialtyId' => $plan['specialtyId'],
                'specialtyTitle' => $plan['specialtyTitle'],
                'specialtyCode' => $plan['specialtyCode'],
                'baseEducation' => $plan['baseEducation'],
                'grantStudentsNumber' => $plan['grantStudentsNumber'],
                'tuitionStudentsNumber' => $plan['tuitionStudentsNumber'],
                'grantApplicationsNumber' => 0,
                'paidApplicationsNumber' => 0,
                'grantDemand' => 0,
                'paidDemand' => 0,
            );

            if (isset($applicationsStatIndexed[$programKey]))
            {
                $result[$programKey]['grantApplicationsNumber'] = $applicationsStatIndexed[$programKey]['grantApplicationsNumber'];
                $result[$programKey]['paidApplicationsNumber'] = $applicationsStatIndexed[$programKey]['paidApplicationsNumber'];

                $result[$programKey]['grantDemand'] = round($applicationsStatIndexed[$programKey]['grantApplicationsNumber'] / $plan['grantStudentsNumber'], 2);
                $result[$programKey]['paidDemand'] = round($applicationsStatIndexed[$programKey]['paidApplicationsNumber'] / $plan['tuitionStudentsNumber'], 2);
            }

        }

        return $result;

    }

    public static function getGeneralStatData($admissionPlanStatData, array $availableSpecialtiesWithQualifications)
    {
        $result = array();
        $availableSpecialtiesWithQualifications = $availableSpecialtiesWithQualifications['list'];

        // Необходимо сгруппировать данные по базовому образованию (11\9 кл)
        // Преобразуем массив, сгруппировав специальности нужным образом
        foreach ($admissionPlanStatData as $plan) {
            $planKey = self::getBaseEducationPlanHash($plan);
            if (!isset($result[$planKey])) {
                $result[$planKey] = array(
                    'trainingLevel' => $plan['trainingLevel'],
                    'studyPeriod' => $plan['studyPeriod'],
                    'studyMode' => $plan['studyMode'],
                    'specialtyId' => $plan['specialtyId'],
                    'specialtyTitle' => $plan['specialtyTitle'],
                    'specialtyCode' => $plan['specialtyCode'],
                    'qualifications' => array(),
                    // 9 классов
                    'baseEducationBasic' => array(),
                    // 11 классов
                    'baseEducationSecondary' => array(),
                );
            }

            if ($plan['baseEducation'] == BaseEducation::BASIC)
            {
                $result[$planKey]['baseEducationBasic']['grantStudentsNumber'] = $plan['grantStudentsNumber'];
                $result[$planKey]['baseEducationBasic']['tuitionStudentsNumber'] = $plan['tuitionStudentsNumber'];
            } elseif ($plan['baseEducation'] == BaseEducation::SECONDARY) {
                $result[$planKey]['baseEducationSecondary']['grantStudentsNumber'] = $plan['grantStudentsNumber'];
                $result[$planKey]['baseEducationSecondary']['tuitionStudentsNumber'] = $plan['tuitionStudentsNumber'];
            }


            // Подставляем квалификации для каждой специальности
            foreach ($availableSpecialtiesWithQualifications as $specialtyWithQualifications) {
                if ($specialtyWithQualifications['id'] != $plan['specialtyId'])
                    continue;

                foreach ($specialtyWithQualifications['qualifications'] as $q) {
                    $result[$planKey]['qualifications'][$q['id']] = $q['title'];
                }

            }
        }

        return $result;
    }

    private static function getBaseEducationPlanHash($plan)
    {
        return $plan['specialtyCode'] . '#' . $plan['studyMode'] . '#' . $plan['studyPeriod'] . '#' . $plan['trainingLevel'];
    }

    private static function getFullEducationProgramHash($plan)
    {
        return $plan['specialtyCode'] . '#' . $plan['studyMode'] . '#' . $plan['studyPeriod'] . '#' . $plan['trainingLevel'] . $plan['baseEducation'];
    }

}