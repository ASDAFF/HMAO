<?php
namespace Spo\Site\Adapters;

use Spo\Site\Domains\SpecialtyDomain;
//use Spo\Site\Doctrine\Entities\Specialty;
//use Spo\Site\Doctrine\Entities\OrganizationSpecialty;
//use Spo\Site\Doctrine\Entities\Qualification;
use Spo\Site\Entities\SpecialtyTable;
use Spo\Site\Entities\SpecialtyGroupTable;
use Symfony\Component\Validator\Constraints\Count;
use Bitrix\Main\Type;

class SpecialtyDomainAdapter
{

    public static function listSpecialties(/*SpecialtyDomain $domain*/)
    {
        /*$specialties = $domain->getEntityCollection();

        foreach ($specialties as $specialty) {
            // @var Specialty $specialty
            $specialtyAttributes = array(
                'id' => $specialty->getId(),
                'code' => $specialty->getCode(),
                'title' => $specialty->getTitle(),
            );

            $result[] = $specialtyAttributes;
        }*/
        $ArrayResult = SpecialtyTable::getList(array(
            //'filter' => array('!SPECIALTY_ID'=>'','!SPECIALTY_TITLE'=>'','!SPECIALTY_CODE'=>'','!SPECIALTY_GROUP_ID'=>'','!ORGANIZATION_SPECIALTY.SPECIALTY_ID'=>''),
            //'group'   => array('SPECIALTY_ID','SPECIALTY_TITLE','SPECIALTY_CODE'),
            'order'  => array('SPECIALTY_CODE' => 'ASC'),
            'select' => array(
                'id'=>'SPECIALTY_ID',
                'code'=>'SPECIALTY_CODE',
                'title'=>'SPECIALTY_TITLE',
            )
        ))->fetchAll();
        return $ArrayResult;
        //return $result;
    }

