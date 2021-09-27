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
// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.html.pagination' );
JHTML::_('behavior.tooltip');
JHTML::_('behavior.framework');
JHtml::_('behavior.calendar');

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
$document->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_AUTHOR_MY_COURSE'))." ".trim(JText::_('GURU_TREE')) );
include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'guruauthor'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'course_modal.php');
$course_modal= guruAdminCourseModal();

//$document->addScript('components/com_guru/js/guru_modal.js');
JHtml::stylesheet('components/com_guru/css/tabs.css');

$allow_teacher_action = json_decode($config["0"]["st_authorpage"]);
$teacher_edit_courses = $allow_teacher_action->teacher_edit_courses;

if(intval($pid) > 0 && $teacher_edit_courses == 1){
	$app = JFactory::getApplication();
	$app->redirect(JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses"));
	return true;
}

?>

<div id="zuzuzuzu"></div>
<div id="cucu" style="display:none"></div>

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
				msg = '<?php echo JText::_("GURU_SURE_DELETE_LESSONS"); ?>';
			}
			else if(value == "publish"){
				msg = '<?php echo JText::_("GURU_SURE_PUBLISH_LESSONS"); ?>';
			}
			else if(value == "unpublish"){
				msg = '<?php echo JText::_("GURU_SURE_UNPUBLISH_LESSONS"); ?>';
			}
			else if(value == "students"){
				msg = '<?php echo JText::_("GURU_SURE_STUDENTS_LESSONS"); ?>';
			}
			else if(value == "members"){
				msg = '<?php echo JText::_("GURU_SURE_MEMBERS_LESSONS"); ?>';
			}
			else if(value == "guests"){
				msg = '<?php echo JText::_("GURU_SURE_GUESTS_LESSONS"); ?>';
			}
			else if(value == "descending"){
				msg = '<?php echo JText::_("GURU_SURE_DESCENDING_LESSONS"); ?>';
			}
			else if(value == "ascending"){
				msg = '<?php echo JText::_("GURU_SURE_ASCENDING_LESSONS"); ?>';
			}
			
			if(confirm(msg)){
				document.getElementById("task").value = "action";
				document.adminForm.submit();
			}
			else{
				document.getElementById("task").value = "treeCourse";
			}
		}
	}
</script>
<style>
	.guru-content .btn-default, 
	.guru-content .btn {
		line-height:10px!important;
	}
</style>

<div id="g_authortreecourse" class="clearfix com-cont-wrap">

