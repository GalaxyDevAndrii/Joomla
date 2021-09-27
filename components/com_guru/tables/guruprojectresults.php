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

class TableguruProjectResult extends JTable {
	var $id = null;
	var $project_id = null;
	var $student_id = null;
	var $user_id = null;
	var $file = null;
	var $created_date = null;
	var $desc		 = null;
	var $score		 = null;

	function __construct () {
		$db = JFactory::getDBO();
		parent::__construct('#__guru_project_results', 'id', $db);
	}
};
?>