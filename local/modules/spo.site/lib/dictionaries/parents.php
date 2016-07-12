<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class Parents extends SPODictionary
{
    const PARENT = 1;
    const GUARDIAN = 2;   

    protected static $values = array(
        self::PARENT => 'Родители',
        self::GUARDIAN => 'Законный представитель',
    );
    protected static $class = array(
        self::PARENT => 'parent',
        self::GUARDIAN => 'guard',
    );
}


/**/