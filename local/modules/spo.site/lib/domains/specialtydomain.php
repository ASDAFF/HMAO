<?php
namespace Spo\Site\Domains;

use Doctrine\ORM\AbstractQuery;
use Spo\Site\Helpers\PagingHelper;
use Spo\Site\Core\SPODomain;
use Spo\Site\Doctrine\Repositories\SpecialtyRepository;
use Spo\Site\Util\CVarDumper;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Spo\Site\Doctrine\Entities\Specialty;
use Bitrix\Main;
use Spo\Site\Exceptions\DomainException;
use Spo\Site\Domains\OrganizationSpecialtyDomain;
use Spo\Site\Doctrine\Entities\Qualification;


class SpecialtyDomain extends SPODomain
{

	public static function loadById($specialtyId, $includeQualifications = false)
	{
		$repo = SpecialtyRepository::create()
            ->getSpecialties()
            ->withGroup()
            ->bySpecialtyId($specialtyId);

        if($includeQualifications)
        {
            $repo->withQualifications();
        }

        $specialty = $repo->one();
        if($specialty === null){ throw DomainException::domainNotFound($specialtyId);}

		return new SpecialtyDomain($specialty);
	}

    /**
     * @param bool $includeQualifications включить выборку квалификаций специальности
     * @param bool $onlyRelatedToOrganizations выбирать только специальности, для которых существуют программы обучения
     * @return SpecialtyDomain
     */
    public static function getSpecialtiesList($includeQualifications = false, $onlyRelatedToOrganizations = false)
    {
        $repository = SpecialtyRepository::create()->getSpecialties();
        if ($includeQualifications) {
            $repository->withQualifications();
        }
        if ($onlyRelatedToOrganizations) {
            $repository->onlyRelatedToOrganizationSpecialties();
        }

        $specialties = $repository
            ->withGroup()
            ->orderBy('Specialty.specialtyCode')
            ->all();
        return new SpecialtyDomain(null, $specialties);
    }

    public function getSpecialtiesIdList()
    {
        $result = array();
        /* @var Specialty $specialty */
        foreach($this->entityCollection as $specialty)
        {
            $result[] = $specialty->getId();
        }

        return $result;
    }

    public static function getSpecialtiesListWithOrganizationSpecialtiesByOrganizationId($organizationId, PagingHelper $paging)
    {
        $repo = SpecialtyRepository::create()
            ->getSpecialties()
            ->orderBy('Specialty.specialtyId')
            ->withOrganizationSpecialty()
            ->byOrganizationId($organizationId);

        //CVarDumper::dump($repo->paging($paging)->queryBuilder->getQuery()->getSQL());exit;
        $pagination = new Paginator($repo->queryBuilder);

        $totalCount = count($pagination);
        $entities = $repo->paging($paging)->all();

        return new self(null, $entities, $totalCount);
    }

    public static function getSpecialtyListExceptIds($specialtyIdList, PagingHelper $paging)
    {
        $repo = SpecialtyRepository::create()
            ->getSpecialties()
            ->orderBy('Specialty.specialtyId')
            ->exceptSpecialtyIds($specialtyIdList);

        $pagination = new Paginator($repo->queryBuilder);

        $totalCount = count($pagination);
        $entities = $repo->paging($paging)->all();

        return new self(null, $entities, $totalCount);
    }

    public static function getSpecialtyWithDescription($specialtyId)
    {
        $specialty = SpecialtyRepository::create()
            ->getSpecialty($specialtyId)
            ->withGroup()
            ->withQualifications()
            ->one();

        if ($specialty === null)
            throw new Main\SystemException('Not Found Exception');

        return new self($specialty);
    }

    /**
     * @return OrganizationSpecialtyDomain
     */
    public function getOrganizationSpecialtyDomain()
    {
        $entities = $this->getEntityCollection();

        $allOrgSpecialties = array();

        /* @var Specialty $specialty */
        foreach($entities as $specialty)
        {
            $orgSpecialties = $specialty->getOrganizationSpecialty();
            foreach($orgSpecialties as $orgSpecialty)
            {
                $allOrgSpecialties []= $orgSpecialty;
            }
        }

        return OrganizationSpecialtyDomain::createByEntityCollection($allOrgSpecialties);
    }

    /**
     * Проверка, все ли квалификации принадлежат специальности
     * @param       $specialtyId
     * @param array $qualificationIdList
     * @return bool
     */
    public static function checkIfQualificationsBelongsToSpecialty($specialtyId, array $qualificationIdList)
    {
        /* @var Specialty $specialty */
        $specialty = SpecialtyRepository::create()
            ->getSpecialties()
            ->withQualifications()
            ->bySpecialtyId($specialtyId)
            ->one();

        $qList = $specialty->getQualifications();

        if($specialty === null){ return false;}

        $found = 0;

        /* @var Qualification $qualification */
        foreach($qList as $qualification)
        {
            if(in_array(intval($qualification->getId()), $qualificationIdList))
            {
                $found++;
            }
        }

        // если количество найденных квалификаций = количеству переданных номинаций, тогда true
        return $found === count($qualificationIdList);
    }

    /**
     * Получить с
     * @param array $idFilterArray
     * @return array
     */
    public function getQualifications(array $idFilterArray = array())
    {
        $result = array();
        /* @var Specialty $specialty */
        $specialty = $this->getModel();

        $qualifications = $specialty->getQualifications();

        if(count($idFilterArray) === 0)
        {
            $result = $qualifications;
        }
        else
        {
            /* @var Qualification $qualification */
            foreach($qualifications as $qualification)
            {
                if(in_array($qualification->getId(), $idFilterArray))
                {
                    $result[] = $qualification;
                }
            }
        }

        return $result;
    }
}