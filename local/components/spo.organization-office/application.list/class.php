<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Domains\ApplicationDomain;
use Spo\Site\Adapters\ApplicationDomainAdapter;
use Bitrix\Main\Localization\Loc;
use Spo\Site\Util\CVarDumper;
use Spo\Site\Helpers\PagingHelper;
use Spo\Site\Util\ApplicationListFilter;
//use Spo\Site\Doctrine\Entities\Organization;
use Spo\Site\Helpers\OrganizationOfficeUrlHelper as Url;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Dictionaries\ApplicationFundingType;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Util\Notification\Notifier;
use Spo\Site\Entities\AbiturientProfileTable;
use Spo\Site\Entities\OrganizationEmployeeTable;

/*==== NEw====*/
use Spo\Site\entities\ApplicationTable;



class ApplicationListComponent extends OrganizationOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('Заявления абитуриентов' => '');
    /* @var Organization */
    protected $organization = null;

    /**
     * @throws Main\ArgumentException
     */
    protected function getResult()
    {
        // Если архив заявок, то исключаем заявки в статус на рассмотрении, и наоборот для текущих заявок
        $archive = $this->arParams['ARCHIVE'];
        global $USER;
        /*======================составления фитьра================*/
        $ArrayResul = OrganizationEmployeeTable::getList(array(
            'filter' => array(
                'USER_MODERATOR'=>$USER->GetID(),
            ),
            'select' => array(
                'USER_ID',
            )
        ))->fetchAll();
        if(count($ArrayResul)>0 and $ArrayResul[0]['USER_ID']!=""){
            $filter['user_org']=$ArrayResul[0]['USER_ID'];
        }
        else {
            $filter['user_org'] = $USER->GetID();// фильтрация по пользователю
        }

        //получаем ID текущей организации
        $orgId = OrganizationEmployeeTable::getList(array(
            'filter' => array(
                'USER_ID' => $USER->GetID(),
            ),
            'select' => array(
                'orgId' => 'ORGANIZATION_ID',
            )
        ))->fetch();
        $orgId = $orgId['orgId'];


        $this->arResult['GetListSpiciality']=$this->GetListSpiciality($filter['user_org']);
        $this->arResult['GetListStudyMode']=$this->GetListStudyMode($filter['user_org']);
        $this->arResult['GetListFundingType']=$this->GetListFundingType($filter['user_org']);
        $this->arResult['GetListNeedHostel']=$this->GetListNeedHostel($filter['user_org']);
        $this->arResult['GetListBaseEducation']=$this->GetListBaseEducation($filter['user_org']);

        if(!empty($_GET['year']))
        {
            $next=(int) $_GET['year']+1;
            $date_f=array(
                "LOGIC" => "AND",
                array(">=creationDate" => '01-01-'.(int) $_GET['year']),
                array("<creationDate" => '01-01-'.$next),
            );
            array_push($filter,$date_f);
        }

        // статус заявки
        $status = (int) $_GET['status'];
        if($archive) {
            switch($status) {
                case 1:
                case 2:
                case 3:
                case 9:
                    $filter['APPLICATION_STATUS'] = $status;
                    break;
                default:
                    $filter['!=APPLICATION_STATUS'] = 1;
                    break;
            }
        } else {
            switch($status) {
                default:
                    $filter['APPLICATION_STATUS'] = 1;
                    break;
            }
        }

        if(!empty($_GET['Serch']['Data1'])) {
            $t=strtotime($_GET['Serch']['Data1']);
            $date_c=date('d.m.Y', $t);
            $date1 = new \Bitrix\Main\Type\DateTime($date_c.' 00:00:00');
            $filter['>=APPLICATION_CREATION_DATE'] = $date1; // Data1
        }
        if(!empty($_GET['Serch']['Data2'])) {
            $t=strtotime($_GET['Serch']['Data2']);
            $date_po=date('d.m.Y',$t);
            $date2 = new \Bitrix\Main\Type\DateTime($date_po.' 23:59:59');
            $filter['<=APPLICATION_CREATION_DATE'] = $date2; // Data2
        }

        if(!empty($_GET['Serch']['ID'])) $filter['%APPLICATION_ID']=(int) $_GET['Serch']['ID']; // id
        if(!empty($_GET['Serch']['Name'])){
            GLOBAl $USER;
            $ArrNAME=preg_split("/[\s]+/", $_GET['Serch']['Name']);
            for($i=0;count($ArrNAME)>$i;$i=$i+1){
                if($i==0){
                    $NameUser=$ArrNAME[$i];
                }
                else{
                    $NameUser=$NameUser.' & '.$ArrNAME[$i];
                }
            }
            if(!empty($NameUser)) {
                $arFilter = array(
                    'NAME' => $NameUser,
                );
                //print_r($arFilter);
                $cUser = new $USER;
                $sort_by = "ID";
                $sort_ord = "ASC";
                $dbUsers = $cUser->GetList($sort_by, $sort_ord, $arFilter);
                while ($arUser = $dbUsers->Fetch()) {
                    $ArrayID[]=$arUser["ID"];
                }
                if(!empty($ArrayID)) {
                    $filter['%USER.ID'] = $ArrayID;
                }
            }

        } // id
        if(!empty($_GET['speciality'])) $filter['=ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID']=(int) $_GET['speciality']; // Специальность
        if(!empty($_GET['studymode'])) $filter['=ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE']=(int) $_GET['studymode']; // Форма обучения
        if(!empty($_GET['fundingtype'])) $filter['=APPLICATION_FUNDING_TYPE']=(int) $_GET['fundingtype']; // Вид финансирования
        if(!empty($_GET['needhostel'])) $filter['=SPO_APPLICATION_NEED_HOSTEL']=(int) $_GET['needhostel']; // Потребность в общежитии
        if(!empty($_GET['baseeducation'])) $filter['=ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION']=(int) $_GET['baseeducation']; // Уровень подготовки
        if(in_array($_GET['valid'], array('1','0'))) $filter['=ABITURIENT_USER_ID.VALIDITY']= (int) $_GET['valid'];
        /*======================групперовка=================================*/
        //пораметр групперовки
        if(!empty($_GET['orderBy']))
            $orderBy=$_GET['orderBy'];
        else
            $orderBy='ASC';
        if(!empty($_GET['orderField']))
        {
            switch ($_GET['orderField']) {
                case 'userLastname':
                    $order['userLastname'] = $orderBy;
                    break;
                case 'applicationId':
                    $order['id'] = $orderBy;
                    break;
                case 'applicationCreateDate':
                    $order['creationDate'] = $orderBy;
                    break;
                default:
                    $order['id'] = $orderBy;
            }
        }
        else
        {
            $order['creationDate']="DESC";
        }

        /*===========================Пагинация=========================*/
        $limit = $_GET['limit'];
        if (empty($limit)) $limit = 10;
        if($_GET['page']>1)
        {
            $offset=$limit*((int)$_GET['page']-1);
        }
        else
        {
            $offset=0;
        }
        /* var_dump($limit*(int)$_GET['page']-1);*/
        $Selct = array(
            'id'                =>  'APPLICATION_ID',
            'creationDate'      =>  'APPLICATION_CREATION_DATE',
            'specialtyCode'     =>  'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
            'studyPeriod'       =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
            'baseEducation'     =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
            'fundingType'       =>  'APPLICATION_FUNDING_TYPE',
            'organizationName'  =>  'ORGANIZATION.ORGANIZATION_NAME',
            'organizationId'    =>  'ORGANIZATION_ID',
            'needHostel'        =>  'SPO_APPLICATION_NEED_HOSTEL',
            'user_id'           =>  'USER.ID',
            'userLastname'      =>  'USER.LAST_NAME',
            'user_org'          =>  'ORGANIZATION.ORGANIZATION_EMPLOYEE.USER_ID',
            'docorigin'         =>  'ABITURIENT_USER_ID.DOCORIGIN',
        );
        if(count($_GET['Shapka'])==0){
            $Selct = array(
                'id'                =>  'APPLICATION_ID',
                'creationDate'      =>  'APPLICATION_CREATION_DATE',//дата подачи
                'specialtyTitle'    =>  'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'specialtyCode'     =>  'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'studyMode'         =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
                'studyPeriod'       =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                'baseEducation'     =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'status'            =>  'APPLICATION_STATUS',// статус заявки
                'fundingType'       =>  'APPLICATION_FUNDING_TYPE',
                'needHostel'        =>  'SPO_APPLICATION_NEED_HOSTEL',
                'organizationName'  =>  'ORGANIZATION.ORGANIZATION_NAME',
                'organizationId'    =>  'ORGANIZATION_ID',
                'user_id'           =>  'USER.ID',
                'userLastname'      =>  'USER.LAST_NAME',
                'user_org'          =>  'ORGANIZATION.ORGANIZATION_EMPLOYEE.USER_ID',
                'docorigin'         =>  'ABITURIENT_USER_ID.DOCORIGIN',
            );
        }


        foreach($_GET['Shapka'] as $item){
            if($item=='specialtyTitle') {
                $Selct['specialtyTitle'] = 'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE';
                $this->arResult['selecttid']['specialtyTitle'] = 1;
            }
            if($item=='studyMode') {
                $Selct['studyMode'] = 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE';
                $this->arResult['selecttid']['studyMode'] = 1;
            }
            if($item=='fundingType') {
                $Selct['fundingType'] = 'APPLICATION_FUNDING_TYPE';
                $this->arResult['selecttid']['fundingType'] = 1;
            }
            if($item=='needHostel') {
                $Selct['needHostel'] = 'SPO_APPLICATION_NEED_HOSTEL';
                $this->arResult['selecttid']['needHostel'] = 1;
            }
            if($item=='trainingLevel') {
                $Selct['trainingLevel'] = 'APPLICATION_FUNDING_TYPE';
                $this->arResult['selecttid']['trainingLevel'] = 1;
            }
            if($item=='creationDate') {
                $Selct['creationDate'] = 'APPLICATION_CREATION_DATE';
                $this->arResult['selecttid']['creationDate'] = 1;
            }
            if($item=='status') {
                $Selct['status'] = 'APPLICATION_STATUS';
                $this->arResult['selecttid']['status'] = 1;
            }
        }

        $applicationDb = ApplicationTable::getList(array(
            'select'                =>  $Selct,
            'filter'                =>  $filter,
            'order'                 =>  $order,
            'offset'                =>  $offset,
            'limit'                 =>  $limit,
        ));
        $applicationCountDb = ApplicationTable::getList(array(
            'select'                =>  $Selct,
            'filter'                =>  $filter,
        ));
        //var_dump($order);

        $applicationList = array();
        while($application = $applicationDb->fetch()){
            $applicationList[$application['id']] = $application;
        }
        $ArrayResult['list'] = $applicationList;
        $ArrayResult['totalCount'] = count($applicationCountDb->fetchAll()); // общее количество
        $this->arResult['MassivTitel'] = array(
            //Специальность
            'specialtyTitle'=>'Специальность',
            //Форма обучение
            'studyMode'=>'Форма обучения',
            //Вид финансирования
            'fundingType'=>'Вид финансирования',
            //Общижитие
            'needHostel'=>'Потребность в общежитии',
            //Вид финансирования
            'trainingLevel'=>'Уровень подготовки',
            //Дата подачи
            'creationDate'=>'Дата подачи',
            //Статус
            'status'=>'Статус',
        );

        /*==============================Удаления лишних записей добовления записи пользователя и перевот даты согласно записи==========================*/
        foreach ($ArrayResult['list'] as &$AR)
        {
            $rsUser = CUser::GetByID($AR['user_id']);
            $arUser = $rsUser->Fetch();
            $AR['creationDate']=date('d-m-Y',strtotime($AR['creationDate']));
            $AR['user']['userFullName'] = $arUser['LAST_NAME']." ".$arUser['NAME']." ".$arUser['SECOND_NAME'];
            $AR['user']['userEmail'] = $arUser['EMAIL'];
            $AR['user']['userId'] = $AR['user_id'];
            $AR['fundingType'] = ApplicationFundingType::getValue($AR['fundingType']);
            $AR['studyMode'] = StudyMode::getValue($AR['studyMode']);
            unset($AR['user_org']);
        }


        $filter = new ApplicationListFilter();
        $paging = new PagingHelper();
        $paging->setLimit($limit);
        $this->dispatchAction();

        //Поулчаем выборку ID пользователей которым требуется обзщежитие в данной организации
        $hostel  = \Spo\Site\Entities\HostelTable::getList(array(
            'select' => array(
               'userId' => 'ID_USER',
            ),
            'filter' => array(
                'ID_ORGANIZATION' => $orgId
            )
        ))->fetchAll();
        $arrayUserNeedHostel = array();
        foreach ($hostel as $key => $item) {
            array_push($arrayUserNeedHostel, $item['userId']);
        }


        foreach ($ArrayResult['list'] as $item){
            if (in_array($item['user_id'], $arrayUserNeedHostel)){
                $ArrayResult['list'][$item['id']]['needHostel'] = 1;
            } else {
                $ArrayResult['list'][$item['id']]['needHostel'] = 0;
            }

        }

        $this->arResult['applications']           = $ArrayResult;
        $this->arResult['applicationStatus']      = ApplicationStatus::getValuesArray();
        $this->arResult['applicationFundingType'] = ApplicationFundingType::getValuesArray();
        $this->arResult['paging']                 = $paging;

    }
    protected function GetListSpiciality($Organiz){
        $ArrayResult=ApplicationTable::getList(array(
            'select'  =>    array(
                'specialtyTitle'    =>  'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'id'                =>  'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID',
            ),
            'group'   =>    array( 'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID'),
            'filter'  =>    array( 'ORGANIZATION.ORGANIZATION_EMPLOYEE.USER_ID' => $Organiz),
        ))->fetchAll();
        return $ArrayResult;
    }
    protected function GetListStudyMode($Organiz){
        $ArrayResult=ApplicationTable::getList(array(
            'select'  =>  array(
                'ID'         =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
            ),
            'group'   => array('ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE'),
            'filter'  =>  array(
                'ORGANIZATION.ORGANIZATION_EMPLOYEE.USER_ID' => $Organiz,
            ),
        ))->fetchAll();
        foreach($ArrayResult as $key=>$item){
            $ArrayResult[$key]['studyMode']=StudyMode::getValue($item['ID']);
        }
        return $ArrayResult;
    }
    protected function GetListFundingType($Organiz){
        $ArrayResult=ApplicationTable::getList(array(
            'select'  =>  array(
                'ID'         =>  'APPLICATION_FUNDING_TYPE',
            ),
            'group'   => array('APPLICATION_FUNDING_TYPE'),
            'filter'  =>  array(
                'ORGANIZATION.ORGANIZATION_EMPLOYEE.USER_ID' => $Organiz,
            ),
        ))->fetchAll();
        foreach($ArrayResult as $key=>$item){
            $ArrayResult[$key]['FundingType']=ApplicationFundingType::getValue($item['ID']);
        }
        return $ArrayResult;
    }
    protected function GetListNeedHostel($Organiz){
        $ArrayResult=ApplicationTable::getList(array(
            'select'  =>  array(
                'ID'         =>  'SPO_APPLICATION_NEED_HOSTEL',
            ),
            'group'   => array('SPO_APPLICATION_NEED_HOSTEL'),
            'filter'  =>  array(
                'ORGANIZATION.ORGANIZATION_EMPLOYEE.USER_ID' => $Organiz,
            ),
        ))->fetchAll();
        foreach($ArrayResult as $key=>$item){
            if($item['ID']==1){
                $ArrayResult[$key]['needHostel']='ДА';
            }
            else{
                $ArrayResult[$key]['needHostel']='НЕТ';
            }
        }
        return $ArrayResult;
    }
    protected function GetListBaseEducation($Organiz){
        $ArrayResult=ApplicationTable::getList(array(
            'select'  =>  array(
                'ID'         =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
            ),
            'group'   => array('ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION'),
            'filter'  =>  array(
                'ORGANIZATION.ORGANIZATION_EMPLOYEE.USER_ID' => $Organiz,
            ),
        ))->fetchAll();
        foreach($ArrayResult as $key=>$item){
            $ArrayResult[$key]['BaseEducation']=TrainingLevel::getValue($item['ID']);
        }
        return $ArrayResult;
    }
    protected function dispatchAction()
    {
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();

        $action = $request->get('action');

        switch($action){
            case 'changeStatus':
                $this->changeStatusAction($request);
                break;
        }

        return;
    }

    protected  function changeStatusAction(Main\HttpRequest $request)
    {
        $applicationId = intval($request->get('applicationId'));
        $status        = intval($request->get('status'));

        $applicationDomain = ApplicationDomain::loadById($applicationId, $this->organization->getOrganizationId());

        $applicationDomain->changeStatus($status, true);
        if(!$applicationDomain->save())
        {
            return false;
        }
        else
        {
            $notifier = new Notifier();
            $notifier->applicationStatusChangeByOrganization($applicationDomain->getApplicationId());
            LocalRedirect(Url::toApplicationList());
        }
    }
}
?>