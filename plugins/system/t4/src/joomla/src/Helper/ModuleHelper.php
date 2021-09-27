<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Helper;

defined('JPATH_PLATFORM') or die;

// Make alias of original FileLayout 
\T4\Helper\Joomla::makeAlias(JPATH_LIBRARIES . '/src/Helper/ModuleHelper.php', 'ModuleHelper', '_JModuleHelper');

abstract class ModuleHelper extends _JModuleHelper {
	public static function getLayoutPath($module, $layout = 'default')
	{
		// Get layout in T4
		$file = \T4\Helper\Path::getLayoutPath($module, $layout);
		if ($file) return $file;

		// original one
		return parent::getLayoutPath($module, $layout);
	}

}