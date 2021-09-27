<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace T4\Renderer;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Document\DocumentRenderer;
use Joomla\CMS\Layout\LayoutHelper;

/**
 * HTML document renderer for the document `<head>` element
 *
 * @since  3.5
 */
class Element extends DocumentRenderer
{
	public function render($name, $params = array(), $content = null)
	{
		$data = (object) [
			'doc' => $this->_doc,
			'name' => $name,
			'content' => $content,
			'params' => $params
		];

		return LayoutHelper::render('t4.element.' . $name, $data);
	}

}
