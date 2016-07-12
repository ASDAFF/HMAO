<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
/*use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Domains\OrganizationPageDomain;
use Spo\Site\Domains\ApplicationDomain;*/
//use Spo\Site\Helpers\PagingHelper;
//use Spo\Site\Adapters\OrganizationDomainAdapter;
//use Spo\Site\Adapters\OrganizationPageDomainAdapter;
//use Spo\Site\Adapters\ApplicationDomainAdapter;
use Spo\Site\Util\ApplicationListFilter;
use Spo\Site\Entities\OrganizationTable;
use Spo\Site\Entities\ApplicationTable;
use Spo\Site\Entities\AbiturientProfileTable;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Dictionaries\ApplicationFundingType;
use Spo\Site\Entities\OrganizationPageTable;
use Spo\Site\Dictionaries\OrganizationPageType;
use Spo\Site\Entities\OrganizationEmployeeTable;

class OrganizationIndexComponent extends OrganizationOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('Главная' => '');

	protected function getResult()
	{
		global $USER;
        $ArrayResul = OrganizationEmployeeTable::getList(array(
            'filter' => array(
                'USER_MODERATOR'=>$USER->GetID(),
            ),
            'select' => array(
                'USER_MODERATOR',
            )
        ))->fetchAll();

        if(count($ArrayResul)>0 and $ArrayResul[0]['USER_MODERATOR']!='' and $ArrayResul[0]['USER_MODERATOR']!=0){
            $this->arResult['NeModerator']=0;
        }
        else{
            $this->arResult['NeModerator']=1;
        }
        $ArrayResult = OrganizationTable::getList(array(
            'filter' => array(
                'LOGIC' => 'OR',
                'ORGANIZATION_EMPLOYEE.USER_ID'=>$USER->GetID(),
                'ORGANIZATION_EMPLOYEE.USER_MODERATOR'=>$USER->GetID(),
            ),
            'select' => array(
                'CITY.CITY_NAME',
                'REGION_AREA.REGION_AREA_ID',
                'ORGANIZ_ID'=>'ORGANIZATION_ID',
            )
        ))->fetchAll();
        //$organization = OrganizationDomain::loadByEmployeeUserId($USER->GetID());
        //$organization->getOrganizationPages();
        //$paging = new PagingHelper();
        $filter = new ApplicationListFilter();
        $filter->setOrderBy(ApplicationListFilter::ORDER_DESC);
        $filter->setSortField('applicationId');
        //$applicationDomain = ApplicationDomain::getOrganizationApplicationList($organization->getOrganizationId(), $paging, $filter);
        $Order=array();
        if($filter !== null)
        {
            if($filter->wasYearSet()){
                //echo $filter->getYear()."<br>";
                $date = new \Bitrix\Main\Type\DateTime("01.01.".$filter->getYear()." 00:00:00");
                $date2 = $filter->getYear()+1;
                $date2 = new \Bitrix\Main\Type\DateTime("01.01.".$date2." 00:00:00");
                $Filter['<APPLICATION_CREATION_DATE']=$date;
                $Filter['>APPLICATION_CREATION_DATE']=$date2;
                //$repo->filterByYear($filter->getYear());
            }

            if($filter->wasStatusSet()){
                //echo $filter->getStatus()."<br>";
                $Filter['APPLICATION_STATUS']=$filter->wasStatusSet();
                //$repo->filterByStatus($filter->getStatus());
            }

            if($filter->wasFundingSet()){
                //echo $filter->getFunding()."<br>";
                $Filter['APPLICATION_FUNDING_TYPE']=$filter->getFunding();
                //$repo->filterByFundingType($filter->getFunding());
            }

            if($filter->wasSortFieldSet()){
                $Order=array('APPLICATION_ID'=>$filter->getOrderBy());
                //echo $filter->getSortField()." ".$filter->getOrderBy()."<br>";
                //$repo->orderBy($filter->getSortField(), $filter->getOrderBy());
            }else{
                $Order=array('APPLICATION_ID'=>$filter->getOrderBy());
                //echo $filter->getOrderBy()."<br>";
                //$repo->orderByApplicationId($filter->getOrderBy());
            }
        }
        // temporary limit
        $applicationLimit = 20;
        $IDORGID = $ArrayResult[0]['ORGANIZ_ID'];
        $Filter['ORGANIZATION.ORGANIZATION_ID'] = $IDORGID;
        $ArrayResult = ApplicationTable::getList(array(
            'filter' => $Filter,
            'order'  => $Order,
            'select' => array(
                'id'=>'APPLICATION_ID',
                'creationDate'=>'APPLICATION_CREATION_DATE',
                'specialtyTitle'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'specialtyCode'=>'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'studyPeriod'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                'studyMode'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
                'trainingLevel'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_LEVEL',
                'baseEducation'=>'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'status'=>'APPLICATION_STATUS',
                'statusCode'=>'APPLICATION_STATUS',
                'applicationFundingType'=>'APPLICATION_FUNDING_TYPE',
                'needHostel'=>'SPO_APPLICATION_NEED_HOSTEL',
                'organizationName'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_NAME',
                'organizationId'=>'ORGANIZATION_SPECIALTY.ORGANIZATION.ORGANIZATION_ID',
                'LAST_NAME'=>'USER.LAST_NAME',
                'NAME'=>'USER.NAME',
                'SECOND_NAME'=>'USER.SECOND_NAME',
                'EMAIL'=>'USER.EMAIL',
                'userId'=>'USER.ID',
                'USER_VALID_ID'=>'ABITURIENT_USER_ID.USER_VALID_ID',
            ),
            'limit' => $applicationLimit
        ));

        // $row - application
        // Группировка заявок по статусу обработки -> текущ.заявки / архив
        $archiveList = $currentList = array();
        while ($row = $ArrayResult->fetch())
        {
            $row['studyMode'] = StudyMode::getValue($row['studyMode']);
            $row['baseEducation'] = BaseEducation::getValue($row['baseEducation']);
            $row['status'] = ApplicationStatus::getValue($row['status']);
            $row['applicationFundingType'] = ApplicationFundingType::getValue($row['applicationFundingType']);
            $row['user'] = array(
                'userFullName' => $row['LAST_NAME']." ".$row['NAME']." ".$row['SECOND_NAME'],
                'userEmail' => $row['EMAIL'],
                'userId' => $row['userId'],
                'user_valid_id' => $row['USER_VALID_ID'],
            );

            unset($row['LAST_NAME'], $row['NAME'], $row['SECOND_NAME'],
                $row['EMAIL'], $row['userId'], $row['USER_VALID_ID']);

            if($row['statusCode'] == 1){
                $currentList[$row['id']] = $row;
            } else {
                $archiveList[$row['id']] = $row;
            }
        }

        $data = array(
            'currentList' => $currentList,
            'archiveList' => $archiveList,
        );

        //$data = ApplicationDomainAdapter::getApplicationList($applicationDomain);
        $organizationModel = OrganizationTable::getList(array(
            'filter' => array('ORGANIZATION_ID'=>$IDORGID),
            'select' => array(
                'organizationId'=>'ORGANIZATION_ID',
                'organizationName'=>'ORGANIZATION_NAME',
                'organizationFullName'=>'ORGANIZATION_FULL_NAME',
                'organizationFoundationYear'=>'ORGANIZATION_FOUNDATION_YEAR',
                'organizationAddress'=>'ORGANIZATION_ADDRESS',
                'organizationEmail'=>'ORGANIZATION_EMAIL',
                'organizationPhone'=>'ORGANIZATION_PHONE',
                'organizationSite'=>'ORGANIZATION_SITE',
                'organizationCoordinateX'=>'ORGANIZATION_COORDINATE_X',
                'organizationCoordinateY'=>'ORGANIZATION_COORDINATE_Y',
                'city'=>'CITY.CITY_NAME',
                'cityId'=>'CITY_ID',
                'regionArea'=>'REGION_AREA_ID',
                'regionAreaName'=>'REGION_AREA.REGION_AREA_NAME',
            )
        ))->fetchAll();
        $organizationModel=$organizationModel[0];
        $this->arResult['organizationModel'] = $organizationModel;
            //$this->arResult['organizationModel'] = OrganizationDomainAdapter::getOrganizationInformation($organization);
        $ArrayResult = OrganizationPageTable::getList(array(
            'filter' => array('ORGANIZATION_ID'=>$IDORGID),
            'select' => array(
                'pageId'=>'ORGANIZATION_PAGE_ID',
                'pageTypeStr'=>'ORGANIZATION_PAGE_TYPE',
            )
        ));
        while ($row = $ArrayResult->fetch())
        {
            $row['pageTypeStr']=OrganizationPageType::getValue($row['pageTypeStr']);
            $pages[] = $row;
        }
        $totalCount = count($pages);
        $pages=array('list'=>$pages,'totalCount'=>$totalCount);
        //$pageDomain = OrganizationPageDomain::listWithoutContent($organization->getOrganizationId());
        $this->arResult['pages']             = $pages;
        $this->arResult['applicationList']   = $data;
	}
}
?>