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
JHtml::_('behavior.framework');

$medias = $this->medias;
$n = count($medias);
$db = JFactory::getDBO();

$sql = "select count(*) from #__guru_media";
$db->setQuery($sql);
$db->execute();
$count = $db->loadresult();

if(!isset($count) || $count == 0){
	echo "<b>".JText::_("GURU_NO_MEDIA")."</b>";
	return;
}
$v = 1;
$z = 1;

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

<script type="text/javascript">
function loadjscssfile(filename, filetype){
 if (filetype=="js"){ //if filename is a external JavaScript file
  var fileref=document.createElement('script')
  fileref.setAttribute("type","text/javascript")
  fileref.setAttribute("src", filename)
 }
 else if (filetype=="css"){ //if filename is an external CSS file
  var fileref=document.createElement("link")
  fileref.setAttribute("rel", "stylesheet")
  fileref.setAttribute("type", "text/css")
  fileref.setAttribute("href", filename)
 }
 if (typeof fileref!="undefined")
  document.getElementsByTagName("head")[0].appendChild(fileref)
}
</script>
<?php //WE ARE NOT LONGER BEEN USING AJAX FROM prototype-1.6.0.2.js, INSTEAD WE WILL BE USING jQuery.ajax({}) function ?>
<script type="text/javascript" src="<?php //echo JURI::base().'components/com_guru/views/gurutasks/tmpl/prototype-1.6.0.2.js' ?>"></script>


<script>

function loadprototipe(){
	//WE ARE NOT LONGER BEEN USING AJAX FROM prototype-1.6.0.2.js, INSTEAD WE WILL BE USING jQuery.ajax({}) function
	//loadjscssfile("<?php echo JURI::base().'components/com_guru/views/gurutasks/tmpl/prototype-1.6.0.2.js' ?>","js");
}
function addmedia (idu, name, asoc_file, description, isquiz, the_real_quiz_id) {
	loadprototipe();
	var url = 'index.php?option=com_guru&controller=guruTasks&tmpl=component&format=raw&task=ajax_request3&id='+idu;
	if(the_real_quiz_id == "new_module"){	
		replace_m = document.getElementById('to_replace').value;
		to_be_replaced = parent.document.getElementById('media_'+replace_m);
		to_be_replaced.innerHTML = name;
		parent.document.getElementById('db_media_'+replace_m).value = idu;
		parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
		parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
		//window.parent.setTimeout('document.getElementById("sbox-window").close()', 1);
		//window.parent.close_modal();
		
		replace_m = document.getElementById('to_replace').value;	
		to_be_replaced = parent.document.getElementById('text_'+replace_m);
		to_be_replaced.innerHTML = "";
		window.parent.document.getElementById('close').click();

		return true;
	}
	<?php  if($v == 1){?>
		jQuery.ajax({url: url, 
		  method: 'get',
		  asynchronous: 'true',
		  success: function(html) {
				replace_m = document.getElementById('to_replace').value;
				//to_be_replaced = top.document.getElementById('text_'+replace_m);
				to_be_replaced = parent.document.getElementById('text_'+replace_m);
				to_be_replaced.innerHTML = '&nbsp;';
				
				//to_be_replaced.innerHTML += '<div style="text-align:center"><i>'+ name +'</i></div><br /><br /><div  style="text-align:center"><i>' + description + '</i></div><br /><br />'+transport;
				to_be_replaced.innerHTML += html;
		
				parent.document.getElementById('before_menu_txt_'+replace_m).style.display = 'none';
				parent.document.getElementById('after_menu_txt_'+replace_m).style.display = '';
				parent.document.getElementById('db_text_'+replace_m).value = idu;
				
				screen_id = document.getElementById('the_screen_id').value;
				replace_edit_link = parent.document.getElementById('a_edit_text_'+replace_m);
				if(isquiz == '0')
					replace_edit_link.href = 'index.php?option=com_guru&controller=guruMedia&tmpl=component&task=editsboxx&cid[]='+ idu +'&scr=' + screen_id;		
				else	
					replace_edit_link.href = 'index.php?option=com_guru&controller=guruQuiz&tmpl=component&task=editsbox&cid[]='+ the_real_quiz_id +'&scr=' + screen_id;		
					
				var qwe='<div style="text-align:center"><i>'+ name +'</i></div><br /><br /><div  style="text-align:center"><i>' + description + '</i></div>&nbsp;'+html+'<br /><br />';
				//window.parent.test1(qwe);
				window.parent.txtest(replace_m, idu,qwe);
		  }
		}
		);
	<?php  }
		if($z == 1){?>
		//window.parent.close_modal();
	<?php  }?>
	setTimeout('window.parent.document.getElementById("close").click()',1000);

	return true;
}
</script>
<!-- <link rel="StyleSheet" href="<?php echo JURI::root(); ?>media/jui/css/bootstrap.min.css" type="text/css"/>
<link rel="StyleSheet" href="<?php echo JURI::root(); ?>media/jui/css/bootstrap-responsive.min.css" type="text/css"/>
<link rel="StyleSheet" href="components/com_guru/css/guru-j30.css" type="text/css"/>


<script type="text/javascript" src="<?php echo JURI::root(); ?>media/jui/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>media/jui/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>media/system/js/mootools-core.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>media/system/js/core.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>media/system/js/mootools-more.js"></script> -->

