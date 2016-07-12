<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;
use Spo\Site\Domains\UserDomain;
use Spo\Site\Adapters\UserDomainAdapter;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\AdditionalLanguage;
use Spo\Site\Util\UiMessage;
use Spo\Site\Entities\AbiturientProfileTable;


class ProfileUpdateComponent extends AbiturientOffice
{
    protected $componentPage = '';
    protected $pageTitle = 'Профиль абитуриента';

	protected function getResult()
	{
		global $USER;
        $userId = $USER->GetID();
		//$userDomain = UserDomain::loadByUserId($userId);
        $userDomainObj = new UserDomain();
		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		$profileFormData = $request->get('AbiturientProfile');
        $profileFormDataParent = $request->get('AbiturientProfileParent');
        $backurl = $request->get('backurl');

        /*проверка СНИЛС*/
        $snilsResult = AbiturientProfileTable::getList(array(
            'filter' => array(
                '=USER_ID' => $USER->GetID(),
            ),
            'select' => array(
                'SPO_ABITURIENT_PROFILE_SNILS',
            )
        ))->fetch();

        $ValidSNILS = 0;
        if($profileFormData) {
            $snilsOtherUser = AbiturientProfileTable::getList(array(
                'filter' => array(
                        'LOGIC' => 'AND',
                        '!=USER_ID' => $USER->GetID(),
                        '=SPO_ABITURIENT_PROFILE_SNILS' => $profileFormData['abiturientProfileSNILS'],
                        '!=SPO_ABITURIENT_PROFILE_SNILS' => 0,
                ),
                'select' => array(
                        'SPO_ABITURIENT_PROFILE_SNILS',
                )
            ))->fetchAll();
            $ValidSNILS = count($snilsOtherUser);
        }
		// Если форма отправлена - пытаемся обновить профиль

		if ($profileFormData and $ValidSNILS == 0 and empty($profileFormData['NameOrgVer'])) {

            if ($userDomainObj->isAbiturientProfileCorrect($userId) == 0) {
                $userDomainObj->isAbiturientProfileAdd($profileFormData, $userId);
            } else {
                $userDomainObj->updateAbiturientProfile($profileFormData, $_FILES['AbiturientProfile'], $userId);
            }

            // вставка родителей на сайт
            if($profileFormDataParent)
                $userDomainObj->addAbiturientProfileParent($profileFormDataParent, $userId, $profileFormData['abiturientParents']);

			//$userDomain->updateAbiturientProfile($profileFormData, $_FILES['AbiturientProfile']);
            /*
            // Профиль сохраняем независимо от того, есть ли в домене ошибки
            if (!$userDomain->save()) {
                throw new Main\DB\Exception('Ошибка при сохранении данных');
            }

            $errors = $userDomain->getErrors();
            if (!empty($errors)) {
                $this->arResult['errors'] = $userDomain->getErrors();
            } else {
                $this->arResult['success'] = 'Данные успешно обновлены';
            }*/
		}
        else {
            $Error = '';
            if($ValidSNILS != 0){
                $Error .= 'Данный номер СНИЛС уже зарегистрирован в системе другим абитуриентом'.'<br>';
                $this->arResult['EroorSnils'] = 1;
            }
            if ($userDomainObj->isAbiturientProfileCorrect($userId) == 0) {
                $Error .= 'Для дальнейшей работы с системой необходимо корректно заполнить данные абитуриента';
            }

            if (!empty($Error)) {
                UiMessage::addMessage($Error, UiMessage::TYPE_WARNING);
            } elseif ($backurl) {
                LocalRedirect($backurl);
            }
        }

        $cUser = new CUser();
        $this->arResult['SNILS'] = $snilsResult;
        $this->arResult['user'] = $cUser->GetList(($by="id"), ($order="desc"), array('ID' => $userId), array('FIELDS' => array('PERSONAL_PHOTO')))->Fetch();
		$this->arResult['profile'] = UserDomainAdapter::getAbiturientProfile($userId);
		$this->arResult['availableEducationList'] = BaseEducation::getValuesArray();
		$this->arResult['availableAdditionalLanguageList'] = AdditionalLanguage::getValuesArray();
	}
}
?>