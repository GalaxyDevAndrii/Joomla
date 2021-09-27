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

class guruAdminControllerguruProjects extends guruAdminController{
	var $_model = null;
	
	function __construct(){
		parent::__construct();
		$this->registerTask ("", "listProjects");
		$this->registerTask ("new", "editProject");
		$this->registerTask ("edit", "editProject");
		$this->registerTask ("resultProject", "resultProject");
		$this->registerTask ("getTeacherCoursesSelect", "getTeacherCoursesSelect");
		$this->registerTask ("apply", "apply");
		$this->registerTask ("save", "save");
		$this->registerTask ("remove", "remove");
		$this->registerTask ("saveResults", "saveResults");

		$this->_model = $this->getModel("guruProjects");
	}

	/**
	* project list
	*
	*/
	function listProjects(){
		$view = $this->getView("guruProjects", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function editProject(){
		$view = $this->getView("guruProjects", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->edit();
	}

	function resultProject(){
		$view = $this->getView("guruProjects", "html");
		$view->setLayout("results");
		$view->setModel($this->_model, true);
		$view->results();
	}

	function getTeacherCoursesSelect(){
		$model = $this->getModel("guruProjects");
		$model->getTeacherCoursesSelect();
		die();
	}

	function apply(){
		$model = $this->getModel("guruProjects");
		$result = $model->save();

		if(!$result["error"]){
			$msg = JText::_('GURU_PROJECT_SAVED_OK');
			$this->setRedirect('index.php?option=com_guru&controller=guruProjects&task=edit&cid[]='.intval($result["id"]), $msg);
		}
		else{
			$msg = JText::_('GURU_PROJECT_SAVED_ERROR');
			$this->setRedirect('index.php?option=com_guru&controller=guruProjects&task=edit&cid[]='.intval($result["id"]), $msg);
		}
	}

	function save(){
		$model = $this->getModel("guruProjects");
		$result = $model->save();

		$save_from_lesson = JFactory::getApplication()->input->get("save_from_lesson", "0", "raw");

		if($save_from_lesson == 1){
	?>
			<style>
				.contentpane.modal{
					display: block !important;
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
						
				function loadprototipe(){
					//WE ARE NOT LONGER BEEN USING AJAX FROM prototype-1.6.0.2.js, INSTEAD WE WILL BE USING jQuery.ajax({}) function
					//loadjscssfile("<?php echo JURI::base().'components/com_guru/views/guruauthor/tmpl/prototype-1.6.0.2.js' ?>","js");
				}

				function addproject (idu, name, asoc_file, description) {
					loadprototipe();
					
					var url = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=add_media_ajax&id='+idu+'&type=project';

					jQuery.ajax({
							url : url,
							cache: false
					})
					.done(function(transport) {
						replace_m = '16';
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
						replace_edit_link.href = 'index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editProject&cid='+ idu;
						
						if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {
							var qwe='&nbsp;'+transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>';
						} else {
							var qwe='&nbsp;'+transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
						}
						
						window.parent.test(replace_m, idu, transport);
					});
					setTimeout('window.parent.document.getElementById("close").click()',1000);
					return true;
				}
			</script>
	<?php

			echo '<div style="padding:15px;"><strong>'.JText::_("GURU_PROJECT_SAVED_WAIT").'</strong></div>';

			echo '<script type="text/javascript">
					window.onload=function(){
						var t=setTimeout(\'addproject('.$result["id"].', "'.$result["title"].'", "-", "");\', 1000);						
					}
				</script>';
			return false;
		}

		if(!$result["error"]){
			$msg = JText::_('GURU_PROJECT_SAVED_OK');
			$this->setRedirect('index.php?option=com_guru&controller=guruProjects', $msg);
		}
		else{
			$msg = JText::_('GURU_PROJECT_SAVED_ERROR');
			$this->setRedirect('index.php?option=com_guru&controller=guruProjects', $msg);
		}
	}

	function cancel(){
		$app = JFactory::getApplication();
		$app->redirect("index.php?option=com_guru&controller=guruProjects");
	}

	function remove(){
		$model = $this->getModel("guruProjects");
		$result = $model->remove();

		if(!$result["error"]){
			$msg = JText::_('GURU_PROJECT_REMOVED_OK');
			$this->setRedirect('index.php?option=com_guru&controller=guruProjects', $msg);
		}
		else{
			$msg = JText::_('GURU_PROJECT_REMOVED_ERROR');
			$this->setRedirect('index.php?option=com_guru&controller=guruProjects', $msg);
		}
	}

	function saveResults(){
		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;
		$project_id = $jinput->post->get('id');
		$ids = $jinput->post->get('ids');
		$scores = $jinput->post->get('scores');
		$a = 0 ;

		require_once(JPATH_SITE.'/components/com_guru/tables/guruprojectresults.php');
        $TableguruProjectResult =  new TableguruProjectResult();
        
        // save the result
		foreach ($ids as $id) {
			$score = $scores[$a];
			$TableguruProjectResult =  new TableguruProjectResult();
        	$TableguruProjectResult->load(array('id'=>$id));
        	$TableguruProjectResult->score = $score;
        	$TableguruProjectResult->store();
			$a++;
		}

		$msg = JText::_('GURU_PROJECT_RESULT_SAVED');
		$this->setRedirect('index.php?option=com_guru&controller=guruProjects&task=resultProject&id='.intval($project_id), $msg);
	}

};

?>