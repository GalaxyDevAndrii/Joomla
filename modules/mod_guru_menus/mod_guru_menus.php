<?php
/*------------------------------------------------------------------------
# com_publisher
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');
	
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root()."modules/mod_guru_menus/guru_menus.css");
$document->addScript(JURI::root()."modules/mod_guru_menus/guru_menus.js");

$helper = new modGuruMenusHelper();
$result = $helper->getGuruCategories($params);
require(JModuleHelper::getLayoutPath('mod_guru_menus'));
?>
