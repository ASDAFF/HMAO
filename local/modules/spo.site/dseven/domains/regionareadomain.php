<?php
namespace Spo\Site\Domains;

use Spo\Site\Core\SPODomain;
use D;
use Spo\Site\Doctrine\Repositories\RegionRepository;
use Spo\Site\Exceptions\DomainException;
use Spo\Site\Doctrine\Entities\Region;
use Spo\Site\Doctrine\Entities\RegionArea;


class RegionAreaDomain extends SPODomain
{
	public static function getRegionAreasList()
    {
        return D::$em->getRepository('Spo\Site\Doctrine\Entities\Region')->findAll();
    }
}