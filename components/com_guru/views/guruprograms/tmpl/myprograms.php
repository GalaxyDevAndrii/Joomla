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
	$k = 0; 
	$program = $this->program;	
	$config = $this->getConfigSettings;
?>

<table cellspacing="0" cellpadding="5" border="0" width="100%">
  <tbody><tr valign="top">
    <td width="100%"><h2><?php echo JText::_('GURU_MYPROGRAMS_MYPROGRAMS'); ?></h2></td>
  </tr>
  <tr valign="top">
    <td><table cellspacing="0" cellpadding="5" border="0" width="100%">
      <tbody><tr bgcolor="#eeeeee" valign="top">
        <td><font size="2"><strong><?php echo JText::_('GURU_MYPROGRAMS_PROGRAM'); ?></strong></font></td>
        <td><font size="2"><strong><?php echo JText::_('GURU_PROGRAM_DETAILS_DAY'); ?></strong></font></td>
        <td><font size="2"><strong><?php echo JText::_('GURU_DAYS_TASK'); ?></strong></font></td>
        <td><font size="2"><strong><?php echo JText::_('GURU_TASKS_TASK_POINTS'); ?></strong></font></td>
        <td><font size="2"><strong><?php echo JText::_('GURU_MYPROGRAMS_START_DATE_TIME'); ?></strong></font></td>
        <td><font size="2"><strong><?php echo JText::_('GURU_MYPROGRAMS_END_DATE_TIME'); ?></strong></font></td>
        <td><font size="2"><strong><?php echo JText::_('GURU_MYPROGRAMS_ACTION'); ?></strong></font></td>
      </tr>
	  <?php foreach($program as $prog) {
	 	
		$show_unpublished = $config[0]->show_unpubl;
		// 0 =  No One
		// 1 Free Trial Only
		// 2 Only Paying Customers
		// 3 All Members (Free Trial and Paying Members)

		if($prog->published=='1' || ($prog->published=='0' && $show_unpublished == '3') || ($prog->published=='0' && $show_unpublished == '2' && $prog->payment!='Trial') || ($prog->published=='0' && $show_unpublished == '1' && $prog->payment=='Trial'))  
		
		{ // IF for SHOW UNPUBLISHED
	  	
		$points_per_program = guruModelguruProgram::getsum_points_and_time_for_program($prog->pid);
		$s = $prog->status; // the status per program can be: 0 = Not started; 1 = In progress; 2 - Completed; (-1) - LOCKED (need to be bought again)
		$p_days = ""; // for each started program we must show the FIRST DAY that hasn't been completed ; DEFAULT is NOTHING
		$p_tasks = ""; // for each started program we must show the FIRST TASK that hasn't been completed ; DEFAULT is NOTHING
		$p_start_date = ""; // for each started program we must show the starting date ; DEFAULT is NOTHING
		$p_end_date = ""; // for each started program we must show the ending date ; DEFAULT is NOTHING
		
		
		
		$day_array = $prog->days;
		$day_array = explode(';', $day_array);
		$how_many_days = count($day_array)-1;
		$day_id_to_get_started_array = explode(',', $day_array[0]); 
		
		//if($s=='0') $p_days = JText::_('GURU_PROGRAM_DETAILS_DAY').' 0 '.JText::_('GURU_MYPROGRAMS_DAYS_OF').' '.$how_many_days;

		$task_array = $prog->tasks;
		$task_array = explode(';', $task_array);
		$task_id_array = explode('-', $task_array[0]);
		$task_id_to_get_started_array = explode(',', $task_id_array[0]); 
		
		// we find the id for the first day who isn't completed

		
		if($s=='1')
			{	
				$first_day_uncompleted = guruModelguruProgram::find_id_for_first_uncompleted_day($day_array);
				$first_day_uncompleted = explode(',', $first_day_uncompleted);
				$id_for_first_day_uncompleted = $first_day_uncompleted[0];
				$ordering_for_first_day_uncompleted = $first_day_uncompleted[1];
				$p_days = JText::_('GURU_PROGRAM_DETAILS_DAY').' '.$ordering_for_first_day_uncompleted.' '.JText::_('GURU_MYPROGRAMS_DAYS_OF').' '.$how_many_days;

				$first_task_uncompleted = guruModelguruProgram::find_id_for_first_uncompleted_task(explode('-',$task_array[($ordering_for_first_day_uncompleted-1)]));
				$first_task_uncompleted = explode(',', $first_task_uncompleted);
				$id_for_first_task_uncompleted = $first_task_uncompleted[0];
				$ordering_for_first_task_uncompleted = $first_task_uncompleted[1];
				$how_many_tasks = count(explode('-',$task_array[($ordering_for_first_day_uncompleted-1)]))-1;
				$p_tasks = JText::_('GURU_DAYS_TASK').' '.$ordering_for_first_task_uncompleted.' '.JText::_('GURU_MYPROGRAMS_DAYS_OF').' '.$how_many_tasks;
				
				$p_start_date = date($config[0]->datetype, strtotime($prog->startdate));
			}
			
		if($s=='2')
			{
				$p_days = JText::_('GURU_MYPROGRAMS_COMPLETED');
				$p_tasks = JText::_('GURU_MYPROGRAMS_COMPLETED');
				$p_start_date = date($config[0]->datetype, strtotime($prog->startdate));
				$p_end_date = date($config[0]->datetype, strtotime($prog->enddate));
				//$p_start_date = $prog->startdate;
				//$p_end_date = $prog->enddate;
			}	
			
		if($s=='-1')
			{
				$p_days = JText::_('GURU_MYPROGRAMS_COMPLETED');
				$p_tasks = JText::_('GURU_MYPROGRAMS_COMPLETED');
				$p_start_date = date($config[0]->datetype, strtotime($prog->startdate));
				$p_end_date = date($config[0]->datetype, strtotime($prog->enddate));				
			}
			
	  ?>
      <tr  bgcolor="<?php if($k%2==0) echo "#f7f7f7"; else echo "#eeeeee"; ?>" valign="top">
        <td><a href="<?php echo JUri::root() ?>index.php?option=com_guru&view=guruPrograms&task=view&cid=<?php echo $prog->pid; ?>"><?php echo $prog->name; ?></a></td>
        <td><?php echo $p_days;?></td>
        <td><?php echo $p_tasks;?></td>
        <td><?php echo $points_per_program[0]->s_points; ?></td>
        <td><?php echo $p_start_date;?></td>
        <td><?php echo $p_end_date;?></td>
        <td>
		<?php if ($s=='0' && isset($task_id_to_get_started_array[0]) && $task_id_to_get_started_array[0]>0 && isset($day_id_to_get_started_array[0]) && $day_id_to_get_started_array[0]>0) { ?><a href="index.php?option=com_guru&view=guruTasks&task=view&cid=1&pid=<?php echo $day_id_to_get_started_array[0]; ?>"><?php echo JText::_('GURU_MYPROGRAMS_ACTION_GETSTARTED');?></a><?php } ?>
		<?php if ($s=='1' && isset($id_for_first_task_uncompleted) && $id_for_first_task_uncompleted>0 && isset($id_for_first_day_uncompleted) && $id_for_first_day_uncompleted>0) { ?><a href="index.php?option=com_guru&view=guruTasks&task=view&cid=<?php echo $ordering_for_first_task_uncompleted; ?>&pid=<?php echo $id_for_first_day_uncompleted; ?>"><?php echo JText::_('GURU_MYPROGRAMS_ACTION_CONTINUE');?></a><?php } ?>
		<?php if ($s=='2' && isset($task_id_to_get_started_array[0]) && $task_id_to_get_started_array[0]>0 && isset($day_id_to_get_started_array[0]) && $day_id_to_get_started_array[0]>0) { ?><a href="index.php?option=com_guru&view=guruTasks&task=view&cid=1&pid=<?php echo $day_id_to_get_started_array[0]; ?>&s=0"><?php echo JText::_('GURU_MYPROGRAMS_ACTION_STARTAGAIN');?></a><?php } ?>
		<?php if ($s=='-1') { ?><a href="<?php echo JUri::root() ?>index.php?option=com_guru&view=guruProfile&task=buy&cid=<?php echo $prog->pid; ?>"><?php echo JText::_('GURU_MYPROGRAMS_ACTION_BUYAGAIN');?></a><?php } ?>
		</td>
      </tr>
	  <?php $k++; 
	  	}  // IF for SHOW UNPUBLISHED
	  } // end foreach ?>

      <tr valign="top">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </tbody></table></td>
  </tr>
  <tr valign="top">
    <td><div align="right"></div></td>
  </tr>
</tbody></table>