    public static function listSpecialtiesByGroup(/*SpecialtyDomain $domain*/)
    {
        /*$specialties = $domain->getEntityCollection();
        $specialtyGroups = array();

        foreach ($specialties as $specialty) {
            // @var Specialty $specialty
            $specialtyAttributes = array(
                'id' => $specialty->getId(),
                'code' => $specialty->getCode(),
                'title' => $specialty->getTitle(),
            );

            if (!isset($specialtyGroups[$specialty->getSpecialtyGroupId()])) {
                $specialtyGroups[$specialty->getSpecialtyGroupId()] = array(
                    'groupTitle' => $specialty->getSpecialtyGroup()->getTitle(),
                    'specialties' => array(),
                );
            }

            $specialtyGroups[$specialty->getSpecialtyGroupId()]['specialties'][] = $specialtyAttributes;
        }*/
        //Фильтр1
        preg_match_all("/[^0-9\.]*([0-9]{1,2}[\.]{0,1}[0-9]{0,2}[\.]{0,1}[0-9]{0,2})[^0-9\.]*/", $_GET['search'], $matches);
        $allMatches=$matches[1];
        preg_match_all("/[^A-zА-я]*([^0-9\.]+)[^A-zА-я]*/", $_GET['search'], $slova);
        $allSlova=$slova[1];
        $date = new \Bitrix\Main\Type\DateTime("01.01.".date("Y")." 00:00:00");
        $date2 = date("Y")+1;
        $date2 = new \Bitrix\Main\Type\DateTime("01.01.".$date2." 00:00:00");
        $FITER2['>=SPECIALITY.ORGANIZATION_SPECIALTY.ADMISSION_PLAN.ADMISSION_PLAN_START_DATE']=$date;
        $FITER2['<=SPECIALITY.ORGANIZATION_SPECIALTY.ADMISSION_PLAN.ADMISSION_PLAN_END_DATE']=$date2;
        $FITER2['=SPECIALITY.ORGANIZATION_SPECIALTY.ADMISSION_PLAN.ADMISSION_PLAN_STATUS']=2;
        $FITER['LOGIC']='OR';
        if(count($allMatches)>0){
            for($i=0;count($allMatches)>$i;$i=$i+1){
                $allMatches[$i]=trim($allMatches[$i]);
            }
            $FITER['%SPECIALITY.SPECIALTY_CODE'] = $allMatches;
        }
        if(count($allSlova)>0) {
            for($i=0;count($allSlova)>$i;$i=$i+1){
                $allSlova[$i]=trim($allSlova[$i]);
            }
            $FITER['%SPECIALITY.SPECIALTY_TITLE'] = $allSlova;
        }
        if(count($allSlova)==0 and count($allMatches)==0) {
            $FITER['!SPECIALTY_GROUP_CODE'] = '';
        }
        $ArrayResult = SpecialtyGroupTable::getList(array(
            'filter' => array($FITER2,$FITER),
            'group'   => array('SPECIALTY_GROUP_TITLE'),
            'order'  => array('SPECIALTY_GROUP_CODE' => 'ASC'),
            'select' => array(
                'groupTitle'=>'SPECIALTY_GROUP_TITLE',
                'id'=>'SPECIALITY.SPECIALTY_ID',
                'code'=>'SPECIALITY.SPECIALTY_CODE',
                'title'=>'SPECIALITY.SPECIALTY_TITLE',
                'SPECIALITY.ORGANIZATION_SPECIALTY.ADMISSION_PLAN.ADMISSION_PLAN_STATUS',
            )
        ))->fetchAll();
        $q=0;
        $ArrayResultNew=array();
        $specialties = array();
        for($i=0;count($ArrayResult)>$i;$i=$i+1){
            $j=$i+1;
            if ($ArrayResult[$i]['groupTitle'] == $ArrayResult[$j]['groupTitle']) {
                $specialtie['id'] = $ArrayResult[$i]['id'];
                $specialtie['code'] = $ArrayResult[$i]['code'];
                $specialtie['title'] = $ArrayResult[$i]['title'];
                $specialties[] = $specialtie;
            } else {
                $ArrayResultNew[$q]['groupTitle'] = $ArrayResult[$i]['groupTitle'];
                $specialtie['id'] = $ArrayResult[$i]['id'];
                $specialtie['code'] = $ArrayResult[$i]['code'];
                $specialtie['title'] = $ArrayResult[$i]['title'];
                $specialties[] = $specialtie;
                $ArrayResultNew[$q]['specialties'] = $specialties;
                $specialties = array();
                $q=$q+1;
            }
        }
        return $ArrayResultNew;
        //return $specialtyGroups;
    }

    /*public static function getSpecialtyList1(SpecialtyDomain $domain)
    {
        $result = array();
        $qList  = array();
        $specialties = $domain->getEntityCollection();
        // @var Specialty $specialty 
        // @var Qualification $qualification 
        foreach ($specialties as $specialty) {
            $qualificationList = $specialty->getQualifications();

            $qList = array();
            foreach($qualificationList as $qualification){
                $qList[] = array(
                    'id'    => $qualification->getId(),
                    'title' => $qualification->getTitle(),
                );
            }

            $specialtyAttributes = array(
                'id'    => $specialty->getId(),
                'code'  => $specialty->getCode(),
                'title' => $specialty->getTitle(),
                'qualifications' => $qList,
            );
            $result[] = $specialtyAttributes;
        }
        return array(
            'list' => $result,
            'totalCount' => $domain->getTotalCount()
        );
    }*/

/*  @param array $filter
    @return array*/
    public  static function getListCastomFilter($filter)
    {
        $ArrayResult = SpecialtyGroupTable::getList(array(
            'filter' => array(
                '=SPECIALITY.SPECIALTY_ID' => $filter,
            ),
            'group'   => array('SPECIALTY_GROUP_TITLE'),
            'order'  => array('SPECIALTY_GROUP_CODE' => 'ASC'),
            'select' => array(
                'groupTitle'=>'SPECIALTY_GROUP_TITLE',
                'id'=>'SPECIALITY.SPECIALTY_ID',
                'code'=>'SPECIALITY.SPECIALTY_CODE',
                'title'=>'SPECIALITY.SPECIALTY_TITLE',
                'SPECIALITY.ORGANIZATION_SPECIALTY.ADMISSION_PLAN.ADMISSION_PLAN_STATUS',
            )
        ))->fetchAll();
        $q=0;
        $ArrayResultNew=array();
        $specialties = array();
        for($i=0;count($ArrayResult)>$i;$i=$i+1){
            $j=$i+1;
            if ($ArrayResult[$i]['groupTitle'] == $ArrayResult[$j]['groupTitle']) {
                $specialtie['id'] = $ArrayResult[$i]['id'];
                $specialtie['code'] = $ArrayResult[$i]['code'];
                $specialtie['title'] = $ArrayResult[$i]['title'];
                $specialties[] = $specialtie;
            } else {
                $ArrayResultNew[$q]['groupTitle'] = $ArrayResult[$i]['groupTitle'];
                $specialtie['id'] = $ArrayResult[$i]['id'];
                $specialtie['code'] = $ArrayResult[$i]['code'];
                $specialtie['title'] = $ArrayResult[$i]['title'];
                $specialties[] = $specialtie;
                $ArrayResultNew[$q]['specialties'] = $specialties;
                $specialties = array();
                $q=$q+1;
            }
        }
        return $ArrayResultNew;
    }

