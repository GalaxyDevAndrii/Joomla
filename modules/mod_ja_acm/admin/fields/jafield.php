<?php
/**
 * ------------------------------------------------------------------------
 * JA ACM Module
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * List of checkbox base on other fields
 *
 * @since      Class available since Release 1.2.0
 */
abstract class JAFormField extends JFormField
{
	function renderLayout ($file, $displayData) {
		$path = JPATH_ROOT . '/modules/mod_ja_acm/admin/tmpl/field-' . $file . '.php';
		if (!is_file ($path)) return null;
		ob_start();
		include $path;
		$layoutOutput = ob_get_contents();
		ob_end_clean();

		return $layoutOutput;
	}

	function getItems() {
		$items = array();
		foreach ($this->element->children() as $element)
		{
			// clone element to make it as field
			$fdata = preg_replace ('/<(\/?)item(\s|>)/mi', '<\1field\2', $element->asXML());
			// remove cols, rows, size attributes
			$fdata = preg_replace ('/\s(cols|rows|size)=(\'|")\d+(\'|")/mi', '', $fdata);
			// change type text to textarea
			$fdata = str_replace ('type="text"', 'type="textarea"', $fdata);

			$felement = simplexml_load_string($fdata);
			$eletype = (string)$felement['type'];
			// use japosition if joomla 4
			if ($eletype=='moduleposition' && version_compare(JVERSION, '4.0', 'ge'))
				$eletype = 'modulepositionj4';

			$field = JFormHelper::loadFieldType($eletype);
			if ($field === false)
			{
				$field = $this->loadFieldType('text');
			}
			// Setup the JFormField object.
			$field->setForm($this->form);
			if ($field->setup($felement, null, $this->name))
			{
				$items[] = $field;
			}
		}

		return $items;
	}

} 