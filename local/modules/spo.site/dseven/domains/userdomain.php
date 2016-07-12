<?php
namespace Spo\Site\Domains;

use Spo\Site\Core\SPODomain;
use Bitrix\Main;
use Spo\Site\Dictionaries\EducationDocumentType;
use Spo\Site\Doctrine\Entities\AbiturientProfile;
use D;
use CIBlockElement;
use CUser;
use CFile;
use Spo\Site\Doctrine\Entities\UserValidData;
use Spo\Site\Doctrine\Repositories\AbiturientProfileRepository;
use Spo\Site\Exceptions\ArgumentException;
use Spo\Site\Util\CVarDumper;
use CModule;
use Spo\Site\Doctrine\Entities\BitrixUser;
use Spo\Site\Helpers\UserDataConfirmationHelper;
use Symfony\Component\Validator\ConstraintViolation;

class UserDomain extends SPODomain
{
	// todo возможно, стоит ориентироваться на символьные коды групп пользователей и получать id групп динамически,
	// todo нужен отдельный класс. Зависит от дальнейшей судьбы проекта, метода его установки, и т.д.

	/** @var BitrixUser */
	protected $entity;

	const ABITURIENTS_GROUP_ID = 5;
	const UNCONFIRMED_ORGANIZER_GROUP_ID = 6;
	const ORGANIZER_GROUP_ID = 7;
	const EDUCATIONAL_DEPARTMENT_EMPLOYEE = 8;

	const PROFILE_IS_NOT_CORRECT = 1;
	const PROFILE_IS_CORRECT = 2;

    const CONFIRMATION_CODE_TYPE_EMAIL = 'email';
    const CONFIRMATION_CODE_TYPE_PHONE = 'phone';

	private static function checkUserBelongsToGroups($groupId, $userGroups)
	{
		foreach ($userGroups as $group) {
			if ($group['GROUP_ID'] == $groupId)
				return true;
		}

		return false;
	}

	/**
	 * Проверяет, есть ли среди идентификаторов в переданом массиве идентификатор, соответствующий группе "Абитуриенты"
	 *
	 * @param array $groupsIds
	 * @return bool
	 */
	public static function checkIsAbiturient(array $groupsIds)
	{
		return self::checkUserBelongsToGroups(self::ABITURIENTS_GROUP_ID, $groupsIds);
	}

	public static function createAbiturientProfile($userId)
	{
		$userDomain = UserDomain::loadByUserId($userId);

		if ($userDomain === null)
			throw new Main\SystemException('Not Found Exception');

		$abiturientProfile = new AbiturientProfile();
		$abiturientProfile->setUser($userDomain->getModel());

		$userDomain->persistEntity($abiturientProfile);

		return $userDomain;
	}

    /**
     *
     * Проверяет, есть ли среди идентификаторов в переданом массиве идентификатор, соответствующий группе "Организация"
     *
     * @param CUser $bxUser
     * @return bool
     */
    public static function checkIsOrganizationEmployee($bxUser)
    {
        return self::checkUserBelongsToGroups(self::ORGANIZER_GROUP_ID, $bxUser->GetUserGroupArray());
    }

	public static function loadByUserId($userId)
	{
		$user = D::$em->getRepository('Spo\Site\Doctrine\Entities\BitrixUser')->find($userId);

		if ($user === null)
			throw new Main\SystemException('Not Found Exception');

		return new UserDomain($user);
	}

	public static function getAbiturientProfile($userId)
	{
		$abiturientProfile = D::$em->getRepository('Spo\Site\Doctrine\Entities\AbiturientProfile')
			->findOneBy(array('userId' => $userId));

		if ($abiturientProfile === null)
			throw new Main\SystemException('Not Found');

		return new UserDomain($abiturientProfile);
	}

