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
$medias = $this->medias;
$n = count($medias);
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
	window.top.setTimeout('window.parent.document.getElementById("sbox-window").close()', 200);
	var myrow = top.document.createElement('TR');
	myrow.id = 'trm'+idu;
	top.document.getElementById('mediafiles').value = top.document.getElementById('mediafiles').value+','+idu+',';
	//top.document.getElementById('mediafiletodel').value = top.document.getElementById('mediafiletodel').value;
	top.document.getElementById('rowsmedia').appendChild(myrow);
	var mycell = top.document.createElement('TD');
	myrow.appendChild(mycell);
	var mycelltwo = top.document.createElement('TD');
	myrow.appendChild(mycelltwo);
	var mycellthree = top.document.createElement('TD');
	myrow.appendChild(mycellthree);
	var mycellfour = top.document.createElement('TD');
	myrow.appendChild(mycellfour);
	var mycellfive = top.document.createElement('TD');
	myrow.appendChild(mycellfive);
	var mycellsix = top.document.createElement('TD');
	myrow.appendChild(mycellsix);
	mycell.innerHTML=idu;
	mycelltwo.innerHTML='<a class="a_guru" href="index.php?option=com_guru&controller=guruTasks&task=edit&cid[]='+idu+'" target="_blank">'+nume+'</a>';
	mycellthree.innerHTML=tip;
	mycellfour.innerHTML='-----';
	mycellfive.innerHTML='<font color="#FF0000"><span onClick="delete_temp_m('+idu+')">Remove</span></font>';
	mycellsix.innerHTML=publicat;
	return true;
}
</script>
<div style="float: right;"><?php echo JText::_("GURU_CLICK_TO_ADD_MEDIA"); ?></div>
<br><br><br>
<div style="width:650px;border:1px solid red;">
<div id="editcell">
<table class="adminlist" width="60%">
<thead>
	<tr>
		<th width="20"><?php echo JText::_("GURU_ID"); ?></th>
		<th><?php echo JText::_("GURU_NAME"); ?></th>
		<th><?php echo JText::_("GURU_TYPE"); ?></th>
		<th><?php echo JText::_("GURU_PUBLISHED"); ?></th>
	</tr>
</thead>
<?php 
	$data_get = JFactory::getApplication()->input->get->getArray();
	
	$cmp = intval($data_get['cid']);
 if ($n>0) { 
	for ($i = 0; $i < $n; $i++):
	$file = $this->medias[$i];
	$type = "";
	   			switch ($file->type) {
		    		case "video": $type = "Video";	    						
		    			break;
		    		case "audio": $type = "Audio";	    						
		    			break;
		    		case "docs": $type = "Document";	    						
		    			break;
		    		case "url": $type = "URL";	    						
		    			break;
	    		}
	$id = $file->id;
	$checked = JHTML::_('grid.id', $i, $id);
	$link = "addmedia('".$id."', '".addslashes($file->name)."', '".addslashes($type)."', '".addslashes($file->published)."', '".$cmp."')";
	$published = JHTML::_('grid.published', $file, $i ); ?>
<tbody>
	<tr class="camp0"> 
	   <td><?php echo $file->id;?></td>		
	    <td><a class="a_guru" onclick="<?php echo $link;?>" href="#"><?php echo $file->name;?></a></td>		
		<td>
	     	  <?php echo $type;?>
		</td>		
		<td><?php echo $published;?></td>		
	</tr>



</tbody>
<?php endfor;
 } ?>

</table></div>
</div>