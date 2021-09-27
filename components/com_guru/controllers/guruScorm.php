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


class guruControllerguruScorm extends guruController {

	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->_model = $this->getModel("guruScorm");
	}

	function saveScorm () {
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

        require_once(JPATH_BASE.'/components/com_guru/tables/guruscorm.php');
        $scormTable =  new TableguruScorm();//JTable::getInstance('TableguruScorm');
        $scormTable->id = $id;
        $scormTable->course_id = $jinput->post->get('course_id');
        $scormTable->title = $jinput->post->get('title','','raw');
        $scormTable->author_id = $user->id;
        $scormTable->file = $jinput->post->get('file','','raw');
        $scormTable->description = $jinput->post->get('description','','raw');
		$scormTable->start = $jinput->post->get('start','','raw');
        $scormTable->end = $jinput->post->get('end','','raw');
        $scormTable->published = $jinput->post->get('published','0','raw');
        
        if($scormTable->end == ""){
			$scormTable->end = '0000-00-00 00:00:00';
		}

		if($scormTable->updated == ""){
			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
			$jnow = new JDate('now');
			$jnow->setTimezone($timezone);
			$scormTable->updated = $jnow->toSQL(true);
		}

		if($scormTable->created == ""){
			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
			$jnow = new JDate('now');
			$jnow->setTimezone($timezone);
			$scormTable->created = $jnow->toSQL(true);
		}

		if($scormTable->layout == ""){
			$scormTable->layout = " ";
		}

        if($scormTable->store()){
        	$idParam = !empty($scormTable->id) ? '&id='.$scormTable->id : '';

        	//start - is scorm is added to lessons and teacher change the scorm zip file, that zip need to be changed to lessons to
        	$db = JFactory::getDbo();

        	$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_days d where mr.type='dtask' and mr.type_id=d.id and d.pid=".intval($scormTable->course_id);
			$db->setQuery($sql);
			$db->execute();
			$lessons = $db->loadColumn();
			
			if(isset($lessons) && count($lessons) > 0){
				$sql = "select mr.type_id from #__guru_mediarel mr where mr.layout='17' and mr.type='scr_m' and mr.type_id in (".implode(", ", $lessons).")";
				$db->setQuery($sql);
				$db->execute();
				$lessons_scorm = $db->loadColumn();

				if(isset($lessons_scorm) && count($lessons_scorm) > 0){
					include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."scorm.php");
					$scorm_guru = new scormGuruImport();

					foreach($lessons_scorm as $key=>$lesson_id){
						$scorm_added = $scorm_guru->uploadScormLesson($lesson_id, (array)$scormTable);
					}
				}
			}
        	//stop - is scorm is added to lessons and teacher change the scorm zip file, that zip need to be changed to lessons to

        	if($action == 'apply'){
        		$this->setRedirect( JRoute::_('index.php?option=com_guru&view=guruauthor&task=scormForm&layout=scormForm&'.$idParam, false), JText::_('GURU_SUCC_SAVE'), 'success');
        	}
        	else{
        		$this->setRedirect( JRoute::_('index.php?option=com_guru&view=guruauthor&task=scorm&layout=scorm', false), JText::_('GURU_SUCC_SAVE'), 'success');
        	}
        }
        else{
        	$idParam = !empty($scormTable->id) ? '&id='.$scormTable->id : '';
        	$this->setRedirect( JRoute::_('index.php?option=com_guru&view=guruauthor&task=scormForm&layout=scormForm&'.$idParam, false), JText::_('GURU_ERR_SAVE'), 'error');
        }

        return false;
	}

	/**
	* delete scorm 
	*
	*/
	function deleteScorm(){
		JSession::checkToken() or jexit(JText::_('GURU_INVALID_TOKEN'));
		$mainframe = JFactory::getApplication();
		$jinput= $mainframe->input;
		$ids = $jinput->post->get('cid');

		require_once(JPATH_BASE.'/components/com_guru/tables/guruscorm.php');
        $scormTable =  new TableguruScorm();
        $message = '';
        if(!empty($ids)){
        	foreach ($ids as $id) {
        		$where = array('id'=>$id);
	        	$scormTable->load($where);
	        	// file need to be deleted
	        	$file = $scormTable->file;
	        	
	        	if($scormTable->delete($where)){
	        		JFile::delete(JPATH_BASE.'/'.$file);
	        		$message = JText::_('GURU_SUCC_DELETE');
	        	}
        	}
        }
        $this->setRedirect( $_SERVER['HTTP_REFERER'], $message, 'success');
        return true;
	}

};

?>