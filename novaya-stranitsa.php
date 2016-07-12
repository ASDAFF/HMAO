<?php
use Doctrine\Common\Lexer\AbstractLexer;

use Spo\Site\Doctrine\Repositories\AdmissionPlanRepository;
use Spo\Site\Entities\AdmissionPlanTable;
use Spo\Site\Entities\AdmissionPlanEventTable;

AbstractLexer::reset();
die;
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_CHECK", true);
define('PUBLIC_AJAX_MODE', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]="N";
$APPLICATION->ShowIncludeStat = false;

//CModule::IncludeModule('spo.site');

/*use Spo\Site\Entities\OrganizationTable;


/*$inn=2147483647;
$res=OrganizationTable::getList(array(
    'filter' => array(
        'INN'=>$inn,
    ),
    'select' => array(
        'id'                    =>  'ADMISSION_PLAN_ID'
    )
))->fetchAll();
var_dump($res);
die;
*/

$result = AdmissionPlanTable::getList(array())->fetchAll();
var_dump($result);
die;
//Сделана
//$result = AdmissionPlanTable::filterByOrganizationId(24);
//Cделано
//$result = AbiturientProfileTable::exceptUserId(162);
//Cделано
/*$result = AdmissionPlanEventTable::getAdmissionPlanEvents(76);
//$input_filter = new AdmissionPlanRepository();
//$result = $input_filter -> loadAdmissionPlanInfoById(8);
/*echo "<pre>";
print_r($result);
echo "</pre>";*/
?>