<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;
use Joomla\Registry\Registry;


class TemplateStyle {
	public static $groups = ['navigation', 'system', 'site', 'other'];
	public static function getMaster($template) {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select(array('*'));
        $query->from( $db->quoteName('#__template_styles') );
        $query->where( $db->quoteName('client_id') . ' = 0' );
        $query->where( $db->quoteName('template') . ' = ' . $db->quote($template) );
        $query->order('id');

        $db->setQuery($query);

        $tpl = $db->loadObject();

        return $tpl;
	}


	public static function updateDefaultSettings($defaultTpl, $tpl) {
		$defaultParams = new Registry($defaultTpl->params);
		$params = is_array($tpl->params) ? new Registry($tpl->params) : $tpl->params;
		$defaultProps = array_keys($defaultParams->toArray());
		$tplProps = array_keys($params->toArray());

		foreach (self::$groups as $group) {
			$name = 'toggle-' . $group;

			if (!$params->get($name, 0)) {
				// // unset
				// foreach($tplProps as $name) {
				// 	if ($name == $group || strpos($name, $group . '_') === 0) {
				// 		$params->remove($name);
				// 	}
				// }

				// not overwrite, using setting from default
				foreach($defaultProps as $name) {
					if ($name == $group || strpos($name, $group . '_') === 0) {
						if ($defaultParams->get($name)) $params->set($name, $defaultParams->get($name));
						else $params->remove($name);
					}
				}
			}
		}/*
		//update user color
		$def_user_color = json_decode($defaultParams->get('styles_color_custom_color'));
		$current_value = json_decode($params->get('styles_color_custom_color'));
		foreach ($def_user_color as $name => $color_data) {
			if(!empty($current_value->{$name})){
				$def_user_color->{$name}->color = $current_value->{$name}->color;
			}
		}
		$params->set('styles_color_custom_color',json_encode($def_user_color));*/
		// update back if is array
		if (is_array($tpl->params)) {
			$tpl->params = $params->toArray();
		}
	}


	/**
	 * Get default template style and check if it is T4 template
	 */
	public static function getDefault($params = false) {
		static $tpl = null;

		if ($tpl === null) {
			$db = JFactory::getDbo();

	        $query = $db->getQuery(true);
	        $query->select(array('*'));
	        $query->from( $db->quoteName('#__template_styles') );
	        $query->where( $db->quoteName('client_id') . ' = 0' );
	        $query->where( $db->quoteName('home') . ' = 1' );

	        $db->setQuery($query);

	        $tpl = $db->loadObject();

	        if (!$tpl) return null;

	        // check if it is T4 template
			$filePath = JPATH_ROOT . '/templates/' . $tpl->template . '/templateDetails.xml';

			$base = null;
			if (is_file ($filePath)) {
				$xml = $xml = simplexml_load_file($filePath);
				// check t4
				if (isset($xml->t4) && isset($xml->t4->basetheme)) {
					$base = trim(strtolower($xml->t4->basetheme));
				}
			}

			if (!$base) {
				$tpl = '';
				return null;
			}
		}

		if ($tpl == '') return null;

        return $params ? $tpl : $tpl->template;
	}


	public static function initDefault ($tpl) {
		$params = is_array($tpl->params) ? new Registry($tpl->params) : $tpl->params;
		// init default template params
		if (empty($params->toArray())) {
			// look like the template is not configured, using default value
			$file = Path::findInTheme('etc/default.json');
			if ($file) {
				$defaultParams = new Registry();
				$defaultParams->loadFile($file);

				// update value for current params
				foreach ($defaultParams->toArray() as $name => $value) {
					if (!$params->get($name)) $params->set($name, $value);
				}
			}
		}
		// update back if is array
		if (is_array($tpl->params)) {
			$tpl->params = $params->toArray();
		}
	}

	public static function loadGlobalParams ($tpl) {
		$params = is_array($tpl->params) ? new Registry($tpl->params) : $tpl->params;
		// load global data from file
		$global = (array)json_decode(\T4\Helper\Path::getFileContent('etc/global.json'), true);

		// clear global params
		// add check for lower joomla version
		if (method_exists($params, 'remove')) {
			foreach ($params->toArray() as $name => $val) {
				if (preg_match ('/^system(_|$)/', $name)) {
					$params->remove($name);
				}
			}
		}
		// set value from global
		foreach ($global as $name => $val) $params->set($name, $val);

		// update back if is array
		if (is_array($tpl->params)) {
			$tpl->params = $params->toArray();
		}
	}
}
