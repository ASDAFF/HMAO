<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\HttpRequest;
use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Domains\OrganizationSpecialtyDomain;
use Spo\Site\Util\JsonResponse;
use Spo\Site\Util\Methodarguments\EducationalProgramArguments;
use Spo\Site\Util\CmsUser;
use Spo\Site\Util\Methodarguments\OrganizationSpecialtyLoadArguments;
use Spo\Site\Adapters\OrganizationSpecialtyDomainAdapter;
use Spo\Site\Entities\OrganizationSpecialtyTable;
use Spo\Site\Entities\OrganizationSpecialtyAdaptationTable;
use Spo\Site\Entities\Qualification2OrganizationSpecialtyTable;
use Spo\Site\Entities\OrganizationSpecialtyExamTable;

class SpecialtyAjaxComponent extends OrganizationOfficeComponent
{
    protected $componentPage = 'template';
	protected function getResult()
	{
        $_POST['data']['specialtyId']=26;
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $result = false;

        $action = $request->get('action');
        switch($action)
        {
            case 'deleteProgram':
                $result = $this->deleteEducationalProgramAction($request);
                break;
            case 'addProgram':
                $result = $this->addEducationalProgramAction($request);
                break;
            case 'loadProgram':
                $result = $this->loadEducationalProgramAction($request);
                break;
            case 'updateProgram':
                $result = $this->updateEducationalProgramAction($request);
                break;
        }

        $this->arResult['response'] = $result;
	}

    protected function deleteEducationalProgramAction(HttpRequest $request)
    {
        $user                    = CmsUser::getCurrentUser();
        $response                = new JsonResponse();
        $organizationSpecialtyId = intval($request->get('organizationSpecialtyId'));
        $organizationId          = intval($request->get('organizationId'));
        if($organizationSpecialtyId <= 0 || $organizationId <= 0){
            return $response->setErrors('Неверно указаны параметры');
        }
        $resultOK = OrganizationSpecialtyTable::delete($organizationSpecialtyId);
        $result = OrganizationSpecialtyAdaptationTable::getList(array(
            'filter' => array(
                'ORGANIZATION_SPECIALTY_ID'=>$organizationSpecialtyId,
            ),
            'select' => array(
                'ORGANIZATION_SPECIALTY_ADAPTATION_ID',
            )
        ))->fetchAll();
        foreach ($result as $item) {
            OrganizationSpecialtyAdaptationTable::delete($item['ORGANIZATION_SPECIALTY_ADAPTATION_ID']);
        }
        $result = Qualification2OrganizationSpecialtyTable::getList(array(
            'filter' => array(
                'ORGANIZATION_SPECIALTY_ID'=>$organizationSpecialtyId,
            ),
            'select' => array(
                'QUALIFICATION_ID',
            )
        ))->fetchAll();
        foreach ($result as $item) {
            Qualification2OrganizationSpecialtyTable::delete($item['QUALIFICATION_ID']);
        }
        $result = OrganizationSpecialtyExamTable::getList(array(
            'filter' => array(
                'ORGANIZATION_SPECIALTY_ID'=>$organizationSpecialtyId,
            ),
            'select' => array(
                'ORGANIZATION_SPECIALTY_EXAM_ID',
            )
        ))->fetchAll();
        foreach ($result as $item) {
            OrganizationSpecialtyExamTable::delete($item['ORGANIZATION_SPECIALTY_EXAM_ID']);
        }
        /*
        $organizationDomain = OrganizationDomain::loadByEmployeeUserId($user->getId());
        if($organizationId !== $organizationDomain->getOrganizationId()){
            return $response->setErrors('Ошибка доступа к организации');
        }

        $organizationSpecialtyDomain = OrganizationSpecialtyDomain::loadById($organizationSpecialtyId);
        $organizationSpecialtyDomain->remove();
        */
        if($resultOK){
            return $response;
        }else{
            return $response->setErrors('Ошибка удаления программы');
        }
    }

