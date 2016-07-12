<?php
namespace Spo\Site\Domains;

use Spo\Site\Core\SPODomain;
use Spo\Site\Dictionaries\ApplicationEventReason;
use Spo\Site\Dictionaries\ApplicationPriority;
use Spo\Site\Doctrine\Entities\ApplicationEvent;
use Spo\Site\Doctrine\Repositories\AdmissionPlanRepository;
use Spo\Site\Doctrine\Repositories\ApplicationEventRepository;
use Spo\Site\Doctrine\Repositories\ApplicationRepository;
use Spo\Site\Doctrine\Entities\Application;
use Spo\Site\Helpers\PagingHelper;
use Spo\Site\Util\CVarDumper;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Validator\ConstraintViolation;
use Bitrix\Main;
use Bitrix\Main\SystemException;
//use D;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Doctrine\Entities\BitrixUser;
use Spo\Site\Doctrine\Entities\OrganizationSpecialty;
//use Doctrine\ORM\AbstractQuery;
use Spo\Site\Dictionaries\ApplicationFundingType;
//use Doctrine\ORM\Tools\Pagination\Paginator;
use Spo\Site\Util\ApplicationListFilter;
use Spo\Site\Doctrine\Entities\AdmissionPlan;
use Spo\Site\Exceptions\AccessException;

class ApplicationDomain extends SPODomain {

	/** @var  Application */
	protected $entity;
    public $applicationEvents;
    public $lastApplicationEvent = null;

	public function getApplicationId()
	{
		/** @var Application $applicationModel */
		$applicationModel = $this->entity;
		return $applicationModel->getId();
	}

	public static function getUserApplicationsList($userId)
	{
		$application = ApplicationRepository::create()->getApplications()
			->ownedByUser($userId)
			->onlyActive()
			->withOrganizationSpecialty()
			->withOrganization()
			->all();

		if ($application === null)
			throw new Main\SystemException('Not Found Exception');

		return $application;
	}

	/**
	 * Возвращает заявку пользователя с указанным идентификатором и статусом отличным от "Удалена",
	 * @param integer $userId идентификато рпользователя
	 * @param integer $applicationId идентификатор заявки
	 * @return ApplicationDomain
	 * @throws SystemException
	 */
	public static function getUserApplication($userId, $applicationId)
	{
		/** @var ApplicationRepository $applicationRepository */
		$applicationRepository = D::$em->getRepository('Spo\Site\Doctrine\Entities\Application');
		$application = $applicationRepository->getApplicationById($applicationId)
			->ownedByUser($userId)
			->onlyActive()
			->withOrganizationSpecialty()
			->one();

		if ($application === null)
			throw new Main\SystemException('Not Found');

		return new self($application);
	}

    public static function getOrganizationApplication($organizationId, $applicationId)
    {
        /** @var ApplicationRepository $applicationRepository */
        /*$applicationRepository = D::$em->getRepository('Spo\Site\Doctrine\Entities\Application');
        $application = $applicationRepository->getApplicationById($applicationId)
            ->byOrganizationId($organizationId)
            ->withOwner()
            ->withOrganizationSpecialty()
            ->one();

        if ($application === null) {
            throw new Main\SystemException('Not Found');
        }*/

        return new self($application);
    }

    public function loadApplicationEvents()
    {
        $applicationEvents = ApplicationEventRepository::create()->getApplicationEvents($this->getApplicationId())->all();
        $this->applicationEvents = $applicationEvents;
    }

    /**
     * @param int $userId идентификатор пользователя
     * @param int $applicationId идентификатор заявки
     * @return ApplicationDomain
     * @throws \Bitrix\Main\SystemException
     */
    public static function deleteUserApplication($userId, $applicationId)
	{
        /** @var ApplicationRepository $applicationRepository */
        $applicationRepository = D::$em->getRepository('Spo\Site\Doctrine\Entities\Application');
        $application = $applicationRepository->getApplicationById($applicationId)
            ->ownedByUser($userId)
            ->onlyActive()
            ->withOrganizationSpecialty()
            ->one();

        if ($application === null)
            throw new Main\SystemException('Not Found');

		$application->setApplicationStatus(ApplicationStatus::DELETED);

        $applicationDomain = new ApplicationDomain($application);
        $applicationDomain->persistEntity($applicationDomain->getModel());

        $applicationEvent = new ApplicationEvent();
        $applicationEvent->setComment('');
        $applicationEvent->setApplication($applicationDomain->getModel());
        $applicationEvent->setStatus(ApplicationStatus::DELETED);
        $applicationEvent->setReason(ApplicationEventReason::CANCELED_BY_ABITURIENT);

        $applicationDomain->persistEntity($applicationEvent);
        $applicationDomain->lastApplicationEvent = $applicationEvent;

		return $applicationDomain;
	}

//    public function getPriorityForNewUserApplication($organizationId, $userId)
//    {
//        $userApplicationsNumber = ApplicationRepository::create()
//        ->byOrganizationId($organizationId)
//        ->filterByUserId($userId)
//        ->count();
//
//        if (empty($userApplications))
//            return ApplicationPriority::HIGH;
//
//        if ($userApplicationsNumber == 1)
//            $t = 1;
//
//
//
//    }

