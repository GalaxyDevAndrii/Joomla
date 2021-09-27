<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');
/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldT4Section extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'T4Section';
	protected function getInput()
	{
		$output   = '<div class="section-group clearfix">';
		$output  .= '<div class="config-section t4-row-container"></div>';
		$output  .= '<input class="t4-layout t4-input-'.$this->element['name'].'" data-attrname="'.$this->element['name'].'" type="hidden">';
		$output  .= '</div>';

		return $output; 
	}
}

