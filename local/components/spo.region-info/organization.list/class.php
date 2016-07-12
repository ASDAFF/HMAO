<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Spo\Site\Adapters\OrganizationDomainAdapter;
use Spo\Site\Adapters\CityDomainAdapter;
use Spo\Site\Adapters\SpecialtyDomainAdapter;
//use Spo\Site\Domains\OrganizationDomain;
use Spo\Site\Domains\CityDomain;
//use Spo\Site\Domains\SpecialtyDomain;
use Spo\Site\Dictionaries\OrganizationStatus;
use Spo\Site\Entities\OrganizationTable;

// Если нужно использовать подкомпоненты комплексного компонента отдельно, нужно явно подгрузить для
// них класс комплексног окомпонента, так как они унаследованы от него. Надо подумать.
require_once(__DIR__ . '/../main/class.php');

class OrganizationListComponent extends RegionInfo
{

    protected $componentPage = '';
    protected $pageTitle = 'Образовательные организации';
    protected $breadcrumbs = array(
        'Образовательные организации' => '',
    );

	protected function getResult()
	{
		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		$organizationFilter = $request->get('organizationFilter');
		$this->arResult['organizations'] = OrganizationDomainAdapter::listOrganizations(
			//OrganizationDomain::getOrganizationsList($organizationFilter, OrganizationStatus::ENABLED)
		);
		$this->arResult['citiesList'] = CityDomainAdapter::listCities(
		//CityDomain::getCitiesList()
		);
		$this->arResult['specialtiesList'] = SpecialtyDomainAdapter::listSpecialties(
		//SpecialtyDomain::getSpecialtiesList()
		);
		preg_match_all("/[^0-9\(\)\-\s\+]*([0-9\(\)\-\s\+]+)[^0-9\(\)\-\s\+]*/", $_GET['search'], $Telephone);
		$TelephoneArr=$Telephone[1];
		preg_match_all("/[^A-zА-я\.\-\/\:]*(http\:\/\/[A-zА-я\.\-\/]*)[^A-zА-я\.\-\/\:]*/", $_GET['search'], $Sait);
		$SaitArr=$Sait[1];
		$Stroka_Arr=preg_split("/[\s]+/",$_GET['search']);
		$SpecialitImvalid=array("any"=>"Да",
                          "1"=>"Для слабовидящих обучающихся",
                          "2"=>"Для слепых обучающихся",
                          "3"=>"Для слабослышащих",
		                  "4"=>"Для глухих",
                          "5"=>"Для слабослышащих обучающихся, имеющих сложную структуру дефекта (нарушение слуха и задержка психического развития)",
		                  "6"=>"Для обучающихся, имеющих нарушения опорно-двигательного аппарата",
                          "7"=>"Для обучающихся, имеющих тяжелые нарушения речи",
                          "8"=>"Для обучающихся с задержкой психического развития",
	                      "9"=>"Для обучающихся с умственной отсталостью",
	                      "10"=>"Для обучающихся с умственной отсталостью, имеющих сложную структуру дефекта",
	                      "11"=>"Для обучающихся с иными ограничениями здоровья",
	    );
		$ArraStudyMode=array(
			                  "1"=>'Очная форма обучения',
							  "2"=>'Заочная форма обучения',
			                  "3"=>'Очно-заочная форма обучения',
			                  );
		$ArraSelectbaseeducation=array(
			                  '1'=>'Основное общее образование (9 классов)',
			                  '2'=>'Среднее (полное) общее образование (11 классов)',
		);
		$ArraTrainingType=array(
			                  '1'=>'Программа подготовки специалистов среднего звена',
			                  '2'=>'Программа подготовки квалифицированных рабочих, служащих',
		);
		//Тест111
		for($i=0;count($Stroka_Arr)>$i;$i=$i+1){
			    //Ищем Города
				foreach($this->arResult['citiesList'] as $item){
					if(!empty($Stroka_Arr[$i])) {
						if (preg_match("/" . addslashes(strtolower($Stroka_Arr[$i])) . "/", strtolower($item['name']))) {
							$ArraCitiList[] = $item['id'];
						}
					}
				}
			    //Ищем Специальност
			    foreach($this->arResult['specialtiesList'] as $item){
					if(!empty($Stroka_Arr[$i])) {
						if (preg_match("/" . addslashes(strtolower($Stroka_Arr[$i])) . "/", strtolower($item['code']))) {
							$ArraCodeList[] = $item['id'];
						}
						if (preg_match("/" . addslashes(strtolower($Stroka_Arr[$i])) . "/", strtolower($item['title']))) {
							$ArraTitleList[] = $item['id'];
						}
					}
			    }
			    //Ищем Форму обучение
			    foreach($ArraStudyMode as $key=>$item){
					if(!empty($Stroka_Arr[$i])) {
						if (preg_match("/" . addslashes(strtolower($Stroka_Arr[$i])) . "/", strtolower($item))) {
							$ArraStudyModeList[] = $key;
						}
					}
			    }
			    //Ищем программу подготовки на базе
			    foreach($ArraSelectbaseeducation as $key=>$item){
					if(!empty($Stroka_Arr[$i])) {
						if (preg_match("/" . addslashes(strtolower($Stroka_Arr[$i])) . "/", strtolower($item))) {
							$ArraSelectbaseeducationList[] = $key;
						}
					}
			    }
			    //Ищем Программу обучения
			    foreach($ArraTrainingType as $key=>$item){
					if(!empty($Stroka_Arr[$i])) {
						if (preg_match("/" . addslashes(strtolower($Stroka_Arr[$i])) . "/", strtolower($item))) {
							$ArraTrainingTypeList[] = $key;
						}
					}
			    }
			    //Ищем Инвалидов
			    foreach($SpecialitImvalid as $key=>$item){
					if(!empty($Stroka_Arr[$i])) {
						if (preg_match("/" . addslashes(strtolower($Stroka_Arr[$i])) . "/", strtolower($item))) {
							$ArraInvalidList[] = $key;
						}
					}
			    }
		}
		if(isset($ArraCitiList) and !empty($_GET['search'])){
			$FILES['LOGIC']='OR';
			$FILES['=CITY_ID']=$ArraCitiList;
		}
		if(isset($ArraCodeList) and !empty($_GET['search'])){
			$ArraCodeList=array_unique($ArraCodeList);
			$FILES['LOGIC']='OR';
			$FILES['=ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID'] =$ArraCodeList;
		}
		if(isset($ArraTitleList) and !empty($_GET['search'])){
			$ArraTitleList=array_unique($ArraTitleList);
			$FILES['LOGIC']='OR';
			$FILES['=ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID'] =$ArraTitleList;
		}
		if(isset($ArraStudyModeList) and !empty($_GET['search'])){
			$ArraStudyModeList=array_unique($ArraStudyModeList);
			$FILES['LOGIC']='OR';
			$FILES['=ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE'] =$ArraStudyModeList;
		}
		if(isset($ArraSelectbaseeducationList) and !empty($_GET['search'])){
			$ArraSelectbaseeducationList=array_unique($ArraSelectbaseeducationList);
			$FILES['LOGIC']='OR';
			$FILES['=ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION'] =$ArraSelectbaseeducationList;
		}
		if(isset($ArraTrainingTypeList) and !empty($_GET['search'])){
			$ArraTrainingTypeList=array_unique($ArraTrainingTypeList);
			$FILES['LOGIC']='OR';
			$FILES['=ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_TYPE'] =$ArraTrainingTypeList;
		}
		if(isset($ArraInvalidList) and !empty($_GET['search'])){
			$ArraInvalidList=array_unique($ArraInvalidList);
			$FILES['LOGIC']='OR';
			$FILES['=ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_ADAPTATION.ORGANIZATION_SPECIALTY_ADAPTATION_TYPE'] =$ArraInvalidList;
		}
		if(!empty($TelephoneArr) and !empty($_GET['search'])){
			$TelephoneArr=array_diff($TelephoneArr, array(0, null," ","-",".",","));
			$TelephoneArr=array_unique($TelephoneArr);
			if(!empty($TelephoneArr)){
				$FILES['LOGIC'] = 'OR';
				$FILES['%ORGANIZATION_PHONE'] = $TelephoneArr;
			}
		}
		if(!empty($SaitArr) and !empty($_GET['search'])){
			$SaitArr=array_unique($SaitArr);
			$FILES['LOGIC']='OR';
			$FILES['%ORGANIZATION_SITE'] =$SaitArr;
		}
		if(!empty($Stroka_Arr) and !empty($_GET['search'])){
			$Stroka_Arr=array_unique($Stroka_Arr);
			$FILES['LOGIC']='OR';
			$FILES['%ORGANIZATION_NAME'] =$Stroka_Arr;
			$FILES['%ORGANIZATION_ADDRESS'] =$Stroka_Arr;
		}


		if(!empty($organizationFilter['name'])){
			$FILES['%ORGANIZATION_NAME'] = $organizationFilter['name'];
		}
		if(!empty($organizationFilter['city'])){
			$FILES['=CITY_ID'] = $organizationFilter['city'];
		}
		if(!empty($organizationFilter['specialty'])){
			$FILES['=ORGANIZATION_SPECIALTY.SPECIALITY.SPECIALTY_ID'] = $organizationFilter['specialty'];
		}
		if(!empty($organizationFilter['studyMode'])){
			$FILES['=ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_STUDY_MODE'] = $organizationFilter['studyMode'];
		}
		if(!empty($organizationFilter['baseEducation'])){
			$FILES['=ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_BASE_EDUCATION'] = $organizationFilter['baseEducation'];
		}
		if(!empty($organizationFilter['trainingType'])){
			$FILES['=ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_TRAINING_TYPE'] = $organizationFilter['trainingType'];
		}
		if(!empty($organizationFilter['adaptationType'])){
			$FILES['=ORGANIZATION_SPECIALTY.ORGANIZATION_SPECIALTY_ADAPTATION.ORGANIZATION_SPECIALTY_ADAPTATION_TYPE'] = $organizationFilter['adaptationType'];
		}
		$ArrFILES[0]=$FILES;
		$ArrFILES['=ORGANIZATION_STATUS']=OrganizationStatus::ENABLED;
		$ArrayResult = OrganizationTable::getList(array(
			'filter' => $ArrFILES,
			'group'   => array('ORGANIZATION_ID'),
			//'order'   => array('ORGANIZATION_SPECIALTY.SPECIALTY_ID'=>'ASC'),
			'select' => array(
				'id' => 'ORGANIZATION_ID',
				'city' => 'CITY.CITY_NAME',
				'name' => 'ORGANIZATION_NAME',
				'phone' => 'ORGANIZATION_PHONE',
				'site' => 'ORGANIZATION_SITE',
				'address' => 'ORGANIZATION_ADDRESS',
			)
		))->fetchAll();
		$this->arResult['organizations']=$ArrayResult;

	}

}
?>