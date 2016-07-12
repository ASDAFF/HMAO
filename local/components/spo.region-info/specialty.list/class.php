<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Spo\Site\Adapters\SpecialtyDomainAdapter;
use Spo\Site\Domains\SpecialtyDomain;
use Spo\Site\Entities\SpecialtyGroupTable;


class SpecialtyListComponent extends RegionInfo
{
    protected $componentPage = '';
    protected $pageTitle = 'Специальности среднего профессионального образования';
    protected $breadcrumbs = array(
        'Специальности среднего профессионального образования' => '',
    );

	protected function getResult()
	{
		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		$organizationFilter = $request->get('organizationFilter');
		$organizationFilter = $organizationFilter['specialty'];

		if (!empty($organizationFilter)){
            $this->arResult['specialtiesList'] = SpecialtyDomainAdapter::getListCastomFilter($organizationFilter);
		} else
			$this->arResult['specialtiesList'] = SpecialtyDomainAdapter::listSpecialtiesByGroup(
				//SpecialtyDomain::getSpecialtiesList()
			);

        $this->arResult['specialyties'] = SpecialtyDomainAdapter::listSpecialties();
	}

}
?>