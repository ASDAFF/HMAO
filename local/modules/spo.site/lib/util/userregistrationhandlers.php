<?php

use Spo\Site\Domains\UserDomain;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Dictionaries\OrganizationPageType;
use Spo\Site\Domains\OrganizationPageDomain;
use Spo\Site\Entities\OrganizationTable;
use Spo\Site\Entities\OrganizationEmployeeTable;
use Spo\Site\Entities\OrganizationPageTable;

class UserRegistrationHandlers
{

	private static function checkModule()
	{
		if (!CModule::IncludeModule('spo.site')) {
			die('spo.site module not found');
		}
	}

	// Соответствие символьных кодов, пришедших с формы, идентификаторам групп пользователей
	public static $userGroups = array(
		'abiturient' => UserDomain::ABITURIENTS_GROUP_ID,
		'organization' => UserDomain::UNCONFIRMED_ORGANIZER_GROUP_ID,
	);

	/**
	 * По параметрам запроса определяет, регистрация какого типа пользователя происходит
	 * и устанавливает соответствующую группу пользователей в arFields. Если необходимое значение с формы регистрации
	 * не пришло - прерываем выполнение скрипта и выдаём ошибку. Пользователь зарегистрирован не будет.
	 *
	 * @param $arFields
	 * @return bool
	 */
	function beforeUserRegister(&$arFields)
	{
		self::checkModule();

		global $APPLICATION;

		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		$requestUserRole = $request->get('userRole');

		if  (empty($requestUserRole)) {
			$APPLICATION->ThrowException('Некорректные данные указаны при регистрации: не выбран тип аккаунта');
			return false;
		}

		if (!isset(self::$userGroups[$requestUserRole])) {
			$APPLICATION->ThrowException('Некорректные данные указаны при регистрации: неправильный тип аккаунта');
			return false;
		}

		$arFields['GROUP_ID'] = array(self::$userGroups[$requestUserRole]);
        //$arFields['ACTIVE'] = 'Y';

		return true;
	}

	function afterUserCreation(&$arFields)
	{
		self::checkModule();

		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		$organizationId = $request->get('organizationId');

		$userLogin = $arFields['LOGIN'];

		if (!empty($arFields['USER_ID']))
			// Пользователь регистрировался самостоятельно
			$userId = $arFields['USER_ID'];
		elseif (!empty($arFields['ID']))
			// Пользователь добавлен из административной части
			$userId = $arFields['ID'];
		else
			// Должен быть определён код пользователя, если регистрация прошла успешно. Иначе завершаем обработку.
			return false;

		$userGroups = $arFields['GROUP_ID'];

		if (is_array($userGroups[0])) {
			// Пользователь добавлен из административной части, преобразуем массив групп пользователя в нужный вид
			$userGroupsIds = array();
			foreach ($userGroups as $group) {
				$userGroupsIds[] = $group['GROUP_ID'];
			}
			$userGroups = $userGroupsIds;
		}

		if (in_array(UserDomain::ABITURIENTS_GROUP_ID, $userGroups)) {
			return self::afterAbiturientCreation($userId, $userLogin);
		} elseif (in_array(UserDomain::UNCONFIRMED_ORGANIZER_GROUP_ID, $userGroups) || in_array(UserDomain::ORGANIZER_GROUP_ID, $userGroups)) {
			return self::afterOrganizationCreation($userId/*, $organizationId*/,$arFields);
		}

		return true;
	}

	private function afterAbiturientCreation($userId)
	{
		self::checkModule();
        if(!empty($userId)){
			$userDomain = UserDomain::createAbiturientProfile($userId);
		}
		if (!$userDomain->save()) {
			AddMessage2Log('Ошибка сохранения профиля абитуриента при регистрации пользователя. UserId = ' . $userId);
			return false;
		}

		return true;
	}

