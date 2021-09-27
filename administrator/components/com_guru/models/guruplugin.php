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

jimport ("joomla.aplication.component.model");


class guruAdminModelguruPlugin extends JModelLegacy {
	
	function __construct () {
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");		
	}

	function getlistPlugins () {
		$db = JFactory::getDBO();
		$sql = "select * from #__extensions where folder='gurupayment'";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}

	function publish(){
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('id', array(0));
		$task = JFactory::getApplication()->input->get('task', '');  
		if($task == 'publish'){
			$sql = "update #__extensions set enabled='1' where extension_id in ('".$cids."')";
			$res = 1;
		}
		else{
			$sql = "update #__extensions set enabled='0' where extension_id in ('".$cids."')";
			$res = -1;
		}			
		$db->setQuery($sql);
		if(!$db->execute()){
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return $res;
	}
};
?>