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
class JFormFieldT4Range extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'T4Range';
	protected function getInput()
	{
		$html = '';
		$html .= '<div class="t4-slider"><input id="'.$this->id.'" type="range" class="t4-input '.$this->element['class'].'" name="' . $this->name . '" min="0" max="1" step="0.01" data-attrname="'.$this->element['name'].'" value="'.$this->value.'"><div class="slider-bg"><div class="slider-bg-lower"></div></div></div><span><input type="number"  min="0" step="0.01" max="1" id="opacityVal" value="'.$this->value.'" name="opacity_val" /></span>'; 
		return $html;
	}
}
