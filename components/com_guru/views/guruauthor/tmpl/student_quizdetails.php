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

$id = JFactory::getApplication()->input->get("id", "0");

$doc = JFactory::getDocument();
JHtml::stylesheet("components/com_guru/css/quiz.css");
//$doc->addScript("components/com_guru/js/programs.js");
include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');

$user_id = JFactory::getApplication()->input->get("userid", "0");
$user_name = $this->userName($user_id);
$user_email = $this->userEmail($user_id);
$quiz_id = JFactory::getApplication()->input->get("quiz", "0");
$course_id = JFactory::getApplication()->input->get("pid", "0");
$quiz_name = $this->getQuizName($quiz_id);
$score = $this->getScoreQuiz($quiz_id, $user_id, $course_id);

$taskModel = new guruModelguruTask;
$taskModel->completeCourse($course_id, $user_id);

$quiz_form_content = "";
$database = JFactory::getDBO();
$db = JFactory::getDBO();
$helperclass = new guruHelper();

$sql = "SELECT * FROM #__guru_quiz WHERE id = ".intval($quiz_id);
$database->setQuery($sql);
$database->execute();
$result_quiz = $db->loadObject();
$show_correct_ans = $result_quiz->show_correct_ans;
$retake_passed_quiz = $result_quiz->retake_passed_quiz;

$sql = "SELECT show_countdown, max_score, questions_per_page, time_quiz_taken, is_final FROM #__guru_quiz WHERE id=".intval($quiz_id);
$database->setQuery($sql);
$result = $database->loadObject();

$sql = "SELECT  score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." and id=".intval($id);     
$database->setQuery($sql);
$result_calc = $database->loadObject();

$sql = "SELECT question_ids FROM  #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." and id=".intval($id);
$database->setQuery($sql);
$question_ids_taken_by_user = $database->loadColumn();
$question_ids_taken_by_user =  $question_ids_taken_by_user["0"];
$number_of_questions =  count(explode(",",$question_ids_taken_by_user));
$configs = guruModelguruTask::getConfig();  

if(trim($question_ids_taken_by_user) == ""){
    $question_ids_taken_by_user = "0";
}

$q  = "SELECT * FROM #__guru_questions_v3 WHERE id IN (".$question_ids_taken_by_user.")";
$database->setQuery( $q );
$quiz_questions = $database->loadObjectList("id");

