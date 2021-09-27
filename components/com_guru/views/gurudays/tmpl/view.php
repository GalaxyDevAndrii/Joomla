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

// if it's not a registered user we redirect him to REGISTER page
$my = JFactory::getUser();
$data_get = JFactory::getApplication()->input->get->getArray();
if($my->id<1) {header("Location:".JUri::root()."index.php?option=com_guru&view=guruProfile&task=account");}

$forbidden = 0;
$k = 0;
$total_points = 0;
$total_time = 0;
$programname = $this->programname;	

$day = $this->day;	
// if the DAY is not a FREETRIAL then we don't display the DAYS page, we display a FORBIDDEN message
if($day->ordering>$programname[0]->freetrial) $forbidden = 1;

// to display the audio/video in a gray-box - begin

$document = JFactory::getDocument();
$document->addScript("components/com_guru/js/modal.js");
JHtml::stylesheet("components/com_guru/css/modal.css");
JHTML::_('behavior.modal');
// to display the audio/video in a gray-box - end			
?>





<script type="text/javascript">

function do_the_change(id){
document.getElementById('_media_'+id).style.display = '';
document.getElementById('media_thumbnail'+id).style.display = 'none';
}		
</script>	

<?php



//$listTask = $this->listTask;	

$configs = guruModelguruDays::getConfigs();
$full_image_size = $configs->days_fullpx;
$full_image_proportional = $configs->days_f_prop;
$thumb_image_size = $configs->days_thumbpx;
$thumb_image_proportional = $configs->days_t_prop;

$document	= JFactory::getDocument();
$document->setTitle($day->metatitle);
$document->setMetaData('keywords', $day->metakwd); 
$document->setMetaData('description', $day->metadesc); 
$guruHelper = new guruHelper();
// parsing the media - begin
if($configs->display_media==1){
$the_media_object = guruModelguruDays::find_intro_media($day->id);

$aheight=0; $awidth=0; $vheight=0; $vwidth=0;
$media = '';
$no_plugin_for_code = 0;
$the_media_order = 0;
foreach($the_media_object as $the_media)
if(isset($the_media))
	{
	
	$the_media->code = stripslashes($the_media->code);	
	if($the_media->type=='video')
		{
			$the_media_order ++;

			if ($the_media->source=='url' || $the_media->source=='local')
				{
					if ($the_media->width == 0 || $the_media->height == 0) 
						{
							$vheight=300; $vwidth=400;
						}
					else
						{
							$vheight=$the_media->height; $vwidth=$the_media->width;
						}		
				}
			elseif ($the_media->source=='code')
				{
					if ($the_media->width == 0 || $the_media->height == 0) 
						{
							$begin_tag = strpos($the_media->code, 'width="');
							if ($begin_tag!==false)
								{
									$remaining_code = substr($the_media->code, $begin_tag+7, strlen($the_media->code));
									$end_tag = strpos($remaining_code, '"');
									$vwidth = substr($remaining_code, 0, $end_tag);
									
									$begin_tag = strpos($the_media->code, 'height="');
									if ($begin_tag!==false)
										{
											$remaining_code = substr($the_media->code, $begin_tag+8, strlen($the_media->code));
											$end_tag = strpos($remaining_code, '"');
											$vheight = substr($remaining_code, 0, $end_tag);
											$no_plugin_for_code = 1;
										}
									else
										{$vheight=300; $vwidth=400;}	
								}	
							else
								{$vheight=300; $vwidth=400;}	
						}
					else	
						{
							$replace_with = 'width="'.$the_media->width.'"';
							$the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
							$replace_with = 'height="'.$the_media->height.'"';
							$the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);
							$vheight=$the_media->height; $vwidth=$the_media->width;						
						}
				}	
		
		if($configs->video_display == '0')		
			{
				//$media = guruHelper::create_media_using_plugin($the_media, $configs, $aheight, $awidth, $vheight, $vwidth);
				if($no_plugin_for_code == 0)
				$media_temp = $guruHelper->create_media_using_plugin($the_media, $configs, $aheight, $awidth, $vheight, $vwidth);
				else
				$media_temp = $the_media->code;
				$media = $media.$media_temp;
			}
		elseif($configs->video_display == '1')	
			{
				if($no_plugin_for_code == 0)
				$media_temp = $guruHelper->create_media_using_plugin($the_media, $configs, $aheight, $awidth, $vheight, $vwidth);
				else
				$media_temp = $the_media->code;
							
				$media_temp = str_replace('&hl=en&fs=1','&hl=en&fs=1&autoplay=1',$media_temp);
				$media_temp = str_replace('name=\"autoplay\" value=\"false\"', 'name=\"autoplay\" value=\"true\"',$media_temp);

				$movie_thumbnail = $guruHelper->generateVideoThumbnail( $the_media_order );
				$media1 = '<div background-color:#FF0000;" id="media_thumbnail'.$the_media_order.'">'.$movie_thumbnail.'</div>';
				$media2 = '<div id="_media_'.$the_media_order.'" style="display:none">'.$media_temp.'</object></div>';
				
				$media = $media.$media1.$media2;		
			}			
		elseif($configs->video_display == '2')	
			$media = $media.'<a rel="{handler: \'iframe\', size: {x: '.($vwidth+100).', y: '.($vheight+50).'}, iframeOptions: {id: \'g_play_video\'}}" href="'.JUri::root().'index.php?option=com_guru&view=guruDays&task=preview&no_html=1&cid='.$the_media->id.'" class="modal">'.JText::_('Play video file').'</a>';	

		}		
	elseif($the_media->type=='audio')
		{
			if ($the_media->source=='url' || $the_media->source=='local')
				{	
					if ($the_media->width == 0 || $the_media->height == 0) 
						{
							$aheight=20; $awidth=300;
						}
					else
						{
							$aheight=$the_media->height; $awidth=$the_media->width;
						}
				}		
			elseif ($the_media->source=='code')
				{
					if ($the_media->width == 0 || $the_media->height == 0) 
						{
							$begin_tag = strpos($the_media->code, 'width="');
							if ($begin_tag!==false)
								{
									$remaining_code = substr($the_media->code, $begin_tag+7, strlen($the_media->code));
									$end_tag = strpos($remaining_code, '"');
									$awidth = substr($remaining_code, 0, $end_tag);
									
									$begin_tag = strpos($the_media->code, 'height="');
									if ($begin_tag!==false)
										{
											$remaining_code = substr($the_media->code, $begin_tag+8, strlen($the_media->code));
											$end_tag = strpos($remaining_code, '"');
											$aheight = substr($remaining_code, 0, $end_tag);
											$no_plugin_for_code = 1;
										}
									else
										{$aheight=20; $awidth=300;}	
								}	
							else
								{$aheight=20; $awidth=300;}							
						}
					else	
						{					
							$replace_with = 'width="'.$the_media->width.'"';
							$the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
							$replace_with = 'height="'.$the_media->height.'"';
							$the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);
							$aheight=$the_media->height; $awidth=$the_media->width;
						}
				}	
		if($configs->audio_display == '0')		
			$media = $guruHelper->create_media_using_plugin($the_media, $configs, $aheight, $awidth, $vheight, $vwidth);
		else
			$media = '<a rel="{handler: \'iframe\', size: {x: '.($awidth+100).', y: '.($aheight+50).'}, iframeOptions: {id: \'lesson_editplans\'}}" href="'.JUri::root().'index.php?option=com_guru&view=guruDays&task=preview&no_html=1&cid='.$the_media->id.'" class="modal">'.JText::_('Play audio file').'</a>';	
		}		
	
	$media = $media.'<br />';
	}
	else
	$media = '';
}
elseif($configs->display_media==0){
$media = '';
} 

