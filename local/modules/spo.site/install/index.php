<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 21.05.15
 * Time: 11:42
 */
IncludeModuleLangFile(__FILE__);

if (class_exists('spo_site')) {
    return;
}

// название класса менять нельзя!!
class spo_site extends CModule
{
    public $MODULE_ID = 'spo.site';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME = 'O P';
    public $PARTNER_URI = '';

    public $START_TYPE = 'WINDOW';
    public $WIZARD_TYPE = "INSTALL";

    public function __construct() // SpoSite
    {
        //$arModuleVersion = array();
        //include(__DIR__ . '/version.php');
        $this->MODULE_VERSION      = '0.1';
        $this->MODULE_VERSION_DATE = '21.05.2015';
        $this->MODULE_NAME         = 'СПО сайт';
        $this->MODULE_DESCRIPTION  = 'СПО';
    }

    public function DoInstall()
    {
        // TODO
        // 1 - создание почтовых шаблонов
        // 2 - создание групп пользователей
        // 3 - создание базы
        // 4 - ..
        $this->installDB();
        $this->InstallEvents();
        $this->InstallFiles();
    }

    public function DoUninstall()
    {
        $this->unInstallDB();
        $this->UnInstallEvents();
        $this->UninstallFiles();
    }

    function InstallEvents($arParams = array())
    {
        //todo включить, когда появится необходимость в меню в админке
        //RegisterModuleDependences('main', 'OnBuildGlobalMenu', $this->MODULE_ID, 'SpoEventHandler', 'onBuildGlobalMenu');


        // Обработчик события регистрации нового пользователя. Выполняется ДО регистрации пользователя. При неудачном завершении
        // пользователь зарегистрирован не будет. Не выполняется при регистрации пользователя из административной части
        RegisterModuleDependences('main', 'OnBeforeUserRegister', $this->MODULE_ID, 'UserRegistrationHandlers', 'beforeUserRegister');


        // Выполняется и при создании пользователя из административной части, и при самостоятельной регистрации пользователя.
        RegisterModuleDependences('main', 'OnAfterUserAdd', $this->MODULE_ID, 'UserRegistrationHandlers', 'afterUserCreation');


        // Выполняется после регистрации пользователя в системе - только если пользователь регистрировался самостоятельно
        // RegisterModuleDependences('main', 'OnAfterUserRegister', $this->MODULE_ID, 'UserRegistrationHandlers', 'checkUserIsActive');

        // Выполняется перед авторизацией пользователя
        //RegisterModuleDependences('main', 'OnBeforeUserLogin', $this->MODULE_ID, 'UserRegistrationHandlers', 'checkUserIsActive');

        // Выполняется после попытки обновить профиль пользователя
        RegisterModuleDependences('main', 'OnAfterUserUpdate', $this->MODULE_ID, 'UserRegistrationHandlers', 'afterUserUpdate');

        // Выполняется перед попыткой обновить профиль пользователя
        RegisterModuleDependences('main', 'OnBeforeUserUpdate', $this->MODULE_ID, 'UserRegistrationHandlers', 'beforeUserUpdate');

        return true;
    }

    function UnInstallEvents($arParams = array())
    {
        //todo включить, когда появится необходимость в меню в админке
        //UnRegisterModuleDependences('main', 'OnBuildGlobalMenu',    $this->MODULE_ID, 'SpoEventHandler',          'onBuildGlobalMenu');
        UnRegisterModuleDependences('main', 'OnBeforeUserRegister', $this->MODULE_ID, 'UserRegistrationHandlers', 'beforeUserRegister');
        UnRegisterModuleDependences('main', 'OnAfterUserAdd',       $this->MODULE_ID, 'UserRegistrationHandlers', 'afterUserCreation');
        //UnRegisterModuleDependences('main', 'OnAfterUserRegister',  $this->MODULE_ID, 'UserRegistrationHandlers', 'checkUserIsActive');
        //RegisterModuleDependences('main', 'OnBeforeUserLogin', $this->MODULE_ID, 'UserRegistrationHandlers', 'checkUserIsActive');
        UnRegisterModuleDependences('main', 'OnAfterUserUpdate',    $this->MODULE_ID, 'UserRegistrationHandlers', 'afterUserUpdate');
        return true;
    }

    function InstallDB()
    {
        RegisterModule($this->MODULE_ID);
    }

    function UnInstallDB()
    {
        UnRegisterModule($this->MODULE_ID);
    }

    function InstallFiles(){
        chdir(__DIR__ . '/../../../');
        symlink('modules/spo.site/vendor/bin/doctrine', 'doctrine');
        copy(__DIR__ . '/cli-config.php', __DIR__ . '/../../../cli-config.php');
    }

    function UninstallFiles(){
        unlink(__DIR__ . '/../../../doctrine');
        unlink(__DIR__ . '/../../../cli-config.php');

    }
}