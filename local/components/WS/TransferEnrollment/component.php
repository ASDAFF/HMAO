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
use Spo\Site\Entities\ApplicationTable;
use Spo\Site\Entities\OrganizationSpecialtyTable;
use Spo\Site\Entities\ApplicationEventTable;
use Spo\Site\Entities\AdmissionPlanEventTable;
use Spo\Site\Entities\OrganizationTable;
use Spo\Site\Entities\EnrollmentTable;
use Spo\Site\Entities\AbiturientProfileTable;
use Bitrix\Main\Type;
use Spo\Site\Helpers\DateFormatHelper;
use Spo\Site\Dictionaries\BaseEducation;

//TODO: Перевести общие функции веб сервиса в отделльный класс
class TransfetEnrollmentWS extends IWebService
{
    private $res; // ответ на запрос
    private $i=0; // счёчик ответа
    private $error; // фатальние ошибки
    private $date_exam;

    /**
     * @param $id
     * @param $type
     * @param $mas
     */
    function result($id,$type,$mas)
    {
        $i=$this->i;
        global $res;
        $this->res[$i.":arRes"]["ID"]=strval($id);
        $this->res[$i.":arRes"]["Kod"]=strval($type);
        $this->res[$i.":arRes"]["Message"]=strval($mas);
        $this->i++;
    }

    function log($res,$table)
    {
        if(!$res->isSuccess())
        {
            $mas=implode(' Error:',$res->getErrorMessages());
            $this->result("Error ".$table,4,'Error:'.$mas);
        }
        else
        {
            $this->result($table,1,'Upadate record'. $res->getId());
        }
    }


