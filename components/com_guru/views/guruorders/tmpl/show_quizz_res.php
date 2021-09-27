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
JHTML::_('behavior.modal', 'a.modal');
$document = JFactory::getDocument();
JHtml::stylesheet("components/com_guru/css/quiz.css");
$guruModelguruOrder = new guruModelguruOrder();

$k = 0;
$n = count($this->ads);    
$quiz_id =  intval(JFactory::getApplication()->input->get("quiz_id", ""));
$user_id =  intval(JFactory::getApplication()->input->get("cid", ""));
$id =  intval(JFactory::getApplication()->input->get("id", ""));
$quiz_name = $guruModelguruOrder->getQuizNameF($quiz_id);
$score = $guruModelguruOrder->getScoreQuizF($quiz_id, $user_id,$id);
$score = explode("|", $score);

$how_many_right_answers = $score[0];
$number_of_questions = $score[1];
$database = JFactory::getDBO();
$sql = "SELECT max_score FROM #__guru_quiz WHERE id=".$quiz_id;
$database->setQuery($sql);
$result_maxscore = $database->loadResult();
			
if($number_of_questions > 0){
	$score = intval(($how_many_right_answers/$number_of_questions)*100);
}
else{
	$score = 0;
}

if($score >= $result_maxscore){
	$score = $score." %".JText::_('GURU_QUIZ_PASSED');
}
else{
	$score = $score." %".JText::_('GURU_QUIZ_FAILED');
}
$ans_gived =  $guruModelguruOrder->getAnsGivedF($user_id,$id);
$ans_right =  $guruModelguruOrder->getAnsRightF($quiz_id);
$the_question =  $guruModelguruOrder->getQuestionNameF($id,$quiz_id);
$all_answers_array = $guruModelguruOrder->getAllAnsF($quiz_id,$id);
$all_answers_text_array = $guruModelguruOrder->getAllAnsTextF($quiz_id,$id);

for($i=0; $i<$number_of_questions; $i++){  
	$answer_count = 0;
	$all_answers_array_result = explode("|||",$all_answers_array[$i]); 
	$all_answers_text_array_result = explode("|||",$all_answers_text_array[$i]); 
	$ans_right_result = explode("|||", $ans_right[$i]->answers); 
	$ans_gived_result = explode(" ||", $ans_gived[$i]->answers_gived);
	for($t=0; $t<count($ans_gived_result); $t++){
		if($ans_gived_result[$t] != ""){
			if(!in_array($ans_gived_result[$t], $ans_right_result)){
				$gasit = false;
				break;
			}
			else{
				$gasit = true;
				$answer_count++;
			}
		}
	}
			
	@$quiz_result_content .= '<ul class="guru_list">';
	$empty_elements = array("");
	$ans_gived_result = array_diff($ans_gived_result,$empty_elements);
	if(count($ans_gived_result) == $answer_count){
		$quiz_result_content .= '<li class="question right">'. $the_question[$i]->text.'</li>';                                
	}
	else{    
		$quiz_result_content .= '<li class="question wrong g_quize_q">'. $the_question[$i]->text.'</li>';                    
	}
	
	for($j=0; $j<count($all_answers_array_result); $j++){
		if($all_answers_array_result[$j] != "") {
			//--------------------------------------------
			$inArray = in_array($all_answers_array_result[$j], $ans_right_result);
			//-------------------------------------------- 
			if($inArray){
				$quiz_result_content .= '<li class="correct">'.$all_answers_text_array_result[$j].'</li>'; 
			}
			else{
				$quiz_result_content .= '<li class="incorrect">'.$all_answers_text_array_result[$j].'</li>'; 
			}
		}
	}    
	
	$quiz_result_content .= '</ul>';    
}
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div id="editcell" class="guru_quiz_title">
    <div>
        <span style="font-size:16px; padding-left:10px"><?php echo $quiz_name ; ?></span>
        <span class="guru_quiz_title"><?php echo JText::_("GURU_QUIZ_RESULT"); ?>:</span>
        <span class="guru_quiz_score"><?php echo JText::_("GURU_YOUR_SCORE"); ?>: <?php echo $score. "%";?></span>
        <br/>   
        
        <div id="the_quiz"> 
            <?php echo $quiz_result_content;?>
        </div>
    </div>
</div>