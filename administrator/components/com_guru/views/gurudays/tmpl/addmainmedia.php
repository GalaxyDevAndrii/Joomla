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

$medias 	= $this->medias;
$filter	= $this->filters;
$n = count($medias);
$data_get = JFactory::getApplication()->input->get->getArray();
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
<script type="text/javascript" language="javascript">
function addmedia (idu, nume, tip, publicat, idtask) {
	//window.parent.setTimeout('window.parent.document.getElementById("sbox-window").close()', 200);
	window.parent.document.getElementById("sbox-window").close();
	var myrow = window.parent.document.createElement('TR');
	myrow.id = 'trm'+idu;
	window.parent.document.getElementById('mediafilesday').value = window.parent.document.getElementById('mediafilesday').value+','+idu+',';
	window.parent.document.getElementById('rowsmedia').appendChild(myrow);
	var mycell = window.parent.document.createElement('TD');
	myrow.appendChild(mycell);
	var mycelltwo = window.parent.document.createElement('TD');
	myrow.appendChild(mycelltwo);
	var mycellthree = window.parent.document.createElement('TD');
	myrow.appendChild(mycellthree);
	var mycellfour = window.parent.document.createElement('TD');
	myrow.appendChild(mycellfour);
	var mycellfive = window.parent.document.createElement('TD');
	myrow.appendChild(mycellfive);
	var mycellsix = window.parent.document.createElement('TD');
	myrow.appendChild(mycellsix);
	mycell.innerHTML=idu;
	mycelltwo.innerHTML='<a href="index.php?option=com_guru&controller=guruMedia&task=edit&cid[]='+idu+'" target="_blank">'+nume+'</a>';
	mycellthree.innerHTML=tip;
	mycellfour.innerHTML='-----';
	mycellfive.innerHTML='<font color="#FF0000"><span onClick="delete_temp_m('+idu+')">Remove</span></font>';
	mycellsix.innerHTML=publicat;
	//top.document.getElementById('paul').innerHTML='ajkskdjsdjlk';
	return true;
}
</script>

<div style="float: right;"><?php echo JText::_("GURU_CLICK_TO_ADD_MEDIA"); ?></div>
<br /><br />
<div>
<form id="adminForm" name="form1" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruDays&task=addmainmedia&no_html=1&cid[]=<?php echo $data_get['cid'][0];?>" method="post">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>
				<input type="text" name="search_text" value="<?php echo $filter->search_text; ?>" />
				<input type="submit" name="submit_search" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" />
			</td>
			<td>
				<?php 
					echo JText::_("GURU_DAY_MEDIATYPE"); 
					echo $filter->media_select;
				?>
			</td>
		</tr>
	</table>		
</form>
</div>
<br />

<div>
<div id="editcell">
<table class="adminlist" width="60%">
	<thead>
		<tr>
			<th width="20"><?php echo JText::_("GURU_ID"); ?></th>
			<th><?php echo JText::_("GURU_NAME"); ?></th>
			<th><?php echo JText::_("GURU_TYPE"); ?></th>
			<th><?php echo JText::_("GURU_TYPE"); ?></th>
		</tr>
	</thead>
	<?php 
		$cmp = intval($data_get['cid']);
	 	if ($n>0) { 
			for ($i = 0; $i < $n; $i++){
				$file = $this->medias[$i];
				$checked = JHTML::_('grid.id', $i, $file->id);
				$link = "addmedia('".$file->id."', '".addslashes($file->name)."', '".addslashes($file->type)."', '".addslashes($file->published)."', '".$cmp."')";
				$published = JHTML::_('grid.published', $file, $i ); 
	?>
	<tbody>
		<tr class="camp0"> 
	   		<td><?php echo $file->id;?></td>		
	   		<td><a onclick="<?php echo $link;?>" href="#"><?php echo $file->name;?></a></td>		
			<td align="center">
	     	  <?php echo $file->type;?>
			</td>		
			<td align="center"><?php echo $published;?></td>		
		</tr>
	</tbody>
<?php 
		}
 } 
 ?>

</table>

</div>

</div>