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

jimport ('joomla.application.component.controller');
JHTML::_('behavior.modal');
JHtml::_('behavior.framework');
$doc = JFactory::getDocument();

class guruAdminControllerguruMedia extends guruAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listMedia");
		$this->_model = $this->getModel("guruMedia");
		$this->registerTask ("unpublish", "publish");
		$this->registerTask ("mass", "mass");
		$this->registerTask ("save_mass", "saveMass");
		$this->registerTask ("list_of_modules", "listOfModules");
		$this->registerTask ("change_teacher", "changeTeacher");
		$this->registerTask ("ajaxSearchMedia", "ajaxSearchMedia");
		$this->registerTask ("change_category", "changeCategory");
	}

	function listMedia(){
		$view = $this->getView("guruMedia", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function edit(){
		$redirect_to= JFactory::getApplication()->input->get('redirect_to');
		$type		= JFactory::getApplication()->input->get('type',"");
		if(isset($redirect_to)) {
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('temp_type', $type);
			$msg=NULL;
			$this->setRedirect($redirect_to, $msg);			
		}	
		$view = $this->getView("guruMedia", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->editForm();
	}
	
	function mass(){
		$view = $this->getView("guruMedia", "html");
		$view->setLayout("mass");
		$view->setModel($this->_model, true);
		$view->mass();
	}
	
	function editsboxx(){
		$view = $this->getView("guruMedia", "html");
		$view->setLayout("editformsboxx");
		$view->setModel($this->_model, true);
		$view->editForm();
	}	
	
	function changes(){
		$view = $this->getView("guruMedia", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->editForm();
	}
	
	function preview(){
		$view = $this->getView("guruMedia", "html");
		$view->setLayout("preview");
		$view->setModel($this->_model, true);
		$view->preview();
	}	
	
	function previewdocs(){
		$view = $this->getView("guruMedia", "html");
		$view->setLayout("previewdocs");
		$view->setModel($this->_model, true);
		$view->preview();
	}		

	function save(){
		if($id=$this->_model->store()){
			$msg = JText::_('GURU_MEDIASAVED');
		} 
		else{
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$isempty = $registry->get('isempty', "");
			$nosize = $registry->get('nosize', "");
		
			if($isempty == 1){
				$msg = JText::_('GURU_MEDIASAVEDFAILED_TEXTEMPTY');
				$registry->set('isempty', "");
			}
			else{
				$msg = JText::_('GURU_MEDIASAVEDFAILED');
				
				if($nosize == 0){
					$msg = $msg.JText::_('GURU_MEDIASAVEDSIZE');
					$n='warning';
				}
				
				$registry->set('nosize', "");
			}	
		}
		$link = "index.php?option=com_guru&controller=guruMedia";
		$this->setRedirect($link, $msg, $n);
	}

	function save2new(){
		$id = 0;

		if($this->_model->store()){
			$msg = JText::_('GURU_MEDIAAPPLY');
		} 
		else{
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$isempty = $registry->get('isempty', "");
			$nosize = $registry->get('nosize', "");
			
			if($isempty == 1){
				$msg = JText::_('GURU_MEDIASAVEDFAILED_TEXTEMPTY');
				$registry->set('isempty', "");
			}
			else{
				$msg = JText::_('GURU_MEDIAAPPLYFAILED');
				if($nosize == 0){
					$msg = $msg.JText::_('GURU_MEDIASAVEDSIZE');
					$n='warning';
				}
				$registry->set('nosize', "");
			}	
		}
		
		$link = "index.php?option=com_guru&controller=guruMedia&task=edit&cid[]=".intval($id);

		$this->setRedirect($link, $msg,$n);
	}
	
	function savesbox(){
		JFactory::getApplication()->input->set ("tmpl", "component");
		$id				= JFactory::getApplication()->input->get("id","0");
		$mediatext		= JFactory::getApplication()->input->get('mediatext','');
		$mediatextvalue	= JFactory::getApplication()->input->get('mediatextvalue','');
		$screen			= JFactory::getApplication()->input->get('screen','');
		
		$action = JFactory::getApplication()->input->get("action", "");
		
		if($action == "addtext" || $action == "addmedia"){
			$screen = "1";
		}		
		
		if($id==0){
			if((($mediatext!="") && ($mediatextvalue!="") && ($screen!="")) || ($screen=="0")){				
				if($id=$this->_model->store()){
					?>
					<script type="text/javascript" src="<?php echo JURI::root().'media/system/js/mootools.js' ?>"></script>
								
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
								
						function loadprototipe(){
							//WE ARE NOT LONGER BEEN USING AJAX FROM prototype-1.6.0.2.js, INSTEAD WE WILL BE USING jQuery.ajax({}) function
							//loadjscssfile("<?php echo JURI::base().'components/com_guru/views/gurutasks/tmpl/prototype-1.6.0.2.js' ?>","js");
						//alert('testing');
						}
								
						function addmedia (idu, name, description) {
						<?php if($screen != "0"){ ?>
							<?php
								if($mediatext=='med'){
									echo "var url = 'index.php?option=com_guru&controller=guruTasks&tmpl=component&format=raw&task=ajax_request2&id='+idu;";
								}
								else{
									echo "var url = 'index.php?option=com_guru&controller=guruTasks&tmpl=component&format=raw&task=ajax_request3&id='+idu;";
								}
							?>
						
							jQuery.ajax({ url: url,
								method: 'get',
								asynchronous: 'true',
								success: function(transport) {
									replace_m = <?php echo $mediatextvalue;?>;
									to_be_replaced = parent.document.getElementById('<?php if($mediatext=='med') { echo "media";} else { echo "text";} ?>_'+replace_m);
									to_be_replaced.innerHTML = '&nbsp;';
									if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {
										to_be_replaced.innerHTML += transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>'; 
									} 
									else {
										to_be_replaced.innerHTML += transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><br /><div  style="text-align:center"><i>' + description + '</i></div>';
									}
									
									parent.document.getElementById('before_menu_<?php if($mediatext=='med') { echo "med";} else { echo "txt";} ?>_'+replace_m).style.display = 'none';
									parent.document.getElementById('after_menu_<?php if($mediatext=='med') { echo "med";} else { echo "txt";} ?>_'+replace_m).style.display = '';
									parent.document.getElementById('db_<?php if($mediatext=='med') { echo "media";} else { echo "text";} ?>_'+replace_m).value = idu;
									
									screen_id = <?php echo $screen; ?>;
									replace_edit_link = parent.document.getElementById('a_edit_<?php if($mediatext=='med') { echo "media";} else { echo "text";} ?>_'+replace_m);
									replace_edit_link.href = 'index.php?option=com_guru&controller=guruMedia&tmpl=component&task=editsboxx&cid[]='+ idu +'&scr=' + screen_id;
									if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {
										var qwe='&nbsp;'+transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>';
									} 
									else {
										var qwe='&nbsp;'+transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
									}
									//window.parent.<?php if($mediatext=='med') { echo "";} else { echo "tx";} ?>test(replace_m, idu,qwe);
									window.parent.<?php if($mediatext=='med') { echo "";} else { echo "tx";} ?>test(replace_m, idu, '<span class="success-add-media"><?php echo JText::_("GURU_SUCCESSFULLY_ADED_MEDIA"); ?></span><br /><span class="muted"><?php echo JText::_("GURU_SAVE_TO_PREVIEW_MEDIA"); ?></span>');
								},
							});
						<?php } 
						else { 
						// for adding sound ?>						
							var url = 'index.php?option=com_guru&controller=guruTasks&tmpl=component&format=raw&task=ajax_request2&id='+idu;
							
							jQuery.ajax({ url: url,
								method: 'get',
								asynchronous: 'true',
								success: function(transport) {
									replace_m = "99";
									to_be_replaced = parent.document.getElementById('media_'+replace_m);
									to_be_replaced.innerHTML = '&nbsp;';
									if(replace_m!=99){
										if((transport.trim().match(/(.*?).pdf(.*?)/))&&(!transport.trim().match(/(.*?).iframe(.*?)/))){
											to_be_replaced.innerHTML += transport.trim()+'<p /><div  style="text-align:center"><i>' + description + '</i></div>'; 
										}
										else{
											to_be_replaced.innerHTML += transport.trim()+'<br /><div style="text-align:center"><i>'+ name +'</i></div><br /><div  style="text-align:center"><i>' + description + '</i></div>';
										}
									}
									else{
										to_be_replaced.innerHTML += transport.trim();
										parent.document.getElementById("media_"+99).style.display="";
										parent.document.getElementById("description_med_99").innerHTML=''+name;
									}
									parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
									parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
									parent.document.getElementById('db_media_'+replace_m).value = idu;
									
									screen_id = document.getElementById('the_screen_id').value;
									replace_edit_link = parent.document.getElementById('a_edit_media_'+replace_m);
									replace_edit_link.href = 'index.php?option=com_guru&controller=guruMedia&tmpl=component&task=editsboxx&cid[]='+ idu+"&scr="+replace_m;
									if ((transport.trim().match(/(.*?).pdf(.*?)/))&&(!transport.trim().match(/(.*?).iframe(.*?)/))) {
										var qwe='&nbsp;'+transport.trim()+'<p /><div  style="text-align:center"><i>' + description + '</i></div>';
									} 
									else {
										var qwe='&nbsp;'+transport.trim()+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
									}
									//window.parent.test(replace_m, idu,qwe);
									window.parent.test(replace_m, idu, '<span class="success-add-media"><?php echo JText::_("GURU_SUCCESSFULLY_ADED_MEDIA"); ?></span><br /><span class="muted"><?php echo JText::_("GURU_SAVE_TO_PREVIEW_MEDIA"); ?></span>');
								},
							});
						<?php }?>
							//window.parent.close_modal();
							setTimeout('window.parent.document.getElementById("close").click()',1000);
							//window.parent.SqueezeBox.close();						
							return true;						
						}				
					</script>
                    					
					<?php
					
					$current_media = $this->_model->getMediaInfo($id);
					
					if($screen=="0"){
						echo '<script type="text/javascript">window.onload=function(){
						loadprototipe();
						var t=setTimeout(\'addmedia('.$id.', "'.addslashes(trim($current_media->name)).'", "-", "");\',1000);						
						}</script>';
					}
					else{
						echo '<script type="text/javascript">window.onload=function(){
						loadprototipe();
						var t=setTimeout(\'addmedia('.$id.', "'.addslashes(trim($current_media->name)).'", "'.$current_media->instructions.'");\',1000);						
						}</script>'; 
					}
					
							
					echo '<strong>Media saved. Please wait...</strong>';
				}
			}		
		} 
		else{
			if($id = $this->_model->store()){
				$msg = JText::_('GURU_MEDIASAVED');
			} 
			else{
				$msg = JText::_('GURU_MEDIASAVEDFAILED');
			}

			echo '<script type="text/javascript">window.onload=function(){
				window.parent.page_refresh('.$screen.');
				var t=setTimeout(\'window.parent.SqueezeBox.close();\',0);
			}</script>';
			echo '<strong>Media saved. Please wait...</strong>';
		}
	}	
	
	function apply(){
		$id = JFactory::getApplication()->input->get("id","0");
		if($this->_model->store()){
			$msg = JText::_('GURU_MEDIAAPPLY');
		} 
		else{
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$isempty = $registry->get('isempty', "");
			$nosize = $registry->get('nosize', "");
			
			if($isempty == 1){
				$msg = JText::_('GURU_MEDIASAVEDFAILED_TEXTEMPTY');
				$registry->set('isempty', "");
			}
			else{
				$msg = JText::_('GURU_MEDIAAPPLYFAILED');
				if($nosize == 0){
					$msg = $msg.JText::_('GURU_MEDIASAVEDSIZE');
					$n='warning';
				}
				$registry->set('nosize', "");
			}	
		}
		
		if($id!=0){
			$link = "index.php?option=com_guru&controller=guruMedia&task=edit&cid[]=".intval($id);
		} 
		else{
			$last_media = $this->_model->last_media();
			$link = "index.php?option=com_guru&controller=guruMedia&task=edit&cid[]=".$last_media;
		}
		$this->setRedirect($link, $msg,$n);
	}

	function cancel(){
	 	$msg = JText::_('GURU_MEDIACANCEL');
		$link = "index.php?option=com_guru&controller=guruMedia";
		$this->setRedirect($link, $msg);
	}
	
	function publish(){
		$res = $this->_model->publish();
		$type = "";
		$task = JFactory::getApplication()->input->get('task', "");
		
		if ($res == -1){
			$msg = JText::_('GURU_MEDIAACTERR');
			$type = "error";
		}
		elseif($res == -2){
			$msg = JText::_('GURU_MEDIA_ASSIGNED_TO_LESSON');
			$type = "error";
		}
		elseif($res == 1){
			if($task == 'publish'){
				$msg = JText::_('GURU_MEDIAPUB');
			}
			else{
				$msg = JText::_('GURU_MEDIAUNPUB');
			}
		}
		$link = "index.php?option=com_guru&controller=guruMedia";
		$this->setRedirect($link, $msg, $type);
	}
	
	function remove(){
		if(!$this->_model->delete()){
			$msg = JText::_('GURU_MEDIAREMERR');
		}
		else{
		 	$msg = JText::_('GURU_MEDIAREMSUCC');
		}
		$link = "index.php?option=com_guru&controller=guruMedia";
		$this->setRedirect($link, $msg);
	}	
	
	function duplicate(){ 
		$res = $this->_model->duplicate();
		if ($res == 1){
			$msg = JText::_('GURU_MEDIA_DUPLICATE_SUCC');
		}
		else{
			$msg = JText::_('GURU_MEDIA_DUPLICATE_ERR');
		}
			
		$link = "index.php?option=com_guru&controller=guruMedia";
		$this->setRedirect($link, $msg);
	}
	
	function saveMass(){
		$res = $this->_model->saveMass();
		$type = "";
		
		if($res === TRUE){
			$msg = JText::_('GURU_MASS_SAVED_SUCC');
			$type = "message";
		}
		else{
			$msg = JText::_('GURU_MASS_SAVED_ERR');
			$type = "notice";
		}
		
		$category_id = JFactory::getApplication()->input->get("category_id", "0");
		$course_id = JFactory::getApplication()->input->get("course_id", "0");
		$link = "index.php?option=com_guru&controller=guruMedia&task=mass&action=next&category_id=".intval($category_id)."&course_id=".intval($course_id);
		$this->setRedirect($link, $msg, $type);
	}
	
	function listOfModules(){
		$course_id = JFactory::getApplication()->input->get("course_id", "0");
		$db = JFactory::getDBO();
		$sql = "select id, title from #__guru_days where pid=".intval($course_id);
		$db->setQuery($sql);
		$db->execute();
		$modules = $db->loadAssocList();
		
		$return  = '<select name="module_id" id="module_id">
                		<option value="0">'.JText::_("GURU_SELECT_MODULE").'</option>';
		if(isset($modules) && count($modules) > 0){
			foreach($modules as $key=>$module){
				$return .= '<option value="'.intval($module["id"]).'">'.$module["title"].'</option>';
			}
		}
		$return .= '</select>';
		
		echo $return;
		die();
	}
	
	function changeTeacher(){
		$msg = "";
		
		if(!$this->_model->changeTeacher()){
			$msg = JText::_('GURU_CHANGED_TEACHER_UNSUCCESSFULLY');
		} 
		else{
		 	$msg = JText::_('GURU_CHANGED_TEACHER_SUCCESSFULLY');
		}
		$link = "index.php?option=com_guru&controller=guruMedia";

		$this->setRedirect($link, $msg);
	}

	function changeCategory(){
		$msg = "";
		
		if(!$this->_model->changeCategory()){
			$msg = JText::_('GURU_CHANGED_CATEGORY_UNSUCCESSFULLY');
		} 
		else{
		 	$msg = JText::_('GURU_CHANGED_CATEGORY_SUCCESSFULLY');
		}
		
		$link = "index.php?option=com_guru&controller=guruMedia";

		$this->setRedirect($link, $msg);
	}
	
	function ajaxSearchMedia(){
		$this->_model->ajaxSearchMedia();
		die();
	}
};

?>