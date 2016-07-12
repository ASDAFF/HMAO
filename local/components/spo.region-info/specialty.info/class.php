<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Spo\Site\Adapters\SpecialtyDomainAdapter;
//use Spo\Site\Domains\SpecialtyDomain;
//use Spo\Site\Domains\CityDomain;
use \Spo\Site\Adapters\CityDomainAdapter;
use Spo\Site\Helpers\RegionInfoUrlHelper;


class SpecialtyInfoComponent extends RegionInfo
{
    protected $componentPage = '';
    protected $pageTitle = 'Специальности среднего профессионального образования';

	protected function getResult()
	{
        $specialtyId = $this->arParams['specialtyId'];
		$this->arResult['specialtyInfo'] = SpecialtyDomainAdapter::getSpecialtyWithDescription(
            /*SpecialtyDomain::getSpecialtyWithDescription($specialtyId)*/$specialtyId
        );
        $this->arResult['cityWithSelectedSpecialty'] = CityDomainAdapter::listCitiesWithOrganizationsCount(
            /*CityDomain::getCitiesListWithOrganizationCount($specialtyId),*/$specialtyId
        );

        $this->breadcrumbs['Специальности среднего профессионального образования'] = RegionInfoUrlHelper::getSpecialtyListUrl();
        $this->breadcrumbs[$this->arResult['specialtyInfo']['specialtyTitle']] = RegionInfoUrlHelper::getSpecialtyListUrl();
	}

}
?>