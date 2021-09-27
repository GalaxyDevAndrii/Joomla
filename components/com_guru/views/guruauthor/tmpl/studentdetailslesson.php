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
$all_lessons = $this->all_lessons;
$n 		= count ($ads);
$pid = JFactory::getApplication()->input->get("pid", "0");
$config = $this->getConfigs();
$document = JFactory::getDocument();

$userid = JFactory::getApplication()->input->get("userid", "0");
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<form action="<?php echo JUri::root() ?>index.php" id="adminForm" name="adminForm" method="post">

<button onclick="window.location.href='<?php echo JURI::root(); ?>index.php?option=com_guru&view=guruauthor&layout=studentdetails&userid=<?php echo intval($userid); ?>&tmpl=component'; return false;" class="btn btn-primary pull-right"><?php echo JText::_("GURU_BACK"); ?></button>

<div class="g_row">
	<div class="g_cell span12">
		<div>
			<div>
                <div id="g_course_tree" class="clearfix com-cont-wrap">
                   
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
 
 <?php
	if(isset($ads[0]->id)){
		$ProgramName = $this->getProgramName($ads[0]->id);
	}
	else{
		$progr = $this->getProgram($pid);
		$ProgramName = $progr['name'];
	}
?>

	<span style="margin-top: -10px; color: black; font-family:Arial,Helvetica,sans-serif; font-size:25px;">
    	<b><?php echo $ProgramName; ?></b>
	</span>
 
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
                    $style = "block";
                    $registry->set('saved_new', "0");
                } 
			?>

               
                    <div class="tree tree-selectable" id="tree1">
                        <ul id="dhtmlgoodies_tree2" class="tree-folder-header dhtmlgoodies_tree">
                            <li style="list-style-type:none;" id="node0" noDrag="true" noSiblings="true" noDelete="true" noRename="true">
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
                    
                                    <li  style="background:url(components/com_guru/images/join_dot.png) repeat-y" isLeaf="false" LeafId="<?php echo $id; ?>" id="node<?php echo $rank; ?>">
                                        <img border="0" src="<?php echo JURI::base()."components/com_guru/images/module-tn.gif"; ?>" alt="alt" />
                                       	<?php echo $day_title; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php
                                            if($config["0"]["back_size_type"] == "1"){
                                                $link_addlesson = JURI::root().'index.php?option=com_guru&controller=guruAuthor&task=editsbox&tmpl=component&day='.$id.'&progrid='.$pid.'&cid=0';
                                        ?>
                                        <?php
                                            }
                                            else{
                                                $link_addlesson = JURI::root().'index.php?option=com_guru&controller=guruAuthor&task=editsbox&tmpl=component&day='.$id.'&progrid='.$pid.'&cid=';
                                        ?>
                                        		
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
                                                
                                                $task_link = JUri::root() . "index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsbox&cid=".$aTask->id;
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
                                                <li isLeaf="true" LeafId="<?php echo $aTask->id; ?>" id="node<?php echo $new_rank; ?>">
                                                <?php if($nr == count($idTasksForDay)){?>
                                                        <img src="components/com_guru/images/joinbottom.png" alt="" border="0" />
                                                <?php }
                                                      else{?>
                                                        <img alt="" src="components/com_guru/images/join.png" border="0" />
                                                <?php }
                                                        if(isset($layout_nr) && ($layout_nr<13) && ($layout_nr>0)){
                                                        echo '<img border="0" src="'.JURI::base().'components/com_guru/images/screen-'.$layout_nr.'-tn.gif" alt="layout" />';
                                                    } 
                                                    
                    
                                                    if($image_assoc!=''){
                                                ?>
                                                        <img border="0" src="<?php echo JURI::base()."components/com_guru/images/".$image_assoc; ?>" />
                                                <?php 
                                                    }
												?>
                                                <?php
													$opacity = "1";
													if($aTask->published == 0){
														$opacity = "0.5";
													}
												
													$icon = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
													if(in_array($aTask->id, $all_lessons)){
														$icon = '<i class="fa fa-eye"></i>&nbsp;&nbsp;';
													}
												?>
                                                		<script type="text/javascript">
                                                            width = window.screen.availWidth - 120;
															height = window.screen.availHeight - 180;
															document.write('<?php echo $icon.addslashes($aTask->name); ?>');
                                                        </script>
                                                <?php
                                                    
													$lesson_poz ++;
                                                ?>
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
                </div> 
			</div> 
		</div> 
	</div> 
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