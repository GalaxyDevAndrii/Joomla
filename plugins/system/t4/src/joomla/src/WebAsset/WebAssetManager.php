<?php
namespace Joomla\CMS\WebAsset;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory as JFactory;
// Make alias of original FileLayout 
\T4\Helper\Joomla::makeAlias(JPATH_LIBRARIES . '/src/WebAsset/WebAssetManager.php', 'WebAssetManager', '_JWebAssetManager');

class WebAssetManager extends _JWebAssetManager {

	public function enableAsset($name) {
		// already enabled
		if (isset($this->activeAssets['style'][$name]) || isset($this->activeAssets['script'][$name])) return;
		$assetScript = $this->registry->exists('script',$name);
		$assetStyle = $this->registry->exists('style',$name);

		if ($assetScript) {
			$this->activeAssets['script'][$name] = $this->registry->get('script',$name);

			// enable dependency
			if (!empty($asset['dependencies'])) {
				foreach($asset['dependencies'] as $dep) {
					$this->enableAsset($dep);
				}
			}
		}
		if ($assetStyle) {
			$this->activeAssets['style'][$name] = $this->registry->get('style',$name);

			// enable dependency
			if (!empty($asset['dependencies'])) {
				foreach($asset['dependencies'] as $dep) {
					$this->enableAsset($dep);
				}
			}
		}
		return $this;
	}
}
