<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Spo\Site\Util\SpoConfig;
//use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Adapters\OrganizationDomainAdapter;
//use Spo\Site\Domains\CityDomain;
//use Spo\Site\Domains\RegionDomain;
use Spo\Site\Adapters\RegionDomainAdapter;
use Spo\Site\Adapters\CityDomainAdapter;
//use Spo\Site\Exceptions\DomainException;
//use Spo\Site\Util\Notification\SmsNotifier;
use Spo\Site\Entities\OrganizationTable;

class OrganizationEditComponent extends OrganizationOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('Основная информация' => '');
	protected function getResult()
	{
		global $USER;
        $ArrayResult = OrganizationTable::getList(array(
            'filter' => array(
                'LOGIC' => 'OR',
                'ORGANIZATION_EMPLOYEE.USER_ID'=>$USER->GetID(),
                'ORGANIZATION_EMPLOYEE.USER_MODERATOR'=>$USER->GetID(),
            ),
            'select' => array(
                'CITY.CITY_NAME',
                'REGION_AREA.REGION_AREA_ID',
                'ORGANIZ_ID'    =>  'ORGANIZATION_ID'
            )
        ))->fetchAll();
        $IDORGID=$ArrayResult[0]['ORGANIZ_ID'];

        //$organizationDomain = OrganizationDomain::loadByEmployeeUserId($USER->GetID());

        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();
        $organizerData = $request->getPost('Organization');
        // Если форма отправлена - пытаемся обновить организацию
        if($organizerData)
        {
            $result = OrganizationTable::update($IDORGID, array(
                'ORGANIZATION_NAME'             => $organizerData['organizationName'],
                'ORGANIZATION_FULL_NAME'        => $organizerData['organizationFullName'],
                'ORGANIZATION_FOUNDATION_YEAR'  => $organizerData['organizationFoundationYear'],
                'ORGANIZATION_ADDRESS'          => $organizerData['organizationAddress'],
                'ORGANIZATION_EMAIL'            => $organizerData['organizationEmail'],
                'ORGANIZATION_PHONE'            => $organizerData['organizationPhone'],
                'ORGANIZATION_SITE'             => $organizerData['organizationSite'],
                'ORGANIZATION_COORDINATE_X'     => $organizerData['organizationCoordinateX'],
                'ORGANIZATION_COORDINATE_Y'     => $organizerData['organizationCoordinateY'],
                'CITY_ID'                       => $organizerData['city'],
                'REGION_AREA_ID'                => $organizerData['regionArea'],
                'ORGANIZATION_HOSTEL'           => $organizerData['hostel'],
            ));
            if(!$result)
            {
                throw new Main\DB\Exception('Ошибка при сохранении данных');
            }
            else
            {
                $this->arResult['success'] = 'Данные успешно обновлены';
            }
        }

        $this->arResult['citiesList'] = CityDomainAdapter::listCities(
            //CityDomain::getCitiesList()
        );
        $this->arResult['regionAreasList'] = RegionDomainAdapter::listRegionAreas(
            //RegionDomain::getById(
                SpoConfig::getSiteRegionId()
            //)
        );
        $data = OrganizationDomainAdapter::getOrganizationInformation($IDORGID/*$organizationDomain*/);
        $this->arResult['organizationData'] = $data;
	}
}
?>