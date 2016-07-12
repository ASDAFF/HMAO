<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("webservice") || !CModule::IncludeModule("iblock"))
    return;
Bitrix\Main\Loader::includeModule('spo.site');
use Spo\Site\Entities\OrganizationTable;
use Spo\Site\Entities\AdmissionPlanTable;
use Spo\Site\Entities\AdmissionPlanEventTable;
use Spo\Site\Entities\OrganizationSpecialtyTable;
use Spo\Site\Entities\SpecialtyTable;
use Spo\Site\Entities\OrganizationSpecialtyExamTable;
use Bitrix\Main\Type;
use Spo\Site\Entities\OrganizationSpecialtyAdaptationTable;


$i=0;//счёчик ответа
$res=array();
$re=array();
$roul=0;
/*запись данных в файл*/
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

/*функция удаления дублей в массиве key_or */
function KillDubll($key_or)
{
    // var_dump($key_or);
    $res_or=array();

    foreach ($key_or as $k=>&$kr)
    {
        $key = array_search($kr['ID'], array_column($key_or, 'ID'));
        $temp['ID']=$kr['ID'];
        $temp['SPECIALIZATION']=$kr['SPECIALIZATION'];
        $temp['CODE']=$kr['CODE'];
        //var_dump($key);
        if (($key!==false || $k==0) && !isset($res_or[$key]))
        {
            $temp['ADMISSION_PLAN_GRANT_GROUPS_NUMBER']=0;
            $temp['ADMISSION_PLAN_TUITION_STUDENTS_NUMBER']=0;
        }
        if($kr['FinanceProduct']==1)
        {
            $temp['ADMISSION_PLAN_GRANT_GROUPS_NUMBER']=$kr['Plan'];
        }
        if($kr['FinanceProduct']==2)
        {
            $temp['ADMISSION_PLAN_TUITION_STUDENTS_NUMBER']=$kr['Plan'];
        }

        if ($key===false)
        {
            $res_or[]=$temp;
        }
        else
        {
            $res_or[$key]=$temp;
            //unset($kr);
        }
        // var_dump($res_or);
        //$res_or[]
    }
    sort($res_or);
    return $res_or;
}

