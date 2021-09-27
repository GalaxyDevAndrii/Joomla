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

class guruViewguruOrders extends JViewLegacy {

	function display ($tpl =  null ) {
		$myorders = $this->get('MyOrders');
		$this->myorders = $myorders;
		$configs = $this->get('ConfigSettings');
		$this->configs = $configs;
		$datetype =  $this->get( 'DateType' );
		$this->datetype = $datetype;
		parent::display($tpl);
	}
	
	function myCourses($tpl =  null){
		$configs = $this->get('ConfigSettings');
		$this->configs = $configs;
		$my_courses = $this->get('MyCourses');
		$this->my_courses = $my_courses;
		parent::display($tpl);
	}
	function mycertificates($tpl =  null){
		$configs = $this->get('ConfigSettings');
		$this->configs = $configs;
		$my_courses = $this->get('MyCourses');
		$this->my_courses = $my_courses;
		parent::display($tpl);
	}
	function myQuizandfexam($tpl =  null){
		$configs = $this->get('ConfigSettings');
		$this->configs = $configs;
		parent::display($tpl);
	}
	function listquizstud($tpl =  null){
		$pid = JFactory::getApplication()->input->get('pid',"");
		$model = $this->getModel('guruorder');
		$list = $model->getlistQuizTakenStud($pid);
		$this->ads = $list;
		parent::display($tpl);
	}
	function show_quizz_res($tpl =  null){
		$list1 = $this->get('getlistQuizTakenStudF');
		$this->ads = $list1;
		parent::display($tpl);
	}	
	
	function orderDetails1($tpl =  null){
		$order = $this->get("OrderFromOrders");
		$this->order = $order;
		$this->show = false;
		parent::display($tpl);
	}
	function printcertificate($tpl =  null){
		parent::display($tpl);
	}
	function orderDetails2($tpl =  null){
		$order = $this->get("OrderFromOrders");
		$this->order = $order;
		$this->show = true;
		parent::display($tpl);
	}
	
	function getPlans(){
		$plans = $this->get("Plans");
		return $plans;
	}
	
	function countCourseOrders($id){
		$model = $this->getModel("guruorder");
		$number = $model->countCourseOrders($id);
		return $number;
	}
	
	function getConfigSettings(){
		$model = $this->getModel("guruorder");
		$configs = $model->getConfigSettings();
		return $configs;
	}

	/**
	* to show student projects from students
	*
	*/
	function myProjects($tpl =  null){
		$mainframe = JFactory::getApplication();
        $jinput= $mainframe->input;

        $filter['course_id'] = $jinput->get->get('filter_course_id','');
		$filter['keyword'] =  $jinput->get->get('filter_keyword','');

		$filter['limitstart'] = $jinput->get->get('limitstart', 0, '', 'int');
		$filter['limit'] = $jinput->get->get('limit',  $mainframe->getCfg('list_limit'),  'int');

		
        // load model
        JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_guru/models');
        
        $modelOrder = JModelLegacy::getInstance('guruOrder','guruModel');
		$courseList = $modelOrder->getMyCourses();

		$arrCourse = array();
		foreach ($courseList as $course) {
			$arrCourse[] = $course['course_id'];
		}
		$courses = implode($arrCourse, ',');

		$model = JModelLegacy::getInstance('guruProjects','guruModel');
		$my_projects = $model->getStudentProjects($courses, $filter);

        $my_courses = $this->get('MyCourses');

        $this->filter_keyword = $filter['keyword'];
		$this->filter_course_id = $filter['course_id'];

        $this->my_courses = $courseList;
		$this->my_projects = $my_projects;
		$pagination = $model->getPagination();
		$this->pagination = $pagination;
		parent::display($tpl);
	}

	/**
	* get project detail
	*
	*/
	function projectDetail($tpl = null){
		$mainframe = JFactory::getApplication();
        $jinput= $mainframe->input;

        $id = $jinput->get->get('id');

        $modelOrder = JModelLegacy::getInstance('guruOrder','guruModel');
		$courseList = $modelOrder->getMyCourses();

        require_once(JPATH_BASE.'/components/com_guru/tables/guruproject.php');
	    $projectTable =  new TableguruProject();
	    $where = array('id'=>$id);
        $projectTable->load($where);
        $projectDetail = $projectTable;
        // if project empty or not my project
        $courseList = $modelOrder->getMyCourses();
		$arrCourse = array();
		foreach ($courseList as $course) {
			$arrCourse[] = $course['course_id'];
		}
        if(empty($projectDetail) || !in_array($projectDetail->course_id,$arrCourse)){
        	return $mainframe->redirect(JRoute::_('index.php?option=com_guru&view=guruorders&layout=myprojects',false));
        }

        $this->projectDetail = $projectDetail;
		parent::display($tpl);
	}

}

?>