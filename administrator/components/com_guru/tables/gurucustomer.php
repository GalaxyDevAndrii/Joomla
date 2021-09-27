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

class TableguruCustomer extends JTable {
	var $id = null;
	var $user_id = null;
	var $fullname = null;

	function __construct (&$db) {
		parent::__construct('#__guru_customer', 'id', $db);
	}

	function load ($id = 0, $reset = true) {
		$db = JFactory::getDBO();
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		$sql = "select id from #__guru_customer where id='".$id."'";
		$db->setQuery($sql);
		$realid = $db->loadResult();
		if (isset($data_post['cid'])) $realid = $id;
		parent::load($realid,true);
	}

	function store($updateNulls = false){ 
		$db = JFactory::getDBO(); 
		parent::store(true);
		return true;
	}

};


?>