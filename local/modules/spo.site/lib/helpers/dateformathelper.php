<?php
namespace Spo\Site\Helpers;

use Spo\Site\Util\CVarDumper;

class DateFormatHelper
{
    /**
     * Преобразует количество месяцев в читаемый вид - "N лет M месяцев"
     * TODO Вспомогательный глупый метод, скорее всего станет не нужен, если длительность обучения (studyPeriod)
     * не произвольная, а будут заранее определены все варианты и занесены в dictionary
     *
     * @param integer $months
     * @param boolean $shortFormat использовать краткие подписи "г." и "м." вместо полных "года" и "месяцев"
     * @return string $yearsAndMonths
     */
    public static function months2YearsMonths($months, $shortFormat = true)
    {
        if ($shortFormat) {
            $yearsLabel = 'г.';
            $monthsLabel = 'м.';
        }
        else {
            $yearsLabel = 'года';
            $monthsLabel = 'месяцев';
        }

        $monthsNumber = ($months % 12);
        $yearsNumber = (($months - $months % 12) / 12);

        $result =  (($yearsNumber > 0) ? $yearsNumber . ' ' . $yearsLabel . ' ' : '') . (($monthsNumber > 0) ? $monthsNumber . ' ' . $monthsLabel : '');

        return $result;
    }
}

