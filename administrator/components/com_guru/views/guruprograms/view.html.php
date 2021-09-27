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

jimport ("joomla.application.component.view");
require_once JPATH_COMPONENT.'/models/gurusubplan.php';
?>
<script type="text/javascript">
	function guruCheckboxEmail(value){
		coursetype = document.getElementById('course_type').value;
		lessonrelease = document.getElementById('lesson_release').value;

		if(value == 6 && (coursetype == 0 || (coursetype == 1 && lessonrelease ==0 ))){
			alert("This email can be sent only if the course if of sequential type, please change the type on the general tab!");
		}
	
	}

</script>
<?php
class guruAdminViewguruPrograms extends JViewLegacy {

	function display ($tpl =  null ) { 
		
		JToolBarHelper::title(JText::_('GURU_COURSEMAN'), 'generic.png');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::addNew();
		JToolBarHelper::addNew('duplicate',JText::_('GURU_DUPLICATE_PROGRAM_BTN'));
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();		
		
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		if(isset($data_post['course_publ_status'])) {
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('course_publ_status', $data_post['course_publ_status']);
		}
		
		if(isset($data_post['course_lock_status'])) {
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('course_lock_status', $data_post['course_lock_status']);
		}
		
		$programs = $this->get('Items');
		$this->programs = $programs;
		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;	
		
		parent::display($tpl);

	}
	
	function addmedia ($tpl =  null ) { 
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_media WHERE type in ('docs', 'file') ";
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		if(isset($data_post['search_text']) && $data_post['search_text']!="")
			$sql = $sql." AND name LIKE '%".$data_post['search_text']."%' " ;
		if(isset($data_post['media_select']) && $data_post['media_select']!='all')
			$sql = $sql." AND type='".$data_post['media_select']."' ";			
		$db->setQuery($sql);
		$medias = $db->loadObjectList();
		$this->medias = $medias;		
		$programs = $this->get('listPrograms');
		$this->programs = $programs;
		parent::display($tpl);
	}
	
	function addcourse ($tpl =  null ) { 
		$data_post = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_program WHERE 1=1 ";
		if(isset($data_post['search_text']) && $data_post['search_text']!="")
			$sql = $sql." AND name LIKE '%".$data_post['search_text']."%' " ;
		$db->setQuery($sql);
		$programs = $db->loadObjectList();
		$this->programs = $programs;
		parent::display($tpl);
	}
	
	function studentsenrolled ($tpl =  null ){ 
		JToolBarHelper::title(JText::_('GURU_COURSEMAN'), 'generic.png');
		JToolBarHelper::custom('export_button', 'download', 'download', 'Export', false, false);
		
		$customers = $this->get('Items');
		$pagination = $this->get('Pagination');
		$this->customers = $customers;
		$this->pagination = $pagination;
		
		$filters= $this->get('Filters');
		$this->filters = $filters;
		
		$programs = $this->get('listPrograms');
		$this->programs = $programs;
		
		$ads = $this->get('listDays');
		$this->ads = $ads;
		
		parent::display($tpl);	
	}	
	/*
	List categories
	*/
	function list_all($search, $name, $category_id, $selected_categories=Array(), $size=1, $toplevel=true, $multiple=false) {

		$db = JFactory::getDBO();

		$q  = "SELECT parent_id FROM #__guru_categoryrel ";
		if( $category_id )
		$q .= "WHERE child_id ='$category_id'";
		$db->setQuery($q);   
		$db->execute();
		$category_id=$db->loadResult();
		
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		if (isset($data_post['catid'])) $category_id=intval($data_post['catid']);
		$multiple = $multiple ? "multiple=\"multiple\"" : "";
		
		$search_submit = '';
		if($search==1)
			$search_submit = " onchange=\"document.adminForm.submit()\" ";		

		
		echo "<select ".$search_submit." class=\"inputbox\" size=\"$size\" $multiple id=\"$name\" name=\"$name\">\n";
		if( $toplevel ) {
			echo "<option value=\"-1\">".JText::_("GURU_CATEGORY_SEARCH")."</option>\n";
		}
		$this->list_tree($category_id, '0', '0', $selected_categories);
		echo "</select>\n";
	}
	
