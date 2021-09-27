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
class JFormFieldT4multiradio extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'T4MultiRadio';
	protected function getInput(){
		
		$html  = '<div class="fuildwidth btn-group">
						<a class="contaifix btn active" data-container="container">Fix</a>
						<a class="btn fluid" data-container="container-fluid">Fluid</a>
						<a class="btn fluid-none" data-container="none">None</a>
						<input id="t4-cont" type="hidden" name="container" data-attrname="container" value="container" class="t4-layout t4-layout-container" />
					</div>';
		// $html  .= '</div>';
		return $html;
	}
}
