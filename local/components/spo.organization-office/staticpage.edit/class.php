<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
//use Spo\Site\Domains\OrganizationDomain;
//use Spo\Site\Adapters\OrganizationPageDomainAdapter;
//use Spo\Site\Domains\OrganizationPageDomain;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpRequest;
use Spo\Site\Helpers\UploadedFileHelper;
use Spo\Site\Entities\OrganizationTable;
use Spo\Site\Entities\OrganizationPageFileTable;
use Spo\Site\Dictionaries\OrganizationPageType;
use Spo\Site\Entities\OrganizationPageTable;

class StaticPageEditComponent extends OrganizationOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('Редактирование страницы' => '');

	protected function getResult()
	{
		global $USER;
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();

        $organizationPageId = intval($request->get('pageId'));
        //$organizationDomain = OrganizationDomain::loadByEmployeeUserId($USER->GetID());
        $organizationPageData = $request->getPost('OrganizationPage');
        // Если форма отправлена - пытаемся обновить организацию
        $ArrayResult = OrganizationTable::getList(array(
            'filter' => array(
                'LOGIC' => 'OR',
                'ORGANIZATION_EMPLOYEE.USER_ID'=>$USER->GetID(),
                'ORGANIZATION_EMPLOYEE.USER_MODERATOR'=>$USER->GetID(),
                'ORGANIZATION_PAGE.ORGANIZATION_PAGE_ID'=>$organizationPageId,
            ),
            'select' => array(
                'pageId'=>'ORGANIZATION_PAGE.ORGANIZATION_PAGE_ID',
            )
        ))->fetchAll();
        if($request->isPost())
        {
            $this->populateData($request, $organizationPageData,$ArrayResult[0]['pageId']);
        }
        $ArrayResult = OrganizationTable::getList(array(
            'filter' => array(
                'ORGANIZATION_EMPLOYEE.USER_ID'=>$USER->GetID(),
                'ORGANIZATION_PAGE.ORGANIZATION_PAGE_ID'=>$organizationPageId,
            ),
            'select' => array(
                'ORGANIZ_ID'=>'ORGANIZATION_ID',
                'pageId'=>'ORGANIZATION_PAGE.ORGANIZATION_PAGE_ID',
                'pageContent'=>'ORGANIZATION_PAGE.ORGANIZATION_PAGE_CONTENT',
                'pageTypeStr'=>'ORGANIZATION_PAGE.ORGANIZATION_PAGE_TYPE',
            )
        ))->fetchAll();
        $ArrayResult[0]['pageTypeStr']=OrganizationPageType::getValue($ArrayResult[0]['pageTypeStr']);
        foreach ($ArrayResult as $item) {
            $file = OrganizationPageFileTable::getList(array(
                'filter' => array(
                    'ORGANIZATION_PAGE_ID'=>$item['pageId'],
                ),
                'select' => array(
                    'organization_page_file_title'=>'ORGANIZATION_PAGE_FILE_TITLE',
                    'organization_page_file_id'=>'ORGANIZATION_PAGE_FILE_ID',
                    'FILE_ID',
                )
            ));
            while ($row = $file->fetch())
            {
                $row['href'] =  CFile::GetPath($row['FILE_ID']);
                unset($row['FILE_ID']);
                $ArrayResult[0]['files'][] = $row;
            }
        }
        $data=$ArrayResult[0];


        //$organizationPageDomain = OrganizationPageDomain::loadById($organizationPageId, $organizationDomain->getOrganizationId());

        /*$data = OrganizationPageDomainAdapter::getOrganizationPageWithFiles($organizationPageDomain);*/
        $this->arResult['organizationPageData'] = $data;
        $this->breadcrumbs[$data['pageTypeStr']] = '';
	}

    protected function populateData(HttpRequest $request, $organizationPageData,$ID)
    {
        //\Spo\Site\Util\CVarDumper::dump($_FILES['OrganizationPageFile']);
        //\Spo\Site\Util\CVarDumper::dump($organizerPageFileData);exit;

        if(!empty($_FILES['OrganizationPageFile']['name'][0])) {
            for ($i = 0; count($_FILES['OrganizationPageFile']['name']) > $i; $i = $i + 1) {
                $file['name'] = $_FILES['OrganizationPageFile']['name'][$i];
                $file['size'] = $_FILES['OrganizationPageFile']['size'][$i];
                $file['tmp_name'] = $_FILES['OrganizationPageFile']['tmp_name'][$i];
                $file['type'] = $_FILES['OrganizationPageFile']['type'][$i];
                $fid = CFile::SaveFile($file);
                if($_POST['OrganizationPageFile'][$i]){
                    $failName=$_POST['OrganizationPageFile'][$i];
                }
                else {
                    $failName="Файл";
                }
                $result = OrganizationPageFileTable::add(array(
                    'FILE_ID' => $fid,
                    'ORGANIZATION_PAGE_ID' => $ID,
                    'ORGANIZATION_PAGE_FILE_TITLE' => $failName,
                ));
            }
        }
        foreach ($organizationPageData['deletableFiles'] as $item) {
            $file = OrganizationPageFileTable::getList(array(
                'filter' => array(
                    'ORGANIZATION_PAGE_FILE_ID'=>$item,
                ),
                'select' => array(
                    'FILE_ID',
                    'ORGANIZATION_PAGE_FILE_ID'
                    //'files'=>'ORGANIZATION_PAGE.ORGANIZATION_PAGE_FILE.ORGANIZATION_PAGE_ID',
                )
            ))->fetchAll();
            $result = OrganizationPageFileTable::delete($file[0]['ORGANIZATION_PAGE_FILE_ID']);
        }
        OrganizationPageTable::update($ID, array(
            'ORGANIZATION_PAGE_CONTENT' => $organizationPageData['organizationPageContent'],
        ));
        /*$uploader = new UploadedFileHelper('OrganizationPageFile');

        $organizationPageDomain->populate($organizationPageData);

        $isValid = $organizationPageDomain->validate();
        if(!$isValid)
        {
            $this->arResult['errors'] = $organizationPageDomain->getErrors();
            return;
        }

        if($uploader->hasFiles())
        {
            $organizationPageFileData = $request->getPost('OrganizationPageFile');

            foreach($organizationPageFileData as $key => $fileName)
            {
                $fileId = $uploader->tryUploadFile($key);
                if($fileId === null)
                {
                    $this->arResult['errors'] = 'Ошибка добавления файла';
                    return;
                }
                $organizationPageDomain->addFileById($fileId, $fileName);
            }
        }
        if(!$organizationPageDomain->save(false))
        {
            throw new Main\DB\Exception('Ошибка при сохранении данных');
        }
        else
        {
            $this->arResult['success'] = 'Данные успешно обновлены';
        }*/
    }
}
?>