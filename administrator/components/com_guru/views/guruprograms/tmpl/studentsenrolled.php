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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
	JHTML::_('behavior.modal');
	$doc =JFactory::getDocument();
	$guruAdminModelguruProgram = new guruAdminModelguruProgram();
	
	$k = 0;
	$customers 	= $this->customers;
	$filter		= $this->filters;
	$n			= count($customers);
	
	$program = $this->programs;
	$pid =  intval(JFactory::getApplication()->input->get("pid", ""));
	$course_name = $guruAdminModelguruProgram->getCourseName($pid);
	$course_name = '"'.$course_name.'"';
	$ads 	= $this->ads;
	
	$db = JFactory::getDBO();
	$sql = "Select datetype FROM #__guru_config where id=1 ";
	$db->setQuery($sql);
	$format_date = $db->loadColumn();
	$format_date = $format_date[0];
	
	
	?>
<table width="100%">
	<tr>
		<td width="50%">&nbsp;
			
		</td>
	</tr>
</table>
	
<form action="<?php echo JUri::root() ?>administrator/index.php" name="adminForm" id="adminForm" method="post">

<table cellspacing="2" cellpadding="2" bgcolor="#ffffff" style="width: 100%;">
			<tbody>
				<tr>
					<td align="left" style="font-size:20px;"><?php echo JText::_("GURU_STUDENTS_ENROLLED_IN")." ".$course_name." ".JText::_("GURU_COURSE");?></td>
					<td align="right">
						<input type="text" value="<?php echo $filter->search; ?>" name="search"/>&nbsp;&nbsp;
						<input class="btn" type="submit" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" name="submit_search"/>
					</td>					
				</tr>
			</tbody>
</table>
<div id="editcell" >
<table class="table table-striped adminlist">
	<thead>
		<tr>
			<th width="5">
				<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
			</th>
	        <th width="25">
				<?php echo JText::_('GURU_ID');?>
			</th>
			<th>
				<?php echo JText::_('GURU_FULLNAME');?>
			</th>
			<th>
				<?php echo JText::_('GURU_USERNAME');?>
			</th>
			<th>
				<?php echo JText::_('GURU_COURSE_PROGRESS');?>
			</th>
			<th>
				<?php echo JText::_('GURU_LAST_VISIT');?>
			</th>
			<th>
				<?php echo JText::_('GURU_QUIZZES_RESULTS');?>
			</th>			
		</tr>
	</thead>
<?php 
	for ($i = 0; $i < $n; $i++){
		$customers[$i] = (Array)$customers[$i];
		//$customers = (Array)$customers;
		$id = $customers[$i]["id"];
		$checked = JHTML::_('grid.id', $i, $id);	
		$usrlink = JRoute::_("index.php?option=com_users&task=user.edit&id=".$id);
		$lms_link = "index.php?option=com_guru&controller=guruCustomers&task=edit&cid[]=".intval($id);
		
		$completed_progress = $guruAdminModelguruProgram->courseCompleted($id,$pid);
		$date_completed = $guruAdminModelguruProgram->dateCourseCompleted($id, $pid);
		$date_completed = date("".$format_date."", strtotime($date_completed));

		$style_color = "";
		if($completed_progress == true){
			$var_lang = JText::_('GURU_COMPLETED');
			$lesson_module_progress = $var_lang." ". "(".date("Y-m-d", strtotime($date_completed)).")";
			$style_color = 'style="color:#669900"';
		}
		else{
			$lesson_module_progress = $guruAdminModelguruProgram->getLastViewedLessandMod($id, $pid);	
		}	
		
		$date_last_visit = $guruAdminModelguruProgram->dateLastVisit($id, $pid);
		if($date_last_visit !="0000-00-00" && $date_last_visit !=NULL ){
			$date_last_visit = date("".$format_date."", strtotime($date_last_visit));
		}
		else{
			$date_last_visit = "";
		}
		
		$count_quizz_taken = $guruAdminModelguruProgram->countQuizzTaken($id, $pid);				
?>
		<tr class="row<?php echo $k;?>"> 
	    	<td align="center">
	    		<?php echo $checked;?>
			</td>		
	     	<td align="center">
	     		<?php echo $id;?>
			</td >			
	     	<td align="left">
	     		<a class="a_guru" href="<?php echo $lms_link; ?>"><?php echo $customers[$i]["name"]; ?></a>
			</td>		
			<td align="left">
	     		<a class="a_guru" href="<?php echo $usrlink;?>"><?php echo $customers[$i]["username"]; ?></a>
			</td>
			<td <?php echo $style_color; ?> align="left">
	     		<?php if(isset($lesson_module_progress)){
						 echo $lesson_module_progress; 
					  } 
					  else{
					  	echo "";
					  }
				?>
			</td>
			<td>			
				<?php
					echo $date_last_visit;
				 ?>
			</td>
			<td align="left">
			<?php 
			if($count_quizz_taken !=0){?>
            
             <script type="text/javascript">
				width = window.screen.availWidth - 120;
				height = window.screen.availHeight - 180;
				document.write('<a rel="{handler: \'iframe\', size: {x:'+width+', y: '+height+'}}" href="<?php echo JRoute::_("index.php?option=com_guru&controller=guruQuiz&task=listQuizStud&cid=".intval($id)."&pid=".$pid."&tmpl=component"); ?>" class="modal"><?php echo JText::_('GURU_VIEW_QUIZZES_RESULTS')."(".$count_quizz_taken.")";?></a>');
			</script>
			<?php 
			}
			else{
			}
			?>
			</td>				
		</tr>
	<?php 
		$k = 1 - $k;
	}
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
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="show" />
	<input type="hidden" name="pid" value="<?php echo $pid;?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="guruPrograms" />
</form>

<style>
	#sbox-window{
		width: 90% !important;
		height: 90% !important;
		left: 1%;
		margin: auto;
		right: 1%;
	}

	#sbox-content iframe{
		height: 100% !important;
	}
</style>