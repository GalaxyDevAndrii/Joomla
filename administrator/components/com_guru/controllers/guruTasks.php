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
jimport ('joomla.application.component.controller');

class guruAdminControllerguruTasks extends guruAdminController {
	var $model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listTasks");
		$this->registerTask ("", "addQuiz");
		$this->registerTask ("unpublish", "publish");	
		$this->registerTask ("apply", "apply");
		$this->registerTask ("save_new", "saveAndNew");
		$this->registerTask ("ajax_request", "ajax_request");
		$this->registerTask ("ajax_request2", "ajax_request2");
		$this->registerTask ("ajax_request3", "ajax_request3");
		$this->registerTask ("check_lesson_title", "checkLessonTitle");

		$this->_model = $this->getModel("guruTask");
	}

	function listTasks() {
		$view = $this->getView("guruTasks", "html"); 
		$view->setModel($this->_model, true);
		$view->display();
	}
	
	function edit(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->editForm();
	}
	
	function editsbox(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("editformsbox");
		$view->setModel($this->_model, true);
		$view->editForm();
	}

	function addmedia(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("addmedia");
		$view->setModel($this->_model, true);
		$view->addmedia();
	}
	
	function addproject(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("addproject");
		$view->setModel($this->_model, true);
		$view->addproject();
	}

	function addscorm(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("addscorm");
		$view->setModel($this->_model, true);
		$view->addscorm();
	}

	function addQuiz(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("addquiz");
		$view->setModel($this->_model, true);
		$view->addQuiz();
	}
	
	function jumpbts(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("jumpbts");
		$view->setModel($this->_model, true);
		$view->jumpbts();
	}
	
	function jumpbts_save(){
		JFactory::getApplication()->input->set ("tmpl", "component");
		$pieces = $this->_model->jump_save();
		echo '<script type="text/javascript">window.onload=function(){
				window.parent.document.getElementById("close").click();
				window.parent.jump('.$pieces[1].','.$pieces[0].',"'.$pieces[2].'");
			}</script>';
		echo '<strong>Jump saved. Please wait...</strong>';
	}
	
	function addtext(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("addtext");
		$view->setModel($this->_model, true);
		$view->addmedia();
	}	
	
	function addmainmedia(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("addmainmedia");
		$view->setModel($this->_model, true);
		$view->addmedia();
	}
	
	function save(){
		$task = JFactory::getApplication()->input->get("task","");
		if($task=='save2'){
			$this->save2();
		} 
		else {
			$return = $this->_model->store();
			if($return["return"] === TRUE){
				$msg = JText::_('GURU_TASKS_SAVED');
			}
			else {
				$msg = JText::_('GURU_TASKS_NOTSAVED');
			}
			$link = "index.php?option=com_guru&controller=guruTasks";
			$this->setRedirect($link, $msg);
		}
	}
	
	function apply() {
		$task = JFactory::getApplication()->input->get("task","");
		$return = $this->_model->store();
		if($return["return"] === TRUE){
			$msg = JText::_('GURU_TASKS_SAVED');
		} else {
			$msg = JText::_('GURU_TASKS_NOTSAVED');
		}
		$id = JFactory::getApplication()->input->get("id","");
		$module = JFactory::getApplication()->input->get("module","");
		if($id == ""){
			$id = $return["id"];
		}
		$progrid=JFactory::getApplication()->input->get("day","");
		$link ="index.php?option=com_guru&controller=guruTasks&tmpl=component&task=editsbox&cid[]=".$id."&progrid=".$progrid."&module=".intval($module);	
		$this->setRedirect($link, $msg);
	}
	
	function saveAndNew(){
		$task = JFactory::getApplication()->input->get("task", "");
		$return = $this->_model->store();
		
		if($return["return"] === TRUE){
			$msg = JText::_('GURU_TASKS_SAVED');
		}
		else{
			$msg = JText::_('GURU_TASKS_NOTSAVED');
		}
		
		$module = JFactory::getApplication()->input->get("module", "");
		$progrid = JFactory::getApplication()->input->get("day", "");
		
		$link = "index.php?option=com_guru&controller=guruTasks&task=editsbox&tmpl=component&day=".intval($module)."&progrid=".intval($progrid)."&cid[]=";
		$this->setRedirect($link, $msg);
	}
	
	function save2(){
		JFactory::getApplication()->input->set ("tmpl", "component");	
		$return = $this->_model->store();
		if($return["return"] === TRUE){
			$msg = JText::_('GURU_TASKS_SAVED');
		} 
		else{
			$msg = JText::_('GURU_TASKS_NOTSAVED');
		}

		echo "Step saved. Please wait...";
		echo "Step saved. Please wait...";
		echo '<script type="text/javascript">window.onload=function(){
			window.parent.location.reload(true);
			}</script>';
	}
	
	function remove(){
		if(!$this->_model->delete()) {
			$msg = JText::_('GURU_TASKS_DELFAILED');
		} 
		else {
		 	$msg = JText::_('GURU_TASKS_DEL');
		}
		$link = "index.php?option=com_guru&controller=guruTasks";
		$this->setRedirect($link, $msg);		
	}
	
	function del(){ 
		$tid = JFactory::getApplication()->input->get('tid','0');	
		$main = JFactory::getApplication()->input->get('main','0');	
		$cid = JFactory::getApplication()->input->get('cid',array(), "raw");	
		$cid = intval($cid[0]);
		if(!$this->_model->delmedia($tid,$cid,$main)){
			$msg = JText::_('GURU_TASKS_DELFAILED2');
		}
		else{
		 	$msg = JText::_('GURU_TASKS_DEL2');
		}
		
		$link = "index.php?option=com_guru&controller=guruTasks&task=edit&cid[]=".$tid;
		$this->setRedirect($link, $msg);
	}

	function cancel(){
	 	$msg = JText::_('GURU_TASKS_PUBCANCEL');
		$link = "index.php?option=com_guru&controller=guruTasks";
		$this->setRedirect($link, $msg);
	}					
	
	function savemedia(){
		$insertit	= JFactory::getApplication()->input->get('idmedia','0');
		$taskid   	= JFactory::getApplication()->input->get('idtask','0');
		$mainmedia 	= JFactory::getApplication()->input->get('mainmedia','0');
		$this->_model->addmedia($insertit, $taskid, $mainmedia);
	}

	function upload() {
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->uploadimage();
		$newid = $this->_model->store();
		$link = "index.php?option=com_guru&controller=guruTasks&task=edit&cid[]=".$newid;
		$this->setRedirect($link, $msg);
	}
	
	function ajax_request(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."gurutasks".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."ajax.php");
		die();
	}
	
	function ajax_request2(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."gurutasks".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."ajaxAddMedia.php");
		die();
	}
	
	function ajax_request3(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."gurutasks".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."ajaxAddText.php");
		die();
	}

	function checkLessonTitle(){
		$db = JFactory::getDbo();

		$course_id = JFactory::getApplication()->input->get('course_id', 0, "raw");
		$lesson_id = JFactory::getApplication()->input->get('lesson_id', 0, "raw");
		$name = JFactory::getApplication()->input->get('name', "", "raw");
		$alias = JFactory::getApplication()->input->get('alias', "", "raw");

		$sql = "SELECT * FROM #__guru_days WHERE pid='" . intval($course_id) . "' ORDER BY ordering ASC";
		$db->setQuery($sql);
		$modules = $db->loadAssocList();

		if(isset($modules) && count($modules) > 0){
			foreach($modules as $module){
				$sql = "SELECT count(*)
					FROM #__guru_task t
					LEFT JOIN #__guru_mediarel m
					ON m.media_id=t.id
					LEFT JOIN #__guru_days d 
					ON m.type_id=d.id
					LEFT JOIN #__guru_program p
					ON d.pid = p.id
					WHERE d.id=".$module["id"]." and m.type='dtask' and (t.`name`='".$db->escape($name)."' OR t.`alias`='".$db->escape($alias)."') and t.`id` <> ".intval($lesson_id)."
				";
				$db->setQuery($sql);
				$db->execute();
				$count = $db->loadColumn();
				$count = @$count["0"];

				if(intval($count) != 0){
					die("0");
				}
			}
		}
		else{
			die("1");
		}

		die("1");
	}
};
?>