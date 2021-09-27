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
$node = JFactory::getApplication()->input->get("node","0");
$document = JFactory::getDocument();
$document->addScript(JURI::base()."/components/com_guru/views/gurudays/tmpl/js/drag-drop-folder-tree.js");
$data_post = JFactory::getApplication()->input->post->getArray();
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

function addtask (idu, nume, tip, publicat, idtask, image, node) {
	//top.document.getElementById('taskitems').value = top.document.getElementById('taskitems').value+','+idu+',';
	//window.top.setTimeout('window.parent.document.getElementById("sbox-window").close()', 200);
	window.parent.document.getElementById("sbox-window").close();

	var the_node = 'tree_ul_'+node;
	var container = top.document.getElementById(the_node);

	// Create a new <li> element for to insert inside <ul id="myList">
	var new_element = top.document.createElement('li');

	if(image=='')
		new_element.innerHTML = '<img border="0" onClick="javascript:deleteScreen('+idtask+','+ idu +','+ node +')" src="http://86.121.190.64/ijoomlatrainer/administrator/components/com_guru/images/delete.gif"/><a id="nodeATag'+node+node+node+node+node+idtask+'" href="#">'+nume+'</a>';
	else
		new_element.innerHTML = '<img border="0" onClick="javascript:deleteScreen('+idtask+','+ idu +','+ node +')" src="http://86.121.190.64/ijoomlatrainer/administrator/components/com_guru/images/delete.gif"/><img border="0" src="http://86.121.190.64/ijoomlatrainer/administrator/components/com_guru/images/'+ image +'" /><a id="nodeATag'+node+node+node+node+node+idtask+'" href="#">'+nume+'</a>';

		new_element.id='node'+node+node+node+node+node+idtask;
		new_element.setAttribute('isLeaf', 'true');
		new_element.setAttribute('LeafId', idu);
		container.appendChild(new_element);

		//window.parent.saveMyTree();
		window.parent.addNewScreen(idtask,idu);
		window.parent.refreshTree();
		window.parent.dom_refresh();
	return true;
}

function addtask1 (idu, nume, image, node) {
	var the_node = 'tree_ul_'+node;
	var container = top.document.getElementById(the_node);
	var groupid = document.getElementById('group_id').value;
	// Create a new <li> element for to insert inside <ul id="myList">
	var new_element = top.document.createElement('li');
	if(image=='')
		new_element.innerHTML = '<img border="0"  onClick="javascript:deleteScreen('+groupid+','+ idu +','+node+node+node+node+node+idu+')" src="http://86.121.190.64/ijoomlatrainer/administrator/components/com_guru/images/delete.gif"/><a id="nodeATag'+node+node+node+node+node+idu+'" href="#">'+nume+'</a>';
	else
		new_element.innerHTML = '<img border="0"  onClick="javascript:deleteScreen('+groupid+','+ idu +','+node+node+node+node+node+idu+')" src="http://86.121.190.64/ijoomlatrainer/administrator/components/com_guru/images/delete.gif"/><img border="0" src="http://86.121.190.64/ijoomlatrainer/administrator/components/com_guru/images/'+ image +'" /><a id="nodeATag'+node+node+node+node+node+idu+'" href="#">'+nume+'</a>';
	new_element.id='node'+node+node+node+node+node+idu;
	new_element.setAttribute('isLeaf', 'true');
	new_element.setAttribute('LeafId', idu);
	container.appendChild(new_element);

	window.parent.refreshTree();
	//window.parent.saveTheNewTree();
	window.parent.addNewScreen(groupid,idu);

	return true;
}

function str_replace(haystack, needle, replacement) {
	var temp = haystack.split(needle);
	return temp.join(replacement);
}

function checkAll( n, fldName ) {
	if (!fldName) {
    	fldName = 'cb';
  	}
	var f = document.adminForm;
	var c = f.toggle.checked;
	var n2 = 0;
	document.adminForm.boxchecked_id.value = '';
	document.adminForm.boxchecked_name.value = '';
	document.adminForm.boxchecked_image.value = '';
	for (i=0; i < n; i++) {
		task_id = 'cb'+i;	
		cb = eval( 'f.' + fldName + '' + i );
		if (cb) {
			cb.checked = c;
			n2++;
		}
		document.adminForm.boxchecked_id.value = document.adminForm.boxchecked_id.value + document.getElementById(task_id).value + ',';
		task_name = 'file_name'+document.getElementById(task_id).value;
		document.adminForm.boxchecked_name.value = document.adminForm.boxchecked_name.value + document.getElementById(task_name).value + ',';
		image_name = 'image_name'+document.getElementById(task_id).value;
		document.adminForm.boxchecked_image.value = document.adminForm.boxchecked_image.value + document.getElementById(image_name).value + ',';
	}
	if (c) {
		document.adminForm.boxchecked.value = n2;
	} else {
		document.adminForm.boxchecked.value = 0;
		document.adminForm.boxchecked_id.value = '';
		document.adminForm.boxchecked_name.value = '';
		document.adminForm.boxchecked_image.value = '';
	}
}