	public function updateAbiturientProfileFiles($uploadedFiles)
	{
		foreach ($uploadedFiles['name'] as $key => $value) {
			$fileData = array(
				'type' => $uploadedFiles['type'][$key],
				'tmp_name' => $uploadedFiles['tmp_name'][$key],
				'size' => $uploadedFiles['size'][$key],
				'del' => 'Y',
				'name' => uniqid($this->entity->getAbiturientProfile()->getId()) . $value,
				'MODULE_ID' => 'spo.site',
			);

			if ($fileData['size'] == 0)
				continue;

			switch ($key) {
				case 'identityDocumentScanFile':
					$this->updateAbiturientIdentityDocumentScanFile($fileData);
					break;
				case 'identityDocumentRegistrationScanFile':
					$this->updateAbiturientIdentityDocumentRegistrationScanFile($fileData);
					break;
				case 'INNScanFile':
					$this->updateAbiturientINNScanFile($fileData);
					break;
				case 'SNILSScanFile':
					$this->updateAbiturientSNILSScanFile($fileData);
					break;
				case 'educationDocumentScanFile':
					$this->updateAbiturientEducationDocumentScanFile($fileData);
					break;
				default:
					continue;
			}
		}
	}

	public function updateAbiturientIdentityDocumentScanFile($fileData)
	{
		$fileData['old_file'] = $this->entity->getAbiturientProfile()->getIdentityDocumentScanFile();
		$fileId = CFile::SaveFile($fileData, 'abiturient');

		if (empty($fileId))
			$this->addError('Ошибка при сохранении файла', 'AbiturientProfile', 'IdentityDocumentScanFile');

		$this->entity->getAbiturientProfile()->setIdentityDocumentScanFile($fileId);
	}

	public function updateAbiturientIdentityDocumentRegistrationScanFile($fileData)
	{
		$fileData['old_file'] = $this->entity->getAbiturientProfile()->getIdentityDocumentScanFile();
		$fileId = CFile::SaveFile($fileData, 'abiturient');

		if (empty($fileId))
			$this->addError('Ошибка при сохранении файла', 'AbiturientProfile', 'IdentityDocumentRegistrationScanFile');

		$this->entity->getAbiturientProfile()->setIdentityDocumentScanFile($fileId);
	}

	public function updateAbiturientEducationDocumentScanFile($fileData)
	{
		$fileData['old_file'] = $this->entity->getAbiturientProfile()->getEducationDocumentScanFile();
		$fileId = CFile::SaveFile($fileData, 'abiturient');

		if (empty($fileId))
			$this->addError('Ошибка при сохранении файла', 'AbiturientProfile', 'EducationDocumentScanFile');

		$this->entity->getAbiturientProfile()->setEducationDocumentScanFile($fileId);
	}

	public function updateAbiturientINNScanFile($fileData)
	{
		$fileData['old_file'] = $this->entity->getAbiturientProfile()->getINNScanFile();
		$fileId = CFile::SaveFile($fileData, 'abiturient');

		if (empty($fileId))
			$this->addError('Ошибка при сохранении файла', 'AbiturientProfile', 'INNScanFile');

		$this->entity->getAbiturientProfile()->setINNScanFile($fileId);
	}

	public function updateAbiturientSNILSScanFile($fileData)
	{
		$fileData['old_file'] = $this->entity->getAbiturientProfile()->getSNILSScanFile();
		$fileId = CFile::SaveFile($fileData, 'abiturient');

		if (empty($fileId))
			$this->addError('Ошибка при сохранении файла', 'AbiturientProfile', 'SNILSScanFile');

		$this->entity->getAbiturientProfile()->setSNILSScanFile($fileId);
	}

    public function isAbiturientProfileCorrect()
    {
        if ($this->entity->getAbiturientProfile() == null)
            throw new Main\SystemException('Not Found Exception');

        return $this->entity->getAbiturientProfile()->isCorrect();
    }

