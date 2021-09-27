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
	$n = count ($this->plugins);


	if ($n < 1): 
//		echo JText::_('VIEWPLUGNOPLUG');
?>
<table class="table table-striped adminlist">
<thead>

	<tr>
		<th width="5">
			<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
		</th>
	        <th width="20">
			<?php echo JText::_('VIEWPLUGID');?>
		</th>
		<th>
			<?php echo JText::_('VIEWPLUGTITLE');?>
		</th>

		<th><?php echo JText::_("VIEWPLUGPUBLISH");?>	
		</th>

	</tr>
</thead>

<tbody>
</tbody>
</table>
	<form action="<?php echo JUri::root() ?>administrator/index.php" name="adminForm" method="post">
	  	<input type="hidden" name="option" value="com_guru" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="guruPlugins" />
	</form>

<?php

	else:

?>
<script language="javascript" type="text/javascript" >
<!--
	function checkPluginFile () {
		var file = document.getElementById("pluginfile");
		if (file.value.length < 1) {
			alert ('<?php echo JText::_("VIEWPLUGNOPLUGFORUPL");?>');
			return false;
		}

	}

-->
</script>

<form action="<?php echo JUri::root() ?>administrator/index.php" name="adminForm" method="post">
<div id="editcell" >
<table class="table table-striped adminlist">
<thead>

	<tr>
		<th width="5">
			<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
		</th>
	        <th width="20">
			<?php echo JText::_('VIEWPLUGID');?>
		</th>
		<th>
			<?php echo JText::_('VIEWPLUGTITLE');?>
		</th>
		<th><?php echo JText::_("Plugin Type");?>	
		</th>
		<th><?php echo JText::_("VIEWPLUGPUBLISH");?>	
		</th>

	</tr>
</thead>

<tbody>

<?php 
	for ($i = 0; $i < $n; $i++):
		$plugin = $this->plugins[$i];
		if (empty($plugin)) continue;
		$id = $plugin->id;
		$checked = JHTML::_('grid.id', $i, $id);
		$link = JRoute::_("index.php?option=com_guru&controller=guruPlugins&task=edit&cid[]=".$id);
		$published = JHTML::_('grid.published', $plugin, $i );
?>
	<tr class="row<?php echo $k;?>"> 
	     	<td>
	     	    	<?php echo $checked;?>
		</td>		

	     	<td>
	     	    	<?php echo $i+1;?>
		</td>		

	     	<td>
	     	    	<?php //echo $plugin->name.' ( '.$plugin->filename.' ) ';?>
					<?php echo '<a href="'.$link.'">'.$plugin->name.'</a>'; ?>
		</td>		
	     	<td>
	     	    	<?php echo 'payment';?>
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
<input type="hidden" name="controller" value="guruPlugins" />
</form>
<br />




<?php
	endif;

?>
<form id="adminForm" action="<?php echo JUri::root() ?>administrator/index.php" name="pluginFileForm" method="post" enctype="multipart/form-data" onsubmit="return checkPluginFile();">
<div id="editcell" >
<table class="table table-striped adminlist">
<tr>
<td nowrap>
<input type="file" name="pluginfile" id="pluginfile" /> <input type="submit" name="submit" value="<?php echo JText::_("GURU_UPLOAD_PLUGIN"); ?>" />
</td>

</tr>
</table>

</div>

<input type="hidden" name="option" value="com_guru" />
<input type="hidden" name="task" value="upload" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="guruPlugins" />
</form>
