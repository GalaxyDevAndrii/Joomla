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
JHTML::_('behavior.tooltip');
$div_menu = $this->authorGuruMenuBar();

$doc = JFactory::getDocument();
JHtml::stylesheet("components/com_guru/css/quiz.css");
JHtml::stylesheet("components/com_guru/css/uikit.almost-flat.min.css");
//$doc->addScript("components/com_guru/js/programs.js");
include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');

$step_quiz = JFactory::getApplication()->input->get("step_quiz", "0");
$user_id = JFactory::getApplication()->input->get("userid", "0");
$user_name = $this->userName($user_id);
$image = $this->userImage($user_id);
$user_email = $this->userEmail($user_id);
$quiz_id = JFactory::getApplication()->input->get("quiz", "0");
$course_id = JFactory::getApplication()->input->get("pid", "0");
$quiz_name = $this->getQuizName($quiz_id);
$score = $this->getScoreQuiz($quiz_id, $user_id, $course_id);
$quiz_form_content = "";
$database = JFactory::getDBO();
$db = JFactory::getDBO();
$helperclass = new guruHelper();

$sql = "SELECT show_countdown, max_score, questions_per_page, time_quiz_taken, is_final FROM #__guru_quiz WHERE id=".intval($quiz_id);
$database->setQuery($sql);
$result = $database->loadObject();

//$sql = "SELECT  score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,1";     
$sql = "SELECT `score_quiz` FROM #__guru_quiz_question_taken_v3 WHERE `user_id`=".intval($user_id)." and `quiz_id`=".intval($quiz_id)." and `pid`=".intval($course_id)." and `id`=".intval($step_quiz);
$database->setQuery($sql);
$result_calc = $database->loadObject();

//$sql = "SELECT  count(id) as time_quiz_taken_per_user FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,1";     
$sql = "SELECT count(*) as time_quiz_taken_per_user FROM #__guru_quiz_question_taken_v3 WHERE `user_id`=".intval($user_id)." and `quiz_id`=".intval($quiz_id)." and `pid`=".intval($course_id);
$database->setQuery($sql);
$result_calct = $database->loadObject();
$time_quiz_taken_per_user = $result_calct->time_quiz_taken_per_user;

//$sql = "SELECT question_ids FROM  #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,1 ";
$sql = "SELECT `question_ids` FROM  #__guru_quiz_question_taken_v3 WHERE `user_id`=".intval($user_id)." and `quiz_id`=".intval($quiz_id)." and `pid`=".intval($course_id)." and `id`=".intval($step_quiz);
$database->setQuery($sql);
$question_ids_taken_by_user = $database->loadColumn();
$question_ids_taken_by_user =  @$question_ids_taken_by_user["0"];

if(!isset($question_ids_taken_by_user) || trim($question_ids_taken_by_user) == ""){
	$question_ids_taken_by_user = "0";
}

$number_of_questions =  count(explode(",",$question_ids_taken_by_user));
$configs = guruModelguruTask::getConfig();	

$q  = "SELECT * FROM #__guru_questions_v3 WHERE id IN (".$question_ids_taken_by_user.")";
$database->setQuery( $q );
$quiz_questions = $database->loadObjectList();

if($result->time_quiz_taken < 11){
	$time_user = $result->time_quiz_taken - $time_quiz_taken_per_user;
}

@$res = $result_calc->score_quiz;
									
