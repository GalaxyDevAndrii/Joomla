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
$count = $db->loadResult();

if(!isset($count) || $count == 0){
	echo "<b>".JText::_("GURU_NO_MEDIA")."</b>";
	return;
}

$session = JFactory::getSession();
$registry = $session->get('registry');
$filter_status_tskmed = $registry->get('filter_status_tskmed', "");

if( isset($data_post['filter2']) && !isset($data_post['filter_status']) && $filter_status_tskmed == "" ){
	$data_post['filter_status']=$data_post['filter2'];
}

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
textarea {
    width: 100px!important;
}
select {
    width: 160px!important;
	margin-right: 10px;
}

div.modal2 {
		left: 4% !important;
		height: 380px !important;
		top:6%!important;
		position: fixed;
    	z-index: 9999;
}
.modal-header {
	border-bottom:none !important;
}

</style>

<!-- <link rel="StyleSheet" href="<?php echo JURI::root(); ?>media/jui/css/bootstrap.min.css" type="text/css"/>
<link rel="StyleSheet" href="<?php echo JURI::root(); ?>media/jui/css/bootstrap-responsive.min.css" type="text/css"/>


<script type="text/javascript" src="<?php echo JURI::root(); ?>media/jui/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>media/jui/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>media/system/js/mootools-core.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>media/system/js/core.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>media/system/js/mootools-more.js"></script> -->

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
	
	loadjscssfile("<?php echo JURI::base().'components/com_guru/views/gurutasks/tmpl/prototype-1.6.0.2.js' ?>", "js");
</script>

<link rel="stylesheet" href="<?php echo JURI::base()."components/com_guru/css/modal.css";?>" type="text/css" />

<script>
	function showContent2(href){
		jQuery( '#myModal2 .modal-body iframe').attr('src', href);
	}
	
	function loadprototipe(){
		//WE ARE NOT LONGER BEEN USING AJAX FROM prototype-1.6.0.2.js, INSTEAD WE WILL BE USING jQuery.ajax({}) function
		//loadjscssfile("<?php echo JURI::root().'components/com_guru/views/gurutasks/tmpl/prototype-1.6.0.2.js' ?>","js");
	}
	
	function addmedia (idu, name, asoc_file, description, action) {
		loadprototipe();
		if(action == "new_module"){
			replace_m = document.getElementById('to_replace').value;
			parent.document.getElementById('db_media_'+replace_m).value = idu;
			to_be_replaced = parent.document.getElementById('text_'+replace_m);
			to_be_replaced.innerHTML = "";
		
			replace_m = document.getElementById('to_replace').value;
			to_be_replaced = parent.document.getElementById('media_'+replace_m);
			to_be_replaced.innerHTML = name;
			parent.document.getElementById('db_media_'+replace_m).value = idu;
			parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
			parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
			window.parent.document.getElementById('close').click();
			return true;
		}
		
		var url = 'index.php?option=com_guru&controller=guruTasks&tmpl=component&format=raw&task=ajax_request2&id='+idu;
		jQuery.ajax({ url: url,
		  method: 'get',
		  asynchronous: 'true',
		  success: function(transport) {
				replace_m = document.getElementById('to_replace').value;
				//to_be_replaced = top.document.getElementById('media_'+replace_m);//
				to_be_replaced = parent.document.getElementById('media_'+replace_m);
				to_be_replaced.innerHTML = '&nbsp;';

				if(description == ""){
					description = name;
				}

				if(replace_m != 99){
					if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))){
						to_be_replaced.innerHTML += transport+'<p /><div style="text-align:center"><i>' + description + '</i></div>'; 
					}
					else{
						var videoInput = document.createElement("div");
						videoInput.innerHTML = transport+'<br /><div  style="text-align:center"><i>' + description + '</i></div>';
						to_be_replaced.appendChild(videoInput);
						//to_be_replaced.innerHTML += transport+'<br /><div  style="text-align:center"><i>' + description + '</i></div>';
					}
					replace_edit_link = parent.document.getElementById('a_edit_media_'+replace_m);
					replace_edit_link.href = 'index.php?option=com_guru&controller=guruMedia&tmpl=component&task=editsboxx&cid[]='+ idu+"&scr="+replace_m;
				}
				else{
					to_be_replaced.innerHTML += transport;
					parent.document.getElementById("media_"+99).style.display="";
					parent.document.getElementById("description_med_99").innerHTML=''+name;
				}
				parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
				parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
				parent.document.getElementById('db_media_'+replace_m).value = idu;
				
				screen_id = document.getElementById('the_screen_id').value;
				
				if((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))){
					var qwe='&nbsp;'+transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>';
				}
				else{
					var qwe='&nbsp;'+transport+'<br /><div style="text-align:center"><i>' + description + '</i></div>';
				}
				//window.parent.test(replace_m, idu, '<span class="success-add-media"><?php //echo JText::_("GURU_SUCCESSFULLY_ADED_MEDIA"); ?></span>');
				window.parent.test(replace_m, idu, qwe);
		  },
		}
		
		);
		setTimeout('window.parent.document.getElementById("close").click()', 1000);
	
		return true;
	}
