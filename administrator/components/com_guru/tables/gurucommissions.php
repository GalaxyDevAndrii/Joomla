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

class TableguruCommissions extends JTable {
	var $id = null;
	var $default_commission = null;
	var $commission_plan = null;
	var $teacher_earnings = null;
	
	
	function __construct ($db) {
		parent::__construct('#__guru_commissions', 'id', $db);
	}

	function store ($updateNulls=true) {
		$res = parent::store();
		if (!$res) return $res;

		return true;		
	}

	
};


?>