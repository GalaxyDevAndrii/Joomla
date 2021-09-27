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

jimport ("joomla.application.component.view");

class guruAdminViewguruTasks extends JViewLegacy {

	function display ($tpl =  null ) { 
		
		JToolBarHelper::title(JText::_('GURU_TASK_TASK_MANAGER'), 'generic.png');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::addNew();
		JToolBarHelper::addNew('duplicate',JText::_('GURU_DUPLICATE_TASK_BTN'));
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();		
		$programs = $this->get('listTasks');
		$this->programs = $programs;
		parent::display($tpl);

	}

	function addmedia ($tpl =  null ) { 
		$medias = $this->get('listaddmedia');
		$data_get = JFactory::getApplication()->input->get->getArray();
		
		if(isset($data_get['type'])&&($data_get['type']=='audio')){
			foreach($medias as $element){
				$modal_task = new guruAdminModelguruTask();
				$element->prevw = $modal_task->parse_audio($element->id);
			}
 		}

		$types = $this->get('distincttypes');
		$this->types = $types;
		$this->medias = $medias;
		
 		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}

	function addproject ($tpl =  null ) { 
		$projects = $this->get('listProjects');
		$this->projects = $projects;
		
 		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}

	function addscorm ($tpl =  null ) { 
		$scorms = $this->get('listScorms');
		$this->scorms = $scorms;
		
 		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}
	
	function addQuiz ($tpl =  null ) { 
		$quiz = $this->get('listQuiz');
		$this->quiz = $quiz;
		
 		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}
	
	function jumpbts($tpl = null){
		$days = $this->get('listDays');
		$this->days = $days;
		$data_get = JFactory::getApplication()->input->get->getArray();
		
		if(isset($data_get['id'])){
			$current = $this->get('CurrentJump');
		} else {
			$current = NULL;
		}
		$this->current = $current;
		parent::display($tpl);
	}
	
	
	function approve( &$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= ($row->approved=='Y') ? $imgY : $imgX;
		$task 	= ($row->approved=='Y') ? 'unapprove' : 'approve';
		$alt 	= ($row->approved=='Y') ? JText::_( 'Approve' ) : JText::_( 'Unapprove' );
		$action = ($row->approved=='Y') ? JText::_( 'Unapprove Item' ) : JText::_( 'Approve item' );

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
		<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}

	function time_difference($start_datetime, $end_datetime){
		// Splits the dates into parts, to be reformatted for mktime.
		$start_datetime = explode(" ", $start_datetime, 2);
		$end_datetime = explode(" ", $end_datetime, 2);
		
		$first_date_ex = explode("-",$start_datetime[0]);
		$first_time_ex = explode(":",$start_datetime[1]);
		$second_date_ex = explode("-",$end_datetime[0]);
		$second_time_ex = explode(":",$end_datetime[1]);
	
		// makes the dates and times into unix timestamps.
		$first_unix  = mktime($first_time_ex[0], $first_time_ex[1], $first_time_ex[2], $first_date_ex[1], $first_date_ex[2], $first_date_ex[0]);
		$second_unix  = mktime($second_time_ex[0], $second_time_ex[1], $second_time_ex[2], $second_date_ex[1], $second_date_ex[2], $second_date_ex[0]);
	
		// Gets the difference between the two unix timestamps.
		$timediff = $second_unix-$first_unix;
		
		// Works out the days, hours, mins and secs.
		$days=intval($timediff/86400);
		$remain=$timediff%86400;
		$hours=intval($remain/3600);
		$remain=$remain%3600;
		$mins=intval($remain/60);
		$secs=$remain%60;
		
		// Returns a pre-formatted string. Can be chagned to an array.
		$ARR = array();
		$ARR['days'] = $days;
		$ARR['hours'] = $hours;
		$ARR['mins'] = $mins;
		
		return $ARR;
	}
	
