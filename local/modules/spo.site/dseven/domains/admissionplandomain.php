<?php
namespace Spo\Site\Domains;

use Spo\Site\Core\SPODomain;
use D;
use Spo\Site\Dictionaries\AdmissionPlanStatus;
use Spo\Site\Doctrine\Entities\AdmissionPlan;
use Spo\Site\Doctrine\Entities\AdmissionPlanEvent;
use Spo\Site\Doctrine\Repositories\AdmissionPlanEventRepository;
use Spo\Site\Doctrine\Repositories\AdmissionPlanRepository;
use Spo\Site\Doctrine\Repositories\OrganizationSpecialtyRepository;
use Spo\Site\Exceptions\AccessException;
use Spo\Site\Exceptions\ArgumentException;
use Spo\Site\Doctrine\Entities\OrganizationSpecialty;
use Spo\Site\Exceptions\DomainException;
use Spo\Site\Util\CVarDumper;
use Spo\Site\Entities\AdmissionPlanTable;


class AdmissionPlanDomain extends SPODomain {

    const COMMENT_FROM_DEPARTMENT = 1;
    const COMMENT_FROM_ORGANIZER = 2;

    public $admissionPlanEvents = null;

    public static function loadByOrganizationToYear($organizationId, $year)
    {
        $admissionPlans = AdmissionPlanRepository::create()
            ->getAdmissionPlan()
            ->filterByYear($year)
            ->filterByOrganizationId($organizationId)
            ->all();

        $domain = new self(null, $admissionPlans);
        return $domain;
    }

    public static function loadAdmissionPlanInfoById($admissionPlanId)
    {
        $admissionPlan = AdmissionPlanRepository::create()->loadAdmissionPlanInfoById($admissionPlanId)->one();
        if (!$admissionPlan)
            throw DomainException::domainNotFound($admissionPlanId);

        $domain = new self($admissionPlan);
        $domain->admissionPlanEvents = AdmissionPlanEventRepository::create()->getAdmissionPlanEvents($admissionPlanId)->all();

        return $domain;
    }

    /**
     * @param $organizationId
     * @param array $admissionPlanData
     * @return AdmissionPlanDomain
     * @throws \Spo\Site\Exceptions\AccessException
     * @throws \Spo\Site\Exceptions\ArgumentException
     */
    public static function createOrUpdate($organizationId, array $admissionPlanData)
    {
        if (!isset($admissionPlanData['organizationSpecialtyId']))
            $organizationSpecialtyId = null;
        else
            $organizationSpecialtyId = $admissionPlanData['organizationSpecialtyId'];

        /** @var OrganizationSpecialty $organizationSpecialty */
        $organizationSpecialty = OrganizationSpecialtyRepository::create()->findOneBy(
            array('organizationId' => $organizationId, 'organizationSpecialtyId' => $organizationSpecialtyId)
        );

        if (empty($organizationSpecialty))
            throw ArgumentException::argumentIncorrect();

        /** @var AdmissionPlan $admissionPlan */
        $admissionPlan = AdmissionPlanRepository::create()
            ->getAdmissionPlan()
            ->filterByOrganizationSpecialtyId($organizationSpecialty->getOrganizationSpecialtyId())
            ->filterByYear($admissionPlanData['year'])
            ->one();

        if (!$admissionPlan) {
            $admissionPlan = new AdmissionPlan();
            $admissionPlan->setOrganizationSpecialty($organizationSpecialty);
        } else {
            // Нельзя изменять план приёма, если он уже одобрен департаментом
            if ($admissionPlan->isAccepted())
                throw new AccessException();
        }

        $admissionPlan->setStatus(AdmissionPlanStatus::CREATED);

        if (isset($admissionPlanData['grantStudentsNumber']))
            $admissionPlan->setGrantStudentsNumber($admissionPlanData['grantStudentsNumber']);

        if (isset($admissionPlanData['tuitionStudentsNumber']))
            $admissionPlan->setTuitionStudentsNumber($admissionPlanData['tuitionStudentsNumber']);

        if (isset($admissionPlanData['startDate'])) {
            try {
                $startDate = new \DateTime($admissionPlanData['startDate']);
            } catch (\Exception $e) {
                $startDate = new \DateTime();
            }

            $admissionPlan->setStartDate($startDate);
        }

        if (isset($admissionPlanData['endDate'])) {
            try {
                $endDate = new \DateTime($admissionPlanData['endDate']);
            } catch (\Exception $e) {
                $endDate = new \DateTime();
            }

            $admissionPlan->setEndDate($endDate);
        }

        $admissionPlanDomain = new AdmissionPlanDomain($admissionPlan);
        $admissionPlanDomain->persistEntity($admissionPlan);

        $planEvent = new AdmissionPlanEvent();
        $planEvent->setAdmissionPlan($admissionPlan);
        $planEvent->setComment($admissionPlanData['reason'] ? $admissionPlanData['reason'] : '');
        $planEvent->setStatus(AdmissionPlanStatus::CREATED);
        $admissionPlanDomain->persistEntity($planEvent);

        return $admissionPlanDomain;
    }

    /**
     * @param $admissionPlanId
     * @param $newAdmissionPlanStatus
     * @param string $reason причина смены статуса
     * @return AdmissionPlanDomain
     * @throws \Spo\Site\Exceptions\ArgumentException
     */
    public static function changeStatus($admissionPlanId, $newAdmissionPlanStatus, $reason = '')
    {
        /** @var AdmissionPlan $admissionPlan */
        $admissionPlan = AdmissionPlanRepository::create()->find($admissionPlanId);

        if (!AdmissionPlanStatus::isDefined($newAdmissionPlanStatus))
            throw ArgumentException::argumentIncorrect();

        if (!$admissionPlan)
            throw ArgumentException::argumentIncorrect();

        $admissionPlan->setStatus($newAdmissionPlanStatus);

        $admissionPlanDomain = new AdmissionPlanDomain($admissionPlan);
        $admissionPlanDomain->persistEntity($admissionPlan);

        $planEvent = new AdmissionPlanEvent();
        $planEvent->setAdmissionPlan($admissionPlan);
        $planEvent->setComment($reason);
        $planEvent->setStatus($newAdmissionPlanStatus);
        $admissionPlanDomain->persistEntity($planEvent);

        return $admissionPlanDomain;
    }

    public function validate()
    {
        /** @var AdmissionPlan $admissionPlan */
        $admissionPlan = $this->getModel();
        $startDate = $admissionPlan->getStartDate();
        $endDate = $admissionPlan->getEndDate();

        if (!($endDate > $startDate))
            $this->addError('Дата завершения должна быть позже даты начала приёма заявок');

        if (($startDate->format('Y') != $endDate->format('Y')))
            $this->addError('Даты завершения и начала должны быть в пределах ' . $startDate->format('Y') . ' года');

        if (($admissionPlan->getGrantStudentsNumber() == 0) && ($admissionPlan->getTuitionStudentsNumber() == 0))
            $this->addError('Число бюджетных или коммерческих мест должно быть целым положительным числом');

        if (($admissionPlan->getGrantStudentsNumber() < 0) || ($admissionPlan->getTuitionStudentsNumber() < 0))
            $this->addError('Число бюджетных или коммерческих не может быть меньше нуля');

        return parent::validate();
    }

}