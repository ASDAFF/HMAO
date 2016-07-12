<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use Spo\Site\Helpers\OrganizationInfoUrlHelper;
use Bitrix\Main\Loader;
//use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Adapters\OrganizationDomainAdapter;
use Spo\Site\Helpers\PagingHelper;
use Spo\Site\Core\SPOComponent;

class RegionPreviewOrganizationList extends SPOComponent
{
    protected $componentPage = '';

    protected function checkUserAccess()
    {

    }
    protected function checkParams()
    {
        if (empty($this->arParams['organizationCount']))
            $this->arParams['organizationCount'] = 10;

    }

	protected function getResult()
	{
        $paging = new PagingHelper(true, $this->arParams['organizationCount']);
        $this->arResult['organizationsList'] = OrganizationDomainAdapter::listOrganizations(
            //OrganizationDomain::getSimpleOrganizationsList($paging)
        );
	}

}
?>