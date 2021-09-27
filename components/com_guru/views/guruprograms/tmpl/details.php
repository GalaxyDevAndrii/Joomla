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
$pdays = $this->pdays;	
$number_of_days_per_program = count($pdays);
// how many points and how much time has a program
$getsum_points_and_time = $this->getsum_points_and_time;
// we divide the time in HOURS and MINUTES
$total_hours_per_program = floor($getsum_points_and_time[0]->s_time/60);
$total_minutes_per_program = $getsum_points_and_time[0]->s_time%60;
$configs = $this->getConfigSettings;
$full_image_size = $configs[0]->prog_fullpx;
$full_image_proportional = $configs[0]->prog_f_prop;
$thumb_image_size = $configs[0]->prog_thumbpx;
$thumb_image_proportional = $configs[0]->prog_t_prop;
//echo '<font color="red">'.$program->freetrial.'</font>';

// if it's not a registered user we redirect him to REGISTER page
$my = JFactory::getUser();
$program_bought = guruModelguruProgram::find_if_rogram_was_bought($my->id, $program->id);
if($my->id<1) {header("Location: index.php?option=com_user&task=register#content");}

$document	= JFactory::getDocument();
$document->setTitle($program->metatitle);
$document->setMetaData('keywords', $program->metakwd); 
$document->setMetaData('description', $program->metadesc); 
?>

<h2><?php echo $program->name;?></h2>
<table class="" cellpadding="0" cellspacing="5" border="0">
<tr>
 <td valign="top" width="15%">	<?php 
	// we display the image asociated to this "Program"
	if ($program->image) { 
	
	// generating thumb image - start
	$img_size = @getimagesize(JURI::base().$configs[0]->imagesin.'/'.$program->image);
	$img_width = $img_size[0];
	$img_height = $img_size[1];
	if($img_width>0 && $img_height>0)
	{
		if($full_image_proportional=='w')
			{
				$thumb_width = $full_image_size;
				$thumb_height = $img_height / ($img_width/$full_image_size);
			}
		elseif($full_image_proportional=='h')	
			{
				$thumb_height = $full_image_size;
				$thumb_width = $img_width / ($img_height/$full_image_size);		
			}
		
		$image_to_thumb = JURI::base().$configs[0]->imagesin.'/'.$program->image;
		$image_full_thumb = guruHelper::create_thumbnails($image_to_thumb, $thumb_width, $thumb_height,$img_width,$img_height, 'full_');
		$prog_image = '<img style="margin:5px;" border="0" alt="t" src="'.JURI::base().$configs->imagesin.DIRECTORY_SEPARATOR.$image_full_thumb.'" />';
	}
	else
		$prog_image = '';
	// generating thumb image - stop		
	
	?>
	 <?php echo $prog_image; /* <img  <?php echo $full_image_proportional.'="'.$full_image_size.'"';?>  style="margin:5px;" border="0" alt="" src="<?php echo JURI::base().'/images/stories/'.$program->image?>" /> */ 
	 ?>
	<?php } //else {echo "NO PICTURE!"; } ?>
	<br /><br />
</td>
<td width="85%" valign="top">
<?php echo stripslashes($program->introtext);?><br>
	<table cellspacing="0" cellpadding="5" bordercolor="#d4d0c8" border="0" width="100%">
                <tr>
                  <td width="28%"><strong><?php echo JText::_('GURU_PROGRAM_DETAILS_TOTAL_POINTS'); ?></strong></td>
                  <td width="43%"><?php echo $getsum_points_and_time[0]->s_points; ?></td>
                  <td width="29%" rowspan="3"><table cellspacing="5" cellpadding="0" border="0" bgcolor="#eeeeee" align="right" width="120">
                    <tr>
                      <td colspan="2"><strong><?php echo JText::_('GURU_PROGRAM_DETAILS_KEY'); ?>: </strong></td>
                    </tr>
                    <tr>
                      <td width="4px" style="background-color:green;">&nbsp;&nbsp;</td>
                      <td><?php echo JText::_('GURU_PROGRAM_DETAILS_KEY_COMPLETE'); ?></td>
                    </tr>
                    <tr>
                      <td width="4px" style="background-color:yellow;">&nbsp;&nbsp;</td>
                      <td><?php echo JText::_('GURU_PROGRAM_DETAILS_KEY_IN_PROGRESS'); ?></td>
                    </tr>
                    <tr>
                      <td width="4px" style="background-color:white;">&nbsp;&nbsp;</td>
                      <td><?php echo JText::_('GURU_PROGRAM_DETAILS_KEY_NOT_STARTED'); ?></td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td><strong><?php echo JText::_('GURU_PROGRAM_DETAILS_TOTAL_TIME'); ?></strong></td>
                  <td><?php if ($total_hours_per_program==1) echo $total_hours_per_program.' '.JText::_('GURU_PROGRAM_DETAILS_HOUR').', '; if ($total_hours_per_program>1) echo $total_hours_per_program.' '.JText::_('GURU_PROGRAM_DETAILS_HOURS').', ';  
				  			if ($total_minutes_per_program==1) echo $total_minutes_per_program.' '.JText::_('GURU_PROGRAM_DETAILS_MINUTE'); else echo $total_minutes_per_program.' '.JText::_('GURU_PROGRAM_DETAILS_MINUTES');
					  ?></td>
                  </tr>
                <tr>
                  <td><strong><?php echo JText::_('GURU_PROGRAM_DETAILS_TOTAL_DAYS'); ?></strong></td>
                  <td><?php echo $number_of_days_per_program; ?></td>
                  </tr>
              </table>
