<?php
/**
 * Created by PhpStorm.
 * User: sham
 * Date: 10.06.2016
 * Time: 11:25
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("webservice") || !CModule::IncludeModule("iblock")) return;


Bitrix\Main\Loader::includeModule('spo.site');

use Spo\Site\Entities\AdmissionPlanTable;
use Spo\Site\Entities\AdmissionPlanEventTable;
use Spo\Site\Entities\OrganizationTable;
use Bitrix\Main\Type;

class TransfetStatisWS extends IWebService
{
    private $res; // ответ на запрос
    private $i=0; // счёчик ответа
    private $error; // фатальние ошибки
    private $date_exam;
// метод GetWebServiceDesc возвращает описание сервиса и его методов


    function result($id,$type,$mas)
    {
        $i=$this->i;
        global $res;
        $this->res[$i.":arRes"]["ID"]=strval($id);
        $this->res[$i.":arRes"]["Kod"]=strval($type);
        $this->res[$i.":arRes"]["Message"]=strval($mas);
        $this->i++;
    }

    function FilleRes($data,$name)
    {
        $str='';
        if(is_array($data))
        {
            foreach ($data as $kd=>$d_val)
            {
                if(is_array($d_val))
                {
                    foreach ($d_val as $k=>$d)
                    {
                        if (is_array($d))
                        {
                            foreach ($d as $kk=>$dd)
                            {
                                if (is_array($dd))
                                {
                                    foreach ($dd as $kkk=>$ddd)
                                    {
                                        $str.="$".$name."['".$kd."']['".$k."']['".$kk."']['".$kkk."']='".$ddd."';
";
                                    }
                                }
                                else
                                {
                                    $str.="$".$name."['".$kd."']['".$k."']['".$kk."']='".$dd."';
";
                                }
                            }
                        }
                        else
                        {
                            $str.="$".$name."['".$kd."']['".$k."']='".$d."';
";
                        }
                    }
                }
                else
                {
                    $str.="$".$name."['".$kd."']='".$d_val."';
";
                }
            }
        }
        else
        {
            $str.="$".$name."='".$data."';
";
        }
        return $str;
    }

    /**
     * @param $inn
     * @param $year
     * @param $date
     * @return mixed
     */
    public function TransfetStatis($inn, $year, $date)
    {
        $str=$this->FilleRes($inn,'inn');
        $str.=$this->FilleRes($year,'year');
        $str.=$this->FilleRes($date,'date');
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/TransfetStatis.txt',$str);

        $userIds = array();
        $organization = $this->getOrganization($inn);

        /*получение данных организации*/
        if (empty($organization))
        {
            $this->result("inn",3,"NotFound");
            $this->error++;
        }
        else
        {
            $this->result("inn",1,"OK");
            $idOrg=$organization['ORGANIZATION_ID']; // id организации
        }

        /*получение всех программ у данной организации за этот год*/
        $programm=$this->getProgram($idOrg,$year,array_column($date,'IdProgram'),array_column($date,'IdSpecialization'));
        if (empty($programm))
        {
            $this->result("AllProgram",3,"NotFound");
            $this->error++;
        }
        else
        {
            while ($row=$programm->fetch())
            {
                global $IdPro,$IdSpec;
                $IdPro=$row['IdProgram'];
                $IdSpec=$row['IdSpecialization'];
                $FinanProduct=$row['IdSpecialization'];
                print_r($row);
                $SearchPlan=array_filter($date,function($a){
                    global $IdPro,$IdSpec;
                    //print_r($a);
                    if (trim($a['IdProgram'])==$IdPro && trim($a['IdSpecialization'])==$IdSpec )
                    {
                        return true;
                    }
                    return false;
                }); // возращение соответствующенго элемента массива
                /*вставка значений в план приёма*/
                foreach ($SearchPlan as $SP)
                {
                    switch ($SP['FinanceProduct'])
                    {
                        case 1:
                            /*проверка на привышение максимального допустимого лимита*/
                            if($row['ADMISSION_PLAN_GRANT_STUDENTS_NUMBER']>=$SP['Submitted'])
                            {
                                $this->setGrantAdmissionPlan($row['ADMISSION_PLAN_ID'],$SP['Submitted']);
                            }
                            else
                            {
                                $this->result('Program:'.$SP['IdProgram'],2,'exceed the limit');
                            }
                            break;
                        case 2:
                            if($row['ADMISSION_PLAN_TUITION_STUDENTS_NUMBER']>=$SP['Submitted'])
                            {
                                $this->setTuitionAdmissionPlan($row['ADMISSION_PLAN_ID'],$SP['Submitted']);
                            }
                            else
                            {
                                $this->result('Program:'.$SP['IdProgram'],2,'exceed the limit');
                            }
                            break;
                    }
                }
            }
        }
        return $this->res;
    }

    /**
     * получение организации по inn
     * @param $inn
     * @return mixed
     * получение организации по inn
     */
    public function getOrganization($inn)
    {
        return OrganizationTable::getList(array(
            'filter' => array(
                "INN"  => $inn
            )
        ))->fetch();
    }

    /**
     * Вывод программ обучения по idprogramm и
     * @param $id_org
     * @param $yaer
     * @param $IdProgram
     * @param $IdSpicel
     * @return mixed
     */
    public function getProgram($id_org,$yaer,$IdProgram,$IdSpicel)
    {
        $next_yaer=$yaer+1;
        return AdmissionPlanTable::getlist(array(
            'filter'    =>  array(
                'ORGANIZATION_SPECIALTY.ORGANIZATION_ID'     =>     $id_org,
                '>=ADMISSION_PLAN_START_DATE'                =>     $date = new \Bitrix\Main\Type\DateTime("01.01.".$yaer." 00:00:00"),
                '<ADMISSION_PLAN_END_DATE'                   =>     $date = new \Bitrix\Main\Type\DateTime("01.01.".$next_yaer." 00:00:00"),
                'IdProgram'                                  =>     $IdProgram,
                'IdSpecialization'                           =>     $IdSpicel,
                ),
            'select'    =>  array(
                'ADMISSION_PLAN_ID',
                'IdProgram'         =>  'ORGANIZATION_SPECIALTY.IDPROGRAM',
                'IdSpecialization'         =>  'ORGANIZATION_SPECIALTY.IDSPECIALIZATION',
                'ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
                'ADMISSION_PLAN_TUITION_STUDENTS_NUMBER'
            )
        ));
    }

    /**
     * Заполение значений контрольной цифре приёма по бюджету
     * @param $id_admin
     * @param $Submitted
     */
    public function setGrantAdmissionPlan($id_admin,$Submitted)
    {
        $res=AdmissionPlanTable::update($id_admin,array(
            'ADMISSION_PLAN_GRANT_GROUPS_NUMBER'    => (int)$Submitted
        ));
        if(!$res->isSuccess())
        {
            $mas=implode(' Error:',$res->getErrorMessages());
            $this->result("AdmissionPlanTable:".$id_admin,4,'Error:'.$mas);
        }
        else
        {
            $this->result("AdmissionPlanTable:".$id_admin,1,'Enter information for Grant in the PCC: '.$Submitted);
        }
        /*запись в лог контрольной цифре приёма*/
        $res=AdmissionPlanEventTable::add(array(
            'ADMISSION_PLAN_ID'             =>  $id_admin,
            'USER_ID'                       =>  213,
            'ADMISSION_PLAN_EVENT_STATUS'   =>  7,
            'ADMISSION_PLAN_EVENT_DATE'     =>  new \Bitrix\Main\Type\DateTime(date("d.m.Y H:i:s")),
            'ADMISSION_PLAN_EVENT_COMMENT'  =>  'Обновлён контрольная цифра приёма по контракту из 1с:Колледж',
        ));
    }

    /**
     * Заполение значений контрольной цифре приёма по коммерции
     * @param $id_admin
     * @param $Submitted
     */
    public function setTuitionAdmissionPlan($id_admin,$Submitted)
    {
        $res=AdmissionPlanTable::update($id_admin,array(
            'ADMISSION_PLAN_TUITION_GROUPS_NUMBER'    => (int)$Submitted
        ));
        if(!$res->isSuccess())
        {
            $mas=implode(' Error:',$res->getErrorMessages());
            $this->result("AdmissionPlanTable:".$g,4,'Error:'.$mas);
        }
        else
        {
            $this->result("AdmissionPlanTable:".$id_admin,1,'Enter information for Tuition in the PCC: '.$Submitted);
        }
        /*запись в лог контрольной цифре приёма*/
        $res=AdmissionPlanEventTable::add(array(
            'ADMISSION_PLAN_ID'             =>  $id_admin,
            'USER_ID'                       =>  213,
            'ADMISSION_PLAN_EVENT_STATUS'   =>  7,
            'ADMISSION_PLAN_EVENT_DATE'     =>  new \Bitrix\Main\Type\DateTime(date("d.m.Y H:i:s")),
            'ADMISSION_PLAN_EVENT_COMMENT'  =>  'Обновлён контрольная цифра приёма по бюджету из 1с:Колледж',
        ));
    }

    public function GetWebServiceDesc()
    {
        $wsdesc = new CWebServiceDesc();
        $wsdesc->wsname = "bitrix.WS.TransfetStatis";
        $wsdesc->wsclassname = "TransfetStatisWS";
        $wsdesc->wsdlauto = true;
        $wsdesc->wsendpoint = CWebService::GetDefaultEndpoint();
        $wsdesc->wstargetns = CWebService::GetDefaultTargetNS();
        $wsdesc->classTypes = array();
        $wsdesc->structTypes = Array();

        $wsdesc->structTypes["arstatistics"] = array
        (
            "IdProgram"         => array("varType" => "string", "strict" => "off"),
            "FinanceProduct"    => array("varType" => "string", "strict" => "off"),
            "IdSpecialization"  => array("varType" => "string", "strict" => "off"),
            "Submitted"         => array("varType" => "string", "strict" => "off"),
        );

        $wsdesc->structTypes["arRes"]= array
        (
            "ID"                => array("varType" => "string", "strict" => "off"),
            "Kod" 				=> array("varType" => "string", "strict" => "off"),
            "Message"		    => array("varType" => "string", "strict" => "off"),
        );

        $wsdesc->classes = array(
            "TransfetStatisWS" => array(
                "TransfetStatis" => array(
                    "type" => "public",
                    "input" => array(
                        "organization" => array("varType" => "string", "strict" => "off"),
                        "year" => array("varType" => "string", "strict" => "off"),
                        "statistics" => array("arrType" => "arstatistics", "varType" => "statistics"),
                    ),
                    "output" => array(
                        "res" => array("arrType" => "arRes", "varType" => "Res"),
                    ),
                    "httpauth" => "Y" 
                ),
            )
        );

        return $wsdesc;
    }
}
$arParams["WEBSERVICE_NAME"] = "bitrix.WS.TransfetStatis";
$arParams["WEBSERVICE_CLASS"] = "TransfetStatisWS";
$arParams["WEBSERVICE_MODULE"] = "";

// передаем в компонент описание веб-сервиса
$APPLICATION->IncludeComponent(
    "bitrix:webservice.server",
    "",
    $arParams
);


die();
?>
