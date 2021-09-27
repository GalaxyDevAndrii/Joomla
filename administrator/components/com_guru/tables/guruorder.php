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

class TableguruOrder extends JTable {
	var $id = null;
	var $oid = null;
	var $userid = null;
	var $programid = null;
	var $amount = null;
	var $date = null;
	var $payment = null;
	var $published = null;
	var $promocodeid = null;

	function __construct (&$db) {
		parent::__construct('#__guru_order', 'id', $db);
	}

};


?>