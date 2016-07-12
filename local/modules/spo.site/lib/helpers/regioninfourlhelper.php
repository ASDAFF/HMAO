<?php
namespace Spo\Site\Helpers;

use Spo\Site\Core\SpoUrlHelper;


class RegionInfoUrlHelper extends SpoUrlHelper
{
	public function getComponentName()
	{
		return 'region-info';
	}

	public static function getOrganizationListUrl(array $params = array())
	{
		return self::getComponentUrl('organizationList', $params);
	}

    public static function getSpecialtyInfoUrl($params)
    {
        return self::getComponentUrl('specialtyInfo', $params);
    }

    public static function getSpecialtyListUrl(array $params = array())
    {
        return self::getComponentUrl('specialtiesList', $params);
    }

}