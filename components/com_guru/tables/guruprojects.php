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

class TableGuruProjects extends JTable {
	var $id = null;
	var $title = null;
	var $course_id = null;
	var $author_id = null;
	var $file = null;
	var $created = null;
	var $updated = null;
	var $start = null;
	var $end = null;
	var $layout = null;
	var $published = null;
	var $description = null;

	function __construct (&$db) {
		parent::__construct('#__guru_projects', 'id', $db);
	}



};
?>