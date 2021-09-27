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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport ("joomla.application.component.view");

class guruAdminViewguruFields extends JViewLegacy {

	function display ($tpl =  null ) { 
		JToolBarHelper::title(JText::_('GURU_FIELDSMAN'), 'generic.png');

		$bar = JToolbar::getInstance('toolbar');
		
		$url_field = JURI::root()."administrator/index.php?option=com_guru&task=new_field&controller=guruFields&tmpl=component";
		$bar->appendButton('Popup', 'new', 'GURU_NEW_FIELD', $url_field);

		$url_group = JURI::root()."administrator/index.php?option=com_guru&task=new_group&controller=guruFields&tmpl=component";
		$bar->appendButton('Popup', 'new-group', 'GURU_NEW_GROUP', $url_group);

		JToolbarHelper::cancel('cancel');
		
		$groups = $this->get('Groups');
		$this->groups = $groups;

		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;	
		
		parent::display($tpl);
	}

	function getFields($group_id){
		$model = $this->getModel();
		$fields = $model->getFields($group_id);

		return $fields;
	}

	function newField($tpl =  null ){
		$field = $this->get('Field');
		$this->field = $field;

		$groups = $this->get('Groups');
		$this->groups = $groups;

		parent::display($tpl);
	}

	function newGroup($tpl =  null ){
		$group = $this->get('Group');
		$this->group = $group;

		parent::display($tpl);
	}
}

?>