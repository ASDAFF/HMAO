<?php
namespace Spo\Site\Domains;

use Spo\Site\Core\SPODomain;
use D;
use Spo\Site\Doctrine\Repositories\RegionRepository;
use Spo\Site\Exceptions\DomainException;
use Spo\Site\Doctrine\Entities\Region;
use Spo\Site\Doctrine\Entities\RegionArea;


class RegionDomain extends SPODomain
{
	public static function getById($regionId, $withRegionAreas = true)
	{
        $repo = RegionRepository::create()
            ->getRegions()
            ->byId($regionId);

        if($withRegionAreas)
        {
            $repo->withRegionArea();
        }

        $region = $repo->one();

        if($region === null){throw DomainException::domainNotFound($regionId);}

        return new self($region);
	}

	public static function getByRegionAreaId($regionAreaId)
	{
        $repo = RegionRepository::create()
            ->getRegions()
            ->withRegionArea()
            ->byRegionAreaId($regionAreaId);

        $region = $repo->one();

        if($region === null){throw DomainException::domainNotFound($regionAreaId);}

        return new self($region);
	}

    public function getRegionAreaIdByRegionAreaId($regionAreaId)
    {
        /* @var Region $region */
        $region = $this->getModel();

        $regionAreas = $region->getRegionAreas();

        /* @var RegionArea $regionArea */
        foreach($regionAreas as $regionArea)
        {
            if($regionArea->getRegionAreaId() === $regionAreaId)
            {
                return $regionArea;
            }
        }

        return null;
    }

}