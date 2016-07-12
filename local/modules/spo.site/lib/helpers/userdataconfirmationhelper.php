<?php
namespace Spo\Site\Helpers;

use Spo\Site\Core\SpoUrlHelper;


class UserDataConfirmationHelper
{
    public static function generateConfirmationCode()
    {
        $length = 5;
        $num = rand(11111, 99999);
        $code = md5($num);
        return substr($code, 0, $length);
    }

    public static function generatePhoneConfirmationCode()
    {
        return rand(100000, 999999);
    }

}