//echo $media;

// parsing the media - end

$program_bought = guruModelguruDays::find_if_rogram_was_bought($my->id, $programname[0]->id);
?>

<?php if($forbidden && $program_bought==0) { // if the DAY is not a FREETRIAL then we display a FORBIDDEN message?>
<table cellspacing="0" cellpadding="5" border="0" width="100%" style="height:300px; ">
  <tr>
  	<td valign="top">
		<font color="#FF0000"><?php echo JText::_('GURU_DAYS_FORBIDDEN_ACCESS'); ?></font>
	</td>
  </tr>
</table>  		
<?php }else { // if the DAY is a FREETRIAL then we display the DAY 
$daylayout = $configs->daypage; 
$daylayout = str_replace ('{program_name}','<h2>'.$programname[0]->name.'<h2>',$daylayout);
$day_name = '<span class="dayname">'.JText::_('GURU_PROGRAM_DETAILS_DAY').' '.$day->ordering.'</span>';
$day_title = '<span class="daytitleheading">'.$day->title.'</span>';
$daylayout = str_replace ('{day_name}',$day_name,$daylayout);
$daylayout = str_replace ('{day_title}',$day_title,$daylayout);
$daylayout = str_replace('{media}',$media,$daylayout);

// generating thumb image - start
$img_size = @getimagesize(JURI::base().$configs->imagesin.'/'.$day->image);
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
	
	$image_to_thumb = JURI::base().$configs->imagesin.'/'.$day->image;
	$image_full_thumb = $guruHelper->create_thumbnails($image_to_thumb, $thumb_width, $thumb_height,$img_width,$img_height, 'full_');
	$cfg_image = '<img style="margin:5px;" border="0" alt="" src="'.JURI::base().'images'.DIRECTORY_SEPARATOR.'stories'.DIRECTORY_SEPARATOR.$image_full_thumb.'" />';
}
else
	$cfg_image = '';

