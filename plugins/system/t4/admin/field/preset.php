<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldPreset extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Preset';
	protected function getInput()
	{
		// get a preset list on folder
		$path = dirname(dirname(__DIR__)) . '/etc/presets/' . $this->element['group'];
		$presets = array();
		if (is_dir($path)) {
			$files = glob($path . '/*.nvp');
			foreach ($files as $file) {
				// Get content and parse
				$set = explode("\n", file_get_contents($file));
				$title = array_shift($set);
				// parse name-value pair
				$nvps = array();
				foreach ($set as $nvp) {
					$arr =  explode(':', $nvp, 2);
					$name = trim($arr[0]);
					if (!$name) continue;
					$val = count($arr) > 1 ? trim($arr[1]) : '';
					$nvps[$name] = $val;
				}
				$presets[$title] = $nvps;
			}
		}

		// Build dropdown list base on the presets
		$options = array();
		// Blank one
		$options[] = array(
			'text' => JText::_('TPL_T4_SELECT_PRESET'),
			'value' => ''
			);
		// Preset group
		$options[] = array(
			'text' => JText::_('TPL_T4_PRESET_GROUP_PRESET'),
			'value' => '<OPTGROUP>'
			);
		$i = 0;
		foreach ($presets as $title => $set) {
			$option = array();
			$option['text'] = $title;
			$option['value'] = $i++;
			$option['attr'] = array('data-set' => htmlentities(json_encode($set)));
			$options[] = $option;
		}
		// Last saved group
		$options[] = array(
			'text' => JText::_('TPL_T4_PRESET_GROUP_REVISIONS'),
			'value' => '<OPTGROUP>'
			);
		// load from revisons
		//$style_id = JFactory::getApplication()->input->getInt('id');
		$helper = require ((dirname(__DIR__)) . '/helper.php');
		//$helper->loadStyle ($style_id);
		// Check if template style is just saved and run once
		 
		$path = JPATH_ROOT . $helper->getMediaLocation() . '/revisions/' . $this->element['group'] . '/';
		// saved revision
		$saved_revisions = glob ($path . $style_id . '-*.nvp');
		rsort ($saved_revisions);
		$revisions = array();
		$limit = 0;
		foreach ($saved_revisions as $file) {
			if ($limit++ < 20) {
				// Get content and parse
				$set = explode("\n", file_get_contents($file));
				$title = basename($file);
				$arr = explode('-', substr($title, 0, -4), 2);
				$title = count($arr) > 1 ? $arr[1] : $arr[0];
				if ($limit == 1) $title .= ' (current)';
				// parse name-value pair
				$nvps = array();
				foreach ($set as $nvp) {
					$arr =  explode(':', $nvp, 2);
					$name = trim($arr[0]);
					if (!$name) continue;
					$val = count($arr) > 1 ? trim($arr[1]) : '';
					$nvps[$name] = $val;
				}
				$revisions[$title] = $nvps;
			} else {
				// clean it
				@unlink($file);
			}
		}
		foreach ($revisions as $title => $set) {
			$option = array();
			$option['text'] = $title;
			$option['value'] = $i++;
			$option['attr'] = array('data-set' => htmlentities(json_encode($set)));
			$options[] = $option;
		}

		$html = JHtml::_('select.genericlist', $options, '', array('option.attr'=>'attr', 'groups' => true, 'list.attr'=>'class="preset-loader"'));
		return $html;
	}

}
