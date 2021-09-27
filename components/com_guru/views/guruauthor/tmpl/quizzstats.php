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
$quizz_name = $this->quizz_name;
$total_students = $this->total_students;
$score_to_pass = $this->score_to_pass;
$avg_score = $this->avg_score;
$students_pass = $this->students_pass;
$students_failed = $this->students_failed;

$link1 = JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents&qid=".$id."&action=passed");
$link2 = JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents&qid=".$id."&action=failed");

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div class="g_row">
	<div class="g_cell span12">
		<div>
			<div>
                <div id="g_myquizzesstats" class="clearfix com-cont-wrap">
                    <?php 	echo $div_menu; //MENU TOP OF AUTHORS?>
                    <div class="profile_page page_title">
                        <h2><?php echo JText::_('GURU_STATS_QUIZ').' "'.$quizz_name.'"'; ?></h2>
                    </div>
                    
                    <div id="g_mycoursesauthorcontent" class="g_sect clearfix">
                        <form  action="<?php echo JUri::root() ?>index.php" class="form-horizontal" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data">
                        	<div class="clearfix">
                                    <div class="g_table_wrap">
                                        <table class="table table-striped">
                                            <tr class="g_table_header">
                                                <th class="g_cell_1">#<?php echo JText::_("GURU_COU_STUDENTS"); ?></th>
                                                <th class="g_cell_2"><?php echo JText::_('GURU_SCORE_TO_PASS'); ?></th>
                                                <th class="g_cell_3"><?php echo JText::_("GURU_AVG_SCORE"); ?></th>
                                                <th class="g_cell_4"><?php echo JText::_("GURU_PASS")." / ".JText::_("GURU_COU_STUDENTS"); ?></th>
                                                <th class="g_cell_5"><?php echo JText::_("GURU_FAILED")." / ".JText::_("GURU_COU_STUDENTS"); ?></th>
                                            </tr>
                           					<tr class="guru_row">
                                            	<td class="g_cell_1">
													<?php echo $total_students; ?>
                                                </td> 
                                                <td class="g_cell_2">
                                                    <?php echo $score_to_pass."%"; ?>
                                                </td> 
                                                <td class="g_cell_3">
                                                	<?php echo $avg_score."%"; ?>
                                                </td> 
                                                <td class="g_cell_4"> 
													 <?php
                                                        $percent = 0;
                                                        if($total_students > 0){
                                                            $percent = ($students_pass * 100) / $total_students;
                                                            $percent = number_format((float)$percent, 2, '.', '');
                                                        }
														
														if($students_pass > 0){
															echo $percent."% / ".'<a href='.$link1.'>'.$students_pass.'</a>';
														}
														else{
                                                        	echo $percent."% / 0";
														}
                                                    ?> 
                                                </td>
                                                <td class="g_cell_5">
                                                <?php
													$percent = 0;
													if($total_students > 0){
														$percent = ($students_failed * 100) / $total_students;
														$percent = number_format((float)$percent, 2, '.', '');
													}
													
													if($students_failed > 0){
														echo $percent."% / ".'<a href='.$link2.'>'.$students_failed.'</a>';
													}
													else{
														echo $percent."% / 0";
													}
												?>
                                                </td>         
                                        	</tr>
                                    </table>
                                </div>
                            </div>
                                       
                            <input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get("task", ""); ?>" />
                            <input type="hidden" name="option" value="com_guru" />
                            <input type="hidden" name="controller" value="guruAuthor" />
                        </form>
                   </div>  
              </div>
           </div>   
		</div>
	</div>
 </div>                   