<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
/*use Spo\Site\Doctrine\Entities\Application;
use Spo\Site\Doctrine\Entities\Organization;
use Spo\Site\Doctrine\Entities\OrganizationSpecialty;
use Spo\Site\Doctrine\Entities\AdmissionPlan;*/

use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\ApplicationFundingType;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr;

class AdmissionPlanViewComponent extends EduDepartmentOfficeComponent
{
    protected $componentPage = 'template';
    protected $breadcrumbs = array('Главная' => '');


	protected function getResult()
	{
        echo "OK";
        //$this->arResult['planData'] = $this->getPlanTest();
	}

    /*public function getRequestListTest()
    {
        $qb = D::$em->createQueryBuilder();
        $qb
            ->select('a')
            ->from('Spo\Site\Doctrine\Entities\Application', 'a')
            ->setMaxResults(10);
        //var_dump($qb->getDQL());
        $query = $qb->getQuery();
        //var_dump($query->getSQL());
//        \Spo\Site\Util\CVarDumper::dump($query->getResult(AbstractQuery::HYDRATE_ARRAY));
    }

    public function getPlanTest()
    {
        //echo "OK";
        $year = 2015;
        D::$em->getConfiguration()->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');


        //todo если требуется вывести только КЦП, то запрос можно облегчить убрав джоин Application и подсчет кол-ва заявок
        //todo пока не понятно, лучше ли сделать параметрами или вынести в другой метод
        $qb = D::$em->createQueryBuilder();
        $qb
            ->select(array(
                'org.organizationId',
                'org.organizationName',
                'orgSpec.organizationSpecialtyId',
                'orgSpec.baseEducation', // 9/11
                'orgSpec.studyMode', // очн/заочн/очн-заочн
                'orgSpec.trainingLevel', // базовый/углубленный
                'orgSpec.studyPeriod', // период обучения
                'spec.specialtyTitle', // название специальности
                'spec.specialtyId',   // id специальности
                'spec.specialtyCode', // код специальности
                'ql.qualificationTitle', // квалификация
                'plan.grantStudentsNumber',
                'plan.grantGroupsNumber',
                'plan.tuitionStudentsNumber',
                'plan.tuitionGroupsNumber',
                'app.applicationFundingType',
                'count(app.applicationId) as reqCount',
            ))
            ->from('Spo\Site\Doctrine\Entities\Organization', 'org')

            ->leftJoin('Spo\Site\Doctrine\Entities\OrganizationSpecialty', 'orgSpec',
                Expr\Join::WITH, 'org.organizationId = orgSpec.organizationId')

            ->leftJoin('Spo\Site\Doctrine\Entities\Qualification2OrganizationSpecialty', 'ql2os',
                Expr\Join::WITH, 'ql2os.organizationSpecialtyId = orgSpec.organizationSpecialtyId')

            ->leftJoin('Spo\Site\Doctrine\Entities\Qualification', 'ql',
                Expr\Join::WITH, 'ql2os.qualificationId = ql.qualificationId')

            ->leftJoin('Spo\Site\Doctrine\Entities\Specialty', 'spec',
                Expr\Join::WITH, 'spec.specialtyId = orgSpec.specialtyId')

            ->leftJoin('Spo\Site\Doctrine\Entities\AdmissionPlan', 'plan',
                Expr\Join::WITH, 'plan.organizationSpecialtyId = orgSpec.organizationSpecialtyId')

            ->leftJoin('Spo\Site\Doctrine\Entities\Application', 'app',
                Expr\Join::WITH, 'app.organizationSpecialtyId = orgSpec.organizationSpecialtyId')

            // чтобы не джойнить таблицу application 2 раза, сгруппировал по типу финансирования
            ->addGroupBy('app.applicationFundingType')
            ->addGroupBy('orgSpec.organizationSpecialtyId')
            ->addGroupBy('org.organizationId')
            ->addOrderBy('org.organizationId')
            ->andWhere('YEAR(plan.startDate) = :year')
            ->setParameter('year', $year);
            //->setMaxResults(1000);

        //var_dump($qb->getDQL());
        $query = $qb->getQuery();
        //var_dump($query->getSQL());

        //sql запрос (ничего не возвращает)
        //$rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        //$q  = D::$em->createNativeQuery($query->getSQL(), $rsm);
        //\Spo\Site\Util\CVarDumper::dump($q->getResult());exit;

        //sql запрос без rsm (выставляет рандомные алиасы для колонок)
        //$q = D::$em->getConnection()->prepare($query->getSQL());
        //$q->execute();
        //\Spo\Site\Util\CVarDumper::dump($q->fetchAll());exit;


//$query->execute()
        $statData = $query->execute();
        $resultData = array();

        foreach($statData as $orgStat)
        {
            $orgId = $orgStat['organizationId'];
            $specId = $orgStat['specialtyId'];
            $orgSpecId = $orgStat['organizationSpecialtyId'];

            // если нет данных по организации, создаем
            if(!isset($resultData[$orgId]))
            {
                $resultData[$orgId] = array(
                    'organizationName'  => $orgStat['organizationName'],
                    'orgSpecCnt'        => 0,
                    'specialties'       => array(),
                );
            }

            // если нет специальностей, дельше не проходим
            if(empty($specId))
            {
                continue;
            }

            // если нет данных по специальности создаем
            if(!isset($resultData[$orgId]['specialties'][$specId]))
            {
                $resultData[$orgId]['specialties'][$specId] = array(
                    'specialtyTitle' => $orgStat['specialtyTitle'],
                    //'specialtyId'    => $orgStat['specialtyId'],
                    'specialtyCode'  => $orgStat['specialtyCode'],
                    'organizationSpecialties' => array(),
                );
            }

            // из-за группировки по типу финансирования одна учебная программа может приходить 2 раза
            // отличасться будет только типом финансирования и кол-вом поданных заявок
            if(isset($resultData[$orgId]['specialties'][$specId]['organizationSpecialties'][$orgSpecId]))
            {
                $resultData[$orgId]['specialties'][$specId]['organizationSpecialties'][$orgSpecId]
                    [
                        $orgStat['applicationFundingType'] === ApplicationFundingType::GRANT
                        ? 'grantReqCount' : 'tuitionReqCount'
                    ] = intval($orgStat['reqCount']);
            }

            // запись данных учебной программы
            $resultData[$orgId]['specialties'][$specId]['organizationSpecialties'][$orgSpecId] = array(
                'baseEducation'           => BaseEducation::getShortValue($orgStat['baseEducation']),
                'studyMode'               => StudyMode::getShortValue($orgStat['studyMode']),
                'trainingLevel'           => TrainingLevel::getValue($orgStat['trainingLevel']),
                'studyPeriod'             => $orgStat['studyPeriod'],
                'qualificationTitle'      => $orgStat['qualificationTitle'],

                'grantStudentsNumber'     => intval($orgStat['grantStudentsNumber']),
                'grantGroupsNumber'       => intval($orgStat['grantGroupsNumber']),
                'tuitionStudentsNumber'   => intval($orgStat['tuitionStudentsNumber']),
                'tuitionGroupsNumber'     => intval($orgStat['tuitionGroupsNumber']),
                'grantReqCount'           => $orgStat['applicationFundingType'] === ApplicationFundingType::GRANT
                        ? intval($orgStat['reqCount']) : 0,
                'tuitionReqCount'         => $orgStat['applicationFundingType'] === ApplicationFundingType::PAID
                        ? intval($orgStat['reqCount']) : 0,
            );
            $resultData[$orgId]['orgSpecCnt']++;
        }

        //\Spo\Site\Util\CVarDumper::dump($resultData);exit;

        return $resultData;
    }*/
}