    /**
     * @param $inn
     * @param $year
     * @param $date
     * @return mixed
     */
    public function TransferEnrollment($inn, $year, $date)
    {
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
        $res=EnrollmentTable::deleteEnroll($idOrg); // удаление значений с рекомендованых по зачеслению по определённой организации
        /*получение года*/
        if ((int)$year<2016 || (int)$year>2099)
        {
            $year=date("Y");
        }

        /*получение списка образовательных программ*/
        if (empty($date))
        {
            $this->result("Rating",3,"empty Rating");
            $this->error++;
        }
        else
        {
            $id_programm = array_column($date,'IdProgram'); // получение списка id Programm
            $id_Specializ = array_column($date,'IdSpecialization'); // Получение списка id Specializ
            $TargetDirection = array_column($date,'FinanceProduct'); // вид финансирования

            $year2=$year+1;
            $ListApps=OrganizationSpecialtyTable::getlist(array(
                'filter'    =>  array(
                    'ORGANIZATION_ID'                                           =>  $organization['ORGANIZATION_ID'], // организация
                    'IDPROGRAM'                                                 =>  $id_programm,
                    'IDSPECIALIZATION'                                          =>  $id_Specializ,
              //      'TARGETDIRECTION'                                           =>  $TargetDirection,
                    //'APPLICATION.APPLICATION_FUNDING_TYPE'                      =>  $kod_final,
                    '>=ADMISSION_PLAN.ADMISSION_PLAN_START_DATE'                =>  new \Bitrix\Main\Type\DateTime("01.01.".$year." 00:00:00"),
                    '<=ADMISSION_PLAN.ADMISSION_PLAN_END_DATE'                  =>  new \Bitrix\Main\Type\DateTime("01.01.".$year2." 00:00:00"),
                ),
                'select'    =>  array(
                    'IdOrgSpec'     =>  'ORGANIZATION_SPECIALTY_ID',
                    'IdProgramm'    =>  'IDPROGRAM',
                    'IdSpecializ'   =>  'IDSPECIALIZATION',
                    'IdApp'         =>  'APPLICATION.APPLICATION_ID',
                    'IdUser'        =>  'APPLICATION.USER_ID',
                    'IdProfel'      =>  'APPLICATION.ABITURIENT.SPO_ABITURIENT_PROFILE_ID',
                    'FinanProd'     =>  'APPLICATION.APPLICATION_FUNDING_TYPE',
                    'AppStatus'     =>  'APPLICATION.APPLICATION_STATUS',
                    'AdmisID'       =>  'ADMISSION_PLAN.ADMISSION_PLAN_ID',
                    'Apps'          =>  'APPLICATION.*',
                    'IdOrga'        =>  'ORGANIZATION_ID',
                )
            ));/*получение списка заявление по полученным данных*/

            $Structured=array();
            while ($ListApp=$ListApps->fetch()) /*преобразование массива в структурированный массив*/
            {

                $Structured[$ListApp['IdOrgSpec']]['IdProgram']=$ListApp['IdProgramm'];
                $Structured[$ListApp['IdOrgSpec']]['IdSpecializ']=$ListApp['IdSpecializ'];
                $Structured[$ListApp['IdOrgSpec']]['IdOrga']=$ListApp['IdOrga'];
                $Structured[$ListApp['IdOrgSpec']]['AdmisID']=$ListApp['AdmisID'];
                $Structured[$ListApp['IdOrgSpec']]['FinanProd']=$ListApp['FinanProd'];
                if (!empty($ListApp['IdApp']))
                {
                    $Structured[$ListApp['IdOrgSpec']]['App'][$ListApp['IdUser']]['IdApp']=$ListApp['IdApp'];
                    $Structured[$ListApp['IdOrgSpec']]['App'][$ListApp['IdUser']]['IdUser']=$ListApp['IdUser'];
                    $Structured[$ListApp['IdOrgSpec']]['App'][$ListApp['IdUser']]['FinanProd']=$ListApp['FinanProd'];
                    $Structured[$ListApp['IdOrgSpec']]['App'][$ListApp['IdUser']]['Status']=$ListApp['AppStatus'];
                    $Structured[$ListApp['IdOrgSpec']]['App'][$ListApp['IdUser']]['IdProfel']=$ListApp['IdProfel'];
                    $Structured[$ListApp['IdOrgSpec']]['App'][$ListApp['IdUser']]['AdmisID']=$ListApp['AdmisID'];
                    $Structured[$ListApp['IdOrgSpec']]['App'][$ListApp['IdUser']]['IdOrga']=$ListApp['IdOrga'];
                    $Structured[$ListApp['IdOrgSpec']]['App'][$ListApp['IdUser']]['IdOrgSpec']=$ListApp['IdOrgSpec'];
                    $Structured[$ListApp['IdOrgSpec']]['App'][$ListApp['IdUser']]['IdOrga']=$ListApp['IdOrga'];
                }
            }

            /*сравнение между найдеными и запрошенными данными. Дописание абитуриентов*/
            foreach ($Structured as $key=>&$item)
            {
                global $IdProgram,$IdSpecializ,$FinanProd;
                $IdProgram=$item['IdProgram'];
                $IdSpecializ=$item['IdSpecializ'];
                $FinanProd=$item['FinanProd'];
                $SearchApp=array();
                $SearchApp=array_filter($date,function($a){
                    global $IdProgram,$IdSpecializ,$FinanProd;
                    if (empty($IdSpecializ))
                    {
                        if ((trim($a["IdProgram"])==trim($IdProgram)) && trim($a["FinanceProduct"])==trim($FinanProd))
                        {
                            return true;
                        }
                    }
                    else
                    {
                        if (trim($a["IdProgram"])==trim($IdProgram) && $a['IdSpecialization']==trim($IdSpecializ) && (trim($a["FinanProd"])==trim($FinanProd)))
                        {
                            return true;
                        }
                    }
                    return false;
                }); // возращение соответствующенго элемента массива
                /*Записывание элементов к масcиву*/
                if (count($SearchApp)>1)
                {
                    $this->result("IdProgram:".$IdProgram." IdSpecializ:".$IdSpecializ,3," It has several meanings");
                    continue;
                }
                elseif (empty($SearchApp))
                {
                    $this->result("IdProgram:".$IdProgram." IdSpecializ:".$IdSpecializ,3,"Empty ");
                    continue;
                }
                else
                {
                    $temp=array_pop($SearchApp);

                    $Apps=$temp['Enrollment'];
                    $Apps=array_merge($Apps,array('FinanceProduct'=>$temp['FinanceProduct']));

                    //print_r(array_pop($SearchApp));

                    if (!is_array($Apps))
                    {
                        $this->result("IdProgram:".$IdProgram." IdSpecializ:".$IdSpecializ,3,"Empty Enrollment");
                        continue;
                    }
                    else
                    {

                        foreach ($Apps as $App)
                        {
                            /*проверка на то что id user будет соответствовать значением в системе*/

                            $App=array_merge($App,array('AdmisID'=>$item['AdmisID'],'IdOrga'=>$item['IdOrga'],'FinanProd'=>$Apps['FinanceProduct']));

                            if(empty($App['IdAbiturient']))
                            {
                                    $item['New_App'][]=$App;
                            }
                            else
                            {
                                if(is_array($item['App'][$App['IdAbiturient']]))
                                {
                                    $item['App'][$App['IdAbiturient']]['Copy']=$App['Copy'];
                                    $item['App'][$App['IdAbiturient']]['Recommended']=$App['Recommended'];
                                    $item['App'][$App['IdAbiturient']]['Koment']=$App['Koment'];
                                    $staus_orig=($App['Copy']==0 || $App['Copy']=='Нет') ? $organization['ORGANIZATION_ID'] : 0;

                                    $this->setStatusAbiturient($item['App'][$App['IdAbiturient']],$App); // смена статуса у заявления
                                    $res=AbiturientProfileTable::update($item['App'][$App['IdAbiturient']]['IdProfel'],array('DOCORIGIN'   =>  $staus_orig)); //установкa статуса оригинала
                                    $this->log($res,'AbiturientProfileTable');
                                    $this->result("IdProgram:".$IdProgram." IdSpecializ:".$IdSpecializ." IdAbiturient:".$App['IdAbiturient'],3,"Abiturient NotFount");//
                                    $item['New_App'][]=$App;
                                }
                                else
                                {
                                    $this->result("IdProgram:".$IdProgram." IdSpecializ:".$IdSpecializ." IdAbiturient:".$App['IdAbiturient'],3,"Abiturient NotFount");
                                    $item['New_App'][]=$App;
                                }
                            }

                        }

                        $this->setEnrollment($organization['ORGANIZATION_ID'],$item['New_App']);
                    }

                }

            }
            pritn_r($Structured);
            die;
            /*запись данных в таблицу $organization['ORGANIZATION_ID']*/

        }
        return $this->res;
    }

