<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_PLATFORM') or die;
use T4Admin\RowColumnSettings;
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldT4list extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'T4list';
	protected function getInput(){
		$html = parent::getInput();
		$function = $this->element['functions'] ? (string) $this->element['functions'] : '';
		if($function != ''){
			$options = RowColumnSettings::$function();
			if ($this->element['name'] == 'module') {
				$html = '';
				$html .= '<select class="t4-layout input-select" data-attrname="'.$this->element['name'].'">';
				foreach( $options as $keyName => $valueMod ){
					$html .= '<option value="'.$valueMod->title.'" data-modname="'.$valueMod->module.'" data-id="'.$valueMod->id.'">'.$valueMod->title.'</option>';
				}
					
				$html .= '</select>';
			}else{
				$html = \JHTML::_('select.genericlist', $options, $this->name , 'data-attrname="'.$this->element["name"].'" class="'.$this->element["class"].'"', 'value', 'text', $this->value,$this->id);
			}
		}else{
			$html = '';
			$options = parent::getOptions();
			$html .= '<select id="'.$this->id.'" class="t4-layout input-select '.$this->element['class'].'" name="'.$this->name.'" data-attrname="'.$this->element['name'].'">';
			foreach($options as $key => $option )
			{
				$html .= '<option value="'.$option->value.'">'.$option->text.'</option>';
			}
			
			$html .= '</select>';
		}
		if ($this->element['name'] == 'block') {
			$html .="<span class='t4-btn btn-action' data-action='block.edit'><i class='fal fa-edit'></i> edit</span>";
			$html .="<span class='t4-btn btn-action btn-success' data-action='block.add'><i class='fal fa-plus'></i> Add New</span>";
		}
		return $html;
	}

}
