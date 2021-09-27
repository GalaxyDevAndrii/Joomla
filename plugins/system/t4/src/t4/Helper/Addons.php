<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;

class Addons {
	static $addons = [];

	public static function load() {
		if (empty(self::$addons)) {
			// get preset addons
			self::loadAddons(T4PATH_BASE . '/etc/addons.xml');
			self::loadAddons(T4PATH_TPL . '/etc/addons.xml');
			self::loadUserAddons(T4PATH_LOCAL . '/etc/addons.json');
		}

		return self::$addons;
	}

	protected static function loadAddons($xmlfile) {
		if (!is_file($xmlfile)) return;
		
		$xml = simplexml_load_file($xmlfile);
		// check t4
		if ($xml->addon) {
			foreach($xml->addon as $addon) {
				$item = [];
				$item['name'] = (string)$addon->name;
				if (!$item['name']) continue;
				$type = (string)$addon->type;
				if ($type) $item['type'] = $type;

				$item['title'] = (string)$addon->title;
				$item['assets'] = [];
				foreach ($addon->assets->url as $url) {
					$item['assets'][] = (string) $url;
				}

				self::$addons[] = $item;
			}
		}
	}

	protected static function loadUserAddons($jsonfile) {
		if (!is_file($jsonfile)) return;
		$addons = json_decode(file_get_contents($jsonfile), true);
		foreach ($addons as $name => $addon) {
			if (!$name) continue;
			$addon['local'] = 1;
			$addon['name'] = $name;
			self::$addons[] = $addon;
		}
	}

	public static function addToHead($addons) {
		self::load();
		$doc = JFactory::getDocument();
		foreach (self::$addons as $addon) {
			if (in_array($addon['name'], $addons)) {
				$type = !empty($addon['type']) ? $addon['type'] : null;
				foreach ($addon['assets'] as $asset) {
					$url = preg_match('/^(https?:)?\/\//', $asset) ? $asset : Path::findInTheme($asset, true);
					if (!$url) continue;

					if ($type == 'css' || preg_match('/\.css(\?|\#|$)/', $url)) {
						$doc->addStylesheet($url);
					} 
					if ($type == 'js' || preg_match('/\.js(\?|\#|$)/', $url)) {
						$doc->addScript($url);
					} 
				}
			}
		}
	}
}