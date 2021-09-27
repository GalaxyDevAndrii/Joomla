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

	$k		= 0;
	$ads 	= $this->ads;
	$n 		= count ($ads);
	$filter = $this->filters;
	$pid = JFactory::getApplication()->input->get("pid", "0");
	$config = $this->getConfigs();
	$dimensions_backend = $config["0"]["lesson_window_size_back"];
	$width = "715";
	$height = "1150";
	if(trim($dimensions_backend) != ""){
		$temp = explode("x", $dimensions_backend);
		$height = $temp["0"];
		$width = $temp["1"];
	}
	
	$document = JFactory::getDocument();
	include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'gurudays'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'course_modal.php');
	$document->addStyleSheet(JURI::base()."components/com_guru/css/g_admin_modal.css");
	$course_modal= guruAdminCourseModal();
	
	$data_get = JFactory::getApplication()->input->get->getArray();

	if(intval($pid) > 0){
		echo '<a href="'.JURI::root().'index.php?option=com_guru&view=guruPrograms&task=view&cid='.intval($pid).'&preview=true" target="_blank" class="btn btn-success">'.JText::_("GURU_PREVIEW_COURSE").'</a> <br /><br />';
	}
?>

<script type="text/javascript">
	function dom_refresh() { 
			window.parent.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); });
			window.parent.addEvent('domready', function() {
	
				SqueezeBox.initialize({});
	
				$$('a.modal').each(function(el) {
					el.addEvent('click', function(e) {
						new Event(e).stop();
						SqueezeBox.fromElement(el);
					});
				});
			});
	}
	
	function showContent(href){
		jQuery( '#myModal .modal-body iframe').attr('src', href);
		jQuery( '#myModal-sm .modal-body iframe').attr('src', href);
	}

	function changeAction(value){
		if(value != "descending" && value != "ascending"){
			cids = document.getElementsByName("cid[]");
			selected = false;
			for(i=0; i<cids.length; i++){
				if(cids[i].checked){
					selected = true;
					break;
				}
			}
			
			if(!selected){
				alert("<?php echo JText::_("GURU_NO_LESSON_SELECTED"); ?>");
				return false;
			}
		}
		
		if(value != 0){
			msg = "";
			if(value == "delete"){
				msg = '<?php echo addslashes(JText::_("GURU_SURE_DELETE_LESSONS")); ?>';
			}
			else if(value == "publish"){
				msg = '<?php echo addslashes(JText::_("GURU_SURE_PUBLISH_LESSONS")); ?>';
			}
			else if(value == "unpublish"){
				msg = '<?php echo addslashes(JText::_("GURU_SURE_UNPUBLISH_LESSONS")); ?>';
			}
			else if(value == "students"){
				msg = '<?php echo addslashes(JText::_("GURU_SURE_STUDENTS_LESSONS")); ?>';
			}
			else if(value == "members"){
				msg = '<?php echo addslashes(JText::_("GURU_SURE_MEMBERS_LESSONS")); ?>';
			}
			else if(value == "guests"){
				msg = '<?php echo addslashes(JText::_("GURU_SURE_GUESTS_LESSONS")); ?>';
			}
			else if(value == "descending"){
				msg = '<?php echo addslashes(JText::_("GURU_SURE_DESCENDING_LESSONS")); ?>';
			}
			else if(value == "ascending"){
				msg = '<?php echo addslashes(JText::_("GURU_SURE_ASCENDING_LESSONS")); ?>';
			}
			
			if(confirm(msg)){
				document.getElementById("task").value = "action";
				document.adminForm.submit();
			}
			else{
				document.getElementById("task").value = "";
			}
		}
	}
	
</script>
<?php JHtmlBehavior::framework(); ?>
<div id="myModalVideo" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalVideoLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="javascript:closeModal();">x</button>
     </div>
     <div class="modal-body">
    </div>
</div>
<?php echo $course_modal; ?>

<form action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruDays&pid=<?php echo $pid; ?>" id="adminForm" name="adminForm" method="post">

