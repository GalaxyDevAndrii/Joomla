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

class guruAdminViewguruMediacategs extends JViewLegacy {

	function display ($tpl =  null ) { 
		JToolBarHelper::title(JText::_('GURU_MEDIACATEGS'), 'generic.png');
		JToolBarHelper::addNew('add');
		JToolBarHelper::editList();
		JToolBarHelper::custom( 'publish', 'publish', 'publish',JText::_('GURU_PUB'), true );
		JToolBarHelper::custom( 'unpublish', 'unpublish', 'unpublish', JText::_('GURU_UNPUB'), true );
		JToolBarHelper::deleteList(JText::_('GURU_SURE_DELETE_CATEGORY'));
		// Get data from the model
		$items = $this->get('Items');				
		$this->items = $items;	
		
		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
			
		parent::display($tpl);
	}
	
	function editForm($tpl = null) { 
		$id = JFactory::getApplication()->input->get("id", "");
		$task = JFactory::getApplication()->input->get("task", "");
		$lang = JText::_('GURU_MEDIA_CATEGORY_MANAGER');
		if($id !=""){
			JToolBarHelper::title($lang.JText::_('GURU_MEDIA_CATEGORY_MANAGER1'), 'generic.png');	
		}
		else{
			if($task == 'edit'){
				JToolBarHelper::title($lang.JText::_('GURU_MEDIA_CATEGORY_MANAGER1'), 'generic.png');	
			}
			else{
				JToolBarHelper::title($lang.JText::_('GURU_MEDIA_CATEGORY_MANAGER2'), 'generic.png');	
			}
		}
		JToolBarHelper::save('save');
		JToolBarHelper::apply();
		JToolBarHelper::cancel('cancel');		
		$this->categories = $this->get('Categories');
		$this->parent_id = $this->get('Parent');
		parent::display($tpl);
	}
	
	function parentCategory($parent_id){
		$model = $this->getModel();
		$categ_db = $model->getAllRows(0, 0);
		
		$return = '';
		if(is_array($categ_db) && count($categ_db) == 0){
			$return .= '<select name="parent_id">';
			$return .= 		'<option value="">(0) '.JText::_("GURU_TOP").'</option>';		
			$return .= '</select>';
		}
		else{
			$return .= '<select name="parent_id">';
			$return .= 		'<option value="">(0) '.JText::_("GURU_TOP").'</option>';
			if(isset($categ_db) && count($categ_db) > 0){
				foreach($categ_db as $key=>$val){
					$val = (object)$val;
					$id = $val->id;
					$name = $val->name;
					$line = "";
					for($i=0; $i<$val->level; $i++){
						$line .= "&#151;";
					}
					$selected = "";
					if($parent_id == $id){
						$selected = 'selected="selected"';
					}
					$return .= '<option value="'.$id.'" '.$selected.'>'.$line."(".$val->level.") ".$name.'</option>';			
				}
			}	
			$return .= '</select>';		
		}			
		return $return;
	}
};

?>