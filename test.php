<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "test");
$APPLICATION->SetTitle("test");
Bitrix\Main\Loader::includeModule('spo.site');
use Spo\Site\Entities\OrganizationTable;
use Spo\Site\Entities\AdmissionPlanTable;
use Spo\Site\Entities\AdmissionPlanEventTable;
use Spo\Site\Entities\OrganizationSpecialtyTable;
use Spo\Site\Entities\SpecialtyTable;
use Spo\Site\Entities\OrganizationSpecialtyExamTable;
use Bitrix\Main\Type;
use Spo\Site\Entities\OrganizationSpecialtyAdaptationTable;

$inn='1111111111';
$year='?';
$EntranceTests['0']['IdAbiturient']='120';
$EntranceTests['0']['Test']='Русский язык';
$EntranceTests['0']['Ball']='5';
$EntranceTests['0']['Appear']='0';
$EntranceTests['0']['DateTest']='2016-06-02T00:00:00';
$EntranceTests['0']['From']='Диктант';
$EntranceTests['1']['IdAbiturient']='279';
$EntranceTests['1']['Test']='Русский';
$EntranceTests['1']['Ball']='4';
$EntranceTests['1']['Appear']='0';
$EntranceTests['1']['DateTest']='2016-06-02T00:00:00';
$EntranceTests['1']['From']='Диктант';


$res=array();
$re=array();
$roul=0;

/*функция добовления ответа*/
function result($id,$type,$mas)
{
    global $i;
    global $res;
    $res[$i.":arRes"]["ID"]=strval($id);
    $res[$i.":arRes"]["Kod"]=strval($type);
    $res[$i.":arRes"]["Message"]=strval($mas);
    $i++;
}

function TransferTestData($inn,$year,$EntranceTests)
{
    /*ИНН*/
    if (empty($inn))// ИНН пустой
    {
        result("inn",2,"Empty");
        $error++;
    }
    elseif (strlen($inn)!=10)//проверка на соответствие типа ИНН длина 10
    {
        result("inn",2,"Length not 10");
        $error++;
    }
    else//поиск ИНН в базе
    {
        $org = OrganizationTable::getList(array(
            'select' => array('*'),
            'filter' => array('=INN' => $inn)
        ))->fetchAll();
        if(count($org)==1)
        {
            result("inn",1,"OK");
            $IdOrg=$org[0]["ORGANIZATION_ID"]; // номер организации
        }
        else
        {
            result("inn",3,"NotFound");
            $error++;
        }
    }
    var_dump($IdOrg);

}




TransferTestData($inn,$year,$EntranceTests);



?>
<?$USER->Authorize(1);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>