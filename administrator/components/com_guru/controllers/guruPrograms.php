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

class guruAdminControllerguruPrograms extends guruAdminController{
	var $model = null;
	
	function __construct(){
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listPrograms");
		$this->registerTask ("show", "showStudents");
		$this->registerTask( 'export_button', 'exportFile' );
		$this->registerTask ("selectCourse", "listPrograms");		
		$this->registerTask ("unpublish", "publish");
		$this->registerTask ("delete", "del");
		$this->registerTask ("saveorder", "saveorder");	
		$this->registerTask ("orderup", "orderup");
		$this->registerTask ("orderdown", "orderdown");
		$this->registerTask ("orderupfile", "orderFile");
		$this->registerTask ("orderdownfile", "orderFile");
		$this->registerTask ("saveorderfile", "saveorderFile");
		$this->registerTask ("savenbquizzes", "savenbquizzes");
		$this->registerTask ("saveOrderAjax", "saveOrderAjax");
		$this->registerTask ("saveOrderExercices", "saveOrderExercices");
		$this->registerTask ("apply32", "apply");
		$this->registerTask ("approve", "approve");
		$this->registerTask ("pending", "pending");
		$this->registerTask ("getcoursecost", "getcoursecost");
		$this->registerTask ("getplans", "getplans");
		$this->registerTask ("setrenew", "setrenew");
		$this->registerTask ("setpromo", "setpromo");
		$this->registerTask ("checkExistingUser", "checkExistingUser");
		$this->registerTask ("checkCommissionPlan", "checkCommissionPlan");
		$this->registerTask ("addcourse_ajax", "addcourse_ajax");
		$this->registerTask ("publish_un_ajax", "publish_un_ajax");
		$this->registerTask ("saveOrderG", "saveOrderG");
		$this->registerTask ("saveOrderS", "saveOrderS");
		$this->registerTask ("deleteFinalQuiz", "deleteFinalQuiz");
		$this->registerTask ("deleteGroup", "deleteGroup");
		$this->registerTask ("deleteScreen", "deleteScreen");
		$this->registerTask ("check_values", "check_values");
		$this->registerTask ("delete_course_image", "delete_course_image");
		$this->registerTask ("add_edit_lesson", "addEditLessons");
		
		$this->_model = $this->getModel("guruProgram");
	}
	
	function addEditLessons(){
		$id = JFactory::getApplication()->input->get("id", "0");
		
		$app = JFactory::getApplication();
		$app->redirect("index.php?option=com_guru&controller=guruDays&pid=".intval($id));
	}
	
	function orderFile(){
		$id = JFactory::getApplication()->input->get('id', "0");
		$this->_model->orderFile();
		$link = "index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=".intval($id);
		$this->setRedirect($link, $msg);
	}
	
	function showStudents(){
		$view = $this->getView("guruPrograms", "html"); 
		$view->setLayout("studentsenrolled");
		$view->setModel($this->_model, true);
		$view->studentsenrolled();	
	}
	
	function saveorderFile(){
		$id = JFactory::getApplication()->input->get('id', "0");
		$this->_model->saveorderFile();
		$link = "index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=".intval($id);
		$this->setRedirect($link, $msg);
	}
		
