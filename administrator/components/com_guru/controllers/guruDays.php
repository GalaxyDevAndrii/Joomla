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

class guruAdminControllerguruDays extends guruAdminController {
	var $_model = NULL;
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listDays");
		$this->registerTask("newmodule", "newModule");
		$this->registerTask("save_new_module", "saveNewModule");
		$this->_model = $this->getModel("guruDays");
		$this->registerTask ("unpublish", "publish");
		$this->registerTask ("new_course", "newCourse");	
		$this->registerTask ("back", "backCourse");
		$this->registerTask ("change_access", "changeAccess");
		$this->registerTask ("save_module_admin", "save_module_admin");
		$this->registerTask ("apply", "apply");
		$this->registerTask("action", "action");
		$this->registerTask("edit_course", "editCourse");
	}

	function newCourse(){
		$this->setRedirect("index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=0");
	}
	
	function editCourse(){
		$pid = JFactory::getApplication()->input->get("pid", "0");
		$this->setRedirect("index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=".intval($pid));
	}
	
	function action(){
		$return = $this->_model->action();
		$pid = JFactory::getApplication()->input->get("pid", "0");
		JFactory::getApplication()->input->set("action", "0");
		$this->setRedirect("index.php?option=com_guru&controller=guruDays&pid=".intval($pid));
	}
	
	function listDays(){
		$view = $this->getView("guruDays", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function edit(){
		$view = $this->getView("guruDays", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
	
		$model = $this->getModel("guruDays");
		$view->setModel($model);
		$view->editForm();
	}

	function newModule(){
		$view = $this->getView("guruDays", "html");
		$view->setLayout("newModule");
		$view->newModule();
	}

	function remove(){
		if(!$this->_model->delete()){
			$msg = JText::_('GURU_DAY_REMERR');
		}
		else{
		 	$msg = JText::_('GURU_DAY_REMSUCC');
		}
		$link = "index.php?option=com_guru&controller=guruDays";
		$this->setRedirect($link, $msg);
	}

	
	function publish(){
		$res = $this->_model->publish();
		if(!$res){
			$msg = JText::_('GURU_DAY_ACTION_ERROR');
		} 
		elseif($res == -1){
		 	$msg = JText::_('GURU_DAY_UNPUBLISH_DAYS');
		}
		elseif ($res == 1){
			$msg = JText::_('GURU_DAY_PUBLISH_DAYS');
		} 
		else{
            $msg = JText::_('GURU_DAY_ACTION_ERROR');
		}
		
		$link = "index.php?option=com_guru&controller=guruDays";
		$this->setRedirect($link, $msg);
	}	
	
	function save_module_admin(){
		$pid = JFactory::getApplication()->input->get("pid","0");
		if ($this->_model->store()){
			$msg = JText::_('GURU_DAY_SAVE');
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('saved_new', "1");
		}
		else{
			$msg = JText::_('GURU_DAY_NOTSAVE');
		}
		$link = "index.php?option=com_guru&controller=guruDays&pid=".$pid;
		echo "	<script> 
					window.parent.location.href=\"".JURI::base()."index.php?option=com_guru&controller=guruDays&pid=".$pid."\";
					window.parent.document.getElementById('close').click();

				</script>";
		exit();
	}
	
	function saveNewModule(){
		$pid = JFactory::getApplication()->input->get("pid", "0");
		if($this->_model->store_new_module()){
			$msg = JText::_('GURU_DAY_SAVE');
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('saved_new', "1");
		}
		else{
			$msg = JText::_('GURU_DAY_NOTSAVE');
		}
		echo "	<script> 
					window.parent.location.href=\"".JURI::base()."index.php?option=com_guru&controller=guruDays&pid=".$pid."\";
					window.parent.document.getElementById('close').click();

				</script>";
	}
	
	function cancel(){
	 	$msg = JText::_('GURU_DAY_CANCEL');
		$link = "index.php?option=com_guru&controller=guruDays";
		$this->setRedirect($link, $msg);
	}
	
	function upload(){
		$view = $this->getView("guruDays", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->uploadimage();
		$newid = $this->_model->store();
		$link = "index.php?option=com_guru&controller=guruDays&tmpl=component&task=edit&cid[]=".$newid;
		$this->setRedirect($link, $msg);
	}

	function addtask(){
		$view = $this->getView("guruDays", "html");
		$view->setLayout("addtask");
		$view->setModel($this->_model, true);
		$view->addtask();
	}
	
	function addmainmedia(){
		$view = $this->getView("guruDays", "html");
		$view->setLayout("addmainmedia");
		$view->setModel($this->_model, true);
		$view->addmainmedia();
	}
	
	function savetask(){
		$insertit 	= JFactory::getApplication()->input->get('idmedia', "0");
		$taskid 	= JFactory::getApplication()->input->get('idtask',"0");
		$mainmedia 	= JFactory::getApplication()->input->get('mainmedia',"0");
		$this->_model->addtask($insertit, $taskid, $mainmedia);
	}
	
	function savemedia(){
		$insertit 	= JFactory::getApplication()->input->get('idmedia',"0");
		$taskid   	= JFactory::getApplication()->input->get('idtask',"0");
		$mainmedia 	= JFactory::getApplication()->input->get('mainmedia',"0");
		$this->_model->addtask($insertit, $taskid, $mainmedia);
	}	
	
	function deltask(){
		$tid = JFactory::getApplication()->input->get('tid','0', "raw");	
		$cid = JFactory::getApplication()->input->get('cid','0', "raw");
		
		if (!$this->_model->deltask($tid,$cid)){
			$msg = JText::_('GURU_DAY_ACTION_FAILED');
		} 
		else{
		 	$msg = JText::_('GURU_DAY_TASK_REMOVED');
		}
		$link = "index.php?option=com_guru&controller=guruDays&task=edit&cid[]=".$tid;
		$this->setRedirect($link, $msg);
	}	
	
	function delmedia(){
		$tid = JFactory::getApplication()->input->get('tid','0', "raw");	
		$cid = JFactory::getApplication()->input->get('cid','0', "raw");	

		if(!$this->_model->delmedia($tid,$cid)){
			$msg = JText::_('GURU_DAY_ACTION_FAILED');
		} 
		else{
		 	$msg = JText::_('GURU_DAY_MEDIA_REMOVED');
		}
		$link = "index.php?option=com_guru&controller=guruDays&task=edit&cid[]=".$tid;
		$this->setRedirect($link, $msg);
	}	
	
	function Duplicate(){
		$view = $this->getView("guruDays", "html");
		$view->setLayout("duplicate");
		$view->setModel($this->_model, true);
		$view->duplicate();
	}	
	
	function make_duplicate(){
		$the_days=JFactory::getApplication()->input->get("the_days","");
		$the_days = substr($the_days, 0, strlen($the_days)-1);
		$the_days = explode(',', $the_days);
		$the_program_id = JFactory::getApplication()->input->get('program_id',"0");
	
		$db = JFactory::getDBO();
	
		$sql = "SELECT count(id) FROM #__guru_days WHERE pid = ".$the_program_id;
		$db->setQuery($sql);
		$day_order = $db->loadColumn();
		$day_order = $day_order["0"];
	
		$sql = "SELECT imagesin,influence FROM #__guru_config WHERE id = 1";
		$db->setQuery($sql);
		$configs = $db->loadObject();	
	
		foreach($the_days as $one_day) {
			$sql = "SELECT * FROM #__guru_days WHERE id = ".$one_day;
			$db->setQuery($sql);
			$the_day_object = $db->loadObject();
		
			$new_image = $the_day_object->image;
			if($the_day_object->image!=''){
				$new_image = 'copy_'.$the_day_object->image;
				// do a copy of the image on the server
				copy(JPATH_SITE.'/'.$configs->imagesin.'/'.$the_day_object->image, JPATH_SITE.'/'.$configs->imagesin.'/'.$new_image);
			}

			$sql = "INSERT INTO #__guru_days 
											( 
												pid, 
												title, 
												description, 
												image, 
												published,
												startpublish,
												endpublish,
												metatitle,
												metakwd,
												metadesc,
												afterfinish,
												url,
												pagetitle,
												pagecontent,
												ordering,
												locked
									) VALUES (
												'".$the_program_id."', 
												'".$the_day_object->title."', 
												'".$the_day_object->description."' , 
												'".$new_image."', 
												'".$the_day_object->published."',
												'".$the_day_object->startpublish."',
												'".$the_day_object->endpublish."',
												'".$the_day_object->metatitle."',
												'".$the_day_object->metakwd."',
												'".$the_day_object->metadesc."',
												'".$the_day_object->afterfinish."',
												'".$the_day_object->url."',
												'".$the_day_object->pagetitle."',
												'".$the_day_object->pagecontent."',
												'".($day_order+1)."',
												'".$the_day_object->locked."'												
											)";
			$db->setQuery($sql);
			if (!$db->execute() ){
				//$this->setError($db->getErrorMsg());
				return false;
			}
		
			$sql = "SELECT max(id) FROM #__guru_days";
			$db->setQuery($sql);
			$the_day_copy_id = $db->loadColumn();
			$the_day_copy_id = $the_day_copy_id["0"];
		
			// we duplicate now the tasks + media (inside mediarel table) - BEGIN
			$sql = "SELECT * FROM #__guru_mediarel WHERE type_id = ".$one_day;
			$db->setQuery($sql);
			$media_rel_object_list = $db->loadObjectList();
		
			$task_list = '';
			foreach($media_rel_object_list as $media_rel_object){
				$sql = "INSERT INTO #__guru_mediarel 
													( 
														type, 
														type_id, 
														media_id, 
														mainmedia
											) VALUES (
														'".$media_rel_object->type."', 
														'".$the_day_copy_id."', 
														'".$media_rel_object->media_id."' , 
														'".$media_rel_object->mainmedia."'												
													)";
				$db->setQuery($sql);
				if (!$db->execute() ){
					//$this->setError($db->getErrorMsg());
					return false;
				}
				
				if($media_rel_object->type == 'dtask')	
					$task_list = $task_list.$media_rel_object->media_id.',0-';			
			}
			$task_list = $task_list.';';	
			// we duplicate now the tasks + media (inside mediarel table) - END		
			$influence = $configs->influence; // we have selected the INFLUENCE		
			// if the influence is ON then we add the duplicated days to program_status - begin
			if($influence == 1){
				$sql = "SELECT days,tasks,id FROM #__guru_programstatus WHERE pid = ".$the_program_id;
				$db->setQuery($sql);
				$status_object_list = $db->loadObjectList();				
				foreach($status_object_list as $one_status){
					$new_days = $one_status->days.$the_day_copy_id.',0;';
					$new_tasks = $one_status->tasks.$task_list;
					
					$sql = "UPDATE #__guru_programstatus SET 
							days='".$new_days."', tasks = '".$new_tasks."' 
							where id = '".$one_status->id."' ";
					$db->setQuery($sql);
					$db->execute();
				}
			}
			// if the influence is ON then we add the duplicated days to program_status - end		
			$day_order++;
		}
		$msg = JText::_('GURU_DAY_DUPLICATE_SUCC');
		$link = "index.php?option=com_guru&controller=guruDays&pid=".$the_program_id;
		$this->setRedirect($link, $msg);	
	}
	
	function backCourse(){
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link);	
	}
	
	function changeAccess(){
		$pid = JFactory::getApplication()->input->get('pid', '0');
		$msg = "";
		
		if(!$this->_model->changeAccess()){
			$msg = JText::_('GURU_CHANGED_ACCESS_UNSUCCESSFULLY');
		} 
		else{
		 	$msg = JText::_('GURU_CHANGED_ACCESS_SUCCESSFULLY');
		}
		$link = "index.php?option=com_guru&controller=guruDays&pid=".$pid;

		$this->setRedirect($link, $msg);
	}
	
	function save(){
		$return = $this->_model->saveCourse();
		
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link);
	}
	function apply(){
		$pid    = JFactory::getApplication()->input->get("pid","0");
		$return = $this->_model->saveCourse();
		
		$link = "index.php?option=com_guru&controller=guruDays&pid=".$pid;
		$this->setRedirect($link);
	}
};
?>