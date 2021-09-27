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

$db = JFactory::getDBO();

$group_id = JFactory::getApplication()->input->get("group");
$screen_id = JFactory::getApplication()->input->get("screen");

$sql = "SELECT media_id FROM  #__guru_mediarel where type_id=" . $screen_id . " and type='scr_m'";
$db->setQuery($sql);
$result = $db->loadResult();
if ($result) {
	$sql = "UPDATE  #__guru_quiz set hide = 1 where id=" . $result;
	$db->setQuery($sql);
	$db->execute();

	$sql = "UPDATE  #__guru_program set id_final_exam = 0  WHERE id = ( SELECT pid FROM #__guru_days WHERE id = '" . $group_id . "' ) ";
	$db->setQuery($sql);
	$db->execute();

	$sql = "SELECT hasquiz FROM  #__guru_program where id = ( SELECT pid FROM #__guru_days WHERE id = '" . $group_id . "' ) ";
	$db->setQuery($sql);
	$result = $db->loadResult();

	$new_val = $result - 1;
	if ($new_val < 0) {
		$new_val = 0;
	}

	$sql = "UPDATE  #__guru_program set hasquiz =" . $new_val . "  WHERE id = ( SELECT pid FROM #__guru_days WHERE id = '" . $group_id . "' ) ";
	$db->setQuery($sql);
	$db->execute();
}

if (JComponentHelper::isEnabled('com_kunena')) {
	$query = "SELECT deleted_boards FROM #__guru_kunena_forum WHERE id =1 ";
	$db->setQuery($query);
	$deleted_boards = $db->loadResult();

	$sql = "SELECT count(*) from #__extensions where element='com_kunena'";
	$db->setQuery($sql);
	$count = $db->loadResult();

	if ($count > 0) {
		if ($deleted_boards == 1) {
			$sql = "SELECT alias FROM #__guru_task WHERE id =" . $screen_id;
			$db->setQuery($sql);
			$alias = $db->loadResult();
			$query = "DELETE FROM #__kunena_categories WHERE alias = '" . $alias . "'";
			$db->setQuery($query);
			$db->execute();

			$query = "DELETE FROM #__kunena_aliases WHERE alias = '" . $alias . "'";
			$db->setQuery($query);
			$db->execute();
		} elseif ($deleted_boards == 2) {
			$sql = "SELECT alias FROM #__guru_task WHERE id =" . $screen_id;
			$db->setQuery($sql);
			$alias = $db->loadResult();


			$query = "UPDATE #__kunena_categories set published=0 WHERE alias = '" . $alias . "'";
			$db->setQuery($query);
			$db->execute();
		}
	}
}

// deleting the old day-task relation
$sql = "DELETE FROM #__guru_mediarel 
		WHERE type='dtask' AND  media_id = " . $screen_id;
$db->setQuery($sql);
$db->execute();

//delete all the relation between this task and medias
$sql = "DELETE FROM #__guru_mediarel 
		WHERE (type='scr_m' or type='scr_t' or type='scr_l') AND type_id=" . $screen_id;
$db->setQuery($sql);
$db->execute();

// deleting the task 
$sql = "DELETE FROM #__guru_task 
		WHERE id = " . $screen_id;
$db->setQuery($sql);

$db->execute();

$sql = "SELECT influence FROM #__guru_config WHERE id = 1";
$db->setQuery($sql);
$influence = $db->loadResult(); // we have selected the INFLUENCE 

$sql = "SELECT locked FROM #__guru_days WHERE id = " . $group_id;
$db->setQuery($sql);
$locked = $db->loadResult(); // we have selected the LOCKED property for a day 		

//we need to know where in the status array is the "day" that has a future deleting screen
$sql = "SELECT ordering FROM #__guru_days WHERE id = " . $group_id;
$db->setQuery($sql);
$ordering = $db->loadResult();

$sql = "SELECT id,days,tasks FROM #__guru_programstatus 
				 WHERE pid = ( SELECT pid FROM #__guru_days WHERE id = '" . $group_id . "' ) ";
$db->setQuery($sql);
$days_array = $db->loadObjectList();

// if the INFLUENCE is active we also add the day in program_status - begin
if (($influence == 1 && $locked == 0)) {
	foreach ($days_array as $one_day_array) {
		//$one_day_array_days = $days_array->days;
		$one_day_array_tasks = $one_day_array->tasks;
		//$one_day_array_id = $days_array->id;

		$new_day_array_tasks = '';

		$removing_start_pos = strpos($one_day_array_tasks, $screen_id . '-');
		$new_day_array_tasks = substr($one_day_array_tasks, 0, $removing_start_pos);
		$new_day_array_tasks = $new_day_array_tasks . substr($one_day_array_tasks, $removing_start_pos + strlen($screen_id) + 2, strlen($one_day_array_tasks));

		$new_day_array_tasks = str_replace(';,', ';', $new_day_array_tasks);
		$new_day_array_tasks = str_replace(',;', ';', $new_day_array_tasks);
		$new_day_array_tasks = str_replace(',,', ',', $new_day_array_tasks);

		$sql = "update #__guru_programstatus set tasks='" . $new_day_array_tasks . "' where id =" . $one_day_array->id;
		$db->setQuery($sql);
		$db->execute();
	}
}

return;
$sql = "SELECT id, lesson_id, completed, pid FROM  #__guru_viewed_lesson where pid = ( SELECT pid FROM #__guru_days WHERE id = '" . $group_id . "' ) ";
$db->setQuery($sql);
$result = $db->loadAssocList();

if (isset($result) && count($result) > 0) {
	foreach ($result as $key => $value) {
		$id = $value["id"];
		$lesson_id = $value["lesson_id"];
		$completed = $value["completed"];
		$pid = $value["pid"];

		$temp = str_replace("|" . $screen_id . "|", "", $lesson_id);

		$set = "";
		$date = "";
		if ($completed == 0) {
			$sql = "SELECT id FROM #__guru_task WHERE id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (SELECT id FROM #__guru_days WHERE pid =" . intval($pid) . "))";
			$db->setQuery($sql);
			$lessons_id = $db->loadColumn();

			$temp1 = $temp;
			$temp1 = substr($temp1, 1, -1);
			$temp1 = explode("|", $temp1);

			$diff = array_diff($lessons_id, $temp1);

			if (is_array($diff) && count($diff) > 0) {
				$set = ", completed=" . $completed;
			} else {
				$completed = "1";
				$set .= ", completed=" . $completed;
				$set .= ", date_completed='" . date("Y-m-d H:i:s") . "'";
			}
		}

		$query = "UPDATE #__guru_viewed_lesson set lesson_id='" . $temp . "'" . $set . " WHERE id = " . intval($id);
		$db->setQuery($query);
		$db->execute();
	}
}