	/*
	List categories
	*/
	function list_all($search, $name, $category_id, $selected_categories=Array(), $size=10, $toplevel=true, $multiple=false) {

		$db = JFactory::getDBO();
		$data_post = JFactory::getApplication()->input->post->getArray();
		$q  = "SELECT parent_id FROM #__guru_taskcategoryrel ";
		if( $category_id )
		$q .= "WHERE child_id ='$category_id'";
		$db->setQuery($q);   
		$db->execute();
		$category_id=$db->loadResult();
		//$multiple = $multiple ? "multiple=\"multiple\"" : "";
		if (isset($data_post['category'])) $category_id=intval($data_post['category']);
		
		$search_submit = '';
		if($search==1)
			$search_submit = " onchange=\"document.topform1.submit()\" ";
		
		echo "<select ".$search_submit." class=\"inputbox\" name=\"$name\">\n";
		if( $toplevel ) {
			//echo "<option value=\"0\">(0) Top</option>\n";
			$selectedall = '';
			$selectedtop = '';
			if (isset($data_post['category']) && $data_post['category']=='50000') $selectedall=' selected="selected" ';
			if (isset($data_post['category']) && $data_post['category']=='0') $selectedtop=' selected="selected" '; 
			if($search==1)
			echo "<option value=\"50000\" ".$selectedall.">- All -</option>\n";
			echo "<option value=\"0\" ".$selectedtop.">(0) Top</option>\n";			
		}
		$this->list_tree($category_id, '0', '0', $selected_categories);
		echo "</select>\n";
	}
	/*
	List categories
	*/
	function list_tree($category_id="", $cid='0', $level='0', $selected_categories=Array() ) {

		$db = JFactory::getDBO();

		$level++;

		$q = "SELECT id, child_id, listorder, name FROM #__guru_taskcategory,#__guru_taskcategoryrel ";
		$q .= "WHERE #__guru_taskcategoryrel.parent_id='$cid' ";
		$q .= "AND #__guru_taskcategory.id=#__guru_taskcategoryrel.child_id ";
		$q .= "ORDER BY #__guru_taskcategory.listorder ASC";
		$db->setQuery($q);
		$allresults = $db->loadObjectList();
		foreach ($allresults as $child) {
			$child_id = $child->id;
			if ($child_id != $cid) {
				$selected = ($child_id == $selected_categories) ? "selected=\"selected\"" : "";
				
				echo "<option $selected value=\"$child_id\">\n";
			}
			for ($i=0;$i<$level;$i++) {
				echo "&#151;";
			}
			echo "($level)";
			//echo "&nbsp;-";
			echo "&nbsp;" . $child->name . "</option>";
			$this->list_tree($category_id, $child_id, $level, $selected_categories);
		}
	}

