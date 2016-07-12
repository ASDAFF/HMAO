<?php
namespace Spo\Site\Core;


class SPOSiteMap
{

	public static $map = array(
		'abiturient-office' => array(
			'root' => '/abiturient-office/',
			'applicationList' => 'index.php',
			'createApplication' => 'create/#APPLICATION_ID#/',
			'deleteApplication' => 'delete/#APPLICATION_ID#/',
			'editApplication' => 'edit/#APPLICATION_ID#/',
			'abiturientProfile' => 'profile/'
		)
	);

	public static function getMap()
	{

		return self::$map;
	}


}