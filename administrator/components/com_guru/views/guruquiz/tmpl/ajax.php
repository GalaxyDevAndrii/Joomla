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
$input = JFactory::getApplication()->input;
$action = $input->get("action", "");

if ($action == "publish") {
	$v = $input->get("v", "0");
	$table = "#__guru_questions_v3";
	if ($v == 1) {
		$table = "#__guru_quiz";
	}

	$id = $input->get("id", "0");
	$sql = "UPDATE $table SET published='1' WHERE id=" . intval($id);
	$db->setQuery($sql);
	if (!$db->execute()) {
		return false;
	}
	return true;
	die();
} elseif ($action == "unpublish") {
	$v = $input->get("v", "0");
	$table = "#__guru_questions_v3";
	if ($v == 1) {
		$table = "#__guru_quiz";
	}

	$id = $input->get("id", "0");
	$sql = "UPDATE $table SET published='0' WHERE id=" . intval($id);
	$db->setQuery($sql);
	if (!$db->execute()) {
		return false;
	}
	return true;
	die($sql);
}

$deleted = $input->get('deleted');
$f = $input->get('f');
$id = $input->getInt('id', 0);
if ($f == 0) {
	if ($id) {
		$query = "DELETE FROM `#__guru_questions_v3` WHERE id=" . $deleted . " AND qid=" . $id . "";
		$db->setQuery($query);
		if ($db->execute()) {
			echo "2";
		} else {
			echo $query;
		}
	}
} elseif ($f == 1) {
	$query = "SELECT quizzes_ids FROM `#__guru_quizzes_final` WHERE qid=" . $id;
	$db->setQuery($query);
	$result = $db->loadResult();
	$ids = explode(',', $result);
	$newIds = array_filter($ids, function($i) use ($deleted) {
		return $i != $deleted;
	});

	if ($newIds) {
		$query = "UPDATE `#__guru_quizzes_final` SET quizzes_ids='" . implode(',', $newIds) . "' WHERE qid=" . $id;
		$db->setQuery($query);
		if ($db->execute()) {
			echo "2";
		} else {
			echo $query;
		}
	} else {
		$query = "DELETE FROM `#__guru_quizzes_final` WHERE qid = $id";
		$db->setQuery($query)->execute();
		echo '2';
	}
}
