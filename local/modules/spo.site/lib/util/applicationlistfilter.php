<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 30.04.15
 * Time: 15:00
 */
namespace Spo\Site\Util;

use Exception;

class ApplicationListFilter
{
    const ORDER_ASC  = 'ASC';
    const ORDER_DESC = 'DESC';
    protected $params = array(
        'orderBy' => array(
            'map'   => 'orderBy', // как параметр будет в GET
            'value' =>  null, // значение
            'def'   => 'ASC' // значение по-умолчанию
        ),
        'sortField' => array(
            'map'   => 'orderField', // как параметр будет в GET
            'value' =>  null, // значение
        ),
        'year' => array(
            'map'   => 'year',
            'value' =>  null,
        ),
        'status' => array(
            'map'   => 'status',
            'value' =>  null,
        ),
        'funding' => array(
            'map'   => 'funding',
            'value' =>  null,
        )
    );

    public function __construct($autoInit = true)
    {
        if($autoInit){
            $this->initFilterParams();
        }
    }

    protected function initFilterParams()
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

        foreach($this->params as $paramName=>$paramCfg)
        {
            $val = $request->get($paramCfg['map']);
            if($val === null)
            {
                continue;
            }

            $setterName = 'set' . ucfirst($paramName);
            if(method_exists($this, $setterName))
            {
                call_user_func(array($this, $setterName), $val);
            }
            else
            {
                $this->setParam($paramName, $val);
            }
        }
    }

    public function setParam($name, $value)
    {
        if(!isset($this->params[$name]))
        {
            throw new Exception('param ' . $name . ' is not defined');
        }

        $this->setParamValue($name, $value);
    }

    protected function setParamValue($name, $value)
    {
        $this->params[$name]['value'] = $value;
    }

    public function wasParamSet($name)
    {
        return isset($this->params[$name]) && $this->params[$name]['value'] !== null;
    }

// order
    public function getOrderBy()
    {
        return $this->params['orderBy']['value'] !== null ?
            $this->params['orderBy']['value'] :
            $this->params['orderBy']['def'];
    }

    public function setOrderBy($orderBy)
    {
        $this->setParamValue('orderBy', $orderBy === self::ORDER_DESC ? self::ORDER_DESC : self::ORDER_ASC);
    }

    public function wasOrderBySet()
    {
        return $this->wasParamSet('orderBy');
    }


// year
    public function getYear()
    {
        return $this->params['year']['value'];
    }

    public function setYear($year)
    {
        $this->setParamValue('year', intval($year));
    }

    public function wasYearSet()
    {
        return $this->wasParamSet('year');
    }


// status
    public function getStatus()
    {
        return $this->params['status']['value'];
    }

    public function setStatus($status)
    {
        $this->setParamValue('status', intval($status));
    }

    public function wasStatusSet()
    {
        return $this->wasParamSet('status');
    }


// funding
    public function getFunding()
    {
        return $this->params['funding']['value'];
    }

    public function setFunding($funding)
    {
        $this->setParamValue('funding', intval($funding));
    }

    public function wasFundingSet()
    {
        return $this->wasParamSet('funding');
    }

// sort
    public function getSortField()
    {
        return $this->params['sortField']['value'];
    }

    public function setSortField($sortField)
    {
        switch($sortField){
            case 'applicationId':
                $f= 'Application.applicationId';
                break;
            case 'applicationCreateDate':
                $f= 'Application.applicationCreationDate';
                break;
            case 'userLastname':
                $f= 'User.lastName';
                break;
            case 'userName':
                $f= 'User.name';
                break;
            case 'userSecondname':
                $f= 'User.secondName';
                break;
            default:
                return;
                break;
        }
        $this->setParamValue('sortField', $f);
    }

    public function wasSortFieldSet()
    {
        return $this->wasParamSet('sortField');
    }
}