	public function updateAbiturientProfile($profileData, $profileFiles)
	{
		if ($this->entity->getAbiturientProfile() == null)
			throw new Main\SystemException('Not Found Exception');

		if (isset($profileData['abiturientProfileGender']))
			$this->entity->getAbiturientProfile()->setGender($profileData['abiturientProfileGender']);

        if (isset($profileData['abiturientProfileINN'])) {
            $this->entity->getAbiturientProfile()->setINN($profileData['abiturientProfileINN']);

//            $innIsNotUnique = AbiturientProfileRepository::create()
//                ->findByINN($profileData['abiturientProfileINN'])
//                ->exceptUserId($this->entity->getId())
//                ->count();
//
//            if ($innIsNotUnique)
//                $this->addError('Данный номер ИНН уже используется', 'AbiturientProfile', 'abiturientProfileINN');
        }

		if (isset($profileData['abiturientProfileSNILS'])) {

            $this->entity->getAbiturientProfile()->setSNILS($profileData['abiturientProfileSNILS']);

//            $SNILSIsNotUnique = AbiturientProfileRepository::create()
//                ->findBySNILS($profileData['abiturientProfileSNILS'])
//                ->exceptUserId($this->entity->getId())
//                ->count();
//
//            if ($SNILSIsNotUnique)
//                $this->addError('Данный номер СНИЛС уже используется', 'AbiturientProfile', 'abiturientProfileSNILS');
        }

        if (isset($profileData['abiturientProfileBirthplace']))
            $this->entity->getAbiturientProfile()->setBirthplace($profileData['abiturientProfileBirthplace']);

		if (isset($profileData['abiturientProfileBirthday'])) {
			try {
				$birthday = new \DateTime($profileData['abiturientProfileBirthday']);
				$this->entity->getAbiturientProfile()->setBirthday($birthday);
			} catch (\Exception $e) {
				$this->addError('Должна быть указана корректная дата', 'AbiturientProfile', 'abiturientProfileBirthday');
			}
		}

		if (isset($profileData['abiturientProfileIsReservist'])) {
			$this->entity->getAbiturientProfile()->setIsReservist(true);
			$this->entity->getAbiturientProfile()->setMilitaryDocumentSeries($profileData['abiturientProfileMilitaryDocumentSeries']);
			$this->entity->getAbiturientProfile()->setMilitaryDocumentNumber($profileData['abiturientProfileMilitaryDocumentNumber']);
			$this->entity->getAbiturientProfile()->setMilitaryDocumentRegion($profileData['abiturientProfileMilitaryDocumentRegion']);
		} else {
			$this->entity->getAbiturientProfile()->setIsReservist(false);
			$this->entity->getAbiturientProfile()->setMilitaryDocumentSeries(null);
			$this->entity->getAbiturientProfile()->setMilitaryDocumentNumber(null);
			$this->entity->getAbiturientProfile()->setMilitaryDocumentRegion(null);
		}

		if (isset($profileData['abiturientProfileEducation'])) {
			$this->entity->getAbiturientProfile()->setEducation($profileData['abiturientProfileEducation']);
		}

		if (isset($profileData['abiturientProfileCAS']))
			$this->entity->getAbiturientProfile()->setCAS($profileData['abiturientProfileCAS']);

		if (isset($profileData['abiturientProfileAdditionalLanguage']))
			$this->entity->getAbiturientProfile()->setAdditionalLanguage($profileData['abiturientProfileAdditionalLanguage']);

		if (isset($profileData['abiturientProfileNationality']))
			$this->entity->getAbiturientProfile()->setNationality($profileData['abiturientProfileNationality']);

		if (isset($profileData['abiturientProfileNationalityCountry']))
			$this->entity->getAbiturientProfile()->setNationalityCountry($profileData['abiturientProfileNationalityCountry']);

		if (isset($profileData['abiturientProfileIdentityDocumentType']))
			$this->entity->getAbiturientProfile()->setIdentityDocumentType($profileData['abiturientProfileIdentityDocumentType']);

		if (isset($profileData['abiturientProfileIdentityDocumentSeries']))
			$this->entity->getAbiturientProfile()->setIdentityDocumentSeries($profileData['abiturientProfileIdentityDocumentSeries']);

		if (isset($profileData['abiturientProfileIdentityDocumentNumber']))
			$this->entity->getAbiturientProfile()->setIdentityDocumentNumber($profileData['abiturientProfileIdentityDocumentNumber']);

		if (isset($profileData['abiturientProfileIdentityDocumentIssuedBy']))
			$this->entity->getAbiturientProfile()->setIdentityDocumentIssuedBy($profileData['abiturientProfileIdentityDocumentIssuedBy']);

		if (isset($profileData['abiturientProfileIdentityDocumentIssuedDate'])) {
			try {
				$date = new \DateTime($profileData['abiturientProfileIdentityDocumentIssuedDate']);
				$this->entity->getAbiturientProfile()->setIdentityDocumentIssuedDate($date);
			} catch (\Exception $e) {
				$this->addError('Должна быть указана корректная дата', 'AbiturientProfile', 'abiturientProfileIdentityDocumentIssuedDate');
			}
		}

		if (isset($profileData['abiturientProfileIdentityDocumentIssuedCode']))
			$this->entity->getAbiturientProfile()->setIdentityDocumentIssuedCode($profileData['abiturientProfileIdentityDocumentIssuedCode']);

		if (isset($profileData['abiturientProfileInsuranceCompanyName']))
			$this->entity->getAbiturientProfile()->setInsuranceCompanyName($profileData['abiturientProfileInsuranceCompanyName']);

		if (isset($profileData['abiturientProfileInsuranceNumber']))
			$this->entity->getAbiturientProfile()->setInsuranceNumber($profileData['abiturientProfileInsuranceNumber']);

		if (isset($profileData['abiturientProfileInsuranceSeries']))
			$this->entity->getAbiturientProfile()->setInsuranceSeries($profileData['abiturientProfileInsuranceSeries']);

		if (isset($profileData['abiturientProfileInsuranceDate'])) {
			try {
				$date = new \DateTime($profileData['abiturientProfileInsuranceDate']);
				$this->entity->getAbiturientProfile()->setInsuranceDate($date);
			} catch (\Exception $e) {
				$this->addError('Должна быть указана корректная дата', 'AbiturientProfile', 'abiturientProfileInsuranceDate');
			}
		}

		if (isset($profileData['abiturientProfileRegistrationAddress']))
			$this->entity->getAbiturientProfile()->setRegistrationAddress($profileData['abiturientProfileRegistrationAddress']);

		if (isset($profileData['abiturientProfileEducationOrganizationType']))
			$this->entity->getAbiturientProfile()->setEducationOrganizationType($profileData['abiturientProfileEducationOrganizationType']);

		if (isset($profileData['abiturientProfileEducationOrganizationNumber']))
			$this->entity->getAbiturientProfile()->setEducationOrganizationNumber($profileData['abiturientProfileEducationOrganizationNumber']);

		if (isset($profileData['abiturientProfileEducationOrganizationCity']))
			$this->entity->getAbiturientProfile()->setEducationOrganizationCity($profileData['abiturientProfileEducationOrganizationCity']);

		if (isset($profileData['abiturientProfileEducationOrganizationName']))
			$this->entity->getAbiturientProfile()->setEducationOrganizationName($profileData['abiturientProfileEducationOrganizationName']);

		if (isset($profileData['abiturientProfileEducationCompletionDate'])) {
			try {
				$date = new \DateTime($profileData['abiturientProfileEducationCompletionDate']);
				$this->entity->getAbiturientProfile()->setEducationCompletionDate($date);
			} catch (\Exception $e) {
				$this->addError('Должна быть указана корректная дата', 'AbiturientProfile', 'abiturientProfileEducationCompletionDate');
			}
		}

		if (isset($profileData['abiturientProfileEducationDocumentType']))
			$this->entity->getAbiturientProfile()->setEducationDocumentType($profileData['abiturientProfileEducationDocumentType']);

		if (isset($profileData['abiturientProfileEducationDocumentNumber']))
			$this->entity->getAbiturientProfile()->setEducationDocumentNumber($profileData['abiturientProfileEducationDocumentNumber']);

		if (isset($profileData['abiturientProfileEducationDocumentSeries']))
			$this->entity->getAbiturientProfile()->setEducationDocumentSeries($profileData['abiturientProfileEducationDocumentSeries']);

		if (isset($profileData['abiturientProfileAdditionalData']))
			$this->entity->getAbiturientProfile()->setAdditionalData($profileData['abiturientProfileAdditionalData']);

		if (isset($profileData['abiturientProfileSeniority']))
			$this->entity->getAbiturientProfile()->setSeniority($profileData['abiturientProfileSeniorityString']);
		else
			$this->entity->getAbiturientProfile()->setSeniority(false);

		if (isset($profileData['abiturientProfileOlympiadWinner']))
			$this->entity->getAbiturientProfile()->setOlympiadWinner($profileData['abiturientProfileOlympiadWinnerString']);
		else
			$this->entity->getAbiturientProfile()->setOlympiadWinner(false);

		if (isset($profileData['abiturientProfileGraduatedWithHonours']))
			$this->entity->getAbiturientProfile()->setGraduatedWithHonours(true);
		else
			$this->entity->getAbiturientProfile()->setGraduatedWithHonours(false);

		if (isset($profileData['abiturientProfileFirstTimeEnrolment']))
			$this->entity->getAbiturientProfile()->setFirstTimeEnrolment(true);
		else
			$this->entity->getAbiturientProfile()->setFirstTimeEnrolment(false);

		$this->updateAbiturientProfileFiles($profileFiles);

        $errors = D::$v->validate($this->entity->getAbiturientProfile());

        if ($errors->count())
            foreach ($errors as $error)
                /** @var ConstraintViolation $error */
                $this->addError($error->getMessage(), 'AbiturientProfile', $error->getPropertyPath());

        $domainErrors = $this->getErrors();
        if (empty($domainErrors))
            $this->entity->getAbiturientProfile()->setIsCorrect(true);
        else
            $this->entity->getAbiturientProfile()->setIsCorrect(false);

		$this->persistEntity($this->entity->getAbiturientProfile());
	}