    protected function loadEducationalProgramAction(HttpRequest $request)
    {
        $user                    = CmsUser::getCurrentUser();
        $response                = new JsonResponse();
        $organizationSpecialtyId = intval($request->get('organizationSpecialtyId'));
        //$organizationId          = intval($request->get('organizationId'));

        if($organizationSpecialtyId <= 0/* || $organizationId <= 0*/){
            return $response->setErrors('Неверно указаны параметры');
        }
        $ArrayResult = OrganizationSpecialtyTable::getList(array(
            'filter' => array(
                'ORGANIZATION_SPECIALTY_ID'=>$organizationSpecialtyId,
            ),
            'group'   => array('SPECIALTY_ID'),
            'order'   => array('ORGANIZATION_SPECIALTY_EXAM.ORGANIZATION_SPECIALTY_EXAM_ID'=>'ASC','QUALIFICATION2ORGANIZATIONSPECIALTYTABLE.QUALIFICATION.QUALIFICATION_ID'=>'ASC','ORGANIZATION_SPECIALTY_ADAPTATION.ORGANIZATION_SPECIALTY_ADAPTATION_TYPE'=>'ASC'),
            'select' => array(
                'id'                      => 'ORGANIZATION_SPECIALTY_ID',
                'specialty'               => 'SPECIALTY_ID',
                'baseEducation'           => 'ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                'trainingLevel'           => 'ORGANIZATION_SPECIALTY_TRAINING_LEVEL',
                'trainingType'            => 'ORGANIZATION_SPECIALTY_TRAINING_TYPE',
                'studyMode'               => 'ORGANIZATION_SPECIALTY_STUDY_MODE',
                'studyPeriod'             => 'ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                'plannedAbiturientsCount' => 'ORGANIZATION_SPECIALTY_PLANNED_ABITURIENTS_NUMBER',
                'plannedGroupsCount'      => 'ORGANIZATION_SPECIALTY_PLANNED_GROUPS_NUMBER',
                'disciplineId'            => 'ORGANIZATION_SPECIALTY_EXAM.ORGANIZATION_SPECIALTY_EXAM_DISCIPLINE',
                'ExamId'                  => 'ORGANIZATION_SPECIALTY_EXAM.ORGANIZATION_SPECIALTY_EXAM_ID',
                'ExamType'                => 'ORGANIZATION_SPECIALTY_EXAM.ORGANIZATION_SPECIALTY_EXAM_TYPE',
                'ExamDate'                => 'ORGANIZATION_SPECIALTY_EXAM.DATE',
                'ExamAdres'               => 'ORGANIZATION_SPECIALTY_EXAM.ADRES',
                'qualificationsId'        => 'QUALIFICATION2ORGANIZATIONSPECIALTYTABLE.QUALIFICATION.QUALIFICATION_ID',
                'qualificationsTitle'     => 'QUALIFICATION2ORGANIZATIONSPECIALTYTABLE.QUALIFICATION.QUALIFICATION_TITLE',
                'adaptationTypes'         => 'ORGANIZATION_SPECIALTY_ADAPTATION.ORGANIZATION_SPECIALTY_ADAPTATION_TYPE',
            )
        ))->fetchAll();
        /*echo "<pre>";
        print_r($ArrayResult);
        echo "</pre>";*/
        $ArrayResultNew['exams']=array();
        $ArrayResultNew['qualifications']=array();
        for($i=0;count($ArrayResult)>$i;$i=$i+1){
            $j=$i+1;
            $ArrayResultNew['qualifications']=array();
            if(count($ArrayResult)>=$j) {
                if ($ArrayResult[$i]['ExamId'] == $ArrayResult[$j]['ExamId']) {
                    $exam['id'] = $ArrayResult[$i]['ExamId'];
                    $exam['disciplineId'] = $ArrayResult[$i]['disciplineId'];
                    $exam['type'] = $ArrayResult[$i]['ExamType'];
                    $exam['date'] = date("Y-m-d",strtotime($ArrayResult[$i]['ExamDate']));
                    $exam['adres'] = $ArrayResult[$i]['ExamAdres'];
                } else {
                    $exam['id'] = $ArrayResult[$i]['ExamId'];
                    $exam['disciplineId'] = $ArrayResult[$i]['disciplineId'];
                    $exam['type'] = $ArrayResult[$i]['ExamType'];
                    $exam['date'] = date("Y-m-d",strtotime($ArrayResult[$i]['ExamDate']));
                    $exam['adres'] = $ArrayResult[$i]['ExamAdres'];
                    $exams[] = $exam;
                    $ArrayResultNew['exams'] = $exams;
                }
                if ($ArrayResult[$i]['qualificationsId'] == $ArrayResult[$j]['qualificationsId']) {
                    $qval['id'] = $ArrayResult[$i]['qualificationsId'];
                    $qval['title'] = $ArrayResult[$i]['qualificationsTitle'];
                } else {
                    $qval['id'] = $ArrayResult[$i]['qualificationsId'];
                    $qval['title'] = $ArrayResult[$i]['qualificationsTitle'];
                    $qvals[] = $qval;
                    $ArrayResultNew['qualifications'] = $qvals;
                }
                if ($ArrayResult[$i]['adaptationTypes'] == $ArrayResult[$j]['adaptationTypes']) {
                    $adaptadtion = $ArrayResult[$i]['adaptationTypes'];
                } else {
                    $adaptation = $ArrayResult[$i]['adaptationTypes'];
                    $adaptations[] = $adaptation;
                    $adaptations=array_unique($adaptations);
                    $ArrayResultNew['adaptationTypes'] = $adaptations;
                }
            }
        }
        $ArrayResultNew['id']=$ArrayResult[0]['id'];
        $ArrayResultNew['specialty']=$ArrayResult[0]['specialty'];
        $ArrayResultNew['baseEducation']=$ArrayResult[0]['baseEducation'];
        $ArrayResultNew['trainingLevel']=$ArrayResult[0]['trainingLevel'];
        $ArrayResultNew['trainingType']=$ArrayResult[0]['trainingType'];
        $ArrayResultNew['studyMode']=$ArrayResult[0]['studyMode'];
        $ArrayResultNew['studyPeriod']=$ArrayResult[0]['studyPeriod'];
        $ArrayResultNew['plannedAbiturientsCount']=$ArrayResult[0]['plannedAbiturientsCount'];
        $ArrayResultNew['plannedGroupsCount']=$ArrayResult[0]['plannedGroupsCount'];
        if(count($ArrayResultNew['adaptationTypes'])>0){
            $ArrayResult['adapted']=true;
        }
        else{
            $ArrayResult['adapted']=false;
            $ArrayResultNew['adaptationTypes']=array();
        }


        return $response->setData($ArrayResultNew);

    }