    function getMail($IdOrgSpec,$IdOrg,$FinanceProduct)
    {
        /*данные для письма*/
        /*полученние данных пользователя */
        $SPECIALTYS=ApplicationTable::getList(array(
            'filter'	=>	array(
                'ORGANIZATION_SPECIALTY_ID'	=> $IdOrgSpec,
                'fundingType'               => $FinanceProduct
            ),
            'select'	=>	array(
                'Code'			    =>	'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'NameSpec'		    =>	'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'CodeCiliza'	    =>	'ORGANIZATION_SPECIALTY.IDSPECIALIZATION',
                'NameCiliza'	    =>	'ORGANIZATION_SPECIALTY.NAMESPECIALIZATION',
                'BaseEducation'	    =>	'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'Period'		    =>	'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                'fundingType'       =>  'APPLICATION_FUNDING_TYPE',
                'ABITURIENT_EMAIL'  =>  'ABITURIENT.Spo\Site\Entities\UserValidDataTable:ABITURIENT_PROFILE.USER_VALID_DATA_EMAIL',
                'ABITURIENT_FIO'    =>  'ABITURIENT.FIO'
            )
        ))->fetchAll();

        $APPLICATION_SPECIALTY='';
        foreach ($SPECIALTYS as $SPECIALTY)
        {
            if($SPECIALTY['fundingType']==1)
            {
                $final='Контрактная форма обучения';
            }
            else
            {
                $final='Бюджетная форма обучения';
            }
            $fio=$SPECIALTY['ABITURIENT_FIO'];
            $email=$SPECIALTY['ABITURIENT_EMAIL'];
            $Ciliza='';
            if(!empty($SPECIALTY['CodeCiliza'])) $Ciliza.=$SPECIALTY['CodeCiliza'].' '.$SPECIALTY['NameCiliza'].', ';
            $APPLICATION_SPECIALTY.=$SPECIALTY['Code'].' '.$SPECIALTY['NameSpec'].', '.$Ciliza.BaseEducation::getshortValues($SPECIALTY['BaseEducation']).', '.mb_strtolower($final).', '.DateFormatHelper::months2YearsMonths($SPECIALTY['Period']).' ';
        }
        /*получение данных организации*/
        $arInfoOrgan=OrganizationTable::getlist(array(
            'filter'	=>	array(
                'ORGANIZATION_ID'	=>	$IdOrg,
            ),
            'select'	=>	array(
                'ORGANIZATION_NAME',
                'ORGANIZATION_INFO_LINK'	=>	'ORGANIZATION_SITE'
            )
        ))->fetch();
        return array(
            'APPLICATION_SPECIALTY'		=>		$APPLICATION_SPECIALTY,
            'ABITURIENT_EMAIL'			=>		$email,
            'ABITURIENT_FIO'			=>		$fio,
            'ORGANIZATION_NAME'			=>		$arInfoOrgan['ORGANIZATION_NAME'],
            'ORGANIZATION_INFO_LINK'	=>		$arInfoOrgan['ORGANIZATION_INFO_LINK'],
        );

    }

