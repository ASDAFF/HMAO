<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("webservice") || !CModule::IncludeModule("iblock")) return;

Bitrix\Main\Loader::includeModule('spo.site');

use Spo\Site\Entities\ApplicationTable;
use Spo\Site\Entities\ApplicationEventTable;
use Spo\Site\Entities\AbiturientProfileTable;
use Spo\Site\Entities\HostelTable;
use Bitrix\Main\Entity\ExpressionField;


// наш новый класс наследуется от базового IWebService
class GetEnrolleesWS extends IWebService
{
    private $result;

    /**
     * @param $inn
     * @param $date
     * @return mixed
     */
    public function GetEnr($inn, $date)
    {
        $userIds = array();
        $applications = $this->getApplications($inn, $date);

        if (!$applications)
        {
            $this->result['EnrolleePersonalData']=array();
            $this->result['BlockApplications']=array();
        }
        else
        {
            foreach ($applications as $index => $application) {
                $userIds[] = $application['USER_ID'];
                /**
                 * Заполенение данными блока arBlockApplications
                 */
                foreach ($application as $key => $value){
                    if ($key == 'USER_ID') continue;
                    $this->result['BlockApplications'][$index . ':arBlockApplications'][$key] = $value;
                }
            }
            $profiles = $this->getProfiles($userIds,$application['IdOrg']);
            foreach ($profiles as $index => $profile) {
                foreach ($profile as $key => $value) {
                    if (!is_array($value)) {
                        $this->result['EnrolleePersonalData'][$index . ':arEnrolleePersonalData'][$key] = $value;
                    } elseif ($key == 'Parent') {
                        foreach ($value as $parentKey => $parentValue) {
                            foreach ($parentValue as $pKey => $pValue) {
                                $this->result['EnrolleePersonalData'][$index . ':arEnrolleePersonalData'][$parentKey . ':arParent'][$pKey] = $pValue;
                            }
                        }
                    }
                }
            }
        }

       /* print_r($this->result);
        die;*/
        return $this->result;
    }