    /**
     * @param integer $userId идентификатор пользователя
     * @param array $applicationData данные заявки
     * @return ApplicationDomain
     * @throws \Spo\Site\Exceptions\AccessException
     * @throws \Bitrix\Main\SystemException
     */
    public static function createAbiturientApplication($userId, $applicationData)
	{
        if (!isset($applicationData['organizationSpecialtyId']))
            throw new Main\SystemException('Not Found');

        /** @var OrganizationSpecialty $organizationSpecialty */
		/** @var BitrixUser $bitrixUser */
		$organizationSpecialtyRepository = D::$em->getRepository('Spo\Site\Doctrine\Entities\OrganizationSpecialty');
		$organizationSpecialty = $organizationSpecialtyRepository->findOneBy(array(
			'organizationSpecialtyId' => $applicationData['organizationSpecialtyId'],
			'status' => true,
		));

		$bitrixUser = D::$em->getRepository('Spo\Site\Doctrine\Entities\BitrixUser')->find($userId);

		if (!$organizationSpecialty || !$bitrixUser)
			throw new Main\SystemException('Not Found');

        $actualAdmissionPlan = AdmissionPlanRepository::create()
            ->getActualAdmissionPlanByOrganizationSpecialtyId($organizationSpecialty->getOrganizationSpecialtyId())
            ->one();

        /** @var AdmissionPlan $actualAdmissionPlan */
        if (
            empty($actualAdmissionPlan)
            || !$actualAdmissionPlan->isAccepted()
            || !$actualAdmissionPlan->isStarted()
            || $actualAdmissionPlan->isFinished()
        )
        {
            throw new AccessException('Не найден актуальный план приёма для подачи заявки');
        }

        if (!ApplicationFundingType::isDefined($applicationData['fundingType']))
            $applicationData['fundingType'] = ApplicationFundingType::GRANT;

        if (!isset($applicationData['needHostel']))
            $applicationData['needHostel'] = false;

		$userApplication = new Application();
		$userApplication->setUser($bitrixUser);
		$userApplication->setOrganization($organizationSpecialty->getOrganization());
		$userApplication->setOrganizationSpecialty($organizationSpecialty);
		$userApplication->setCreationDate(new \DateTime);
		$userApplication->setApplicationStatus(ApplicationStatus::CREATED);
		$userApplication->setFundingType($applicationData['fundingType']);
		$userApplication->setNeedHostel($applicationData['needHostel']);

        // TODO
        $userApplication->setPriority(ApplicationPriority::HIGH);
        $userApplication->setAdmissionPlan($actualAdmissionPlan);

		$applicationDomain = new ApplicationDomain($userApplication);
		$applicationDomain->persistEntity($userApplication);

        $applicationEvent = new ApplicationEvent();
        $applicationEvent->setStatus(ApplicationStatus::CREATED);
        $applicationEvent->setApplication($userApplication);
        $applicationEvent->setComment('');
        $applicationDomain->persistEntity($applicationEvent);

        $applicationDomain->lastApplicationEvent = $applicationEvent;

		return $applicationDomain;

	}

