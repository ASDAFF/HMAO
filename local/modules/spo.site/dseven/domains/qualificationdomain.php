<?php
namespace Spo\Site\Domains;

use Spo\Site\Core\SPODomain;
use D;
use Spo\Site\Doctrine\Repositories\CityRepository;
use Spo\Site\Exceptions\DomainException;


class QualificationDomain extends SPODomain
{

//	public static function getById($cityId)
//	{
//        $city = CityRepository::create()
//            ->getCities()
//            ->byId($cityId)
//            ->one();
//
//        if($city === null){throw DomainException::domainNotFound($cityId);}
//
//        return new self($city);
//	}
//
//    public static function getCitiesList()
//    {
//        $cities = D::$em->getRepository('Spo\Site\Doctrine\Entities\City')->findAll();
//
//        return new CityDomain(null, $cities);
//    }
//    public  static function getCitiesListWithOrganizationCount($specialtyId)
//    {
//        $citiesRepository = CityRepository::create()
//            ->getCities()
//            ->withCityOrganizationsCountBySpecialty($specialtyId);
//
//        return new self(null, $citiesRepository->all());
//    }

//    public static function getQualificationList()
//    {
//
//    }
}