<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;
//use Spo\Site\Domains\ApplicationDomain;
use Spo\Site\Exceptions\ArgumentException;
use Spo\Site\Util\Notification\Notifier;
use Spo\Site\Entities\ApplicationTable;
use Spo\Site\Entities\ApplicationEventTable;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Dictionaries\ApplicationEventReason;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Helpers\DateFormatHelper;


class ApplicationDeleteComponent extends AbiturientOffice
{

    protected $componentPage = '';

	protected function checkParams()
	{
		if (empty($this->arParams['applicationId']))
			throw ArgumentException::argumentMissing('applicationId');
	}

	protected function getResult()
	{
		global $USER;
		$userId = $USER->GetID();

		$date = new \Bitrix\Main\Type\DateTime(date("d.m.Y H:i:s"));
		$result = ApplicationTable::update($this->arParams['applicationId'],
			array(
		       'APPLICATION_STATUS' => ApplicationStatus::DELETED,
	         ));
		if (!$result->isSuccess())
			$this->arResult['message'] = 'Не удалось удалить заявку № ' . $this->arParams['applicationId'];
		else {
			$this->arResult['message'] = 'Заявка № ' . $this->arParams['applicationId'] . ' была успешно удалена';
			$applicationDataSave = array(
				'APPLICATION_ID' => $this->arParams['applicationId'],
				'USER_ID' => $userId,
				'APPLICATION_EVENT_DATE' => $date,
				'APPLICATION_EVENT_STATUS' => ApplicationStatus::DELETED,
				'APPLICATION_EVENT_REASON' => ApplicationEventReason::CANCELED_BY_ABITURIENT,
				'APPLICATION_EVENT_COMMENT' => '',
			);
			ApplicationEventTable::add($applicationDataSave);


			/*отправка почтового события*/
			$SPECIALTY=ApplicationTable::getList(array(
				'filter'	=>	array(
					'APPLICATION_ID'	=> $this->arParams['applicationId']
				),
				'select'	=>	array(
					'fundingType'				=>	'APPLICATION_FUNDING_TYPE',
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
			);
			CEvent::SendImmediate("APPLICATION_DELETED", 's1', $arEventFields);


			//$notifier = new Notifier();
			//$notifier->applicationStatusChanged($applicationDomain->lastApplicationEvent);
		}
		/*$applicationDomain = ApplicationDomain::deleteUserApplication($userId, $this->arParams['applicationId']);
        if (!$applicationDomain->save())
            $this->arResult['message'] = 'Не удалось удалить заявку № ' . $this->arParams['applicationId'];
        else {
		    $this->arResult['message'] = 'Заявка № ' . $this->arParams['applicationId'] . ' была успешно удалена';
            $notifier = new Notifier();
            $notifier->applicationStatusChanged($applicationDomain->lastApplicationEvent);
        }*/
	}

}
?>