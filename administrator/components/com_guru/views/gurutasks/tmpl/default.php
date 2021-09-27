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
	$k = 0;
	$n = count ($this->programs);
	$programs = $this->programs;
?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton=='duplicate') 
			{
				if (form['boxchecked'].value == 0) {
						alert( "<?php echo JText::_("GURU_TASK_MAKESEL_JAVAMSG");?>" );
				} else 	{
							submitform( pressbutton );
						}
			}
			else 
				submitform( pressbutton );
		}
		-->
</script>
<form name="topform1" id="topform1" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruTasks" method="post">
<table width="100%" cellpadding="5" cellspacing="5" bgcolor="#FFFFFF">
	<tr>
		<td>
        	<?php
            	$session = JFactory::getSession();
				$registry = $session->get('registry');
				$search_text = $registry->get('search_text', "");
			?>
        
			<input type="text" name="search_text" value="<?php if(isset($search_text) && trim($search_text) != "") { echo $search_text; };?>" />
			<input type="submit" name="submit_search" value="<?php echo JText::_('GURU_SEARCHTXT');?>" />
		</td>
		<td>
			<?php echo JText::_('GURU_CATEGORY_SEARCH');?>
			<?php
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$category = $registry->get('category', "");
			
			if(isset($category) && trim($category) != ""){
				$categ_set = intval($category);
			}
			else{
				$categ_set = 0;
			}
			
			$lists['treecateg']=$this->list_all(1, "category", 0, $categ_set); ?>
		</td>
		<td>
			<?php echo JText::_('GURU_MEDIA_SEARCH');?>
            
            <?php
            	$session = JFactory::getSession();
				$registry = $session->get('registry');
				$media_select = $registry->get('media_select', "");
			?>
            
			<select name="media_select" onChange="document.topform1.submit()">
				<option value="all" <?php if ( (!isset($media_select)) || (isset($media_select) && $media_select == 'all') ) echo ' selected="selected" ';?>><?php echo JText::_("GURU_ALL"); ?></option>
				<option value="audio" <?php if (isset($media_select) && $media_select == 'audio') echo ' selected="selected" ';?>><?php echo JText::_("GURU_AUDIO"); ?></option>
				<option value="video"  <?php if (isset($media_select) && $media_select == 'video') echo ' selected="selected" ';?>><?php echo JText::_("GURU_VIDEO"); ?></option>
				<option value="docs" <?php if (isset($media_select) && $media_select == 'docs') echo ' selected="selected" ';?>><?php echo JText::_("GURU_DOCS"); ?></option>
				<option value="url" <?php if (isset($media_select) && $media_select == 'url') echo ' selected="selected" ';?>><?php echo JText::_("GURU_URL"); ?></option>
			</select>
		</td>
		<td>
			<?php echo JText::_('GURU_STATUS_SEARCH');?>
            
            <?php
            	$session = JFactory::getSession();
				$registry = $session->get('registry');
				$status_select = $registry->get('status_select', "");
			?>
            
			<select name="status_select" onChange="document.topform1.submit()">
				<option value="-1" <?php if ( (!isset($status_select)) || (isset($status_select) && $status_select == '-1') ) echo ' selected="selected" ';?>><?php echo JText::_('GURU_STATUS_SEARCH_ALLPUUNP');?></option>
				<option value="1" <?php if (isset($status_select) && $status_select == '1') echo ' selected="selected" ';?>><?php echo JText::_('GURU_STATUS_SEARCH_PUBL');?></option>
				<option value="0" <?php if (isset($status_select) && $status_select == '0') echo ' selected="selected" ';?>><?php echo JText::_('GURU_STATUS_SEARCH_UNPUBL');?></option>
			</select>
		</td>						
	</tr>
</table>		
</form>

<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post">
<div id="editcell" >
<table class="table table-striped adminlist">
<thead>
	<tr>
		<th width="5">
			<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
		</th>
	    <th width="20">
			<?php echo JText::_('GURU_ID');?>
		</th>
		<th>
			<?php echo JText::_('GURU_TASK_TASK');?>
		</th>
		<th> <?php echo JText::_('GURU_TASK_CATEGORY');?>
		</th>
		<th>
			<?php echo JText::_('GURU_TASK_MEDIA');?>
		</th>
		<th>
			<?php echo JText::_('GURU_REORDER');?>
		</th>
		<th>
			<?php echo JText::_('GURU_PUBLISHED');?>
		</th>
		<th>
			<?php echo JText::_('GURU_TASK_LOCKED');?>
		</th>		
	</tr>
</thead>

<tbody>

<?php 

	for ($i = 0; $i < $n; $i++):
	$program = $this->programs[$i];
	$id = $program->id;
	//$checked = JHTML::_('grid.id', $i, $id);
	$checked_array = guruAdminModelguruTask::checkbox_construct( $i, $id, $name='cid' );
	$checked_array_expld = explode('$$$$$', $checked_array);
	$checked = $checked_array_expld[0];
	$locked = $checked_array_expld[1];
	if($locked == 'disabled="disabled"')
		$locked = '<font color="#FF0000">'.JText::_('GURU_TASK_LOCKED_Y').'</font>';		
	else
		$locked = '<font color="#00FF00">'.JText::_('GURU_TASK_LOCKED_N').'</font>';
	$link = JRoute::_("index.php?option=com_guru&controller=guruTasks&task=edit&cid[]=".$id);
	$link2 = JRoute::_("index.php?option=com_guru&controller=guruTCategs&task=edit&cid[]=".$id);
	$published = JHTML::_('grid.published', $program, $i );
	$mediatype = 'image';
?>
	<tr class="camp<?php echo $k;?>"> 
	    <td>
	     	    	<?php echo $checked;?>
		</td>		

	     	<td>
	     	    	<?php echo $id;?>
		</td>		
	     	<td>
	     	    	<a href="<?php echo $link;?>" ><?php echo $program->name;?></a>
		</td>		

		<td>
				<a href="<?php echo $link2;?>" ><?php echo $program->catname;?></a>
		</td>
	     	<td align="center">
	     	    <?php $srcimg = JURI::base()."/components/com_guru/images/";
				$image = "doc.gif";
				switch ($mediatype) {
						case "video": $image = "video.gif";	    						
							break;
						case "audio": $image = "audio.gif";	    						
							break;
						case "docs": $image = "doc.gif";	    						
							break;
						case "quiz": $image = "quiz.gif";	    						
							break;
						case "url": $image = "url.gif";	    						
							break;										
				}?>
				<?php if (isset($mediatype)) { ?>
				<img src="<?php echo $srcimg.$image;?>" alt="" />
				<?php } ?>
		</td>		
		<td>
	     	   
		</td>		
		<td>
			<?php echo $published;?>
		</td>
		<td>
			<?php echo $locked;?>
		</td>
	</tr>
<?php 
		$k = 1 - $k;
	endfor;
?>
</tbody>
</table>
</div>
<input type="hidden" name="option" value="com_guru" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="guruTasks" />
</form>