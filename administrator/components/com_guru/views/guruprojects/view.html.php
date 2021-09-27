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

class guruAdminViewguruProjects extends JViewLegacy {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('GURU_Q_PROJECTS_MANAGER'), 'generic.png');		
		//JToolBarHelper::publishList();
		//JToolBarHelper::unpublishList();
		//JToolBarHelper::deleteList(JText::_("GURU_DELETE_QUIZ_RESULTS"), 'deletequizresult', JText::_("GURU_CLEAR_RESULTS"));
		//JToolBarHelper::addNew('editZ',JText::_('GURU_NEW_Q_BTN'));
		JToolBarHelper::addNew('new', JText::_('GURU_NEW_Q_BTN'));
		//JToolBarHelper::addNew('duplicate', JText::_('GURU_DUPLICATE_Q_BTN'));
		//JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_("GURU_SURE_DELETE_PROJECT"));

		$mainframe = JFactory::getApplication();
        $jinput= $mainframe->input;
        
		$user = JFactory::getUser();
		
		$filter['course_id'] = $jinput->get->get('filter_course_id','');
		$filter['keyword'] =  $jinput->get->get('filter_keyword','');
		$filter['limitstart'] = $jinput->get->get('limitstart', 0, '', 'int');
		$filter['limit'] = $jinput->get->get('limit',  $mainframe->getCfg('list_limit'),  'int');

		$model = $this->getModel('guruProjects');
		//$listProjects = $model->getProjects($filter);
		$listProjects = $model->getItems();

		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		$this->listProjects = $listProjects;
		parent::display($tpl);
	}

	function edit($tpl = null){
		$model = $this->getModel('guruProjects');
		$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
		$jnow = new JDate('now');
		$jnow->setTimezone($timezone);
		$start = $jnow->toSQL(true);

		$project = array("id"=>"0", "course_id"=>"0", "author_id"=>"0", "title"=>"", "description"=>"", "file"=>"", "created"=>$start, "updated"=>"", "start"=>$start, "end"=>"", "layout"=>"", "published"=>"0");

		$cid = JFactory::getApplication()->input->get("cid", array(), "raw");

		if(isset($cid) && isset($cid["0"]) && intval($cid["0"]) > 0){
			$project_temp = $model->getProject(intval($cid["0"]));

			if(isset($project_temp) && is_array($project_temp) && count($project_temp) > 0){
				$project = $project_temp;
			}
		}

		$teachers = $model->getTeachers();
		$courses = $model->getCourses($project["author_id"]);

		$this->project = $project;
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

		$model = $this->getModel('guruProjects');
		$id = JFactory::getApplication()->input->get("id", "0", "raw");

		$project = $model->getProject($id);
		$this->project = $project;

		$mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        
		$user = JFactory::getUser();
		
		$filter['limitstart'] = $jinput->get->get('limitstart', 0, '', 'int');
		$filter['limit'] = $jinput->get->get('limit',  $mainframe->getCfg('list_limit'),  'int');
		$filter['id'] = intval($id);

		//$listProjectsResults = $model->getProjectsResults($filter);
		$listProjectsResults = $model->getItems();

		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		$this->listProjectsResults = $listProjectsResults;

		parent::display($tpl);
	}
}
?>