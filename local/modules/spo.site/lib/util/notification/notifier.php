<?php
namespace Spo\Site\Util\Notification;

use CEvent;
use Spo\Site\Domains\ApplicationDomain;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Domains\UserDomain;
use Spo\Site\Util\Notification\MailNotificator;

class Notifier
{
    const TYPE_BY_EMAIL = 1;
    const TYPE_BY_SMS = 2;

    /**
     * @deprecated
     * @param $applicationId
     * @param int $notificationType
     */
    public function applicationStatusChangeByOrganization($applicationId, $notificationType = self::TYPE_BY_EMAIL)
    {
        //$applicationDomain = $this->loadApplicationDomain($applicationId);
        if($notificationType & self::TYPE_BY_EMAIL){
            MailNotificator::sendApplicationStatusChangedByUser($applicationId);
        }
    }

    public function applicationStatusChanged($applicationEventId, $notificationType = self::TYPE_BY_EMAIL)
    {
        if($notificationType & self::TYPE_BY_EMAIL){
            MailNotificator::sendApplicationStatusChanged($applicationEventId);
        }
    }

    public function emailConfirmationCodeGenerated($userId)
    {
        return MailNotificator::sendEmailConfirmationCodeGenerated($userId);
    }

    /*public function phoneConfirmationCodeGenerated($userId)
    {

        return SmsNotifier::sendUserAccountActivationRequired($userId);
    }*/
}