    protected function addEducationalProgramAction(HttpRequest $request)
    {
        $response       = new JsonResponse();
        $data           = $request->getPost('data');
        $data           = mb_strlen($data) > 0 ? json_decode($data, true) : array();
        if(!empty($data['studyPeriod'])) {
            $organizationId = $request->get('organizationId');
            $result = OrganizationSpecialtyTable::add(array(
                'ORGANIZATION_ID' => $organizationId,
                'SPECIALTY_ID' => $data['specialtyId'],
                'ORGANIZATION_SPECIALTY_BASE_EDUCATION' => $data['specialtyBaseEducation'],
                'ORGANIZATION_SPECIALTY_STUDY_MODE' => $data['specialtyStudyMode'],
                'ORGANIZATION_SPECIALTY_TRAINING_LEVEL' => $data['trainingLevel'],
                'ORGANIZATION_SPECIALTY_TRAINING_TYPE' => $data['trainingType'],
                'ORGANIZATION_SPECIALTY_STUDY_PERIOD' => $data['studyPeriod'],
                'ORGANIZATION_SPECIALTY_PLANNED_ABITURIENTS_NUMBER' => 0,
                'ORGANIZATION_SPECIALTY_PLANNED_GROUPS_NUMBER' => 0,
                'ORGANIZATION_SPECIALTY_STATUS' => 1,
            ));
            if ($result->isSuccess()) {
                $OrganizSpesalId = $result->getId();
            }
            foreach ($data['adaptationTypes'] as $item) {
                $result = OrganizationSpecialtyAdaptationTable::add(array(
                    'ORGANIZATION_SPECIALTY_ID' => $OrganizSpesalId,
                    'ORGANIZATION_SPECIALTY_ADAPTATION_TYPE' => $item,
                ));
                if ($result->isSuccess()) {
                    $Adaptat = $result->getId();
                }
            }
            foreach ($data['qualificationList'] as $item) {
                $result = Qualification2OrganizationSpecialtyTable::add(array(
                    'ORGANIZATION_SPECIALTY_ID' => $OrganizSpesalId,
                    'QUALIFICATION_ID' => $item,
                ));
                if ($result->isSuccess()) {
                    $Adaptat = $result->getId();
                }
            }
            foreach ($data['examList'] as $item) {
                $result = OrganizationSpecialtyExamTable::add(array(
                    'ORGANIZATION_SPECIALTY_ID' => $OrganizSpesalId,
                    'ORGANIZATION_SPECIALTY_EXAM_DISCIPLINE' => $item['disciplineId'],
                    'ORGANIZATION_SPECIALTY_EXAM_TYPE' => $item['type'],
                ));
                if ($result->isSuccess()) {
                    $Adaptat = $result->getId();
                }
            }
        }
        if($result){
            return $response;
        }

    }

