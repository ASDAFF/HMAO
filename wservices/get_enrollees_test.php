<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

<<<<<<< HEAD
$date = '2016-05-16';
=======
$date = '2016-05-17';
>>>>>>> FETCH_HEAD

if(!CModule::IncludeModule("webservice") || !CModule::IncludeModule("iblock")) return;
Bitrix\Main\Loader::includeModule('spo.site');

use Bitrix\Main\Type;
use Spo\Site\Entities\ApplicationEventTable;
use Spo\Site\Entities\ApplicationTable;
<<<<<<< HEAD
use Spo\Site\Entities\AbiturientProfileTable;

$arResult = array();
$datef="";
$BlockApplications=array();

/*получение списка */
$rsBase = ApplicationTable::getList(array(
	'select'=> array(
		'*',
		'DATE'		=>	'APPLICATION_EVENT.APPLICATION_EVENT_DATE',
		'IdProgram'	=>	'ORGANIZATION_SPECIALTY.IDPROGRAM',//Программа обучения
		''
		),

	'filter'=> array(
		'=APPLICATION_STATUS'	=> 2,
		'>DATE'					=> new Type\Date($date, 'Y-m-d')
	),
))->fetchAll();
var_dump($rsBase);
die;
while ($row = $rsBase->fetch())
{
	$arResult['EVENTS'] = array(
=======

$arResult = array();

$rsBase = ApplicationEventTable::getList(array(
    'select' => array('*'),
	'filter' => array('>APPLICATION_EVENT_DATE' => new Type\Date($date, 'Y-m-d'))
));

while ($row = $rsBase->fetch())
{
    $arResult['EVENTS'] = array(
>>>>>>> FETCH_HEAD
		'ID'	 		=> $row['APPLICATION_EVENT_ID'],
		'APPLICATION' 	=> $row['APPLICATION_ID'],
		'USER'			=> $row['USER_ID'],
	);
	$arResult['APPLICATIONS'][$row['APPLICATION_ID']] = array();
}

<<<<<<< HEAD


$rsBase = ApplicationTable::getList(array(
	'select' => array('*'),
=======
$rsBase = ApplicationTable::getList(array(
    'select' => array('*'),
>>>>>>> FETCH_HEAD
	'filter' => array('>APPLICATION_EVENT_DATE' => new Type\Date($date, 'Y-m-d'))
));
while ($row = $rsBase->fetch())
{
<<<<<<< HEAD
	printvar('row',$row);
=======
    printvar('row',$row);
>>>>>>> FETCH_HEAD
}



printvar('d',$arResult);

<<<<<<< HEAD
?>
=======
?>
>>>>>>> FETCH_HEAD
