<?php
namespace Spo\Site\Domains;

use Spo\Site\Core\SPODomain;
use D;
use Spo\Site\Doctrine\Repositories\AdmissionPlanStatRepository;
use Spo\Site\Util\CVarDumper;


class AdmissionPlanStatDomain extends SPODomain {

    public static function getAdmissionPlanByOrganizations($filter)
    {
        if (!isset($filter['year']))
            $filter['year'] = date('Y');

        $repository = AdmissionPlanStatRepository::create()
            ->getAdmissionPlanByOrganization()
            ->filterByAdmissionPlanYear($filter['year']);

        $repository->applyFilter($filter);

        $result = $repository->executeQuery();

        return $result;
    }

    public static function getApplicationsWithSpecialtiesByYear($year = null)
    {
        if (!$year)
            $year = date('Y');

        return AdmissionPlanStatRepository::create()->getApplicationsWithSpecialtiesByYear($year)->executeQuery();

    }

    public static function getGeneralApplicationsNumber($filter)
    {
        if (!isset($filter['year']))
            $filter['year'] = date('Y');

        $repository = AdmissionPlanStatRepository::create()
            ->getGeneralApplicationsNumber()
            ->filterByAdmissionPlanYear($filter['year']);

        $repository->applyFilter($filter);

        $result = $repository->executeQuery();

        return $result;
    }

    public static function getAllAdmissionPlans($year = null)
    {
        $admissionPlans = AdmissionPlanStatRepository::create()->getCommonQuery();

        if ($year)
            $admissionPlans->filterByAdmissionPlanYear($year);

        return $admissionPlans->executeQuery();
    }

    public static function getGeneralStatData(array $filter)
    {
        if (!isset($filter['year']))
            $filter['year'] = date('Y');

        $repository = AdmissionPlanStatRepository::create()
            ->getAdmissionPlanGeneralStat()
            ->filterByAdmissionPlanYear($filter['year']);

        $repository->applyFilter($filter);

        $result = $repository->executeQuery();

        return $result;
    }

    // TODO Временный вспомогательный метод. Скорее всего, варианты периодов обучения будут предопределены
    // TODO пока что же выбираем из базы все существующие, чтобы можно ыло сделать фильтр
    public static function getExistingStudyPeriods()
    {
        $queryBuilder = D::$em->createQueryBuilder();

        $organizationSpecialties = $queryBuilder
            ->select('DISTINCT OrganizationSpecialty.studyPeriod')
            ->from('Spo\Site\Doctrine\Entities\OrganizationSpecialty', 'OrganizationSpecialty')
            ->getQuery()
            ->execute();

        $result = array();
        foreach ($organizationSpecialties as $organizationSpecialty)
            $result[] = $organizationSpecialty['studyPeriod'];

        return $result;

    }

}