<div align="left">	
    <table>
    	<tr>
            <td style="background-color:#ffffff;" align="right" colspan="11">
                <div id="zuzuzuzu"></div>
                <div id="cucu" style="display:none"></div>				
                <?php 
                    echo $filter->pid;
                ?>
            </td>
            
        	<td>
				&nbsp;&nbsp;&nbsp;
				<?php echo JText::_("GURU_ACTION"); ?>:
                &nbsp;
                <select name="action" onchange="javasctip:changeAction(this.value);">
                    <option value="0"><?php echo JText::_("GURU_SELECT_OPTION"); ?></option>
                    <optgroup label="<?php echo JText::_("GURU_ACTIONS"); ?>">
                        <option value="delete"><?php echo JText::_("GURU_DELETE_OPTION"); ?></option>
                        <option value="publish"><?php echo JText::_("GURU_PUBLISH_OPTION"); ?></option>
                        <option value="unpublish"><?php echo JText::_("GURU_UNPUBLISH_OPTION"); ?></option>
                    </optgroup>
                    <optgroup label="<?php echo JText::_("GURU_ACCESS"); ?>">
                        <option value="students"><?php echo JText::_("GURU_STUDENTS_ACCESS_OPTION"); ?></option>
                        <option value="members"><?php echo JText::_("GURU_MEMBERS_ACCESS_OPTION"); ?></option>
                        <option value="guests"><?php echo JText::_("GURU_GUESTS_ACCESS_OPTION"); ?></option>
                    </optgroup>
                    <optgroup label="<?php echo JText::_("GURU_ORDERING"); ?>">
                        <option value="descending"><?php echo JText::_("GURU_DESCENDING_OPTION"); ?></option>
                        <option value="ascending"><?php echo JText::_("GURU_ASCENDING_OPTION"); ?></option>
                    </optgroup>
                </select>
            </td>
        </tr>
	</table>
</div>
<table class="adminlist" width="100%">
	
<tbody>
<?php
if(isset($ads[0]->id)){
	$ProgramName = guruAdminModelguruDays::getProgramName($ads[0]->id);
} else {
	$progr = guruAdminModelguruDays::getProgram($pid);
	$ProgramName = $progr['name'];
}

?>
	<tr>
		<td class="noborder">

<?php 

