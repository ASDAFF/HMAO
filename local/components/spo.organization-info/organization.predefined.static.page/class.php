<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Spo\Site\Adapters\OrganizationDomainAdapter;
//use Spo\Site\Domains\OrganizationDomain;
use Bitrix\Main;
use Spo\Site\Dictionaries\OrganizationPageType;
//use Spo\Site\Domains\OrganizationPageDomain;
//use Spo\Site\Adapters\OrganizationPageDomainAdapter;
use Spo\Site\Entities\OrganizationPageTable;


class OrganizationInfoPredefinedStaticPageComponent extends OrganizationInfo
{
    protected $componentPage = '';
	private $pageType = '';

	protected function checkParams()
	{
		if (!empty($this->arParams['pageType']))
			$this->pageType = $this->arParams['pageType'];
	}

	protected function getResult()
	{
		if (empty($this->pageType))
			$this->pageType = OrganizationPageType::BASIC_INFORMATION;

		if (!OrganizationPageType::isDefined($this->pageType) || $this->pageType == OrganizationPageType::COMMON)
			throw new Main\SystemException('Unknown Page Type');
		$ArrayResult = OrganizationPageTable::getList(array(
			'filter' => array(
				'ORGANIZATION_ID'=>$this->arParams['organizationId'],
				'ORGANIZATION_PAGE_TYPE'=>$this->pageType,
			),
			//'group'   => array('ORGANIZATION_SPECIALTY.SPECIALTY_ID'),
			//'order'   => array('ORGANIZATION_SPECIALTY.SPECIALTY_ID'=>'ASC'),
			'select' => array(
				'pageId' => 'ORGANIZATION_PAGE_ID',
				'pageContent' => 'ORGANIZATION_PAGE_CONTENT',
				'pageTypeStr' => 'ORGANIZATION_PAGE_TYPE',
				'organization_page_file_title' => 'ORGANIZATION_PAGE_FILE.ORGANIZATION_PAGE_FILE_TITLE',
				'organization_page_file_id' => 'ORGANIZATION_PAGE_FILE.ORGANIZATION_PAGE_FILE_ID',
				'href' => 'ORGANIZATION_PAGE_FILE.FILE_ID',
			)
		))->fetchAll();
		for($i=0;count($ArrayResult)>$i;$i=$i+1){
			$j=$i+1;
			if(count($ArrayResult)>=$j) {
				if ($ArrayResult[$i]['pageId'] == $ArrayResult[$j]['pageId']) {
					$file['organization_page_file_title'] = $ArrayResult[$i]['organization_page_file_title'];
					$file['organization_page_file_id'] = $ArrayResult[$i]['organization_page_file_id'];
					$file['href'] = CFile::GetPath($ArrayResult[$i]['href']);
					$files[] = $file;
				} else {
					$file['organization_page_file_title'] = $ArrayResult[$i]['organization_page_file_title'];
					$file['organization_page_file_id'] = $ArrayResult[$i]['organization_page_file_id'];
					$file['href'] = CFile::GetPath($ArrayResult[$i]['href']);
					$files[] = $file;
					$ArrayResultNew['pageId'] = $ArrayResult[$i]['pageId'];
					$ArrayResultNew['pageContent'] = $ArrayResult[$i]['pageContent'];
					$ArrayResultNew['pageTypeStr'] = OrganizationPageType::getValue($ArrayResult[$i]['pageTypeStr']);
					$ArrayResultNew['files'] = $files;
					$files = array();
				}
			}
		}
		$this->arResult['page'] = $ArrayResultNew;
		/*$this->arResult['page'] = OrganizationPageDomainAdapter::getOrganizationPageWithFiles(
			OrganizationPageDomain::loadOrganizationPageByType($this->arParams['organizationId'], $this->pageType)
		);*/
	}

}
?>