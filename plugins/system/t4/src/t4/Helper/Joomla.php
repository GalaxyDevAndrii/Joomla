<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;

class Joomla {
	// Create alias class for original call in $filepath, then overload the class
	public static function makeAlias($filepath, $originClassName, $aliasClassName) {
		if (!is_file($filepath)) return false;
		$code = file_get_contents($filepath);
		$code = str_replace('class ' . $originClassName, 'class ' . $aliasClassName, $code);
		eval('?>'. $code);
		return true;
	}


	// public static function getWebAssetManager() {
	// 	static $wam = null;
	// 	if ($wam === null) {
	// 		$doc = JFactory::getApplication()->getDocument();
	// 		if (!$doc) {
	// 			$doc = JFactory::getDocument();
	// 		}

	// 		if (method_exists($doc, 'getWebAssetManager')) {
	// 			$wam = $doc->getWebAssetManager();
	// 		} else {
	// 			// joomla 3, register WebAsset
	// 			\JLoader::registerNamespace('Joomla\CMS\WebAsset', T4PATH . '/src/joomla3/src/WebAsset', false, false, 'psr4');
	// 			$wam = new \Joomla\CMS\WebAsset\WebAssetManager();
	// 		}
	// 	}
	// 	return $wam;
	// }
}
