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
defined('_JEXEC') or die('Restricted access');

require_once 'jafield.php';

/**
 * Radio List Element
 *
 * @since      Class available since Release 1.2.0
 */
class JFormFieldJAAcm extends JAFormField
{
	/**
	 * Element name
	 *
	 * @access  protected
	 * @var    string
	 */
	protected $type = 'jaacm';


	function getLabel()
	{
		return '';
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	function getInput()
	{
		$jdoc = JFactory::getDocument();
		if (version_compare(JVERSION, '3.0', 'lt')) {
			$jdoc->addScript('//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js');
			// add bootstrap for Joomla 2.5

			if (defined('T3_ADMIN_URL')) {
				$jdoc->addStyleSheet(T3_ADMIN_URL . '/admin/bootstrap/css/bootstrap.min.css');
				$jdoc->addScript(T3_ADMIN_URL . '/admin/bootstrap/js/bootstrap.min.js');
			}
		}
		// add font awesome 4
		if (defined('T3_ADMIN_URL')) {
			$jdoc->addStyleSheet(T3_ADMIN_URL . '/admin/fonts/fa4/css/font-awesome.min.css');
		}

		//$jdoc->addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css');
		//$jdoc->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js');
		// jBox library
		$jdoc->addScript(JUri::root(true) . '/modules/mod_ja_acm/admin/assets/jBox/jBox.min.js');
		$jdoc->addStyleSheet(JUri::root(true) . '/modules/mod_ja_acm/admin/assets/jBox/jBox.css');
		if (version_compare(JVERSION, '4.0', 'ge'))
			$jdoc->addScript(JUri::root(true) . '/modules/mod_ja_acm/admin/assets/script_j4.js');
		else
			$jdoc->addScript(JUri::root(true) . '/modules/mod_ja_acm/admin/assets/script.js');
    	$jdoc->addStyleSheet(JUri::root(true) . '/modules/mod_ja_acm/admin/assets/style.css');

    	// get value, activetype, priority for cookie value
    	$cookie = JFactory::getApplication()->input->cookie;
    	$svalue = $cookie->get('advancedvalue', '', 'raw');
    	if (!$svalue) {
    		$svalue = $this->value;
    	} else {
    		setcookie('advancedvalue', null, -1, '/');
    		$this->value = $svalue;
    	}
    	$value = json_decode($svalue, true);
    	 
    	// get active type
    	$activelayout = $activetypename = $activetpl = '';
    	$activetype = $cookie->get('activetype', '', 'raw');
    	if (!$activetype) {
    		if (isset($value[':type'])) {
    			$activetype = $value[':type'];
    		}
    	} else {
    		setcookie('activetype', null, -1, '/');
    		$tmp = explode('::', $activetype);
    		if (count ($tmp) > 1) {
    			$activelayout = $tmp[1];
    			$activetype = $tmp[0];
    		}
    	}
    	 
		// helper
		$params = new JRegistry;
		$params->set ('jatools-config', $svalue);
		JLoader::register('ModJAACMHelper', JPATH_SITE . '/modules/mod_ja_acm/helper.php');
		$helper = new ModJAACMHelper ($params);

		// get active type
		//$activetype = JFactory::getApplication()->input->cookie->get('activetype', '', 'raw');

		if ($activetype) {
			$tmp = explode(':', $activetype);
			$activetypename = count($tmp) > 1 ? $tmp[1] : $tmp[0];
			$activetpl = count($tmp) > 1 ? $tmp[0] : '_';
			if (!$activelayout && isset($value[$activetypename]) && isset($value[$activetypename]['jatools-layout-' . $activetypename])) {
				$activelayout = $value[$activetypename]['jatools-layout-' . $activetypename];
				if (is_array ($activelayout)) $activelayout = $activelayout[0];
			}
			// clear value if not the right layout
			$savedlayout = isset($value[$activetypename]) && isset($value[$activetypename]['jatools-layout-' . $activetypename]) ? $value[$activetypename]['jatools-layout-' . $activetypename] : '';
			if (is_array ($savedlayout)) $savedlayout = $savedlayout[0];
			if ($savedlayout != $activelayout) $this->value = null;
		}

		// load all xml
		$paths = array();
		$paths['_'] = JPATH_ROOT . '/modules/mod_ja_acm/acm/';
		// template folders
		$tpls = JFolder::folders(JPATH_ROOT . '/templates/');
		foreach ($tpls as $tpl) {
			$paths[$tpl] = JPATH_ROOT . '/templates/' . $tpl . '/acm/';
		}

		$fields = array();
		$group_types = array();
		$group_layouts = array();
		foreach ($paths as $template => $path) {
			if (!is_dir($path)) continue;
			$types = JFolder::folders($path);
			if (!is_array($types)) continue;

			$group_types[$template] = array();

			// get layout for each type
			foreach ($types as $type) {
				if (!isset($group_layouts[$type])) $group_layouts[$type] = array();
				if (!is_dir($path . $type . '/tmpl')) continue;
				$layouts = JFolder::files($path . $type . '/tmpl', '.php');
				if (is_array($layouts)) {
					foreach ($layouts as $layout) {
						$layout = JFile::stripExt($layout);
						$group_layouts[$type][] = $layout;
					}
				}
			}

			foreach ($types as $type) {
				$lname = $type;
				if (is_file($path . $type . '/config.xml')) {
					$xml = simplexml_load_file($path . $type . '/config.xml');
					$title = isset($xml->title) ? $xml->title : $lname;
					$group_types[$template][$lname] = $title;
				}
			}
		}



		$description = $sampledata = $configform = '';
		$activetypetitle = $activetypename;

		// load activetype xml
		if ($activetype && $activelayout) {
			$type = $activetypename;
			$tpl = $activetpl;
			$path = $paths[$tpl];

			// load template language
			if ($tpl != '_') {
				JFactory::getLanguage()->load('tpl_' . $tpl, JPATH_SITE, null, true);
				JFactory::getLanguage()->load('tpl_' . $tpl . '.sys', JPATH_SITE, null, true);
			}

			if (is_file($path . $type . '/config.xml')) {
				$xml = simplexml_load_file($path . $type . '/config.xml');
				$description = isset($xml->description) ? $xml->description : '';
				if ($xml->title) $activetypetitle = (string) $xml->title;
				$sampledata = isset($xml->sampledata) ? $xml->sampledata : '';
				// load xml for selected layout
				$lname = $activelayout;
				$lform = new JForm($lname);
				$lform->load($xml, false);
				if (is_file ($path . $type . '/tmpl/' . $activelayout . '.xml')) {
					$lxml = simplexml_load_file($path . $type . '/tmpl/' . $activelayout . '.xml');
					if (isset($lxml->sampledata)) $sampledata = $lxml->sampledata;
					$lform->load($lxml, false);
				}
				$fieldsets = $lform->getFieldsets();

				$configform = $this->renderLayout('jaacm-type', array('form' => $lform, 'fieldsets' => $fieldsets, 'sample-data' => $sampledata, 'helper' => $helper));
			}
		}

		$layoutform = $this->renderLayout('jaacm-select-layout', array('group_types' => $group_types, 'group_layouts' => $group_layouts,
			'activetype' => $activetype, 'activetypename' => $activetypename, 'activelayout' => $activelayout, 'activetypetitle' => $activetypetitle, 'activelayouttitle' => $activelayout));

		$html = '';
		$html .= "\n<input type=\"hidden\" name=\"{$this->name}\" id=\"jatools-config\" value=\"" . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . "\" />";
		$html .= $this->renderLayout('jaacm', array('config-form' => $configform, 'layout-form' => $layoutform,
			'activetype' => $activetype, 'activetypename' => $activetypename, 'activelayout' => $activelayout, 'description' => $description));
		// $html .= $this->renderConfig('layouts-config', array('group_types' => $group_types, 'fields' => $fields), JPATH_ROOT);
		return $html;
	}

	function renderConfig($file, $displayData)
	{
		$path = JPATH_ROOT . '/modules/mod_ja_acm/admin/tmpl/' . $file . '.php';
		if (!is_file($path)) return null;
		ob_start();
		include $path;
		$layoutOutput = ob_get_contents();
		ob_end_clean();

		return $layoutOutput;
	}

	function renderFieldSet($form, $name)
	{
		//if (method_exists ($form, 'renderFieldSet')) {
		//	$html = $form->renderFieldSet ($name);
		//	return $html;
		//} else {
		$fields = $form->getFieldset($name);
		$html = array();
		foreach ($fields as $field) {
			$layouts = $field->element['layouts'] ? ' data-layouts="' . $field->element['layouts'] . '"' : '';

			$html[] = '
				<div class="control-group"' . $layouts . '>
					<div class="control-label">' . $field->getLabel() . '</div>
					<div class="controls">' . $field->getInput() . '</div>
				</div>';
		}

		return implode('', $html);
		//}
	}
} 