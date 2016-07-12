<?php
namespace Spo\Site\Adapters;

use Spo\Site\Domains\CityDomain;
use Spo\Site\Domains\RegionDomain;
use Spo\Site\Doctrine\Entities\Region;
use Spo\Site\Doctrine\Entities\RegionArea;
use Spo\Site\Doctrine\Entities\City;
use Spo\Site\Util\CVarDumper;

class RegionAreaDomainAdapter
{
	public static function listRegionAreas(RegionDomain $domain)
    {
        $result = array();

        /* @var Region $region */
        $region = $domain->getModel();
        $regionAreas = $region->getRegionAreas();

        /* @var RegionArea $regionArea */
        foreach($regionAreas as $regionArea)
        {
            $result[] = array(
                'regionAreaId'   => $regionArea->getRegionAreaId(),
                'regionAreaName' => $regionArea->getRegionAreaName(),
            );
        }

        return $result;
    }
}