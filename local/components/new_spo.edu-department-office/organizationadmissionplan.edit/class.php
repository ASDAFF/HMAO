<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;

use Spo\Site\Domains\AdmissionPlanDomain;
use Spo\Site\Adapters\AdmissionPlanDomainAdapter;
use Spo\Site\Util\UiMessage;


class OrganizationAdmissionPlanEditComponent extends EduDepartmentOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('Утверждение плана приёма' => '');
    protected $pageTitle = 'Утверждение плана приёма';

	protected function getResult()
	{
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();
        $admissionPlanId = $request->get('admissionPlanId');

        // Если пришла форма, нужно поменять статус приёма
        $admissionPlanForm = $request->get('admissionPlan');
        if ($admissionPlanForm) {

            $newStatus = isset($admissionPlanForm['status']) ? $admissionPlanForm['status'] : '';
            $reason = !empty($admissionPlanForm['reason']) ? $admissionPlanForm['reason'] : '';
            $domain = AdmissionPlanDomain::changeStatus($admissionPlanId, $newStatus, $reason);
            /*if (!$domain->validate()) {
                $errors = $domain->getErrors();
                foreach ($errors as $error)
                    UiMessage::addMessage($error['message'], UiMessage::TYPE_ERROR);
            }
            elseif (!$domain->save()) {
                throw new Main\DB\Exception('Ошибка при сохранении данных');
            }*/
        }
        $this->arResult['data'] = AdmissionPlanDomainAdapter::getAdmissionPlanInfo(
            //AdmissionPlanDomain::loadAdmissionPlanInfoById($admissionPlanId)
            $admissionPlanId
        );
	}

}