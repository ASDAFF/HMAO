<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Spo\Site\Domains\OrganizationDomain;
//use Spo\Site\Domains\AdmissionPlanDomain;
use Spo\Site\Adapters\AdmissionPlanDomainAdapter;
use Spo\Site\Exceptions\ArgumentException;


/*======NEW=====*/
use Spo\Site\Entities\OrganizationTable;
use Spo\Site\Entities\SpecialtyTable;
use Spo\Site\Entities\AdmissionPlanTable;
use Spo\Site\Entities\AdmissionPlanEventTable;
use Spo\Site\Entities\OrganizationEmployeeTable;

class SpecialtyListComponent extends OrganizationOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('План приёма' => '');

    protected function getResult()
    {
        global $USER;
        $ArrayResul = OrganizationEmployeeTable::getList(array(
            'filter' => array(
                'USER_MODERATOR'=>$USER->GetID(),
            ),
            'select' => array(
                'USER_MODERATOR',
            )
        ))->fetchAll();

        if(count($ArrayResul)>0 and $ArrayResul[0]['USER_MODERATOR']!='' and $ArrayResul[0]['USER_MODERATOR']!=0){
            $this->arResult['NeModerator']=0;
        }
        else{
            $this->arResult['NeModerator']=1;
        }
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();
        $year = $request->get('year');
        if (empty($year))
            $year = date('Y');
        $organization = OrganizationDomain::loadByEmployeeUserId($USER->GetID());




        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();
        $admissionPlanData = $request->get('admissionPlan');
        // Если форма отправлена - пытаемся обновить план приёма
        if ($admissionPlanData) {
            if (!is_array($admissionPlanData))
                throw ArgumentException::argumentIncorrect();

            /*================= изминение данных в таблице AdmissionPlan ===========*/

            $ArrayAdmission = AdmissionPlanTable::getList(array(
                'filter' => array(
                    'ORGANIZATION_SPECIALTY_ID'=>$admissionPlanData['organizationSpecialtyId'],
                ),
                'select' => array(
                    'id'                    =>  'ADMISSION_PLAN_ID'
                )
            ))->fetchAll();
            $fetch=array_pop($ArrayAdmission); /*получение последнего значения для изминение данных*/

            $startdate = new Bitrix\Main\Type\DateTime(date('d.m.Y H:i:s',strtotime($admissionPlanData['startDate'])));
            $enddate = new Bitrix\Main\Type\DateTime(date('d.m.Y H:i:s',strtotime($admissionPlanData['endDate'])));

            $res=AdmissionPlanTable::update($fetch['id'],
                array(
                    'ADMISSION_PLAN_START_DATE'             => $startdate,
                    'ADMISSION_PLAN_END_DATE'               => $enddate,
                    'ADMISSION_PLAN_GRANT_STUDENTS_NUMBER'  => (int) $admissionPlanData['grantStudentsNumber'],
                    'ADMISSION_PLAN_TUITION_STUDENTS_NUMBER'=> (int) $admissionPlanData['tuitionStudentsNumber'],
                )
            );

            $ress=AdmissionPlanEventTable::add(
                array(
                    'ADMISSION_PLAN_ID'                     => $fetch['id'],
                    'ADMISSION_PLAN_EVENT_DATE'             => new Bitrix\Main\Type\DateTime(date('d.m.Y H:i:s')),
                    'ADMISSION_PLAN_EVENT_STATUS'           => 1,
                    'ADMISSION_PLAN_EVENT_COMMENT'          => $admissionPlanData['reason'],
                    'USER_ID'                               => $USER->GetID(),
                )
            );

            if (!$ress->isSuccess())
            {
                $errors=$ress->getErrorMessages();
            }
            if (!$res->isSuccess())
            {
                $errors=$errors.' '.$res->getErrorMessages();
            }
            if (!empty($errors)) {
                $this->arResult['errors'] = $errors;
            } else {
                $this->arResult['success'] = 'Данные успешно обновлены';
            }
            /*

                        //$res=AdmissionPlanEventTable::update()
                        //$admissionPlan = AdmissionPlanDomain::createOrUpdate($organization->getOrganizationId(), $admissionPlanData);
                        if (!$admissionPlan->validate()) {
                            $errors = $admissionPlan->getErrors();
                        } else {
                            if (!$admissionPlan->save()) {
                                throw new Main\DB\Exception('Ошибка при сохранении данных');
                            } else {
                                // Только что созданная запись не фигурирует в последующей выборке
                                D::$em->clear();
                            }
                        }

                        */
        }

        /*$admissionPlan = AdmissionPlanDomain::loadByOrganizationToYear($organization->getOrganizationId(), $year);

        $organizationSpecialties = OrganizationDomain::getOrganizationWithSpecialties($organization->getOrganizationId());*/

        $this->arResult['year'] = $year;


        $this->arResult['admissionPlan'] =  SpecialtyListComponent::admissionPlan();

    }

    static function admissionPlan(){
        global $USER;
        /*данные SPECIALTY*/
        $ArrayResul = OrganizationTable::getList(array(
            'filter' => array(
                'LOGIC' => 'OR',
                'ORGANIZATION_EMPLOYEE.USER_ID'=>$USER->GetID(),
                'ORGANIZATION_EMPLOYEE.USER_MODERATOR'=>$USER->GetID(),
            ),
            'select' => array(
                'specialtyId'                           => 'ORGANIZATION_SPECIALTY.SPECIALTY_ID',
                'specialtyCode'                         => 'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_CODE',
                'specialtyTitle'                        => 'ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_TITLE',
                'organizationSpecialtyId'               => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_ID',
                'organizationSpecialtyBaseEducation'    => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'organizationSpecialtyStudyMode'        => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE',
                'status'                                => 'ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STATUS'
            )
        ))->fetchAll();
        $inactiveSpecialties=array();
        /*данные Admission*/
        foreach ($ArrayResul as &$AR)
        {
            /*данные AdmissionPlan*/
            $ArrayAdmission = AdmissionPlanTable::getList(array(
                'filter' => array(
                    'ORGANIZATION_SPECIALTY_ID'=>$AR['organizationSpecialtyId'],
                ),
                'select' => array(
                    'grantStudentsNumber'   =>  'ADMISSION_PLAN_GRANT_STUDENTS_NUMBER',
                    'grantGroupsNumber'     =>  'ADMISSION_PLAN_GRANT_GROUPS_NUMBER',
                    'tuitionStudentsNumber' =>  'ADMISSION_PLAN_TUITION_STUDENTS_NUMBER',
                    'tuitionGroupsNumber'   =>  'ADMISSION_PLAN_TUITION_GROUPS_NUMBER',
                    'startDate'             =>  'ADMISSION_PLAN_START_DATE',
                    'endDate'               =>  'ADMISSION_PLAN_END_DATE',
                    'status'                =>  'ADMISSION_PLAN_STATUS',
                    'admissionPlanId'       =>  'ADMISSION_PLAN_ID'
                )
            ))->fetchAll();
            if (!$AR['status']) {
                $inactiveSpecialties[] = $ArrayAdmission;
                continue;
            }
            $PlanEvent=array();
            if (is_array($ArrayAdmission)) {
                foreach ($ArrayAdmission as $AA) {
                    $PlanEvent = AdmissionPlanEventTable::getList(array(
                        'filter' => array(
                            'ADMISSION_PLAN_ID' => $AA['admissionPlanId']
                        ),
                        'select' => array(
                            'date'      => 'ADMISSION_PLAN_EVENT_DATE',
                            'status'    => 'ADMISSION_PLAN_EVENT_STATUS',
                            'comment'   => 'ADMISSION_PLAN_EVENT_COMMENT',
                        )
                    ))->fetchAll();
                }
                $PlanEvent=array_pop($PlanEvent);
            }
            /*данные AdmissionPlan*/
            $AR['admissionPlan']=array_pop($ArrayAdmission);
            $AR['admissionPlan']['lastEvent']=$PlanEvent;
        }
        return  array('activeSpecialties' => $ArrayResul, 'inactiveSpecialties' => $inactiveSpecialties);

    }

}
?>