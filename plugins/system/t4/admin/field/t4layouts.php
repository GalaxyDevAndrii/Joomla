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
class JFormFieldT4layouts extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 't4layouts';

	protected function getOptions() {
		$options = [];
		$options[] = (object) ['value' => '', 'text' => JText::_('JGLOBAL_INHERIT')];
		// get all exist layouts
		$layouts = \T4\Helper\Path::files('etc/layout');
		if (!empty($layouts)) {
			foreach ($layouts as $layout) {
				$options[] = (object) ['value' => $layout, 'text' => $layout];
			}
		}

		return $options;
	}
}