	function list_all2($search, $name, $category_id, $selected_categories=Array(), $size=1, $toplevel=true, $multiple=false) {

		$db = JFactory::getDBO();

		$q  = "SELECT parent_id FROM #__guru_categoryrel ";
		if($category_id){
			$q .= "WHERE child_id ='".$category_id."'";
		}
		$db->setQuery($q);   
		$db->execute();
		$category_id = $db->loadResult();
		
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		$multiple = $multiple ? "multiple=\"multiple\"" : "";
		if (isset($data_post['catid'])) $category_id=intval($data_post['catid']);
		if (isset($data_post['catid']) && intval($data_post['catid']) == 0) $selected= 'selected="selected"';
			else $selected = '';
		$search_submit = '';
		if($search==1)
			$search_submit = " onchange=\"document.adminForm.submit()\" ";		

		echo "<select ".$search_submit." class=\"inputbox\" size=\"$size\" $multiple name=\"$name\">\n";
		if( $toplevel ) {
			echo "<option value=\"-1\">".JText::_("GURU_ALLCATHEG2")."</option>\n";
			echo "<option value=\"0\" ".$selected.">".JText::_("GURU_TOP")."</option>\n";
		}
		$this->list_tree($category_id, '0', '0', $selected_categories);
		echo "</select>\n";
	}	
	
	
	function list_tree($category_id="", $cid='0', $level='0', $selected_categories=Array() ) {

		$db = JFactory::getDBO();
		$level++;

		$q = "SELECT id, child_id, ordering, name FROM #__guru_category,#__guru_categoryrel ";
		$q .= "WHERE #__guru_categoryrel.parent_id='$cid' ";
		$q .= "AND #__guru_category.id=#__guru_categoryrel.child_id ";
		$q .= "AND #__guru_category.published='1' ";
		$q .= "ORDER BY #__guru_category.ordering ASC";
		$db->setQuery($q);
		$allresults = $db->loadObjectList();

		foreach ($allresults as $child) {
			$selected = "";
			$child_id = $child->id;
			if ($child_id != $cid) {
				if( $selected_categories==$child_id) {
					$selected = "selected=\"selected\"";
				}
				echo "<option $selected value=\"$child_id\">\n";
			}
			for ($i=0;$i<$level;$i++) {
				echo "&#151;";
			}			
			echo "&nbsp;".$child->name."</option>";
			$this->list_tree($category_id, $child_id, $level, $selected_categories);
		}
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

	function editForm($tpl = null) {
		require_once(JPATH_BASE.'/../components/com_guru/helpers/helper.php');
		$guruHelper = new guruHelper();

        $lists = NULL;
		$db = JFactory::getDBO();
		$program = $this->get('Program');
		$isNew = ($program->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		$task = JFactory::getApplication()->input->get('task', '', 'get');
		JToolBarHelper::title(JText::_('GURU_COURSE').":<small>[".$text."]</small>");
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::save2new();
		
		$sql = "SELECT currency from #__guru_config where id=1";
		$db->setQuery($sql);
		$db->execute();
		$currency = $db->loadResult();
		$currency = "GURU_CURRENCY_".$currency;
		$currency = JText::_(''.$currency.'');
		
		$sql = "SELECT currencypos from #__guru_config where id=1";
		$db->setQuery($sql);
		$db->execute();
		$currencypos = $db->loadResult();
		if($currencypos == 0){
	    	$positionB = $currency."&nbsp;&nbsp;";
		}
		else{
			$positionA = "&nbsp;&nbsp;".$currency;
		}
		
		if(!$isNew){
			JToolBarHelper::custom('add_edit_lesson', 'publish', 'publish', JText::_('GURU_ADD_EDIT_LESSONS'), false);
		}
		
		if($isNew){
			JToolBarHelper::cancel();	
			$mmediam = new stdClass();
			$mmediam_req = NULL;			
		} 
		else{
			JToolBarHelper::cancel ('cancel', 'Close');
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_media as b WHERE a.type='pmed' AND a.media_id=b.id AND a.mainmedia=0 AND a.type_id=".$program->id." order by a.order asc");
			$mmediam = $db->loadObjectList();
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_program as b WHERE a.type='preq' AND a.media_id=b.id AND a.mainmedia=0 AND a.type_id=".$program->id);
			$mmediam_preq = $db->loadObjectList();
		}
       
        $price_formats = array(
			'1' => JText::_('GURU_PF1'),
			'2' => JText::_('GURU_PF2'),
			'3' => JText::_('GURU_PF3'),
			'4' => JText::_('GURU_PF4'),
			'5' => JText::_('GURU_PF5')
		);
		foreach ( $price_formats as $key => $pf ) {
			$pfs[] = JHTML::_( 'select.option', $key, $pf );
		}
		$lists['priceformat'] = '<label for="priceformat">' . JText::_( 'GURU_PRICE_FORMAT' ) . '</label>';
		$lists['priceformat'] .= JHTML::_( 'select.genericlist', $pfs, 'priceformat', ' id="priceformat" class="inputbox" size="1" ', 'value', 'text', $program->priceformat );
	
		$plans = guruAdminModelguruSubplan::getAllPlans();
    	
        $program_plans = $this->get('ProgramPlans');
        
        $renewals = $plans;
        $program_renewals = $this->get( 'ProgramRenewals' );
        
        $reminds = $this->get('AllReminds');
        $program_reminds = $this->get('ProgramReminds');
        $plains_html = "<table id='subscriptions'>";
		$plains_html .= "<tr style='background:#999'>
							<th width='1%' style='padding:0.5em;'>" . JText::_( 'GURU_DEFAULT' ) . "</th>
                            <th width='1%' style='padding:0.5em;'><input type='checkbox' id='splains' name='splains' value='' onclick='checkPlans(\"subscriptions\");'/><span class=\"lbl\"></span></th>
                            <th style='padding:0.5em;'>" . JText::_( 'GURU_NAME' ) . "</th>
                            <th style='padding:0.5em;'>" . JText::_( 'GURU_RTERMS' ) . "</th>
                            <th style='padding:0.5em;'>" . JText::_( 'GURU_PRICE' ) . "</th>
                        </tr>";
						
		if(is_array($plans))
		
		$k = 0;
		foreach($plans as $plain ) {
			$checked = false;
			$default = false;
			$price = "";
            if ( is_array($program_plans) ) {
                foreach ( $program_plans as $plain_value ) {
                    if ( $plain_value->plan_id == $plain->id ) {
                        $checked = true;
                        $price = $plain_value->price;
                        if ( $plain_value->default == 1 ) {
                            $default = true;
                        }
                    }
                }
            }

            $display_price = $guruHelper->displayPrice($price);
            
			$plains_html .= "<tr>";
			$plains_html .= "<td style='padding:0.5em;'><input type='radio' name='subscription_default' value='" . $plain->id . "' " . (($default) ? "checked='checked'" : "") . "/><span class=\"lbl\"></span></td>";
			$plains_html .= "<td style='padding:0.5em;'><input class='plain' type='checkbox' id='subscriptions_".$k."' name='subscriptions[]' value='" . $plain->id . "'" . (($checked) ? " checked='checked'" : "") . "/><span class=\"lbl\"></span></td>";
			$plains_html .= "<td style='padding:0.5em;'>" . $plain->name . "</td>";
            if ($plain->term != 0) {
                $zplain = $plain->term . ' ' . $plain->period;
            } else {
                $zplain = ucfirst(JText::_( 'GURU_UNLIMPROMO' ));
            }
            
			$plains_html .= "<td style='padding:0.5em;'>" . $zplain . "</td>";
			$plains_html .= "<td style='padding:0.5em;'>".@$positionB."<input type='text' id='subscription_price_".$k++."' name='subscription_price[" . $plain->id . "]' value='" . $display_price . "' />".@$positionA."</td>";
			$plains_html .= "</tr>\n";
		}
		$plains_html .= "</table>";

        $plains_html2 = "<table id='renewals'>";
		$plains_html2 .= "<tr style='background:#999'>
							<th width='1%' style='padding:0.5em;'>" . JText::_( 'GURU_DEFAULT' ) . "</th>
                            <th width='1%' style='padding:0.5em;'><input type='checkbox' id='splains' name='splains' value='' onclick='checkPlans(\"renewals\");'/><span class=\"lbl\"></span></th>
                            <th style='padding:0.5em;'>" . JText::_( 'GURU_NAME' ) . "</th>
                            <th style='padding:0.5em;'>" . JText::_( 'GURU_RTERMS' ) . "</th>
                            <th style='padding:0.5em;'>" . JText::_( 'GURU_PRICE' ) . "</th>
                        </tr>";
		if(is_array($renewals))
		$k = 0;
		foreach($renewals as $plain ) {
			$checked = false;
			$default = false;
			$price = "";
            if ( is_array($program_renewals) ) {
                foreach ( $program_renewals as $plain_value ) {
                    if ( $plain_value->plan_id == $plain->id ) {
                        $checked = true;
                        $price = $plain_value->price;
                        if ( $plain_value->default == 1 ) {
                            $default = true;
                        }
                    }
                }
            }

            $display_price = $guruHelper->displayPrice($price);
            
			$plains_html2 .= "<tr>";
			$plains_html2 .= "<td style='padding:0.5em;'><input type='radio' name='renewal_default' value='" . $plain->id . "' " . (($default) ? "checked='checked'" : "") . "/><span class=\"lbl\"></span></td>";
			$plains_html2 .= "<td style='padding:0.5em;'><input class='plain' type='checkbox' id='renewals_".$k."' name='renewals[]' value='" . $plain->id . "'" . (($checked) ? " checked='checked'" : "") . "/><span class=\"lbl\"></span></td>";
			$plains_html2 .= "<td style='padding:0.5em;'>" . $plain->name . "</td>";
            if ($plain->term != 0) {
                $zplain = $plain->term . ' ' . $plain->period;
            } else {
                $zplain = ucfirst(JText::_( 'GURU_UNLIMPROMO' ));
            }
            
			$plains_html2 .= "<td style='padding:0.5em;'>" . $zplain . "</td>";
			$plains_html2.= "<td style='padding:0.5em;'>".@$positionB."<input type='text' id='renewal_price_".$k++."' name='renewal_price[" . $plain->id . "]' value='" . $display_price . "' />".@$positionA."</td>";
			$plains_html2 .= "</tr>\n";
		}
		$plains_html2 .= "</table>";
        
        $plains_html3 = "<table id='emails'>";
		$plains_html3 .= "<tr style='background:#999'>
                            <th width='1%' style='padding:0.5em;'><input type='checkbox' id='semails' name='semails' value='' onclick='checkPlans(\"emails\")'/><span class=\"lbl\"></span></th>
                            <th style='padding:0.5em;'>" . JText::_( 'Name' ) . "</th>
                            <th style='padding:0.5em;'>" . JText::_( 'Terms' ) . "</th>
                        </tr>";
		if(is_array($reminds))
		foreach($reminds as $plain){
			$checked = false;
            if (is_array($program_reminds)) {
                foreach ( $program_reminds  as $plain_value ) {
                    if ( $plain_value->emailreminder_id == $plain->id )
                        $checked = true;
                }
            }

            $plain->term = JText::_('GURU_REM_EXP' . $plain->term);
            
			$plains_html3 .= "<tr>";
			$plains_html3.= "<td style='padding:0.5em;'><input class='plain' type='checkbox' name='reminders[]' value='" . $plain->id . "'" . (($checked) ? " checked='checked'" : "") . " onclick='javascript:guruCheckboxEmail(value)'/><span class=\"lbl\"></span></td>";
			$plains_html3 .= "<td style='padding:0.5em;'>" . $plain->name . "</td>";
			$plains_html3 .= "<td style='padding:0.5em;'>" . $plain->term . "</td>";
			$plains_html3 .= "</tr>\n";
		}
		$plains_html3 .= "</table>\n\n";
        
        $this->plans = $plains_html;
        $this->renewals = $plains_html2;
        $this->emails = $plains_html3;

		$this->program = $program;
		$this->mmediam = $mmediam;
		if(!isset($mmediam_preq)) {$mmediam_preq=NULL;}
		$this->mmediam_preq = $mmediam_preq;
		$this->lists = $lists;
		$gurudateformat = $this->get('DateFormat');
		$this->gurudateformat = $gurudateformat;
		parent::display($tpl);

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
		//end image folder
		//$targetPath = JPATH_SITE.'/images/stories/'.$imgfolder.'/'.$advertiser_id.'/';
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
	
	function listAuthors(){
		$db = JFactory::getDBO();
		$sql = "SELECT id, name FROM #__users where id in (select userid from #__guru_authors)";
		$db->setQuery($sql);
		$db->execute();
		$result=$db->loadAssocList();
		
		return $result;	
	}
	function selectedCoursesforFree(){
		$program = $this->get('Program');
		$db = JFactory::getDBO();
		$sql = "SELECT chb_free_courses  FROM #__guru_program where id = ".$program->id;
		$db->setQuery($sql);
		$db->execute();
		$result=$db->loadResult();
		return $result;	
	}	

	function getStepAccessCourses(){
		$program = $this->get('Program');
		$db = JFactory::getDBO();
		$sql = "SELECT step_access_courses  FROM #__guru_program where id = ".$program->id;
		$db->setQuery($sql);
		$db->execute();
		$result=$db->loadResult();
		return $result;	
	}	
	function getCourseListForStudents(){
		$program = $this->get('Program');
		$selected_course = $this->getSelectedCourse();
		$selected_course_final = explode ("|", $selected_course);
		$database = JFactory::getDBO();
		if($program->id < 1){
		$sql = "select id, name from #__guru_program ";
		}
		else{
		$sql = "select id, name from #__guru_program where id !=".$program->id;
		}
		$database->setQuery($sql);
		$database->execute();
		$result = $database->loadAssocList();
		$selected = "";
		if ($selected_course == "-1") {$selected = 'selected="selected"'; }
		$html = '<select name ="selected_course[]" id="selected_course[]" multiple="multiple">';
		if(isset($result) && is_array($result) && count($result) > 0){
			$html .= '<option value="-1" '.$selected.'>'.JText::_("GURU_ANY_COURSE").'</option>';
			foreach($result as $key=>$value){
				$selected = "";
				if(in_array($value["id"], $selected_course_final)){
					$selected = 'selected="selected"';
				}
				$html .= '<option value="'.$value["id"].'" '.$selected.' >'.$value["name"].'</option>';
			}
		}
		$html .= '</select>';
		echo $html;
	}

	function getSelectedCourse() {
		$program = $this->get('Program');
		$db = JFactory::getDBO();
		if($program->id !=NULL){
			$sql = "SELECT selected_course  FROM #__guru_program where id = ".$program->id;
			$db->setQuery($sql);
			$db->execute();
			$result=$db->loadResult();
		}
		else{
			$result = "";
		}

		return $result;
	}

	function isJSInstalled(){
		$db = JFactory::getDbo();
		$sql = "select `extension_id` from #__extensions where `type`='component' and `element`='com_community'";
		$db->setQuery($sql);
		$db->execute();
		$id = $db->loadColumn();

		if(isset($id["0"]) && intval($id["0"]) > 0){
			return true;
		}

		return false;
	}

	function getJSGroups(){
		$db = JFactory::getDbo();
		$sql = "select `id`, `name` from #__community_groups order by `name` asc";
		$db->setQuery($sql);
		$db->execute();
		$groups = $db->loadAssocList();

		return $groups;
	}
}

?>