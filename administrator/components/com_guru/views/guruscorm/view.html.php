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

class guruAdminViewguruScorm extends JViewLegacy {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('GURU_Q_SCORM_MANAGER'), 'generic.png');		
		//JToolBarHelper::publishList();
		//JToolBarHelper::unpublishList();
		//JToolBarHelper::deleteList(JText::_("GURU_DELETE_QUIZ_RESULTS"), 'deletequizresult', JText::_("GURU_CLEAR_RESULTS"));
		//JToolBarHelper::addNew('editZ',JText::_('GURU_NEW_Q_BTN'));
		JToolBarHelper::addNew('new', JText::_('GURU_NEW_Q_BTN'));
		//JToolBarHelper::addNew('duplicate', JText::_('GURU_DUPLICATE_Q_BTN'));
		//JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_("GURU_SURE_DELETE_SCORM"));

		$mainframe = JFactory::getApplication();
        $jinput= $mainframe->input;
        
		$user = JFactory::getUser();
		
		$filter['course_id'] = $jinput->get->get('filter_course_id','');
		$filter['keyword'] =  $jinput->get->get('filter_keyword','');
		$filter['limitstart'] = $jinput->get->get('limitstart', 0, '', 'int');
		$filter['limit'] = $jinput->get->get('limit',  $mainframe->getCfg('list_limit'),  'int');

		$model = $this->getModel('guruScorm');
		$listScorm = $model->getItems();

		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		$this->listScorm = $listScorm;
		parent::display($tpl);
	}

	function edit($tpl = null){
		$model = $this->getModel('guruScorm');
		$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
		$jnow = new JDate('now');
		$jnow->setTimezone($timezone);
		$start = $jnow->toSQL(true);

		$scorm = array("id"=>"0", "course_id"=>"0", "author_id"=>"0", "title"=>"", "description"=>"", "file"=>"", "created"=>$start, "updated"=>"", "start"=>$start, "end"=>"", "layout"=>"", "published"=>"0");

		$cid = JFactory::getApplication()->input->get("cid", array(), "raw");

		if(isset($cid) && isset($cid["0"]) && intval($cid["0"]) > 0){
			$scorm_temp = $model->getScorm(intval($cid["0"]));

			if(isset($scorm_temp) && is_array($scorm_temp) && count($scorm_temp) > 0){
				$scorm = $scorm_temp;
			}
		}

		$teachers = $model->getTeachers();
		$courses = $model->getCourses($scorm["author_id"]);

		$this->scorm = $scorm;
		$this->teachers = $teachers;
		$this->courses = $courses;

		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel ();

		parent::display($tpl);
	}

	function results($tpl = null){
		JToolBarHelper::custom('saveResults', 'publish', 'publish', JText::_('GURU_SAVE'), false);
		JToolBarHelper::cancel ();

		$model = $this->getModel('guruScorm');
		$id = JFactory::getApplication()->input->get("id", "0", "raw");

		$scorm = $model->getScorm($id);
		$this->scorm = $scorm;

		$mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        
		$user = JFactory::getUser();
		
		$filter['limitstart'] = $jinput->get->get('limitstart', 0, '', 'int');
		$filter['limit'] = $jinput->get->get('limit',  $mainframe->getCfg('list_limit'),  'int');
		$filter['id'] = intval($id);

		$listScormResults = $model->getItems();

		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		$this->listScormResults = $listScormResults;

		parent::display($tpl);
	}
}
?>