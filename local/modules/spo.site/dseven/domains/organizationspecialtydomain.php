<?php
namespace Spo\Site\Domains;

use Spo\Site\Doctrine\Entities\OrganizationSpecialtyAdaptation;
use Spo\Site\Helpers\PagingHelper;
use Spo\Site\Core\SPODomain;
use Spo\Site\Doctrine\Repositories\OrganizationSpecialtyRepository;
use Spo\Site\Domains\SpecialtyDomain;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Doctrine\Entities\OrganizationSpecialty;
use Spo\Site\Doctrine\Entities\Organization;
use Spo\Site\Doctrine\Entities\OrganizationSpecialtyExam;
use Spo\Site\Doctrine\Entities\Specialty;
use Spo\Site\Doctrine\Entities\Qualification;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Spo\Site\Exceptions\DomainException;
use Spo\Site\Util\CVarDumper;
use Spo\Site\Util\Methodarguments\EducationalProgramArguments;
use Spo\Site\Util\Methodarguments\OrganizationSpecialtyLoadArguments;


class OrganizationSpecialtyDomain extends SPODomain
{
    const DEFAULT_ORGANIZATION_SPECIALTY_STATUS = 1;
    public static function loadById($orgSpecialtyId)
    {
        $orgSpecialty = OrganizationSpecialtyRepository::create()
            ->getOrganizationSpecialties()
            ->byId($orgSpecialtyId)
            ->one();

        if ($orgSpecialty === null){throw DomainException::domainNotFound($orgSpecialtyId);}

        return new self($orgSpecialty);
    }

    /**
     * Универсальный метод-загрузчик домена. По идее, через него можно загружать основную модель домена
     * в разных конфигурациях. Метод рассчитывается как базовый загрузчик, который будет вызываться
     * другими методами
     * @param OrganizationSpecialtyLoadArguments $args
     * @return OrganizationSpecialtyDomain
     * @throws static
     */
    public static function load(OrganizationSpecialtyLoadArguments $args)
    {
        $repo = OrganizationSpecialtyRepository::create()
            ->getOrganizationSpecialties();

        if($args->isById()){
            $repo->byId($args->getById());
        }

        if($args->isWithExams()){
            $repo->withExams();
        }
        if($args->isWithQualifications()){
            $repo->withQualifications();
        }

        $orgSpecialty = $repo->one();

        if ($orgSpecialty === null){
            throw DomainException::domainNotFound();
        }

        return new self($orgSpecialty);
    }

    public static function createNew()
    {
        return new self(new OrganizationSpecialty());
    }

    public static function createByEntityCollection(array $entities)
    {
        return new self(null, $entities);
    }

    public static function getOrganizationSpecialtiesListByOrganizationId($organizationId, PagingHelper $paging)
    {
        $repo = OrganizationSpecialtyRepository::create()
            ->getOrganizationSpecialties()
            ->withSpecialty()
            ->withOrganization()
            ->byOrganizationId($organizationId)
            ->orderBy('Specialty.specialtyId');

        //CVarDumper::dump($repo->paging($paging)->queryBuilder->getQuery()->getSQL());exit;
        $pagination = new Paginator($repo->queryBuilder);

        $totalCount = count($pagination);
        $entities = $repo->paging($paging)->all();

        return new self(null, $entities, $totalCount);
    }



    public function getOrganizationSpecialtiesApplicationCount()
    {
        return self::getOrganizationSpecialtiesApplicationCountByIds($this->getEntityCollectionIds());
    }

    public static function getOrganizationSpecialtiesApplicationCountByIds(array $organizationSpecialtyIds)
    {
        $result = array();
        if(count($organizationSpecialtyIds) === 0){ return $result;}

        $qResult = OrganizationSpecialtyRepository::create()
            ->getOrganizationSpecialtyApplicationStat($organizationSpecialtyIds);

        foreach($qResult as $qResultItem)
        {
            $result[$qResultItem['id']] = intval($qResultItem['stat']);
        }

        return $result;
    }

    public function getEntityCollectionIds()
    {
        $ids = array();
        $collection = $this->getEntityCollection();

        /* @var OrganizationSpecialty $orgSpecialty */
        foreach($collection as $orgSpecialty){
            $ids []= $orgSpecialty->getOrganizationSpecialtyId();
        }

        return $ids;
    }

    public function getOrganizationId()
    {
        /* @var OrganizationSpecialty $model */
        $model = $this->getModel();
        return $model->getOrganizationId();
    }

