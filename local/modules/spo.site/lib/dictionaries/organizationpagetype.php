<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class OrganizationPageType extends SPODictionary
{
	const COMMON = 1;
	const BASIC_INFORMATION = 2;
    const STRUCTURE_AND_MANAGEMENT = 3;
    const DOCUMENTS = 4;
    const EDUCATION = 5;
    const EDUCATIONAL_STANDARDS = 6;
    const MANAGEMENT_TEACHING_STAFF = 7;
    const LOGISTICAL_SUPPORT = 8;
    const SCHOLARSHIPS = 9;
    const PAID_EDUCATIONAL_SERVICES = 10;
    const FINANCIAL_AND_ECONOMIC_ACTIVITY = 11;
    const VACANCY = 12;
    const ENTRANCE_RULES = 13;

    const ENTRANCE_PROGRAM = 14;
    const CREATIVE_TESTS = 15;

    const ORDER_ADMISSION = 14;


	protected static $values = array(
		self::COMMON => 'Общая страница',
        self::BASIC_INFORMATION => 'Основные сведения',
        self::STRUCTURE_AND_MANAGEMENT => 'Структура и органы управления образовательной организацией',
        self::DOCUMENTS => 'Документы',
        self::EDUCATION => 'Образование',
        self::EDUCATIONAL_STANDARDS => 'Образовательные стандарты',
        self::MANAGEMENT_TEACHING_STAFF => 'Руководство. Педагогический (научно-педагогический) состав',
        self::LOGISTICAL_SUPPORT => 'Материально-техническое обеспечение и оснащенность образовательного процесса',
        self::SCHOLARSHIPS => 'Стипендии и иные виды материальной поддержки',
        self::PAID_EDUCATIONAL_SERVICES => 'Платные образовательные услуги',
        self::FINANCIAL_AND_ECONOMIC_ACTIVITY => 'Финансово-хозяйственная деятельность',
        self::VACANCY => 'Вакантные места для приема (перевода)',
        self::ENTRANCE_RULES => 'Правила приёма',

        self::ENTRANCE_PROGRAM => 'Программа вступительных экзаменов',
        self::CREATIVE_TESTS => 'Творческие испытания',
        self::ORDER_ADMISSION => 'Приказы о зачислении',

	);

}