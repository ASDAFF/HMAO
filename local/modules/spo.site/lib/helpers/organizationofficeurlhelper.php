<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 23.04.15
 * Time: 15:43
 */
namespace Spo\Site\Helpers;

use Bitrix\Main\Type\DateTime;

class OrganizationOfficeUrlHelper
{
    protected static $urlTree = array(
        'root' => array(
            'url'   => 'organization-office',
            'items' => array(
                'index' =>array(
                    'url' => ''
                ),
                'org' => array(
                    'url' => 'organization',
                    'items' => array(
                        'edit' => array(
                            'url' => 'edit'
                        ),
                        'page' => array(
                            'url' => 'page',
                            'items' => array(
                                'edit' => array(
                                    'url' => 'edit'
                                ),
                            )
                        ),
                    )
                ),
                'appl' => array(
                    'url' => 'application',
                    'items' => array(
                        'list' => array(
                            'url' => 'list',
                            'items' => array(
                                'archive' => array(
                                    'url' => 'archive'
                                ),
                            )
                        ),
                        'changeStatus' => array(
                            'url' => 'list'
                        ),
                        'edit' => array(
                            'url' => 'edit'
                        ),
                    )
                ),
                'spec' => array(
                    'url' => 'specialty',
                    'items' => array(
                        'list' => array(
                            'url' => 'list'
                        ),
                        'ajax' => array(
                            'url' => 'ajax'
                        ),
                        'plan' => array(
                            'url' => 'admissionPlan',
                        ),
                    )
                ),
                'abitur' => array(
                    'url' => 'abiturient',
                    'items' => array(
                        'profile' => array(
                            'url' => 'profile'
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

    public static function toOrganizationInfoEdit($forBitrixComponentUrlArray = false)
    {
        return self::getUrl(array('root', 'org', 'edit'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);
    }

    public static function toApplicationList($forBitrixComponentUrlArray = false)
    {
        return self::getUrl(array('root', 'appl', 'list'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);
    }

    public static function toApplicationArchiveList($forBitrixComponentUrlArray = false)
    {
        return self::getUrl(array('root', 'appl', 'list', 'archive'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);
    }

    public static function toSpecialtyList($forBitrixComponentUrlArray = false)
    {
        return self::getUrl(array('root', 'spec', 'list'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);
    }

    public static function toStaticPageEdit($pageId = 0, $forBitrixComponentUrlArray = false)
    {
        $baseUrl = self::getUrl(array('root', 'org', 'page', 'edit'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);
        $params = array();
        if($pageId > 0)
        {
            $params['pageId'] = $pageId;
        }
        return self::modifyUrlWithParams($baseUrl, $params);
    }

    public static function toApplicationEdit($applicationId, $forBitrixComponentUrlArray = false)
    {
        $baseUrl = self::getUrl(array('root', 'appl', 'edit'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);
        $params = array();

        if($applicationId > 0) {
            $params['applicationId'] = $applicationId;
        }

        return self::modifyUrlWithParams($baseUrl, $params);
    }

    public static function toAdmissionPlanEdit($year = 0, $forBitrixComponentUrlArray = false)
    {
        $baseUrl = self::getUrl(array('root', 'spec', 'plan'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);
        $params = array();

        if($year > 0) {
            $params['year'] = $year;
        }

        return self::modifyUrlWithParams($baseUrl, $params);
    }

    public static function toSpecialtyAjax($forBitrixComponentUrlArray = false, $params = array())
    {
        $baseUrl = self::getUrl(array('root', 'spec', 'ajax'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);
        $params['nolayout'] = 1;
        return self::modifyUrlWithParams($baseUrl, $params);
    }

    public static function toDeleteEducationalProgram($organizationSpecialtyId,$organizationId)
    {
        return self::toSpecialtyAjax(false, array(
            'action'         => 'deleteProgram',
            'organizationSpecialtyId'    => $organizationSpecialtyId,
            'organizationId' => $organizationId
        ));
    }
    public static function toUpdateEducationalProgram()
    {
        return self::toSpecialtyAjax(false, array(
            'action'         => 'updateProgram',
        ));
    }
    public static function toAddEducationalProgram($organizationId)
    {
        return self::toSpecialtyAjax(false, array(
            'action'         => 'addProgram',
            //'specialtyId'    => $specialtyId,
            'organizationId' => $organizationId
        ));
    }
    public static function toLoadEducationalProgram($organizationSpecialtyId)
    {
        return self::toSpecialtyAjax(false, array(
            'action'         => 'loadProgram',
            'organizationSpecialtyId' => $organizationSpecialtyId
        ));
    }

    public static function toAbiturientProfile($abiturientUserId = 0, $forBitrixComponentUrlArray = false)
    {
        $baseUrl = self::getUrl(array('root', 'abitur', 'profile'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);
        $params  = array();

        if($abiturientUserId > 0)
        {
            $params['userId'] = $abiturientUserId;
        }
        return self::modifyUrlWithParams($baseUrl, $params);
    }

    public static function toApplicationStatusChange($applicationId = 0, $status = 0, $forBitrixComponentUrlArray = false)
    {
        $baseUrl = self::getUrl(array('root', 'appl', 'changeStatus'), !$forBitrixComponentUrlArray, !$forBitrixComponentUrlArray);
        $params  = array(
            'action' => 'changeStatus'
        );

        if($applicationId > 0)
        {
            $params['applicationId'] = $applicationId;
        }
        if($status > 0)
        {
            $params['status'] = $status;
        }

        return self::modifyUrlWithParams($baseUrl, $params);
    }
}