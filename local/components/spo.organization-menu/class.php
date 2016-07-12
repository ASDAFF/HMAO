<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('spo.site');

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use Spo\Site\Helpers\OrganizationInfoUrlHelper;
use Bitrix\Main\Loader;
use Spo\Site\Dictionaries\OrganizationPageType;
use Spo\Site\Adapters\OrganizationPageDomainAdapter;

class OrganizationMenu extends CBitrixComponent
{
	public function onIncludeComponentLang()
	{
		Loc::loadMessages(__DIR__ . '../../../../messages.php');
	}

	protected function getResult()
	{
		$organizationId = $this->arParams['organizationId'];

		if (empty($organizationId))
			throw new Main\ArgumentNullException('organizationId');

		$systemAndPredefinedPages = array(
			'Главная' => OrganizationInfoUrlHelper::getOrganizationMainPageUrl($organizationId),
            'Специальности подготовки' => OrganizationInfoUrlHelper::getOrganizationSpecialtiesUrl($organizationId),
            'Правила приёма' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::ENTRANCE_RULES),
            'Контрольные цифры приёма' => OrganizationInfoUrlHelper::getOrganizationControlOfEntranceUrl($organizationId),
            'Программа вступительных экзаменов' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::ENTRANCE_PROGRAM),
			'Расписание вступительных экзаменов' => OrganizationInfoUrlHelper::getOrganizationEntryExamsScheduleUrl($organizationId),
			'Результаты вступительных экзаменов ' => OrganizationInfoUrlHelper::getOrganizationEntryExamsResultUrl($organizationId),
			'Творческие испытания' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::CREATIVE_TESTS),
			'Информация о количестве поданных заявлений' => OrganizationInfoUrlHelper::getOrganizationStatisticApplication($organizationId),
			'Приказы о зачислении' =>  OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::ORDER_ADMISSION),
			'Список лиц, рекомендованных к зачислению' => OrganizationInfoUrlHelper::getOrganizationErenrollmentUrl($organizationId),
//            'Количество поданных заявлений' => OrganizationInfoUrlHelper::getOrganizationApplicationsCountUrl($organizationId),
			'Основные сведения' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::BASIC_INFORMATION),
			'Структура и органы управления' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::STRUCTURE_AND_MANAGEMENT),
			'Документы' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::DOCUMENTS),
			'Образование' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::EDUCATION),
			'Образовательные стандарты' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::EDUCATIONAL_STANDARDS),
			'Руководство. Педагогический состав.' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::MANAGEMENT_TEACHING_STAFF),
			'Материально техническое обеспечение' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::LOGISTICAL_SUPPORT),
			'Стипендии и материальная поддержка' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::SCHOLARSHIPS),
			'Платные образовательные услуги' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::PAID_EDUCATIONAL_SERVICES),
			'Финансово-хозяйственная деятельность' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::FINANCIAL_AND_ECONOMIC_ACTIVITY),
			'Вакантные места для приёма' => OrganizationInfoUrlHelper::getOrganizationPredefinedPageUrl($organizationId, OrganizationPageType::VACANCY),
		);

//		$customPages = OrganizationPageDomainAdapter::getOrganizationPageList(
//			OrganizationPageDomain::listOrganizationCustomPagesWithoutContent($organizationId)
//		);

		$this->arResult['menu'] = array(
			'predefinedPages' => $systemAndPredefinedPages,
		);
	}

	public function executeComponent()
	{
		Loader::includeModule('spo.site');
		$this->getResult();
		$this->includeComponentTemplate();
	}

}
?>