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
$scorms = $this->scorms;
$n = count($scorms);
JHtml::_('behavior.framework');

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
<link rel="stylesheet" href="<?php echo JURI::base()."components/com_guru/css/modal.css";?>" type="text/css" />
<script type="text/javascript" src="<?php echo JURI::root().'media/system/js/mootools.js' ?>"></script>
<script type="text/javascript" src="<?php echo JURI::base().'components/com_guru/js/modal.js' ?>"></script>
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
  		document.getElementsByTagName("head")[0].appendChild(fileref);
}

function loadprototipe(){
	//WE ARE NOT LONGER BEEN USING AJAX FROM prototype-1.6.0.2.js, INSTEAD WE WILL BE USING jQuery.ajax({}) function
	//loadjscssfile("<?php echo JURI::base().'components/com_guru/views/gurutasks/tmpl/prototype-1.6.0.2.js' ?>","js");
}

function addscorm (idu, name, asoc_file, description) {
	loadprototipe();
	var url = 'index.php?option=com_guru&controller=guruTasks&tmpl=component&format=raw&task=ajax_request2&id='+idu+'&type=scorm';
	jQuery.ajax({
		url:url,
  		method: 'get',
  		asynchronous: 'true',
  		success: function(transport) {
			replace_m = document.getElementById('to_replace').value;
			to_be_replaced = parent.document.getElementById('media_'+replace_m);
			to_be_replaced.innerHTML = '&nbsp;';
			if(replace_m == 99){
				if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {to_be_replaced.innerHTML += transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>'; } else {
					to_be_replaced.innerHTML += transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><br /><div  style="text-align:center"><i>' + description + '</i></div>';
				}
			} else {
				to_be_replaced.innerHTML += transport;
				parent.document.getElementById("media_"+99).style.display="";
				parent.document.getElementById("description_med_99").innerHTML=''+name;
				
				parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
				parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
				parent.document.getElementById('db_media_'+replace_m).value = idu;
			}			
		
			screen_id = document.getElementById('the_screen_id').value;
			replace_edit_link = parent.document.getElementById('a_edit_media_'+replace_m);
			replace_edit_link.href = 'index.php?option=com_guru&controller=guruScorm&tmpl=component&task=edit&cid[]='+ idu;
			
			if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {
				var qwe='&nbsp;'+transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>';
			} else {
				var qwe='&nbsp;'+transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
			}
			window.parent.test(replace_m, idu,qwe);
  		}
	});
	
	setTimeout('window.parent.document.getElementById("close").click()',1000);
	return true;
}

</script>

	<div style="float: left; font-weight:bold"><?php echo JText::_("GURU_CLICK_TO_SCORM"); ?></div>
		<br /><br />
	<div>
<form name="adminForm2" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruTasks&task=addscorm&med=<?php echo $data_get['med'];?>&tmpl=component&cid[]=<?php echo $data_get['cid'][0];?><?php if(isset($data_get['scorm'])){echo "&scorm=".$data_get['scorm'];}?><?php if(isset($_REQUEST['type'])){echo "&type=".$_REQUEST['type'];}?>&pid=<?php echo $data_get['pid']; ?>" method="post">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="g_top_filters">
            	<?php
                	$session = JFactory::getSession();
					$registry = $session->get('registry');
					$search_scorm_tskmed = $registry->get('search_scorm_tskmed', "");
					
					if(isset($data_post['search_scorm'])&&($data_post['search_scorm']!='')){
						$search_scorm_tskmed = $data_post['search_scorm'];
					}
				?>
                
				<input type="text" name="search_scorm" value="<?php echo $search_scorm_tskmed; ?>" />
				<input type="hidden" name="type" value="<?php if(isset($data_get['type'])) echo $data_get['type']; elseif (isset($data_post['type'])) echo $data_post['type'];?>" />
				<input class="btn" type="submit" name="submit_search" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" />
                
                <input type="hidden" name="type" value="scorm">
                <input type="hidden" name="med" value="<?php echo $data_get['med']; ?>">
                <input type="hidden" name="pid" value="<?php echo $data_get['pid']; ?>">
			</td>
			<td>
			</td>
			<td>
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
				<th><?php echo JText::_("GURU_TITLE"); ?></th>
				<th><?php echo JText::_("GURU_COURSE"); ?></th>
			</tr>
		</thead>
		<tbody>
<?php 
		$pid = intval($data_get['cid'][0]);
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$addmed_tskmed_to_rep = $registry->get('addmed_tskmed_to_rep', "");
		
		if ($n>0) { 
			for ($i = 0; $i < $n; $i++){
				$file =$scorms[$i];
				if(isset($data_get['med'])){	
					$media_to_replace = $data_get['med'];
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$registry->set('addmed_tskmed_to_rep', $data_get['med']);
					
				} elseif(isset($addmed_tskmed_to_rep) && trim($addmed_tskmed_to_rep) != ""){
					$media_to_replace = $addmed_tskmed_to_rep;
				} else {
					$media_to_replace = NULL;
			}

			$id = $file->id;
			$checked = JHTML::_('grid.id', $i, $id);
			$asoc_file = "-";
			$link = "addscorm('".$id."', '".addslashes($file->title)."', '".addslashes($asoc_file)."', '' ); ";
		?>
			<tr class="camp0"> 
	   			<td><?php echo $file->id;?></td>		
	    		<td><a onmouseover="loadprototipe()" onclick="<?php echo $link;?>" href="#"><?php echo $file->title;?></a></td>	
				<td>
                	<?php
						include_once(JPATH_SITE."/administrator/components/com_guru/models/guruscorm.php");
						$modelguruscorm = new guruAdminModelguruScorm();
                    	$course = $modelguruscorm->scormCourse($id);
						echo $course;
					?>
                </td>	
			</tr>
		<?php 
		} // endif for MEDIA check
 } ?>

	<form name="adminForm" id="adminForm" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruTasks&task=addscorm&med=<?php echo $data_get['med'];?>&tmpl=component&cid[]=<?php echo $data_get['cid'][0];?>" method="post">
		<table>
        	<tr>
        		<td colspan="6">
                    <div class="pagination pagination-toolbar">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </div>
                    <div class="btn-group pull-left">
                        <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                        <?php echo $this->pagination->getLimitBox(); ?>
                   </div>
                </td>
			</tr>
		</table>
		<input type="hidden" name="filter2" id="filter2" value="<?php if(isset($filter2)) {echo $filter2;} else {echo '3';}?>" />
        <input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
	</form>
	</tbody>
</table>
</div>

</div>
<input type="hidden" id="to_replace" value="<?php 
	echo $media_to_replace; 
?>">
<input type="hidden" id="the_screen_id" value="<?php 
	echo $pid; 
?>">