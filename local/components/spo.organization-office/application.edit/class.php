<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Domains\ApplicationDomain;
use Spo\Site\Util\UiMessage;
use Spo\Site\Adapters\ApplicationDomainAdapter;
use Spo\Site\Dictionaries\ApplicationEventReason;
use Spo\Site\Helpers\OrganizationOfficeUrlHelper as Url;
use Spo\Site\Exceptions\ArgumentException;
use Spo\Site\Util\Notification\Notifier;
use Spo\Site\entities\OrganizationSpecialtyExamTable;
use Spo\Site\entities\AbiturientProfileTable;

/*==== NEw====*/
use Spo\Site\entities\ApplicationTable;
use Spo\Site\entities\ApplicationEventTable;
use Spo\Site\entities\OrganizationEmployeeTable;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Core\SPODomain;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Helpers\DateFormatHelper;
class ApplicationEditComponent extends OrganizationOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array();

    function userValidation ($userId){
        $paramForFilter = array (
            'select' => array(
                'validStatus' => 'VALIDITY',
                'moderValidId' => 'USER_VALID_ID',
            ),
            'filter' => array(
                '=USER_ID' => $userId,
            )
        );
        $abiturientValid = AbiturientProfileTable::getList($paramForFilter)->fetch();
        if ($abiturientValid['validStatus'] == 1)
        {
            return true;
        } else
        {
            return false;
        }
    }


    protected function getResult()
    {
        $this->breadcrumbs = array('Список заявок' => Url::toApplicationList(), 'Заявка абитуриента' => '');
        global $USER;
        $organizationID = OrganizationDomain::loadByEmployeeUserId($USER->GetID());
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();

        $applicationData = $request->get('application');
        $applicationId = $request->get('applicationId');
        //$applicationDomain = ApplicationDomain::getOrganizationApplication($organizationID, $applicationId);
        $application_data=ApplicationTable::getList(array(
            'select' => array(
                'id'                        =>  'APPLICATION_ID',
                'creationDate'              =>  'APPLICATION_CREATION_DATE',//дата подачи
                'specialtyTitle'            =>  'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'specialtyCode'             =>  'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'studyMode'                 =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
                'baseEducation'             =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'status'                    =>  'APPLICATION_STATUS',// статус заявки
                'fundingType'               =>  'APPLICATION_FUNDING_TYPE',
                'needHostel'                =>  'SPO_APPLICATION_NEED_HOSTEL',
                'user_app_name'             =>  'USER.NAME',
                'user_app_second_name'      =>  'USER.SECOND_NAME',
                'user_app_last_name'        =>  'USER.LAST_NAME',
                'userId'                    =>  'USER_ID'
            ),
            'filter'                =>  array('id' => $applicationId),
        ))->fetchAll();

        $abiturientId = $application_data[0]['userId'];

        $userValid = ApplicationEditComponent::userValidation($abiturientId);



        /*перевод в нормальную форму*/
        foreach ($application_data as &$AR)
        {
            $AR['creationDate']=date('d-m-Y',strtotime($AR['creationDate']));
            $AR['abiturient']['fullname']=$AR['user_app_last_name']." ".$AR['user_app_name']." ".$AR['user_app_second_name'];
            $AR['abiturient']['userId']=$AR['user_id'];
            $AR['needHostel']=(boolean)$AR['needHostel'];
            unset($AR['userId']);
            unset($AR['user_app_name']);
            unset($AR['user_app_second_name']);
            unset($AR['user_app_last_name']);
            unset($AR['CREATIONDATE']);

            // дописываем коментарии к заявке
            $applicationEvents=ApplicationEventTable::getList(array(
                'select' => array(
                    'date'                      =>  'APPLICATION_EVENT_DATE',
                    'comment'                   =>  'APPLICATION_EVENT_COMMENT',
                    'status'                    =>  'APPLICATION_EVENT_STATUS',
                    'reason'                    =>  'APPLICATION_EVENT_REASON',
                ),
                'filter'                =>  array('APPLICATION_ID' => $applicationId),
            ))->fetchAll();
            $AR['applicationEvents']=$applicationEvents;
        }

        $this->arResult['application'] = $application_data[0];
        // Если пришла форма - обновляем заявку
        if ($applicationData) {
            
            /*var_dump($applicationData);
            /*die;*/
            if (!isset($applicationData['status']))
                throw ArgumentException::argumentIncorrect();


            /*отправить увидромления */
            $applicationEventReason = (isset($applicationData['applicationEventReason'])) ? $applicationData['applicationEventReason'] : ApplicationEventReason::NONE;
            $eventComment = (isset($applicationData['applicationEventComment'])) ? $applicationData['applicationEventComment'] : '';
            $oldStatus=$application_data[0]['status']; // старый статусж
            $status=$applicationData['status'];//новый статус
            //-------------------------------------
            $propuskNadzer=OrganizationEmployeeTable::getList(array(
                'select' => array(
                    'USER_MODERATOR',
                ),
                'filter'       =>  array(
                    'USER_MODERATOR'=>$USER->GetID()),
            ))->fetchAll();
            if(count($propuskNadzer)>0 and $propuskNadzer[0]['USER_MODERATOR']!="" and $propuskNadzer[0]['USER_MODERATOR']!=0){
                $propuskNadzer=0;
            }
            else {
                $propuskNadzer=1;
            }
            //--------------------------------------
            if (!ApplicationStatus::canChangeStatus($oldStatus, $status) and $propuskNadzer!=1)
            {
                UiMessage::addMessage('Нельзя поменять статус заявки с "' . ApplicationStatus::getValue($oldStatus) . '" на "' . ApplicationStatus::getValue($status) . '"', UiMessage::TYPE_ERROR);
                return;
            }
            $res = ApplicationTable::update($applicationId, array(
                'APPLICATION_STATUS' =>$applicationData['status']
            ));
            $date = new Bitrix\Main\Type\DateTime(date('d.m.Y H:i:s'));
            $com_res=ApplicationEventTable::add(array(
                'APPLICATION_ID'                =>  $applicationId,
                'APPLICATION_EVENT_COMMENT'     =>  $eventComment,
                'APPLICATION_EVENT_REASON'      =>  $applicationEventReason,
                'APPLICATION_EVENT_STATUS'      =>  $applicationData['status'],
                'APPLICATION_EVENT_DATE'        =>  $date,
                'USER_ID'                       =>  $USER->GetID()
            ));
            if (!$com_res->isSuccess())
            {
                $errors = $com_res->getErrorMessages();
            }
            //$applicationDomain->changeStatus($applicationData['status'], $eventComment, $applicationEventReason);
            /// дописываем коментарии к заявке
            $applicationEvents=ApplicationEventTable::getList(array(
                'select' => array(
                    'date'                      =>  'APPLICATION_EVENT_DATE',
                    'comment'                   =>  'APPLICATION_EVENT_COMMENT',
                    'status'                    =>  'APPLICATION_EVENT_STATUS',
                    'reason'                    =>  'APPLICATION_EVENT_REASON',
                ),
                'filter'                =>  array('APPLICATION_ID' => $applicationId),
            ))->fetchAll();
            $application_data[0]['applicationEvents']=$applicationEvents;
            $application_data[0]['status']=$status;


                /*отправка почтового события*/
                $SPECIALTY=ApplicationTable::getList(array(
                    'filter'	=>	array(
                        'APPLICATION_ID'	        =>  $applicationId
                    ),
                    'select'	=>	array(
                        'fundingType'				=>	'APPLICATION_FUNDING_TYPE',
                        'IdOrgan'                   =>  'ORGANIZATION_SPECIALTY_ID',
                        'Code'						=>	'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                        'NameSpec'					=>	'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                        'CodeCiliza'				=>	'ORGANIZATION_SPECIALTY.IDSPECIALIZATION',
                        'NameCiliza'				=>	'ORGANIZATION_SPECIALTY.NAMESPECIALIZATION',
                        'BaseEducation'				=>	'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                        'Period'					=>	'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                        'ORGANIZATION_NAME'			=>	'ORGANIZATION.ORGANIZATION_NAME',
                        'ORGANIZATION_INFO_LINK'	=>	'ORGANIZATION.ORGANIZATION_SITE',
                        'ABITURIENT_FIO'			=>	'ABITURIENT.FIO',
                        'ABITURIENT_EMAIL'			=>	'ABITURIENT.Spo\Site\Entities\UserValidDataTable:ABITURIENT_PROFILE.USER_VALID_DATA_EMAIL',

                    )
                ))->fetch();
                /*проверка на то что у данной программы есть экзамен*/
                $exam=OrganizationSpecialtyExamTable::exem($SPECIALTY['IdOrgan']);
                if (empty($exam))
                {
                    if($SPECIALTY['fundingType']==1)
                    {
                        $final='Контрактная форма обучения';
                    }
                    else
                    {
                        $final='Бюджетная форма обучения';
                    }
                    $Ciliza='';
                    if(!empty($SPECIALTY['CodeCiliza'])) $Ciliza.=$SPECIALTY['CodeCiliza'].' '.$SPECIALTY['NameCiliza'].', ';
                    $APPLICATION_SPECIALTY=$SPECIALTY['Code'].' '.$SPECIALTY['NameSpec'].', '.$Ciliza.BaseEducation::getshortValues($SPECIALTY['BaseEducation']).', '.mb_strtolower($final).', '.DateFormatHelper::months2YearsMonths($SPECIALTY['Period']);

                    $arEventFields = array(
                        'APPLICATION_SPECIALTY'		=>		$APPLICATION_SPECIALTY,
                        'ABITURIENT_EMAIL'			=>		$SPECIALTY['ABITURIENT_EMAIL'],
                        'ABITURIENT_FIO'			=>		$SPECIALTY['ABITURIENT_FIO'],
                        'ORGANIZATION_NAME'			=>		$SPECIALTY['ORGANIZATION_NAME'],
                        'ORGANIZATION_INFO_LINK'	=>		$SPECIALTY['ORGANIZATION_INFO_LINK'],
                        'APPLICATION_STATUS_STR'    =>      ApplicationStatus::getValue($status),
                        'COMMENT'                   =>      $applicationData['applicationEventComment']
                    );
                    CEvent::SendImmediate("APPLICATION_STATUS_CHANGE_BY_ORGANIZATION", 's1', $arEventFields);
                }
        }

        //$applicationDomain->loadApplicationEvents();


        /*==============Вывод данных о абитуриент======
        $result = array(
            'id' => $application->getId(),
            'creationDate' => $application->getCreationDate(),
            'specialtyTitle' => $application->getOrganizationSpecialty()->getSpecialty()->getTitle(),
            'specialtyCode' => $application->getOrganizationSpecialty()->getSpecialty()->getCode(),
            'studyMode' => $application->getOrganizationSpecialty()->getStudyMode(),
            'baseEducation' => $application->getOrganizationSpecialty()->getBaseEducation(),
            'status' => $application->getStatus(),
            'fundingType' => $application->getFundingType(),
            'needHostel' => $application->getNeedHostel(),
            'applicationEvents' => array(),
            'abiturient' => array(
                'fullname' => $abiturient->getFullName(),
                'userId' => $abiturient->getId(),
            ),
        );
        */


        // var_dump($application_data);
        $this->arResult['valid'] = $userValid;
        $this->arResult['application'] = $application_data[0];
        //var_dump($this->arResult['application']['applicationEvents']);
        $this->arResult['organizationId'] = $organizationID;

    }
}
?>