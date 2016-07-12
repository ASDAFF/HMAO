<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 19.05.15
 * Time: 13:47
 */
namespace Spo\Site\Exceptions;
use Exception;

class DomainException extends SpoException
{
    const CODE_DOMAIN_NOT_FOUND = 1;
    protected static $defaultMessage = 'Ошибка обработки данных';

    public static function domainNotFound($id = '')
    {
        $msg = 'Домен "' . $id . '" с идентификатором не найден';
        return new static($msg, self::CODE_DOMAIN_NOT_FOUND);
    }
}