	function editForm($tpl = null) {
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$registry->set('addmed_tskmed_to_rep', NULL);
		
		$db = JFactory::getDBO();
		$program = $this->get('Task');
		$jumps = $this->get('Jumps');
		$this->jumps = $jumps;
		$isNew = ($program->id < 1);
		$text = $isNew?JText::_('GURU_NEW'):JText::_('GURU_EDIT');
		$advertiser_id = JFactory::getApplication()->input->get('advertiser_id', '');
		$task = JFactory::getApplication()->input->get('task', '');
		$mainmedia= '';
		$mainquiz= '';
		$the_layout = 1;
		$mmediam =  new stdClass();
		JToolBarHelper::title(JText::_('GURU_TASK').":<small>[".$text."]</small>");
		JToolBarHelper::save();

		if ($isNew) {
			JToolBarHelper::cancel();
			$program->published=1;
		} else {
			JToolBarHelper::apply();
			JToolBarHelper::cancel ('cancel', JText::_('GURU_CLOSE_TASK_BTN'));
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_media as b WHERE a.media_id=b.id AND a.mainmedia=1 AND a.type_id=".$program->id);
			$mainmedia = $db->loadObjectList();
			
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_media as b WHERE a.type='task' AND a.media_id=b.id AND a.mainmedia=0 AND a.type_id=".$program->id);
			$mmediam = $db->loadObjectList();
			
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_quiz as b WHERE a.type='tquiz' AND a.media_id=b.id AND a.mainmedia=1 AND a.type_id=".$program->id);
			$mainquiz = $db->loadObjectList();	
			
			$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type='scrnl' AND type_id=".$program->id);
			$the_layout = $db->loadResult();		
		}
		$lists = array();
		
		$published = '<input type="hidden" name="published" value="0">';
		if($program->published == 1){
			$published .= '<input type="checkbox" checked="checked" value="1" class="ace-switch ace-switch-5" name="published">';
		}
		else{
			$published .= '<input type="checkbox" value="1" class="ace-switch ace-switch-5" name="published">';
		}
		$published .= '<span class="lbl"></span>';
		
		@$lists['published'] = $published;
		
		//dificulty lists
		$dificulty[] = JHTML::_('select.option',  "none", JText::_('GURU_SELLEVEL'), 'value', 'option' );	
		$dificulty[] = JHTML::_('select.option',  "easy", JText::_('GURU_EASY'), 'value', 'option' );	
		$dificulty[] = JHTML::_('select.option',  "medium", JText::_('GURU_MEDIUM'), 'value', 'option' );	
		$dificulty[] = JHTML::_('select.option',  "hard", JText::_('GURU_HARD'), 'value', 'option' );		
		$javascript = '';
		
		if ($isNew) 
			$the_difficultylevel = 'easy';
		else
			$the_difficultylevel = $program->difficultylevel;
		
	  	@$lists['difficulty']  =  JHTML::_( 'select.genericlist', $dificulty, 'difficultylevel', 'class="inputbox" size="1"'.$javascript,'value', 'option', $the_difficultylevel);

	  	//all media
	  	$db->setQuery("SELECT id,name FROM #__guru_media ORDER BY id DESC");
		$allmedia = $db->loadObjectList();
		//all quiz
	  	$db->setQuery("SELECT id,name FROM #__guru_quiz ORDER BY id DESC");
		$allquiz = $db->loadObjectList();
		/*---------------------------------------------*/
		$medias[] = JHTML::_('select.option',  "0", JText::_('Add Media Files'), 'id', 'name' );
		$medias 	= array_merge( $medias, $allmedia );
		$javascript = ' style="margin-top: 3px; margin-bottom: 3px;"';
	  	@$lists['addmedias']  =  JHTML::_( 'select.genericlist', $medias, 'addmedia', 'multiple="multiple" class="inputbox" size="10"'.$javascript,'id', 'name', $program->difficultylevel);	
		/*---------------------------------------------*/
		$medias[] = JHTML::_('select.option',  "0", JText::_('Add Main Media'), 'id', 'name' );
		$medias 	= array_merge( $medias, $allmedia );
		$javascript = ' style="margin-top: 3px; margin-bottom: 3px;"';
	  	@$lists['addmainmedias']  =  JHTML::_( 'select.genericlist', $medias, 'addmainmedia', 'class="inputbox" size="1"'.$javascript,'id', 'name', $program->difficultylevel);
	  	/*---------------------------------------------*/
		$quiz[] = JHTML::_('select.option',  "0", JText::_('Add Quiz'), 'id', 'name' );
		$quiz 	= array_merge( $quiz, $allquiz );
		$javascript = ' style="margin-top: 3px; margin-bottom: 3px;"';
	  	@$lists['addquiz']  =  JHTML::_( 'select.genericlist', $medias, 'addquiz', 'class="inputbox" size="1"'.$javascript,'id', 'name', $program->difficultylevel);		
		//$javascript = ' style="display:none;"';
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$temp_lays = $registry->get('temp_lays', "");
		
		if(isset($temp_lays) && ($temp_lays == 'yes')){
			$db->setQuery("SELECT * FROM #__guru_media_templay WHERE ip='".ip2long($_SERVER['REMOTE_ADDR'])."'");
			$tem_lay = $db->loadObjectList();	
		}
		else{ 
			$tem_lay=NULL; 
		}
		
	  	//all media
		$this->tem_lay = $tem_lay;
	  	$this->program = $program;
	  	$this->mainmedia = $mainmedia;
		$this->mainquiz = $mainquiz;
	  	$this->mmediam = $mmediam;
		$this->lists = $lists;
		$this->the_layout = $the_layout;
		parent::display($tpl);

	}

	function uploadimage() { 
	$db = JFactory::getDBO();
	
		$sqla = "SELECT imagesin FROM #__guru_config LIMIT 1";
		$db->setQuery($sqla);
		$db->execute();
		$imgfolder = $db->loadResult(); 

	$targetPath = JPATH_SITE.'/'.$imgfolder.'/';
	$failed = '0'; 
	
	$file_request = JFactory::getApplication()->input->files->get( 'image_file', NULL);
	
	if (isset($file_request)) {
			
		$filename = $file_request['name'];
		if ($filename) {
			
			$filenameParts = explode('.', $filename);
			$extension = '';
			if (count($filenameParts) > 1)
				$extension = array_pop($filenameParts);
			$extension = strtolower($extension);
			if (!in_array($extension, array('jpg', 'jpeg', 'gif', 'png'))) {
				$text = strip_tags( addslashes( nl2br( "The image must be gif, png, jpg, jpeg." )));
				echo "<script>alert('$text'); </script>";
				$failed=1;
			}
			if ($failed != 1) {
			if (!move_uploaded_file ($file_request['tmp_name'],$targetPath.$filename)) {
				$text = strip_tags( addslashes( nl2br( "Upload of ".$filename." failed." )));
				echo "<script>alert('$text'); </script>";
			} else {
				return $filename;
				}
			}
		  }	
		}		
	}

}

?>