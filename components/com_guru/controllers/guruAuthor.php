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

require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

jimport ('joomla.application.component.controller');

class guruControllerguruAuthor extends guruController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("", "getAuthorList");
		$this->registerTask ("author", "getAuthor");
		$this->registerTask("authorprofile", "authorProfile");
		$this->registerTask("authormycourses", "authorMycourses");
		$this->registerTask("authormymedia", "authorMymedia");
		$this->registerTask("authorcommissions", "authorCommissions");
		$this->registerTask("treeCourse", "authorTreeCourses");
		$this->registerTask("mystudents", "myStudents");
		$this->registerTask("authorregister", "authorRegister");
		$this->registerTask("authorquizzes", "authorquizzes");
		$this->registerTask("removeCourse", "removeCourses");
		$this->registerTask("removeMedia", "removeMedia");
		$this->registerTask("addCourse", "addCourse");
		$this->registerTask("newStudent", "newStudent");
		$this->registerTask("duplicateCourse", "duplicateCourse");
		$this->registerTask("unpublishCourse", "unpublishCourse");
		$this->registerTask("publishCourse", "publishCourse");
		$this->registerTask("unpublishMedia", "unpublishMedia");
		$this->registerTask("publishMedia", "publishMedia");
		$this->registerTask("newmodule", "newModule");
		$this->registerTask("save_new_module", "saveNewModule");
		$this->registerTask("save_module", "saveModule");
		$this->registerTask("edit", "edit");
		$this->registerTask("editsbox", "editsbox");
		$this->registerTask("preview", "preview");
		$this->registerTask("duplicateMedia", "duplicateMedia");
		$this->registerTask("editMedia", "editMedia");
		$this->registerTask("editProject", "editProject");
		$this->registerTask("editScorm", "editScorm");
		$this->registerTask("authormymediacategories", "authormymediacategories");
		$this->registerTask("authoraddeditmediacat", "authoraddeditmediacat");
		$this->registerTask("unpublishMediaCat", "unpublishMediaCat");
		$this->registerTask("publishMediaCat", "publishMediaCat");
		$this->registerTask("removeMediaCat", "removeMediaCat");
		$this->registerTask("apply_media", "applyMedia");
		$this->registerTask("save_media", "saveMedia");
		$this->registerTask("savesbox", "savesbox");
		$this->registerTask("saveModalProject", "saveModalProject");
		$this->registerTask("saveModalScorm", "saveModalScorm");
		$this->registerTask("applymediacat", "applyMediaCat");
		$this->registerTask("savemediacat", "saveMediaCat");
		$this->registerTask("duplicateMediaCat", "duplicateMediaCat");
		$this->registerTask("saveLesson", "saveLesson");
		$this->registerTask("applyLesson", "applyLesson");
		$this->registerTask("saveLessonNew", "saveLessonNew");
		$this->registerTask("editQuiz", "editQuiz");
		$this->registerTask("save_quiz", "saveQuiz");
		$this->registerTask("addquestion", "addQuestion");
		$this->registerTask("savequestion", "saveQuestion");
		$this->registerTask("editquestion", "editQuestion");
		$this->registerTask("jumpbts_save", "saveJump");
		$this->registerTask("publish_quiz", "publishQuiz");
		$this->registerTask("unpublish_quiz", "unpublishQuiz");
		$this->registerTask("editsboxx", "editsboxx");
		$this->registerTask("removeQuiz", "removeQuiz");
		$this->registerTask("course_stats", "courseStats");
		$this->registerTask("quizz_stats", "quizzStats");
		$this->registerTask("duplicateQuiz", "duplicateQuiz");
		$this->registerTask("editQuizFE", "editQuizFE");
		$this->registerTask("apply_quizFE", "applyQuizFE");
		$this->registerTask("save_quizFE", "saveQuizFE");
		$this->registerTask("addquizzes", "addQuizzes");
		$this->registerTask("save", "save");
		$this->registerTask("addexercise", "addexercise");
		$this->registerTask ("saveOrderQuestions", "saveOrderQuestions");
		$this->registerTask ("saveOrderExercices", "saveOrderExercices");
		$this->registerTask ("studentdetails", "studentdetails");
		$this->registerTask ("studentdetailslesson", "studentdetailslesson");
		$this->registerTask ("studentdetailscert", "studentdetailscert");
		$this->registerTask ("studentquizes", "studentquizes");
		$this->registerTask ("quizdetails", "quizdetails");
		$this->registerTask ("student_quizdetails", "student_quizdetails");
		$this->registerTask("terms", "terms");
		$this->registerTask("action", "action");
		$this->registerTask("apply_commissions", "applyCommissions");
		$this->registerTask("paid_commission", "paidCommission");
		$this->registerTask("pending_commission", "pendingCommission");
		$this->registerTask("details_paid", "detailsPaid");
		$this->registerTask("upload_ajax_image", "uploadAjaxImage");
		$this->registerTask("upload_ajax_scorm", "uploadAjaxScorm");
		$this->registerTask("upload_project_file", "uploadAjaxProject");
		$this->registerTask("upload_scorm_file", "uploadAjaxScorm");
		$this->registerTask("pub_unpub_ajax", "pubUnpubAjax");
		$this->registerTask("publish_quiz_ajax", "publishQuizAjax");
		$this->registerTask("unpublish_quiz_ajax", "unpublishQuizAjax");
		$this->registerTask("delete_quiz_ajax", "deleteQuizAjax");
		$this->registerTask("add_quizz_ajax", "addQuizzAjax");
		$this->registerTask("add_text_ajax", "addTextAjax");
		$this->registerTask("delete_final_quizz_ajax", "deleteFinalQuizzAjax");
		$this->registerTask("delete_group_ajax", "deleteGroupAjax");
		$this->registerTask("delete_screen_ajax", "deleteScreenAjax");
		$this->registerTask("saveOrderG", "saveOrderG");
		$this->registerTask("saveOrderS", "saveOrderS");
		$this->registerTask("add_media_ajax", "addMediaAjax");
		$this->registerTask("applyquizdetails", "applyquizdetails");
		$this->registerTask("savequizdetails", "savequizdetails");
		$this->registerTask("authorregistration", "authorRegistration");
		$this->registerTask("studentregistration", "stundetRegistration");
		$this->registerTask("mark", "mark");
		$this->registerTask("export_csv", "exportCsv");
		$this->registerTask("export_pdf", "exportPdf");
		$this->registerTask("authoressays", "authoressays");
		$this->registerTask("saveMark", "saveMark");
		$this->registerTask("ajaxSearchMedia", "ajaxSearchMedia");
		$this->registerTask("student_progress", "studentProgress");
		$this->registerTask("loading_student_certificates", "loadingStudentCertificates");

		$option = JFactory::getApplication()->input->get("option", "", "raw");
    	$view = JFactory::getApplication()->input->get("view", "", "raw");
    	$task = JFactory::getApplication()->input->get("task", "", "raw");
    	$layout = JFactory::getApplication()->input->get("layout", "", "raw");

    	$db = JFactory::getDbo();
    	$sql = "select `st_authorpage` from #__guru_config limit 0, 1";
    	$db->setQuery($sql);
    	$st_authorpage = $db->loadColumn();
    	$st_authorpage = @$st_authorpage["0"];
    	$st_authorpage = json_decode($st_authorpage);

    	if(!isset($st_authorpage->teacher_menu_courses)){
    		$st_authorpage->teacher_menu_courses = 0;
    	}

    	if(!isset($st_authorpage->teacher_menu_students)){
    		$st_authorpage->teacher_menu_students = 0;
    	}

    	if(!isset($st_authorpage->teacher_menu_projects)){
    		$st_authorpage->teacher_menu_projects = 0;
    	}

    	if(!isset($st_authorpage->teacher_menu_scorm)){
    		$st_authorpage->teacher_menu_scorm = 0;
    	}

    	if(!isset($st_authorpage->teacher_menu_quizzes)){
    		$st_authorpage->teacher_menu_quizzes = 0;
    	}

    	if(!isset($st_authorpage->teacher_menu_media)){
    		$st_authorpage->teacher_menu_media = 0;
    	}

    	if(!isset($st_authorpage->teacher_menu_commissions)){
    		$st_authorpage->teacher_menu_commissions = 0;
    	}

    	if(!isset($st_authorpage->teacher_menu_grade)){
    		$st_authorpage->teacher_menu_grade = 0;
    	}

    	if(strtolower($view) == "guruauthor" && $task == "authormycourses" && $layout == "authormycourses" && $st_authorpage->teacher_menu_courses == 1){
    		JFactory::getApplication()->redirect(JURI::root(), JText::_("GURU_REGISTRTION_REQUIRE"));
    	}

    	if(strtolower($view) == "guruauthor" && $task == "mystudents" && $layout == "mystudents" && $st_authorpage->teacher_menu_students == 1){
    		JFactory::getApplication()->redirect(JURI::root(), JText::_("GURU_REGISTRTION_REQUIRE"));
    	}

    	if(strtolower($view) == "guruauthor" && $task == "projects" && $layout == "projects" && $st_authorpage->teacher_menu_projects == 1){
    		JFactory::getApplication()->redirect(JURI::root(), JText::_("GURU_REGISTRTION_REQUIRE"));
    	}

    	if(strtolower($view) == "guruauthor" && $task == "scorm" && $layout == "scorm" && $st_authorpage->teacher_menu_scorm == 1){
    		JFactory::getApplication()->redirect(JURI::root(), JText::_("GURU_REGISTRTION_REQUIRE"));
    	}

    	if(strtolower($view) == "guruauthor" && $task == "authorquizzes" && $layout == "authorquizzes" && $st_authorpage->teacher_menu_quizzes == 1){
    		JFactory::getApplication()->redirect(JURI::root(), JText::_("GURU_REGISTRTION_REQUIRE"));
    	}

    	if(strtolower($view) == "guruauthor" && $task == "authormymedia" && $layout == "authormymedia" && $st_authorpage->teacher_menu_media == 1){
    		JFactory::getApplication()->redirect(JURI::root(), JText::_("GURU_REGISTRTION_REQUIRE"));
    	}
		
    	if(strtolower($view) == "guruauthor" && $task == "authorcommissions" && $layout == "authorcommissions" && $st_authorpage->teacher_menu_commissions == 1){
    		JFactory::getApplication()->redirect(JURI::root(), JText::_("GURU_REGISTRTION_REQUIRE"));
    	}

    	if(strtolower($view) == "guruauthor" && $task == "authoressays" && $layout == "authoressays" && $st_authorpage->teacher_menu_grade == 1){
    		JFactory::getApplication()->redirect(JURI::root(), JText::_("GURU_REGISTRTION_REQUIRE"));
    	}

		$this->_model = $this->getModel("guruauthor");
	}
	
	function authoressays(){
		JFactory::getApplication()->input->set ("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("authoressays");
		$view->setModel($this->_model, true);
		$view->authoressays();
	}
	
	function getAuthorList(){
		JFactory::getApplication()->input->set ("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setModel($this->_model, true);
		parent::display();
	}
	
	function action(){
		$return = $this->_model->action();
		JFactory::getApplication()->input->set("action", "0");
		$pid = JFactory::getApplication()->input->get("pid", "0");
		$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruauthor&task=treeCourse&pid=".intval($pid)));
	}
	
	function removeCourses(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		
		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruauthor"];
		$type = "";
		
		if(intval($itemid_seo) > 0){
			$Itemid = intval($itemid_seo);
		}
		
		if(!$this->_model->delete()){
			$msg = JText::_('GURU_COURSE_NOT_REMOVED');
			$type = "error";
		}
		else{
		 	$msg = JText::_('GURU_COURSE_REMOVED');
		}

		$link = "index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses&Itemid=".$Itemid;
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}

	function removeMedia(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		
		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruauthor"];
		
		if(intval($itemid_seo) > 0){
			$Itemid = intval($itemid_seo);
		}
		
		if(!$this->_model->deleteMedia()){
			$msg = JText::_('GURU_MEDIA_NOT_REMOVED');
		}
		else{
		 	$msg = JText::_('GURU_MEDIA_REMOVED');
		}

		$link = "index.php?option=com_guru&view=guruauthor&task=authormymedia&layout=authormymedia&Itemid=".$Itemid;
		$this->setRedirect(JRoute::_($link, false), $msg);
	}
	function removeMediaCat(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		
		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruauthor"];
		
		if(intval($itemid_seo) > 0){
			$Itemid = intval($itemid_seo);
		}
		
		if(!$this->_model->removeMediaCat()){
			$msg = JText::_('GURU_MEDIACAT_NOT_REMOVED');
		}
		else{
		 	$msg = JText::_('GURU_MEDIACAT_REMOVED');
		}

		$link = "index.php?option=com_guru&view=guruauthor&task=authormymediacategories&layout=authormymediacategories&Itemid=".$Itemid;
		$this->setRedirect(JRoute::_($link, false), $msg);
	}
	function removeQuiz(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		
		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruauthor"];
		
		if(intval($itemid_seo) > 0){
			$Itemid = intval($itemid_seo);
		}
		
		if(!$this->_model->removeQuiz()){
			$msg = JText::_('GURU_QUIZ_NOT_REMOVED');
		}
		else{
		 	$msg = JText::_('GURU_QUIZ_REMOVED');
		}
		$link = "index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes&Itemid=".$Itemid;
		$this->setRedirect(JRoute::_($link, false), $msg);
	}

	function getAuthor(){
		JFactory::getApplication()->input->set ("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("view");
		$view->setModel($this->_model, true);
		$view->view();
	}

	function authorProfile(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user_id);
		$res_enabled = $model->checkAuthorProfileEnabled($user_id);
		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=authorprofile"."&Itemid=".$Itemid, false));
			return true;
		}

		if($res === FALSE){
			/*echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS").'</p>
					  </div>';
			return true;*/

			$view = $this->getView("guruauthor", "html");
			$view->setLayout("authorprofile");
			$view->setModel($this->_model, true);
			$view->authorprofile();
        }
		elseif($res_enabled === FALSE){
			echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS_ENABLED").'</p>
					  </div>';
			return true;
		}
		else{
			$view = $this->getView("guruauthor", "html");
			$view->setLayout("authorprofile");
			$view->setModel($this->_model, true);
			$view->authorprofile();
		}
	}

	function myStudents(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user_id);
		$res_enabled = $model->checkAuthorProfileEnabled($user_id);
		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=mystudents"."&Itemid=".$Itemid, false));
			return true;
		}

		if($res === FALSE){
			/*echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS").'</p>
					  </div>';
			return true;*/

			$teacher_redirect = $this->getTeacherRedirectUrl();

			if(trim($teacher_redirect) != ""){
				$this->setRedirect(trim($teacher_redirect));
				return true;				
			}
			else{
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruauthor&layout=authorprofile"."&Itemid=".$Itemid, false));
				return true;
			}
        }
		elseif($res_enabled === FALSE){
			echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS_ENABLED").'</p>
					  </div>';
			return true;
		}
		else{
			$view = $this->getView("guruauthor", "html");
			$view->setLayout("mystudents");
			$view->setModel($this->_model, true);
			$view->mystudents();
		}
	}
	
	function authorMymedia(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user_id);
		$res_enabled = $model->checkAuthorProfileEnabled($user_id);
		        
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=authormymedia"."&Itemid=".$Itemid, false));
			return true;
		}
		
		if($res === FALSE){
			/*echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS").'</p>
					  </div>';
			return true;*/

			$teacher_redirect = $this->getTeacherRedirectUrl();

			if(trim($teacher_redirect) != ""){
				$this->setRedirect(trim($teacher_redirect));
				return true;				
			}
			else{
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruauthor&layout=authorprofile"."&Itemid=".$Itemid, false));
				return true;
			}
        }
		elseif($res_enabled === FALSE){
			echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS_ENABLED").'</p>
					  </div>';
			return true;
		}
		else{
			$view = $this->getView("guruAuthor", "html");
			$view->setLayout("authormymedia");
			$view->setModel($this->_model, true);
			$view->authormymedia();
		}
	}
	
	function authorCommissions(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user_id);
		$res_enabled = $model->checkAuthorProfileEnabled($user_id);
		        
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=authorcommissions"."&Itemid=".$Itemid, false));
			return true;
		}
		
		if($res === FALSE){
			/*echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS").'</p>
					  </div>';
			return true;*/

			$teacher_redirect = $this->getTeacherRedirectUrl();

			if(trim($teacher_redirect) != ""){
				$this->setRedirect(trim($teacher_redirect));
				return true;				
			}
			else{
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruauthor&layout=authorprofile"."&Itemid=".$Itemid, false));
				return true;
			}
        }
		elseif($res_enabled === FALSE){
			echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS_ENABLED").'</p>
					  </div>';
			return true;
		}
		else{
			$view = $this->getView("guruAuthor", "html");
			$view->setLayout("authorcommissions");
			$view->setModel($this->_model, true);
			$view->authorcommissions();
		}
	}
	function paidCommission(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("authorcommissions_paid");
		$view->setModel($this->_model, true);
		$view->authorcommissions_paid();
	}
	function pendingCommission(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("authorcommissions_pending");
		$view->setModel($this->_model, true);
		$view->authorcommissions_pending();
	}	
	
	function detailsPaid(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("authorcommissions_details_paid");
		$view->setModel($this->_model, true);
		$view->authorcommissions_details_paid();
	}
	
	function authormymediacategories(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user_id);
		$res_enabled = $model->checkAuthorProfileEnabled($user_id);
		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=authormymediacategories"."&Itemid=".$Itemid, false));
			return true;
		}
		if($res === FALSE){
			/*echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS").'</p>
					  </div>';
			return true;*/

			$teacher_redirect = $this->getTeacherRedirectUrl();

			if(trim($teacher_redirect) != ""){
				$this->setRedirect(trim($teacher_redirect));
				return true;				
			}
			else{
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruauthor&layout=authorprofile"."&Itemid=".$Itemid, false));
				return true;
			}
        }
		elseif($res_enabled === FALSE){
			echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS_ENABLED").'</p>
					  </div>';
			return true;
		}
		else{
			$view = $this->getView("guruAuthor", "html");
			$view->setLayout("authormymediacategories");
			$view->setModel($this->_model, true);
			$view->authormymediacategories();
		}
	}
	
	function authorTreeCourses(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user_id);
		$res_enabled = $model->checkAuthorProfileEnabled($user_id);

		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
		
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=mystudents"."&Itemid=".$Itemid, false));
			return true;
		}
		else{
			if($res === FALSE){
				$teacher_redirect = $this->getTeacherRedirectUrl();

				if(trim($teacher_redirect) != ""){
					$this->setRedirect(trim($teacher_redirect));
					return true;				
				}
				else{
					$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruauthor&layout=authorprofile"."&Itemid=".$Itemid, false));
					return true;
				}
	        }
			elseif($res_enabled === FALSE){
				echo '<div class="alert alert-error">
							<h4 class="alert-heading">Message</h4>
							<p>'.JText::_("GURU_ONLY_AUTHORS_ENABLED").'</p>
						  </div>';
				return true;
			}
			else{
				$view = $this->getView("guruauthor", "html");
				$view->setLayout("authortreecourse");
				$view->setModel($this->_model, true);
				$view->authortreecourse();
			}
		}
	}
	
	function newStudent(){
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("newstudent");
		$view->setModel($this->_model, true);
		$view->newstudent();
	}
	
	function authorQuizzes(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user_id);
		$res_enabled = $model->checkAuthorProfileEnabled($user_id);
		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
		
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=authorquizzes"."&Itemid=".$Itemid, false));
			return true;
		}
		if($res === FALSE){
			/*echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS").'</p>
					  </div>';
			return true;*/

			$teacher_redirect = $this->getTeacherRedirectUrl();

			if(trim($teacher_redirect) != ""){
				$this->setRedirect(trim($teacher_redirect));
				return true;				
			}
			else{
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruauthor&layout=authorprofile"."&Itemid=".$Itemid, false));
				return true;
			}
        }
		elseif($res_enabled === FALSE){
			echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS_ENABLED").'</p>
					  </div>';
			return true;
		}
		else{
			$view = $this->getView("guruauthor", "html");
			$view->setLayout("authorquizzes");
			$view->setModel($this->_model, true);
			$view->authorquizzes();
		}
	}
	function authorMycourses(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user_id);
		$res_enabled = $model->checkAuthorProfileEnabled($user_id);
		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=authormycourses"."&Itemid=".$Itemid, false));
			return true;
		}

		if($res === FALSE){
			/*echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS").'</p>
					  </div>';
			return true;*/

			$teacher_redirect = $this->getTeacherRedirectUrl();

			if(trim($teacher_redirect) != ""){
				$this->setRedirect(trim($teacher_redirect));
				return true;				
			}
			else{
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruauthor&layout=authorprofile"."&Itemid=".$Itemid, false));
				return true;
			}
        }
		elseif($res_enabled === FALSE){
			echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS_ENABLED").'</p>
					  </div>';
			return true;
		}
		else{
			$view = $this->getView("guruauthor", "html");
			$view->setLayout("authormycourses");
			$view->setModel($this->_model, true);
			$view->authormycourses();
		}
	}

	function authorRegister(){
		$view = $this->getView("guruauthor", "html");
        $view->setLayout("authorprofile");
		$view->setModel($this->_model, true);
        $view->authorprofile();
	}

	function addCourse(){
		$model = $this->getModel("guruAuthor");
		$user = JFactory::getUser();
		$user_id = $user->id;
		$res = $model->checkAuthorProfile($user_id); /* check if user is teacher */

		if($res === FALSE){
			/*echo '<div class="alert alert-error">
						<h4 class="alert-heading">Message</h4>
						<p>'.JText::_("GURU_ONLY_AUTHORS").'</p>
					  </div>';
			return true;*/

			$teacher_redirect = $this->getTeacherRedirectUrl();

			if(trim($teacher_redirect) != ""){
				$this->setRedirect(trim($teacher_redirect));
				return true;				
			}
			else{
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruauthor&layout=authorprofile"."&Itemid=".$Itemid, false));
				return true;
			}
        }
        else{
        	$view = $this->getView("guruauthor", "html");
	        $view->setLayout("authoraddcourse");
			$view->setModel($this->_model, true);
	        $view->authoraddcourse();
        }
		
	}
	
	function authoraddeditmediacat(){
		$view = $this->getView("guruauthor", "html");
        $view->setLayout("authoraddeditmediacat");
		$view->setModel($this->_model, true);
        $view->authoraddeditmediacat();
	
	}	
	
	function save(){
		$come_from =  JFactory::getApplication()->input->get("g_page", "");
		$msg = "";
		$link = "";
		
		if($come_from == "courseadd"){
			$link = "index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses";
			if($this->_model->store()){
				$msg = JText::_('GURU_COURSE_SAVED_SUCCESSFULLY');
			} 
			else{
				$msg = JText::_('GURU_COURSE_SAVED_UNSUCCESSFULLY');
			}
		}
		elseif($come_from == "courseedit"){
			$link = "index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses";
			if($this->_model->store()){
				$msg = JText::_('GURU_COURSE_SAVED_SUCCESSFULLY');
			} 
			else{
				$msg = JText::_('GURU_COURSE_SAVED_UNSUCCESSFULLY');
			}
		}
		else{
			if($this->_model->store()){
				$msg = JText::_('GURU_CUST_SAVED');
			} 
			else{
				$msg = JText::_('GURU_CUST_SAVEFAIL');
			}
			$link = "index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents";
		}
		
		$this->setRedirect(JRoute::_($link, false), $msg);
	}
	
	function apply(){
		$come_from =  JFactory::getApplication()->input->get("g_page", "");
		$result = $this->_model->store();
		$userId = $result["id"];
		$msg = "";
		if($come_from == "courseadd"){
			$link = "index.php?option=com_guru&view=guruauthor&task=addCourse&id=".intval($result["id"]);
			if(intval($result["id"]) !=0){
				$msg = JText::_('GURU_COURSE_SAVED_SUCCESSFULLY');
			} 
			else{
				$msg = JText::_('GURU_COURSE_SAVED_UNSUCCESSFULLY');
			}
		}
		elseif($come_from == "courseedit" ){
			$link = "index.php?option=com_guru&view=guruauthor&task=addCourse&id=".intval($result["id"]);
			if(intval($result["id"]) !=0){
				$msg = JText::_('GURU_COURSE_SAVED_SUCCESSFULLY');
			} 
			else{
				$msg = JText::_('GURU_COURSE_SAVED_UNSUCCESSFULLY');
			}
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=newStudent&layout=newStudent&id=".intval($result["id"]);
			if(isset($result["error"]) && $result["error"] === TRUE){
				$msg = JText::_('GURU_CUST_APPLY');
			} 
			elseif(isset($result["error"]) && $result["error"] === FALSE){
				$msg = JText::_('GURU_CUST_APPLYFAIL');
			}
		}
		
		$this->setRedirect(JRoute::_($link, false), $msg);
	}
	
	function duplicateCourse(){
		$result = $this->_model->duplicateCourse();
		$type = "message";
		if($result === TRUE){
			$link = "index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses";
			$msg = JText::_("GURU_DUPLICATE_COURSE_SUCCESSFULLY");
			$type = "message";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses";
			$msg = JText::_("GURU_DUPLICATE_COURSE_UNSUCCESSFULLY");
			$type = "error";
		}
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}
	
	function unpublishCourse(){
		$result = $this->_model->unpublishCourse();
		$type = "message";
		if($result === TRUE){
			$link = "index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses";
			$msg = JText::_("GURU_UNPUBLISH_COURSE_SUCCESSFULLY");
			$type = "message";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses";
			$msg = JText::_("GURU_UNPUBLISH_COURSE_UNSUCCESSFULLY");
			$type = "error";
		}
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}
	
	function publishCourse(){
		$result = $this->_model->publishCourse();
		$type = "message";
		if($result === TRUE){
			$link = "index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses";
			$msg = JText::_("GURU_PUBLISH_COURSE_SUCCESSFULLY");
			$type = "message";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses";
			$msg = JText::_("GURU_PUBLISH_COURSE_UNSUCCESSFULLY");
			$type = "error";
		}
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}
	function unpublishMedia(){
		$result = $this->_model->unpublishMedia();
		$type = "message";
		if($result === TRUE){
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymedia&layout=authormymedia";
			$msg = JText::_("GURU_MEDIAUNPUB");
			$type = "message";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymedia&layout=authormymedia";
			$msg = JText::_("GURU_MEDIAPUB");
			$type = "error";
		}
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}
	
	function publishMedia(){
		$result = $this->_model->publishMedia();
		$type = "message";
		if($result === TRUE){
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymedia&layout=authormymedia";
			$msg = JText::_("GURU_MEDIAPUB");
			$type = "message";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymedia&layout=authormymedia";
			$msg = JText::_("GURU_MEDIAUNPUB");
			$type = "error";
		}
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}
	
	function unpublishMediaCat(){
		$result = $this->_model->unpublishMediaCat();
		$type = "message";
		if($result === TRUE){
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymediacategories&layout=authormymediacategories";
			$msg = JText::_("GURU_MEDIAUNPUB");
			$type = "message";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymediacategories&layout=authormymediacategories";
			$msg = JText::_("GURU_MEDIAPUB");
			$type = "error";
		}
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}
	
	function publishMediaCat(){
		$result = $this->_model->publishMediaCat();
		$type = "message";
		if($result === TRUE){
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymediacategories&layout=authormymediacategories";
			$msg = JText::_("GURU_MEDIAPUB");
			$type = "message";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymediacategories&layout=authormymediacategories";
			$msg = JText::_("GURU_MEDIAUNPUB");
			$type = "error";
		}
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}
	function unpublishQuiz(){
		$result = $this->_model->unpublishQuiz();
		$type = "message";
		if($result === TRUE){
			$link = "index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes";
			$msg = JText::_("GURU_QUIZUNPUB");
			$type = "message";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes";
			$msg = JText::_("GURU_QUIZPUB");
			$type = "error";
		}
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}
	
	function publishQuiz(){
		$result = $this->_model->publishQuiz();
		$type = "message";
		if($result === TRUE){
			$link = "index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes";
			$msg = JText::_("GURU_QUIZPUB");
			$type = "message";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes";
			$msg = JText::_("GURU_QUIZUNPUB");
			$type = "error";
		}
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}
	function newModule(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("newModule");
		$view->newModule();
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
					window.parent.location.href=\"".JURI::base()."index.php?option=com_guru&view=guruauthor&task=treeCourse&pid=".$pid."\";
					window.parent.document.getElementById('close').click();
				</script>";
	}
	
	function saveModule(){
		$pid = JFactory::getApplication()->input->get("pid", "0");
		if($this->_model->store_module()){
			$msg = JText::_('GURU_DAY_SAVE');
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('saved_new', "1");
		}
		else{
			$msg = JText::_('GURU_DAY_NOTSAVE');
		}
		echo "	<script> 
					window.parent.location.href=\"".JURI::base()."index.php?option=com_guru&view=guruauthor&task=treeCourse&pid=".$pid."\";
					window.parent.document.getElementById('close').click();
				</script>";
	}
	
	function edit(){
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
	
		$model = $this->getModel("guruAuthor");
		$view->setModel($model);
		$view->editForm();
	}
	
	function vimeo() {
   		JFactory::getApplication()->input->set('view', 'guruAuthor');
		JFactory::getApplication()->input->set('layout', 'vimeo');
        $view = $this->getView("guruAuthor", "html");
		$view->setLayout("vimeo");
        $view->vimeo();
        die();
    }
	function preview(){
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("preview");
		$view->setModel($this->_model, true);
		$view->preview();
	}
	
	function editsbox(){
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("editformsbox");
		$view->setModel($this->_model, true);
		$view->editLessonForm();
	}
	
	function addmedia(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1); 
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("addmedia");
		$view->setModel($this->_model, true);
		$view->addmedia();
	}

	function addproject () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1); 
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("addproject");
		$view->setModel($this->_model, true);
		$view->addproject();
	}

	function addscorm () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1); 
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("addscorm");
		$view->setModel($this->_model, true);
		$view->addscorm();
	}
	
	function addexercise(){
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("addexercise");
		$view->setModel($this->_model, true);
		$view->addexercise();
	}
	
	function addQuiz(){
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("addquiz");
		$view->setModel($this->_model, true);
		$view->addQuiz();
	}
	
	function addtext(){
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("addtext");
		$view->setModel($this->_model, true);
		$view->addmedia();
	}
	
	function duplicateMedia(){ 
		$res = $this->_model->duplicateMedia();
		if($res == 1){
			$msg = JText::_('GURU_MEDIA_DUPLICATE_SUCC');
		}
		else{
			$msg = JText::_('GURU_MEDIA_DUPLICATE_ERR');
		}
		
		$link = "index.php?option=com_guru&view=guruauthor&task=authormymedia&layout=authormymedia";
		$this->setRedirect(JRoute::_($link, false), $msg);
	}
	
	function editMedia(){
		$redirect_to= JFactory::getApplication()->input->get('redirect_to', NULL);
		$type		= JFactory::getApplication()->input->get('type',"");
		
		if(isset($redirect_to)) {
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('temp_type', $type);
			
			$msg=NULL;
			$this->setRedirect(JRoute::_($redirect_to, false), $msg);			
		}	
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("editmedia");
		$view->setModel($this->_model, true);
		$view->editMediaForm();
	}

	function editProject(){
		$redirect_to= JFactory::getApplication()->input->get('redirect_to', NULL);
		$type		= JFactory::getApplication()->input->get('type',"");
		
		if(isset($redirect_to)) {
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('temp_type', $type);
			
			$msg=NULL;
			$this->setRedirect(JRoute::_($redirect_to, false), $msg);			
		}	
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("editproject");
		$view->setModel($this->_model, true);
		$view->editProjectForm();
	}

	function editScorm(){
		$redirect_to= JFactory::getApplication()->input->get('redirect_to', NULL);
		$type		= JFactory::getApplication()->input->get('type',"");
		
		if(isset($redirect_to)) {
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('temp_type', $type);
			
			$msg=NULL;
			$this->setRedirect(JRoute::_($redirect_to, false), $msg);			
		}	
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("editscorm");
		$view->setModel($this->_model, true);
		$view->editScormForm();
	}
	
	function saveMedia(){
		if($id = $this->_model->storeMedia()){
			$msg = JText::_('GURU_MEDIASAVED');
		} 
		else{
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$isempty = $registry->get('isempty', "");
			$nosize = $registry->get('nosize', "");
		
			if($isempty == 1){
				$msg = JText::_('GURU_MEDIASAVEDFAILED_TEXTEMPTY');
				$registry->set('isempty', "");
			}
			else{
				$msg = JText::_('GURU_MEDIASAVEDFAILED');
				
				if($nosize == 0){
					$msg = $msg.JText::_('GURU_MEDIASAVEDSIZE');
					$n='warning';
				}
				
				$registry->set('nosize', "");
			}	
		}
		$link = "index.php?option=com_guru&view=guruauthor&task=authormymedia&layout=authormymedia";
		$this->setRedirect(JRoute::_($link, false), $msg, $n);
	}
	
	function applyMedia(){
		$id = JFactory::getApplication()->input->get("id","0");
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$isempty = $registry->get('isempty', "");
		$nosize = $registry->get('nosize', "");
		
		if($this->_model->storeMedia()){
			$msg = JText::_('GURU_MEDIAAPPLY');
		} 
		else{
			if($isempty == 1){
				$msg = JText::_('GURU_MEDIASAVEDFAILED_TEXTEMPTY');
				$registry->set('isempty', "");
			}
			else{
				$msg = JText::_('GURU_MEDIAAPPLYFAILED');
				
				if($nosize == 0){
					$msg = $msg.JText::_('GURU_MEDIASAVEDSIZE');
					$n='warning';
				}
				
				$registry->set('nosize', "");
			}	
		}
		
		if($id != 0){
			$link = "index.php?option=com_guru&view=guruauthor&task=editMedia&cid=".intval($id);
		} 
		else{
			$last_media = $this->_model->last_media();
			$link = "index.php?option=com_guru&view=guruauthor&task=editMedia&cid=".$last_media;
		}
		$this->setRedirect(JRoute::_($link, false), $msg,$n);
	}

	function saveModalProject(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		JFactory::getApplication()->input->set ("tmpl", "component");

		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;
		$id = $jinput->post->get('id', "0", "raw");
		$user = JFactory::getUser();

        require_once(JPATH_BASE.'/components/com_guru/tables/guruproject.php');
        $projectTable =  new TableguruProject();
        $projectTable->id = $id;
        $projectTable->course_id = $jinput->post->get('course_id');
		$projectTable->author_id = $user->id;
        $projectTable->title = $jinput->post->get('title','','raw');
        $projectTable->description = $jinput->post->get('description','','raw');
        $projectTable->file = $jinput->post->get('file','','raw');
		$projectTable->created = $jinput->post->get('start','','raw');
		$projectTable->updated = date("Y-m-d H:i:s");
		$projectTable->start = $jinput->post->get('start','','raw');
        $projectTable->end = $jinput->post->get('end','','raw');
        $projectTable->layout = 0;
        $projectTable->published = $jinput->post->get('published','0','raw');

        $idParam = !empty($projectTable->id)?'&id='.$projectTable->id:'';

        if($projectTable->end == ""){
        	$projectTable->end = "0000-00-00 00:00:00";
        }

        if($projectTable->store()){
?>
			<style>
				.contentpane.modal{
					display: block !important;
				}
			</style>

			<script type="text/javascript">
			
				function loadjscssfile(filename, filetype){
					if (filetype=="js"){ //if filename is a external JavaScript file
						var fileref=document.createElement('script')
					  	fileref.setAttribute("type","text/javascript")
					  	fileref.setAttribute("src", filename)
					}
					else if (filetype=="css"){ //if filename is an external CSS file
						var fileref=document.createElement("link")
						fileref.setAttribute("rel", "stylesheet")
						fileref.setAttribute("type", "text/css")
						fileref.setAttribute("href", filename)
					}
						if (typeof fileref!="undefined")
						document.getElementsByTagName("head")[0].appendChild(fileref)
				}
						
				function loadprototipe(){
					//loadjscssfile("<?php echo JURI::base().'components/com_guru/views/guruauthor/tmpl/prototype-1.6.0.2.js' ?>","js");
				}

				function addproject (idu, name, asoc_file, description) {
					//loadprototipe();
					
					var url = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=add_media_ajax&id='+idu+'&type=project';

					jQuery.ajax({
						url : url,
						cache: false,
						success: function(transport) {
							replace_m = '16';
							to_be_replaced = parent.document.getElementById('media_'+replace_m);
							to_be_replaced.innerHTML = '&nbsp;';
							
							if(replace_m == 99){
								if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {to_be_replaced.innerHTML += transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>'; } else {
									to_be_replaced.innerHTML += transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><br /><div  style="text-align:center"><i>' + description + '</i></div>';
								}
							} else {
								to_be_replaced.innerHTML += transport;
								parent.document.getElementById("media_"+99).style.display="";
								parent.document.getElementById("description_med_99").innerHTML=''+name;
								
								parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
								parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
								parent.document.getElementById('db_media_'+replace_m).value = idu;
							}			
						
							screen_id = document.getElementById('the_screen_id').value;
							replace_edit_link = parent.document.getElementById('a_edit_media_'+replace_m);
							replace_edit_link.href = 'index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editProject&cid='+ idu;
							
							if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {
								var qwe='&nbsp;'+transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>';
							} else {
								var qwe='&nbsp;'+transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
							}
							
							window.parent.test(replace_m, idu, transport);
						}
					});
					setTimeout('window.parent.document.getElementById("close").click()',1000);
					return true;
				}
			</script>
<?php
			echo '<div style="padding:15px;"><strong>'.JText::_("GURU_PROJECT_SAVED_WAIT").'</strong></div>';

			echo '<script type="text/javascript">
					window.onload=function(){
						var t=setTimeout(\'addproject('.$projectTable->id.', "'.$projectTable->title.'", "-", "'.$projectTable->description.'");\', 1000);						
					}
				</script>';
        }

        return false;
	}

	function saveModalScorm(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		JFactory::getApplication()->input->set ("tmpl", "component");

		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;
		$id = $jinput->post->get('id', "0", "raw");
		$user = JFactory::getUser();

        require_once(JPATH_BASE.'/components/com_guru/tables/guruscorm.php');
        $scormTable =  new TableguruScorm();
        $scormTable->id = $id;
        $scormTable->course_id = $jinput->post->get('course_id');
		$scormTable->author_id = $user->id;
        $scormTable->title = $jinput->post->get('title','','raw');
        $scormTable->description = $jinput->post->get('description','','raw');
        $scormTable->file = $jinput->post->get('file','','raw');
		$scormTable->created = $jinput->post->get('start','','raw');
		$scormTable->updated = date("Y-m-d H:i:s");
		$scormTable->start = $jinput->post->get('start','','raw');
        $scormTable->end = $jinput->post->get('end','','raw');
        $scormTable->layout = 0;
        $scormTable->published = $jinput->post->get('published','0','raw');

        $idParam = !empty($scormTable->id)?'&id='.$scormTable->id:'';

        if($scormTable->end == ""){
        	$scormTable->end = "0000-00-00 00:00:00";
        }

        if($scormTable->store()){
?>
			<style>
				.contentpane.modal{
					display: block !important;
				}
			</style>

			<script type="text/javascript">
			
				function addscorm (idu, name, asoc_file, description) {
					var url = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=add_media_ajax&id='+idu+'&type=scorm';

					jQuery.ajax({
						url : url,
						cache: false,
						success: function(transport) {
							replace_m = '17';
							to_be_replaced = parent.document.getElementById('media_'+replace_m);
							to_be_replaced.innerHTML = '&nbsp;';
							
							if(replace_m == 99){
								if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {to_be_replaced.innerHTML += transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>'; } else {
									to_be_replaced.innerHTML += transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><br /><div  style="text-align:center"><i>' + description + '</i></div>';
								}
							} else {
								to_be_replaced.innerHTML += transport;
								parent.document.getElementById("media_"+99).style.display="";
								parent.document.getElementById("description_med_99").innerHTML=''+name;
								
								parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
								parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
								parent.document.getElementById('db_media_'+replace_m).value = idu;
							}			
						
							screen_id = document.getElementById('the_screen_id').value;
							replace_edit_link = parent.document.getElementById('a_edit_media_'+replace_m);
							replace_edit_link.href = 'index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editScorm&cid='+ idu;
							
							if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {
								var qwe='&nbsp;'+transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>';
							} else {
								var qwe='&nbsp;'+transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
							}
							
							window.parent.test(replace_m, idu, transport);
						}
					});
					setTimeout('window.parent.document.getElementById("close").click()',1000);
					return true;
				}
			</script>
<?php
			echo '<div style="padding:15px;"><strong>'.JText::_("GURU_SCORM_SAVED_WAIT").'</strong></div>';

			echo '<script type="text/javascript">
					window.onload=function(){
						setTimeout(\'addscorm('.$scormTable->id.', "'.$scormTable->title.'", "-", "'.$scormTable->description.'")\', 1000);
					}
				</script>';
        }

        return false;
	}
	
	function savesbox(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		JFactory::getApplication()->input->set ("tmpl", "component");
		$id				= JFactory::getApplication()->input->get("id","0");
		$mediatext		= JFactory::getApplication()->input->get('mediatext','');
		$mediatextvalue	= JFactory::getApplication()->input->get('mediatextvalue','');
		$screen			= JFactory::getApplication()->input->get('screen', '0');
		
		$action = JFactory::getApplication()->input->get("action", "");
		
		if($action == "addtext" || $action == "addmedia"){
			$screen = "1";
		}
		
		if($id==0){
			if((($mediatext!="") && ($mediatextvalue!="") && ($screen!="")) || ($screen=="0")){				
				if($id=$this->_model->storeMedia()){
					?>
					<script type="text/javascript" src="<?php echo JURI::root().'media/system/js/mootools.js' ?>"></script>
								
					<script type="text/javascript">
					
						function loadjscssfile(filename, filetype){
							if (filetype=="js"){ //if filename is a external JavaScript file
								var fileref=document.createElement('script')
							  	fileref.setAttribute("type","text/javascript")
							  	fileref.setAttribute("src", filename)
							}
							else if (filetype=="css"){ //if filename is an external CSS file
								var fileref=document.createElement("link")
								fileref.setAttribute("rel", "stylesheet")
								fileref.setAttribute("type", "text/css")
								fileref.setAttribute("href", filename)
							}
								if (typeof fileref!="undefined")
								document.getElementsByTagName("head")[0].appendChild(fileref)
						}
								
						function loadprototipe(){
							//loadjscssfile("<?php echo JURI::base().'components/com_guru/views/guruauthor/tmpl/prototype-1.6.0.2.js' ?>","js");
						//alert('testing');
						}
								
						function addmedia (idu, name, description) {
						<?php if($screen != "0"){ ?>
							jQuery.ajax({
								<?php
									if($mediatext=='med'){
										echo "url: '".JURI::root()."index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=add_media_ajax&id='+idu,";
									}
									else{
										echo "url: '".JURI::root()."index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=add_text_ajax&id='+idu,";
									}
								?>
								cache: false
							})
							.done(function(transport) {
								replace_m = <?php echo $mediatextvalue;?>;
								to_be_replaced = parent.document.getElementById('<?php if($mediatext=='med') { echo "media";} else { echo "text";} ?>_'+replace_m);
								to_be_replaced.innerHTML = '&nbsp;';
								if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {
									to_be_replaced.innerHTML += transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>'; 
								} 
								else {
									to_be_replaced.innerHTML += transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><br /><div  style="text-align:center"><i>' + description + '</i></div>';
								}
								
								parent.document.getElementById('before_menu_<?php if($mediatext=='med') { echo "med";} else { echo "txt";} ?>_'+replace_m).style.display = 'none';
								parent.document.getElementById('after_menu_<?php if($mediatext=='med') { echo "med";} else { echo "txt";} ?>_'+replace_m).style.display = '';
								parent.document.getElementById('db_<?php if($mediatext=='med') { echo "media";} else { echo "text";} ?>_'+replace_m).value = idu;
								
								screen_id = <?php echo $screen; ?>;
								
								replace_edit_link = parent.document.getElementById('a_edit_<?php if($mediatext=='med') { echo "media";} else { echo "text";} ?>_'+replace_m);
								replace_edit_link.href = 'index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&cid='+ idu +'&scr=' + screen_id;
								if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {
									var qwe='&nbsp;'+transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>';
								} 
								else {
									var qwe='&nbsp;'+transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
								}
								window.parent.<?php if($mediatext=='med') { echo "";} else { echo "tx";} ?>test(replace_m, idu, qwe);
							});
						<?php } 
							else { 
						// for adding sound ?>
							jQuery.ajax({
								url :'<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=add_media_ajax&id='+idu;
								cache: false
							})
							.done(function(transport) {
								replace_m = "99";
								to_be_replaced = parent.document.getElementById('media_'+replace_m);
								to_be_replaced.innerHTML = '&nbsp;';
								
								if(replace_m!=99){
									if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {
										to_be_replaced.innerHTML += transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>'; 
									} 
									else {
										to_be_replaced.innerHTML += transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><br /><div  style="text-align:center"><i>' + description + '</i></div>';
									}
								} 
								else {
									to_be_replaced.innerHTML += transport;
									parent.document.getElementById("media_"+99).style.display="";
									parent.document.getElementById("description_med_99").innerHTML=''+name;
								}
								parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
								parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
								parent.document.getElementById('db_media_'+replace_m).value = idu;
								
								screen_id = document.getElementById('the_screen_id').value;
								replace_edit_link = parent.document.getElementById('a_edit_media_'+replace_m);
								replace_edit_link.href = 'index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&cid='+ idu+"&scr="+replace_m;
								if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {
									var qwe='&nbsp;'+transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>';
								} 
								else {
									var qwe='&nbsp;'+transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
								}
								window.parent.test(replace_m, idu, '<span class="success-add-media"><?php echo JText::_("GURU_SUCCESSFULLY_ADED_MEDIA"); ?></span>');
							});
						<?php }?>
							//window.parent.close_modal();
							setTimeout('window.parent.document.getElementById("close").click()',1000);
							//window.parent.SqueezeBox.close();						
							return true;						
						}				
					</script>
                    					
					<?php
					
					$current_media = $this->_model->getMediaInfo($id);
					
					if($screen=="0"){
						echo '<script type="text/javascript">window.onload=function(){
					
						var t=setTimeout(\'addmedia('.$id.', "'.addslashes(trim($current_media->name)).'", "-", "");\',1000);						
						}</script>';
					}
					else{
						echo '<script type="text/javascript">window.onload=function(){
						
						var t=setTimeout(\'addmedia('.$id.', "'.addslashes(trim($current_media->name)).'", "'.$current_media->instructions.'");\',1000);						
						}</script>'; 
					}
					
							
					echo '<div style="padding:15px;"><strong>Media saved. Please wait...</strong></div>';
				}
			}		
		} 
		else{
			if($id = $this->_model->storeMedia()){
				$msg = JText::_('GURU_MEDIASAVED');
			} 
			else{
				$msg = JText::_('GURU_MEDIASAVEDFAILED');
			}

			echo '<script type="text/javascript">window.onload=function(){
				window.parent.page_refresh('.$screen.');
				var t=setTimeout(\'window.parent.SqueezeBox.close();\',0);
			}</script>';
			
			echo '<div style="padding:15px;"><strong>Media saved. Please wait...</strong></div>';
		}
	}
	function saveMediaCat(){
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$isempty = $registry->get('isempty', "");
		$nosize = $registry->get('nosize', "");
		
		if($id = $this->_model->storeMediaCat()){
			$msg = JText::_('GURU_MEDIAAPPLY');
		} 
		else{
			if($isempty == 1){
				$msg = JText::_('GURU_MEDIASAVEDFAILED_TEXTEMPTY');
				$registry->set('isempty', "");
			}
			else{
				$msg = JText::_('CUSTSAVEFAILED');
				
				if($nosize == 0){
					$n='warning';
				}
				
				$registry->set('nosize', "");
			}	
		}
		$link = "index.php?option=com_guru&view=guruauthor&task=authormymediacategories&layout=authormymediacategories";
		$this->setRedirect(JRoute::_($link, false), $msg, $n);
	}
	
	function applyMediaCat(){
		$id = JFactory::getApplication()->input->get("id","0");
		$return = $this->_model->storeMediaCat();
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$isempty = $registry->get('isempty', "");
		$nosize = $registry->get('nosize', "");
		
		if($return["0"]){
			$msg = JText::_('GURU_MEDIAAPPLY');
		} 
		else{
			if($isempty == 1){
				$msg = JText::_('GURU_MEDIASAVEDFAILED_TEXTEMPTY');
				$registry->set('isempty', "");
			}
			else{
				$msg = JText::_('GURU_MEDIAAPPLYFAILED');
				
				if($nosize == 0){
					$msg = $msg.JText::_('GURU_MEDIASAVEDSIZE');
					$n='warning';
				}
				
				$registry->set('nosize', "");
			}	
		}
		
		if($id != 0){
			$link = "index.php?option=com_guru&view=guruauthor&task=authoraddeditmediacat&id=".intval($id);
		} 
		else{
			$last_media = $return["1"];
			$link = "index.php?option=com_guru&view=guruauthor&task=authoraddeditmediacat&id=".intval($last_media);
		}
		$this->setRedirect(JRoute::_($link, false), $msg,$n);
	}
	
	function duplicateMediaCat(){
		$result = $this->_model->duplicateMediaCat();
		
		$type = "message";
		if($result){
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymediacategories&layout=authormymediacategories";
			$msg = JText::_("GURU_DUPLICATE_CATEG_SUCCESSFULLY");
			$type = "message";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymediacategories&layout=authormymediacategories";
			$msg = JText::_("GURU_DUPLICATE_CATEG_UNSUCCESSFULLY");
			$type = "error";
		}
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}

	function applyLesson(){
		$task = JFactory::getApplication()->input->get("task", "");
		$return = $this->_model->storeLesson();
		
		if($return["return"] === TRUE){
			$msg = JText::_('GURU_TASKS_SAVED');
		} else {
			$msg = JText::_('GURU_TASKS_NOTSAVED');
		}
		
		$id = JFactory::getApplication()->input->get("id", "");
		$module = JFactory::getApplication()->input->get("module","");
		if($id == ""){
			$id = $return["id"];
		}
		$progrid=JFactory::getApplication()->input->get("day", "");
		$link ="index.php?option=com_guru&view=guruauthor&tmpl=component&task=editsbox&cid=".$id."&progrid=".$progrid."&module=".intval($module);	
		$this->setRedirect(JURI::root().$link);
	}
	
	function saveLessonNew(){
		$task = JFactory::getApplication()->input->get("task", "");
		$return = $this->_model->storeLesson();
		
		if($return["return"] === TRUE){
			$msg = JText::_('GURU_TASKS_SAVED');
		}
		else{
			$msg = JText::_('GURU_TASKS_NOTSAVED');
		}
		
		$module = JFactory::getApplication()->input->get("module","");
		$progrid = JFactory::getApplication()->input->get("day", "");
		
		$link = "index.php?option=com_guru&controller=guruAuthor&task=editsbox&tmpl=component&day=".intval($module)."&progrid=".intval($progrid)."&cid=";
		$this->setRedirect(JURI::root().$link);
	}
	
	function saveLesson(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		JFactory::getApplication()->input->set ("tmpl", "component");	
		$return = $this->_model->storeLesson();
		
		if($return["return"] === TRUE){
			$msg = JText::_('GURU_TASKS_SAVED');
		} 
		else{
			$msg = JText::_('GURU_TASKS_NOTSAVED');
		}

		echo '<style>.page-title{display:none;} .guru-content{padding:10px;}</style>';
		echo "Step saved. Please wait...";
		echo '<script type="text/javascript">window.onload=function(){
			window.parent.location.reload(true);
			}</script>';
	}
	
	function editQuiz(){
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=authorprofile"."&Itemid=".$Itemid, false));
			return true;
		}
		
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("editQuiz");
		$view->setModel($this->_model, true);
		$view->editQuiz();
	}
	
	function editQuizFE(){
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
		
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=authorprofile"."&Itemid=".$Itemid, false));
			return true;
		}
		
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("editquizfe");
		$view->setModel($this->_model, true);
		$view->editQuizFE();
	}
	
	function mark(){
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=authorprofile"."&Itemid=".$Itemid, false));
			return true;
		}
		
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("mark");
		$view->setModel($this->_model, true);
		$view->mark();
	}
	
	function saveQuiz(){
		JFactory::getApplication()->input->set("hidemainmenu", 1);
		JFactory::getApplication()->input->set("tmpl", "component");
		$id	= JFactory::getApplication()->input->get("id", "0");
		$screen	= 12;
		
		?>
        <script language="javascript" type="text/javascript">	
			function loadjscssfile(filename, filetype){
				if (filetype=="js"){ //if filename is a external JavaScript file
					var fileref=document.createElement('script')
					fileref.setAttribute("type","text/javascript")
					fileref.setAttribute("src", filename)
				}
				else if (filetype=="css"){ //if filename is an external CSS file
					var fileref=document.createElement("link")
					fileref.setAttribute("rel", "stylesheet")
					fileref.setAttribute("type", "text/css")
					fileref.setAttribute("href", filename)
				}
				if (typeof fileref!="undefined"){
					document.getElementsByTagName("head")[0].appendChild(fileref);
				}
			}
		
			function loadprototipe(){
				//loadjscssfile("<?php echo JURI::base().'components/com_guru/views/guruauthor/tmpl/prototype-1.6.0.2.js' ?>","js");
			}
		
			function addmedia (idu, name, asoc_file, description) {
				//loadprototipe();
				jQuery.ajax({
					url: '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=add_quizz_ajax&id='+idu+'&type=quiz',
					cache: false
				})
				.done(function(transport) {
					to_be_replaced=parent.document.getElementById('media_15');
					replace_m=15;
					to_be_replaced.innerHTML = '&nbsp;';
			
					to_be_replaced.innerHTML += transport;
					parent.document.getElementById("media_"+99).style.display="";
					parent.document.getElementById("description_med_99").innerHTML=''+name;
						
					parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
					parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
					parent.document.getElementById('db_media_'+replace_m).value = idu;
					
					replace_edit_link = parent.document.getElementById('a_edit_media_'+replace_m);
					replace_edit_link.href = 'index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editQuiz&cid='+ idu;
					var qwe='&nbsp;'+transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
							
					window.parent.test(replace_m, idu,qwe);
				});
				
				setTimeout('window.parent.document.getElementById("close").click()',1000);
				return true;
			}
		</script>
        <?php
		
		if($id==0){
			if($id=$this->_model->storeQuiz()){
				$quiz=$this->_model->getQuizById();
				echo '<script type="text/javascript">window.onload=function(){
					var t=setTimeout(\'addmedia('.$quiz->id.', "'.$quiz->name.'","-", "'.$quiz->description.'");\',1000);						
			}</script>';
			}		
		}
		else{
			if($id=$this->_model->storeQuiz()){
				$msg = JText::_('GURU_MEDIASAVED');
				$quiz=$this->_model->getQuizById();
				echo '<script type="text/javascript">window.onload=function(){
					var t=setTimeout(\'addmedia('.$quiz->id.', "'.$quiz->name.'","-", "'.$quiz->description.'");\',1000);						
			}</script>';
			}
			else {
				$msg = JText::_('GURU_MEDIASAVEDFAILED');
			}
			echo '<script type="text/javascript">window.onload=function(){
				var t=setTimeout(\'window.parent.document.getElementById("sbox-window").close();\',0);
				window.parent.page_refresh('.$screen.');
			}</script>';
		}
		echo '<strong>'.JText::_('GURU_QUIZSAVED_PLSWAIT').'</strong>';
	}
	
	function addQuestion(){
		JFactory::getApplication()->input->set("hidemainmenu", 1); 
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("addquestion");
		$view->setModel($this->_model, true);
		$view->addQuestion();
	}
	
	function saveQuestion(){
		$qtext 	= JFactory::getApplication()->input->get('question_text','','raw');
		$quizid = JFactory::getApplication()->input->get('quizid','0', "raw");
		$is_from_modal_lesson = JFactory::getApplication()->input->get('is_from_modal', "raw");
		$question_type = JFactory::getApplication()->input->get('type', "", "raw");
		$media_ids_question = JFactory::getApplication()->input->get('question_media_ids', "", "raw");
		$points = JFactory::getApplication()->input->get('question_weight_tf', "", "raw");
		$true_false_ch = JFactory::getApplication()->input->get('truefs_ans', "-1", "raw");
		$question_id = JFactory::getApplication()->input->get('question_id', "0", "raw");
		$from_save_or_not = "saveandclose";
		$ans_content = JFactory::getApplication()->input->get('ans_content','','raw');
		
		$qid = $question_id;
		
		if(intval($qid) == 0){
			$this->_model->addquestion($qtext, $quizid, $question_type, $media_ids_question, $points, $true_false_ch, $question_id, $from_save_or_not, $ans_content);
		}
		else{
			$this->_model->updatequestion($qtext, $quizid, $question_type, $media_ids_question, $points, $true_false_ch, $question_id, $from_save_or_not, $ans_content);
		}
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$registry->set('added_questions_tab', "1");

		if($quizid != 0){
			if($is_from_modal_lesson != 1){
				echo '<style>.page-title{display:none;} .guru-content{padding:10px;}</style>';
				echo "Saving question. Please wait...";
				echo '<script type="text/javascript">';
				
				echo 'if(window.parent.document.adminForm.name.value == ""){
						window.parent.location.reload();	
					  }
					  else{
						window.parent.document.adminForm.task.value = "apply_quizFE";
						window.parent.document.adminForm.submit();
					  }';
				echo '</script>';
			
			}
			else{
				echo '<style>.page-title{display:none;} .guru-content{padding:10px;}</style>';
				echo "Saving question. Please wait...";
				echo '<script type="text/javascript">';
				echo '	window.parent.location.reload();';
				echo '</script>';
			}
			die();
			
		}
		
		if($quizid == 0){
			if($is_from_modal_lesson != 1){
				echo '<style>.page-title{display:none;} .guru-content{padding:10px;}</style>';
				echo "Saving question. Please wait...";
				echo '<script type="text/javascript">';
				
				echo 'if(window.parent.document.adminForm.name.value == ""){
							window.parent.location.reload();	
					  }
					  else{
							window.parent.document.adminForm.task.value = "apply_quizFE";
							window.parent.document.adminForm.submit();
					  }';
				echo '</script>';
			
			}
			else{
				echo '<style>.page-title{display:none;} .guru-content{padding:10px;}</style>';
				echo "Saving question. Please wait...";
				echo '<script type="text/javascript">';
				echo '	window.parent.location.reload();';
				echo '</script>';
			}
			die();
		}
	}
	
	function editQuestion(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1); 
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("editquestion");
		$view->setModel($this->_model, true);
		$view->editQuestion();
	}
	
	function jumpbts(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1); 
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("jumpbts");
		$view->setModel($this->_model, true);
		$view->jumpbts();
	}
	
	function saveJump(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		JFactory::getApplication()->input->set ("tmpl", "component");
		$pieces = $this->_model->saveJump();
		echo '<script type="text/javascript">window.onload=function(){
				window.parent.jump('.$pieces["1"].','.$pieces["0"].',"'.$pieces["2"].'");
				window.parent.document.getElementById("close").click();
			}</script>';
		echo '<strong>Jump saved. Please wait...</strong>';
	}
	
	function editsboxx(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("editformsboxx");
		$view->setModel($this->_model, true);
		$view->editMediaForm();
	}
	
	function duplicateQuiz(){
		$result = $this->_model->duplicateQuiz();
		$type = "message";
		if($result === TRUE){
			$link = "index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes";
			$msg = JText::_("GURU_DUPLICATE_QUIZ_SUCCESSFULLY");
			$type = "message";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes";
			$msg = JText::_("GURU_DUPLICATE_QUIZ_UNSUCCESSFULLY");
			$type = "error";
		}
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}
	
	function courseStats(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("coursestats");
		$view->setModel($this->_model, true);
		$view->courseStats();
	}
	
	function quizzStats(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("quizzstats");
		$view->setModel($this->_model, true);
		$view->quizzStats();
	}
	
	function saveQuizFE(){
		if($id = $this->_model->storeQuiz()){
			$msg = JText::_('GURU_QUIZSAVED');
		} 
		else{
			$msg = JText::_('GURU_QUIZ_NOT_SAVED');
		}
		$link = "index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes";
		$this->setRedirect(JRoute::_($link), $msg);
	}

	function applyQuizFE(){
		$id = JFactory::getApplication()->input->get("id", "0");
		if($this->_model->storeQuiz()){
			$msg = JText::_('GURU_QUIZSAVED');
		}
		else{
			$msg = JText::_('GURU_QUIZ_NOT_SAVED');
		}
		
		$valueop = JFactory::getApplication()->input->post->getArray();
		if($id == 0){
			$db = JFactory::getDBO();
			$sql = "SELECT max(id) FROM #__guru_quiz ";
			$db->setQuery($sql);
			$new_quiz_id = $db->loadColumn();
			$id = $new_quiz_id["0"];
		}
		
		if($valueop['valueop'] == 1){
			$link = "index.php?option=com_guru&view=guruauthor&task=editQuizFE&cid=".intval($id)."&e=1";
		}
		else{
			$link = "index.php?option=com_guru&view=guruauthor&task=editQuizFE&cid=".intval($id);
		}
		$this->setRedirect(JRoute::_($link), $msg);
	}
	
	function saveQuestionandkeep(){
		$qtext 	= JFactory::getApplication()->input->get('question_text','','raw');
		$quizid = JFactory::getApplication()->input->get('quizid','0', "raw");
		$is_from_modal_lesson = JFactory::getApplication()->input->get('is_from_modal', "raw");
		$question_type = JFactory::getApplication()->input->get('type', "");
		$media_ids_question = JFactory::getApplication()->input->get('question_media_ids', "", "raw");
		$points = JFactory::getApplication()->input->get('question_weight_tf', "", "raw");
		$true_false_ch = JFactory::getApplication()->input->get('truefs_ans', "-1", "raw");
		$question_id = JFactory::getApplication()->input->get('question_id', "0", "raw");
		$ans_content = JFactory::getApplication()->input->get('ans_content','','raw');
		$from_save_or_not = "savekeep";
		
		$qid = $question_id;
		if(intval($qid) == 0){
			$return = $this->_model->addquestion($qtext,$quizid,$question_type,$media_ids_question,$points, $true_false_ch,$question_id,$from_save_or_not, $ans_content);
		}
		else{
			$return = $this->_model->updatequestion($qtext,$quizid,$question_type,$media_ids_question,$points, $true_false_ch,$question_id,$from_save_or_not, $ans_content);
		}
		$msg = JText::_('GURU_QUIZSAVED');
		$link ="index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&cid=".$quizid."&qid=".$return."&type=".$question_type;	
		$this->setRedirect($link, $msg);
	}
	function addquizzes(){
		JFactory::getApplication()->input->set("hidemainmenu", 1); 
		$view = $this->getView("guruAuthor", "html");
		$view->setLayout("addQuizzes");
		$view->setModel($this->_model, true);
		$view->addquizzes();
	}	
	function savequizzes(){
		$quizid = JFactory::getApplication()->input->get('quizid','0', "raw");
		$quizzes_ids = JFactory::getApplication()->input->get('quizzes_ids','', "raw");
		$db = JFactory::getDbo();

		$query = "SELECT id FROM `#__guru_quiz` WHERE id = " . $db->q($quizid);
		if (!$db->setQuery($query)->loadResult()) {
			die('quiz not found');
		}

		$sql = "SELECT quizzes_ids FROM `#__guru_quizzes_final` WHERE qid=".$quizid;
		$db->setQuery($sql);
		$result = $db->loadResult();	

		if($result){
			$sql = "UPDATE `#__guru_quizzes_final` SET quizzes_ids='".$quizzes_ids."' WHERE qid=".$quizid;
			$db->setQuery($sql);
			$db->execute();
		}
		else{
			$sql = "INSERT INTO `#__guru_quizzes_final` (quizzes_ids, qid) VALUES ('".$quizzes_ids."','".$quizid."' )";
			$db->setQuery($sql);
			$db->execute();
		}

		echo "Saving quizzes. Please wait...";
		echo '<script type="text/javascript">';
		echo '
				if(window.parent.document.adminForm){
					window.parent.location.reload();	
				}';
		echo '</script>';
		die();
	}
		
	public function saveOrderQuestions(){
		// Get the arrays from the Request
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("guruAuthor");
		// Save the ordering
		$return = $model->saveOrderQuest();
		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}

	public function saveOrderExercices(){
		// Get the arrays from the Request
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("guruAuthor");
		// Save the ordering
		$return = $model->saveorderFile();
		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}
	
	function studentdetails(){
		JFactory::getApplication()->input->set ("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("studentdetails");
		$view->setModel($this->_model, true);
		$view->studentdetails();
	}
	
	function studentdetailslesson(){
		JFactory::getApplication()->input->set ("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("studentdetailslesson");
		$view->setModel($this->_model, true);
		$view->studentdetailslesson();
	}

	function studentdetailscert(){
		JFactory::getApplication()->input->set ("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("studentdetailscert");
		$view->setModel($this->_model, true);
		$view->studentdetailscert();
	}
	
	function studentquizes(){
		JFactory::getApplication()->input->set ("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("studentquizes");
		$view->setModel($this->_model, true);
		$view->studentquizes();
	}
	
	function quizdetails(){
		JFactory::getApplication()->input->set ("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("quizdetails");
		$view->setModel($this->_model, true);
		$view->quizdetails();
	}
	
	function student_quizdetails(){
		JFactory::getApplication()->input->set ("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("student_quizdetails");
		$view->setModel($this->_model, true);
		$view->student_quizdetails();
	}
	
	function terms(){
        $view = $this->getView("guruauthor", "html");
        $view->setLayout("terms");
        $view->terms();
    }
	function applyCommissions(){
		$result = $this->_model->applyCommissions();
		$type = "message";
		if($result === TRUE){
			$link = "index.php?option=com_guru&view=guruauthor&task=authorcommissions&layout=authorcommissions";
			$msg = JText::_("GURU_MODIF_OK");
			$type = "message";
		}		
		$this->setRedirect(JRoute::_($link, false), $msg, $type);
	}
	
	function uploadAjaxImage(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."fileuploader.php");
		die();
	}

	function uploadAjaxProject(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."fileuploader.php");
		die();
	}

	function uploadAjaxScorm(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."fileuploader.php");
		die();
	}
	
	function checkExistingUserU(){
		$database = JFactory::getDBO();
		$username = JFactory::getApplication()->input->get("username", "", "raw");
		$email = JFactory::getApplication()->input->get("email", "", "raw");
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		
		if(intval($id) == 0){// new user
			$sql = "select count(*) from #__users where username='".$database->escape(trim($username))."'";
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadResult();
	
			if($result > 0){
				echo "222";die();
				return true;
			}
			else{
				echo "333";die();
				return true;
			}
			die();
		}
		elseif(intval($id) != 0){
			$sql = "select username, email from #__users where id=".intval($id);
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadAssocList();
			$old_username = $result["0"]["username"];
			$old_email = $result["0"]["email"];
			if($username != $old_username){
				$sql = "select count(*) from #__users where username='".$database->escape(trim($username))."'";
				$database->setQuery($sql);
				$database->execute();
				$result = $database->loadResult();
				if($result > 0){
					echo "222";die();
					return true;
				}
				die();
			}
		}
	}
	
	function checkExistingUserE(){	
		$database = JFactory::getDBO();
		$username = JFactory::getApplication()->input->get("username", "", "raw");
		$email = JFactory::getApplication()->input->get("email", "", "raw");
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		
		if(intval($id) == 0){// new user
			$sql = "select count(*) from #__users where email='".$database->escape(trim($email))."'";
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadResult();
			if($result > 0){
				echo "111";die();
				return true;
			}
			else{
				echo "222";die();
				return true;
			}
			
		}
		elseif(intval($id) != 0){
			$sql = "select username, email from #__users where id=".intval($id);
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadAssocList();
			$old_username = $result["0"]["username"];
			$old_email = $result["0"]["email"];
			if($email != $old_email){
				$sql = "select count(*) from #__users where email='".$database->escape(trim($email))."'";
				$database->setQuery($sql);
				$database->execute();
				$result = $database->loadResult();
				if($result > 0){
					echo "111";die();
					return true;
				}
				die();
			}
		}
	}
	
	function pubUnpubAjax(){
		$db = JFactory::getDBO();		
		$id = JFactory::getApplication()->input->get("id");
		
		$sql = "select published from #__guru_media where id =".$id;
		$db->setQuery($sql);
		$published = $db->loadColumn();
		$published = $published["0"];
		$ret = "";
		
		if($published){
			$sql = "update #__guru_media set published='0' where id =".$id;
			$ret = 'unpublish';
		}
		else{
			$ret = 'publish';
			$sql = "update #__guru_media set published='1' where id =".$id;
		}
		$db->setQuery($sql);
		if (!$db->execute() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
		echo $ret;
		die();
	}
	
	function publishQuizAjax(){
		$db = JFactory::getDBO();
		$v = $this->input->get("v", "0");
		$table = "#__guru_questions_v3";
		if($v == 1){
			$table = "#__guru_quiz";
		}
		
		$id = $this->input->getInt("id", "0");
		$sql = "update ".$table." set published='1' where id=".$id;
		$db->setQuery($sql);
		if(!$db->execute()){
			die('error');
		}
		die();
	}
	
	function unpublishQuizAjax(){
		$db = JFactory::getDBO();
		$v = $this->input->get("v", "0");
		$table = "#__guru_questions_v3";
		if($v == 1){
			$table = "#__guru_quiz";
		}
		
		$id = $this->input->getInt("id", "0");
		$sql = "update ".$table." set published='0' where id=".$id;
		$db->setQuery($sql);
		if(!$db->execute()){
			die('error');
		}

		die();
	}
	
	function deleteQuizAjax(){
		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;
		$deleted = $input->get('deleted');
		$f = $input->get('f');
		$id = $input->getInt('id', 0);
		if ($f == 0) {
			if ($id) {
				$query = "DELETE FROM `#__guru_questions_v3` WHERE id=" . $deleted . " AND qid=" . $id . "";
				$db->setQuery($query);
				if ($db->execute()) {
					echo "2";
				} else {
					echo $query;
				}
			}
		} elseif ($f == 1) {
			$query = "SELECT quizzes_ids FROM `#__guru_quizzes_final` WHERE qid=" . $id;
			$db->setQuery($query);
			$result = $db->loadResult();
			$ids = explode(',', $result);
			$newIds = array_filter($ids, function($i) use ($deleted) {
				return $i != $deleted;
			});

			if ($newIds) {
				$query = "UPDATE `#__guru_quizzes_final` SET quizzes_ids='" . implode(',', $newIds) . "' WHERE qid=" . $id;
				$db->setQuery($query);
				if ($db->execute()) {
					echo "2";
				} else {
					echo $query;
				}
			} else {
				$query = "DELETE FROM `#__guru_quizzes_final` WHERE qid = $id";
				$db->setQuery($query)->execute();
				echo '2';
			}
		}

	}
	
	function addQuizzAjax(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."guruauthor".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."ajaxAddMedia.php");
		die();
	}
	
	function addTextAjax(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."guruauthor".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."ajaxAddText.php");
		die();
	}
	
	function addMediaAjax(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."guruauthor".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."ajaxAddMedia.php");
		die();
	}
	
	function deleteFinalQuizzAjax(){
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
	
	function deleteGroupAjax(){
		$database = JFactory::getDBO();
		
		$sql = "SELECT imagesin FROM #__guru_config WHERE id ='1' ";
		$database->setQuery($sql);
		if (!$database->execute()) {
			echo $database->stderr();
			return;
		}
		$imagesin = $database->loadResult();			
		$data_get = JFactory::getApplication()->input->get->getArray();
		
		$group_id = $data_get['group'];
		$cid = $group_id;
		
		$sql = " SELECT locked, ordering FROM #__guru_days WHERE id = ".$cid;
		$database->setQuery($sql);
		if (!$database->execute()) {
			echo $database->stderr();
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
				echo $database->stderr();
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
				$this->setError($database->getErrorMsg());
				return false;
			}
			// we delete the relations with the media - stop
			
			// we delete the relations with the tasks - start
			$sql = "delete from #__guru_mediarel where type='dtask' and type_id=".$cid;
			$database->setQuery($sql);
			if (!$database->execute() ){
				$this->setError($database->getErrorMsg());
				return false;
			}
			// we delete the relations with the tasks - stop		
						
			
			$sql = "SELECT pid FROM #__guru_days WHERE id = ".$cid;
			$database->setQuery($sql);
			$database->execute();	
			$prog_id = $database->loadColumn();				
			
			$sql = "update #__guru_days set ordering=(ordering-1) where pid = '".$prog_id[0]."' AND ordering > ".$ordering;
			$database->setQuery($sql);
			$database->execute();
				
			$sql = "DELETE FROM #__guru_days WHERE id=".$cid;
			$database->setQuery($sql);	
			$database->execute();
		} // end if locked=0
	}
	
	function deleteScreenAjax(){
		$db = JFactory::getDBO();
		$group_id = JFactory::getApplication()->input->get("group");
		$screen_id = JFactory::getApplication()->input->get("screen");
		
		$sql = "SELECT media_id FROM  #__guru_mediarel where type_id=".$screen_id." and type='scr_m'";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		
		if(@$result[0] !=""|| @$result[0] !=NULL){	
			$sql = "UPDATE  #__guru_quiz set hide = 1 where id=".$result[0];
			$db->setQuery($sql);
			$db->execute();
		
		
		$sql = "UPDATE  #__guru_program set id_final_exam = 0  WHERE id = ( SELECT pid FROM #__guru_days WHERE id = '".$group_id."' ) ";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "SELECT hasquiz FROM  #__guru_program where id = ( SELECT pid FROM #__guru_days WHERE id = '".$group_id."' ) ";
		$db->setQuery($sql);
		$result = $db->loadResult();
		
		$new_val = $result -1;
		if($new_val < 0){
			$new_val = 0;
		}
		
		$sql = "UPDATE  #__guru_program set hasquiz =".$new_val."  WHERE id = ( SELECT pid FROM #__guru_days WHERE id = '".$group_id."' ) ";
		$db->setQuery($sql);
		$db->execute();
		
		}
		$query = "SELECT deleted_boards FROM #__guru_kunena_forum WHERE id =1 ";
		$db->setQuery( $query );
		$deleted_boards = $db->loadResult();
		
		$sql = "select count(*) from #__extensions where element='com_kunena'";
		$db->setQuery($sql);
		$count = $db->loadResult();
		
		if($count > 0){
			if($deleted_boards == 1){
				$sql = "SELECT alias FROM #__guru_task WHERE id =".$screen_id;	
				$db->setQuery( $sql );
				$alias = $db->loadResult();		
				$query = "DELETE FROM #__kunena_categories WHERE alias = '".$alias."'";
				$db->setQuery( $query );
				$db->execute();	
				
				$query = "DELETE FROM #__kunena_aliases WHERE alias = '".$alias."'";
				$db->setQuery( $query );
				$db->execute();
			}
			elseif($deleted_boards == 2){
				$sql = "SELECT alias FROM #__guru_task WHERE id =".$screen_id;	
				$db->setQuery( $sql );
				$alias = $db->loadResult();
		
				
				$query = "UPDATE #__kunena_categories set published=0 WHERE alias = '".$db->escape($alias)."'";
				$db->setQuery( $query );
				$db->execute();	
			}
		
		}		
		
		// deleting the old day-task relation
		$sql = "DELETE FROM #__guru_mediarel 
				WHERE type='dtask' AND  media_id = ".$screen_id;
		$db->setQuery($sql);	
		$db->execute();			
		
		//delete all the relation between this task and medias
		$sql = "DELETE FROM #__guru_mediarel 
				WHERE (type='scr_m' or type='scr_t' or type='scr_l') AND type_id=".$screen_id;
		$db->setQuery($sql);	
		$db->execute();	
		
		// deleting the task 
		$sql = "DELETE FROM #__guru_task 
				WHERE id = ".$screen_id;
		$db->setQuery($sql);
			
		$db->execute();	

		$sql = "SELECT influence FROM #__guru_config WHERE id = 1";
		$db->setQuery($sql);
		$influence = $db->loadResult(); // we have selected the INFLUENCE 
		
		$sql = "SELECT locked FROM #__guru_days WHERE id = ".$group_id;
		$db->setQuery($sql);
		$locked = $db->loadResult(); // we have selected the LOCKED property for a day 		
		
		//we need to know where in the status array is the "day" that has a future deleting screen
		$sql = "SELECT ordering FROM #__guru_days WHERE id = ".$group_id;
		$db->setQuery($sql);
		$ordering = $db->loadResult();				
		
		$sql = "SELECT id,days,tasks FROM #__guru_programstatus 
				 WHERE pid = ( SELECT pid FROM #__guru_days WHERE id = '".$group_id."' ) ";
		$db->setQuery($sql);
		$days_array = $db->loadObjectList();				
	
		// if the INFLUENCE is active we also add the day in program_status - begin
		if(($influence==1 && $locked==0))
		{
			foreach($days_array as $one_day_array)
			{
				//$one_day_array_days = $days_array->days;
				$one_day_array_tasks = $one_day_array->tasks;
				//$one_day_array_id = $days_array->id;
				
				$new_day_array_tasks = '';
				
				$removing_start_pos = strpos($one_day_array_tasks, $screen_id.'-');
				$new_day_array_tasks = substr($one_day_array_tasks, 0, $removing_start_pos);
				$new_day_array_tasks = $new_day_array_tasks.substr($one_day_array_tasks, $removing_start_pos+strlen($screen_id)+2, strlen($one_day_array_tasks));
				
				$new_day_array_tasks = str_replace(';,', ';', $new_day_array_tasks);
				$new_day_array_tasks = str_replace(',;', ';', $new_day_array_tasks);
				$new_day_array_tasks = str_replace(',,', ',', $new_day_array_tasks);
				
				$sql = "update #__guru_programstatus set tasks='".$new_day_array_tasks."' where id =".$one_day_array->id;
				$db->setQuery($sql);
				$db->execute();
			}
		}
		$sql = "SELECT user_id, lesson_id, completed, pid FROM  #__guru_viewed_lesson where pid = ( SELECT pid FROM #__guru_days WHERE id = '".$group_id."' ) ";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				$user_id = $value["user_id"];
				$lesson_id = $value["lesson_id"];
				$completed = $value["completed"];
				$pid = $value["pid"];
				
				$temp = str_replace("|".$screen_id."|", "", $lesson_id);
				
				$set = "";
				$date = "";

				if($completed == 0){
					$sql = "SELECT id FROM #__guru_task WHERE id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (SELECT id FROM #__guru_days WHERE pid =".intval($pid)."))";
    				$db->setQuery($sql);
					$lessons_id = $db->loadColumn();
					
					$temp1 = $temp;
					$temp1 = substr($temp1, 1, -1);
					$temp1 = explode("|", $temp1);
					
					$diff = array_diff($lessons_id, $temp1);
					
					if(is_array($diff) && count($diff) > 0){
						$set = ", completed=".$completed;
					}
					else{
						$completed = "1";
						$set .= ", completed=".$completed;
						$set .= ", date_completed='".date("Y-m-d H:i:s")."'";
					}
				}
				
				$query = "UPDATE #__guru_viewed_lesson set lesson_id='".$temp."'".$set." WHERE user_id = ".intval($user_id)." and pid=".intval($pid);
				$db->setQuery( $query );
				$db->execute();
			}
		}
	}
	
	function saveOrderG(){
		include_once(JPATH_SITE."/components/com_guru/views/guruauthor/tmpl/saveOrderG.php");
		die();
	}
	
	function saveOrderS(){
		include_once(JPATH_ADMINISTRATOR . "/components/com_guru/views/gurudays/tmpl/saveOrderS.php");
		die();
	}
	
	function applyquizdetails(){
		$return = $this->_model->storequizdetails();
		$pid = JFactory::getApplication()->input->get("pid", "0");
		$user_id = JFactory::getApplication()->input->get("user_id", "0");
		$quiz_id = JFactory::getApplication()->input->get("quiz_id", "0");
		$action = JFactory::getApplication()->input->get("action", "");
		
		$url = JURI::root()."index.php?option=com_guru&view=guruauthor&task=quizdetails&layout=quizdetails&pid=".$pid."&userid=".$user_id."&quiz=".$quiz_id."&tmpl=component&action=".$action;
		if($return){
			$msg = JText::_("GURU_MODIF_OK");
			$this->setRedirect($url, $msg, "message");
		}
		else{
			$msg = JText::_("GURU_MEDIAAPPLYFAILED");
			$this->setRedirect($url, $msg, "message");
		}
	}
	
	function savequizdetails(){
		$return = $this->_model->storequizdetails();
		$pid = JFactory::getApplication()->input->get("pid", "0");
		$user_id = JFactory::getApplication()->input->get("user_id", "0");
		$quiz_id = JFactory::getApplication()->input->get("quiz_id", "0");
		$action = JFactory::getApplication()->input->get("action", "");
		
		$url = JURI::root()."index.php?option=com_guru&view=guruauthor&task=studentquizes&layout=studentquizes&pid=".intval($pid)."&userid=".intval($user_id)."&tmpl=component";
		
		if($action == "mark"){
			$url = JRoute::_("index.php?option=com_guru&view=guruauthor&task=mark&id=".intval($pid), true);
			echo '
				<script type="text/javascript" language="javascript">
					window.parent.location.href="'.$url.'";
				</script>';
			return;
		}
		
		if($return){
			$msg = JText::_("GURU_MODIF_OK");
			$this->setRedirect($url, $msg, "message");
		}
		else{
			$msg = JText::_("GURU_MEDIAAPPLYFAILED");
			$this->setRedirect($url, $msg, "message");
		}
	}
	
	function authorRegistration(){
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		if($user_id == 0){
			$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
			
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			
			//$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=authorprofile"."&Itemid=".$Itemid, false));
			//return true;

			echo '
				<form name="register" method="post" class="uk-form wk-form" style="visibility: hidden;">
                    <input type="hidden" name="option" value="com_guru">
                    <input type="hidden" name="controller" value="guruAuthor">
                    <input type="hidden" name="Itemid" value="'.$Itemid.'">
                    <input type="hidden" name="task" value="authorregister">
                    <input type="hidden" name="returnpage" value="authorprofile">
                    <input type="hidden" name="lesson_id" value="0">
                    <input type="hidden" name="cid" value="0">
                    <input type="submit" id="register-teacher-button" class="uk-button uk-button-primary wk-button wk-button-primary" value="Register as a teacher >>" />
                    <script> document.getElementById("register-teacher-button").click(); </script>
                 </form>
			';
			die();
		}
		else{
			$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
			
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruauthor"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			else{
				$user = JFactory::getUser();
				$user_id = $user->id;

            	$itemid_menu = $helper->getTeacherMenuItem(intval($user_id));

            	if(intval($itemid_menu) > 0){
                    $Itemid = intval($itemid_menu);
                }
            }
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorprofile&layout=authorprofile"."&Itemid=".$Itemid, false));
		}
	}
	
	function studentRegistration(){
		$user = JFactory::getUser();
		$user_id = $user->id;
		if($user_id == 0){
			$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
			
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin"."&Itemid=".$Itemid, false));
		}
		else{
			$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
			
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruprofile"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			else{
				$user = JFactory::getUser();
				$user_id = $user->id;

            	$itemid_menu = $helper->getTeacherMenuItem(intval($user_id));

            	if(intval($itemid_menu) > 0){
                    $Itemid = intval($itemid_menu);
                }
            }
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruProfile&task=edit"."&Itemid=".$Itemid, false));
		}
	}
	
	function exportCsv(){
		$db = JFactory::getDBO();
		$pid = JFactory::getApplication()->input->get("course", 0);
		$result = array();
		
		$user = JFactory::getUser();
		$sql = "select id from #__guru_program where author like '%|".intval($user->id)."|%' OR author='".intval($user->id)."'";
		$db->setQuery($sql);
		$courses_ids = $db->loadColumn();
		
		$sql = "select id, name from #__guru_program where author like '%|".intval($user->id)."|%' OR author='".intval($user->id)."'";
		$db->setQuery($sql);
		$courses_names = $db->loadAssocList("id");
		
		if(!isset($courses_ids) || count($courses_ids) <= 0){
			$courses_ids = array("0"=>"0");
		}
		
		if(intval($pid) != 0){
			$courses_ids = array(intval($pid));
		}
		
		$sql = "select distinct(userid) from #__guru_buy_courses where course_id in (".implode(",", $courses_ids).")";
		$db->setQuery($sql);
		$students_ids = $db->loadColumn();
		
		if(isset($students_ids) && count($students_ids) > 0){
			$sql = "select distinct(c.userid), A.courses, u.* from #__guru_buy_courses c, (select userid, GROUP_CONCAT(course_id SEPARATOR '-') as courses from #__guru_buy_courses where course_id in (".implode(",", $courses_ids).") group by userid) as A, #__users u, #__guru_customer cust where course_id in (".implode(",", $courses_ids).") and c.userid=A.userid and u.id=c.userid and cust.id=u.id";
			$db->setQuery($sql);
			$result = $db->loadAssocList();
		}
		
		$content = JText::_("GURU_NAME").",".JText::_("GURU_EMAIL").",".JText::_("GURU_PROGRAM")."\n";
		
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				$name = $value["name"];
				$email = $value["email"];
				$courses = $value["courses"];
				$student_courses = array();
				$courses = explode("-", $courses);
				
				if(isset($courses) && count($courses) > 0){
					foreach($courses as $key=>$value){
						$student_courses[] = $courses_names[$value]["name"];
					}
				}
				
				$content .= $name.",".$email.",".implode("; ", $student_courses)."\n";
			}
		}
		
		/*header("Content-Type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=ExportResult.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $content;
		exit();*/

		echo "\xEF\xBB\xBF";
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/x-msdownload; charset=UTF-8");
		header("Content-Disposition: attachment; filename=ExportResult.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $content;
		exit();
	}
	
	function exportPdf(){
		$db = JFactory::getDBO();
		
		if(file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."MPDF".DIRECTORY_SEPARATOR."mpdf.php")){
			// export MPDF
			
			error_reporting(0);
			ini_set('display_errors','Off');
			
			require (JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."MPDF".DIRECTORY_SEPARATOR."mpdf.php");
			$pdf = new mPDF('utf-8','A4-L', 0, "verdana");
			$pdf->AddPage('L');

			$table_result = '<style>table {border-collapse: collapse;} table, th, td {border: 1px solid black;}</style> <table><tr><td>'.JText::_("GURU_NAME").'</td><td>'.JText::_("GURU_EMAIL").'</td><td>'.JText::_("GURU_PROGRAM").'</td></tr>';

			$pid = JFactory::getApplication()->input->get("course", 0, "raw");
			$result = array();
			
			$user = JFactory::getUser();
			$sql = "select id from #__guru_program where author like '%|".intval($user->id)."|%' OR author='".intval($user->id)."'";
			$db->setQuery($sql);
			$courses_ids = $db->loadColumn();
			
			$sql = "select id, name from #__guru_program where author like '%|".intval($user->id)."|%' OR author='".intval($user->id)."'";
			$db->setQuery($sql);
			$courses_names = $db->loadAssocList("id");
			
			if(!isset($courses_ids) || count($courses_ids) <= 0){
				$courses_ids = array("0"=>"0");
			}
			
			if(intval($pid) != 0){
				$courses_ids = array(intval($pid));
			}
			
			$sql = "select distinct(userid) from #__guru_buy_courses where course_id in (".implode(",", $courses_ids).")";
			$db->setQuery($sql);
			$students_ids = $db->loadColumn();
			
			if(isset($students_ids) && count($students_ids) > 0){
				$sql = "select distinct(c.userid), A.courses, u.* from #__guru_buy_courses c, (select userid, GROUP_CONCAT(course_id SEPARATOR '-') as courses from #__guru_buy_courses where course_id in (".implode(",", $courses_ids).") group by userid) as A, #__users u, #__guru_customer cust where course_id in (".implode(",", $courses_ids).") and c.userid=A.userid and u.id=c.userid and cust.id=u.id";
				$db->setQuery($sql);
				$result = $db->loadAssocList();
			}

			$new_id = 0;
			$nr = 1;
			
			for($i = 0; $i < count($result); $i++){
				$name = $result[$i]["name"];
				$email = $result[$i]["email"];
				$courses = $result[$i]["courses"];
				$student_courses = array();
				$courses = explode("-", $courses);
				
				if(isset($courses) && count($courses) > 0){
					foreach($courses as $key=>$value){
						$student_courses[] = $courses_names[$value]["name"];
					}
				}
				
				$table_result .= '<tr><td>'.$name.'</td><td>'.$email.'</td><td>'.implode(", ", $student_courses).'</td></tr>';
			}

			$table_result .= '</table>';

			$pdf->SetXY(0, 20);
			$pdf->SetDisplayMode('fullpage');  
			$pdf->WriteHTML($table_result);
			$pdf->Output('ExportResult.pdf', 'D');
			exit;
		}
		else{
			// export FPDF
			
			while (ob_get_level())
			ob_end_clean();
			header("Content-Encoding: None", true);
			
			require(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'tfpdf.php');
			include_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'font'.DIRECTORY_SEPARATOR.'helvetica.php');
			$quiz_id =  intval(JFactory::getApplication()->input->get("id", ""));
			//create a FPDF object
			$pdf=new tFPDF();

			//set font for the entire document
			$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
			$pdf->SetFont('DejaVu','',14);
			$pdf->SetTextColor(50,60,100);
			
			//set up a page
			$pdf->AddPage();
			@$pdf->SetDisplayMode(real,'default');
			
			$pdf->SetXY(10,5);
			$pdf->SetFontSize(8);
			
			$z = 25;
			
			$t = $z + 10;
			$pdf->SetXY(10,$t);
			$pdf->SetFontSize(7);
			$pdf->Cell(25, 10, JText::_("GURU_NAME"), 'LRTB', '', 'L', 0);
			$pdf->Cell(39, 10, JText::_("GURU_EMAIL"), 'LRTB', '', 'L', 0);
			$pdf->Cell(100, 10, JText::_("GURU_PROGRAM"), 'LRTB', '', 'L', 0);
			$pdf->Ln();
			
			$pid = JFactory::getApplication()->input->get("course", 0);
			$result = array();
			
			$user = JFactory::getUser();
			$sql = "select id from #__guru_program where author like '%|".intval($user->id)."|%' OR author='".intval($user->id)."'";
			$db->setQuery($sql);
			$courses_ids = $db->loadColumn();
			
			$sql = "select id, name from #__guru_program where author like '%|".intval($user->id)."|%' OR author='".intval($user->id)."'";
			$db->setQuery($sql);
			$courses_names = $db->loadAssocList("id");
			
			if(!isset($courses_ids) || count($courses_ids) <= 0){
				$courses_ids = array("0"=>"0");
			}
			
			if(intval($pid) != 0){
				$courses_ids = array(intval($pid));
			}
			
			$sql = "select distinct(userid) from #__guru_buy_courses where course_id in (".implode(",", $courses_ids).")";
			$db->setQuery($sql);
			$students_ids = $db->loadColumn();
			
			if(isset($students_ids) && count($students_ids) > 0){
				$sql = "select distinct(c.userid), A.courses, u.* from #__guru_buy_courses c, (select userid, GROUP_CONCAT(course_id SEPARATOR '-') as courses from #__guru_buy_courses where course_id in (".implode(",", $courses_ids).") group by userid) as A, #__users u, #__guru_customer cust where course_id in (".implode(",", $courses_ids).") and c.userid=A.userid and u.id=c.userid and cust.id=u.id";
				$db->setQuery($sql);
				$result = $db->loadAssocList();
			}

			$new_id = 0;
			$nr = 1;
			
			for($i = 0; $i < count($result); $i++){
				$name = $result[$i]["name"];
				$email = $result[$i]["email"];
				$courses = $result[$i]["courses"];
				$student_courses = array();
				$courses = explode("-", $courses);
				
				if(isset($courses) && count($courses) > 0){
					foreach($courses as $key=>$value){
						$student_courses[] = $courses_names[$value]["name"];
					}
				}
				
				$pdf->SetFontSize(7);
				$pdf->Cell(25, 10, $name, 'LRTB', '', 'L', 0);
				$pdf->Cell(39, 10, $email, 'LRTB', '', 'L', 0);
				$pdf->Cell(100, 10, implode(", ", $student_courses), 'LRTB', '', 'L', 0);
				$pdf->Ln();
			}
			//Output the document
			$pdf->Output('ExportResult.pdf', 'I');
		}
	}
	
	function saveMark(){
		$model = $this->getModel("guruAuthor");
		$model->saveMark();
		die();
	}
	
	function ajaxSearchMedia(){
		$model = $this->getModel("guruAuthor");
		$model->ajaxSearchMedia();
		die();
	}

	function studentProgress(){
		JFactory::getApplication()->input->set ("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("student_progress");
		$view->setModel($this->_model, true);
		$view->studentProgress();
	}

	function projectForm(){
		$user = JFactory::getUser();
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user->id);
        
		if($res==false){
        	$this->setRedirect(JRoute::_("index.php?option=com_guru", false));
        }

		$view = $this->getView('guruAuthor','html');
		$view->setLayout('projectform');
        $view->projectForm();
	}

	function scormForm(){
		$user = JFactory::getUser();
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user->id);
        
		if($res==false){
        	$this->setRedirect(JRoute::_("index.php?option=com_guru", false));
        }

		$view = $this->getView('guruAuthor','html');
		$view->setLayout('scormform');
        $view->scormForm();
	}

	function projects(){
		$user = JFactory::getUser();
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user->id);
		
		if($res==false){
        	$this->setRedirect(JRoute::_("index.php?option=com_guru", false));
        }

		$view = $this->getView('guruAuthor','html');
		$view->setLayout('projects');
		$view->setModel($this->_model, true);
        $view->projects();
	}

	function scorm(){
		$user = JFactory::getUser();
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user->id);
		
		if($res==false){
        	$this->setRedirect(JRoute::_("index.php?option=com_guru", false));
        }

		$view = $this->getView('guruAuthor','html');
		$view->setLayout('scorm');
		$view->setModel($this->_model, true);
        $view->scorm();
	}

	function projectResult(){
		$user = JFactory::getUser();
		$model = $this->getModel("guruAuthor");
        $res = $model->checkAuthorProfile($user->id);
		
		if($res==false){
        	$this->setRedirect(JRoute::_("index.php?option=com_guru", false));
        }

		$view = $this->getView('guruAuthor','html');
		$view->setLayout('projectresults');
        $view->projectResult();
	}

	function copyquestion(){
		$qid = JFactory::getApplication()->input->get('qid', '0', "raw");
		$model = $this->getModel("guruAuthor");
		
		if(!$model->copyquestion()){
			$msg = JText::_('GURU_CAN_NOT_COPY_QUESTION');
		}
		else{
		 	$msg = JText::_('GURU_QUESTION_COPIED');
		}

		$link = JRoute::_("index.php?option=com_guru&view=guruauthor&task=editQuizFE&cid=".$qid."&v=0&e=1");
		$this->setRedirect($link, $msg);
	}

	function getTeacherRedirectUrl(){
		$db = JFactory::getDbo();

		$sql = "select `st_authorpage` from #__guru_config";
		$db->setQuery($sql);
		$db->query();
		$st_authorpage = $db->loadColumn();
		$st_authorpage = @$st_authorpage["0"];
		$st_authorpage = json_decode($st_authorpage, true);

		if(isset($st_authorpage["teacher_redirect"]) && trim($st_authorpage["teacher_redirect"]) != ""){
			return trim($st_authorpage["teacher_redirect"]);
		}
		else{
			return "";
		}
	}

	function loadingStudentCertificates(){
		$db = JFactory::getDbo();
		$student_id = JFactory::getApplication()->input->get('student_id', '0', "raw");
		$student_certificates = $this->_model->getStudentCertificates($student_id, "sum");

		if(intval($student_certificates) == 0){
		    echo "0";
		}
		else{
			$link_modal_cert = JURI::root()."index.php?option=com_guru&view=guruauthor&layout=studentdetailscert&userid=".intval($student_id)."&tmpl=component";

			echo '<a onclick="javascript:openMyModal(0, 0, \''.$link_modal_cert.'\'); return false;" href="#">'.intval($student_certificates).'</a>';
		}

		die();
	}
};

?>