/* order result by quiz questions ordering */
if(isset($question_ids_taken_by_user)){
    $question_ids_taken_by_user_array = explode(",", $question_ids_taken_by_user);

    if(is_array($question_ids_taken_by_user_array) && count($question_ids_taken_by_user_array) > 0){
        $quiz_questions_temp = array();

        foreach($question_ids_taken_by_user_array as $key=>$question_id){
            if(isset($quiz_questions[$question_id])){
                $quiz_questions_temp[] = $quiz_questions[$question_id];
            }
        }

        $quiz_questions = $quiz_questions_temp;
    }
}
/* order result by quiz questions ordering */


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
            
            $answer_given_by_user = "SELECT question_id as question_idd, answers_given FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." and id_question_taken=".intval($id)." LIMIT 0,".$number_of_questions."";
            $db->setQuery($answer_given_by_user);
            $db->execute();
            $answer_given_by_user = $db->loadAssocList("question_idd");
            
            $sql = "select id as answer_id from #__guru_question_answers where question_id=".intval($quiz_questions[$i]->id)." and correct_answer=1";
            $db->setQuery($sql);
            $db->execute();
            $answers_right = $db->loadAssocList("answer_id");
            
            $css_validate_class = "question-false";
            $validate_answer = guruModelguruTask::validateAnswer($answers_right, $answer_given_by_user[$quiz_questions[$i]->id]);

            $answer_status = 'guru-quiz__status--false';
            $answer_status_text = '<i class="uk-icon-meh-o wk-icon-meh-o"></i>' . JText::_("GURU_ANSWER_FALSE_MESSAGE");
            
            if($quiz_questions[$i]->type == "essay"){
                // check if question was assessed "your question was assessed"
				$assested = false;
				
				if(isset($question_answers) && count($question_answers) > 0){
					foreach($question_answers as $key_assested=>$value_assested){
						if(intval($value_assested->question_id) == $quiz_questions[$i]->id){
							$assested = true;
						}
					}
				}
				
				if($assested){
					$answer_status = 'guru-quiz__status--correct';
	                $answer_status_text = '<i class="uk-icon-smile-o wk-icon-smile-o"></i>' . JText::_("GURU_ANSWER_ASSESSED_MESSAGE");
				}
				else{
					$answer_status = 'guru-quiz__status--pending';
					$answer_status_text = '<i class="uk-icon-smile-o wk-icon-smile-o"></i>' . JText::_("GURU_ANSWER_PENDING_MESSAGE");
				}
            }
            elseif($validate_answer){
                $css_validate_class = "question-true";
                $answer_status = 'guru-quiz__status--correct';
                $answer_status_text = '<i class="uk-icon-smile-o wk-icon-smile-o"></i>' . JText::_("GURU_ANSWER_CORRECT_MESSAGE");
            }

                    
            for($j=0; $j<count($media_associated_question); $j++){
                @$media_that_needs_to_be_sent = guruModelguruTask::getMediaFromId($media_associated_question[$j]);
                if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
                    $result_media[] = $helperclass->create_media_using_plugin($media_that_needs_to_be_sent["0"], $configs, '', '', '150', '150');
                }   
            }

            $quiz_form_content .= '<div class="guru-quiz__question guru-question">';
            
            if($quiz_questions[$i]->type == "essay"){ //start essay question
                // do nothing
            }//end essay question
            else{// the rest: true/false, single, multiple
                $quiz_form_content .= '<div class="guru-quiz__media">'.implode("",$result_media).'</div>';
                $quiz_form_content .= '<div class="guru-quiz__question-title">'.$quiz_questions[$i]->question_content.'</div>';
            }

            $quiz_form_content .= '<div class="guru-quiz__answers-wrapper">';
            $quiz_form_content .= '<div class="guru-quiz__answers uk-grid uk-grid-small wk-grid wk-grid-small" data-uk-grid-match data-uk-grid-margin>';
            
            if($quiz_questions[$i]->type == "true_false"){
                foreach($question_answers as $question_answer){
                    if(isset($answer_given_by_user[$question_answer->question_id]["answers_given"]) && $answer_given_by_user[$question_answer->question_id]["answers_given"] == $question_answer->id){
                        $checked = 'checked="checked"';
                        
						if($show_correct_ans){
							$answer_checked = 'guru-quiz__answer--checked ';
						}
                    }
                    else{
                        $checked = '';
                        $answer_checked = '';
                    }

                    $border_correct_class = "";
                    
                    if($question_answer->correct_answer == 1 && $show_correct_ans){
						$border_correct_class = "guru-quiz__answer--correct";
                    }

                    $quiz_form_content .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3" id="guru-question-answer-'.$question_answer->question_id.'-'.$question_answer->id.'">';
                    $quiz_form_content .= '
                                <div class="guru-quiz__answer '.$answer_checked . $border_correct_class.'">
                                     <div class="uk-float-left wk-float-left">
                                        <input type="radio" '.$checked.' id="'.$question_answer->question_id.intval($question_answer->id).'" disabled name="truefs_ans['.intval($question_answer->question_id).']" value="'.$question_answer->id.'" />
                                        <label for="'.$question_answer->question_id.intval($question_answer->id).'" class="guru-quiz__check-box"></label>
                                     </div>
                                     <div class="uk-float-left wk-float-left">
                                        '.JText::_("GURU_QUESTION_OPTION_".mb_strtoupper($question_answer->answer_content_text)).'
                                     </div>
                                </div>';
                    $quiz_form_content .= '</div>';
                }
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
                            
							if($show_correct_ans){
								$answer_checked = 'guru-quiz__answer--checked ';
							}
                        }
                        else{
                            $checked = '';
                            $answer_checked = '';
                        }

                        $border_correct_class = "";
                        
                        if($question_answer->correct_answer == 1 && $show_correct_ans){
                            $border_correct_class = "guru-quiz__answer--correct";
                        }

                        $quiz_form_content .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3" id="guru-question-answer-'.$question_answer->question_id.'-'.$question_answer->id.'">';
                        $quiz_form_content .= '
                                    <div class="guru-quiz__answer '.$answer_checked . $border_correct_class.'">
                                        <input type="radio" '.$checked.' id="'.$question_answer->question_id.intval($question_answer->id).'" disabled name="answers_single['.intval($quiz_questions[$i]->id).']" value="'.$question_answer->id.'"/>
                                        <label for="'.$question_answer->question_id.intval($question_answer->id).'" class="guru-quiz__check-box"></label>
                                        <span>'.$question_answer->answer_content_text.'</span><div class="guru-quiz__answer-media">'.implode("",$result_media_answers).'</div>';
                        $quiz_form_content .= '</div></div>';
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
                        $answer_checked = '';
                        if(in_array($question_answer->id, $multiple_ans_given)){
                            $checked = 'checked="checked"';
                            
							if($show_correct_ans){
								$answer_checked = 'guru-quiz__answer--checked ';
							}
                        }

                        $border_correct_class = "";
                        
                        if($question_answer->correct_answer == 1 && $show_correct_ans){
                            $border_correct_class = "guru-quiz__answer--correct";
                        }
                        
                        $quiz_form_content .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3" id="guru-question-answer-'.$question_answer->question_id.'-'.$question_answer->id.'">';
                        $quiz_form_content .= '
                                    <div class="guru-quiz__answer '.$answer_checked . $border_correct_class.'">
                                            <input type="checkbox" '.$checked.' id="'.$question_answer->question_id.intval($question_answer->id).'" disabled name="multiple_ans['.intval($quiz_questions[$i]->id).'][]" value="'.$question_answer->id.'"/>
                                            <label for="'.$question_answer->question_id.intval($question_answer->id).'" class="guru-quiz__check-box"></label>
                                            <span>'.$question_answer->answer_content_text.'</span><div class="guru-quiz__answer-media">'.implode("",$result_media_answers).'</div>';
                        $quiz_form_content .= '</div></div>';
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
                
                $points_select .= '<select name="grade['.$quiz_questions[$i]->id.']" disabled="disabled">';
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
								<!--
                                '.JText::_("GURU_PICK_GRADE").'&nbsp;'.$points_select.'
                                <br/>
                                '.JText::_("GURU_TEACHER_NOTES").'
                                <textarea style="max-width:100%; width:100%;" rows="10" disabled name="feedback['.$quiz_questions[$i]->id.']">'.$feedback.'</textarea>
								-->
                            </div>
                        </div>
                        
                        <div class="uk-grid wk-grid">
                            <div class="uk-width-1-1 wk-width-1-1">
                                '.JText::_("GURU_FEEDBACK_QUIZ_RESULT").'
                                <br/>
                                <textarea style="max-width:100%; width:100%;" rows="7" disabled name="feedback_quiz_results['.$quiz_questions[$i]->id.']">'.$feedback_quiz_results.'</textarea>
                            </div>
                        </div>
                    </div>
                ';
            }
        
            $quiz_form_content .= '</div>'; // quiz answer
            $quiz_form_content .= '</div>'; // uk-grid
            $quiz_form_content .= '<div class="guru-quiz__status '.$answer_status.'">'.$answer_status_text.'</div>';
            $quiz_form_content .= '</div>'; // quiz question
    
            $added++;
        }
        $quiz_form_content .= '</div>'; // end page
    }