$document->addScript(JURI::base()."components/com_guru/views/gurudays/tmpl/js/ajax.js");
$document->addScript(JURI::base()."components/com_guru/views/gurudays/tmpl/js/context-menu.js");
$document->addScript(JURI::base()."components/com_guru/views/gurudays/tmpl/js/drag-drop-folder-tree.js");
$document->addStyleSheet(JURI::base()."components/com_guru/views/gurudays/tmpl/css/drag-drop-folder-tree.css");
$document->addStyleSheet(JURI::base()."components/com_guru/views/gurudays/tmpl/css/context-menu.css");
 ?>	
	<style type="text/css">
	/* CSS for the demo */
	img{
		border:0px;
		
	}
	.modal-color-myModal1 .modal-backdrop {
      background-color: #f00!important;
	}
	</style>
	<script type="text/javascript">
	//--------------------------------
	// Save functions
	//--------------------------------
		
	jQuery('.modal[data-color]').on('show hidden', function(e) {
	  jQuery('body')
		.toggleClass('modal-color-' + jQuery(this).data('color'));
	});
	var ajaxObjects = new Array();

	function refreshTree()
	{
		var save_string = treeObj.getNodeOrders();
		treeObj.initTree(2, 1);
	}	

	function saveTheNewTree()
	{
			// hack LMS - using AJAX to save the new order
			var ajaxObjects = new Array();
			
			saveString = JSTreeObj.getNodeOrders();
			var ajaxIndex = ajaxObjects.length;
			ajaxObjects[ajaxIndex] = new sack();
			
			var url = 'components/com_guru/views/gurudays/tmpl/saveOrder.php?saveString=' + saveString;
			ajaxObjects[ajaxIndex].requestFile = url;	// Specifying which file to get
			
			ajaxObjects[ajaxIndex].onCompletion = function() 
					{ 
						
					} ;	// Specify function that will be executed after file has been found
			ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function				
	}
	
	function addNewScreen(day_id, screen_id)
	{
			var ajaxObjects = new Array();
			
			var ajaxIndex = ajaxObjects.length;
			ajaxObjects[ajaxIndex] = new sack();
			
			var url = 'components/com_guru/views/gurudays/tmpl/addScreen.php?day_id=' + day_id + '&screen_id=' + screen_id;
			ajaxObjects[ajaxIndex].requestFile = url;	// Specifying which file to get
			
			ajaxObjects[ajaxIndex].onCompletion = function() 
					{ 
					} ;	
			ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function				
	}	

	function saveMyTree(day_title, day_ordering, newdayid)
	{
		
			saveString = treeObj.getNodeOrders();
			var ajaxIndex = ajaxObjects.length;
			ajaxObjects[ajaxIndex] = new sack();
			
			var url = '<?php echo JURI::base()."components/com_guru/views/gurudays/tmpl/saveNodes.php?saveString=";?>' + saveString;
			ajaxObjects[ajaxIndex].requestFile = url;	// Specifying which file to get
			
			//ajaxObjects[ajaxIndex].onCompletion = function() { saveComplete(ajaxIndex); } ;	// Specify function that will be executed after file has been found
			ajaxObjects[ajaxIndex].onCompletion = function() 
					{ 
						treeObj._____addElement(day_title, day_ordering, newdayid) ; 
						saveTheNewTree();
						refreshTree();
						dom_refresh();
				
						/*treeObj.initTree(); */
					} ;	// Specify function that will be executed after file has been found
			ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function			
	}
	

	function GetNodesOrder()
	{
		var save_string = treeObj.getNodeOrders();
		//alert(save_string);
	}		
	
	function deleteFinalExam(course_id){
		var ajaxObjects = new Array();
		var ajaxIndex = ajaxObjects.length;
		
		ajaxObjects[ajaxIndex] = new sack();
		if(confirm ('<?php echo JText::_('GURU_SURE_DELETE_FINAL_EXAM'); ?>')){
			var url = 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=deleteFinalQuiz&course_id='+ course_id;
			ajaxObjects[ajaxIndex].requestFile = url;	// Specifying which file to get
			
			//ajaxObjects[ajaxIndex].onCompletion = function() { saveComplete(ajaxIndex); } ;	// Specify function that will be executed after file has been found
			ajaxObjects[ajaxIndex].onCompletion = function() 
					{ 
						document.getElementById("final-exam").innerHTML = "";
						
					} ;	// Specify function that will be executed after file has been found
			ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function		
		}
		else{
		}
	}
	
	function deleteScreen(group_id, screen_id, node_id)
	{
			// hack LMS - using AJAX to save the new order
			var ajaxObjects = new Array();
			
			//saveString = JSTreeObj.getNodeOrders();
			var ajaxIndex = ajaxObjects.length;
			ajaxObjects[ajaxIndex] = new sack();
			if(confirm ('<?php echo JText::_('GURU_SURE_DELETE_LESSON'); ?>')){
				node_screen = document.getElementById('node' + node_id);
				node_screen.style.opacity = '.15';

				var url = 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=deleteScreen&group='+ group_id +'&screen=' + screen_id;
				ajaxObjects[ajaxIndex].requestFile = url;	// Specifying which file to get
				
				//ajaxObjects[ajaxIndex].onCompletion = function() { saveComplete(ajaxIndex); } ;	// Specify function that will be executed after file has been found
				ajaxObjects[ajaxIndex].onCompletion = function() 
						{ 
							//treeObj._____addElement(day_title, day_ordering, newdayid) ; 
							//treeObj.initTree(); 
							node_screen = document.getElementById('node' + node_id);
							treeObj.__deleteOfScreen(node_screen) ; 	
							
							
						} ;	// Specify function that will be executed after file has been found
				ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function		
			}
			else{
			}		
	
	}	
	
	function deleteGroup(group_id, node_id)
	{
			// hack LMS - using AJAX to save the new order
			var ajaxObjects = new Array();
			
			//saveString = JSTreeObj.getNodeOrders();
			var ajaxIndex = ajaxObjects.length;
			ajaxObjects[ajaxIndex] = new sack();
			if(confirm ('<?php echo JText::_('GURU_SURE_DELETE_GROUP'); ?>')){
				node_screen = document.getElementById('node' + node_id);
				node_screen.style.opacity = '.15';

				var url = 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=deleteGroup&group='+group_id;
				ajaxObjects[ajaxIndex].requestFile = url;	// Specifying which file to get
				
				//ajaxObjects[ajaxIndex].onCompletion = function() { saveComplete(ajaxIndex); } ;	// Specify function that will be executed after file has been found
				ajaxObjects[ajaxIndex].onCompletion = function() 
						{ 
							//treeObj._____addElement(day_title, day_ordering, newdayid) ; 
							//treeObj.initTree(); 
							node_screen = document.getElementById('node' + node_id);
							treeObj.__deleteOfScreen(node_screen) ; 
							document.getElementById('guru_message_module').style.display = "block";
						} ;	// Specify function that will be executed after file has been found
				ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function	
		   }
		   else{
		   }
	}		

	// Call this function if you want to save it by a form.
	function saveMyTree_byForm()
	{
		document.myForm.elements['saveString'].value = treeObj.getNodeOrders();
		document.myForm.submit();		
	}
	function guruCheckBoxex(id_module, lesson_poz){
		for(i=1; i<(lesson_poz+1); i++){
			if(!document.getElementById('check_all_lessons'+id_module).checked){
				document.getElementById("lesson_"+id_module+"_"+i).checked = false;
			}
			else{
				document.getElementById("lesson_"+id_module+"_"+i).checked = true;
			}
		}
		
	}
	
	</script>
