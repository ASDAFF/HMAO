<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
//use Doctrine\ORM\Query\Expr;
use Spo\Site\Domains\AdmissionPlanStatDomain;
use Spo\Site\Adapters\AdmissionPlanStatDomainAdapter;

class EduDepartmentOfficeIndexComponent extends EduDepartmentOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('Главная' => '');

	protected function getResult()
	{
        echo "OK";
        $year = date('Y');
        $yearAdmissionPlans = AdmissionPlanStatDomain::getAllAdmissionplans($year);
        $yearApplications = AdmissionPlanStatDomain::getApplicationsWithSpecialtiesByYear($year);

        $this->arResult['admissionPlansStat'] = AdmissionPlanStatDomainAdapter::getAdmissionPlansStatForMainPage($yearAdmissionPlans);
        $this->arResult['applicationsStat'] = AdmissionPlanStatDomainAdapter::getApplicationsStatForMainPage($yearApplications);

	}


}