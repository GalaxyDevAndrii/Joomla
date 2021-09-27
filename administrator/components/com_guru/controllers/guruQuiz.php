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

class guruAdminControllerguruQuiz extends guruAdminController{
	var $_model = null;
	
	function __construct(){
		parent::__construct();
		$this->registerTask ("", "listQuiz");
		$this->registerTask ("listQuizStud", "listQuizStud");
		$this->registerTask ("show_quizz_res", "show_quizz_res");
		$this->registerTask ("listStudentsQuizTaken", "listStudentsQuizTaken");
		$this->registerTask("export", "exportFile");
		$this->registerTask("exportpdf", "exportFilePdf");
		$this->registerTask("orderup", "saveorder");
        $this->registerTask("orderdown", "saveorder");
		$this->registerTask("deletequizresult", "deletequizresult");
		$this->registerTask ("saveOrderQuestions", "saveOrderQuestions");
		$this->registerTask ("ajax_request", "ajax_request");
		$this->registerTask ("savequestionandkeep", "saveQuestionandkeep");

		$this->_model = $this->getModel("guruQuiz");
	}

	function listQuiz(){
		$view = $this->getView("guruQuiz", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}
	function listQuizStud(){
		$view = $this->getView("guruQuiz", "html");
		$view->setLayout("listQuizStud");
		$view->setModel($this->_model, true);
		$view->listQuizStud();
	}
	function listStudentsQuizTaken(){
		$view = $this->getView("guruQuiz", "html");
		$view->setModel($this->_model, true);
		$view->setLayout("liststudentsquiztaken");
		$view->listStudentsQuizTaken();
	}
	function show_quizz_res(){
		$view = $this->getView("guruQuiz", "html");
		$view->setLayout("show_quizz_res");
		$view->setModel($this->_model, true);
		$view->show_quizz_res();
	}

	function editZ(){
		$ids = JFactory::getApplication()->input->get('cid',array(), "raw");	
		$view = $this->getView("guruQuiz", "html");
		$view->setModel($this->_model, true);
		$view->setLayout("settype");
		$view->settypeform();
	}

	function edit () {
		$view = $this->getView("guruQuiz", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->editForm();
	}
	
	function editsboxx(){
		$view = $this->getView("guruQuiz", "html");
		$view->setLayout("editFormsbox");
		$view->setModel($this->_model, true);
		$view->editForm();
	}

	function save(){
		if($this->_model->store()){
			$msg = JText::_('GURU_QSAVED');
		} 
		else{
			$msg = JText::_('GURU_QNOTSAVED');
		}
		$link = "index.php?option=com_guru&controller=guruQuiz";
		$this->setRedirect($link, $msg);
	}	
	
	function deletequizresult(){
		if($this->_model->removequizresults()){
			$msg = JText::_('GURU_MODIF_OK');
		} 
		else{
			$msg = JText::_('GURU_ORDFAILED');
		}
		$link = "index.php?option=com_guru&controller=guruQuiz";
		$this->setRedirect($link, $msg);
	
	}	
	function savesbox () {
		JFactory::getApplication()->input->set("tmpl", "component");
		$id	= JFactory::getApplication()->input->get("id","0");
		$screen	= 12;
		?>
							
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
  					document.getElementsByTagName("head")[0].appendChild(fileref);
				}

				function loadprototipe(){
					//WE ARE NOT LONGER BEEN USING AJAX FROM prototype-1.6.0.2.js, INSTEAD WE WILL BE USING jQuery.ajax({}) function
					//loadjscssfile("<?php echo JURI::base().'components/com_guru/views/gurutasks/tmpl/prototype-1.6.0.2.js' ?>","js");
				}

				function addmedia (idu, name, asoc_file, description) {
					loadprototipe();
							
					var url = 'index.php?option=com_guru&controller=guruTasks&tmpl=component&format=raw&task=ajax_request2&id='+idu+'&type=quiz';
					jQuery.ajax({ url: url,
  						method: 'get',
  						asynchronous: 'true',
  						success: function(transport) {
									
						to_be_replaced=parent.document.getElementById('media_15');
						replace_m=15;
						to_be_replaced.innerHTML = '&nbsp;';
				
						to_be_replaced.innerHTML += transport.trim();
						parent.document.getElementById("media_"+99).style.display="";
						parent.document.getElementById("description_med_99").innerHTML=''+name;
							
						parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
						parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
						parent.document.getElementById('db_media_'+replace_m).value = idu;
		
						//screen_id = document.getElementById('the_screen_id').value;
						
						replace_edit_link = parent.document.getElementById('a_edit_media_'+replace_m);
						replace_edit_link.href = 'index.php?option=com_guru&controller=guruQuiz&tmpl=component&task=editsboxx&cid[]='+ idu;
						var qwe='&nbsp;'+transport.trim()+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
								
						window.parent.test(replace_m, idu,qwe);
  					},
				});
				setTimeout('window.parent.document.getElementById("close").click()',1000);
				return true;
			}
			</script>
		<?php
		
		if($id==0){
			if($id=$this->_model->store()){
				$quiz=$this->_model->getQuizById();
				echo '<script type="text/javascript">window.onload=function(){
					loadprototipe();
					var t=setTimeout(\'addmedia('.$quiz->id.', "'.$quiz->name.'","-", "'.$quiz->description.'");\',1000);						
			}</script>';
				echo '<strong>Saving quiz, please wait...</strong>';
			}		
		}
		else{
			if($id=$this->_model->store()){
				$msg = JText::_('GURU_MEDIASAVED');
				$quiz=$this->_model->getQuizById();
				echo '<script type="text/javascript">window.onload=function(){
					loadprototipe();
					var t=setTimeout(\'addmedia('.$quiz->id.', "'.$quiz->name.'","-", "'.$quiz->description.'");\',1000);						
			}</script>';
				echo '<strong>Media saved. Please wait...</strong>';
			}
			else {
				$msg = JText::_('GURU_MEDIASAVEDFAILED');
			}
			echo '<script type="text/javascript">window.onload=function(){
				var t=setTimeout(\'window.parent.document.getElementById("sbox-window").close();\',0);
				window.parent.page_refresh('.$screen.');
			}</script>';
			echo '<strong>'.JText::_('GURU_QUIZSAVED_PLSWAIT').'</strong>';
		}
	}	
	
	function apply(){
		$id = JFactory::getApplication()->input->get("cid", array(), "raw");

		if(is_array($id)){
			$id = $id["0"];
		}

		if($this->_model->store()){
			$msg = JText::_('GURU_QAPPLY');
		}
		else{
			$msg = JText::_('GURU_QAPPLYFAILED');
		}
		
		$valueop = JFactory::getApplication()->input->post->getArray();
		
		if($id == 0){
			$db =  JFactory::getDBO();
			$sql = "SELECT max(id) FROM #__guru_quiz ";
			$db->setQuery($sql);
			$new_quiz_id = $db->loadColumn();
			$id = $new_quiz_id[0];
		}	
		
		if($valueop['valueop'] == 1){
			$link = "index.php?option=com_guru&controller=guruQuiz&task=edit&cid[]=".$id."&v=1&e=1";
		}
		else{
			$link = "index.php?option=com_guru&controller=guruQuiz&task=edit&cid[]=".$id;
		}
		
		$this->setRedirect($link, $msg);
	}
	
	function apply_qq(){
		$id = JFactory::getApplication()->input->get("id","0");
		if($this->_model->store()){
			$msg = JText::_('GURU_QAPPLY');
		}
		else{
			$msg = JText::_('GURU_QAPPLYFAILED');
		}
		$valueop = JFactory::getApplication()->input->post->getArray();
		if($id == 0){
			$db =  JFactory::getDBO();
			$sql = "SELECT max(id) FROM #__guru_quiz ";
			$db->setQuery($sql);
			$new_quiz_id = $db->loadColumn();
			$id = $new_quiz_id[0];
		}	
		if($valueop['valueop'] == 1){
			$link = "index.php?option=com_guru&controller=guruQuiz&task=editsboxx&cid[]=".$id."&v=1&e=1&tmpl=component";
		}
		else{
			$link = "index.php?option=com_guru&controller=guruQuiz&task=edit&cid[]=".$id;
		}
		$this->setRedirect($link, $msg);
	}
	
	function apply_q(){
		$id = JFactory::getApplication()->input->get("id","0");
		if($this->_model->store()){
		}
		else {
		}
		$link = "index.php?option=com_guru&controller=guruQuiz&tb=q&task=edit&cid[]=".$id;
		$this->setRedirect($link);
	}
	
	function applynew () {
		if ($this->_model->store() ) {
			$db = JFactory::getDBO();
			$db->setQuery('SELECT id FROM #__guru_quiz ORDER BY id DESC LIMIT 1');
			$last=$db->loadColumn();
			$last = $last["0"];
		}
		else {
		}
		$link = "index.php?option=com_guru&controller=guruQuiz&task=edit&cid[]=".$last."&tb=q";
		$this->setRedirect($link);
	}
	
	function saveorder_q(){
		$id = JFactory::getApplication()->input->get("id","0");
		if($this->_model->saveorder_q()){
			$msg = JText::_('GURU_QSAVED');
		} 
		else{
			$msg = JText::_('GURU_QNOTSAVED');
		}
		$link = "index.php?option=com_guru&controller=guruQuiz&task=edit&cid[]=".$id;
		$this->setRedirect($link);
	}

	function upload(){
		$msg = $this->_model->upload();
		$view = $this->getView("guruQuiz", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->editForm();
	}

	function remove(){
		$notice = '';
		$return = $this->_model->delete();
		
		if($return === TRUE){
			$msg = JText::_('GURU_QREMSUCC');
			$notice = '';
		}
		elseif($return === FALSE){
			$msg = JText::_('GURU_QREMERR');
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$course_publ_status = $registry->get('is_atribuited', "");
			
			if($course_publ_status == 1){
				$msg = $msg.JText::_('GURU_FINAL_EXAM_NOT_REMOVE');
				$notice = 'error';
			}
		}
		elseif($return == "assigned"){
			$msg = JText::_('GURU_QUIZ_ASSIGNED_TO_LESSON');
			$notice = 'error';
		}
		
		$link = "index.php?option=com_guru&controller=guruQuiz";
		$this->setRedirect($link, $msg, $notice);
	}

	function cancel(){
		$this->_model->cancel();
	 	$msg = JText::_('GURU_QACTCANCEL');	
		$link = "index.php?option=com_guru&controller=guruQuiz";
		$this->setRedirect($link, $msg);
	}

	function publish(){ 
		$res = $this->_model->publish();
		if(!$res){
			$msg = JText::_('GURU_QPUBERR');
		}
		elseif($res == 1){
			$msg = JText::_('GURU_QPUB');
		} 
		elseif($res == 2){
			$msg = JText::_('GURU_FQPUB');
		}
		elseif($res == -1){
			$msg = JText::_('GURU_UNPUB2');
		}
		elseif($res == -2){
			$msg = JText::_('GURU_FUNPUB2');
		}
			
		$link = "index.php?option=com_guru&controller=guruQuiz";
		$this->setRedirect($link, $msg);
	}
		
	function unpublish(){
		$res = $this->_model->publish();
		if(!$res){
			$msg = JText::_('GURU_QPUBERR');
		}
		elseif($res == 1){
			$msg = JText::_('GURU_QPUB');
		} 
		elseif($res == 2){
			$msg = JText::_('GURU_FQPUB');
		}
		elseif($res == -1){
			$msg = JText::_('GURU_UNPUB2');
		}
		elseif($res == -2){
			$msg = JText::_('GURU_FUNPUB2');
		}
			
		$link = "index.php?option=com_guru&controller=guruQuiz";
		$this->setRedirect($link, $msg);
	}

	function copyquestion(){
		$qid = JFactory::getApplication()->input->get('qid', '0', "raw");
		
		if(!$this->_model->copyquestion()){
			$msg = JText::_('GURU_CAN_NOT_COPY_QUESTION');
		}
		else{
		 	$msg = JText::_('GURU_QUESTION_COPIED');
		}

		$link = "index.php?option=com_guru&controller=guruQuiz&task=edit&cid[]=".intval($qid);
		$this->setRedirect($link, $msg);
	}

	function addquestion(){
		$view = $this->getView("guruQuiz", "html");
		$view->setLayout("addquestion");
		$view->setModel($this->_model, true);
		$view->addquestion();
	}
	function addquizzes(){
		$view = $this->getView("guruQuiz", "html");
		$view->setLayout("addQuizzes");
		$view->setModel($this->_model, true);
		$view->addquizzes();
	}	
	function editquestion(){
		$view = $this->getView("guruQuiz", "html");
		$view->setLayout("editquestion");
		$view->setModel($this->_model, true);
		$view->editquestion();
	}	
	
	function savequestionandclose(){
		$qtext 	= JFactory::getApplication()->input->get('question_text', '', 'raw');
		$quizid = JFactory::getApplication()->input->get('quizid','0','post','int');
		$is_from_modal_lesson = JFactory::getApplication()->input->get('is_from_modal');
		$question_type = JFactory::getApplication()->input->get('type', "");
		$media_ids_question = JFactory::getApplication()->input->get('question_media_ids', "", "raw");
		$points = JFactory::getApplication()->input->get('question_weight_tf', "");
		$true_false_ch = JFactory::getApplication()->input->get('truefs_ans', "-1");
		$question_id = JFactory::getApplication()->input->get('question_id', "0");
		$from_save_or_not = "saveandclose";
		$ans_content = JFactory::getApplication()->input->get('ans_content','','raw');
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$registry->set('added_question', "1");
		
		$this->_model->addquestion($qtext,$quizid,$question_type,$media_ids_question,$points, $true_false_ch,$question_id,$from_save_or_not, $ans_content);

		if($quizid != 0){
			if($is_from_modal_lesson != 1){
				echo JText::_('GURU_SAVE_QUESTION_WAIT');
				echo '<script type="text/javascript">';
				echo 'if(window.parent.document.adminForm.name.value == ""){
							window.parent.location = parent.document.URL;	
					  }
					  else{
							window.parent.document.adminForm.task.value = "apply_qq";
							window.parent.document.adminForm.submit();
					  }';
				echo '</script>';
			
			}
			else{
				echo JText::_('GURU_SAVE_QUESTION_WAIT');
				echo '<script type="text/javascript">';
				echo 'window.parent.location = parent.document.URL';
				echo '</script>';
			}
			die();
			
		}
		
		if($quizid==0){
			if($is_from_modal_lesson != 1){
				echo JText::_('GURU_SAVE_QUESTION_WAIT');
				echo '<script type="text/javascript">';
				
				echo 'if(window.parent.document.adminForm.name.value == ""){
							//window.parent.location.reload();	
							window.parent.location = parent.document.URL;
					  }
					  else{
							window.parent.document.adminForm.task.value = "apply_qq";
							window.parent.document.adminForm.submit();
					  }';
				echo '</script>';
			
			}
			else{
				echo JText::_('GURU_SAVE_QUESTION_WAIT');
				echo '<script type="text/javascript">';
				
				echo 'window.parent.location = parent.document.URL';
				echo '</script>';
			}
			die();
		}
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
	
	function addmedia(){
		$app = JFactory::getApplication('administrator');
		$view = $this->getView("guruQuiz", "html");
		$view->setLayout("addmedia");
		$view->setModel($this->_model, true);
		$view->addmedia();
	}
	
	function savemedia(){
		$insertit 	= JFactory::getApplication()->input->get('idmedia','0');
		$taskid 	= JFactory::getApplication()->input->get('idtask','0');
		$mainmedia 	= JFactory::getApplication()->input->get('mainmedia','0');
		$this->_model->addmedia($insertit, $taskid, $mainmedia);
	}
	
	function del(){ 
		$tid = JFactory::getApplication()->input->get('tid','0'); 
		$cid = JFactory::getApplication()->input->get('cid',array(1), "raw");
		$cid = intval($cid[0]);
		if(!$this->_model->delmedia($tid,$cid)){
			$msg = JText::_('GURU_MEDIA_CANTBE_REMOVED');
		}
		else{
		 	$msg = JText::_('GURU_MEDIA_REMOVED');
		}
		$link = "index.php?option=com_guru&controller=guruQuiz&task=edit&cid[]=".$tid;
		$this->setRedirect($link, $msg);
	}
	
	function duplicate(){ 
		$res = $this->_model->duplicate();
		if($res == 1){
			$msg = JText::_('GURU_Q_DUPLICATE_SUCC');
		}
		else{
			$msg = JText::_('GURU_Q_DUPLICATE_ERR');
		}
		$link = "index.php?option=com_guru&controller=guruQuiz";
		$this->setRedirect($link, $msg);
	}
	function saveorder(){
        $app = JFactory::getApplication('administrator');
        if($this->_model->saveorder()){
            $msg = JText::_('GURU_MODIF_OK');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruQuiz');
        }
		else{
            $msg = JText::_('GURU_ERROR');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruQuiz');
        }
        $this->display();    
    }
	
	function display($cachable = false, $urlparams = Array()){
		$view = $this->getView("guruQuiz", "html");
        $view->setLayout('default');
		$view->setModel($this->_model, true);
		@$view->display();
    }
	function exportFile(){
		$header = "First Name , Last Name ,  Email ,#, Username ,  Date Taken , Score"; 
		$data  = $header."\n";
		$pid = JFactory::getApplication()->input->get("pid", 0);
		$quiz_id =  intval(JFactory::getApplication()->input->get("id", ""));
		$db = JFactory::getDBO();
		$sql = "select u.id, u.username, u.email, c.firstname, c.lastname, tq.date_taken_quiz, tq.score_quiz, tq.id as tq_id  from #__guru_customer c, #__users u, #__guru_quiz_question_taken_v3 tq where c.id=u.id and c.id = tq.user_id and u.id IN (select  user_id from #__guru_quiz_taken_v3 where quiz_id=".$quiz_id.") and tq.quiz_id=".$quiz_id." order by c.id desc";
		$db->setQuery($sql);
		$tmp = $db->loadObjectList();
		$n = count($tmp);
		$new_id = 0;
		$nr = 1;
		
		for ($i = 0; $i < $n; $i++){
			$firstname = $tmp[$i]->firstname;
			$lastname  = $tmp[$i]->lastname;
			$username = $tmp[$i]->username;
			if($tmp[$i]->id == $new_id){
				$nr = $nr+1;
			}
			else{
				$nr=1;
			
			}
			if($nr==1){
				$nr=$nr."st";
			}
			elseif($nr == 2){
				$nr =$nr."nd";
			}
			elseif($nr == 3){
				$nr =$nr."rd";
			}
			elseif($nr >3){
				$nr =$nr."td";
			}
			$nr = $nr;
			$email = $tmp[$i]->email;
			$date =  date("d/n/Y ", strtotime($tmp[$i]->date_taken_quiz));
			$score = $tmp[$i]->score_quiz;
			/*$score = explode("|", $score);
			$score = intval(($score[0]/$score[1])*100);*/
			
			$data .= ''.$firstname.' , '.$lastname.', '.$email.', '.$nr.', '.$username.', '.$date.', '.$score.''."\n";
			
			$new_id = $tmp[$i]->id;
		}
		header("Content-Type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=Scores.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $data;
		exit();
	
	}
	
	function exportFilePdf(){
		while (ob_get_level())
		ob_end_clean();
		header("Content-Encoding: None", true);

	
		require ( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'fpdf.php' );
		include_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'font'.DIRECTORY_SEPARATOR.'helvetica.php');
		$quiz_id =  intval(JFactory::getApplication()->input->get("id", ""));
		 //create a FPDF object
		$pdf=new FPDF();

		//set font for the entire document
		$pdf->SetFont('Arial','B',20);
		$pdf->SetTextColor(50,60,100);
		
		//set up a page
		$pdf->AddPage();
		@$pdf->SetDisplayMode(real,'default');
		
		$pdf->SetXY(10,5);
		$pdf->SetFontSize(8);
		$db = JFactory::getDBO();
		$sql = "select name from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$name = $db->loadColumn();
		$name = $name["0"];
		$pdf->Write(5,'Student Quiz Result for '."'".$name."'");
		
		
		$pdf->SetXY(10,15);
		$pdf->SetFontSize(8);
		$pdf->Cell(20,10,'Times',1,0,'C',0);
		$pdf->Cell(20,10,'Students',1,0,'C',0);
		$pdf->Cell(20,10,'Avg Score',1,0,'C',0);
		//display the title with a border around it
		$res = guruAdminModelguruQuiz::NbOfTimesandStudents($quiz_id);
		
				$z = 25;
				$scoresByUserId = array();
				$maxNoOfTimes = 0;
				for($i=0; $i < count($res); $i++) {	
					$newElem = new stdClass();
					$newElem->user_id = $res[$i]["user_id"];
					$newElem->scores = explode(",", $res[$i]["score_by_user"]);
						if(count($newElem->scores) > $maxNoOfTimes) {
							$maxNoOfTimes = count($newElem->scores);
						}	
						array_push($scoresByUserId, $newElem);
				}		
				
				$newvect = array();	
				for($i = 0; $i < $maxNoOfTimes; $i++) {	
					$newElem = new stdClass();	
					$newElem->noOfTimes = $i + 1;	
					$newElem->noOfStudents = 0;		
					$newElem->sumScores = 0;	
					for($j = 0; $j < count($scoresByUserId); $j++) {
						if(count($scoresByUserId[$j]->scores) >= $i + 1) {
							$newElem->noOfStudents += 1;
							$newElem->sumScores += $scoresByUserId[$j]->scores[$i];
						}
					}	
					$newElem->avgScore = $newElem->sumScores / $newElem->noOfStudents;	
					array_push($newvect, $newElem);	
				}				
				for($i = 0; $i < count($newvect); $i++){					
					if($i + 1 == 1){
						$nboftimes = ($i+1)."st";
					}
					elseif($i + 1 == 2){
						$nboftimes = ($i+1)."nd";
					}
					elseif($i + 1 == 3){
						$nboftimes = ($i+1)."rd";
					}
					elseif($i + 1 > 3){
						$nboftimes = ($i+1)."th";
					}
					$studtot =  $newvect[$i]->noOfStudents;	
					$avg = intval($newvect[$i]->avgScore*100);

			
					$pdf->SetXY(10,$z);
					$pdf->SetFontSize(7);
					$pdf->Cell(20,10,$nboftimes ,1,0,'C',0);
					$pdf->Cell(20,10,$studtot,1,0,'C',0);
					$pdf->Cell(20,10,$avg,1,0,'C',0);
					$z += 10;
					
					
		}
		 $t = $z+10;
		$pdf->SetXY(10,$t);
		$pdf->SetFontSize(7);
		$pdf->Cell(25,10,'First Name','LRTB','','L',0);
		$pdf->Cell(25,10,'Last Name','LRTB','','L',0);
		$pdf->Cell(39,10,'Email','LRTB','','L',0);
		$pdf->Cell(15,10,'#','LRTB','','L',0);
		$pdf->Cell(20,10,'Username','LRTB','','L',0);
		$pdf->Cell(20,10,'Date Taken','LRTB','','L',0);
		$pdf->Cell(20,10,'Score','LRTB','','L',0);
		$pdf->Ln();
		//-----------------------------------------
		
		$pid = JFactory::getApplication()->input->get("pid", 0);
		
		$db = JFactory::getDBO();
		$sql = "select u.id, u.username, u.email, c.firstname, c.lastname, tq.date_taken_quiz, tq.score_quiz, tq.id as tq_id  from #__guru_customer c, #__users u, #__guru_quiz_question_taken_v3 tq where c.id=u.id and c.id = tq.user_id and u.id IN (select  user_id from #__guru_quiz_taken_v3 where quiz_id=".$quiz_id.") and tq.quiz_id=".$quiz_id." order by c.id desc";
		$db->setQuery($sql);
		$tmp = $db->loadObjectList();

		$new_id = 0;
		$nr = 1;
		for ($i = 0; $i < count($tmp); $i++){
			$firstname = $tmp[$i]->firstname;
			$lastname  = $tmp[$i]->lastname;
			$username = $tmp[$i]->username;
			$email = $tmp[$i]->email;
			$date =  date("d/n/Y ", strtotime($tmp[$i]->date_taken_quiz));
			$score = $tmp[$i]->score_quiz;
			
			if($tmp[$i]->id == $new_id){
				$nr = $nr+1;
			}
			else{
				$nr=1;
			}
			if($nr==1){
				$nr=$nr."st";
			}
			elseif($nr == 2){
				$nr =$nr."nd";
			}
			elseif($nr == 3){
				$nr =$nr."rd";
			}
			elseif($nr >3){
				$nr =$nr."td";
			}
			$pdf->SetFontSize(7);
			$pdf->Cell(25,10,$firstname,'LRTB','','L',0);
			$pdf->Cell(25,10,$lastname,'LRTB','','L',0);
			$pdf->Cell(39,10,$email,'LRTB','','L',0);
			$pdf->Cell(15,10,$nr,'LRTB','','L',0);
			$pdf->Cell(20,10,$username,'LRTB','','L',0);
			$pdf->Cell(20,10, $date ,'LRTB','','L',0);
			$pdf->Cell(20,10,$score,'LRTB','','L',0);
			$pdf->Ln();
			$new_id = $tmp[$i]->id;
		}
		//Output the document
		$pdf->Output('Scores.pdf','I'); 
	}
	
	public function saveOrderQuestions(){
		// Get the arrays from the Request
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("guruQuiz");
		// Save the ordering
		$return = $model->saveOrderQuest();
		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}
	
	function ajax_request(){
		include_once(JPATH_SITE. "/administrator/components/com_guru/views/guruquiz/tmpl/ajax.php");
		die();
	}
	
	function saveQuestionandkeep(){
		$qtext 	= JFactory::getApplication()->input->get('question_text','','raw');
		$quizid = JFactory::getApplication()->input->get('quizid','0');
		$is_from_modal_lesson = JFactory::getApplication()->input->get('is_from_modal');
		$question_type = JFactory::getApplication()->input->get('type', "");
		$media_ids_question = JFactory::getApplication()->input->get('question_media_ids', "", "raw");
		$points = JFactory::getApplication()->input->get('question_weight_tf', "");
		$true_false_ch = JFactory::getApplication()->input->get('truefs_ans', "-1");
		$question_id = JFactory::getApplication()->input->get('question_id', "0");
		$ans_content = JFactory::getApplication()->input->get('ans_content', '', 'raw');
		$from_save_or_not = "savekeep";
		
		$return = $this->_model->addquestion($qtext, $quizid, $question_type, $media_ids_question, $points, $true_false_ch, $question_id, $from_save_or_not, $ans_content);
		$msg = JText::_('GURU_CS_APPLY_DONE');
		$link ="index.php?option=com_guru&controller=guruQuiz&task=addquestion&tmpl=component&cid[]=".$quizid."&qid=".$return."&type=".$question_type;	
		$this->setRedirect($link, $msg);
	}
};

?>