// наш новый класс наследуется от базового IWebService
class TransferPlanWS extends IWebService
{
    // функция вывода реестра задач
    function TransferPlan($inn,$year,$DateStart,$DateFinish,$program,$EntranceTests)
    {

        /*$str=FilleRes($inn,'inn');
        $str.=FilleRes($year,'year');
        $str.=FilleRes($DateStart,'DateStart');
        $str.=FilleRes($DateFinish,'DateFinish');
        $str.=FilleRes($program,'program');
        $str.=FilleRes($EntranceTests,'EntranceTests');
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/TransferPlan.txt',$str);
        $inn='1111111111';
        $year='2016';
        $DateStart='2016-01-01T00:00:00';
        $DateFinish='2016-08-30T00:00:00';
        $program['0']['IdProgram']='000000002';
        $program['0']['IdSpecialty']='09.02.02  ';
        $program['0']['FormOfStudy']='1';
        $program['0']['BaseReclamation']='2';
        $program['0']['LevelOfTraining']='1';
        $program['0']['FinanceProduct']='2';
        $program['0']['IdSpecialization']='';
        $program['0']['NameSpecialization']='';
        $program['0']['TargetDirection']='0';
        $program['0']['Plan']='40';
        $program['0']['Month']='34';
        $program['0']['TypeOfProgram']='1';
        $program['0']['AdaptationProgram']='';
        $program['1']['IdProgram']='000000004';
        $program['1']['IdSpecialty']='09.02.04  ';
        $program['1']['FormOfStudy']='1';
        $program['1']['BaseReclamation']='1';
        $program['1']['LevelOfTraining']='1';
        $program['1']['FinanceProduct']='2';
        $program['1']['IdSpecialization']='';
        $program['1']['NameSpecialization']='';
        $program['1']['TargetDirection']='0';
        $program['1']['Plan']='15';
        $program['1']['Month']='46';
        $program['1']['TypeOfProgram']='1';
        $program['1']['AdaptationProgram']='';
        $program['2']['IdProgram']='000000004';
        $program['2']['IdSpecialty']='09.02.04  ';
        $program['2']['FormOfStudy']='1';
        $program['2']['BaseReclamation']='1';
        $program['2']['LevelOfTraining']='1';
        $program['2']['FinanceProduct']='1';
        $program['2']['IdSpecialization']='000003';
        $program['2']['NameSpecialization']='Информационные системы в банковской сфере';
        $program['2']['TargetDirection']='0';
        $program['2']['Plan']='5';
        $program['2']['Month']='46';
        $program['2']['TypeOfProgram']='1';
        $program['2']['AdaptationProgram']='';
        $program['3']['IdProgram']='000000005';
        $program['3']['IdSpecialty']='09.02.05  ';
        $program['3']['FormOfStudy']='1';
        $program['3']['BaseReclamation']='2';
        $program['3']['LevelOfTraining']='1';
        $program['3']['FinanceProduct']='1';
        $program['3']['IdSpecialization']='';
        $program['3']['NameSpecialization']='';
        $program['3']['TargetDirection']='0';
        $program['3']['Plan']='25';
        $program['3']['Month']='34';
        $program['3']['TypeOfProgram']='1';
        $program['3']['AdaptationProgram']='';
        $EntranceTests['0']['IdSpecialty']='09.02.02  ';
        $EntranceTests['0']['Tests']['0']['Test']='Математика';
        $EntranceTests['0']['Tests']['0']['Form']='письменно';
        $EntranceTests['0']['Tests']['0']['IdSpecialization']='';
        $EntranceTests['0']['Tests']['1']['Test']='Русский язык';
        $EntranceTests['0']['Tests']['1']['Form']='диктант';
        $EntranceTests['0']['Tests']['1']['IdSpecialization']='';
        $EntranceTests['0']['Tests']['2']['Test']='Биология';
        $EntranceTests['0']['Tests']['2']['Form']='письменно';
        $EntranceTests['0']['Tests']['2']['IdSpecialization']='000003';
*/
        //die;
        global $res;
        global $roul;
        global $re;
        $error=0;
        /*=============Проверка на ИНН и на год============*/
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
        /*ГОД*/
        if (empty($year))// Год пуст
        {
            result("year",2,"Empty");
            $error++;
        }
        elseif($year!=date("Y")) // Год не этот год
        {
            result("year",2,"Not this year");
            $error++;
        }
        else // Год ОК
        {
            result("year",1,"OK");
        }
        /*Проверка даты*/
        $dateS=strtotime($DateStart);
        $dateF=strtotime($DateFinish);
        if ($dateS===false)//Не верный формат даты
        {
            result("DateStart",2,"Wrong format");
            $error++;
        }
        elseif($dateF<time())//Даты меньше сегоднешней
        {
            result("DateStart",2,"Less today");
            $error++;
        }
        else// Дата ОК
        {
            result("DateStart",1,"OK");
        }
        if ($dateF===false)//Не верный формат даты
        {
            result("DateFinish",2,"Wrong format");
            $error++;
        }
        elseif($dateF<time())//Даты меньше сегоднешней
        {
            result("DateFinish",2,"Less today");
            $error++;
        }
        else// Дата ОК
        {
            result("DateFinish",1,"OK");
        }
        if ($error>0)
        {
            result("FatalError",2,"Stop");
            return $res;
        }
        /*===проверка на наличия специальностей в этом учебном заведении===*/
        /*получение списка специальностей по определённой организации*/
        $OrganizationSpecialty = OrganizationSpecialtyTable::getList(array(
            'select' => array(
                '*',
                'SPECIALITY_'                           =>'SPECIALITY.*',
            ),
            'filter' => array(
                '=ORGANIZATION_ID'                      => $IdOrg, //Id оргонизации
                '=ORGANIZATION_SPECIALTY_STATUS'        => 1, // статус
            )
        ))->fetchAll();
        $str='';

        /*полный список специальностей*/
        $Specialty = SpecialtyTable::getList(array(
            'select' => array('*'),
        ))->fetchAll();
        /*полный список инвалидных специальностей у данной специ*/
        $Adaptation=OrganizationSpecialtyAdaptationTable::getList(
            array(
                'select' => array(
                    '*',
                    'ORGAN_' => 'ORGANIZATION_SPECIALTY.*',
                ),
                'filter' => array(
                    '=ORGAN_ORGANIZATION_ID'                      => $IdOrg, //Id оргонизации
                ),
            )
        )->fetchAll();
        /*получение списка тестов по этой специальности*/
        $Test=OrganizationSpecialtyExamTable::getList(array(
                'select' => array(
                    '*',
                    'ORGANIZATION_ID' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_ID',
                    'CODE' => 'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                    //  'rrr_' => 'ORGANIZATION.*',
                ),
                'filter' => array(
                    '=ORGANIZATION_ID'                      => $IdOrg, //Id оргонизации
//            '=ORGANIZATION_SPECIALTY_ID'             =>  $org_speci_id,
                ),
            )
        )->fetchAll();
        $g=0;//порядковый номер в Program_Id
        /*просмотр специальностей и состовления массива $key_or формата
        * ID - индификатор специализации в базе
        * CODE - номер/код специальности
        * SPECIALIZATION - код специализации
        * FinanceProduct - Вид финансирования
        * Plan - Кол-во мест
        */
        foreach ($program as $pr)
        {
            $g++;
            if(empty($pr['IdProgram']) || empty($pr['IdSpecialty']) || empty($pr['BaseReclamation']) || empty($pr['LevelOfTraining']) || empty($pr['LevelOfTraining']) || empty($pr['Plan']) || empty($pr['Month']) || $pr['Month']==0 || $pr['Plan']==0)
            {
                $mass=' empty: ';
                $mass.=empty($pr['IdProgram'])? ' IdProgram ':'';
                $mass.=empty($pr['IdSpecialty'])? ' IdSpecialty ':'';
                $mass.=empty($pr['BaseReclamation'])? ' BaseReclamation ':'';
                $mass.=empty($pr['LevelOfTraining'])? ' LevelOfTraining ':'';
                $mass.=empty($pr['Month']) || ($pr['Month']==0)? ' Month ':'';
                $mass.=empty($pr['Plan']) || ($pr['Plan']==0)? ' Month ':'';
                result("Program_Id:".$g,2,' Data is not complete:'.$mass);
                continue;
            }
            $IdSpecialty=trim($pr["IdSpecialty"]);
            $roul=$IdSpecialty;
            $re=$pr;

            /*поиск соответствующей специальности в таблице OrganizationSpecialty */
            $SearchOrg=array_filter($OrganizationSpecialty,function($a){
                global $roul;
                global $re;
                $pr=$re;
                $IdSpecialty=$roul;
                if (empty($pr["IdSpecialization"]))
                {
                    if ($a["SPECIALITY_SPECIALTY_CODE"]==$IdSpecialty //код специальности
                        && $a['ORGANIZATION_SPECIALTY_BASE_EDUCATION']==$pr["BaseReclamation"] //Базовое образование
                        && $a['ORGANIZATION_SPECIALTY_STUDY_MODE']==$pr['FormOfStudy'] //Форма обучения
                        && $a['ORGANIZATION_SPECIALTY_TRAINING_LEVEL']==$pr['LevelOfTraining'] //Уровень обучения
                        && $a['ORGANIZATION_SPECIALTY_TRAINING_TYPE']==$pr["TypeOfProgram"] // ВидПрограммы
                        && $a['ORGANIZATION_SPECIALTY_STUDY_PERIOD']==$pr['Month'] // месяцы обучения
                    )
                    {
                        return true;
                    }
                }
                else
                {
                    if ($a["SPECIALITY_SPECIALTY_CODE"]==$IdSpecialty //код специальности
                        && $a['ORGANIZATION_SPECIALTY_BASE_EDUCATION']==$pr["BaseReclamation"] //Базовое образование
                        && $a['ORGANIZATION_SPECIALTY_STUDY_MODE']==$pr['FormOfStudy'] //Форма обучения
                        && $a['ORGANIZATION_SPECIALTY_TRAINING_LEVEL']==$pr['LevelOfTraining'] //Уровень обучения
                        && $a['ORGANIZATION_SPECIALTY_TRAINING_TYPE']==$pr["TypeOfProgram"] // ВидПрограммы
                        && $a['ORGANIZATION_SPECIALTY_STUDY_PERIOD']==$pr['Month'] // месяцы обучения
                        && $a['IDSPECIALIZATION']==$pr["IdSpecialization"] // код специализации
                    )
                    {
                        return true;
                    }
                }
                return false;
            }); // возращение соответствующенго элемента массива
            $rs_or=true;
            //проверка на то что элемент найден и он в единственном энкземпляре
            if (empty($SearchOrg)) //Элемент не найден
            {
                $rs_or=false;
            }
            elseif (count($SearchOrg)>1) //Элемент имеет несколько экземпляров, выбран последний
            {
                $key_or[$g-1]['ID']=array_pop($SearchOrg)['ORGANIZATION_SPECIALTY_ID'];// Id специальности в таблице OrganizationSpecialty
                $key_or[$g-1]['CODE']=$IdSpecialty; // Код специальности
                $key_or[$g-1]['SPECIALIZATION']=$pr["IdSpecialization"]; // код специализации
                $key_or[$g-1]['FinanceProduct']=$pr["FinanceProduct"]; // Вид финансирования
                $key_or[$g-1]['Plan']=$pr["Plan"]; // Кол-во мест
                result("Program_Id:".$g,3,'IdSpecialty:'.$pr["IdSpecialty"].' Several key, selection key:'.$key_or[$g-1]['ID']);
            }
            else // Эелемент найден в одном экземпляре
            {
                $key_or[$g-1]['ID']=array_pop($SearchOrg)['ORGANIZATION_SPECIALTY_ID'];// Id специальности в таблице OrganizationSpecialty
                $key_or[$g-1]['CODE']=$IdSpecialty; // Код специальности
                $key_or[$g-1]['SPECIALIZATION']=$pr["IdSpecialization"]; // Код специальности
                $key_or[$g-1]['FinanceProduct']=$pr["FinanceProduct"]; // Вид финансирования
                $key_or[$g-1]['Plan']=$pr["Plan"]; // Кол-во мест
                result("Program_Id:".$g,5,"OldRecord:".$key_or[$g-1]['ID']);
            }
            /*проверка на наличие специальности в таблице OrganizationSpecialty*/
            if ($rs_or===false) // Создания новой записи в таблице OrganizationSpecialty и сохронения id в переменной $key_or
            {
                $key_sp = array_search($IdSpecialty, array_column($Specialty, 'SPECIALTY_CODE'));
                if($key_sp===false)//проверка на наличие специальности в таблице Specialty вывод сообщение о ошибке
                {
                    result("Program_Id:".$g,3,"IdSpecialty:notFound");
                    continue;
                }
                else // Добовления специальности в таблицу OrganizationSpecialty
                {
                    $elm=array(
                        'ORGANIZATION_ID'                                   => $IdOrg,// Id оргонизации
                        'SPECIALTY_ID'                                      => $Specialty[$key_sp]['SPECIALTY_ID'], // Id специальности
                        'ORGANIZATION_SPECIALTY_BASE_EDUCATION'             => $pr["BaseReclamation"], //Базовое образование
                        'ORGANIZATION_SPECIALTY_STUDY_MODE'                 => $pr['FormOfStudy'],//Форма обучения
                        'ORGANIZATION_SPECIALTY_TRAINING_LEVEL'             => $pr['LevelOfTraining'],//Уровень обучения
                        'ORGANIZATION_SPECIALTY_TRAINING_TYPE'              => $pr["TypeOfProgram"],// ВидПрограммы
                        'ORGANIZATION_SPECIALTY_STUDY_PERIOD'               => $pr['Month'], // месяцы обучения
                        'ORGANIZATION_SPECIALTY_PLANNED_ABITURIENTS_NUMBER' => '0', // ХЗ
                        'ORGANIZATION_SPECIALTY_PLANNED_GROUPS_NUMBER'      => '0', // ХЗ
                        'ORGANIZATION_SPECIALTY_STATUS'                     => '1', // Статус
                        'NAMESPECIALIZATION'                                => $pr["NameSpecialization"], // название специализации
                        'IDSPECIALIZATION'                                  => $pr['IdSpecialization'], // код специализации
                        'IDPROGRAM'                                         => $pr['IdProgram']
                    );
                    $IdOrgSpe = OrganizationSpecialtyTable::add($elm);
                    /*проверка на наличие ошибок записи*/
                    if (!$IdOrgSpe->isSuccess())
                    {
                        $mas=implode(' Error:',$IdOrgSpe->getErrorMessages());
                        result("Program_Id:".$g,4,'Error:'.$mas);
                        $g++;
                        continue;
                    }
                    else
                    {
                        $ID=$IdOrgSpe->getId();
                        $elm['ORGANIZATION_SPECIALTY_ID']=$ID;
                        $elm['SPECIALITY_SPECIALTY_CODE']=$IdSpecialty;
                        $OrganizationSpecialty[]=$elm;
                        result("Program_Id:".$g,1,"NewOrganizationSpecialty:".$ID);
                        $key_or[$g-1]['ID']=$ID;// Id специальности в таблице OrganizationSpecialty
                        $key_or[$g-1]['CODE']=$IdSpecialty; // Код специальности
                        $key_or[$g-1]['SPECIALIZATION']=$pr["IdSpecialization"]; // код специализации
                        $key_or[$g-1]['FinanceProduct']=$pr["FinanceProduct"]; // Вид финансирования
                        $key_or[$g-1]['Plan']=$pr["Plan"]; //Кол-во мест
                    }
                }
            }
            /*Добовления записей в OrganizationSpecialtyAdaptationTable*/
            //проверка на записаные теже самые результаты
            if(!empty($pr['AdaptationProgram']))
            {
                if (!empty($key_or[$g-1]['ID']))
                {
                    $key_adap=array_search($key_or[$g-1]['ID'], array_column($Adaptation, 'ORGANIZATION_SPECIALTY_ID'));
                    if ($key_adap===false)
                    {
                        $IdOrgSpeAda=OrganizationSpecialtyAdaptationTable::add(array(
                                'ORGANIZATION_SPECIALTY_ID'                 => $key_or[$g-1]['ID'],
                                'ORGANIZATION_SPECIALTY_ADAPTATION_TYPE'    => $pr['AdaptationProgram']
                            )
                        );
                        /*проверка на наличие ошибок записи*/
                        if (!$IdOrgSpeAda->isSuccess())
                        {
                            $mas=implode(' Error:',$IdOrgSpeAda->getErrorMessages());
                            result("AdaptationProgram:".$g,4,'Error:'.$mas);
                            //$g++;
                            continue;
                        }
                        else
                        {
                            $ID=$IdOrgSpeAda->getId();
                            result("AdaptationProgram:".$key_or[$g-1]['ID'],1,"NewAdaptationProgram:".$ID);
                        }
                    }
                    else
                    {
                        result("AdaptationProgram:".$key_or[$g-1]['ID'],5,"OldRecord:".$Adaptation[$key_adap]['ORGANIZATION_SPECIALTY_ADAPTATION_ID']);
                    }
                }
            }
            //$g++;
        }
        $key_or=KillDubll($key_or); // удаления дублей

        if(!is_array($key_or))// проверка на наличие найденных программ в системе
        {
            result("Program:".$g,3,'FatalError lack of programm');
            return $res;
        }
        /*вступительные экзаменны к специальностям*/
        $org_speci_id = array_filter ($org_speci_id);
        /*получение списка тестов по этой специальности*/
        $Test=OrganizationSpecialtyExamTable::getList(array(
            'select' => array(
                '*',
                'ORGANIZATION_ID' => 'ORGANIZATION_SPECIALTY.ORGANIZATION_ID',
            ),
            'filter' => array(
                '=ORGANIZATION_ID'                       => $IdOrg, //Id оргонизации
                '=ORGANIZATION_SPECIALTY_ID'             => array_column($key_or,'ID'),
            ),
        ))->fetchAll();
        /*запись тестов в таблицу*/

        if (is_array($EntranceTests))
        {
            foreach ($EntranceTests as $k => $et) {
                $IdSpecialty = trim($et["IdSpecialty"]);
                /*проверка на то IdSpecialty есть в данных специальностей*/
                if (array_search($IdSpecialty, array_column($key_or, 'CODE')) === false) {
                    result("EntranceTestsEl:" . $k, 2, 'IdSpecialty:NotFount in teg ProgramPlan' . $mas);
                    $error++;
                    continue;
                }

                /*получение всех соответствующих ключей с таблице OrganizationSpecialty*/
                foreach ($key_or as $kk => $kr) {
                    //
                    $key_test = array_search($kr['ID'], array_column($Test, 'ORGANIZATION_SPECIALTY_ID'));

                    if ($key_test === false) {
                        if ($kr['CODE'] == $IdSpecialty) {
                            
                            $g = 0;
                            foreach ($et['Tests'] as $test_el) {
                                if ($kr['SPECIALIZATION'] == $test_el['IdSpecialization']) {
                                    $g++;
                                    $IdSpeExa = OrganizationSpecialtyExamTable::add(array(
                                        'ORGANIZATION_SPECIALTY_ID' => $kr['ID'],//id спициализации
                                        'ORGANIZATION_SPECIALTY_EXAM_DISCIPLINE' => $test_el['Test'],//Название экзамена
                                        'ORGANIZATION_SPECIALTY_EXAM_TYPE' => $test_el['Form'],//Тип экзамена
                                    ));
                                    /*проверка на наличие ошибок записи*/
                                    if (!$IdSpeExa->isSuccess()) {
                                        $mas = implode(' Error:', $IdSpeExa->getErrorMessages());
                                        result("TestsEl:" . $g, 4, 'Error:' . $mas);
                                        continue;
                                    } else {
                                        $ID = $IdSpeExa->getId();
                                        result("Program_Id:" . $g, 1, "NewTest:" . $ID);
                                    }

                                }
                            }
                        }
                    } else {
                        $re = array();
                        $re = $kr;
                        $SearchOrg = array_filter($Test, function ($a) {
                            global $re;
                            $kr = $re;
                            if ($a['ORGANIZATION_SPECIALTY_ID'] == $kr['ID'] //idORGANIZATION_SPECIALTY
                            ) {
                                return true;
                            }
                            return false;
                        }); // возращение массив элементов
                        result("Test:" . $IdSpecialty, 5, "OldRecord:" . implode(' ', array_column($SearchOrg, 'ORGANIZATION_SPECIALTY_EXAM_ID')));
                    }
                }
            }
        }
        /*вставка данных в таблицу admission_plan*/
        $g=1;
        $dateSS=new \Bitrix\Main\Type\DateTime(date("d.m.Y",$dateS)." 00:00:00");
        $dateFF=new \Bitrix\Main\Type\DateTime(date("d.m.Y",$dateF)." 00:00:00");
        /*получение списка данных с AdmissionPlanTable по заполненым значениям*/
        $AdmissionPlan=AdmissionPlanTable::getList(array(
            'select' => array(
                '*',
            ),
            'filter' => array(
                '=ORGANIZATION_SPECIALTY_ID'             => array_column($key_or,'ID'),
            ),
        ))->fetchAll();

        foreach($key_or as $kr)
        {
            $key_adplan=array_search($kr['ID'], array_column($AdmissionPlan, 'ORGANIZATION_SPECIALTY_ID'));
            if($key_adplan===false)
            {
                //if(empty($kr['ADMISSION_PLAN_TUITION_STUDENTS_NUMBER']))

                $IdAdmPlan=AdmissionPlanTable::add(array(
                    'ORGANIZATION_SPECIALTY_ID'                 => $kr['ID'],
                    'ADMISSION_PLAN_START_DATE'                 => $dateSS,
                    'ADMISSION_PLAN_END_DATE'                   => $dateFF,
                    'ADMISSION_PLAN_GRANT_STUDENTS_NUMBER'      => $kr['ADMISSION_PLAN_GRANT_GROUPS_NUMBER'],
                    'ADMISSION_PLAN_TUITION_STUDENTS_NUMBER'    => $kr['ADMISSION_PLAN_TUITION_STUDENTS_NUMBER'],
                    'ADMISSION_PLAN_STATUS'                     => 2
                ));
                if (!$IdAdmPlan->isSuccess())
                {
                    $mas=implode(' Error:',$IdAdmPlan->getErrorMessages());
                    result("AdmissionPlanEl:".$g,4,'Error:'.$mas);
                    $g++;
                    continue;
                }
                else
                {
                    $ID=$IdAdmPlan->getId();
                    result("AdmissionEl:".$g,1,"NewAdmissionEl:".$ID);
                    $DateNow=new \Bitrix\Main\Type\DateTime(date("d.m.Y h:i:s"));
                    $IdAdmPlanEven=AdmissionPlanEventTable::add(array(
                        'ADMISSION_PLAN_ID'             => $ID,
                        'ADMISSION_PLAN_EVENT_DATE'     => $DateNow,
                        'ADMISSION_PLAN_EVENT_COMMENT'  => 'Импорт из 1с',
                        'USER_ID'                       =>  231,
                        'ADMISSION_PLAN_EVENT_STATUS'   =>  1,
                    ));
                    if(!$IdAdmPlanEven->isSuccess())
                    {
                        $mas=implode(' Error:',$IdAdmPlanEven->getErrorMessages());
                        result("AdmissionPlanEvanEl:".$ID,4,'Error:'.$mas);
                    }
                    else
                    {
                        $IDev=$IdAdmPlanEven->getId();
                        result("AdmissionPlanEvanEl:".$g,1,"NewAdmissionPlanEvanEl:".$IDev);
                    }
                }
                $g++;
            }
            else
            {
                result("AdmissionPlan:".$AdmissionPlan[$key_adplan]['ADMISSION_PLAN_ID'],5,"OldRecord:".$AdmissionPlan[$key_adplan]['ADMISSION_PLAN_ID']);
            }
        }


        return $res;
    }

