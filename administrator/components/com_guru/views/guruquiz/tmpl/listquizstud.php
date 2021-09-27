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
$doc->addScript('components/com_guru/js/new_student.js');

?>
<script language="javascript" type="text/javascript">
	Joomla.submitform = function(pressbutton){
		document.getElementById("adminForm").submit();
	}
</script>
<script language="javascript">
	function showContentVideo(href){
		jQuery.ajax({
		  url: href,
		  success: function(response){
		   jQuery('#myModal2 .modal-body').html('');
		   jQuery( '#myModal2 .modal-body').html(response);
		  }
		});
	}
</script>
<style>
	div.modal2{
		left: 4% !important;
		position: fixed;
		top: 8% !important;
		width: 90% !important;
		z-index: 9999;
		background-color:#FFFFFF;
		height:88%;
		
	}
	#the_quiz>ul>li{
		list-style: none outside none;
	}
</style>
<?php
$k = 0;
$n = count($this->ads);
$cid =  intval(JFactory::getApplication()->input->get("cid", "", "raw"));
$stud_name = guruAdminModelguruQuiz::getStudName($cid);	
$pid =  intval(JFactory::getApplication()->input->get("pid", "", "raw"));
$course_name = guruAdminModelguruQuiz::getCourseName($pid); 
$db = JFactory::getDBO();
$sql = "Select datetype FROM #__guru_config where id=1 ";
$db->setQuery($sql);
$format_date = $db->loadColumn();
$format_date = $format_date[0];
?>
<div id="myModal2" class="modal2 hide">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    </div>
    <div class="modal-body">
    </div>
</div>

<form action="<?php echo JUri::root() ?>administrator/index.php" name="adminForm" id="adminForm" method="post">
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
			<td align="left" style="font-size:14px"><?php echo JText::_('GURU_COURSE');?>:</td>
			<td align="left" style="font-size:14px"><?php echo $course_name;?></td>
		</tr>
		<tr height="10px"></tr>
        </table>
            <table class="table table-striped adminlist">
                <thead>
                    <tr>
                        <th width="30%"><?php echo JText::_('GURU_QUIZ_NAME');?></th>
                        <th width="15%"><?php echo JText::_('GURU_QUIZ_DATE_TIME_TAKEN');?></th>
						<th width="15%"><?php echo JText::_('GURU_QUIZ_SCORE');?></th>
						<th width="15%"><?php echo JText::_('GURU_QUIZ_SHOW_RESULTS');?></th>
                    </tr>
                </thead>                
                <tbody>
                <?php
                    for ($i = 0; $i < $n; $i++){
					$value = $this->ads;
			        $quiz_name = guruAdminModelguruQuiz::getQuizName($value[$i]->quiz_id);
					$score = $value[$i]->score_quiz;
                ?>
                    <tr colspan="4"> 
                         <td align="center"><?php echo $quiz_name;?></td>		
                         <td align="center"><?php echo date("".$format_date."", strtotime($value[$i]->date_taken_quiz));?></td>  
						 <td align="center"><?php echo $score."%";?></td>  
						 <td align="center"><a data-toggle="modal" data-target="#myModal2" onclick="showContentVideo('index.php?option=com_guru&controller=guruQuiz&task=show_quizz_res&cid=<?php echo intval($value[$i]->user_id);?>&pid=<?php echo $pid; ?>&quiz_id=<?php echo $value[$i]->quiz_id;?>&id=<?php echo $value[$i]->id?>&tmpl=component&format=raw')"><?php echo JText::_('GURU_SHOW_QUIZ_RESULTS'); ?></td>            
                    </tr>
                <?php 
                    }//end for
                ?>
                    
                </tbody>
                <tfoot>
		<tr>
            <td colspan="10">
                <div class="pagination pagination-toolbar">
                    <?php echo $this->pagination->getListFooter(); ?>
                </div>
                <div class="btn-group pull-left">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                    <?php echo $this->pagination->getLimitBox(); ?>
               </div>
            </td>
        </tr>
	</tfoot>
            </table>
    </div>
    <input type="hidden" name="controller" value="guruQuiz" />
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="task" value="listQuizStud" />
    <input type="hidden" name="cid" value="<?php echo JFactory::getApplication()->input->get("cid", "0", "raw"); ?>" />
    <input type="hidden" name="pid" value="<?php echo JFactory::getApplication()->input->get("pid", "0", "raw"); ?>" />
    <input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
</form>    