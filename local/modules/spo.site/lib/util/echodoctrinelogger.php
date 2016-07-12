<?php
namespace Spo\Site\Util;
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 07.05.15
 * Time: 16:08
 */
use Doctrine\DBAL\Logging\SQLLogger;
use Spo\Site\Util\CVarDumper;

class EchoDoctrineLogger implements SQLLogger
{
    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        CVarDumper::dump($sql);
        //echo $sql . PHP_EOL;

        if ($params) {
            echo '<br/>params: ';
            CVarDumper::dump($params);
        }

        if ($types) {
            echo '<br/>types: ';
            CVarDumper::dump($types);
        }

        echo '<br/>';
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
    }
}