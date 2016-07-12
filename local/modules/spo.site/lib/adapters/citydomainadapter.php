<?php
namespace Spo\Site\Adapters;

use Spo\Site\Domains\CityDomain;
use Spo\Site\Doctrine\Entities\City;
use Spo\Site\Util\CVarDumper;
use Spo\Site\Entities\CityTable;
use Spo\Site\Entities\SpecialtyTable;
use Bitrix\Main\Entity;

class CityDomainAdapter
{
	public static function listCities(/*CityDomain $domain*/)
	{
		/*$cities = $domain->getEntityCollection();
		$result = array();

		foreach ($cities as $city) {
			// @var City $city
			$cityAttributes = array(
				'id' => $city->getCityId(),
				'name' => $city->getCityName(),
			);

			$result[] = $cityAttributes;
		}*/
        //return $result;
        $ArrayResult = CityTable::getList(array(
            'select' => array(
                'id'=>'CITY_ID',
                'name'=>'CITY_NAME',
            )
        ))->fetchAll();
        return $ArrayResult;
	}

    public static function listCitiesWithOrganizationsCount(/*CityDomain $domain,*/ $specialtyId)
    {
        /*$cities = $domain->getEntityCollection();
        $result = array();

        foreach ($cities as $city) {
            // @var City $cityModel
            $cityModel = $city[0];
            $cityOrganizationCount = $city['organizationCount'];

            $cityAttributes = array(
                'id' => $cityModel->getCityId(),
                'name' => $cityModel->getCityName(),
                'organizationCount' => $cityOrganizationCount,
            );

            $result[] = $cityAttributes;
        }*/
        $ArrayResult = SpecialtyTable::getList(array(
            'filter' => array(
                'SPECIALTY_ID'=>$specialtyId,
            ),
            'select' => array(
                'id'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.CITY.CITY_ID',
                'name'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.CITY.CITY_NAME',
                 new Entity\ExpressionField('organizationCount', 'COUNT(%s)', array('ORGANIZATION_SPECIALTY.ORGANIZATION.CITY.CITY_ID'))
            )
        ))->fetchAll();
        return $ArrayResult;
        //return $result;
    }
}