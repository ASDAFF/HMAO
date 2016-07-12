<?php
/*use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ValidatorInterface;*/

class D
{

    /**
     * @var EntityManager
     */
    //public static $em;

    /**
     * @var ValidatorInterface
     */
    //public static $v;

    /**
     *  Инициализация
     */
    /*public static function init()
    {
        //self::initAutoloader();
        //self::initEntityManager();
        //self::initValidator();
    }*/

    /**
     * Инициализация автозагрузчика сущностей и репозиториев
     */
//    private static function initAutoloader()
//    {
//        spl_autoload_register(
//            function ($class) {
//                $entityPath = realpath(__DIR__.'/../doctrine/entities/');
//                $repositoryPath = realpath(__DIR__.'/../doctrine/repositories/');
//                $class = explode('\\', $class);
//                foreach ($class as $part) {
//                    $entityPath .= '/' . $part;
//                    $repositoryPath .= '/' . $part;
//                }
//                $entityPath .= '.php';
//                $repositoryPath .= '.php';
//                if (file_exists($entityPath)) {
//                    require_once($entityPath);
//                    return true;
//                } elseif (file_exists($repositoryPath)) {
//                    require_once($repositoryPath);
//                    return true;
//                } else {
//                    return false;
//                }
//
//            },
//            true,
//            false
//        );
//    }

    private static function initEntityManager()
    {
    $bxConnectionConfig = Bitrix\Main\Application::getConnection()->getConfiguration();
    $conn = array(
        'driver' => 'pdo_mysql',
        'user' => $bxConnectionConfig['login'],
        'password' => $bxConnectionConfig['password'],
        'dbname' => $bxConnectionConfig['database'],
        'host' => $bxConnectionConfig['host'],
        'charset' => 'utf8',
    );
    }
        /*AnnotationRegistry::registerFile(
            __DIR__ . "/../../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php"
        );
        AnnotationRegistry::registerAutoloadNamespace(
            'Symfony\Component\Validator\Constraint',
            __DIR__ . '/../../vendor/symfony/validator'
        );

        $config = Setup::createAnnotationMetadataConfiguration(
            array(__DIR__. "/../doctrine/entities"),
            true,
            __DIR__ . '/../doctrine/proxies/'
        );

        $evm = null;*/
        //self::$em = EntityManager::create($conn, $config);

    /**
     * Инициализация валидатора
     */
    /*private static function initValidator()
    {*/
        /*self::$v = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();*/
    //}

} 