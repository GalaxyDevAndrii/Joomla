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


class guruControllerguruProjects extends guruController {

	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->_model = $this->getModel("guruProjects");
	}


	/**
	* update score for student
	*
	*/
	function updateScore() {
		
	}


	/**
	* delete project
	*
	*/
	function delete(){
		
	}

	/**
	* save project from add new or edit
	*
	*/
	function saveProject () {
		// check token
		JSession::checkToken() or jexit(JText::_('GURU_INVALID_TOKEN'));
		$mainframe = JFactory::getApplication("site");
        $jinput= $mainframe->input;
		$id = $jinput->post->get('id');
		$action = $jinput->post->get('action');
		$user = JFactory::getUser();

		if(trim($id) == ""){
			$id = 0;
		}

        require_once(JPATH_BASE.'/components/com_guru/tables/guruproject.php');
        $projectTable =  new TableguruProject();//JTable::getInstance('TableguruProject');
        $projectTable->id = $id;
        $projectTable->course_id = $jinput->post->get('course_id');
        $projectTable->title = $jinput->post->get('title','','raw');
        $projectTable->author_id = $user->id;
        $projectTable->file = $jinput->post->get('file','','raw');
        $projectTable->description = $jinput->post->get('description','','raw');
		$projectTable->start = $jinput->post->get('start','','raw');
        $projectTable->end = $jinput->post->get('end','','raw');
        $projectTable->published = $jinput->post->get('published','0','raw');
        
        if($projectTable->end == ""){
			$projectTable->end = '0000-00-00 00:00:00';
		}

		if($projectTable->updated == ""){
			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
			$jnow = new JDate('now');
			$jnow->setTimezone($timezone);
			$projectTable->updated = $jnow->toSQL(true);
		}

		if($projectTable->created == ""){
			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
			$jnow = new JDate('now');
			$jnow->setTimezone($timezone);
			$projectTable->created = $jnow->toSQL(true);
		}

		if($projectTable->layout == ""){
			$projectTable->layout = " ";
		}

        if($projectTable->store()){
        	$idParam = !empty($projectTable->id)?'&id='.$projectTable->id:'';

        	if($action == 'apply'){
        		$this->setRedirect( JRoute::_('index.php?option=com_guru&view=guruauthor&task=projectForm&layout=projectForm&'.$idParam,false), JText::_('GURU_SUCC_SAVE'), 'success');
        	}
        	else{
        		$this->setRedirect( JRoute::_('index.php?option=com_guru&view=guruauthor&task=projects&layout=projects',false), JText::_('GURU_SUCC_SAVE'), 'success');
        	}
        }
        else{
        	$idParam = !empty($projectTable->id)?'&id='.$projectTable->id:'';
        	$this->setRedirect( JRoute::_('index.php?option=com_guru&view=guruauthor&task=projectForm&layout=projectForm&'.$idParam,false), JText::_('GURU_ERR_SAVE'), 'error');
        }
        return false;
	}

	/**
	* delete projects 
	*
	*/
	function deleteProject(){
		JSession::checkToken() or jexit(JText::_('GURU_INVALID_TOKEN'));
		$mainframe = JFactory::getApplication();
		$jinput= $mainframe->input;
		$ids = $jinput->post->get('cid');

		require_once(JPATH_BASE.'/components/com_guru/tables/guruproject.php');
        $projectTable =  new TableguruProject();
        $message = '';
        if(!empty($ids)){
        	foreach ($ids as $id) {
        		$where = array('id'=>$id);
	        	$projectTable->load($where);
	        	// file need to be deleted
	        	$file = $projectTable->file;
	        	
	        	if($projectTable->delete($where)){
	        		JFile::delete(JPATH_BASE.'/'.$file);
	        		$message = JText::_('GURU_SUCC_DELETE');
	        	}
        	}
        }
        $this->setRedirect( $_SERVER['HTTP_REFERER'], $message, 'success');
        return true;
	}

	/**
	* delete projects 
	*
	*/
	function duplicateProject(){
		JSession::checkToken() or jexit(JText::_('GURU_INVALID_TOKEN'));
		$mainframe = JFactory::getApplication();
		$jinput= $mainframe->input;
		$ids = $jinput->post->get('cid');

		require_once(JPATH_BASE.'/components/com_guru/tables/guruproject.php');
        $projectTable =  new TableguruProject();
        $projectTableNew =  new TableguruProject();
        $message = '';
        if(!empty($ids)){
        	foreach ($ids as $id) {
        		$where = array('id'=>$id);
	        	$projectTable->load($where);
	        	
	        	$projectTableNew->title = 'Copy : '.$projectTable->title;
	        	$projectTableNew->course_id = $projectTable->course_id;
		        $projectTableNew->author_id = $projectTable->author_id;
		        $projectTableNew->file = $projectTable->file;
		        $projectTableNew->description = $projectTable->description;
				$projectTableNew->start = $projectTable->start;
		        $projectTableNew->end = $projectTable->end;
	        	if($projectTableNew->store()){
	        		// copy the file
	        		//JFile::copy(JPATH_BASE.'/'.$file);
	        		$message = JText::_('GURU_SUCC_DUPLICATE');
	        	}
	        	
        	}
        }
        $this->setRedirect( $_SERVER['HTTP_REFERER'], $message, 'success');
        return true;
	}

	public function saveProjectResult(){
		JSession::checkToken() or jexit(JText::_('GURU_INVALID_TOKEN'));
		$mainframe = JFactory::getApplication();
		$jinput= $mainframe->input;
		$project_id = $jinput->post->get('id');
		$file = $jinput->post->get('file','','raw');
		$desc = $jinput->post->get('description','','raw');
		$user = JFactory::getUser();

		require_once(JPATH_BASE.'/components/com_guru/tables/guruprojectresults.php');
        $TableguruProjectResult =  new TableguruProjectResult();
        $TableguruProjectResult->load(array('project_id'=>$project_id));
        $TableguruProjectResult->project_id = $project_id;
        $TableguruProjectResult->student_id = $user->id;
        $TableguruProjectResult->desc = $desc;
        $TableguruProjectResult->file = $file;
        $created_date = JFactory::getDate();
        $TableguruProjectResult->created_date = $created_date;
        if($TableguruProjectResult->store()){
        	$this->setRedirect( $_SERVER['HTTP_REFERER'], JText::_('GURU_SUCC_SAVE'), 'success');
        }

        $this->setRedirect( $_SERVER['HTTP_REFERER']);
        return true;
	}


	public function saveScore(){
		JSession::checkToken() or jexit(JText::_('GURU_INVALID_TOKEN'));
		$mainframe = JFactory::getApplication();
		$jinput= $mainframe->input;
		$ids = $jinput->post->get('ids');
		$scores = $jinput->post->get('scores');
		$a = 0 ;

		require_once(JPATH_BASE.'/components/com_guru/tables/guruprojectresults.php');
        $TableguruProjectResult =  new TableguruProjectResult();
        // save the result
		foreach ($ids as $id) {
			$score = $scores[$a];
			$TableguruProjectResult =  new TableguruProjectResult();
        	$TableguruProjectResult->load(array('id'=>$id));
        	$TableguruProjectResult->score = $score;
        	$TableguruProjectResult->store();
			$a++;
		}

		$this->setRedirect( $_SERVER['HTTP_REFERER'], JText::_('GURU_SUCC_SAVE'), 'success');
	}

	function download(){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$cid = JFactory::getApplication()->input->get('cid', "0", "raw");
		$user_id = JFactory::getApplication()->input->get('user_id', "0", "raw");

		if(intval($user_id) == 0){
			$user_id = $user->id;
		}

		$sql = "select count(*) from #__guru_project_results where `student_id`=".intval($user_id)." and `id`=".intval($cid);
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadColumn();
		$count = @$count["0"];

		if(intval($count) == 0){
			return false;
		}
		else{
			$sql = "select * from #__guru_project_results where `student_id`=".intval($user_id)." and `id`=".intval($cid);
			$db->setQuery($sql);
			$db->execute();
			$project_details = $db->loadAssocList();

			if(isset($project_details["0"]) && isset($project_details["0"]["file"])){
				$filename = trim($project_details["0"]["file"]);
				$filepath = JPATH_BASE.DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."projects".DIRECTORY_SEPARATOR.intval($user_id).DIRECTORY_SEPARATOR.$filename;
				$fsize = filesize( $filepath );
				$ftime = date( "D, d M Y H:i:s T", filemtime( $filepath ) );

				/*header( "Content-type: application/pdf" );
				header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
				header( "Last-Modified: $ftime" );
				header( "Content-Length: $fsize" );
				readfile( $filepath );
				exit;*/

				header('Content-Description: File Transfer'); 
			    header('Content-Type: application/octet-stream'); 
			    header('Content-Disposition: attachment; filename="'.$filename.'"'); 
			    header('Content-Transfer-Encoding: binary'); 
			    header('Expires: 0'); 
			    header('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
			    header('Pragma: public'); 
			    header('Content-Length: ' . $fsize); 
			    ob_clean(); 
			    flush(); 
			    readfile($filepath);      
			    exit();
			}
			else{
				return false;
			}
		}
	}
};

?>