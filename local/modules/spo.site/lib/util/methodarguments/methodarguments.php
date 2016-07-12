<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 30.04.15
 * Time: 15:00
 */
namespace Spo\Site\Util\Methodarguments;

use Exception;
use Spo\Site\Core\SPODomain;

abstract class MethodArguments
{
    const AUTOINIT   = true;
    const MANUALINIT = false;

    const DEFAULT_ACTION = 'default';

    protected $domain    = null;
    protected $errorList = array();
    protected $arguments = array(
//        'orderBy' => array(
//            'map'   => 'orderBy', // как параметр будет в GET
//            'value' =>  null, // значение
//            'def'   => 'ASC' // значение по-умолчанию
//        ),
    );

    public function __construct($autoInit = self::AUTOINIT)
    {
        if($autoInit = self::AUTOINIT){
            $this->initArguments();
        }
    }

    public function initArguments()
    {
        return $this->initArgumentsFromArray($_POST);
    }
    
    public function initArgumentsFromArray(array $data)
    {
        foreach($this->arguments as $paramName=>$paramCfg)
        {
            $val = $data[$paramCfg['map']];
            if($val === null)
            {
                if(isset($paramCfg['def']))
                {
                    $val = $paramCfg['def'];
                }
                else
                {
                    continue;
                }
            }

            $setterName = 'set' . ucfirst($paramName);
            if(method_exists($this, $setterName))
            {
                call_user_func(array($this, $setterName), $val);
            }
            else
            {
                $this->setArgument($paramName, $val);
            }
        }

        return $this;
    }

    public function setArgument($name, $value)
    {
        if(!isset($this->arguments[$name]))
        {
            throw new Exception('param ' . $name . ' is not defined');
        }

        return $this->setArgumentValue($name, $value);
    }

    protected function setArgumentValue($name, $value)
    {
        $this->arguments[$name]['value'] = $value;
        return $this;
    }

    public function wasArgumentSet($name)
    {
        if(!isset($this->arguments[$name])){
            return false;
        }

        if(is_array($this->arguments[$name]) && count($this->arguments[$name]) === 0){
            return false;
        }

        return $this->arguments[$name]['value'] !== null;
    }

    public function addError($msg, $fld = null)
    {
        if($fld === null)
        {
            $this->errorList[] = $msg;
        }
        else
        {
            $this->errorList[$fld] = $msg;
        }
        return $this;
    }
    public function hasErrors()
    {
        return count($this->errorList) > 0;
    }
    public function getErrors()
    {
        return $this->errorList;
    }
    public function resetErrors()
    {
        $this->errorList = array();
        return $this;
    }

    public function setDomain(SPODomain $domain)
    {
        $this->domain = $domain;

        return $this;
    }
    public function getDomain()
    {
        return $this->domain;
    }
    public function isDomainSet()
    {
        return $this->domain !== null;
    }

    /**
     * @param string $action
     * @return bool
     */
    abstract public function validateArguments($action = self::DEFAULT_ACTION);
}