function isChecked(id, isitchecked){

	if (isitchecked == true){
		//document.adminForm.boxchecked.value++;
		document.adminForm.boxchecked_id.value = document.adminForm.boxchecked_id.value + id + ',';
		task_name = 'file_name'+id;
		document.adminForm.boxchecked_name.value = document.adminForm.boxchecked_name.value + document.getElementById(task_name).value + ',';
		task_image = 'image_name'+id;
		document.adminForm.boxchecked_image.value = document.adminForm.boxchecked_image.value + document.getElementById(task_image).value + ',';
	}
	else {
			my_tasks = document.getElementById('boxchecked_id').value;
			my_tasks_name = document.getElementById('boxchecked_name').value;
			my_tasks_image = document.getElementById('boxchecked_image').value;
			my_tasks_name_array = my_tasks_name.split(",");
			my_tasks_image_array = my_tasks_image.split(",");
			var my_tasks_array=my_tasks.split(",");		
			var part_num=0;
			var new_task_array = '';
			var new_tasks_name_array = '';
			var new_tasks_image_array = '';
				while (part_num < my_tasks_array.length-1)
				 {
					if(my_tasks_array[part_num]!=id)
						{
							new_task_array = new_task_array+my_tasks_array[part_num]+',';
							new_tasks_name_array = new_tasks_name_array + my_tasks_name_array[part_num]+',';
							new_tasks_image_array = new_tasks_image_array + my_tasks_image_array[part_num]+',';
						}	
				  part_num+=1;
				  }	
			document.getElementById('boxchecked_id').value = new_task_array;
			document.getElementById('boxchecked_name').value = new_tasks_name_array;
			document.getElementById('boxchecked_image').value = new_tasks_image_array;	  	
	}
}

function submitbutton(pressbutton) {
	var form = document.adminForm;
	//alert(document.getElementById('boxchecked_id').value);
	if (pressbutton=='addtasks') {
		my_tasks = document.getElementById('boxchecked_id').value;
		//alert(my_tasks);
		my_tasks_name = document.getElementById('boxchecked_name').value;
		//alert(my_tasks_name);
		my_tasks_image = document.getElementById('boxchecked_image').value;
		node = document.getElementById('node').value;	
		var my_tasks_name_array = my_tasks_name.split(",");
		var my_tasks_array=my_tasks.split(",");
		var my_tasks_image_array=my_tasks_image.split(",");	
		var part_num=0;
		while (part_num < my_tasks_array.length-1){
			addtask1 (my_tasks_array[part_num], my_tasks_name_array[part_num], my_tasks_image_array[part_num], node);
			part_num+=1;
		}
		window.top.setTimeout('window.parent.document.getElementById("sbox-window").close()', 200);
		//document.adminForm.submit();
	}
}	
</script>

<div style="float: right;"><?php echo JText::_("GURU_CLICK_TO_ADD_SCREEN"); ?></div>
<br /><br />
<div>
<form name="form1" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruDays&task=addtask&no_html=1&cid[]=<?php echo $data_get['cid'][0];?>" method="post">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>
				<input type="text" name="search_text" value="" />
				<input type="submit" name="submit_search" value="Search" />
			</td>
			<td><?php echo JText::_("GURU_CATEGORY"); ?>:
			<?php 
				if(isset($data_post['category']))
					$categ_set = $data_post['category'];
				else
					$categ_set = -1;
				$lists['treecateg']=$this->list_all("category", 0, $categ_set); ?>
		</td>
		<td><?php echo JText::_("GURU_MEDIA_"); ?>:
			<select name="media_select" onChange="document.form1.submit()">
				<option value="all" <?php if ( (!isset($data_post['media_select'])) || (isset($data_post['media_select']) && $data_post['media_select'] == 'all') ) echo ' selected="selected" ';?>>all</option>
				<option value="audio" <?php if (isset($data_post['media_select']) && $data_post['media_select'] == 'audio') echo ' selected="selected" ';?>>audio</option>
				<option value="video"  <?php if (isset($data_post['media_select']) && $data_post['media_select'] == 'video') echo ' selected="selected" ';?>>video</option>
				<option value="docs" <?php if (isset($data_post['media_select']) && $data_post['media_select'] == 'docs') echo ' selected="selected" ';?>>document</option>
				<option value="url" <?php if (isset($data_post['media_select']) && $data_post['media_select'] == 'url') echo ' selected="selected" ';?>>url</option>
			</select>
		</td>
	</tr>
