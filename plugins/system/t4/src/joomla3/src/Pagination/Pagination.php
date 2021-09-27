<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Pagination;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Layout\LayoutHelper;

// Make alias of original FileLayout 
\T4\Helper\Joomla::makeAlias(JPATH_LIBRARIES . '/src/Pagination/Pagination.php', 'Pagination', '_Pagination');


// Override original FileLayout to trigger event when find layout
class Pagination extends _Pagination
{

	/**
	 * Method to create an active pagination link to the item
	 *
	 * @param   PaginationObject  $item  The object with which to make an active link.
	 *
	 * @return  string  HTML link
	 *
	 * @since   1.5
	 */
	protected function _item_active(PaginationObject $item)
	{
		return LayoutHelper::render('joomla.pagination.link', ['data' => $item, 'active' => true]);
	}

	/**
	 * Method to create an inactive pagination string
	 *
	 * @param   PaginationObject  $item  The item to be processed
	 *
	 * @return  string
	 *
	 * @since   1.5
	 */
	protected function _item_inactive(PaginationObject $item)
	{
		return LayoutHelper::render('joomla.pagination.link', ['data' => $item, 'active' => false]);
	}
	
}