if($nr_pages > 1){
    $quiz_form_content .= '<div class="guru-quiz__pagination"><ul>';
    $quiz_form_content .=   '<li class="guru-quiz__pagination-item guru-quiz__pagination-item--start" id="pagination-start"><span>'.JText::_("GURU_START").'</span></li>';
    $quiz_form_content .=   '<li class="guru-quiz__pagination-item guru-quiz__pagination-item--prev" id="pagination-prev"><span>'.JText::_("GURU_PREV").'</span></li>';
    for($p=1; $p<=$nr_pages; $p++){
        if($p == 1){
            $quiz_form_content .= '<li class="guru-quiz__pagination-item" id="list_1"><span>1</span></li>';
        }
        else{
            $quiz_form_content .= '<li class="guru-quiz__pagination-item" id="list_'.$p.'">
                                <a onclick="changePage('.intval($p).', '.intval($nr_pages).'); return false;" href="#">'.$p.'</a>
                             </li>';
        }
    }
    $quiz_form_content .=   '<li class="guru-quiz__pagination-item guru-quiz__pagination-item--next" id="pagination-next">
                            <a href="#" onclick="changePage(2, '.intval($nr_pages).'); return false;">'.JText::_("GURU_NEXT").'</a>
                         </li>';
    $quiz_form_content .=   '<li class="guru-quiz__pagination-item guru-quiz__pagination-item--end" id="pagination-end">
                            <a href="#" onclick="changePage('.intval($nr_pages).', '.intval($nr_pages).'); return false;">'.JText::_("GURU_END").'</a>
                         </li>';
    $quiz_form_content .= '</ul></div>';
}


$quiz_form_content .= '</div>';
?>

<style type="text/css">
    div.g_row{
        margin:0px !important;
    }
</style>

<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");
</script>

