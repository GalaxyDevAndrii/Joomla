<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Session\Session;

class Cache {
	static $cache_group = 't4';
	static $attribs = ['_scripts', '_styleSheets', '_script', '_style', '_links', '_custom', '_file'];

	public static function getCache() {
		$conf = \JFactory::getConfig();

		$devmode = (int)$conf->get('devmode', 0);
		$options = [
			'caching' => $devmode ? false : true,
			'cachebase'    => $conf->get('cache_path', JPATH_ROOT . DIRECTORY_SEPARATOR . 'cache'),
			'lifetime' => 43200 // 60*24*30 a month
		];

		return \JCache::getInstance('', $options);
	}

	public static function clean() {
		$cache = self::getCache();
		$cache->clean(self::$cache_group);
	}

	public static function store($key, $data) {

		$cache = self::getCache();
		$cache->store($data, $key, self::$cache_group);

		return $key;
	}

	public static function load($key) {

		$cache = self::getCache();
		$data = $cache->get($key, self::$cache_group);

		if ($data) {
			//$data = @json_decode($data, true);
			return $data;
		}

		return null;
	}


	public static function loadLayout($key) {
		$data = self::load($key);
		if (!is_array($data) || empty($data['layout'])) return null;
		// set css/js/style/script
		$doc = JFactory::getDocument();

		// load attribs data
		foreach (self::$attribs as $attr) {
			if (!empty($data[$attr])) {
				// merge
				if (is_array($data[$attr]))  {
					foreach ($data[$attr] as $key => $val) {
						if (empty($doc->$attr[$key])) $doc->$attr[$key] = $val;
					}
				} else {
					$doc->$attr = $data[$attr];
				}
			}
		}

		// enable webasset data
		if (!empty($data['assets'])) {
			$wam = \T4\Helper\Asset::getWebAssetManager();
			foreach ($data['assets'] as $name) $wam->enableAsset($name);
		}


		return $data['layout'];
	}

	public static function storeLayout($key, $layout) {
		$data = ['layout' => $layout];
		$doc = JFactory::getDocument();
		
		if(version_compare(JVERSION, '4', 'lt')){
			// store webasset data
			$wam = \T4\Helper\Asset::getWebAssetManager();
			$assets = $wam->getAssets();
			if (!empty($assets)) $data['assets'] = array_keys($assets);
		}

		// store attribs data
		// load attribs data
		foreach (self::$attribs as $attr) {
			if (!empty($doc->$attr)) {
				$data[$attr] = $doc->$attr;
			}
		}

		self::store($key, $data);
	}

}
