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
jimport('joomla.html.pagination'); 
JHtml::_('behavior.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<?php

$data_get = JFactory::getApplication()->input->get->getArray();
$data_post = JFactory::getApplication()->input->post->getArray();

$doc = JFactory::getDocument();
$medias = $this->medias;
$db = JFactory::getDBO();
//$helpval = new JApplication();
if(empty($medias)) {
	echo "<strong>".JText::_("GURU_MEDIA_NOMED")."</strong>";
	return "";
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

$second = JFactory::getApplication()->input->get("second", "", "raw");

if($second == "ok"){
	$session = JFactory::getSession();
	$registry = $session->get('registry');
	$value = $registry->get('median_number', "");
	$value++;
	$registry->set('median_number', $value);
}
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
	
	
	function addexercise (idu, img_type, name, published, cid, root) {
		if(parent.document.getElementById('mediafiles').value.indexOf(idu) == -1){// is not in array
			var i = window.parent.document.getElementById("media_number").value;	
			
			var myrow = parent.document.createElement('TR');
			myrow.id = 'tr'+idu;
			
			parent.document.getElementById('mediafiles').value = parent.document.getElementById('mediafiles').value+idu+',';
			parent.document.getElementById('rowsmedia').appendChild(myrow);
			
			/*parent.document.getElementById('mediafiles').value = parent.document.getElementById('mediafiles').value+idu+',';
			parent.document.getElementById('rowsmedia').appendChild(myrow);*/

			
			var mycelltwo = parent.document.createElement('TD');
			mycelltwo.style.textAlign = 'center';
			myrow.appendChild(mycelltwo);
			var mycellthree = parent.document.createElement('TD');
			mycellthree.style.textAlign = 'center';
			myrow.appendChild(mycellthree);
			var mycellfive = parent.document.createElement('TD');
			mycellfive.style.textAlign = 'center';
			myrow.appendChild(mycellfive);
			var mycellseven = parent.document.createElement('TD');
			mycellseven.style.textAlign = 'center';
			myrow.appendChild(mycellseven);
			var mycelleight = parent.document.createElement('TD');
			mycelleight.style.textAlign = 'center';
			myrow.appendChild(mycelleight);
			
			
			var span ='<span class="sortable-handler active" style="cursor: move;"><i class="icon-menu"></i></span>';
			switch(img_type){
				case "Video":{						
							 img_path = root+"components/com_guru/images/video.gif";
							 img_type = '<img src="'+img_path+'" alt="video type"/>';									
							}
					break;
				case "Document":{						
							 img_path = root+"components/com_guru/images/doc.gif";
							 img_type = '<img src="'+img_path+'" alt="video type"/>';								
							}
					break;
				case "URL":{						
							 img_path = root+"components/com_guru/images/url.gif";
							 img_type = '<img src="'+img_path+'" alt="video type"/>';						
							}
					break;
				case "image":{						
							 img_path = root+"components/com_guru/images/image.jpg";
							 img_type = '<img src="'+img_path+'" alt="video type"/>';								
							}
					break;	
				case "Audio":{						
							 img_path = root+"components/com_guru/images/audio.gif";
							 img_type = '<img src="'+img_path+'" alt="audio type"/>';								
							}
					break;	
				case "Quiz":{						
							 img_path = root+"components/com_guru/images/quiz.gif";
							 img_type = '<img src="'+img_path+'" alt="quiz type"/>';								
							}
					break;		
				case "Document":{						
							 img_path = root+"components/com_guru/images/doc.gif";
							 img_type = '<img src="'+img_path+'" alt="doc type"/>';								
							}
					break;		
				case "File":{						
							 img_path = root+"components/com_guru/images/file.gif";
							 img_type = '<img src="'+img_path+'" alt="doc type"/>';								
							}
					break;													
				 
			}
			
			var name_string = '<a class="a_guru" target="_blank" href="<?php echo JURI::root(); ?>index.php?option=com_guru&view=guruauthor&task=editMedia&cid='+idu+'">'+name+'</a>'
			
			var yes_no = "";
			var publish = "";
			
			if(published==1){
				yes_no = "unpublish";
				publish = '<a id="g_publish'+idu+'" style="cursor: pointer;" class="icon-ok" title="Publish Item" onclick="javascript:publishUn(\''+idu+'\')"></a>';
			}
			else{
				yes_no = "publish";
				publish = '<a id="g_publish'+idu+'" style="cursor: pointer;" class="icon-remove" title="Unpublish Item" onclick="javascript:publishUn(\''+idu+'\')"></a>';
			}
			
			var cb = '<input type="checkbox" onclick="isChecked(this.checked);" value="'+idu+'" name="cid[]" id="cb'+(i)+'">';
			var value = "cb"+i;
	
			
			var access = '<select class="g_choosen_select" name="access'+idu+'">';
				access += '<option value="0">Students</option>';
				access += '<option value="1">Members</option>';
				access += '<option value="2">Guests</option>';
				access += '</select>';
			
			var img_path = root+"components/com_guru/images/delete2.gif";	
			var delete_string = '<img src="'+img_path+'" alt="delete" onclick="deleteMedia(\''+idu+'\');"/>';
			
			var poz = parseInt(i+"")+1;		

			mycelltwo.innerHTML=poz+"";
			mycellthree.innerHTML=img_type+" "+name_string;
			mycellfive.innerHTML=publish;
			mycellseven.innerHTML=access;
			mycelleight.innerHTML=delete_string;
			i++;
			window.parent.document.getElementById("media_number").value = i;
			//if joomla <= 3.8 means that it will include modal.js script witch generate sbox-window with modal
		    if(window.parent.document.getElementById("close-window")){
		        window.parent.document.getElementById("close-window").click();
		    }
		    //if joomla > 3.8 means that it will not include modal.js anymore and will use boostrap modal
		    else{
		        window.parent.jQuery('#GuruModal').modal('toggle');
		    }
			return true;
		}
		else{
			//if joomla <= 3.8 means that it will include modal.js script witch generate sbox-window with modal
		    if(document.getElementById('close-window')){
		        window.parent.document.getElementById("close-window").click();
		    }
		    //if joomla > 3.8 means that it will not include modal.js anymore and will use boostrap modal
		    else{
		        window.parent.jQuery('#GuruModal').modal('toggle');
		    }
			return true;
		}	
	}
	function publishUn(i){
		var url = 'components/com_guru/js/ajaxExercices.php?id='+i;
		var myAjax = jQuery.ajax({
			url: url,
			method: 'get',
			data: { 'do' : '1' },
			success: function(data) {
				if(data == 'publish'){
					element_id = "g_publish"+i;
					document.getElementById(element_id).className = "icon-ok";
				}
				else if(data == 'unpublish'){
					element_id = "g_publish"+i;
					document.getElementById(element_id).className = "icon-remove";
				}
			},
		})	
		return true;	
	}	
</script>

<div style="float: left; width: 100%;">
	<div class="alert alert-info">
		<?php echo JText::_("GURU_ADD_DOCUMENT"); ?>
		<br />
		<span style="color: red"><?php echo JText::_("GURU_ADD_DOCUMENT_TYPE"); ?></span>
	</div>
</div>

<br /><br />

<div>
    <form name="form1" action="<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addexercise&tmpl=component&cid=<?php echo $data_get['cid'];?>" method="post">
       <div class="filter-search btn-group span7">
            <div class="input-group g_search">
                    <input type="text" name="search_text" value="<?php if(isset($data_post['search_text'])) echo $data_post['search_text'];?>" />
                    <input class="btn btn-primary" style="margin-top:-10px;" type="submit" name="submit_search" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" />
            </div> 
                   
             <div class="btn-group g_margin_left">
			 	<?php echo JText::_("GURU_TASKS_MEDIATYPE"); ?>
                <select name="media_select" onChange="document.form1.submit()">
                    <option value="all" <?php if ( (!isset($data_post['media_select'])) || (isset($data_post['media_select']) && $data_post['media_select'] == 'all') ) echo ' selected="selected" ';?>><?php echo JText::_("GURU_ALL"); ?></option>
                    <option value="docs" <?php if (isset($data_post['media_select']) && $data_post['media_select'] == 'docs') echo ' selected="selected" ';?>><?php echo JText::_("GURU_MEDIATYPEDOCS"); ?></option>
                    <option value="file" <?php if (isset($data_post['media_select']) && $data_post['media_select'] == 'file') echo ' selected="selected" ';?>><?php echo JText::_("GURU_FILE"); ?></option>
                </select>
            </div>
      </div>
    </form>
</div>
<br />
<div>
<div id="editcell">
 <div class="addexercises_row_guru clearfix ">
    <div class="addexercises_row_guru g_cell span12">
         <div class="g_table_wrap">
            <div class="g_table clearfix" id="g_addexerc_list_table">  
            
                <div class="g_table_row">
                    <div class="g_cell span2 g_table_cell g_th pull-left g_hide_mobile">
                        <div>
                            <div>
                                <?php echo JText::_('GURU_ID'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="g_cell span3 g_table_cell g_th pull-left">
                        <div>
                            <div>
                                <?php echo JText::_('GURU_NAME'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="g_cell span3 g_table_cell g_th pull-left">
                        <div>
                            <div>
                                <?php echo JText::_('GURU_TYPE'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="g_cell span2 g_table_cell g_th pull-left">
                        <div>
                            <div>
                                <?php echo JText::_('GURU_PUBLISHED'); ?>
                            </div>
                        </div>
                    </div>
               </div> 
               <?php 
                $cmp = intval($data_get['cid']);
                if($n>0) { 
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
                                    case "text": $type = "Text";	    						
                                        break;
                                    case "file": $type = "File";	    						
                                        break;
                                    case "quiz": $type = "Quiz";	    						
                                        break;
                                }
                    $id = $file->id;
                    $link = "addexercise('".$id."', '".$type."', '".addslashes($file->name)."', '".$file->published."', '".$data_get['cid']."', '".JURI::root()."')";
                    
              ?> 
               <div class="g_table_row">
                    <div class="g_cell span2 g_table_cell pull-left  g_hide_mobile">
                        <div>
                            <div>
                                <?php echo $file->id;?>                                     
                            </div>
                        </div>
                    </div>
                     <div class="g_cell span3 g_table_cell pull-left">
                        <div>
                            <div>
                                <a class="a_guru" onclick="<?php echo $link; ?>" href="#"><?php echo $file->name; ?></a>
                            </div>
                        </div>
                    </div>
                     <div class="g_cell span3 g_table_cell pull-left">
                        <div>
                            <div>
                                <?php echo $type;?>
                            </div>
                        </div>
                    </div>
                     <div class="g_cell span2 g_table_cell pull-left">
                        <div>
                            <div>
                                 <?php
									if($file->published ==1){
										echo JText::_('GURU_PUBLISHED'); 
									}
									else{
										echo JText::_('GURU_UNPUBLISHED'); 
									}
								  ?>        
                            </div>
                        </div>
                    </div>
               </div>         
            <?php endfor;
             } ?>   
	</div>
</div>