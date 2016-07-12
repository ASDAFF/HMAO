<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 19.05.15
 * Time: 13:47
 */
namespace Spo\Site\Exceptions;
use Exception;

class AccessException extends SpoException
{
    const CODE_USER_IS_NOT_ORGANIZATION_EMPLOYEE = 1;
    const CODE_USER_IS_NOT_ADMIN = 2;
    const USER_CONFIRMATION_HASH_INCORRECT = 3;
    const USER_IS_NOT_AUTHORIZED = 4;
    const CODE_USER_IS_NOT_EDUCATION_DEPARTMENT_EMPLOYEE = 5;

    protected static $defaultMessage = 'Ошибка доступа';

    public static function isNotEducationDepartmentEmployee()
    {
        $msg = 'Пользователь не является сотрудником департамента образования';
        return new static($msg, self::CODE_USER_IS_NOT_EDUCATION_DEPARTMENT_EMPLOYEE);
    }

    public static function isNotOrganizationEmployee()
    {
        $msg = 'Пользователь не является сотрудником организации';
        return new static($msg, self::CODE_USER_IS_NOT_ORGANIZATION_EMPLOYEE);
    }

    public static function isNotAdmin()
    {
        $msg = 'Пользователь не является администратором';
        return new static($msg, self::CODE_USER_IS_NOT_ADMIN);
    }

    public static function incorrectConfirmationHash()
    {
        return new static(self::$defaultMessage, self::USER_CONFIRMATION_HASH_INCORRECT);
    }

    public static function isNotAuthorized()
    {
        $msg = 'Пользователь не авторизован';
        return new static($msg, self::USER_IS_NOT_AUTHORIZED);
    }
}