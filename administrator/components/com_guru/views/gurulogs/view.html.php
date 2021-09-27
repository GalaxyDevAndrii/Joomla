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

class guruAdminViewguruLogs extends JViewLegacy {

	function emails($tpl = null){
		$emails = $this->get('Items');
		$this->emails = $emails;
		
		$pagination = $this->get('Pagination');
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}
	
	function purchases($tpl = null){
		$purchases = $this->get('Items');
		$this->purchases = $purchases;
		
		$pagination = $this->get('Pagination');
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}
	
	function getEmailName($email){
		$name = "";
		if($email->emailid == 0){
			switch($email->emailname){
				case 'user-registration' : {
					$name = JText::_("GURU_FOR_TEACHER_APPROVED");
					break;
				}
				case 'email-certificate' : {
					$name = JText::_("GURU_MY_CERTIFICATE");
					break;
				}
				case 'teacher-registration' : {
					$name = JText::_("GURU_FOR_TEACHER_REGISTERED");
					break;
				}
				case 'student-registration' : {
					$name = JText::_("GURU_FOR_STUDENT_REGISTERED");
					break;
				}
				case 'email-to-ask-approved' : {
					$name = JText::_("GURU_FOR_TEACHER_APPROVED");
					break;
				}
				case 'buy-offline' : {
					$name = JText::_("GURU_OFFLINE_ORDER");
					break;
				}
				case 'my-quiz-marcked' : {
					$name = JText::_("GURU_RADED_AND_CHECKED_RESULT");
					break;
				}
				case 'get-certificate' : {
					$name = JText::_("GURU_GET_CERTIFICATE");
					break;
				}
				case 'teacher-mark-essay' : {
					$name = JText::_("GURU_REVIEW_QUIZ");
					break;
				}
				case 'new-lesson' : {
					$name = JText::_("GURU_NEW_LESSON");
					break;
				}
				case 'to-author-student-enrolled' : {
					$name = JText::_("GURU_TO_AUTHOR_STUDENT_ENROLLED");
					break;
				}
				case 'to-admin-student-enrolled' : {
					$name = JText::_("GURU_TO_ADMIN_STUDENT_ENROLLED");
					break;
				}
				case 'email-essay-graded' : {
					$name = JText::_("GURU_TO_STUDENT_ESSAY_GRADED");
					break;
				}
				case 'to-admin-student-completed_course' : {
					$name = JText::_("GURU_TO_ADMIN_COMPLETED_COURSE");
					break;
				}
				case 'to-author-student-completed_course' : {
					$name = JText::_("GURU_TO_AUTHOR_COMPLETED_COURSE");
					break;
				}
			}
		}
		else{
			$db = JFactory::getDbo();
			$sql = "select name from #__guru_subremind where id=".intval($email->emailid);
			$db->setQuery($sql);
			$db->execute();
			$name = $db->loadColumn();
			$name = @$name["0"];
		}
		return $name;
	}
	
	function edit($tpl = null){
		$email = $this->get("Email");
		$this->email = $email;
		parent::display($tpl);
	}
};

?>