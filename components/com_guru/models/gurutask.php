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
jimport ("joomla.aplication.component.model");

require_once(JPATH_BASE . "/components/com_guru/helpers/Mobile_Detect.php");

class guruModelguruTask extends JModelLegacy {
	var $_attributes;
	var $_attribute;
	var $_id = null;
	var $_module = null;

	function __construct () {
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");

		if(intval($cids) == 0){
            $cids = JFactory::getApplication()->input->get('cid_req', 0, "raw");
        }
        
		$this->setId((int)$cids);
		
		$module = JFactory::getApplication()->input->get('module', '0', "raw");
		$this->setModule($module);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_attribute = null;
	}

	function setModule($module) {
		$this->_module = $module;
	}
	
	function view () {
		$my= JFactory::getUser();	
	}

	function getProgresBarSettings(){
		$db = JFactory::getDBO();
		$sql = "select progress_bar, st_donecolor, st_notdonecolor, st_txtcolor, st_width, st_height from #__guru_config";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getLessonOrder($course_id){
		$db = JFactory::getDBO();
		$sql = "select id from #__guru_task WHERE id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (SELECT id FROM #__guru_days WHERE pid =".intval($course_id).")) order by ordering asc";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		
		return $result;
	}
	
	function getLessonJumpOrder($course_id, $less_id){
		$db = JFactory::getDbo();
		$return = 0;
		
		$sql = "SELECT id FROM #__guru_days WHERE pid=".intval($course_id);
		$db->setQuery($sql);
		$days = $db->loadColumn();
		
		if(isset($days) && count($days) > 0){
			$sql = "SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (".implode(",", $days).")";
			$db->setQuery($sql);
			$media_ids = $db->loadColumn();
			
			if(isset($media_ids) && count($media_ids) > 0){
				$sql = "select t.id from #__guru_task t where t.id in (".implode(",", $media_ids).") ORDER BY t.ordering ASC";
				$db->setQuery($sql);
				$ids = $db->loadColumn();
				
				if(isset($ids) && count($ids) > 0){
					foreach($ids as $key=>$value){
						if(intval($value) == intval($less_id)){
							return $key+1;
						}
					}
				}
			}
		}
		
		return $return;
	}
	
	function getAllSteps($module_id){
		$db = JFactory::getDBO();
		$jnow 	= new JDate('now');
		$date 	= $jnow->toSQL(true);
		
		$sql = "select t.id, t.name, t.step_access 
				from #__guru_task t, #__guru_mediarel m 
				where m.type_id = ".intval($module_id)." and m.type='dtask' and m.media_id=t.id
				AND t.startpublish <= '".$date."'
				AND (t.endpublish = '0000-00-00 00:00:00' OR t.endpublish >= '".$date."') 
				order by ordering asc";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}

	function getJumpStep($id){
		$db = JFactory::getDBO();
		$sql = "select jump_step, module_id1, type_selected from #__guru_jump where id = ".intval($id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}

	function getlistTask ($dayid, $dayord) {
			$my = JFactory::getUser();
			$database = JFactory::getDBO();
			$sql = "SELECT tasks FROM #__guru_programstatus WHERE userid=".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id=".$dayid." )";
			$database->setQuery($sql);
			$task_object = $database->loadResult();
			
			$task_object = explode(';', $task_object);
			$task_array = $task_object[$dayord-1];
			
			$task_ids_in = '';
			$task_array = explode('-', $task_array);
			foreach($task_array as $task)
				{
					$the_task = explode(',', $task);
					if ($the_task[0])
						$task_ids_in = $task_ids_in.$the_task[0].',';
				}
				
			$task_ids_in = substr($task_ids_in, 0, strlen($task_ids_in)-1);
			$task_ids_in = explode(',', $task_ids_in);
			return $task_ids_in;
	}	
	
	function getMainMediaForTask($taskid){
		$database = JFactory::getDBO();
			
		$sql = "SELECT * FROM #__guru_media
					WHERE id = (SELECT media_id FROM #__guru_mediarel WHERE mainmedia='1' AND type_id = ".$taskid.") "; 
		$database->setQuery($sql);
		$media = $database->loadObject();
		return $media;	
	}
	
	function getAllModules($pid){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_days where pid=".intval($pid)." order by ordering asc";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getNextModule($pid, $module_id){
		$db = JFactory::getDBO();
		$sql = "select id from #__guru_days where pid = ".intval($pid)." order by ordering asc";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		$return = "0";
		
		if(isset($result) && is_array($result) && count($result) > 0){
			foreach($result as $key=>$value){
				if(intval($value) == intval($module_id)){
					if(isset($result[$key + 1])){
						$return = $result[$key + 1];
					}
				}
			}
		}
		return $return;
	}	
	
	function getPrevModule($pid, $module_id){
		$db = JFactory::getDBO();
		$sql = "select id from #__guru_days where pid = ".intval($pid)." order by ordering asc";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		$return = "0";
		
		if(isset($result) && is_array($result) && count($result) > 0){
			foreach($result as $key=>$value){
				if(intval($value) == intval($module_id)){
					if(isset($result[$key - 1])){
						$return = $result[$key - 1];
					}
				}
			}
		}
		return $return;
	}	
	
	function editModuleOnFront(){
		$db = JFactory::getDBO();
		$attribs = array();
		$id = intval(JFactory::getApplication()->input->get("module", "", "raw"));
		$attribs["id"] = $id;
		$sql = "select * from #__guru_days where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		$attribs["name"] = $result["0"]["title"];
		$attribs["alias"] = $result["0"]["alias"];
		$attribs["category"] = "";
		$attribs["difficultylevel"] = "";
		$attribs["points"] = "";
		$attribs["image"] = "";
		$attribs["published"] = $result["0"]["published"];
		$attribs["startpublish"] = $result["0"]["startpublish"];
		$attribs["endpublish"] = $result["0"]["endpublish"];
		$attribs["metatitle"] = $result["0"]["metatitle"];
		$attribs["metakwd"] = $result["0"]["metakwd"];
		$attribs["metadesc"] = $result["0"]["metadesc"];
		$attribs["time"] = 0;
		$attribs["ordering"] = $result["0"]["ordering"];
		$attribs["step_access"] = $result["0"]["access"];
		$attribs["layout"] = 6;
		$attribs["next_module"] = $id;
		$attribs["prev_module"] = $id;
		$attribs["layout_text"] = array();
		
		if(trim($result["0"]["media_id"]) != ""){
			$attribs["layout_media"]["0"] = $this->parse_media(intval($result["0"]["media_id"]), 0);
		}
		else{
			$attribs["layout_media"]["0"] = array();
		}
		$attribs["layout_jump"] = array();
		
		//preview----------------
		$stop = false;
		while(!$stop){
			$prev_module = $this->getPrevModule($result["0"]["pid"], $id);
						
			if($prev_module == "0"){
				$stop = true;
			}
			else{
				$cid_array = $this->getAllSteps($prev_module);
				if(count($cid_array) > 0){
					$stop = true;
					$current_module = $prev_module;
					$attribs["prev_module"] = $prev_module;
					$attribs["prevs"] = $cid_array[count($cid_array)-1]["id"];
				}
				else{
					$current_module = $prev_module;
					$attribs["prev_module"] = $prev_module;
				}
			}
		}		
		//preview----------------
		
		//next-------------------
		$stop = false;
		$next_id = "0";
		$next_id_access = "0";
		
		while(!$stop){
			$cid_array = $this->getAllSteps($id);
			
			if(count($cid_array) > 0){
				$next_id = $cid_array["0"]["id"];
				$next_id_access = $cid_array["0"]["step_access"];
				$attribs["next_module"] = $id;
				$stop = true;
			}
			else{
				if(isset($next_module)){
					$current_module = $next_module;
				}
				$stop = true;
			}
		}
		
		if(!isset($next_module) || $next_module === NULL){
		
		}
		//next-------------------
		
    	$attribs["nexts"] = $next_id;
    	$attribs["nextaccess"] = $next_id_access;
    	$attribs["pid"] = $result["0"]["pid"];



		
		return $attribs;
	}
	
	function getTask(){
		$action = JFactory::getApplication()->input->get("action", "", "raw");
		$jnow 	= new JDate('now');
		$date 	= $jnow->toSQL(true);
		
		$date = date("Y-m-d", strtotime($date))." 23:59:59";
		
		if($action != ""){
			$attribs = $this->editModuleOnFront();
			return (object)$attribs;
		}
		
		$my = JFactory::getUser();
		$db = JFactory::getDBO();

		if(empty ($this->_attributes)){
			$sql = "SELECT lt.*, lm.media_id as layout
					FROM #__guru_task AS lt
					LEFT JOIN #__guru_mediarel as lm 
					ON lt.id=lm.type_id
					WHERE lt.id = ".intval($this->_id)." AND lt.published = 1 AND  lt.startpublish <='".$date."' AND (lt.endpublish >='".$date."' OR lt.endpublish = '0000-00-00 00:00:00' )
					 AND type='scr_l' ";
			$this->_attributes = $this->_getList($sql);		
			
			// if the course whom the lesson belong to is set as free for guests,
			// then the lesson is free for guests
			$sql = "SELECT p.chb_free_courses, p.step_access_courses, p.selected_course
				FROM #__guru_program p				
				LEFT JOIN #__guru_days d
				ON d.pid = p.id
				where d.id=".intval(JFactory::getApplication()->input->get("module", "0", "raw"));
			
			$this->_attributes[0]->course_details = $this->_getList($sql);
			$this->_attributes[0]->course_details = $this->_attributes[0]->course_details[0];
			
			$is_course_free_for_guests = false;
			if($this->_attributes[0]->course_details->chb_free_courses == 1 && $this->_attributes[0]->course_details->step_access_courses == 2) { 
				$is_course_free_for_guests = true;
				$this->_attributes[0]->step_access = 2;
			}
		}

		$attribs=$this->_attributes[0];
		$attribs->module=$this->_module;		
		$attribs->prev_module = $this->_module;
		
		//start get text, media 
		$attribs->layout_text=array();
		$attribs->layout_media=array();
		$attribs->layout_jump=array();
		
		if($attribs->layout != ""){
			$sql="SELECT type, lm.media_id as media_id, lm.layout
				 FROM #__guru_mediarel as lm
				 WHERE type_id=".intval($this->_id)." 
				 AND (type='scr_t' or type='scr_m') and layout=".$attribs->layout." order by mainmedia asc, text_no asc";
		
			$result=$this->_getList($sql);
			
			for($i=0;$i<count($result);$i++){
				if($result[$i]->type=="scr_t"){
					$attribs->layout_text[]=$this->parse_txt(intval($result[$i]->media_id));
				}
				else if($result[$i]->type == "scr_m" && $result[$i]->layout != "12" && $result[$i]->layout != "16" && $result[$i]->layout != "17"){
					$sql = "select * from #__guru_media where id=".intval($result[$i]->media_id);
					$db->setQuery($sql);
					$media_details = $db->loadObject();
					
					if($media_details->type == "video" && $media_details->source == "url"){
						$configs = $this->getConfig();
						$video_size = $configs->default_video_size;
						
						if(trim($video_size) != "" && $media_details->option_video_size == 0){
							$temp = explode("x", trim($video_size));
							$media_details->width = $temp["1"];
							$media_details->height = $temp["0"];
						}
						
						if($media_details->width==0){
							$media_details->width=400;
						}
						
						require_once(JPATH_ROOT .'/components/com_guru/helpers/videos/helper.php');
						$parsedVideoLink = parse_url($media_details->url);
						preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $parsedVideoLink['host'], $matches);
						$domain	= $matches['domain'];

						if (!empty($domain)){
							$provider		= explode('.', $domain);
							$providerName	= strtolower($provider[0]);
							
							if($providerName == "youtu"){
								$providerName = "youtube";
							}
							
							$libraryPath = JPATH_ROOT .'/components/com_guru/helpers/videos' .'/'. $providerName . '.php';
							
							if(file_exists($libraryPath)){
								require_once($libraryPath);
								$className		= 'PTableVideo' . ucfirst($providerName);
								$videoObj		= new $className();
								$videoObj->init($media_details->url);
								$video_id		= $videoObj->getId();
								
								if($providerName == "youtube" || $providerName == "vimeo" || $providerName == "dailymotion"){
									$video_id = $video_id."?autoplay=".$media_details->auto_play;
								}

								// start hide video info and related videos
								/*if(strpos(" ".$video_id, "?") !== FALSE){
									$video_id .= "&showinfo=0&rel=0";
								}
								else{
									$video_id .= "?showinfo=0&rel=0";
								}*/
								// stop hide video info and related videos
								
								$videoPlayer	= $videoObj->getViewHTML($video_id, $media_details->width, $media_details->height);
								$videoPlayer = preg_replace('/width="(.*)"/msU', 'width="100%"', $videoPlayer);
								
								//$videoPlayer = str_replace('<iframe', '<iframe allownetworking="internal"', $videoPlayer);
								//$videoPlayer = str_replace('autoplay=0', 'autoplay=0&amp;rel=0&amp;showinfo=0&amp;allownetworking=internal&amp;wmode=transparent&amp;modestbranding=1', $videoPlayer);
								//http://www.youtube.com/embed/KQtR2dx61Y0?wmode=opaque&amp;rel=0&amp;autohide=1&amp;showinfo=0&amp;wmode=transparent&amp;modestbranding=1
								$attribs->layout_media[] = $videoPlayer;
							}
							else{
								$attribs->layout_media[] = $this->parse_media(intval($result[$i]->media_id), $attribs->layout);
							}
						}
					}
					else{
						$attribs->layout_media[] = $this->parse_media(intval($result[$i]->media_id), $attribs->layout);
					}
				}
				elseif($result[$i]->type=="scr_m" && $result[$i]->layout=="12"){
					$attribs->layout_media[] = $this->parse_media(intval($result[$i]->media_id), $attribs->layout);
				}
				elseif($result[$i]->type=="scr_m" && $result[$i]->layout == "16"){
					$attribs->layout_media[] = $this->parse_media(intval($result[$i]->media_id), $attribs->layout);
					@$attribs->project_id = intval($result[$i]->media_id);

					$user = JFactory::getUser();
					$db = JFactory::getDbo();
					$sql = "select `file` from #__guru_project_results where `student_id`=".intval($user->id)." and `project_id`=".intval($result[$i]->media_id)." and `lesson_id`=".intval($attribs->id);
					$db->setQuery($sql);
					$uploaded_file = $db->loadColumn();
					$uploaded_file = @$uploaded_file["0"];

					@$attribs->uploaded_file = $uploaded_file;
				}
				elseif($result[$i]->type=="scr_m" && $result[$i]->layout == "17"){
					$attribs->layout_media[] = $this->parse_media(intval($result[$i]->media_id), $attribs->layout);
					@$attribs->scorm_id = intval($result[$i]->media_id);
				}
			}
		}

		if(!isset($attribs->layout_text[0]))
			$attribs->layout_text[0]="";
		if(!isset($attribs->layout_text[1]))
			$attribs->layout_text[1]="";
		if(!isset($attribs->layout_media[0]))
			$attribs->layout_media[0]="";
		if(!isset($attribs->layout_media[1]))
			$attribs->layout_media[1]="";
		
		$sql = "select media_id from #__guru_mediarel where type_id=".intval($this->_id)." and layout=99";
		$db->setQuery($sql);
		$audio_id = $db->loadResult();
		if(isset($audio_id)){
			$attribs->audio = $this->parse_media($audio_id);
		}
		
		$sql="SELECT lm.media_id as jump, lj.text
			 FROM #__guru_mediarel as lm
			 LEFT JOIN #__guru_jump as lj
			 ON lm.media_id=lj.id
			 WHERE type_id=".intval($this->_id)." 
			 AND type='jump' order by lj.button asc ";
			 
		$attribs->layout_jump=$this->_getList($sql);

		if($attribs->step_access==2 || ($attribs->step_access<2 && $my->id>0)){		
			$sql="SELECT distinct(lt.id), ordering, step_access
				FROM #__guru_task lt
				LEFT JOIN #__guru_mediarel lm
				ON lt.id=lm.media_id
				WHERE lm.type_id=".intval($this->_module)."
				and type='dtask' 
				AND lt.startpublish <= '".$date."' 
				AND (lt.endpublish = '0000-00-00 00:00:00' OR lt.endpublish >= '".$date."')
				ORDER BY ordering";
				
			$db->setQuery($sql);
			$result=$db->loadObjectList();
			
			
			for($i=0;$i<count($result);$i++){
				if($result[$i]->id == intval($this->_id) && $i==0){
					$attribs->prevs = 0;
					if(isset($result[$i+1])){
						$attribs->nexts	= $result[$i+1]->id;
						$attribs->nextaccess = $is_course_free_for_guests ? 2 : @$result[$i+1]->step_access;
					}
				}
				elseif($result[$i]->id==intval($this->_id) && $i==count($result)-1){
					$attribs->prevs	= @$result[$i-1]->id;
					$attribs->prevaccess = $is_course_free_for_guests ? 2 : @$result[$i-1]->step_access;
					$attribs->nexts	= 0;
				}
				elseif($result[$i]->id==intval($this->_id)){
					$attribs->prevs = @$result[$i-1]->id;
					$attribs->nexts	= @$result[$i+1]->id;
					$attribs->prevaccess = $is_course_free_for_guests ? 2 : @$result[$i-1]->step_access;
					$attribs->nextaccess = $is_course_free_for_guests ? 2 : @$result[$i+1]->step_access;
				}
			}		
			//start get the program/course
			$sql = "SELECT pid
					FROM #__guru_days
					WHERE id=".intval($attribs->module);

			$db->setQuery($sql);
			$result=$db->loadObject();
			$attribs->pid = $result->pid;
			if(!isset($attribs->nexts) || $attribs->nexts == "0"){
				$next_module = $this->getNextModule($attribs->pid, $this->_module);				
				$attribs->next_module = $next_module;
			}
			else{
				$attribs->next_module = $this->_module;
			}
			
			return $attribs;
		}
		else{
			return false;
		}
			
	}
	
	public static function parse_txt ($id){
		$db = JFactory::getDBO();
		
		$q = "SELECT * FROM #__guru_config WHERE id = '1' ";
		$db->setQuery( $q );
		$configs = $db->loadObject();
				
		$q  = "SELECT * FROM #__guru_media WHERE id = ".$id;
		$db->setQuery( $q );
		$result = $db->loadObject();	
		
		$the_media = $result;
		
		if($the_media->type=='text')
		{
			if($the_media->show_instruction ==2){
				@$media .= $the_media->code; 
			}
			elseif($the_media->show_instruction ==1){
				@$media .= $the_media->code; 
			} 
			elseif($the_media->show_instruction ==0){
				@$media .= $the_media->code; 
			}
			
		}
		
		if($the_media->type=='docs')
		{
			$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
			$the_base_link = $the_base_link[0];
			//$media = 'The selected element is a text file that can\'t have a preview';
			$media = JText::_("GURU_NO_PREVIEW");
			
			if($the_media->source == 'local' && (substr($the_media->local,(strlen($the_media->local)-3),3) == 'txt' || substr($the_media->local,(strlen($the_media->local)-3),3) == 'pdf') && $the_media->width == 0){
				if(substr($the_media->local,(strlen($the_media->local)-3),3) == 'pdf'){
					$media='<div class="contentpane">
								<iframe id="blockrandom"
									name="iframe"
									src=""
									width="100%"
									height="600"
									scrolling="auto"
									align="top"
									frameborder="2"
									class="wrapper">
										<object data="'.$the_base_link.'/'.$configs->docsin.'/'.$the_media->local.'" type="application/pdf">
											<embed src="'.$the_base_link.'/'.$configs->docsin.'/'.$the_media->local.'" type="application/pdf" />
										</object>
									</iframe>
								</div>';
				}
				else{
					$media='<div class="contentpane">
								<iframe id="blockrandom"
									name="iframe"
									src="'.$the_base_link.'/'.$configs->docsin.'/'.$the_media->local.'"
									width="100%"
									height="600"
									scrolling="auto"
									align="top"
									frameborder="2"
									class="wrapper">
									This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
								</div>';
				}
			}
							
			if($the_media->source == 'local' && $the_media->width == 1)
				$media='<a href="'.$the_base_link.'/'.$configs->docsin.'/'.$the_media->local.'" target="_blank">'.$the_media->name.'</a>';
	
			if($the_media->source == 'url'  && $the_media->width == 0)
				$media='<div class="contentpane">
							<iframe id="blockrandom"
								name="iframe"
								src="'.$the_media->url.'"
								width="100%"
								height="600"
								scrolling="auto"
								align="top"
								frameborder="2"
								class="wrapper">
								This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
							</div>';		
							
			if($the_media->source == 'url'  && $the_media->width == 1)
				$media='<a href="'.$the_media->url.'" target="_blank">'.$the_media->name.'</a>';								
		}	
		
		if(!isset($media)){
			$media=NULL;
		}
		
		if($the_media->show_instruction == "0"){//show the instructions above
			$media = '<div class="uk-text-center wk-text-center"><i>'.$the_media->instructions.'</i></div>'.
					 $media;
		}
		elseif($the_media->show_instruction == "1"){//show the instructions above
			$media = $media.
					 '<br /><br />
					 <div class="uk-text-center wk-text-center"><i>'.$the_media->instructions.'</i></div>';
		}
		elseif($the_media->show_instruction == "2"){//don't show the instructions
			$media = $media;
		}
		if($the_media->type != 'quiz'){
			if(@$the_media->hide_name == 0){
				$media .= '<div class="uk-text-bold uk-text-center wk-text-bold wk-text-center">'.@$the_media->name.'</div>';
			}
		}

		$media = str_replace("<img ", '<img onclick="window.parent.openFullImageGuru(this) "', $media);
		
		return stripslashes($media);	
	}
	
	public static function parse_media ($id, $layout=0){
		$questions_html_ids = array();
		$db = JFactory::getDBO();
		$jnow 	= new JDate('now');
		$date 	= $jnow->toSQL(true);
		$guruHelper = new guruHelper();
		$max_id = NULL;
		$open_target = "";
		
		$configs = self::getConfig();	
		$no_plugin_for_code = 0;
		$aheight			= 0; 
		$awidth				= 0; 
		$vheight			= 0; 
		$vwidth				= 0;
		
		if($layout != 12 && $layout != 16 && $layout != 17){
			$sql = "SELECT * FROM #__guru_media
					WHERE id = ".intval($id);
			$db->setQuery($sql);
			$media = $db->loadObject();	
			@$media->code = stripslashes(@$media->code);
		}
		elseif($layout == 12){
			$sql = "SELECT * FROM #__guru_quiz WHERE id = ".intval($id);
			$db->setQuery($sql);
			$media = $db->loadObject();
			@$media->type="quiz";
			$media->code="";
		}
		elseif($layout == 16){
			$sql = "SELECT * FROM #__guru_projects WHERE id = ".intval($id);
			$db->setQuery($sql);
			$media = $db->loadObject();
			@$media->type = "project";
			$media->code = "";
		}
		elseif($layout == 17){
			$sql = "SELECT * FROM #__guru_scorm_items WHERE id = ".intval($id);
			$db->setQuery($sql);
			$media = $db->loadObject();
			@$media->type = "scorm";
			$media->code = "";
		}
		
		$default_video_size_string = $configs->default_video_size;
		$default_video_size_array = explode("x", $default_video_size_string);
		$default_video_height = $default_video_size_array ["0"];
		$default_video_width = "100%"; //$default_video_size_array ["1"];
		
 		//start video		
		if(isset($media->type) && $media->type=='video'){
			if(intval($default_video_width) == 0){
				$default_video_width = "100%";
			}
			
			if ($media->source=='url' || $media->source=='local'){				
				if($media->width == 0 || $media->height == 0 || $media->option_video_size == "0"){
					$media->width = $default_video_width; //300; 
					$media->height = $default_video_height; //400;
				}
			}elseif($media->source=='code'){	
				if($media->option_video_size == "0"){
					$media->width = $default_video_width; //300; 
					$media->height = $default_video_height; //400;
					
					$replace_with = 'width="'.$media->width.'"';
					$media->code = preg_replace('#width="[0-9]+"#', $replace_with, $media->code);
					
					$replace_with = 'height="'.$media->height.'"';
					$media->code = preg_replace('#height="[0-9]+"#', $replace_with, $media->code);	
					
					$replace_with = 'name="width" value="'.$media->width.'"';
					$media->code = preg_replace('#name="width" value="[0-9]+"#', $replace_with, $media->code);
					$replace_with = 'name="height" value="'.$media->height.'"';
					$media->code = preg_replace('#name="height" value="[0-9]+"#', $replace_with, $media->code);
				}
				elseif ($media->width == 0 || $media->height == 0){
					//parse the code to get the width and height if we have width=... and height=....
					$begin_tag = strpos($media->code, 'width="');
					if ($begin_tag!==false){
						$remaining_code = substr($media->code, $begin_tag+7, strlen($media->code));
						$end_tag = strpos($remaining_code, '"');
						$media->width = substr($remaining_code, 0, $end_tag);					
						$begin_tag = strpos($media->code, 'height="');
						if ($begin_tag!==false){
							$remaining_code = substr($media->code, $begin_tag+8, strlen($media->code));
							$end_tag = strpos($remaining_code, '"');
							$media->height = substr($remaining_code, 0, $end_tag);
							$no_plugin_for_code = 1;
						}
						else{
							$media->width = $default_video_width; //300; 
							$media->height = $default_video_height; //400;
						}	
					}
					else{
						$media->width = $default_video_width; //300; 
						$media->height = $default_video_height; //400;
					}						
				}
				else{
					if($media->option_video_size == "0"){
						$media->width = $default_video_width; //300; 
						$media->height = $default_video_height; //400;
					}
					$replace_with = 'width="'.$media->width.'"';
					$media->code = preg_replace('#width="[0-9]+"#', $replace_with, $media->code);
					$replace_with = 'height="'.$media->height.'"';
					$media->code = preg_replace('#height="[0-9]+"#', $replace_with, $media->code);	
					
					$replace_with = 'name="width" value="'.$media->width.'"';
					$media->code = preg_replace('#name="width" value="[0-9]+"#', $replace_with, $media->code);
					$replace_with = 'name="height" value="'.$media->height.'"';
					$media->code = preg_replace('#name="height" value="[0-9]+"#', $replace_with, $media->code);	
				}
			}
			$vwidth=$media->width;
			$vheight=$media->height;
		}		
		//end video
		
		//start audio	
		elseif(isset($media->type) && $media->type=='audio'){
			if ($media->source=='url' || $media->source=='local'){	
				if ($media->width == 0 || $media->height == 0){
					$media->width=24; 
					$media->height=300;
				}
			}		
			elseif ($media->source=='code'){
				if ($media->width == 0 || $media->height == 0){
					$begin_tag = strpos($media->code, 'width="');
					if ($begin_tag!==false){
						$remaining_code = substr($media->code, $begin_tag+7, strlen($media->code));
						$end_tag = strpos($remaining_code, '"');
						$media->width = substr($remaining_code, 0, $end_tag);
						$begin_tag = strpos($media->code, 'height="');
						if ($begin_tag!==false){
							$remaining_code = substr($media->code, $begin_tag+8, strlen($media->code));
							$end_tag = strpos($remaining_code, '"');
							$media->height = substr($remaining_code, 0, $end_tag);
							$no_plugin_for_code = 1;
						}else{
							$media->height=24; 
							$media->width=300;
						}	
					}else{
						$media->height=24; 
						$media->width=300;
					}							
				}else{					
					$replace_with = 'width="'.$media->width.'"';
					$media->code = preg_replace('#width="[0-9]+"#', $replace_with, $media->code);
					$replace_with = 'height="'.$media->height.'"';
					$media->code = preg_replace('#height="[0-9]+"#', $replace_with, $media->code);
				}
			}	
			$awidth=$media->width;
			$aheight=$media->height;
		}	
		
		//-------------------------------------
		require_once(JPATH_ROOT .'/components/com_guru/helpers/videos/helper.php');
		$parsedVideoLink = parse_url(@$media->url);
		preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', @$parsedVideoLink['host'], $matches);
		$domain	= @$matches['domain'];
		
		if(!empty($domain)){
			$provider		= explode('.', $domain);
			$providerName	= strtolower($provider[0]);
			
			if($providerName == "youtu"){
				$providerName = "youtube";
			}
			
			$libraryPath = JPATH_ROOT .'/components/com_guru/helpers/videos'.'/'.$providerName.'.php';
	
			if(!file_exists($libraryPath) && $media->type == 'video'){
				$media->source = 'local';
				$media->local = $media->url;
				$media->exception = "1";
			}
		}
		//-------------------------------------
		
		$parts=explode(".", @$media->local);
		$extension=$parts[count($parts)-1];

		if(isset($media->type) && ($media->type=='video' || $media->type=='audio')){
			// $media->width = "100%";
			
			if($media->type=='video' && $extension=="avi"){
				$auto_play = "";
				
				if($media->auto_play == "1"){
					$auto_play = "&autoplay=1";
				}
				
				$media->code = '<object id="MediaPlayer1" CLASSID="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" type="application/x-oleobject" width="'.$media->width.'" height="'.$media->height.'">
<param name="fileName" value="'.JURI::root().$configs->videoin."/".$media->local.'">
<param name="animationatStart" value="true">
<param name="transparentatStart" value="true">
<param name="autoStart" value="true">
<param name="showControls" value="true">
<param name="Volume" value="10">
<param name="autoplay" value="false">
<embed width="'.$media->width.'" height="'.$media->height.'" name="plugin" src="'.JURI::root().$configs->videoin."/".$media->local.'" type="video/x-msvideo">
</object>';
			}
			elseif($no_plugin_for_code == 0){
				
				if($media->type == "video" && $media->source == "url"){
					$vwidth = "100%";
					$awidth = "100%";
					if(strrpos($media->url, "youtu") !== FALSE || strrpos($media->url, "vimeo") !== FALSE){
						require_once(JPATH_ROOT .'/components/com_guru/helpers/videos/helper.php');
						$parsedVideoLink = parse_url($media->url);
						preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $parsedVideoLink['host'], $matches);
						$domain	= $matches['domain'];

						if (!empty($domain)){
							$provider		= explode('.', $domain);
							$providerName	= strtolower($provider[0]);
							
							if($providerName == "youtu"){
								$providerName = "youtube";
							}
							
							$libraryPath = JPATH_ROOT .'/components/com_guru/helpers/videos' .'/'. $providerName . '.php';
							
							if(file_exists($libraryPath)){
								require_once($libraryPath);
								$className		= 'PTableVideo' . ucfirst($providerName);
								$videoObj		= new $className();
								$videoObj->init($media->url);
								$video_id		= $videoObj->getId();
								
								if($providerName == "youtube" || $providerName == "vimeo" || $providerName == "dailymotion"){
									$video_id = $video_id."?autoplay=".$media->auto_play;
								}

								$videoPlayer	= $videoObj->getViewHTML($video_id, $media->width, $media->height);
								$videoPlayer = preg_replace('/width="(.*)"/msU', 'width="100%"', $videoPlayer);
								
								$media->code = $videoPlayer;
							}
							else{
								$media->code = $guruHelper->create_media_using_plugin($media, $configs, $awidth, $aheight, $vwidth, $vheight);
							}
						}
					}
					else{
						$media->code = $guruHelper->create_media_using_plugin($media, $configs, $awidth, $aheight, $vwidth, $vheight);
					}
				}
				else{
					$media->code = $guruHelper->create_media_using_plugin($media, $configs, $awidth, $aheight, $vwidth, $vheight);	
				}
			}
		} 
		//end audio

		//start docs type
		if(isset($media->type) && $media->type=='docs'){
			//$media->code = 'The selected element is a text file that can\'t have a preview';	
			$media->code = JText::_("GURU_NO_PREVIEW");
			
			if($media->source == 'local' && (substr($media->local,(strlen($media->local)-3),3) == 'txt' || substr($media->local,(strlen($media->local)-3),3) == 'xls' || substr($media->local,(strlen($media->local)-4),4) == 'xlsx' || substr($media->local,(strlen($media->local)-3),3) == 'pdf') && $media->width > 1 && $media->height > 0) {
				if($media->height == 0){
					$media->height = 600;
				}
				
				if(substr($media->local,(strlen($media->local)-3),3) == 'pdf'){
					$detect = new Mobile_Detect;
					$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
					
					if($deviceType == "computer"){
						$media->code = '<div class="contentpane">
											<object data="'.JURI::root().$configs->docsin.'/'.$media->local.'" type="application/pdf" width="'.$media->width.'" height="'.$media->height.'" style="max-width:100%;">
												<embed src="'.JURI::root().$configs->docsin.'/'.$media->local.'" type="application/pdf" />
											</object>
										</div>';
					}
					else{
						$media->code .= '<iframe class="pdf-iframe" src="https://docs.google.com/gview?url='.urlencode(JURI::root().$configs->docsin.'/'.$media->local).'&embedded=true" style="width:'.$media->width.'px; max-width:100%; min-width:100%; height:'.$media->height.'px;" frameborder="0"></iframe>

							<script>
								$( document ).ready(function() {
									pdf_src = $(".pdf-iframe").attr("src");
									$(".pdf-iframe").attr("src", pdf_src);
								});
							</script>
						';
					}
				}
				elseif(substr($media->local,(strlen($media->local)-3),3) == 'xls' || substr($media->local,(strlen($media->local)-4),4) == 'xlsx'){
					include_once(JPATH_SITE.DS."components".DS."com_guru".DS."helpers".DS."excel_reader.php");
					$data = new Spreadsheet_Excel_Reader(JPATH_SITE.DS.$configs->docsin.'/'.$media->local);
					$boundsheets = $data->boundsheets;
					
					if(isset($boundsheets) && count($boundsheets) > 0){
						$sheets = array();
						
						foreach($boundsheets as $key=>$sheet){
							$display = "none";
							$sheets[$key] = $sheet["name"];
							
							if($key == 0){
								$display = "block";
							}
							
							$media->code .= '<div class="contentpane excel-content" id="sheet-'.intval($key).'" style="display:'.$display.';" >'.$data->dump(true, true, $key).'</div>';
						}
						
						if(isset($sheets) && count($sheets) > 0){
							foreach($sheets as $key=>$sheet){
								$btn_class = "uk-button uk-button-primary wk-button wk-button-primary sheet-btn";
								
								if($key == 0){
									$btn_class = "uk-button uk-button-success wk-button wk-button-success sheet-btn";
								}
								
								$media->code .= '<input type="button" id="btn-sheet-'.$key.'" class="'.$btn_class.'" value="'.$sheet.'" onclick="changeSheet('.intval($key).')" />';
							}
						}
						
						$media->code .= '<input type="hidden" id="nr-sheets" value="'.intval(count($sheets)).'" />';
					}
				}
				else{
					$media->code='<div class="contentpane">
									<iframe id="blockrandom" name="iframe" src="'.JURI::root().$configs->docsin.'/'.$media->local.'" width="'.$media->width.'" height="'.$media->height.'" scrolling="auto" align="top" frameborder="2" class="wrapper"> This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
								  </div>';
				}
				
				$media->name = '<div class="uk-text-center wk-text-center"><i>'.$media->name.'</i></div>';
				$media->instructions = '<div class="uk-text-center wk-text-center"><i>'.$media->instructions.'</i></div>';
				$media->code = '<div class="uk-text-center wk-text-center"><i>'.$media->code.'</i></div>';
				
				$return = "";
				if($media->show_instruction ==2){
					$return .= $media->code;
				}
				elseif($media->show_instruction ==1){
					$return .= $media->code; 
					$return .= ''.$media->instructions.'<br/>';
				} 
				elseif($media->show_instruction ==0){
					$return .= ''.$media->instructions.'<br/>';
					$return .= $media->code;
				}
				
				if(isset($media->hide_name) && $media->hide_name == 0){
					$return .= $media->name;
				}
				
				return $return;
			}
			elseif($media->source == 'url' && (substr($media->url,(strlen($media->url)-3),3) == 'xls' || substr($media->url,(strlen($media->url)-3),3) == 'txt' || substr($media->url,(strlen($media->url)-3),3) == 'pdf') && $media->width > 1) {
				
				$detect = new Mobile_Detect;
				$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
				
				if($deviceType == "computer"){
					$media->code = '<div class="contentpane">
							<iframe id="blockrandom" name="iframe" src="'.$media->url.'" width="'.$media->width.'" height="'.$media->height.'" scrolling="auto" align="top" frameborder="2" class="wrapper"> This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
						</div>';
				}
				else{
					if(substr($media->url,(strlen($media->url)-3),3) == 'pdf'){
						$media->code .= '<iframe class="pdf-iframe" src="https://docs.google.com/gview?url='.urlencode($media->url).'&embedded=true" style="width:'.$media->width.'px; max-width:100%; min-width:100%; height:'.$media->height.'px;" frameborder="0"></iframe>

							<script>
								$( document ).ready(function() {
									pdf_src = $(".pdf-iframe").attr("src");
									$(".pdf-iframe").attr("src", pdf_src);
								});
							</script>
						';
					}
					else{
						$media->code = '<div class="contentpane">
							<iframe id="blockrandom" name="iframe" src="'.$media->url.'" width="'.$media->width.'" height="'.$media->height.'" scrolling="auto" align="top" frameborder="2" class="wrapper"> This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
						</div>';
					}
				}

				$media->name = '<div class="uk-text-center wk-text-center"><i>'.$media->name.'</i></div>';
				$media->instructions = '<div class="uk-text-center wk-text-center"><i>'.$media->instructions.'</i></div>';
				$media->code = '<div class="uk-text-center wk-text-center"><i>'.$media->code.'</i></div>';
				
				$return = "";
				if($media->show_instruction ==2){
					$return .= $media->code;
				}
				elseif($media->show_instruction ==1){
					$return .= $media->code; 
					$return .= ''.$media->instructions.'<br/>';
				} 
				elseif($media->show_instruction ==0){
					$return .= ''.$media->instructions.'<br/>';
					$return .= $media->code;
				}
				
				if(isset($media->hide_name) && $media->hide_name == 0){
					$return .= $media->name;
				}
				
				return $return;
			}
			elseif($media->source == 'local' && $media->width == 1){
				//$media->code='<br /><a href="'.JURI::root().$configs->docsin.'/'.$media->local.'" target="_blank">'.$media->local.'</a>';
				
				$lesson_id_request = intval(JFactory::getApplication()->input->get("cid", "0", "raw"));
				$module_id_request = intval(JFactory::getApplication()->input->get("module", "0", "raw"));
				$media->code='<br /><a href="'.JURI::root()."index.php?option=com_guru&controller=guruTasks&task=download&media=".intval($media->id)."&lesson=".intval($lesson_id_request)."&module=".intval($module_id_request).'&tmpl=component&format=raw" target="_blank">'.$media->local.'</a>';
				
				return stripslashes($media->code.'<p /><div class="uk-text-center wk-text-center"><i>'.$media->instructions.'</i></div>');
			}
			
			elseif($media->source == 'url'  && $media->width == 0){
				$media->code='<div class="contentpane">
							<iframe id="blockrandom" name="iframe" src="'.$media->url.'" width="100%" height="600" scrolling="auto" align="top" frameborder="2" class="wrapper"> This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe> </div>';		
			}				
			elseif($media->source == 'url'  && $media->width == 1){
				$media->code='<a href="'.$media->url.'" target="_blank">'.$media->name.'</a>';		
			}
			elseif($media->source == 'local'  && $media->height == 0){
				//$media->code='<br /><a href="'.JURI::root().$configs->docsin.'/'.$media->local.'" target="_blank">'.$media->name.'</a>';
				
				$lesson_id_request = intval(JFactory::getApplication()->input->get("cid", "0", "raw"));
				$module_id_request = intval(JFactory::getApplication()->input->get("module", "0", "raw"));
				$media->code='<br /><a href="'.JURI::root()."index.php?option=com_guru&controller=guruTasks&task=download&media=".intval($media->id)."&lesson=".intval($lesson_id_request)."&module=".intval($module_id_request).'&tmpl=component&format=raw" target="_blank">'.$media->name.'</a>';

				return stripslashes($media->code.'<p /><div class="uk-text-center wk-text-center"><i>'.$media->instructions.'</i></div>');		
			}		
		}
		//end doc
	
		//start url
		if(isset($media->type) && $media->type=='url'){
			if($media->width == 1){
				$media->code = '<a href="'.$media->url.'" target="_blank">'.$media->url.'</a>';
			}
			else{
				$frame_width = "800";
				$frame_height = "600";
				
				if($media->width != 0 && $media->width != "" && $media->width != 1){
					$frame_width = $media->width;
				}

				if($media->height != 0 && $media->height != ""){
					$frame_height = $media->height;
				}
				
				$media->code = '<iframe id="blockrandom" name="iframe" src="'.$media->url.'" width="'.intval($frame_width).'px" height="'.intval($frame_height).'px" scrolling="auto" align="top" frameborder="2"></iframe>';
			}
		}
		//end url
		
		//start article
		if(isset($media->type) && $media->type=='Article'){
			$id = $media->code;
			include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');
			$media->code = guruAdminModelguruTask::getArticleById($id);
		}
		//end article

		//start image				
		if(isset($media->type) && $media->type=='image'){
			require_once("components/com_guru/helpers/helper.php");
			$helper = new guruHelper();
			$width = $media->width;
			$height = $media->height;
			$new_size = "";
			$type = "";
			if(intval($width) != 0){
				$new_size = $width;
				$type = "w";
			}
			else{
				$new_size = $height;
				$type = "h";
			}
			
			$ext = explode(".", $media->local);
			$ext = $ext[count($ext)-1];
			
			if(strtolower($ext) == "gif"){
				$media->code = '<img src="'.JURI::root().$configs->imagesin.'/media'.$media->local.'" />';
			}
			else{
				$helper->createThumb($media->local, $configs->imagesin.'/media', $new_size, $type);
				//$media->code = '<img src="'.JURI::root().$configs->imagesin.'/media/thumbs'.$media->local.'" />';
				$media->code = '<img src="'.JURI::root().$configs->imagesin.'/media'.$media->local.'" />';
			}
		}
		//end image
		
		//start text
		if(isset($media->type) && $media->type=='text'){
			//$media->code = str_replace("<img ", '<img onclick="openFullImageGuru(this) "', $media->code);
			$media->code = $media->code;
		}
		//end text
		
		//start docs type
		if(isset($media->type) && $media->type=='file'){			
			$media->code = JText::_('GURU_NO_PREVIEW');	
			if($media->source == 'local' && (substr($media->local,(strlen($media->local)-3),3) == 'zip' || substr($media->local,(strlen($media->local)-3),3) == 'exe')){
				//$media->code='<br /><a href="'.JURI::root().$configs->filesin.'/'.$media->local.'" target="_blank">'.$media->local.'</a>';

				$lesson_id_request = intval(JFactory::getApplication()->input->get("cid", "0", "raw"));
				$module_id_request = intval(JFactory::getApplication()->input->get("module", "0", "raw"));
				$media->code='<br /><a href="'.JURI::root()."index.php?option=com_guru&controller=guruTasks&task=download&media=".intval($media->id)."&lesson=".intval($lesson_id_request)."&module=".intval($module_id_request).'&tmpl=component&format=raw" target="_blank">'.$media->local.'</a>';
			}			
			else if($media->source == 'url'){
				$media->code='<a href="'.$media->url.'" target="_blank">'.$media->name.'</a>';		
			}
		}

		if (isset($media->type) && $media->type === 'zoom') {
			$mediaData = new JRegistry($media->code);
			$html = '<div class="media-zoom-container" style="text-align: center;">';
			$html .= '<h3>'.JText::_('GURU_MEDIA_ZOOM_MEETING_ID').': ' . $mediaData->get('meeting_id') . '</h3>';
			$html .= '<h3>'.JText::_('GURU_MEDIA_ZOOM_MEETING_PASSWORD').': ' . $mediaData->get('meeting_pass') . '</h3>';
			$html .= '<div><a class="btn btn-primary" target="_blank" href="' . $mediaData->get('invite_link') . '" >'.JText::_('GURU_ZOOM_CLICK_TO_JOIN').'</a></div>';
			$html .= '</div>';

			$media->code = $html;
		}
		
		//end doc
		
		if(isset($media->type) && $media->type=='quiz' && @$media->published=='1' && strtotime($media->startpublish) <= strtotime($date) && ( strtotime($media->endpublish) >= strtotime($date) || $media->endpublish == "0000-00-00 00:00:00")){

			$document = JFactory::getDocument();
    		JHtml::stylesheet("components/com_guru/css/quiz.css");
			$helperclass = new guruHelper();
			$all_quiz_question_from_page = array();

			$media->code = '';				
			$query  = "SELECT * FROM #__guru_quiz WHERE id = ".$media->id." and published=1";
			$db->setQuery($query);
			$result_quiz = $db->loadObject();
			$result_settings_quiz = $result_quiz;

			if($result_quiz->is_final == 0){
				$text_quiz_info_top = JText::_("GURU_MINIMUM_SCORE_QUIZ");
				$text_quiz_info_top1 = JText::_("GURU_QUIZ_TAKEN_UP_TO");
			}
			else{
				$text_quiz_info_top = JText::_("GURU_MINIMUM_SCORE_FINAL_QUIZ");
				$text_quiz_info_top1 = JText::_("GURU_QUIZ_CAN_BE_TAKEN");
			}
			
			$table_quiz = '<ul>';
			
			if($result_settings_quiz->show_limit_time == 0){
				if(intval($result_settings_quiz->limit_time) > 0){
					$table_quiz .= '<li class="guru-quiz__header-icon">'.JText::_("GURU_LIMIT_QUIZ").": "."<span><i class='fontello-hourglass'></i><span id='ijoomlaguru_time'>".$result_settings_quiz->limit_time." ".JText::_("GURU_REAL_MINUTES").'</span></span></li>';
				}
				else{
					$table_quiz .= '<li class="guru-quiz__header-icon">'.JText::_("GURU_LIMIT_QUIZ").": "."<span><i class='fontello-hourglass'></i><span id='ijoomlaguru_time'>".JText::_("GURU_UNLIMITED").'</span></span></li>';
				}
			}
			
			if($result_settings_quiz->pbl_max_score ==0){
				$table_quiz.= '<li>'.$text_quiz_info_top.": "."<span>".$result_settings_quiz->max_score.JText::_("GURU_PERCENT")."</span>".'</li>';
			}
			
			$sql = "SELECT count(*) as total from #__guru_questions_v3 where qid=".intval($media->id);
			$db->setQuery($sql);
			$total_quiz_questions = $db->loadColumn();
			$total_quiz_questions = @$total_quiz_questions["0"];
			
			if($result_settings_quiz->show_nb_quiz_select_up == 0){
				if(intval($result_settings_quiz->nb_quiz_select_up) == "0"){
					$result_settings_quiz->nb_quiz_select_up = intval($total_quiz_questions);
				}
			
				$table_quiz.= '<li>'.JText::_("GURU_QUESTIONS").": "."<span>".$result_settings_quiz->nb_quiz_select_up."</span>".'</li>';
			}
			
			if($result_settings_quiz->show_nb_quiz_taken ==0){
				if($result_settings_quiz->time_quiz_taken < 0){
					$result_settings_quiz->time_quiz_taken = 0;
				}

				if($result_settings_quiz->time_quiz_taken == 11){
					$timestotake = "Unlimited";
				}
				else{
					$timestotake = $result_settings_quiz->time_quiz_taken;

					$user = JFactory::getUser();

					$module_id = intval(JFactory::getApplication()->input->get("module"));
					$sql = "select pid from #__guru_days where id=".intval($module_id);
					$db->setQuery($sql);
					$pid = intval($db->loadResult());

					$user_id = $user->id;
					$db = JFactory::getDbo();
					$sql = "select count(*) from #__guru_quiz_question_taken_v3 where `user_id`=".intval($user_id)." and `quiz_id`=".intval($id)." and `pid`=".intval($pid);
                    $db->setQuery($sql);
                    $guru_quiz_taken_v3 = $db->loadColumn();
                    $guru_quiz_taken_v3 = @$guru_quiz_taken_v3["0"];
                    
                    if(intval($guru_quiz_taken_v3) > 0){
                    	$timestotake = $timestotake - $guru_quiz_taken_v3;
                    }

                    if($timestotake < 0){
                    	$timestotake = 0;
                    }
				}
				
				if(intval($timestotake) == 1){
					$table_quiz.= '<li>'.$text_quiz_info_top1.": "."<span>".$timestotake." ".JText::_("GURU_TIME").'</span></li>';
				}
				else{
					$table_quiz.= '<li>'.$text_quiz_info_top1.": "."<span>".$timestotake." ".JText::_("GURU_TIMES").'</span></li>';
				}
			}
			
			$user = JFactory::getUser();
			$database = JFactory::getDbo();
			
			$module_id = intval(JFactory::getApplication()->input->get("module"));
			$sql = "select pid from #__guru_days where id=".intval($module_id);
			$db->setQuery($sql);
			$pid = intval($db->loadResult());
			
			$task = JFactory::getApplication()->input->get("task", "", "raw");
			$user_id = $user->id;
			$sql = "SELECT count(*) as time_quiz_taken_per_user FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($media->id)." and pid=".intval($pid);
			$database->setQuery($sql);
			$database->execute();
			$time_quiz_taken_per_user = $database->loadColumn();
			$time_quiz_taken_per_user = $time_quiz_taken_per_user["0"];
			
			$sql = "SELECT id as id_quiz_taken_per_user FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($media->id)." and pid=".intval($pid)." ORDER BY id DESC";
			$database->setQuery($sql);
			$database->execute();
			$id_quiz_taken_per_user = $database->loadColumn();
			$id_quiz_taken_per_user = @$id_quiz_taken_per_user["0"];
			
			if($time_quiz_taken_per_user["0"] > 0 && $task != "quizz_fe_submit"){
				$catid = intval(JFactory::getApplication()->input->get("catid","", "raw"));
				$module = intval(JFactory::getApplication()->input->get("module","", "raw"));
				$cid = intval(JFactory::getApplication()->input->get("cid","", "raw"));
				$action_retake = JFactory::getApplication()->input->get("action_retake", "", "raw");
				$itemid_req = JFactory::getApplication()->input->get("Itemid", "", "raw");
				
				// not first time when access the quiz
				if($action_retake != "retake"){
					$app = JFactory::getApplication();

					$lang = JFactory::getLanguage()->getTag();
        			$lang = explode("-", $lang);
        			$lang = @$lang["0"];

        			$app->redirect(JURI::root().'index.php?option=com_guru&view=guruauthor&task=student_quizdetails&layout=student_quizdetails&pid='.intval($pid).'&userid='.intval($user_id).'&quiz='.intval($media->id).'&id='.intval($id_quiz_taken_per_user).'&tmpl=component&action=from_lesson&catid='.intval($catid)."&module=".intval($module)."&cid=".intval($cid)."&lang=".$lang."&Itemid=".intval($itemid_req));
				}
			}
			
			if($result_settings_quiz->time_quiz_taken > 1 && $result_settings_quiz->time_quiz_taken < 11){
				$time_user = $result_settings_quiz->time_quiz_taken - $time_quiz_taken_per_user["0"];

				$table_quiz .= '<li class="guru-quiz__header-icon guru-quiz__header--alt">';
				
				if($time_user == 1){
					$table_quiz .= '<span>';
					$table_quiz .= '<i class="fontello-info-circled-alt"></i>' . JText::_("GURU_TIMES_REMAIN_TAKE_QUIZ")." <span>".$time_user."</span>"." ".JText::_("GURU_ATTEMPT_LEFT");
					$table_quiz .= '</span>';
				}
				else{
					$table_quiz .= '<span>';
					$table_quiz .= '<i class="fontello-info-circled-alt"></i>' . JText::_("GURU_TIMES_REMAIN_TAKE_QUIZ")." <span>".$time_user."</span>"." " .JText::_("GURU_ATTEMPTS_LEFT");
					$table_quiz .= '</span>';
				}

				$table_quiz .= '</li>';
			}
			
			$table_quiz .= '</ul>';
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$submit_disabled = $registry->get('submit_disabled', "");
			
			if(isset($submit_disabled) && $submit_disabled != ""){
				$table_quiz .= '<div class="uk-grid wk-grid">';
				$table_quiz .= 		'<div class="uk-width-large-1-2 wk-width-large-1-2">'.$submit_disabled.'</div>';
				$table_quiz .= '</div>';
			}
			
			$user = JFactory::getUser();
			$user_id = $user->id;
			
			$media->code .= '<div class="guru-quiz__header">'.$table_quiz.'</div>';
			
			if($result_quiz->description !=""){
				$media->code .= '<div class="uk-panel uk-panel-box wk-panel wk-panel-box">'.$result_quiz->description.'</div>';
			}
			
			if(intval($result_settings_quiz->nb_quiz_select_up) == "0"){
				$result_settings_quiz->nb_quiz_select_up = intval($total_quiz_questions);
			}
			
			if(isset($result_settings_quiz->nb_quiz_select_up) && $result_settings_quiz->nb_quiz_select_up != 0 && $result_settings_quiz->show_nb_quiz_select_up == 0){
				$order_by = " GROUP BY `id`,  qid, type, question_content, media_ids, points, published, question_order ORDER BY RAND() LIMIT  ".$result_settings_quiz->nb_quiz_select_up."";
			}
			else{
				$order_by = " GROUP BY `id`,  qid, type, question_content, media_ids, points, published, question_order ORDER BY question_order LIMIT  ".$result_settings_quiz->nb_quiz_select_up."";
			}
			
			if($result_quiz->is_final == 1){
				$sql = "SELECT 	quizzes_ids FROM #__guru_quizzes_final WHERE qid=".$media->id;
				$db->setQuery($sql);
				$result=$db->loadResult();	
				$result_qids = explode(",",trim($result,","));
				
				if($result_qids["0"] == ""){
					$result_qids["0"] = 0;
				}
				
				if(isset($result_qids) && count($result_qids) > 0){
					foreach($result_qids as $key=>$value){
						$quiz_id = intval($value);
						$sql = "select published from #__guru_quiz where id=".intval($quiz_id);
						$db->setQuery($sql);
						$published = $db->loadColumn();
						$published = @$published["0"];
						if(intval($published) == 0){
							unset($result_qids[$key]);
						}
					}
				}
				
				if(count($result_qids) == 0 || @$result_qids["0"] == ""){
					$result_qids["0"] = 0;
				}
				
				$query  = "SELECT * FROM #__guru_questions_v3 WHERE qid IN (".implode(",", $result_qids).") and published=1".$order_by;
			}
			else{
				$query  = "SELECT * FROM #__guru_questions_v3 WHERE qid = ".$media->id." and published=1".$order_by;
			}

			$db->setQuery($query);
			$quiz_questions = $db->loadObjectList();

			$media->code.='<div id="the_quiz" class="uk-panel uk-panel-box-secondary uk-margin wk-panel wk-panel-box-secondary wk-margin">';
				
			$array_quest = array();
				
			$question_number = 1;
			
			$per_page = $result_quiz->questions_per_page;// questions per page
			if($per_page == 0){
				$per_page = count($quiz_questions);
			}
			$nr_pages = 1;// default one page
			
			if(count($quiz_questions) > 0 && count($quiz_questions) > $per_page){
				$nr_pages = ceil(count($quiz_questions) / $per_page);
			}

			for($pag = 1; $pag <= $nr_pages; $pag++){
				$i = ($pag - 1) * $per_page;
				$added = 0;

				$display = "";
				if($pag == 1){
					$display = "block";
				}
				else{
					$display = "none";
				}
				
				$media->code .= '<div id="quiz_page_'.$pag.'" style="display:'.$display.';">'; // start page
				
				while(isset($quiz_questions[$i]) && $added < $per_page){
					$all_quiz_question_from_page[] = $quiz_questions[$i]->id;
					$question_answers_number = 0;
					$media_associated_question = json_decode($quiz_questions[$i]->media_ids);
					$media_content = "";
					$result_media = array();
					
					$q  = "SELECT * FROM #__guru_question_answers WHERE question_id = ".intval($quiz_questions[$i]->id)." ORDER BY id";
					$db->setQuery( $q );
					$question_answers = $db->loadObjectList();	
					
					for($j=0; $j<count($media_associated_question); $j++){
						@$media_that_needs_to_be_sent = self::getMediaFromId($media_associated_question[$j]);
						if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
							$media_created = $helperclass->create_media_using_plugin_for_quiz($media_that_needs_to_be_sent["0"], $configs, '100%', '24', '150', 150);
							
							if($media_that_needs_to_be_sent["0"]->type == "file"){
								// do nothing
							}
							elseif($media_that_needs_to_be_sent["0"]->type == "video"){
								if(strpos($media_created, "width") !== FALSE){
									$media_created = preg_replace('/width="(.*)"/msU', 'width="150"', $media_created);
								}
								
								if(strpos($media_created, "height") !== FALSE){
									$media_created = preg_replace('/height="(.*)"/msU', 'height="150"', $media_created);
								}
								
								$hover_div = '<div class="hover-video" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">&nbsp;</div>';
								$media_created = $hover_div.$media_created;
							}
							elseif($media_that_needs_to_be_sent["0"]->type == "image"){
								$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_created.'</a>';
							}
							elseif($media_that_needs_to_be_sent["0"]->type == "text"){
								$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
							}
							elseif($media_that_needs_to_be_sent["0"]->type == "Article"){
								$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
							}
							elseif($media_that_needs_to_be_sent["0"]->type == "url"){
								$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.$media_that_needs_to_be_sent["0"]->url.'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
							}
							elseif($media_that_needs_to_be_sent["0"]->type == "audio"){
								// do nothing
							}
							elseif($media_that_needs_to_be_sent["0"]->type == "docs"){
								// do nothing
							}
						
							$result_media[] = $media_created;
						}	
					}

					$answer_status = '';
					$answer_status_text = '';
					
					if(isset($answer_given_by_user[$quiz_questions[$i]->id]) && isset($answers_right)){
						$css_validate_class = "question-false";
						$validate_answer = $this->validateAnswer($answers_right, $answer_given_by_user[$quiz_questions[$i]->id]);
						$answer_status = 'guru-quiz__status--false';
						$answer_status_text = '<i class="uk-icon-meh-o wk-icon-meh-o"></i>' . JText::_("GURU_ANSWER_FALSE_MESSAGE");
						
						if($validate_answer){
							$count_questions_right ++;
							$css_validate_class = "question-true";
							$answer_status = 'guru-quiz__status--correct';
							$answer_status_text = '<i class="uk-icon-smile-o wk-icon-smile-o"></i>' . JText::_("GURU_ANSWER_CORRECT_MESSAGE");
						}
					}
					
					$media->code .= '<div class="guru-quiz__question guru-question">';
					
					if($quiz_questions[$i]->type == "essay"){ //start essay question
						$random_nr = rand(1000, 9000);
						$questions_html_ids["essay"][$quiz_questions[$i]->id][] = 'quiz-essay-'.$random_nr;
						
						$doc = JFactory::getDocument();
						$doc->addStyleSheet(JURI::root().'components/com_guru/css/redactor.css');
						
						//echo '<script type="text/javascript" language="javascript" src="'.JURI::root().'components/com_guru/js/redactor.min.js'.'"></script>';
						
						$media->code .= '<div class="guru-quiz__media">'.implode("", $result_media).'</div>';
						$media->code .= '<div class="guru-quiz__question-title">';
						$media->code .= 	$quiz_questions[$i]->question_content;
						$media->code .= '</div>';
						$media->code .= '<div><textarea id="quiz-essay-'.$random_nr.'" style="max-width:100%" name="essay['.intval($quiz_questions[$i]->id).']" rows="10" class="useredactor"></textarea></div>';
						
						$upload_script = 'jQuery( document ).ready(function(){
											jQuery(".useredactor").redactor({
												 buttons: [\'bold\', \'italic\', \'underline\', \'link\', \'alignment\', \'unorderedlist\', \'orderedlist\']
											});
											jQuery(".redactor_useredactor").css("height","400px");
										  });';
						$doc->addScriptDeclaration($upload_script);
						
					}//end essay question
					else{// the rest: true/false, single, multiple
						$media->code .= '<div class="guru-quiz__media">'.implode("", $result_media).'</div>';
						$media->code .= '<div class="guru-quiz__question-title">';
						$media->code .= 	$quiz_questions[$i]->question_content;
						$media->code .= '</div>';
					}
					
					$media->code .= '<div class="guru-quiz__answers-wrapper">';
					$media->code .= '<div class="guru-quiz__answers uk-grid uk-grid-small wk-grid wk-grid-small" data-uk-grid-match data-uk-grid-margin>';

					if($quiz_questions[$i]->type == "true_false"){
						foreach($question_answers as $question_answer){
							if($question_answer->answer_content_text == "True"){
								$question_answer->answer_content_text = JText::_("GURU_QUESTION_OPTION_TRUE");
							}
							elseif($question_answer->answer_content_text == "False"){
								$question_answer->answer_content_text = JText::_("GURU_QUESTION_OPTION_FALSE");
							}
							
							$questions_html_ids["true"][$question_answer->question_id][] = $question_answer->question_id . intval($question_answer->id);
							
							$media->code .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3" id="guru-question-answer-'.$question_answer->question_id.'-'.$question_answer->id.'">
												 <div class="guru-quiz__answer">
													 <div class="uk-float-left wk-float-left">
														<input type="radio" id="'.$question_answer->question_id.intval($question_answer->id).'" onclick="javascript:answerTrueFalseSelected('.intval($question_answer->question_id).', '.intval($question_answer->id).');" name="truefs_ans['.intval($question_answer->question_id).']" value="'.$question_answer->id.'" />
														<label for="'.$question_answer->question_id.intval($question_answer->id).'" class="guru-quiz__check-box"></label>
													 </div>
													 <div class="uk-float-left wk-float-left">
														'.$question_answer->answer_content_text.'
													 </div>
												 </div>
											 </div>';
						}
					}
					elseif($quiz_questions[$i]->type == "single"){
						if(isset($question_answers) && count($question_answers) > 0){
							
							foreach($question_answers as $question_answer){
								$media_associated_answers = json_decode($question_answer->media_ids);
								$media_content = "";
								$result_media_answers = array();
								
								if(isset($media_associated_answers) && count($media_associated_answers) > 0){
									foreach($media_associated_answers as $media_key=>$answer_media_id){
										$media_that_needs_to_be_sent = self::getMediaFromId($answer_media_id);
										
										$media_created = $helperclass->create_media_using_plugin_for_quiz($media_that_needs_to_be_sent["0"], $configs, '100', '24', '150', 150);
										$media_created = preg_replace('/height="(.*)"/msU', 'height="100%"', $media_created);
										
										if($media_that_needs_to_be_sent["0"]->type == "file"){
											// do nothing
										}
										elseif($media_that_needs_to_be_sent["0"]->type == "video"){
											if(strpos($media_created, "width") !== FALSE){
												$media_created = preg_replace('/width="(.*)"/msU', 'width="150"', $media_created);
											}
											
											if(strpos($media_created, "height") !== FALSE){
												$media_created = preg_replace('/height="(.*)"/msU', 'height="150"', $media_created);
											}
											
											$hover_div = '<div class="hover-video" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">&nbsp;</div>';
											$media_created = $hover_div.$media_created;
										}
										elseif($media_that_needs_to_be_sent["0"]->type == "image"){
											$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_created.'</a>';
										}
										elseif($media_that_needs_to_be_sent["0"]->type == "text"){
											$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
										}
										elseif($media_that_needs_to_be_sent["0"]->type == "Article"){
											$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
										}
										elseif($media_that_needs_to_be_sent["0"]->type == "url"){
											$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.$media_that_needs_to_be_sent["0"]->url.'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
										}
										elseif($media_that_needs_to_be_sent["0"]->type == "audio"){
											// do nothing
										}
										elseif($media_that_needs_to_be_sent["0"]->type == "docs"){
											// do nothing
										}
									
										$result_media_answers[] = $media_created;
										
									}
								}
								
								$questions_html_ids["simple"][$question_answer->question_id][] = 'ans' . $question_answer->question_id . intval($question_answer->id);
								
								$option_value = '<input type="radio" id="ans'.$question_answer->question_id.intval($question_answer->id).'" name="answers_single['.intval($quiz_questions[$i]->id).']" value="'.$question_answer->id.'" onclick="javascript:answerSingleSelected('.intval($question_answer->question_id).', '.intval($question_answer->id).');"/><label for="ans'.$question_answer->question_id.intval($question_answer->id).'" class="guru-quiz__check-box"></label> <span>'.$question_answer->answer_content_text.'</span><div class="guru-quiz__answer-media">'.implode("",$result_media_answers).'</div>';
								
								$media->code .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3" id="guru-question-answer-'.$question_answer->question_id.'-'.$question_answer->id.'"><div class="guru-quiz__answer">'.$option_value.'</div></div>';
							}
						}						
					}
					elseif($quiz_questions[$i]->type == "multiple"){
						if(isset($question_answers) && count($question_answers) > 0){
							
							foreach($question_answers as $question_answer){
								$media_associated_answers = json_decode($question_answer->media_ids);
								$media_content = "";
								$result_media_answers = array();
								
								if(isset($media_associated_answers) && count($media_associated_answers) > 0){
									foreach($media_associated_answers as $media_key=>$answer_media_id){
										$media_that_needs_to_be_sent = self::getMediaFromId($answer_media_id);
										
										$media_created = $helperclass->create_media_using_plugin_for_quiz(@$media_that_needs_to_be_sent["0"], $configs, '100%', '24', '150', 150);
										$media_created = preg_replace('/height="(.*)"/msU', 'height="100%"', $media_created);
										
										if(@$media_that_needs_to_be_sent["0"]->type == "file"){
											// do nothing
										}
										elseif(@$media_that_needs_to_be_sent["0"]->type == "video"){
											if(strpos($media_created, "width") !== FALSE){
												$media_created = preg_replace('/width="(.*)"/msU', 'width="150"', $media_created);
											}
											
											if(strpos($media_created, "height") !== FALSE){
												$media_created = preg_replace('/height="(.*)"/msU', 'height="150"', $media_created);
											}
											
											$hover_div = '<div class="hover-video" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">&nbsp;</div>';
											$media_created = $hover_div.$media_created;
										}
										elseif(@$media_that_needs_to_be_sent["0"]->type == "image"){
											$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_created.'</a>';
										}
										elseif(@$media_that_needs_to_be_sent["0"]->type == "text"){
											$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
										}
										elseif(@$media_that_needs_to_be_sent["0"]->type == "Article"){
											$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
										}
										elseif(@$media_that_needs_to_be_sent["0"]->type == "url"){
											$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.$media_that_needs_to_be_sent["0"]->url.'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
										}
										elseif(@$media_that_needs_to_be_sent["0"]->type == "audio"){
											// do nothing
										}
										elseif(@$media_that_needs_to_be_sent["0"]->type == "docs"){
											// do nothing
										}
									
										$result_media_answers[] = $media_created;
									}
								}
								
								$questions_html_ids["multiple"][$question_answer->question_id][] = intval($question_answer->id);
								
								$option_value = '<input type="checkbox" onclick="javascript:answerMultipleSelected('.intval($question_answer->question_id).', '.intval($question_answer->id).');" name="multiple_ans['.intval($quiz_questions[$i]->id).'][]" id="'.$question_answer->id.'" value="'.$question_answer->id.'"/><label for="'.$question_answer->id.'" class="guru-quiz__check-box"></label> <span>'.$question_answer->answer_content_text.'</span><div class="guru-quiz__answer-media">'.implode("",$result_media_answers).'</div>';
								
								$media->code .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3" id="guru-question-answer-'.$question_answer->question_id.'-'.$question_answer->id.'"><div class="guru-quiz__answer">'.$option_value.'</div></div>';
							}
						}		
					}
					$media->code .= '	</div>';
					$media->code .= '<div class="guru-quiz__status '.$answer_status.'">'.$answer_status_text.'</div>';
					$media->code .= '</div>';
					$media->code .= '</div>'; // close answers wrapper
				
					$i++;
					$added++;
				}
				
				if($pag == $nr_pages){
					$catid_req = JFactory::getApplication()->input->get("catid","", "raw");
					$module_req = JFactory::getApplication()->input->get("module","", "raw");
					$cid_req = JFactory::getApplication()->input->get("cid","", "raw");
					
					$sql = "SELECT count(*) as total from #__guru_questions_v3 where qid=".intval($media->id);
					$db->setQuery($sql);
					$total_quiz_questions = $db->loadColumn();
					$total_quiz_questions = @$total_quiz_questions["0"];
					
					if(intval($result_settings_quiz->nb_quiz_select_up) == "0"){
						$result_settings_quiz->nb_quiz_select_up = intval($total_quiz_questions);
					}
					
					$media->code.='
						   <div>
								<input type="hidden" value="'.$media->name.'" id="quize_name" name="quize_name"/>
								<input type="hidden" value="'.$result_settings_quiz->nb_quiz_select_up.'" id="nb_of_questions" name="nb_of_questions"/>
								<input type="hidden" value="'.$media->id.'" id="quize_id" name="quize_id"/>
								<input type="hidden" value="1" name="submit_action" id="submit_action" />
								<input type="hidden" value="'.$catid_req.'" name="catid_req" id="catid_req">
								<input type="hidden" value="'.$module_req.'" name="module_req" id="module_req">
								<input type="hidden" value="'.$cid_req.'" name="cid_req" id="cid_req">
								<input type="hidden" value="'.$open_target.'" name="open_target" id="open_target">
								<input type="hidden" value="'.implode(",",$all_quiz_question_from_page).'" name="all_questions_ids">';
								
					if(isset($quiz_questions) && count($quiz_questions) > 0){
						$media->code .= '<input type="submit" name="submitbutton" class="guru-quiz__btn" id="submitbutton" value="'.JText::_("GURU_QUIZ_SUBMIT").'" />';
					}
													
					$media->code.= '</div><br>';
				}
				
				$media->code .= '</div>'; // end page
			}
			
			if($nr_pages > 1){
				$media->code .= '<div class="guru-quiz__pagination"><ul>';
				$media->code .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--start" id="pagination-start"><span>'.JText::_("GURU_START").'</span></li>';
				$media->code .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--prev" id="pagination-prev"><span>'.JText::_("GURU_PREV").'</span></li>';
				for($p=1; $p<=$nr_pages; $p++){
					if($p == 1){
						$media->code .= '<li class="guru-quiz__pagination-item" id="list_1"><span>1</span></li>';
					}
					else{
						$media->code .= '<li class="guru-quiz__pagination-item" id="list_'.$p.'">
											<a onclick="changePage('.intval($p).', '.intval($nr_pages).'); return false;" href="#">'.$p.'</a>
										 </li>';
					}
				}
				$media->code .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--next" id="pagination-next">
										<a href="#" onclick="changePage(2, '.intval($nr_pages).'); return false;">'.JText::_("GURU_NEXT").'</a>
									 </li>';
				$media->code .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--end" id="pagination-end">
										<a href="#" onclick="changePage('.intval($nr_pages).', '.intval($nr_pages).'); return false;">'.JText::_("GURU_END").'</a>
									 </li>';
				$media->code .= '</ul></div>';
			}
			// create quiz taken and question
			$sql = "SELECT open_target FROM #__guru_config WHERE id=1";
			$db->setQuery($sql);
			$open_target = $db->loadColumn();
			$open_target = $open_target["0"];
			
			$media->code.='<input type="hidden" value="'.($question_number-1).'" name="question_number" id="question_number" />';
			$media->code.='<input type="hidden" value="'.implode(",", $array_quest).'" name="list_questions_id" id="list_questions_id" />';
			$media->code.='<input type="hidden" value="'.$max_id.'" name="id_quiz_question" id="id_quiz_question" />';
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('questionsids', implode(",", $array_quest));
			$registry->set('quiz_id', $media->id);
			
			if(isset($result_time_user) && $result_time_user <= 0){
				$disabled='disabled=disabled';
				$msg= JText::_("GURU_QUIZ_RES_MC");
				$registry->set('submit_disabled', $msg);
			}
			else{
				$disabled='';
			}
			
			$media->code.='</div>';
			
			$script_validation = 'function validateQuizQuestions(){
				if(typeof secs != "undefined" && typeof mins != "undefined"){
					if(secs == 0 && mins == 0){
						return true;
					}
				}
			';
			
			if(isset($questions_html_ids) && count($questions_html_ids) > 0){
				foreach($questions_html_ids as $key=>$questios){
					if($key == "multiple" || $key == "simple" || $key == "true"){
						foreach($questios as $question_id=>$answers){
							if(count($answers) > 0){
								$if_array = array();
								
								foreach($answers as $key_answer=>$answer_id){
									$if_array[] = "document.getElementById('".$answer_id."').checked == false";
								}
								
								$script_validation .= "if(".implode(" && ", $if_array)."){
									alert(\"".JText::_("GURU_ANSWER_TO_QUESTIONS")."\");
									return false;
								}";
							}
						}
					}
					elseif($key == "essay"){
						foreach($questios as $question_id=>$answers){
							if(count($answers) > 0){
								$textarea_id = $answers["0"];
							
								$script_validation .= "if(document.getElementById(\"".$textarea_id."\").value == \"\"){
									alert(\"".JText::_("GURU_ANSWER_TO_QUESTIONS")."\");
									return false;
								}";
							}
						}
					}
				}
			}
			
			$script_validation .= '}';
			
			$media->code = "<script>".$script_validation."</script>".$media->code;
		}

