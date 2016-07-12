<?php
namespace Spo\Site\Helpers;

use Spo\Site\Core\SpoUrlHelper;

// TODO  вынести сюда функции из edudepartmentofficeurlhelper.php, полсе чего оставить один хелпер

class DepartmentOfficeUrlHelper extends SpoUrlHelper
{
	public function getComponentName()
	{
		return 'edu-department-office';
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