	public function getUserEmail()
	{
		return $this->entity->getEmail();
	}

    public function getUserEmailConfirmationCode()
    {
        if ($this->entity->getUserValidData() == null)
            return null;

        return $this->entity->getUserValidData()->getUserValidDataEmailConfirmCode();
    }

    public function getUserPhoneConfirmationCode()
    {
        if ($this->entity->getUserValidData() == null)
            return null;

        return $this->entity->getUserValidData()->getUserValidDataPhoneConfirmCode();
    }

    public function getUserPersonalPhone()
    {
        return $this->entity->getPersonalPhone();
    }

	public function getUserFullName()
	{
		return $this->entity->getFullName();
	}

    public function getUserId()
    {
        return $this->entity->getId();
    }

    public function isUserDataConfirmed()
    {
        $userValidData = $this->entity->getUserValidData();

        if (empty($userValidData))
            return false;

        return $userValidData->getStatus();
    }

    public function getUserPasswordHash()
    {
        return $this->entity->getPasswordHash();
    }

    /**
     * @param $codeType
     * @return string сгенерированный код подтверждения
     * @throws \Spo\Site\Exceptions\ArgumentException
     */
    public function generateNewConfirmationCode($codeType)
    {
        if ($codeType != self::CONFIRMATION_CODE_TYPE_EMAIL && $codeType != self::CONFIRMATION_CODE_TYPE_PHONE)
            throw ArgumentException::argumentIncorrect('codeType');

        $userValidData = $this->entity->getUserValidData();
        if (!$userValidData) {
            $userValidData = new UserValidData();
            $userValidData->setUser($this->entity);
            $userValidData->setStatus(false);
        }

        $code = UserDataConfirmationHelper::generateConfirmationCode();

        if ($codeType == self::CONFIRMATION_CODE_TYPE_EMAIL) {
            $userValidData->setUserValidDataEmailConfirmCode($code);
        } elseif ($codeType == self::CONFIRMATION_CODE_TYPE_PHONE) {

            $userPersonalPhone = $this->entity->getPersonalPhone();
            if (empty($userPersonalPhone)) {
                $this->addError('Необходимо указать ваш номер телефона');
                return false;
            }

            if ($userValidData->getPhoneConfirmCodeRequestDate() != null) {
                $lastRequestDate = $userValidData->getPhoneConfirmCodeRequestDate();
                $now = new \DateTime();

                if ($now->diff($lastRequestDate)->format('%i') < 1) {
                    $this->addError(
                        'Запрос на получение кода подтверждения по смс можно отправлять не чаще, чем раз в минуту'
                    );
                    return false;
                }
            }

            $userValidData->setUserValidDataPhoneConfirmCode($code);
            $userValidData->setPhoneConfirmCodeRequestDate(new \DateTime());
        }


        $this->entity->setUserValidData($userValidData);
        $this->persistEntity($this->entity->getUserValidData());

        return true;
    }

