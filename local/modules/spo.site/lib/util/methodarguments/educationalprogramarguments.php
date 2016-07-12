<?php

namespace Spo\Site\Util\Methodarguments;

use Exception;
use Spo\Site\Exceptions\SpoException;
use Spo\Site\Util\CmsUser;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Domains\SpecialtyDomain;
use Spo\Site\Dictionaries\ExamDiscipline;
use Spo\Site\Dictionaries\ExamType;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Dictionaries\TrainingType;
use Spo\Site\Domains\OrganizationSpecialtyDomain;
use Spo\Site\Exceptions\AccessException;

class EducationalProgramArguments extends MethodArguments
{
    const CREATE_ACTION = 'create';
    const UPDATE_ACTION = 'update';
    protected $arguments = array(
//        'organizationSpecialtyId' => array( // только для обновления
//            'map'   => 'organizationSpecialtyId',
//            'value' =>  null,
//        ),
        'specialtyId' => array(
            'map'   => 'specialtyId',
            'value' =>  null,
        ),
        'organizationId' => array(
            'map'   => 'organizationId',
            'value' =>  null,
        ),
        'baseEducation' => array(
            'map'   => 'specialtyBaseEducation',
            'value' =>  null,
        ),
        'studyMode' => array(
            'map'   => 'specialtyStudyMode',
            'value' =>  null,
        ),
        'examList' => array(
            'map'   => 'examList',
            'value' =>  null,
            'def'   =>  array(),
        ),
        'adaptationTypes' => array(
            'map'   => 'adaptationTypes',
            'value' =>  null,
            'def'   =>  array(),
        ),
        'adapted' => array(
            'map'   => 'adapted',
            'value' =>  null,
        ),
        'qualificationList' => array(
            'map'   => 'qualificationList',
            'value' =>  null,
            'def'   =>  array(),
        ),
        'trainingLevel' => array(
            'map'   => 'trainingLevel',
            'value' =>  null,
            //'def'   =>  0,
        ),
        'trainingType' => array(
            'map'   => 'trainingType',
            'value' =>  null,
            //'def'   =>  0,
        ),
        'studyPeriod' => array(
            'map'   => 'studyPeriod',
            'value' =>  null,
            'def'   =>  0,
        ),
        'plannedAbiturientsCount' => array(
            'map'   => 'abitCount',
            'value' =>  null,
            'def'   =>  0,
        ),
        'plannedGroupsCount' => array(
            'map'   => 'groupsCount',
            'value' =>  null,
            'def'   =>  0,
        ),
    );

    // <editor-fold desc="Getters, Setters, Checkers">

    // organizationSpecialtyId
//    public function getOrganizationSpecialtyId()
//    {
//        return $this->arguments['organizationSpecialtyId']['value'];
//    }
//    public function setOrganizationSpecialtyId($value)
//    {
//        return $this->setArgumentValue('organizationSpecialtyId', intval($value));
//    }
//    public function wasOrganizationSpecialtyIdSet()
//    {
//        return $this->wasArgumentSet('organizationSpecialtyId');
//    }

    // specialtyId
    public function getSpecialtyId()
    {
        return $this->arguments['specialtyId']['value'];
    }
    public function setSpecialtyId($value)
    {
        return $this->setArgumentValue('specialtyId', intval($value));
    }
    public function wasSpecialtyIdSet()
    {
        return $this->wasArgumentSet('specialtyId');
    }

    // organizationId
    public function getOrganizationId()
    {
        return $this->arguments['organizationId']['value'];
    }
    public function setOrganizationId($value)
    {
        return $this->setArgumentValue('organizationId', intval($value));
    }
    public function wasOrganizationIdSet()
    {
        return $this->wasArgumentSet('organizationId');
    }

    // baseEducation
    public function getBaseEducation()
    {
        return $this->arguments['baseEducation']['value'];
    }
    public function setBaseEducation($value)
    {
        return $this->setArgumentValue('baseEducation', intval($value));
    }
    public function wasBaseEducationSet()
    {
        return $this->wasArgumentSet('baseEducation');
    }

    // trainingLevel
    public function getTrainingLevel()
    {
        return $this->arguments['trainingLevel']['value'];
    }
    public function setTrainingLevel($value)
    {
        return $this->setArgumentValue('trainingLevel', abs(intval($value)));
    }
    public function wasTrainingLevelSet()
    {
        return $this->wasArgumentSet('trainingLevel');
    }

    // trainingType
    public function getTrainingType()
    {
        return $this->arguments['trainingType']['value'];
    }
    public function setTrainingType($value)
    {
        return $this->setArgumentValue('trainingType', abs(intval($value)));
    }
    public function wasTrainingTypeSet()
    {
        return $this->wasArgumentSet('trainingType');
    }

    // studyMode
    public function getStudyMode()
    {
        return $this->arguments['studyMode']['value'];
    }
    public function setStudyMode($value)
    {
        return $this->setArgumentValue('studyMode', abs(intval($value)));
    }
    public function wasStudyModeSet()
    {
        return $this->wasArgumentSet('studyMode');
    }