<div id="g_myquizzesstats" class="gru-myquizzesstats">
    <form name="adminForm" method="post" action="<?php echo JUri::root() ?>index.php">
        <h3><?php echo $quiz_name ; ?></h3>

        <div class="guru-quiz__header">
            <ul>
                <li class="guru-quiz__header--inline">
                <?php
                    $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($user_email)))."?d=mm&s=100";
                    echo '<img class="uk-border-rounded" src="'.$grav_url.'" alt="'.$user_name.'" width="50" title="'.$user_name.'"/>&nbsp;';
                    echo '<span>' . $user_name . '</span>';
                ?>
                </li>
                <li class="guru-quiz__header--alt2">
                    <?php echo JText::_("GURU_QUIZ_RESULT"); ?>:
                    <span><?php echo $user_name.JText::_("GURU_STUDENT_SCORE_S")." ".JText::_("GURU_QUIZ_SCORE"); ?>: <?php echo intval($score)."%";?></span>
                </li>
                <?php
                    $action = JFactory::getApplication()->input->get("action", "");

                    if(trim($action) == ""){
                ?>
                
                <?php
                    }
                    else{
                        $database = JFactory::getDbo();
                        $catid_req = intval(JFactory::getApplication()->input->get("catid",""));
                        $module_req = intval(JFactory::getApplication()->input->get("module",""));
                        $cid_req = intval(JFactory::getApplication()->input->get("cid",""));
                        $quiz = intval(JFactory::getApplication()->input->get("quiz", "0"));
                        $pid = intval(JFactory::getApplication()->input->get("pid", "0"));
                        $itemid_req = JFactory::getApplication()->input->get("Itemid", "", "raw");

                        $lang = JFactory::getLanguage()->getTag();
                        $lang = explode("-", $lang);
                        $lang = @$lang["0"];
                
                        $link_quiz = JURI::root().'index.php?option=com_guru&view=gurutasks&catid='.$catid_req.'&module='.$module_req.'&cid='.$cid_req.'&tmpl=component&action_retake=retake&lang='.$lang.'&Itemid='.intval($itemid_req);
                        
                        $sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz)." and pid=".intval($pid)." ORDER BY id DESC LIMIT 0,1";
                        $database->setQuery($sql);
                        $result_q = $database->loadObject();
                        
                        $sql = "SELECT show_countdown, max_score, nb_quiz_select_up, time_quiz_taken FROM #__guru_quiz WHERE id=".intval($quiz);
                        $database->setQuery($sql);
                        $database->execute();
                        $result = $database->loadObject();
                        
                        $time_quiz_taken = $result->time_quiz_taken;
                        $sql = "select count(*) from #__guru_quiz_question_taken_v3 where `user_id`=".intval($user_id)." and `quiz_id`=".intval($quiz)." and `pid`=".intval($pid);
                        $database->setQuery($sql);
                        $database->execute();
                        $guru_quiz_taken_v3 = $db->loadColumn();
                        $guru_quiz_taken_v3 = @$guru_quiz_taken_v3["0"];

                        if($result->time_quiz_taken == 11 || $result->time_quiz_taken > intval($guru_quiz_taken_v3)){
                            if($result_q->score_quiz < intval($result->max_score) || intval($retake_passed_quiz) == 1){
                ?>
                                <li class="uk-text-right wk-text-right">
                                    <input type="button" onclick="window.location.href='<?php echo $link_quiz; ?>';" value="<?php echo JText::_("GURU_RETAKE"); ?>" class="guru-quiz__btn" />
                                </li>
                <?php
                            }
                        }
                        elseif($result->time_quiz_taken <= intval($guru_quiz_taken_v3)){
                            if($result_q->score_quiz >= intval($result->max_score) && intval($retake_passed_quiz) == 1){
                ?>
                                <li class="uk-text-right wk-text-right">
                                    <input type="button" onclick="window.location.href='<?php echo $link_quiz; ?>';" value="<?php echo JText::_("GURU_RETAKE"); ?>" class="guru-quiz__btn" />
                                </li>
                <?php
                            }
                            else{
                ?>
                                <li class="uk-text-right wk-text-right">
                                    <div class="no-attempts-left">
                                        <?php echo JText::_("GURU_NO_ATTEMPTS_LEFT"); ?>
                                    </div>
                                </li>
                <?php
                            }
                        }
                    }
                ?>
            </ul>
        </div>
        
        <div class="uk-clearfix wk-clearfix">
            <?php echo $quiz_form_content; ?>
        </div>
        
        <input type="hidden" name="controller" value="guruAuthor" />
        <input type="hidden" name="option" value="com_guru"/>
        <input type="hidden" name="task" value="applyquizdetails" />
        <input type="hidden" name="pid" value="<?php echo intval($course_id); ?>" />
        <input type="hidden" name="user_id" value="<?php echo intval($user_id); ?>" />
        <input type="hidden" name="quiz_id" value="<?php echo intval($quiz_id); ?>" />
    </form>
</div>