<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule("webservice") || !CModule::IncludeModule("iblock"))
    return;
Bitrix\Main\Loader::includeModule('spo.site');
use Spo\Site\Entities\OrganizationTable;
use Spo\Site\Entities\ApplicationTable;
use Spo\Site\Entities\AbiturientExamTable;
use Spo\Site\Entities\OrganizationSpecialtyTable;
use Spo\Site\Entities\AbiturientProfileTable;
use Bitrix\Main\Type;
use Spo\Site\Entities\OrganizationSpecialtyAdaptationTable;
use Spo\Site\Helpers\DateFormatHelper;
use Spo\Site\Dictionaries\BaseEducation;


// наш новый класс наследуется от базового IWebService
class TransferTestWS extends IWebService
{
    private $res; // ответ на запрос
    private $i=0; // счёчик ответа
    private $error; // фатальние ошибки
    private $date_exam;
    /**
     * функция отработки веб сервиса
     * @param $inn
     * @param $year
     * @param $EntranceTests
     * @return mixed
     */
    function TransferTestData($inn,$year,$EntranceTests)
    {
        /*посик id организхации*/
        $IdOrg=$this->GetOrg($inn);
        if(!$IdOrg)
        {
            return $this->res;
        }
        /*год*/
        if((int) $year ==0)
        {
            $year=date('Y');
        }
        /*проверка данных по экзаменнам получение */
        $this->GetTest($EntranceTests,$IdOrg,$year);
        return $this->res;
    }

    /**
     * функция сообщений
     * @param $id
     * @param $type
     * @param $mas
     *
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

    /**
     * функция поиска id организауции
     * @param $inn
     * @return bool or int
     */
    function GetOrg($inn)
    {
        $IdOrg=false;
        if (empty($inn))// ИНН пустой
        {
            $this->result("inn",2,"Empty");
            $this->error++;
        }
        elseif (strlen($inn)!=10 && strlen($inn)!=12)//проверка на соответствие типа ИНН длина 10 или 12
        {
            $this->result("inn",2,"Length not format");
            $this->error++;
        }
        else//поиск ИНН в базе
        {
            $org = OrganizationTable::getList(array(
                'select' => array('*'),
                'filter' => array('=INN' => $inn)
            ))->fetchAll();
            if(count($org)==1)
            {
                $this->result("inn",1,"OK");
                $IdOrg=$org[0]["ORGANIZATION_ID"]; // номер организации
            }
            else
            {
                $this->result("inn",3,"NotFound");
                $this->error++;
            }
        }
        return $IdOrg;
    }

