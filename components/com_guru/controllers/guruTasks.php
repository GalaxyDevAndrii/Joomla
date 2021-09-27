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

//JHTML::_('behavior.modal', 'a.modal');
JHTML::_('behavior.framework');

$ajax = JFactory::getApplication()->input->get("ajax", 0);
$task = JFactory::getApplication()->input->get("task", "", "raw");

if ($ajax == 0 && $task != "download") {
?>
	<script>
        function editgurucomment1(comid){
            var gurutext = document.getElementById('gurupostcomment'+comid).innerHTML;
            document.getElementById('gurupostcomment'+comid).style.display = "none";
            document.getElementById("message1"+comid).value = gurutext;
            document.getElementById('message1'+comid).style.display = "block";
            if(document.getElementById('delete'+comid)){
                document.getElementById('delete'+comid).style.display = "none";
            }
            if(document.getElementById('edit'+comid)){
                document.getElementById('edit'+comid).style.display = "none";
            }
            document.getElementById('save'+comid).style.display = "block";
        }
    </script>
<?php
}

jimport ('joomla.application.component.controller');

class guruControllerguruTasks extends guruController {
	var $model = null;
		
	function __construct () {
		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "view");
		$this->registerTask ("unpublish", "publish");	
		$this->registerTask ("exercise","exerciseFile");
		$this->registerTask ("saveInDb", "saveInDb");
		$this->registerTask ("saveInDbQuiz", "saveInDbQuiz");
		$this->registerTask ("saveInDbaseHowMany", "saveInDbaseHowMany");
		$this->registerTask ("viewcertificate", "viewcertificate");
		$this->registerTask ("savecertificatepdf", "savecertificatepdf");
		$this->registerTask ("lessonmessage", "lessonmessage");
		$this->registerTask ("deletecom", "deletecom");
		$this->registerTask ("editgurucomment", "editgurucomment");
		$this->registerTask ("editformgurupost", "editformgurupost");
		$this->registerTask ("calculatecertificate", "calculatecertificate");
		$this->registerTask ("showCertificateFr", "showCertificateFr");
		$this->registerTask ("quizz_fe_submit", "save_quiz_result");
		$this->registerTask ("set_viewed", "setViewed");
		$this->registerTask ("get_lessons", "getLessons");
		$this->registerTask ("get_comments", "getComments");
		$this->registerTask ("insert_comment", "insertComment");
		$this->registerTask ("edit_comment", "editComment");
		$this->registerTask ("delete_comment", "deleteComment");
		$this->registerTask ("get_lesson_description", "getLessonDescription");
		$this->registerTask ("saveDateTime", "saveDateTime");
		$this->registerTask ("saveDateTimeLesson", "saveDateTimeLesson");
		$this->registerTask ("set_lesson_viewed", "setLessonViewed");
		$this->registerTask ("set_lesson_notviewed", "setLessonNotViewed");
		$this->registerTask ("check_course_completed", "checkCourseCompleted");
		$this->registerTask ("scorm", "scorm");
		$this->registerTask ("download", "download");

