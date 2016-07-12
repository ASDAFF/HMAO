<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class ParentsType extends SPODictionary
{
    const MOTHER = 1;
    const FATHER = 2;

    protected static $values = array(
        self::MOTHER => 'Мать',
        self::FATHER => 'Отец',
    );

}
/**/