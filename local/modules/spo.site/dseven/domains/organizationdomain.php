<?php
namespace Spo\Site\Domains;

use Spo\Site\Core\SPODomain;
use Spo\Site\Dictionaries\OrganizationStatus;
use Spo\Site\Doctrine\Entities\Organization;
use Spo\Site\Doctrine\Entities\OrganizationEmployee;
use Spo\Site\Doctrine\Repositories\OrganizationEmployeeRepository;
use Spo\Site\Doctrine\Repositories\OrganizationRepository;
use Spo\Site\Util\CVarDumper;
use Symfony\Component\Validator\ConstraintViolation;
use Spo\Site\Domains\ApplicationDomain;
use Spo\Site\Domains\RegionDomain;
use Bitrix\Main;
use Bitrix\Main\SystemException;
use Spo\Site\Helpers\PagingHelper;
use D;
use Spo\Site\Doctrine\Entities\BitrixUser;
use Spo\Site\Doctrine\Entities\Specialty;
use Spo\Site\Doctrine\Entities\OrganizationSpecialty;
use Spo\Site\Doctrine\Repositories\SpecialtyRepository;
use Spo\Site\Exceptions\DomainException;

class OrganizationDomain extends SPODomain
{
	/** @var  Organization */
	protected $entity;

    public static function loadById($organizationId)
    {
        $organization = OrganizationRepository::create()
            ->getOrganization($organizationId)
            ->one();

        if ($organization === null){throw DomainException::domainNotFound($organizationId);}

        return new self($organization);
    }

	/**
	 * @return OrganizationDomain
	 */
	public static function createOrganization()
	{
		$organization = new Organization();
        $organization->setStatus(OrganizationStatus::DISABLED);

		$organizationDomain = new OrganizationDomain($organization);
		$organizationDomain->persistEntity($organization);

		return $organizationDomain;
	}

	public function createOrganizationEmployee($userId)
	{
		$user = D::$em->getRepository('Spo\Site\Doctrine\Entities\BitrixUser')->find($userId);

		if ($user === null)
			throw new Main\SystemException('Not Found Exception');

		$organizationEmployee = new OrganizationEmployee();
		$organizationEmployee->setUser($user);
		$organizationEmployee->setOrganization($this->entity);
		$this->persistEntity($organizationEmployee);

		return new OrganizationDomain($organizationEmployee);
	}

    public static function loadByEmployeeUserId($userId)
    {
        $organization = OrganizationRepository::create()
            ->getOrganization()
            ->byEmployeeUserId($userId)
            ->withCity()
            ->withRegionArea()
            ->one();

        if ($organization === null){
            throw new Main\SystemException('Not Found Exception');
        }

        return new self($organization);
    }

    public function getOrganizationSpecialties()
    {
        return $this->entity->getSpecialties();
    }

    public function getOrganizationPages()
    {
        return $this->entity->getOrganizationPages();
    }

    public function setOrganizationSpecialties($specialties)
    {
        $result = $this->entity->setSpecialties($specialties);
        $this->persistEntity($this->entity);
        return $result;
    }

//    public function unbindSpecialtyById($id)
//    {
//        $organizationSpecialties = $this->getOrganizationSpecialties();
//        /* @var OrganizationSpecialty $organizationSpecialty */
//        foreach($organizationSpecialties as $organizationSpecialty){
//            if($organizationSpecialty->getSpecialtyId() === $id){
//                $this->removeEntity($organizationSpecialty);
//            }
//        }
//    }
//
//    public function bindSpecialtyById($specialtyId, $data)
//    {
//        $specialty = SpecialtyRepository::create()
//            ->getSpecialties()
//            ->bySpecialtyId($specialtyId)
//            ->one();
//
//        if($specialty === null){
//            return false;
//        }
//
//        return $this->bindSpecialtyByModel($specialty, $data);
//    }
//
//    public function bindSpecialtyByModel(Specialty $specialty, $data)
//    {
//        $organizationSpecialties = $this->getOrganizationSpecialties();
//
//        /* @var OrganizationSpecialty $organizationSpecialty */
//        foreach($organizationSpecialties as $organizationSpecialty){
//            if($organizationSpecialty->getSpecialtyId() === $specialty->getId()){
//                return false;
//            }
//        }
//
//        $newOrganizationSpecialty = new OrganizationSpecialty();
//        $newOrganizationSpecialty->setSpecialty($specialty);
//        $newOrganizationSpecialty->setOrganization($this->entity);
//        $newOrganizationSpecialty->setStudyMode($data['studyMode']);
//        $newOrganizationSpecialty->setStatus(1);
//        $newOrganizationSpecialty->setBaseEducation($data['baseEducation']);
//
//        $organizationSpecialties[] = $newOrganizationSpecialty;
//
//        return $this->setOrganizationSpecialties($organizationSpecialties);
//    }

	public static function getOrganizationWithSpecialties($organizationId, $onlyActiveSpecialties = false)
	{
		$organization = OrganizationRepository::create()
            ->getOrganization($organizationId)
            ->withSpecialties($onlyActiveSpecialties)
            ->withSpecialtiesActualAdmissionPlan()
            ->one();

		if ($organization === null){
			throw new Main\SystemException('Not Found Exception');
        }

		return new OrganizationDomain($organization);
	}

    public static function getEnabledOrganizations()
    {
        $organizations = OrganizationRepository::create()
            ->getOrganizations()
            ->filterByStatus(OrganizationStatus::ENABLED)
            ->all();

        return new OrganizationDomain(null, $organizations);
    }

