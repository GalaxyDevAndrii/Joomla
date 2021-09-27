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

jimport ("joomla.application.component.view");

class guruViewguruLogin extends JViewLegacy {

	function display($tpl = null){
		parent::display($tpl);
	}
	
	function editForm($tpl = null) {
	
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_config";
		$db->setQuery($sql);
		$db->execute();
		$configs = $db->loadAssocList();
		$this->configs = $configs;
		
			
		parent::display($tpl);
	}
	
	function terms($tpl = null) {
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_config";
		$db->setQuery($sql);
		$db->execute();
		$configs = $db->loadAssocList();
		$this->configs = $configs;
		
		parent::display($tpl);
	}

	function getCustomFieldsGroups(){
		$db = JFactory::getDbo();
		
		$sql = "select * from #__guru_groups where `published`='1' ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$db->execute();
		$groups = $db->loadAssocList();

		return $groups;
	}
	
	function getCustomFields($group_id){
		$db = JFactory::getDbo();
		
		$sql = "select `student_custom_fields` from #__guru_config";
		$db->setQuery($sql);
		$db->execute();
		$student_custom_fields = $db->loadColumn();

		if(isset($student_custom_fields["0"]) && trim($student_custom_fields["0"]) != ""){
			$student_custom_fields = json_decode($student_custom_fields["0"], true);

			if(is_array($student_custom_fields) && count($student_custom_fields) > 0){
				$student_custom_fields = implode(",", $student_custom_fields);
			}
			else{
				$student_custom_fields = "0";
			}
		}
		else{
			$student_custom_fields = "0";
		}

		$sql = "select * from #__guru_fields where `group_id`=".intval($group_id)." and `published`='1' and `id` in (".$student_custom_fields.") ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$db->execute();
		$fields = $db->loadAssocList();

		return $fields;
	}
}

?>