    /**
     * функция изминение статуса заявление
     * @param $item - заявления
     * @param $App - данные из soap запроса
     * @return bool - true-заявление изминено иначе false
     */
    public function setStatusAbiturient($item,$App)
    {
        global $USER;

        /*проверка на то что заявление было ранее удаленно пользователем*/
        if ($item['Status']==4)
        {
            $this->result("IdAbiturient: ".$App['IdAbiturient'],3,"Application to remotely");
            return false;
        }
        $status_new=($App['Recommended']==1 || $App['Recommended']=='Да') ? 9 : 3;


        /*проверка статуса у заявления на соответствие рекомендации*/
        if($item['Status']!==$status_new)
        {
            $status=$status_new;
            /*изминение статуса заявления*/
            $res=ApplicationTable::update(
                $item['IdApp'],array(
                    'APPLICATION_STATUS'    => $status
                )
            );
            if(!$res->isSuccess())
            {
                $mas=implode(' Error:',$res->getErrorMessages());
                $this->result("ApplicationTable: ID:".$item['IdApp'],4,'Error:'.$mas);
                return false;
            }
            else
            {
                $this->result("ApplicationTable: ID:".$item['IdApp'],1,'Upadate record');
                /*отправка почтового сообщения*/
                $res_mail=$this->getMail($item['IdOrgSpec'],$item['IdOrga'],$App['FinanProd']);
                if($status==3 && $item['Status']!=9)
                {
                    CEvent::SendImmediate("APPLICATION_ACCEPTED", 's1', $res_mail,'Y',22);
                }
                elseif($status==3 && $item['Status']==9)
                {
                    CEvent::SendImmediate("APPLICATION_ACCEPTED", 's1', $res_mail,'Y',23);
                }
                if($status==9)
                {
                    CEvent::SendImmediate("APPLICATION_ACCEPTED", 's1', $res_mail,'Y',21);
                }

            }
            /*Запись в лог действия заявлений*/
            $ResEvent=ApplicationEventTable::add(array(
                    'APPLICATION_ID'                =>      $item['IdApp'],
                    'USER_ID'                       =>      231,//,
                    'APPLICATION_EVENT_DATE'        =>      new \Bitrix\Main\Type\DateTime(date("d.m.Y H:i:s")),
                    'APPLICATION_EVENT_STATUS'      =>      9,
                    'APPLICATION_EVENT_REASON'      =>      1,
                    'APPLICATION_EVENT_COMMENT'     =>      $App['Koment'],
                )
            );
            $this->log($res,'ApplicationEventTable');

        }
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

    public function setEnrollment($org,$dates)
    {


        foreach ($dates as $date)
        {
            if ($date)
            {
                if($date['Recommended']==1 || trim($date['Recommended'])=='Да')
                    $Recommended=1;
                else
                    $Recommended=0;
                if($date['Copy']==1 || trim($date['Copy'])=='Да')
                    $Copy=1;
                else
                    $Copy=0;
                print_r(array(
                    'ADMISSION_PLAN_ID'     =>  $date['AdmisID'],
                    'ORGANIZATION_ID'       =>  $org,
                    'ENROLLMENT_FINANCE'    =>  $date['FinanProd'],
                    'ENROLLMENT_FIO'        =>  $date['FIO'],
                    'ENROLLMENT_COPY'       =>  $Copy,
                    'ENROLLMENT_BALL'       =>  floatval(str_replace(',','.',$date['Ball'])),
                    'ENROLLMENT_PRIORY'     =>  $date['Priority'],
                    'ENROLLMENT'            =>  $Recommended,
                ));
                $resEnll=EnrollmentTable::add(array(
                    'ADMISSION_PLAN_ID'     =>  $date['AdmisID'],
                    'ORGANIZATION_ID'       =>  $org,
                    'ENROLLMENT_FINANCE'    =>  $date['FinanProd'],
                    'ENROLLMENT_FIO'        =>  $date['FIO'],
                    'ENROLLMENT_COPY'       =>  $Copy,
                    'ENROLLMENT_BALL'       =>  floatval(str_replace(',','.',$date['Ball'])),
                    'ENROLLMENT_PRIORY'     =>  $date['Priority'],
                    'ENROLLMENT'            =>  $Recommended,
                ));
                $this->log($resEnll,'EnrollmentTable');
            }
        }
    }


    public function GetWebServiceDesc()
    {
        $wsdesc = new CWebServiceDesc();
        $wsdesc->wsname = "bitrix.WS.TransfetEnrollment";
        $wsdesc->wsclassname = "TransfetEnrollmentWS";
        $wsdesc->wsdlauto = true;
        $wsdesc->wsendpoint = CWebService::GetDefaultEndpoint();
        $wsdesc->wstargetns = CWebService::GetDefaultTargetNS();
        $wsdesc->classTypes = array();
        $wsdesc->structTypes = Array();

        $wsdesc->structTypes["arRating"] = array
        (
            "IdProgram"         => array("varType" => "string", "strict" => "off"),
            "FinanceProduct"    => array("varType" => "string", "strict" => "off"),
            "IdSpecialization"  => array("varType" => "string", "strict" => "off"),
            "Enrollment"        => array("arrType" => "arEnrollment", "varType" => "Enrollment"),
        );

        $wsdesc->structTypes["arRes"]= array
        (
            "ID"                => array("varType" => "string", "strict" => "off"),
            "Kod" 				=> array("varType" => "string", "strict" => "off"),
            "Message"		    => array("varType" => "string", "strict" => "off"),
        );

        $wsdesc->structTypes["arEnrollment"]= array
        (
            "IdAbiturient"      => array("varType" => "string", "strict" => "off"),
            "FIO" 				=> array("varType" => "string", "strict" => "off"),
            "Ball" 				=> array("varType" => "string", "strict" => "off"),
            "Priority"			=> array("varType" => "string", "strict" => "off"),
            "Copy" 				=> array("varType" => "string", "strict" => "off"),
            "Koment"		    => array("varType" => "string", "strict" => "off"),
            "Recommended"	    => array("varType" => "string", "strict" => "off"),
        );

        $wsdesc->classes = array(
            "TransfetEnrollmentWS" => array(
                "TransferEnrollment" => array(
                    "type" => "public",
                    "input" => array(
                        "organization"  => array("varType" => "string", "strict" => "off"),
                        "year"          => array("varType" => "string", "strict" => "off"),
                        "Rating"        => array("arrType" => "arRating", "varType" => "Rating"),
                    ),
                    "output" => array(
                        "res"           => array("arrType" => "arRes", "varType" => "Res"),
                    ),
                    "httpauth" => "Y"
                ),
            )
        );

        return $wsdesc;
    }
}
$arParams["WEBSERVICE_NAME"] = "bitrix.WS.TransfetEnrollment";
$arParams["WEBSERVICE_CLASS"] = "TransfetEnrollmentWS";
$arParams["WEBSERVICE_MODULE"] = "";

// передаем в компонент описание веб-сервиса
$APPLICATION->IncludeComponent(
    "bitrix:webservice.server",
    "",
    $arParams
);


die();
?>
