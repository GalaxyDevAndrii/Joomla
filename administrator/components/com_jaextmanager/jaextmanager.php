<?php
/**
 * ------------------------------------------------------------------------
 * JA Extension Manager Component for J3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

@set_time_limit(0);
// no direct access
defined('_JEXEC') or die('Restricted access');

//error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);


define('JACOMPONENT', 'com_jaextmanager');
// Require the base controller
JLoader::register('JAEMController', JPATH_COMPONENT.'/controllers/controller.php');
JLoader::register('JAEMView', JPATH_COMPONENT.'/views/view.php');
JLoader::register('JAEMModel', JPATH_COMPONENT.'/models/model.php');

if (version_compare(JVERSION, '4', 'ge')) {
	JLoader::register('JRequest', JPATH_COMPONENT . '/lib/request.php');
}

require_once (JPATH_COMPONENT . '/controller.php');
require_once (JPATH_COMPONENT . "/jaupdate.php");
$JaExtUpdatehelper = new JaExtUpdatehelper();
$JaExtUpdatehelper->update();
// Require constants
require_once (JPATH_COMPONENT . "/constants.php");

require_once (JPATH_COMPONENT .  "/helpers/menu.class.php");
require_once (JPATH_COMPONENT .  "/helpers/helper.php");
require_once (JPATH_COMPONENT .  "/helpers/jahelper.php");
require_once (JPATH_COMPONENT .  "/helpers/jauc.php");
require_once (JPATH_COMPONENT .  "/helpers/tree.php");
require_once (JPATH_COMPONENT .  "/helpers/repo.php");
require_once (JPATH_COMPONENT .  "/helpers/uploader/uploader.php");
if(jaIsJoomla3x()){
	require_once (JPATH_COMPONENT .  "/lib/simplexml.php");
}

//Check xml file only for version 2.5.3
if(JFile::exists(JPATH_COMPONENT."/installer/update/update.php")){
	require_once JPATH_COMPONENT."/installer/update/update.php";
}

// Load global stylesheets and javascript
if (!defined('JA_GLOBAL_SKIN')) {
	define('JA_GLOBAL_SKIN', 1);
	$assets = JURI::root() . 'administrator/components/com_jaextmanager/assets/';
	
	JHtml::_('behavior.modal', 'a.modal');
	
	JHtml::_('stylesheet', $assets . 'css/' . 'default.css');
	JHtml::_('stylesheet', $assets . 'css/' . 'style.css');
	JHtml::_('stylesheet', $assets . 'japopup/' . 'ja.popup.css');
	JHtml::_('stylesheet', $assets . 'jadiffviewer/' . 'diffviewer.css');
	JHtml::_('stylesheet', $assets . 'jatooltips/themes/default/' . 'style.css');
	JHtml::_('stylesheet', $assets . 'jquery.alerts/' . 'jquery.alerts.css');
	
	JHtml::_('bootstrap.popover');
	JHtml::_('script', $assets . 'js/' . 'js.cookie.js', false, true);
	JHtml::_('script', $assets . 'js/' . 'jquery.event.drag-1.4.min.js', false, true);
	JHtml::_('script', $assets . 'js/' . 'jauc.js', false, true);
	JHtml::_('script', $assets . 'js/' . 'jatree.js', false, true);
	JHtml::_('script', $assets . 'js/' . 'menu.js', false, true);
	JHtml::_('script', $assets . 'japopup/' . 'ja.popup.js', false, true);
	JHtml::_('script', $assets . 'jadiffviewer/' . 'diffviewer.js', false, true);
	JHtml::_('script', $assets . 'jquery.alerts/' . 'jquery.alerts.js', false, true);

}

// Require jaupdater library
require_once (JPATH_COMPONENT . "/lib/UpdaterClient.php");

global $compUri, $settings, $jauc;
$compUri = "index.php?option=" . JRequest::getVar('option');
$jauc = new UpdaterClient();

JToolbarHelper::title(JText::_("JOOMART_EXTENSIONS_MANAGER"));

// -----
// Require specific controller if requested
if ($controller = JRequest::getWord('view', 'components')) {
	$path = JPATH_COMPONENT . '/controllers/' . $controller . '.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$className = 'JaextmanagerController' . $controller;

$controller = new $className();

// Perform the Request task
$controller->execute(JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();
