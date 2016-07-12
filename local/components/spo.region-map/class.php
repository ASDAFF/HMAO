<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use \Bitrix\Main;
use Spo\Site\Core\SPOComponent;
//use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Dictionaries\OrganizationStatus;
use Spo\Site\Helpers\OrganizationInfoUrlHelper;
use Spo\Site\Entities\OrganizationTable;

class SpoPaging extends SPOComponent
{
    protected $componentPage = 'template';
    protected function checkUserAccess(){}
    protected function checkParams(){}
    public function onPrepareComponentParams($arParams){
    }
	protected function getResult()
	{
        //$formatedMapData = array();
        //$mapData = OrganizationDomain::getOrganizationsRegionMapData();
        $ArrayResult = OrganizationTable::getList(array(
            'filter' => array(
                'ORGANIZATION_STATUS'=>OrganizationStatus::ENABLED,
            ),
            'select' => array(
                'id'=>'ORGANIZATION_ID',
                'x'=>'ORGANIZATION_COORDINATE_X',
                'y'=>'ORGANIZATION_COORDINATE_Y',
                'name'=>'ORGANIZATION_NAME',
            )
        ))->fetchAll();
        foreach($ArrayResult as $index=>$mapDataItem){
            $ArrayResult[$index]['url'] = OrganizationInfoUrlHelper::getOrganizationMainPageUrl($mapDataItem['id']);
            //$formatedMapData[$mapDataItem['id']] = $mapDataItem;
        }

        $this->arResult['mapData'] = $ArrayResult;
	}
}
?>