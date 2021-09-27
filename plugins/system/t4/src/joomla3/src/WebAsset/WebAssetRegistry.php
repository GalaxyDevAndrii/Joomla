<?php
namespace Joomla\CMS\WebAsset;

use Joomla\CMS\Factory as JFactory;

class WebAssetRegistry {
	var $assets = [];

	public function addRegistryFile($path) {
		$filepath = (strpos($path, JPATH_ROOT) === 0) ? $path : JPATH_ROOT . '/' . $path;

		// parse assets
		if (is_file($filepath)) {
			$data = file_get_contents($filepath);
			$data = $data ? json_decode($data, true) : null;

			if (!$data || empty($data['assets'])) return;

			// Keep source info
			$assetSource = [
				'registryFile' => $path,
			];

			// Prepare WebAssetItem instances
			foreach ($data['assets'] as $name => $item)
			{
				if (!empty($item['name'])) $name = $item['name'];
				$item['assetSource'] = $assetSource;
				$this->assets[$name] = $item;
			}
		}

		return $this;
	}

	public function getAsset($name) {
		return !empty($this->assets[$name]) ? $this->assets[$name] : null;
	}

	public function getAssets() {
		return $this->assets;
	}

	public function add($asset) {
		$name = $asset['name'];
		$this->assets[$name] = $asset;
	}

	public function createAsset($name, $asset = []) {
		$asset['name'] = $name;
		return $asset;
	}
}
