<?php
namespace Spo\Site\Adapters;

use Spo\Site\Domains\OrganizationPageDomain;
use Spo\Site\Doctrine\Entities\OrganizationPage;
use Spo\Site\Doctrine\Entities\OrganizationPageFile;
use Spo\Site\Dictionaries\OrganizationPageType;
use Spo\Site\Util\CVarDumper;
use CFile;

class OrganizationPageDomainAdapter
{
    public static function getOrganizationPageList(OrganizationPageDomain $organizationPageDomain)
    {
        $result = array();

        /* @var OrganizationPage $organization */
        $pages = $organizationPageDomain->getEntityCollection();
        foreach ($pages as $page) {
            /** @var OrganizationPage $page */
            $result[] = array(
                'pageId'      => $page->getOrganizationPageId(),
                'pageTypeStr' => OrganizationPageType::getValue($page->getOrganizationPageType()),
            );
        }
        return array(
            'list'       => $result,
            'totalCount' => $organizationPageDomain->getTotalCount()
        );
    }

    public static function getOrganizationPageWithFiles(OrganizationPageDomain $organizationPageDomain)
    {
        /* @var OrganizationPage $page */
        /* @var OrganizationPageFile $file */
        $page  = $organizationPageDomain->getModel();
        $files = $page->getOrganizationPageFiles();
        $filesData = array();
        foreach($files as $file)
        {
            $bFile = $file->getFile();
            $id = $file->getOrganizationPageFileId();
            $filesData[] = array(
                'organization_page_file_title' => $file->getOrganizationPageFileTitle(),
                //'organization_page_file_title' => CVarDumper::dump($bFile),
                'organization_page_file_id' => $id,
                'href' => CFile::GetPath($bFile->getId())
                //'' => $bFile->
            );
        }

        return array(
            'pageId'      => $page->getOrganizationPageId(),
            'pageContent' => $page->getOrganizationPageContent(),
            'pageTypeStr' => OrganizationPageType::getValue($page->getOrganizationPageType()),
            'files'       => $filesData,
        );
    }
}