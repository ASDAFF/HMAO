<?php
$_SERVER["DOCUMENT_ROOT"] = __DIR__.'/../';
require_once __DIR__.'/../bitrix/modules/main/include/prolog_before.php';
require_once __DIR__.'/modules/spo.site/lib/core/d.php';
D::init();

/** @var $em \Doctrine\ORM\EntityManager */
$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper(D::$em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper(D::$em)
));

return $helperSet;