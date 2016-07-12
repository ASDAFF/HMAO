<?php

define('SPO_DEV_MODE', false);

CModule::AddAutoloadClasses('spo.site',
    array(
        'D' => 'lib/core/d.php',
        'SpoEventHandler' => 'classes/spoeventhandler.php',
        'UserRegistrationHandlers' => 'lib/util/userregistrationhandlers.php',
        'UserRegistrationHandlers' => 'lib/util/userregistrationhandlers.php',
    )
);


//function SpoSiteAutoloader($class) {
//    $doctrinePath = 'doctrine/';
//    $filePathArray = $arFile = explode("\\", $class);
//    $fileName = array_pop($filePathArray) . '.php';
//
//    if (file_exists($doctrinePath . 'entities/' . $fileName)){require_once $doctrinePath . 'entities/' . $fileName; return;}
//    if (file_exists($doctrinePath . 'proxies/' . $fileName)){require_once $doctrinePath . 'proxies/' . $fileName; return;}
//    if (file_exists($doctrinePath . 'repositories/' . $fileName)){require_once $doctrinePath . 'repositories/' . $fileName; return;}
//}

function SpoSiteAutoloader($class) {
    //$doctrinePath = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/spo.site/lib/doctrine/';
    $entityPath = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/spo.site/lib/entities/';
    $filePathArray = $arFile = explode("\\", $class);
    $fileName = array_pop($filePathArray) . '.php';

    if (file_exists($doctrinePath . 'entities/' . $fileName)){require_once $doctrinePath . 'entities/' . $fileName; return;}
    if (file_exists($doctrinePath . 'proxies/' . $fileName)){require_once $doctrinePath . 'proxies/' . $fileName; return;}
    if (file_exists($doctrinePath . 'repositories/' . $fileName)){require_once $doctrinePath . 'repositories/' . $fileName; return;}
    if (file_exists($entityPath . $fileName)){require_once $entityPath . $fileName; return;}
}

spl_autoload_register('SpoSiteAutoloader');

require_once 'vendor/autoload.php';
//D::init();

//$classLoader = new Doctrine\Common\ClassLoader('DoctrineExtensions', __DIR__ . '/vendor');
//$classLoader->register();


