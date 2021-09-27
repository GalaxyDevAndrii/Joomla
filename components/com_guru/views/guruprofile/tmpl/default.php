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
	$n = count ($this->custs);


	if ($n < 1): 
		echo JText::_('DSNOCUSTOMER');
?>

	<form action="<?php echo JUri::root() ?>index.php" name="adminForm" method="post">
	  	<input type="hidden" name="option" value="com_guru" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="guruCustomers" />
	</form>

<?php

	else:

?>
<form action="<?php echo JUri::root() ?>index.php" name="adminForm" method="post">
<div id="editcell" >
<table class="adminlist">
<thead>

	<tr>
		<th width="5">
			<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
		</th>
	        <th width="20">
			<?php echo JText::_('DSID');?>
		</th>
		<th>
			<?php echo JText::_('DSFULLNAME');?>
		</th>
		<th>
			<?php echo JText::_('DSUSERNAME');?>
		</th>


	</tr>
</thead>

<tbody>

<?php 
	for ($i = 0; $i < $n; $i++):
	$cust = $this->custs[$i];
	$id = $cust->id;
	$checked = JHTML::_('grid.id', $i, $id);
	$link = JRoute::_("index.php?option=com_guru&view=guruCustomers&task=edit&cid[]=".$id);
//	$published = JHTML::_('grid.published', $cat, $i );
?>
	<tr class="row<?php echo $k;?>"> 
	     	<td>
	     	    	<?php echo $checked;?>
		</td>		

	     	<td>
	     	    	<?php echo $id;?>
		</td>		
	     	<td>
	     	    	<a href="<?php echo $link;?>" ><?php echo $cust->firstname." ".$cust->lastname;?></a>
		</td>		
	     	<td>
	     	    	<?php echo $cust->username;?>
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
<input type="hidden" name="controller" value="guruCustomers" />
</form>

<?php
	endif;

?>