    public static function getOrganizationApplicationList($organizationId, PagingHelper $paging, ApplicationListFilter $filter = null)
    {
        $repo = ApplicationRepository::create()
            ->getApplications()
            ->withOwner()
            ->withOrganizationSpecialty()
            ->byOrganizationId($organizationId);
        
        /*
           ->select('Application');
           ->addSelect('User')
           ->innerJoin('Application.user', 'User');
           ->addSelect('OrganizationSpecialty, Specialty')
           ->innerJoin('Application.organizationSpecialty', 'OrganizationSpecialty')
           ->innerJoin('OrganizationSpecialty.specialty', 'Specialty');
           ->innerJoin('Application.organization', 'organization')
           ->andWhere('organization.organizationId = :organizationId')
           ->setParameter('organizationId', $organizationId);
        */
        if($filter !== null)
        {
            if($filter->wasYearSet()){
                $repo->filterByYear($filter->getYear());
            }

            if($filter->wasStatusSet()){
                $repo->filterByStatus($filter->getStatus());
            }

            if($filter->wasFundingSet()){
                $repo->filterByFundingType($filter->getFunding());
            }
            
            if($filter->wasSortFieldSet()){
                $repo->orderBy($filter->getSortField(), $filter->getOrderBy());
            }else{
                $repo->orderByApplicationId($filter->getOrderBy());
            }
        }

        //echo '<pre>';
        //var_dump($repo->queryBuilder->getQuery()->getSQL());
        //var_dump($repo->all(AbstractQuery::HYDRATE_ARRAY));exit('</pre>');
        $pagination = new Paginator($repo->queryBuilder);
        $totalCount = count($pagination);
        //print_r($totalCount);
        if ($totalCount < $paging->getPageSize() * $paging->getCurrentPage() && (($totalCount % $paging->getPageSize()) == 0))
            $paging->setPageParam(1);

        $entities = $repo->paging($paging)->all();
        return new self(null, $entities, $totalCount);
    }

    public static function loadById($applicationId, $organizationId = 0)
    {
        $repo = ApplicationRepository::create()
            ->getApplicationById($applicationId)
            ->withOwner()
            ->withOrganizationSpecialty();

        if($organizationId > 0)
        {
            $repo->byOrganizationId($organizationId);
        }

        $entity = $repo->one();

        if($entity === null)
        {
            throw new NotFoundResourceException('application not found');
        }

        return new self($entity);
    }

    public function updateApplication($applicationData)
    {
        /** @var Application $application */
        $application = $this->getModel();

        if ($application->getStatus() != ApplicationStatus::CREATED && $application->getStatus() != ApplicationStatus::RETURNED) {
            $this->addError('Редактирование данной заявки недоступно');
            return;
        }

        if (isset($applicationData['fundingType']))
            $application->setFundingType($applicationData['fundingType']);

        if (isset($applicationData['needHostel']))
            $application->setNeedHostel(true);
        else
            $application->setNeedHostel(false);


        $application->setApplicationStatus(ApplicationStatus::CREATED);

        $applicationEvent = new ApplicationEvent();
        $applicationEvent->setComment($applicationData['applicationEventComment'] ? $applicationData['applicationEventComment'] : '');
        $applicationEvent->setApplication($application);
        $applicationEvent->setStatus($application->getStatus());

        $this->persistEntity($application);
        $this->persistEntity($applicationEvent);

        $this->lastApplicationEvent = $applicationEvent;
    }

    public function changeStatus($status, $comment = '', $reason = ApplicationEventReason::NONE)
    {
        /* @var Application $application */
        $application = $this->entity;
        $oldStatus = $application->getApplicationStatus();

        if (!ApplicationStatus::isDefined($status))
            throw new \Exception('wrong status');

        if (!ApplicationStatus::canChangeStatus($oldStatus, $status)) {
            $this->addError('Нельзя поменять статус заявки с "' . ApplicationStatus::getValue($oldStatus) . '" на "' . ApplicationStatus::getValue($status) . '"');
            return;
        }

        $application->setApplicationStatus($status);
        $this->persistEntity($application);

        $applicationEvent = new ApplicationEvent();
        $applicationEvent->setApplication($application);
        $applicationEvent->setComment($comment);
        $applicationEvent->setReason($reason);
        $applicationEvent->setStatus($application->getStatus());

        $this->persistEntity($applicationEvent);
        $this->lastApplicationEvent = $applicationEvent;
    }

    /**
     * @return null|ApplicationEvent
     */
    public function getGeneratedApplicationEvent()
    {
        return ($this->lastApplicationEvent instanceof ApplicationEvent) ? $this->lastApplicationEvent : null;
    }

	public function getApplicationOrganizationId()
	{
		return $this->entity->getOrganization()->getOrganizationId();
	}

    public function getUserId()
    {
        return $this->entity->getUser()->getId();
    }

    public function getApplicationStatus()
    {
        return $this->entity->getApplicationStatus();
    }

	public function getApplicationSpecialtyTitle()
	{
		return $this->entity->getOrganizationSpecialty()->getSpecialty()->getTitle();
	}

}