</script>

<div style="float: left; font-weight:bold"><?php echo JText::_("GURU_CLICK_TO_MEDIA"); ?></div>
<br /><br />
<div>
<form name="adminForm2" id="adminForm2" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruTasks&task=addmedia&med=<?php echo $data_get['med'];?>&tmpl=component&cid[]=<?php echo $data_get['cid'][0];?><?php if(isset($data_get['quiz'])){echo "&quiz=".$data_get['quiz'];}?><?php if(isset($data_get['type'])){echo "&type=".$data_get['type'];}?><?php if(isset($data_get['action'])){echo "&action=".$data_get['action'];}?>" method="post">
<div class="g_top_filters span12">
	<div>
		<div class="g_row">
        	<?php
            	$session = JFactory::getSession();
				$registry = $session->get('registry');
				$search_value_session = $registry->get('search_text', "");
				$search_value = JFactory::getApplication()->input->get('search_text', "");
				
				if(trim($search_value) != ''){
					// do nothing
				}
				elseif(isset($search_value_session) && ($search_value_session != '')) {
					$search_value = $search_value_session;
				}
			?>
        
			<input type="text" name="search_text" value="<?php echo $search_value; ?>" />
			<input type="hidden" name="type" value="<?php if(isset($data_get['type'])) echo $_REQUEST['type']; elseif (isset($_REQUEST['type'])) echo $_REQUEST['type'];?>" />
			<input class="btn "type="submit" name="submit_search" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" />
		</div>
		<div class="g_row"><?php 
			if(!isset($_REQUEST['type'])){
				$task = JFactory::getApplication()->input->get("task", "");				
				if(isset($data_get['quiz']) && ($data_get['quiz']=='yes')){
					echo "&nbsp;"; 
				} 
				else{
				
					if($task != "addmedia" && $task != "addtext" ){
						$session = JFactory::getSession();
						$registry = $session->get('registry');
						$filter_type_tskmed = $registry->get('filter_type_tskmed', "");
			?>
						<?php echo JText::_("GURU_TASKS_MEDIATYPE"); ?>:&nbsp;<select name="filter_type"  onchange="document.adminForm2.submit()">
						<option value="">- <?php echo JText::_("GURU_SELECT_TYPE"); ?> -</option>
						<?php 
						foreach($this->types as $element){
							if(($element->type!='quiz')&&($element->type!='text')){
								echo "<option value='".$element->type."' ";
								if(isset($data_post['filter_type'])){
									if($element->type==$data_post['filter_type']) {echo "selected='selected'";}
								} elseif (isset($filter_type_tskmed) && trim($filter_type_tskmed) != ""){
									if($element->type == $filter_type_tskmed){
										echo "selected='selected'";
									}
								}
								echo ">".$element->type;
								echo "</option>";
							}
						}
				?>
			</select>
			<?php 
					}
				} //end get quiz?>
			<?php 
			} //end get type
			?>
		</div>
         <div class="g_row">

        <?php 
			$type="";
			$type = @$data_get['type'];
			
			if($type != "quiz"){
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$filter_status_tskmed = $registry->get('filter_status_tskmed', "");
		
		?>
            <div class="g_cell span4 pull-left">
                <?php echo JText::_("GURU_STATUS"); ?>:&nbsp;<select name="filter_status"  onchange="document.adminForm2.submit()">
                    <option value="3">- select status -</option>
                    <option value="1" <?php 
                        if(isset($data_post['filter_status'])&&($data_post['filter_status']==1)){
                            echo 'selected="selected"';$filter2=1;
                        }
						elseif(isset($filter_status_tskmed)&&($filter_status_tskmed==1)){
                            echo 'selected="selected"';$filter2=1;				
                        }
                    ?>><?php echo JText::_("GURU_PUBLISHED"); ?></option>
                    <option value="2" <?php 
                        if(isset($data_post['filter_status'])&&($data_post['filter_status']==2)){
                            echo 'selected="selected"';$filter2=2;
                        } elseif(isset($filter_status_tskmed)&&($filter_status_tskmed==2)){
                            echo 'selected="selected"';$filter2=2;				
                        }
                    ?>><?php echo JText::_("GURU_UNPUBLISHED"); ?></option>
                </select>
            </div>
            
            <div class="g_cell span4 pull-left">
                <?php
                    echo JText::_('GURU_TREEMEDIACAT'),":"."&nbsp;";
                    $all_media_categ = guruAdminModelguruTask::getAllMediaCategs();
                    $filter_media = JFactory::getApplication()->input->get("filter_media", "");
                    
                    if($filter_media != "" || $filter_media == "-1"){
                        $session = JFactory::getSession();
						$registry = $session->get('registry');
						$registry->set('filter_media', $filter_media);
                    }
                    else{
						$session = JFactory::getSession();
						$registry = $session->get('registry');
						$filter_media = $registry->get('filter_media', "");
                    }
                ?>
                <select name="filter_media"  onchange="document.adminForm2.submit()">
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
            </div>
           <div class=" g_cell span4 pull-left">
           
                <?php if(@$data_get['type'] != "audio") {echo JText::_('GURU_TYPE');
                    $filter_type = JFactory::getApplication()->input->get("filter_type", "");
                    $data_post["filter_type"] = $filter_type;
                    
            ?>
                <select name="filter_type" onChange="document.adminForm2.submit()">
                    <option value="" <?php if ( (!$data_post["filter_type"]) || (isset($data_post["filter_type"]) && $data_post['filter_type'] == '') ) echo ' selected="selected" ';?>><?php echo JText::_("GURU_SELECT"); ?></option>
                    <option value="audio" <?php if (isset($data_post['filter_type']) && $data_post['filter_type'] == 'audio') echo ' selected="selected" ';?>><?php echo JText::_("GURU_AUDIO"); ?></option>
                    <option value="video"  <?php if (isset($data_post['filter_type']) && $data_post['filter_type'] == 'video') echo ' selected="selected" ';?>><?php echo JText::_("GURU_VIDEO"); ?></option>
                    <option value="docs" <?php if (isset($data_post['filter_type']) && $data_post['filter_type'] == 'docs') echo ' selected="selected" ';?>><?php echo JText::_("GURU_DOCS"); ?></option>
                    <option value="url" <?php if (isset($data_post['filter_type']) && $data_post['filter_type'] == 'url') echo ' selected="selected" ';?>><?php echo JText::_("GURU_URL"); ?></option>
                    <option value="Article" <?php if (isset($data_post['filter_type']) && $data_post['filter_type'] == 'Article') echo ' selected="selected" ';?>><?php echo JText::_("GURU_ARTICLE"); ?></option>
                    <option value="file" <?php if (isset($data_post['filter_type']) && $data_post['filter_type'] == 'file') echo ' selected="selected" ';?>><?php echo JText::_("GURU_FILE"); ?></option>
                    <option value="image" <?php if (isset($data_post['filter_type']) && $data_post['filter_type'] == 'image') echo ' selected="selected" ';?>><?php echo JText::_("GURU_IMAGE"); ?></option>
                </select>
                <?php }?>
            </div>
         <?php } ?>
         </div>
	</div>
