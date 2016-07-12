<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class IdentityRegisterType extends SPODictionary
{
    const PERMANENT_REGISTRATION = 1;
    const TEMPORARY_REGISTRATION = 2;

    protected static $values = array(
        self::PERMANENT_REGISTRATION => 'Адрес регистрации по месту жительства',
        self::TEMPORARY_REGISTRATION => 'Адрес регистрации по месту пребывания',
    );

}


/**/