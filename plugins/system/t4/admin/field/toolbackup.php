<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;
/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldToolBackup extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'toolbackup';
	protected $layout = 'field.toolbackup';

	protected function getInput()
	{
		$data = [];

		// get preset config
		$path = T4PATH_TPL . '/etc/presets';
		$files = \JFolder::files($path, '.json');
		if (!$files) $files = [];
		// strip file extension		
		foreach ($files as $i => $file) {
			$files[$i] = substr($file, 0, -5);
		}
		$data['presets'] = $files;

		return \JLayoutHelper::render ($this->layout, $data, T4PATH_ADMIN . '/layouts');
	}

	protected function getLabel() {
		return null;
	}
}