    // метод GetWebServiceDesc возвращает описание сервиса и его методов
    /**
     * @return CWebServiceDesc
     */
    public function GetWebServiceDesc()
    {
        $wsdesc = new CWebServiceDesc();
        $wsdesc->wsname = "bitrix.WS.GetEnrollees";
        $wsdesc->wsclassname = "GetEnrolleesWS";
        $wsdesc->wsdlauto = true;
        $wsdesc->wsendpoint = CWebService::GetDefaultEndpoint();
        $wsdesc->wstargetns = CWebService::GetDefaultTargetNS();
        $wsdesc->classTypes = array();
        $wsdesc->structTypes = Array();

        $wsdesc->structTypes["arEnrolleePersonalData"] = array
        (
            "Id"                                => array("varType" => "string", "strict" => "off"),
            "FIO"                               => array("varType" => "string", "strict" => "off"),
            "BirthDate"                         => array("varType" => "string", "strict" => "off"),
            "BirthPlace"                        => array("varType" => "string", "strict" => "off"),
            "Citizenship"                       => array("varType" => "string", "strict" => "off"),
            "DocTypePers"                       => array("varType" => "string", "strict" => "off"),
            "DocSerPers"                        => array("varType" => "string", "strict" => "off"),
            "DocNumPers"                        => array("varType" => "string", "strict" => "off"),
            "DocIssuedPers"                     => array("varType" => "string", "strict" => "off"),
            "DocDatePers"                       => array("varType" => "string", "strict" => "off"),
            "DocCodePers"                       => array("varType" => "string", "strict" => "off"),
            "INN"                               => array("varType" => "string", "strict" => "off"),
            "SNILS"                             => array("varType" => "string", "strict" => "off"),
            "HealthInsNumb"                     => array("varType" => "string", "strict" => "off"),
            "HealthInsSer"                      => array("varType" => "string", "strict" => "off"),
            "DateIns"                           => array("varType" => "string", "strict" => "off"),
            "AddressResidence"                  => array("varType" => "string", "strict" => "off"),
            "AddressRegistration"               => array("varType" => "string", "strict" => "off"),
            "TypeResidence"                     => array("varType" => "string", "strict" => "off"),
            "Phone"                             => array("varType" => "string", "strict" => "off"),
            "Email"                             => array("varType" => "string", "strict" => "off"),
            "ReceivedEducation"                 => array("varType" => "string", "strict" => "off"),
            "EducationDocNumb"                  => array("varType" => "string", "strict" => "off"),
            "EducationDocSer"                   => array("varType" => "string", "strict" => "off"),
            "EducationalOrganizationEndDate"    => array("varType" => "string", "strict" => "off"),
            "AverageСertificate"                => array("varType" => "string", "strict" => "off"),
            "Sex"                               => array("varType" => "string", "strict" => "off"),
            "AdditionalIinformation"            => array("varType" => "string", "strict" => "off"),
            "RegistrationCertificateNumb"       => array("varType" => "string", "strict" => "off"),
            "RegistrationCertificateSer"        => array("varType" => "string", "strict" => "off"),
            "Hostel"                            => array("varType" => "string", "strict" => "off"),
            "Parent"                            => array("arrType" => "arParent", "varType" => "Parent"),
        );

        $wsdesc->structTypes["arParent"] = array
        (
            "Id"                                => array("varType" => "string", "strict" => "off"),
            "FIO"                               => array("varType" => "string", "strict" => "off"),
            "BirthDate"                         => array("varType" => "string", "strict" => "off"),
            "SNILS"                             => array("varType" => "string", "strict" => "off"),
            "Citizenship"                       => array("varType" => "string", "strict" => "off"),
            "DocTypePers"                       => array("varType" => "string", "strict" => "off"),
            "DocSerPers"                        => array("varType" => "string", "strict" => "off"),
            "DocNumPers"                        => array("varType" => "string", "strict" => "off"),
            "DocIssuedPers"                     => array("varType" => "string", "strict" => "off"),
            "DocDatePers"                       => array("varType" => "string", "strict" => "off"),
            "DobDocument"                       => array("varType" => "string", "strict" => "off"),
        );

        $wsdesc->structTypes["arBlockApplications"] = array
        (
            "IdProgram"                         => array("varType" => "string", "strict" => "off"),
            "IdAbiturient"                      => array("varType" => "string", "strict" => "off"),
            "IdApplication"                     => array("varType" => "string", "strict" => "off"),
            "FinanceProduct"                    => array("varType" => "string", "strict" => "off"),
            "IdSpecialization"                  => array("varType" => "string", "strict" => "off"),
            "TargetDirection"                   => array("varType" => "string", "strict" => "off"),
            "ApplicationPriority"               => array("varType" => "string", "strict" => "off"),
            "ApplicationStatus"                 => array("varType" => "string", "strict" => "off"),
        );

        $wsdesc->classes = array(
            "GetEnrolleesWS" => array(
                "GetEnr" => array(
                    "type" => "public",
                    "input" => array(
                        "organization"          => array("varType" => "string", "strict" => "off"),
                        "date"                  => array("varType" => "string", "strict" => "off"),
                    ),
                    "output" => array(
                        "EnrolleePersonalData"  => array("arrType" => "arEnrolleePersonalData", "varType" => "EnrolleePersonalData", "strict" => "off"),
                        "BlockApplications"     => array("arrType" => "arBlockApplications", "varType" => "BlockApplications", "strict" => "off"),
                        //"Parent" => array("arrType" => "arParent", "varType" => "Parent"),
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
            'filter'    => array(
                '=ORGANIZATION.INN'                         => $inn,
                '>APPLICATION_EVENT.APPLICATION_EVENT_DATE' => $date,
                '=ABITURIENT.VALIDITY'                      => 1,
                array
                (
                    'LOGIC'                                 => 'OR',
                    'APPLICATION_STATUS'                    => 2,
                    'IMPORT_TO_C'                           => 1,
                )
            ),
            'select'    => array(
                'IdProgram'             => 'ORGANIZATION_SPECIALTY.IDPROGRAM',
                'IdApplication'         => 'APPLICATION_ID',
                'FinanceProduct'        => 'APPLICATION_FUNDING_TYPE',
                'IdSpecialization'      => 'ORGANIZATION_SPECIALTY.IDSPECIALIZATION',
                'TargetDirection'       => 'ORGANIZATION_SPECIALTY.TARGETDIRECTION',
                'ApplicationPriority'   => 'APPLICATION_PRIORITY',
                'ApplicationStatus'     => 'APPLICATION_STATUS',
                'IdOrg'                 => 'ORGANIZATION.ORGANIZATION_ID',
                'USER_ID',
                'CNT',
            ),
            'runtime'   =>  array(
                new ExpressionField('CNT', 'MAX(APPLICATION_EVENT_ID)')
            )
        ));

        while ($application = $applicationDb->fetch()) {
            $result[] = array(
                'IdProgram'             =>  strval($application['IdProgram']),
                'IdApplication'         =>  strval($application['IdApplication']),
                'IdAbiturient'          =>  strval($application['USER_ID']),
                'FinanceProduct'        =>  strval($application['FinanceProduct']),
                'IdSpecialization'      =>  strval($application['IdSpecialization']),
                'TargetDirection'       =>  strval($application['TargetDirection']),
                'ApplicationPriority'   =>  strval($application['ApplicationPriority']),
                'ApplicationStatus'     =>  strval($application['ApplicationStatus']),
                'USER_ID'               =>  $application['USER_ID'],
                'IdOrg'                 =>  $application['IdOrg'],
            );
            $res=ApplicationTable::update($application['IdApplication'],
                array(
                    'IMPORT_TO_C'           => 1,
                    'APPLICATION_STATUS'    => 7,
                ));
            $resApEven=ApplicationEventTable::add(array(
                'APPLICATION_ID'                => $application['IdApplication'],
                'USER_ID'                       => 231,
                'APPLICATION_EVENT_DATE'        => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:i:s")),
                'APPLICATION_EVENT_STATUS'      => 7,
                'APPLICATION_EVENT_REASON'      => 1,
                'APPLICATION_EVENT_COMMENT'     => 'Данные переданые в 1С:Колледж',
            ));
        }
        return $result;
    }

    /**
     * @param  array $userIds
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    protected function getProfiles($userIds,$idOrg)
    {
        $result = array();
        $res=HostelTable::UserId($userIds,$idOrg);
        if (empty($res))
        {
            $hostel=0;
        }
        else
        {
            $hostel=1;
        }
        $profilesDb = AbiturientProfileTable::getList(array(
            'filter' => array(
                '=USER_ID'  => $userIds,
                '=VALIDITY' => 1,
            ),
            'select' => array(
                "Id"                                => 'USER_ID',
                'FIO'                               => 'FIO',
                'BirthDate'                         => 'SPO_ABITURIENT_PROFILE_BIRTHDAY',
                'BirthPlace'                        => 'SPO_ABITURIENT_PROFILE_BIRTHPLACE',
                'Citizenship'                       => 'SPO_ABITURIENT_PROFILE_NATIONALITY',
                'DocTypePers'                       => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_TYPE',
                "DocSerPers"                        => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_SERIES',
                "DocNumPers"                        => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_NUMBER',
                "DocIssuedPers"                     => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_BY',
                "DocDatePers"                       => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_DATE',
                "DocCodePers"                       => 'SPO_ABITURIENT_PROFILE_IDENTITY_DOCUMENT_ISSUED_CODE',
                "INN"                               => 'SPO_ABITURIENT_PROFILE_INN',
                "SNILS"                             => 'SPO_ABITURIENT_PROFILE_SNILS',
                "HealthInsNumb"                     => 'SPO_ABITURIENT_PROFILE_INSURANCE_NUMBER',
                "HealthInsSer"                      => 'SPO_ABITURIENT_PROFILE_INSURANCE_SERIES',
                "DateIns"                           => 'SPO_ABITURIENT_PROFILE_INSURANCE_DATE',
                "AddressResidence"                  => 'ADDRESSRESIDENCE',
                "AddressRegistration"               => 'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_REGION',
                'TypeResidence'                     => 'TYPERESIDENCE',
                "Phone"                             => 'Spo\Site\Entities\UserValidDataTable:ABITURIENT_PROFILE.USER_VALID_DATA_PHONE',
                "Email"                             => 'Spo\Site\Entities\UserValidDataTable:ABITURIENT_PROFILE.USER_VALID_DATA_EMAIL',
                "ReceivedEducation"                 => 'SPO_ABITURIENT_PROFILE_EDUCATION',
                "EducationDocNumb"                  => 'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_NUMBER',
                "EducationDocSer"                   => 'SPO_ABITURIENT_PROFILE_EDUCATION_DOCUMENT_SERIES',
                "EducationalOrganizationEndDate"    => 'SPO_ABITURIENT_PROFILE_EDUCATION_COMPLETION_DATE',
                "AverageСertificate"                => 'SPO_ABITURIENT_PROFILE_CAS',
                "Sex"                               => 'SPO_ABITURIENT_PROFILE_GENDER',
                "AdditionalIinformation"            => 'SPO_ABITURIENT_PROFILE_ADDITIONAL_DATA',
                "RegistrationCertificateNumb"       => 'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_NUMBER',
                "RegistrationCertificateSer"        => 'SPO_ABITURIENT_PROFILE_MILITARY_DOCUMENT_SERIES',

            )
        ));

        while ($profile = $profilesDb->fetch()) {
            $result[$profile['Id']] = array(
                'Id'                                => strval($profile['Id']),
                'FIO'                               => strval($profile['FIO']),
                'BirthDate'                         => $this->convertDateTo1cFormat($profile['BirthDate']),
                'BirthPlace'                        => strval($profile['BirthPlace']),
                'Citizenship'                       => strval($profile['Citizenship']),
                'DocTypePers'                       => strval($profile['DocTypePers']),
                'DocSerPers'                        => strval($profile['DocSerPers']),
                'DocNumPers'                        => strval($profile['DocNumPers']),
                'DocIssuedPers'                     => strval($profile['DocIssuedPers']),
                'DocDatePers'                       => $this->convertDateTo1cFormat($profile['DocDatePers']),
                'DocCodePers'                       => strval($profile['DocCodePers']),
                'INN'                               => strval($profile['INN']),
                'SNILS'                             => strval($profile['SNILS']),
                'HealthInsNumb'                     => strval($profile['HealthInsNumb']),
                'HealthInsSer'                      => strval($profile['HealthInsSer']),
                'DateIns'                           => $this->convertDateTo1cFormat($profile['DateIns']),
                'AddressResidence'                  => strval($profile['AddressResidence']),
                'AddressRegistration'               => strval($profile['AddressRegistration']),
                'TypeResidence'                     => strval($profile['TypeResidence']),
                'Phone'                             => strval($profile['Phone']),
                'Email'                             => strval($profile['Email']),
                'ReceivedEducation'                 => strval($profile['ReceivedEducation']),
                'EducationDocNumb'                  => strval($profile['EducationDocNumb']),
                'EducationDocSer'                   => strval($profile['EducationDocSer']),
                'EducationalOrganizationEndDate'    => $this->convertDateTo1cFormat($profile['EducationalOrganizationEndDate']),
                'AverageСertificate'                => strval($profile['AverageСertificate']),
                'Sex'                               => strval($profile['Sex']),
                'AdditionalIinformation'            => strval($profile['AdditionalIinformation']),
                'RegistrationCertificateNumb'       => strval($profile['RegistrationCertificateNumb']),
                'RegistrationCertificateSer'        => strval($profile['RegistrationCertificateSer']),
                "Hostel"                            => strval($hostel),
            );
        }

        $profilesDb = AbiturientProfileTable::getList(array(
            'filter' => array(
                '=USER_ID' => $userIds,
                '!Spo\Site\Entities\ParentTable:ABITURIENT.ID_PARENT' => false
            ),
            'select' => array(
                'ID'        => 'USER_ID',
                'PARENT_'   => 'Spo\Site\Entities\ParentTable:ABITURIENT.*',
                //'USER_ID',
            )
        ));

        while ($profileParent = $profilesDb->fetch()) {
            $parent = array(
                "Id"            => strval($profileParent['ID']),
                "FIO"           => strval($profileParent['PARENT_FIO']),
                "BirthDate"     => $this->convertDateTo1cFormat($profileParent['PARENT_BIRTHDATE']),
                "SNILS"         => strval($profileParent['PARENT_SNILS']),
                "Citizenship"   => strval($profileParent['PARENT_CITIZENSHIP']),
                "DocTypePers"   => strval($profileParent['PARENT_DOCTYPEPERS']),
                "DocSerPers"    => strval($profileParent['PARENT_DOCSERPERS']),
                "DocNumPers"    => strval($profileParent['PARENT_DOCNUMPERS']),
                "DocIssuedPers" => strval($profileParent['PARENT_DOCISSUEDPERS']),
                "DocDatePers"   => $this->convertDateTo1cFormat($profileParent['PARENT_DOCDATEPERS']),
                "DobDocument"   => strval($profileParent['PARENT_DOBDOCUMENT']),
            );

            $result[$profileParent['ID']]['Parent'][] = $parent;
        }

        return array_values($result);
    }

    /**
     * @param string|\Bitrix\Main\Type\Date $date
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

$arParams["WEBSERVICE_NAME"] = "bitrix.WS.GetEnrollees";
$arParams["WEBSERVICE_CLASS"] = "GetEnrolleesWS";
$arParams["WEBSERVICE_MODULE"] = "";

// передаем в компонент описание веб-сервиса
$APPLICATION->IncludeComponent(
    "bitrix:webservice.server",
    "",
    $arParams
);


die();
?>