<div style="float: left; font-weight:bold"><?php echo JText::_("GURU_CLICK_TO_TEXT"); ?></div>
<br /><br />
<div>
<form name="form1" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruTasks&task=addtext&txt=<?php echo $data_get['txt'];?>&tmpl=component&cid[]=<?php echo $data_get['cid'][0];?>" method="post">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left" class="g_top_filters">
			<?php
				$search_value = JFactory::getApplication()->input->get("search_text", "");
				
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$search_value_session = $registry->get('search_value', "");
				
				if((isset($search_value_session)) && trim($search_value_session) != "" && ($search_value == "")){
					$search_value = $search_value_session;
				}
			?>
			<input type="text" name="search_text" value="<?php echo $search_value; ?>" />
			<input type="hidden" name="type" value="<?php if(isset($data_get['type'])) echo $data_get['type']; elseif (isset($data_post['type'])) echo $data_post['type'];?>" />
			<input type="submit" class="btn" name="submit_search" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" />
		</td>
		<td>
			<?php
				echo JText::_('GURU_TREEMEDIACAT'),":"."&nbsp;";
				$all_media_categ = guruAdminModelguruTask::getAllMediaCategs();
				$filter_media = JFactory::getApplication()->input->get("filter_media", "");
				
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$filter_media_session = $registry->get('filter_media', "");
				
				if(isset($filter_media_session) && trim($filter_media_session) != ""){
					if($filter_media != "" || $filter_media == "-1"){
						$registry->set('filter_media', $filter_media);
					}
					else{
						$filter_media = $filter_media_session;
					}
				}
			?>
			<select name="filter_media"  onchange="document.form1.submit()">
			<option value="-1">- <?php echo JText::_("GURU_ALL_CATEGORIES"); ?> -</option>
			<?php 
				if(isset($all_media_categ) && count($all_media_categ) > 0){
					foreach($all_media_categ as $key=>$value){
						$selected = "";
						if($value["id"] == $filter_media){
							$selected = 'selected="selected"';
						}
						echo '<option value="'.$value["id"].'" '.$selected.'>'.$value["name"].'</option>';
					}
				}
			?>
			</select>
		</td>
	</tr>
</table>		
</form>
</div>
<br />

<div>
<div id="editcell">
<table class="table table-striped adminlist">
<thead>
	<tr>
		<th width="20"><?php echo JText::_("GURU_ID"); ?></th>
		<th><?php echo JText::_("GURU_NAME"); ?></th>
		<th><?php echo JText::_("GURU_TREEMEDIACAT"); ?></th>
		<th><?php echo JTExt::_("GURU_PUBLISHED"); ?></th>
	</tr>
</thead>

<tbody>
<?php 
	$pid = intval($data_get['cid'][0]);
	if($n>0){ 
	for ($i = 0; $i < $n; $i++):
	$file = $this->medias[$i];	
	$media_to_replace = $data_get['txt'];

	$id = $file->id;
	$checked = JHTML::_('grid.id', $i, $id);
	$asoc_file = guruAdminModelguruTask::get_asoc_file_for_media($id);
	$action = JFactory::getApplication()->input->get("action", "");
	if($file->type=='quiz')
		{
			$the_quiz_id = guruAdminModelguruTask::real_quiz_id($file->id);
		}		
	
	$instructions = $file->instructions;
	$instructions = nl2br($instructions);
	$instructions = str_replace("<br/>", " ", $instructions);
	$instructions = str_replace("<br />", " ", $instructions);
	$instructions = str_replace("\n", " ", $instructions);
	$instructions = str_replace("\r\n", " ", $instructions);
	$instructions = str_replace("\r", " ", $instructions);
	
	$file->name = str_replace('"', "&quot;", $file->name);
	$file->instructions = str_replace('"', "&quot;", $file->instructions);
	
	if($file->type=='quiz'){
		$link = "addmedia('".$id."', '".addslashes($file->name)."', '".addslashes($asoc_file)."', '".addslashes($instructions)."' , '1', '".$the_quiz_id."' ); ";
	}	
	else{
		if($action == "new_module"){
			$link = "addmedia('".$id."', '".addslashes($file->name)."', '".addslashes($asoc_file)."', '".addslashes($instructions)."' , '0', 'new_module' ); ";
		}
		else{
			$link = "addmedia('".$id."', '".addslashes($file->name)."', '".addslashes($asoc_file)."', '".addslashes($instructions)."' , '0', '0' ); ";
		}	
	}	
	$published = JHTML::_('grid.published', $file, $i ); 
	
	if($file->type=='text')
	{
	?>
	<tr class="camp0"> 
	   <td><?php echo $file->id;?></td>		
	    <td><a onclick="<?php echo $link;?>" href="#"><?php echo $file->name;?></a></td>		
		<td>
	     	  <?php echo $file->categ_name; ?>
		</td>		
		<td><?php echo $published;?></td>		
	</tr>
<?php 
	} // endif for MEDIA check
	endfor;
 } ?>

<form id="adminForm" name="adminForm" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruTasks&task=addtext&txt=<?php echo $data_get['txt'];?>&tmpl=component&cid[]=<?php echo $data_get['cid'][0];?>" method="post">
<td colspan="6" align="center"><?php echo $this->pagination->getListFooter(); ?></td>
<input type="hidden" name="filter2" id="filter2" value="<?php if(isset($filter2)) {echo $filter2;} else {echo '3';}?>" />
</form> 
 
</tbody>
</table>

</div>

</div>
<input type="hidden" id="to_replace" value="<?php echo @$media_to_replace; ?>">
<input type="hidden" id="the_screen_id" value="<?php echo $pid; ?>">