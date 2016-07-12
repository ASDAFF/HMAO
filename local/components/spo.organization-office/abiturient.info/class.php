<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;
use Spo\Site\Domains\UserDomain;
use Spo\Site\Adapters\UserDomainAdapter;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\AdditionalLanguage;
use Spo\Site\Entities\AbiturientProfileTable;
use Spo\Site\Entities\OrganizationEmployeeTable;
use Spo\Site\Util\UiMessage;

class ProfileUpdateComponent extends OrganizationOfficeComponent
{
    protected $componentPage = 'template';


	/* Выдает id организации которой пренадлежит пользователь
	 * @param $UserId
	 * @return string*/
	private function getOrganizationId($UserId){
		$params = array(
			'select' => array (
				'organizationId' => 'ORGANIZATION_ID',
			),
			'filter' => array (
				'=USER_ID' => $UserId,
			)
		);
		$organizationId = OrganizationEmployeeTable::getList($params)->fetch();
		$organizationId = $organizationId['organizationId'];

		return $organizationId;
	}


	protected function getResult()
	{
		global $USER;
		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		$userId  = intval($request->get('userId'));
		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		$profileFormData = $request->get('AbiturientProfile');
		$profileFormDataParent = $request->get('AbiturientProfileParent');
		$backurl = $request->get('backurl');
		$verification = $request->get('verification');

		if($profileFormData) {
			$ArrayResult = AbiturientProfileTable::getList(array(
				'filter' => array(
					'LOGIC' => 'AND',
					'!=USER_ID' => $userId,
					'=SPO_ABITURIENT_PROFILE_SNILS' => $profileFormData['abiturientProfileSNILS'],
					'!=SPO_ABITURIENT_PROFILE_SNILS' => 0,
				),
				'select' => array(
					'SPO_ABITURIENT_PROFILE_SNILS',
				)
			))->fetchAll();
			$ValidSNILS = count($ArrayResult);
		}
		// Если форма отправлена - пытаемся обновить профиль
		if ($profileFormData and $ValidSNILS == 0 and empty($profileFormData['NameOrgVer'])) {

			if(!empty($verification))
			{
				$profileFormData['VALIDITY'] = 1;
				$profileFormData['USER_VALID_ID'] = $this->getOrganizationId($USER->GetID());
			}

            //var_dump(UserDomain::isAbiturientProfileCorrect($userId));
			if (UserDomain::isAbiturientProfileCorrect($userId) == 0) {
				UserDomain::isAbiturientProfileAdd($profileFormData,$userId);

			} else {
				UserDomain::updateAbiturientProfile($profileFormData, $_FILES['AbiturientProfile'],$userId);
				//var_dump($profileFormData);
				if($profileFormDataParent)/*вставка родных на сайт*/
					UserDomain::addAbiturientProfileParent($profileFormDataParent,$userId,$profileFormData['abiturientParents']);
			}

			LocalRedirect($backurl); // переадрисация на саму себя для избижание 2-х данных

		}
		else{
			$Error = '';
			if($ValidSNILS != 0){
				$Error .= 'Данный номер СНИЛС уже зарегистрирован в системе другим абитуриентом'.'<br>';
				$this->arResult['EroorSnils'] = 1;
			}
			if (UserDomain::isAbiturientProfileCorrect($userId) == 0) {
				$Error .= 'Для дальнейшей работы с системой необходимо корректно заполнить данные абитуриента';
			}

			if (!empty($Error)) {
				UiMessage::addMessage($Error, UiMessage::TYPE_WARNING);
			} elseif ($backurl) {
				LocalRedirect($backurl);
			}
		}

		$cUser = new CUser();
		$this->arResult['user'] = $cUser->GetList(($by="id"), ($order="desc"), array('ID' => $userId), array('FIELDS' => array('PERSONAL_PHOTO')))->Fetch();
		$this->arResult['profile'] = UserDomainAdapter::getAbiturientProfile($userId);
		$this->arResult['availableEducationList'] = BaseEducation::getValuesArray();
		$this->arResult['availableAdditionalLanguageList'] = AdditionalLanguage::getValuesArray();
	}
}
?>