		if(isset($media->type) && $media->type=='project'){
			$project_image = "";

			if(isset($media->file) && trim($media->file) != ""){
				$media->file = str_replace("thumbs/", "", $media->file);

				$project_image = '
					<div class="lesson-project-image">
						<img src="'.JURI::root().$media->file.'" alt="'.$media->title.'" title="'.$media->title.'" />
					</div>
				';
			}

			$end_text = JText::_("GURU_UNLIMITED");

			if(trim($media->end) != '0000-00-00 00:00:00' && trim($media->end) != ''){
				$end_text = date("M d, Y", strtotime($media->end));
			}

			$project_content = '
				<div class="lesson-project-content">
					<h2>'.$media->title.'</h2>
					'.$project_image.'
					<div class="lesson-project-description">
						'.$media->description.'
						<div class="lesson-project-details">
							<div class="lesson-project-details-label">'.JText::_("GURU_START").':</div> <div>'.date("M d, Y", strtotime($media->start)).'</div>
							<br />
							<div class="lesson-project-details-label">'.JText::_("GURU_END").':</div> <div>'.$end_text.'</div>
						</div>
					</div>
				</div>
			';

			$media->code = $project_content;
		}

		if(isset($media->type) && $media->type=='scorm'){
			$cid = JFactory::getApplication()->input->get("cid", "0", "raw");

			$db = JFactory::getDbo();
			$sql = "select `id`, `entry` from #__guru_scorm where `lesson_id`=".intval($cid);
			$db->setQuery($sql);
			$scorm_details = $db->loadAssocList();
			$scorm_id = 0;
			$scoid = 0;

			if(isset($scorm_details) && isset($scorm_details["0"]["id"])){
				$scorm_id = intval($scorm_details["0"]["id"]);
			}

			if(isset($scorm_details) && isset($scorm_details["0"]["entry"])){
				$scoid = intval($scorm_details["0"]["entry"]);
			}

			$media->show_instruction = 2;
			$media->code = '<iframe id="scorm_object" frameBorder="0" type="text/html" src="'.JURI::root().'index.php?option=com_guru&view=gurutasks&task=scorm&lesson_id='.intval($cid).'&mode=&scorm='.intval($scorm_id).'&scoid='.intval($scoid).'&attempt=1&tmpl=component" style="height: 100vh !important; width: 100%;"></iframe>';
		}
		
