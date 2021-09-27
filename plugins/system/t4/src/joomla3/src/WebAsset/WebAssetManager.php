<?php
namespace Joomla\CMS\WebAsset;

use Joomla\CMS\Factory as JFactory;

class WebAssetManager {
	var $registry = null;
	var $activeAssets = [];

	public function __construct() {
		// create registry
		$this->registry = new WebAssetRegistry();

		$this->debug = (int)JFactory::getConfig()->get('debug');
		//$this->registry->addRegistryFile(T4PATH_BASE . $coreasset);
		//$this->registry->addRegistryFile(T4PATH_TPL . $coreasset);
	}

	public function getRegistry() {
		return $this->registry;
	}

	public function enableAsset($name) {
		// already enabled
		if (isset($this->activeAssets[$name])) return;
		$asset = $this->registry->getAsset($name);

		if ($asset) {
			$this->activeAssets[$name] = $asset;

			// enable dependency
			if (!empty($asset['dependencies'])) {
				foreach($asset['dependencies'] as $dep) {
					$this->enableAsset($dep);
				}
			}

			// add assets to document
			$doc = JFactory::getDocument();
			if (!empty($asset['css'])) {
				foreach ($asset['css'] as $url) {
					$url = preg_match('/^(https?:)?\/\//', $url) ? $url : \T4\Helper\Path::findInTheme($url, true);
					if (!$url) continue;
					// remove .min if in debug mode
					if ($this->debug && preg_match('/media\/jui\/css/', $url)) {
						$url = str_replace('.min.css', '.css', $url);
					}
					$doc->addStylesheet($url);
				}
			}

			if (!empty($asset['js'])) {
				foreach ($asset['js'] as $url) {
					$url = preg_match('/^(https?:)?\/\//', $url) ? $url : \T4\Helper\Path::findInTheme($url, true);
					if (!$url) continue;
					// remove .min if in debug mode
					if ($this->debug && preg_match('/media\/jui\/js/', $url)) {
						$url = str_replace('.min.js', '.js', $url);
					}
					$doc->addScript($url);
				}
			}
		}
	}

	public function getAssets($sort = false) {
		return $this->activeAssets;
	}
}
