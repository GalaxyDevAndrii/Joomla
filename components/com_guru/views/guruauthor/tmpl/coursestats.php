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
$course_name = $this->course_name;
$total_students = $this->total_students;
$student_complete = $this->student_complete;
$quizzes = $this->quizzes;
$score = $this->score;
$final_exam = $this->final_exam;
$array = $this->pass;
$pass = @$array["percent"];
$total_pass = @$array["total"];

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div class="gru-mycoursesauthor">
    <?php echo $div_menu; //MENU TOP OF AUTHORS ?>
    
    <h2 class="gru-page-title"><?php echo JText::_('GURU_STATS_REPORT').' "'.$course_name.'"'; ?></h2>
    
    <div id="g_course_stats" class="g_sect clearfix">
        <form action="<?php echo JUri::root() ?>index.php" class="form-horizontal" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="uk-table uk-table-striped wk-table wk-table-striped">
                <tr>
                    <th class="g_cell_1">#<?php echo JText::_("GURU_COU_STUDENTS"); ?></th>
                    <th class="g_cell_2"><?php echo JText::_('GURU_STUDENTS_COMPLETED'); ?></th>
                    <th class="g_cell_3"><?php echo JText::_("GURU_STUDENTS_NOT_COMPLETED"); ?></th>
                    <th class="g_cell_4"><?php echo JText::_('GURU_STUDENTS_QUIZZES_SCORE'); ?></th>
                    <th class="g_cell_5"><?php echo JText::_('GURU_STUDENTS_FINAL_EXAM_PASS'); ?></th>
                </tr>
                
                <tr class="guru_row">
                    <td class="g_cell_1">
                        <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents&cid=".intval($id)); ?>">
                            <?php echo $total_students; ?>
                        </a>
                    </td>
                    <td class="g_cell_2">
                        <?php
                            if($student_complete == 0){
                                echo "0";
                            }
                            else{
                        ?>
                                <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents&cid=".intval($id)."&action=complete"); ?>">
                                    <?php echo $student_complete; ?>
                                </a>
                        <?php
                            }
                            $percent = 0;
                            if(intval($total_students) != 0){
                                $percent = ($student_complete * 100) / $total_students;
                            }
                            $percent = number_format($percent, 2, '.', '');
                            echo " (".$percent."%)";
                        ?>
                    </td>
                    <td class="g_cell_3">
                        <?php
                            $not_complete = intval($total_students - $student_complete);
                            if($not_complete < 0 ){
                                $not_complete = 0;
                            }
                        ?>
                        <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents&cid=".intval($id)."&action=notcomplete"); ?>">
                            <?php echo $not_complete; ?>
                        </a>
                        <?php
                            $percent = 0;
                            if(intval($total_students) != 0){
                                $percent = ($not_complete * 100) / $total_students;
                            }
                            $percent = number_format($percent, 2, '.', '');
                            echo " (".$percent."%)";
                        ?>
                    </td>
                    <td class="g_cell_4">
                        <?php
                            if(intval($quizzes) == 0){
                                echo "0";
                            }
                            else{
                        ?>
                                <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes&selectcoursesd=".intval($id)); ?>">
                                    <?php echo $quizzes; ?>
                                </a>
                        <?php
                            }
                            $score = number_format($score, 2, '.', '');
                            echo " / ";
                            echo "(".$score."%)";
                        ?>
                    </td>
                    <td class="g_cell_5">
                        <?php
                            if(intval($final_exam) != 0){
                                echo JText::_("JYES")." / ";
                            }
                            else{
                                echo JText::_("JNO")." ";
                            }
                            
                            if($pass != 0){
                        ?>
                                <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents&cid=".intval($id)."&action=pass"); ?>">
                                    <?php
                                        echo $total_pass;
                                    ?>
                                </a>
                                <?php
                                    $pass = number_format($pass, 2, '.', '');
                                    echo " (".$pass."%)";
                                ?>
                        <?php
                            }
                            else{
                                echo "0 (0%)";
                            }
                        ?>
                    </td>
                  </tr>
            </table>
            <input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get("task", ""); ?>" />
            <input type="hidden" name="option" value="com_guru" />
            <input type="hidden" name="controller" value="guruAuthor" />
        </form>
   </div>  
</div>               