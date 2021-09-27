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

defined('_JEXEC') or die;

/**
 * Helper for mod_menu
 *
 * @package     Joomla.Site
 * @subpackage  mod_menu
 * @since       1.5
 */
class ModJAACMHelper
{
	protected $data = null;
	protected $layout = '';
	protected $type = '';
	protected $template = '';
	protected $group = '';
	protected $keys = array();

	/**
	 * Get a list of the menu items.
	 *
	 * @param   JRegistry &$params The module options.
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public function __construct($params)
	{
		$config = json_decode($this->decode($params->get('jatools-config')), true);
		$type = '';
		if (isset($config[':type'])) {
			$type = $config[':type'];
		} else if (isset($config[':layout'])) {
			$type = $config[':layout'];
		}

		$tmp = explode(':', $type, 2);
		$this->type = count($tmp) > 1 ? $tmp[1] : $tmp[0];
		$this->template = count($tmp) > 1 && $tmp[0] != '_' ? $tmp[0] : '';

		$this->data = isset($config[$this->type]) ? $config[$this->type] : array();

		// detect group key
		$this->keys = array_keys($this->data);

		// detect layout
		$this->layout = $this->get('jatools-layout-' . $this->type, 0, 'default');
	}

	public function decode($str)
	{
		return str_replace(array('((', '))'), array('<', '>'), $str);
	}

	public function get($name, $i = 0, $default = null)
	{
		$value = (array)$this->findParam($name);
		
		$val = isset($value[$i]) ? $value[$i] : $default;
		if (preg_match ('/^(.*)\|filter:(.*)$/', $val, $match) && method_exists($this, 'filter'.$match[2])) {
			$func = 'filter'.$match[2];
			$val = $this->$func ($match[1]);
		}
		return $val;
	}

	public function getCell($name, $row = 0, $col = 0, $default = null)
	{
		$value = (array)$this->findParam($name);
		if (!isset($value['data']) || !isset($value['data'][$row]) || !isset($value['data'][$row][$col])) return $default;

		$val = $value['data'][$row][$col];
		if (preg_match ('/^(.*)\|filter:(.*)$/', $val, $match) && method_exists($this, 'filter'.$match[2])) {
			$func = 'filter'.$match[2];
			$val = $this->$func ($match[1]);
		}
		return $val;
	}

	public function count($name, $column = false)
	{
		$value = (array)$this->findParam($name);
		if (isset($value['type'])) {
			return $column ? (int)$value['cols'] : (int)$value['rows'];
		}
		return count($value);
	}

	public function getRows($name)
	{
		$value = (array)$this->findParam($name);
		if ($value && isset($value['type'])) {
			return (int)$value['rows'];
		}
		return count($value);
	}

	public function getCols($name)
	{
		$value = (array)$this->findParam($name);
		if ($value && isset($value['type'])) {
			return (int)$value['cols'];
		}
		return 1;
	}

	public function findParam($name)
	{
		$tmp = explode ('.', $name, 2);
		$group = null;
		if (count($tmp) == 2) {
			$group = $tmp[0];
			$name = $tmp[1];
		}
		
		$pattern = '/\[' . preg_quote($name) . '\]/';

		// try to find by group
		$field = null;
		if ($group) {
			$field = $this->findParam($group);
		}
		// check if found field $name in $group
		if ($field) {
			foreach ($field as $key => $value) {
				if ($key == $name || preg_match($pattern, $key)) return $value;
			}
		}

		// find base on $name
		foreach ($this->keys as $key) {
			if ($key == $name || preg_match($pattern, $key)) return $this->data[$key];
		}
		return null;
	}

	public function find($filename, $get_url = false, $return_default = false)
	{
		$template = JFactory::getApplication()->getTemplate();

		// Build the template and base path for the layout
		$paths = array();
		// for T3: local folder
		$paths[JUri::base(true) . '/templates/' . $template . '/local/acm/'] = JPATH_THEMES . '/' . $template . '/local/acm/';
		// config template
		$paths[JUri::base(true) . '/templates/' . $this->template . '/acm/'] = JPATH_THEMES . '/' . $this->template . '/acm/';
		// current template
		$paths[JUri::base(true) . '/templates/' . $template . '/acm/'] = JPATH_THEMES . '/' . $template . '/acm/';
		// in module
		$paths[JUri::base(true) . '/modules/mod_ja_acm/acm/'] = JPATH_BASE . '/modules/mod_ja_acm/acm/';
		
		foreach ($paths as $uri => $path) {
			if (is_file($path . $this->type . '/' . $filename))
				return ($get_url ? $uri : $path) . $this->type . '/' . $filename;
		}

		// return default
		if ($return_default) {
			$default = $get_url ? JUri::base(true) . '/templates/' . $this->template . '/acm/' : JPATH_THEMES . '/' . $this->template . '/acm/';
			return $default . $this->type . '/' . $filename;
		}
		
		return null;
	}

	public function getLayout()
	{
		$layout = $this->find('tmpl/' . $this->layout . '.php');
		if (!$layout) $layout = $this->find('tmpl/style-1.php');
		return $layout;
	}

	public function addAssets()
	{
		$doc = JFactory::getDocument();

		$doc->addStyleSheet(JUri::base() . '/templates/' . JFactory::getApplication()->getTemplate() . '/css/acm.css');

		if (($css = $this->find('css/style.css', true, true))) {
			$doc->addStyleSheet($css);
		}
		if (($js = $this->find('js/script.js', true))) {
			JHtml::_('jquery.framework');		  
			$doc->addScript($js);
		}
	}

	public function renderModule ($name, $attribs=array()) {
		if (!$name) return null;
		static $buffers = array();
		if (isset($buffers[$name])) return $buffers[$name];
		// init cache to prevent nested parse
		$buffers[$name] = '';
		// prevent cache
		$attribs['params'] = '{"cache":0}';
		$buffers[$name] = JFactory::getDocument()->getBuffer('module', $name, $attribs);
		return $buffers[$name];
	}

	public function renderModules ($position, $attribs = array()) {
		if (!$position) return null;
		static $buffers = array();
		if (isset($buffers[$position])) return $buffers[$position];
		// init cache to prevent nested parse
		$buffers[$position] = '';
		$buffers[$position] = JFactory::getDocument()->getBuffer('modules', $position, $attribs);
		return $buffers[$position];
	}

	public function countModules ($condition) {
		if (!$condition) return 0;
		return JFactory::getDocument()->countModules ($condition);
	}

	public function renderJDoc ($id, $buffer) {
		static $buffers = array();
		if (isset($buffers[$id])) return $buffers[$id];

		// init cache to prevent nested parse
		$buffers[$id] = '';

		$doc = JFactory::getDocument();
		$matches = array();

		if (preg_match_all('#<jdoc:include\ type="([^"]+)" (.*)\/>#iU', $buffer, $matches))
		{
			$tags = array();

			// Step through the jdocs in reverse order.
			for ($i = count($matches[0]) - 1; $i >= 0; $i--)
			{
				$type = $matches[1][$i];
				$attribs = empty($matches[2][$i]) ? array() : JUtility::parseAttributes($matches[2][$i]);
				$name = isset($attribs['name']) ? $attribs['name'] : null;

				// Separate buffers to be executed first and last
				$tags[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attribs' => $attribs);
			}

			$replace = array();
			$with = array();

			foreach ($tags as $jdoc => $args)
			{
				$replace[] = $jdoc;
				$with[] = $doc->getBuffer($args['type'], $args['name'], $args['attribs']);
			}

			$buffer = str_replace($replace, $with, $buffer);
		}

		$buffers[$id] = $buffer;

		return $buffers[$id];
	}

	public function toArray($ignore_null = false) {
		$array = array();
		$keys = array_keys($this->data);
		foreach ($keys as $key) {
			$_key = (preg_match('/.*\[([^]]+)\](\[\])?$/', $key, $match)) ? $match[1] : $key;
			$val = count($match)<3 ? trim(implode(',', (array)$this->data[$key])) : $this->data[$key];
			if(empty($_key)) continue;
			if ($ignore_null && $val === '') continue;
			$array[$_key] = $val;
		}

		return $array;
	}

	public function escape ($str) {
		return $str;
	}
}
