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
	$n = count ($this->programs);
	$programs = $this->programs;
?>

<form action="<?php echo JUri::root() ?>index.php" name="adminForm" method="post">
<div id="editcell">
<table class="adminlist">
<thead>

	<tr>
		<th width="5">
			<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
		</th>
	    <th width="20">
			<?php echo JText::_('GURU_ID');?>
		</th>
		<th>
			<?php echo JText::_('GURU_PROGRAM');?>
		</th>
		<th> <?php echo JText::_('GURU_TREEDAYS');?>
		</th>
		<th>
			<?php echo JText::_('GURU_POINTS');?>
		</th>
		<th>
			<?php echo JText::_('GURU_PRICE');?>
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

<?php 

	for ($i = 0; $i < $n; $i++):
	$program = $this->programs[$i];
	$id = $program->id;
	$checked = JHTML::_('grid.id', $i, $id);
	$link = JRoute::_("index.php?option=com_guru&view=guruPrograms&task=edit&cid[]=".$id);
	$published = JHTML::_('grid.published', $program, $i );
	
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
				<a href="#>">0</a>
		</td>
	     	<td>
	     	    	<a href="#>">0</a>
		</td>		
		<td>
	     	    <?php echo $program->price;?>
		</td>		
		<td>
	     	    	-
		</td>		
		<td>
			<?php echo $published;?>
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
<input type="hidden" name="controller" value="guruPrograms" />
</form>