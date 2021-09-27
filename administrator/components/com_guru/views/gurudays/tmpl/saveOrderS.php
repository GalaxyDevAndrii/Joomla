<?php

/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

$db = JFactory::getDbo();
$input = JFactory::getApplication()->input;
$pid = $input->getInt('pid', 0);
$ordering = $input->get('ordering', '', 'raw');
$ordering = (array) @json_decode($ordering);

$query =  "SELECT `id` FROM `#__guru_days` WHERE `pid`=" . $db->q($pid);
$days = $db->setQuery($query)->loadColumn();

if (!$days) {
	die;
}

$query = "SELECT DISTINCT `media_id` FROM `#__guru_mediarel` WHERE `type`='dtask' AND `type_id` in (".implode(',', $days).")";
$tasks = $db->setQuery($query)->loadColumn();

$arrangedList = array();
foreach ($ordering as $id) {
	if (!in_array($id, $tasks)) {
		continue;
	}

	$arrangedList[] = $id;
}

foreach ($tasks as $id) {
	if (!in_array($id, $arrangedList)) {
		$arrangedList[] = $id;
	}
}

foreach ($arrangedList as $key => $id) {
	$order = $key + 1;
	$query = "UPDATE `#__guru_task` SET `ordering` = $order WHERE `id` = $id";
	$db->setQuery($query)->execute();
}

$modules = $input->get('modules', '', 'raw');
$modules = (array) @json_decode($modules);
foreach ($modules as $module) {
	foreach ($module->leafs as $leaf) {
		if (!in_array($leaf, $tasks)) {
			continue;
		}

		$query = "UPDATE `#__guru_mediarel` 
			SET `type_id` = {$db->q($module->id)} 
			WHERE `type` = 'dtask'
			AND `media_id` = $leaf";
		
		$db->setQuery($query)->execute();
	}
}

die;
