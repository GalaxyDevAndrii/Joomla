<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\WebAsset\WebAssetManager;

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldAddons extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'addons';
	protected $layout = 'field.addons';

	protected function getInput()
	{
		$data = [];

		// get preset addons

		// $data['addons'] = \T4\Helper\Addons::load();
		$data['assets'] = $this->loadAssets();

		$data['id'] = $this->id;
		$data['name'] = $this->name;
		$data['value'] = $this->value;

		return \JLayoutHelper::render ($this->layout, $data, T4PATH_ADMIN . '/layouts');
	}

	protected function getLabel() {
		return null;
	}

	private function loadAssets() {
		$assets = [];
		
		$assetfile = '/etc/assets.json';

		$filepath = T4PATH_BASE . $assetfile;

		if (is_file($filepath)) {
			$data = file_get_contents($filepath);
			$data = $data ? json_decode($data, true) : null;

			if ($data && !empty($data['assets'])) {
				foreach ($data['assets'] as $name => $asset) {
					if (!empty($asset['required'])) continue;
					if (empty($asset['title'])) $asset['title'] = $name;
					$assets[$name] = $asset;
				}
			}
		}

		$filepath = T4PATH_TPL . $assetfile;
		if (is_file($filepath)) {
			$data = file_get_contents($filepath);
			$data = $data ? json_decode($data, true) : null;

			if ($data && !empty($data['assets'])) {
				foreach ($data['assets'] as $name => $asset) {
					if (!empty($asset['required'])) continue;
					if (empty($asset['title'])) $asset['title'] = $name;
					$assets[$name] = $asset;
				}
			}
		}
		
		$filepath = T4PATH_LOCAL . $assetfile;
		if (is_file($filepath)) {
			$data = file_get_contents($filepath);
			$data = $data ? json_decode($data, true) : null;

			if ($data && !empty($data['assets'])) {
				foreach ($data['assets'] as $name => $asset) {
					$asset['local'] = true;
					$assets[$name] = $asset;
				}
			}
		}
		
		return $assets;
	}
}
