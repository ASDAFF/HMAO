<?php
namespace Spo\Site\Util\Notification;

use Spo\Site\Exceptions\SpoException;
use Spo\Site\Doctrine\Entities\BitrixUser;
use Spo\Site\Domains\UserDomain;
use CSMS;

\Bitrix\Main\Loader::includeModule('sozdavatel.sms');

class SmsNotifier
{
    protected static $messages = array(
        'USER_ACCOUNT_ACTIVATION' => 'Код для активации аккаунта #ACTIVATION_CODE#'
    );

    protected static function composeInnerMessage($msgId, $variables)
    {
        if(!isset(self::$messages[$msgId])){
            throw new SpoException('Тип сообщения не найден');
        }
        $msg = self::$messages[$msgId];
        return self::composeMessage($msg, $variables);
    }

    protected static function composeMessage($msg, $variables)
    {
        $composedMessage = $msg;
        foreach($variables as $variable=>$value)
        {
            $composedMessage = str_replace('#' . $variable . '#', $value, $composedMessage);
        }
        return $composedMessage;
    }

	/*public static function sendUserAccountActivationRequired($userId)
	{
        $userDomain = UserDomain::loadByUserId($userId);
        $activationCode = $userDomain->getUserPhoneConfirmationCode();

        $variables = array(
            'ACTIVATION_CODE' => $activationCode
        );

        $phone = $userDomain->getUserPersonalPhone();
        $msg   = self::composeInnerMessage('USER_ACCOUNT_ACTIVATION', $variables);
		return self::sendSms($phone, $msg);
	}*/


    /**
     * SMS Bliss
     *
     * Одно SMS-сообщение - это информация размером до 140 байт, вмещающих до 160 символов в латинице,
     * либо до 70 символов в кириллице, или каждая часть сочленённого сообщения размером до 133 байт,
     * вмещающая до 153 символов в латинице, либо до 67 символов в кириллице, или каждое бинарное
     * сообщение размером до 140 байт. Заключительные фрагменты сочленённых текстовых или бинарных
     * сообщений меньшего размера, считаются как отдельные сообщения. Например, в случае отправки двух
     * сочлененных сообщений, их длина ограничена 306 символами в латинице и 134 символами в кириллице,
     * при трех сочлененных сообщениях, соответственно, 459 и 201 символов.
     *
     * @param $phone - телефон (Функция сама удаляет все нецифровые символы)
     * @param $message - текст сообщения
     * @return bool - true (если успех или если отправка смс выключена!!), false, если ошибка
     */
    protected static function sendSms($phone, $message)
	{
        $csms = new CSMS();

        //todo предусмотреть оповещение администратора о неудачных отправках
        $result = $csms->Send($message, $phone);


        return $result;
	}

}