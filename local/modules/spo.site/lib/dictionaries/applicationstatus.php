<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class ApplicationStatus extends SPODictionary
{
	const CREATED = 1;
	const ACCEPTED = 2;
	const DECLINED = 3;
	const DELETED = 4;
    const RECEIVED = 5;
    const RETURNED = 6;
    const IMPORT = 7;
    const PRIOR = 8;
    const ENTERED = 9;
    const NOTENTERED = 10;

	protected static $values = array(
		self::CREATED       => 'На рассмотрении',
		self::ACCEPTED      => 'Принято',
		self::DECLINED      => 'Отклонено',
		self::DELETED       => 'Отменена',
		self::RETURNED      => 'Отправлено на доработку',
		self::RECEIVED      => 'Взято в работу',
        self::IMPORT        => 'Принято, данные переданные',
        self::PRIOR         => 'Изменён приоритет заявления',
        self::ENTERED       => 'Принято, рекомендован к зачислению',
        self::NOTENTERED    => 'Не рекомендован к зачислению',
	);

    /*
     * Правила перехода статусов заявок
     * Ключ - статус с которого осуществляется переход
     * Значение - массив статусов, на которые переход возможен
     * Если ключа нет (Например CREATED), значит возможен переход на любой статус
     * Если массв пуст, значит статус является окончательным
     */
    protected static $statusChangeRules = array(
        self::ACCEPTED => array(
            self::DELETED,
        ),
        self::DECLINED => array(
            self::DELETED,
        ),
        self::DELETED  => array(),
        self::RECEIVED => array(
            self::DELETED,
            self::ACCEPTED,
            self::DECLINED,
        ),
        self::RETURNED => array(
            self::CREATED,
            self::DELETED,
        ),
    );

    /**
     * Можно ли менять статус с $from на $to
     * @param int $from
     * @param int $to
     * @return bool
     */
    public static function canChangeStatus($from, $to)
    {
        $from = intval($from);
        $to   = intval($to);

        if(!isset(self::$statusChangeRules[$from])){
            return true;
        }

        return in_array($to, self::$statusChangeRules[$from]);
    }

    public static function getAvailableStatusChanges($from)
    {
        $from = intval($from);

        return isset(self::$statusChangeRules[$from]) ? self::$statusChangeRules[$from] : self::getClassConstants();
    }

}