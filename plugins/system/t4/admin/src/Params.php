<?php
namespace T4Admin;

use Joomla\CMS\Factory as JFactory;
use Joomla\Registry\Registry;
use T4\Helper\TemplateStyle;
use Joomla\CMS\Form\Form as JForm;

class Params {
	public static function load($form, $data) {
		$tplXml = T4PATH_TPL . '/templateDetails.xml';

		//wrap
		$form = new T4form($form);

		//remove all fields from group 'params' and reload them again in right other base on template.xml
		$form->removeGroup('params');
		//load the template
		$form->loadFile(T4PATH_BASE . '/params/template.xml');
		//overwrite / extend with params of template
		$form->loadFile($tplXml, true, '//config');

		if (empty($data)) return;

		\T4\Helper\TemplateStyle::loadGlobalParams($data);

		// // update data
		// $defaultTpl = \T4\Helper\TemplateStyle::getMaster($data->template);

		// if ($data->id != $defaultTpl->id) {
		// 	\T4\Helper\TemplateStyle::updateDefaultSettings($defaultTpl, $data);
		// }

		// \T4\Helper\TemplateStyle::initDefault($data);

		// define('T4AMIN_DEFAULT', $data->id == $defaultTpl->id);
		// define('T4AMIN_DEFAULT_ID', $defaultTpl->id);
	}

	public static function beforeSave($table) {
		$params = is_string($table->params) ? new Registry($table->params) : $table->params;

		// save global params
		$props = array_keys($params->toArray());

		$data = [];
		foreach($props as $name) {
			if (preg_match ('/^system(_|$)/', $name)) {
				$data[$name] = $params->get($name);
			}
		}
		\T4\Helper\Path::saveLocalContent('etc/global.json', json_encode($data));

		/*
		$template = $table->template;
		if (!Admin::isT4Template($template)) return;

		// get current value
		$currentparams = new Registry(self::getTemplateParams($table->id));


		$params = is_string($table->params) ? new Registry($table->params) : $table->params;

		// Save global params into file
		$defaultTpl = TemplateStyle::getMaster($template);

		if ($table->id != $defaultTpl->id) {
			$props = array_merge(array_keys($params->toArray()), array_keys($currentparams->toArray()));
			// Special for system group, if enable toggle, save it to Master params
			$group = 'system';
			$name = 'toggle-' . $group;
			if ($params->get($name, 0)) {
				$params->remove($name);
				$masterParams = new Registry($defaultTpl->params);

				$props = array_merge($props, array_keys($masterParams->toArray()));
				// update system params to master params and unset in current params
				foreach($props as $name) {
					if ($name == $group || strpos($name, $group . '_') === 0) {
						$masterParams->set($name, $params->get($name, ''));
					}
				}

				// Store master params
				self::saveTemplateParams($defaultTpl->id, $masterParams->toString());
			}



			foreach (TemplateStyle::$groups as $group) {
				$name = 'toggle-' . $group;
				if (!$params->get($name, 0)) {
					// unset
					foreach($props as $name) {
						if ($name == $group || strpos($name, $group . '_') === 0) {
							$params->remove($name);
						}
					}
				}
			}
		}

		// update params back
		$table->params = $params->toString();
		*/
	}


	protected static function getTemplateParams($id) {
		if (!$id) return null;

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('params');
        $query->from( $db->quoteName('#__template_styles') );
        $query->where( $db->quoteName('id') . ' = ' . $db->quote($id) );

        $db->setQuery($query);
        return $db->loadResult();
	}

	protected static function saveTemplateParams($id, $params) {
		if (!$id) return null;

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);
    $query->update( $db->quoteName('#__template_styles') );
    $query->set($db->quoteName('params') . ' = ' . $db->quote($params));
    $query->where( $db->quoteName('id') . ' = ' . $db->quote($id) );

    $db->setQuery($query);
    return $db->execute();
	}

}
