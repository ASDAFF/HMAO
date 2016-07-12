<?php
namespace Spo\Site\Core;
use Bitrix\Main\ArgumentException;
use CBitrixComponent;
use Spo\Site\Util\CVarDumper;

abstract class SpoUrlHelper
{
	abstract function getComponentName();

	public static function getComponentUrl($action, $params = array())
	{
		return self::getUrl(static::getComponentName(), $action, $params);
	}

	public static function getUrl($componentName, $action, $params = array())
	{
		$componentClassName = self::transformClassNameFromComponentName($componentName);


		// Если данный класс ещё не подгружен (например, формируем ссылку из одного компонента на другой), необходимо
		// его подгрузить.
		if (!class_exists($componentClassName)) {
			$localSPOComponentPath = $_SERVER['DOCUMENT_ROOT'] . '/local/components/spo.' . $componentName . '/main/class.php';
			if (file_exists($localSPOComponentPath)) {
				include_once($localSPOComponentPath);
			} else {
				throw new ArgumentException('Не найден компонент ' . $componentName);
			}
		}

		if (!class_exists($componentClassName))
			throw new \LogicException('Не найден класс ' . $componentClassName);

		$url = '';
		$component = new $componentClassName;

		$url .= $component->componentRootUrl;

		$componentUrlTemplates = $component->arDefaultUrlTemplates404;

		if (!isset($componentUrlTemplates[$action]))
			throw new ArgumentException('Действие ' . $action . ' компонента ' . $componentName . 'не определено');

		$url .= $componentUrlTemplates[$action];

		$url = self::addParams($url, $params);

		return $url;

	}

	private static function transformClassNameFromComponentName($componentName)
	{
		// Преобразуем имя компонента в CamelCase и получаем таким образом имя класса

		// 'word1-word2-word3' => 'word1 word2 word 3'
		$componentClassName = str_replace('-', ' ', $componentName);
		// 'word1 word2 word 3' => 'Word1 Word2 Word3'
		$componentClassName = ucwords($componentClassName);
		// 'Word1 Word2 Word3' => 'Word1Word2Word3'
		return str_replace(' ', '', $componentClassName);
	}

	private static function addParams($url, $params)
	{
		$urlGETParams = array();

		foreach ($params as $paramName => $paramValue) {
			$replacedCount = 0;
			$url = str_replace('#' . $paramName . '#', $paramValue, $url, $replacedCount);

			if ($replacedCount == 0)
				$urlGETParams[$paramName] = $paramValue;
		}

		if (!empty($urlGETParams))
			$url .= '?' . http_build_query($urlGETParams);

		return $url;
	}

}