    public static function getSpecialtyList()
    {
        $ArrayResult = SpecialtyTable::getList(array(
            'filter' => array('!SPECIALTY_ID'=>'','!SPECIALTY_TITLE'=>'','!SPECIALTY_CODE'=>'','!SPECIALTY_GROUP_ID'=>'','!ORGANIZATION_SPECIALTY.SPECIALTY_ID'=>''),
            'group'   => array('SPECIALTY_ID','SPECIALTY_TITLE','SPECIALTY_CODE'),
            'order'  => array('SPECIALTY_CODE' => 'ASC'),
            'select' => array(
                '*',
                'QUALIFICATION_ID'=>'QUALIFICATIONS.QUALIFICATION.QUALIFICATION_ID',
                'QUALIFICATION_TITLE'=>'QUALIFICATIONS.QUALIFICATION.QUALIFICATION_TITLE',
            )
        ))->fetchAll();
        $y=0;
        for($i=0;count($ArrayResult)>$i;$i=$i+1){
            $j=$i+1;
            if(count($ArrayResult)>=$j) {
                if ($ArrayResult[$i]['SPECIALTY_ID'] == $ArrayResult[$j]['SPECIALTY_ID']) {
                    $qualification['id'] = $ArrayResult[$i]['QUALIFICATION_ID'];
                    $qualification['title'] = $ArrayResult[$i]['QUALIFICATION_TITLE'];
                    $qualifications[] = $qualification;
                } else {
                    $ArrayResultNew['id'] = $ArrayResult[$i]['SPECIALTY_ID'];
                    $ArrayResultNew['code'] = $ArrayResult[$i]['SPECIALTY_CODE'];
                    $ArrayResultNew['title'] = $ArrayResult[$i]['SPECIALTY_TITLE'];
                    $qualification['id'] = $ArrayResult[$i]['QUALIFICATION_ID'];
                    $qualification['title'] = $ArrayResult[$i]['QUALIFICATION_TITLE'];
                    $qualifications[] = $qualification;
                    $ArrayResultNew['qualifications'] = $qualifications;
                    $ResultNew[] = $ArrayResultNew;
                    $qualifications = array();
                }
            }
        }

        //return $ResultNew;
        return array(
            'list' => $ResultNew,
            'totalCount' => 0
        );
    }

