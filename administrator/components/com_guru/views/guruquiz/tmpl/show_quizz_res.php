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

defined( '_JEXEC' ) or die( 'Restricted access' );
$doc =JFactory::getDocument();
include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');
?>
<style>
	.question-false{
		color:#FF0000;
		font-weight:bold;
	}
	
	.question-true{
		color:#006633;
		font-weight:bold;
	}
	div.modal-body{
		height:94%;
		max-height:100%;
		overflow-y:scroll;
	}
</style>
<?php
$k = 0;
$n = count($this->ads);    
$quiz_id =  intval(JFactory::getApplication()->input->get("quiz_id", ""));
$user_id =  intval(JFactory::getApplication()->input->get("cid", "", "raw"));
$course_id = JFactory::getApplication()->input->get("pid", "0");
$id = JFactory::getApplication()->input->get("id", "0");
$quiz_name = guruAdminModelguruQuiz::getQuizName($quiz_id);
$database = JFactory::getDBO();
$db = JFactory::getDBO();
$helperclass =  new guruAdminHelper();
$configs = guruAdminModelguruTask::getConfigs();	
$quiz_form_content = "";

$sql = "SELECT show_countdown, max_score, questions_per_page, time_quiz_taken, is_final FROM #__guru_quiz WHERE id=".intval($quiz_id);
$database->setQuery($sql);
$result = $database->loadObject();

$sql = "SELECT  score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." and id=".intval($id)." ORDER BY id DESC LIMIT 0,1";     
$database->setQuery($sql);
$result_calc = $database->loadObject();

$sql = "SELECT question_ids FROM  #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,1 ";
$database->setQuery($sql);
$question_ids_taken_by_user = $database->loadColumn();
$question_ids_taken_by_user =  $question_ids_taken_by_user["0"];
$number_of_questions =  count(explode(",",$question_ids_taken_by_user));


$q  = "SELECT * FROM #__guru_questions_v3 WHERE id IN (".$question_ids_taken_by_user.")";
$database->setQuery( $q );
$quiz_questions = $database->loadObjectList();


@$res = $result_calc->score_quiz;
									
$k = 0;
$quiz_id =  intval($quiz_id);
$score = $res;