	public static function getOrganizationsList($filter = null, $byStatus = null)
	{
		$organizationRepository = OrganizationRepository::create()->getOrganizations()->withCity()->withSpecialties();
        if (!empty($byStatus))
            $organizationRepository = $organizationRepository->filterByStatus($byStatus);

		if (!empty($filter))
		{
			if (!empty($filter['name']))
				$organizationRepository->filterByOrganizationName($filter['name']);

			if (!empty($filter['city']))
				$organizationRepository->filterByOrganizationCityId($filter['city']);

			if(!empty($filter['specialty']))
				$organizationRepository->filterBySpecialtiesIds($filter['specialty']);

			if(!empty($filter['studyMode']))
				$organizationRepository->filterByOrganizationSpecialtyStudyMode($filter['studyMode']);

            if(!empty($filter['baseEducation']))
                $organizationRepository->filterByOrganizationSpecialtyBaseEducation($filter['baseEducation']);

            if(!empty($filter['trainingType']))
                $organizationRepository->filterByOrganizationSpecialtyTrainingType($filter['trainingType']);

            if (!empty($filter['adaptationType']))
                $organizationRepository->filterByOrganizationSpecialtyAdaptationType($filter['adaptationType']);
        }

		$organizations = $organizationRepository->all();

		return new OrganizationDomain(null, $organizations);
	}

    public static function getSimpleOrganizationsList($paging = null)
    {
        // TODO делаем отдельный метод для получения списка организаций с постраничной разбивкой. В методе
        // TODO getOrganizationsList использование ->withSpecialties() ломает постраничную разбивку, так как приJOINиваются
        // TODO лишние строки. Нужно пересматривать принципы действия методов репозиториев withN и filterByN
        $organizationRepository = OrganizationRepository::create()
            ->getOrganizations()
            ->withCity()
            ->filterByStatus(OrganizationStatus::ENABLED);

        if (empty($paging))
            $paging = new PagingHelper();

        $organizations = $organizationRepository->paging($paging)->all();

        return new OrganizationDomain(null, $organizations);
    }

    public static function getOrganizationsRegionMapData()
    {
        $mapData = OrganizationRepository::create()->getOrganizationsRegionMapData();
        return $mapData;
    }

    /**
     * Проверка, является ли полльзователь сотрудником организации
     * @param int $userId
     * @param int $organizationId
     */
    public static function checkIfUserIsOrganizationEmployee($userId, $organizationId)
    {

    }

    public static function checkIfUserIsEmployeeOfAnyOrganization($userId)
    {
        return OrganizationEmployeeRepository::create()
            ->isUserEmployeeOfAnyOrganization($userId);
    }

    public function getOrganizationId()
    {
        return $this->entity->getOrganizationId();
    }

	public function getOrganizationName()
	{
		return $this->entity->getOrganizationName();
	}

	public function getOrganizationAddress($withCity = false)
	{
		$organizationAddress = $this->entity->getOrganizationAddress();

		if ($withCity)
			$organizationAddress = $this->entity->getCity()->getCityName() . ', ' . $organizationAddress;

		return $organizationAddress;
	}

    public function getApplicationList(PagingHelper $paging)
    {
        return ApplicationDomain::getOrganizationApplicationList($this->getOrganizationId(), $paging, true);
    }

    public function populate($data)
    {
        if(isset($data['organizationName'])){
            $this->entity->setOrganizationName($data['organizationName']);
        }

        if(isset($data['organizationFullName'])){
            $this->entity->setOrganizationFullName($data['organizationFullName']);
        }

        if(isset($data['organizationFoundationYear'])){
            $this->entity->setOrganizationFoundationYear($data['organizationFoundationYear']);
        }

        if(isset($data['organizationAddress'])){
            $this->entity->setOrganizationAddress($data['organizationAddress']);
        }

        if(isset($data['organizationEmail'])){
            $this->entity->setOrganizationEmail($data['organizationEmail']);
        }

        if(isset($data['organizationPhone'])){
            $this->entity->setOrganizationPhone($data['organizationPhone']);
        }

        if(isset($data['organizationSite'])){
            $this->entity->setOrganizationSite($data['organizationSite']);
        }

        if(isset($data['organizationCoordinateX'])){
            $this->entity->setOrganizationCoordinateX($data['organizationCoordinateX']);
        }

        if(isset($data['organizationCoordinateY'])){
            $this->entity->setOrganizationCoordinateY($data['organizationCoordinateY']);
        }

        if(isset($data['regionArea'])){
            $regionDomain = RegionDomain::getByRegionAreaId($data['regionArea']);
            $regionArea = $regionDomain->getRegionAreaIdByRegionAreaId(intval($data['regionArea']));
            $this->entity->setRegionArea($regionArea);
        }

        if(isset($data['city'])){
            $cityDomain = CityDomain::getById($data['city']);
            $city = $cityDomain->getModel();
            $this->entity->setCity($city);
        }

        $this->persistEntity($this->entity);
    }

    public function save($validate = true)
    {
        if(($validate && parent::validate()) || !$validate){
            return parent::save();
        }

        return false;
    }

    public function isAbiturientApplicationCreationPossible()
    {
        $result = true;

        $activeOrganizationSpecialtiesCount = 0;
        foreach ($this->entity->getSpecialties() as $organizationSpecialty) {
            /** @var OrganizationSpecialty $organizationSpecialty */
            if ($organizationSpecialty->getStatus()) {
                $activeOrganizationSpecialtiesCount++;
            }
        }

        if ($activeOrganizationSpecialtiesCount == 0)
            $result = false;

        if ($this->entity->getStatus() != OrganizationStatus::ENABLED)
            $result = false;

        return $result;
    }

    public function getStatus()
    {
        return $this->entity->getStatus();
    }
}