    public function getOrganizationSpecialtyId()
    {
        /* @var OrganizationSpecialty $model */
        $model = $this->getModel();
        return $model->getOrganizationSpecialtyId();
    }

    public function getSpecialtyId()
    {
        /* @var OrganizationSpecialty $model */
        $model = $this->getModel();
        return $model->getSpecialtyId();
    }

    /* Прикрепление/открепление специальностей */

    public function unbindSpecialtyFromOrganization()
    {
        $this->removeEntity($this->getModel());
    }

//    protected function canSpecialtyBeBindedToOrganization($organizationId, $specialtyId, $baseEducation, $studyMode)
//    {
//        return OrganizationSpecialtyRepository::create()
//            ->getOrganizationSpecialties()
//            ->withSpecialty()
//            ->withOrganization()
//            ->byOrganizationId($organizationId)
//            ->filterBySpecialtyId($specialtyId)
//            ->filterByBaseEducation($baseEducation)
//            ->filterByStudyMode($studyMode)
//            ->count() === 0;
//    }

    public function addEducationalProgram(EducationalProgramArguments $args)
    {
        $organizationDomain = OrganizationDomain::loadById($args->getOrganizationId());
        $specialtyDomain    = SpecialtyDomain::loadById($args->getSpecialtyId(), true);
        $qualIdList         = $args->getQualificationList();
        $qualifications     = count($qualIdList) > 0 ?
            $specialtyDomain->getQualifications($args->getQualificationList()) : array();
        $examList           = array();
        $exams              = $args->getExamList();

        /* @var OrganizationSpecialty $orgSpecialty */
        $orgSpecialty       = $this->getModel();

        foreach($exams as $examData)
        {
            $exam = new OrganizationSpecialtyExam();
            $exam
                ->setType($examData['type'])
                ->setDiscipline($examData['disciplineId'])
                ->setOrganizationSpecialty($orgSpecialty);

            $examList[] = $exam;
            $this->persistEntity($exam);
        }

        /* @var Specialty $specialty */
        $specialty      = $specialtyDomain->getModel();

        //CVarDumper::dump($args);exit;

        // Обновление данных об адаптации программы обучения для лиц с ОВЗ
        $isAdapted = $args->isAdapted();
        $receivedAdaptationTypes = $args->getAdaptationTypes();

        if ($isAdapted && !empty($receivedAdaptationTypes)) {

            foreach ($receivedAdaptationTypes as $receivedAdaptationType) {
                $newAdaptationType = new OrganizationSpecialtyAdaptation();
                $newAdaptationType->setType($receivedAdaptationType);
                $newAdaptationType->setOrganizationSpecialty($orgSpecialty);
                $this->persistEntity($newAdaptationType);
            }
        }

        $orgSpecialty
            ->setSpecialty($specialty)
            ->setOrganization($organizationDomain->getModel())
            ->setStudyMode($args->getStudyMode())
            ->setStatus(self::DEFAULT_ORGANIZATION_SPECIALTY_STATUS)
            ->setOrganizationSpecialtyQualifications($qualifications)
            ->setOrganizationSpecialtyExams($examList)
            ->setTrainingLevel($args->getTrainingLevel())
            ->setTrainingType($args->getTrainingType())
            ->setPlannedAbiturientsCount($args->getPlannedAbiturientsCount())
            ->setPlannedGroupsCount($args->getPlannedGroupsCount())
            ->setStudyPeriod($args->getStudyPeriod())
            ->setBaseEducation($args->getBaseEducation());

        $this->persistEntity($orgSpecialty);
    }

