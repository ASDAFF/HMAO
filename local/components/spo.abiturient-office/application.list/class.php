<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;
use Spo\Site\Entities\ApplicationTable;
use Spo\Site\Entities\ApplicationEventTable;
use Spo\Site\Entities\HostelTable;
use Spo\Site\Dictionaries\ApplicationFundingType;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\BaseEducation;


class ApplicationListComponent extends AbiturientOffice
{
	protected $componentPage = '';
	protected $pageTitle = 'Список заявок на поступление';
	protected function getResult()
	{
		global $USER;
		$this->Updait($_GET,$USER->GetID());
		$this->arResult['userApplications'] = $this->getUserApplicationsList($USER->GetID());
	}
	protected function Updait($ArrId,$idUser){
		$i=1;
		foreach ($ArrId as $key=>$item){
			$GetID=substr($key, 0, 5);
			if($GetID=='Prior') {
				ApplicationTable::update($item, array(
					'APPLICATION_PRIORITY' => $i,
				));
				$resultStatus['APPLICATION_EVENT_STATUS']=8;
				$resultReason['APPLICATION_EVENT_REASON']=8;
				/*while ($result = $resultDb->fetch()) {
					$resultStatus['APPLICATION_EVENT_STATUS'] = $result['APPLICATION_EVENT_STATUS'];
					$resultReason['APPLICATION_EVENT_REASON'] = $result['APPLICATION_EVENT_REASON'];
				}*/
				//echo $resultStatus['APPLICATION_EVENT_STATUS']." ".$resultReason['APPLICATION_EVENT_REASON']."<br>";
				$Massiv=array(
					'APPLICATION_ID' => $item,
					'USER_ID' => $idUser,
					'APPLICATION_EVENT_DATE' => new \Bitrix\Main\Type\DateTime(date('d.m.Y H:i:s')),
					'APPLICATION_EVENT_STATUS' => $resultStatus['APPLICATION_EVENT_STATUS'],
					'APPLICATION_EVENT_REASON' => $resultReason['APPLICATION_EVENT_REASON'],
					'APPLICATION_EVENT_COMMENT' => 'Приоритет изменен',
				);
				ApplicationEventTable::add(
					$Massiv
				);
				$i=$i+1;
			}
		}
	}
	protected function getUserApplicationsList($userId) {
		$resultList = array();
		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		$resprior=$request->get('AppPrior');
		/*получение общежитий*/
		$hostels=HostelTable::getlist(array(
			'filter'	=>	array(
				'=ID_USER'	=>	$userId
			),
			'select'	=>	array('*')
		));
		while ($hostel_el=$hostels->fetch())
		{
			$hostel[$hostel_el['ID_ORGANIZATION']]=$hostel_el;
		}

		if ($resprior)
		{
			global $USER;
			foreach ($resprior as $key=>$app)
			{
				if($app['bul']==1)
				{
					$res=ApplicationTable::update($key,array(
						'APPLICATION_PRIORITY'	=> $app['res'],
					));
					if(!$res->isSuccess())
					{
						//var_dump($res->getErrorMessages());
					}
					else
					{
						$ress=ApplicationEventTable::add(array(
							'APPLICATION_ID'			=>	$key,
							'USER_ID'					=>	$USER->GetID(),
							'APPLICATION_EVENT_DATE'	=>	new \Bitrix\Main\Type\DateTime(),
							'APPLICATION_EVENT_STATUS'	=>  8,
							'APPLICATION_EVENT_REASON'	=>  1,
							'APPLICATION_EVENT_COMMENT'	=>  ""
						));
					}
				}

			}
		}
		//var_dump($resprior);
		$params = array(
			'order'   => array('APPLICATION_PRIORITY'=>'ASC'),
			'filter' => array(
				'=USER_ID' => $userId,
				'!=APPLICATION_STATUS' => ApplicationStatus::DELETED /*скрывать удалённые анкеты*/
			),
			'select' => array(
				'id' => 'APPLICATION_ID',
				'APPLICATION_CREATION_DATE',
				'specialtyTitle' => 'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
				'specialtyCode' => 'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
				'studyPeriod' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
				'studyMode' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
				'trainingLevel' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_LEVEL',
				'baseEducation' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
				'status' => 'APPLICATION_STATUS',
				'statusCode' => 'APPLICATION_STATUS',
				'applicationFundingType' => 'APPLICATION_FUNDING_TYPE',
				'needHostel' => 'SPO_APPLICATION_NEED_HOSTEL',
				'organizationName' => 'ORGANIZATION.ORGANIZATION_NAME',
				'organizationId' => 'ORGANIZATION_ID',
				'priority' => 'APPLICATION_PRIORITY'
			)
		);

		$resultDb = ApplicationTable::getList($params);
		while ($result = $resultDb->fetch()) {
			$result['creationDate'] = $result['APPLICATION_CREATION_DATE']->format('d.m.Y');
			$result['status'] = ApplicationStatus::getValue($result['status']);
			$result['studyMode'] = StudyMode::getValue($result['studyMode']);
			$result['baseEducation'] = BaseEducation::getValue($result['baseEducation']);
			$result['applicationFundingType'] = ApplicationFundingType::getValue($result['applicationFundingType']);
			/*echo "<pre>";
			print_r($result);
			echo "</pre>";*/
			if(empty($hostel[$result['organizationId']]))
			{
				$result['needHostel']=0;
			}
			else
			{
				$result['needHostel']=1;
			}
			$resultList[$result['organizationId']]['items'][] = $result;
			$resultList[$result['organizationId']]['name'] = $result['organizationName'];
		}

		//var_dump($resultList[24]);
		return $resultList;
	}
}
?>
