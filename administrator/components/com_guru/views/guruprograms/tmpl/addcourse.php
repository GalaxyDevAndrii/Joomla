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

$data_post = JFactory::getApplication()->input->post->getArray();
$data_get = JFactory::getApplication()->input->get->getArray();

$medias = $this->programs;
$db = JFactory::getDBO();
if(empty($medias)) {
	echo "<strong>".JText::_("GURU_MEDIA_NOMED")."</strong>";
	$app->close();
}
$n = count($medias);

$sql = "select count(*) from #__guru_media";
$db->setQuery($sql);
$db->execute();
$count = $db->loadresult();

if(!isset($count) || $count == 0){
	echo "<b>".JText::_("GURU_NO_MEDIA")."</b>";
	return;
}
$doc =JFactory::getDocument();

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
function addcourse (idu, nume, publicat, idtask) {	
	var myrow = window.parent.document.createElement('TR');
	myrow.id = 'tr_'+idu;
	
	window.parent.document.getElementById('preqfiles').value = window.parent.document.getElementById('preqfiles').value+idu+',';
	
	window.parent.document.getElementById('rowspreq').appendChild(myrow);
	var mycell = window.parent.document.createElement('TD');
	myrow.appendChild(mycell);
	var mycelltwo = window.parent.document.createElement('TD');
	myrow.appendChild(mycelltwo);
	var mycellfive = window.parent.document.createElement('TD');
	myrow.appendChild(mycellfive);
	mycell.innerHTML=idu;
	mycelltwo.innerHTML='<a class="a_guru" href="index.php?option=com_guru&controller=guruTasks&task=edit&cid[]='+idu+'" target="_blank">'+nume+'</a>';
	mycellfive.innerHTML='<img onclick="deleteCourse(\''+idu+'\');" alt="delete" src="<?php echo JURI::root()."administrator/components/com_guru/images/delete2.gif"; ?>">';
	window.parent.document.getElementById("table_courses_id").style.display = "table";
	//if joomla <= 3.8 means that it will include modal.js script witch generate sbox-window with modal
    if(document.getElementById('sbox-window')){
        window.parent.SqueezeBox.close();
    }
    //if joomla > 3.8 means that it will not include modal.js anymore and will use boostrap modal
    else{
        window.parent.jQuery('#GuruModal').modal('toggle');
    }
	return true;
}
function publishUn1(i){
	jQuery.ajax({
    url: 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&id='+i+'&task=addcourse_ajax',
    type: 'GET',
    success: function(data) {
       if(data == 'publish'){
			element_id = "g_publish"+i;
			document.getElementById(element_id).className = "icon-ok";
		}
		else if(data == 'unpublish'){
			element_id = "g_publish"+i;
			document.getElementById(element_id).className = "icon-remove";
		}
    }
	});
	return true;	
}
</script>
<div style="float: left;"><b><?php echo JText::_("GURU_ADD_PREREQUISITE"); ?></b></div>
<br /><br />
<div>
<form id="adminForm" name="form1" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruPrograms&task=addcourse&tmpl=component&cid[]=<?php echo $data_get['cid'][0];?>" method="post">
<table class="table">
	<tr>
		<td>
			<input type="text" name="search_text" value="<?php if(isset($data_post['search_text'])) echo $data_post['search_text'];?>" />
			<input class="btn" type="submit" name="submit_search" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" />
		</td>
	</tr>
</table>		
</form>
</div>
<br />

<div>
<div id="editcell" style="width:95%;">
<table class="table table-striped">
<thead>
	<tr>
		<th align="left" width="40"><?php echo JText::_("GURU_ID"); ?></th>
		<th align="left"><?php echo JText::_("GURU_NAME"); ?></th>
		<th align="left"><?php echo JText::_("GURU_PUBLISHED"); ?></th>
	</tr>
</thead>
<?php 
	$cmp = intval($data_get['cid'][0]);
 if ($n>0) { 
	for ($i = 0; $i < $n; $i++):
	$file =$medias[$i];
	
	$id = $file->id;
	if($id==$cmp) {continue;}
			$checked = JHTML::_('grid.id', $i, $id);
			$link = "addcourse('".$id."', '".addslashes($file->name)."', '".addslashes($file->published)."', '".$cmp."')";
			?>
		<tbody>
			<tr class="camp0"> 
				<td><?php echo $file->id;?></td>		
				<td><a class="a_guru" onclick="<?php echo $link;?>" href="#"><?php echo $file->name;?></a></td>		
				<td>
                	<?php
						if($file->published ==1){
						?>
							 <a id="g_publish<?php echo $file->id;?>" style="cursor: pointer;" class="icon-ok" title="Publish Item" onclick="javascript:publishUn1(<?php echo $file->id;?>)"></a>
					<?php
						}
						else{?>
							 <a id="g_publish<?php echo $file->id;?>" style="cursor: pointer;" class="icon-remove" title="Unpublish Item" onclick="javascript:publishUn1(<?php echo $file->id;?>)"></a>
						 <?php
						}
					  ?>    
                
                </td>		
			</tr>		
		</tbody>
<?php endfor;
 } ?>

</table></div>
</div>