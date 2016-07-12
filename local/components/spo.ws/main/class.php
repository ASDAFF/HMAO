<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("webservice") || !CModule::IncludeModule("iblock"))
    return;

// наш новый класс наследуется от базового IWebService
class TransferPlanWS extends IWebService
{
    // функция вывода реестра задач
    function ProgramPlan($NameLid,$SurnameLid,$PhoneLid,$EmailLid,$OrgLid,$CommentLid,$ComStatus)
    {
        //$NameLid,$SurnameLid,$PhoneLid,$EmailLid,$OrgLid,$CommentLid
        CModule::IncludeModule('crm');
        global $USER;
        global $DB;
        $strSql1 = "SELECT `ELEMENT_ID`
					 FROM  `bitrix`.`b_crm_field_multi`
					 WHERE (
						 `COMPLEX_ID` LIKE  'EMAIL_WORK'
						 AND  `VALUE` LIKE  '".$EmailLid."'
					 )
					 LIMIT 0 , 30";
        //'Test@tdy.ru'
        $res = $DB->Query($strSql1, false, $err_mess.__LINE__);
        $row = $res->Fetch();
        if ($row['ELEMENT_ID']>0){
            $eventDate = ConvertTimeStamp(time() + CTimeZone::GetOffset(), 'FULL', SITE_ID);
            $CCrmEvent = new CCrmEvent();
            $CCrmEvent->Add(
                array(
                    'ENTITY_TYPE'=> 'LEAD',
                    'ENTITY_ID' => $row['ELEMENT_ID'],
                    'EVENT_ID' => 'INFO',
                    'EVENT_TEXT_1' => $CommentLid,
                    'DATE_CREATE' => $eventDate,
                )
            );
            return strval($row['ELEMENT_ID']);
        }
        else
        {
            $res = "";
            CModule::IncludeModule('crm');
            $oLead = new CCrmLead;
            $arFields = Array(
                "TITLE" => $OrgLid,
                "COMPANY_TITLE" => $OrgLid,
                "NAME" => $NameLid,
                "LAST_NAME" => $SurnameLid,
                "SECOND_NAME" => "",
                "POST" => "Менаджер",
                "ADDRESS" => "",
                "COMMENTS" => $CommentLid,
                "SOURCE_DESCRIPTION" => "",
                "STATUS_DESCRIPTION" => "",
                "OPPORTUNITY" => 0,
                "CURRENCY_ID" => "RUB",
                "STATUS_ID" => "NEW",
                "ASSIGNED_BY_ID" => 1,
                "FM" => Array(
                    "EMAIL" => Array(
                        "n0" => Array(
                            "VALUE" => $EmailLid,
                            "VALUE_TYPE" => "WORK",
                        ),
                    ),
                    "PHONE" => Array(
                        "n0" => Array(
                            "VALUE" => $PhoneLid,
                            "VALUE_TYPE" => "WORK",
                        ),
                    ),
                    "WEB" => Array(
                        "n1" => Array(
                            "VALUE" => "",
                            "VALUE_TYPE" => "WORK",
                        ),
                        "n2" => Array(
                            "VALUE" => "",
                            "VALUE_TYPE" => "FACEBOOK",
                        ),
                        "n3" => Array(
                            "VALUE" => "",
                            "VALUE_TYPE" => "TWITTER",
                        ),
                        "n0" => Array(
                            "VALUE" => "",
                            "VALUE_TYPE" => "WORK",
                        ),
                    ),
                    "IM" => Array(
                        "n1" => Array(
                            "VALUE" => "",
                            "VALUE_TYPE" => "SKYPE",
                        ),
                        "n0" => Array(
                            "VALUE" => "",
                            "VALUE_TYPE" => "SKYPE",
                        ),
                    ),
                ),
                "ASSIGNED_BY_ID" => 819,
                "SOURCE_ID" => "WEB",
                "SOURCE_DESCRIPTION" =>$ComStatus,
            );
            $res=$oLead->Add($arFields);
            return strval($res);
        }
    }

    // метод GetWebServiceDesc возвращает описание сервиса и его методов
    function GetWebServiceDesc()
    {
        $wsdesc = new CWebServiceDesc();
        $wsdesc->wsname = "bitrix.webservice.addlid";
        $wsdesc->wsclassname = "CCRMLidWS";
        $wsdesc->wsdlauto = true;
        $wsdesc->wsendpoint = CWebService::GetDefaultEndpoint();
//		$wsdesc->wsendpoint = "https://portal.gkomega.ru/ws_task.php";
        $wsdesc->wstargetns = CWebService::GetDefaultTargetNS();
//		$wsdesc->wstargetns = "https://portal.gkomega.ru/";

        $wsdesc->classTypes = array();
        $wsdesc->structTypes = Array();


        $wsdesc->classes = array(
            "CCRMLidWS"=> array(
                "ProgramPlan" => array(
                    "type"		=> "public",
                    "input"		=> array(
                        "IdProgram" => array("varType" => "string", "strict" => "no"),
                        "NameProgram" => array("varType" => "string", "strict" => "no"),
                        "IdSpecialty" => array("varType" => "string", "strict" => "no"),
                        "FormOfStudy" => array("varType" => "string", "strict" => "no"),
                        "BaseReclamation" => array("varType" => "string", "strict" => "no"),
                        "LevelOfTraining" => array("varType" => "string", "strict" => "no"),
                        "FinanceProduct" => array("varType" => "string", "strict" => "no"),
                        "IdSpecialization" => array("varType" => "string", "strict" => "no"),
                        "NameSpecialization" => array("varType" => "string", "strict" => "no"),
                        "TargetDirection" => array("varType" => "string", "strict" => "no"),
                        "Plan" => array("varType" => "string", "strict" => "no"),
                    ),
                    "output"	=> array(
                        "res" => array("varType" => "string", "strict" => "no")
                    ),
                    "httpauth" => "Y"
                ),
            )
        );

        return $wsdesc;
    }
    function TestComponent()
    {
        global $APPLICATION;
        $client = new CSOAPClient("bitrix.soap", $APPLICATION->GetCurPage());

        // HTTP Authorization required by GetHTTPUserInfo method
        $client->setLogin("admin");
        $client->setPassword("123456");
        $request = new CSOAPRequest("OutputRegistr", CWebService::GetDefaultTargetNS());

        //$request->addParameter("stub", 0);
        $response = $client->send( $request );
        if ($response->FaultString)
            echo $response->FaultString;
        else
            echo "Call GetHTTPUserInfo():".mydump($response)."";


    }


}

$arParams["WEBSERVICE_NAME"] = "bitrix.webservice.addlid";
$arParams["WEBSERVICE_CLASS"] = "TransferPlanWS";
$arParams["WEBSERVICE_MODULE"] = "";

// передаем в компонент описание веб-сервиса
$APPLICATION->IncludeComponent(
    "bitrix:webservice.server",
    "",
    $arParams
);


die();
?>