</div>	
<div class="clearfix"></div>	
</form>
</div>
<br />

<div id="myModal2" class="modal2 hide">
    <div class="modal-header">
        <button type="button" id="close" class="close" data-dismiss="modal" aria-hidden="true"><img src="components/com_guru/images/closebox.png"></button>
     </div>
     <div class="modal-body" style="background-color:#FFFFFF;" >
     	<iframe height="330" width="630" frameborder="0"></iframe>
    </div>
</div>
<div>
<div id="editcell">
<table class="table table-striped adminlist">
<thead>
	<tr>
		<th width="20"><?php echo JText::_("GURU_ID"); ?></th>
		<th><?php echo JText::_("GURU_NAME"); ?></th>
		<th><?php echo JText::_("GURU_TYPE"); ?></th>
		<th><?php echo JText::_("GURU_TREEMEDIACAT"); ?></th>
		<th><?php echo JText::_("GURU_PREVIEW"); ?></th>
		<th><?php echo JText::_("GURU_PUBLISHED"); ?></th>
	</tr>
</thead>

<tbody>
<?php 
	$pid = intval($data_get['cid'][0]);
 if ($n>0) { 
	for ($i = 0; $i < $n; $i++):
	//echo "<font color='blue'>".$i."</font>";
	$file =$medias[$i];
	
	@$file->prevw = str_replace("autostart=1", "autostart=0", @$file->prevw);
	
	$session = JFactory::getSession();
	$registry = $session->get('registry');
	$addmed_tskmed_to_rep = $registry->get('addmed_tskmed_to_rep', "");
	
	if(isset($data_get['med'])){	
		$media_to_replace = $data_get['med'];
		$registry->set('addmed_tskmed_to_rep', $data_get['med']);
	}
	elseif(isset($addmed_tskmed_to_rep) && trim($addmed_tskmed_to_rep) != ""){
		$media_to_replace = $registry->get('addmed_tskmed_to_rep', "");
	}
	else{
		$media_to_replace = NULL;
	}

	$id = $file->id;
	$checked = JHTML::_('grid.id', $i, $id);
	$asoc_file = guruAdminModelguruTask::get_asoc_file_for_media($id);
	$all_media_categories = guruAdminModelguruTask::getMediaCategoriesName();	
	$action = JFactory::getApplication()->input->get("action", "");
	$link = "";
	
	$file->name = str_replace('"', "&quot;", $file->name);
	$file->instructions = str_replace('"', "&quot;", $file->instructions);

	if($action == "new_module"){
		$link = "addmedia('".$id."', '".addslashes($file->name)."', '".addslashes($asoc_file)."', '".str_replace(array("\n", "\t", "\r"), '<br>',$file->instructions)."', 'new_module'); ";
	}
	else{
		$link = "addmedia('".$id."', '".addslashes($file->name)."', '".addslashes($asoc_file)."', '".str_replace(array("\n", "\t", "\r"), '<br>',$file->instructions)."' ); ";
	}
	
	$published = $file->published; 
	
	// displaying now only the MEDIA (without DOCS and without QUIZ)
	//	if($file->type!='docs' && $file->type!='quiz' && $file->type!='text')
	$type = "";
	switch ($file->type) {
		case "video": $type = JText::_('GURU_MEDIATYPEVIDEO');	    						
			break;
		case "audio": $type = JText::_('GURU_MEDIATYPEAUDIO');	    						
			break;
		case "docs": $type = JText::_('GURU_MEDIATYPEDOCS');	    						
			break;
		case "url": $type = JText::_('GURU_MEDIATYPEURL');	    						
			break;
		case "Article": $type = JText::_('GURU_MEDIATYPEARTICLE');	    						
			break;
		case "image": $type = JText::_('GURU_MEDIATYPEIMAGE');	    						
			break;
		case "text": $type = JText::_('GURU_MEDIATYPETEXT');	    						
			break;
		case "file": $type = JText::_('GURU_MEDIATYPEFILE');	    						
			break;
		case "quiz": $type = JText::_('GURU_MEDIATYPEQUIZ');	    						
			break;																		
	}
	
	if($file->type!='text')
	{
	?>
	<tr class="camp0"> 
	   	<td><?php echo $file->id;?></td>		
	    <td><a onmouseover="loadprototipe();" onclick="<?php echo $link;?>" href="#"><?php echo $file->name;?></a></td>		
		<td><?php echo $type ;?></td>
		<td>
			<?php 
				if(isset($all_media_categories) && isset($all_media_categories[$file->category_id])){
					echo $all_media_categories[$file->category_id]["name"];
				}
			?>
		</td>		
		<td><?php if(isset($file->prevw)) { echo $file->prevw; } else {?>
			<span><a data-toggle="modal" data-target="#myModal2" onClick = "showContent2('index.php?option=com_guru&controller=guruMedia&task=preview&tmpl=component&cid[]=<?php echo $file->id;?>');" href="#"><?php echo JText::_("GURU_MEDIA_PREVIEW_LOWER"); ?></a></span> 
		<?php }?>
		</td>
		<td><?php if($published==1) { echo '<img src="components/com_guru/images/tick.png" alt="Published" />';} 
				else { echo '<img src="components/com_guru/images/publish_x.png" alt="Unpublished" />';}
			?></td>		
	</tr>
<?php 
	} // endif for MEDIA check
	endfor;
 } ?>
<form name="adminForm" id="adminForm" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruTasks&task=addmedia&med=<?php echo $data_get['med'];?>&tmpl=component&cid[]=<?php echo $data_get['cid'][0];?><?php if(isset($data_get['type'])){echo "&type=".$data_get['type'];}?>" method="post">
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
	echo @$media_to_replace; 
?>">
<input type="hidden" id="the_screen_id" value="<?php 
	echo $pid; 
?>">
<script language="javascript">
	jQuery('#myModal2').on('hide', function () {
	 jQuery('#myModal2 .modal-body iframe').attr('src', '');
});
</script>	