    // метод GetWebServiceDesc возвращает описание сервиса и его методов
    function GetWebServiceDesc()
    {
        $wsdesc = new CWebServiceDesc();
        $wsdesc->wsname = "bitrix.WS.TransferPlan";
        $wsdesc->wsclassname = "TransferPlanWS";
        $wsdesc->wsdlauto = true;
        $wsdesc->wsendpoint = CWebService::GetDefaultEndpoint();
        $wsdesc->wstargetns = CWebService::GetDefaultTargetNS();
        $wsdesc->classTypes = array();
        $wsdesc->structTypes = Array();

        /*      */
        $wsdesc->structTypes["arProgramPlana"]= array
        (
            "IdProgram"             => array("varType" => "string", "strict" => "off"),
            //"NameProgram"           => array("varType" => "string", "strict" => "off"),
            "IdSpecialty"           => array("varType" => "string", "strict" => "off"),
            "FormOfStudy"           => array("varType" => "string", "strict" => "off"),
            "BaseReclamation"       => array("varType" => "string", "strict" => "off"),
            "LevelOfTraining"       => array("varType" => "string", "strict" => "off"),
            "FinanceProduct"        => array("varType" => "string", "strict" => "off"),
            "IdSpecialization"      => array("varType" => "string", "strict" => "off"),
            "NameSpecialization"    => array("varType" => "string", "strict" => "off"),
            "TargetDirection"       => array("varType" => "string", "strict" => "off"),
            "Plan"                  => array("varType" => "string", "strict" => "off"),
            "Month"                 => array("varType" => "string", "strict" => "off"),
            "TypeOfProgram"         => array("varType" => "string", "strict" => "off"),
            "AdaptationProgram"     => array("varType" => "string", "strict" => "off"),
        );

        $wsdesc->structTypes["arEntranceTests"]= array
        (
            "IdSpecialty"       => array("varType" => "string", "strict" => "off"),
            "Tests" 			=> array("varType" => "Tests", "arrType" => "arTests", "strict" => "off"), // реестр
        );

        $wsdesc->structTypes["arTests"]= array
        (
            "Test" 				=> array("varType" => "string", "strict" => "off"),
            "Form" 			    => array("varType" => "string", "strict" => "off"),
            "IdSpecialization" 	=> array("varType" => "string", "strict" => "off"),
        );

        $wsdesc->structTypes["arRes"]= array
        (
            "ID"                => array("varType" => "string", "strict" => "off"),
            "Kod" 				=> array("varType" => "string", "strict" => "off"),
            "Message"		    => array("varType" => "string", "strict" => "off"),
        );

        $wsdesc->classes = array(
            "TransferPlanWS"=> array(
                "TransferPlan" => array(
                    "type"		=> "public",
                    "input"		=> array(
                        "organization"      => array("varType" => "string", "strict" => "off"),
                        "year"              => array("varType" => "string", "strict" => "off"),
                        "DateStart"         => array("varType" => "string", "strict" => "off"),
                        "DateFinish"        => array("varType" => "string", "strict" => "off"),
                        "ProgramPlan"       => array("varType" => "ProgramPlana", "arrType" => "arProgramPlana" , "strict" => "off"),
                        "EntranceTests"     => array("varType" => "EntranceTests", "arrType" => "arEntranceTests", "strict" => "off"),
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

$arParams["WEBSERVICE_NAME"] = "bitrix.WS.TransferPlan";
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