<form action="<?php echo JUri::root(true) ?>/index.php?option=com_guru&controller=guruAuthor&pid=<?php echo $pid; ?>" id="adminForm" name="adminForm" method="post">
	<?php 
		$div_menu = $this->authorGuruMenuBar();
		echo $div_menu;
		JHtmlBehavior::framework();
	?>
	<?php
    	echo $course_modal;
		if(isset($ads[0]->id)){
			$ProgramName = $this->getProgramName($ads[0]->id);
		}
		else{
			$progr = $this->getProgram($pid);
			$ProgramName = $progr['name'];
		}
	?>
	<div class="uk-grid uk-margin wk-grid wk-margin">
		<div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2"><h2 class="gru-page-title"><?php echo $ProgramName; ?></h2></div>
	    
	    <div class="uk-width-1-2 uk-text-right uk-hidden-small wk-width-1-2 wk-text-right wk-hidden-small">
		    <div class="uk-button-group wk-button-group">
				<input type="button" class="uk-button uk-button-success wk-button wk-button-success" onclick="history.back();" value="<?php echo JText::_("GURU_BACK_ARROW"); ?>" />
				<input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" onclick="javascript:openMyModal(0, 0, '<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&component=com_guru&task=newmodule&tmpl=component&pid=<?php echo $pid; ?>'); return false;" href="#" value="<?php echo JText::_("GURU_ADD_NEW_MODULE"); ?>" />
		    	<input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" onclick="window.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=addCourse&id=".intval($pid)); ?>';" value="<?php echo JText::_("GURU_EDIT_COURSE_INFO"); ?>" />
		    </div>
	    </div>

	    <div class="uk-width-1-2 uk-text-right uk-visible-small wk-width-1-2 wk-text-right wk-visible-small">
		    <div class="uk-button-group wk-button-group">
				<input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" onclick="history.back();" value="<?php echo JText::_("GURU_BACK_ARROW"); ?>" />
				<input type="button" class="uk-button uk-button-primary uk-width-1-1 wk-button wk-button-primary wk-width-1-1" onclick="javascript:openMyModal(0, 0, '<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&component=com_guru&task=newmodule&tmpl=component&pid=<?php echo $pid; ?>'); return false;" href="#" value="<?php echo JText::_("GURU_ADD_NEW_MODULE"); ?>" />
		    	<input type="button" class="uk-button uk-button-primary uk-width-1-1 wk-button wk-button-primary wk-width-1-1" onclick="window.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=addCourse&id=".intval($pid)); ?>';" value="<?php echo JText::_("GURU_EDIT_COURSE_INFO"); ?>" />
		    </div>
	    </div>
    </div>

    <hr class="uk-divider wk-divider">

    <a href="<?php echo JURI::root().'index.php?option=com_guru&view=guruPrograms&task=view&cid='.intval($pid).'&preview=true'; ?>" target="_blank" class="uk-button uk-button-success wk-button wk-button-success"><?php echo JText::_("GURU_PREVIEW_COURSE"); ?></a>

    <div class="uk-clearfix uk-margin wk-clearfix wk-margin">
    	<span class="uk-text-bold wk-text-bold"><?php echo JText::_("GURU_ACTION_TO_MORE_LESSONS"); ?></span>
    </div>
    
    <div class="uk-clearfix wk-clearfix">
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
    </div>


	<script type="text/javascript" language="javascript">
		var site_url = '<?php echo JURI::root(); ?>';
	</script>
						
	<?php
	$document->addScript(JURI::base()."components/com_guru/views/guruauthor/tmpl/js/ajax.js");
	$document->addScript(JURI::base()."components/com_guru/views/guruauthor/tmpl/js/context-menu.js");
	$document->addScript(JURI::base()."components/com_guru/views/guruauthor/tmpl/js/drag-drop-folder-tree.js");
	$document->addStyleSheet(JURI::base()."components/com_guru/views/guruauthor/tmpl/css/drag-drop-folder-tree.css");
	$document->addStyleSheet(JURI::base()."components/com_guru/views/guruauthor/tmpl/css/context-menu.css");
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
				var url = '<?php echo JURI::root();?>components/com_guru/views/guruauthor/tmpl/saveOrder.php?saveString=' + saveString;
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
				
				var url = '<?php echo JURI::root();?>components/com_guru/views/guruauthor/tmpl/addScreen.php?day_id=' + day_id + '&screen_id=' + screen_id;
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
				
				var url = '<?php echo JURI::base()."components/com_guru/views/guruauthor/tmpl/saveNodes.php?saveString=";?>' + saveString;
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
				var url = '<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=delete_final_quizz_ajax&course_id='+ course_id;
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
					var url = '<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=delete_screen_ajax&group='+ group_id +'&screen=' + screen_id;
					ajaxObjects[ajaxIndex].requestFile = url;	// Specifying which file to get
					
					//ajaxObjects[ajaxIndex].onCompletion = function() { saveComplete(ajaxIndex); } ;	// Specify function that will be executed after file has been found
					ajaxObjects[ajaxIndex].onCompletion = function() 
							{ 
								//treeObj._____addElement(day_title, day_ordering, newdayid) ; 
								//treeObj.initTree(); 
								node_screen = document.getElementById('node' + node_id);
								treeObj.__deleteOfScreen(node_screen) ; 	
								
								
							} ;	// Specify function that will be executed after file has been found
					ajaxObjects[ajaxIndex].runAJAX(); // Execute AJAX function
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
					var url = '<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=delete_group_ajax&group='+ group_id ;
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
		function guruCheckBoxex(id_module, lesson_poz, poz){
			for(i=1; i<(lesson_poz+1); i++){
				if(!document.getElementById('check_all_lessons_'+poz+'_'+id_module).checked){
					document.getElementById("lesson_"+id_module+"_"+i).checked = false;
				}
				else{
					document.getElementById("lesson_"+id_module+"_"+i).checked = true;
				}
			}
			
		}
		</script>
        
        <?php    
					$style="none";
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$saved_new = $registry->get('saved_new', "");
					
					if(isset($saved_new) && $saved_new == 1){
						$style="block";
						$registry->set('saved_new', "");
					} 
					
					?>
	
					<div style="display:none;" id="guru_message_module">
						<button data-dismiss="alert" class="close" type="button">x</button>
						<div class="alert alert-success">
							<h4 class="alert-heading">Message</h4>
							<p><?php echo JText::_("GURU_DAY_REMSUCC"); ?></p>
						</div>
					</div>
                    
					<div style="display:<?php echo $style;?>;" id="guru_message_savemodule">
						<button data-dismiss="alert" class="close" type="button">x</button>
						<div class="alert alert-success">
							<h4 class="alert-heading">Message</h4>
							<p><?php echo JText::_("GURU_DAY_SAVE"); ?></p>
						</div>
					</div>
					
					
						<div class="tree tree-selectable" id="tree1" pid="<?php echo $pid ?>">
							<ul id="dhtmlgoodies_tree2" class="tree-folder-header dhtmlgoodies_tree">
								<li style="list-style-type:none;" id="node0">
									<ul>
										<?php 
										$rank = 1;
						
										for ($i = 0; $i < $n; $i++) {
											$ad =$ads[$i];
											$id = $ad->id;					
											$link = JUri::root() . "index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=edit&node=".$rank."&cid=".$id;
											$day_title = $ad->title;
											$idTasksForDay = $this->getIDTasksForDay($id);
											?>
						
										<li  style="background:url(components/com_guru/images/join_dot.png) repeat-y" class="tree-branch" isLeaf="false" LeafId="<?php echo $id; ?>" id="node<?php echo $rank; ?>">
											<img border="0" src="<?php echo JURI::base()."components/com_guru/images/module-tn.gif"; ?>" alt="alt" />
										   <input type="checkbox" name="check_all_lessons" id="check_all_lessons_<?php echo $i."_".$id ;?>" onclick="guruCheckBoxex(<?php echo $id; ?>,<?php echo intval(count($idTasksForDay));?>, <?php echo $i; ?>);" value="<?php echo intval(@$aTask->id); ?>" />
											<a onclick="javascript:openMyModal(0, 0, '<?php echo $link;?>'); return false;" href="#">
												<?php echo $day_title; ?>
											</a>&nbsp;
											<img border="0" onClick="javascript:deleteGroup(<?php echo $id.','.$rank; ?>)" src="<?php echo JURI::base()."components/com_guru/images/delete.gif"; ?>" />&nbsp;&nbsp;&nbsp;&nbsp;
											<?php
												if($config["0"]["back_size_type"] == "1"){
													$link_addlesson = JURI::root().'index.php?option=com_guru&controller=guruAuthor&task=editsbox&tmpl=component&day='.$id.'&progrid='.$pid.'&cid=0';
											?>
													<a onclick="javascript:openMyModal(0, 0, '<?php echo $link_addlesson;?>'); return false;" href="#">
														<font color="#0B55C4"><?php echo JText::_('GURU_DAY_ADD_SCREEN2'); ?></font>
													</a>
											<?php
												}
												else{
													$link_addlesson = JURI::root().'index.php?option=com_guru&controller=guruAuthor&task=editsbox&tmpl=component&day='.$id.'&progrid='.$pid.'&cid=';
											?>
													<a onclick="javascript:openMyModal(0, 0, '<?php echo $link_addlesson;?>'); return false;" href="#">
														<font color="#0B55C4"><?php echo JText::_('GURU_DAY_ADD_SCREEN2'); ?></font>
													</a>
											<?php
												}
											?>
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
													$aTask = $this->getTask($aIDTask);
													$layout_nr=$this->select_layout($aIDTask);
													
													$task_link = "index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsbox&cid=".$aTask->id;
													$TaskType = $this->getTaskType($aTask->id);
													
													
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
													<?php
														$opacity = "1";
														if($aTask->published == 0){
															$opacity = "0.5";
														}
													
														if($config["0"]["back_size_type"] == "1"){ 
													?>
															<script type="text/javascript">
																width = window.screen.availWidth - 120;
																height = window.screen.availHeight - 180;
																document.write('<a onclick="javascript:openMyModal(0, 0, \'<?php echo JURI::root();?><?php echo $task_link; ?>&progrid=<?php echo intval($_REQUEST['pid']);?>&module=<?php echo $id; ?>\'); return false;" href="#" id="nodeATag<?php echo $new_rank; ?>" style="opacity:<?php echo $opacity; ?>;"><?php echo addslashes($aTask->name); ?></a>');
															</script>
						
													<?php
														}
														else
														{
													?>
															<script type="text/javascript">
																width = window.screen.availWidth - 120;
																height = window.screen.availHeight - 180;
																document.write('<a onclick="javascript:openMyModal(0, 0, \'<?php echo JURI::root();?><?php echo $task_link; ?>&progrid=<?php echo intval($_REQUEST['pid']);?>&module=<?php echo $id; ?>\'); return false;" href="#" id="nodeATag<?php echo $new_rank; ?>" style="opacity:<?php echo $opacity; ?>;"><?php echo addslashes($aTask->name); ?></a>');								
															</script>
						
													<?php	
														}
														$lesson_poz ++;
													?>	
															&nbsp;<img border="0" onClick="javascript:deleteScreen(<?php echo $id.','.intval($aTask->id).','.$new_rank; ?>)" src="<?php echo JURI::base()."components/com_guru/images/delete.gif"; ?>" /></li>
													<?php
												} 
											}
											?>						
											</ul>
									<?php } ?>
									
										</li>
									<?php
                                    	$rank++;
										}
										?>
									</ul>
								</li>
							</ul>	
						</div>
					
		<script type="text/javascript">	
			treeObj = new JSDragDropTree();
			treeObj.setTreeId('dhtmlgoodies_tree2');
			treeObj.setMaximumDepth(6);
			treeObj.setMessageMaximumDepthReached('<?php echo JText::_('GURU_DAY_ILLEGAL_JS');?>'); // If you want to show a message when maximum depth is reached, i.e. on drop.
			treeObj.initTree(1, 0);
		</script>
	
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
		<input type="hidden" name="task" id="task" value="treeCourse" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
		<input type="hidden" name="boxchecked_id" id="boxchecked_id" value="" />
		<input type="hidden" name="controller" value="guruAuthor" />	
    </form>
</div>