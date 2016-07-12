<?php

namespace Spo\Site\Helpers;

class EduDepartmentOfficeUrlHelper
{
    protected static $urlTree = array(
        'root' => array(
            'url'   => 'edu-department-office',
            'items' => array(
                'index' =>array(
                    'url' => ''
                ),
                'plan' => array(
                    'url' => 'plan',
                    'items' => array(
                        'view' => array(
                            'url' => 'view',
                        ),
                        'viewByOrganizations' => array(
                            'url' => 'viewByOrganizations',
                        ),
                        'viewBySpecialty' => array(
                            'url' => 'viewBySpecialty',
                        ),
                        'planFactView' => array(
                            'url' => 'planFactView'
                        ),
                        'edit' => array(
                            'url' => 'planEdit'
                        ),
                    )
                ),
            )
        )
    );

    protected static function getUrl(array $path = array(), $includeRoot = true, $includeLeadingSlash = true)
    {
        $pathAr = self::urlTreeWalker(self::$urlTree, $path);

        if(!$includeRoot){
            array_shift($pathAr);
        }

        return ($includeLeadingSlash ? '/' : '') . implode('/', $pathAr);
    }

    protected static function modifyUrlWithParams($baseUrl, $params)
    {
        if(count($params) === 0){
            return $baseUrl;
        }

        $paramsArr = array();
        foreach($params as $name=>$value){
            $paramsArr[] = $name . '=' . $value;
        }

        return $baseUrl . '?' . implode('&', $paramsArr);
    }

    protected static function urlTreeWalker($subTree, array $path = array())
    {
        $curPath = count($path) > 0 ? $path[0] : -1;
        $url = isset($subTree[$curPath]['url']) ? $subTree[$curPath]['url'] : '';

        if(isset($subTree[$curPath]['items']) && count($path) > 1){
            array_shift($path);
            $urlArr = self::urlTreeWalker($subTree[$curPath]['items'], $path);
        }else{
            return array($url);
        }

        array_unshift($urlArr, $url);
        return $urlArr;
    }

    public static function toAdmissionPlanView($organizationId = '', $forBitrixComponentUrlArray = false)
    {
        $baseUrl =  self::getUrl(array('root', 'plan', 'view'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);

        if($organizationId === '')
        {
            return $baseUrl;
        }
        else
        {
            return self::modifyUrlWithParams($baseUrl, array(
                'organizationId' => intval($organizationId)
            ));
        }
    }

    public static function toAdmissionPlanEdit($admissionPlanId = '', $forBitrixComponentUrlArray = false)
    {
        $baseUrl =  self::getUrl(array('root', 'plan', 'edit'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);

        if($admissionPlanId === '')
        {
            return $baseUrl;
        }
        else
        {
            return self::modifyUrlWithParams($baseUrl, array(
                'admissionPlanId' => intval($admissionPlanId)
            ));
        }
    }

    public static function toAdmissionPlanByOrganizationsView($params = array(), $forBitrixComponentUrlArray = false)
    {
        $baseUrl = self::getUrl(array('root', 'plan', 'viewByOrganizations'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);

        return self::modifyUrlWithParams($baseUrl, $params);
    }

    public static function toAdmissionPlanBySpecialtyView($params = array(), $forBitrixComponentUrlArray = false)
    {
        $baseUrl = self::getUrl(array('root', 'plan', 'viewBySpecialty'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);

        return self::modifyUrlWithParams($baseUrl, $params);
    }

    public static function toAdmissionPlanFactView($params = array(), $forBitrixComponentUrlArray = false)
    {
        $baseUrl = self::getUrl(array('root', 'plan', 'planFactView'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);

        return self::modifyUrlWithParams($baseUrl, $params);
    }
}