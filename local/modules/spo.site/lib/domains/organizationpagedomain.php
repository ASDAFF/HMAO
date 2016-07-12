<?php
namespace Spo\Site\Domains;

use Spo\Site\Core\SPODomain;
use Spo\Site\Dictionaries\OrganizationPageType;
use Spo\Site\Doctrine\Entities\OrganizationPage;
use Spo\Site\Doctrine\Entities\OrganizationPageFile;
use Spo\Site\Doctrine\Entities\BitrixFile;
use Spo\Site\Doctrine\Repositories\BitrixFileRepository;
use Spo\Site\Doctrine\Repositories\OrganizationPageRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Bitrix\Main;
use Bitrix\Main\SystemException;
use Spo\Site\Helpers\PagingHelper;
use Spo\Site\Util\CVarDumper;
use CFile;

class OrganizationPageDomain extends SPODomain
{
	/** @var  OrganizationPage */
    const DEFAULT_FILENAME = 'Файл';
	protected $entity;

    public static function loadById($organizationPageId, $organizationId = 0)
    {
        $repo = OrganizationPageRepository::create()
            ->getOrganizationPages()
            ->withFiles()
            ->byId($organizationPageId);

        if($organizationId > 0){
            $repo->byOrganizationId($organizationId);
        }

        $organization = $repo->one();

        if($organization === null){
            throw new Main\SystemException('Not Found Exception');
        }

        return new self($organization);
    }

	public static function listOrganizationCustomPagesWithoutContent($organizationId)
	{
		return new OrganizationPageDomain(
			null,
			OrganizationPageRepository::create()
				->getOrganizationPageListWithoutContent()
				->byOrganizationId($organizationId)
				->byPageType(OrganizationPageType::COMMON)
				->all()
		);
	}

    public static function listWithoutContent($organizationId = 0)
    {
        $repo = OrganizationPageRepository::create()
            ->getOrganizationPageListWithoutContent();

        if($organizationId > 0)
        {
            $repo->byOrganizationId($organizationId);
        }

        $pagination = new Paginator($repo->queryBuilder);

        $totalCount = count($pagination);
        $pages = $repo->all();

        return new self(null, $pages, $totalCount);
    }

    public function populate($data)
    {
        if(isset($data['organizationPageContent'])){
            $this->entity->setOrganizationPageContent($data['organizationPageContent']);
        }

        if(isset($data['organizationPageType'])){
            $this->entity->setOrganizationPageType($data['organizationPageType']);
        }

        if(isset($data['organizationPageFileTitle']))
        {
            $titles = $data['organizationPageFileTitle'];
            foreach($titles as $fileId => $fileTitle)
            {
                /* @var OrganizationPageFile $file*/
                $file = $this->entity->getOrganizationPageFileById($fileId);
                if($file === null){continue;}

                $file->setOrganizationPageFileTitle(trim($fileTitle) !== '' ? $fileTitle : self::DEFAULT_FILENAME);
                $this->persistEntity($file);
            }
        }

        if(isset($data['deletableFiles']))
        {
            $deletableFiles  = $data['deletableFiles'];
            foreach($deletableFiles as $fileId)
            {
                $fileId = intval($fileId);
                /* @var OrganizationPageFile $file*/
                $file = $this->entity->getOrganizationPageFileById($fileId);
                if($file === null){continue;}

                //todo Ситуация: в данный момент такая конструкция работает так как у таблицы
                //todo spo_organization_page_file поле file_id может быть null и при удалениее
                //todo из b_file проставляется null, что не совсем правильно, но пока что этот вариант
                //todo лучше, чем в домене после вызова save вызывать еще удаление файлов
                //todo блок try catch нужн так как удаление происходит до вызова save, в котором возможны ошибки
                //todo если блок убрать, файл в дальнейшем будет нельзя удалить из за ошибок в Delete
                try
                {
                    CFile::Delete($file->getFile()->getId());
                }
                catch(\Exception $ex)
                {
                }
                $this->entity->removeOrganizationPageFile($file);
                $this->removeEntity($file);
            }
        }

        $this->persistEntity($this->entity);
    }

	public static function loadOrganizationPageByType($organizationId, $pageType)
	{
		$page = OrganizationPageRepository::create()
			->getOrganizationPages()
			->byOrganizationId($organizationId)
			->byPageType($pageType)->one();

		if ($page === null)
			throw new Main\SystemException('Not Found Exception');
		return new OrganizationPageDomain($page);
	}

	public static function initOrganizationSystemPages($organizationId)
	{
		$organizationDomain = OrganizationDomain::loadById($organizationId);
		$organizationPageDomain = new OrganizationPageDomain();

		foreach (OrganizationPageType::getValuesArray() as $key => $value) {
			
			if ($key == OrganizationPageType::COMMON)
				continue;

			$page = OrganizationPageRepository::create()
				->getOrganizationPageListWithoutContent()
				->byOrganizationId($organizationId)
				->byPageType($key)->one();

			if ($page !== null)
				continue;

			$newPage = new OrganizationPage();
			$newPage->setOrganizationPageType($key);
			$newPage->setOrganization($organizationDomain->getModel());
			$newPage->setOrganizationPageContent('');

			$organizationPageDomain->persistEntity($newPage);

		}

		return $organizationPageDomain;
	}

    public function addFileById($id, $fileTitle)
    {
        $bFile = BitrixFileRepository::create()
            ->getBitrixFile()
            ->byId($id)
            ->one();

        $pageFile = new OrganizationPageFile();

        $pageFile
            ->setFile($bFile)
            ->setOrganizationPage($this->entity)
            ->setOrganizationPageFileTitle(trim($fileTitle) !== '' ? $fileTitle : self::DEFAULT_FILENAME);

        $this->entity->addOrganizationPageFile($pageFile);
        $this->persistEntity($this->entity);
    }

//	public static function createOrganization()
//	{
//		$organization = new Organization();
//		return new OrganizationDomain($organization);
//	}
}