$k = 0;
$quiz_id =  intval($quiz_id);
$score = $res;
$essay_exists = FALSE;

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
			
			$question_answers = "";
			if($quiz_questions[$i]->type == "essay"){
				$sql = "select * from #__guru_quiz_essay_mark where question_id=".intval($quiz_questions[$i]->id)." and user_id=".intval($user_id);
				$db->setQuery($sql);
				$db->execute();
				$question_answers = $db->loadObjectList();
			}
			else{
				$q = "SELECT * FROM #__guru_question_answers WHERE question_id=".intval($quiz_questions[$i]->id);
				$db->setQuery($q);
				$db->execute();
				$question_answers = $db->loadObjectList();
			}
			
			//$answer_given_by_user = "SELECT question_id as question_idd, answers_given FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,".$number_of_questions."";

			$answer_given_by_user = "SELECT `question_id` as question_idd, `answers_given` FROM #__guru_quiz_taken_v3 WHERE `user_id`=".intval($user_id)." and `quiz_id`=".intval($quiz_id)." and `pid`=".intval($course_id)." and `id_question_taken`=".intval($step_quiz)." LIMIT 0,".$number_of_questions."";
			$db->setQuery($answer_given_by_user);
			$db->execute();
			$answer_given_by_user = $db->loadAssocList("question_idd");
			
			$sql = "select id as answer_id from #__guru_question_answers where question_id=".intval($quiz_questions[$i]->id)." and correct_answer=1";
			$db->setQuery($sql);
			$db->execute();
			$answers_right = $db->loadAssocList("answer_id");
			
			$css_validate_class = "question-false";
			$validate_answer = guruModelguruTask::validateAnswer($answers_right, $answer_given_by_user[$quiz_questions[$i]->id]);
			if($validate_answer){
				$css_validate_class = "question-true";
			}
					
			for($j=0; $j<count($media_associated_question); $j++){
				@$media_that_needs_to_be_sent = guruModelguruTask::getMediaFromId($media_associated_question[$j]);
				if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
					$result_media[] = $helperclass->create_media_using_plugin($media_that_needs_to_be_sent["0"], $configs, '', '', '100px', 100);
				}	
			}
			$quiz_form_content .= '<div class="uk-grid wk-grid">';
			
			if($quiz_questions[$i]->type == "essay"){ //start essay question
				// do nothing
			}//end essay question
			else{// the rest: true/false, single, multiple
				$quiz_form_content .= '	<div class="uk-width-1-2 wk-width-1-2"><div class="'.$css_validate_class.'">';
				$quiz_form_content .= 		$quiz_questions[$i]->question_content."<br/>".implode("",$result_media);
				$quiz_form_content .= '	</div></div>';
				$quiz_form_content .= '	<div class="uk-width-1-2 wk-width-1-2">';
			}
			
			if($quiz_questions[$i]->type == "true_false"){
				$quiz_form_content .= '<div>';
				foreach($question_answers as $question_answer){
					if(isset($answer_given_by_user[$question_answer->question_id]["answers_given"]) && $answer_given_by_user[$question_answer->question_id]["answers_given"] == $question_answer->id){
						$checked = 'checked="checked"';
					}
					else{
						$checked = '';
					}
					$quiz_form_content .= '
									<div class="uk-float-left wk-float-left">
										<input type="radio" '.$checked.' disabled name="truefs_ans['.intval($question_answer->question_id).']" value="'.$question_answer->id.'" />
										<span class="lbl"></span>
									</div>
									<div class="uk-float-left wk-float-left">
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
								$media_that_needs_to_be_sent = guruModelguruTask::getMediaFromId($answer_media_id);
								
								if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
									if($media_that_needs_to_be_sent["0"]->type == "text"){
										$result_media_answers[] = guruModelguruTask::parse_txt($media_that_needs_to_be_sent["0"]->id);
									}
									else{
										$result_media_answers[] = guruModelguruTask::parse_media($media_that_needs_to_be_sent["0"]->id, 0);
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
						$option_value = '<input type="radio" '.$checked.' id="ans'.$question_answer->id.'" disabled name="answers_single['.intval($quiz_questions[$i]->id).']" value="'.$question_answer->id.'"/><span class="lbl"></span>&nbsp;'.$question_answer->answer_content_text.'<br/>'.implode("<br/><br/>",$result_media_answers)."<br/>";
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
								$media_that_needs_to_be_sent = guruModelguruTask::getMediaFromId($answer_media_id);
								
								if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
									if($media_that_needs_to_be_sent["0"]->type == "text"){
										$result_media_answers[] = guruModelguruTask::parse_txt($media_that_needs_to_be_sent["0"]->id);
									}
									else{
										$result_media_answers[] = guruModelguruTask::parse_media($media_that_needs_to_be_sent["0"]->id, 0);
									}
								}
							}
						}
						$multiple_ans_given = explode(",", @$answer_given_by_user[$question_answer->question_id]["answers_given"]);
						$checked = '';
						if(in_array($question_answer->id, $multiple_ans_given)){
							$checked = 'checked="checked"';
						}
						
						
						$option_value = '<input type="checkbox" '.$checked.' disabled name="multiple_ans['.intval($quiz_questions[$i]->id).'][]" value="'.$question_answer->id.'"/>&nbsp;'.$question_answer->answer_content_text.'<br/>'.implode("",$result_media_answers)."<br/>";
						$quiz_form_content .= $option_value;
					}
				}		
			}
			elseif($quiz_questions[$i]->type == "essay"){
				$essay_exists = TRUE;
				$sql = "select max(id) from #__guru_quiz_question_taken_v3 where user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id);
				$db->setQuery($sql);
				$db->execute();
				$id_question_taken = $db->loadColumn();
				$id_question_taken = $id_question_taken["0"];
				
				$q = "SELECT * FROM #__guru_quiz_taken_v3 WHERE id_question_taken = ".intval($id_question_taken)." and question_id=".intval($quiz_questions[$i]->id);
				$db->setQuery($q);
				$db->execute();
				$essay_answers = $db->loadObjectList();
				
				$points_select = "";
				$max_points = $quiz_questions[$i]->points;
				
				$points_select .= '<select name="grade['.$quiz_questions[$i]->id.']">';
				$points_select .= '<option value="-1">'.JText::_("GURU_CHOOSE").'</option>';
				for($k=0; $k<=$max_points; $k++){
					$selected = "";
					if(isset($question_answers["0"]) && isset($question_answers["0"]->grade)){
						if($question_answers["0"]->grade == $k){
							$selected = 'selected="selected"';
						}
					}
					$points_select .= '<option value="'.$k.'" '.$selected.'>'.$k.'</option>';
				}
				$points_select .= '</select>';
				
				$feedback = "";
				$feedback_quiz_results = "";
				if(isset($question_answers["0"]) && isset($question_answers["0"]->feedback) && isset($question_answers["0"]->feedback_quiz_results)){
					$feedback = trim($question_answers["0"]->feedback);
					$feedback_quiz_results = trim($question_answers["0"]->feedback_quiz_results);
				}
				
				$quiz_form_content .= '
					<div class="uk-width-1-1 wk-width-1-1">
						<div class="uk-grid wk-grid">
							<div class="uk-width-large-7-10 uk-width-small-1-1 uk-width-medium-1-1 wk-width-large-7-10 wk-width-small-1-1 wk-width-medium-1-1">
								<div class="uk-grid wk-grid">
									<div class="uk-width-large-1-10 uk-width-small-1-1 uk-width-medium-1-1 wk-width-large-1-10 wk-width-small-1-1 wk-width-medium-1-1">
										'.JText::_("GURU_QUESTION").'
									</div>
									<div class="uk-width-large-9-10 uk-width-small-1-1 uk-width-medium-1-1 wk-width-large-9-10 wk-width-small-1-1 wk-width-medium-1-1">
										'.$quiz_questions[$i]->question_content."<br/>".implode("",$result_media).'
									</div>
								</div>
								<div class="uk-grid wk-grid">
									<div class="uk-width-large-1-10 uk-width-small-1-1 uk-width-medium-1-1 wk-width-large-1-10 wk-width-small-1-1 wk-width-medium-1-1">
										'.JText::_("GURU_ANSWER").'
									</div>
									<div class="uk-width-large-9-10 uk-width-small-1-1 uk-width-medium-1-1 wk-width-large-9-10 wk-width-small-1-1 wk-width-medium-1-1" style="max-height:300px; overflow-y:scroll;">
										'.$essay_answers["0"]->answers_given.'
									</div>
								</div>
							</div>
							<div class="uk-width-large-3-10 uk-width-small-1-1 uk-width-medium-1-1 wk-width-large-3-10 wk-width-small-1-1 wk-width-medium-1-1">
								'.JText::_("GURU_PICK_GRADE").'&nbsp;'.$points_select.'
								<br/>
								'.JText::_("GURU_TEACHER_NOTES").'
								<textarea style="max-width:100%; width:100%;" rows="10" name="feedback['.$quiz_questions[$i]->id.']">'.$feedback.'</textarea>
							</div>
						</div>
						
						<div class="uk-grid wk-grid">
							<div class="uk-width-1-1 wk-width-1-1">
								'.JText::_("GURU_FEEDBACK_QUIZ_RESULT").'
								<br/>
								<textarea style="max-width:100%; width:100%;" rows="7" name="feedback_quiz_results['.$quiz_questions[$i]->id.']">'.$feedback_quiz_results.'</textarea>
							</div>
						</div>
					</div>
				';
			}
			
			if($quiz_questions[$i]->type == "essay"){ //start essay question
				// do nothing
			}//end essay question
			else{// the rest: true/false, single, multiple
				$quiz_form_content .= '</div>';
			}
			$quiz_form_content .= '</div>'; // uk-grid
	
	$added++;
	}
	$quiz_form_content .= '</div>'; // end page
}

