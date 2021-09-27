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


class guruAdminModelguruTask extends JModelLegacy {
	var $_attributes;
	var $_attribute;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct () {
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");
		$this->setId((int)$cids[0]);
		global $app, $option;
		
		$app = JFactory::getApplication('administrator');
		$limit = $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart = $app->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		if(JFactory::getApplication()->input->get("limitstart") == JFactory::getApplication()->input->get("old_limit")){
			JFactory::getApplication()->input->set("limitstart", "0");		
			$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
			$limitstart = $app->getUserStateFromRequest($option.'limitstart', 'limitstart', 0, 'int');
		}

		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_attribute = null;
	}
	
	public static function getMediaCategoriesName(){
		$db = JFactory::getDBO();
		$sql = "select id, name from #__guru_media_categories";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList("id");
		return $result;
	}
	
	public static function getMediaType($id){
		$db = JFactory::getDBO();
		$sql = "select type from #__guru_media where id=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadResult();
		return $result;
	}	
	
	public static function getAllMediaCategs(){
		$db = JFactory::getDBO();
		$sql = "select id, name from #__guru_media_categories";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
	
	public static function jump_save(){
		$data = JFactory::getApplication()->input->post->getArray();

		$db = JFactory::getDBO();
		$pieces=explode("|",$data['selstep']);
		$module_id = $data["jump_mod_id"];
		
		$type_selected = JFactory::getApplication()->input->get("type_selected", "");
		
		if(isset($data['editid'])&&($data['editid']!=0)) {
			$sql="UPDATE #__guru_jump SET text = '".trim(addslashes($data['jumptext']))."',jump_step='".$pieces["0"]."', module_id1=".intval($module_id).", type_selected='".trim($type_selected)."' WHERE id = ".$data['editid']." LIMIT 1 ;";
			$db->setQuery($sql);
			$db->execute();
			$ret[]=$data['editid'];
		} 
		else{
			$sql="INSERT INTO #__guru_jump (button ,text ,jump_step, module_id1, type_selected)
				VALUES ('".$pieces[1]."', '".trim(addslashes($data['jumptext']))."', '".$pieces[0]."', ".intval($module_id).", '".trim($type_selected)."');";
			$db->setQuery($sql);
			$db->execute();
			if(!isset($last_id)||($last_id==0)){
				$sql="SELECT id FROM #__guru_jump ORDER BY id DESC LIMIT 1";
				$db->setQuery($sql);
				$last_id=$db->loadResult($sql);
			}
			$ret[]=$last_id;
		}
		$ret[]=$pieces[1];
		$ret[]=$data['jumptext'];
		
		return $ret;
	}
	
	public static function saveorder_q(){
		$db = JFactory::getDBO();
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		foreach($data_post['order_q'] as $key=>$value){
			$sql="UPDATE #__guru_questions_v3 SET reorder = '".$value."' WHERE id ='".$key."' LIMIT 1 ;";
			$db->setQuery($sql);
			$db->execute();
		}
	}
	
	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getlistQuiz();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	function getlistQuiz(){
		$db = JFactory::getDBO();
		$app = JFactory::getApplication('administrator');
		
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
		$limit_cond = "";
		
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		}

		$search_text = JFactory::getApplication()->input->get('search_quiz', "");
		$and = "";
		if($search_text!=""){
			$and ="AND name like '%".$search_text."%' ";
		}
		
		$sql = "SELECT count(*) FROM  #__guru_quiz WHERE is_final<>1 ".$and." GROUP BY id ORDER BY ordering";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		$result = @$result["0"];
		$this->_total = intval($result);
		
		$sql = "SELECT * FROM #__guru_quiz WHERE is_final<>1 ".$and." ORDER BY `id` DESC ".$limit_cond;
		$db->setQuery($sql);
		$db->execute();
		
		return $db->loadObjectList();
	}

	function getlistProjects(){
		$db = JFactory::getDBO();
		$app = JFactory::getApplication('administrator');
		
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
		$limit_cond = "";
		
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		}

		$search_text = JFactory::getApplication()->input->get('search_project', "");
		$and = "";
		if($search_text!=""){
			$and ="AND title like '%".$search_text."%' ";
		}
		
		$sql = "SELECT count(*) FROM  #__guru_projects WHERE published=1 ".$and;
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		$result = @$result["0"];
		$this->_total = intval($result);
		
		$sql = "SELECT * FROM #__guru_projects WHERE published=1 ".$and.$limit_cond;
		$db->setQuery($sql);
		$db->execute();
		
