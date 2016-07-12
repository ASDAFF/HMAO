<?php
namespace Spo\Site\Util\Notification;

use CEvent;
use Spo\Site\Doctrine\Repositories\ApplicationEventRepository;
use Spo\Site\Domains\ApplicationDomain;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Doctrine\Entities\Organization;
use Spo\Site\Helpers\OrganizationInfoUrlHelper;
use Spo\Site\Domains\UserDomain;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Doctrine\Entities\ApplicationEvent;
use Spo\Site\Dictionaries\ApplicationEventReason;
use Spo\Site\Entities\UserValidDataTable;

class MailNotificator
{

    // Идентификаторы почтовых событий. Каждому событию должен соответствовать один и только один почтовый шаблон. Технически
    // можно назначить каждому событию несколько шаблонов, но шаблоны эти впоследствии можно идентифицировать только по целочисленному
    // идентификатору. Чтобы не учитывать это при установке модуля, делаем каждый раз пару "событие + единственный шаблон"
    static $emailEvents = array(
        'application' => array(
            'created' => 'APPLICATION_CREATED',
            'returned' => 'APPLICATION_RETURNED',
            'accepted' => 'APPLICATION_ACCEPTED',
            'declined' => 'APPLICATION_DECLINED',
            'deleted' => 'APPLICATION_DELETED',
            'changedByUser' => 'APPLICATION_STATUS_CHANGE_BY_ORGANIZATION', // Старое, позже удалить
        ),
        'confirmation' => array(
            'emailConfirmation' => 'USER_EMAIL_CONFIRMATION'
        )
    );

	public static function sendApplicationStatusChangedByUser($applicationId)
	{
		$applicationDomain = ApplicationDomain::loadById($applicationId);
        $userDomain = UserDomain::loadByUserId($applicationDomain->getUserId());
		$organizationId = $applicationDomain->getApplicationOrganizationId();
		$organizationDomain = OrganizationDomain::loadById($organizationId);

		$variables = array(
			'ORGANIZATION_NAME' => $organizationDomain->getOrganizationName(),
			'ORGANIZATION_ADDRESS' => $organizationDomain->getOrganizationAddress(true),
			'ORGANIZATION_INFO_LINK' => OrganizationInfoUrlHelper::getOrganizationMainPageUrl($organizationId),
			'ABITURIENT_EMAIL' => $userDomain->getUserEmail(),
			'ABITURIENT_FIO' => $userDomain->getUserFullName(),
			'APPLICATION_ID' => $applicationId,
			'APPLICATION_STATUS_STR' => ApplicationStatus::getValue($applicationDomain->getApplicationStatus()),
			'APPLICATION_SPECIALTY' => $applicationDomain->getApplicationSpecialtyTitle(),
		);

		self::sendMail(self::$emailEvents['application']['changedByUser'], $variables);
	}

    public static function sendApplicationStatusChanged($applicationEventId)
    {
        /** @var ApplicationEvent $applicationEvent */
        $applicationEvent = ApplicationEventRepository::create()->find($applicationEventId);
        $application = $applicationEvent->getApplication();
        $user = $application->getUser();
        $organization = $application->getOrganization();

        $variables = array(
            'ORGANIZATION_NAME' => $organization->getOrganizationName(),
            'ORGANIZATION_ADDRESS' => $organization->getOrganizationAddress(),
            'ORGANIZATION_INFO_LINK' => OrganizationInfoUrlHelper::getOrganizationMainPageUrl($organization->getId()),
            'ABITURIENT_EMAIL' => $user->getEmail(),
            'ABITURIENT_FIO' => $user->getFullName(),
            'APPLICATION_ID' => $application->getId(),
            'APPLICATION_STATUS_STR' => ApplicationStatus::getValue($applicationEvent->getStatus()),
            'APPLICATION_SPECIALTY' => $application->getOrganizationSpecialty()->getSpecialty()->getTitle(),
            'APPLICATION_EVENT_REASON' => ApplicationEventReason::getValue($applicationEvent->getReason()),
            'APPLICATION_EVENT_COMMENT' => $applicationEvent->getComment() ?  $applicationEvent->getComment() : '-',
        );

        switch ($applicationEvent->getStatus()) {
            case ApplicationStatus::CREATED:
                self::sendMail(self::$emailEvents['application']['created'], $variables);
                break;
            case ApplicationStatus::RETURNED:
                self::sendMail(self::$emailEvents['application']['returned'], $variables);
                break;
            case ApplicationStatus::DECLINED:
                self::sendMail( self::$emailEvents['application']['declined'], $variables);
                break;
            case ApplicationStatus::ACCEPTED:
                self::sendMail(self::$emailEvents['application']['accepted'], $variables);
                break;
            case ApplicationStatus::DELETED:
                self::sendMail(self::$emailEvents['application']['deleted'], $variables);
                break;
            default:
                self::sendMail(self::$emailEvents['application']['changedByUser'], $variables);
        }
    }

    public static function sendEmailConfirmationCodeGenerated($userId)
    {
        global $USER;
        $userDomain = UserDomain::loadByUserId($userId);
        $params = array(
            'filter' => array(
                '=USER_ID' => $userId,
            ),
            'select' => array(
                'user_valid_data_email_confirm_code' => 'USER_VALID_DATA_EMAIL_CONFIRM_CODE',
            )
        );
        $resultDb = UserValidDataTable::getList($params)->fetchAll();
        //$confirmationCode = $userDomain->getUserEmailConfirmationCode();
        $variables = array(
            'EMAIL' => $USER->GetEmail(),
            'LOGIN' => $USER->GetLogin(),
            'CONFIRMATION_CODE' => $resultDb[0]['user_valid_data_email_confirm_code'],
        );
        return self::sendMail('NEW_USER_CONFIRM', $variables,5);
    }

	private static function sendMail(
		$eventType, $variables, $templateId = null, $duplicate = false, $immediately = false
	)
	{
		$duplicate = ($duplicate) ? 'Y' : 'N';
		$templateId = ($templateId === null) ? '' : $templateId;

		if ($immediately) {
			CEvent::SendImmediate($eventType, 's1', $variables, $duplicate, $templateId);
		} else {
			CEvent::Send($eventType, 's1', $variables, $duplicate, $templateId);
		}
        return true;
	}

}