<?php    
$style = "none";

$session = JFactory::getSession();
$registry = $session->get('registry');
$saved_new = $registry->get('saved_new', "0");

if($saved_new == 1){
	$style = "block";
	$registry->set('saved_new', "0");
}

?>
<div style="display:none;" id="guru_message_module">
	<button data-dismiss="alert" class="close" type="button">×</button>
    <div class="alert alert-success">
        <h4 class="alert-heading">Message</h4>
        <p><?php echo JText::_("GURU_DAY_REMSUCC"); ?></p>
	</div>
</div>
<div style="display:<?php echo $style;?>;" id="guru_message_savemodule">
	<button data-dismiss="alert" class="close" type="button">×</button>
    <div class="alert alert-success">
        <h4 class="alert-heading">Message</h4>
        <p><?php echo JText::_("GURU_DAY_SAVE"); ?></p>
	</div>
</div>

<table width="100%">	
	<tr>
		<td class="noborder" width="70%" valign="top">
	<br>
    <div class="tree tree-selectable" id="tree1" pid="<?php echo $pid ?>">
        <ul id="dhtmlgoodies_tree2" class="tree-folder-header dhtmlgoodies_tree">
            <li style="list-style-type:none;" id="node0" noDrag="true" noSiblings="true" noDelete="true" noRename="true"><span style="margin-top: -10px; color: black; font-family:Arial,Helvetica,sans-serif; font-size:17px;"><b><?php echo $ProgramName; ?></b></span><a data-toggle="modal" data-target="#myModal-sm" onClick = "showContent('index.php?option=com_guru&controller=guruDays&component=com_guru&task=newmodule&tmpl=component&pid=<?php echo $pid; ?> ');" style="color:#0b55c4 !important; "  href="#"><?php echo JText::_("GURU_ADD_NEW_MODULE"); ?></a><br />
                <ul>
                    
                    <?php 
                    $rank = 1;
    
                    for ($i = 0; $i < $n; $i++) {
                        $ad =$ads[$i];
                        $id = $ad->id;					
                        $link = "index.php?option=com_guru&controller=guruDays&tmpl=component&task=edit&node=".$rank."&cid[]=".$id;
                        $day_title = $ad->title;
                        $idTasksForDay = guruAdminModelguruDays::getIDTasksForDay($id);
                        ?>
    
                    <li  style="background:url(components/com_guru/images/join_dot.png) repeat-y" class="tree-branch" isLeaf="false" LeafId="<?php echo $id; ?>" id="node<?php echo $rank; ?>">
                        <img border="0" src="<?php echo JURI::base()."components/com_guru/images/module-tn.gif"; ?>" alt="alt" />
                        <input type="checkbox" name="check_all_lessons" id="check_all_lessons<?php echo $id ;?>" onclick="guruCheckBoxex(<?php echo $id; ?>,<?php echo intval(count($idTasksForDay));?>);" value="<?php echo intval(@$aTask->id); ?>" />
                        
                        <span class="lbl"></span>
                        <a data-toggle="modal" data-target="#myModal-sm" onClick = "showContent('<?php echo $link;?> ');" href="#">
                              <?php echo $day_title; ?>
                        </a>&nbsp;
                        <img border="0" onClick="javascript:deleteGroup(<?php echo $id.','.$rank; ?>)" src="<?php echo JURI::base()."components/com_guru/images/delete.gif"; ?>" />&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php 
                            if($config["0"]["back_size_type"] == "1"){
                        ?>
                                <script type="text/javascript">
                                    document.write('<a data-toggle="modal" data-target="#myModal" onClick = "showContent(\'index.php?option=com_guru&controller=guruTasks&task=editsbox&tmpl=component&day=<?php echo $id;?>&progrid=<?php echo intval($data_get['pid']);?>&cid[]=0 \');" href="#" ><font color="#0B55C4"><?php echo JText::_('GURU_DAY_ADD_SCREEN2'); ?></font></a>');
                                </script>
                        <?php
                            }
                            else{
                        ?>
                                <script type="text/javascript">
                                    document.write('<a data-toggle="modal" data-target="#myModal" onClick = "showContent(\'index.php?option=com_guru&controller=guruTasks&task=editsbox&tmpl=component&day=<?php echo $id;?>&progrid=<?php echo intval($data_get['pid']);?>&cid[]=\');" href="#"><font color="#0B55C4"><?php echo JText::_('GURU_DAY_ADD_SCREEN2'); ?></font></a>');
                                </script>		
                        <?php
                            }
                        ?>
                    &nbsp;&nbsp;
            <?php	
                
                    $nr=0;
    
                    if(count($idTasksForDay)) {
                    ?>
                    <ul>
                    <?php
						$new_order = 1;
						$lesson_poz = 1;
                        foreach($idTasksForDay as $aIDTask) {
                            if($aIDTask>0){
                                $rank++;
                                $nr++;
                                $aTask = guruAdminModelguruDays::getTask($aIDTask);
                                $layout_nr=guruAdminModelguruDays::select_layout($aIDTask);
                                
                                $task_link = "index.php?option=com_guru&controller=guruTasks&tmpl=component&task=editsbox&cid[]=".$aTask->id;
                                $TaskType = guruAdminModelguruDays::getTaskType($aTask->id);
                                
                                
                                $image_assoc = '';
                            
                                if(isset($TaskType) && $TaskType == 'audio') $image_assoc = 'audio.gif';
                                if(isset($TaskType) && $TaskType == 'video') $image_assoc = 'video.gif';
                                if(isset($TaskType) && $TaskType == 'docs') $image_assoc = 'doc.gif';
                                if(isset($TaskType) && $TaskType == 'url') $image_assoc = 'url.gif';
                                if(isset($TaskType) && $TaskType == 'quiz') $image_assoc = 'quiz.gif';
                                if(isset($TaskType) && $TaskType == 'text') $image_assoc = 'text.gif';	
                                
                                if($aTask->final_lesson != 0){
                                    $new_rank=$rank+10;
                                }
                                else{
                                    $new_rank=$rank;
                                }
                                ?>
                                <li class="tree-leaf" isLeaf="true" LeafId="<?php echo $aTask->id; ?>" id="node<?php echo $new_rank; ?>">
                                <?php if($nr == count($idTasksForDay)){?>
                                        <img src="components/com_guru/images/joinbottom.png" alt="" border="0" />
                                <?php }
                                      else{?>
                                        <img alt="" src="components/com_guru/images/join.png" border="0" />
                                <?php }
                                        if(isset($layout_nr) && ($layout_nr<18) && ($layout_nr>0)){
                                        	echo '<img border="0" src="'.JURI::base().'components/com_guru/images/screen-'.$layout_nr.'-tn.gif" alt="layout" />';
                                    	} 
                                    
    
                                    if($image_assoc!=''){
                                ?>
                                        <img border="0" src="<?php echo JURI::base()."components/com_guru/images/".$image_assoc; ?>" />
                                <?php 
                                    }
								?>
                                    <input type="checkbox" name="cid[]" id="lesson_<?php echo $id."_".$lesson_poz; ?>" value="<?php echo intval($aTask->id); ?>" />
                                    <span class="lbl"></span>
								<?php
									$opacity = "1";
									if($aTask->published == 0){
										$opacity = "0.5";
									}
								
                                    if($config["0"]["back_size_type"] == "1"){ 
                                ?>
                                        <script type="text/javascript">
                                            document.write('<a  onClick = "showContent(\'<?php echo $task_link; ?>&progrid=<?php echo intval($_REQUEST['pid']);?>&module=<?php echo $id; ?>\');" data-toggle="modal" data-target="#myModal" id="nodeATag<?php echo $new_rank; ?>" href="#" style="opacity:<?php echo $opacity; ?>;"><?php echo addslashes($aTask->name); ?></a>');
                                        </script>
    
                                <?php
                                    }
                                    else{
                                ?>
                                        <script type="text/javascript">
                                            document.write('<a data-toggle="modal" data-target="#myModal" id="nodeATag<?php echo $new_rank; ?>"  onClick = "showContent(\'<?php echo $task_link; ?>&progrid=<?php echo intval($_REQUEST['pid']);?>&module=<?php echo $id; ?>\');" href="#" style="opacity:<?php echo $opacity; ?>;"><?php echo addslashes($aTask->name); ?></a>');								
                                        </script>
    
                                <?php	
                                    }
									$lesson_poz ++;
                                ?>	
                                        &nbsp;<img border="0" onClick="javascript:deleteScreen(<?php echo $id.','.intval($aTask->id).','.$new_rank; ?>)" src="<?php echo JURI::base()."components/com_guru/images/delete.gif"; ?>" />
                                        &nbsp;
                                        </li>
                                <?php
                            } 
                        }
                        ?>						
                        </ul>
                    
                <?php }
    
                    $rank++;
                    }
                    ?>
                </ul>
            </li>
        </ul>	
	</div>
		</td>
		<td class="noborder" align="right" valign="top">
        <div>
        	<div>
                <a data-toggle="modal" data-target="#myModalVideo" onClick = "showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=27181365&tmpl=component');" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                    <?php echo JText::_("GURU_ADDING_A_TABLE_CONTENT_VIDEO"); ?>                  
                </a>
            </div>
        	<div>
                <a id="close_gb" style="display:none;">#</a>
                <a data-toggle="modal" data-target="#myModalVideo" onClick = "showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=30058444&tmpl=component');" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                    <?php echo JText::_("GURU_ADDING_A_LESSON_VIDEO"); ?>                  
                </a>
            </div>
        </div>
		</td>
	</tr>
