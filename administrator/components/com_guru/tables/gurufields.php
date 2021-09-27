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

class TableguruFields extends JTable {
	var $id = null;
	var $name = null;
	var $type = null;
	var $field_code = null;
	var $published = null;
	var $required = null;
	var $ordering = null;
	var $group_id = null;
	var $options = null;
	
	function __construct (&$db) {
		parent::__construct('#__guru_fields', 'id', $db);
	}
};

?>