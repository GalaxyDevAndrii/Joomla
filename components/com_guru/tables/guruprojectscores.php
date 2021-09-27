<?php

/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class TableguruProjectScores extends JTable {
	var $id = null;
	var $course_id = null;
	var $project_id = null;
	var $user_id = null;
	var $score = null;
	var $created = null;
	var $updated = null;

	function __construct (&$db) {
		parent::__construct('#__guru_project_scores', 'id', $db);
	}

};
?>