		$return = "";
		
		if(isset($media->show_instruction) && $media->show_instruction == "0"){//show the instructions above
			$return = '<div  style="text-align:center"><i>'.$media->instructions.'</i></div>'.
					 $media->code;
		}
		elseif(isset($media->show_instruction) && $media->show_instruction == "1"){//show the instructions above
			$return = $media->code.
			   		 '<br /><br />
					 <div class="uk-text-center wk-text-center"><i>'.$media->instructions.'</i></div>';
		}
		elseif(isset($media->show_instruction) && $media->show_instruction == "2"){//don't show the instructions
			$return = $media->code;
		}
		elseif(!isset($media->show_instruction) || $media->show_instruction == NULL){
			$return = $media->code;
		}

		if(@$media->type != 'quiz'){
			if(@$media->hide_name == 0){
				$return .= '<div class="uk-text-bold uk-text-center wk-text-bold wk-text-center media-name">'.@$media->name.'</div>';
			}
		}

		return stripslashes($return);
	}	
	
	function getTask2($taskid){
			$database = JFactory::getDBO();
			
			$sql = "SELECT t.*, cat.name as cat_name FROM #__guru_task as t
					LEFT JOIN #__guru_taskcategory as cat on t.category = cat.id
					WHERE t.id = ".$taskid; 
			$database->setQuery($sql);
			$task = $database->loadObject();
			return $task;	
	
	}
	
	function getMediaForTask() {
		$db = JFactory::getDbo();
		$cid = JFactory::getApplication()->input->get("cid", "0", "raw");
		
		$sql = "SELECT media_id FROM #__guru_mediarel WHERE type_id=".intval($cid)." AND type='scr_m'";
		$db->setQuery($sql);
		$media_ids = $db->loadColumn();
		
		if(!is_array($media_ids) || count($media_ids) == 0){
			$media_ids = array("0");
		}
		
		$sql = "SELECT * FROM #__guru_media WHERE id in (".implode(",", $media_ids).")";
		$this->_attributes = $this->_getList($sql);
		
		return $this->_attributes;
	}
	
	function getprogramname () {
		$db =  JFactory::getDbo();
		$id = JFactory::getApplication()->input->get("cid","0", "raw");
		
		$sql = "SELECT pid FROM #__guru_days WHERE id=".intval($id);
		$db->setQuery($sql);
		$pid = $db->loadColumn();
		$pid = @$pid["0"];
		
		$sql = "SELECT * FROM #__guru_program WHERE id=".intval($pid);
		$programname = $this->_getList($sql);
		return $programname;
	}

	function getday() {
		$cid = JFactory::getApplication()->input->get("cid", "0", "raw");
		
		$sql = "SELECT * FROM #__guru_days WHERE id = ".intval($cid);
		$day = $this->_getList($sql);		
		return $day;
	}


	function find_task_status($progid, $day, $taskord) {
		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		$sql = "SELECT tasks FROM #__guru_programstatus WHERE userid=".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id= ".$progid.")";
		$database->setQuery($sql);
		$result = $database->loadObjectList();
		
		$task_array = $result[0]->tasks;

		$task_array = explode(';', $task_array);
		$task_array = $task_array[$day-1];
		$task_array = explode('-', $task_array);
		
		if(isset($task_array[$taskord]))
			{
				$the_task_status = $task_array[$taskord];
				$task_value_array = explode(',', $the_task_status);		
				if(isset($task_value_array[1]))
					$status = $task_value_array[1];		
				else
					$status = 0;		
			}
		else $status = 0;		
	
		return $status;
	}	

	function getTaskforOrd($progid, $day, $taskord) {
		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		$sql = "SELECT tasks FROM #__guru_programstatus WHERE userid=".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id= ".$progid.")";
		$database->setQuery($sql);
		$result = $database->loadResult();
		
		$task_array = $result;


		$task_array = explode(';', $task_array);
		$task_array = $task_array[$day-1];
		$task_array = explode('-', $task_array);
		
		if(isset($task_array[$taskord]))
			{
				$the_task_status = $task_array[$taskord];
				$task_value_array = explode(',', $the_task_status);		
				if(isset($task_value_array[0]))
					$taskid = $task_value_array[0];		
				else
					$taskid = 0;		
			}
		else $taskid = 0;		

		return $taskid;
	}	

	function find_no_of_tasks_done($progid, $day){
		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		$sql = "SELECT tasks FROM #__guru_programstatus WHERE userid=".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id= ".$progid.")";
		$database->setQuery($sql);
		$result = $database->loadResult();
		$task_array = explode(';', $result);
		$task_list_of_day = $task_array[$day-1];
		$no = substr_count ($task_list_of_day, ',2');
		return $no;
	}

	function change_task_status_2($progid, $day, $taskord) {
		$jnow 	= new JDate('now');
		$date 	= $jnow->toSQL(true);
		
		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		if($my->id>0)
			$sql = "SELECT tasks, days FROM #__guru_programstatus WHERE userid = ".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id= ".$progid.")";
		else 
			$sql = "SELECT tasks, days FROM #__guru_programstatus WHERE pid = (SELECT pid FROM #__guru_days WHERE id= ".$progid.")";
		$database->setQuery($sql);
		$result = $database->loadObject();
		
		$task_array = $result->tasks;
		$day_array = $result->days;
		
		//$copy_to_replace = $task_array;
		
		$task_array = explode(';', $task_array);

		$to_be_replaced = $task_array[$day-1];
		
		$to_be_replaced_array = explode('-', $to_be_replaced);
		
		$to_be_replaced_obj = $to_be_replaced_array[$taskord-1];
			$task_obj = explode(',', $to_be_replaced_obj);
		if($task_obj[1]==0)	
			$to_be_replaced_array[$taskord-1] = $task_obj[0].',1';
		
		$recreating_task_array = implode('-', $to_be_replaced_array);
		
		$task_array[$day-1] = $recreating_task_array;
		
		$new_task_array = implode(';', $task_array);
		
	
		$sql = "UPDATE #__guru_programstatus SET tasks='".$new_task_array."' WHERE pid = (SELECT pid FROM #__guru_days WHERE id= ".$progid.") AND userid =".$my->id;	
		$database->setQuery($sql);
		$database->execute();		
		
				$day_array = explode(';', $day_array);
				
				$day_status_array = explode(',', $day_array[$day-1]);
				if($day_status_array[1]=='0')
					{
						$day_array[$day-1] = $day_status_array[0].',1';
						$day_for_database = implode(';', $day_array);
						$sql = "UPDATE #__guru_programstatus SET days='".$day_for_database."' WHERE pid =(SELECT pid FROM #__guru_days WHERE id= ".$progid.") AND userid =".$my->id;	
						$database->setQuery($sql);		
						$database->execute();
					}	
					
				$sql = "SELECT status FROM #__guru_programstatus WHERE userid = ".$my->id." AND pid =(SELECT pid FROM #__guru_days WHERE id= ".$progid.")";
				$database->setQuery($sql);
				$result = $database->loadObjectList();
				$program_status = $result[0]->status;	
				
				if($program_status==0)
					{
						$sql = "UPDATE #__guru_programstatus SET status='1',startdate='".$date."' WHERE pid =(SELECT pid FROM #__guru_days WHERE id= ".$progid.") AND userid =".$my->id;	
						$database->setQuery($sql);		
						$database->execute();
					}		
	}	

	function done_task_status_2($progid, $day, $taskord) {
		$jnow 	= new JDate('now');
		$date 	= $jnow->toSQL(true);
		
		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		$sql = "SELECT tasks, days FROM #__guru_programstatus WHERE userid = ".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id= ".$progid.")";
		$database->setQuery($sql);
		$result = $database->loadObject();
		$task_array = $result->tasks;
		$day_array = $result->days;
		
		$task_array = explode(';', $task_array);

		$to_be_replaced = $task_array[$day-1];
		
		$to_be_replaced_array = explode('-', $to_be_replaced);
		
		$to_be_replaced_obj = $to_be_replaced_array[$taskord-1];
			$task_obj = explode(',', $to_be_replaced_obj);
		if($task_obj[1]!=2)	
			$to_be_replaced_array[$taskord-1] = $task_obj[0].',2';
		
		$recreating_task_array = implode('-', $to_be_replaced_array);
		
		$task_array[$day-1] = $recreating_task_array;
		
		$new_task_array = implode(';', $task_array);
	
		$sql = "UPDATE #__guru_programstatus SET tasks='".$new_task_array."' WHERE pid = (SELECT pid FROM #__guru_days WHERE id= ".$progid.") AND userid =".$my->id;	
		$database->setQuery($sql);
		$database->execute();		
		
				// we seach again to see if all the tasks for the current day are done
				$day_done = 1;
				
				foreach($to_be_replaced_array as $to_be_replaced_array_value)
					{
						$to_be_replaced_array_value_array = explode(',', $to_be_replaced_array_value);
						if($to_be_replaced_array_value_array[0] && $to_be_replaced_array_value_array[1]!=2)
							{
								$day_done = 0;
								break;
							}
					}

				$day_array = explode(';', $day_array);
				
				$day_status_array = explode(',', $day_array[$day-1]);
				if($day_status_array[1]=='1' && $day_done == 1)
					{
						$day_array[$day-1] = $day_status_array[0].',2';
						$day_for_database = implode(';', $day_array);
						$sql = "UPDATE #__guru_programstatus SET days='".$day_for_database."' WHERE pid =(SELECT pid FROM #__guru_days WHERE id= ".$progid.") AND userid =".$my->id;	
						$database->setQuery($sql);		
						$database->execute();

					}	
					
				$sql = "SELECT status FROM #__guru_programstatus WHERE userid = ".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id= ".$progid.")";
				$database->setQuery($sql);
				$result = $database->loadObjectList();
				$program_status = $result[0]->status;	
				
				if($program_status==0)
					{
						$sql = "UPDATE #__guru_programstatus SET status='1',startdate='".$date."' WHERE pid =(SELECT pid FROM #__guru_days WHERE id= ".$progid.") AND userid =".$my->id;	
						$database->setQuery($sql);		
						$database->execute();
					}
										
				// we seach again to see if all the tasks for the current day are done
				$program_done = 1;
				$to_be_replaced_array = $day_array;
				foreach($to_be_replaced_array as $to_be_replaced_array_value)
					{
						$to_be_replaced_array_value_array = explode(',', $to_be_replaced_array_value);
						if($to_be_replaced_array_value_array[0] && $to_be_replaced_array_value_array[1]!=2)
							{
								$program_done = 0;
								break;
							}
					}
					
				if($program_status==1 && $program_done==1)
					{
						$sql = "UPDATE #__guru_programstatus SET status='2',enddate='".$date."' WHERE pid =(SELECT pid FROM #__guru_days WHERE id= ".$progid.") AND userid =".$my->id;	
						$database->setQuery($sql);		
						$database->execute();
						
						// if the program doesn't have a RE-DO free we LOCK it - status = -1 (begin)
						$sql = "SELECT redo FROM #__guru_program WHERE id = (SELECT pid FROM #__guru_days WHERE id= ".$progid.")";
						$database->setQuery($sql);
						$result = $database->loadResult();
						$program_redo = $result;
						if($program_redo == 'cost' || $program_redo == 'same')
							{
								$sql = "UPDATE #__guru_programstatus SET status='-1' WHERE pid =(SELECT pid FROM #__guru_days WHERE id= ".$progid.") AND userid =".$my->id;	
								$database->setQuery($sql);		
								$database->execute();
							}
						// if the program doesn't have a RE-DO free we LOCK it - status = -1 (end)						
					}			

		$sql = "SELECT media_id FROM #__guru_mediarel WHERE type = 'email' AND type_id = (SELECT pid FROM #__guru_days WHERE id = '".$progid."') ";

		$database->setQuery($sql);
		$mail_array_ids = $database->loadResultArray();	
		$mail_array_ids = implode(',', $mail_array_ids);
						
		$task_array_to_string = implode(';', $task_array);	
		$how_many_tasks_are = substr_count($task_array_to_string , ',');	
		$how_many_tasks_are_done = substr_count($task_array_to_string , ',2');
		
		$done_raport =  $how_many_tasks_are_done /$how_many_tasks_are * 100;

		if ($done_raport >= 25 && $done_raport < 50)
			{
				$sql = "SELECT * FROM #__guru_emails WHERE type = 'trigger' AND trigger = 'quarter' AND published = '1' AND id in (".$mail_array_ids.") ";
			}
		elseif($done_raport >= 50 && $done_raport < 75)
			{
				$sql = "SELECT * FROM #__guru_emails WHERE type = 'trigger' AND trigger = 'half' AND published = '1' AND id in (".$mail_array_ids.") ";
			}
		elseif($done_raport >= 75 && $done_raport < 100)
			{
				$sql = "SELECT * FROM #__guru_emails WHERE type = 'trigger' AND trigger = 'uncompleted' AND published = '1' AND id in (".$mail_array_ids.") ";
			}
		elseif($done_raport == 100)
			{
				$sql = "SELECT * FROM #__guru_emails WHERE type = 'trigger' AND trigger = 'completed' AND published = '1' AND id in (".$mail_array_ids.") ";
			}	
		
		$database->setQuery($sql);
		$the_mail = $database->loadObjectList();
		
		if(isset($the_mail[0]))
			{
				$subject = $the_mail[0]->subject;
				$message = $the_mail[0]->body;
				// to do: parsing the {variables}
				$sqls = "SELECT * FROM #__guru_config WHERE id = 1 ";
				$database->setQuery($sqls);
				$configs = $database->loadObject();					

				if($the_mail[0]->sendtime == 0){
					// a real time mail with trigger - begin
					JFactory::getMailer()->sendMail( $configs->fromemail, $configs->fromname, $my->email, $subject, $message, 1 );
				}
				else{
					// a delayed message with trigger - begin
						$mail_id = $the_mail[0]->id;
						$sql_pend_em = "SELECT count(id) FROM #__guru_emails_pending WHERE mail_id = ".$mail_id." AND user_id = ".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id = '".$progid."') ";
						$database->setQuery($sql_pend_em);
						$pending_emails = $database->loadResult();			
						
						if($pending_emails==0)
							{
								$sql_pid = "SELECT pid FROM #__guru_days WHERE id= ".$progid;
								$database->setQuery($sql_pid);
								$prog_id = $database->loadResult();									
								
								if ($the_mail[0]->sendday == 1)
									$time_type = 3600*24;// day;
								elseif ($the_mail[0]->sendday == 2)
									$time_type = 3600; // hours;	
								elseif ($the_mail[0]->sendday == 3)
									$time_type = 3600*24*30;// months;
								elseif ($the_mail[0]->sendday == 3)
									$time_type = 3600*24*365;// years;
								
								$the_moment = time();
								
								$the_moment_final = $the_moment + $the_mail[0]->sendtime * $time_type;
								
								$sql = "INSERT INTO #__guru_emails_pending ( 
																						sending_time , 
																						mail_id , 
																						mail_subj , 
																						mail_body,
																						user_id ,
																						pid,
																						type
																			) VALUES ( 
																						'".$the_moment_final."', 
																						'".$mail_id."' , 
																						'".$subject."', 
																						'".$message."',
																						'".$my->id."',
																						'".$prog_id."',
																						'T'
																			)";
								$database->setQuery($sql);
								if (!$database->execute() ){
									$this->setError($database->getErrorMsg());
									return false;
								}

							}				
					} // a delayed message with trigger - end
			}	
		
		return true;	
	}	

	function reset_program ($progid){
		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		$sql = "SELECT tasks, days FROM #__guru_programstatus WHERE userid = ".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id= ".$progid.") ";
		$database->setQuery($sql);
		$result = $database->loadObjectList();
		
		$task_array = $result[0]->tasks;
		$day_array = $result[0]->days;
		
		$day_array_reset = str_replace(',2', ',0', $day_array);
		$task_array_reset = str_replace(',2', ',0', $task_array);

		$jnow 	= new JDate('now');
		$date 	= $jnow->toSQL(true);

		$sql = "UPDATE #__guru_programstatus SET 
													status='0',
													startdate='".$date."',
													enddate='0000-00-00 00:00:00',
													days = '".$day_array_reset."',
													tasks = '".$task_array_reset."',
													status=1
				WHERE pid =(SELECT pid FROM #__guru_days WHERE id= ".$progid.") AND userid =".$my->id;	
		$database->setQuery($sql);		
		$database->execute();
	}
	
	
	function getlistPackages () { 
		$database = JFactory::getDBO();
		$sql = "select * from #__ad_agency_order_type";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();
		return $rows;
	}	
	
	function find_day_status($dayid){
		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		$sql = "SELECT days FROM #__guru_programstatus WHERE userid = ".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id= ".$dayid.") ";
		$database->setQuery($sql);
		$result = $database->loadResult();
		
		$status = 0;
		
		$day_array = explode(';', $result);
		foreach($day_array as $day_value)
			{
				$day_value = explode(',', $day_value);
				if($day_value[0]==$dayid)
					{
						$status = $day_value[1];
						break;
					}
			}
		return $status;	
	}

	function find_ids_for_skip_and_done_2($dayid, $dayord, $taskord) {
		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		$sql = "SELECT tasks FROM #__guru_programstatus WHERE userid = ".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id = ".$dayid." ) ";
		$database->setQuery($sql);
		$result = $database->loadResult();
		
		$task_object = explode(';', $result);
		
		$task_array_object = $task_object[$dayord-1];
		
		$skip = 0;
		$done = 0;
		$the_order = 0;
		
		$task_array = explode('-', $task_array_object);
		
		if(isset($task_array[$taskord])){
				$skip = 1;}
		else {
				$skip = 0;}
		return $skip;
	}

	public static function getConfig(){
		$db =  JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config WHERE id = '1' ";
		$db->setQuery($sql);
		$config = $db->loadObject();
		return $config;
	}

	function parse_day_finnish_content($tasksarray) {
		$tasks_to_parse = '';
		$time_to_parse = 0;
		$points_to_parse = 0;

		$db =  JFactory::getDBO();
		foreach($tasksarray as $task)
		{
			$sql = "SELECT * FROM #__guru_task WHERE id = ".$task;
			$db->setQuery($sql);
			$returned_task = $db->loadObject();
			$tasks_to_parse = $tasks_to_parse.$returned_task->name.', ';
			$time_to_parse = $time_to_parse + $returned_task->time;
			$points_to_parse = $points_to_parse + $returned_task->points;
		} // foreach end	
		
		$tasks_to_parse = substr($tasks_to_parse, 0, strlen($tasks_to_parse)-2);
		$to_return = $tasks_to_parse.'$$$$$'.$time_to_parse.'$$$$$'.$points_to_parse;
		return $to_return;
	}


	function find_link_for_next_day($dayid, $dayord) {
		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		$sql = "SELECT days, tasks FROM #__guru_programstatus WHERE userid = ".$my->id." AND pid = (SELECT pid FROM #__guru_days WHERE id = ".$dayid." ) ";
		$database->setQuery($sql);
		$result = $database->loadObject();
		
		$no_of_days = 0;
		$day_array = explode(';', $result->days);
		foreach($day_array as $day)
			if(isset($day) && $day>0)
				$no_of_days++;
		
		if($dayord == $no_of_days)	
			{
				// we are on the last day already - we go back to "My programs" - begin
				$link = 'index.php?option=com_guru&view=guruPrograms&task=myprograms';
				// we are on the last day already - we go back to "My programs" - end
			}
		else
			{
				// we find now the day and the task
				$day_ = explode(',',$day_array[$dayord]);
				$next_day_id = $day_[0];
				
				//$task_object = explode(';', $result->tasks);
				//$task_array_object = $task_object[$dayord];
				//$task_array = explode('-', $task_array_object);
				//$the_task = explode(',', $task_array[0]);
				//$next_task_id = $the_task[0];
				
				$link = 'index.php?option=com_guru&view=guruTasks&task=view&cid=1&pid='.$next_day_id;
				// we find now the day and the task
			}	
		return $link;
	}	

	function create_trial($userid, $progid) {
		$db = JFactory::getDBO();	
		
		$sql = "SELECT count(id) FROM #__guru_programstatus 
				WHERE pid = ".$progid." AND userid = ".$userid;
		$db->setQuery($sql);

		$result = $db->loadResult();
		
		if($result==0)
		// only if this program hasn't got a line in the status program - trial or paid - begin
		{	
		$sql = "SELECT id FROM #__guru_days 
				WHERE pid = ".$progid." ORDER BY ordering ASC";
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		$day_status = '';
		$task_status = '';
		foreach($result as $day)
			{$day_status = $day_status.$day->id.',0;'; 
						
			$sqltasks = "SELECT media_id FROM #__guru_mediarel 
						WHERE type_id = ".$day->id." AND type = 'dtask' ";
			$db->setQuery($sqltasks);
			$resultt = $db->loadObjectList();
			foreach($resultt as $task)
					{$task_status = $task_status.$task->media_id.',0-';}
								
					$task_status = $task_status.';';		
					}
				
			$sqlins = "INSERT INTO #__guru_programstatus 
						( 
						userid , 
						pid , 
						days , 
						tasks , 
						status )
				VALUES (
						'".$userid."', 
						'".$progid."', 
						'".$day_status."', 
						'".$task_status."', 
						'0'
					);";
			$db->setQuery($sqlins);		
			$db->execute();		
			
		$sqlorder = "SELECT max(oid) FROM #__guru_order";
		$db->setQuery($sqlorder);
		$maxoid = $db->loadResult();	
		
		if(!isset($maxoid))
			$maxoid = 999;
		
			$sqlins = "INSERT INTO #__guru_order 
						( 
						oid , 
						userid , 
						programid , 
						date , 
						payment,
						published )
				VALUES (
						'".($maxoid+1)."', 
						'".$userid."', 
						'".$progid."',
						now(), 
						'Trial', 
						'1'
					);";
			$db->setQuery($sqlins);		
			$db->execute();		
		} // only if this program hasn't got a line in the status program - trial or paid - end
			
	}	
	
	function find_if_program_was_bought($userid, $progid) {
		$db = JFactory::getDBO();	
		
		$sql = "SELECT payment FROM #__guru_order
				WHERE programid = ".$progid." AND userid = ".$userid;
		$db->setQuery($sql);
		$result = $db->loadResult();
		
		return strtolower($result);
		}	
		
	function generate_quiz($qid){
		$db = JFactory::getDBO();	
		
		$sql = "SELECT q.description, q.image, quest.* FROM #__guru_quiz as q
				LEFT JOIN #__guru_questions_v3 as quest on q.id = quest.qid
				WHERE q.id = ".$qid." AND q.published = 1";
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		
		$media = '';
		
		foreach($result as $rez)
			{
				$media = $media.'<b>'.$rez->text.'</b><br />';
				for($i=1; $i<=10; $i++)
					{
						$answer = 'a'.$i;
						if($rez->$answer != '')
							{
								$media = $media.'<input name="'.$answer.'" type="radio" value="'.$i.'a">'.$rez->$answer.'</input><br />';
							}
					}
				$media = $media.'<br>';	
			}	
		return $media;	
	}	
	
	function getExercise(){
		$my=JFactory::getUser();
		$db = JFactory::getDBO();
		$config= $this->getConfig();
		$exercise = JFactory::getApplication()->input->get("id","0", "raw");
		$course	  = JFactory::getApplication()->input->get("pid","0", "raw");
		$sql="SELECT lmr.access, lm.*
			  FROM #__guru_mediarel lmr
			  LEFT JOIN #__guru_media lm
			  ON lmr.media_id=lm.id
			  WHERE lmr.type_id=".$course." AND lmr.media_id=".$exercise." AND lmr.type='pmed'";
		$db->setQuery($sql);
		$result=$db->loadObject();
		
		if($result->access==2 || ($result->access<2 && $my->id > 0)){		
			return $result;
		}	
		else{
		 	return false;
		}	
	}	
	
	function getSteps($author_id){
		$db= JFactory::getDBO();		
		$jnow 	= new JDate('now');
		$date 	= $jnow->toSQL(true);
	
		$sql = "SELECT lt.*
			FROM #__guru_task as lt, #__guru_program as lp
			LEFT JOIN #__guru_days as ld on lt.id=ld.pid  
			LEFT JOIN #__guru_mediarel as lm on ld.id=lm.type_id 						
			WHERE lp.author=".$author_id."  
			AND lp.published=1 
			AND lp.startpublish<='".$date."' 
			AND (lp.endpublish>'".$date."' or lp.endpublish='0000-00-00 00:00:00')
			AND lt.id=lm.media_id
			AND lp.id=lt.id
			GROUP BY lp.id";
		$db->setQuery($sql);
		$steps = $db->loadObjectList();
		
		return $steps;
	}
	
	function getSkipAction($course_id){
		$db = JFactory::getDBO();
		$sql = "select skip_module from #__guru_program where id=".intval($course_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	
	function getViewLesson($lesson_id){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$sql = "select count(*) from #__guru_viewed_lesson where user_id=".intval($user_id)." and lesson_id like '%|".$lesson_id."|%'";
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result > 0){
			return true;	
		}
		return false;
	}
	
	function setLessonNotViewed($step_id, $pid, $module_id = FALSE){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$id = $user->id;
		$config = JFactory::getConfig();
		$offset = JFactory::getApplication()->getCfg('offset');
		$jnow = new JDate('now');
		$date_last_visit = $jnow->toSQL(true);
		
		$module_id = intval($module_id ? $module_id : JFactory::getApplication()->input->get('module','0', "raw"));
		
		if($id != 0 && $pid !=""){
			$sql = "select `lesson_id` from #__guru_viewed_lesson where `pid`=".intval($pid)." and `user_id`=".intval($id);
			$db->setQuery($sql);
			$lesson_id = $db->loadColumn();
			$lesson_id = @$lesson_id["0"];

			if(isset($lesson_id) && trim($lesson_id) != ""){
				$lesson_id = str_replace( "|".intval($step_id)."|", "", $lesson_id);
				$sql = "update #__guru_viewed_lesson set `lesson_id`='".$db->escape(trim($lesson_id))."' where `pid`=".intval($pid)." and `user_id`=".intval($id);
				$db->setQuery($sql);
				$db->execute();
			}
		}
	}

	function saveLessonViewed($step_id, $pid, $module_id = FALSE){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$id = $user->id;
		$jnow = new JDate('now');
		$date_last_visit = $jnow->toSQL(true);
		
		$module_id = intval($module_id ? $module_id : JFactory::getApplication()->input->get('module','0', "raw"));
		
		if($id != 0 && $pid !=""){
			$sql = "select count(*) from #__guru_viewed_lesson where user_id=".intval($id)." and pid=".$pid;
			$db->setQuery($sql);
			$count = $db->loadResult();
			
			if($count == 0){
				$sql  = "insert into #__guru_viewed_lesson (user_id, lesson_id, module_id, date_last_visit, pid, date_completed) values ";
				$sql .= "(".$id.", '|".$step_id."|', '|".$module_id."|', '".$date_last_visit."' ,'".$pid."', '0000-00-00 00:00:00')";			
				$db->setQuery($sql);
				$db->execute();
				$sql = 'update #__guru_viewed_lesson set module_id = "|'.$module_id.'|" WHERE pid='.$pid;
			}
			else{
				$sql = "select count(*) from #__guru_viewed_lesson where user_id=".intval($id)." and lesson_id like '%|".$step_id."|%' and pid=".$pid;
				$db->setQuery($sql);
				$count = $db->loadResult();
				
				if($count == 0){
					$sql = 'update #__guru_viewed_lesson set lesson_id = CONCAT(lesson_id, "|'.$step_id.'|"), module_id = "|'.$module_id.'|", date_last_visit = "'.$date_last_visit.'"  where user_id='.intval($id)." and pid=".$pid;
					$db->setQuery($sql);
					$db->execute();
				}
				else{
					$sql = "SELECT lesson_id from #__guru_viewed_lesson WHERE user_id =".intval($id)." and lesson_id like '%|".$step_id."|%' and pid=".$pid;
					$db->setQuery($sql);
					$result = $db->loadResult();
					$result_lesson = explode('||', trim($result, "||"));
					foreach($result_lesson as $key=>$value){
						if($step_id == $value){
							unset($result_lesson[$key]);
							$result_lesson[] = $step_id;
							break;
						}
					}
					$result_lesson = implode("||", $result_lesson);
					$result_lesson = "|".$result_lesson."|";
					$sql = 'update #__guru_viewed_lesson set lesson_id ="'.$result_lesson.'", date_last_visit = "'.$date_last_visit.'" where user_id='.intval($id).' and pid='.intval($pid);
					$db->setQuery($sql);
					$db->execute();
				}
				$sql = 'update #__guru_viewed_lesson set module_id = "|'.$module_id.'|" where user_id='.intval($id)." and pid=".$pid;
				$db->setQuery($sql);
				$db->execute();
			}
			
			$this->completeCourse($pid, $id);
		}
	}

	function completeCourse($course_id, $user_id)
	{
		$db = JFactory::getDbo();
		$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
		$jnow = new JDate('now');
		$jnow->setTimezone($timezone);
		$date = $jnow->format('Y-m-d');
		$pid = $course_id;
		$id = $user_id;

		$sql = "SELECT lesson_id from `#__guru_viewed_lesson` WHERE user_id =".intval($id)." and pid=".$pid;
		$db->setQuery($sql);
		$result1 = $db->loadResult();
		if (!$result1) {
			return;
		}

		$result1 = explode('||', trim($result1, "||"));
		
		$sql ="SELECT id FROM `#__guru_task` WHERE published = 1 and id IN (SELECT media_id FROM `#__guru_mediarel` WHERE type = 'dtask' AND type_id in (SELECT id FROM `#__guru_days` WHERE pid =".$pid.") ) ";
		$db->setQuery($sql);
		$result2 = $db->loadColumn();
		
		$result1 = array_unique($result1);
		$result2 = array_unique($result2);
		
		@$intersect = array_intersect($result1, $result2);
		
		$sql = "SELECT `certificate_term`, `course_completed_term` FROM #__guru_program WHERE id =".intval($pid);
		$db->setQuery($sql);
		$certificate_details = $db->loadAssocList();
		$course_certificate_term = $certificate_details["0"]["certificate_term"];
		$course_completed_term = $certificate_details["0"]["course_completed_term"];

		$set_course_completed = false;
		$set_certificate_completed = false;

		if($course_completed_term == 2){
			//Complete all the lessons
			if(count($intersect) == count($result2)){
				$set_course_completed = true;	
			}
		}
		elseif($course_completed_term == 3){
			//Pass the final exam
			$sql = "select `id_final_exam` from #__guru_program where `id`=".intval($pid);
			$db->setQuery($sql);
			$quiz_id = $db->loadResult();
			
			$sql = "SELECT max_score FROM #__guru_quiz WHERE id=".intval($quiz_id);
			$db->setQuery($sql);
			$result_maxs = $db->loadResult();

			$sql = "SELECT `score_quiz` FROM #__guru_quiz_question_taken_v3 WHERE `user_id`=".intval($id)." and `quiz_id`=".intval($quiz_id)." and `pid`=".intval($pid)." ORDER BY `id` DESC LIMIT 0,1";
			$db->setQuery($sql);
			$score_quiz = $db->loadColumn();
			$score_quiz = @$score_quiz["0"];
			$res = intval($score_quiz);
			
			if(isset($result_maxs) && $res >= intval($result_maxs)){
				$set_course_completed = true;
			}
		}
		elseif($course_completed_term == 4){
			//Pass the quizzes in avg of
			include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');
			$guruModelguruTask = new guruModelguruTask();
			$scores_avg_quizzes = $guruModelguruTask->getAvgScoresQ($id, $pid);

			$sql = "SELECT avg_certificate_course_term from `#__guru_program` where id=".intval($pid);
			$db->setQuery($sql);
			$avg_certif = $db->loadResult();
			
			if($scores_avg_quizzes >= intval($avg_certif)){
				$set_course_completed = true;
			}
		}
		elseif($course_completed_term == 5){
			//Finish all lessons and pass final exam
			$sql = "SELECT `id_final_exam` from `#__guru_program` where `id`=".intval($pid);
			$db->setQuery($sql);
			$quiz_id = $db->loadResult();
			
			$sql = "SELECT max_score FROM `#__guru_quiz` WHERE id=".intval($quiz_id);
			$db->setQuery($sql);
			$result_maxs = $db->loadResult();
			
			$sql = "SELECT `score_quiz` FROM `#__guru_quiz_question_taken_v3` WHERE `user_id`=".intval($id)." and `quiz_id`=".intval($quiz_id)." and `pid`=".intval($pid)." ORDER BY `id` DESC LIMIT 0,1";
			$db->setQuery($sql);
			$score_quiz = $db->loadResult();
			$res = intval($score_quiz);
			
			if(isset($result_maxs) && $res >= intval($result_maxs)){
				if(count($intersect) == count($result2)){
					$set_course_completed = true;	
				}
			}
		}
		elseif($course_completed_term == 6){
			//Finish all lessons and pass quizzes in avg of
			include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');
			$guruModelguruTask = new guruModelguruTask();
			$scores_avg_quizzes = $guruModelguruTask->getAvgScoresQ($id, $pid);

			$sql = "select avg_certificate_course_term from #__guru_program where id=".intval($pid);
			$db->setQuery($sql);
			$avg_certif = $db->loadResult();
			
			if($scores_avg_quizzes >= intval($avg_certif)){
				if(count($intersect) == count($result2)){
					$set_course_completed = true;
				}
			}
		}
		elseif($course_completed_term == 7){
			//Course time recording
			$sql = "select `record_hour_course_term`, `record_min_course_term` from #__guru_program where `id`=".intval($pid);
			$db->setQuery($sql);
			$course_record_details = $db->loadAssocList();

			$sql = "select `viewed_time` from #__guru_viewed_lesson where `user_id`=".intval($id)." and `pid`=".intval($pid);
			$db->setQuery($sql);
			$viewed_time = $db->loadColumn();
			$viewed_time = @$viewed_time["0"];

			$saved_time = explode(":", $viewed_time);
			$saved_sec = $saved_time["2"];
			$saved_min = $saved_time["1"];
			$saved_hour = $saved_time["0"];

			if( (intval($saved_min) >= intval($course_record_details["0"]["record_min_course_term"])) && (intval($saved_hour) >= intval($course_record_details["0"]["record_hour_course_term"])) ){
				$set_course_completed = true;
			}
		}

		//------------------------------------------
		if($course_certificate_term == "1"){
			//No Certificate
			$set_certificate_completed = false;
		}
		elseif($course_certificate_term == "2"){
			//Complete all the lessons
			if(count($intersect) == count($result2)){
				$set_certificate_completed = true;	
			}
		}
		elseif($course_certificate_term == "3"){
			//Pass the final exam
			$sql = "select `id_final_exam` from #__guru_program where `id`=".intval($pid);
			$db->setQuery($sql);
			$quiz_id = $db->loadResult();
			
			$sql = "SELECT max_score FROM #__guru_quiz WHERE id=".intval($quiz_id);
			$db->setQuery($sql);
			$result_maxs = $db->loadResult();

			$sql = "SELECT `score_quiz` FROM #__guru_quiz_question_taken_v3 WHERE `user_id`=".intval($id)." and `quiz_id`=".intval($quiz_id)." and `pid`=".intval($pid)." ORDER BY `id` DESC LIMIT 0,1";
			$db->setQuery($sql);
			$score_quiz = $db->loadColumn();
			$score_quiz = @$score_quiz["0"];
			$res = intval($score_quiz);
			
			if(isset($result_maxs) && $res >= intval($result_maxs)){
				$set_certificate_completed = true;
			}
		}
		elseif($course_certificate_term == "4"){
			//Pass the quizzes in avg of
			include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');
			$guruModelguruTask = new guruModelguruTask();
			$scores_avg_quizzes = $guruModelguruTask->getAvgScoresQ($id, $pid);

			$sql = "select avg_certificate_course_term from #__guru_program where id=".intval($pid);
			$db->setQuery($sql);
			$avg_certif = $db->loadResult();
			
			if($scores_avg_quizzes >= intval($avg_certif)){
				$set_certificate_completed = true;
			}
		}
		elseif($course_certificate_term == "5"){
			//Finish all lessons and pass final exam
			$sql = "select `id_final_exam` from #__guru_program where `id`=".intval($pid);
			$db->setQuery($sql);
			$quiz_id = $db->loadResult();
			
			$sql = "SELECT max_score FROM #__guru_quiz WHERE id=".intval($quiz_id);
			$db->setQuery($sql);
			$result_maxs = $db->loadResult();
			
			$sql = "SELECT `score_quiz` FROM #__guru_quiz_question_taken_v3 WHERE `user_id`=".intval($id)." and `quiz_id`=".intval($quiz_id)." and `pid`=".intval($pid)." ORDER BY `id` DESC LIMIT 0,1";
			$db->setQuery($sql);
			$score_quiz = $db->loadColumn();
			$score_quiz = @$score_quiz["0"];
			$res = intval($score_quiz);
			
			if(isset($result_maxs) && $res >= intval($result_maxs)){
				if(count($intersect) == count($result2)){
					$set_certificate_completed = true;	
				}
			}
		}
		elseif($course_certificate_term == "6"){
			//Finish all lessons and pass quizzes in avg of
			include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');
			$guruModelguruTask = new guruModelguruTask();
			$scores_avg_quizzes = $guruModelguruTask->getAvgScoresQ($id, $pid);

			$sql = "select avg_certificate_course_term from #__guru_program where id=".intval($pid);
			$db->setQuery($sql);
			$avg_certif = $db->loadResult();
			
			if($scores_avg_quizzes >= intval($avg_certif)){
				if(count($intersect) == count($result2)){
					$set_certificate_completed = true;
				}
			}
		}
		elseif($course_certificate_term == "7"){
			//Course time recording
			$user = JFactory::getUser();
			$sql = "select `record_hour_course_term`, `record_min_course_term` from #__guru_program where `id`=".intval($pid);
			$db->setQuery($sql);
			$course_record_details = $db->loadAssocList();

			$sql = "select `viewed_time` from #__guru_viewed_lesson where `user_id`=".intval($id)." and `pid`=".intval($pid);
			$db->setQuery($sql);
			$viewed_time = $db->loadColumn();
			$viewed_time = @$viewed_time["0"];

			$saved_time = explode(":", $viewed_time);
			$saved_sec = $saved_time["2"];
			$saved_min = $saved_time["1"];
			$saved_hour = $saved_time["0"];

			if( (intval($saved_min) >= intval($course_record_details["0"]["record_min_course_term"])) && (intval($saved_hour) >= intval($course_record_details["0"]["record_hour_course_term"])) ){
				$set_certificate_completed = true;
			}
		}

		$sql = 'select `completed` from #__guru_viewed_lesson where user_id='.intval($id)." and pid=".intval($pid);
		$db->setQuery($sql);
		$completed_in_database = $db->loadResult();
		
		if($set_course_completed){
			if(intval($completed_in_database) == 0){
				$sql = 'update #__guru_viewed_lesson set `completed` = "1", `date_completed` = "'.$date.'" where user_id='.intval($id)." and pid=".intval($pid);
				$db->setQuery($sql);

				if($db->execute()){
					$sql = "select template_emails from #__guru_config limit 0, 1";
					$db->setQuery($sql);
					$template_emails = $db->loadColumn();
					$template_emails = @$template_emails["0"];
					$template_emails = json_decode($template_emails, true);
					
					$sql = "select c.firstname, c.lastname from #__guru_customer c, #__users u where u.id=c.id and c.id=".intval($id);
					$db->setQuery($sql);
					$student = $db->loadAssocList();
					$student_name = $student["0"]["firstname"];
					
					if(trim($student["0"]["lastname"]) != ""){
						$student_name .= " ".$student["0"]["lastname"];
					}
					
					$sql = "select * from #__guru_program where id=".intval($pid);
					$db->setQuery($sql);
					$course_details = $db->loadAssocList();
					
					$authors = $course_details["0"]["author"];
					$authors = explode("|", $authors);
					
					$app = JFactory::getApplication();
					$site_name = $app->getCfg('sitename');
					
					$config = new JConfig();
					$from = $config->mailfrom;
					$fromname = $config->fromname;
					
					// send email to authors
					if(isset($authors) && count($authors) > 0){
						foreach($authors as $key=>$author_id){
							$teacher_completed_course_subject = $template_emails["teacher_completed_course_subject"];
							$teacher_completed_course_body = $template_emails["teacher_completed_course_body"];
							
							if(intval($author_id) != 0){
								$sql = "select email, name from #__users where id=".intval($author_id);
								$db->setQuery($sql);
								$teacher = $db->loadAssocList();
								
								$email = $teacher["0"]["email"];
								$teacher_name = $teacher["0"]["name"];
								$recipient = array($email);
								
								$teacher_completed_course_subject = str_replace('[STUDENT_NAME]', $student_name, $teacher_completed_course_subject);
								$teacher_completed_course_subject = str_replace('[COURSE_NAME]', $course_details["0"]["name"], $teacher_completed_course_subject);
								$teacher_completed_course_subject = str_replace('[TEACHER_FULL_NAME]', $teacher_name, $teacher_completed_course_subject);
								$teacher_completed_course_subject = str_replace('[SITE_NAME]', $site_name, $teacher_completed_course_subject);
								
								$teacher_completed_course_body = str_replace('[STUDENT_NAME]', $student_name, $teacher_completed_course_body);
								$teacher_completed_course_body = str_replace('[COURSE_NAME]', $course_details["0"]["name"], $teacher_completed_course_body);
								$teacher_completed_course_body = str_replace('[TEACHER_FULL_NAME]', $teacher_name, $teacher_completed_course_body);
								$teacher_completed_course_body = str_replace('[SITE_NAME]', $site_name, $teacher_completed_course_body);
								
								$send_teacher_email_course_finished = isset($template_emails["send_teacher_email_course_finished"]) ? $template_emails["send_teacher_email_course_finished"] : 1;

								if($send_teacher_email_course_finished){
									JFactory::getMailer()->sendMail($from, $fromname, $recipient, $teacher_completed_course_subject, $teacher_completed_course_body, true);
								}
								
								$db = JFactory::getDbo();
								$query = $db->getQuery(true);
								$query->clear();
								$query->insert('#__guru_logs');
								$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type') ));
								$query->values(intval($author_id) . ',' . $db->quote('to-author-student-completed_course') . ',' . '0' . ',' . $db->quote(trim($email)) . ',' . $db->quote(trim($teacher_completed_course_subject)) . ',' . $db->quote(trim($teacher_completed_course_body)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''" );
								$db->setQuery($query);
								$db->execute();
							}
						}
						
						// send email to admins
						$sql = "select admin_email from #__guru_config limit 0, 1";
						$db->setQuery($sql);
						$admin_email = $db->loadColumn();
						$admin_email = @$admin_email["0"];
						$admin_email = explode(",", $admin_email);
						
						if(isset($admin_email) && count($admin_email) > 0){
							foreach($admin_email as $key=>$admin_id){
								if(intval($admin_id) != 0){
									$admin_completed_course_subject = $template_emails["admin_completed_course_subject"];
									$admin_completed_course_body = $template_emails["admin_completed_course_body"];
									
									$sql = "select email, name from #__users where id=".intval($admin_id);
									$db->setQuery($sql);
									$admin = $db->loadAssocList();
									
									$email = $admin["0"]["email"];
									$admin_name = $admin["0"]["name"];
									$recipient = array($email);
									
									$admin_completed_course_subject = str_replace('[STUDENT_NAME]', $student_name, $admin_completed_course_subject);
									$admin_completed_course_subject = str_replace('[COURSE_NAME]', $course_details["0"]["name"], $admin_completed_course_subject);
									$admin_completed_course_subject = str_replace('[ADMIN_NAME]', $admin_name, $admin_completed_course_subject);
									$admin_completed_course_subject = str_replace('[SITE_NAME]', $site_name, $admin_completed_course_subject);
									
									$admin_completed_course_body = str_replace('[STUDENT_NAME]', $student_name, $admin_completed_course_body);
									$admin_completed_course_body = str_replace('[COURSE_NAME]', $course_details["0"]["name"], $admin_completed_course_body);
									$admin_completed_course_body = str_replace('[ADMIN_NAME]', $admin_name, $admin_completed_course_body);
									$admin_completed_course_body = str_replace('[SITE_NAME]', $site_name, $admin_completed_course_body);
								
									$send_admin_email_course_finished = isset($template_emails["send_admin_email_course_finished"]) ? $template_emails["send_admin_email_course_finished"] : 1;

									if($send_admin_email_course_finished){
										JFactory::getMailer()->sendMail($from, $fromname, $recipient, $admin_completed_course_subject, $admin_completed_course_body, true);
									}
									
									$db = JFactory::getDbo();
									$query = $db->getQuery(true);
									$query->clear();
									$query->insert('#__guru_logs');
									$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type') ));
									$query->values(intval($admin_id) . ',' . $db->quote('to-admin-student-completed_course') . ',' . '0' . ',' . $db->quote(trim($email)) . ',' . $db->quote(trim($admin_completed_course_subject)) . ',' . $db->quote(trim($admin_completed_course_body)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''" );
									$db->setQuery($query);
									$db->execute();
								}
							}
						}
					}
				}
			}
		}
		else{
			$sql = 'update #__guru_viewed_lesson set completed = "0" where user_id='.intval($id)." and pid=".intval($pid);
			$db->setQuery($sql);
			$db->execute();
		}

		if($set_certificate_completed){
			$sql = "SELECT count(id) from #__guru_mycertificates WHERE user_id =".intval($id)." and course_id=".intval($pid);
			$db->setQuery($sql);
			$count_cert = $db->loadResult();

			if(intval($count_cert) == 0){
				$this->InsertMyCertificateDetails(intval($pid), $id);
			}

			$sql = "select `completed` from #__guru_mycertificates where user_id=".intval($id)." and course_id=".intval($pid);
			$db->setQuery($sql);
			$completed_in_database = $db->loadResult();

			if(intval($completed_in_database) == 0){
				$sql = 'update #__guru_mycertificates set `completed` = "1", `datecertificate`="'.$date.'" where user_id='.intval($id)." and course_id=".intval($pid);
				$db->setQuery($sql);
				$db->execute();

				$this->emailCertificate(intval($pid), $id);
			}
		}
		else{
			$sql = 'update #__guru_mycertificates set completed = "0" where user_id='.intval($id)." and course_id=".intval($pid);
			$db->setQuery($sql);
			$db->execute();
		}
	}
	
	function emailCertificate($pid, $uid = 0){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$id = $uid ? $uid : $user->id;
		$config = JFactory::getConfig();
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurubuy.php');
		@$guru_configs = guruModelguruBuy::getConfigs();
		$sql = "SELECT name from #__guru_program WHERE id =".$pid;
		$db->setQuery($sql);
		$resultcn = $db->loadResult();

		$imagename = "SELECT * FROM #__guru_certificates WHERE id=1";
		$db->setQuery($imagename);
		$imagename = $db->loadAssocList();

		$sql = "select `custom_certificate` from #__guru_program where `id`=".intval($pid);
		$db->setQuery($sql);
		$custom_certificate = $db->loadColumn();
		$custom_certificate = @$custom_certificate["0"];

		if(intval($custom_certificate) == 1){
			$sql = "SELECT * FROM #__guru_certificates WHERE `course_id`=".intval($pid);
			$db->setQuery($sql);
			$course_imagename = $db->loadAssocList();

			if(isset($course_imagename) && isset($course_imagename["0"]) && count($course_imagename) > 0){
				foreach ($course_imagename["0"] as $key => $value){
					//if(trim($value) != ""){
						$imagename["0"][$key] = $value;
					//}
				}
			}
		}

		$date_completed = "SELECT datecertificate FROM #__guru_mycertificates WHERE user_id=".intval($id)." and course_id=".$pid;
		$db->setQuery($date_completed);
		$date_completed = $db->loadResult();

		$format = "SELECT datetype FROM #__guru_config WHERE id=1";
		$db->setQuery($format);
		$format = $db->loadResult();
		
		$date_completed = date($format, strtotime($date_completed));

		$completiondate = $date_completed;
		$completiondate = date("Y-m-d", strtotime($completiondate));
		$sitename = $config->get('sitename');
		$coursename = $resultcn;

		$firstname = "SELECT firstname, lastname FROM #__guru_customer WHERE id=".intval($id);
		$db->setQuery($firstname);
		$firstname = $db->loadAssocList();
		
		$email = "SELECT email FROM #__users WHERE id=".intval($id);
		$db->setQuery($email);
		$email = $db->loadResult();

		$sql = "SELECT * FROM #__guru_mycertificates WHERE user_id=".intval($id)." and course_id=".$pid;
		$db->setQuery($sql);
		$certificate_details = $db->loadAssocList();
		$certificate_id = @$certificate_details["0"]["id"];
		$course_id = @$certificate_details["0"]["course_id"];
		$user_id = @$certificate_details["0"]["user_id"];
		
		$certificate_href = JURI::root()."index.php?option=com_guru&view=guruTasks&task=viewcertificate&certificate=".intval($certificate_id)."&pdf=1&dw=2&ci=2&course_id=".intval($course_id);
		$certificate_url = '<a href="'.$certificate_href.'" target="_blank">'.JText::_("GURU_URL_CERTIFICATE").'</a>';

		$imagename[0]["templates3"]  = str_replace("[SITENAME]", $sitename, $imagename[0]["templates3"]);
		$imagename[0]["templates3"]  = str_replace("[STUDENT_FIRST_NAME]", $firstname[0]["firstname"], $imagename[0]["templates3"]);
		$imagename[0]["templates3"]  = str_replace("[STUDENT_LAST_NAME]", $firstname[0]["lastname"], $imagename[0]["templates3"]);
		$imagename[0]["templates3"]  = str_replace("[COMPLETION_DATE]", $completiondate, $imagename[0]["templates3"]);
		$imagename[0]["templates3"]  = str_replace("[COURSE_NAME]", $coursename, $imagename[0]["templates3"]);
		$imagename[0]["templates3"]  = str_replace("[CERTIFICATE_URL]", $certificate_url, $imagename[0]["templates3"]);
		$imagename[0]["templates3"]  = str_replace("[SITEURL]", JURI::root(), $imagename[0]["templates3"]);

		if(isset($guru_configs["0"]["fromname"]) && trim($guru_configs["0"]["fromname"]) != ""){
			$fromname = trim($guru_configs["0"]["fromname"]);
		}
		if(isset($guru_configs["0"]["fromemail"]) && trim($guru_configs["0"]["fromemail"]) != ""){
			$from = trim($guru_configs["0"]["fromemail"]);
		}
		
		$email_body	= $imagename[0]["templates3"];
		
		$recipient = $email;
		$mode = true;
		
		$imagename[0]["subjectt3"]  = str_replace("[SITENAME]", $sitename, $imagename[0]["subjectt3"]);
		$imagename[0]["subjectt3"]  = str_replace("[STUDENT_FIRST_NAME]", $firstname[0]["firstname"], $imagename[0]["subjectt3"]);
		$imagename[0]["subjectt3"]  = str_replace("[STUDENT_LAST_NAME]", $firstname[0]["lastname"], $imagename[0]["subjectt3"]);
		$imagename[0]["subjectt3"]  = str_replace("[COMPLETION_DATE]", $completiondate, $imagename[0]["subjectt3"]);
		$imagename[0]["subjectt3"]  = str_replace("[COURSE_NAME]", $coursename, $imagename[0]["subjectt3"]);
		$imagename[0]["subjectt3"]  = str_replace("[CERTIFICATE_URL]", $certificate_url, $imagename[0]["subjectt3"]);
		$imagename[0]["subjectt3"]  = str_replace("[SITEURL]", JURI::root(), $imagename[0]["subjectt3"]);

		$subject_procesed = $imagename[0]["subjectt3"];
		$body_procesed = $email_body;
	
		$email_sent = "SELECT emailcert FROM #__guru_mycertificates WHERE user_id=".intval($id)." and course_id=".$pid;
		$db->setQuery($email_sent);
		$email_sent = $db->loadResult();

		if($email_sent == 0){
			if(!is_array($recipient)){
				$recipient = array($recipient);
			}
			
			JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject_procesed, $body_procesed, $mode);
			
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->clear();
			$query->insert('#__guru_logs');
			$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type') ));
			$query->values(intval($id) . ',' . $db->quote('get-certificate') . ',' . '0' . ',' . $db->quote(trim($recipient["0"])) . ',' . $db->quote(trim($subject_procesed)) . ',' . $db->quote(trim($body_procesed)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''" );
			$db->setQuery($query);
			$db->execute();
			
			$email_sentok = "UPDATE #__guru_mycertificates set emailcert=1 where user_id=".intval($id)." and course_id=".$pid;
			$db->setQuery($email_sentok);
			$db->execute();
		}
	
	}
	
	function InsertMyCertificateDetails($pid, $uid = 0){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		
		$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
		$jnow = new JDate('now');
		$jnow->setTimezone($timezone);
		
		$id = $uid ? $uid : $user->id;
		$sql = "SELECT count(id) from #__guru_mycertificates WHERE user_id =".intval($id)." and course_id=".intval($pid);
		$db->setQuery($sql);
		$count_cert = $db->loadResult();
		
		$current_date_cert = $jnow->toSQL(true);

		if(intval($count_cert) == 0){
			$author_id = "SELECT author from #__guru_program WHERE id =".intval($pid);
			$db->setQuery($author_id);
			$resultauth = $db->loadAssocList();
			$resultauth = $resultauth["0"]["author"];
			
			if(isset($resultauth) && trim($resultauth) != ""){
				$resultauth = explode("|", $resultauth);
				$resultauth = array_filter($resultauth);
			}

			foreach($resultauth as $key=>$value){
				if(intval($value) == 0){
					continue;
				}
			
				$sql = "insert into  #__guru_mycertificates (`course_id`, `author_id`, `user_id`, `emailcert`, `datecertificate`, `completed`) values ('".intval($pid)."', '".intval($value)."', '".intval($id)."', '0', '".$current_date_cert."', '0')";
				$db->setQuery($sql);
				$db->execute();
			}
		}
	
	}
	
	function getStepAccessCourses(){
		$course	  = JFactory::getApplication()->input->get("catid","0", "raw");
		$program = $this->get('Program');
		$db = JFactory::getDBO();
		$sql = "SELECT step_access_courses  FROM #__guru_program where id = ".$course;
		$db->setQuery($sql);
		$result=$db->loadResult();
		return $result;	
	}
	function getChbAccessCourses(){
		$course	  = JFactory::getApplication()->input->get("catid","0", "raw");
		$program = $this->get('Program');
		$db = JFactory::getDBO();
		$sql = "SELECT chb_free_courses  FROM #__guru_program where id = ".$course;
		$db->setQuery($sql);
		$result=$db->loadResult();
		return $result;	
	}
	function getDataStepAccessCourses($course_id){
		$program = $this->get('Program');
		$db = JFactory::getDBO();
		$sql = "SELECT step_access_courses  FROM #__guru_program where id = ".$course_id;
		$db->setQuery($sql);
		$result=$db->loadResult();
		return $result;	
	
	
	}
	function getDataChbAccessCourses($course_id){
		$program = $this->get('Program');
		$db = JFactory::getDBO();
		$sql = "SELECT chb_free_courses  FROM #__guru_program where id = ".$course_id;
		$db->setQuery($sql);
		$result=$db->loadResult();
		return $result;	
	
	}
	function getCertificate(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_certificates
				WHERE id = '1'";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	function getCertificateTerm($id){
		$db = JFactory::getDBO();
		$sql = "SELECT certificate_term  FROM #__guru_program
				WHERE id =".intval($id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}

	function getQuizzesByCourseId($id){
		$db = JFactory::getDBO();
		
		$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_days d where mr.type='dtask' and mr.type_id=d.id and d.pid=".intval($id);
		$db->setQuery($sql);
		$lessons = $db->loadColumn();
		
		if(!isset($lessons) || count($lessons) == 0){
			$lessons = array("0");
		}
		
		$sql = "select mr.media_id from #__guru_mediarel mr where mr.layout='12' and mr.type='scr_m' and mr.type_id in (".implode(", ", $lessons).")";
		$db->setQuery($sql);
		$result = $db->loadColumn();

		return $result;
	}

	function getFinalScoreQuizz($uid, $pid)
	{
		$db = JFactory::getDbo();
		$query = "SELECT id_final_exam FROM `#__guru_program` WHERE id = " . $db->q($pid);
		$final_id = $db->setQuery($query)->loadResult();

		if ($final_id) {
			$query = "SELECT score_quiz 
				FROM `#__guru_quiz_question_taken_v3` 
				WHERE user_id = " . $db->q($uid) . " AND quiz_id = " . $db->q($final_id) . " AND pid = " . $db->q($pid) . " ORDER BY id DESC";
			$score = $db->setQuery($query, 0, 1)->loadResult();

			return (int) $score;
		}

		return 0;
	}
	
	function getAvgScoresQ($uid, $pid){
		$db = JFactory::getDBO();
		$s = 0;
		
		$sql = "SELECT id_final_exam  FROM #__guru_program WHERE id =".intval($pid);
		$db->setQuery($sql);
		$id_final_exam = $db->loadResult();
		
		$quiz_id = $this->getQuizzesByCourseId($pid);
		
		$nb_ofscores = 0;
		
		if(isset($quiz_id) && count($quiz_id) > 0){
			$nb_ofscores = count($quiz_id);
		}

		for($i=0; $i<count($quiz_id); $i++){
			
			if($id_final_exam != 0){
				$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id =".intval($uid)." and pid =".intval($pid)." and quiz_id = ".$quiz_id[$i]." order by id desc limit 0,1";
			}
			else{
				$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id =".intval($uid)." and pid =".intval($pid). " order by id desc limit 0,1";
			}

			$db->setQuery($sql);
			$result = $db->loadObjectList();
			
			foreach($result as $key=>$value){
				$score = $value->score_quiz;
				if($score != ""){
					$s += $score;
				}
			}
		}
		
		if($nb_ofscores != 0){
			$result_score = intval($s / $nb_ofscores);
		}

		return @$result_score;
	}

	function getIsQuizOrNot($lid){
		$db = JFactory::getDBO();
		$sql = "SELECT type  FROM #__guru_media
				WHERE id =(SELECT media_id from #__guru_mediarel where type_id=".$lid." LIMIT 1)";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return @$result[0];
	}
	function studFailedQuiz($lid){
		$db = JFactory::getDBO();
		$sql = "SELECT student_failed_quiz  FROM #__guru_quiz WHERE id =(SELECT media_id FROM #__guru_mediarel WHERE type='scr_m' and type_id=".intval($lid)." LIMIT 1)";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return @$result[0];
	}
	function getIsFinal($id){
		$db = JFactory::getDBO();
		$sql = "SELECT is_final  FROM #__guru_quiz WHERE id =(SELECT media_id FROM #__guru_mediarel WHERE type='scr_m' and type_id=".intval($id)." LIMIT 1)";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return @$result[0];
	}

	function createTimer($quiz_id){
		$db = JFactory::getDBO();
		$timer_style = "";
		
		$sql = "SELECT  qct_alignment, qct_border_color, qct_minsec, qct_title_color, qct_bg_color, qct_font , qct_width,  qct_height, qct_font_nb, qct_font_words  FROM  #__guru_config WHERE id=1";
		$db->setQuery($sql);
		$result = $db->loadObjectList();
	
		if($result[0]->qct_alignment ==1){
			$align = "left";
		}
		elseif($result[0]->qct_alignment==2){
			$align = "right";
		}
		elseif($result[0]->qct_alignment ==3){
			$align = "center";
		}
	
		$sql = "SELECT limit_time, limit_time_f, show_finish_alert from #__guru_quiz WHERE id=".intval($quiz_id);
		$db->setQuery($sql);
		$quiz_details = $db->loadObject();
	
		$minutes = intval($quiz_details->limit_time);

		if(intval($minutes) != 0){
			$timer_style = '<div align ='.$align.'><span>
			<div style="width:'.$result[0]->qct_width.'px; height:'.$result[0]->qct_height.'px; border: 1px solid; border-color:'.'#'.$result[0]->qct_border_color.'; font-family:'.$result[0]->qct_font.'; background-color:'.'#'.$result[0]->qct_bg_color.';">
					<div align="center" style="border-bottom:1px '.'#'.$result[0]->qct_border_color.'solid; font-size:'.$result[0]->qct_font_words.'px; color:'.'#'.$result[0]->qct_title_color.'; background-color:'.'#'.$result[0]->qct_border_color.';">'.JText::_("GURU_TIMEPROMO").'</div>
			  <div id="totalbg" style="background-color:'.'#'.$result[0]->qct_bg_color.';">
				<div align="center" id="ijoomlaguru_time" style="font-size:'.$result[0]->qct_font_nb.'px; border-color:'.'#'.$result[0]->qct_border_color.'; color:'.'#'.$result[0]->qct_minsec.'; padding-top:10px;"></div>
				<div align="center" style="font-size:'.$result[0]->qct_font_words.'px;">'.JText::_("GURU_PROGRAM_DETAILS_MINUTES").'  '.JText::_("GURU_PROGRAM_DETAILS_SECONDS") .'</div>
			 </div>
			</div> 
			</span></div>';
		}
		
		return $timer_style;
	}
	
	function get_time_difference($start, $end){
    $uts['start'] = $start;
    $uts['end'] = $end;
    if( $uts['start'] !== -1 && $uts['end'] !== -1){
        if($uts['end'] >= $uts['start']){
            $diff = $uts['end'] - $uts['start'];
            if($days=intval((floor($diff/86400)))){
                $diff = $diff % 86400;
			}
				
            if($hours=intval((floor($diff/3600)))){
                $diff = $diff % 3600;
			}	
            
			if($minutes=intval((floor($diff/60)))){
                $diff = $diff % 60;
			}	
            $diff = intval($diff);
            return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff));
        }
		else{
			return false;
		}
    }
    return false;
}

	function isLastPassedQuiz($course_id){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$sql = "SELECT certificate_term from #__guru_program WHERE id=".intval($course_id);
		$db->setQuery($sql);
		$certificate_term = $db->loadColumn();
		$certificate_term = @$certificate_term["0"];
		
		if($certificate_term == 1){// no certificate
			return false;
		}
		
		if($certificate_term == 2){// Complete all the lessons
			$sql = "select completed from #__guru_viewed_lesson WHERE user_id=".intval($user_id)." and pid=".intval($course_id);
			$db->setQuery($sql);
			$completed = $db->loadColumn();
			$completed = @$completed["0"];

			if($completed == 1){
				return true;
			}
			return false;
		}
		
		if($certificate_term == 3){// Pass the final exam
			$sql = "select id_final_exam from #__guru_program where id=".intval($course_id);
			$db->setQuery($sql);
			$id_final_exam = $db->loadColumn();
			$id_final_exam = @$id_final_exam["0"];
			
			if(intval($id_final_exam) > 0){
				$sql = "select score_quiz from #__guru_quiz_taken_v3 where user_id=".intval($user_id)." and pid=".intval($course_id)." and quiz_id=".intval($id_final_exam);
				$db->setQuery($sql);
				$score_quiz = $db->loadColumn();
				$score_quiz = @$score_quiz["0"];
				
				if(isset($score_quiz) && trim($score_quiz) != ""){
					$sql = "select max_score from #__guru_quiz where id=".intval($id_final_exam);
					$db->setQuery($sql);
					$max_score = $db->loadColumn();
					$max_score = @$max_score["0"];
					
					$score_quiz_array = explode("|", $score_quiz);
					$correct = $score_quiz_array["0"];
					$total = $score_quiz_array["1"];
					
					$percent = @($correct * 100) / @$total;
					
					if($percent >= $max_score){
						return true;
					}
					return false;
				}
				return false;
			}
			return false;
		}
		
		if($certificate_term == 4){// Pass the quizzes in avg of...
			$sql = "SELECT * FROM #__guru_quiz_taken_v3 WHERE id in (select max(id) from #__guru_quiz_taken_v3 where user_id=".intval($user_id)." and pid=".intval($course_id)." group by quiz_id)";
			$db->setQuery($sql);
			$all_quizes = $db->loadAssocList();
			
			$sql = "select avg_certc from #__guru_program where id=".intval($course_id);
			$db->setQuery($sql);
			$avg_certc = $db->loadColumn();
			$avg_certc = @$avg_certc["0"];
			
			$sql = "select hasquiz from #__guru_program where id=".intval($course_id);
			$db->setQuery($sql);
			$all_quizes_from_course = $db->loadColumn();
			$all_quizes_from_course = @$all_quizes_from_course["0"];
			
			if(isset($all_quizes) && count($all_quizes) > 0){
				$percent = 0;
				foreach($all_quizes as $key=>$value){
					$score_quiz = $value["score_quiz"];
					$score_quiz_array = explode("|", $score_quiz);
					$correct = $score_quiz_array["0"];
					$total = $score_quiz_array["1"];
					
					$percent += ($correct * 100) / $total;
				}

				$total_percent = $percent / $all_quizes_from_course;
				
				if($total_percent >= $avg_certc){
					return true;
				}
				return false;
			}
			return false;
		}
		
		if($certificate_term == 5){// Finish all lessons and pass final exam
			$sql = "select completed from #__guru_viewed_lesson WHERE user_id=".intval($user_id)." and pid=".intval($course_id);
			$db->setQuery($sql);
			$completed = $db->loadColumn();
			$completed = @$completed["0"];
			
			if($completed == 0){
				return false;
			}
			
			$sql = "select id_final_exam from #__guru_program where id=".intval($course_id);
			$db->setQuery($sql);
			$id_final_exam = $db->loadColumn();
			$id_final_exam = @$id_final_exam["0"];
			
			if(intval($id_final_exam) > 0){
				$sql = "select score_quiz from #__guru_quiz_taken_v3 where user_id=".intval($user_id)." and pid=".intval($course_id)." and quiz_id=".intval($id_final_exam);
				$db->setQuery($sql);
				$score_quiz = $db->loadColumn();
				$score_quiz = @$score_quiz["0"];
				
				if(isset($score_quiz) && trim($score_quiz) != ""){
					$sql = "select max_score from #__guru_quiz where id=".intval($id_final_exam);
					$db->setQuery($sql);
					$max_score = $db->loadColumn();
					$max_score = @$max_score["0"];
					
					$score_quiz_array = explode("|", $score_quiz);
					$correct = $score_quiz_array["0"];
					$total = $score_quiz_array["1"];
					
					$percent = ($correct * 100) / $total;
					
					if($percent >= $max_score){
						return true;
					}
					return false;
				}
				return false;
			}
			return false;
		}
		
		if($certificate_term == 6){// Finish all lessons and pass quizzes in avg of...
			$sql = "select completed from #__guru_viewed_lesson WHERE user_id=".intval($user_id)." and pid=".intval($course_id);
			$db->setQuery($sql);

			$completed = $db->loadColumn();
			$completed = @$completed["0"];
			
			if($completed == 0){
				return false;
			}
			
			$sql = "SELECT * FROM #__guru_quiz_taken_v3 WHERE id in (select max(id) from #__guru_quiz_taken_v3 where user_id=".intval($user_id)." and pid=".intval($course_id)." group by quiz_id)";
			$db->setQuery($sql);
			$all_quizes = $db->loadAssocList();
			
			$sql = "select hasquiz from #__guru_program where id=".intval($course_id);
			$db->setQuery($sql);
			$all_quizes_from_course = $db->loadColumn();
			$all_quizes_from_course = @$all_quizes_from_course["0"];
			
			$sql = "select avg_certc from #__guru_program where id=".intval($course_id);
			$db->setQuery($sql);
			$avg_certc = $db->loadColumn();
			$avg_certc = @$avg_certc["0"];
			
			if(isset($all_quizes) && count($all_quizes) > 0){
				$percent = 0;
				foreach($all_quizes as $key=>$value){
					$score_quiz = $value["score_quiz"];
					$score_quiz_array = explode("|", $score_quiz);
					$correct = $score_quiz_array["0"];
					$total = $score_quiz_array["1"];
					
					$percent += ($correct * 100) / $total;
				}
				$total_percent = $percent / $all_quizes_from_course;
				
				if($total_percent >= $avg_certc){
					return true;
				}
				return false;
			}
			return false;
		}
		
		return false;
	}
	
	function createPendingQuiz($quiz_id,$all_questions_ids){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		//$date = date('Y-m-d h:i:s');
		$max_id = NULL;

		$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
		$jnow = new JDate('now');
		$jnow->setTimezone($timezone);
		$date = $jnow->toSQL(true);

		$sql = "SELECT time_quiz_taken FROM #__guru_quiz WHERE id=".intval($quiz_id);
		$db->setQuery($sql);
		$resultt = $db->loadColumn();
		$resultt = $resultt["0"];
		
		$sql = "SELECT count(user_id) FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id);
		$db->setQuery($sql);
		$resultu = $db->loadColumn();
		$iterator = 1;
		
		if($resultt < 11){
			if(intval($resultu["0"]) != 0){
				$iterator = intval($resultu["0"]) + 1;
			}
		}
		else{
			$iterator = 11;
		}
		
		$sql = "insert into #__guru_quiz_taken_v3 (user_id, quiz_id, score_quiz, date_taken_quiz, pid, time_quiz_taken_per_user) values (".intval($user_id).", ".intval($quiz_id).", '".$score_quiz."', '".$date."', '', ".intval($iterator).")";
		$db->setQuery($sql);
		if($db->execute()){
			$sql = "select max(id) from #__guru_quiz_taken_v3";
			$db->setQuery($sql);
			$max_id = $db->loadColumn();
			$max_id = $max_id["0"];
			
			$all_questions_ids_array = explode(",", $all_questions_ids);
			$sql = "INSERT INTO #__guru_quiz_question_taken_v3 (user_id, show_result_quiz_id, answers_gived,question_id, question_order_no) VALUES";			
			foreach($all_questions_ids_array as $key=>$q_id){
				$q_id = intval($q_id);
				if($q_id != 0){
					 $sql .= "('".intval($user_id)."', '".$max_id."', '', '".intval($q_id)."', '".($key +1)."'),";
					
				}
			}	
			$db->setQuery(substr($sql, 0, strlen($sql)  - 1));
			$db->execute();
		}
	
	}
	
	function getResultQuizzes($quiz_id, $course_id, $user_id, $number_of_questions){
		if(!isset($quiz_id) || intval($quiz_id) == 0){
			return "";
		}
	
		$db = JFactory::getDBO();
		$date = date('Y-m-d h:i:s');
		$quiz_form_content = "";
		$your_score_text = JText::_("GURU_YOUR_SCORE");
		$count_questions_right = 0;
		
		$q  = "SELECT * FROM #__guru_quiz WHERE id = ".intval($quiz_id);
		$db->setQuery($q);
		$result_quiz = $db->loadObject();
		
		$show_correct_ans = $result_quiz->show_correct_ans;
		
		$helperclass = new guruHelper();
		$configs = $this->getConfig();	
		
		$sql = "SELECT id, score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,1";                                               
		$db->setQuery($sql);
		$result_sql = $db->loadAssocList();
		$result_q = @$result_sql["0"]["score_quiz"];
		$score = @$result_sql["0"]["score_quiz"];
		$answer_id = @$result_sql["0"]["id"];
		
		$sql = "SELECT count(*) as time_quiz_taken_per_user FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id);
		$db->setQuery($sql);
		$result_qt = $db->loadColumn();
		$time_quiz_taken_per_user = $result_qt["0"];
		
		$answer_given_by_user = "SELECT question_id as question_idd, answers_given FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." and id_question_taken=".intval($answer_id);
		$db->setQuery($answer_given_by_user);
		$answer_given_by_user = $db->loadAssocList("question_idd");
		
		$sql = "SELECT count(*) as total from #__guru_questions_v3 where qid=".intval($quiz_id);
		$db->setQuery($sql);
		$total_quiz_questions = $db->loadColumn();
		$total_quiz_questions = @$total_quiz_questions["0"];
		
		if(intval($result_quiz->nb_quiz_select_up) == "0"){
			$result_quiz->nb_quiz_select_up = intval($total_quiz_questions);
		}
		
		$order_by = " ORDER BY question_order LIMIT  ".$result_quiz->nb_quiz_select_up."";

		$my_quiz_questions = array();
		
		if(isset($answer_given_by_user) && count($answer_given_by_user) > 0){
			foreach($answer_given_by_user as $my_key=>$my_question){
				$my_quiz_questions[] = $my_key;
			}
		}
		
		if(count($my_quiz_questions) == 0){
			$my_quiz_questions = array("0");
		}
		
		$query  = "SELECT * FROM #__guru_questions_v3 WHERE id IN (".implode(",", $my_quiz_questions).") and published=1".$order_by;
		$db->setQuery($query);
		$quiz_questions = $db->loadObjectList("id");
		
		/* order result by quiz questions ordering */
		if(isset($my_quiz_questions)){
		    $my_quiz_questions_array = $my_quiz_questions;

		    if(is_array($my_quiz_questions_array) && count($my_quiz_questions_array) > 0){
		        $quiz_questions_temp = array();

		        foreach($my_quiz_questions_array as $key=>$question_id){
		            if(isset($quiz_questions[$question_id])){
		                $quiz_questions_temp[] = $quiz_questions[$question_id];
		            }
		        }

		        $quiz_questions = $quiz_questions_temp;
		    }
		}
		/* order result by quiz questions ordering */

		$quiz_form_content .='<div id="the_quiz">';
			
		$array_quest = array();
			
		$question_number = 1;
		
		$per_page = $result_quiz->questions_per_page;// questions per page
		if($per_page == 0){
			$per_page = count($quiz_questions);
		}
		$nr_pages = 1;// default one page
		
		if(count($quiz_questions) > 0 && count($quiz_questions) > $per_page){
			$nr_pages = ceil(count($quiz_questions) / $per_page);
		}

		for($pag = 1; $pag <= $nr_pages; $pag++){
			$i = ($pag - 1) * $per_page;
			$added = 0;
			$exist_essay = FALSE;

			$display = "";
			if($pag == 1){
				$display = "block";
			}
			else{
				$display = "none";
			}
			
			$quiz_form_content .= '<div id="quiz_page_'.$pag.'" style="display:'.$display.';">'; // start page
			
			while(isset($quiz_questions[$i]) && $added < $per_page){
				$question_answers_number = 0;
				$media_associated_question = json_decode($quiz_questions[$i]->media_ids);
				$media_content = "";
				$result_media = array();
				
				$q  = "SELECT * FROM #__guru_question_answers WHERE question_id = ".intval($quiz_questions[$i]->id)." ORDER BY id";
				$db->setQuery( $q );
				$question_answers = $db->loadObjectList();	
							
				for($j=0; $j<count($media_associated_question); $j++){
					@$media_that_needs_to_be_sent = self::getMediaFromId($media_associated_question[$j]);
					
					if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
						$media_created = $helperclass->create_media_using_plugin_for_quiz($media_that_needs_to_be_sent["0"], $configs, '100%', '24', '150', 150);
						
						if($media_that_needs_to_be_sent["0"]->type == "file"){
							// do nothing
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "video"){
							if(strpos($media_created, "width") !== FALSE){
								$media_created = preg_replace('/width="(.*)"/msU', 'width="150"', $media_created);
							}
							
							if(strpos($media_created, "height") !== FALSE){
								$media_created = preg_replace('/height="(.*)"/msU', 'height="150"', $media_created);
							}
							
							$hover_div = '<div class="hover-video" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">&nbsp;</div>';
							$media_created = $hover_div.$media_created;
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "image"){
							$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_created.'</a>';
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "text"){
							$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "Article"){
							$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "url"){
							$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.$media_that_needs_to_be_sent["0"]->url.'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "audio"){
							// do nothing
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "docs"){
							// do nothing
						}
					
						$result_media[] = $media_created;
					}
				}
				
				$sql = "select id as answer_id from #__guru_question_answers where question_id=".intval($quiz_questions[$i]->id)." and correct_answer=1";
				$db->setQuery($sql);
				$answers_right = $db->loadAssocList("answer_id");
				
				$css_validate_class = '';
				$answer_status = '';
				$answer_status_text = '';
				
				if(isset($answer_given_by_user[$quiz_questions[$i]->id]) && isset($answers_right)){
					$css_validate_class = "question-false";
					$validate_answer = $this->validateAnswer($answers_right, $answer_given_by_user[$quiz_questions[$i]->id]);
					$answer_status = 'guru-quiz__status--false';
					$answer_status_text = '<i class="uk-icon-meh-o wk-icon-meh-o"></i>' . JText::_("GURU_ANSWER_FALSE_MESSAGE");
					
					if($quiz_questions[$i]->type == "essay"){
						$answer_status = 'guru-quiz__status--pending';
						$answer_status_text = '<i class="uk-icon-smile-o wk-icon-smile-o"></i>' . JText::_("GURU_ANSWER_PENDING_MESSAGE");
					}
					elseif($validate_answer){
						$count_questions_right ++;
						$css_validate_class = "question-true";
						$answer_status = 'guru-quiz__status--correct';
						$answer_status_text = '<i class="uk-icon-smile-o wk-icon-smile-o"></i>' . JText::_("GURU_ANSWER_CORRECT_MESSAGE");
					}
				}
				
				$quiz_form_content .= '<div class="guru-quiz__question guru-question">';
				
				if($quiz_questions[$i]->type == "essay"){ //start essay question
					$quiz_form_content .= '<div class="guru-quiz__media">'.implode("", $result_media).'</div>';
					$quiz_form_content .= '		<div class="guru-quiz__question-title">';
					$quiz_form_content .= 			$quiz_questions[$i]->question_content;
					$quiz_form_content .= '		</div>';
					$quiz_form_content .= '<div class="uk-grid wk-grid">';
					$quiz_form_content .= '<div class="uk-width-large-1-1 wk-width-large-1-1">';
				}//end essay question
				else{// the rest: true/false, single, multiple
					$quiz_form_content .= '<div class="guru-quiz__media">'.implode("", $result_media).'</div>';
					$quiz_form_content .= '		<div class="guru-quiz__question-title">';
					$quiz_form_content .= 			$quiz_questions[$i]->question_content;
					$quiz_form_content .= '		</div>';
					$quiz_form_content .= '<div class="guru-quiz__answers-wrapper">';
					$quiz_form_content .= '<div class="guru-quiz__answers uk-grid uk-grid-small wk-grid wk-grid-small" data-uk-grid-match data-uk-grid-margin>';
				}
				
				$checked = '';
				if($quiz_questions[$i]->type == "true_false"){
					
					foreach($question_answers as $question_answer){
						if(isset($answer_given_by_user[$question_answer->question_id]["answers_given"]) && $answer_given_by_user[$question_answer->question_id]["answers_given"] == $question_answer->id){
							$checked = 'checked="checked"';
							
							if($show_correct_ans){
								$answer_checked = 'guru-quiz__answer--checked ';
							}
							else{
								$checked = '';
							}
						}
						else{
							$checked = '';
							$answer_checked = '';
						}
						
						$correct_class = "";
						$border_correct_class = "";
						
						if($question_answer->correct_answer == 1){
							$correct_class = "correct-answer";
							
							if($show_correct_ans){
								$border_correct_class = "guru-quiz__answer--correct";
							}
						}
						
						if($question_answer->answer_content_text == "True"){
							$question_answer->answer_content_text = JText::_("GURU_QUESTION_OPTION_TRUE");
						}
						elseif($question_answer->answer_content_text == "False"){
							$question_answer->answer_content_text = JText::_("GURU_QUESTION_OPTION_FALSE");
						}
						
						$quiz_form_content .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3">
													<div class="guru-quiz__answer '.$answer_checked . $border_correct_class.'">
														<div class="uk-float-left wk-float-left">
															<input type="radio" '.$checked.' id="ans'.$question_answer->question_id.intval($question_answer->id).'" name="truefs_ans['.intval($question_answer->question_id).']" value="'.$question_answer->id.'" />
															<label for="ans'.$question_answer->question_id.intval($question_answer->id).'" class="guru-quiz__check-box"></label>
													 	</div>
													 	<div class="uk-float-left wk-float-left '.$correct_class.'">
															'.$question_answer->answer_content_text.'
														</div>
														<span class="answer-check"><i class="fontello-ok"></i><i class="fontello-cancel"></i></span>
													</div>
											   </div>';
					}
				}
				elseif($quiz_questions[$i]->type == "single"){
					if(isset($question_answers) && count($question_answers) > 0){
						
						foreach($question_answers as $question_answer){
							$media_associated_answers = json_decode($question_answer->media_ids);
							$media_content = "";
							$result_media_answers = array();
							
							if(isset($media_associated_answers) && count($media_associated_answers) > 0){
								foreach($media_associated_answers as $media_key=>$answer_media_id){
									$media_that_needs_to_be_sent = self::getMediaFromId($answer_media_id);
										
									$media_created = $helperclass->create_media_using_plugin_for_quiz($media_that_needs_to_be_sent["0"], $configs, '100%', '24', '150', 150);
									$media_created = preg_replace('/height="(.*)"/msU', 'height="100%"', $media_created);
									
									if($media_that_needs_to_be_sent["0"]->type == "file"){
										// do nothing
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "video"){
										if(strpos($media_created, "width") !== FALSE){
											$media_created = preg_replace('/width="(.*)"/msU', 'width="150"', $media_created);
										}
										
										if(strpos($media_created, "height") !== FALSE){
											$media_created = preg_replace('/height="(.*)"/msU', 'height="150"', $media_created);
										}
										
										$hover_div = '<div class="hover-video" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">&nbsp;</div>';
										$media_created = $hover_div.$media_created;
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "image"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_created.'</a>';
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "text"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "Article"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "url"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.$media_that_needs_to_be_sent["0"]->url.'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "audio"){
										// do nothing
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "docs"){
										// do nothing
									}
								
									$result_media_answers[] = $media_created;
								}
							}
							
							if(isset($answer_given_by_user[$question_answer->question_id]["answers_given"]) && $answer_given_by_user[$question_answer->question_id]["answers_given"] == $question_answer->id){
								$checked = 'checked="checked"';
								
								if($show_correct_ans){
									$answer_checked = 'guru-quiz__answer--checked ';
								}
								else{
									$checked = '';
								}
							}
							else{
								$checked = '';
								$answer_checked = '';
							}
							
							$correct_class = "";
							$border_correct_class = "";
							
							if($question_answer->correct_answer == 1){
								$correct_class = "correct-answer";
								
								if($show_correct_ans){
									$border_correct_class = "guru-quiz__answer--correct";
								}
							}
							
							$option_value = '<input type="radio" '.$checked.' id="ans'.$question_answer->question_id.intval($question_answer->id).'" name="answers_single['.intval($quiz_questions[$i]->id).']" value="'.$question_answer->id.'"/><label for="ans'.$question_answer->question_id.intval($question_answer->id).'" class="guru-quiz__check-box"></label>&nbsp;<span class="'.$correct_class.'">'.$question_answer->answer_content_text.'</span><div class="guru-quiz__answer-media">'.implode("",$result_media_answers).'</div>';
							$quiz_form_content .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3"><div class="guru-quiz__answer '.$answer_checked . $border_correct_class.'">'.$option_value.'<span class="answer-check"><i class="fontello-ok"></i><i class="fontello-cancel"></i></span></div></div>';
						}

					}						
				}
				elseif($quiz_questions[$i]->type == "multiple"){
					if(isset($question_answers) && count($question_answers) > 0){
						
						foreach($question_answers as $question_answer){
							$media_associated_answers = json_decode($question_answer->media_ids);
							$media_content = "";
							$result_media_answers = array();
							
							if(isset($media_associated_answers) && count($media_associated_answers) > 0){
								foreach($media_associated_answers as $media_key=>$answer_media_id){
									$media_that_needs_to_be_sent = self::getMediaFromId($answer_media_id);
										
									$media_created = $helperclass->create_media_using_plugin_for_quiz(@$media_that_needs_to_be_sent["0"], $configs, '100%', '24', '150', 150);
									$media_created = preg_replace('/height="(.*)"/msU', 'height="100%"', $media_created);
									
									if(@$media_that_needs_to_be_sent["0"]->type == "file"){
										// do nothing
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "video"){
										if(strpos($media_created, "width") !== FALSE){
											$media_created = preg_replace('/width="(.*)"/msU', 'width="150"', $media_created);
										}
										
										if(strpos($media_created, "height") !== FALSE){
											$media_created = preg_replace('/height="(.*)"/msU', 'height="150"', $media_created);
										}
										
										$hover_div = '<div class="hover-video" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">&nbsp;</div>';
										$media_created = $hover_div.$media_created;
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "image"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_created.'</a>';
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "text"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "Article"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "url"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.$media_that_needs_to_be_sent["0"]->url.'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "audio"){
										// do nothing
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "docs"){
										// do nothing
									}
								
									$result_media_answers[] = $media_created;
								}
							}
							
							$multiple_ans_given = explode(",", @$answer_given_by_user[$question_answer->question_id]["answers_given"]);
							
							$checked = '';
							$answer_checked = '';
							if(in_array($question_answer->id, $multiple_ans_given)){
								$checked = 'checked="checked"';
								
								if($show_correct_ans){
									$answer_checked = 'guru-quiz__answer--checked ';
								}
								else{
									$checked = '';
								}
							}
							
							$correct_class = "";
							$border_correct_class = "";
							
							if($question_answer->correct_answer == 1){
								$correct_class = "correct-answer";
								
								if($show_correct_ans){
									$border_correct_class = "guru-quiz__answer--correct";
								}
							}
							
							$option_value = '<input type="checkbox" '.$checked.' id="'.$question_answer->id.'" name="multiple_ans['.intval($quiz_questions[$i]->id).'][]" value="'.$question_answer->id.'"/><label for="'.$question_answer->id.'" class="guru-quiz__check-box"></label> &nbsp;<span class="'.$correct_class.'">'.$question_answer->answer_content_text.'</span><div class="guru-quiz__answer-media">'.implode("",$result_media_answers).'</div>';
							
							$quiz_form_content .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3"><div class="guru-quiz__answer '.$answer_checked . $border_correct_class.'">'.$option_value.'<span class="answer-check"><i class="fontello-ok"></i><i class="fontello-cancel"></i></span></div></div>';
						}
						
					}		
				}
				elseif($quiz_questions[$i]->type == "essay"){
					$sql = "select max(id) from #__guru_quiz_question_taken_v3 where user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id);
					$db->setQuery($sql);
					$id_question_taken = $db->loadColumn();

					$id_question_taken = $id_question_taken["0"];
					
					$q = "SELECT * FROM #__guru_quiz_taken_v3 WHERE id_question_taken = ".intval($id_question_taken)." and question_id=".intval($quiz_questions[$i]->id);
					$db->setQuery($q);
					$essay_answers = $db->loadObjectList();
					
					if(isset($essay_answers) && count($essay_answers) > 0){
						$doc = JFactory::getDocument();
						$doc->addStyleSheet(JURI::root().'components/com_guru/css/redactor.css');
						
						//echo '<script type="text/javascript" language="javascript" src="'.JURI::root().'components/com_guru/js/redactor.min.js'.'"></script>';
						
						$quiz_form_content .= '<textarea style="max-width:100%" rows="10" class="useredactor">'.$essay_answers["0"]->answers_given.'</textarea>';
						
						$upload_script = 'jQuery( document ).ready(function(){
											jQuery(".useredactor").redactor({
												 buttons: [\'bold\', \'italic\', \'underline\', \'link\', \'alignment\', \'unorderedlist\', \'orderedlist\']
											});
											jQuery(".redactor_useredactor").css("height","400px");
										  });';
						$doc->addScriptDeclaration($upload_script);
					}
					$exist_essay = TRUE;
				}
				
				$quiz_form_content .= '	</div>';
				$quiz_form_content .= '<div class="guru-quiz__status '.$answer_status.'">'.$answer_status_text.'</div>';
				$quiz_form_content .= '</div>';
				$quiz_form_content .= '</div>'; // close answers wrapper
			
				$i++;
				$added++;
			}
			
			$chances_remained = intval($result_quiz->time_quiz_taken - $time_quiz_taken_per_user);
			
			$sql = "SELECT count(*) as total from #__guru_questions_v3 where qid=".intval($result_quiz->id);
			$db->setQuery($sql);
			$total_quiz_questions = $db->loadColumn();
			$total_quiz_questions = @$total_quiz_questions["0"];
			
			if(intval($result_quiz->nb_quiz_select_up) == "0"){
				$result_quiz->nb_quiz_select_up = intval($total_quiz_questions);
			}
			
			if($pag == $nr_pages){
				$catid_req = intval(JFactory::getApplication()->input->get("catid","", "raw"));
				$module_req = intval(JFactory::getApplication()->input->get("module","", "raw"));
				$cid_req = intval(JFactory::getApplication()->input->get("cid","", "raw"));
				$quiz_form_content .='
					<input type="hidden" value="'.$result_quiz->name.'" id="quize_name" name="quize_name"/>
					<input type="hidden" value="'.$result_quiz->nb_quiz_select_up.'" id="nb_of_questions" name="nb_of_questions"/>
					<input type="hidden" value="'.$result_quiz->id.'" id="quize_id" name="quize_id"/>
					<input type="hidden" value="1" name="submit_action" id="submit_action" />
					<input type="hidden" value="'.$catid_req.'" name="catid_req" id="catid_req">
					<input type="hidden" value="'.$module_req.'" name="module_req" id="module_req">
					<input type="hidden" value="'.$cid_req.'" name="cid_req" id="cid_req">';
			}
			
			$quiz_form_content .= '</div>'; // end page
		}
		
		if($nr_pages > 1){
			$quiz_form_content .= '<div class="guru-quiz__pagination"><ul>';
			$quiz_form_content .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--start" id="pagination-start"><span>'.JText::_("GURU_START").'</span></li>';
			$quiz_form_content .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--prev" id="pagination-prev"><span>'.JText::_("GURU_PREV").'</span></li>';
			for($p=1; $p<=$nr_pages; $p++){
				if($p == 1){
					$quiz_form_content .= '<li class="guru-quiz__pagination-item" id="list_1"><span>1</span></li>';
				}
				else{
					$quiz_form_content .= '<li class="guru-quiz__pagination-item" id="list_'.$p.'">
										<a onclick="changePage('.intval($p).', '.intval($nr_pages).'); return false;" href="#">'.$p.'</a>
									 </li>';
				}
			}
			$quiz_form_content .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--next" id="pagination-next">
									<a href="#" onclick="changePage(2, '.intval($nr_pages).'); return false;">'.JText::_("GURU_NEXT").'</a>
								 </li>';
			$quiz_form_content .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--end" id="pagination-end">
									<a href="#" onclick="changePage('.intval($nr_pages).', '.intval($nr_pages).'); return false;">'.JText::_("GURU_END").'</a>
								 </li>';
			$quiz_form_content .= '</ul></div>';
		}
		$quiz_form_header = "";
		
		if(@$quiz_details["0"]["is_final"] == 0){
			$lang_quizpassed = JText::_("GURU_QUIZ_PASSED_TEXT");
			$lang_quiz = JText::_("GURU_QUIZ_FAILED_TEXT");
			$next_button_text = JText::_("GURU_COURSE_CONTINUE_COURSE");
			$more_times = JText::_("GURU_MORE_TIMES");
		}
		else{
			$lang_quizpassed = JText::_("GURU_FEXAM_PASSED_TEXT");
			$lang_quiz = JText::_("GURU_FEXAM_FAILED_TEXT");
			$next_button_text = "";
			$more_times = JText::_("GURU_MOREFE_TIMES");
		}
		
		$passed_quiz = JText::_("GURU_QUIZ_PASSED");
		$percent =  JText::_("GURU_PERCENT");
		$min_to_pass = JText::_("GURU_MIN_TO_PASS");
		$congrat =  JText::_("GURU_CONGRAT");
		$failed =  JText::_("GURU_QUIZ_FAILED");
		$take_again = JText::_("GURU_TAKE_AGAIN_QUIZ");
		$time_remain_task_quiz = JText::_("GURU_TIMES_REMAIN_TAKE_QUIZ");
		$yes = JText::_("GURU_YES");
		$yes_again = JText::_("GURU_TAKE_AGAIN_QUIZ");
		$unlimited = JText::_("GURU_UNLIMITED");
		
		$catid_req = JFactory::getApplication()->input->get("catid_req","", "raw");
		$module_req = JFactory::getApplication()->input->get("module_req","", "raw");
		$cid_req = JFactory::getApplication()->input->get("cid_req","", "raw");
		$open_target = JFactory::getApplication()->input->get("open_target","", "raw");
		$tmpl_req = JFactory::getApplication()->input->get("tmpl", "", "raw");
		$itemid_req = JFactory::getApplication()->input->get("Itemid", "", "raw");

		if($tmpl_req == "component"){
			$tmpl="&tmpl=component";
		}
		else{
			$tmpl="";
		}
		
		$catid_req = intval(JFactory::getApplication()->input->get("catid","", "raw"));
		$module_req = intval(JFactory::getApplication()->input->get("module","", "raw"));
		$cid_req = intval(JFactory::getApplication()->input->get("cid","", "raw"));
		
		if(intval($catid_req) == 0){
            $catid_req = intval(JFactory::getApplication()->input->get("catid","", "raw"));
        }

        if(intval($module_req) == 0){
            $module_req = intval(JFactory::getApplication()->input->get("module","", "raw"));
        }

        if(intval($cid_req) == 0){
            $cid_req = intval(JFactory::getApplication()->input->get("cid","", "raw"));
        }

        $lang = JFactory::getLanguage()->getTag();
        $lang = explode("-", $lang);
        $lang = @$lang["0"];

		$link_quiz = JURI::root().'index.php?option=com_guru&view=gurutasks&catid='.$catid_req.'&module='.$module_req.'&cid='.$cid_req.$tmpl."&lang=".$lang."&Itemid=".intval($itemid_req);
		
		if($result_quiz->time_quiz_taken >= 0){
			if($score >= $result_quiz->max_score){
				$quiz_form_header .= '<div class="guru-quiz__header"><ul>';
					$quiz_form_header .= '<li class="guru-quiz__header-icon">'.$your_score_text.':<span><i class="fontello-ok"></i>'.$score."%".'</span></li>';
					
					$quiz_form_header .= '<li>'.JText::_("GURU_QUIZ_STATUS").'<span>'.$lang_quizpassed.'</span></li>';

					$quiz_form_header .= '<li>'.$min_to_pass.'<span>'.$result_quiz->max_score.$percent.'</span></li>';
				$quiz_form_header .= '</ul></div>';

				$quiz_form_header .= '<h3>'.$congrat.'</h3>';

				$quiz_form_header .= '<div class="uk-alert wk-alert">'.$next_button_text.'</div>';

				if(isset($result_quiz->pass_message) && trim($result_quiz->pass_message) != ""){
					$quiz_form_header .= '<div class="guru-quiz-timer">'.trim($result_quiz->pass_message).'</div>';
				}
			}
			else{
				if(!$exist_essay){
					$sql = "select student_failed from #__guru_quiz where id=".intval($quiz_id);
					$db->setQuery($sql);
					$student_failed = $db->loadColumn();
					$student_failed = @$student_failed["0"];
					
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$registry->set('stop_next', intval($student_failed));
					
					if(intval($student_failed) == "1"){
						$sql = "select author from #__guru_program where id=".intval($course_id);
						$db->setQuery($sql);
						$authors = $db->loadColumn();
						$authors = @$authors["0"];
						$authors = explode("|", $authors);
						$authors = array_filter($authors);
						
						if(!isset($authors) || count($authors) <= 0){
							$authors = array("0"=>"0");
						}
						
						$sql = "select u.name from #__users u where u.id in (".implode(",", $authors).")";
						$db->setQuery($sql);
						$names = $db->loadColumn();
						
						if(trim($result_quiz->fail_message) == ""){
							@$result_quiz->fail_message = JText::_("GURU_STUDENT_NOT_CONTINUE_MSG")." ".implode(", ", $names)." ".JText::_("GURU_FOR_MORE_INFO");
						}
						else{
							@$result_quiz->fail_message = JText::_("GURU_STUDENT_NOT_CONTINUE_MSG")." ".implode(", ", $names)." ".JText::_("GURU_FOR_MORE_INFO").'<br/>'.$result_quiz->fail_messag;
						}
					}

					$quiz_form_header .= '<div class="guru-quiz__header"><ul>';
						$quiz_form_header .= '<li class="guru-quiz__header-icon">'.$your_score_text.':<span style="color:#F43636;"><i class="fontello-cancel"></i>'.$score."%".'</span></li>';
						
						$quiz_form_header .= '<li>'.JText::_("GURU_QUIZ_STATUS").'<span>'.$lang_quiz.'</span></li>';

						$quiz_form_header .= '<li>'.$min_to_pass.'<span>'.$result_quiz->max_score.$percent.'</span></li>';
					$quiz_form_header .= '</ul></div>';

					if(isset($result_quiz->fail_message) && trim($result_quiz->fail_message) != ""){
						$quiz_form_header .= '<div class="uk-panel uk-panel-box uk-margin-bottom wk-panel wk-panel-box wk-margin-bottom">'.trim($result_quiz->fail_message).'</div>';
					}
				}
				else{
					if(isset($result_quiz->pending_message) && trim($result_quiz->pending_message) != ""){
						$quiz_form_header .= '<div class="uk-alert uk-alert-warning wk-alert wk-alert-warning">'.$result_quiz->pending_message.'</div>';
					}
					else{
						$quiz_form_header .= '<div class="uk-alert wk-alert">';
						$quiz_form_header .= 	JText::_("GURU_REVIEW_ESSAY_ANSWER");
						$quiz_form_header .= '</div>';
					}
				}
				
				if($result_quiz->time_quiz_taken != 11){
					if($chances_remained > 0){
						if($chances_remained == 1){
							$more_times = JText::_("GURU_MORE_TIME");
						}
					
						$quiz_form_header .= '<div class="guru-quiz__retake">';
						$quiz_form_header .='<span>'.$time_remain_task_quiz." ".$chances_remained." ".$more_times.'.';
						$quiz_form_header .=' '.$yes_again.'</span>';
						$quiz_form_header .= '<div class="guru-quiz__retake-actions">';
						$quiz_form_header .='<input type="button" class="guru-quiz__btn"  onClick="window.location=\''.$link_quiz.'\'" name="yesbutton" value="'.$yes.'"/>'.'';
						$quiz_form_header .= '</div>';
						$quiz_form_header .= '</div>';
					}
				}
				else{
					$quiz_form_header .= '<div class="guru-quiz__retake">';
					$quiz_form_header .='<span>'.$time_remain_task_quiz." ".$unlimited." ".$more_times.'.';
					$quiz_form_header .=' '.$yes_again.'</span>';
					$quiz_form_header .= '<div class="guru-quiz__retake-actions">';
                    $quiz_form_header .='<input type="button" class="guru-quiz__btn"  onClick="window.location=\''.$link_quiz.'\'" name="yesbutton" value="'.$yes.'"/>'.'';
                    $quiz_form_header .= '</div>';
                    $quiz_form_header .= '</div>';
				}
				$quiz_form_header .= '</div>';
			   
            }
		}
		
		$sql = "select max(id) from #__guru_quiz_question_taken_v3";
		$db->setQuery($sql);
		$max_id1 = $db->loadColumn();
		$max_id1 = $max_id1["0"];
		
		if($count_questions_right != 0 && $max_id1 != NULL){
			$sql = "UPDATE #__guru_quiz_question_taken_v3 set count_right_answer = '".$count_questions_right."' WHERE id =".$max_id1."";
			$db->setQuery($sql);		
			$db->execute();
		}

		$quiz_form_header .= "
		<script>
			if (window.parent) {
				window.parent.jQuery('.guru-lesson').trigger('guru.updateLessonProgress', ". $cid_req .");
			}
		</script>
		";
		
		$quiz_form_content .= '</div>';

		return $quiz_form_header.$quiz_form_content;
	}

	function eliminateBlankAnswers($answers){
		$temp_array = array();
		if(isset($answers) && count($answers) > 0){
			foreach($answers as $key=>$value){
				//if(trim($value) != ""){
					$temp_array[] = $value;
				//}
			}
		}
		return $temp_array;
	}
	
	function generatePassed_Failed_quizzes($quiz_id, $course_id, $number_of_questions, $pass){
		$time_quiz_taken = "";
		$database = JFactory::getDBO();
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$date = date('Y-m-d h:i:s');
		$quiz_form_content = "";
		$resultt ="";
		$your_score_text = JText::_("GURU_YOUR_SCORE");
		$guruModelguruOrder = new guruModelguruOrder();
		$helperclass = new guruHelper();
		$configs = $this->getConfig();	
		
		$sql = "select show_correct_ans from #__guru_quiz where id=".intval($quiz_id);
		$database->setQuery($sql);
		$quiz_details = $database->loadAssocList();
		$show_correct_ans = $quiz_details["0"]["show_correct_ans"];
		
		$sql = "SELECT show_countdown, max_score, questions_per_page, time_quiz_taken, is_final, pass_message, fail_message, pending_message FROM #__guru_quiz WHERE id=".intval($quiz_id);
		$database->setQuery($sql);
		$result = $database->loadObject();

		$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,1";     
		$database->setQuery($sql);
		$result_calc = $database->loadObject();
		
		$sql = "SELECT  count(id) as time_quiz_taken_per_user FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id);     
		$database->setQuery($sql);
		$result_calct = $database->loadObject();
		$time_quiz_taken_per_user = $result_calct->time_quiz_taken_per_user;
		
		$sql = "SELECT question_ids FROM  #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,1 ";
		$database->setQuery($sql);
		$question_ids_taken_by_user = $database->loadColumn();
		$question_ids_taken_by_user =  $question_ids_taken_by_user["0"];
		
		$q  = "SELECT * FROM #__guru_questions_v3 WHERE id IN (".$question_ids_taken_by_user.")";
		$db->setQuery( $q );
		$quiz_questions = $db->loadObjectList("id");

		/* order result by quiz questions ordering */
		if(isset($question_ids_taken_by_user)){
		    $question_ids_taken_by_user_array = explode(",", $question_ids_taken_by_user);

		    if(is_array($question_ids_taken_by_user_array) && count($question_ids_taken_by_user_array) > 0){
		        $quiz_questions_temp = array();

		        foreach($question_ids_taken_by_user_array as $key=>$question_id){
		            if(isset($quiz_questions[$question_id])){
		                $quiz_questions_temp[] = $quiz_questions[$question_id];
		            }
		        }

		        $quiz_questions = $quiz_questions_temp;
		    }
		}
		/* order result by quiz questions ordering */
		
		$sql = "select question_id from #__guru_quiz_essay_mark where question_id in (".$question_ids_taken_by_user.") and user_id=".intval($user_id);
		$db->setQuery($sql);
		$mark_questions = $db->loadColumn();
		$all_essay_quiz_questions = array();
		
		if($result->time_quiz_taken < 11){
			$time_user = $result->time_quiz_taken - $time_quiz_taken_per_user;
		}
		
		@$res = $result_calc->score_quiz;
											
		$k = 0;
		$quiz_id =  intval($quiz_id);
		$score = $res;
		
		$exist_essay = FALSE;
		if(isset($quiz_questions) && count($quiz_questions) > 0){
			foreach($quiz_questions as $key=>$value){
				if($value->type == "essay"){
					$exist_essay = TRUE;
					$all_essay_quiz_questions[] = $value->id;
				}
			}
		}
		
		$array_diff = array_diff($mark_questions, $all_essay_quiz_questions);
		if(count($array_diff) == 0){
			$array_diff = array_diff($all_essay_quiz_questions, $mark_questions);
		}
		
		if(count($array_diff) == 0){
			$exist_essay = FALSE;
		}
		
		if(!$exist_essay){
			if($pass == 1){   
				@$quiz_form_content .= '<div class="guru-quiz__header"><ul>';
					$quiz_form_content .= '<li class="guru-quiz__header-icon">'.$your_score_text.':<span><i class="fontello-ok"></i>'.$score."%".'</span></li>';
					
					$quiz_form_content .= '<li>'.JText::_("GURU_QUIZ_STATUS").':<span>'.JText::_("GURU_QUIZ_PASSED_TEXT").'</span></li>';

					$quiz_form_content .= '<li>'.JText::_("GURU_MIN_TO_PASS").'<span>'.$result->max_score.JText::_("GURU_PERCENT").'</span></li>';
				$quiz_form_content .= '</ul></div>';

				$quiz_form_content .= '<h3>'.JText::_("GURU_CONGRAT").'</h3>';
				
				if($result->is_final == 1){
					$sql = "select `id_final_exam`, `certificate_term` from #__guru_program where `id`=".intval($course_id);
					$db->setQuery($sql);
					$course_certificate_details = $db->loadAssocList();
					$id_final_exam = $course_certificate_details["0"]["id_final_exam"];
					$certificate_term = $course_certificate_details["0"]["certificate_term"];
					$final_url = "";
					
					if(intval($id_final_exam) != 0 && ($certificate_term == 3 || $certificate_term == 5)){
						$Itemid_orders = JFactory::getApplication()->input->get("Itemid", "0", "raw");
						
						require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');
						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["guruorders"];
						
						if(intval($itemid_seo) > 0){
							$Itemid_orders = intval($itemid_seo);
						}
						
						$final_url = ' <a href="#" onclick="window.parent.location.href=\''.JRoute::_("index.php?option=com_guru&view=guruorders&layout=mycertificates&Itemid=".intval($Itemid_orders), false).'\';">'.JText::_("GURU_CLICK_HERE_FOR_CERTIFICATE").'</a>';
					}
				
					$quiz_form_content .= '<div class="uk-alert uk-alert-success wk-alert wk-alert-success">'.JText::_("GURU_COURSE_FINISH_FINAL_EXAM").$final_url.'</div>';
				}
				else{
					$quiz_form_content .= '<div class="uk-alert uk-alert-success wk-alert wk-alert-success">'.JText::_("GURU_COURSE_CONTINUE_COURSE").'</div>';
				}
			}
			else{
				@$quiz_form_content .= '<div class="guru-quiz__header"><ul>';
					$quiz_form_content .= '<li class="guru-quiz__header-icon">'.$your_score_text.':<span style="color:#F43636;"><i class="fontello-cancel"></i>'.$score."%".'</span></li>';
					
					$quiz_form_content .= '<li>'.JText::_("GURU_QUIZ_STATUS").':<span>'.JText::_("GURU_QUIZ_FAILED_TEXT").'</span></li>';

					$quiz_form_content .= '<li>'.JText::_("GURU_MIN_TO_PASS").'<span>'.$result->max_score.JText::_("GURU_PERCENT").'</span></li>';
				$quiz_form_content .= '</ul></div>';
			}
		}
		elseif($exist_essay){
			if(isset($result->pending_message) && trim($result->pending_message) != ""){
				$quiz_form_header .= '<div class="uk-alert uk-alert-warning wk-alert wk-alert-warning">'.$result->pending_message.'</div>';
			}
			else{
				$quiz_form_content .= '<div class="uk-alert wk-alert">';
				$quiz_form_content .= 	JText::_("GURU_REVIEW_ESSAY_ANSWER");
				$quiz_form_content .= '</div>';
			}
		}
		
		$quiz_form_content .= '<div id="the_quiz">';
		
		$per_page = $result->questions_per_page;// questions per page
		if($per_page == 0){
			$per_page = $number_of_questions;
		}
		$nr_pages = 1;// default one page
		
		if(count($quiz_questions) < $number_of_questions){
			$number_of_questions = count($quiz_questions);
		}
		
		if($number_of_questions > 0 && $number_of_questions > $per_page){
			$nr_pages = ceil($number_of_questions / $per_page);
		}
		
		for($pag = 1; $pag <= $nr_pages; $pag++){
			$k = ($pag - 1) * $per_page;
			$added = 0;

			$display = "";
			if($pag == 1){
				$display = "block";
			}
			else{
				$display = "none";
			}
			$quiz_form_content .= '<div id="quiz_page_'.$pag.'" style="display:'.$display.';">'; // start page
			
			for($i=$k; $i<intval($pag * $per_page); $i++){		
				if(!isset($quiz_questions[$i])){
					continue;
				}
				
				$question_answers_number = 0;
				$media_associated_question = json_decode($quiz_questions[$i]->media_ids);
				$media_content = "";
				$result_media = array();	
				
				$q = "SELECT * FROM #__guru_question_answers WHERE question_id=".intval($quiz_questions[$i]->id);
				$db->setQuery( $q );
				$question_answers = $db->loadObjectList();
				
				$answer_given_by_user = "SELECT question_id as question_idd, answers_given FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,".$number_of_questions."";
				$db->setQuery($answer_given_by_user);
				$answer_given_by_user = $db->loadAssocList("question_idd");
				
				$sql = "select id as answer_id from #__guru_question_answers where question_id=".intval($quiz_questions[$i]->id)." and correct_answer=1";
				$db->setQuery($sql);
				$answers_right = $db->loadAssocList("answer_id");
				
				$css_validate_class = "";
				$answer_status = '';
				$answer_status_text = '';
				
				if(isset($answer_given_by_user[$quiz_questions[$i]->id]) && isset($answers_right)){
					$css_validate_class = "question-false";
					$validate_answer = $this->validateAnswer($answers_right, $answer_given_by_user[$quiz_questions[$i]->id]);
					$answer_status = 'guru-quiz__status--false';
					$answer_status_text = '<i class="uk-icon-meh-o wk-icon-meh-o"></i>' . JText::_("GURU_ANSWER_FALSE_MESSAGE");
					
					if($quiz_questions[$i]->type == "essay"){
						$sql = "select grade from #__guru_quiz_essay_mark where user_id=".intval($user_id)." and question_id=".intval($quiz_questions[$i]->id);
						$db->setQuery($sql);
						$grade = $db->loadColumn();
						$grade = @$grade["0"];
						
						if(!isset($grade)){
							$answer_status = 'guru-quiz__status--pending';
							$answer_status_text = '<i class="uk-icon-meh-o wk-icon-meh-o"></i>' . JText::_("GURU_PENDING_ASSESSMENT")." ".intval($grade);
						}
						elseif(intval($grade) < ($quiz_questions[$i]->points / 2) ){
							$answer_status = 'guru-quiz__status--false';
							$answer_status_text = '<i class="uk-icon-meh-o wk-icon-meh-o"></i>' . JText::_("GURU_ANSWER_ESSAY_SCORE_IS")." ".intval($grade);
						}
						else{
							@$count_questions_right ++;
							$css_validate_class = "question-true";
							$answer_status = 'guru-quiz__status--correct';
							$answer_status_text = '<i class="uk-icon-smile-o wk-icon-smile-o"></i>' . JText::_("GURU_ANSWER_ESSAY_SCORE_IS")." ".intval($grade);
						}
					}
					elseif($validate_answer){
						@$count_questions_right ++;
						$css_validate_class = "question-true";
						$answer_status = 'guru-quiz__status--correct';
						$answer_status_text = '<i class="uk-icon-smile-o wk-icon-smile-o"></i>' . JText::_("GURU_ANSWER_CORRECT_MESSAGE");
					}
				}
							
				for($j=0; $j<count($media_associated_question); $j++){
					@$media_that_needs_to_be_sent = self::getMediaFromId($media_associated_question[$j]);
					
					if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
						$media_created = $helperclass->create_media_using_plugin_for_quiz($media_that_needs_to_be_sent["0"], $configs, '100%', '24', '150', 150);
						
						if($media_that_needs_to_be_sent["0"]->type == "file"){
							// do nothing
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "video"){
							if(strpos($media_created, "width") !== FALSE){
								$media_created = preg_replace('/width="(.*)"/msU', 'width="150"', $media_created);
							}
							
							if(strpos($media_created, "height") !== FALSE){
								$media_created = preg_replace('/height="(.*)"/msU', 'height="150"', $media_created);
							}
							
							$hover_div = '<div class="hover-video" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">&nbsp;</div>';
							$media_created = $hover_div.$media_created;
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "image"){
							$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_created.'</a>';
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "text"){
							$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "Article"){
							$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "url"){
							$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.$media_that_needs_to_be_sent["0"]->url.'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "audio"){
							// do nothing
						}
						elseif($media_that_needs_to_be_sent["0"]->type == "docs"){
							// do nothing
						}
					
						$result_media[] = $media_created;
					}	
				}
				
				$quiz_form_content .= '<div class="guru-quiz__question guru-question">';
				
				if($quiz_questions[$i]->type == "essay"){ //start essay question
					$quiz_form_content .= '<div class="guru-quiz__media">'.implode("", $result_media).'</div>';
					$quiz_form_content .= '		<div class="guru-quiz__question-title">';
					$quiz_form_content .= 			$quiz_questions[$i]->question_content;
					$quiz_form_content .= '		</div>';
					$quiz_form_content .= '<div class="uk-grid wk-grid">';
					$quiz_form_content .= '<div class="uk-width-large-1-1 wk-width-large-1-1">';
				}//end essay question
				else{// the rest: true/false, single, multiple
					$quiz_form_content .= '<div class="guru-quiz__media">'.implode("", $result_media).'</div>';
					$quiz_form_content .= '		<div class="guru-quiz__question-title">';
					$quiz_form_content .= 			$quiz_questions[$i]->question_content;
					$quiz_form_content .= '		</div>';
					$quiz_form_content .= '<div class="guru-quiz__answers-wrapper">';
					$quiz_form_content .= '<div class="guru-quiz__answers uk-grid uk-grid-small wk-grid wk-grid-small" data-uk-grid-match data-uk-grid-margin>';
				}
				
				if($quiz_questions[$i]->type == "true_false"){
					
					foreach($question_answers as $question_answer){
						if(isset($answer_given_by_user[$question_answer->question_id]["answers_given"]) && $answer_given_by_user[$question_answer->question_id]["answers_given"] == $question_answer->id){
							$checked = 'checked="checked"';
							
							if($show_correct_ans){
								$answer_checked = 'guru-quiz__answer--checked ';
							}
							else{
								$checked = '';
							}
						}
						else{
							$checked = '';
							$answer_checked = '';
						}
						
						$correct_class = "";
						$border_correct_class = "";
						
						if($question_answer->correct_answer == 1){
							$correct_class = "correct-answer";
							
							if($show_correct_ans){
								$border_correct_class = "guru-quiz__answer--correct";
							}
						}
						
						$quiz_form_content .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3">
													<div class="guru-quiz__answer '.$answer_checked . $border_correct_class.'">
														<div class="uk-float-left wk-float-left">
															<input type="radio" '.$checked.' id="ans'.$question_answer->question_id.intval($question_answer->id).'" name="truefs_ans['.intval($question_answer->question_id).']" value="'.$question_answer->id.'" />
															<label for="ans'.$question_answer->question_id.intval($question_answer->id).'" class="guru-quiz__check-box"></label>
											 			</div>
											 			<div class="uk-float-left wk-float-left '.$correct_class.'">
															'.$question_answer->answer_content_text.'
											 			</div>
											 			<span class="answer-check"><i class="fontello-ok"></i><i class="fontello-cancel"></i></span>
										 			</div>
											   </div>';
					}
					
				}
				elseif($quiz_questions[$i]->type == "single"){
					if(isset($question_answers) && count($question_answers) > 0){
						
						foreach($question_answers as $question_answer){
							$media_associated_answers = json_decode($question_answer->media_ids);
							$media_content = "";
							$result_media_answers = array();
							
							if(isset($media_associated_answers) && count($media_associated_answers) > 0){
								foreach($media_associated_answers as $media_key=>$answer_media_id){
									$media_that_needs_to_be_sent = self::getMediaFromId($answer_media_id);
									
									$media_created = $helperclass->create_media_using_plugin_for_quiz($media_that_needs_to_be_sent["0"], $configs, '100%', '24', '150', 150);
									$media_created = preg_replace('/height="(.*)"/msU', 'height="100%"', $media_created);
									
									if($media_that_needs_to_be_sent["0"]->type == "file"){
										// do nothing
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "video"){
										if(strpos($media_created, "width") !== FALSE){
											$media_created = preg_replace('/width="(.*)"/msU', 'width="150"', $media_created);
										}
										
										if(strpos($media_created, "height") !== FALSE){
											$media_created = preg_replace('/height="(.*)"/msU', 'height="150"', $media_created);
										}
										
										$hover_div = '<div class="hover-video" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">&nbsp;</div>';
										$media_created = $hover_div.$media_created;
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "image"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_created.'</a>';
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "text"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "Article"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "url"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.$media_that_needs_to_be_sent["0"]->url.'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "audio"){
										// do nothing
									}
									elseif($media_that_needs_to_be_sent["0"]->type == "docs"){
										// do nothing
									}
								
									$result_media_answers[] = $media_created;
								}
							}
							if(isset($answer_given_by_user[$question_answer->question_id]["answers_given"]) && $answer_given_by_user[$question_answer->question_id]["answers_given"] == $question_answer->id){
								$checked = 'checked="checked"';
								
								if($show_correct_ans){
									$answer_checked = 'guru-quiz__answer--checked ';
								}
								else{
									$checked = '';
								}
							}
							else{
								$checked = '';
								$answer_checked = '';
							}
							
							$correct_class = "";
							$border_correct_class = "";
							
							if($question_answer->correct_answer == 1){
								$correct_class = "correct-answer";
								
								if($show_correct_ans){
									$border_correct_class = "guru-quiz__answer--correct";
								}
							}
							
							$option_value = '<input type="radio" '.$checked.' id="ans'.$question_answer->question_id.intval($question_answer->id).'" name="answers_single['.intval($quiz_questions[$i]->id).']" value="'.$question_answer->id.'"/><label for="ans'.$question_answer->question_id.intval($question_answer->id).'" class="guru-quiz__check-box"></label>&nbsp;<span class="'.$correct_class.'">'.$question_answer->answer_content_text.'</span><div class="guru-quiz__answer-media">'.implode("",$result_media_answers).'</div>';
							
							$quiz_form_content .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3"><div class="guru-quiz__answer '.$answer_checked . $border_correct_class.'">'.$option_value.'<span class="answer-check"><i class="fontello-ok"></i><i class="fontello-cancel"></i></span></div></div>';
						}
						
					}						
				}
				elseif($quiz_questions[$i]->type == "multiple"){
					if(isset($question_answers) && count($question_answers) > 0){
						
						foreach($question_answers as $question_answer){
							$media_associated_answers = json_decode($question_answer->media_ids);
							$media_content = "";
							$result_media_answers = array();
							
							if(isset($media_associated_answers) && count($media_associated_answers) > 0){
								foreach($media_associated_answers as $media_key=>$answer_media_id){
									$media_that_needs_to_be_sent = self::getMediaFromId($answer_media_id);
									
									$media_created = $helperclass->create_media_using_plugin_for_quiz(@$media_that_needs_to_be_sent["0"], $configs, '100%', '24', '150', 150);
									$media_created = preg_replace('/height="(.*)"/msU', 'height="100%"', $media_created);
									
									if(@$media_that_needs_to_be_sent["0"]->type == "file"){
										// do nothing
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "video"){
										if(strpos($media_created, "width") !== FALSE){
											$media_created = preg_replace('/width="(.*)"/msU', 'width="150"', $media_created);
										}
										
										if(strpos($media_created, "height") !== FALSE){
											$media_created = preg_replace('/height="(.*)"/msU', 'height="150"', $media_created);
										}
										
										$hover_div = '<div class="hover-video" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">&nbsp;</div>';
										$media_created = $hover_div.$media_created;
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "image"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_created.'</a>';
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "text"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "Article"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.JURI::root()."index.php?option=com_guru&view=gurutasks&task=preview&id=".intval($media_that_needs_to_be_sent["0"]->id)."&tmpl=component".'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "url"){
										$media_created = '<a href="#" onclick="javascript:openMyModal(0, 0, \''.$media_that_needs_to_be_sent["0"]->url.'\'); return false;">'.$media_that_needs_to_be_sent["0"]->name.'</a>';
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "audio"){
										// do nothing
									}
									elseif(@$media_that_needs_to_be_sent["0"]->type == "docs"){
										// do nothing
									}
								
									$result_media_answers[] = $media_created;
								}
							}
							
							$multiple_ans_given = explode(",", @$answer_given_by_user[$question_answer->question_id]["answers_given"]);
							$checked = '';
							$answer_checked = '';
							
							if(in_array($question_answer->id, $multiple_ans_given)){
								$checked = 'checked="checked"';
								
								if($show_correct_ans){
									$answer_checked = 'guru-quiz__answer--checked ';
								}
								else{
									$checked = '';
								}
							}
							
							$correct_class = "";
							$border_correct_class = "";
							
							if($question_answer->correct_answer == 1){
								$correct_class = "correct-answer";
								
								if($show_correct_ans){
									$border_correct_class = "guru-quiz__answer--correct";
								}
							}
							
							$option_value = '<input type="checkbox" '.$checked.' id="'.$question_answer->id.'" name="multiple_ans['.intval($quiz_questions[$i]->id).'][]" value="'.$question_answer->id.'"/><label for="'.$question_answer->id.'" class="guru-quiz__check-box"></label>&nbsp;<span class="'.$correct_class.'">'.$question_answer->answer_content_text.'</span><div class="guru-quiz__answer-media">'.implode("",$result_media_answers).'</div>';
							
							$quiz_form_content .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3"><div class="guru-quiz__answer '.$answer_checked . $border_correct_class.'">'.$option_value.'<span class="answer-check"><i class="fontello-ok"></i><i class="fontello-cancel"></i></span></div></div>';
						}
						
					}		
				}
				elseif($quiz_questions[$i]->type == "essay"){
					$sql = "select max(id) from #__guru_quiz_question_taken_v3 where user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id);
					$db->setQuery($sql);
					$id_question_taken = $db->loadColumn();
					$id_question_taken = $id_question_taken["0"];
					
					$q = "SELECT * FROM #__guru_quiz_taken_v3 WHERE id_question_taken = ".intval($id_question_taken)." and question_id=".intval($quiz_questions[$i]->id);
					$db->setQuery($q);
					$essay_answers = $db->loadObjectList();
					
					$sql = "select feedback_quiz_results from #__guru_quiz_essay_mark where user_id=".intval($user_id)." and question_id=".intval($quiz_questions[$i]->id);
					$db->setQuery($sql);
					$feedback_quiz_results = $db->loadColumn();
					$feedback_quiz_results = @$feedback_quiz_results["0"];
					
					if(isset($essay_answers) && count($essay_answers) > 0){
						$quiz_form_content .= '<div class="uk-panel uk-panel-box uk-panel-box-secondary wk-panel wk-panel-box wk-panel-box-secondary">';
						$quiz_form_content .= 	$essay_answers["0"]->answers_given;
						$quiz_form_content .= '</div>';
						
						if(trim($feedback_quiz_results) != ""){
							$quiz_form_content .= '<div class="teacher-feedback">';
							$quiz_form_content .= 	trim($feedback_quiz_results);
							$quiz_form_content .= '</div>';
						}
					}
				}
				
				$quiz_form_content .= '		</div>';
				$quiz_form_content .= '<div class="guru-quiz__status '.$answer_status.'">'.$answer_status_text.'</div>';
				$quiz_form_content .= '</div></div>';
				$quiz_form_content .= '</div>'; // close answers wrapper
			
				$added++;
			}
			$quiz_form_content .= '</div>'; // end page
		}
		
		if($nr_pages > 1){
			$quiz_form_content .= '<div class="guru-quiz__pagination-item"><ul>';
			$quiz_form_content .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--start" id="pagination-start"><span>'.JText::_("GURU_START").'</span></li>';
			$quiz_form_content .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--prev" id="pagination-prev"><span>'.JText::_("GURU_PREV").'</span></li>';
			for($p=1; $p<=$nr_pages; $p++){
				if($p == 1){
					$quiz_form_content .= '<li class="guru-quiz__pagination-item" id="list_1"><span>1</span></li>';
				}
				else{
					$quiz_form_content .= '<li class="guru-quiz__pagination-item" id="list_'.$p.'">
										<a onclick="changePage('.intval($p).', '.intval($nr_pages).'); return false;" href="#">'.$p.'</a>
									 </li>';
				}
			}
			$quiz_form_content .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--next" id="pagination-next">
									<a href="#" onclick="changePage(2, '.intval($nr_pages).'); return false;">'.JText::_("GURU_NEXT").'</a>
								 </li>';
			$quiz_form_content .= 	'<li class="guru-quiz__pagination-item guru-quiz__pagination-item--end" id="pagination-end">
									<a href="#" onclick="changePage('.intval($nr_pages).', '.intval($nr_pages).'); return false;">'.JText::_("GURU_END").'</a>
								 </li>';
			$quiz_form_content .= '</ul></div>';
		}
		
		
		$quiz_form_content .= '</div>';
		$quiz_form_header = "";
		
		if(@$result->time_quiz_taken >= 0){
			$lang_quizpassed = JText::_("GURU_QUIZ_PASSED_TEXT");
			$lang_quiz = JText::_("GURU_QUIZ_FAILED_TEXT");
			$next_button_text = JText::_("GURU_COURSE_CONTINUE_COURSE");
			$more_times = JText::_("GURU_MORE_TIMES");
		}
		else{
			$lang_quizpassed = JText::_("GURU_FEXAM_PASSED_TEXT");
			$lang_quiz = JText::_("GURU_FEXAM_FAILED_TEXT");
			$next_button_text = "";
			$more_times = JText::_("GURU_MOREFE_TIMES");
		}
		$passed_quiz = JText::_("GURU_QUIZ_PASSED");
		$percent =  JText::_("GURU_PERCENT");
		$min_to_pass = JText::_("GURU_MIN_TO_PASS");
		$congrat =  JText::_("GURU_CONGRAT");
		$failed =  JText::_("GURU_QUIZ_FAILED");
		$take_again = JText::_("GURU_TAKE_AGAIN_QUIZ");
		$time_remain_task_quiz = JText::_("GURU_TIMES_REMAIN_TAKE_QUIZ");
		$yes = JText::_("GURU_YES");
		$yes_again = JText::_("GURU_TAKE_AGAIN_QUIZ");
		$unlimited = JText::_("GURU_UNLIMITED");
		
		$catid_req = JFactory::getApplication()->input->get("catid_req","", "raw");
		$module_req = JFactory::getApplication()->input->get("module_req","", "raw");
		$cid_req = JFactory::getApplication()->input->get("cid_req","", "raw");
		$open_target = JFactory::getApplication()->input->get("open_target","", "raw");
		if($open_target == 1){
			$tmpl="&tmpl=component";
		}
		else{
			$tmpl="";
		}
		$link_quiz = JRoute::_('index.php?option=com_guru&view=gurutasks&catid='.$catid_req.'&module='.$module_req.'&cid='.$cid_req.$tmpl.'&Itemid=');
		$chances_remained = intval($result->time_quiz_taken - $time_quiz_taken_per_user);
		if($result->time_quiz_taken >= 0){
		/*	if($score >= $result->max_score){
				if(!$exist_essay){
					$quiz_form_header .= '<span class="guru_quiz_score">'.$your_score_text.':'.$score."%".'<span style="color:#292522;">'.$passed_quiz.'</span></span>';
					if(isset($result->pass_message) && trim($result->pass_message) != ""){
						$quiz_form_header .= '<div class="guru-quiz-timer">'.$result->pass_message.'</div>';
					}
				}
			}
			else{
				if(!$exist_essay){
					$quiz_form_header .= '<span class="guru_quiz_score">'.$your_score_text.':'.$score.$percent.'<span style="color:#292522;">'.$failed.'</span></span>';
					
					if(isset($result->fail_message) && trim($result->fail_message) != ""){
						$quiz_form_header .= '<div class="guru-quiz-timer">'.$result->fail_message.'</div>';
					}
				}
				else{
					if(isset($result->pending_message) && trim($result->pending_message) != ""){
						$quiz_form_header .= '<div class="guru-quiz-timer">'.$result->pending_message.'</div>';
					}
				}
				
				if($result->time_quiz_taken < 11){
					if($chances_remained > 0){
						$quiz_form_header .='<br/><span>'.$time_remain_task_quiz.'<span style="color:#669900;">'." ".$chances_remained." ".'</span>'.$more_times.'</span>';
						$quiz_form_header .='<br/></br><span>'.$yes_again.'</span>';					
                    	$quiz_form_header .='<br/><br/><input type="button" class="uk-button uk-button-success"  onClick="window.location=\''.$link_quiz.'\'" name="yesbutton" value="'.$yes.'"/>'.'&nbsp;&nbsp;';
					}
				}
				else{
					$quiz_form_header .='<br/><span>'.$time_remain_task_quiz.'<span style="color:#669900;">'." ".$unlimited.'</span>'." ".$more_times.'</span>';
					$quiz_form_header .='<br/></br><span>'.$yes_again.'</span>';
                    $quiz_form_header .='<br/><br/><input type="button" class="uk-button uk-button-success"  onClick="window.location=\''.$link_quiz.'\'" name="yesbutton" value="'.$yes.'"/>'.'&nbsp;&nbsp;';
				}
			   
            }*/
		}

		$quiz_form_header .= '
			<script>
				window.parent.reloadCourseLessonsBar();
			</script>
		';

		return $quiz_form_header.$quiz_form_content;
	}
	
	public static function getMediaFromId($id_media){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_media WHERE id =".intval($id_media);
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		return $result;
	}
	
	public static function validateAnswer($answers_right, $answer_given){
		$return = FALSE;
		$temp_answers_right = array();
		
		if(isset($answers_right) && count($answers_right) > 0){
			foreach($answers_right as $key=>$value){
				$temp_answers_right[] = $key;
			}
		}
		
		if(isset($answer_given) && count($answer_given) > 0 && isset($answer_given["answers_given"])){
			$answer_given = $answer_given["answers_given"];
			$answer_given = explode(",", $answer_given);
		}
		
		$diff_1 = array_diff($temp_answers_right, $answer_given);
		$diff_2 = array_diff($answer_given, $temp_answers_right);
		
		if((is_array($diff_1) && count($diff_1) == 0) && (is_array($diff_2) && count($diff_2) == 0)){
			$return = TRUE;
		}
		return $return;
	}
	
	function store_quiz_results(){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$quiz_id = JFactory::getApplication()->input->get("quize_id", 0, "raw");
		$pid = JFactory::getApplication()->input->get("pid", 0, "raw");
		$question_ids = array();
		$answers_single = JFactory::getApplication()->input->get("answers_single", array(), "raw");
		$truefs_ans = JFactory::getApplication()->input->get("truefs_ans", array(), "raw");
		$multiple_ans = JFactory::getApplication()->input->get("multiple_ans", array(), "raw");
		$essay_ans = JFactory::getApplication()->input->get("essay", array(), "raw");
		$all_question_from_page = JFactory::getApplication()->input->get("all_questions_ids", "", "raw");
		$config = JFactory::getConfig();
		$offset = JFactory::getApplication()->getCfg('offset');
		
		$jnow = new JDate('now');
		//$date = $jnow->toSQL(true);

		$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
		$jnow = new JDate('now');
		$jnow->setTimezone($timezone);
		$date = $jnow->toSQL(true);

		// start insert answers in database -----------------------------------------
		if(isset($answers_single) && count($answers_single) > 0){
			foreach($answers_single as $question_id=>$value_id){
				$question_ids[] = $question_id;
			}
		}
		
		if(isset($truefs_ans) && count($truefs_ans) > 0){
			foreach($truefs_ans as $question_id=>$value_id){
				$question_ids[] = $question_id;
			}
		}
		
		if(isset($multiple_ans) && count($multiple_ans) > 0){
			foreach($multiple_ans as $question_id=>$values){
				$values_string = "";
				$question_ids[] = $question_id;
			}
		}
		// stop insert answers in database -----------------------------------------
		
		$points_array = $this->getTotalPoints($question_ids);
		$summary_points = 0;
		$total_points = 0;
		
		// start calculate points -----------------------------------------
		if(isset($answers_single) && count($answers_single) > 0){
			foreach($answers_single as $question_id=>$value_id){
				//$total_points += $points_array[$question_id]["points"];
				if($points_array[$question_id]["answers"][$value_id]["correct_answer"] == "1"){
					$summary_points += $points_array[$question_id]["points"];
				}
			}
		}
		
		if(isset($truefs_ans) && count($truefs_ans) > 0){
			foreach($truefs_ans as $question_id=>$value_id){
				//$total_points += $points_array[$question_id]["points"];
				if(@$points_array[$question_id]["answers"][$value_id]["correct_answer"] == "1"){
					$summary_points += $points_array[$question_id]["points"];
				}
			}
		}
		
		if(isset($multiple_ans) && count($multiple_ans) > 0){
			foreach($multiple_ans as $question_id=>$values){
				//$total_points += $points_array[$question_id]["points"];
				$temp_correct_ans = $points_array[$question_id]["answers"];
				$temp_values = array();
				
				if(isset($temp_correct_ans) && count($temp_correct_ans) > 0){
					foreach($temp_correct_ans as $ans_id=>$temp_val){
						if($temp_val["correct_answer"] == "1"){
							$temp_values[] = $temp_val["answer_id"];
						}
					}
				}
				
				$dif1 = array_diff($temp_values, $values);
				$dif2 = array_diff($values, $temp_values);
				
				if(count($dif1) == 0 && count($dif2) == 0){
					$summary_points += $points_array[$question_id]["points"];
				}
			}
		}
		// stop calculate points -----------------------------------------
		
		
		// start to check if some questions don't have answer
		$all_question_from_page_exploded = explode(",", $all_question_from_page);
		
		$sql = "SELECT SUM(points) FROM #__guru_questions_v3 WHERE id IN (".$all_question_from_page.")";
		$db->setQuery($sql);
		$total_points = $db->loadColumn();
		$total_points = $total_points["0"];
		
		// stop to check if some questions don't have answer		
		$score_quiz = 0;
		if(intval($total_points) > 0){
			$score_quiz = ($summary_points * 100) / $total_points;
			$score_quiz = number_format($score_quiz, 2, ".", ",");
		}
		
		$autosubmit = JFactory::getApplication()->input->get("autosubmit", "0", "raw");
		
		$sql = "INSERT INTO #__guru_quiz_question_taken_v3(user_id, question_ids, quiz_id, score_quiz, pid, date_taken_quiz, points, failed, count_right_answer) VALUES ('".intval($user_id)."', '".$all_question_from_page."', '".intval($quiz_id)."', '".$score_quiz."', '".intval($pid)."', '".$date."', '".intval($summary_points)."', '".intval($autosubmit)."', 0)";
		$db->setQuery($sql);
		$max_id = 0;
		if($db->execute()){
			$sql = "select max(id) from #__guru_quiz_question_taken_v3 where user_id='".intval($user_id)."' and quiz_id='".intval($quiz_id)."' and pid='".intval($pid)."'";
			$db->setQuery($sql);
			$max_id = $db->loadColumn();
			$max_id = intval($max_id["0"]);
		}
		
		// start insert answers in database -----------------------------------------
		if(isset($answers_single) && count($answers_single) > 0){
			foreach($answers_single as $question_id=>$value_id){
				$question_ids[] = $question_id;
				
				$sql = "INSERT INTO #__guru_quiz_taken_v3(user_id, quiz_id, question_id, answers_given, pid, id_question_taken) VALUES ('".intval($user_id)."', '".intval($quiz_id)."', '".intval($question_id)."', '".intval($value_id)."', '".intval($pid)."', '".intval($max_id)."')";
				$db->setQuery($sql);
				$db->execute();
			}
		}
		
		if(isset($truefs_ans) && count($truefs_ans) > 0){
			foreach($truefs_ans as $question_id=>$value_id){
				$question_ids[] = $question_id;
				
				$sql = "INSERT INTO #__guru_quiz_taken_v3(user_id, quiz_id, question_id, answers_given, pid, id_question_taken) VALUES ('".intval($user_id)."', '".intval($quiz_id)."', '".intval($question_id)."', '".intval($value_id)."', '".intval($pid)."', '".intval($max_id)."')";
				$db->setQuery($sql);
				$db->execute();
			}
		}
		
		if(isset($multiple_ans) && count($multiple_ans) > 0){
			foreach($multiple_ans as $question_id=>$values){
				$values_string = "";
				$question_ids[] = $question_id;
				
				if(isset($values) && count($values) > 0){
					$values_string = implode(",", $values);
				}
				
				$sql = "INSERT INTO #__guru_quiz_taken_v3(user_id, quiz_id, question_id, answers_given, pid, id_question_taken) VALUES ('".intval($user_id)."', '".intval($quiz_id)."', '".intval($question_id)."', '" . $db->escape(trim($values_string)) . "', '".intval($pid)."', '".intval($max_id)."')";
				$db->setQuery($sql);
				$db->execute();
			}
		}

		if(isset($essay_ans) && count($essay_ans)){
			foreach($essay_ans as $question_id=>$values){
				$values_string = "";
				$question_ids[] = $question_id;
				
				$sql = "INSERT INTO #__guru_quiz_taken_v3(user_id, quiz_id, question_id, answers_given, pid, id_question_taken) VALUES ('".intval($user_id)."', '".intval($quiz_id)."', '".intval($question_id)."', '" . $db->escape(trim($values)) . "', '".intval($pid)."', '".intval($max_id)."')";
				$db->setQuery($sql);
				if($db->execute()){
					$sql = "delete from #__guru_quiz_essay_mark where `user_id`=".intval($user_id)." and `question_id`=".intval($question_id);
					$db->setQuery($sql);
					$db->execute();
				}
			}
			$this->sendEmailToTeacher();
		}
		// stop insert answers in database -----------------------------------------

		$result_diff = array_diff($all_question_from_page_exploded, $question_ids);
		foreach($result_diff as $key=>$value_id){
			$sql = "INSERT INTO #__guru_quiz_taken_v3(user_id, quiz_id, question_id, answers_given, pid, id_question_taken) VALUES ('".intval($user_id)."', '".intval($quiz_id)."', '".intval($value_id)."', '', '".intval($pid)."', '".intval($max_id)."')";
			$db->setQuery($sql);
			$db->execute();
		}
		
		$lesson_id = JFactory::getApplication()->input->get('cid', '0', "raw");
		$pid = JFactory::getApplication()->input->get('pid', '0', "raw");
		$module_id = JFactory::getApplication()->input->get('module', '0', "raw");

		$sql = "select `lesson_view_confirm` from #__guru_program where `id`=".intval($pid);
		$db->setQuery($sql);
		$lesson_view_confirm = $db->loadColumn();
		$lesson_view_confirm = @$lesson_view_confirm["0"];

		if(intval($lesson_view_confirm) == 1){
			$this->saveLessonViewed(intval($lesson_id), intval($pid), intval($module_id));
		}
		
		return true;
	}
	
	function sendEmailToTeacher(){
		$course_id = JFactory::getApplication()->input->get("pid", "0", "raw");
		$db = JFactory::getDbo();
		
		$user = JFactory::getUser();
		$user_name = $user->name;
		
		$sql = "select template_emails, fromname, fromemail,admin_email from #__guru_config";
		$db->setQuery($sql);
		$confic = $db->loadAssocList();
		$template_emails = $confic["0"]["template_emails"];
		$template_emails = json_decode($template_emails, true);
		$fromname = $confic["0"]["fromname"];
		$fromemail = $confic["0"]["fromemail"];
		
		$app = JFactory::getApplication();
		$site_name = $app->getCfg('sitename');
		
		$app = JFactory::getApplication();
		$site_name = $app->getCfg('sitename');
		$quize_name = JFactory::getApplication()->input->get("quize_name", "", "raw");
		
		$subject = $template_emails["review_quiz_subject"];
		$body = $template_emails["review_quiz_body"];
		
		$sql = "select author from #__guru_program where id=".intval($course_id);
		$db->setQuery($sql);
		$authors = $db->loadColumn();
		$authors = @$authors["0"];
		$authors = explode("|", $authors);
		$authors = array_filter($authors);
		
		if(isset($authors) && count($authors) > 0){
			foreach($authors as $key=>$author){
				if(intval($author) == 0){
					continue;
				}

				$subject = $template_emails["review_quiz_subject"];
				$body = $template_emails["review_quiz_body"];
				
				$sql = "select p.*, u.name as username from #__guru_program p, #__users u where u.id=".intval($author)." and p.id=".intval($course_id);
				$db->setQuery($sql);
				$result = $db->loadAssocList();
				
				$sql = "select email from #__users where id=".intval($author);
				$db->setQuery($sql);
				$teacher_email = $db->loadColumn();
				$teacher_email = @$teacher_email["0"];
				
				$link_to_quiz = '<a href="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents", true, -1).'" target="_blank">'.JText::_("GURU_QUIZ_RESULT").'</a>';
				
				$subject = str_replace("[AUTHOR_NAME]", $result["0"]["username"], $subject);
				$subject = str_replace("[COURSE_NAME]", $result["0"]["name"], $subject);
				$subject = str_replace("[SITE_NAME]", $site_name, $subject);
				$subject = str_replace("[QUIZ_NAME]", $quize_name, $subject);
				$subject = str_replace("[LINK_TO_QUIZ_RESULT]", $link_to_quiz, $subject);
				$subject = str_replace("[STUDENT_NAME]", $user_name, $subject);
				
				$body = str_replace("[AUTHOR_NAME]", $result["0"]["username"], $body);
				$body = str_replace("[COURSE_NAME]", $result["0"]["name"], $body);
				$body = str_replace("[SITE_NAME]", $site_name, $body);
				$body = str_replace("[QUIZ_NAME]", $quize_name, $body);
				$body = str_replace("[LINK_TO_QUIZ_RESULT]", $link_to_quiz, $body);
				$body = str_replace("[STUDENT_NAME]", $user_name, $body);
				
				$send_teacher_email_review_quiz = isset($template_emails["send_teacher_email_review_quiz"]) ? $template_emails["send_teacher_email_review_quiz"] : 1;

				if($send_teacher_email_review_quiz){
					JFactory::getMailer()->sendMail($fromemail, $fromname, $teacher_email, $subject, $body, 1);
				}
				
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->clear();
				$query->insert('#__guru_logs');
				$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type') ));
				$query->values(intval($author) . ',' . $db->quote('teacher-mark-essay') . ',' . '0' . ',' . $db->quote(trim($teacher_email)) . ',' . $db->quote(trim($subject)) . ',' . $db->quote(trim($body)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''" );
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
	
	function getTotalPoints($question_ids){
		$question_ids_list = implode("," ,$question_ids);
		if($question_ids_list == ""){
			$question_ids_list = "0";
		}
		$db = JFactory::getDBO();
		$sql = "select id, points from #__guru_questions_v3 where id in (".$question_ids_list.")";
		$db->setQuery($sql);
		$result = $db->loadAssocList("id");
		
		if(isset($result) && count($result) > 0){
			foreach($result as $question_id=>$value){
				$sql = "select id as answer_id, correct_answer from #__guru_question_answers where question_id=".intval($question_id);
				$db->setQuery($sql);
				$answers = $db->loadAssocList("answer_id");
				$result[$question_id]["answers"] = $answers;
			}
		}
		
		return $result;
	}
	
	function getMediaType($id){
		$db = JFactory::getDBO();
		$sql = "select type from #__guru_media where id=".intval($id);
		$db->setQuery($sql);
		$type = $db->loadColumn();
		return @$type["0"];
	}
	
	function getKunenaSettings(){
		$db = JFactory::getDbo();
		
		$sql = "select * from #__guru_kunena_forum";
		$db->setQuery($sql);
		$settings = $db->loadAssoc();

		$isKunenaEnable = JComponentHelper::isEnabled('com_kunena');
		$settings['allow_stud'] = $isKunenaEnable ? '0' : '1';

		return $settings;
	}
};
?>