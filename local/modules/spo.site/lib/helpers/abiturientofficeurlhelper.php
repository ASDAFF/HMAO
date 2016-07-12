<?php
namespace Spo\Site\Helpers;

use Spo\Site\Core\SpoUrlHelper;
use Spo\Site\Util\CVarDumper;


class AbiturientOfficeUrlHelper extends SpoUrlHelper
{
	public function getComponentName()
	{
		return 'abiturient-office';
	}

	public static function getApplicationListUrl()
	{
		return self::getComponentUrl('applicationList');
	}

	public static function getApplicationDeleteUrl($applicationId)
	{
		return self::getComponentUrl('applicationDelete', array('applicationId' => $applicationId));
	}

	public static function getApplicationCreateUrl($organizationId, $organizationSpecialtyId = null)
	{
        $params = array('organizationId' => $organizationId);
        if ($organizationSpecialtyId)
            $params['organizationSpecialtyId'] = $organizationSpecialtyId;

		return self::getComponentUrl('applicationCreate', $params);
	}

	public static function getApplicationEditUrl($applicationId)
	{
		return self::getComponentUrl('applicationEdit', array('applicationId' => $applicationId));
	}

	public static function getProfileUpdateUrl($params = array())
	{
		return self::getComponentUrl('profileUpdate', $params);
	}
}