// generating thumb image - stop	

/*
if ($day->image)
	$cfg_image = '<img '.$full_image_proportional.'="'.$full_image_size.'" class="dayimage" alt="" src="'.JURI::base().'/'.$configs->imagesin.'/'.$day->image.'" />';
else $cfg_image = '';	
*/

///////// - we find the link for RESUME -  BEGIN ///////////
		$s = guruModelguruDays::program_status($my->id, $programname[0]->id); // the status per program can be: 0 = Not started; 1 = In progress; 2 - Completed; (-1) = Locked - he needs to re-buy for use it

if(isset($s))
	{ // if there is a status line - begin
		$status_line = guruModelguruDays::find_status_line_for_program($my->id, $programname[0]->id);
		$status_button = guruModelguruDays::find_link_text_for_day_resume_button($status_line->days, $status_line->tasks, $s);
		$status_button = explode('$$$$$', $status_button);
		$link_for_resume = $status_button[0];
		$text_for_resume = $status_button[1];
	
	}// if there is a status line - end
else
	{ // there is no status line = he can do a trial - begin
		$status_line = guruModelguruDays::create_status_line_for_program ($programname[0]->id);
		$status_line = explode('$$$$$', $status_line);
		$day_array = explode(';', $status_line[0]);
		$day_id_to_get_started_array = explode(',', $day_array[0]); 
		$task_array = explode(';', $status_line[1]);
		$task_id_array = explode('-', $task_array[0]);
		$task_id_to_get_started_array = explode(',', $task_id_array[0]); 		
		
		$link_for_resume = JUri::root() . 'index.php?option=com_guru&view=guruTasks&task=view&cid=1&pid='.$day_id_to_get_started_array[0].'&t=1';
		$text_for_resume = JText::_('GURU_MYPROGRAMS_ACTION_STARTTRIAL');
	} // there is no status line = he can do a trial - end
///////// - we find the link for RESUME -  END ///////////			


$daylayout = str_replace ('Tasks',JText::_('GURU_DAYS_TASKS'),$daylayout);
$daylayout = str_replace ('{full_image}',$cfg_image,$daylayout);	
//$daylayout = str_replace ('{tasks}','<h1>'.JText::_('GURU_DAYS_TASKS').':</h1>',$daylayout);	
$daylayout = str_replace ('{day_description}',stripslashes($day->description),$daylayout);	
$daybutton = '<input type="submit" onclick="window.location=\''.$link_for_resume.'\'" value="'.$text_for_resume.'" name="Submit" />';
$daylayout = str_replace ('{button}',$daybutton,$daylayout);
$status_color = "#DDDDDD";
$colorkey = '<table cellspacing="5" cellpadding="0" border="0" bgcolor="#eeeeee" align="right" width="120">
                    <tr>
                      <td colspan="2"><strong>'.JText::_('GURU_PROGRAM_DETAILS_KEY').'</strong></td>
                    </tr>
                    <tr>
                      <td width="4" style="background-color: '.$configs->st_donecolor.'">&nbsp;&nbsp;</td>
                      <td>'.JText::_('GURU_PROGRAM_DETAILS_KEY_COMPLETE').'</td>
                    </tr>
                    <tr>
                      <td width="4" style="background-color: '.$configs->st_notdonecolor.';"></td>
                      <td>'.JText::_('GURU_PROGRAM_DETAILS_KEY_IN_PROGRESS').'</td>
                    </tr>
                    <tr>
                      <td width="4" style="background-color: '.$status_color.';"></td>
                      <td>'.JText::_('GURU_PROGRAM_DETAILS_KEY_NOT_STARTED').'</td>
                    </tr>
       		</table>';
$daylayout = str_replace ('{key}',$colorkey,$daylayout);
$row_tasks = '';

$the_task_list = guruModelguruDays::getlistTask(intval($data_get['cid']));

