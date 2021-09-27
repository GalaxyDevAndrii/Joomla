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
class JFormFieldT4Radio extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 't4Radio';
	protected function getInput()
	{

		$value     = !empty($this->default) ? $this->default : '1';
		$checked = $this->checked || !empty($this->value) ? ' checked' : '';
		$class = $this->element['group'] ? (string) $this->element['group'] : '';
		$html = '';
		$html .= '<input type="checkbox" class="t4-input btn-group radio '.$class.'" data-group="'.$class.'" name="' . $this->name . '" id="'.$this->id.'" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" '.$checked.'>';
		return $html;
	}
	protected function getLabel()
	{

		$html = '';
		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;
		$desc = $this->element['description'] ? (string) $this->element['description'] : '';
		$desc = $this->translateLabel ? JText::_($desc) : $desc;
		$class = 't4-checkbox';
		$class .= !empty($this->class) ? ' ' . $this->class : '';
		$class .= $this->element['subgroup'] ? ' sub-legend-checkbox ' : '';
		$class .= $this->element['group'] ? (string) $this->element['group'] : '';

		$class = 'class="' . $class . '"';

		//
		$expend = $this->element['expend'] ? ' data-expend="' . $this->element['expend'] . '"' : '';

		$html .= "<div $class$expend><label for='".$this->id."'>$text</label></div>";
		return $html;
	}
}
