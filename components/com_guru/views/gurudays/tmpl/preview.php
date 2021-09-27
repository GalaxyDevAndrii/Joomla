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
	$data_get = JFactory::getApplication()->input->get->getArray();
	$configs = guruModelguruDays::getConfigs();
	$the_media = guruModelguruDays::getMedia(intval($data_get['cid']));
	$the_media->code = stripslashes($the_media->code);
	//// ------- previewing - begin /////
	
	$no_plugin_for_code = 0;
	$aheight=0; $awidth=0; $vheight=0; $vwidth=0;
	//if($main_media->type=='video')
	if($the_media->type=='video')
		{
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
		}	
$guruHelper = new guruHelper();	
if ($no_plugin_for_code == 0)

$media = $guruHelper->create_media_using_plugin($the_media, $configs, $aheight, $awidth, $vheight, $vwidth);	
else
$media = $the_media->code;
	//// ------- previewing - end /////
?>
<style>
table.adminlist {
background-color:#E7E7E7;
border-spacing:1px;
color:#666666;
width:100%;
font-family:Arial,Helvetica,sans-serif;
font-size:11px;
}
</style>

<?php /*
<div style="float: right;">The preview page!</div>
*/ ?>
<br />
<div>
<div id="editcell">
<table class="adminlist" align="center">
	<tr>
		<td align="center">
			<?php echo $media; ?>
		</td>
	</tr>
</table>
</div>

</div>