    protected function updateEducationalProgramAction(HttpRequest $request)
    {
        $response       = new JsonResponse();
        $data           = $request->getPost('data');
        $data           = mb_strlen($data) > 0 ? json_decode($data, true) : array();
        $organizationSpecialtyId = intval($request->get('organizationSpecialtyId'));
        if(!empty($data['studyPeriod'])) {
            $organizationId = $request->get('organizationId');
            $result = OrganizationSpecialtyTable::update($organizationSpecialtyId,array(
                'ORGANIZATION_SPECIALTY_BASE_EDUCATION' => $data['specialtyBaseEducation'],
                'ORGANIZATION_SPECIALTY_STUDY_MODE' => $data['specialtyStudyMode'],
                'ORGANIZATION_SPECIALTY_TRAINING_LEVEL' => $data['trainingLevel'],
                'ORGANIZATION_SPECIALTY_TRAINING_TYPE' => $data['trainingType'],
                'ORGANIZATION_SPECIALTY_STUDY_PERIOD' => $data['studyPeriod'],
                'ORGANIZATION_SPECIALTY_PLANNED_ABITURIENTS_NUMBER' => 0,
                'ORGANIZATION_SPECIALTY_PLANNED_GROUPS_NUMBER' => 0,
                'ORGANIZATION_SPECIALTY_STATUS' => 1,
            ));
            //--------------------------
            $result = OrganizationSpecialtyAdaptationTable::getList(array(
                'filter' => array(
                    'ORGANIZATION_SPECIALTY_ID'=>$organizationSpecialtyId,
                ),
                'select' => array(
                    'ORGANIZATION_SPECIALTY_ADAPTATION_ID',
                )
            ))->fetchAll();
            foreach ($result as $item) {
                OrganizationSpecialtyAdaptationTable::delete($item['ORGANIZATION_SPECIALTY_ADAPTATION_ID']);
            }

            $result = OrganizationSpecialtyExamTable::getList(array(
                'filter' => array(
                    'ORGANIZATION_SPECIALTY_ID'=>$organizationSpecialtyId,
                ),
                'select' => array(
                    'ORGANIZATION_SPECIALTY_EXAM_ID',
                )
            ))->fetchAll();
            foreach ($result as $item) {
                OrganizationSpecialtyExamTable::delete($item['ORGANIZATION_SPECIALTY_EXAM_ID']);
            }
            //--------------------------
            foreach ($data['adaptationTypes'] as $item) {
                $result = OrganizationSpecialtyAdaptationTable::add(array(
                    'ORGANIZATION_SPECIALTY_ID' => $organizationSpecialtyId,
                    'ORGANIZATION_SPECIALTY_ADAPTATION_TYPE' => $item,
                ));
                if ($result->isSuccess()) {
                    $Adaptat = $result->getId();
                }
            }
            foreach ($data['qualificationList'] as $item) {
                $result = Qualification2OrganizationSpecialtyTable::add(array(
                    'ORGANIZATION_SPECIALTY_ID' => $organizationSpecialtyId,
                    'QUALIFICATION_ID' => $item,
                ));
                if ($result->isSuccess()) {
                    $Adaptat = $result->getId();
                }
            }

            foreach ($data['examList'] as $item) {
                $date2=date('d.m.Y',strtotime($item['date']));
                $result = OrganizationSpecialtyExamTable::add(array(
                    'ORGANIZATION_SPECIALTY_ID' => $organizationSpecialtyId,
                    'ORGANIZATION_SPECIALTY_EXAM_DISCIPLINE' => $item['disciplineId'],
                    'ORGANIZATION_SPECIALTY_EXAM_TYPE' => $item['type'],
                    'DATE' => new \Bitrix\Main\Type\Date($date2),
                    'ADRES' => $item['adres'],
                ));
                if ($result->isSuccess()) {
                    $Adaptat = $result->getId();
                }
            }
        }
        if($result){
            return $response;
        }

    }
}
?>