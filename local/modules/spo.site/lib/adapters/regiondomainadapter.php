<?php
namespace Spo\Site\Adapters;
use Spo\Site\Util\CVarDumper;
use Spo\Site\Entities\RegionTable;
//use Spo\Site\Domains\CityDomain;
//use Spo\Site\Domains\RegionDomain;
//use Spo\Site\Doctrine\Entities\Region;
//use Spo\Site\Doctrine\Entities\RegionArea;
//use Spo\Site\Doctrine\Entities\City;

class RegionDomainAdapter
{
	/*public static function listRegionAreas(RegionDomain $domain)
    {
        $result = array();

        // @var Region $region
        $region = $domain->getModel();
        $regionAreas = $region->getRegionAreas();

        // @var RegionArea $regionArea
        foreach($regionAreas as $regionArea)
        {
            $result[] = array(
                'regionAreaId'   => $regionArea->getRegionAreaId(),
                'regionAreaName' => $regionArea->getRegionAreaName(),
            );
        }

        return $result;
    }*/
    public static function listRegionAreas($domain)
    {
        $ArrayResult = RegionTable::getList(array(
            'filter' => array('REGION_ID'=>$domain),
            'select' => array(
                'regionAreaId'=>'REGION_AREA.REGION_AREA_ID',
                'regionAreaName'=>'REGION_AREA.REGION_AREA_NAME',
            )
        ))->fetchAll();
        //return $result;
        return $ArrayResult;
    }
}