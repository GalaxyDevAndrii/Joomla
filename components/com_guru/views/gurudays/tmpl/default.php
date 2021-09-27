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
	$n = count ($this->ads);

	if ($n < 1):
?>

<table class="adminlist">
<thead>

	<tr>
		<th width="5">
			<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
		</th>
	        <th width="20">
			<?php echo JText::_('ID');?>
		</th>
		<th>
			<?php echo JText::_('Day');?>
		</th>

		<th>
			<?php echo JText::_('GURU_TITLE');?>
		</th>
		<th><?php echo JText::_("GURU_TASKS");?>	
		</th>
		<th>
			<?php echo JText::_('GURU_REORDER');?>
		</th>

		<th>
			<?php echo JText::_('GURU_PUBLISHED');?>
		</th>
		
	</tr>
</thead>

<tbody>
	<tr>
<td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>

</td>
</tr>
</tbody>
</table>


	<form action="<?php echo JUri::root() ?>index.php" name="adminForm" method="post">	
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="guruDays" />
	</form>

<?php

	else:

?>
<form action="<?php echo JUri::root() ?>index.php" name="adminForm" method="post">
<div id="editcell" >
<table class="adminlist">
<thead>

	<tr>
		<th width="5%"><input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" /></th>
	    <th width="5%"><?php echo JText::_('ID');?></th>
		<th width="30%"><?php echo JText::_('Day');?></th>
		<th width="30%"><?php echo JText::_('GURU_TITLE');?></th>
		<th width="15%"><?php echo JText::_("Tasks");?></th>
		<th width="10%"><?php echo JText::_('GURU_REORDER');?></th>
		<th width="5%"><?php echo JText::_('GURU_PUBLISHED');?></th>		
	</tr>
</thead>

<tbody>

<?php 
	for ($i = 0; $i < $n; $i++):
		$ad = $this->ads[$i];
		$id = $ad->id;
		$checked = JHTML::_('grid.id', $i, $id);
		$published = JHTML::_('grid.published', $ad, $i );
		$link = JUri::root() . "index.php?option=com_guru&view=guruDays&task=edit&cid[]=".$id;
?>
	<tr class="row<?php echo $k;?>"> 
	 	<td align="center"><?php echo $checked;?></td>		
	    <td><?php echo $ad->id;?></td>		
	    <td nowrap><a href="<?php echo $link;?>" ><?php echo 'Day&nbsp;'.$ad->id;?></a></td>		
		<td nowrap><a href="<?php echo $link;?>" ><?php echo $ad->title;?></a></td>
	    <td>3 Tasks</td>		
	    <td>-</td>		
		<td><?php echo $published;?></td>
	</tr>
<?php 
		$k = 1 - $k;
	endfor;
?>
	<tr>
<td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>

</td>
</tr>

</tbody>


</table>

</div>

<input type="hidden" name="option" value="com_guru" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="guruDays" />
</form>

<?php
	endif;

?>