$quiz_form_content .= '<div id="the_quiz">';
	$per_page = $result->questions_per_page;// questions per page
	if($per_page == 0){
		$per_page = $number_of_questions;
	}
	$nr_pages = 1;// default one page

	if($number_of_questions > 0 && $number_of_questions > $per_page){
		$nr_pages = ceil($number_of_questions / $per_page);
	}

	for($pag = 1; $pag <= $nr_pages; $pag++){
		$k = ($pag - 1) * $per_page;
		$added = 0;
	
		$display = "";
		if($pag == 1){
			$display = "block";
		}
		else{
			$display = "none";
		}
		$quiz_form_content .= '<div id="quiz_page_'.$pag.'" style="display:'.$display.';">'; // start page
	
		for($i=$k; $i<intval($pag * $per_page); $i++){		
			if(!isset($quiz_questions[$i])){
				continue;
			}
			
			$question_answers_number = 0;
			$media_associated_question = json_decode($quiz_questions[$i]->media_ids);
			$media_content = "";
			$result_media = array();	
			
			$q = "SELECT * FROM #__guru_question_answers WHERE question_id=".intval($quiz_questions[$i]->id);
			$db->setQuery( $q );
			$question_answers = $db->loadObjectList();
			
			$answer_given_by_user = "SELECT question_id as question_idd, answers_given FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." and id_question_taken=".intval($id)."";
			$db->setQuery($answer_given_by_user);
			$db->execute();
			$answer_given_by_user = $db->loadAssocList("question_idd");
			
			$sql = "select id as answer_id from #__guru_question_answers where question_id=".intval($quiz_questions[$i]->id)." and correct_answer=1";
			$db->setQuery($sql);
			$db->execute();
			$answers_right = $db->loadAssocList("answer_id");
			
			$css_validate_class = "question-false";
			$validate_answer = guruAdminModelguruTask::validateAnswer($answers_right, $answer_given_by_user[$quiz_questions[$i]->id]);
			if($validate_answer){
				$css_validate_class = "question-true";
			}
					
			for($j=0; $j<count($media_associated_question); $j++){
				@$media_that_needs_to_be_sent = guruAdminModelguruTask::getMediaFromId($media_associated_question[$j]);
				if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
					$result_media[] = $helperclass->create_media_using_plugin($media_that_needs_to_be_sent["0"], $configs, '', '', '100px', 100);
				}	
			}
			$quiz_form_content .= '<div class="row-fluid">';
			$quiz_form_content .= '	<div class="span12">';
			$quiz_form_content .= '		<div class="span5 pull-left clearfix '.$css_validate_class.'">';
			$quiz_form_content .= 			$quiz_questions[$i]->question_content."<br/>".implode("",$result_media);
			$quiz_form_content .= '		</div>';
				
			$quiz_form_content .= '<div class="span6 pull-left clearfix">';
	
			if($quiz_questions[$i]->type == "true_false"){
				$quiz_form_content .= '<div>';
				foreach($question_answers as $question_answer){
					if(isset($answer_given_by_user[$question_answer->question_id]["answers_given"]) && $answer_given_by_user[$question_answer->question_id]["answers_given"] == $question_answer->id){
						$checked = 'checked="checked"';
					}
					else{
						$checked = '';
					}
					$quiz_form_content .= '<div class="pull-left">
										<input type="radio" '.$checked.' name="truefs_ans['.intval($question_answer->question_id).']" value="'.$question_answer->id.'" />
										<span class="lbl"></span>
									 </div>
									 <div class="pull-left">
										&nbsp;'.$question_answer->answer_content_text.'&nbsp;&nbsp;
									 </div>';
				}
				$quiz_form_content .= '</div>';
			}
			elseif($quiz_questions[$i]->type == "single"){
				if(isset($question_answers) && count($question_answers) > 0){
					foreach($question_answers as $question_answer){
						$media_associated_answers = json_decode($question_answer->media_ids);
						$media_content = "";
						$result_media_answers = array();
						
						if(isset($media_associated_answers) && count($media_associated_answers) > 0){
							foreach($media_associated_answers as $media_key=>$answer_media_id){
								$media_that_needs_to_be_sent = guruAdminModelguruTask::getMediaFromId($answer_media_id);
								
								if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
									if($media_that_needs_to_be_sent["0"]->type == "text"){
										$result_media_answers[] = guruAdminModelguruTask::parse_txt($media_that_needs_to_be_sent["0"]->id);
									}
									else{
										$result_media_answers[] = guruAdminModelguruTask::parse_media($media_that_needs_to_be_sent["0"]->id, 0);
									}
								}
							}
						}
						if(isset($answer_given_by_user[$question_answer->question_id]["answers_given"]) && $answer_given_by_user[$question_answer->question_id]["answers_given"] == $question_answer->id){
							$checked = 'checked="checked"';
						}
						else{
							$checked = '';
						}
						$option_value = '<input type="radio" '.$checked.' id="ans'.$question_answer->id.'" name="answers_single['.intval($quiz_questions[$i]->id).']" value="'.$question_answer->id.'"/><span class="lbl"></span>&nbsp;'.$question_answer->answer_content_text.'<br/>'.implode("<br/><br/>",$result_media_answers)."<br/>";
						$quiz_form_content .= $option_value;
					}
				}						
			}
			elseif($quiz_questions[$i]->type == "multiple"){
				if(isset($question_answers) && count($question_answers) > 0){
					foreach($question_answers as $question_answer){
						$media_associated_answers = json_decode($question_answer->media_ids);
						$media_content = "";
						$result_media_answers = array();
						
						if(isset($media_associated_answers) && count($media_associated_answers) > 0){
							foreach($media_associated_answers as $media_key=>$answer_media_id){
								$media_that_needs_to_be_sent = guruAdminModelguruTask::getMediaFromId($answer_media_id);
								
								if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
									if($media_that_needs_to_be_sent["0"]->type == "text"){
										$result_media_answers[] = guruAdminModelguruTask::parse_txt($media_that_needs_to_be_sent["0"]->id);
									}
									else{
										$result_media_answers[] = guruAdminModelguruTask::parse_media($media_that_needs_to_be_sent["0"]->id, 0);
									}
								}
							}
						}
						$multiple_ans_given = explode(",", @$answer_given_by_user[$question_answer->question_id]["answers_given"]);
						$checked = '';
						if(in_array($question_answer->id, $multiple_ans_given)){
							$checked = 'checked="checked"';
						}
						
						
						$option_value = '<input type="checkbox" '.$checked.' name="multiple_ans['.intval($quiz_questions[$i]->id).'][]" value="'.$question_answer->id.'"/>&nbsp;'.$question_answer->answer_content_text.'<br/>'.implode("",$result_media_answers)."<br/>";
						$quiz_form_content .= $option_value;
					}
				}		
			}
			$quiz_form_content .= '		</div>';
	$quiz_form_content .= '	</div>';
$quiz_form_content .= '</div>';
	
	$added++;
	}
	$quiz_form_content .= '</div>'; // end page
}

if($nr_pages > 1){
	$quiz_form_content .= '<div class="pagination pagination-centered"><ul class="uk-pagination">';
	$quiz_form_content .= 	'<li class="pagination-start" id="pagination-start"><span class="pagenav">'.JText::_("GURU_START").'</span></li>';
	$quiz_form_content .= 	'<li class="pagination-prev" id="pagination-prev"><span class="pagenav">'.JText::_("GURU_PREV").'</span></li>';
	for($p=1; $p<=$nr_pages; $p++){
		if($p == 1){
			$quiz_form_content .= '<li id="list_1"><span class="pagenav">1</span></li>';
		}
		else{
			$quiz_form_content .= '<li id="list_'.$p.'">
								<a onclick="changePage('.intval($p).', '.intval($nr_pages).'); return false;" href="#">'.$p.'</a>
							 </li>';
		}
	}
	$quiz_form_content .= 	'<li class="pagination-next" id="pagination-next">
							<a href="#" onclick="changePage(2, '.intval($nr_pages).'); return false;">'.JText::_("GURU_NEXT").'</a>
						 </li>';
	$quiz_form_content .= 	'<li class="pagination-end" id="pagination-end">
							<a href="#" onclick="changePage('.intval($nr_pages).', '.intval($nr_pages).'); return false;">'.JText::_("GURU_END").'</a>
						 </li>';
	$quiz_form_content .= '</ul></div>';
}


$quiz_form_content .= '</div>';
?>


<div id="editcell">
    <div>
        <span style="font-size:16px; padding-left:10px"><?php echo $quiz_name ; ?></span>
        <span class="guru_quiz_title"><?php echo JText::_("GURU_QUIZ_RESULT"); ?>:</span>
        <span class="guru_quiz_score"><?php echo JText::_("GURU_YOUR_SCORE"); ?>: <?php echo $score. "%";?></span>
        <br/>   
        <?php echo $quiz_form_content;?>
    </div>
</div>