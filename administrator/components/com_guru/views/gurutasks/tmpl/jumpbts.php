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

$data_get = JFactory::getApplication()->input->get->getArray();

?>

<?php $current=$this->current;?>
<form id="adminForm" name="adminForm" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruTasks" method="post">

<table>
	<tr>
		<td valign="top"><?php echo JText::_("GURU_JUMP_TEXT"); ?> <span style="color:#FF0000">*</span> :</td>
		<td>
			<input type="text" name="jumptext" id="jumptext" style="width:242px;" value="<?php if(isset($current->text)){echo $current->text;}?>" />			
		</td>
	</tr>
	<tr>
		<td valign="top"><?php echo JText::_("GURU_JUMP_TO"); ?> <span style="color:#FF0000">*</span> :</td>
		<td>
			<div style="border:1px solid silver; padding: 12px;">
				<?php 
					$counter=1;
					$days=$this->days;
					$module_count = 1;
					foreach($days as $element){
						?>
						<img border="0" src="<?php echo JURI::base()."components/com_guru/images/module-tn.gif"; ?>" alt="alt" />
						<?php
						$background = "";						
						if(isset($current) && isset($current->jump_step) && ($current->jump_step == $element->id) && ($current->type_selected == "module")){
							$background=" style='background: #C8BBBE;' ";
							$counter2=$counter;
						}
						 
						echo "<span ".$background." onmouseover='javascript:on_over(\"".$counter."\")' onmouseout='javascript:on_out(\"".$counter."\")' onclick='javascript:on_click(\"".$counter."\", \"".$element->id."\", \"module\")' id=".$counter.">".$element->title."</span><input type='hidden' name='".$counter."F' id='".$counter."F' value='".$element->id."' />";
						$background = "";
						$idTasksForDay = guruAdminModelguruTask::getIDTasksForDay($element->id);
						if(count($idTasksForDay)) {
							echo "<ul style='display:block; margin-left:20px; padding-left:0; margin-bottom:0;margin-top:0;'>";
							$nr=0;
							$counter++;
							
							foreach($idTasksForDay as $element2){
								$nr++;
								if($element2 != 0){
									$aTask = guruAdminModelguruTask::getTask2($element2);
									$layout_nr=guruAdminModelguruTask::select_layout($element2);
									
									?>
									<li style="padding-top: 2px; list-style-type:none;vertical-align:middle;" isLeaf="true" LeafId="<?php echo $aTask->id; ?>">
									<?php
										if(isset($layout_nr)&&($layout_nr<13)&&($layout_nr>0)){
											echo '<img border="0" src="'.JURI::base().'components/com_guru/images/screen-'.$layout_nr.'-tn.gif" alt="layout" />';
										} 
										?>
									
									<?php
									$background = "";
									if(isset($current) && isset($aTask) && isset($current->jump_step)&&($current->jump_step==$aTask->id) && ($current->type_selected != "module")){
										$background=" style='background: #C8BBBE;' ";
										$counter2=$counter;
									}
									if(isset($aTask) && isset($aTask->name) && isset($aTask->id)){
										echo "<span ".$background." onmouseover='javascript:on_over(\"".$counter."\")' onmouseout='javascript:on_out(\"".$counter."\")' onclick='javascript:on_click(\"".$counter."\", \"".$element->id."\")' id=".$counter.">".$aTask->name."</span><input type='hidden' name='".$counter."F' id='".$counter."F' value='".$aTask->id."' />"; 
										$counter++;
									}
									echo "</li>";
								}
							}
							echo "</ul>";
						}
						else{
							echo "<br />";
						}
					}
				?>
			</div>
		</td>
	</tr>
	<tr>
		<td>
		</td>
		<td>
			<input type="button" class="btn" onclick="javascript:submitbutton()" value="<?php echo JText::_("GURU_SAVE"); ?>" size="16" style="padding:2px 22px;" />
		</td>
	</tr>
</table>

<input type="hidden" name="selected_id" id="selected_id" value="<?php if(isset($counter2)){echo $counter2;}?>" />
<input type="hidden" name="jump_mod_id" id="jump_mod_id" value="<?php echo @$current->module_id; ?>" />
<input type="hidden" name="selstep" id="selstep" value="0" />
<input type="hidden" name="option" value="com_guru" />
<input type="hidden" name="controller" value="guruTasks" />	
<input type="hidden" name="task" id="task" value="jumpbts_save" />
<input type="hidden" name="type_selected" id="type_selected" value="" />
<input type="hidden" name="editid" id="editid" value="<?php if(isset($current->id)){echo $current->id;} else {echo '0';} ?>" />
</form>

<script type="text/javascript">
	function on_click(id, mod_id, module_type){
		if(module_type == "module"){
			document.getElementById("type_selected").value = "module";
		}
		else{
			document.getElementById("type_selected").value = "";
		}
		if(document.getElementById("selected_id").value!=0){
			document.getElementById(document.getElementById("selected_id").value).style.background="#FFFFFF";
		}
		//console.log(jQuery("#jump_mod_id"));
		document.getElementById("jump_mod_id").value = mod_id;
		document.getElementById(id).style.background="#C8BBBE";
		document.getElementById("selected_id").value = id;		
	}
	function on_over(id){
		document.getElementById(id).style.background="#C8BBBE";
	}
	function on_out(id){
		if(document.getElementById("selected_id").value!=id){
			document.getElementById(id).style.background="#FFFFFF";
		}
	}
	
	function submitbutton(){
		if(document.getElementById("jumptext").value==0){
			alert("<?php echo JText::_('GURU_WRITE_JUMP_TEXT');?>"); 
			return false;
		}
		else if(document.getElementById("selected_id").value==0){
			alert("<?php echo JText::_('GURU_SEL_STP');?>"); 
			return false;
		} 
		else {
			var sel=document.getElementById("selected_id").value;
			document.getElementById("selstep").value=document.getElementById(sel+"F").value+"|"+<?php echo intval($data_get['button']);?>;
			document.adminForm.submit();
		}
	}
	
</script>