if(isset($the_task_list))
{// if trial or subscriber - begin
$the_task_list = explode(';', $the_task_list );
$the_task_list = $the_task_list[($day->ordering-1)];
$the_task_list = explode('-', $the_task_list );

if(isset($s) && $s=='-1')
	$force_guest_for_a_rebuy = 'guest';
else
	$force_guest_for_a_rebuy = '';	

foreach ($the_task_list as $one_task_obj) 
	{ 
	if($one_task_obj)
		{ // begin if
			$one_task_obj = explode(',', $one_task_obj);
			$task = guruModelguruDays::getTask($one_task_obj[0]);
			
			$task_status = $one_task_obj[1];
			
	 		
			if ($task_status==2) $status_color = $configs->st_donecolor;
	 		else $status_color = $configs->st_notdonecolor;
	  		
			if($k%2==0) $rowcolor = "#f7f7f7"; else $rowcolor = "#eeeeee";
	  $row_tasks = $row_tasks.'
      <tr bgcolor="'.$rowcolor.'" valign="top">
        <td width="14%"><strong>'.JText::_('GURU_DAYS_TASK').' '.($k+1).':</strong></td>
        <td></td>
        <td width="42%"><a href="'.JUri::root().'index.php?option=com_guru&view=guruTasks&task=view'.$force_guest_for_a_rebuy.'&cid='.($k+1).'&pid='.$day->id.'">'.$task->name.'</a></td>
        <td width="15%">'.$task->points.'</td>
        <td width="12%">'.$task->time.'</td>
        <td width="9%">
			<table align="center"><tr><td width="4px" style="background-color:'.$status_color.'">&nbsp;&nbsp;</td></tr></table>
		</td>
      </tr>';
	  
	$total_points+=$task->points;
	$total_time+=$task->time;
	$k++;
		} // end if
		
	}
// if trial or subscriber - end
}
else
{
	// guest - begin
	$task_list_for_day = guruModelguruDays::find_tasks_for_aday(intval($data_get['cid']));
	foreach ($task_list_for_day as $one_task_obj) 
		{ 
		if($one_task_obj)
			{ // begin if
				$task = guruModelguruDays::getTask($one_task_obj);

				$status_color = $configs->st_notdonecolor;
				
				if($k%2==0) $rowcolor = "#f7f7f7"; else $rowcolor = "#eeeeee";
		  $row_tasks = $row_tasks.'
		  <tr bgcolor="'.$rowcolor.'" valign="top">
			<td width="14%"><strong>'.JText::_('GURU_DAYS_TASK').' '.($k+1).':</strong></td>
			<td></td>
			<td width="42%"><a href="'.JUri::root().'index.php?option=com_guru&view=guruTasks&task=viewguest&cid='.$task->id.'&pid='.$day->id.'">'.$task->name.'</a></td>
			<td width="15%">'.$task->points.'</td>
			<td width="12%">'.$task->time.'</td>
			<td width="9%">
				<table align="center"><tr><td width="4px" style="background-color:'.$status_color.'">&nbsp;&nbsp;</td></tr></table>
			</td>
		  </tr>';
		  
		$total_points+=$task->points;
		$total_time+=$task->time;
		$k++;
			} // end if
			
		}
	// guest begin
}

$layouttasks = '
<table cellspacing="0" cellpadding="5" border="0" width="100%">
	<tr bgcolor="#cccccc" valign="top">
        <td><strong>'.JText::_('GURU_DAYS_TASK').'</strong></td>
        <td></td>
        <td><strong>'.JText::_('GURU_DAYS_NAME').'</strong></td>
        <td><strong>'.JText::_('GURU_PROGRAM_DETAILS_POINTS').'</strong></td>
        <td><strong>'.JText::_('GURU_PROGRAM_DETAILS_TIME_MINUTES').'</strong></td>
        <td><strong>'.JText::_('GURU_PROGRAM_DETAILS_STATUS').'</strong></td>
</tr>'.
 		$row_tasks
.'<tr bgcolor="#ffffff" valign="top">
       	<td></td>
       	<td></td>
       	<td></td>
        <td>'.$total_points.'</td>
        <td>'.$total_time.'</td>
		<td></td>
	</tr>
</table>';
$daylayout = str_replace ('{tasks}',$layouttasks,$daylayout);


// now parsing the DAY variables - begin
$returned = guruModelguruDays::parse_day_finnish_content($day->id);
$returned = explode('$$$$$', $returned);

$tasks_to_parse = $returned[0];
$time_to_parse = $returned[1];
$points_to_parse = $returned[2];
$day_to_parse = JText::_('GURU_TASKS_DAY').' '.$day->ordering;
$name_to_parse = $my->name;

$daylayout = str_replace('{day}', $day_to_parse, $daylayout);
$daylayout = str_replace('{tasks_list}', $tasks_to_parse, $daylayout);
$daylayout = str_replace('{points}', $points_to_parse, $daylayout);
$daylayout = str_replace('{time}', $time_to_parse, $daylayout);
$daylayout = str_replace('{name}', $name_to_parse, $daylayout);
//$parsed_page_content = str_replace('{last_name}', $name_to_parse, $parsed_page_content);
echo $daylayout;
}
?>