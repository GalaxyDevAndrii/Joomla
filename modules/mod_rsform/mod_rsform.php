<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Check if the helper exists
$helper = JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php';
if (file_exists($helper)) {
	// Load Helper functions
	require_once $helper;

	// Params
	$formId			 = (int) $params->def('formId', 1);
	$moduleclass_sfx = $params->def('moduleclass_sfx', '');

	$lang = JFactory::getLanguage();
	$lang->load('com_rsform', JPATH_SITE);

	// Display template
	require JModuleHelper::getLayoutPath('mod_rsform');
}