    public static function getSpecialtiesListWithOrganizationSpecialtiesAndTotalCount(SpecialtyDomain $domain)
    {
        $specialties = $domain->getEntityCollection();
        $result = array();

        //$orgSpecialties = $domain->getEntityCollection();

        foreach ($specialties as $specialty) {
            /* @var Specialty $specialty */
            /* @var OrganizationSpecialty $orgSpecialty */
            $orgSpecialties = $specialty->getOrganizationSpecialty();

            $orgSpecialtyAttributes = array();
            foreach($orgSpecialties as $orgSpecialty){
                $orgSpecialtyAttributes[] = array(
                    'organizationSpecialtyId'            => $orgSpecialty->getId(),
                    'organizationSpecialtyBaseEducation' => $orgSpecialty->getBaseEducation(),
                    'organizationSpecialtyStudyMode'     => $orgSpecialty->getStudyMode(),
                    'organizationSpecialtyStatus'        => $orgSpecialty->getStatus(),
                );
            }

            $specialtyAttributes = array(
                'specialtyId'    => $specialty->getId(),
                'specialtyCode'  => $specialty->getCode(),
                'specialtyTitle' => $specialty->getTitle(),
                'organizationSpecialties' => $orgSpecialtyAttributes
            );

            $result[] = $specialtyAttributes;
        }

        return array(
            'list'       => $result,
            'totalCount' => $domain->getTotalCount()
        );
    }

    public static function getSpecialtyWithDescription($specialtyId)
    {
        /** @var Specialty $specialty */
        /*$specialty = $domain->getModel();
        $result = array(
            'specialtyId' => $specialty->getId(),
            'specialtyTitle' => $specialty->getTitle(),
            'specialtyCode' => $specialty->getCode(),
            'specialtyDescription' => $specialty->getDescription(),
            'specialtyGroupTitle' => $specialty->getSpecialtyGroup()->getTitle(),
            'specialtyQualifications' => array(),
        );

        foreach ($specialty->getQualifications() as $qualification) {
            // @var Qualification $qualification
            $result['specialtyQualifications'][] = array(
                'id' => $qualification->getId(),
                'title' => $qualification->getTitle(),
            );
        }*/
        $ArrayResult = SpecialtyTable::getList(array(
            'filter' => array('=SPECIALTY_ID'=>$specialtyId),
            //'group'   => array('SPECIALTY_ID','SPECIALTY_TITLE','SPECIALTY_CODE'),
            'order'  => array('SPECIALTY_CODE' => 'ASC'),
            'select' => array(
                'specialtyId'=>'SPECIALTY_ID',
                'specialtyTitle'=>'SPECIALTY_TITLE',
                'specialtyCode'=>'SPECIALTY_CODE',
                'specialtyDescription'=>'SPECIALTY_DESCRIPTION',
                'specialtyGroupTitle'=>'GROUP.SPECIALTY_GROUP_TITLE',
                'specialtyQualifications_id'=>'QUALIFICATIONS.QUALIFICATION.QUALIFICATION_ID',
                'specialtyQualifications_title'=>'QUALIFICATIONS.QUALIFICATION.QUALIFICATION_TITLE',
            )
        ))->fetchAll();
        for($i=0;count($ArrayResult)>$i;$i=$i+1){
            $j=$i+1;
            if ($ArrayResult[$i]['specialtyId'] == $ArrayResult[$j]['specialtyId']) {
                $qualif['id'] = $ArrayResult[$i]['specialtyQualifications_id'];
                $qualif['title'] = $ArrayResult[$i]['specialtyQualifications_title'];
                $specialtyQualifications[] = $qualification;
            } else {
                $ArrayResultNew['specialtyId'] = $ArrayResult[$i]['specialtyId'];
                $ArrayResultNew['specialtyTitle'] = $ArrayResult[$i]['specialtyTitle'];
                $ArrayResultNew['specialtyCode'] = $ArrayResult[$i]['specialtyCode'];
                $ArrayResultNew['specialtyDescription'] = $ArrayResult[$i]['specialtyDescription'];
                $ArrayResultNew['specialtyGroupTitle'] = $ArrayResult[$i]['specialtyGroupTitle'];
                $qualif['id'] = $ArrayResult[$i]['specialtyQualifications_id'];
                $qualif['title'] = $ArrayResult[$i]['specialtyQualifications_title'];
                $specialtyQualifications[] = $qualif;
                $ArrayResultNew['specialtyQualifications']=$specialtyQualifications;
                $specialtyQualifications = array();
            }
        }
        return $ArrayResultNew;
        //return $result;
    }
}