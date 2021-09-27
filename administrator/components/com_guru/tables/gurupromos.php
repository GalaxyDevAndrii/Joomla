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

class TableguruPromos extends JTable {
	var $id = null;
	var $title = null;
	var $code = null;
	var $codelimit = null;
	var $codeused = null;
	var $discount = null;
	var $codestart = null;
	var $codeend = null;
	var $forexisting = null;
	var $published = null;
	var $typediscount = null;
	
	function __construct (&$db) {
		parent::__construct('#__guru_promos', 'id', $db);
	}

	function store ($updateNulls=true) {
		$res = parent::store();
		if (!$res) return $res;

		return true;		
	}

	
};


?>