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
class JFormFieldT4Text extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'T4Text';
	protected function getInput()
	{
		$checked = $this->value ? 'checked' : '';
		if(!$this->value)$this->value = 0;
		$hint = $this->element['hint'] ? 'placeholder="'.htmlspecialchars((string)$this->element['hint']).'"' : '';
		$hidden = $this->element['hideLabel'] ? 'hidden' : 'text';
		$html = '';
		$html	.= '<input id="'.$this->id.'" class="t4-layout '.$this->element['class'].'" type="'.$hidden.'" data-attrname="'.$this->element['name'].'" value="'.$this->value.'" '. $hint .' />';
		return $html;
	}
}