		return $db->loadObjectList();
	}

	function getlistScorms(){
		$db = JFactory::getDBO();
		$app = JFactory::getApplication('administrator');
		
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
		$limit_cond = "";
		
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		}

		$search_text = JFactory::getApplication()->input->get('search_scorm', "");
		$and = "";

		if($search_text!=""){
			$and = "AND title like '%".$search_text."%' ";
		}

		$pid = JFactory::getApplication()->input->get('pid', "0", "raw");

		if(intval($pid) > 0){
			$and .= " AND `course_id`=".intval($pid);
		}
		
		$sql = "SELECT count(*) FROM  #__guru_scorm_items WHERE published=1 ".$and;
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		$result = @$result["0"];
		$this->_total = intval($result);
		
		$sql = "SELECT * FROM #__guru_scorm_items WHERE published=1 ".$and.$limit_cond;
		$db->setQuery($sql);
		$db->execute();
		
		return $db->loadObjectList();
	}
	
	function getlistaddmedia(){
		$db = JFactory::getDbo();
		$type = JFactory::getApplication()->input->get("type", "", "raw");
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		$task = JFactory::getApplication()->input->get("task", "", "raw");
 		$condition=array();
		
		$sql = "SELECT m.*, mc.name as categ_name FROM #__guru_media m LEFT OUTER JOIN #__guru_media_categories mc on mc.id=m.category_id where 1=1 ";
		if($type!=""){
			$sql .="AND m.type='".$type."' ";
		}

		if($task != 'addtext'){
			$search_text = JFactory::getApplication()->input->get('search_text', "null", "raw");
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');

			if($search_text == "null"){
				$search_value = $registry->get('search_value', "");
				
				if(isset($search_value) && trim($search_value) != ""){
					$search_text =$search_value;
				}
			}

			if($search_text != "null" && $search_text != ""){
				$sql = $sql." AND m.name LIKE '%".addslashes(JFactory::getApplication()->input->get('search_text', "", "raw"))."%' ";
				$registry->get('search_value', $search_text);
			}

			if(isset($data_post['filter_type'])){
				if($data_post['filter_type']!='' && $data_post['filter_type'] != NULL) {
					$sql.= " AND m.type='".$data_post['filter_type']."'";
				}
				elseif($data_post['filter_type'] == NULL){
					$session = JFactory::getSession();
					$registry = $session->get('registry', "");
					$registry->set('filter_type_tskmed', "");
				}
			}
			
			$session = JFactory::getSession();
			$registry = $session->get('registry', "");
			$filter_status_tskmed = $registry->get('filter_status_tskmed', "");
			
			if(isset($data_post['filter_status'])&&($data_post['filter_status']!='')){
				if($data_post['filter_status']=='1') {
					$sql.= " AND m.published=1 ";
				} elseif($data_post['filter_status']=='2') {
					$sql.= " AND m.published=0 ";
				}
			} elseif(isset($data_post['filter2'])&&($data_post['filter2']!='')&&($data_post['filter2']!=0)){
				if($data_post['filter2']=='1') {
					$sql.= " AND m.published=1 ";
				} elseif($data_post['filter2']=='2') {
					$sql.= " AND m.published=0 ";
				}		
			} elseif (isset($filter_status_tskmed) && ($filter_status_tskmed != '')){
				if($filter_status_tskmed == '1') {
					$sql.= " AND m.published=1 ";
				} elseif($filter_status_tskmed == '2') {
					$sql.= " AND m.published=0 ";
				}
			}
			if(isset($data_post['filter_status'])) {
				$registry->set('filter_status_tskmed', $data_post['filter_status']);
			} elseif(isset($data_post['filter2'])){
				$registry->set('filter_status_tskmed', $data_post['filter2']);
			}
		}
		
		$media_category = JFactory::getApplication()->input->get("filter_media", "");
		
		if($media_category == ""){
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$media_category = $registry->get('filter_media', "");
		}
		elseif($media_category == "-1"){
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('filter_media', $media_category);
		}
		
		if($media_category != "" && $media_category != "-1"){
			$sql.= " AND m.category_id=".intval($media_category);
		}
		
		
		$search_text = JFactory::getApplication()->input->get('search_text', "null");
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$search_value = $registry->get('search_value', "");
		
		if($search_text == "null"){
			if(isset($search_value) && trim($search_value) != ""){
				$search_text = $search_value;
			}
		}
		elseif($search_text == ""){
			$registry->set('search_value', "");
		}
		
		if($search_text != "null" && $search_text != ""){
			$sql = $sql." AND m.name LIKE '%".$search_text."%' " ;
			$registry->set('search_value', $search_text);
		}
		
			
		if($task=='addmedia' && $type!="quiz" && $type!="text"){
			$sql.=" AND m.type <> 'text' AND m.type <> 'quiz' ";
		}
		elseif($task=='addmedia' && $type=="quiz"){
			$sql.=" AND m.type='quiz' ";
		}
		else{
			$sql.=" AND m.type='text' ";
		}
		
		$sql.= " order by m.id desc ";
		
		
		$limit_cond=NULL;
	
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
		
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		}
		$db->setQuery($sql.$limit_cond);
		$medias = $db->loadObjectList();
		$this->_total = $this->_getListCount($sql);
       
		if(($this->_total>1)&&(count($medias)==0)){
			$limit_cond=NULL;
			if($limit!=0){
				$limit_cond=" LIMIT 0,".$limit." ";
			}	
			$db->setQuery($sql.$limit_cond);
			$medias=$db->loadObjectList();
		}
		return $medias;
	}

	function getTask() {
		if (empty ($this->_attribute)) { 
			$this->_attribute =$this->getTable("guruTasks");
			$this->_attribute->load($this->_id);
		}
			$data = JFactory::getApplication()->input->post->getArray();
			
			if (!$this->_attribute->bind($data)){
				$this->setError($item->getError());
				return false;
	
			}
	
			if (!$this->_attribute->check()) {
				$this->setError($item->getError());
				return false;
	
			}
			
		return $this->_attribute;

	}
	
	public static function select_media ($pid, $media_no, $layout=0){
		// m_type = scr_m for media
		$db = JFactory::getDBO();
		if($pid != ""){
			$sql = "SELECT media_id FROM #__guru_mediarel WHERE type_id = ".intval($pid)." AND type='scr_m' AND mainmedia='".intval($media_no)."' AND layout = ".$layout;
			$db->setQuery($sql);
			$db->execute();
			$media_id = $db->loadColumn();
		}
		return @$media_id[0];
	}	
	
	public static function select_text ($pid, $text_no = NULL, $layout=0){
		$db = JFactory::getDBO();
		$media_id = 0;
		
		if(isset($text_no)){
			$cond = " AND text_no = '".intval($text_no)."'";
		}
		else{
			$cond = NULL;
		}
		
		if(intval($pid) != 0){
			$db->setQuery("SELECT media_id,mainmedia FROM #__guru_mediarel WHERE type_id = ".$pid." AND type='scr_t' ".$cond." AND layout=".$layout);
			$db->execute();
			$media_obj = $db->loadObject();
			
			if(isset($media_obj)){
				@$media_id = $media_obj->media_id.'$$$$$'.$media_obj->mainmedia;
			}
			else{
				$media_id = 0;
			}
		}
			
		return $media_id;
	}
	
	public static function parse_media ($id, $layout_id){
		$db = JFactory::getDBO();
		$helperclass = new guruAdminHelper();
		$sql = "SELECT * FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}	
		$configs = $db->loadObject();
		
		if(!isset($media)){
			$media = "";
		}
		
		$default_size = $configs->default_video_size;
		$default_width = "100%";
		$default_height = "";
		
		if(trim($default_size) != ""){
			$default_size = explode("x", $default_size);
			$default_width = $default_size["1"];
			$default_height = $default_size["0"];
		}

		if($layout_id != 15 && $layout_id != 16 && $layout_id != 17){
			$sql = "SELECT * FROM #__guru_media
						WHERE id = ".$id;
			$db->setQuery($sql);
			$db->execute();
			$the_media = $db->loadObject();
			@$the_media->code = stripslashes($the_media->code);
		}
		elseif($layout_id == 15){
			$sql = "SELECT * FROM #__guru_quiz
						WHERE id = ".$id; 
			$db->setQuery($sql);
			$db->execute();
			$the_media = $db->loadObject();
			$the_media->type="quiz";
			$the_media->code="";
		}
		elseif($layout_id == 16){
			$sql = "SELECT * FROM #__guru_projects WHERE id = ".$id; 
			$db->setQuery($sql);
			$db->execute();
			$the_media = $db->loadObject();

			@$the_media->type = "project";
			$the_media->code = "";
		}
		elseif($layout_id == 17){
			$sql = "SELECT * FROM #__guru_scorm_items WHERE id = ".$id; 
			$db->setQuery($sql);
			$db->execute();
			$the_media = $db->loadObject();

			@$the_media->type = "scorm";
			$the_media->code = "";
		}
		
		$no_plugin_for_code = 0;
		$aheight=0; $awidth=0; $vheight=0; $vwidth=0;
		
		if(@$the_media->type=='video'){
			if(intval($default_width) == 0){
				$default_width = "100%";
			}
			
			if($the_media->source == 'url' || $the_media->source == 'local'){
				if(($the_media->width == 0 || $the_media->height == 0) && $the_media->option_video_size == 1){
					$vheight=300; 
					$vwidth=400;
				}
				elseif(($the_media->width != 0 && $the_media->height != 0) && $the_media->option_video_size == 1){
					$vheight = $the_media->height; 
					$vwidth = $the_media->width;
				}
				elseif($the_media->option_video_size == 0){
					$vheight = $default_height; 
					$vwidth = $default_width;
				}		
			}
			elseif($the_media->source=='code'){				
				if(($the_media->width == 0 || $the_media->height == 0) && $the_media->option_video_size == 1){
					$begin_tag = strpos($the_media->code, 'width="');
					
					if($begin_tag!==false){
						$remaining_code = substr($the_media->code, $begin_tag+7, strlen($the_media->code));
						$end_tag = strpos($remaining_code, '"');
						$vwidth = substr($remaining_code, 0, $end_tag);
						$begin_tag = strpos($the_media->code, 'height="');
						
						if($begin_tag !== false){
							$remaining_code = substr($the_media->code, $begin_tag+8, strlen($the_media->code));
							$end_tag = strpos($remaining_code, '"');
							$vheight = substr($remaining_code, 0, $end_tag);
							$no_plugin_for_code = 1;
						}
						else{
							$vheight=300;
							$vwidth=400;
						}
					}	
					else{
						$vheight=300;
						$vwidth=400;
					}
				}
				elseif(($the_media->width != 0 || $the_media->height != 0) && $the_media->option_video_size == 1){
					$replace_with = 'width="'.$the_media->width.'"';
					$the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
					$replace_with = 'height="'.$the_media->height.'"';
					$the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);
					$replace_with = 'name="width" value="'.$the_media->width.'"';
					$the_media->code = preg_replace('#name="width" value="[0-9]+"#', $replace_with, $the_media->code);
					$replace_with = 'name="height" value="'.$the_media->height.'"';
					$the_media->code = preg_replace('#name="height" value="[0-9]+"#', $replace_with, $the_media->code);	
					$vheight=$the_media->height; $vwidth=$the_media->width;	
				}
				elseif($the_media->option_video_size == 0){
					$replace_with = 'width="'.$default_width.'"';
					$the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
					
					$replace_with = 'height="'.$default_height.'"';
					$the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);
					
					$replace_with = 'name="width" value="'.$default_width.'"';
					$the_media->code = preg_replace('#value="[0-9]+" name="width"#', $replace_with, $the_media->code);
					$replace_with = 'name="height" value="'.$default_height.'"';
					$the_media->code = preg_replace('#value="[0-9]+" name="height"#', $replace_with, $the_media->code);
					
					$replace_with = 'name="width" value="'.$default_width.'"';
					$the_media->code = preg_replace('/name="width" value="[0-9]+"/', $replace_with, $the_media->code);
					$replace_with = 'name="height" value="'.$default_height.'"';
					$the_media->code = preg_replace('/name="height" value="[0-9]+"/', $replace_with, $the_media->code);
					
					$vheight = $default_height;
					$vwidth = $default_width;
				}
			}	
		}		
		elseif(@$the_media->type=='audio')
				{
					if ($the_media->source=='url' || $the_media->source=='local')
						{	
							if ($the_media->width == 0 || $the_media->height == 0) 
								{
									$aheight=20; $awidth=300;
								}
							else
								{
									$aheight=$the_media->height; $awidth=$the_media->width;
								}
						}		
					elseif ($the_media->source=='code')
						{
							if ($the_media->width == 0 || $the_media->height == 0) 
								{
									$begin_tag = strpos($the_media->code, 'width="');
									if ($begin_tag!==false)
										{
											$remaining_code = substr($the_media->code, $begin_tag+7, strlen($the_media->code));
											$end_tag = strpos($remaining_code, '"');
											$awidth = substr($remaining_code, 0, $end_tag);
											
											$begin_tag = strpos($the_media->code, 'height="');
											if ($begin_tag!==false)
												{
													$remaining_code = substr($the_media->code, $begin_tag+8, strlen($the_media->code));
													$end_tag = strpos($remaining_code, '"');
													$aheight = substr($remaining_code, 0, $end_tag);
													$no_plugin_for_code = 1;
												}
											else
												{$aheight=20; $awidth=300;}	
										}	
									else
										{$aheight=20; $awidth=300;}							
								}
							else	
								{					
									$replace_with = 'width="'.$the_media->width.'"';
									$the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
									$replace_with = 'height="'.$the_media->height.'"';
									$the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);
									$aheight=$the_media->height; $awidth=$the_media->width;
								}
						}	
				}	
		
		$parts=explode(".", @$the_media->local);
		$extension=strtolower($parts[count($parts)-1]);

		if(@$the_media->type=='video' || @$the_media->type=='audio'){
			if(@$the_media->type=='video' && $extension=="avi"){
				$media = '<object width="'.$vwidth.'" height="'.$vheight.'" type="application/x-oleobject" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" id="MediaPlayer1">
<param value="'.JURI::root().$configs->videoin."/".$the_media->local.'" name="fileName">
<param value="true" name="animationatStart">
<param value="true" name="transparentatStart">
<param value="true" name="autoStart">
<param value="true" name="showControls">
<param value="10" name="Volume">
<param value="false" name="autoplay">
<embed width="'.$vwidth.'" height="'.$vheight.'" type="video/x-msvideo" src="'.JURI::root().$configs->videoin."/".$the_media->local.'" name="plugin">
</object>';
				/*$media = '<object id="MediaPlayer1" CLASSID="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" type="application/x-oleobject" width="'.$vwidth.'" height="'.$vheight.'">
<param name="fileName" value="'.JURI::root().$configs->videoin."/".$the_media->local.'">
<param name="animationatStart" value="true">
<param name="transparentatStart" value="true">
<param name="autoStart" value="true">
<param name="showControls" value="true">
<param name="Volume" value="10">
<embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" src="'.JURI::root().$configs->videoin."/".$the_media->local.'" name="MediaPlayer1" width="'.$vwidth.'" height="'.$vheight.'" autostart="1" showcontrols="1" volume="10">
</object>';*/
			}
			elseif($no_plugin_for_code == 0){
				if($the_media->type == "video" && $the_media->source == "url"){
					require_once(JPATH_ROOT .'/components/com_guru/helpers/videos/helper.php');
					$parsedVideoLink = parse_url($the_media->url);
					preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $parsedVideoLink['host'], $matches);
					$domain	= $matches['domain'];
					
					if (!empty($domain)){
						$provider		= explode('.', $domain);
						$providerName	= strtolower($provider[0]);
						
						if($providerName == "youtu"){
							$providerName = "youtube";
						}
						
						$libraryPath	= JPATH_ROOT .'/components/com_guru/helpers/videos' .'/'. $providerName . '.php';
						
						if(file_exists($libraryPath)){
							require_once($libraryPath);
							$className		= 'PTableVideo' . ucfirst($providerName);
							$videoObj		= new $className();
							$videoObj->init($the_media->url);
							$video_id		= $videoObj->getId();
							$videoPlayer	= $videoObj->getViewHTML($video_id, $vwidth, $vheight);
							$media = $videoPlayer;
						}
						else{
							$temp_media = $the_media;
							$temp_media->source = 'local';
							$temp_media->local = $temp_media->url;
							$temp_media->exception = "1";
							
							$media = $helperclass->create_media_using_plugin($temp_media, $configs, $awidth, $aheight, $vwidth, $vheight);
						}
					}
				}
				else{
					$media = $helperclass->create_media_using_plugin($the_media, $configs, $awidth, $aheight, $vwidth, $vheight);
				}
			}
		}

		if(@$the_media->type=='docs'){	
			$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
			$the_base_link = $the_base_link[0];				
			
			$media = JText::_('GURU_NO_PREVIEW');
			//$media = JText::_("GURU_TASKS");
			
			if($the_media->source == 'local' && (substr($the_media->local,(strlen($the_media->local)-3),3) == 'txt' || substr($the_media->local,(strlen($the_media->local)-3),3) == 'pdf') && $the_media->width > 1) {
				$media='<div class="contentpane">
							<iframe id="blockrandom"
								name="iframe"
								src="'.$the_base_link.'/'.$configs->docsin.'/'.$the_media->local.'"
								width="'.$the_media->width.'"
								height="'.$the_media->height.'"
								scrolling="auto"
								align="top"
								frameborder="2"
								class="wrapper">
								This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
							</div>';
				return stripslashes($media.'<div  style="text-align:center"><i>'.$the_media->instructions.'</i></div>');
			}
			elseif($the_media->source == 'url' && (substr($the_media->url,(strlen($the_media->url)-3),3) == 'txt' || substr($the_media->url,(strlen($the_media->url)-3),3) == 'pdf') && $the_media->width > 1) {
				$media='<div class="contentpane">
							<iframe id="blockrandom"
								name="iframe"
								src="'.$the_media->url.'"
								width="'.$the_media->width.'"
								height="'.$the_media->height.'"
								scrolling="auto"
								align="top"
								frameborder="2"
								class="wrapper">
								This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
							</div>';
				return stripslashes($media.'<div  style="text-align:center"><i>'.$the_media->instructions.'</i></div>');
			}
							
			if($the_media->source == 'local' && $the_media->width == 1){
				$media='<br /><a href="'.$the_base_link.$configs->docsin.'/'.$the_media->local.'" target="_blank">'.$the_media->name.'</a>';
				return stripslashes($media.'<div  style="text-align:center"><i>'.$the_media->instructions.'</i></div>');
			}
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
	
		if(@$the_media->type=='url'){
			$src = $the_media->url;
			$media = '<a href="'.$src.'" target="_blank">'.$src.'</a>';
		}
		if(@$the_media->type=='Article'){
			$media = self::getArticleById($the_media->code);
		}
		
		if(@$the_media->type=='image'){
			$img_size = @getimagesize(JPATH_SITE.DIRECTORY_SEPARATOR.$configs->imagesin.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'thumbs'.$the_media->local);
			$img_width = $img_size[0];
			$img_height = $img_size[1];
			if($img_width>0 && $img_height>0){ 
				$thumb_width=0;$thumb_height=0;
				if($the_media->width > 0){
					$thumb_width = $the_media->width;
					$thumb_height = $img_height / ($img_width/$the_media->width);
				}
				elseif($the_media->height > 0){
					$thumb_height = $the_media->height;
					$thumb_width = $img_width / ($img_height/$the_media->height);		
				}
				else{
					$thumb_height = 200;
					$thumb_width = $img_width / ($img_height/200);									
				}
				$media = '<img width="'.$thumb_width.'" height="'.$thumb_height.'" src="'.JURI::root().DIRECTORY_SEPARATOR.$configs->imagesin.'/media/thumbs'.$the_media->local.'" />';	
				}
				if(!isset($media)) { $media=NULL;}
		}

		if(@$the_media->type == 'project'){
			$db = JFactory::getDbo();
	
			$sql = "select c.`name` from #__guru_program c, #__guru_projects p where c.`id`=p.`course_id` and p.`id`=".intval(@$the_media->id);
			$db->setQuery($sql);
			$db->execute();
			$course_name = $db->loadColumn();
			$course_name = @$course_name["0"];

			$sql = "select u.`name` from #__users u, #__guru_projects p where u.`id`=p.`author_id` and p.`id`=".intval(@$the_media->id);
			$db->setQuery($sql);
			$db->execute();
			$user_name = $db->loadColumn();
			$user_name = @$user_name["0"];

			$media = '
				<table style="margin:auto;">
					<tr>
						<th style="text-align:right; padding-right:15px;">'.JText::_("GURU_TITLE").':</th>
						<td style="text-align:left; padding:0px !important;">'.@$the_media->title.'</td>
					</tr>
					<tr>
						<th style="text-align:right; padding-right:15px;">'.JText::_("GURU_COURSE").':</th>
						<td style="text-align:left; padding:0px !important;">'.$course_name.'</td>
					</tr>
					<tr>
						<th style="text-align:right; padding-right:15px;">'.JText::_("GURU_AUTHOR_CERTIFICATE").':</th>
						<td style="text-align:left; padding:0px !important;">'.$user_name.'</td>
					</tr>
				</table>
			';
		}

		if(@$the_media->type == 'scorm'){
			$db = JFactory::getDbo();
	
			$sql = "select c.`name` from #__guru_program c, #__guru_scorm_items p where c.`id`=p.`course_id` and p.`id`=".intval(@$the_media->id);
			$db->setQuery($sql);
			$db->execute();
			$course_name = $db->loadColumn();
			$course_name = @$course_name["0"];

			$sql = "select u.`name` from #__users u, #__guru_scorm_items p where u.`id`=p.`author_id` and p.`id`=".intval(@$the_media->id);
			$db->setQuery($sql);
			$db->execute();
			$user_name = $db->loadColumn();
			$user_name = @$user_name["0"];

			$media = '
				<table style="margin:auto;">
					<tr>
						<th style="text-align:right; padding-right:15px;">'.JText::_("GURU_TITLE").':</th>
						<td style="text-align:left; padding:0px !important;">'.@$the_media->title.'</td>
					</tr>
					<tr>
						<th style="text-align:right; padding-right:15px;">'.JText::_("GURU_COURSE").':</th>
						<td style="text-align:left; padding:0px !important;">'.$course_name.'</td>
					</tr>
					<tr>
						<th style="text-align:right; padding-right:15px;">'.JText::_("GURU_AUTHOR_CERTIFICATE").':</th>
						<td style="text-align:left; padding:0px !important;">'.$user_name.'</td>
					</tr>
				</table>
			';
		}

		if(@$the_media->type=='quiz'){
			$document = JFactory::getDocument();
			$document->addStyleSheet(JURI::root()."components/com_guru/css/uikit.almost-flat.min.css");
    		$document->addStyleSheet(JURI::root()."components/com_guru/css/quiz.css");
    		$document->addScript(JURI::root()."components/com_guru/js/uikit.min.js");
			$media = '';
				
			$q  = "SELECT * FROM #__guru_quiz WHERE id = ".$the_media->id;
			$db->setQuery( $q );
			$result_quiz = $db->loadObject();				
			
			$media .= '<span class="guru-quiz__title">'.@$result_quiz->name.'</span>';
			$media .= '<span class="guru-quiz__desc">'.@$result_quiz->description.'</span>';
			
			if(isset($result_quiz) && $result_quiz->is_final == 1){
				$sql = "SELECT 	quizzes_ids FROM #__guru_quizzes_final WHERE qid=".$the_media->id;
				$db->setQuery($sql);
				$db->execute();
				$result=$db->loadResult();	
				$result_qids = explode(",",trim($result,","));
				
				if(count($result_qids) || $result_qids["0"] = ""){
					$result_qids["0"] = 0;
				}
				
				$q  = "SELECT * FROM #__guru_questions_v3 WHERE qid IN (".implode(",", $result_qids).") and published=1 ORDER BY question_order";
			}
			else{
				$q  = "SELECT * FROM #__guru_questions_v3 WHERE qid = ".$the_media->id." and published=1 ORDER BY question_order";
			}
				
				
				
			$db->setQuery( $q );
			$quiz_questions = $db->loadObjectList();			
			
			$media = $media.'<div id="the_quiz">';
				
			$question_number = 1;
			
			for($i=0;$i<count($quiz_questions);$i++){
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
						$result_media[] = $helperclass->create_media_using_plugin($media_that_needs_to_be_sent["0"], $configs, '', '', '100px', 100);
					}	
				}
				
				$media .= '<div class="guru-quiz__question guru-question">';
				$media .= 	'<div class="guru-quiz__media">'.implode("",$result_media).'</div>';
				$media .= 	'<div class="guru-quiz__question-title">';
				$media .= 		$quiz_questions[$i]->question_content."";
				$media .= 	'</div>';
					
				$media .= '<div class="guru-quiz__answers-wrapper">';
				$media .= '<div class="guru-quiz__answers uk-grid uk-grid-small" data-uk-grid-match data-uk-grid-margin>';
				
				if($quiz_questions[$i]->type == "true_false"){
					foreach($question_answers as $question_answer){
						if($question_answer->answer_content_text == "True"){
							$question_answer->answer_content_text = JText::_("GURU_QUESTION_OPTION_TRUE");
						}
						elseif($question_answer->answer_content_text == "False"){
							$question_answer->answer_content_text = JText::_("GURU_QUESTION_OPTION_FALSE");
						}
						
						$questions_html_ids["true"][$question_answer->question_id][] = $question_answer->question_id . intval($question_answer->id);
						
						$media .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3" id="guru-question-answer-'.$question_answer->question_id.'-'.$question_answer->id.'">
											 <div class="guru-quiz__answer">
												 <div class="uk-float-left">
													<input type="radio" id="'.$question_answer->question_id.intval($question_answer->id).'"  name="truefs_ans['.intval($question_answer->question_id).']" value="'.$question_answer->id.'" />
													<label for="'.$question_answer->question_id.intval($question_answer->id).'" class="guru-quiz__check-box"></label>
												 </div>
												 <div class="uk-float-left">
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
									
									if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
										if($media_that_needs_to_be_sent["0"]->type == "text"){
											$result_media_answers[] = self::parse_txt($media_that_needs_to_be_sent["0"]->id);
										}
										else{
											$result_media_answers[] = self::parse_media($media_that_needs_to_be_sent["0"]->id, 0);
										}
									}
								}
							}

							$questions_html_ids["simple"][$question_answer->question_id][] = 'ans' . $question_answer->question_id . intval($question_answer->id);
							
							$option_value = '<input type="radio" id="ans'.$question_answer->question_id.intval($question_answer->id).'" name="answers_single" value="'.$question_answer->id.'"/><label for="ans'.$question_answer->question_id.intval($question_answer->id).'" class="guru-quiz__check-box"></label> <span>'.$question_answer->answer_content_text.'</span><div class="guru-quiz__answer-media">'.implode("",$result_media_answers).'</div>';
							
							$media .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3" id="guru-question-answer-'.$question_answer->question_id.'-'.$question_answer->id.'"><div class="guru-quiz__answer">'.$option_value.'</div></div>';
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
									
									if(isset($media_that_needs_to_be_sent) && count($media_that_needs_to_be_sent) > 0){
										if($media_that_needs_to_be_sent["0"]->type == "text"){
											$result_media_answers[] = self::parse_txt($media_that_needs_to_be_sent["0"]->id);
										}
										else{
											$result_media_answers[] = self::parse_media($media_that_needs_to_be_sent["0"]->id, 0);
										}
									}
								}
							}
							
							$questions_html_ids["multiple"][$question_answer->question_id][] = intval($question_answer->id);
							
							$option_value = '<input type="checkbox" name="multiple_ans['.intval($quiz_questions[$i]->id).'][]" id="'.$question_answer->id.'" value="'.$question_answer->id.'"/><label for="'.$question_answer->id.'" class="guru-quiz__check-box"></label> <span>'.$question_answer->answer_content_text.'</span><div class="guru-quiz__answer-media">'.implode("",$result_media_answers).'</div>';
							
							$media .= '<div class="guru-quiz__answer-wrapper uk-width-1-1 uk-width-medium-1-3" id="guru-question-answer-'.$question_answer->question_id.'-'.$question_answer->id.'"><div class="guru-quiz__answer">'.$option_value.'</div></div>';
						}
					}		
				}
				$media .= '</div>';					
				$media .= '</div>';
				$media .= '</div>';
			}
			
			$media = $media.'<input type="hidden" value="'.($question_number-1).'" name="question_number" id="question_number" />';
			$media = $media.'<input type="hidden" value="'.@$result_quiz->name.'" id="quize_name" name="quize_name"/>';
			//$media = $media.'<br /><div align="left" style="clear:both;"><input type="submit" class ="btn" value="Submit" onclick="get_quiz_result()" /></div>';	
			$media = $media.'</div>';
		}
		
		if(@$the_media->type == "file"){			
			$media = '<a target="_blank" href="'.JURI::ROOT().$configs->filesin.'/'.$the_media->local.'">'.$the_media->name.'</a><br/><br/>'.$the_media->instructions;
		}

		if (@$the_media->type === 'zoom') {
			$data = new JRegistry($the_media->code);
			$media = '<h3>'.JText::_('GURU_MEDIA_ZOOM_MEETING_ID').': ' . $data->get('meeting_id') . '</h3>';
			$media .= '<h3>'.JText::_('GURU_MEDIA_ZOOM_MEETING_PASSWORD').': ' . $data->get('meeting_pass') . '</h3>';
			$media .= '<div><a class="btn btn-primary" target="_blank" href="' . $data->get('invite_link') . '" >'.JText::_('GURU_ZOOM_CLICK_TO_JOIN').'</a></div>';
		}
		
		return stripslashes($media);
	}	
	
	function parse_audio ($id){
		$db = JFactory::getDBO();
		$helperclass =  new guruAdminHelper();
		$sql = "SELECT * FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}	
		$configs = $db->loadObject();		
	
			$sql = "SELECT * FROM #__guru_media
					WHERE id = ".$id; 
			$db->setQuery($sql);
			$the_media = $db->loadObject();
			$the_media->code=stripslashes($the_media->code);
			
			$no_plugin_for_code = 0;
			$aheight=0; $awidth=0; $vheight=0; $vwidth=0;
			if(@$the_media->type=='audio')
				{
					if ($the_media->source=='url' || $the_media->source=='local')
						{	
							if ($the_media->width == 0 || $the_media->height == 0) 
								{
									$aheight=20; $awidth=300;
								}
							else
								{
									$aheight=$the_media->height; $awidth=$the_media->width;
								}
						}		
					elseif ($the_media->source=='code')
						{
							if ($the_media->width == 0 || $the_media->height == 0) 
								{
									$begin_tag = strpos($the_media->code, 'width="');
									if ($begin_tag!==false)
										{
											$remaining_code = substr($the_media->code, $begin_tag+7, strlen($the_media->code));
											$end_tag = strpos($remaining_code, '"');
											$awidth = substr($remaining_code, 0, $end_tag);
											
											$begin_tag = strpos($the_media->code, 'height="');
											if ($begin_tag!==false)
												{
													$remaining_code = substr($the_media->code, $begin_tag+8, strlen($the_media->code));
													$end_tag = strpos($remaining_code, '"');
													$aheight = substr($remaining_code, 0, $end_tag);
													$no_plugin_for_code = 1;
												}
											else
												{$aheight=20; $awidth=300;}	
										}	
									else
										{$aheight=20; $awidth=300;}							
								}
							else	
								{					
									$replace_with = 'width="'.$the_media->width.'"';
									$the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
									$replace_with = 'height="'.$the_media->height.'"';
									$the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);
									$aheight=$the_media->height; $awidth=$the_media->width;
								}
						}	
				}	
		
		$awidth="200";$aheight="20";
		if(@$the_media->type=='audio'){
			if(!isset($layout_id)){
				$layout_id = "";
			}
			if ($no_plugin_for_code == 0){
				$media = $helperclass->create_media_using_plugin($the_media, $configs, $awidth, $aheight, $vwidth, $vheight,$layout_id);	
			}
			else{
				$media = $the_media->code;
			}
		}

		if(!isset($media)) { $media=NULL;}
		
		return stripslashes($media);
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
		
		if(@$the_media->type=='text')
			{
				$media = $the_media->code;
				if(strpos($media, 'src="') !== FALSE){
					$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
					$the_base_link = $the_base_link[0];
					$media = str_replace('src="', 'src="'.$the_base_link, $media);
				}
			}
		if(@$the_media->type=='docs')
			{
			
				$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
				$the_base_link = $the_base_link[0];				
				
				$media = JText::_('GURU_NO_PREVIEW');
				if($the_media->source == 'local' && (substr($the_media->local,(strlen($the_media->local)-3),3) == 'txt' || substr($the_media->local,(strlen($the_media->local)-3),3) == 'pdf') && $the_media->width == 0){
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
				elseif($the_media->source == 'url' && (substr($the_media->url,(strlen($the_media->url)-3),3) == 'txt' || substr($the_media->url,(strlen($the_media->url)-3),3) == 'pdf') && $the_media->width == 0){
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
		if(@$the_media->type=='quiz'){
				$media = '';
				
				$q  = "SELECT * FROM #__guru_quiz WHERE id = ".$the_media->source;
				$db->setQuery( $q );
				$result_quiz = $db->loadObject();				
				
				$media = $media. '<strong>'.$result_quiz->name.'</strong><br /><br />';
				$media = $media. $result_quiz->description.'<br /><br />';
				
				$q  = "SELECT * FROM #__guru_questions_v3 WHERE qid = ".$the_media->source." and published=1";
				$db->setQuery( $q );
				$quiz_questions = $db->loadObjectList();			
				
				foreach( $quiz_questions as $one_question )
					{
						$media = $media.'<div align="left">'.$one_question->text.'<div>';
						
						$media = $media.'<div align="left" style="padding-left:30px;">';
						if($one_question->a1!='')
							$media = $media.'<input type="radio" name="'.$one_question->id.'">'.$one_question->a1.'</input><br />';
						if($one_question->a2!='')
							$media = $media.'<input type="radio" name="'.$one_question->id.'">'.$one_question->a2.'</input><br />';
						if($one_question->a3!='')
							$media = $media.'<input type="radio" name="'.$one_question->id.'">'.$one_question->a3.'</input><br />';
						if($one_question->a4!='')
							$media = $media.'<input type="radio" name="'.$one_question->id.'">'.$one_question->a4.'</input><br />';
						if($one_question->a5!='')
							$media = $media.'<input type="radio" name="'.$one_question->id.'">'.$one_question->a5.'</input><br />';
						if($one_question->a6!='')
							$media = $media.'<input type="radio" name="'.$one_question->id.'">'.$one_question->a6.'</input><br />';
						if($one_question->a7!='')
							$media = $media.'<input type="radio" name="'.$one_question->id.'">'.$one_question->a7.'</input><br />';
						if($one_question->a8!='')
							$media = $media.'<input type="radio" name="'.$one_question->id.'">'.$one_question->a8.'</input><br />';
						if($one_question->a9!='')
							$media = $media.'<input type="radio" name="'.$one_question->id.'">'.$one_question->a9.'</input><br />';		
						if($one_question->a10!='')
							$media = $media.'<input type="radio" name="'.$one_question->id.'">'.$one_question->a10.'</input><br />';		
						$media = $media.'</div>';																																										
					}		
					
				$media = $media.'<br /><div align="left" style="padding-left:30px;"><input type="submit" value="'.JText::_("GURU_SUBMIT").'" disabled="disabled" /></div>';	
			}	
		if(!isset($media)) {$media=NULL;}
		$media = $media.'<div  style="text-align:center"><i>' .$the_media->instructions. '</i></div>';
		
		return stripslashes($media);	
	}	

	function store(){
		$item = $this->getTable('guruTasks');
		$data = JFactory::getApplication()->input->post->getArray();

		if(trim($data["id"]) == ""){
			$data["id"] = 0;
		}

		if(trim($data["endpublish"]) == ""){
			$data["endpublish"] = "0000-00-00 00:00:00";
		}

		$database = JFactory::getDBO();
		$return_array = array();
		
		$course_id = JFactory::getApplication()->input->get("day", "0");
		$module_id = JFactory::getApplication()->input->get("my_menu_id", "0");
		$change_order = false;
		$last_lesson_id = 0;
		
		$sql = "select id from #__guru_days where pid=".intval($course_id)." order by ordering desc";
		$database->setQuery($sql);
		$database->execute();
		$ids = $database->loadColumn();
		$last_module_id = $ids["0"];

		if($last_module_id == $module_id){
			$sql = "select id_final_exam from #__guru_program where id=".intval($course_id);
			$database->setQuery($sql);
			$database->execute();
			$id_final_exam = $database->loadColumn();
			$id_final_exam = @$id_final_exam["0"];
			
			if(intval($id_final_exam) > 0){
				$change_order = true;
				$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_task t where mr.type='dtask' and mr.type_id=".intval($module_id)." and mr.media_id=t.id order by t.ordering desc limit 0,1";
				$database->setQuery($sql);
				$database->execute();
				$lesson_id = $database->loadColumn();
				$last_lesson_id = @$lesson_id["0"];
			}
		}
		
		if($data['alias']==''){
			$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['name']);
		} 
		else {
			$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['alias']);
		}
		
		$data['startpublish'] = date('Y-m-d H:i:s', strtotime($data['startpublish']));
		
		if($data['endpublish'] != 'Never' && $data['endpublish'] != '' && $data['endpublish'] != "0000-00-00 00:00:00"){ // calendar change
			$data['endpublish'] = date('Y-m-d H:i:s', strtotime($data['endpublish']));
		}
		$db = JFactory::getDBO();
		
		$id = JFactory::getApplication()->input->get("id", "");
		
		if($id == "" || $id == "0"){
			//start set the order. this step must to be the last one
			$query="select max(ordering) as ordering from #__guru_task";
			$database->setQuery($query);
			$database->execute();
			$result=$database->loadObject();
			$data['ordering']=intval($result->ordering)+1;
			//end set the order. this step must to be the last one
		}
		
		$groups = JFactory::getApplication()->input->get("groups", array(), "raw");
		if(isset($groups) && count($groups) > 0){
			$data["groups_access"] = implode(",", $groups);
		}
		
		$minutes = $data["minutes"];
		$seconds = $data["seconds"];
		
		if(trim($minutes) != "" || trim($seconds) != ""){
			$data["duration"] = trim($minutes)."x".trim($seconds);
		}
		elseif(trim($minutes) == "" && trim($seconds) == ""){
			$data["duration"] = "";
		}

		$description = JFactory::getApplication()->input->get("description", "", "raw");
		$data["description"] = $description;

		if (!$item->bind($data)){
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}
		if (!$item->check()) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}		
		if (!$item->store()) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}

		$return_array["id"] = $item->id;
		if($data['id'] == "" || $data['id'] == 0){
			$new_lesson = "yes";
		}
		else{
			$new_lesson = "no";
		}
		
		$db->setQuery("SELECT forumboardcourse,forumboardlesson FROM #__guru_kunena_forum WHERE id=1 ");
		$db->execute();	
		$ressult = $db->loadAssocList();

		if($ressult[0]["forumboardlesson"] ==1){
			$new_lesson = "no";
		}
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$lesson_removed = $registry->get('lesson_removed', "");
		
		if(isset($lesson_removed) && $lesson_removed == "yes"){
			$new_lesson = "yes";	
		}
		
		if(!isset($data['id']) || $data['id'] == 0)
			{
				$db->setQuery("SELECT max(id) FROM #__guru_task ");
				$db->execute();	
				$data['id'] = $db->loadResult();		
			}	
		
		// scr_l = the layout for the screen
		$db->setQuery("DELETE FROM #__guru_mediarel WHERE type_id='".$data['id']."' AND type='scr_l' ");
		$db->execute();		
		
		$db->setQuery("INSERT INTO #__guru_mediarel (`type`, `type_id`, `media_id`, `mainmedia`, `text_no`, `layout`, `access`, `order`) VALUES ('scr_l','".$data['id']."','".$data['layout_db']."','0','0','0','0','0')");
		$db->execute();

		// scr_m = the file type for the screen - media
		// mainmedia = 1 for the first media
		// mainmedia = 2 for the second media
		$db->setQuery("DELETE FROM #__guru_mediarel WHERE type_id='".$data['id']."' AND type='scr_m' ");
		$db->execute();	
		
		// scr_t = 	the file type for the screen - text	
		// mainmedia = 1 for normal text
		// mainmedia = 2 for quiz	
		$db->setQuery("DELETE FROM #__guru_mediarel WHERE type_id='".$data['id']."' AND type='scr_t' ");
		$db->execute();		
	
		if(isset($data['day']) && intval($data['day'])>0){		
		$queri="INSERT INTO #__guru_mediarel (`type`, `type_id`, `media_id`, `mainmedia`, `text_no`, `layout`, `access`, `order`) VALUES ('dtask','".intval($data['my_menu_id'])."','".$data['id']."','0','0','0','0','0')";	
			$db->setQuery($queri);
			$db->execute();
		}	
		
		if(1==1)
			{
				if(intval($data['db_media_1'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (`type`, `type_id`, `media_id`, `mainmedia`, `layout`, `access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_1'])."', '1', 1, '0', '0')");
						$db->execute();					
					}		
					
				$mainmedia = 1;
				if(intval($data['text_is_quiz']) == 1)
					$mainmedia = 2;
				if(intval($data['db_text_1'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (`type`, `type_id`, `media_id`, `mainmedia`, `layout`, `access`, `order`) VALUES ('scr_t','".$data['id']."','".intval($data['db_text_1'])."','".$mainmedia."',1, '0', '0')");
						$db->execute();					
					}							
			}
			
		if(1==1)
			{
				if(intval($data['db_media_2'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_2'])."','1',2, 0, 0)");
						$db->execute();					
					}	
				if(intval($data['db_media_3'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_3'])."','2',2, 0, 0)");
						$db->execute();					
					}	
					
				$mainmedia = 1;
				if(intval($data['text_is_quiz']) == 1)
					$mainmedia = 2;
				if(intval($data['db_text_2'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_t','".$data['id']."','".intval($data['db_text_2'])."','".$mainmedia."',2, 0, 0)");
						$db->execute();					
					}														
			}
			
		if(1==1)
			{
				if(intval($data['db_media_4'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_4'])."','1',3, 0, 0)");
						$db->execute();					
					}	
					
				$mainmedia = 1;
				if(intval($data['text_is_quiz']) == 1)
					$mainmedia = 2;
				if(intval($data['db_text_3'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_t','".$data['id']."','".intval($data['db_text_3'])."','".$mainmedia."',3, 0, 0)");
						$db->execute();					
					}								
			}	
			
		if(1==1)
			{
				if(intval($data['db_media_5'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_5'])."','1',4, 0, 0)");
						$db->execute();					
					}	
				if(intval($data['db_media_6'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_6'])."','2',4, 0, 0)");
						$db->execute();					
					}
					
				$mainmedia = 1;
				if(intval($data['text_is_quiz']) == 1)
					$mainmedia = 2;
				if(intval($data['db_text_4'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_t','".$data['id']."','".intval($data['db_text_4'])."','".$mainmedia."',4, 0, 0)");
						$db->execute();					
					}														
			}	
			
			
		if(1==1)
			{
				$mainmedia = 1;
				if(intval($data['text_is_quiz']) == 1)
					$mainmedia = 2;
				if(intval($data['db_text_5'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_t','".$data['id']."','".intval($data['db_text_5'])."','".$mainmedia."',5, 0, 0)");
						$db->execute();					
					}				
			}				
			
			
		if(1==1)
			{
				if(intval($data['db_media_7'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_7'])."','1',6, 0, 0)");
						$db->execute();					
					}			
			}		
		
		
		if(1==1)
			{
				if(intval($data['db_media_8'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_8'])."','1',7, 0, 0)");
						$db->execute();					
				}			
								
				$mainmedia = 1;
				if(intval($data['text_is_quiz']) == 1)
					$mainmedia = 2;
				if(intval($data['db_text_6'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_t','".$data['id']."','".intval($data['db_text_6'])."','".$mainmedia."',7, 0, 0)");
						$db->execute();					
					}
					
				
			}	
		
		
		if(1==1)
			{
				if(intval($data['db_media_9'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_9'])."','1',8, 0, 0)");
						$db->execute();					
					}	
				if(intval($data['db_media_10'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_10'])."','2',8, 0, 0)");
						$db->execute();					
					}	
					
				$mainmedia = 1;
				if(intval($data['text_is_quiz']) == 1)
					$mainmedia = 2;
				if(intval($data['db_text_7'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_t','".$data['id']."','".intval($data['db_text_7'])."','".$mainmedia."',8, 0, 0)");
						$db->execute();					
					}														
			}	
		
		
		if(1==1)
			{
				if(intval($data['db_media_11'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_11'])."','1',9, 0, 0)");
						$db->execute();					
					}	
					
				$mainmedia = 1;
				if(intval($data['text_is_quiz']) == 1)
					$mainmedia = 2;
				if(intval($data['db_text_8'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_t','".$data['id']."','".intval($data['db_text_8'])."','".$mainmedia."',9, 0, 0)");
						$db->execute();					
					}								
			}	
		
		
		if(1==1)
			{
				if(intval($data['db_media_12'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_12'])."','1',10, 0, 0)");
						$db->execute();					
					}	
				if(intval($data['db_media_13'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_13'])."','2',10, 0, 0)");
						$db->execute();					
					}
					
				$mainmedia = 1;
				if(intval($data['text_is_quiz']) == 1)
					$mainmedia = 2;
				if(intval($data['db_text_9'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_t','".$data['id']."','".intval($data['db_text_9'])."','".$mainmedia."',10, 0, 0)");
						$db->execute();					
					}														
			}	
				
		if(1==1)
			{
				if(intval($data['db_media_14'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_14'])."','1',11, 0, 0)");
						$db->execute();					
					}	
					
				$mainmedia = 1;
				if(intval($data['text_is_quiz']) == 1)
					$mainmedia = 2;
				if(intval($data['db_text_10'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,layout,`access`, `order`) VALUES ('scr_t','".$data['id']."','".intval($data['db_text_10'])."','".$mainmedia."', 1, 11, 0, 0)");
						$db->execute();					
					}	
				
				if(intval($data['db_text_11'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,layout,`access`, `order`) VALUES ('scr_t','".$data['id']."','".intval($data['db_text_11'])."','".$mainmedia."', 2,11, 0, 0)");
						$db->execute();					
					}		
												
			}	
			
		if(1==1)
			{
				if(intval($data['db_media_15'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_15'])."','1',12, 0, 0)");
						$db->execute();
						
						$listoflessons = "select distinct(media_id) from #__guru_mediarel where type='dtask' and type_id IN (select id from #__guru_days where pid=".intval($data['day']).")";
						$db->setquery($listoflessons);
						$db->execute();
						$listoflessons = $db->loadColumn();

						
						$listoflessons = implode("," ,$listoflessons);
						if($listoflessons == ""){
							$listoflessons = "0";
						}
						
						$count_quiz = "select count(media_id) from #__guru_mediarel where type='scr_l' and media_id='12' and type_id IN (".$listoflessons.")";
						$db->setquery($count_quiz);
						$db->execute();
						$count_quiz = $db->loadColumn();
						$count_quiz = $count_quiz["0"];
						
						$sql="UPDATE #__guru_program set hasquiz = ".intval($count_quiz)." WHERE id =".intval($data['day']);
						$db->setQuery($sql);
						$db->execute();	
					}			
			}

		if(1==1)
			{
				if(intval($data['db_media_16'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_16'])."','1',16, 0, 0)");
						$db->execute();
					}			
			}

		if(1==1)
			{
				if(intval($data['db_media_17'])>0)
					{
						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_17'])."','1',17, 0, 0)");
						$db->execute();
					}

				$db = JFactory::getDbo();
				$sql = "select * from #__guru_scorm_items where `id`=".intval($data['db_media_17']);
				$db->setQuery($sql);
				$db->execute();
				$scorm_item = $db->loadAssocList();

				if(isset($scorm_item["0"]) && intval($scorm_item["0"]) > 0){
					include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."scorm.php");
					$scorm_guru = new scormGuruImport();
					$scrum_added = $scorm_guru->uploadScormLesson($data['id'], $scorm_item);
				}
			}		
		
		// jump buttons - Start //
		
		// delete existing buttons before inserting the new ones
		$sql="DELETE FROM #__guru_mediarel WHERE type='jump' AND type_id='".$data['id']."'";
		$db->setQuery($sql);
		$db->execute();
		
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		// insert the 4 buttons
		if(intval($data_post['jumpbutton1'])!=0){
			$sql1="INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,`access`, `order`) VALUES ('jump','".$data['id']."','".intval($data_post['jumpbutton1'])."','0','0', 0, 0)";
			$db->setQuery($sql1);
			$db->execute();
		}
		if(intval($data_post['jumpbutton2'])!=0){	
			$sql="INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,`access`, `order`) VALUES ('jump','".$data['id']."','".intval($data_post['jumpbutton2'])."','0','0', 0, 0)";
			$db->setQuery($sql);
			$db->execute();
		}
		
		if(intval($data_post['jumpbutton3'])!=0){
			$sql="INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,`access`, `order`) VALUES ('jump','".$data['id']."','".intval($data_post['jumpbutton3'])."','0','0', 0, 0)";
			$db->setQuery($sql);
			$db->execute();
		}

		if(intval($data_post['jumpbutton4'])!=0){
			$sql="INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,`access`, `order`) VALUES ('jump','".$data['id']."','".intval($data_post['jumpbutton4'])."','0','0', 0, 0)";
			$db->setQuery($sql);
			$db->execute();
		}
		
		if(!isset($data['db_media_99'])) {$data['db_media_99']=0;}
		if(intval($data['db_media_99'])>0){
			$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`, `order`) VALUES ('scr_m','".$data['id']."','".intval($data['db_media_99'])."','1',99, 0, 0)");
			$db->execute();					
		}	
		
		// jump buttons - End //
		
		//start - kunenea forum integration
		$sql = "select count(*) from #__extensions where element='com_kunena' and name='com_kunena'";
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadResult();
		
		if($count > 0){
			if(JComponentHelper::isEnabled( 'com_kunena', true) ){
				$sql = "select forumboardlesson from #__guru_kunena_forum where id=1";
				$db->setQuery($sql);
				$db->execute();
				$forumboardlesson = $db->loadResult();
				
				if($data['kunenabuttonactive'] == 'on'){
					$forumboardlesson = 1;
				}
				
				if($forumboardlesson != 0 ){
					if($new_lesson == "no"){
						$sql="UPDATE #__guru_task SET forum_kunena_generatedt = '1' WHERE id=".intval($data['id']);
						$db->setQuery($sql);
						$db->execute();
						
						$db->setQuery("SELECT `kunena_category` FROM #__guru_kunena_forum WHERE id=1");
						$db->execute();	
						$kunena_category = $db->loadColumn();
						$kunena_category = @$kunena_category["0"];

						if(intval($kunena_category) == 0){
							$nameofmainforum = JText::_('GURU_TREECOURSE');
						}
						else{
							$sql = "SELECT `name` FROM #__kunena_categories WHERE id='".intval($kunena_category)."'";
							$db->setQuery($sql);
							$db->execute();
							$nameofmainforum = $db->loadResult();
						}
						
						$sql = "SELECT name FROM #__kunena_categories WHERE name='".addslashes($nameofmainforum)."'";
						$db->setQuery($sql);
						$db->execute();
						$result = $db->loadResult();
						
						if(!isset($result)){
							$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES (".intval($kunena_category).", '".$db->escape($nameofmainforum)."', 'course', 0, 0, 'joomla.level', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
							$db->setQuery($sql);
							$db->execute();

							$sql = "SELECT id FROM #__kunena_categories WHERE name='".addslashes($nameofmainforum)."'";
							$db->setQuery($sql);
							$db->execute();
							$idmainforum= $db->loadResult();

							$sql = "select count(*) from #__kunena_aliases where `alias`='course' and `item`=".$idmainforum;
							$db->setQuery($sql);
							$db->execute();
							$count_alias = $db->loadResult();
							
							if($count_alias == 0){
								$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES (  'course', 'catid', ".$idmainforum.", 0)";
								$db->setQuery($sql);
								$db->execute();
							}
						}
					
						$sql = "SELECT name from #__guru_program where id =".intval($data['day']);
						$db->setQuery($sql);
						$db->execute();	
						$coursename = $db->loadResult();
						
						$sql = "SELECT alias from #__guru_program where id=".intval($data['day']);
						$db->setQuery($sql);
						$db->execute();	
						$aliascourse = $db->loadColumn();
						$aliascourse = @$aliascourse["0"];
						
						$sql = "SELECT id FROM #__kunena_categories WHERE name='".addslashes($nameofmainforum)."'";
						$db->setQuery($sql);
						$db->execute();
						$idmainforum= $db->loadResult();
						
						$sql = "SELECT name FROM #__kunena_categories WHERE parent_id='".$idmainforum."' and name='".addslashes($coursename)."'";
						$db->setQuery($sql);
						$db->execute();
						$result1 = $db->loadColumn();
						$result1 = @$result1["0"];
						
						if(!isset($result1)){
							$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( '".$idmainforum."', '".addslashes($coursename)."', '".$aliascourse."', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
							$db->setQuery($sql);
							$db->execute();
							
							$sql = "SELECT id FROM #__kunena_categories WHERE  name='".addslashes($coursename)."'";
							$db->setQuery($sql);
							$db->execute();
							$resultid = $db->loadResult();
							
							if(trim($aliascourse) != ""){
								$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES (  '".$aliascourse."', 'catid', ".$resultid.", 0)";
								$db->setQuery($sql);
								$db->execute();
							}
							
							// start assign user like a moderator
							$sql = "select forumboardteacher from #__guru_kunena_forum where id='1'";
							$db->setQuery($sql);
							$db->execute();
							$forumboardteacher = $db->loadColumn();
							$forumboardteacher = @$forumboardteacher["0"];
							
							if($forumboardteacher == "1"){
								$sql = "SELECT author from #__guru_program where id=".intval($data['day']);
								$db->setQuery($sql);
								$db->execute();	
								$author = $db->loadColumn();
								$author = @$author["0"];
								$author_array = explode("|", $author);
								
								if(isset($author_array) && count($author_array) > 0){
									foreach($author_array as $key=>$author){
										$sql = "SELECT count(*) FROM #__kunena_user_categories WHERE category_id='".intval($resultid)."' and user_id=".intval($author);
										$db->setQuery($sql);
										$db->execute();
										$already = $db->loadColumn();
										$already = @$already["0"];
										
										if($already == 0){
											$sql = "INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES ('".intval($author)."', '".intval($resultid)."', 1, '', 0, '')";
											$db->setQuery($sql);
											$db->execute();
										}
									}
								}
							}
							// stop assign user like a moderator
						}
		
						$sql = "SELECT title from #__guru_days where pid =".intval($data['day'])." and id IN (SELECT type_id FROM #__guru_mediarel WHERE media_id=".$data['id'].")";
						$db->setQuery($sql);
						$db->execute();	
						$modulename = $db->loadResult();
		
						$sql = "SELECT alias from #__guru_days where pid=".intval($data['day'])." and title='".$db->escape($modulename)."'";
						$db->setQuery($sql);
						$db->execute();	
						$aliasmodule = $db->loadColumn();
						$aliasmodule = @$aliasmodule["0"];
						
						$sql = "SELECT id FROM #__kunena_categories WHERE alias ='".$aliascourse."'";
						$db->setQuery($sql);
						$db->execute();
						$idmaincourse = $db->loadResult();
						
						$sql = "SELECT name FROM #__kunena_categories WHERE parent_id='".$idmaincourse."' and name='".addslashes($modulename.'-'.$data['day'])."'";
						$db->setQuery($sql);
						$db->execute();
						$resultmodule = $db->loadColumn();
						$resultmodule = @$resultmodule["0"];
						
						$alias_for_module = $data['day'];
						
						if(!isset($resultmodule)){
							$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( '".$idmaincourse."', '".addslashes($modulename.'-'.$data['day'])."', '".$aliasmodule.'-'.$data['day']."', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
							$db->setQuery($sql);
							$db->execute();	
							
							$sql = "SELECT id FROM #__kunena_categories WHERE name='".addslashes($modulename.'-'.$data['day'])."'";
							$db->setQuery($sql);
							$db->execute();
							$resultidmodule = $db->loadResult();	
							
							if(trim($aliasmodule) != ""){
								$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES (  '".$aliasmodule.'-'.$data['day']."', 'catid', ".$resultidmodule.", 0)";
								$db->setQuery($sql);
								$db->execute();
							}
							
							// start assign user like a moderator
							$sql = "select forumboardteacher from #__guru_kunena_forum where id='1'";
							$db->setQuery($sql);
							$db->execute();
							$forumboardteacher = $db->loadColumn();
							$forumboardteacher = @$forumboardteacher["0"];
							
							if($forumboardteacher == "1"){
								$sql = "SELECT author from #__guru_program where id=".intval($data['day']);
								$db->setQuery($sql);
								$db->execute();	
								$author = $db->loadColumn();
								$author = @$author["0"];
								$author_array = explode("|", $author);
								
								if(isset($author_array) && count($author_array) > 0){
									foreach($author_array as $key=>$author){
										$sql = "SELECT count(*) FROM #__kunena_user_categories WHERE category_id='".intval($resultidmodule)."' and user_id=".intval($author);
										$db->setQuery($sql);
										$db->execute();
										$already = $db->loadColumn();
										$already = @$already["0"];
										
										if($already == 0){
											$sql = "INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES ('".intval($author)."', '".intval($resultidmodule)."', 1, '', 0, '')";
											$db->setQuery($sql);
											$db->execute();
										}
									}
								}
							}
							// stop assign user like a moderator
						}
						
						$sql = "SELECT id FROM #__kunena_categories WHERE  name='".addslashes($coursename)."'";
						$db->setQuery($sql);
						$db->execute();
						$resultid = $db->loadResult();
						//$count_c = count($resultid);
						
						$sql = "SELECT id FROM #__kunena_categories WHERE name='".addslashes($modulename.'-'.$alias_for_module)."'";
						$db->setQuery($sql);
						$db->execute();
						$resultidmodule = $db->loadResult();
						//$count_m = count($resultidmodule);
	
						$sql = "INSERT INTO #__guru_kunena_courseslinkage (idcourse, coursename, catidkunena) VALUES (  '".$data['day']."', '".addslashes($coursename)."', '".$resultid."')";
						$db->setQuery($sql);
						$db->execute();
						
						$sql = "SELECT alias from #__guru_task where id =".intval($data['id']);
						$db->setQuery($sql);
						$db->execute();	
						$aliaslesson = $db->loadColumn();
						$aliaslesson = @$aliaslesson["0"];
						
						$sql = "SELECT name FROM #__kunena_categories WHERE alias='".$aliaslesson.'-'.$data['id']."'";
						$db->setQuery($sql);
						$db->execute();
						$result2 = $db->loadColumn();
						$result2 = @$result2["0"];
						
						if(!isset($result2)){
							$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( ".$resultidmodule.", '".addslashes($data['name'])."', '".$aliaslesson.'-'.$data['id']."', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
							$db->setQuery($sql);
							$db->execute();
							
							$sql = "SELECT id FROM #__kunena_categories WHERE  alias='".$aliaslesson.'-'.$data['id']."'";
							$db->setQuery($sql);
							$db->execute();
							$resultidlesson = $db->loadResult();
							
							if(trim($aliaslesson) != ""){
								$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('".$aliaslesson.'-'.$data['id']."', 'catid', '".$resultidlesson."', 0)";
								$db->setQuery($sql);
								$db->execute();
							}
							
							// start assign user like a moderator
							$sql = "select forumboardteacher from #__guru_kunena_forum where id='1'";
							$db->setQuery($sql);
							$db->execute();
							$forumboardteacher = $db->loadColumn();
							$forumboardteacher = @$forumboardteacher["0"];
							
							if($forumboardteacher == "1"){
								$sql = "SELECT author from #__guru_program where id=".intval($data['day']);
								$db->setQuery($sql);
								$db->execute();	
								$author = $db->loadColumn();
								$author = @$author["0"];
								$author_array = explode("|", $author);
								
								if(isset($author_array) && count($author_array) > 0){
									foreach($author_array as $key=>$author){
										$sql = "SELECT count(*) FROM #__kunena_user_categories WHERE category_id='".intval($resultidlesson)."' and user_id=".intval($author);
										$db->setQuery($sql);
										$db->execute();
										$already = $db->loadColumn();
										$already = @$already["0"];
										
										if($already == 0){
											$sql = "INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES ('".intval($author)."', '".intval($resultidlesson)."', 1, '', 0, '')";
											$db->setQuery($sql);
											$db->execute();
										}
									}
								}
							}
							// stop assign user like a moderator

							$sql = "INSERT INTO #__guru_kunena_lessonslinkage (idlesson, lessonname, catidkunena) VALUES (  '".$data['id']."', '".addslashes($data['name'])."', '".$resultidlesson."')";
							$db->setQuery($sql);
							$db->execute();

							$sql = "SELECT catidkunena  FROM #__guru_kunena_lessonslinkage where idlesson=".$data['id']." order by id desc limit 0,1";
							$db->setQuery($sql);
							$db->execute();
							$catidkunena = $db->loadResult();
							
							$sql = "UPDATE #__kunena_categories set name='".$db->escape($data['name'])."' WHERE id=".intval($catidkunena);
							$db->setQuery($sql);
							$db->execute();
						}
					}
				}
			}
		}
		//end - kunenea forum integration
		
		$registry->set('lesson_removed', "");
		
		$return_array["return"] = true;
		
		if($change_order){
			$id_new_lesson = $return_array["id"];
			$sql = "select ordering from #__guru_task where id=".intval($id_new_lesson);
			$db->setQuery($sql);
			$db->execute();
			$new_lesson_ordering = $db->loadColumn();
			$new_lesson_ordering = @$new_lesson_ordering["0"];
			
			$sql = "select ordering from #__guru_task where id=".intval($last_lesson_id);
			$db->setQuery($sql);
			$db->execute();
			$last_lesson_ordering = $db->loadColumn();
			$last_lesson_ordering = @$last_lesson_ordering["0"];
			
			$sql = "update #__guru_task set ordering = ".intval($new_lesson_ordering)." where id=".intval($last_lesson_id);
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "update #__guru_task set ordering = ".intval($last_lesson_ordering)." where id=".intval($id_new_lesson);
			$db->setQuery($sql);
			$db->execute();
		}

		return $return_array;
	}
	
	function getJumps(){
		$data_get = JFactory::getApplication()->input->get->getArray();
		
		$stepid=$data_get['cid'][0];
		$db = JFactory::getDBO();
		$sql="SELECT j . *
		FROM #__guru_jump AS j, #__guru_mediarel AS m
		WHERE j.id = m.media_id
		AND m.type = 'jump'
		AND m.type_id =".intval($stepid)."
		ORDER BY j.button ASC
		LIMIT 10";
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	function getCurrentJump(){
		$db = JFactory::getDBO();
		$data_get = JFactory::getApplication()->input->get->getArray();
		
		if(isset($data_get['id'])){
			$id=intval($data_get['id']);
		} else { return NULL;}
		$sql="SELECT * FROM #__guru_jump WHERE id = ".$id;
		$db->setQuery($sql);
		return $db->loadObject();		
	}
	
	function last_task(){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT max(id) FROM #__guru_task ");
		$db->execute();	
		$last_task = $db->loadResult();	
		return $last_task;
	}
	
	function more_media_files ($ids){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT *, id as media_id FROM #__guru_media WHERE id in (".$ids.") GROUP BY media_id");
		$db->execute();
		$more_media_files = $db->loadObjectList();
		$this->more_media_files = $more_media_files;
		return true;
	}
	
	function existing_ids ($ids){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = ".$ids." AND type='task' AND mainmedia = '0' ");
		$db->execute();
		$existing_ids = $db->loadObjectList();
		return $existing_ids;
	}	
	
	function existing_mmid ($id){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT *, id as media_id FROM #__guru_media WHERE id in (".$id.") GROUP BY media_id");
		$db->execute();
		$existing_ids = $db->loadObjectList();
		return $existing_ids;
	}		

	function existing_mqid ($id){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT *, id as media_id FROM #__guru_quiz WHERE id in (".$id.") GROUP BY media_id");
		$db->execute();
		$existing_ids = $db->loadObjectList();
		return $existing_ids;
	}	
		
	public static function getMediaName ($id) {
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_media WHERE id=".intval($id)." LIMIT 1";
		$db->setQuery($sql);
		$db->execute();
		$existing_ids = $db->loadResult();
		return $existing_ids;
	}

	function delete(){
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$database = JFactory::getDBO();
		
		// we retain the TASKS STATUS from PROGRAMSTATUS table - START
		$sql = "SELECT id, tasks FROM #__guru_programstatus";
		$database->setQuery($sql);
		if (!$database->execute()){
			return;
		}
		$tasks_ids = $database->loadObjectList();
		
		foreach($tasks_ids as $tasks_id){
			$new = '';
			$day_array = explode(';', $tasks_id->tasks);

			foreach($day_array as $day_tasks) {
				$tasks_array = explode ('-',$day_tasks);
				
				// removing a certain VALUE from an array - start
				foreach($tasks_array as $key => $value) {
					$task_number_array = explode(',',$value);
					
					if(in_array($task_number_array[0],$cids)) {
						unset($tasks_array[$key]);
					}
				}
				$new_array = array_values($tasks_array);	
				$new_array = implode('-',$new_array);
				
				//if(isset($new_array[0]))
				$new = $new.$new_array.';';
			}
			$new = substr($new, 0, strlen($new)-1);
			// $new has the task STATUS 
			$sql = "update #__guru_programstatus set tasks='".$new."' where id =".$tasks_id->id;
			$database->setQuery($sql);
			$database->execute();	
		}
		// we retain the TASKS STATUS from PROGRAMSTATUS table - STOP
	
		$item = $this->getTable('guruTasks');
		
		$sql = "SELECT imagesin FROM #__guru_config WHERE id ='1' ";
		$database->setQuery($sql);
		if (!$database->execute()) {
			return;
		}
		$imagesin = $database->loadResult();
		
		foreach ($cids as $cid) {
			
			$sql = "SELECT image FROM #__guru_task WHERE id =".$cid;
			$database->setQuery($sql);
			if (!$database->execute()) {
				return;
			}
			$image = $database->loadResult();	
			
			if (!$item->delete($cid)) {
				$this->setError($item->getError());
				return false;

			}
			// we delete the relations with MAIN MEDIA, SUPPORTING MEDIA
			$query = "DELETE FROM #__guru_mediarel WHERE type = 'task' AND type_id = '".$cid."'";
			$database->setQuery( $query );
			$database->execute();
			
			// we delete the relations with QUIZES
			$query = "DELETE FROM #__guru_mediarel WHERE type = 'tquiz' AND type_id = '".$cid."'";
			$database->setQuery( $query );
			$database->execute();

			// we delete the relations with DAYS
			$query = "DELETE FROM #__guru_mediarel WHERE type = 'dtask' AND media_id = '".$cid."'";
			$database->setQuery( $query );
			$database->execute();			

			$targetPath = JPATH_SITE.'/'.$imagesin.'/';		
			unlink($targetPath.$image);			
		}
		return true;
	}

	function addmedia ($toinsert, $taskid, $mainmedia) {
		$db = JFactory::getDBO();
		$sql = "INSERT INTO #__guru_mediarel ( id , type , type_id , media_id , mainmedia ) VALUES ('', 'task', '".$taskid."' , '".$toinsert."', '".$mainmedia."');";
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
	return true;
	}
	
	function delmedia($tid,$cid,$main) {
		$db = JFactory::getDBO();
		
		$sql = "delete from #__guru_mediarel where type='task' and type_id=".$tid." and media_id=".$cid." and mainmedia=".$main;
		
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return 1;
	}

	function getlistDays () {
		$data_get = JFactory::getApplication()->input->get->getArray();
		
		if(!isset($data_get['progrid'])) { return false;}
		$sql = "SELECT * FROM #__guru_days WHERE pid =".intval($data_get['progrid'])." ORDER BY ordering ASC ";
		$result = $this->_getList($sql);
		return $result;
	}
	
	public static function getTask2($taskid){
			$database = JFactory::getDBO();
			$sql = " SELECT * FROM #__guru_task WHERE id = ".$taskid; 
			$database->setQuery($sql);
			$result = $database->loadObject();
			return $result;	
	}	
	
	public static function select_layout ($pid){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = ".$pid." AND type='scr_l' ");
		$db->execute();
		$layout_id = $db->loadResult();
		return $layout_id;
	}	

	public static function getIDTasksForDay($dayid){
			$database = JFactory::getDBO();
			$sql = "SELECT distinct(media_id) FROM #__guru_mediarel WHERE type='dtask' AND type_id = ".$dayid." ORDER BY id ASC ";
			$database->setQuery($sql);
			$result = $database->loadColumn();
			return $result;	
	}
	
	public static function getArticleById($id) {
			$db = JFactory::getDBO();
			$sql = "SELECT jc.introtext, jc.fulltext FROM #__content jc WHERE id = ".$id;
			$db->setQuery($sql);
			$row = $db->loadAssoc();
			$fullArticle = $row['introtext'].$row['fulltext'];
			if(!strlen(trim($fullArticle))) $fullArticle = "Article is empty ";
			return $fullArticle; 
	}
	
	public static function getConfigs() {
		$db = JFactory::getDBO();
		
		$sql = "SELECT * FROM #__guru_config LIMIT 1";
		
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}	
		$result = $db->loadObject();	
		return $result;
	}		
	
	function checkbox_construct( $rowNum, $recId, $name='cid' )
	{
		$db = JFactory::getDBO();
		
		$sql = "SELECT id FROM #__guru_days WHERE pid in (SELECT programid FROM #__guru_order GROUP BY programid)";
		
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}	
		$result = $db->loadObjectList();	
		
		$sql = "SELECT influence FROM #__guru_config WHERE id = 1";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
			}
		$influence = $db->loadResult(); // we have selected the INFLUENCE 
		
		$sql = "SELECT locked FROM #__guru_days WHERE id in (SELECT type_id FROM #__guru_mediarel WHERE media_id = '".$recId."' AND type = 'dtask') ";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
			}
		$locked = $db->loadColumn(); // we have selected the LOCKED property for a day 				

		$days_in_sold_programs = array();
		foreach ($result as $day_id)
			{
				array_push( $days_in_sold_programs, $day_id->id );
			}
		
		if(($influence==1 && in_array('1', $locked)) || $influence==0)
			{
				$not = 'not';
				$disabled = 'disabled="disabled"';	
			}	
		else 
			{
				$disabled = '';
				$not = '';
			}	
		
		return '<input type="checkbox" id="'.$not.'cb'.$rowNum.'" '.$disabled.'" name="'.$name.'[]" value="'.$recId.'" onclick="isChecked(this.checked);" />$$$$$'.$disabled;
	}	
	
	public static function get_asoc_file_for_media($media_id)	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_media WHERE id=".$media_id;
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
		}
		$the_media = $db->loadObject();			
		if(!empty($the_media )){
			if($the_media->source == 'local' || $the_media->type == 'image')
				$asoc_file = $the_media->local;
			else
				$asoc_file = '-';
		}
		else $asoc_file = '-';
		
		return 	$asoc_file;			
	}	
	
	function real_quiz_id($media_id)	{
		$db = JFactory::getDBO();
		$sql = "SELECT source FROM #__guru_media WHERE id=".$media_id;
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
		}
		$the_media = $db->loadResult();		
		
		return 	$the_media;			
	}	

	function parse_quiz ($id){

		$db = JFactory::getDBO();
		
		$q = "SELECT * FROM #__guru_config WHERE id = '1' ";
		$db->setQuery( $q );
		$configs = $db->loadObject();
				
		$q  = "SELECT * FROM #__guru_media WHERE id = ".$id;
		$db->setQuery( $q );
		$result = $db->loadObject();	
	
		$the_media = $result;
		
		if(@$the_media->type=='text')
			{
				$media = $the_media->code;
			}
		if(@$the_media->type=='docs')
			{
			
				$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
				$the_base_link = $the_base_link[0];				
				
				$media = JText::_('GURU_NO_PREVIEW');
				
				if($the_media->source == 'local' && (substr($the_media->local,(strlen($the_media->local)-3),3) == 'txt' || substr($the_media->local,(strlen($the_media->local)-3),3) == 'pdf') && $the_media->width == 0){
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
				elseif($the_media->source == 'url' && (substr($the_media->url,(strlen($the_media->url)-3),3) == 'txt' || substr($the_media->url,(strlen($the_media->url)-3),3) == 'pdf') && $the_media->width == 0){
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
		if(@$the_media->type=='quiz'){
				$media = '';
				
				$q  = "SELECT * FROM #__guru_quiz WHERE id = ".$the_media->source;
				$db->setQuery( $q );
				$result_quiz = $db->loadObject();				
				
				$media = $media. '<strong>'.$result_quiz->name.'</strong><br /><br />';
				$media = $media. $result_quiz->description.'<br /><br />';
				
				$q  = "SELECT * FROM #__guru_questions_v3 WHERE qid = ".$the_media->source." and and published=1";
				$db->setQuery( $q );
				$quiz_questions = $db->loadObjectList();			
				
				$media = $media.'<div id="the_quiz">';
				
				$question_number = 1;
				foreach( $quiz_questions as $one_question )
					{
						$question_answers_number = 0;

						$media = $media.'<div align="left">'.$one_question->text.'<div>';
						
						$media = $media.'<div align="left" style="padding-left:30px;">';
						if($one_question->a1!='')
							{
								$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a1).'" \' type="radio" value="1a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a1.'</input><br />';
								$question_answers_number++;
							}	
						if($one_question->a2!='')
							{
								$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a2).'" \' type="radio" value="2a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a2.'</input><br />';
								$question_answers_number++;
							}	
						if($one_question->a3!='')
							{
								$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a3).'" \' type="radio" value="3a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a3.'</input><br />';
								$question_answers_number++;
							}	
						if($one_question->a4!='')
							{
								$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a4).'" type="radio" value="4a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a4.'</input><br />';
								$question_answers_number++;
							}	
						if($one_question->a5!='')
							{
								$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a5).'" type="radio" value="5a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a5.'</input><br />';
								$question_answers_number++;
							}	
						if($one_question->a6!='')
							{
								$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a6).'" type="radio" value="6a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a6.'</input><br />';
								$question_answers_number++;
							}	
						if($one_question->a7!='')
							{
								$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a7).'" type="radio" value="7a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a7.'</input><br />';
								$question_answers_number++;
							}	
						if($one_question->a8!='')
							{
								$question_answers_number++;
								$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a8).'" type="radio" value="8a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a8.'</input><br />';
							}
						if($one_question->a9!='')
							{
								$question_answers_number++;
								$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a9).'" type="radio" value="9a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a9.'</input><br />';		
							}
						if($one_question->a10!='')
							{
								$question_answers_number++;
								$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a10).'" type="radio" value="10a'.$question_number.'" name="'.$question_number.'">'.$one_question->a10.'</input><br />';		
							}	
						$media = $media.'</div>';		
						
						$the_first_answer = explode(',', $one_question->answers);
						$the_first_answer = $the_first_answer[0];
						$the_first_answer = str_replace('a', '', $the_first_answer);
						
						if(intval($the_first_answer) == 1)
							$the_right_answer = $one_question->a1;
						if(intval($the_first_answer) == 2)
							$the_right_answer = $one_question->a2;
						if(intval($the_first_answer) == 3)
							$the_right_answer = $one_question->a3;
						if(intval($the_first_answer) == 4)
							$the_right_answer = $one_question->a4;
						if(intval($the_first_answer) == 5)
							$the_right_answer = $one_question->a5;
						if(intval($the_first_answer) == 6)
							$the_right_answer = $one_question->a6;
						if(intval($the_first_answer) == 7)
							$the_right_answer = $one_question->a7;
						if(intval($the_first_answer) == 8)
							$the_right_answer = $one_question->a8;
						if(intval($the_first_answer) == 9)
							$the_right_answer = $one_question->a9;
						if(intval($the_first_answer) == 10)
							$the_right_answer = $one_question->a10;																		
																																											
						
						$media = $media.'<input type="hidden" value="" name="question_answergived'.$question_number.'" id="question_answergived'.$question_number.'" />';
						$media = $media.'<input type="hidden" value="'.str_replace("'","$$$$$" ,$the_right_answer).'" name="question_answerright'.$question_number.'" id="question_answerright'.$question_number.'" />';
						$media = $media.'<input type="hidden" value="'.str_replace("'","&acute;" ,$one_question->text).'" name="the_question'.$question_number.'" id="the_question'.$question_number.'" />';
						
						$question_number++;																																								
					}		
				
				$media = $media.'<input type="hidden" value="'.($question_number-1).'" name="question_number" id="question_number" />';
				$media = $media.'<input type="hidden" value="'.$result_quiz->name.'" id="quize_name" name="quize_name"/>';
				
				
				$media = $media.'<br /><div align="left" style="padding-left:30px;"><input type="submit" value="'.JText::_("GURU_SUBMIT").'" onclick="get_quiz_result()" /></div>';	
			
				$media = $media.'</div>';
			}	
		
		return $media;	
		}	
		
		public static function getMediaFromId($id_media){
			$db = JFactory::getDBO();
			$sql = "SELECT * FROM #__guru_media WHERE id =".intval($id_media);
			$db->setQuery($sql);
			$db->execute();
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
		
		$diff_1 = @array_diff($temp_answers_right, $answer_given);
		$diff_2 = @array_diff($answer_given, $temp_answers_right);
		
		if((is_array($diff_1) && count($diff_1) == 0) && (is_array($diff_2) && count($diff_2) == 0)){
			$return = TRUE;
		}
		return $return;
	}
	
};
?>