</td>
</tr>
</table>

<table cellspacing="0" cellpadding="5" border="0" width="100%" style="vertical-align:top;">
      <tr bgcolor="#cccccc" valign="top">
        <td><strong><?php echo JText::_('GURU_PROGRAM_DETAILS_DAY'); ?></strong></td>
        <td><strong><?php echo JText::_('GURU_PROGRAM_DETAILS_TITLE'); ?></strong></td>
        <td><strong><?php echo JText::_('GURU_PROGRAM_DETAILS_TOTAL_POINTS'); ?></strong></td>
        <td><strong><?php echo JText::_('GURU_PROGRAM_DETAILS_TIME_MINUTES'); ?></strong></td>
        <td><strong><?php echo JText::_('GURU_PROGRAM_DETAILS_STATUS'); ?></strong></td>
        </tr>
<?php 
if ($program_bought == 0) 
{ // program bought = 0 = trial - begin ?>
     <?php foreach ($pdays as $days) { 
	 $total_for_a_day = guruModelguruProgram::getsum_points_and_time_for_a_day1($days->id);
	 if ($total_for_a_day[0]->s_points) $total_points_for_a_day = $total_for_a_day[0]->s_points; else $total_points_for_a_day = 0;
	 if ($total_for_a_day[0]->s_time) $total_time_for_a_day = $total_for_a_day[0]->s_time; else $total_time_for_a_day = 0;
	 
	 $status_color = "white";
	 // we find the DAY STATUS for the days and we change the COLOR accordingly
	 //if($k<$program->freetrial) {
	 	$day_status = guruModelguruProgram::find_day_status($my->id, $program->id, $days->id);
	 if ($day_status==2) $status_color = "green";
	 if ($day_status==1) $status_color = "yellow";
	// }
	 
	 ?>
      <tr bgcolor="<?php if($k%2==0) echo "#f7f7f7"; else echo "#eeeeee"; ?>" valign="top">
        <td bgcolor="<?php if($k%2==0) echo "#f7f7f7"; else echo "#eeeeee"; ?>" width="15%"><strong><?php echo JText::_("GURU_DAY"); ?> <?php echo $k+1;//$days->id;?>: </strong></td>
        <td bgcolor="<?php if($k%2==0) echo "#f7f7f7"; else echo "#eeeeee"; ?>" width="27%"><?php if($k<$program->freetrial || $program_bought==1) { ?><a href="index.php?option=com_guru&view=guruDays&task=view&cid=<?php echo $days->id;?>"><?php }; // there is no link if it's not FREETRIAL ?><?php echo $days->title;?> <?php if($k<$program->freetrial || $program_bought==1) { ?></a> <?php };  // there is no link if it's not FREETRIAL?></td>
        <td bgcolor="<?php if($k%2==0) echo "#f7f7f7"; else echo "#eeeeee"; ?>" width="17%"><?php echo $total_points_for_a_day;?></td>
        <td bgcolor="<?php if($k%2==0) echo "#f7f7f7"; else echo "#eeeeee"; ?>" width="22%"><?php echo $total_time_for_a_day;?></td>
        <td width="19%"><table align="center"><tr><td width="4px" style="background-color:<?php echo $status_color; ?>;">&nbsp;&nbsp;</td></tr></table></td>
        </tr>
     <?php 
	 $k++;
	 } 
			if($program->freetrial == 1)
				$final_text_for_trial = JText::_('GURU_PROGRAM_TRY_FREE_FOR_BUTTON_3');
			elseif($program->freetrial > 1)
				$final_text_for_trial = JText::_('GURU_PROGRAM_TRY_FREE_FOR_BUTTON_2');		
		
		if(isset($pdays[0]->id))
			if($pdays[0]->id > 0)
			{ // if the program HAS a day
				$link_for_button = JUri::root() . 'index.php?option=com_guru&view=guruDays&task=view&cid='.$pdays[0]->id;
				$get_started = '<input type="submit" onclick="document.location.href=\''.$link_for_button.'\'" value="'.JText::_('GURU_MYPROGRAMS_ACTION_GETSTARTEDWITH').'" name="getstarted"/>';
			}
			else  
			{ // if the program doesn't have a day
				$get_started = '';
			}	
		else
		$get_started = '';
				
		$buy_now_link = JUri::root() . 'index.php?option=com_guru&view=guruProfile&task=account';
		$buy_now = '<input type="submit" onclick="document.location.href=\''.$buy_now_link.'\'" value="'.JText::_('GURU_PROGRAM_BUY_NOW_BUTTON').'" name="Submit"/>';		
} // program bought = 0 = trial - end
else
{ // the program is bought - begin
		$s = guruModelguruProgram::program_status($my->id, $program->id); // the status per program can be: 0 = Not started; 1 = In progress; 2 - Completed
	
		$status_line = guruModelguruProgram::find_status_line_for_program($my->id, $program->id);
		$status_button = guruModelguruProgram::find_link_text_for_day_resume_button($status_line->days, $status_line->tasks, $s);
		$status_button = explode('$$$$$', $status_button);
		$link_for_resume = $status_button[0];
		$text_for_resume = $status_button[1];
	
	//$link_for_resume = 'index.php?option=com_guru&view=guruTasks&task=view&cid='.$first_uncompl_task.'&pid='.$first_uncompl_day_id[0];
	//$text_for_resume = JText::_('GURU_MYPROGRAMS_ACTION_RESUME');
	$buy_now = '';
	$get_started = '<input type="submit" onclick="document.location.href=\''.$link_for_resume.'\'" value="'.$text_for_resume.'" name="getstarted"/>';
    $status_line->days = substr($status_line->days, 0, strlen($status_line->days)-1); // we remove the last ';'
	$status_line->days = explode(';',$status_line->days);
    foreach ($status_line->days as $one_day_obj) 
	{
	 $one_day_obj = explode(',', $one_day_obj);
	 
	 $day_id = $one_day_obj[0];
	 
	 $days = guruModelguruProgram::get_a_day_by_id ($day_id);
	 $task_array = explode(';',$status_line->tasks);
	 $task_array = $task_array[($days->ordering - 1)];
	 $task_array = substr($task_array, 0, strlen($task_array)-1);
	 $task_array = explode('-', $task_array);
	 $new_task_array = '';
	 foreach($task_array as $one_task)
	 	{
			$one_task_array = explode(',', $one_task);
			$new_task_array = $new_task_array.$one_task_array[0].',';
		}
	$new_task_array = substr($new_task_array, 0, strlen($new_task_array)-1);	
	 $total_for_a_day = guruModelguruProgram::getsum_points_and_time_for_a_day2($new_task_array);
	 if ($total_for_a_day[0]->s_points) $total_points_for_a_day = $total_for_a_day[0]->s_points; else $total_points_for_a_day = 0;
	 if ($total_for_a_day[0]->s_time) $total_time_for_a_day = $total_for_a_day[0]->s_time; else $total_time_for_a_day = 0;
	 
	 $status_color = "white";
	 // we find the DAY STATUS for the days and we change the COLOR accordingly
	 //if($k<$program->freetrial) {
	 	$day_status = guruModelguruProgram::find_day_status($my->id, $program->id, $day_id);
	 if ($day_status==2) $status_color = "green";
	 if ($day_status==1) $status_color = "yellow";
	// }
	 
	 ?>
      <tr bgcolor="<?php if($k%2==0) echo "#f7f7f7"; else echo "#eeeeee"; ?>" valign="top">
        <td bgcolor="<?php if($k%2==0) echo "#f7f7f7"; else echo "#eeeeee"; ?>" width="15%"><strong><?php echo JText::_("GURU_DAY"); ?> <?php echo $k+1;//$days->id;?>: </strong></td>
        <td bgcolor="<?php if($k%2==0) echo "#f7f7f7"; else echo "#eeeeee"; ?>" width="27%"><?php if($k<$program->freetrial || $program_bought==1) { ?><a href="<?php echo JUri::root() ?>index.php?option=com_guru&view=guruDays&task=view&cid=<?php echo $day_id;?>"><?php }; // there is no link if it's not FREETRIAL ?><?php echo $days->title;?> <?php if($k<$program->freetrial || $program_bought==1) { ?></a> <?php };  // there is no link if it's not FREETRIAL?></td>
        <td bgcolor="<?php if($k%2==0) echo "#f7f7f7"; else echo "#eeeeee"; ?>" width="17%"><?php echo $total_points_for_a_day;?></td>
        <td bgcolor="<?php if($k%2==0) echo "#f7f7f7"; else echo "#eeeeee"; ?>" width="22%"><?php echo $total_time_for_a_day;?></td>
        <td width="19%"><table align="center"><tr><td width="4px" style="background-color:<?php echo $status_color; ?>;">&nbsp;&nbsp;</td></tr></table></td>
        </tr>
     <?php 
	 $k++;
	 } 
} // the program is bought - end
	 
	 ?> 
	 <tr>
	 	<td colspan="5">
		<?php echo $get_started;?>
		<?php echo $buy_now;?>
		</td>
	</tr>
</table>        