if($nr_pages > 1){
	$quiz_form_content .= '<div class="pagination pagination-centered"><ul class="uk-pagination wk-pagination">';
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

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div id="g_myquizzesstats" class="gru-myquizzesstats" style="width:95%; margin:auto;">
    <form name="adminForm" method="post" action="<?php echo JUri::root() ?>index.php">
        <div class="uk-grid wk-grid">
            <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1">
                <h4>
                    <?php
                        if(trim($image) == ""){
                            $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($user_email)))."?d=mm&s=40";
                            echo '<img src="'.$grav_url.'" alt="'.$user_name.'" title="'.$user_name.'"/>&nbsp;';
                        }
                        else{
                            echo '<img src="'.JURI::root().trim($image).'" style="width:40px;" alt="'.$user_name.'" title="'.$user_name.'" />&nbsp;';
                        }
                        echo $user_name;
                    ?>
                </h4>
            </div>
            
            <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-text-right wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1 wk-text-right">
            	<button class="uk-button uk-button-primary wk-button wk-button-primary" onclick="window.location='<?php echo JURI::root(); ?>index.php?option=com_guru&view=guruauthor&task=studentquizes&layout=studentquizes&pid=<?php echo $course_id; ?>&userid=<?php echo $user_id; ?>&tmpl=component'; return false;">
                    <?php echo JText::_("GURU_BACK"); ?>
                </button>
                <?php
                    if($essay_exists === TRUE){
                ?>
                        <button onclick="document.adminForm.task.value='applyquizdetails';" class="uk-button uk-button-success wk-button wk-button-success">
                            <?php echo JText::_("GURU_SAVE"); ?>
                        </button>
                        
                        <button onclick="document.adminForm.task.value='savequizdetails';" class="uk-button uk-button-success wk-button wk-button-success">
                            <?php echo JText::_("GURU_SV_AND_CL"); ?>
                        </button>
                <?php
                    }
                ?>
            </div>
		</div>
		
        <div class="uk-grid wk-grid">
        	<div class="uk-width-1-1 wk-width-1-1">
            	<h3><?php echo $quiz_name ; ?></h3>
            </div>
            <div class="uk-width-1-1 wk-width-1-1">
                <div class="uk-panel uk-panel-box uk-panel-box-secondary wk-panel wk-panel-box wk-panel-box-secondary">
                    <h3 class="uk-panel-title wk-panel-title"><?php echo JText::_("GURU_QUIZ_RESULT"); ?>:</h3>
                    <?php echo $user_name."'s"." ".JText::_("GURU_QUIZ_SCORE"); ?>: <?php echo $score. "%";?>
                </div>
            </div>
        </div>
        
        <div class="uk-align-center wk-align-center">
            <?php echo $quiz_form_content;?>
        </div>
        
        <input type="hidden" name="controller" value="guruAuthor" />
        <input type="hidden" name="option" value="com_guru"/>
        <input type="hidden" name="task" value="applyquizdetails" />
        <input type="hidden" name="pid" value="<?php echo intval($course_id); ?>" />
        <input type="hidden" name="user_id" value="<?php echo intval($user_id); ?>" />
        <input type="hidden" name="quiz_id" value="<?php echo intval($quiz_id); ?>" />
        <input type="hidden" name="action" value="<?php echo JFactory::getApplication()->input->get("action", ""); ?>" />
    </form>
</div>