	private function afterOrganizationCreation($userId/*, $organizationId*/,$arFields)
	{
		//print_r($userId);
		global $USER;
		//print_r($organizationId);
		if (!empty($userId) and $arFields['maderator']=='on'){
			//$isNewOrganization = true;
			$eventParams = array(
				'ORGANIZATION_STATUS' => 1,
			);
			$resultAdd = OrganizationTable::add($eventParams);
			$IDOrg=$resultAdd->getId();
			$eventParams = array(
				'USER_ID' => $userId,
				'ORGANIZATION_ID' => $IDOrg,
			);
			$resultAdd = OrganizationEmployeeTable::add($eventParams);
			for($i=2; 14>$i; $i=$i+1){
				$eventParams = array(
					'ORGANIZATION_ID' => $IDOrg,
					'ORGANIZATION_PAGE_TYPE' => $i,
				);
				$resultAdd = OrganizationPageTable::add($eventParams);
			}
			$USER->Authorize($userId);
			//header("Location: http://127.0.0.1:8080/auth/edu-organization-registration.php");
		}
		else{
			$eventParams = array(
				'USER_MODERATOR' => $userId,
			);
			OrganizationEmployeeTable::update($arFields['Organization'],$eventParams);
			$USER->Authorize($userId);
			//header("Location: http://127.0.0.1:8080/auth/edu-organization-registration.php");
		}
		/*self::checkModule();

		if ($organizationId === null) {
			$isNewOrganization = true;
			$eventParams = array(
				'ORGANIZATION_STATUS' => 1,
			);
			$resultAdd = OrganizationTable::add($eventParams);
			$IDOrg=$resultAdd->getId();
			$eventParams = array(
				'USER_ID' => $userId,
				'ORGANIZATION_ID' => $ID,
			);
			$resultAdd = OrganizationEmployeeTable::add($eventParams);
			$resultAdd->getId();
			for($i=2; 14>$i; $i=$i+1){
				$eventParams = array(
					'ORGANIZATION_ID' => $IDOrg,
					'ORGANIZATION_PAGE_TYPE' => $i,
				);
				$resultAdd = OrganizationPageTable::add($eventParams);
				$resultAdd->getId();
			}
		}
			/*$organizationDomain = OrganizationDomain::createOrganization();

			if (!$organizationDomain->save(false)) {
				AddMessage2Log('Ошибка сохранения организаиции при регистрации пользователя. UserId = ' . $userId);
				return false;
			}

			$organizationId = $organizationDomain->getOrganizationId();
		} else {
			$isNewOrganization = false;
			$organizationDomain = OrganizationDomain::loadById($organizationId);
		}

		$organizationDomain->createOrganizationEmployee($userId);

		if (!$organizationDomain->save()) {
			AddMessage2Log('Ошибка при привязке пользователя к организации. UserId = ' . $userId);
			return false;
		}

		if ($isNewOrganization) {
			$organizationPageDomain = OrganizationPageDomain::initOrganizationSystemPages($organizationId);

			if (!$organizationPageDomain->save()) {
				AddMessage2Log('Ошибка при создании страниц организации. OrganizationId = ' . $organizationId);
				return false;
			}
		}*/

		return true;
	}

    public function afterUserUpdate(&$arFields)
    {
        self::checkModule();

        // Если обновление профиля не прошло успешно, ничего не делаем
        if (!$arFields['RESULT'])
            return true;

        $userId = $arFields['ID'];

        // Если профиль был обновлён, проверяем, не изменились ли email и телефон пользователя
        /** @var BitrixUser $bitrixUserEntity */
        $userDomain = UserDomain::loadByUserId($userId);
        $userDomain->refreshUserValidDataStatus();
        $userDomain->save();

        return true;
    }

    public function beforeUserUpdate(&$arFields)
    {
        if(is_set($arFields, 'PERSONAL_PHONE') && strlen($arFields['PERSONAL_PHONE'])<=0)
        {
            global $APPLICATION;
            $APPLICATION->throwException('Пожалуйста, укажите номер телефона.');
            return false;
        }

        if(is_set($arFields, 'NAME') && strlen($arFields['NAME'])<=0)
        {
            global $APPLICATION;
            $APPLICATION->throwException('Пожалуйста, укажите имя.');
            return false;
        }

        if(is_set($arFields, 'LAST_NAME') && strlen($arFields['LAST_NAME'])<=0)
        {
            global $APPLICATION;
            $APPLICATION->throwException('Пожалуйста, укажите фамилию.');
            return false;
        }

        return true;
    }

}