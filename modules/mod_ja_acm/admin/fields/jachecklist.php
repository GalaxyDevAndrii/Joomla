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
class JFormFieldJAChecklist extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'jachecklist';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	function getInput()
	{
		$html = $this->renderLayout ('jachecklist', array('field' => $this));
		return $html;
	}

	function renderLayout ($file, $displayData) {
		$path = JPATH_ROOT . '/modules/mod_ja_acm/admin/tmpl/' . $file . '.php';
		if (!is_file ($path)) return null;
		ob_start();
		include $path;
		$layoutOutput = ob_get_contents();
		ob_end_clean();

		return $layoutOutput;
	}

} 