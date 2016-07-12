<?php
/**
 * Класс конфигурации приложения. Работает через b_option
 */
namespace Spo\Site\Util;

use Bitrix\Main\Config\Option;
use Spo\Site\Exceptions\ConfigException;

class SpoConfig extends Option
{
    const SPO_MODULE_NAME = 'spo.site';

    const OPTION_SITE_REGION = 'site_region_id';

    public static function getSiteRegionId()
    {
        $regionId = intval(self::get(self::SPO_MODULE_NAME, self::OPTION_SITE_REGION));

        if($regionId <= 0)
        {
            throw ConfigException::optionUndefined(self::OPTION_SITE_REGION, self::SPO_MODULE_NAME);
        }

        return $regionId;
    }

    //todo организовать метод, который будет устанавливать значения по умолчанию
//    public static function initDefaultOptions()
//    {
//    }
}