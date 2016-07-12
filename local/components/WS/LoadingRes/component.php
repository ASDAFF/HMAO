<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("webservice") || !CModule::IncludeModule("iblock")) return;

Bitrix\Main\Loader::includeModule('spo.site');

use Spo\Site\Entities\ApplicationTable;
use Spo\Site\Entities\AbiturientProfileTable;

// наш новый класс наследуется от базового IWebService
class LoadingResultsWS extends IWebService
{
    public function LoadingResults($inn, $date, $IdAbiturient)
    {
        $res="OK!!!";
        $applications = $this->getApplications($inn, $date);
        $userIds=array();

        if (!$applications)
        {
            $this->result['Res']="NotFount";
        }
        else
        {
            foreach ($applications as $index => $application) $userIds[] = $application['USER_ID'];
            $userIds=array_unique($userIds);
            foreach ($IdAbiturient as &$IA)
            {
                $IA=(int) $IA;
            }
            $inval_user=array_diff($userIds,$IdAbiturient);
            if (count($inval_user)>0)
            {

            }
        }
        return $res;

        
    }
    // метод GetWebServiceDesc возвращает описание сервиса и его методов
    public function GetWebServiceDesc()
    {
        $wsdesc = new CWebServiceDesc();
        $wsdesc->wsname = "bitrix.WS.LoadingResults";
        $wsdesc->wsclassname = "LoadingResultsWS";
        $wsdesc->wsdlauto = true;
        $wsdesc->wsendpoint = CWebService::GetDefaultEndpoint();
        $wsdesc->wstargetns = CWebService::GetDefaultTargetNS();
        $wsdesc->classTypes = array();
        $wsdesc->structTypes = Array();

        $wsdesc->structTypes["arIdAbitureent"] = array("varType" => "string", "strict" => "off");

        $wsdesc->classes = array(
            "LoadingResultsWS" => array(
                "LoadingResults" => array(
                    "type" => "public",
                    "input" => array(
                        "organization"      => array("varType" => "string", "strict" => "off"),
                        "date"              => array("varType" => "string", "strict" => "off"),
                        "idabitureent"      => array("arrType" => "arIdAbitureent", "varType" => "IdAbitureent", "strict" => "off"),
                    ),
                    "output"	=> array(
                        "Res"               => array("varType" => "string", "strict" => "off")
                    ),
                    "httpauth" => "Y" 
                ),
            )
        );

        return $wsdesc;
    }


    /**
     * @param $inn
     * @param $date
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    protected function getApplications($inn, $date)
    {
        $result = array();
        print_r($inn, $date);
        $date = date('d.m.Y H:i:s', strtotime($date));

        $applicationDb = ApplicationTable::getList(array(
            'filter' => array(
                '=ORGANIZATION.INN' => $inn,
                '>APPLICATION_EVENT.APPLICATION_EVENT_DATE' => $date,
            ),
            'select' => array(
                'IdProgram' => 'ORGANIZATION_SPECIALTY.IDPROGRAM',
                'FinanceProduct' => 'APPLICATION_FUNDING_TYPE',
                'IdSpecialization' => 'ORGANIZATION_SPECIALTY.IDSPECIALIZATION',
                'TargetDirection' => 'ORGANIZATION_SPECIALTY.TARGETDIRECTION',
                'ApplicationPriority' => 'APPLICATION_PRIORITY',
                'USER_ID'
            )
        ));

        while ($application = $applicationDb->fetch()) {
            if(AbiturientProfileTable::UserIds($application['USER_ID'])['VALIDITY']!=1)
            {
                continue;
            }
            $result[] = array(
                'IdProgram' => strval($application['IdProgram']),
                'IdAbiturient' =>strval($application['USER_ID']),
                'FinanceProduct' => strval($application['FinanceProduct']),
                'IdSpecialization' => strval($application['IdSpecialization']),
                'TargetDirection' => strval($application['TargetDirection']),
                'ApplicationPriority' => strval($application['ApplicationPriority']),
                'USER_ID' => $application['USER_ID']
            );
        }

        return $result;
    }

    /**
     * @param  array $userIds
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    protected function trueProfiles($userIds)
    {
        $result = 'OK!!!';
        print_r($userIds);
        $profilesDb = AbiturientProfileTable::getList(array(
            'filter' => array(
                '=USER_ID' => $userIds
            ),
            'select' => array(
                'Id' => 'SPO_ABITURIENT_PROFILE_ID',
            )
        ));
        while ($Id = $profilesDb->fetch())
        {

            /*$res=AbiturientProfileTable::update($Id['Id'],array(
                'VALIDITY' =>  0,
            ));
            if (!$res->isSuccess()) {
                $result=implode(' Error:', $res->getErrorMessages());
            }*/
        }
        die;
        return $result;
    }

    protected function getProfiles($userIds)
    {
        $result = 'OK!!!';
        print_r($userIds);
        $profilesDb = AbiturientProfileTable::getList(array(
            'filter' => array(
                '=USER_ID' => $userIds
            ),
            'select' => array(
                'Id' => 'SPO_ABITURIENT_PROFILE_ID',
            )
        ));
        while ($Id = $profilesDb->fetch())
        {

            /*$res=AbiturientProfileTable::update($Id['Id'],array(
                'VALIDITY' =>  0,
            ));
            if (!$res->isSuccess()) {
                $result=implode(' Error:', $res->getErrorMessages());
            }*/
        }
        die;
        return $result;
    }
    /**
     * @param $date
     * @return string
     */
    protected function convertDateTo1cFormat($date) {
        $format = "Y-m-d";
        if ($date instanceof \Bitrix\Main\Type\Date) {
            $timestamp = $date->getTimestamp();
        } else {
            $timestamp = strtotime($date);
        }

        return date($format, $timestamp) . "T00:00:00";
    }
}

$arParams["WEBSERVICE_NAME"] = "bitrix.WS.LoadingResults";
$arParams["WEBSERVICE_CLASS"] = "LoadingResultsWS";
$arParams["WEBSERVICE_MODULE"] = "";

// передаем в компонент описание веб-сервиса
$APPLICATION->IncludeComponent(
    "bitrix:webservice.server",
    "",
    $arParams
);


die();
?>
