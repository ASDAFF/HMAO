<?php
namespace Spo\Site\Helpers;

use Spo\Site\Core\SpoUrlHelper;


class OrganizationInfoUrlHelper extends SpoUrlHelper
{
	public function getComponentName()
	{
		return 'organization-info';
	}

	public static function getOrganizationSpecialtiesUrl($organizationId)
	{
		return self::getOrganizationSystemPageUrl($organizationId, 'specialties');
	}

	public static function getOrganizationEntryExamsScheduleUrl($organizationId)
	{
		return self::getOrganizationSystemPageUrl($organizationId, 'entry-exams-schedule');
	}

	public static function getOrganizationEntryExamsResultUrl($organizationId)
	{
		return self::getOrganizationSystemPageUrl($organizationId, 'entry-exams-result');
	}

    public static function getOrganizationControlOfEntranceUrl($organizationId)
    {
        return self::getOrganizationSystemPageUrl($organizationId, 'control-of-entrance');
    }
	public static function getOrganizationStatisticApplication($organizationId)
	{
		return self::getOrganizationSystemPageUrl($organizationId, 'statistic-application');
	}

	public static function getOrganizationErenrollmentUrl($organizationId)
	{
		return self::getOrganizationSystemPageUrl($organizationId, 'renrollment-count');
	}

    public static function getOrganizationApplicationsCountUrl($organizationId)
    {
        return self::getOrganizationSystemPageUrl($organizationId, 'applications-count');
    }

	public static function getOrganizationMainPageUrl($organizationId)
	{
		return self::getOrganizationSystemPageUrl($organizationId, '');
	}

	public static function getOrganizationSystemPageUrl($organizationId, $section = '')
	{
		return self::getComponentUrl('organizationSystemPage', array(
			'organizationId' => $organizationId, 'section' => $section
		));
	}

	public static function getOrganizationPredefinedPageUrl($organizationId, $pageType = '')
	{
		return self::getComponentUrl('organizationPredefinedStaticPage', array(
			'organizationId' => $organizationId, 'pageType' => $pageType
		));
	}

	public static function getOrganizationCustomPageUrl($organizationId, $pageId = '')
	{
		return self::getComponentUrl('organizationCustomStaticPage', array(
			'organizationId' => $organizationId, 'pageId' => $pageId
		));
	}

}