    public function updateEducationalProgram(EducationalProgramArguments $args)
    {
        $organizationDomain = OrganizationDomain::loadById($args->getOrganizationId());
        $specialtyDomain    = SpecialtyDomain::loadById($args->getSpecialtyId(), true);
        $qualIdList         = $args->getQualificationList();
        $qualifications     = count($qualIdList) > 0 ?
            $specialtyDomain->getQualifications($args->getQualificationList()) : array();
        $examList           = array();
        $exams              = $args->getExamList();

        /* @var OrganizationSpecialty $orgSpecialty */
        $orgSpecialty       = $this->getModel();

        $alreadySavedExams = $orgSpecialty->getOrganizationSpecialtyExams();

        /* @var OrganizationSpecialtyExam $alreadySavedExam */
        /* @var OrganizationSpecialtyExam $exam */
        //CVarDumper::dump($exams);exit;
        foreach($exams as $examData)
        {
            $found = false;
            if(isset($examData['id']))
            {
                foreach($alreadySavedExams as $alreadySavedExam)
                {
                    if($alreadySavedExam->getId() === $examData['id'])
                    {
                        $alreadySavedExam
                            ->setType($examData['type'])
                            ->setDiscipline($examData['disciplineId']);

                        $examList[] = $alreadySavedExam;
                        $found = true;
                        $this->persistEntity($alreadySavedExam);
                        break;
                    }
                }
            }
            if(!$found)
            {
                $exam = new OrganizationSpecialtyExam();
                $exam
                    ->setType($examData['type'])
                    ->setDiscipline($examData['disciplineId'])
                    ->setOrganizationSpecialty($orgSpecialty);

                $examList[] = $exam;
                $this->persistEntity($exam);
            }
        }
        foreach($alreadySavedExams as $alreadySavedExam)
        {
            $found = false;
            foreach($examList as $exam)
            {
                if($alreadySavedExam->getId() === $exam->getId())
                {
                    $found = true;
                }
            }
            if(!$found)
            {
                $this->removeEntity($alreadySavedExam);
            }
        }

        $alreadySavedQualifications = $orgSpecialty->getOrganizationSpecialtyQualifications();

        /* @var Qualification $savedQualification */
        /* @var Qualification $qualification */
        $mergedQualifications = array();
        foreach($qualifications as $qualification)
        {
            $found = false;
            foreach($alreadySavedQualifications as $savedQualification)
            {
                if($qualification->getId() === $savedQualification->getId())
                {
                    $found = true;
                    break;
                }
            }

            if($found)
            {
                $mergedQualifications[] = $savedQualification;
            }
            else
            {
                $mergedQualifications[] = $qualification;
            }
        }

        /* @var Specialty $specialty */
        $specialty      = $specialtyDomain->getModel();


        // Обновление данных об адаптации программы обучения для лиц с ОВЗ
        $isAdapted = $args->isAdapted();
        $receivedAdaptationTypes = $args->getAdaptationTypes();
        $existingAdaptationTypes = $orgSpecialty->getOrganizationSpecialtyAdaptationTypes();
        $adaptationTypesToCreate = array();
        $adaptationTypesToDelete = array();

        if ($isAdapted && !empty($receivedAdaptationTypes)) {

            // Поиск тех, что нужно создать
            foreach ($receivedAdaptationTypes as $receivedAdaptationType) {
                $found = false;

                /** @var OrganizationSpecialtyAdaptation $existing */
                foreach ($existingAdaptationTypes as $existing) {
                    if ($existing->getType() == $receivedAdaptationType) {
                        $found = true;
                        continue;
                    }
                }

                if (!$found) {
                    $newAdaptationType = new OrganizationSpecialtyAdaptation();
                    $newAdaptationType->setType($receivedAdaptationType);
                    $newAdaptationType->setOrganizationSpecialty($orgSpecialty);
                    $this->persistEntity($newAdaptationType);
                }
            }

            // Поиск тех, что нужно удалить
            foreach ($existingAdaptationTypes as $existing) {
                $found = false;

                /** @var OrganizationSpecialtyAdaptation $existing */
                foreach ($receivedAdaptationTypes as $received) {
                    if ($existing->getType() == $received) {
                        $found = true;
                        continue;
                    }
                }

                if (!$found) {
                    $this->removeEntity($existing);
                }
            }
        } else {
            // Если программа не адаптирована, то удаляем все записи
            foreach ($existingAdaptationTypes as $at)
                $this->removeEntity($at);
        }

        //CVarDumper::dump($args);exit;

        $orgSpecialty
            //->setSpecialty($specialty)
            ->setOrganization($organizationDomain->getModel())
            ->setStudyMode($args->getStudyMode())
            ->setStatus(self::DEFAULT_ORGANIZATION_SPECIALTY_STATUS)
            ->setOrganizationSpecialtyQualifications($mergedQualifications)
            ->setOrganizationSpecialtyExams($examList)
            ->setTrainingLevel($args->getTrainingLevel())
            ->setTrainingType($args->getTrainingType())
            ->setPlannedAbiturientsCount($args->getPlannedAbiturientsCount())
            ->setPlannedGroupsCount($args->getPlannedGroupsCount())
            ->setStudyPeriod($args->getStudyPeriod())
            ->setBaseEducation($args->getBaseEducation());

        $this->persistEntity($orgSpecialty);
    }
}