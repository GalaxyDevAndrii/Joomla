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
require_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");

class guruAdminViewguruCustomers extends JViewLegacy {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('GURU_MANAGEC'), 'generic.png');
		//JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_('GURU_DELETE_STUDENT'));	
		
		$customers = $this->get('Items');
		$pagination = $this->get( 'Pagination' );
		
		$this->customers = $customers;
		$this->pagination = $pagination;
		
		$filters= $this->get('Filters');
		$this->filters = $filters;
				
		parent::display($tpl);

	}
	
	function editForm($tpl = null){
		$db = JFactory::getDBO();
		$customer = $this->get('customer');

		$isNew = ($customer->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		JToolBarHelper::title(JText::_('Student').":<small>[".$text."]</small>");
		//JToolBarHelper::apply();
		//JToolBarHelper::save();
		JToolBarHelper::save('save', 'Save');
		JToolBarHelper::cancel ('cancel', 'Close');	
		parent::display($tpl);
	}
	
	function addForm($tpl = null){
		$text = JText::_('GURU_NEW');
		JToolBarHelper::title(JText::_('Student').":<small>[".$text."]</small>");
		JToolBarHelper::custom('next','forward.png','forward_f2.png', JText::_("GURU_NEXT"), false);
		JToolBarHelper::cancel();
		parent::display($tpl);
	}
	
	function getCustomerDetails($id){
		$model = $this->getModel();
		$result = $model->getCustomerDetails($id);
		return $result;
	}
	function getStudentCourses($id){
		$model = $this->getModel();
		$result = $model->getStudentCourses($id);
		return $result;
	}

	function getSequentialCourses($id){
		$model = $this->getModel();
		$result = $model->getSequentialCourses($id);
		return $result;
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