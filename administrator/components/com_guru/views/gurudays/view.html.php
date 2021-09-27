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
jimport('joomla.html.toolbar');

class guruAdminViewguruDays extends JViewLegacy {

	function display ($tpl =  null ) { 
		$app = JFactory::getApplication('administrator');
		$pid=$app->getUserStateFromRequest("pid","pid","0");
		$db = JFactory::getDBO();
		JToolBarHelper::title(JText::_('GURU_DAY_MANAGER'), 'generic.png');
		
		JToolBarHelper::addNew('new_course', JText::_('GURU_CREATE_NEW_COURSE'));
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::custom('edit_course', 'publish', 'publish', JText::_('GURU_EDIT_COURSE_INFO'), false);
		JToolBarHelper::cancel('back', JText::_('GURU_BACK'));
		
		$filters = $this->get('filters');
		$this->filters = $filters;

		$ads = $this->get('listDays');
		$this->ads = $ads;
		
		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
	
		// add a DUPLICATE button
		//$bar = JToolBar::getInstance('toolbar');		
		//$bar->appendButton( 'Popup', 'new', JText::_('GURU_DAY_NEW_GROUP'), 'index.php?option=com_guru&amp;controller=guruDays&amp;component=com_guru&amp;task=newmodule&amp;tmpl=component&amp;pid='.$pid, '600', '300' );		
		parent::display($tpl);

	}
	
	function approve( &$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= ($row->approved=='Y') ? $imgY : $imgX;
		$task 	= ($row->approved=='Y') ? 'unapprove' : 'approve';
		$alt 	= ($row->approved=='Y') ? JText::_( 'Approve' ) : JText::_( 'Unapprove' );
		$action = ($row->approved=='Y') ? JText::_( 'Unapprove Item' ) : JText::_( 'Approve item' );
		if (($row->zone > 0) || ((!$row->zone && $row->approved=='Y')))
			$onclick = 'onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')"';
		else 
			$onclick = 'onclick="alert(\''.JText::_( 'JS_SPECIFY_ZONE' ).'\');"';
			$href = '
		<a href="javascript:void(0);" '.$onclick.' title="'. $action .'">
		<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}
	
	function duplicate ($tpl =  null ) { 
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_program ";
		$db->setQuery($sql);
		$programs = $db->loadObjectList();
		$this->programs = $programs;
		parent::display($tpl);
	}	

	function addmainmedia ($tpl =  null ) {
		$medias = $this->get('mediaList');
		$this->medias = $medias;
		$filters = $this->get('mediaFilters');
		$this->filters = $filters;		
		parent::display($tpl);

	}

	function editForm($tpl = null) {
		$app = JFactory::getApplication('administrator');

		$db = JFactory::getDBO();
		$program = $this->get('day'); 
	    
		JToolBarHelper::title(JText::_('Day').":<small>[".$program->text."]</small>");
		if ($program->id<1) {
			JToolBarHelper::save('save', 'Save');
			JToolBarHelper::cancel('cancel', 'Cancel');

		} else {
			JToolBarHelper::save('save', 'Save');
			JToolBarHelper::apply();
			JToolBarHelper::cancel ('cancel', 'Cancel');
		}

		$this->program = $program;		
		parent::display($tpl);
	}

	function newModule($tpl = null){
		parent::display($tpl);
	}
	
	function getConfigs(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
	
	function uploadimage() { 
	//$absolutepath = JPATH_SITE;
	$database = JFactory::getDBO();
	$db = JFactory::getDBO();
 	//get the image folder 
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
				//mosErrorAlert("The image must be gif, png, jpg, jpeg, swf");
				$text = strip_tags( addslashes( nl2br( "The image must be gif, png, jpg, jpeg." )));
				echo "<script>alert('$text'); </script>";
				$failed=1;
			}
			if ($failed != 1) { 
			if (!move_uploaded_file ($file_request['tmp_name'],$targetPath.$filename)) {
				//mosErrorAlert("Upload of ".$filename." failed");
				$text = strip_tags( addslashes( nl2br( "Upload of ".$filename." failed." )));
				echo "<script>alert('$text'); </script>";
			} else {
				return $filename;
				}
			}
		  }	
		}		
	}

	
	function list_all($name, $category_id, $selected_categories=Array(), $size=10, $toplevel=true, $multiple=false) {
		$data_post = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDBO();

		$q  = "SELECT parent_id FROM #__guru_taskcategoryrel ";
		if( $category_id )
		$q .= "WHERE child_id ='$category_id'";
		$db->setQuery($q);   
		$db->execute();
		$category_id=$db->loadResult();
		//$multiple = $multiple ? "multiple=\"multiple\"" : "";
		if (isset($data_post['category'])) $category_id=intval($data_post['category']);
		echo "<select onchange=\"document.form1.submit()\" class=\"inputbox\" name=\"$name\">\n";
		if( $toplevel ) {
			$selectedall = '';
			$selectedtop = '';
			if (isset($data_post['category']) && $data_post['category']=='50000') $selectedall=' selected="selected" ';
			if (isset($data_post['category']) && $data_post['category']=='0') $selectedtop=' selected="selected" '; 
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
				$selected = ($child_id == $category_id) ? "selected=\"selected\"" : "";
				if( $selected == "" && @$selected_categories[$child_id] == "1") {
					$selected = "selected=\"selected\"";
				}
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

	function getTask_type($task_id){
		$db = JFactory::getDBO();
		$sql = "SELECT type FROM #__guru_media WHERE id = (SELECT media_id FROM #__guru_mediarel WHERE type = 'task' AND mainmedia = 1 AND type_id = '".$task_id."')";
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}	
		$result = $db->loadResult();	
		return $result;	
	}

}

?>