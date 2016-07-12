<?php

namespace Spo\Site\Util\Methodarguments;

use Exception;
use Spo\Site\Exceptions\SpoException;
use Spo\Site\Util\CmsUser;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Domains\SpecialtyDomain;
use Spo\Site\Dictionaries\ExamDiscipline;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\TrainingLevel;

class OrganizationSpecialtyLoadArguments extends MethodArguments
{
    const BY_ID = 'organizationSpecialtyId';
    const WITH_QUALIFICATIONS = 'qualifications';
    const WITH_EXAMS          = 'exams';

    protected static $availableBY = array(
        self::BY_ID
    );
    protected static $availableWITH = array(
        self::WITH_EXAMS,
        self::WITH_QUALIFICATIONS
    );
    protected $arguments = array(
        'by' => array(
            'value' => array()
        ),
        'with' => array(
            'value' => array()
        )
    );

    public function __construct()
    {
        parent::__construct(self::MANUALINIT);
    }

    // <editor-fold desc="Getters, Setters, Checkers">

    public function withQualifications()
    {
        return $this->addWith(self::WITH_QUALIFICATIONS);
    }
    public function isWithQualifications()
    {
        return $this->isWith(self::WITH_QUALIFICATIONS);
    }

    public function withExams()
    {
        $this->addWith(self::WITH_EXAMS);
    }
    public function isWithExams()
    {
        return $this->isWith(self::WITH_EXAMS);
    }

    public function byId($id){
        return $this->addBy(self::BY_ID, intval($id));
    }

    public function isById(){
        return $this->isBy(self::BY_ID);
    }

    public function getById(){
        return $this->getBy(self::BY_ID);
    }

    // </editor-fold>

    protected function isWith($modelName)
    {
        return isset($this->arguments['with']['value'][$modelName]) && $this->arguments['with']['value'][$modelName] === true;
    }
    protected function addWith($modelName)
    {
        if(!in_array($modelName, self::$availableWITH))
        {
            throw new SpoException('unavailable WITH argument');
        }

        $this->arguments['with']['value'][$modelName] = true;
        return $this;
    }

    protected function isBy($attrName)
    {
        return isset($this->arguments['by']['value'][$attrName]);
    }
    protected function addBy($attrName, $val)
    {
        if(!in_array($attrName, self::$availableBY))
        {
            throw new SpoException('unavailable BY argument');
        }

        $this->arguments['by']['value'][$attrName] = $val;
        return $this;
    }
    protected function getBy($attrName)
    {
        if(!$this->isBy($attrName))
        {
            return null;
        }

        return $this->arguments['by']['value'][$attrName];
    }

    public function validateArguments($action = self::DEFAULT_ACTION)
    {
        return true;
    }
}