	function orderup(){
		if($this->_model->orderUp()){
			$msg = JText::_('GURU_FM_SAVE');
		}
		else{
			$msg = JText::_('GURU_FM_NOT_SAVE');
		}
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link, $msg);
	}
	
	function orderdown(){
		if($this->_model->orderDown()) {
			$msg = JText::_('GURU_FM_SAVE');
		}
		else{
			$msg = JText::_('GURU_FM_NOT_SAVE');
		}
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link, $msg);
	}

	function saveorder(){
		if($this->_model->saveOrder()){
			$msg = JText::_('GURU_FM_SAVE');
		}
		else{
			$msg = JText::_('GURU_FM_NOT_SAVE');
		}
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link, $msg);
	}
	
	function del(){
		$task2 = JFactory::getApplication()->input->get("task2", "");
		$msg = "";
		$id = JFactory::getApplication()->input->get("id");
		$cids = JFactory::getApplication()->input->get('cid', "", "raw");
		$cid = $cids[0];		

		if($task2 != NULL && $task2 != "" && $task2 == "edit"){			
			if(!$this->_model->delFileMedia($id, $cid)){
				$msg = JText::_('GURU_FM_CANTREMOVED');
			}
			else{
				$msg = JText::_('GURU_FM_REMOVED');
			}							
			$link = "index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=".$id;
			$this->setRedirect($link, $msg);
		}
		else{
			$tid = JFactory::getApplication()->input->get('tid','0', "raw");	
			$cid = JFactory::getApplication()->input->get('cid',array(), "raw");	
			$cid = intval($cid[0]);
			if (!$this->_model->delmedia($tid,$cid)) {
				$msg = JText::_('GURU_CS_CANTREMOVED');
			}
			else{
				$msg = JText::_('GURU_CS_REMOVED');
			}
			$link = "index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=".$tid;
			$this->setRedirect($link, $msg);
		}
	}
	
	function listPrograms(){		
		$view = $this->getView("guruPrograms", "html"); 
		$view->setModel($this->_model, true);
		$view->display();
	}
	
	function upload(){
		$is_sbox= JFactory::getApplication()->input->get("is_sbox","0");
		$view = $this->getView("guruPrograms", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->uploadimage();
		$progid = $this->_model->store();
		
		if($is_sbox == '1'){
			$tmpl = '&tmpl=component';
		}	
		else{
			$tmpl = '';
		}
		$link = "index.php?option=com_guru&controller=guruPrograms&task=edit".$tmpl."&cid[]=".$progid;
		$this->setRedirect($link, $msg);
	}

	function edit(){
		$view = $this->getView("guruPrograms", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
        
		$model = $this->getModel("guruSubplan");
		$view->setModel($model);

		$model = $this->getModel("guruSubremind");
		$view->setModel($model);
		$view->editForm();
	}
	
	function editsbox(){
		$view = $this->getView("guruPrograms", "html");
		$view->setLayout("editFormsbox");
		$view->setModel($this->_model, true);
		$view->editForm();
	}


	function save(){
		if($this->_model->store()){
			$msg = JText::_('GURU_CS_SAVE');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_CS_NOTSAVE');
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$empltyprice = $registry->get('empltyprice', "");
			
			if($empltyprice == 1){
				$msg = $msg." ".JText::_('GURU_NO_EMPTY_PRICE');	
			}
			$notice = 'warning';
			$registry->set('empltyprice', "");
		}
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link, $msg,$notice);
	}
	
	function apply(){
		$id = $this->_model->store();
		if(intval($id) > 0){
			$msg = JText::_('GURU_CS_APPLY_DONE');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_CS_APPLY_FAILED');
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$empltyprice = $registry->get('empltyprice', "");
			
			if($empltyprice == 1){
				$msg = $msg." ".JText::_('GURU_NO_EMPTY_PRICE');	
			}
			$notice = 'warning';
			$registry->set('empltyprice', "");
		}
		$link = "index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=".$id;
		$this->setRedirect($link, $msg, $notice);
	}

	function save2new(){
		$id = $this->_model->store();
		if(intval($id) > 0){
			$msg = JText::_('GURU_CS_APPLY_DONE');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_CS_APPLY_FAILED');
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$empltyprice = $registry->get('empltyprice', "");
			
			if($empltyprice == 1){
				$msg = $msg." ".JText::_('GURU_NO_EMPTY_PRICE');	
			}
			$notice = 'warning';
			$registry->set('empltyprice', "");
		}
		$link = "index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=0";
		$this->setRedirect($link, $msg, $notice);
	}
	
	function addmedia(){
		$view = $this->getView("guruPrograms", "html"); 
		$view->setLayout("addmedia");
		$view->setModel($this->_model, true);
		$view->addmedia();
	}

	function addcourse(){
		$view = $this->getView("guruPrograms", "html"); 
		$view->setLayout("addcourse");
		$view->setModel($this->_model, true);
		$view->addcourse();
	}

	function remove(){
		$notice = "";
		$return = $this->_model->delete();
		
		if($return === TRUE){
			$msg = JText::_('GURU_CS_REMOVED');
		}
		elseif($return === FALSE){
			$msg = JText::_('GURU_CS_CANTREMOVED');
			$notice = "error";
		} 
		else{
			$msg = JText::_('GURU_CAN_NOT_DELETE_COURSE');
			$notice = "error";
		}
		
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link, $msg, $notice);
	}

	function cancel(){
	 	$msg = JText::_('GURU_CS_OPCANC');
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link, $msg);
	}
		
	function publish(){
		$task2 = JFactory::getApplication()->input->get("task2");
		$res = "";
		$cid = JFactory::getApplication()->input->get("id");
			
		if($task2 != NULL && $task2 != "" && $task2 == "edit"){
			$res = $this->_model->publishEdit();
			if(!$res){ 
				$msg = JText::_('GURU_CS_ACTION_ERROR');
			}
			elseif($res == -1){
				$msg = JText::_('GURU_FM_UNPUBLISHED');
			}
			elseif($res == 1){
				$msg = JText::_('GURU_FM_PUBLISHED');
			}
			else{
				$msg = JText::_('GURU_CS_ACTION_ERROR');
			}				
			$link = "index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=".$cid;
			$this->setRedirect($link, $msg);
		}
		else{
			$res = $this->_model->publish();
			if(!$res){ 
				$msg = JText::_('GURU_CS_ACTION_ERROR');
			}
			elseif($res == -1){
				$msg = JText::_('GURU_CS_UNPUBLISHED');
			}
			elseif($res == 1){
				$msg = JText::_('GURU_CS_PUBLISHED');
			}
			else{
				$msg = JText::_('GURU_CS_ACTION_ERROR');
			}	
			$link = "index.php?option=com_guru&controller=guruPrograms";
			$this->setRedirect($link, $msg);
		}		
	}
		
	function unpublish(){
		$res = $this->_model->publish();
		if(!$res){
			$msg = JText::_('GURU_CS_ACTION_ERROR');
		} 
		elseif ($res == -1) {
			$msg = JText::_('GURU_CS_UNPUBLISHED');
		} 
		elseif ($res == 1) {
			$msg = JText::_('GURU_CS_PUBLISHED');
		} 
		else{
			$msg = JText::_('GURU_CS_ACTION_ERROR');
		}
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link, $msg);
	}

	function duplicate () { 
		$res = $this->_model->duplicate();
		if ($res == 1) {
			$msg = JText::_('GURU_CS_DUPLICATE_SUCC');
		}
		else{
			$msg = JText::_('GURU_CS_DUPLICATE_ERR');
		}
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link, $msg);
	}
	
	function exportFile(){
		$this->_model->exportFile();			
	}	
	function savenbquizzes(){
		$id= JFactory::getApplication()->input->get("id");
		$db = JFactory::getDBO();
		
		$sql="Select id_final_exam  from #__guru_program where id='".$id."' ";
		$db->setQuery($sql);
		$db->execute();
		$resultidfe = $db->loadColumn();
		$resultidfe = $resultidfe["0"];
		
		$sql = "SELECT id from #__guru_days where pid=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$resulids = $db->loadColumn();
		
				
		$sql = "SELECT distinct media_id from #__guru_mediarel where type_id IN(".implode(",",$resulids).") and type='dtask'";
		$db->setQuery($sql);
		$db->execute();
		$resultmids = $db->loadColumn();

		
		$sql = "SELECT distinct media_id from #__guru_mediarel where type_id IN(".implode(",",$resultmids).") and type='scr_m' and layout=12 ";
		$db->setQuery($sql);
		$db->execute();
		$resultqids = $db->loadColumn();

		$result=array_diff($resultqids, array($resultidfe));
		$result = count($result);

		$sql="Select updated from #__guru_program where id='".$id."' ";
		$db->setQuery($sql);
		$db->execute();
		$result1 = $db->loadColumn();
		$result1 = $result1["0"];
		
		$sql="Select hasquiz from #__guru_program where id='".$id."' ";
		$db->setQuery($sql);
		$db->execute();
		$result2 = $db->loadColumn();
		$result2 = $result2["0"];

		if($result != 0){
			if($result1 == 0){
				$sql="update #__guru_program set hasquiz =".$result." where id='".$id."'";
				$db->setQuery($sql);
				$db->execute();
				
				$query = "update #__guru_program set updated='1' where id='".intval($id)."'";
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
	
	public function saveOrderAjax(){
		// Get the arrays from the Request
		
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("guruProgram");
		// Save the ordering
		$return = $model->saveOrder();
		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}
	
	public function saveOrderExercices(){
		// Get the arrays from the Request
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("guruProgram");
		// Save the ordering
		$return = $model->saveorderFile();
		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}
	
	function approve(){
		$res = $this->_model->approve();
		if($res === TRUE){
			$msg = JText::_('GURU_CS_APPROVE_SUCC');
		}
		else{
			$msg = JText::_('GURU_CS_APPROVE_ERR');
		}
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link, $msg);
	}
	
	function pending(){
		$res = $this->_model->pending();
		if($res === TRUE){
			$msg = JText::_('GURU_CS_PENDING_SUCC');
		}
		else{
			$msg = JText::_('GURU_CS_PENDING_ERR');
		}
		$link = "index.php?option=com_guru&controller=guruPrograms";
		$this->setRedirect($link, $msg);
	}
	
	function deleteFinalQuiz(){
		$db = JFactory::getDBO();
		$course_id = JFactory::getApplication()->input->get("course_id", "0");
		$sql = "update #__guru_program set id_final_exam=0 where id=".intval($course_id);
		$db->setQuery($sql);
		if($db->execute()){
			return true;
		}
		else{
			return false;
		}
		die();
	}
	
	function deleteGroup(){
		$database = JFactory::getDBO();
		
		$sql = "SELECT imagesin FROM #__guru_config WHERE id ='1' ";
		$database->setQuery($sql);
		if (!$database->execute()) {
			return;
		}
		$imagesin = $database->loadResult();			
		
		$data_get = JFactory::getApplication()->input->get->getArray();
		$group_id = $data_get['group'];
		$cid = $group_id;
		
		$sql = " SELECT locked, ordering FROM #__guru_days WHERE id = ".$cid;
		$database->setQuery($sql);
		if (!$database->execute()) {
			return;
		}
		$locked_and_ordering = $database->loadObject();
		
		$locked = $locked_and_ordering->locked;
		$ordering = $locked_and_ordering->ordering;
		
		if ($locked==0) { // if locked=0
			// we delete also the DAY from program status - start
			$sql = " SELECT id, days, tasks FROM #__guru_programstatus WHERE pid = (SELECT pid FROM #__guru_days WHERE id = '".$cid."')";
			$database->setQuery($sql);
			if (!$database->execute()) {
				return;
			}
			$ids = $database->loadObjectList();
			
			foreach($ids as $one_id){
				$day_array = explode(';', $one_id->days);
				$task_array = explode(';', $one_id->tasks);
				
				$the_key_to_be_removed=0;
				foreach ($day_array as $key=>$day_item)
					{
						$day_item_expld = explode(',',$day_item);
						if($day_item_expld[0]==$cid)
							{
								unset($day_array[$key]);
								$day_array = array_values($day_array);
								unset($task_array[$key]);
								$task_array = array_values($task_array);
							}
					}
			$new_day_array = implode(';', $day_array);
			//$task_array[$ordering-1] = '';
			$new_task_array = implode(';', $task_array);
			$sql = "update #__guru_programstatus set tasks='".$new_task_array."', days='".$new_day_array."' where id =".$one_id->id;
			$database->setQuery($sql);
			$database->execute();
			}
			// we delete also the DAY from program status - stop
		
			// we delete the relations with the media - start
			$sql = "delete from #__guru_mediarel where type='dmed' and type_id=".$cid;
			$database->setQuery($sql);
			if (!$database->execute() ){
				//$this->setError($database->getErrorMsg());
				return false;
			}
			// we delete the relations with the media - stop
			
			// we delete the relations with the tasks - start
			$sql = "delete from #__guru_mediarel where type='dtask' and type_id=".$cid;
			$database->setQuery($sql);
			if (!$database->execute() ){
				//$this->setError($database->getErrorMsg());
				return false;
			}
			// we delete the relations with the tasks - stop		
		
			$sql = "SELECT pid FROM #__guru_days WHERE id = ".$cid;
			$database->setQuery($sql);
			$database->execute();	
			$prog_id = $database->loadColumn();				
			
			$sql = "update #__guru_days set ordering=(ordering - 1) where pid = '".$prog_id[0]."' AND ordering > ".$ordering;
			$database->setQuery($sql);
			$database->execute();
				
			$sql = "DELETE FROM #__guru_days WHERE id=".$cid;
			$database->setQuery($sql);	
			$database->execute();
		} // end if locked=0
	}
	
	function getcoursecost(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."ajax.php");
		die();
	}
	
	function getplans(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."ajax.php");
		die();
	}
	
	function setrenew(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."ajax.php");
		die();
	}
	
	function setpromo(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."ajax.php");
		die();
	}
	
	function checkExistingUser(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."ajax.php");
		die();
	}
	
	function checkCommissionPlan(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."ajax.php");
		die();
	}
	
	function addcourse_ajax(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."ajaxExercices.php");
		die();
	}
	
	function publish_un_ajax(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."ajaxExercices.php");
		die();
	}
	
	function saveOrderG(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."gurudays".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."saveOrderG.php");
		die();
	}
	
	function saveOrderS(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."gurudays".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."saveOrderS.php");
		die();
	}
	
	function deleteScreen(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."gurudays".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."deleteScreen.php");
		die();
	}
	
	function check_values(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."guruprograms".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."ajax.php");
		die();
	}
	
	function delete_course_image(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."guruprograms".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."ajax.php");
		die();
	}
};

?>