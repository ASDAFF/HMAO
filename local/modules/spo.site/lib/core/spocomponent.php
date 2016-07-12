<?
namespace Spo\Site\Core;

use \Bitrix\Main;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Security\SecurityException;
use \Bitrix\Main\Localization\Loc;
use \Spo\Site\Domains\OrganizationDomain;
use \Spo\Site\Domains\UserDomain;
use CBitrixComponent;
use CComponentEngine;
use Bitrix\Main\Config\ConfigurationException;
use Bitrix\Main\Page\Asset;
use D;
use Spo\Site\Exceptions\SpoException;
use Spo\Site\Exceptions\AccessException;
use Spo\Site\Util\EchoDoctrineLogger;

abstract class SPOComponent extends CBitrixComponent
{
    public function onIncludeComponentLang()
    {
        Loc::loadMessages(__DIR__ . '../messages.php');
    }

    // Для задания путей "по-умолчанию" для работы в ЧПУ режиме
    protected $arDefaultUrlTemplates404 = array(
    );

    protected $pageTitle = '';
    protected $breadcrumbs = array();

    // Для задания псевдонимов "по-умолчанию" переменных в режиме ЧПУ. Как правило массив пустой
    protected $arDefaultVariableAliases404 = array();

    protected $arUrlTemplates = array();

    // Для задания псевдонимов "по-умолчанию" переменных с выключенным ЧПУ. Как правило массив пустой
    protected $arDefaultVariableAliases = array();

    // Массив, хранящий значения переменных после обработки запроса
    protected $arVariables = array();
    // Массив, хранящий соответствие переменных псевдонимам после обработки запроса
    protected $arVariableAliases = array();
    // Список всех переменных, которые может принимать компонент из параметров GET запроса
    protected $arAllowedComponentVariables = array();
    // Имя шаблона, который будет использоваться (можно сразу задать значение по умолчанию)
    protected $componentPage = 'index';

    abstract protected function getResult();

    /**
     * Проверяет, авторизован ли пользователь и относится ли он к группе "абитуриенты". В составных компонентах
     * комплексного компонента в дальнейшем эта проверка не проводится
     *
     * @throws AccessException
     */
    abstract protected function checkUserAccess();

    /**
     * Проверяет, переданы ли компоненты необходимые для работы параметры в массиве $arParams. В основном для
     * использования в составных компонентах комплексного компонента
     *
     * @return SpoException/ArgumentException
     */
    abstract protected function checkParams();

    /*public function onPrepareComponentParams($arParams)
    {
        global $APPLICATION;

        $urlParams = explode('/', trim($APPLICATION->GetCurPage(true), '/'));

        if(count($urlParams) > 0 && (!isset($arParams['SEF_FOLDER']) || mb_strlen($arParams['SEF_FOLDER']) === null)){
            $arParams['SEF_FOLDER'] = '/' . $urlParams[0] . '/';
        }

        $arParams['SEF_MODE'] = 'Y';

        return parent::onPrepareComponentParams($arParams);
    }*/

    public function onBeforeExecuteComponent()
    {

    }

    public function executeComponent()
    {
        if(\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('logsql') !== null){
            $logger = new \Spo\Site\Util\EchoDoctrineLogger();
            D::$em->getConfiguration()->setSQLLogger($logger);
        }
        $this->onBeforeExecuteComponent();
        Loader::includeModule('spo.site');

        try {
            $this->checkUserAccess();
        } catch (SpoException $ex) {
            echo $ex;
            return false;
        }

        $this->checkParams();

        if ($this->arParams['SEF_MODE'] === 'N') {
            throw new ConfigurationException(Loc::getMessage('SEF_MODE_DISABLED'));
        }

        // Формируем параметры ЧПУ на основе значений "по-умолчанию" и значений, заданных настройкой компонента
        $this->arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates(
            $this->arDefaultUrlTemplates404,
            $this->arParams['SEF_URL_TEMPLATES']
        );

        // Формируем список соответствия имен переменных их псевдонимам
        $this->arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
            $this->arDefaultVariableAliases404,
            $this->arParams['VARIABLE_ALIASES']
        );

        // Определеяем, какому шаблону пути соответствует запрошенный пользователем URL.
        // Если соответствующий шаблон был найден, то возвращается его код, иначе возвращается пустая строка.
        // В arVariables при этом сохраняется массив значений переменных компонента (без учёта псевдонимов)
        $componentPage = CComponentEngine::ParseComponentPath(
            $this->arParams['SEF_FOLDER'],
            $this->arUrlTemplates,
            $this->arVariables
        );

        // Если ParseComponentPath не нашёл соответствия запрошенному URL и вернул пустую строку,
        // то восстанавливаем значение "по умолчанию"
        $this->componentPage = (strlen($componentPage) <= 0) ? $this->componentPage : $componentPage;

        // Заполняем массив arVariables значениями переменных из GET запроса, учитывая заданные псевдонимы.
        // Обрабатываются только те переменные, имена которых перечислены в arAllowedComponentVariables
        CComponentEngine::InitComponentVariables(
            $this->componentPage,
            $this->arAllowedComponentVariables,
            $this->arVariableAliases,
            $this->arVariables
        );

        $this->arResult = array(
            'FOLDER' => $this->arParams['SEF_FOLDER'],
            'URL_TEMPLATES' => $this->arUrlTemplates,
            'VARIABLES' => $this->arVariables,
            'ALIASES' => $this->arVariableAliases,
        );

        try {
            $this->getResult();
        } catch(SpoException $ex) {
            echo $ex;
            return false;
        }

        global $APPLICATION;
        if (!empty($this->pageTitle))
            $APPLICATION->SetTitle($this->pageTitle);

        foreach ($this->breadcrumbs as $title => $url)
            $APPLICATION->AddChainItem($title, $url);

        $this->includeComponentTemplate($this->componentPage);

    }
}
?>