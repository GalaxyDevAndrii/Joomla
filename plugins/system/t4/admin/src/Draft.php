<?php
namespace T4Admin;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Session\Session;

class Draft {
	static $cache_group = 't4admin';

	public static function getCache() {
		$conf = \JFactory::getConfig();
		$options = [
			'caching' => true,
			'cachebase'    => $conf->get('cache_path', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'cache')
		];
		return \JCache::getInstance('', $options);
	}

	public static function clean() {
		$cache = self::getCache();
		$cache->clean(self::$cache_group);
	}

	public static function clear($id = null, $token = null) {
		if ($id === null) $id = JFactory::getApplication()->input->getInt('id');
		if ($token === null) $token = Session::getFormToken();
		$cache = self::getCache();
		$key = self::makekey($token, $id);
		$cache->remove($key, self::$cache_group);
		JFactory::getDocument()->addScriptDeclaration('var t4previewkey="' . $key . '"');
	}

	public static function store($type = null) {
		$input = JFactory::getApplication()->input;
		$post = $input->post;
		$id = $input->getInt('id');
		if ($type === null) $type = JFactory::getApplication()->input->get('type');
		$token = Session::getFormToken();
		// $savedata = json_decode(file_get_contents('php://input'), true);
		$savedata = $post->getRaw('data');

		$key = self::makekey($token, $id);
		$data = self::load($key);
		if (!is_array($data)) $data = [];

		$data[$type] = $savedata;

		$cache = self::getCache();
		$cache->store($data, $key, self::$cache_group);

		return $key;
	}

	public static function load($key) {
		static $data = null;

		if ($data === null) {
			$cache = self::getCache();
			$data = $cache->get($key, self::$cache_group);
			if (empty($data)) $data = [];
		}

		return $data;
	}

	public static function makekey($token, $id) {
		return md5("$token|$id");
	}

}