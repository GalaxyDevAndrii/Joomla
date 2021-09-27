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

class guruViewguruTasks extends JViewLegacy {

	function display ($tpl =  null ) { 
		$module = $this->get('getTask');
		$this->module = $module;
		parent::display($tpl);
	}
	
	function show ($tpl =  null ) {
		$task = $this->get('Task');
		$this->task = $task;

		if($task!=false){
			$programname = $this->get('programname');
			$this->programname = $programname;
			
			$mediaForTask = $this->get('MediaForTask');
			$this->mediaForTask = $mediaForTask;
	
			$day = $this->get('day');
			$this->day = $day;
		}

		parent::display($tpl);
	}

	function scorm($tpl = null){
		$this->mode = "";
		parent::display($tpl);
	}
	
	function showExercise($tpl = null){
		parent::display($tpl);
	}
	function viewcertificate($tpl = null){
		parent::display($tpl);
	}	
	function getSteps($author_id){
		$model = $this->getModel();
		$result = $model->getSteps($author_id);
	}
	
	function saveLesson($step_id, $pid){
		$db = JFactory::getDbo();
		$sql = "select `lesson_view_confirm` from #__guru_program where `id`=".intval($pid);
		$db->setQuery($sql);
		$lesson_view_confirm = $db->loadColumn();
		$lesson_view_confirm = @$lesson_view_confirm["0"];

		if(intval($lesson_view_confirm) == 1){
			return false;
		}

		$model = $this->getModel();
		$result = $model->saveLessonViewed($step_id,$pid);
	}
	function InsertMyCertificateDetails1($pid){
		$model = $this->getModel();
		$result = $model->InsertMyCertificateDetails($pid);
	}
	function emailCertificate1($pid){
		$model = $this->getModel();
		$result = $model->emailCertificate($pid);
	}
	
	function isLastPassedQuiz($course_id){
		$model = $this->getModel();
		//$result = $model->isLastPassedQuiz($course_id);
		return $result;
	}
	
	function eliminateBlankAnswers($answers){
		$temp_array = array();
		if(isset($answers) && count($answers) > 0){
			foreach($answers as $key=>$value){
				if(trim($value) != ""){
					$temp_array[] = $value;
				}
			}
		}
		return $temp_array;
	}
	function quiz_fe_result_calculation(){
		$task = $this->get('Task');
		$this->task = $task;
		if($task!=false){
			$programname = $this->get('programname');
			$this->programname = $programname;
			
			$mediaForTask = $this->get('MediaForTask');
			$this->mediaForTask = $mediaForTask;
			$day = $this->get('day');
			$this->day = $day;
		}
		parent::display(@$tpl);
	
	}
	function getQuizCalculation($quiz_id, $course_id, $user_id, $nb_of_questions){
		$model = $this->getModel();
		$quiz_content = $model->getResultQuizzes($quiz_id, $course_id, $user_id, $nb_of_questions);
		return $quiz_content;
	}
	function generatePassed_Failed_quizzes($quiz_id, $course_id, $nb_of_questions, $pass){
		$model = $this->getModel();
		$quiz_content = $model->generatePassed_Failed_quizzes($quiz_id, $course_id, $nb_of_questions, $pass);
		return $quiz_content;
	}
	
	function preview($tpl = null){
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$model = $this->getModel();
		$media = $model->parse_media($id, 0);
		$type = $model->getMediaType($id);
		$this->type = $type;
		$this->media = $media;
		parent::display($tpl);
	}
}

?>