    // plannedAbiturientsCount
    public function getPlannedAbiturientsCount()
    {
        return $this->arguments['plannedAbiturientsCount']['value'];
    }
    public function setPlannedAbiturientsCount($value)
    {
        return $this->setArgumentValue('plannedAbiturientsCount', abs(intval($value)));
    }
    public function wasPlannedAbiturientsCountSet()
    {
        return $this->wasArgumentSet('plannedAbiturientsCount');
    }

    // plannedGroupsCount
    public function getPlannedGroupsCount()
    {
        return $this->arguments['plannedGroupsCount']['value'];
    }
    public function setPlannedGroupsCount($value)
    {
        return $this->setArgumentValue('plannedGroupsCount', abs(intval($value)));
    }
    public function wasPlannedGroupsCountSet()
    {
        return $this->wasArgumentSet('plannedGroupsCount');
    }

    // studyPeriod
    public function getStudyPeriod()
    {
        return $this->arguments['studyPeriod']['value'];
    }
    public function setStudyPeriod($value)
    {
        return $this->setArgumentValue('studyPeriod', abs(intval($value)));
    }
    public function wasStudyPeriodSet()
    {
        return $this->wasArgumentSet('studyPeriod');
    }

    public function getAdaptationTypes()
    {
        return $this->arguments['adaptationTypes']['value'];
    }

    public function isAdapted()
    {
        return $this->arguments['adapted']['value'];
    }

    // examList
    public function getExamList()
    {
        return $this->arguments['examList']['value'];
    }
    public function setExamList(array $value)
    {
        $list = array();
        foreach($value as $qData)
        {
            $dt = array(
                'disciplineId' => intval($qData['disciplineId']),
                'type'         => intval($qData['type']),
            );
            if(isset($qData['id'])){
                $dt['id'] = intval($qData['id']);
            }
            $list[] = $dt;

        }
        return $this->setArgumentValue('examList', $list);
    }
    public function wasExamListSet()
    {
        return $this->wasArgumentSet('examList');
    }

    // qualificationList
    public function getQualificationList()
    {
        return $this->arguments['qualificationList']['value'];
    }
    public function setQualificationList(array $value)
    {
        $list = array();
        foreach($value as $qId)
        {
            $list[] = intval($qId);
        }
        return $this->setArgumentValue('qualificationList', $list);
    }
    public function wasQualificationListSet()
    {
        return $this->wasArgumentSet('qualificationList');
    }

    // </editor-fold>

    public function validateArguments($action = self::CREATE_ACTION)
    {
        $user = CmsUser::getCurrentUser();

        switch($action){
            case self::CREATE_ACTION:
                if($this->getOrganizationId() <= 0)
                {
                    $this->addError('Не указана организация', 'organizationId');
                    return false;
                }

                if($this->getSpecialtyId() <= 0)
                {
                    $this->addError('Не указана специальность', 'specialtyId');
                    return false;
                }

                $organizationDomain = OrganizationDomain::loadByEmployeeUserId($user->getId());
                if($this->getOrganizationId() !== $organizationDomain->getOrganizationId())
                {
                    $this->addError('Ошибка доступа к организации', 'organizationId');
                    return false;
                }

                if(!$this->wasQualificationListSet())
                {
                    $this->addError('Не задана ни одна квалификация', 'specialtyId');
                    return false;
                }

                $qValid = SpecialtyDomain::checkIfQualificationsBelongsToSpecialty(
                    $this->getSpecialtyId(),
                    $this->getQualificationList()
                );
                if(!$qValid)
                {
                    $this->addError('Квалификации должны принадлежать специальности', 'specialtyId');
                    return false;
                }

                if(!TrainingLevel::isDefined($this->getTrainingLevel())){
                    $this->addError('Уровень обучения указан неверно', 'trainingLevel');
                    return false;
                }

                if(!TrainingType::isDefined($this->getTrainingType())){
                    $this->addError('Программа подготовки указана неверно', 'trainingLevel');
                    return false;
                }

                if(!BaseEducation::isDefined($this->getBaseEducation())){
                    $this->addError('Базовое образование указано неверно', 'baseEducation');
                    return false;
                }

                if(!StudyMode::isDefined($this->getStudyMode())){
                    $this->addError('Форма обучения указана неверно', 'studyMode');
                    return false;
                }

                $examList = $this->getExamList();
                foreach($examList as $examination)
                {
                    if(!ExamDiscipline::isDefined($examination['disciplineId']))
                    {
                        $this->addError('Одна из дисциплин указана неверно');
                        return false;
                    }
                    if(!ExamType::isDefined($examination['type']))
                    {
                        $this->addError('Тип экзамена указан неверно');
                        return false;
                    }
                }
                break;
            case self::UPDATE_ACTION:
                if(!$this->isDomainSet()){
                    throw new SpoException('Необходимо установить домен для валидации');
                }
                /* @var OrganizationSpecialtyDomain $domain */
                $domain = $this->getDomain();
//var_dump($domain->getSpecialtyId(),  $this->getSpecialtyId());exit;
                if($domain->getSpecialtyId() !== $this->getSpecialtyId()){
                    $this->addError('Специальность не может быть изменена');
                    return false;
                }

                if(!self::validateArguments(self::CREATE_ACTION)){
                    return false;
                }
                break;
            default:
                break;
        }
        return true;
    }
}