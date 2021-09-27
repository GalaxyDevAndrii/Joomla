<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace T4\Renderer;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Document\DocumentRenderer;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry as JRegistry;

/**
 * HTML document renderer for the document `<head>` element
 *
 * @since  3.5
 */
class Megamenu extends DocumentRenderer
{
	public function render($name, $params = array(), $content = null)
	{
		$app = \JFactory::getApplication('site');
		// $menutype = 'mainmenu'; // read from template params
		$menutype = '';
		$template = $app->getTemplate(true); 
		$megConfig = $template->params->get('navigation_mega_settings', '');
		if (is_string($megConfig)) $megConfig = json_decode($megConfig, true);
		if(!isset($megConfig)){
			$menutype = 'mainmenu';
		}else{
			$menutype = key($megConfig);
		}
		$modules = ModuleHelper::getModule('mod_menu'); 
		$params = new JRegistry(); 
		$params->loadString($modules->params); 
		$params->set('layout', 'mega');
		$params->set('menutype', $menutype);
		// create a module object to render
		$module = new \stdClass;
		$module->params = $params;
		$module->module = 'mod_menu';
		$module->id = 0;
		$module->name = 'menu';
		$module->title = 'Mega menu';

		return ModuleHelper::renderModule($module, []);
	}

}