    public function confirmUserEmail($code)
    {
        if ($this->entity->getUserValidData()->getUserValidDataEmailConfirmCode() != $code) {
            $this->addError('Неправильный код подтверждения', 'ConfirmationForm', 'emailCode');
            return false;
        }

        $this->entity->getUserValidData()->setUserValidDataEmail($this->entity->getEmail());
        $this->entity->getUserValidData()->setUserValidDataEmailConfirmDate(new \DateTime());
        $this->persistEntity($this->entity->getUserValidData());
    }

    public function confirmUserPhone($code)
    {
        if ($this->entity->getUserValidData()->getUserValidDataPhoneConfirmCode() != $code) {
            $this->addError('Неправильный код подтверждения', 'ConfirmationForm', 'phoneCode');
            return;
        }

        $this->entity->getUserValidData()->setUserValidDataPhone($this->entity->getPersonalPhone());
        $this->entity->getUserValidData()->setUserValidDataPhoneConfirmDate(new \DateTime());
        $this->persistEntity($this->entity->getUserValidData());
    }

    public function refreshUserValidDataStatus()
    {
        $currentUserValidData = $this->entity->getUserValidData();
        if (empty($currentUserValidData))
            return;

        $status = true;

        if ($this->entity->getEmail() != $currentUserValidData->getUserValidDataEmail()) {
            $currentUserValidData->setUserValidDataEmailConfirmCode(null);
            $status = false;
        }

        if ($this->entity->getPersonalPhone() != $currentUserValidData->getUserValidDataPhone()) {
            $currentUserValidData->setUserValidDataPhoneConfirmCode(null);
            $status = false;
        }

        $currentUserValidData->setStatus($status);
        $this->entity->setUserValidData($currentUserValidData);
        $this->persistEntity($this->entity->getUserValidData());

    }


    /**
     *
     * Проверяет, есть ли среди идентификаторов в переданом массиве идентификатор,
     * соответствующий группе "Сотрудник департамента образования"
     *
     * @param CUser $bxUser
     * @return bool
     */
    public static function checkIsEduDepartmentEmployee(CUser $bxUser)
    {
        return self::checkUserBelongsToGroups(self::EDUCATIONAL_DEPARTMENT_EMPLOYEE, $bxUser->GetUserGroupArray());
    }
}
?>