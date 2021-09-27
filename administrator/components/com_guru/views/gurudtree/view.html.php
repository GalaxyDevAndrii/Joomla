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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport ("joomla.application.component.view");

class guruAdminViewguruDtree extends JViewLegacy {

	function showDtree($tpl = null){	
		parent::display($tpl);
	}
	
	function getPendingAuthors(){
		$db = JFactory::getDBO();
		$sql = " SELECT count(a.id) FROM #__guru_authors a, #__users u WHERE a.enabled=2 and a.userid=u.id";
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadColumn();
		return @$count["0"];
	}
	
	function getPendingOrders(){
		$db = JFactory::getDBO();
		$sql = " SELECT count(id) FROM #__guru_order where status='Pending'";
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadColumn();
		return @$count["0"];
	}
	
	function getNewAuthorsNoSelf(){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_config";
		$db->setQuery($sql);
		$db->execute();
		$configs = $db->loadAssocList();
		
		$allow_teacher_action = json_decode($configs["0"]["st_authorpage"]);//take all the allowed action from administator settings
		
		$teacher_aprove = @$allow_teacher_action->teacher_aprove; //allow or not aprove teacher
		$params = JComponentHelper::getParams('com_users');
		$useractivation = $params->get('useractivation');
		$sql = " SELECT count(a.id) FROM #__guru_authors a, #__users u WHERE a.userid=u.id and a.enabled=2 and u.registerDate >= now() and u.block=0";
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadColumn();
		return @$count["0"];
	}
	
	function getNewAuthorsNoNoneAdmin(){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_config";
		$db->setQuery($sql);
		$db->execute();
		$configs = $db->loadAssocList();
		
		$allow_teacher_action = json_decode($configs["0"]["st_authorpage"]);//take all the allowed action from administator settings
		
		$teacher_aprove = @$allow_teacher_action->teacher_aprove; //allow or not aprove teacher
		$params = JComponentHelper::getParams('com_users');
		$useractivation = $params->get('useractivation');
		$sql = " SELECT count(a.id) FROM #__guru_authors a, #__users u WHERE a.userid=u.id and a.enabled=2 and u.registerDate >= now() and u.block=1";
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadColumn();
		return @$count["0"];
	}
	
	function getNewAuthorsYes(){
		$db = JFactory::getDBO();
		$sql = " SELECT count(a.id) FROM #__guru_authors a, #__users u WHERE a.userid=u.id and u.registerDate >= now()";
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadColumn();
		return @$count["0"];
	}
	
	function getNewAuthorsYesSelf(){
		$db = JFactory::getDBO();
		$sql = " SELECT count(a.id) FROM #__guru_authors a, #__users u WHERE a.userid=u.id and u.registerDate >= now() and u.block=0";
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadColumn();
		return @$count["0"];
	}
}

?>