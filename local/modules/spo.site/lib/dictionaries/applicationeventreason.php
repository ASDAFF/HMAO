<?php
namespace Spo\Site\Dictionaries;

use Spo\Site\Core\SPODictionary;

class ApplicationEventReason extends SPODictionary
{
	const NONE = 1;
	const INCORRECT_APPLICATION = 2;
	const EXAM_NON_APPEARANCE = 3;
    const SELECTION_FAIL = 5;
    const DOCUMENTS_NOT_PROVIDED = 4;
    const CANCELED_BY_ABITURIENT = 6;

    protected static $values = array(
		self::NONE => '',
		self::INCORRECT_APPLICATION => 'Некорректное заявление',
		self::EXAM_NON_APPEARANCE => 'Неявка на вступительные испытания',
		self::SELECTION_FAIL => 'Не проходит по отбору',
        self::DOCUMENTS_NOT_PROVIDED => 'Не предоставлены оригиналы документов',
        self::CANCELED_BY_ABITURIENT => 'Отменена абитуриентом',
    );

}