</table>
		

	<script type="text/javascript">	
		treeObj = new JSDragDropTree();
		treeObj.setTreeId('dhtmlgoodies_tree2');
		treeObj.setMaximumDepth(6);
		treeObj.setMessageMaximumDepthReached('<?php echo JText::_('GURU_DAY_ILLEGAL_JS');?>'); // If you want to show a message when maximum depth is reached, i.e. on drop.
		treeObj.initTree(1, 0);
	</script>
	
		</td>
	</tr>
	</tbody>
</table>
<script language="javascript">
	var first = false;
	function showContentVideo(href){
		first = true;
		jQuery.ajax({
		  url: href,
		  success: function(response){
		   jQuery( '#myModalVideo .modal-body').html(response);
		  }
		});
	}

	jQuery('#myModalVideo').on('hide', function () {
	 jQuery('#myModalVideo .modal-body').html('');
	});
	
	jQuery('#myModal').on('hide', function () {
		jQuery('#myModal .modal-body iframe').attr('src', '');
	});
	
	function closeModal(){
		jQuery('#myModalVideo .modal-body iframe').attr('src', '');
	}
	
	jQuery('body').click(function () {
		if(!first){
			jQuery('#myModalVideo .modal-body iframe').attr('src', '');
		}
		else{
			first = false;
		}
	});

</script>	

	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" id="task" value="" />
    <input type="hidden" name="boxchecked" id="boxchecked" value="0" />
    <input type="hidden" name="boxchecked_id" id="boxchecked_id" value="" />
    <input type="hidden" name="controller" value="guruDays" />	
</form>


<div class="loading-ordering" style="display: none;">
	<img src="<?php echo JURI::root()."/administrator/components/com_guru/images/loading.gif"; ?>" />
</div>