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

class guruAdminViewguruAuthor extends JViewLegacy {
	
	function display ($tpl =  null ){	
	
		JToolBarHelper::publishList('unblock', 'GURU_APROVE');
		JToolBarHelper::unpublishList('block', 'GURU_DECLINE');
		JToolBarHelper::title(JText::_( 'GURU_AUTHORMAN'));
		//JToolBarHelper::addNew('new');
		JToolBarHelper::editList('edit');
		JToolBarHelper::deleteList(JText::_( 'GURU_AU_CONF_DELETE'));
	
		$db = JFactory::getDBO(); 

		$authorList = $this->get('Items');
		$pagination = $this->get( 'Pagination' );
		
		$filters= $this->get('Filters');
		$this->filters = $filters;	
		
		$this->authorList = $authorList;
		$this->pagination = $pagination;
		$app	= JFactory::getApplication();
		
		
		parent::display($tpl);

	}
	
	function settypeform($tpl = null){
		$id = JFactory::getApplication()->input->get("id", "0");
		if($id == "0"){
			JToolBarHelper::title(JText::_('GURU_AUTHOR').":<small>[".trim(JText::_("GURU_NEW"))."]</small>");
		}
		else{
			JToolBarHelper::title(JText::_('GURU_AUTHOR').":<small>[".trim(JText::_("GURU_EDIT"))."]</small>");
		}
		JToolBarHelper::custom('next','forward.png','forward_f2.png','Next',false);
		JToolBarHelper::cancel();
		parent::display($tpl);
	}
	
	function editform($tpl = null){
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$cid = JFactory::getApplication()->input->get("cid", "0", "raw");


		if($id == "0" && $cid == "0" ){
			JToolBarHelper::title(JText::_('GURU_AUTHOR').":<small>[".trim(JText::_("GURU_NEW"))."]</small>");
		}
		else{
			JToolBarHelper::title(JText::_('GURU_AUTHOR').":<small>[".trim(JText::_("GURU_EDIT"))."]</small>");
		}
		
		JToolBarHelper::save('save');
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel();	
			
		$user = $this->get('AuthorDetails');
		$this->user = $user;
		
		$config = $this->get('Config');
		$this->config = $config;
		
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
		
		$sql = "select `teacher_custom_fields` from #__guru_config";
		$db->setQuery($sql);
		$db->execute();
		$teacher_custom_fields = $db->loadColumn();

		if(isset($teacher_custom_fields["0"]) && trim($teacher_custom_fields["0"]) != ""){
			$teacher_custom_fields = json_decode($teacher_custom_fields["0"], true);

			if(is_array($teacher_custom_fields) && count($teacher_custom_fields) > 0){
				$teacher_custom_fields = implode(",", $teacher_custom_fields);
			}
			else{
				$teacher_custom_fields = "0";
			}
		}
		else{
			$teacher_custom_fields = "0";
		}

		$sql = "select * from #__guru_fields where `group_id`=".intval($group_id)." and `published`='1' and `id` in (".$teacher_custom_fields.") ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$db->execute();
		$fields = $db->loadAssocList();

		return $fields;
	}

}

?>