    /**
     * @param $IdOrgSpec
     * @param $IdOrg
     * @return array
     */
    function GetMail($IdOrgSpec,$IdOrg)
    {
        /*данные для письма*/
        /*полученние данных пользователя */
        $SPECIALTYS=ApplicationTable::getList(array(
            'filter'	=>	array(
                'ORGANIZATION_SPECIALTY_ID'	=> $IdOrgSpec
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

    function GetTest($EntranceTests,$IdOrg,$year)
    {
        $res=array();
        /*print_r($EntranceTests);*/
        $dateSS=new \Bitrix\Main\Type\DateTime("01.01.".$year." 00:00:00");
        $dateFF=new \Bitrix\Main\Type\DateTime("01.01.".++$year." 00:00:00");

        $App=ApplicationTable::getlist(array(
             'filter' => array(
                 '=ORGANIZATION_ID'                                                                   =>  $IdOrg,
                 '>=APPLICATION_CREATION_DATE'                                                        =>  $dateSS,
                 '<=APPLICATION_CREATION_DATE'                                                        =>  $dateFF,
                 '>ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_EXAM.ORGANIZATION_SPECIALTY_EXAM_ID' =>  0,
                 'USER_ID'                                                                            =>  array_column($EntranceTests,'IdAbiturient'),
                 ),
             'select' => array(
                 'IdUser'       =>  'USER_ID',
                 'IdOrgSpec'    =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_EXAM.ORGANIZATION_SPECIALTY_ID',
                 'ID'           =>  'APPLICATION_ID',
                 'Name'         =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_EXAM.ORGANIZATION_SPECIALTY_EXAM_DISCIPLINE',
                 'From'         =>  'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_EXAM.ORGANIZATION_SPECIALTY_EXAM_TYPE',
                 'IdProgram'    =>  'ORGANIZATION_SPECIALTY.IDPROGRAM'

             //    'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_EXAM.*'
             )
            ))->fetchAll();

        if (!$App)
        {
            $this->result('EntranceTests',3,' NotFount');
            return $res;
        }
        $AbitExm=AbiturientExamTable::getlist(array(
            'filter'    => array(
                "ID_ORGANIZATION"   =>  $IdOrg,
                '>=DATE'            =>  $dateSS,
                '<=DATE'            =>  $dateFF,
            ),
            'select'    => array('*')
        ))->fetchAll();
        /*получение списка экзаменов на */
        $g=0;
        $parm=array();
        foreach ($EntranceTests as $key=>$ET)
        {
            $g++;
            $SearchApp=array();
            $SearchAbitExm=array();
            $this->date_exam='';
            /*перевод в нижний реестр и обрезание левых символов*/
            global $name,$from,$IdUser;
            $name=mb_strtolower(trim($ET['Test']));
            $from=mb_strtolower(trim($ET['Form']));
            $IdUser=$ET['IdAbiturient'];
            $SearchApp=array_filter($App,function($a){
                global $name,$from,$IdUser;
                /*print_r($name);
                print_r(mb_strtolower(trim($a['Name'])));*/
                //print_r($from);
                if (mb_strtolower(trim($a['Name']))==$name && mb_strtolower(trim($a['From']))==$from && $a['IdUser']==$IdUser)
                {
                    return true;
                }
                return false;
            }); // возращение соответствующенго элемента массива
            //print_r($SearchApp);

            if (empty($SearchApp))
            {
                $this->result('EntranceTests:'.$g,3,'IdAbiturient: '.$IdUser.' Exam NotFound');
                continue;
            }
            else
            {

                $IdOrgSpec=implode(';',array_column($SearchApp,"IdOrgSpec")); // id_organization_specialty
                $IdProgram=implode(';',array_column($SearchApp,"IdProgram")); // IdProgram
                $this->result('EntranceTests:'.$g,3,'IdAbiturient: '.$IdUser.' Exam Found IdProgram:'.$IdProgram);
                $date_exam = date('d.m.Y', strtotime($ET['DateTest']));
                $this->date_exam=$date_exam;
                $ball=$ET['Ball'];
                if ($ET['Ball']=='Да')
                {
                    $ball=1;
                }
                elseif ($ET['Ball']=='Нет')
                {
                    $ball=0;
                }
                $parm=array(
                    'ID_ABITURIENT'             => $IdUser,
                    'ID_ORGANIZATION'           => $IdOrg,
                    'ID_ORGANIZATION_SPECIALTY' => $IdOrgSpec,
                    'TEST'                      => $name,
                    'FROM_EXEM'                 => $from,
                    'BALL'                      => $ball,
                    'APPEAR'                    => $ET['Appear'],
                    'DATE'                      => new \Bitrix\Main\Type\Date($date_exam),
                );

                /*перевод заявления в статус "Отклонено"*/
                if($ET['Appear']==1 || $ball==0)
                {
                    /*полученние данных пользователя */
                    if ($ET['Appear']==1)
                    {
                        /*отправка почтового события Уведомление № 5*/
                        $arEventFields=$this->GetMail($IdOrgSpec,$IdOrg);
                        CEvent::SendImmediate("ABSENTEEISM", 's1', $arEventFields);
                    }
                    else if($ball==0)
                    {
                        /*отправка почтового события Уведомление № 6.1*/
                        $arEventFields=$this->GetMail($IdOrgSpec,$IdOrg);
                        CEvent::SendImmediate("TEST", 's1', $arEventFields,'Y',19);
                    }

                    $arIdApp=array_column($SearchApp,"ID");
                    /*смена статуса*/
                    foreach ($arIdApp as $ad)
                    {
                        $resApT=ApplicationTable::update($ad,array(
                            'APPLICATION_STATUS'    =>  2
                        ));
                        if (!$resApT->isSuccess())
                        {
                            $mas=implode(' Error:',$res->getErrorMessages());
                            $this->result("ApplicationTable:".$g,4,'Error:'.$mas);
                        }
                        else
                        {
                            $ID=$res->getId();
                            $this->result("ApplicationTable:".$g,1,"ApplicationTable ID:".$ID." transferred status DECLINED");
                        }
                    }
                }
                else
                {
                    /*отправка почтового события Уведомление № 6*/
                    $arEventFields=$this->GetMail($IdOrgSpec,$IdOrg);
                    CEvent::SendImmediate("TEST", 's1', $arEventFields,'Y',20);
                }

                /*поиск рание введеных данных в базу*/
                if(!empty($AbitExm))
                {
                    $SearchAbitExm=array_filter($AbitExm,function($a){
                        global $name,$from,$IdUser;
                        if (mb_strtolower(trim($a['TEST']))==$name && mb_strtolower(trim($a['FROM_EXEM']))==$from && $a['ID_ABITURIENT']==$IdUser && $a['DATE']==$this->date_exam)
                        {
                            return true;
                        }
                        return false;
                    }); // возращение соответствующенго элемента массива
                    if (!empty($SearchAbitExm))
                    {
                        $ID_ABITURIENT_EXAM=array_pop($SearchAbitExm)['ID_ABITURIENT_EXAM'];
                        $res=AbiturientExamTable::update($ID_ABITURIENT_EXAM,$parm);
                        if (!$res->isSuccess())
                        {
                            $mas=implode(' Error:',$res->getErrorMessages());
                            $this->result("AbiturientExamTable:".$g,4,'Error:'.$mas);
                            continue;
                        }
                        else
                        {
                            $ID=$res->getId();
                            $this->result("AbiturientExamTable:".$g,1,"UpdateRecord:".$ID);
                            continue;
                        }
                    }
                }
                $res=AbiturientExamTable::add($parm);
                if (!$res->isSuccess())
                {
                    $mas=implode(' Error:',$res->getErrorMessages());
                    $this->result("AbiturientExamTable:".$g,4,'Error:'.$mas);
                }
                else
                {
                    $ID=$res->getId();
                    $this->result("AdmissionEl:".$g,1,"NewAbiturientExamTable:".$ID);
                }
            }
        }

    }
    /**
     * метод GetWebServiceDesc возвращает описание сервиса и его методов
     * @return CWebServiceDesc
     */
    function GetWebServiceDesc()
    {
        $wsdesc = new CWebServiceDesc();
        $wsdesc->wsname = "bitrix.WS.TransferTest";
        $wsdesc->wsclassname = "TransferTestWS";
        $wsdesc->wsdlauto = true;
        $wsdesc->wsendpoint = CWebService::GetDefaultEndpoint();
        $wsdesc->wstargetns = CWebService::GetDefaultTargetNS();
        $wsdesc->classTypes = array();
        $wsdesc->structTypes = Array();
        $wsdesc->structTypes["arEntranceTests"]= array
        (
            "IdAbiturient"          => array("varType" => "string", "strict" => "off"),
            "Test"                  => array("varType" => "string", "strict" => "off"),
            "Ball"                  => array("varType" => "string", "strict" => "off"),
            "Appear"                => array("varType" => "string", "strict" => "off"),
            "DateTest"              => array("varType" => "string", "strict" => "off"),
            "Form"                  => array("varType" => "string", "strict" => "off"),
        );
        $wsdesc->structTypes["arRes"]= array
        (
            "ID"                => array("varType" => "string", "strict" => "off"),
            "Kod" 				=> array("varType" => "string", "strict" => "off"),
            "Message"		    => array("varType" => "string", "strict" => "off"),
        );

        $wsdesc->classes = array(
            "TransferTestWS"=> array(
                "TransferTestData" => array(
                    "type"		=> "public",
                    "input"		=> array(
                        "organization"      => array("varType" => "string", "strict" => "off"),
                        "year"              => array("varType" => "string", "strict" => "off"),
                        "EntranceTests"     => array("arrType" => "arEntranceTests","varType" => "EntranceTests"),
                    ),
                    "output"	=> array(
                        "Res"               => array("arrType" => "arRes","varType" => "Res")
                    ),
                    "httpauth" => "Y"
                ),
            )
        );

        return $wsdesc;
    }
}

$arParams["WEBSERVICE_NAME"] = "bitrix.WS.TransferTest";
$arParams["WEBSERVICE_CLASS"] = "TransferTestWS";
$arParams["WEBSERVICE_MODULE"] = "";

// передаем в компонент описание веб-сервиса
$APPLICATION->IncludeComponent(
    "bitrix:webservice.server",
    "",
    $arParams
);


die();
?>