</table>
<input type="hidden" name="node" id="node" value="<?php echo $node; ?>" />		
</form>
</div>
<br />
<div>

<form id="adminForm" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruDays&task=edit&cid[]=<?php echo $data_get['cid'][0];?>" name="adminForm" method="post">
<div id="editcell">
<table class="table table-striped adminlist">
<thead>
	<tr>
		<th align="left" width="20"><?php echo JText::_("GURU_ID"); ?></th>
		<th width="5%" align="left"><input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" /></th>
		<th align="left"><?php echo JText::_("GURU_SCREEN"); ?></th>
		<th align="left"><?php echo JText::_("GURU_CATEGORY"); ?></th>
		<th align="left"><?php echo JText::_("GURU_MEDIA_"); ?></th>
		<th align="left"><?php echo JText::_("GURU_PUBLISHED"); ?></th>
	</tr>
</thead>
<?php 
	$cmp = intval($data_get['cid'][0]);
	
	if(isset($data_get['node']))
		$node = intval($data_get['node']);
	else
		$node = intval($data_post['node']);	
	
 if ($n>0) { 
	for ($i = 0; $i < $n; $i++):
	$file = $this->medias[$i];
	$id = $file->id;
	
	$task_media = $this->getTask_type($file->id);
	
	$image_assoc = '';
	if(isset($task_media) && $task_media == 'audio') $image_assoc = 'audio.gif';
	if(isset($task_media) && $task_media == 'video') $image_assoc = 'video.gif';
	if(isset($task_media) && $task_media == 'docs') $image_assoc = 'doc.gif';
	if(isset($task_media) && $task_media == 'url') $image_assoc = 'url.gif';
	if(isset($task_media) && $task_media == 'quiz') $image_assoc = 'quiz.gif';
	if(isset($task_media) && $task_media == 'text') $image_assoc = 'text.gif';			
	$task_media_image = '<img border="0" src="'.JURI::base()."components/com_guru/images/".$image_assoc.'" />';
	
	//$checked = JHTML::_('grid.id', $i, $id);
	$checked = guruAdminModelguruDays::checkbox_construct_add_task( $i, $id ); 
	$link = "addtask('".$id."', '".addslashes($file->name)."', '".addslashes($file->category)."', '".addslashes($file->published)."', '".$cmp."', '".$image_assoc."', '".$node."')";
	$published = JHTML::_('grid.published', $file, $i ); ?>
<tbody>
	<tr class="camp0" id="<?php echo $i.'_'.$file->category;?>"> 
	   <td align="left"><?php echo $file->id;?></td>		
	   <td><?php echo $checked; ?></td>
	    <td align="left">
			<a onclick="<?php echo $link;?>" href="#"><?php echo $file->name;?></a>
			<input type="hidden" name="file_name<?php echo $file->id;?>" id="file_name<?php echo $file->id;?>" value="<?php echo $file->name;?>" />
			<input type="hidden" name="image_name<?php echo $file->id;?>" id="image_name<?php echo $file->id;?>" value="<?php echo $image_assoc;?>" />
		</td>		
		<td align="left">
	     	  <?php if ($file->category=='0') echo '- Top -'; else echo $file->catname;//$file->category;?>
		</td align="left">		
		<td align="left"><?php echo $task_media_image; ?></td>
		<td align="left"><?php echo $published;?></td>		
	</tr>
</tbody>
<?php endfor;
 } ?>				
			</table>
		</div>
	</div>
	<input type="button" class="btn" onClick="submitbutton('addtasks')" id="addtasks" name="addtasks" value="<?php echo JText::_( 'GURU_DAY_ADD_TASKS' );?>" />
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked_id" id="boxchecked_id" value="" />
	<input type="hidden" name="boxchecked_name" id="boxchecked_name" value="" />
	<input type="hidden" name="boxchecked_image" id="boxchecked_image" value="" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="node" id="node" value="<?php echo $node; ?>" />
	<input type="hidden" name="group_id" id="group_id" value="<?php echo $cmp; ?>" />
	<input type="hidden" name="controller" value="guruDays" />
</form>