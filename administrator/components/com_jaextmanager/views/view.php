<?php

/**
 * @version     $Id: view.php 1620 2012-09-21 12:11:58Z lefteris.kavadas $
 * @package     K2
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

if (version_compare(JVERSION, '3.0', 'ge')) {

	class JAEMView extends JViewLegacy {

		public function assignRef($key, &$val) {
			if (is_string($key) && substr($key, 0, 1) != '_') {
				$this->$key = &$val;
				return true;
			}

			return false;
		}

		public function assign() {
			// Get the arguments; there may be 1 or 2.
			$arg0 = @func_get_arg(0);
			$arg1 = @func_get_arg(1);

			// Assign by object
			if (is_object($arg0)) {
				// Assign public properties
				foreach (get_object_vars($arg0) as $key => $val) {
					if (substr($key, 0, 1) != '_') {
						$this->$key = $val;
					}
				}
				return true;
			}

			// Assign by associative array
			if (is_array($arg0)) {
				foreach ($arg0 as $key => $val) {
					if (substr($key, 0, 1) != '_') {
						$this->$key = $val;
					}
				}
				return true;
			}

			// Assign by string name and mixed value.
			// We use array_key_exists() instead of isset() because isset()
			// fails if the value is set to null.
			if (is_string($arg0) && substr($arg0, 0, 1) != '_' && func_num_args() > 1) {
				$this->$arg0 = $arg1;
				return true;
			}

			// $arg0 was not object, array, or string.
			return false;
		}

	}

} else {

	class JAEMView extends JView {
		
	}

}
