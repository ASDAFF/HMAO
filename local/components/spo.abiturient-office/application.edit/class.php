<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;
//use Spo\Site\Domains\ApplicationDomain;
use Spo\Site\Dictionaries\ApplicationEventReason;
use Spo\Site\Util\UiMessage;
//use Spo\Site\Adapters\ApplicationDomainAdapter;
use Spo\Site\Util\Notification\Notifier;
use Spo\Site\Entities\ApplicationTable;
use Spo\Site\Entities\ApplicationEventTable;
use Spo\Site\Entities\HostelTable;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Dictionaries;

class ApplicationEditComponent extends AbiturientOffice
{
    protected $componentPage = '';

	protected function checkParams()
	{
		if (empty($this->arParams['applicationId']))
			throw new Main\ArgumentNullException('applicationId');
	}

	protected function getResult()
	{
        global $USER;
        $userId = $USER->GetID();
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();
        $applicationData = $request->get('application');

        $this->arResult['application'] = $this->getUserApplication($userId, $this->arParams['applicationId']);

        if ($applicationData) {
            $OldHostel = $this->arResult['application']['needHostel'];
            $IdOrg =  $this->arResult['application']['Orgid'];
            $host=false;
            if($applicationData['needHostel'] != '')
                $host=true;

            if ($OldHostel>0 && !$host)
            {
                $applicationData['applicationEventComment']=$applicationData['applicationEventComment'].'
Общежитие не требуется';
                $res=HostelTable::delete($OldHostel);
                if (!$res->isSuccess()) {
                    $this->arResult['errors'] = $res->getErrorMessages();
                    //var_dump($res->getErrorMessages());
                } else {
                    $this->arResult['success'] = 'Место в общежитии зарезервировано';
                   // var_dump('Место в общежитии зарезервировано');
                }
            }
            if ($host && $OldHostel==0)
            {
                $applicationData['applicationEventComment']=$applicationData['applicationEventComment'].'
Добавлена заявление на общежития';
                $res=HostelTable::add(
                    array(
                        'ID_USER'			=>	$userId,
                        'ID_ORGANIZATION'	=>	$IdOrg,
                    ));
                if (!$res->isSuccess()) {
                    $this->arResult['errors'] = $res->getErrorMessages();
                    //var_dump($res->getErrorMessages());
                } else {
                    $this->arResult['success'] = 'Место в общежитии зарезервировано';
                    //var_dump('Место в общежитии зарезервировано');
                }

            }
            $applicationDataSave = array(
                'APPLICATION_FUNDING_TYPE' => $applicationData['fundingType'],
                'SPO_APPLICATION_NEED_HOSTEL' => $applicationData['needHostel'] != '' ? true : false,
            );
            $resultUpdate = ApplicationTable::update($this->arParams['applicationId'], $applicationDataSave);

            if (!$resultUpdate->isSuccess()) {
                $errors = $resultUpdate->getErrorMessages();
                foreach ($errors as $error)
                    UiMessage::addMessage($error, UiMessage::TYPE_ERROR);
            } else {
                if (strlen($applicationData['applicationEventComment']) > 0) {
                    $eventParams = array(
                        'APPLICATION_ID' => $this->arParams['applicationId'],
                        'APPLICATION_EVENT_REASON' => ApplicationEventReason::NONE,
                        'APPLICATION_EVENT_DATE' => new Bitrix\Main\Type\DateTime(),
                        'APPLICATION_EVENT_STATUS' => $this->arResult['application']['status'],
                        'APPLICATION_EVENT_COMMENT' => $applicationData['applicationEventComment'],
                        'USER_ID' => $USER->getId(),
                    );
                    $resultAdd = ApplicationEventTable::add($eventParams);
                    if ($resultAdd->isSuccess()) {
                        LocalRedirect(POST_FORM_ACTION_URI);
                    } else {
                        $errors = $resultAdd->getErrorMessages();
                        foreach ($errors as $error)
                            UiMessage::addMessage($error, UiMessage::TYPE_ERROR);
                    }
                }
                //$notifier = new Notifier();
                //$notifier->applicationStatusChanged($this->arParams['applicationId']);
            }
        }
    }

    protected function getUserApplication($userId, $applicationId) {
        $params = array(
            'filter' => array(
                '=APPLICATION_ID' => $applicationId,
                '=USER_ID' => $userId,
                '!=APPLICATION_STATUS' => ApplicationStatus::DELETED
            ),
            'select' => array(
                'id' => 'APPLICATION_ID',
                'APPLICATION_CREATION_DATE',
                'specialtyTitle' => 'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'specialtyCode' => 'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'studyMode' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
                'baseEducation' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'status' => 'APPLICATION_STATUS',
                'fundingType' => 'APPLICATION_FUNDING_TYPE',
                'needHostel' => 'SPO_APPLICATION_NEED_HOSTEL',
                'GRANT' => 'ADMISSION_PLAN.ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
                'TUITION' => 'ADMISSION_PLAN.ADMISSION_PLAN_TUITION_STUDENTS_NUMBER',
                'Orgid'=>'ORGANIZATION_ID',
            )
        );
        $resultDb = ApplicationTable::getList($params);
        if($result = $resultDb->fetch()) {
            $result['creationDate'] = $result['APPLICATION_CREATION_DATE']->format('d.m.Y');
            $result['studyMode'] = Dictionaries\StudyMode::getValue($result['studyMode']);
            $result['baseEducation'] = Dictionaries\BaseEducation::getValue($result['baseEducation']);
            $IdOrg=$result['Orgid'];
        }
        $resHostel=HostelTable::UserId($userId,$IdOrg);
        if(empty($resHostel))
        {
            $result['needHostel']=0;
        }
        else
        {
            $result['needHostel']=$resHostel['ID_HOSTEL'];
        }

        $result['applicationEvents'] = $this->getApplicationEvents($applicationId);
        return $result;
    }

    protected function getApplicationEvents($applicationId) {
        $resultList = array();
        $params = array(
            'filter' => array(
                '=APPLICATION_ID' => $applicationId,
            ),
            'select' => array(
                'APPLICATION_EVENT_DATE',
                'status' => 'APPLICATION_EVENT_STATUS',
                'reason' => 'APPLICATION_EVENT_REASON',
                'comment' => 'APPLICATION_EVENT_COMMENT',
            )
        );
        $resultDb = ApplicationEventTable::getList($params);
        while ($result = $resultDb->fetch()) {
            $result['date'] = $result['APPLICATION_EVENT_DATE']->format('d.m.Y');
            $resultList[] = $result;
        }

        return $resultList;
    }
}
?>