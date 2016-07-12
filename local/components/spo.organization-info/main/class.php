<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use Spo\Site\Core\SPOComponent;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Helpers\RegionInfoUrlHelper;
use Spo\Site\Helpers\OrganizationInfoUrlHelper;
use Spo\Site\Dictionaries\OrganizationStatus;
use Spo\Site\Entities\AbiturientProfileTable;
use Spo\Site\Util\UiMessage;

class OrganizationInfo extends SPOComponent
{

	public $componentRootUrl = '/organization-info/';

	public $arDefaultUrlTemplates404 = array(
		'organizationSystemPage' => '#organizationId#/#section#',
		'organizationPredefinedStaticPage' => '#organizationId#/about/#pageType#/',
		'organizationCustomStaticPage' => '#organizationId#/page/#pageId#/',
	);

	protected $arAllowedComponentVariables = array('organizationId', 'section', 'pageId', 'pageType');
	protected $componentPage = 'organizationPredefinedStaticPage';

    protected function checkParams()
    {
    }

    protected function checkUserAccess()
    {
    }

	protected function getResult()
	{		
		GLOBAL $USER;
		$ID=$USER->GetID();
		if(!empty($ID)){
			$params = array(
				'filter' => array(
					'=USER_ID' => $ID,
					'=SPO_ABITURIENT_PROFILE_IS_CORRECT' => 1,
				),
				'select' => array(
					'SPO_ABITURIENT_PROFILE_IS_CORRECT',
				)
			);
			$resultDB = AbiturientProfileTable::getList($params)->fetchAll();
			if(count($resultDB)==0){
				$this->arResult['EroreUser']=1;
			}
		}
		if($_GET['erroUser']==1){
			UiMessage::addMessage(
				'Вы не заполнили коректно свой профиль',
				UiMessage::TYPE_WARNING
			);			
		}
		$requestedOrganizationId = $this->arResult['VARIABLES']['organizationId'];
		
		if (empty($requestedOrganizationId))
			throw new Main\ArgumentNullException('organizationId');

        $organizationDomain = OrganizationDomain::loadById($requestedOrganizationId);

        if ($organizationDomain->getStatus($requestedOrganizationId) != OrganizationStatus::ENABLED)
            LocalRedirect('/404.php');

		$this->arResult['organizationName'] = $organizationDomain->getOrganizationName($requestedOrganizationId);

        $this->breadcrumbs['Образовательные организации'] = RegionInfoUrlHelper::getOrganizationListUrl();
        $this->breadcrumbs[$this->arResult['organizationName']] = OrganizationInfoUrlHelper::getOrganizationMainPageUrl($requestedOrganizationId);
	}

}
?>