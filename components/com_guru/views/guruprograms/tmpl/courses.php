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
jimport ('joomla.application.component.controller');
JHTML::_('behavior.modal', 'a.modal');

$program		= $this->program;
$programContent	= $this->programContent;
$user = JFactory::getUser();
echo "<h1 class='guru_course_name'>".$program->name."</h1>";

foreach($programContent as $key=>$array){
	$subcat = guruModelguruProgram::getSubCategory($array['id']);	
	?>
	<tr style="">
		<td colspan='2'>
			<table style='width:100%;'>
				<tr>
					<td style="border-bottom: 2px solid #F7F7F7;">
						<div class="day" onClick="javascript:show_hidde('<?php echo $array['id'];?>','<?php echo JUri::root()."components/com_guru/images/";?>')">
							<?php
								if(count($subcat)>0){ ?>
									<img id='img_<?php echo $array['id']; ?>' src='<?php echo JUri::root()."components/com_guru/images/arrow-right.gif";?>' />
							<?php } 
								echo $array['title'];
							?>
						</div>
					</td>
				</tr>
			<?php
				if(count($subcat)>0){
			?>
						<tr>
							<td id='td_"<?php echo $array['id']; ?>"' style="border-bottom: 2px solid #F7F7F7;">
								<div id='table_<?php echo $array['id'];?>' style="padding-left:40px; display:block;" class="subcat">
				<?php } 
	
				foreach($subcat as $poz=>$sub_cat){		
					if(($user->id>0 && $sub_cat['step_access']!=2) || $sub_cat['step_access']==2){
						$style=" class='s_underline' ";
						$gray_style=" class=\'s_underline\' ";
					}
					else{
						 $style=" class='s_no_underline'";
						 $gray_style=" class=\'s_no_underline\' ";
					}
		
					if($user->id<=0 && $sub_cat['step_access']!=2){
					?>
						<a rel="{handler: 'iframe', size: {x: 600, y: 400},iframeOptions: {id: 'lesson_task'}}" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&task=view&module=".$array['id']."&cid=".$sub_cat['id']."&tmpl=component"); ?>" class="modal">
							<span <?php echo $gray_style;?> >
								<?php echo $sub_cat['name']; ?>
							</span>
						</a>
					<?php
					}
					else if($config->open_target==0){?>
						<a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&task=view&module=".$array['id']."&cid=".$sub_cat['id']); ?>"><span <?php echo $style;?> ><?php echo $sub_cat['name']; ?></span></a>	
				<?php
					}elseif($config->open_target==1){
				?>
					<a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&task=view&module=".$array['id']."&cid=".$sub_cat['id']."&tmpl=component"); ?>" target="_blank"><span <?php echo $style;?> ><?php echo $sub_cat['name']; ?></span></a>	
				<?php 
					}
				?>
					<div style='margin-top:-19px;padding-bottom:5px;text-align:right;'>
						<?php 
							switch($sub_cat['difficultylevel']){
								case "easy":
									$imgLevel="beginner_level.png";
									break;
								case "medium":
									$imgLevel="intermediate_level.png";
									break;
								case "hard":
									$imgLevel="advanced_level.png";
									break;
							}
						?>
						<img src="<?php echo JURI::root()."components/com_guru/images/".$imgLevel; ?>" />
					</div>
					<hr style="color:#F7F7F7; background-color:#F7F7F7; height:2px; border: none;" />
				<?php
				}
				if(count($subcat)>0){	
				?>		
								</div>
							</td>
						</tr>
					<?php
					}
					?>
				</table>
			</td>
		</tr>	
	<?php
	}
	?>									
	</table>