		$this->_model = $this->getModel("guruTask");
		
	}

	function download(){
		$user = JFactory::getUser();
		$db =  JFactory::getDBO();

		if(intval($user->id) > 0){
			$media = intval(JFactory::getApplication()->input->get("media", "0", "raw"));
	    	$lesson = intval(JFactory::getApplication()->input->get("lesson", "0", "raw"));
	    	$module = intval(JFactory::getApplication()->input->get("module", "0", "raw"));
	    	$course_id = intval(JFactory::getApplication()->input->get("course", "0", "raw"));

	    	if(intval($course_id) == 0){
				$sql = "select `pid` from #__guru_days where `id`=".intval($module);
				$db->setQuery($sql);
				$course = $db->loadColumn();
				$course_id = @$course["0"];
			}

			$sql = "select count(*) from #__guru_buy_courses bc, #__guru_order o, #__guru_customer c where bc.userid=".intval($user->id)." and o.id = bc.order_id and bc.course_id=".intval($course_id)." and o.userid=".intval($user->id)." and c.id=".intval($user->id)." and o.`status`='Paid' and (bc.`expired_date`='0000-00-00 00:00:00' OR bc.`expired_date` >= now())";
		    $db->setQuery($sql);
		    $result = $db->loadColumn();
		    $result = @$result["0"];

			if(intval($result) > 0){
				$sql = "SELECT * FROM #__guru_config WHERE id = '1' ";
				$db->setQuery($sql);
				if (!$db->execute()) {
					echo $db->stderr();
					return;
				}
				
				$configs = $db->loadObject();

				$sql = "SELECT * FROM #__guru_media WHERE `id` = ".intval($media);
				$db->setQuery($sql);
				$media = $db->loadObject();	
				@$media->code = stripslashes(@$media->code);

				$filepath = "";
				$filename = "";

				if($media->source == 'local' && (substr($media->local,(strlen($media->local)-3),3) == 'zip' || substr($media->local,(strlen($media->local)-3),3) == 'exe')){
					$filepath = JPATH_SITE . DIRECTORY_SEPARATOR . $configs->filesin . DIRECTORY_SEPARATOR . $media->local;
					$filename = $media->local;
				}
				elseif($media->source == 'local'){
					$filepath = JPATH_SITE . DIRECTORY_SEPARATOR . $configs->docsin . DIRECTORY_SEPARATOR . $media->local;
					$filename = $media->local;
				}

				if(trim($filepath) != ""){
					$fsize = filesize($filepath);

					if(!file_exists($filepath)){
						header("HTTP/1.0 404 Not Found");
						exit;
					}

					$ftime = date("D, d M Y H:i:s T", filemtime($filepath));
					$fd = @fopen($filepath, "rb");
					
					if(!$fd){
						header( "HTTP/1.0 403 Forbidden" );
						exit;
					}

					// inscrices memory size if need bug in ticket OBQ-977176
					$memoryNeeded = round($fsize * 2);
					
					if(function_exists( 'memory_get_usage' ) && (memory_get_usage() + $memoryNeeded) > (int) ini_get( 'memory_limit' ) * pow( 1024, 2 ) ) {
						$mem = (int) ini_get( 'memory_limit' ) + ceil( ((memory_get_usage() + $memoryNeeded) - (int) ini_get( 'memory_limit' ) * pow( 1024, 2 )) / pow( 1024, 2 ) ) . 'M';
						ini_set( 'memory_limit', $mem );
					}

					header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
					header( "Last-Modified: $ftime" );
					header( "Content-Length: $fsize" );
					header( "Content-type: application/octet-stream" );
					readfile( $filepath );
					exit;
				}
				else{
					die();
				}
			}
			else{
				die();
			}
		}
		else{
			die();
		}
	}

	function listTasks() {
		$view = $this->getView("guruTasks", "html"); 
		$view->setModel($this->_model, true);
		$view->display();
	}
	
	function view () {
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("view");
		$view->setModel($this->_model, "getTask");	
		$view->show();
	}	
	
	function preview(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("preview");
		$view->setModel($this->_model, "getTask");	
		$view->preview();
	}
	
	function viewguest () {
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("viewguest");
		$view->setModel($this->_model, true);	
		$view->show();
	}		

	function edit () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1); 
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
	
		//$model =& $this->getModel("adagencyConfig");
		//$view->setModel($model);

		$view->editForm();
	}

	function addmedia () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1); 
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("addmedia");
		$view->setModel($this->_model, true);
		$view->addmedia();

	}
	
	function addmainmedia () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1); 
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("addmainmedia");
		$view->setModel($this->_model, true);
		$view->addmedia();

	}
	
	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('AD_CMP_SAVED');
		} else {
			$msg = JText::_('AD_CMP_NOT_SAVED');
		}
		$link = "index.php?option=com_guru&view=guruTasks";
		$this->setRedirect($link, $msg);

	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('GURU_TASKS_DELFAILED');
		} else {
		 	$msg = JText::_('GURU_TASKS_DEL');
		}
		
		$link = "index.php?option=com_guru&view=guruTasks";
		$this->setRedirect($link, $msg);
		
	}
	
	function del () { 
		$data_get = JFactory::getApplication()->input->get->getArray();
		
		$tid = intval($data_get['tid']); 
		$cid = intval($data_get['cid'][0]);
		if (!$this->_model->delmedia($tid,$cid)) {
			$msg = JText::_('GURU_TASKS_DELFAILED');
		} else {
		 	$msg = JText::_('GURU_TASKS_DEL');
		}
		
		$link = "index.php?option=com_guru&view=guruTasks&task=edit&cid[]=".$tid;
		$this->setRedirect($link, $msg);
		
	}

	function cancel () {
	 	$msg = JText::_('AD_OP_CANCELED');
		$link = "index.php?option=com_guru&view=guruTasks";
		$this->setRedirect($link, $msg);
	}
	
	
	function approve () { 
		$res = $this->_model->publish();

		if (!$res) {
			$msg = JText::_('AD_CMP_UNERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('AD_CMP_UNNAP');
		} elseif ($res == 1) {
			$msg = JText::_('AD_CMP_APPV');
		} else {
                 	$msg = JText::_('AD_CMP_UNERROR');
		}
		
		$link = "index.php?option=com_guru&view=guruTasks";
		$this->setRedirect($link, $msg);


	}
		function unapprove () {
			$res = $this->_model->publish();
	
			if (!$res) {
				$msg = JText::_('AD_CMP_ERROR');
			} elseif ($res == -1) {
			 	$msg = JText::_('AD_CMP_UNNAP');
			} elseif ($res == 1) {
				$msg = JText::_('AD_CMP_APPV');
			} else {
	                 	$msg = JText::_('AD_CMP_ERROR');
			}
			
			$link = "index.php?option=com_guru&view=guruTasks";
		$this->setRedirect($link, $msg);
	
	
		}
		
		function publish () { 
			$res = $this->_model->publish();

			if (!$res) { 
				$msg = JText::_('AD_CMP_UNERROR');
			} elseif ($res == -1) {
			 	$msg = JText::_('AD_CMP_UNNAP');
			} elseif ($res == 1) {
				$msg = JText::_('AD_CMP_APPV');
			} else {
	                 	$msg = JText::_('AD_CMP_UNERROR');
			}
			
			$link = "index.php?option=com_guru&view=guruTasks";
		$this->setRedirect($link, $msg);

		}
		
		function unpublish () {
			$res = $this->_model->publish();
	
			if (!$res) {
				$msg = JText::_('AD_CMP_ERROR');
			} elseif ($res == -1) {
			 	$msg = JText::_('AD_CMP_UNNAP');
			} elseif ($res == 1) {
				$msg = JText::_('AD_CMP_APPV');
			} else {
	                 	$msg = JText::_('AD_CMP_ERROR');
			}
			
			$link = "index.php?option=com_guru&view=guruTasks";
		$this->setRedirect($link, $msg);
		}
		
		function pause () {
		if (!$this->_model->pause()) {
			$msg = JText::_('AD_CMP_CANTPAUSE');
		} else {
		 	$msg = JText::_('AD_CMP_PAUSED');
		}
		
		$link = "index.php?option=com_guru&view=guruTasks";
		$this->setRedirect($link, $msg);
		
		}
		
		function unpause () {
			if (!$this->_model->unpause()) {
				$msg = JText::_('AD_CMP_CANTUNPAUSE');
			} else {
			 	$msg = JText::_('AD_CMP_UNPAUSED');
			}
			
			$link = "index.php?option=com_guru&view=guruTasks";
		$this->setRedirect($link, $msg);
			
		}
		
	function savemedia () {
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		$insertit = intval($data_post['idmedia']);
		$taskid = intval($data_post['idtask']);
		$mainmedia = intval($data_post['mainmedia']);
		$this->_model->addmedia($insertit, $taskid, $mainmedia);
	}
	
	function saveInDb(){
		$database = JFactory::getDBO();	
		$user = JFactory::getUser();
		$user_id = $user->id;
		$saved_quiz_id = JFactory::getApplication()->input->get("saved_quiz_id");
		$quiz_id =  JFactory::getApplication()->input->get("quiz_id");
		$ans_givedbyuser = JFactory::getApplication()->input->get("ans_gived");
		$qstion_id = JFactory::getApplication()->input->get("qstion_id");	
		$time_quiz_taken = JFactory::getApplication()->input->get("time_quiz_taken");	
		$questions_ids_list = JFactory::getApplication()->input->get("questions_ids_list");

		
		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$database->setQuery($sql);
		$isfinal=$database->loadColumn();
		$isfinal = $isfinal[0];
		
		
		$sql = "SELECT show_nb_quiz_select_up from #__guru_quiz where id=".$quiz_id;
		$database->setQuery($sql);
		$show_nb_quiz_select_up=$database->loadResult();
		
		if($isfinal == 0){
			$sql = "SELECT id FROM #__guru_questions_v3 WHERE qid=".intval($quiz_id)." ORDER BY reorder";
		}
		else{
			$sql = "SELECT 	quizzes_ids FROM #__guru_quizzes_final WHERE qid=".$quiz_id;
			$database->setQuery($sql);
			$result=$database->loadResult();	
			$result_qids = explode(",",trim($result,","));
			
			if(count($result_qids) == 0 || $result_qids["0"] == ""){
				$result_qids["0"] = 0;
			}
		
			$sql  = "SELECT id FROM #__guru_questions_v3 WHERE qid IN (".implode(",", $result_qids).") ORDER BY reorder";
		
		}
		$database->setQuery($sql);
		$quiz_question_id= $database->loadObjectList();
		$qstion_id = $qstion_id - 1;
		
		$quiz_question_id = $quiz_question_id[$qstion_id]->id;	
		
		if($show_nb_quiz_select_up == 0){
			$quiz_question_id = explode(",", trim($questions_ids_list));
			$quiz_question_id  = $quiz_question_id [$qstion_id];
		}
		//$sql = 'DELETE FROM #__guru_quiz_question_taken_v3 WHERE show_result_quiz_id='.$saved_quiz_id.'';
		//$database->setQuery($sql);
		//$database->execute();
		
		$sql = "INSERT INTO #__guru_quiz_question_taken_v3 (user_id, show_result_quiz_id, answers_gived,question_id, question_order_no) VALUES ('".$user_id."', '".$saved_quiz_id."', '".$ans_givedbyuser."', '".$quiz_question_id."', '".($qstion_id +1)."')";
		$database->setQuery($sql);
		$database->execute();
		
		/*if($time_quiz_taken > 0 && $time_quiz_taken != "" && $time_quiz_taken < 11){
			$sql = "UPDATE #__guru_quiz_taken_v3 set time_quiz_taken_per_user = '".($time_quiz_taken-1)."' WHERE quiz_id=".intval($quiz_id)." AND user_id=".intval($user_id);
			$database->setQuery($sql);
			$database->execute();
		}*/
	}
	function saveInDbQuiz(){
		$database = JFactory::getDBO();	
		$user = JFactory::getUser();
		$user_id = $user->id;
		$quiz_id = JFactory::getApplication()->input->get("quiz_id");
		$how_many_right_answers = JFactory::getApplication()->input->get("howmrans");
		$number_of_questions = JFactory::getApplication()->input->get("numbofquestions");
		$course_id = JFactory::getApplication()->input->get("course_id");	
		$score_quiz = $how_many_right_answers."|".$number_of_questions;
		//$date = date('Y-m-d h:i:s');

		$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
		$jnow = new JDate('now');
		$jnow->setTimezone($timezone);
		$date = $jnow->toSQL(true);

		$sql1 = "SELECT time_quiz_taken FROM #__guru_quiz WHERE id=".$quiz_id;
		$database->setQuery($sql1);
		$resultt = $database->loadColumn();
		$resultt = $resultt[0];
		
		$sql2 = "SELECT count(user_id) FROM #__guru_quiz_taken_v3 WHERE user_id=".$user_id." and quiz_id=".$quiz_id;
		$database->setQuery($sql2);
		$resultu = $database->loadColumn();
		$iterator = 1;
		if($resultt < 11){
			if(intval($resultu["0"]) != 0){
				$iterator = intval($resultu["0"]) + 1;
			}
		}
		else{
			$iterator = 11;
		}
		
		$sql = 'INSERT INTO  #__guru_quiz_taken_v3 (user_id, quiz_id, score_quiz, date_taken_quiz, pid,time_quiz_taken_per_user) VALUES ('.$user_id.', '.$quiz_id.', "'.$score_quiz.'","'.$date.'", '.$course_id.', '.$iterator.')';
		$database->setQuery($sql);
		$database->execute();
		
		$sql2 = "SELECT max(id) FROM #__guru_quiz_taken_v3";
		$database->setQuery($sql2);
		$x = $database->loadColumn();
		$x = $x["0"];
		
		echo intval(trim($x));
		die();
	}
	function saveInDbaseHowMany(){
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;	
		$quiz_id = JFactory::getApplication()->input->get("quiz_id");
		$how_many_right_answers = JFactory::getApplication()->input->get("howmanyans");
		$number_of_questions = JFactory::getApplication()->input->get("numbofquestions");
		$saved_quiz_id = JFactory::getApplication()->input->get("saved_quiz_id");		
		$score_quiz = $how_many_right_answers."|".$number_of_questions;	
		$sql = 'UPDATE #__guru_quiz_taken_v3 set score_quiz= "'.$score_quiz.'" WHERE quiz_id='.$quiz_id.' and id='.$saved_quiz_id;
		$database->setQuery($sql);
		if($database->execute()){
			echo "true";
		}
		else{
			echo "false";
		}
		die();
	}	
	function exerciseFile(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("exercise");
		$view->setModel($this->_model, "getExercise");	
		$view->showExercise();
	}
	function viewcertificate(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("certificatefront");
		$view->viewcertificate();
	}
	
	function savecertificatepdf(){
		$datac = JFactory::getApplication()->input->post->getArray();

		$db = JFactory::getDBO();
		$config = JFactory::getConfig();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$sql = "SELECT name from #__guru_program WHERE id =".$datac['ci'];
		$db->setQuery($sql);
		$result = $db->loadResult();
		
		$imagename = "SELECT * FROM #__guru_certificates WHERE id=1";
		$db->setQuery($imagename);
		$imagename = $db->loadAssocList();
		
		$authorname = "SELECT name from #__users where id IN (SELECT author_id FROM #__guru_mycertificates WHERE user_id = ".intval($user_id)." AND course_id =".$datac['ci']." )";
		$db->setQuery($authorname);
		$authorname = $db->loadResult();
		
		$date_completed = "SELECT datecertificate FROM #__guru_mycertificates WHERE user_id=".intval($user_id)." AND course_id=".intval($datac['ci']);
		$db->setQuery($date_completed);
		$date_completed = $db->loadResult();
		
		$format = "SELECT datetype FROM #__guru_config WHERE id=1";
		$db->setQuery($format);
		$format = $db->loadResult();
		$date_completed = date($format, strtotime($date_completed));
		
		$completiondate = $date_completed;
		$completiondate = date("Y-m-d", strtotime($completiondate));
		$sitename = $config->get('sitename');
		$coursename = $result;
		$site_url = JURI::root();
		$certificateid = $datac['id']; 
		
		$coursemsg = "SELECT certificate_course_msg FROM #__guru_program WHERE id=".intval($datac['ci']);
		$db->setQuery($coursemsg);
		$coursemsg = $db->loadResult();
		
		$scores_avg_quizzes =  guruModelguruTask::getAvgScoresQ($user_id,$course_id);

 		$avg_quizzes_cert = "SELECT avg_certc FROM #__guru_program WHERE id=".intval($datac['ci']);
		$db->setQuery($avg_quizzes_cert);
		$avg_quizzes_cert = $db->loadResult();

		$sql = "SELECT id_final_exam FROM #__guru_program WHERE id=".intval($datac['ci']);
		$db->setQuery($sql);
		$result = $db->loadResult();

		$sql = "SELECT hasquiz from #__guru_program WHERE id=".intval($datac['ci']);
		$db->setQuery($sql);
		$resulthasq = $db->loadResult();

		$sql = "SELECT max_score FROM #__guru_quiz WHERE id=".intval($result);
		$db->setQuery($sql);
		$result_maxs = $db->loadResult();

		$sql = "SELECT id,  time_quiz_taken_per_user  FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($result)." and pid=".intval($datac['ci'])." ORDER BY id DESC LIMIT 0,1";
		$db->setQuery($sql);
		$result_q = $db->loadObject();

		$first= explode("|", @$result_q->score_quiz);

		@$res = intval(($first[0]/$first[1])*100);

		if($resulthasq == 0 && $scores_avg_quizzes == ""){
			$avg_certc1 = "N/A";
		}
		elseif($resulthasq != 0 && $scores_avg_quizzes == ""){
			$avg_certc1 = "N/A";
		}
		elseif($resulthasq != 0 && isset($scores_avg_quizzes)){
		if($scores_avg_quizzes >= intval($avg_quizzes_cert)){
			$avg_certc1 =  $scores_avg_quizzes.'%'; 
		}
		else{
			$avg_certc1 = $scores_avg_quizzes.'%';
		}
	}

	if($result !=0 && $res !="" ){
		if( $res >= $result_maxs){
			$avg_certc = $res.'%';
		}
		elseif($res < $result_maxs){
			$avg_certc = $res.'%';
		}
	}
	elseif(($result !=0 && $result !="")){
		$avg_certc = "N/A";
	}
	elseif($result ==0 || $result ==""){
		$avg_certc = "N/A";
	}
		
		
		$firstnamelastname = "SELECT firstname, lastname, company FROM #__guru_customer WHERE id=".intval($user_id);
		$db->setQuery($firstnamelastname);
		$firstnamelastname = $db->loadAssocList();
		
		$certificate_url = JUri::base()."index.php?option=com_guru&view=guruOrders&task=printcertificate&opt=".$certificateid."&cn=".$coursename."&an=".$authorname."&cd=".$completiondate."&id=".$certificateid."&ci=".$datac['ci'];
		$certificate_url = str_replace(" ", "%20", $certificate_url);

		$certificate_url = '<a href="'.$certificate_url.'" target="_blank">'.JText::_("GURU_URL_CERTIFICATE").'</a>';
		
		$imagename[0]["templates1"]  = str_replace("[SITENAME]", $sitename, $imagename[0]["templates1"]);
		$imagename[0]["templates1"]  = str_replace("[STUDENT_FIRST_NAME]", $firstnamelastname[0]["firstname"], $imagename[0]["templates1"]);
		$imagename[0]["templates1"]  = str_replace("[STUDENT_LAST_NAME]", $firstnamelastname[0]["lastname"], $imagename[0]["templates1"]);
		$imagename[0]["templates1"]  = str_replace("[SITEURL]", $site_url, $imagename[0]["templates1"]);
		$imagename[0]["templates1"]  = str_replace("[CERTIFICATE_ID]", $certificateid, $imagename[0]["templates1"]);
		$imagename[0]["templates1"]  = str_replace("[COMPLETION_DATE]", $completiondate, $imagename[0]["templates1"]);
		$imagename[0]["templates1"]  = str_replace("[COURSE_NAME]", $coursename, $imagename[0]["templates1"]);
		$imagename[0]["templates1"]  = str_replace("[AUTHOR_NAME]", $authorname, $imagename[0]["templates1"]);
		$imagename[0]["templates1"]  = str_replace("[CERT_URL]", $certificate_url, $imagename[0]["templates1"]);
		$imagename[0]["templates1"]  = str_replace("[CERTIFICATE_URL]", $certificate_url, $imagename[0]["templates1"]);
		$imagename[0]["templates1"]  = str_replace("[COURSE_MSG]", $coursemsg, $imagename[0]["templates1"]);
		$imagename[0]["templates1"]  = str_replace("[COURSE_AVG_SCORE]", $avg_certc1, $imagename[0]["templates1"]);
        $imagename[0]["templates1"]  = str_replace("[COURSE_FINAL_SCORE]", $avg_certc, $imagename[0]["templates1"]);
        $imagename[0]["templates1"]  = str_replace("[STUDENT_COMPANY]", @$firstnamelastname[0]["company"], $imagename[0]["templates1"]);


		
		$datac["content"] = $imagename[0]["templates1"];

		while (ob_get_level())
		ob_end_clean();
		header("Content-Encoding: None", true);
		
		if(strlen($datac["color"]) == 3) {
		  $r = hexdec(substr($datac["color"],0,1).substr($datac["color"],0,1));
		  $g = hexdec(substr($datac["color"],1,1).substr($datac["color"],1,1));
		  $b = hexdec(substr($datac["color"],2,1).substr($datac["color"],2,1));
	   } else {
		  $r = hexdec(substr($datac["color"],0,2));
		  $g = hexdec(substr($datac["color"],2,2));
		  $b = hexdec(substr($datac["color"],4,2));
	   }
	   	
		$datac["bgcolor"] = explode(":",$datac["bgcolor"] );
		$datac["bgcolor"][1]=str_replace("#", "", $datac["bgcolor"][1]);
		
		if(strlen($datac["bgcolor"][1] ) == 3) {
		  $rg = hexdec(substr($datac["bgcolor"][1],0,1).substr($datac["bgcolor"][1],0,1));
		  $gg = hexdec(substr($datac["bgcolor"][1],1,1).substr($datac["bgcolor"][1],1,1));
		  $bg = hexdec(substr($datac["bgcolor"][1],2,1).substr($datac["bgcolor"][1],2,1));
	   } else {
		  $rg = hexdec(substr($datac["bgcolor"][1],0,2));
		  $gg = hexdec(substr($datac["bgcolor"][1],2,2));
		  $bg = hexdec(substr($datac["bgcolor"][1],4,2));
	   }
		
		$db = JFactory::getDbo();
		$sql = "SELECT `imagesin` FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadResult();
		$certificate_path = JUri::base().$res."/certificates/";
		
		if($imagename[0]["library_pdf"] == 0){
			require (JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."fpdf.php");
			
			$pdf = new PDF('L', 'mm', 'A5');
	
			$pdf->SetFont($datac["font"],'',12);
			$pdf->SetTextColor($r,$g,$b);
			
			//set up a page
			$pdf->AddPage();
	
			if($datac["image"] !=""){
				$pdf->Image($certificate_path.$datac["image"],-4,-1,210, 150);
				//$pdf->Cell(0,75,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C');
	
			}
			else{
				$pdf->SetFillColor($rg,$gg,$bg);
				//$pdf->Cell(0,115,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C',true);
	
			}
			$pdf->Ln(20);
			$pdf->SetXY(100,50);
			$pdf->WriteHTML(iconv('UTF-8', 'ISO-8859-1', $imagename[0]["templates1"]),true);
			$pdf->Output('certificate'.$certificateid.'.pdf','D'); 
		}
		else{
			require (JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."MPDF".DIRECTORY_SEPARATOR."mpdf.php");
			$pdf = new mPDF('utf-8','A4-L');
			$imagename[0]["templates1"] = '<style> body { font-family:"'.strtolower ($datac["font"]).'" ; color: rgb('.$r.', '.$g.', '.$b.'); }</style>'.$imagename[0]["templates1"];
			
			
			//set up a page
			$pdf->AddPage('L');
	
			if($datac["image"] !=""){
				$pdf->Image($certificate_path.$datac["image"],0,0,298, 210, 'jpg', '', true, false);
				//$pdf->Cell(0,75,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C');
				
	
			}
			else{
				$pdf->SetFillColor($rg,$gg,$bg);
				//$pdf->Cell(0,115,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C',true);
	
			}
			//$pdf->Ln(20);
			$pdf->SetXY(100,50);
			$pdf->SetDisplayMode('fullpage');  
			$pdf->WriteHTML($imagename[0]["templates1"]);
			$pdf->Output('certificate'.$certificateid.'.pdf','D'); 
			exit;
		}
	}
	
	function lessonmessage(){
		$datamessage['lessonid'] = JFactory::getApplication()->input->get("lessonid", "");
		$datamessage['message'] = urldecode(JFactory::getApplication()->input->get("message", ""));
		
		$db = JFactory::getDBO();
		$config = JFactory::getConfig();
		$user = JFactory::getUser();
		
		$sql = "select `alias` from #__guru_task where `id`=".intval($datamessage['lessonid']);
		$db->setQuery($sql);
		$lesson_alias = $db->loadResult();

		$sql = "SELECT max(thread) from #__kunena_messages";
		$db->setQuery($sql);
		$threadid = $db->loadResult();
		
		$sql = "select allow_edit from #__guru_kunena_forum where id=1";
		$db->setQuery($sql);
		$allow_edit = $db->loadResult();
		
		$sql = "select allow_delete  from #__guru_kunena_forum where id=1";
		$db->setQuery($sql);
		$allow_delete = $db->loadResult();
		
		$sql = "SELECT count(id) FROM  #__guru_task_kunenacomment WHERE id_lesson=".intval($datamessage['lessonid']);
		$db->setQuery($sql);
		$count = $db->loadResult();
		
		if($count == 0){
			$sql = "INSERT INTO #__guru_task_kunenacomment (id_lesson, thread) VALUES (".$datamessage['lessonid'].", ".($threadid+1).")";
			$db->setQuery($sql);
			$db->execute();
		}

		$sql = "SELECT name from #__guru_task WHERE id=".intval($datamessage['lessonid']);
		$db->setQuery($sql);
		$name = $db->loadResult();

		$sql = "SELECT id from #__kunena_categories WHERE `alias`='".$lesson_alias."-".intval($datamessage['lessonid'])."'";
		$db->setQuery($sql);
		$idcat = $db->loadResult();
		
		$sql = "SELECT count(id) FROM  #__kunena_messages WHERE subject='".$name."' and `catid`=".intval($idcat);
		$db->setQuery($sql);
		$count2 = $db->loadResult();
		
		$jnow = new JDate('now');
		$currentdate = $jnow->toSQL(true);
		
		$sql = "SELECT id from #__kunena_messages WHERE subject ='".$name."' and parent = 0 and `catid`=".intval($idcat);
		$db->setQuery($sql);
		$idparent = $db->loadResult();
		
		if($count2 == 0){
			$sql = "INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, 0, ".$idcat.", '".$user->name."', ".$user->id.", '', '".$name."',  '".strtotime($currentdate)."', '127.0.0.1', 0,0,0,0,0,0,'','','')";	
			$db->setQuery($sql);
			$db->execute();
		}
		
		$sql = "SELECT id from #__kunena_messages WHERE subject ='".$name."' and `catid`=".intval($idcat);
		$db->setQuery($sql);
		$idparent = $db->loadResult();
		
		$sql = "UPDATE #__kunena_messages set thread ='".$idparent."' where parent = 0 and subject ='".$name."' and `catid`=".intval($idcat);
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "SELECT id from #__kunena_categories WHERE `alias`='".$lesson_alias."-".intval($datamessage['lessonid'])."'";
		$db->setQuery($sql);
		$idcat = $db->loadResult();
		
		if($idcat == "" || $idcat == 0){
			$idcat = 1;
		}
		
		if($count2 >0){
			$sql = "INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (".$idparent.", 0, ".$idcat.", '".$user->name."', ".$user->id.", '', '".$name."',  '".strtotime($currentdate)."', '127.0.0.1', 0,0,0,0,0,0,'','','')";
			$db->setQuery($sql);
			$db->execute();
			
			
			$sql = "SELECT thread from #__kunena_messages WHERE subject ='".$name."' and parent = 0 and `catid`=".intval($idcat);
			$db->setQuery($sql);
			$thread = $db->loadResult();
			
			$sql = "UPDATE  #__kunena_messages set thread ='".$thread."' where parent =".$idparent." and subject ='".$name."' and `catid`=".intval($idcat);
			$db->setQuery($sql);
			$db->execute();			
		}
		
		
		$sql = "SELECT id from #__kunena_messages WHERE subject ='".$name."' and `catid`=".intval($idcat)." order by id desc limit 0,1 ";
		$db->setQuery($sql);
		$idmessage = $db->loadColumn();
		$idmessage = $idmessage["0"];
		
		$sql = "INSERT INTO #__kunena_messages_text (mesid, message) VALUES (".$idmessage.",'".addslashes(trim($datamessage['message']))."' )";
		$db->setQuery($sql);
		$db->execute();
		
		if(isset($idcat) &&  $idcat != 0){
			$sql = "SELECT count(id) FROM  #__kunena_topics WHERE subject='".$name."' and `category_id`=".intval($idcat);
			$db->setQuery($sql);
			$count3 = $db->loadResult();
				
				if($count3 == 0){
					$sql = "INSERT INTO #__kunena_topics ( category_id, subject, icon_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, params) VALUES (".$idcat.", '".$name."', 0, 0,0,0,1,0,0,0,0,".$idmessage.",'".strtotime($currentdate)."', ".$user->id.", '".addslashes(trim($datamessage['message']))."', '".$user->username."', ".$idmessage.",'".strtotime($currentdate)."',".$user->id.",'".addslashes(trim($datamessage['message']))."','".$user->username."','')";
					$db->setQuery($sql);
					$db->execute();
				
				}
				else{
					$sql = "SELECT posts from #__kunena_topics WHERE subject ='".$name."' and `category_id`=".intval($idcat)." order by id desc limit 0,1 ";
					$db->setQuery($sql);
					$posts = $db->loadResult();
					
					$subquery = "SELECT  first_post_id from #__kunena_topics WHERE subject ='".$name."' and `category_id`=".intval($idcat)." order by id desc limit 0,1";
					$db->setQuery($subquery);
					$subquery = $db->loadColumn();
					$subquery = implode(",",$subquery);
					
					$sql = "SELECT count(id) from #__kunena_categories WHERE id IN(".$subquery.")";
					$db->setQuery($sql);
					$count_firstpost = $db->loadResult();
					
					if($count_firstpost == 0){
						$sql = "UPDATE #__kunena_topics set  posts=".($posts +1).", first_post_id=".$idmessage.",last_post_id=".$idmessage.", last_post_time='".strtotime($currentdate)."', last_post_userid=".$user->id.", last_post_message='".addslashes(trim($datamessage['message']))."', last_post_guest_name='".$user->username."' WHERE subject='".$name."' and `category_id`=".intval($idcat);
						$db->setQuery($sql);
						$db->execute();
					}
					else{
						$sql = "UPDATE #__kunena_topics set  posts=".($posts +1).", last_post_id=".$idmessage.", last_post_time='".strtotime($currentdate)."', last_post_userid=".$user->id.", last_post_message='".addslashes(trim($datamessage['message']))."', last_post_guest_name='".$user->username."' WHERE subject='".$name."' and `category_id`=".intval($idcat);
						$db->setQuery($sql);
						$db->execute();
					}
				
				}
			
		}
		
		$sql = "SELECT id FROM  #__kunena_topics WHERE subject='".$name."' and `category_id`=".intval($idcat);
		$db->setQuery($sql);
		$idtopic = $db->loadResult();
		
		$sql = "SELECT count(*) from #__kunena_user_topics WHERE user_id =".$user->id." and  topic_id =".$idtopic;
		$db->setQuery($sql);
		$counttopics = $db->loadResult();
		
		if($counttopics == 0){
			$sql = "INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params)  VALUES (".$user->id.", '".$idtopic."', ".$idcat.", 1,".$idmessage.",1,0,1,'')";
			$db->setQuery($sql);
			$db->execute();
		}
		else{
			$sql = "UPDATE #__kunena_user_topics set  posts=".($posts +1).", last_post_id=".$idmessage." WHERE user_id ='".$user->id."' and  topic_id =".$idtopic;
			$db->setQuery($sql);
			$db->execute();
		}
		
		$sql = "UPDATE  #__kunena_messages set thread ='".$idtopic."' where subject ='".$name."' and `catid`=".intval($idcat);
		$db->setQuery($sql);
		$db->execute();

		$sql = "UPDATE  #__kunena_categories set last_topic_id ='".intval($idtopic)."' ,last_post_id=".intval($idmessage)."  where id =".$idcat." and name ='".$db->escape($name)."' ";
		$db->setQuery($sql);
		$db->execute();
		
		$sql ="select id, name, userid from #__kunena_messages WHERE subject='".$name."' and `catid`=".intval($idcat)." order by id desc";
		$db->setQuery($sql);
		$resultid = $db->loadAssocList();
		
		$jnow = new JDate('now');
		$date_currentk = $jnow->toSQL(true);									
		$int_current_datek = strtotime($date_currentk);
		
		$sql ="select id from #__kunena_categories WHERE name='".$name."' order by id desc limit 0,1";
		$db->setQuery($sql);
		$catkunena = $db->loadResult();
		
		$sql ="select id from #__kunena_topics WHERE subject='".$name."' and `category_id`=".intval($idcat)." order by id asc limit 0,1";
		$db->setQuery($sql);

		$idmess = $db->loadResult();
		
			 for($i=0; $i < count($resultid); $i++){	
					$sql = "select message from #__kunena_messages_text WHERE mesid=".$resultid[$i]["id"];
					$db->setQuery($sql);
					$result = $db->loadResult();
					
					$sql = "select time from #__kunena_messages WHERE id=".$resultid[$i]["id"];
					$db->setQuery($sql);
					$datestart = $db->loadResult();
					
					$model_task = new guruModelguruTask();
					$timepast = $model_task->get_time_difference($datestart,$int_current_datek);
					
					if($timepast["days"] == 0){
						if($timepast["hours"] == 0){
							if($timepast["minutes"] == 0){
								$difference = @$timepast["seconds_ago"]." ".JText::_("GURU_FEW_SECS_AGO");
							}
							else{
								$difference = $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
							}
						}
						else{
							$difference = $timepast["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
										  $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
						}
					}
					else{
						$difference = $timepast["days"]." ".JText::_("GURU_REAL_DAYS").", ".
									  $difference = $timepast["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
									  $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
					}
					if($user->id == $resultid[$i]["userid"]){
						if($allow_delete == 0){
							$concat = 1;
							$buttons = '<span><a href="#" id="delete'.$resultid[$i]["id"].'" onclick="javascript:deletegurucomment('.$datamessage['lessonid'].', '.$resultid[$i]["userid"].', '.$resultid[$i]["id"].'); return false;"><i class="fontello-trash"></i>a></span>';
																 			
						}
						else{
							$concat = 0;
						}
						if($allow_edit == 0){
							if($concat == 0){
								$buttons = '<span><a href="#" id="edit'.$resultid[$i]["id"].'" onclick="javascript:editgurucomment1('.$resultid[$i]["id"].'); return false;"><i class="fontello-pencil"></i></a></span>';
							}
							else{
								$buttons .= '<span><a href="#" id="edit'.$resultid[$i]["id"].'" onclick="javascript:editgurucomment1('.$resultid[$i]["id"].'); return false;"><i class="fontello-pencil"></i></a></span>';
							}	
						}
					}
					else{
						$buttons = " ";
					}
					
						echo '<div class="guru-lesson-comment">
							<div class="guru-lesson-comment-wrap">
							<div class="guru-lesson-comment-avatar"></div>

							<div class="guru-lesson-comment-body">
								<a class="guru-lesson-comment-name" href="' . JUri::base() . 'index.php?option=com_kunena&view=topic&catid=' . $catkunena . '&id=' . $idmess . '&Itemid=0#' . $resultid[$i]["id"] . '">
									' . $resultid[$i]["name"] . '
								</a>

								' . $result . '

								<textarea style="display:none;" style="width:100%" name="message1'.$resultid[$i]["id"].'" id="message1'.$resultid[$i]["id"].'" rows="2" cols="90"></textarea>
								<input  class="uk-button uk-button-success wk-button wk-button-success" style="display:none;" id="save'.$resultid[$i]["id"].'" name="save" type="button" onclick="javascript:savegurucomment('.$datamessage['lessonid'].','.$resultid[$i]["id"].');" value="'.JText::_('GURU_SAVE').'" />

								<div class="guru-lesson-comment-meta">
									<span class="guru-lesson-comment-time">' . $difference . '</span>
									'.$buttons.'
								</div>
							</div>
						   </div>
						   </div>';
				}
		
		
	}
	
	function deletecom(){
		$db = JFactory::getDBO();
		$comid = JFactory::getApplication()->input->get("comid", "");
		$uid = JFactory::getApplication()->input->get("uid", "");
		$lid = JFactory::getApplication()->input->get("lessonid", "");
		$user = JFactory::getUser();
		
		$jnow = new JDate('now');
		$date_currentk = $jnow->toSQL(true);									
		$int_current_datek = strtotime($date_currentk);
		
		$sql = "SELECT name from #__guru_task WHERE id=".intval($lid);
		$db->setQuery($sql);
		$name = $db->loadResult();
		
		$sql = "DELETE from #__kunena_messages WHERE id =".$comid." and userid=".$uid." ";
		$db->setQuery($sql);
		$db->execute();
		
		
		
		$sql = "SELECT id FROM  #__kunena_topics WHERE subject='".$name."'";
		$db->setQuery($sql);
		$idtopic = $db->loadResult();
		
		$sql = "SELECT id from #__kunena_messages WHERE subject ='".$name."' order by id desc limit 0,1 ";
		$db->setQuery($sql);
		$idmessage = $db->loadResult();
		
		$sql = "SELECT id from #__kunena_categories WHERE name ='".$name."'";
		$db->setQuery($sql);
		$idcat = $db->loadResult();

		$sql = "UPDATE  #__kunena_categories set last_topic_id ='".intval($idtopic)."' ,last_post_id=".intval($idmessage).", last_post_time='".$int_current_datek ."'  where id =".$idcat." and name ='".$db->escape($name)."' ";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "UPDATE  #__kunena_topics set first_post_id ='".$idmessage."' where id =".$idtopic." ";
		$db->setQuery($sql);
		$db->execute();
		
		$sql ="select id, name, userid from #__kunena_messages WHERE subject='".$name."' order by id desc";
		$db->setQuery($sql);
		$resultid = $db->loadAssocList();
		
		$sql ="select id from #__kunena_categories WHERE name='".$name."' order by id desc limit 0,1";
		$db->setQuery($sql);
		$catkunena = $db->loadResult();
		
		$sql ="select id from #__kunena_topics WHERE subject='".$name."' order by id asc limit 0,1";
		$db->setQuery($sql);
		$idmess = $db->loadResult();
		
		 for($i=0; $i < count($resultid); $i++){	
					$sql = "select message from #__kunena_messages_text WHERE mesid=".$resultid[$i]["id"];
					$db->setQuery($sql);
					$result = $db->loadResult();
					
					$sql = "select time from #__kunena_messages WHERE id=".$resultid[$i]["id"];
					$db->setQuery($sql);
					$datestart = $db->loadResult();
					
					$model_task = new guruModelguruTask();
					$timepast = $model_task->get_time_difference($datestart,$int_current_datek);
					
					if($timepast["days"] == 0){
						if($timepast["hours"] == 0){
							if($timepast["minutes"] == 0){
								$difference = "a few seconds ago";
							}
							else{
								$difference = $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
							}
						}
						else{
							$difference = $timepast["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
										  $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
						}
					}
					else{
						$difference = $timepast["days"]." ".JText::_("GURU_REAL_DAYS").", ".
									  $difference = $timepast["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
									  $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
					}
					if($user->id == $resultid[$i]["userid"]){
						if($allow_delete == 0){
							$concat = 1;
							$buttonsd = '<span style="display:block; float:left;"><a href="#" id="delete'.$resultid[$i]["id"].'" onclick="javascript:deletegurucomment('.$lid.', '.$resultid[$i]["userid"].', '.$resultid[$i]["id"].'); return false;">'.JText::_("GURU_DELETE").'</a></span>';
																 			
						}
						else{
							$concat = 0;
						}
						if($allow_edit == 0){
							if($concat == 0){
								$buttonsd = '<span style="float:right;display:block"><a href="#" id="edit'.$resultid[$i]["id"].'" onclick="javascript:editgurucomment1('.$resultid[$i]["id"].'); return false;">'.JText::_("GURU_EDIT").'</a></span>';
							}
							else{
								$buttonsd .= '<span style="float:right;display:block"><a href="#" id="edit'.$resultid[$i]["id"].'" onclick="javascript:editgurucomment1('.$resultid[$i]["id"].'); return false;">'.JText::_("GURU_EDIT").'</a></span>';
							}
						}
					}
					else{
						$buttonsd = " ";
					}
					
					echo '<div class="guru-header">
							<span><img  src="'.JUri::base().'components/com_guru/images/guru_comment.gif'.'"</span>
							<span>'.JText::_ ( "GURU_POSTED" ).':'.$difference.'</span>
							<span style="float:right;"><a href='.JUri::base().'index.php?option=com_kunena&view=topic&catid='.$catkunena.'&id='.$idmess.'&Itemid=0#'.$resultid[$i]["id"].'>#'.$resultid[$i]["id"].'</a></span>
							<span>'. JText::_ ( "GURU_COMMENTED_BY" ) . ' ' . $resultid[$i]["name"] .'</span>
							</div>
							<div class="guru-reply-body clearfix">
								<div style="display:block;" class="guru-text" id="gurupostcomment'.$resultid[$i]["id"].'">'.$result.'</div>
								<textarea style="display:none;" style="width:100%" name="message1'.$resultid[$i]["id"].'" id="message1'.$resultid[$i]["id"].'" rows="2" cols="90"></textarea>
                                 <input class="btn btn-success" style="display:none;" id="save'.$resultid[$i]["id"].'" name="save" type="button" onclick="javascript:savegurucomment('.$lid.','.$resultid[$i]["id"].');" value="'.JText::_('GURU_SAVE').'" />
								<div>'.$buttonsd.'</div>
						   </div>';
				}
		
	
	}
	
	function editformgurupost(){
		$db = JFactory::getDBO();
		$comid = JFactory::getApplication()->input->get("comid", "");
		$user = JFactory::getUser();
		$message = urldecode(JFactory::getApplication()->input->get("message", ""));
		
		$sql = "UPDATE #__kunena_messages_text set message= '".$message."' WHERE mesid =".$comid."";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "SELECT message from #__kunena_messages_text WHERE mesid =".$comid."";
		$db->setQuery($sql);
		$message = $db->loadResult();
		
		echo $message;
	}
	
	function calculatecertificate(){
		$model = $this->getModel("guruTask");
		$course_id = JFactory::getApplication()->input->get("course_id", "0");
		$return = $model->isLastPassedQuiz($course_id);
		
		if($return === TRUE){
			echo "ok";
			die();
		}
		else{
			echo "not_ok";
			die();
		}
	}
	
	function showCertificateFr(){
		$db = JFactory::getDBO();	
		$user = JFactory::getUser();
		$user_id = $user->id;
		$pid =  JFactory::getApplication()->input->get("course_id");
		$lesson_id = JFactory::getApplication()->input->get("lesson_id");
		$scores_avg_quizzes =  guruModelguruTask::getAvgScoresQ($user_id,$pid );

		$sql = "SELECT completed from #__guru_viewed_lesson WHERE user_id =".intval($user_id)." and pid=".intval($pid);
		$db->setQuery($sql);
		$completed_course = $db->loadResult();
		
		$sql = "SELECT certificate_term FROM #__guru_program WHERE id =".intval($pid);
		$db->setQuery($sql);
		$course_certificate_term = $db->loadResult();
		
		$sql = "select avg_certc from #__guru_program where id=".intval($pid);
		$db->setQuery($sql);
		$avg_certif = $db->loadResult();
		
		$sql = "SELECT media_id FROM #__guru_mediarel WHERE type='scr_m' and type_id=".intval($lesson_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
	   
		$sql = "SELECT max_score FROM #__guru_quiz WHERE id=".intval($result);
		$db->setQuery($sql);
		$result_maxs = $db->loadResult();

		$sql = "SELECT id, score_quiz, time_quiz_taken_per_user  FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($result)." and pid=".$pid." ORDER BY id DESC LIMIT 0,1";
		$db->setQuery($sql);
		$result_q = $db->loadObject();
	   
		$first= explode("|", @$result_q->score_quiz);
	   
		@$res = intval(($first[0]/$first[1])*100);
		if($course_certificate_term == 2 && ($completed_course==true || $completed_course == 1) ){
			$this->InsertMyCertificateDetails2($pid);
			echo "yes";
		} 
		elseif($course_certificate_term == 3 && isset($result_maxs) && $res >= intval($result_maxs) ){
			$this->InsertMyCertificateDetails2($pid);
			echo "yes";
		}
		elseif($course_certificate_term == 4 && $scores_avg_quizzes >= intval($avg_certif)){
			$this->InsertMyCertificateDetails2($pid);
			echo "yes";
		}
		elseif($course_certificate_term == 5 && ($completed_course==true || $completed_course ==1) && isset($result_maxs) && $res >= intval($result_maxs)){
			$this->InsertMyCertificateDetails2($pid);
			echo "yes";
		}
		elseif($course_certificate_term == 6 && ($completed_course==true || $completed_course ==1) && isset($scores_avg_quizzes) && ($scores_avg_quizzes >= intval($avg_certif))){
			$this->InsertMyCertificateDetails2($pid);
			echo "yes";
		}
		else{
			echo "no";	
		}
		die();
	}
	function InsertMyCertificateDetails2($pid){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$jnow = new JDate('now');
		$id = $user->id;
		$sql = "SELECT count(id) from #__guru_mycertificates WHERE user_id =".intval($id)." and course_id=".intval($pid);
		$db->setQuery($sql);
		$count_cert = $db->loadResult();
		
		$current_date_cert = $jnow->toSQL(true);

			$author_id = "SELECT author from #__guru_program WHERE id =".intval($pid);
			$db->setQuery($author_id);
			$resultauth = $db->loadResult();

			$sql = 'insert into  #__guru_mycertificates (`course_id`, `author_id`, `user_id`, `emailcert`, `datecertificate` ) values ("'.intval($pid).'", "'.intval($resultauth).'", "'.intval($id).'", \'0\', "'.$current_date_cert.'")';
			$db->setQuery($sql);
			$db->execute();	
	
	}
	function neededinfo(){
		$db = JFactory::getDBO();
		$quiz_id =  JFactory::getApplication()->input->get("quiz_id");
		$question_id =  JFactory::getApplication()->input->get("question_id");
		
		$q  = "SELECT * FROM #__guru_quiz WHERE id = ".$quiz_id." and published=1 ";
		$db->setQuery($q);
		$result_quiz = $db->loadObject();
		
		$sql = "SELECT max_score, pbl_max_score, limit_time, show_limit_time, time_quiz_taken, show_nb_quiz_taken, nb_quiz_select_up, show_nb_quiz_select_up  FROM #__guru_quiz where id=".$result_quiz->id;
		$db->setQuery($sql);
		$result_settings_quiz = $db->loadObject();
		
		$order_by = "";
		
		if(isset($result_settings_quiz->nb_quiz_select_up) && $result_settings_quiz->nb_quiz_select_up !=0 && $result_settings_quiz->show_nb_quiz_select_up ==0){
			$order_by = "";
		}
		else{
			$order_by = " ORDER BY reorder";
		}
		
		if($result_quiz->is_final == 1){
			$sql = "SELECT 	quizzes_ids FROM #__guru_quizzes_final WHERE qid=".intval($quiz_id);
			$db->setQuery($sql);
			$result=$db->loadColumn();	
			$result_qids = explode(",",trim($result[0],","));
			
			if($result_qids["0"] == ""){
				$result_qids["0"] = 0;
			}
			
			if(isset($result_qids) && count($result_qids) > 0){
				foreach($result_qids as $key=>$value){
					$quiz_id = intval($value);
					$sql = "select published from #__guru_quiz where id=".intval($quiz_id);
					$db->setQuery($sql);
					$published = $db->loadColumn();
					$published = @$published["0"];
					if(intval($published) == 0){
						unset($result_qids[$key]);
					}
				}
			}
			
			if(count($result_qids) == 0 || $result_qids["0"] == ""){
				$result_qids["0"] = 0;
			}
			
			$query  = "SELECT * FROM #__guru_questions_v3 WHERE qid IN (".implode(",", $result_qids).") and published=1".$order_by;
			$db->setQuery($query);
			$quiz_questions = $db->loadObjectList();
			
			foreach($quiz_questions as $one_question ){
				if($one_question->id == intval($question_id)){
					echo trim($one_question->answers);
					die();	
				}
			}
		}
		else{
			$query  = "SELECT * FROM #__guru_questions_v3 WHERE qid = ".intval($quiz_id)." and id=".intval($question_id);
			$db->setQuery($query);
			$question_details = $db->loadAssocList();
			echo trim($question_details["0"]["answers"]);
			die();
		}	
		
		
	}
	
	function get_quiz_result(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("view");
		$view->setModel($this->_model, "getTask");		
		$view->quiz_fe_result_calculation();	
	}
	
	function save_quiz_result(){
		$this->_model->store_quiz_results();
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("view");
		$view->setModel($this->_model, "getTask");		
		$view->quiz_fe_result_calculation();
	}

	function getContent($type_id) {
		$db = JFactory::getDBO();
		$type_id = intval($type_id);
		$media_ids = array();
		$types = array();

		// get layout
		$sql = "SELECT media_id FROM #__guru_mediarel WHERE type = 'scr_l' AND type_id = " . $type_id;
		$db->setQuery($sql);
		$layout = intval( $db->loadResult() );

		$guruModelguruTask = new guruModelguruTask();

		// get videos
		if ( in_array($layout, array(1, 2, 3, 4, 6, 7, 8, 9, 10, 11)) ) {
			$sql = "SELECT media_id FROM #__guru_mediarel WHERE type = 'scr_m' AND layout = " . $layout . " AND type_id = " . $type_id . " ORDER BY mainmedia ASC";
			$db->setQuery($sql);
			$media = $db->loadColumn();
			
			foreach ($media as $index => $media_id) {
				$media_ids[] = $media_id;
				$media[$index] = $media_id ? $guruModelguruTask->parse_media($media_id) : FALSE;
			}
		}

		// get texts
		if ( in_array($layout, array(1, 2, 3, 4, 5, 7, 8, 9, 10, 11)) ) {
			$sql = "SELECT media_id FROM #__guru_mediarel WHERE type = 'scr_t' AND layout = " . $layout . " AND type_id = " . $type_id . " ORDER BY mainmedia ASC";
			$db->setQuery($sql);
			$text = $db->loadColumn();
			
			foreach ($text as $index => $text_id) {
				$media_ids[] = $text_id;
				$text[$index] = $text_id ? $guruModelguruTask->parse_txt($text_id) : FALSE;
			}
		}

		// get types
		foreach ($media_ids as $index => $media_id) {
			if ($media_id) {
				$sql = "SELECT `type` FROM #__guru_media WHERE `id` = " . $media_id;
				$db->setQuery($sql);
				$type = $db->loadResult();
				if ($type) {
					$types[] = $type;
				}
			}
		}
	
		return array(
			'layout' => $layout,
			'media' => isset($media) ? $media : array(),
			'text' => isset($text) ? $text : array(),
			'type' => $types
		);
	}

	function getLessonDescription(){
		$db = JFactory::getDBO();
		
		$lesson_url = JFactory::getApplication()->input->get("lesson_url", "", "raw");
		$lesson_id = JFactory::getApplication()->input->get("lesson_id", "0", "raw");
		
		if(intval($lesson_id) == 0){
			$parts = parse_url($lesson_url);
			parse_str($parts['query'], $query);
			$lesson_id = isset($query["cid"]) ? (int) $query['cid'] : 0;
		}
		
		$sql = "select name, description from #__guru_task where id=".intval($lesson_id);
		$db->setQuery($sql);
		$lesson_details = $db->loadAssocList();
		if ($lesson_details) {
			$name = $lesson_details["0"]["name"];
			$description = $lesson_details["0"]["description"];
			
			if(trim($description) == ""){
				$description = $name;
			}
		} else {
			$description = '';
		}
		
		$description = str_replace("\r\n\r\n", "<br /><br />", $description);
		$description = str_replace("\r\n", "<br />", $description);
		
		$ret = array(
			"description" => $description
		);

		die( json_encode($ret) );
	}

	function getLessons() {
		require_once(JPATH_BASE . "/components/com_guru/models/guruprogram.php");
		require_once(JPATH_BASE . "/components/com_guru/models/gurutask.php");

		$db = JFactory::getDBO();
		$program_id = intval( JFactory::getApplication()->input->get("pid") );
		$course_id = intval( JFactory::getApplication()->input->get("cid") );
		
		//first lesson description
		$description = "";

		// get modules
		$sql = "SELECT * FROM #__guru_days WHERE pid='" . $program_id . "' ORDER BY ordering ASC";
		$db->setQuery($sql);
		$modules = $db->loadAssocList();
		$lessons = array();
		$selected = FALSE;

		$sql = "select * from #__guru_program where id=".intval($program_id);
		$db->setQuery($sql);
		$coursetype_details = $db->loadAssocList();
		$guruModelguruProgram = new guruModelguruProgram();
		$guruModelguruTask = new guruModelguruTask();

		$program = $guruModelguruProgram->getProgram();
		$itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		
		$diff_start = "";
		$diff_date = "";
		$start_date = "";
		
		$lesson_details_for_quiz = array();
		
		//-----------------------------------------
		// start calculation for one lesson per (option in admin)
		$my = JFactory::getUser();
		$user_id = $my->id;

		if($user_id > 0){
			$db = JFactory::getDBO();
			$sql = "select DATE_FORMAT(buy_date,'%Y-%m-%d %H:%i:%s') from #__guru_buy_courses where course_id=".intval($course_id)." and userid =".$user_id;
			$db->setQuery($sql);
			$date_enrolled = $db->loadResult();
			$date_enrolled = strtotime($date_enrolled);
		}

		$lessons_per_release = 1;
		
		if(isset($date_enrolled) && $date_enrolled !== FALSE && !is_null($date_enrolled)){
			$start_relaese_date1 = $coursetype_details[0]["start_release"];
			$start_relaese_date = strtotime($start_relaese_date1);
			$start_date = $date_enrolled;
			
			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
            $jnow = new JDate('now');
            $jnow->setTimezone($timezone);
            $date9 = $jnow->toSQL(true);

            $date_9 = date("Y-m-d H:i:s",strtotime($date9));
            $date9 = strtotime($date9);
			
			$interval = abs($date9 - $start_date);
			
			$dif_hours = floor($interval/(60*60));
			$dif_days = floor($interval/(60*60*24));
			$dif_week = floor($interval/(60*60*24*7));
			$dif_month = floor($interval/(60*60*24*30));
			// start changes for lessons per release
            $lessons_per_release = $program->lessons_per_release;
			
			if($coursetype_details[0]["course_type"] == 1){
				if($coursetype_details[0]["lesson_release"] == 1){
					$diff_start = $dif_days+1;
					$diff_date = $dif_days+1;
				}
				elseif($coursetype_details[0]["lesson_release"] == 2){
					$diff_start = $dif_week+1;
					$diff_date = $dif_week + 1;
				}
				elseif($coursetype_details[0]["lesson_release"] == 3){
					$diff_start = $dif_month+1;
					$diff_date = $dif_month+1;
				}
				elseif($coursetype_details[0]["lesson_release"] == 4){
                   $diff_start = $dif_hours + $coursetype_details["0"]["after_hours"];
                   $diff_date = $dif_hours + $coursetype_details["0"]["after_hours"];
                }
                elseif($coursetype_details[0]["lesson_release"] == 5){
                   $diff_start = 1;
                   $diff_date = 1;
                }
			}
		}
		
		$step_less = @$diff_start;
		$nr_lesson = 1;

		$lang = JFactory::getLanguage()->getTag();
        $lang = explode("-", $lang);
        $lang = @$lang["0"];
	
		//-----------------------------------------
		$poz = 0;

		foreach ($modules as $module) {
			$poz ++;

			//$module_url = JURI::root() . "index.php?option=com_guru&view=gurutasks&catid=" . $program->catid . "&module=" . $module["id"] . "-" . $module["alias"] . "&tmpl=component&Itemid=" . $itemid;

			$module_url = JURI::root()."index.php?option=com_guru&view=guruTasks&catid=".intval($program->catid)."&module=".intval($module["id"])."&action=viewmodule&tmpl=component&Itemid=".intval($itemid);
			
			$lessons = $guruModelguruProgram->getSubCategory($module['id']);
			
			foreach ($lessons as $key => $lesson) {
				$lessons[$key]['url'] = JURI::root() . "index.php?option=com_guru&view=gurutasks&catid=" . $program->catid . "&module=" . $module["id"] . "-" . $module["alias"] . "&cid=" . $lesson['id'] . "-" . $lesson["alias"] . "&tmpl=component&Itemid=" . $itemid . "&lang=".$lang;
				$author = $guruModelguruProgram->getAuthor();
				
				if($key == 0){
					$sql = "select description from #__guru_task where id=".intval($lesson['id']);
					$db->setQuery($sql);
					$description = $db->loadColumn();
					$description = @$description["0"];
				}
				
				$config = $guruModelguruProgram->getConfigSettings();
				
				$lesson = $guruModelguruProgram->checkLessonQuiz($lessons[$key], $program);
				$lesson_details_for_quiz[] = $lesson;
				$lessons[$key] = $guruModelguruProgram->getLessonDetails(
					$program,
					$author,
					$lessons[$key],
					$diff_date,
					$diff_start,
					$step_less,
					$start_date,
					$config,
					$lesson_details_for_quiz,
					$key,
					$nr_lesson,
					$lessons_per_release
				);
			// end changes for lessons per release
				$nr_lesson++;
				
				if($diff_date != ""){
					@$diff_date--;
				}
				
				if($lessons[$key]['can_open_lesson'] == 0 && isset($lessons[$key]["available_div"]) && trim($lessons[$key]["available_div"]) != ""){
					// do nothing, the lesson is not available
					$step_less++;
				}
				
				if ( $lessons[$key]['can_open_lesson'] == 1 ) {
					$lessons[$key]['data'] = $this->getContent( intval($lesson['id']) );
				}
				
				$lessons[$key]['viewed'] = $guruModelguruTask->getViewLesson($lesson['id']);

				if ( intval($lesson['id']) == $course_id ) {
					$selected = $lessons[$key];
				}

				$lessons[$key]["course_type"] = $program->course_type;
				$lessons[$key]["lesson_release"] = $program->lesson_release;

				if($program->course_type == 1 && $program->lesson_release == 5){
					$db = JFactory::getDbo();
					$sql = "SELECT `id` FROM #__guru_days WHERE `pid`=".intval($program->id);
					$db->setQuery($sql);
					$course_modules = $db->loadColumn();

					if(!isset($course_modules) || count($course_modules) == 0){
						$course_modules = array("0");
					}
					
					$sql = "SELECT `media_id` FROM #__guru_mediarel WHERE type='dtask' AND `type_id` in (".implode(',', $course_modules).")";
					$db->setQuery($sql);
					$module_lessons = $db->loadColumn();

					if(isset($module_lessons) && count($module_lessons) > 0){
						$sql = "SELECT `id` FROM #__guru_task WHERE `id` in (".implode(',', $module_lessons).") and `published`='1' and `startpublish` <= now() and (`endpublish` >= now() OR `endpublish`='0000-00-00 00:00:00') order by `ordering` ASC";
						$db->setQuery($sql);
						$module_lessons = $db->loadColumn();

						if(isset($module_lessons) && count($module_lessons) > 0){
							foreach($module_lessons as $key_module_lesson=>$module_lesson_id){
								if($lesson['id'] == $module_lesson_id){
									if(isset($module_lessons[$key_module_lesson+1])){
										$lessons[$key]["next_lesson"] = $module_lessons[$key_module_lesson+1];
									}
								}
							}
						}
					}
				}
			}
			
			$can_open_module = "1";
			
			if($program->skip_module == "1"){
				$can_open_module = "0";
			}
			
			$sql = "select media_id from #__guru_days where id=".intval($module['id']);
			$db->setQuery($sql);
			$media_id = $db->loadColumn();
			$media_id = @$media_id["0"];
			
			if(intval($media_id) > 0){
				$sql = "select type from #__guru_media where id=".intval($media_id);
				$db->setQuery($sql);
				$type = $db->loadColumn();
				$type = @$type["0"];
				
				if(trim($type) != ""){
					switch($type){
						case "video" :{
							$module['title'] .= '<br /><i class="fontello-play-circled"></i>Video';
							break;
						}
						case "audio" :{
							$module['title'] .= '<br /><i class="fontello-volume-up"></i>Audio';
							break;
						}
						case "quiz" :{
							$module['title'] .= '<br /><i class="fontello-check"></i>Quiz';
							break;
						}
						case "text" :{
							$module['title'] .= '<br /><i class="fontello-doc-text-inv"></i>Text';
							break;
						}
						case "url" :{
							$module['title'] .= '<br /><i class="fontello-shuffle"></i>URL';
							break;
						}
						case "docs" :{
							$module['title'] .= '<br /><i class="fontello-doc-text-inv"></i>Document';
							break;
						}
						case "image" :{
							$module['title'] .= '<br /><i class="fontello-camera"></i>Image';
							break;
						}
					}
				}
			}

			$sql = "select `custom_page_url` from #__guru_program where `id`=".intval($program_id);
			$db->setQuery($sql);
			$custom_page_url = $db->loadColumn();
			$custom_page_url = @$custom_page_url["0"];

			if(trim($custom_page_url) != "" && intval($poz) == count($modules)){
				$completed_course = array(
					"name" => JText::_("GURU_COURSE_COMPLETED"),
		            "alias" => "completed-course-redirect",
		            "time" => 0,
		            "id" => 0,
		            "groups_access" => "",
		            "duration" => "",
		            "chb_free_courses" => 0,
		            "step_access_courses" => 0,
		            "lessons_show" => 1,
		            "selected_course" => "",
		            "ordering" => "",
		            "step_access" => 0,
		            "difficultylevel" => "easy",
		            "url" => trim($custom_page_url),
		            "can_open_lesson" => 1,
		            "available_div" => '',
		            "data" => "",
		            "viewed" => 1
		        );
		        
		        $lessons[] = $completed_course;
			}

			$contents[] = array(
				'module_id' => $module['id'],
				'url' => $module_url,
				'title' => $module['title'],
				'can_open_module' => $can_open_module,
				'lessons' => $lessons
			);
		}
		
		$ret = array(
			"description" => $description,
			"modules" => $contents,
			"selected" => $selected,
			"info" => $this->programInfo($program_id),
			"kunena" => $guruModelguruTask->getKunenaSettings()
		);

		die( json_encode($ret) );
	}

	function getComments() {
		$lesson_id = intval( JFactory::getApplication()->input->get("lesson_id") );
		$commentbox = $this->getCommentBox( $lesson_id );
		$commentlist = $this->getCommentList( $lesson_id );
		$ret = array(
			"commentbox" => $commentbox,
			"commentlist" => $commentlist
		);

		die( json_encode($ret) );
	}

	function getCommentBox( $lesson_id ) {
		$db = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "select count(*) from #__extensions where element='com_kunena'";
		$db->setQuery($sql);
		$count = $db->loadResult();

		$user = JFactory::getUser();
		$user_id = $user->id;

		if($user_id != 0){
			if($count >0){
				$gurucommentbox = '<div class="guru-lesson-comments-form">'
					. '<textarea placeholder="'.JText::_("GURU_ADD_COMMENT").'" data-id="' . $lesson_id . '"></textarea>'
					. '<button class="uk-button uk-button-success wk-button wk-button-success" disabled="disabled"><i class="fontello-ok-circled"></i></button>'
					. '</div>';
				return $gurucommentbox;
			}
 		}

		return '';
	}

	function getCommentList( $lesson_id ) {
		$db = JFactory::getDBO();
		$guruModelguruTask = new guruModelguruTask();
		$html = '';

		$sql = "select count(*) from #__extensions where element='com_kunena' and name='com_kunena'";
		$db->setQuery($sql);
		$count = $db->loadResult();

		if($count > 0) {
			$sql ="select enabled from #__extensions WHERE element='ijoomlagurudiscussbox'";
			$db->setQuery($sql);
			$enabled = $db->loadResult();

			$sql ="select alias from #__guru_task WHERE id=" . $lesson_id;
			$db->setQuery($sql);
			$lesson_alias = $db->loadResult();

			$sql ="select name from #__guru_task WHERE id=" . $lesson_id;
			$db->setQuery($sql);
			$lesson_name = $db->loadResult();

			$sql ="select `id` from #__kunena_categories WHERE `alias`='".$lesson_alias."-".intval($lesson_id)."'";
			$db->setQuery($sql);
			$catid = $db->loadResult();

			$sql ="select count(id) from #__kunena_categories WHERE alias = '" . $lesson_alias."-" . intval($lesson_id) . "'";
			$db->setQuery($sql);
			$board_less = $db->loadResult();

			if($enabled == 1){//if the plugin  is enabled
				if($board_less != 0){//if we have category created for the lesson
					$sql ="select numPosts from #__kunena_categories WHERE alias = '" . $lesson_alias."-" . intval($lesson_id) . "' order by id desc limit 0,1";
					$db->setQuery($sql);
					$numposts = $db->loadResult();

					$user = JFactory::getUser();
					$user_id = $user->id;

					if($user_id != 0 ){//if you are login
						$sql = "select allow_stud from #__guru_kunena_forum where id=1";
						$db->setQuery($sql);
						$allow_stud = $db->loadResult();

						$sql = "select allow_edit from #__guru_kunena_forum where id=1";
						$db->setQuery($sql);
						$allow_edit = $db->loadResult();

						$sql = "select allow_delete  from #__guru_kunena_forum where id=1";
						$db->setQuery($sql);
						$allow_delete = $db->loadResult();

						if($allow_stud == 0){

							$sql ="select k.id, k.name, k.userid, u.email from #__kunena_messages k, #__users u WHERE k.subject='".addslashes(trim($lesson_name))."' and u.id=k.userid and `catid`=".intval($catid)." order by k.id desc";
							$db->setQuery($sql);
							$resultid = $db->loadAssocList();

							$jnow = new JDate('now');
							$date_currentk = $jnow->toSQL(true);                                   
							$int_current_datek = strtotime($date_currentk);

							$sql ="select id from #__kunena_categories WHERE alias='".addslashes(trim($lesson_alias))."-".intval($lesson_id)."' order by id desc limit 0,1";
							$db->setQuery($sql);
							$catkunena = $db->loadResult();

							$sql ="select id from #__kunena_topics WHERE subject='".addslashes(trim($lesson_name))."' and `category_id`=".intval($catid)." order by id asc limit 0,1";
							$db->setQuery($sql);
							$idmess = $db->loadResult();

							for($i=0; $i < count($resultid); $i++){   
								$sql = "select message from #__kunena_messages_text WHERE mesid=".$resultid[$i]["id"];
								$db->setQuery($sql);
								$result = $db->loadResult();

								$sql = "select time from #__kunena_messages WHERE id=".$resultid[$i]["id"];
								$db->setQuery($sql);
								$datestart = $db->loadResult();

								$timepast = $guruModelguruTask->get_time_difference($datestart,$int_current_datek);

								if($timepast["days"] == 0){
									if($timepast["hours"] == 0){
										if($timepast["minutes"] == 0){
											$difference = "a few seconds ago";
										}
										else{
											$difference = $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
										}
									}
									else{
										$difference = $timepast["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
													  $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
									}
								}
								else{
									$difference = $timepast["days"]." ".JText::_("GURU_REAL_DAYS").", ".
												  $difference = $timepast["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
												  $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
								}

								if(/*$deviceType !='phone'*/ TRUE){
									$rows_cols = ' rows="2" cols="90"';
									$style = 'style="width:100%"';
								}
								else{
									$rows_cols = 'rows="3"';
									$style = 'style="width:50%"';
								}

								if($user_id == $resultid[$i]["userid"]){
									if(/*$allow_delete == 0*/ TRUE){ $html .= '<div class="guru-lesson-comment my-message">'; }
								} else {
									$html .= '<div class="guru-lesson-comment guru-comment">';
								};
								
								$sql = "select image from #__guru_customer where id=".intval($resultid[$i]["userid"]);
								$db->setQuery($sql);
								$student_image = $db->loadColumn();
								$student_image = @$student_image["0"];
								
								if(!isset($student_image) || trim($student_image) == ""){
									$sql = "select images from #__guru_authors where userid=".intval($resultid[$i]["userid"]);
									$db->setQuery($sql);
									$student_image = $db->loadColumn();
									$student_image = @$student_image["0"];
								}
								
								$url_image = "http://www.gravatar.com/avatar/".md5(strtolower(trim($resultid[$i]["email"])))."?d=mm&s=40";
								
								if(trim($student_image) != "" && trim($student_image) != "/"){
									$url_image = JURI::root().$student_image;
								}
				
								$html .= '
									<div class="guru-lesson-comment-wrap">
									<div class="guru-lesson-comment-avatar">
										<span style="background-image: url('.$url_image.');">
										</span>
									</div>

									<div class="guru-lesson-comment-body guru-reply-body">
											<a class="guru-lesson-comment-name" href="' . JRoute::_('index.php?option=com_kunena&view=topic&catid=' . $catkunena . '&id=' . $idmess) . '">
											' . $resultid[$i]["name"] . '
											</a>

											<span class="guru-text" id="gurupostcomment'.$resultid[$i]["id"].'">' . $result . '</span>

											<textarea style="display:none;" ' . $style . ' name="message1' . $resultid[$i]["id"] . '" id="message1' . $resultid[$i]["id"] . '" ' . $rows_cols . '>' . $result . '</textarea>
											<input style="display:none;" id="save' . $resultid[$i]["id"] . '" name="save" class="uk-button uk-button-success wk-button wk-button-success guru-comment-save" type="button" data-id="' . $lesson_id . '" data-result-id="' . $resultid[$i]["id"] . '" value="' . JText::_('GURU_SAVE') . '" />

											<div class="guru-lesson-comment-meta">
												<span class="guru-lesson-comment-time">' . $difference . '</span>';

								if($user_id == $resultid[$i]["userid"]){
									if($allow_delete == 0){
										$html .= '<span><a class="guru-comment-delete" data-id="' . $lesson_id . '" data-result-id="' . $resultid[$i]['id'] . '" data-result-user="' . $resultid[$i]['userid'] . '" href="javascript:"><i class="fontello-trash"></i></a></span>';
									}
									if($allow_edit == 0){
										$html .= '<span><a class="guru-comment-edit" data-id="' . $lesson_id . '" data-resultid="' . $resultid[$i]['id'] . '" href="javascript:"><i class="fontello-pencil"></i></a></span>';
									}
								}

								$html .= '</div>
										</div>
									</div>
									</div>';

								$html .= '</div></div>';
							}

						}//end allow_stud
					}//end if you are login
				}//end if we have category created for the lesson
			}// end if the plugin  is enabled
		}// end if you come from module page 

		return $html;
	}

	function insertComment() {
		$datamessage['lessonid'] = JFactory::getApplication()->input->get("lessonid", "");
		$data_request = JFactory::getApplication()->input->get->getArray();
		$datamessage['message'] = urldecode($data_request["message"]);
		
		$db = JFactory::getDBO();
		$config = JFactory::getConfig();
		$user = JFactory::getUser();
		
		//------------------------------------------------------------------
		$sql = "select type_id from #__guru_mediarel where media_id=".intval($datamessage['lessonid'])." and type='dtask'";
		$db->setQuery($sql);
		$modul_id = $db->loadColumn();
		$modul_id = @$modul_id["0"];
		
		$sql = "select pid from #__guru_days where id=".intval($modul_id);
		$db->setQuery($sql);
		$course_id = $db->loadColumn();
		$course_id = @$course_id["0"];
		
		$db->setQuery("SELECT `kunena_category` FROM #__guru_kunena_forum WHERE id=1");
		$kunena_category = $db->loadColumn();
		$kunena_category = @$kunena_category["0"];

		if(intval($kunena_category) == 0){
			$nameofmainforum = JText::_('GURU_TREECOURSE');
		}
		else{
			$sql = "SELECT `name` FROM #__kunena_categories WHERE id='".intval($kunena_category)."'";
			$db->setQuery($sql);
			$nameofmainforum = $db->loadResult();
		}
						
		$sql = "SELECT name FROM #__kunena_categories WHERE name='".addslashes($nameofmainforum)."'";
		$db->setQuery($sql);
		$result = $db->loadResult();

		if(isset($result)){
			$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES (".intval($kunena_category).", '".$db->escape($nameofmainforum)."', 'course', 0, 0, 'joomla.level', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
			$db->setQuery($sql);
			$db->execute($sql);
		}
		
		$sql = "SELECT name from #__guru_program where id =".intval($course_id);
		$db->setQuery($sql);
		$coursename = $db->loadResult();
		
		$sql = "SELECT alias from #__guru_program where id=".intval($course_id);
		$db->setQuery($sql);
		$aliascourse = $db->loadColumn();
		$aliascourse = @$aliascourse["0"];
		
		$sql = "SELECT id FROM #__kunena_categories WHERE name='".addslashes($nameofmainforum)."'";
		$db->setQuery($sql);
		$idmainforum= $db->loadResult();
		
		$sql = "SELECT name FROM #__kunena_categories WHERE parent_id='".$idmainforum."' and name='".addslashes($coursename)."'";
		$db->setQuery($sql);
		$result1 = $db->loadColumn();
		$result1 = @$result1["0"];

		if(!isset($result1)){
			$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( '".$idmainforum."', '".addslashes($coursename)."', '".$aliascourse."', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
			$db->setQuery($sql);
			$db->execute($sql);
		}
		
		$sql = "SELECT id FROM #__kunena_categories WHERE  name='".addslashes($coursename)."'";
		$db->setQuery($sql);
		$resultid = $db->loadResult();
		
		$sql = "SELECT title from #__guru_days where pid =".intval($course_id)." and id IN (SELECT type_id FROM #__guru_mediarel WHERE media_id=".$datamessage['lessonid'].")";
		$db->setQuery($sql);
		$modulename = $db->loadResult();

		$sql = "SELECT alias from #__guru_days where pid=".intval($course_id)." and title='".$db->escape($modulename)."'";
		$db->setQuery($sql);
		$aliasmodule = $db->loadColumn();
		$aliasmodule = @$aliasmodule["0"];
		
		$sql = "SELECT id FROM #__kunena_categories WHERE alias ='".$aliascourse."'";
		$db->setQuery($sql);
		$idmaincourse = $db->loadResult();
		
		$sql = "SELECT name FROM #__kunena_categories WHERE parent_id='".$idmaincourse."' and name='".addslashes($modulename."-".intval($course_id))."'";
		$db->setQuery($sql);
		$resultmodule = $db->loadColumn();
		$resultmodule = @$resultmodule["0"];
		
		if(!isset($resultmodule)){
			$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( '".$idmaincourse."', '".addslashes($modulename."-".intval($course_id))."', '".$aliasmodule."-".intval($course_id)."', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
			$db->setQuery($sql);
			$db->execute($sql);	
			
			$sql = "SELECT id FROM #__kunena_categories WHERE name='".addslashes($modulename."-".intval($course_id))."'";
			$db->setQuery($sql);
			$resultidmodule = $db->loadResult();

			if(trim($aliasmodule) != ""){
				$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES (  '".$aliasmodule."-".intval($course_id)."', 'catid', ".$resultidmodule.", 0)";
				$db->setQuery($sql);
				$db->execute();
			}
		}
		
		$sql = "SELECT id FROM #__kunena_categories WHERE  name='".addslashes($coursename)."'";
		$db->setQuery($sql);
		$resultid = $db->loadResult();
		$count_c = @count($resultid);
		
		$sql = "SELECT id FROM #__kunena_categories WHERE name='".addslashes($modulename."-".intval($course_id))."'";
		$db->setQuery($sql);
		$resultidmodule = $db->loadResult();
		$count_m = @count($resultidmodule);

		$sql = "INSERT INTO #__guru_kunena_courseslinkage (idcourse, coursename, catidkunena) VALUES (  '".$course_id."', '".addslashes($coursename)."', '".$resultid."')";
		$db->setQuery($sql);
		$db->execute($sql);
		
		$sql = "SELECT alias, name from #__guru_task where id =".intval($datamessage['lessonid']);
		$db->setQuery($sql);
		$lesson_details = $db->loadAssocList();
		$aliaslesson = $lesson_details["0"]["alias"];
		$lesson_name = $lesson_details["0"]["name"];
		
		$sql = "SELECT name FROM #__kunena_categories WHERE alias='".$db->escape($aliaslesson."-".intval($datamessage['lessonid']))."'";
		$db->setQuery($sql);
		$result2 = $db->loadColumn();
		$result2 = @$result2["0"];

		if(!isset($result2)){
			$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( ".$resultidmodule.", '".addslashes($lesson_name)."', '".$db->escape($aliaslesson."-".intval($datamessage['lessonid']))."', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
			$db->setQuery($sql);
			$db->execute($sql);
			
			$sql = "SELECT id FROM #__kunena_categories WHERE alias='".$db->escape($aliaslesson."-".intval($datamessage['lessonid']))."'";
			$db->setQuery($sql);
			$resultidlesson = $db->loadResult();
			
			if(trim($aliaslesson) != ""){
				$sql = "select count(*) from #__kunena_aliases where alias='".$db->escape($aliaslesson."-".intval($datamessage['lessonid']))."' and type='catid' and item='".intval($resultidlesson)."'";
				$db->setQuery($sql);
				$count = $db->loadColumn();
				$count = @$count["0"];
				
				if(intval($count) == 0){
					$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('".$db->escape($aliaslesson."-".intval($datamessage['lessonid']))."', 'catid', '".intval($resultidlesson)."', 0)";
					$db->setQuery($sql);
					$db->execute($sql);
				}
			}
		}
		//------------------------------------------------------------------
		
		$sql = "SELECT max(thread) from #__kunena_messages";
		$db->setQuery($sql);
		$threadid = $db->loadResult();
		
		$sql = "select allow_edit from #__guru_kunena_forum where id=1";
		$db->setQuery($sql);
		$allow_edit = $db->loadResult();
		
		$sql = "select allow_delete  from #__guru_kunena_forum where id=1";
		$db->setQuery($sql);
		$allow_delete = $db->loadResult();
		
		$sql = "SELECT count(id) FROM  #__guru_task_kunenacomment WHERE id_lesson=".intval($datamessage['lessonid']);
		$db->setQuery($sql);
		$count = $db->loadResult();
		
		if($count == 0){
			$sql = "INSERT INTO #__guru_task_kunenacomment (id_lesson, thread) VALUES (".$datamessage['lessonid'].", ".($threadid+1).")";
			$db->setQuery($sql);
			$db->execute();
		}
		
		$sql = "SELECT name from #__guru_task WHERE id=".intval($datamessage['lessonid']);
		$db->setQuery($sql);
		$name = $db->loadResult();

		$sql = "SELECT id from #__kunena_categories WHERE `alias`='".$aliaslesson."-".intval($datamessage['lessonid'])."'";
		$db->setQuery($sql);
		$idcat = $db->loadResult();
		
		$sql = "SELECT count(id) FROM #__kunena_messages WHERE subject='".$name."' and `catid`=".intval($idcat);
		$db->setQuery($sql);
		$count2 = $db->loadResult();
		
		$jnow = new JDate('now');
		$currentdate = $jnow->toSQL(true);
		
		$sql = "SELECT id from #__kunena_messages WHERE subject ='".$name."' and parent = 0 and `catid`=".intval($idcat);
		$db->setQuery($sql);
		$idparent = $db->loadResult();
		
		if($count2 == 0){
			$sql = "INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, 0, ".(empty($idcat) ? 0 : $idcat).", '".$user->name."', ".$user->id.", '', '".$name."',  '".strtotime($currentdate)."', '127.0.0.1', 0,0,0,0,0,0,'','','')";
			$db->setQuery($sql);
			$db->execute();
		}
		
		$sql = "SELECT id from #__kunena_messages WHERE subject ='".$name."' and `catid`=".intval($idcat);
		$db->setQuery($sql);
		$idparent = $db->loadResult();
		
		$sql = "UPDATE  #__kunena_messages set thread ='".$idparent."' where parent= 0 and subject ='".$name."' and `catid`=".intval($idcat);
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "SELECT id from #__kunena_categories WHERE `alias`='".$aliaslesson."-".intval($datamessage['lessonid'])."'";
		$db->setQuery($sql);
		$idcat = $db->loadResult();
		
		if($idcat == "" || $idcat == 0){
			$idcat = 1;
		}
		
		if($count2 > 0){
			$sql = "INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (".$idparent.", 0, ".$idcat.", '".$user->name."', ".$user->id.", '', '".$name."',  '".strtotime($currentdate)."', '127.0.0.1', 0,0,0,0,0,0,'','','')";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "SELECT thread from #__kunena_messages WHERE subject ='".$name."' and parent = 0 and `catid`=".intval($idcat);
			$db->setQuery($sql);
			$thread = $db->loadResult();
			
			$sql = "UPDATE  #__kunena_messages set thread ='".$thread."' where parent =".$idparent." and subject ='".$name."' and `catid`=".intval($idcat);
			$db->setQuery($sql);
			$db->execute();
		}
		
		$sql = "SELECT id from #__kunena_messages WHERE subject ='".$name."' and `catid`=".intval($idcat)." order by id desc limit 0,1 ";
		$db->setQuery($sql);
		$idmessage = $db->loadColumn();
		$idmessage = $idmessage["0"];
		
		$sql = "INSERT INTO #__kunena_messages_text (mesid, message) VALUES (".$idmessage.",'".$db->escape(trim($datamessage['message']))."' )";
		$db->setQuery($sql);
		$db->execute();
		
		if(isset($idcat) &&  $idcat != 0){
			$sql = "SELECT count(id) FROM  #__kunena_topics WHERE subject='".$name."' and `category_id`=".intval($idcat);
			$db->setQuery($sql);
			$count3 = $db->loadResult();
			
			if($count3 == 0){
				$sql = "INSERT INTO #__kunena_topics ( category_id, subject, icon_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, params) VALUES (".$idcat.", '".$name."', 0, 0,0,0,1,0,0,0,0,".$idmessage.",'".strtotime($currentdate)."', ".$user->id.", '".addslashes(trim($datamessage['message']))."', '".$user->username."', ".$idmessage.",'".strtotime($currentdate)."',".$user->id.",'".addslashes(trim($datamessage['message']))."','".$user->username."','')";
				$db->setQuery($sql);
				$db->execute();
			}
			else{
				$sql = "SELECT posts from #__kunena_topics WHERE subject ='".$name."' and `category_id`=".intval($idcat)." order by id desc limit 0,1 ";
				$db->setQuery($sql);
				$posts = $db->loadResult();
				
				$subquery = "SELECT  first_post_id from #__kunena_topics WHERE subject ='".$name."' and `category_id`=".intval($idcat)." order by id desc limit 0,1";
				$db->setQuery($subquery);
				$subquery = $db->loadColumn();
				$subquery = implode(",",$subquery);
				
				$sql = "SELECT count(id) from #__kunena_categories WHERE id IN(".$subquery.")";
				$db->setQuery($sql);
				$count_firstpost = $db->loadResult();
				
				if($count_firstpost == 0){
					$sql = "UPDATE #__kunena_topics set  posts=".($posts +1).", first_post_id=".$idmessage.",last_post_id=".$idmessage.", last_post_time='".strtotime($currentdate)."', last_post_userid=".$user->id.", last_post_message='".addslashes(trim($datamessage['message']))."', last_post_guest_name='".$user->username."' WHERE subject='".$name."' and `category_id`=".intval($idcat);
					$db->setQuery($sql);
					$db->execute();
				}
				else{
					$sql = "UPDATE #__kunena_topics set  posts=".($posts +1).", last_post_id=".$idmessage.", last_post_time='".strtotime($currentdate)."', last_post_userid=".$user->id.", last_post_message='".addslashes(trim($datamessage['message']))."', last_post_guest_name='".$user->username."' WHERE subject='".$name."' and `category_id`=".intval($idcat);
					$db->setQuery($sql);
					$db->execute();
				}
			
			}
		}
		
		$sql = "SELECT id FROM  #__kunena_topics WHERE subject='".$name."' and `category_id`=".intval($idcat);
		$db->setQuery($sql);
		$idtopic = $db->loadResult();
		
		$sql = "SELECT count(*) from #__kunena_user_topics WHERE user_id =".$user->id." and  topic_id =".$idtopic;
		$db->setQuery($sql);
		$counttopics = $db->loadResult();
		
		if($counttopics == 0){
			$sql = "INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params)  VALUES (".$user->id.", '".$idtopic."', ".$idcat.", 1,".$idmessage.",1,0,1,'')";
			$db->setQuery($sql);
			$db->execute();
		}
		else{
			$sql = "UPDATE #__kunena_user_topics set  posts=".($posts +1).", last_post_id=".$idmessage." WHERE user_id ='".$user->id."' and  topic_id =".$idtopic;
			$db->setQuery($sql);
			$db->execute();
		}
		
		$sql = "UPDATE  #__kunena_messages set thread ='".$idtopic."' where subject ='".$name."' and `catid`=".intval($idcat);
		$db->setQuery($sql);
		$db->execute();

		$sql = "UPDATE  #__kunena_categories set last_topic_id ='".intval($idtopic)."' ,last_post_id=".intval($idmessage)."  where id =".$idcat." and name ='".$db->escape($name)."' ";
		$db->setQuery($sql);
		$db->execute();		

		// build html
		$sql ="select id, name, userid, email from #__kunena_messages WHERE subject='".$name."' and `catid`=".intval($idcat)." order by id desc";
		$db->setQuery($sql);
		$resultid = $db->loadAssocList();
		
		$jnow = new JDate('now');
		$date_currentk = $jnow->toSQL(true);									
		$int_current_datek = strtotime($date_currentk);
		
		$sql ="select id from #__kunena_categories WHERE name='".$name."' order by id desc limit 0,1";
		$db->setQuery($sql);
		$catkunena = $db->loadResult();
		
		$sql ="select id from #__kunena_topics WHERE subject='".$name."' and `category_id`=".intval($idcat)." order by id asc limit 0,1";
		$db->setQuery($sql);

		$idmess = $db->loadResult();
		$html = '';
		
		for($i=0; $i < count($resultid); $i++){
			if ( $resultid[$i]["id"] != $idmessage ) {
				continue;
			}

			$sql = "select message from #__kunena_messages_text WHERE mesid=".$resultid[$i]["id"];
			$db->setQuery($sql);
			$result = $db->loadResult();

			$sql = "select time from #__kunena_messages WHERE id=".$resultid[$i]["id"];
			$db->setQuery($sql);
			$datestart = $db->loadResult();

			$model_task = new guruModelguruTask();
			$timepast = $model_task->get_time_difference($datestart,$int_current_datek);

			if($timepast["days"] == 0){
				if($timepast["hours"] == 0){
					if($timepast["minutes"] == 0){
						$difference = "a few seconds ago";
					}
					else{
						$difference = $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
					}
				}
				else{
					$difference = $timepast["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
								  $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
				}
			}
			else{
				$difference = $timepast["days"]." ".JText::_("GURU_REAL_DAYS").", ".
							  $difference = $timepast["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
							  $timepast["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
			}

			if(/*$deviceType !='phone'*/ TRUE){
				$rows_cols = ' rows="2" cols="90"';
				$style = 'style="width:100%"';
			}
			else{
				$rows_cols = 'rows="3"';
				$style = 'style="width:50%"';
			}

			$html .= '<div class="guru-lesson-comment my-message">';
			
			$sql = "select image from #__guru_customer where id=".intval($resultid[$i]["userid"]);
			$db->setQuery($sql);
			$student_image = $db->loadColumn();
			$student_image = @$student_image["0"];
			
			if(!isset($student_image) || trim($student_image) == ""){
				$sql = "select images from #__guru_authors where userid=".intval($resultid[$i]["userid"]);
				$db->setQuery($sql);
				$student_image = $db->loadColumn();
				$student_image = @$student_image["0"];
			}
			
			$url_image = "http://www.gravatar.com/avatar/".md5(strtolower(trim($user->email)))."?d=mm&s=40";
			
			if(trim($student_image) != "" && trim($student_image) != "/"){
				$url_image = JURI::root().$student_image;
			}

			$html .= '
				<div class="guru-lesson-comment-wrap">
				<div class="guru-lesson-comment-avatar">
					<span style="background-image: url('.$url_image.');">
					</span>
				</div>
				<div class="guru-lesson-comment-body guru-reply-body">
					<a class="guru-lesson-comment-name" href="' . JRoute::_('index.php?option=com_kunena&view=topic&catid=' . $catkunena . '&id=' . $idmess) . '">' . $resultid[$i]["name"] . '</a>
					<span class="guru-text" id="gurupostcomment'.$resultid[$i]["id"].'">' . $result . '</span>
					<textarea style="display:none;" ' . $style . ' name="message1' . $resultid[$i]["id"] . '" id="message1' . $resultid[$i]["id"] . '" ' . $rows_cols . '>' . $result . '</textarea>
					<input style="display:none;" id="save' . $resultid[$i]["id"] . '" name="save" class="uk-button uk-button-success wk-button wk-button-success guru-comment-save" type="button" data-id="' . $datamessage['lessonid'] . '" data-result-id="' . $resultid[$i]["id"] . '" value="' . JText::_('GURU_SAVE') . '" />
					<div class="guru-lesson-comment-meta">
						<span class="guru-lesson-comment-time">' . $difference . '</span>';
						if($allow_delete == 0){
							$html .= '<span><a class="guru-comment-delete" data-id="' . $datamessage['lessonid'] . '" data-result-id="' . $resultid[$i]['id'] . '" data-result-user="' . $resultid[$i]['userid'] . '" href="javascript:"><i class="fontello-trash"></i></a></span>';
						}
						if($allow_edit == 0){
							$html .= '<span><a class="guru-comment-edit" data-id="' . $datamessage['lessonid'] . '" data-resultid="' . $resultid[$i]['id'] . '" href="javascript:"><i class="fontello-pencil"></i></a></span>';
						}
			$html .= '</div></div></div></div>';
		}

		die( json_encode( array( 'success' => TRUE, 'html' => $html ) ) );
	}

	function editComment() {
		$db = JFactory::getDBO();
		$comid = JFactory::getApplication()->input->get("comid", "");
		$user = JFactory::getUser();
		$message = urldecode(JFactory::getApplication()->input->get("message", ""));

		$sql = "UPDATE #__kunena_messages_text set message= '".$message."' WHERE mesid =".$comid."";
		$db->setQuery($sql);
		$db->execute();

		die( json_encode( array( 'success' => TRUE ) ) );
	}

	function deleteComment() {
		$db = JFactory::getDBO();
		$comid = JFactory::getApplication()->input->get("comid", "");
		$uid = JFactory::getApplication()->input->get("uid", "");
		$lid = JFactory::getApplication()->input->get("lessonid", "");
		$user = JFactory::getUser();
		
		$jnow = new JDate('now');
		$date_currentk = $jnow->toSQL(true);									
		$int_current_datek = strtotime($date_currentk);
		
		$sql = "SELECT name from #__guru_task WHERE id=".intval($lid);
		$db->setQuery($sql);
		$name = $db->loadResult();
		
		$sql = "DELETE from #__kunena_messages WHERE id =".$comid." and userid=".$uid." ";
		$db->setQuery($sql);
		$db->execute();

		$sql = "SELECT id FROM  #__kunena_topics WHERE subject='".$name."'";
		$db->setQuery($sql);
		$idtopic = $db->loadResult();
		
		$sql = "SELECT id from #__kunena_messages WHERE subject ='".$name."' order by id desc limit 0,1 ";
		$db->setQuery($sql);
		$idmessage = $db->loadResult();
		
		$sql = "SELECT id from #__kunena_categories WHERE name ='".$name."'";
		$db->setQuery($sql);
		$idcat = $db->loadResult();

		$sql = "UPDATE  #__kunena_categories set last_topic_id ='".intval($idtopic)."' ,last_post_id=".intval($idmessage).", last_post_time='".$int_current_datek ."'  where id =".$idcat." and name ='".$db->escape($name)."' ";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "UPDATE  #__kunena_topics set first_post_id ='".$idmessage."' where id =".$idtopic." ";
		$db->setQuery($sql);
		$db->execute();

		die( json_encode( array( 'success' => TRUE ) ) );
	}

	function programInfo($pid) {
		$db = JFactory::getDBO();
		$sql = "SELECT skip_module, course_type from #__guru_program WHERE id = " . $pid;
		$db->setQuery($sql);
		$result = $db->loadAssoc();

		return $result;
	}

	function saveDateTimeLesson(){
		$dif_hour = JFactory::getApplication()->input->get("dif_hour", 0, "raw");
    	$dif_min = JFactory::getApplication()->input->get("dif_min", 0, "raw");
    	$dif_sec = JFactory::getApplication()->input->get("dif_sec", 0, "raw");
    	$lesson_id = JFactory::getApplication()->input->get("lesson_id", 0, "raw");
    	$user = JFactory::getUser();

    	if(intval($dif_hour) < 10){
    		$dif_hour = "0".$dif_hour;
    	}

    	if(intval($dif_min) < 10){
    		$dif_min = "0".$dif_min;
    	}

    	if(intval($dif_sec) < 10){
    		$dif_sec = "0".$dif_sec;
    	}

    	if(intval($user->id) > 0 && intval($lesson_id) > 0){
    		$new_time = "00:00:00";
    		$db = JFactory::getDbo();
    		$sql = "select `viewed_time` from #__guru_viewed_lesson_time where `user_id`=".intval($user->id)." and `lesson_id`=".intval($lesson_id);
    		$db->setQuery($sql);
    		$viewed_time = $db->loadColumn();
    		$viewed_time = @$viewed_time["0"];

    		if(trim($viewed_time) == "" || trim($viewed_time) == "00:00:00"){
    			$new_time = $dif_hour.":".$dif_min.":".$dif_sec;

    			$sql = "insert into #__guru_viewed_lesson_time (`user_id`, `lesson_id`, `viewed_time`) values ('".intval($user->id)."', '".intval($lesson_id)."', '".$new_time."')";
	    		$db->setQuery($sql);
	    		$db->execute();
    		}
    		else{
    			$saved_time = explode(":", $viewed_time);
    			$saved_sec = $saved_time["2"];
    			$saved_min = $saved_time["1"];
    			$saved_hour = $saved_time["0"];

    			$saved_sec = intval($saved_sec) + intval($dif_sec);
    			$saved_min = intval($saved_min) + intval($dif_min);
    			$saved_hour = intval($saved_hour) + intval($dif_hour);

    			$saved_sec_min = $saved_sec / 60;
    			$saved_sec_sec = $saved_sec % 60;

    			if($saved_sec_min > 0){
    				$saved_min += floor($saved_sec_min);
    				$saved_sec = $saved_sec_sec;
    			}

    			$saved_min_hour = $saved_min / 60;
    			$saved_min_min = $saved_min % 60;

    			if($saved_min_hour > 0){
    				$saved_hour += floor($saved_min_hour);
    				$saved_min = $saved_min_min;
    			}

    			if(intval($saved_hour) < 10){
		    		$saved_hour = "0".$saved_hour;
		    	}

		    	if(intval($saved_min) < 10){
		    		$saved_min = "0".$saved_min;
		    	}

		    	if(intval($saved_sec) < 10){
		    		$saved_sec = "0".$saved_sec;
		    	}

    			$new_time = $saved_hour.":".$saved_min.":".$saved_sec;

    			$sql = "update #__guru_viewed_lesson_time set `viewed_time`='".$new_time."' where `user_id`=".intval($user->id)." and `lesson_id`=".intval($lesson_id);
	    		$db->setQuery($sql);
	    		$db->execute();
    		}
    	}

    	die();
	}

	function saveDateTime(){
		$dif_hour = JFactory::getApplication()->input->get("dif_hour", 0, "raw");
    	$dif_min = JFactory::getApplication()->input->get("dif_min", 0, "raw");
    	$dif_sec = JFactory::getApplication()->input->get("dif_sec", 0, "raw");
    	$course_id = JFactory::getApplication()->input->get("course_id", 0, "raw");
    	$user = JFactory::getUser();

    	if(intval($dif_hour) < 10){
    		$dif_hour = "0".$dif_hour;
    	}

    	if(intval($dif_min) < 10){
    		$dif_min = "0".$dif_min;
    	}

    	if(intval($dif_sec) < 10){
    		$dif_sec = "0".$dif_sec;
    	}

    	if(intval($user->id) > 0 && intval($course_id) > 0){
    		$new_time = "00:00:00";
    		$db = JFactory::getDbo();
    		$sql = "select `viewed_time` from #__guru_viewed_lesson where `user_id`=".intval($user->id)." and `pid`=".intval($course_id);
    		$db->setQuery($sql);
    		$viewed_time = $db->loadColumn();
    		$viewed_time = @$viewed_time["0"];

    		if(trim($viewed_time) == "" || trim($viewed_time) == "00:00:00"){
    			$new_time = $dif_hour.":".$dif_min.":".$dif_sec;
    		}
    		else{
    			$saved_time = explode(":", $viewed_time);
    			$saved_sec = $saved_time["2"];
    			$saved_min = $saved_time["1"];
    			$saved_hour = $saved_time["0"];

    			$saved_sec = intval($saved_sec) + intval($dif_sec);
    			$saved_min = intval($saved_min) + intval($dif_min);
    			$saved_hour = intval($saved_hour) + intval($dif_hour);

    			$saved_sec_min = $saved_sec / 60;
    			$saved_sec_sec = $saved_sec % 60;

    			if($saved_sec_min > 0){
    				$saved_min += floor($saved_sec_min);
    				$saved_sec = $saved_sec_sec;
    			}

    			$saved_min_hour = $saved_min / 60;
    			$saved_min_min = $saved_min % 60;

    			if($saved_min_hour > 0){
    				$saved_hour += floor($saved_min_hour);
    				$saved_min = $saved_min_min;
    			}

    			if(intval($saved_hour) < 10){
		    		$saved_hour = "0".$saved_hour;
		    	}

		    	if(intval($saved_min) < 10){
		    		$saved_min = "0".$saved_min;
		    	}

		    	if(intval($saved_sec) < 10){
		    		$saved_sec = "0".$saved_sec;
		    	}

    			$new_time = $saved_hour.":".$saved_min.":".$saved_sec;
    		}

    		$sql = "update #__guru_viewed_lesson set `viewed_time`='".$new_time."' where `user_id`=".intval($user->id)." and `pid`=".intval($course_id);
    		$db->setQuery($sql);
    		$db->execute();
    	}

    	die();
	}

	function setLessonViewed(){
		$model = $this->getModel("guruTask");
		$lesson_id = JFactory::getApplication()->input->get('lesson_id', '0', "raw");
		$pid = JFactory::getApplication()->input->get('pid', '0', "raw");
		$model->saveLessonViewed($lesson_id, $pid);
		die();
	}

	function setLessonNotViewed(){
		$model = $this->getModel("guruTask");
		$lesson_id = JFactory::getApplication()->input->get('lesson_id', '0', "raw");
		$pid = JFactory::getApplication()->input->get('pid', '0', "raw");
		$model->setLessonNotViewed($lesson_id, $pid);
		die();
	}

	function checkCourseCompleted(){
		$course_id = JFactory::getApplication()->input->get('course_id', '0', "raw");

		if(intval($course_id) > 0){
			$db = JFactory::getDbo();
			$user = JFactory::getUser();

			$sql = "select `date_completed` from #__guru_viewed_lesson where `user_id`=".intval($user->id)." and `pid`=".intval($course_id);
			$db->setQuery($sql);
			$date_completed = $db->loadColumn();

			if(isset($date_completed) && isset($date_completed["0"]) && trim($date_completed["0"]) != "0000-00-00" && trim($date_completed["0"]) != "0000-00-00 00:00:00"){
				die("true");
			}
			else{
				die("false");
			}
		}
		else{
			die("false");
		}
	}

	function scorm(){
		$view = $this->getView("guruTasks", "html");
		$view->setLayout("scorm");
		$view->setModel($this->_model, true);	
		$view->scorm();
	}

};

?>