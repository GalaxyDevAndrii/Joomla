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
JHtml::_('behavior.framework');

$document = JFactory::getDocument();
JHtml::stylesheet("components/com_guru/css/quiz.css");

$db = JFactory::getDBO();
$sql = "SELECT guru_turnoffjq FROM #__guru_config WHERE id=1";
$db->setQuery($sql);
$db->execute();
$guru_turnoffjq = $db->loadResult();
$guru_turnoffjq = @$guru_turnoffjq["0"];

if(intval($guru_turnoffjq) != 0){
	//$document->addScript('components/com_guru/js/jquery_1_11_2.js');
}
//$document->addScript('components/com_guru/js/open_modal.js');


$k = 0;
$n = count($this->ads);
$cid =  intval(JFactory::getApplication()->input->get("cid", ""));
$stud_name = guruModelguruOrder::getStudNameF($cid);	
$pid =  intval(JFactory::getApplication()->input->get("pid", ""));
$course_name = guruModelguruOrder::getCourseNameF($pid); 


$sql = "select datetype from #__guru_config";
$db->setQuery($sql);
$db->execute();
$datetype = $db->loadColumn();
$datetype = @$datetype["0"];

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

	<style type="text/css">
        body{
            padding: 0px !important;
        }
    </style>

	<script language="javascript" type="text/javascript">
    	function showQuizResult(href){
			jQuery('#myModal .modal-body iframe').attr('src', href);
		}
		
		jQuery('#myModal').on('hide', function () {
			 jQuery('#myModal .modal-body iframe').attr('src', '');
		});
    </script>
    
    <div id="myModal" class="modal hide" style="display:none; height:90%;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" style="height:100%;">
        <iframe id="g_list_quiz" style="width:100%; height:90%; border:none;"></iframe>
        </div>
    </div>
    
    <div id="editcell">
        <table width="100%">
            <tr>
            	<td align="left" style="font-size:20px;"><?php echo JText::_('GURU_QUIZZES_RESULTS');?></td>
            </tr>
        </table>
        <table>
		<tr>
			<td align="left" style="font-size:14px"><?php echo JText::_('GURU_NAME');?>: </td>
			<td align="left" style="font-size:14px"><?php echo $stud_name;?></td>
		</tr><br/>	
		<tr>	
			<td align="left" style="font-size:14px"><?php echo JText::_('GURU_COURSE_NAMEF');?>:</td>
			<td align="left" style="font-size:14px"><?php echo $course_name;?></td>
		</tr>
		<tr height="10px"></tr>
        </table>
            <table class="guru_orders">
                <thead>
                    <tr class="guru_orderhead">
                        <th width="25%"><?php echo JText::_('GURU_QUIZ_NAME');?></th>
                        <th width="15%"><?php echo JText::_('GURU_QUIZ_DATE_TIME_TAKEN');?></th>
						<th width="15%"><?php echo JText::_('GURU_QUIZ_SCORE');?></th>
						<th width="15%"><?php echo JText::_('GURU_QUIZ_SHOW_RESULTS');?></th>
                    </tr>
                </thead>                
                <tbody>
                <?php
					$k = 0;
					$database = JFactory::getDBO();
                    for ($i = 0; $i < $n; $i++){
					$value = $this->ads;
			        $quiz_name = guruModelguruOrder::getQuizNameF($value[$i]->quiz_id);
					$score = $value[$i]->score_quiz;
					$score = explode("|",$score );
					$how_many_right_answers = $score[0];
					$number_of_questions = $score[1];		
					
					$sql = "SELECT max_score FROM #__guru_quiz WHERE id=".$value[$i]->quiz_id;
					$database->setQuery($sql);
					$result_maxscore = $database->loadResult();
					
					if($number_of_questions != 0){
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
					$class = "odd";
					if($k%2 != 0){
						$class = "even";
					}
                ?>
                    <tr class="guru_row <?php echo $class; ?>" colspan ="4" >
                         <td class="guru_result_listq" align="left"><?php echo $quiz_name;?></td>		
                         <td class="guru_result_listq" align="left"><?php echo date($datetype, strtotime($value[$i]->date_taken_quiz));?></td>  
						 <td class="guru_result_listq" align="left"><?php echo $score;?></td>  
						 <td class="guru_result_listq" align="left">
                         	<a data-toggle="modal" data-target="#myModal" onclick="showQuizResult('<?php echo JUri::root() ?>index.php?option=com_guru&view=guruOrders&task=show_quizz_res&cid=<?php echo intval($value[$i]->user_id); ?>&pid=<?php echo $pid; ?>&quiz_id=<?php echo $value[$i]->quiz_id; ?>&id=<?php echo $value[$i]->id; ?>&tmpl=component')" href="#">
								<?php echo JText::_('GURU_SHOW_QUIZ_RESULTS'); ?>
                            </a>
                            
                            <!-- <a class="modal2" rel="{handler: 'iframe', size: {x: 550, y: 450}}" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruOrders&task=show_quizz_res&cid=".intval($value[$i]->user_id)."&pid=".$pid."&quiz_id=".$value[$i]->quiz_id."&id=".$value[$i]->id."&tmpl=component"); ?>"><?php echo JText::_('GURU_SHOW_QUIZ_RESULTS'); ?></a> -->
						</td>
                    </tr>
                <?php 
					$k++;
                    }//end for
					$k++;
                ?>
                    <tr>
                        <td colspan="4"><?php //echo $this->pagination->getListFooter(); ?></td>
                    </tr>
                </tbody>
            </table>
    </div>