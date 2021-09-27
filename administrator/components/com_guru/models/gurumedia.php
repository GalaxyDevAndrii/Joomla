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
jimport('joomla.filesystem.folder');

class guruAdminModelguruMedia extends JModelLegacy {
	var $_licenses;
	var $_license;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;	
	var $return_array = array();

	function __construct () {
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");

		$this->setId((int)$cids[0]);
		$mainframe = JFactory::getApplication();

		global $option;
		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
		
		if(JFactory::getApplication()->input->get("limitstart") == JFactory::getApplication()->input->get("old_limit")){
			JFactory::getApplication()->input->set("limitstart", "0");		
			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart = $mainframe->getUserStateFromRequest($option.'limitstart', 'limitstart', 0, 'int');
		}
		
		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);	

	}
	
	function getAllRows($parent, $level){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_media_categories where parent_id=".intval($parent);
		$db->setquery($sql);
		$db->execute();
		$result = $db->loadAssocList();		
		if(isset($result) && is_array($result) && count($result) > 0){
			$level ++;			
			foreach($result as $key=>$value){
				$value["level"] = $level;				
				$this->return_array[] = $value;
				$this->getAllRows($value["id"], $level);
			}
		}
		return $this->return_array;
	}
	
	public static function getAllMediaCategory(){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_media_categories";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList("id");
		return $result;
	}	

	function getPagination() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getlistFiles();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	function setId($id) {
		$this->_tid = $id;
		$this->_package = null;
	}
	
	function getFilters(){
		$app = JFactory::getApplication('administrator');
		$db = JFactory::getDBO();
		$filter = new stdClass;
		$filter->search_media	= $db->Quote($app->getUserStateFromRequest("search_media","search_media","","string"));
		$status		= $app->getUserStateFromRequest("media_publ_status","media_publ_status","YN","string");
		$type		= $app->getUserStateFromRequest('media_type', 'media_type', " " , 'string');
		$media_category = $app->getUserStateFromRequest('media_category', 'media_category', " " , 'string');
		$teacher_id = $app->getUserStateFromRequest('teacher_id', 'teacher_id', " " , 'string');
		
		$statusOption=array();
		$javascript="onchange='document.topform1.submit();'";
		$statusOption[]=JHTML::_("select.option", JText::_("GURU_ALLYN"),"YN");
		$statusOption[]=JHTML::_("select.option", JText::_("GURU_PUBLISHED"),"Y");
		$statusOption[]=JHTML::_("select.option", JText::_("GURU_UNPUBLISHED"),"N");
		
		$filter->status=JHTML::_("select.genericlist",$statusOption,"media_publ_status", "size=1 ".$javascript,"text", "value",$status);
				
		$typeOption=array();
		$javascript="onchange='document.topform1.submit();'";
		
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_ALLTYPES"),"-");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_VIDEO"),"video");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_AUDIO"),"audio");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_DOCS"),"docs");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_URL"),"url");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_ARTICLE"),"Article");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_IMAGE"),"image");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_text"),"text");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_MEDIATYPEFILE_"),"file");
		
		$filter->type=JHTML::_("select.genericlist",$typeOption,"media_type", "size=1 ".$javascript,"text", "value",$type);
		
		$sql = "select id, name from #__guru_media_categories";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		$categoryOption = array();
		$categoryOption[] = JHTML::_("select.option", JText::_("GURU_ALLCATHEG"),"-");
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				$categoryOption[] = JHTML::_("select.option", $value["name"], $value["id"]);
			}
		}
		$filter->media_category = JHTML::_("select.genericlist", $categoryOption, "media_category", "size=1 ".$javascript, "text", "value", $media_category);

		$sql = "select u.id, u.name from #__users u, #__guru_authors g where g.userid=u.id and g.enabled='1' order by u.name ASC";
		$db->setQuery($sql);
		$db->execute();
		$teachers = $db->loadAssocList();
		$techerOption = array();
		$techerOption[] = JHTML::_("select.option", JText::_("GURU_ALL_TEACHERS"),"-");
		foreach($teachers as $key => $value){
			$techerOption[] = JHTML::_("select.option", $value["name"], $value["id"]);
		}

		$filter->teachers = JHTML::_("select.genericlist", $techerOption, "teacher_id", "size=1 ".$javascript, "text", "value", $teacher_id);
		
		return $filter;
	}
	
	function getlistFiles () { 
		$app = JFactory::getApplication();
		$config	= $this->getConfig();
		$db = JFactory::getDBO();
			
		$limit		= $app->getUserStateFromRequest( 'limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int' );
		$teacher_id = $app->getUserStateFromRequest('teacher_id', 'teacher_id', 0, 'int');
		$condition	= array();
	
		$search_media = trim($app->input->getString("search_media"));

		
		$status		= $app->getUserStateFromRequest("media_publ_status","media_publ_status","YN","string");
		$type		= $app->getUserStateFromRequest('media_type','media_type',' ','string');
		$media_category		= $app->getUserStateFromRequest('media_category', 'media_category', ' ', 'string');
			
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		}
		else $limit_cond="";
		
		if($status=="Y"){
			$condition[] =" c.published=1 ";
		}else if($status=="N"){
			$condition[] =" c.published=0 ";
		}
		
		if($search_media!=""){
			$condition[] =" (c.name LIKE '%".$db->escape($search_media)."%' OR c.instructions LIKE '%".$db->escape($search_media)."%' OR c.local LIKE '%".$db->escape($search_media)."%' OR c.url='%".$db->escape($search_media)."%' ) ";
		}
		
		if(trim($type)!="-" && trim($type)!=""){
			$condition[]=" c.type='".$type."' ";
		}
		
		if(trim($media_category) != "-" && trim($media_category)!= ""){
			$condition[]=" c.category_id=".intval($media_category);
		}

		if ($teacher_id) {
			$condition[] = " c.author = $teacher_id ";
		}

		if(!empty($condition)) {
			$condition = " AND ".implode(" AND ",$condition);
		} else {
			$condition = "";	
		}
			
		$sql = "SELECT * FROM `#__guru_media` AS c WHERE type <> 'quiz' ".$condition." ORDER BY id desc";
		$this->_total = $this->_getListCount($sql);
		$this->_media = $this->_getList($sql.$limit_cond);
		
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
			
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		} else {
			$limit_cond = NULL;
		}
		$this->_media = $this->_getList($sql.$limit_cond);
		$this->_total = $this->_getListCount($sql);
		
		$medias=$this->_media;
		
		for($i=0; $i<count($medias); $i++){
			if($medias[$i]->type == "image"){
				$medias[$i]->local = str_replace(" ", "%20", $medias[$i]->local);
				$size = @getimagesize(JURI::root()."/".$config->imagesin."/media/thumbs".$medias[$i]->local);
				$medias[$i]->width = $size[0];
				$medias[$i]->height = $size[1];
			}
		}
		
		return $medias;
	}	

	function getfile() {
		$config = $this->getConfig();
		$folder	= JFactory::getApplication()->input->get("directory","");
		$txt = JFactory::getApplication()->input->get("txt","0");
		$fileType = JFactory::getApplication()->input->get("type","");
		$db = JFactory::getDBO();
		if ($folder!='root') 
			$getin = DIRECTORY_SEPARATOR.$folder; 
		else $getin='';
		if (empty ($this->_package)) {
			$this->_package = $this->getTable("guruMedia");
			$this->_package->load($this->_tid);
			$data = JFactory::getApplication()->input->post->getArray();
			
			if (!$this->_package->bind($data)){
				$this->setError($item->getError());
				return false;
	
			}
			
			if (!$this->_package->check()) {
				$this->setError($item->getError());
				return false;
			}
		}
		
		if($this->_tid<1){
			$this->_package->text=JText::_("GURU_NEW");
		}
		else{
			$this->_package->text=JText::_("GURU_EDIT");
		}
		
		$this->_package->lists['flash_directory'] = JURI::root() . "/images/stories/";	
		$task = JFactory::getApplication()->input->get('task', '');
		//start type drop-down
		$typeOption	  = array();
		$javascript	  = "onchange=changeType(this.value)";
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_SELECT"),"-");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_VIDEO"),"video");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_AUDIO"),"audio");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_DOCS"),"docs");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_URL"),"url");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_ARTICLE"),"Article");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_IMAGE"),"image");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_ZOOM_MEETING"),"zoom");
		if($task == "add"){
			$typeOption[] = JHTML::_("select.option", JText::_("GURU_TEXT"),"text");	
		}
		else{	
			if($this->_package->type == 'text'){
				$typeOption[] = JHTML::_("select.option", JText::_("GURU_TEXT"),"text");
			}
		}
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_TEXT"),"text");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_FILE"),"file");
		
		if($this->_package->type=="" && $txt==1){
			$this->_package->type="text";
		}
		
		if($this->_package->type == ""){
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$type = $registry->get('type', "");
			
			if($type){
				$this->_package->type = $type;
			}
		}
		
		if(!isset($this->_package->type)){
			$type = JFactory::getApplication()->input->get("type", "");
			if($type != ""){
				$this->_package->type = $type;
			}
		}
		
		$this->_package->lists['type']=JHTML::_("select.genericlist",$typeOption,"type", "size=1 ".$javascript,"text", "value",$this->_package->type);
		
		if(!isset($this->_package->published)){
			$this->_package->published = 1;
		}
		
		
		$approved = '<input type="hidden" name="published" value="0">';
		if($this->_package->published == 1){
			$approved .= '<input type="checkbox" checked="checked" value="1" class="ace-switch ace-switch-5" name="published">';
		}
		else{
			$approved .= '<input type="checkbox" value="1" class="ace-switch ace-switch-5" name="published">';
		}
		$approved .= '<span class="lbl"></span>';
		
		$this->_package->lists['approved'] = $approved;
		
		//start get author list
		$sql = "SELECT u.id, u.name FROM #__users u, #__guru_authors la where u.id=la.userid";	
		$db->setQuery($sql);
		$db->execute();
		$result_authors = $db->loadObjectList();
		
		$author_listl=array();
		$author_listl[]=JHTML::_("select.option",JText::_('GURU_SELECT'),"0");
		for($i=0;$i<count($result_authors);$i++){
			$author_listl[]=JHTML::_("select.option",$result_authors[$i]->name,$result_authors[$i]->id);
		}	
		$this->_package->lists['author']=JHTML::_("select.genericlist",$author_listl,"author","","text","value",$this->_package->author);
				
		
		//start video
		$directory 	= JPATH_SITE.DIRECTORY_SEPARATOR.$config->videoin;
		$directoryt = JPATH_SITE.DIRECTORY_SEPARATOR.$config->videoin.$getin;
		
		$allfolders = JFolder::folders($directory); 		
		
		$javascript 	= 'onchange="changefolder();change_radio_local();" onClick="change_radio_local();"';
		$videoOption[] = JHTML::_("select.option","...","root");
		
		if(isset($allfolders) && count($allfolders) > 0 && $allfolders !== FALSE){
			foreach($allfolders as $fille) {
				$extension_array = explode('.', $fille);
				$extension = $extension_array[count($extension_array)-1];
				$extension = strtolower($extension);
				
				$allowed_extensions = array("lv", "swf", "mov", "mp4", "wmv", "wma", "mp3", "3gp", "webm", "ogv", "ogg", "divx", "m4a");
				
				if(in_array($extension, $allowed_extensions)){
					$videoOption[] = JHTML::_("select.option",$fille,$fille);
				}
			}
		}
		$this->_package->lists['video_dir']=JHTML::_("select.genericlist",$videoOption,"video_dir", "size=1 ".$javascript,"text", "value",$this->_package->type);

		
		$javascript 	= 'onchange="change_radio_local();" onClick="change_radio_local();"';	
		$allfiles=JFolder::files($directoryt); 
		$imageOption = array();
		
		if(count($allfiles)>0){
			foreach ($allfiles as $fille) {
				$extension_array = explode('.', $fille);
				$extension = $extension_array[count($extension_array)-1];
				$extension = strtolower($extension);
				
				if($extension =='mov' || $extension =='avi' || $extension =='wmv' || $extension =='swf' || $extension =='mpg' || $extension =='mpeg' || $extension =='fla' ||  $extension =='mp4' ||  $extension =='flv'){
					$imageOption[]=JHTML::_("select.option", $fille,$fille);
				}
			}
			$this->_package->lists['video_url'] = JHTML::_("select.genericlist", $imageOption, "localfile", "size=10 ".$javascript, "text", "value", $this->_package->local);
		}
		else{
			$this->_package->lists['video_url']="";
			$this->_package->lists['video_url'] = '<select onclick="change_radio_local();" onchange="change_radio_local();" size="10" name="localfile_v" id="localfile_v"><option value="0">...</option></select>';
		}
		
		//end video
		
		
		
		//start audio
		$directory 	= JPATH_SITE.'/'.$config->audioin;
		chmod($directory, 0755);
		$directoryt 	= JPATH_SITE.'/'.$config->audioin.$getin;
		
		$allfolders = JFolder::folders($directory); 
		
		$audioOption[] = array();
		$javascript 	= 'onchange="changefolder();change_radio_local();" onClick="change_radio_local();"';
		$audioOption[] = JHTML::_("select.option","...","root");
		if(count($allfolders)){
			foreach ($allfolders as $fille) {
				$extension_array = explode('.', $fille);
				$extension = $extension_array[count($extension_array)-1];
				$extension = strtolower($extension);
				if($extension =='mp3' || $extension =='m4a' || $extension =='wav'){			
					$audioOption[]=JHTML::_("select.option",$fille,$fille);
				}
			}
			$this->_package->lists['audio_dir']=JHTML::_("select.genericlist",$audioOption,"audio_dir", "size=10 ".$javascript,"text", "value",$directory);
		}else{
			$this->_package->lists['audio_dir']="";
		}
		
		
		$javascript	= 'onchange="change_radio_local();" onClick="change_radio_local();"';
		$allfiles=JFolder::files($directoryt); 
		$audioUrl=array();
		if(count($allfiles)>0){		
			foreach ($allfiles as $fille) {
				$extension_array = explode('.', $fille);
				$extension = $extension_array[count($extension_array)-1];
				$extension = strtolower($extension);
				if($extension =='mp3' || $extension =='m4a' || $extension =='wav'){		
					$audioUrl[]=JHTML::_("select.option",$fille,$fille);
				}
			}
			$this->_package->lists['audio_url']=JHTML::_("select.genericlist",$audioUrl,"localfile_a", "size=10 ".$javascript,"text", "value",$this->_package->local);
		}
		else{
			$this->_package->lists['audio_url'] = '<select onclick="change_radio_local();" onchange="change_radio_local();" size="10" name="localfile_a" id="localfile_a"><option value="0">...</option></select>';
		}
		//end audio
		
		
		//start docs
		$directory 	= JPATH_SITE.'/'.$config->docsin;
		chmod($directory, 0755);
		$directoryt = JPATH_SITE.'/'.$config->docsin.$getin;				
		
		$allfolders = JFolder::folders($directory); 
		$javascript	= 'onchange="changefolder();change_radio_local();" onClick="change_radio_local();"';
		$docsOption[]=JHTML::_("select.option","../","root");
		
		foreach ($allfolders as $fille) {
			$extension_array = explode('.', $fille);
			$extension = $extension_array[count($extension_array)-1];
			$extension = strtolower($extension);
			if($extension =='doc' || $extension =='docx' || $extension =='txt' || $extension =='pdf' || $extension =='csv' || $extension =='htm' || $extension =='html' || $extension =='xhtml' || $extension =='xml' || $extension =='sxw' || $extension =='rtf' || $extension =='odt' || $extension =='css' || $extension =='odp' || $extension =='pps' || $extension =='ppt' || $extension =='sxi' || $extension =='xls'){
				$docsOption[]=JHTML::_("select.option",$fille,$fille);
			}
		}
		$this->_package->lists['docs_dir']=JHTML::_("select.genericlist",$docsOption,"docs_dir", "size=1 ".$javascript,"text", "value",$directory);
		
		
		$allfiles=JFolder::files($directoryt); 
		$javascript 	= 'onchange="change_radio_local();" onClick="change_radio_local();"';
		if(count($allfiles)>0){
			foreach ($allfiles as $fille) {
				$extension_array = explode('.', $fille);
				$extension = $extension_array[count($extension_array)-1];
				$extension = strtolower($extension);
				if($extension =='doc' || $extension =='docx' || $extension =='txt' || $extension =='pdf' || $extension =='csv' || $extension =='htm' || $extension =='html' || $extension =='xhtml' || $extension =='xml' || $extension =='sxw' || $extension =='rtf' || $extension =='odt' || $extension =='css' || $extension =='odp' || $extension =='pps' || $extension =='ppt' || $extension =='sxi' || $extension =='xls' || $extension =='xlsx'){
				$docsOption[]=JHTML::_("select.option",$fille,$fille);
				}
			}
			$this->_package->lists['docs_url']=JHTML::_("select.genericlist",$docsOption,"localfile_d", "size=10 ".$javascript,"text", "value",$this->_package->local);
		}	
		else{
			$this->_package->lists['docs_url'] = '<select onclick="change_radio_local();" onchange="change_radio_local();" size="10" name="localfile_d" id="localfile_d"><option value="0">...</option></select>';
		}
			
		//end docs
		
		//start files
		$directory 	= JPATH_SITE.'/'.$config->filesin;
		chmod($directory, 0755);
		$directoryt = JPATH_SITE.'/'.$config->filesin.$getin;				
		
		$allfolders = JFolder::folders($directory); 
		$javascript	= 'onchange="changefolder();change_radio_local();" onClick="change_radio_local();"';
		$filesOption[]=JHTML::_("select.option","../","root");
		
		foreach ($allfolders as $fille) {
			$extension_array = explode('.', $fille);
			$extension = $extension_array[count($extension_array)-1];
			$extension = strtolower($extension);
			if($extension =='zip' || $extension =='exe'){					
				$filesOption[]=JHTML::_("select.option",$fille,$fille);
			}
		}
		$this->_package->lists['files_dir']=JHTML::_("select.genericlist",$filesOption,"files_dir", "size=1 ".$javascript,"text", "value",$directory);
		
		
		$allfiles=JFolder::files($directoryt); 
		$javascript 	= 'onchange="change_radio_local();" onClick="change_radio_local();"';
		if(count($allfiles)>0){
			foreach ($allfiles as $fille) {
				$extension_array = explode('.', $fille);
				$extension = $extension_array[count($extension_array)-1];
				$extension = strtolower($extension);
				if($extension =='zip' || $extension =='exe'){					
				$filesOption[]=JHTML::_("select.option",$fille,$fille);
				}
			}
			$this->_package->lists['files_url']=JHTML::_("select.genericlist",$filesOption,"localfile_f", "size=10 ".$javascript,"text", "value",$this->_package->local);
		}	
		else{
			$this->_package->lists['files_url'] = '<select onclick="change_radio_local();" onchange="change_radio_local();" size="10" name="localfile_f" id="localfile_f"><option value="0">...</option></select>';
		}	
		//end files	
		
		return $this->_package;
		
		if(intval($this->_package->id) == 0){
			$this->_package->hide_name = 1;
		}
	}
	
	function getdesc($id){
		$db =  JFactory::getDBO();
		$sql="SELECT instructions FROM #__guru_media WHERE id = ".$id." ";
		$db->setQuery($sql);
		$db->loadResult();
		return true;
	}
	
	function getMediaInfo($id){
		$db =  JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_media WHERE id = ".$id." ";
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}
	
	function store () {
		$database =  JFactory::getDBO();
		$item = $this->getTable('guruMedia');
		$data = JFactory::getApplication()->input->post->getArray();	
		$data['code_v']=JFactory::getApplication()->input->get('code_v','','raw');	
		$data['code_v'] = str_replace('s-cript', 'script',$data['code_v']);
		$data['text']=JFactory::getApplication()->input->get('text','','raw');
		$data["id"] = intval($data["id"]);
		
		if(trim($data["id"]) == ""){
			$data["id"] = 0;
		}

		if(trim($data["height"]) == ""){
			$data["height"] = 0;
		}

		$config=$this->getConfig();
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		
		$registry->set('type', $data['type']);
		$registry->set('category_id', $data['category_id']);
		//start video type
		if($data['type']=='video'){
			$data['code'] = '';
			$data['url'] = '';
			$data['local'] = '';
			
			if(!isset($data['source_v'])){
				if(isset($data['localfile'])&&($data['localfile']!='')) {
					$data['source']='local';
					$data['uploaded'] = 1;
				} elseif (isset($data['code_v'])){
					$data['source']='code';
				} elseif (isset($data['url_v'])){
					$data['source']='url';
				}
			}	
			else {
				$data['source']=$data['source_v'];
				$data['uploaded'] = 1;
			}
			
			
			if($data['source'] == 'code')
				$data['code'] = $data['code_v'];	
			if($data['source'] == 'url')
				$data['url'] = $data['url_v'];	
			
			if($data['source'] == 'local'){
				if(strpos($data['localfile'],$config->videoin)!==false){
					$data['localfile'] = substr($data['localfile'],strlen($config->videoin)+1);
				}
				$data['local']	= $data['localfile'];
			}
			$data['width'] = intval(@$data['width_v']);
			$data['height'] = intval(@$data['height_v']);
		}	
		//end video type
		
		//start audio type
		if($data['type']=='audio'){
			$data['code'] = '';
			$data['url'] = '';
			$data['local'] = '';
			$data['code_a'] = str_replace('s-cript', 'script',$data['code_a']);
			
			if(isset($data['source_a'])){
				$data['source'] = $data['source_a'];
				
				if($data['source_a'] == 'code'){
					$data['code'] = $data['code_a'];	
				} elseif($data['source_a'] == 'url'){
					$data['url'] = $data['url_a'];	
				} elseif($data['source_a'] == 'local'){
					if(isset($data['was_uploaded']) && $data['was_uploaded'] == 1){
						$data['local'] = $data['image'];
						$data['uploaded'] = 1;
					}	
					elseif(isset($data['localfile']) && $data['localfile'] != ''){
						$data['local']	= $data['localfile_a'];
						$data['uploaded'] = 0;
					}	
				} 
			}
	
			if(isset($data['source'])&&($data['source'] == 'local')){
				$data['local']	= $data['localfile_a'];
			}
			$data['width'] = $data['width_a'];
			$data['height'] = $data['height_a'];
		}					
		//end audio type
		
		//start image type
		if($data['type']=='image'){
			if($data['media_prop']=='w'){
				if($data['media_fullpx']>0) 
					$data['width'] = intval($data['media_fullpx']);
				else
					$data['width'] = 200;	
				$data['height'] = 0;
			}
			if($data['media_prop']=='h'){
				if($data['media_fullpx']>0) 
					$data['height'] = intval($data['media_fullpx']);
				else
					$data['height'] = 200;	
				$data['width'] = 0;
			}		
			if(isset($data['image']))
				$data['local'] = $data['image'];		
			$data['url'] = '';	
			$data['code'] = '';		
		}
		//end image type
		
		//start document type
		if($data['type']=='docs'){
			$data['code'] = '';
			$data['url'] = '';
			$data['local'] = '';

			$data['source'] = $data['source_d'];
	
			if($data['source_d'] == 'url')
				$data['url'] = $data['url_d'];	
			if($data['source_d'] == 'local'){
				$data['local']	= $data['localfile_d'];
			}		
			 // else we display the doc in a LINK
			if($data['display_as'] == 'link'){
				$data['width'] = 1; // else we display the doc in a LINK
				$data['height'] = 0;
			}
		}	
		//end document type
		
		//start files type
		if($data['type']=='file'){
			$data['code'] = '';
			$data['url'] = '';
			$data['local'] = '';

			$data['source'] = $data['source_f'];
	
			if($data['source_f'] == 'url')
				$data['url'] = $data['url_f'];	
			if($data['source_f'] == 'local'){
				$data['local']	= $data['localfile_f'];
			}		
			 // else we display the doc in a LINK
			
			$data['width'] = 300; 
			$data['height'] = 20;
		}	
		//end files type
		
		if($data['type']=='url'){
			$data['source'] = '';		
			$data['local'] = '';
			//$data['width'] = 0; // if it's 0 then we display the doc in a WRAPPER
			if($data['display_as2'] == 'link'){
				$data['width'] = 1; // else we display the doc in a LINK
			}
			else{
				if(isset($data['width_u']) && $data['width_u'] == 1){
					$data['width_u'] = 0;
				}
				elseif(!isset($data['width_u'])){
					$data['width_u'] = 0;
				}
				
				$data['width'] = $data['width_u'];
				$data['height'] = $data['height_u'];
			}
			//$data['height'] = 200;
			$data['url'] = $data['url'];
			$data['code'] = '';
		}
				
		if($data['type']=='text'){
			$data['code'] = $data['text'];	
			$data['local'] = '';
			$data['width'] = 0;
			$data['height'] = 0;	
			$data['url'] = '';
			
			/*if($data['text'] == '' || $data['text'] == NULL){
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('isempty', "1");
				return false;
			}*/
		}
		if($data['type']=='Article'){
			$data['code'] = $data['articleid'];	
			$data['local'] = '';
			$data['width'] = 0;
			$data['height'] = 0;	
			$data['url'] = '';	
		}

		if ($data['type'] === 'zoom') {
			$data['source'] = 'zoom';
			$code = new JRegistry;
			$code->set('meeting_id', $data['meeting_id_z']);
			$code->set('meeting_pass', $data['meeting_password_z']);
			$code->set('invite_link', $data['invite_link_z']);
			$data['code'] = $code->toString();
		}
		
		if(!isset($data["hide_name"])){
			$data["hide_name"] = 0;
		}
		
		if (!$item->bind($data)){
			$this->setError($item->getError());
			return false;
		}
		
		if (!$item->check()) {
			$this->setError($item->getError());
			return false;
		}
		if (!$item->store()) {
			$this->setError($item->getError());
			return false;
		}

		if (intval($data['id']) > 0) {
			$newid = intval($data['id']);
		} else {
			if ((isset($newid)) && ($newid > 0)){
			}
			else {
				$sql = "SELECT id FROM #__guru_media ORDER BY id DESC LIMIT 1 ";
				$database->setQuery( $sql );
				$newid = $database->loadColumn();
				$newid = $newid[0];				
			}
		}
		
		$registry->set('type', "");
		
		return $newid;
	}	
	
	/*  */
	function change_display_link($id){
		$db = JFactory::getDBO();
		$sql = "UPDATE #__guru_media SET width = '1',
				height = '0' WHERE #__guru_media.id =".intval($id)." LIMIT 1 ";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	function last_media(){
		$database = JFactory::getDBO();
		$ask = "SELECT id FROM #__guru_media ORDER BY id DESC LIMIT 1 ";
		$database->setQuery( $ask );
		$newid = $database->loadColumn();		
		return $newid["0"];
	}
	
	function publish () {
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$task = JFactory::getApplication()->input->get('task', '');
		$item = $this->getTable('guruMedia');
		$ret = 1;
		if($task == 'publish'){
			foreach($cids as $key=>$cid){
				$sql = "update #__guru_media set published='1' where id=".intval($cid);
				$db->setQuery($sql);
				if(!$db->execute()){
					//$this->setError($db->getErrorMsg());
					$ret = -1;
				}
			}
		}	
		elseif($task == 'unpublish'){
			foreach($cids as $key=>$cid){
				$sql = "select count(*) from #__guru_mediarel gmr, #__guru_task gt where gt.id=gmr.type_id and gmr.media_id=".intval($cid)." and gmr.type='scr_m'";
				$db->setQuery($sql);
				$db->execute();
				$count = $db->loadColumn();
				$count1 = @$count["0"];
				
				$sql = "select count(*) from #__guru_days where media_id=".intval($cid);
				$db->setQuery($sql);
				$db->execute();
				$count = $db->loadColumn();
				$count2 = @$count["0"];
				
				if(intval($count1) <= 0 && intval($count2) <= 0){
					$sql = "update #__guru_media set published='0' where id=".intval($cid);
					$db->setQuery($sql);
					if(!$db->execute()){
						//$this->setError($db->getErrorMsg());
						$ret = -1;
					}
				}
				else{
					$ret = -2;
				}
			}
		}
		return $ret;
	}
	
	function unpublish () { 
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$task = JFactory::getApplication()->input->get('task', '');
		$item = $this->getTable('guruMedia');
		if($task == 'unpublish'){
			foreach($cids as $key=>$cid){
				$sql = "select count(*) from #__guru_mediarel gmr, #__guru_task gt where gt.id=gmr.type_id and gmr.media_id=".intval($cid);
				$db->setQuery($sql);
				$db->execute();
				$count = $db->loadColumn();
				$count = @$count["0"];
				if(intval($count) > 0){
					$sql = "update #__guru_media set published='0' where id=".intval($cid);
					$db->setQuery($sql);
					if(!$db->execute()){
						//$this->setError($db->getErrorMsg());
						return false;
					}
				}
				else{
					return false;
				}
			}
		}
		return true;
	}
	
	function delete () {
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");

		$sql = "SELECT videoin,audioin,docsin FROM #__guru_config WHERE id ='1' ";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
		}
		$config = $db->loadObject();

		$item = $this->getTable('guruMedia');
		foreach ($cids as $cid) {
			$sql = "SELECT type, source, local FROM #__guru_media WHERE id =".$cid;
			$db->setQuery($sql);
			if (!$db->execute()) {
				return;
			}
			$resultmedia = $db->loadObject();	
	
			if($resultmedia->source == 'local')
			{ 
				if($resultmedia->type == 'audio')
					$imgfolder = $config->audioin;
				if($resultmedia->type == 'video')
					$imgfolder = $config->videoin;
				if($resultmedia->type == 'docs')
					$imgfolder = $config->docsin;		
				$targetPath = JPATH_SITE.'/'.$imgfolder.'/';
			}
		
			if (!$item->delete($cid)) {
				//$this->setError($item->getErrorMsg());
				return false;
			}
			
			if($resultmedia->type == 'quiz'){
				if(intval($resultmedia->source)>0){
					$sqldel = "DELETE FROM #__guru_quiz WHERE id = ".$resultmedia->source;
					$db->setQuery($sqldel);
					$db->execute();		
				}
				$sqldel = "DELETE FROM #__guru_mediarel WHERE type = 'scr_m' AND media_id = ".$cid;
				$db->setQuery($sqldel);
				$db->execute();		
			}
			
			$sqldel = "DELETE FROM #__guru_mediarel WHERE type = 'qmed' AND media_id = ".$cid;
			$db->setQuery($sqldel);
			$db->execute();
			
			$sqldel = "DELETE FROM #__guru_mediarel WHERE type = 'pmed' AND media_id = ".$cid;
			$db->setQuery($sqldel);
			$db->execute();
			
			$sqldel = "DELETE FROM #__guru_mediarel WHERE type = 'dmed' AND media_id = ".$cid;
			$db->setQuery($sqldel);
			$db->execute();	
			
			$sqldel = "DELETE FROM #__guru_mediarel WHERE type = 'task' AND media_id = ".$cid;
			$db->setQuery($sqldel);
			$db->execute();					
						
		} // end foreach

		return true;
	}
	
	function checkbox_construct( $rowNum, $recId, $name='cid' )
	{
		$db = JFactory::getDBO();
		
		$sql = " SELECT media_id FROM #__guru_mediarel WHERE type_id in ( SELECT media_id FROM #__guru_mediarel WHERE type_id in (SELECT id FROM #__guru_days WHERE pid in (SELECT id FROM #__guru_order GROUP BY id)) AND type = 'dtask' ) AND  type = 'task' ";
				
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}	
		$result = $db->loadColumn();	
		
		$sql = "SELECT influence FROM #__guru_config WHERE id = 1";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
			}
		$influence = $db->loadResult(); // we have selected the INFLUENCE 
		
		if(($influence==0 && in_array($recId, $result)))
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


	public static function getConfig(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config WHERE id = '1' ";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
		}
		$config = $db->loadObject();
		return $config;
	}
	
	function preview(){
		return true;
	}	
	
	function getMainMedia(){
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		if($id == 0){
			$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
			$id = $cid["0"];
		}
		
		$database = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_media
				WHERE id = ".$id; 
		$database->setQuery($sql);
		$media = $database->loadObject();
		
		$media->code=stripslashes($media->code);
		if ($media->type == 'Article') {
			$id = $media->code;
			include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');
			$class_guru_task = new guruAdminModelguruTask();
			$media->code = $class_guru_task->getArticleById($id);
		}
		
		$configs = $this->getConfig();
		$video_size = $configs->default_video_size;
		
		if($media->type != "url"&& $media->type != "image" && $media->type != "docs" && $media->type != "audio"  && $media->option_video_size == 0){
			if(trim($video_size) != ""){
				$temp = explode("x", trim($video_size));
				$media->width = $temp["1"];
				$media->height = $temp["0"];
			}
		}	
		if($media->width==0){
			$media->width=400;
		}

		$media->code=$this->parse_media($media);
		return $media;	
	}
	
	function parse_media ($media){
		$db = JFactory::getDBO(); 	
		$configs =$this->getConfig();		
	
		$no_plugin_for_code = 0;
		$aheight=0; 
		$awidth=0; 
		$vheight=0; 
		$vwidth=0;
		
		//start video
		if($media->type=='video'){
			if ($media->source=='url' || $media->source=='local'){
				if ($media->width == 0 || $media->height == 0){
					$media->width=300; 
					$media->height=400;
				}	
			}elseif ($media->source=='code'){
				if ($media->width == 0 || $media->height == 0){
					//parse the code to get the width and height
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
							$media->height=300;
							$media->width=400;
						}	
					}else{
						$media->height=300; 
						$media->width=400;
					}	
				}else{
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
		elseif($media->type=='audio'){
			if ($media->source=='url' || $media->source=='local'){	
				if ($media->width == 0 || $media->height == 0){
					$media->width=20; 
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
							$media->height=20; 
							$media->width=300;
						}	
					}else{
						$media->height=20; 
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
		
		$parts=explode(".",$media->local);
		$extension=strtolower($parts[count($parts)-1]);
		
		if($media->type=='video' || $media->type=='audio'){
			if($media->type=='video' && $extension=="avi"){
				$media->code = '<object id="MediaPlayer1" CLASSID="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" type="application/x-oleobject" width="'.$media->width.'" height="'.$media->height.'">
<param name="fileName" value="'.JURI::root().$configs->videoin."/".$media->local.'">
<param name="animationatStart" value="true">
<param name="transparentatStart" value="true">
<param name="autoStart" value="true">
<param name="showControls" value="true">
<param name="Volume" value="10">
<embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" src="'.JURI::root().$configs->videoin."/".$media->local.'" name="MediaPlayer1" width="'.$media->width.'" height="'.$media->height.'" autostart="1" showcontrols="1" volume="10">
</object>';
			}
			elseif ($no_plugin_for_code == 0){
				if($media->type=='video' && $media->source == "url"){
					require_once(JPATH_ROOT .'/components/com_guru/helpers/videos/helper.php');
					$parsedVideoLink = parse_url($media->url);
					preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $parsedVideoLink['host'], $matches);
					$domain	= $matches['domain'];
					
					if(!empty($domain)){
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
							$videoPlayer	= $videoObj->getViewHTML($video_id, $media->width, $media->height);
							$media->code = $videoPlayer;
						}
						else{
							$helper = new guruAdminHelper();
							$temp_media = $media;
							$temp_media->source = 'local';
							$temp_media->local = $temp_media->url;
							$temp_media->exception = "1";
							
							$media->code = $helper->create_media_using_plugin($temp_media, $configs, $awidth, $aheight, $vwidth, $vheight);
						}
					}
				}
				else{
					$helper = new guruAdminHelper();
					$media->code = $helper->create_media_using_plugin($media, $configs, $awidth, $aheight, $vwidth, $vheight);
				}
			}
		}
		//end audio

		//start docs type
		if($media->type=='docs'){
			//$media->code = 'The selected element is a text file that can\'t have a preview';	
			$media->code = JText::_("GURU_NO_PREVIEW");
			
			if($media->source == 'local' && (substr($media->local,(strlen($media->local)-3),3) == 'txt' || substr($media->local,(strlen($media->local)-3),3) == 'pdf') && $media->width > 1) {
				$media->code='<div class="contentpane">
							<iframe id="blockrandom" name="iframe" src="'.JURI::root().$configs->docsin.'/'.$media->local.'" width="'.$media->width.'" height="'.$media->height.'" scrolling="auto" align="top" frameborder="2" class="wrapper"> This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
						</div>';
				if($media->show_instruction ==2){
				$media->code = stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br />';
			}
				elseif($media->show_instruction ==1){
					$media->code = stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br /><div  style="text-align:center"><i>'.$media->instructions.'</i></div>';

				}	
				elseif($media->show_instruction ==0){
					$media->code = '<div  style="text-align:center"><i>'.$media->instructions.'</i></div>'.stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br />';

				}	
				return 	$media->code;
			}
							
			elseif($media->source == 'local' && $media->width == 1){
				$media->code='<br /><a href="'.JURI::root().$configs->docsin.'/'.$media->local.'" target="_blank">'.$media->name.'</a>';
					if($media->show_instruction ==2){
						$media->code = stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br />';
					}
						elseif($media->show_instruction ==1){
							$media->code = stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br /><div  style="text-align:center"><i>'.$media->instructions.'</i></div>';
		
						}	
						elseif($media->show_instruction ==0){
							$media->code = '<div  style="text-align:center"><i>'.$media->instructions.'</i></div>'.stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br />';
		
						}
						return 	$media->code;	
			}
			
			elseif($media->source == 'url'  && $media->width == 0){
				$media->code='<div class="contentpane">
							<iframe id="blockrandom" name="iframe" src="'.$media->url.'" width="100%" height="600" scrolling="auto" align="top" frameborder="2" class="wrapper"> This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe> </div>';		
			}				
			else if($media->source == 'url'  && $media->width == 1){
				$media->code='<a href="'.$media->url.'" target="_blank">'.$media->name.'</a>';		
			}	
		}
		//end doc
	
		//start url
		if($media->type=='url'){ 
			if($media->width > 1) {
				$media->code='<div class="contentpane">
							<iframe id="blockrandom" name="iframe" src="'.$media->url.'" width="800px" height="600px" scrolling="auto" align="top" frameborder="2" class="wrapper"> This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
						</div>';
					if($media->show_instruction ==2){
						$media->code = stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br />';
					}
						elseif($media->show_instruction ==1){
							$media->code = stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br /><div  style="text-align:center"><i>'.$media->instructions.'</i></div>';
		
						}	
						elseif($media->show_instruction ==0){
							$media->code = '<div  style="text-align:center"><i>'.$media->instructions.'</i></div>'.stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br />';
		
						}
						return 	$media->code;	
			}
			else{
				$media->code = '<a href="'.$media->url.'" target="_blank">'.$media->url.'</a>';
			}
			
		}
		//end url

		//start image				
		if($media->type=='image'){
			$media->code = '<img width="'.$media->width.'" height="'.$media->height.'" src="'.JURI::root().$configs->imagesin.'/media/thumbs/'.$media->local.'" />';	
		}
		//end image
		
		//start text
		if($media->type=='text'){
			$media->code=$media->code;
		}
		//end text
		
		//start docs type
		if($media->type=='file'){	
			$media->code = JText::_('GURU_NO_PREVIEW');	
			$x = filesize(JPATH_SITE.DIRECTORY_SEPARATOR.$configs->filesin.'/'.$media->local)/(1024*1024);
			$x = number_format($x, 2, '.', '');
			if($media->source == 'local' && (substr($media->local,(strlen($media->local)-3),3) == 'zip' || substr($media->local,(strlen($media->local)-3),3) == 'exe')) {
				$media->code='<br /><a href="'.JURI::root().$configs->filesin.'/'.$media->local.'" target="_blank">'.$media->local." (".$x." Ko)".'</a>';
				//return stripslashes($media->code);
					if($media->show_instruction ==2){
						$media->code = stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br />';
					}
						elseif($media->show_instruction ==1){
							$media->code = stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br /><div  style="text-align:center"><i>'.$media->instructions.'</i></div>';
		
						}	
						elseif($media->show_instruction ==0){
							$media->code = '<div  style="text-align:center"><i>'.$media->instructions.'</i></div>'.stripslashes($media->code).'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br />';
		
						}
						return 	$media->code;	
			}			
			else if($media->source == 'url'){
				$media->code='<a href="'.$media->url.'" target="_blank">'.$media->name.'</a>';		
			}	
		}
		//end doc


		if ($media->type === 'zoom') {
			$data = new JRegistry($media->code);
			$html = '<h3>'.JText::_('GURU_MEDIA_ZOOM_MEETING_ID').': ' . $data->get('meeting_id') . '</h3>';
			$html .= '<h3>'.JText::_('GURU_MEDIA_ZOOM_MEETING_PASSWORD').': ' . $data->get('meeting_pass') . '</h3>';
			$html .= '<div><a class="btn btn-primary" target="_blank" href="' . $data->get('invite_link') . '" >'.JText::_('GURU_ZOOM_CLICK_TO_JOIN').'</a></div>';
			return $html;
		}
		

		if($media->type=='text'){
			$media->code = $media->code;
		}
		else{
			if($media->show_instruction ==2){
				$media->code = $media->code.'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br />';
			}
				elseif($media->show_instruction ==1){
					$media->code = $media->code.'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br /><div  style="text-align:center"><i>'.$media->instructions.'</i></div>';

				}	
				elseif($media->show_instruction ==0){
					$media->code = '<div  style="text-align:center"><i>'.$media->instructions.'</i></div>'.$media->code.'<br /><br /><div style="text-align:center"><i>'.$media->name.'</i></div><br />';

				}			
		}
		return stripslashes($media->code);
	}	
	
			
	
	
	
	function duplicate () {
		
		$cid	= JFactory::getApplication()->input->get('cid', array(), "raw");
		$n		= count( $cid );

		foreach ($cid as $id)
		{
			$row 	= $this->getTable('guruMedia');
			$db =  JFactory::getDBO();
			// load the row from the db table
			$row->load( (int) $id );
			
			$row->name 	= JText::_( 'GURU_MEDIA_COPY_TITLE' ).' '.$row->name;
			$row->id 			= 0;
			
			if($row->local!=NULL && $row->local!='NULL' && $row->local!='')
			{
				$sql = "SELECT videoin,audioin,docsin FROM #__guru_config WHERE id ='1' ";
				$db->setQuery($sql);
				if (!$db->execute()) {
					return;
				}
				$config = $db->loadObject();
		
				if($row->source == 'local')
					{ 
						if($row->type == 'audio') $imgfolder = $config->audioin;
						if($row->type == 'video') $imgfolder = $config->videoin;
						if($row->type == 'docs') $imgfolder = $config->docsin;		
						$targetPath = JPATH_SITE.'/'.$imgfolder.'/';		
						copy($targetPath.$row->local, $targetPath.'copy_'.$row->local);
					}
				$row->local = 'copy_'.$row->local;	
			}

			if (!$row->check()) {
				return JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			}
			if (!$row->store()) {
				return JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			}
			$row->checkin();
			unset($row);
		}
	return 1;
				
	}				
	
	function generate_quiz_list($qid){
		$db = JFactory::getDBO();
		$sql = "SELECT id, name FROM #__guru_quiz WHERE published=1";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
		}
		$allquizes = $db->loadObjectList();

	    $the_quiz_list  =  JHTML::_( 'select.genericlist', $allquizes, 'qid', 'class="inputbox" size="1"', "id", "name", $qid);	
		
		return 	$the_quiz_list;	
	}	
	
	public static function now_selected_media ($mediaid){
		$db = JFactory::getDBO();
		if((isset($mediaid)) && ($mediaid != "")){
			$sql = "SELECT local FROM #__guru_media WHERE id = ".$mediaid;
			$db->setQuery($sql);
			if (!$db->execute()) {
				return;
			}
			$now_selected = $db->loadColumn();	
		}
		else{
			$now_selected = "";
		}	
		return @$now_selected[0];
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
	
	if($the_media->type=='text')
		{
			$media = $the_media->code;
		}
	if($the_media->type=='docs')
		{
		
			$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
			$the_base_link = $the_base_link[0];				
			//$media = 'The selected element is a text file that can\'t have a preview';
			$media = JText::_("GURU_NO_PREVIEW");
			
			if($the_media->source == 'local' && (substr($the_media->local,(strlen($the_media->local)-3),3) == 'txt' || substr($the_media->local,(strlen($the_media->local)-3),3) == 'pdf') && $the_media->width == 0)
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
	if($the_media->type=='quiz')
		{
			$media = '';
			
			$q  = "SELECT * FROM #__guru_quiz WHERE id = ".$the_media->source;
			$db->setQuery( $q );
			$result_quiz = $db->loadObject();				
			
			$media = $media. '<strong>'.$result_quiz->name.'</strong><br /><br />';
			$media = $media. $result_quiz->description.'<br /><br />';
			
			$q  = "SELECT * FROM #__guru_questions_v3 WHERE qid = ".$the_media->source;
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
							$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a1).'" \' type="checkbox" value="1a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a1.'</input><br />';
							$question_answers_number++;
						}	
					if($one_question->a2!='')
						{
							$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a2).'" \' type="checkbox" value="2a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a2.'</input><br />';
							$question_answers_number++;
						}	
					if($one_question->a3!='')
						{
							$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a3).'" \' type="checkbox" value="3a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a3.'</input><br />';
							$question_answers_number++;
						}	
					if($one_question->a4!='')
						{
							$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a4).'" type="checkbox" value="4a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a4.'</input><br />';
							$question_answers_number++;
						}	
					if($one_question->a5!='')
						{
							$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a5).'" type="checkbox" value="5a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a5.'</input><br />';
							$question_answers_number++;
						}	
					if($one_question->a6!='')
						{
							$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a6).'" type="checkbox" value="6a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a6.'</input><br />';
							$question_answers_number++;
						}	
					if($one_question->a7!='')
						{
							$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a7).'" type="checkbox" value="7a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a7.'</input><br />';
							$question_answers_number++;
						}	
					if($one_question->a8!='')
						{
							$question_answers_number++;
							$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a8).'" type="checkbox" value="8a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a8.'</input><br />';
						}
					if($one_question->a9!='')
						{
							$question_answers_number++;
							$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a9).'" type="checkbox" value="9a'.$question_number.'" name="q'.$question_number.'">'.$one_question->a9.'</input><br />';		
						}
					if($one_question->a10!='')
						{
							$question_answers_number++;
							$media = $media.'<input onclick=\'document.getElementById("question_answergived'.$question_number.'").value="'.str_replace("'","$$$$$" ,$one_question->a10).'" type="checkbox" value="10a'.$question_number.'" name="'.$question_number.'">'.$one_question->a10.'</input><br />';		
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
					$media = $media.'<input type="hidden" value="'.str_replace("'","$$$$$" ,$one_question->text).'" name="the_question'.$question_number.'" id="the_question'.$question_number.'" />';
					
					$question_number++;																																								
				}		
			
			$media = $media.'<input type="hidden" value="'.($question_number-1).'" name="question_number" id="question_number" />';
			$media = $media.'<input type="hidden" value="'.$result_quiz->name.'" id="quize_name" name="quize_name"/>';
			
			
			$media = $media.'<br /><div align="left" style="padding-left:30px;"><input type="submit" value="'.JText::_("GURU_SUBMIT").'" onclick="get_quiz_result()" /></div>';	
		
			$media = $media.'</div>';
		}	
	
		if(isset($media)) {return $media;} else { return false;}
	}	

	function saveMass(){
		$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
		$image = JFactory::getApplication()->input->get("image", array(), "raw");
		$title = JFactory::getApplication()->input->get("title", array(), "raw");
		$url = JFactory::getApplication()->input->get("url", array(), "raw");
		$description = JFactory::getApplication()->input->get("description", array(), "raw");
		$category_id = JFactory::getApplication()->input->get("category_id", "0", "raw");
		$teacher_id = JFactory::getApplication()->input->get("teacher_id", "0", "raw");
		$step_access = JFactory::getApplication()->input->get("step_access", "2", "raw");
		
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		if(isset($cid) && count($cid) > 0){
			$sql = "select name from #__guru_media where type='video'";
			$db->setQuery($sql);
			$db->execute();
			$all_media = $db->loadColumn();
			$module_id = JFactory::getApplication()->input->get("module_id", "0");
				
			$sql = "select media_id from #__guru_mediarel where type_id='".intval($module_id)."' and type='dtask'";
			$db->setQuery($sql);
			$db->execute();
			$result_lessons_ids_list = $db->loadColumn();
			if(count($result_lessons_ids_list) == 0){
				$result_lessons_ids_list = array("0");
			}
			$result_lessons_ids_list = implode(",",$result_lessons_ids_list);
			
			$sql = "select max(ordering) from #__guru_task where id IN ( ".$result_lessons_ids_list.") order by ordering";
			$db->setQuery($sql);
			$db->execute();
			$max_ordering = $db->loadColumn();				
			$max_ordering = $max_ordering["0"];
			
			foreach($cid as $key=>$poz){
				$name = $title[$poz];
				$instructions = "";
				$type = "video";
				$source = "url";
				$code = "";
				$video_url = str_replace("&feature=youtube_gdata", "", $url[$poz]);
				$local = "";
				$width = "0";
				$height = "0";
				$published = "1";
				$category_id = $category_id;
				$auto_play = "1";
				$hide_name = "0";
				$video_image = $image[$poz];
				$video_description = $description[$poz];
				$author = intval($teacher_id);
				
				$new_video_id = "";
				
				if(in_array(trim($name), $all_media)){
					$sql = "select id from #__guru_media where name='".addslashes(trim($name))."'";
					$db->setQuery($sql);
					$db->execute();
					$result = $db->loadColumn();
					$new_video_id = @$result["0"];
				}
				else{
					$sql = "insert into #__guru_media (name, instructions, type, source, code, url, local, width, height, published, category_id, auto_play, hide_name, author, image, description) values ('".addslashes(trim($name))."', '".$instructions."', '".$type."', '".$source."', '".$code."', '".addslashes(trim($video_url))."', '".$local."', ".intval($width).", ".intval($height).",  ".intval($published).", ".intval($category_id).", ".intval($auto_play).", ".intval($hide_name).", ".intval($author).", '".addslashes(trim($video_image))."', '".addslashes(trim($video_description))."')";
					$db->setQuery($sql);
					if(!$db->execute()){
						return FALSE;
					}
					$sql = "select max(id) from #__guru_media";
					$db->setQuery($sql);
					$db->execute();
					$result = $db->loadColumn();
					$new_video_id = @$result["0"];
				}
				
				$course_id = JFactory::getApplication()->input->get("course_id", "0");
				if(intval($course_id) != 0 && intval($module_id) != 0){
					$lesson_id = "0";
					$max_ordering ++;
					$alias = JFilterOutput::stringURLSafe($name);
					// create default lesson
					$sql = "insert into #__guru_task (name, alias, difficultylevel, published, startpublish, time, ordering, step_access, final_lesson, forum_kunena_generatedt) values ('".addslashes(trim($name))."', '".addslashes(trim($alias))."', 'easy', '1', '".date("Y-m-d H:i:s")."', '0', '".$max_ordering."', '".intval($step_access)."', '0', '0')";
					$db->setQuery($sql);
					if($db->execute()){
						$sql = "select max(id) from #__guru_task";
						$db->setQuery($sql);
						$db->execute();
						$result = $db->loadColumn();
						$lesson_id = @$result["0"];
					}
					
					$sql = "INSERT INTO #__guru_mediarel (type, type_id, media_id, mainmedia) VALUES ('scr_l', '".intval($lesson_id)."', '6', '0')";
					$db->setQuery($sql);
					$db->execute();
					
					$sql = "INSERT INTO #__guru_mediarel (type, type_id, media_id, mainmedia, layout) VALUES ('scr_m', '".intval($lesson_id)."', '".intval($new_video_id)."', '1', '6')";
					$db->setQuery($sql);
					$db->execute();
					
					$sql = "INSERT INTO #__guru_mediarel (type, type_id, media_id, mainmedia, text_no) VALUES ('dtask', '".intval($module_id)."', '".intval($lesson_id)."', '0', '0')";
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}
		return TRUE;
	}
	
	function changeTeacher(){
		$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
		
		if(isset($cid) && count($cid) > 0){
			$db = JFactory::getDbo();
			$teacher_id = JFactory::getApplication()->input->get("teacher_id", "0");
			
			$sql = "update #__guru_media set author = '".intval($teacher_id)."' where id in (".implode(", ", $cid).")";
			$db->setQuery($sql);
			if($db->execute()){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}

	function changeCategory(){
		$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
		
		if(isset($cid) && count($cid) > 0){
			$db = JFactory::getDbo();
			$category_id = JFactory::getApplication()->input->get("category_id", "0");
			
			$sql = "update #__guru_media set `category_id` = '".intval($category_id)."' where id in (".implode(", ", $cid).")";
			$db->setQuery($sql);
			if($db->execute()){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	function ajaxSearchMedia(){
		$db = JFactory::getDbo();
		$text = JFactory::getApplication()->input->get("text", "", "raw");
		$answer_id = JFactory::getApplication()->input->get("answer_id", "-1", "raw");
		$list = "";
		
		if(trim($text) != ""){
			$sql = "select id, name from #__guru_media where name like '%".$db->escape(trim($text))."%' and type <> 'quiz'";
			$db->setQuery($sql);
			$db->execute();
			$media = $db->loadAssocList();
			
			if(isset($media) && count($media) > 0){
				$list = '<table>';
				
				foreach($media as $key=>$value){
					$list .= '<tr>';
					$list .= 	'<td>';
					
					if(intval($answer_id) == -1){
						$list .= 	'<a href="#" onclick="javascript:selectMediaFromList(\''.intval($value["id"]).'\', \''.addslashes($value["name"]).'\'); return false;">'.$value["name"]."</a>";
					}
					else{
						$list .= 	'<a href="#" onclick="javascript:selectMediaFromListForAnswers(\''.intval($value["id"]).'\', \''.addslashes($value["name"]).'\', \''.intval($answer_id).'\'); return false;">'.$value["name"]."</a>";
					}
					
					
					$list .= 	'</td>';
					$list .= '</tr>';
				}
				
				$list .= '</table>';
			}
			else{
				$list  = '<table>';
				$list .= 	'<tr>';
				$list .= 		'<td>';
				$list .= 			JText::_("GURU_NO_MATCHING");
				$list .= 		'</td>';
				$list .= 	'</tr>';
				$list .= '</table>';
			}
		}
		
		echo $list;
		die();
	}
};
?>