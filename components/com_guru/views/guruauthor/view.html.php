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

jimport ("joomla.application.component.view");
class guruViewguruauthor extends JViewLegacy {

	function display ($tpl =  null ) {
		$db = JFactory::getDBO();
		$authors = $this->get('authorList');
		$this->authors = $authors;
		
		$author = $this->get('Author');
		$this->author =$author;
		
		$config =  $this->get('Config');
		$this->config = $config;

		parent::display($tpl);
	}
	
	function authoressays($tpl =  null){
		$this->state = $this->get('State');
		
		$items = $this->get('Items');
		$this->items = $items;
		
		$this->pagination = $this->get('Pagination');
		
		parent::display($tpl);
	}
	
	function view ($tpl =  null ) {
		$db = JFactory::getDBO();
		$author = $this->get('author');
		$this->author = $author;
		
		$config = $this->get('Config');
		$this->config = $config;

		parent::display($tpl);
	}

	function studentProgress($tpl =  null ) {
		$student = $this->get('Student');
		$this->student = $student;

		$student_courses = $this->get('StudentCourses');
		$this->student_courses = $student_courses;

		$config = $this->get('Config');
		$this->config = $config;
		
		parent::display($tpl);
	}
	
	function authorprofile($tpl =  null ) {
		$db = JFactory::getDBO();
		$author = $this->get('author');
		$this->author = $author;
		
		$user =  $this->get('AuthorDetails');
		$this->user = $user;
		
		$config =  $this->get('Config');
		$this->config = $config;
		
		parent::display($tpl);

	}
	
	function mark($tpl =  null){
		$this->state = $this->get('State');
		$this->items = $this->get("ForMark");
		parent::display($tpl);
	}
	
	function authorcommissions_pending($tpl =  null ) {
		$details = $this->get('PendingDetails');
		$this->details = $details;

		$total_pending = $this->get('AuthorcommissionsPending');
		$this->total_pending = $total_pending;
		
		$config = $this->get('Config');
		$this->config = $config;

		parent::display($tpl);
	}
	function authorcommissions_paid($tpl =  null ) {
		$details = $this->get('PaidDetails');
		$this->details = $details;
		
		$total_paid = $this->get('AuthorcommissionsPaid');
		$this->total_paid = $total_paid;
		
		$config = $this->get('Config');
		$this->config = $config;
		
		parent::display($tpl);

	}	
	
	function mystudents($tpl =  null ) {
		$db = JFactory::getDBO();
		$author = $this->get('author');
		$this->author = $author;
		$this->state = $this->get('State');
		
		$user =  $this->get('AuthorDetails');
		$this->user = $user;
		
		$config =  $this->get('Config');
		$this->config = $config;
		
		$students = $this->get('Items');
		$this->students = $students;
		
		$this->pagination = $this->get('Pagination');
		
		parent::display($tpl);

	}
	
	function authormymedia($tpl =  null ) {
		$db = JFactory::getDBO();
		$author = $this->get('author');
		$this->author = $author;
		$this->state = $this->get('State');
		
		$all_categs =  $this->get('AllMediaCategory');
		$this->all_categs = $all_categs;
		
		$config =  $this->get('Config');
		$this->config = $config;
		
		$mymediath = $this->get('Items');
		$this->mymediath = $mymediath;
		
		$this->pagination = $this->get('Pagination');

		$filters = $this->get('filtersMedia');
		$this->filters = $filters;
		
		parent::display($tpl);
	}
	
	function authortreecourse($tpl =  null){
		$app = JFactory::getApplication('site');
		$pid = $app->getUserStateFromRequest("pid", "pid", "0");
		$db = JFactory::getDBO();
		
		$filters = $this->get('filters');
		$this->filters = $filters;

		$ads = $this->get('listDays');
		$this->ads = $ads;
		
		$pagination = $this->get('Pagination');
		$this->pagination = $pagination;

		parent::display($tpl);
	}
	
	function newstudent($tpl = null){
		$this->student = $this->get("Student");
		parent::display($tpl);
	}
	
	function authorquizzes($tpl =  null ) {
		$db = JFactory::getDBO();
		$author = $this->get('author');
		$this->author = $author;
		
		$user =  $this->get('AuthorDetails');
		$this->user = $user;
		
		$config =  $this->get('Config');
		$this->config = $config;
		
		$myquizzes = $this->get('Items');
		$this->myquizzes = $myquizzes;
		
		$this->pagination = $this->get('Pagination');
		
		parent::display($tpl);

	}
	
	function authormycourses($tpl =  null ) {
		$db = JFactory::getDBO();
		$author = $this->get('author');
		$this->author = $author;
		$this->state = $this->get('State');
		
		$user =  $this->get('AuthorDetails');
		$this->user = $user;
		
		$config =  $this->get('Config');
		$this->config = $config;
		
		$mycoursesth = $this->get('Items');
		$this->mycoursesth = $mycoursesth;
		
		$this->pagination = $this->get('Pagination');
		
		parent::display($tpl);
	}
	function authoraddeditmediacat($tpl =  null ){
		$this->categories = $this->get('Categories');
		$this->parent_id = $this->get('Parent');
		parent::display($tpl);
	}
	function parentMediaCategory($parent_id){
		$model = $this->getModel();
		$categ_db = $model->getAllRowsMediaCat(0, 0);
		
		$return = '';
		if(is_array($categ_db) && count($categ_db) == 0){
			$return .= '<select name="parent_id" class="input-large">';
			$return .= 		'<option value="">(0) '.JText::_("GURU_TOP").'</option>';		
			$return .= '</select>';
		}
		else{
			$return .= '<select name="parent_id" class="input-large">';
			$return .= 		'<option value="">(0) '.JText::_("GURU_TOP").'</option>';
			if(isset($categ_db) && count($categ_db) > 0){
				foreach($categ_db as $key=>$val){
					$val = (object)$val;
					$id = $val->id;
					$name = $val->name;
					$line = "";
					for($i=0; $i<$val->level; $i++){
						$line .= "&#151;";
					}
					$selected = "";
					if($parent_id == $id){
						$selected = 'selected="selected"';
					}
					$return .= '<option value="'.$id.'" '.$selected.'>'.$line."(".$val->level.") ".$name.'</option>';			
				}
			}	
			$return .= '</select>';		
		}			
		return $return;
	}
	function authoraddcourse($tpl =  null ){
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');
		$guruHelper = new guruHelper();

		$lists = array();
		$positionA = "";
		$positionB = "";
		$db = JFactory::getDBO();
		$program = $this->get('Program');
		$isNew = ($program->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		$task = JFactory::getApplication()->input->get('task', '');
		
		$sql = "SELECT currency from #__guru_config where id=1";
		$db->setQuery($sql);
		$currency = $db->loadColumn();
		$currency = $currency["0"];
		$currency = "GURU_CURRENCY_".$currency;
		$currency = JText::_(''.$currency.'');
		
		$sql = "SELECT currencypos from #__guru_config where id=1";
		$db->setQuery($sql);
		$currencypos = $db->loadColumn();
		$currencypos = $currencypos["0"];
		if($currencypos == 0){
	    	$positionB = $currency."&nbsp;&nbsp;";
		}
		else{
			$positionA = "&nbsp;&nbsp;".$currency;
		}
		
		if($isNew){
			$mmediam = new stdClass();
			$mmediam_req = NULL;			
		} 
		else{
			$db->setQuery("SELECT a.*, b.* FROM #__guru_mediarel as a, #__guru_media as b WHERE a.type='pmed' AND a.media_id=b.id AND a.mainmedia=0 AND a.type_id=".$program->id." order by a.order asc");
			$mmediam = $db->loadObjectList();
			
			$db->setQuery("SELECT a.*, b.* FROM #__guru_mediarel as a, #__guru_program as b WHERE a.type='preq' AND a.media_id=b.id AND a.mainmedia=0 AND a.type_id=".$program->id);
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
	
		$plans = $this->get('AllPlans');
    	
        $program_plans =  $this->get('ProgramPlans');
        
        $renewals = $plans;
        $program_renewals =  $this->get( 'ProgramRenewals' );
        
        $reminds = $this->get('AllReminds');
        $program_reminds = $this->get('ProgramReminds');
        $plains_html = "<table id='subscriptions' class='uk-table uk-table-striped wk-table wk-table-striped'>";
		$plains_html .= "<tr>
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
			$plains_html .= "<td style='padding:0.5em;' nowrap>".$positionB."<input type='text' class='uk-form-width-small wk-form-width-small gru-plan-price' id='subscription_price_".$k++."' name='subscription_price[" . $plain->id . "]' value='" . $guruHelper->displayPrice($price) . "' />".$positionA."</td>";
			$plains_html .= "</tr>\n";
		}
		$plains_html .= "</table>";

        $plains_html2 = "<table id='renewals' class='uk-table uk-table-striped wk-table wk-table-striped'>";
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
            
			$plains_html2 .= "<tr>";
			$plains_html2 .= "<td style='padding:0.5em;'><input type='radio' name='renewal_default' value='" . $plain->id . "' " . (($default) ? "checked='checked'" : "") . "/><span class=\"lbl\"></span></td>";
			$plains_html2 .= "<td style='padding:0.5em;'><input class='plain' type='checkbox' id='renewals_".$k."' name='renewals[]' value='" . $plain->id . "'" . (($checked) ? " checked='checked'" : "") . "/><span class=\"lbl\"></span></td>";
			$plains_html2 .= "<td style='padding:0.5em;'>" . $plain->name . "</td>";
            if ($plain->term != 0) {
                $zplain = $plain->term . ' ' . $plain->period;
            } else {
                $zplain = ucfirst(JText::_( 'GURU_UNLIMPROMO'));
            }
            
			$plains_html2 .= "<td style='padding:0.5em;'>" . $zplain . "</td>";
			$plains_html2.= "<td style='padding:0.5em;' nowrap>".$positionB."<input type='text' id='renewal_price_".$k++."' class='uk-form-width-small wk-form-width-small gru-plan-price' name='renewal_price[" . $plain->id . "]' value='" . $guruHelper->displayPrice($price) . "' />".$positionA."</td>";
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
		$config = $this->get('Configs'); 
		$this->config = $config;
		parent::display($tpl);
	}
	function authoraddquizfe($tpl =  null){
		parent::display($tpl);
	}
	function listAuthors(){
		$db = JFactory::getDBO();
		$sql = "SELECT id, name FROM #__users where id in (select userid from #__guru_authors)";
		$db->setQuery($sql);
		$result=$db->loadAssocList();
		return $result;	
	}
	function list_all($search, $name, $category_id, $selected_categories=Array(), $size=1, $toplevel=true, $multiple=false) {

		$db = JFactory::getDBO();
		$data_post = JFactory::getApplication()->input->post->getArray();

		$q  = "SELECT parent_id FROM #__guru_categoryrel ";
		if( $category_id )
		$q .= "WHERE child_id ='$category_id'";
		$db->setQuery($q);   
		$category_id=$db->loadResult();
		if (isset($data_post['catid'])) $category_id=intval($data_post['catid']);
		$multiple = $multiple ? "multiple=\"multiple\"" : "";
		
		$search_submit = '';
		if($search==1)
			$search_submit = " onchange=\"document.adminForm.submit()\" ";		

		
		echo "<select ".$search_submit." class=\"input-medium\" size=\"$size\" $multiple id=\"$name\" name=\"$name\">\n";
		if( $toplevel ) {
			echo "<option value=\"-1\">".JText::_("GURU_SELECT_CATEGORY")."</option>\n";
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
	function getCourseListForStudents(){
		$program =$this->get('Program');
		$selected_course = $this->getSelectedCourse();
		$selected_course_final = explode ("|", $selected_course);
		$database =JFactory::getDBO();
		if($program->id < 1){
		$sql = "select id, name from #__guru_program ";
		}
		else{
		$sql = "select id, name from #__guru_program where id !=".$program->id;
		}
		$database->setQuery($sql);
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
			$result=$db->loadResult();
		}
		else{
			$result = "";
		}
		return $result;		
	
	}

	function authorGuruMenuBar(){
		$tmpl = JFactory::getApplication()->input->get("tmpl", "");
		if($tmpl == "component"){
			return "";
		}
		
		$g_my_profile_active = "";
		$g_my_courses_active = "";
		$g_my_students = "";
		$g_my_quizzes = "";
		$g_my_media = "";
		$g_my_media = "";
		$g_my_commissions = "";
		$g_my_essay = "";
		$g_projects = "";
		$g_scorm = "";
		$menu_g_my_profile_active = "";
		$menu_g_my_courses_active = "";
		$menu_g_my_students = "";
		$menu_g_my_quizzes = "";
		$menu_g_my_media = "";
		$menu_g_my_media_cat = "";
		$menu_g_my_commissions = "";
		$menu_g_my_essay = "";
		$menu_g_projects_active = "";
		$menu_g_scorm_active = "";

		$controller = JFactory::getApplication()->input->get("view", "");
		$layout = JFactory::getApplication()->input->get("layout", "");
		$task = JFactory::getApplication()->input->get("task", "");
		$config =  $this->getConfigsObject();

		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		
		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruauthor"];
		
		if(intval($itemid_seo) > 0){
			$Itemid = intval($itemid_seo);
		}

		if(is_array($Itemid) && isset($Itemid["0"])){
			$Itemid = intval($Itemid["0"]);
		}
		
		$link = "index.php?option=com_guru&view=guruauthor&task=authorprofile&layout=authorprofile&Itemid=".$Itemid;

		if(trim($layout) == ""){
			$layout = $task;
		}
		if($controller == "guruauthor" && $layout == "authorprofile"){
			$g_my_profile_active = 'class="uk-active"';
			$menu_g_my_profile_active = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && ($layout == "authormycourses" || $layout == "mark")){
			$g_my_courses_active = 'class="uk-active"';
			$menu_g_my_courses_active = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && $layout == "authorquizzes"){
			$g_my_quizzes = 'class="uk-active"';
			$menu_g_my_quizzes = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && $layout == "mystudents"){
			$g_my_students = 'class="uk-active"';
			$menu_g_my_students = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && $layout == "authormymedia"){
			$g_my_media = 'class="uk-active"';
			$menu_g_my_media = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && $layout == "authorcommissions"){
			$g_my_commissions = 'class="uk-active"';
			$menu_g_my_commissions = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && $layout == "authoressays"){
			$g_my_essay = 'class="uk-active"';
			$menu_g_my_essay = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && $layout == "authormymediacategories"){
			$g_my_media_cat = 'class="uk-active"';
			$menu_g_my_media_cat = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && $layout == "editMedia"){
			$g_my_media = 'class="uk-active"';
			$menu_g_my_media = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && $layout == "editQuizFE"){
			$g_my_quizzes = 'class="uk-active"';
			$menu_g_my_quizzes = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && $layout == "treeCourse"){
			$g_my_courses_active = 'class="uk-active"';
			$menu_g_my_courses_active = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && $layout == "addCourse"){
			$g_my_courses_active = 'class="uk-active"';
			$menu_g_my_courses_active = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && ($layout == "projects" || $layout == "projectForm")){
			$g_projects = 'class="uk-active"';
			$menu_g_projects_active = 'selected="selected"';
		}
		elseif($controller == "guruauthor" && ($layout == "scorm" || $layout == "scorm")){
			$g_scorm = 'class="uk-active"';
			$menu_g_scorm_active = 'selected="selected"';
		}
		
		$log_out = "";
		$user = JFactory::getUser();
		if($user->id > 0){
			$itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
			
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$itemid = intval($itemid_seo);
			}
			
			$layout_come = JFactory::getApplication()->input->get("layout", "authormycourses");
			$return_url = base64_encode("index.php?option=com_guru&view=guruLogin&returnpage=".$layout_come."&Itemid=".intval($itemid));

			$teacher_logout_redirect = $this->getTeacherRedirectLogoutUrl();

			if(trim($teacher_logout_redirect) != ""){
				$return_url = base64_encode(trim($teacher_logout_redirect));
			}

			$log_out = '<li id="g_logout" class="logout-btn">
							<a style="padding:0px 5px;" href="index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&Itemid='.$Itemid.'&return='.$return_url.'">
								<i class="fa fa-sign-out"></i>
							</a>
					   </li>';
		}

		@$st_authorpage = $config->st_authorpage;
		@$st_authorpage = json_decode($st_authorpage);

		$teacher_menu_courses = 0;
		$teacher_menu_students = 0;
		$teacher_menu_projects = 0;
		$teacher_menu_scorm = 0;
		$teacher_menu_quizzes = 0;
		$teacher_menu_media = 0;
		$teacher_menu_commissions = 0;
		$teacher_menu_grade = 0;

		if(isset($st_authorpage->teacher_menu_courses)){
    		$teacher_menu_courses = $st_authorpage->teacher_menu_courses;
    	}

    	if(isset($st_authorpage->teacher_menu_students)){
    		$teacher_menu_students = $st_authorpage->teacher_menu_students;
    	}

    	if(isset($st_authorpage->teacher_menu_projects)){
    		$teacher_menu_projects = $st_authorpage->teacher_menu_projects;
    	}

    	if(isset($st_authorpage->teacher_menu_scorm)){
    		$teacher_menu_scorm = $st_authorpage->teacher_menu_scorm;
    	}

    	if(isset($st_authorpage->teacher_menu_quizzes)){
    		$teacher_menu_quizzes = $st_authorpage->teacher_menu_quizzes;
    	}

    	if(isset($st_authorpage->teacher_menu_media)){
    		$teacher_menu_media = $st_authorpage->teacher_menu_media;
    	}

    	if(isset($st_authorpage->teacher_menu_commissions)){
    		$teacher_menu_commissions = $st_authorpage->teacher_menu_commissions;
    	}

    	if(isset($st_authorpage->teacher_menu_grade)){
    		$teacher_menu_grade = $st_authorpage->teacher_menu_grade;
    	}
		
		$return = '
			<nav class="uk-navbar wk-navbar hidden-phone">
				<ul class="uk-navbar-nav wk-navbar-nav">';

					$return .= '
					<li id="g_my_profile" '.$g_my_profile_active.'>
						<a href="'.JRoute::_("".$link."").'">
							<i class="fa fa-user"></i>&nbsp;'.JText::_('GURU_AUTHOR_PROFILE').'
						</a>
					</li>';
				
				if($teacher_menu_courses == 0){	
					$return .= '
					<li id="g_my_courses_active" '.$g_my_courses_active.'>
						<a href="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses").'">
							<i class="fa fa-file-o"></i>&nbsp;'.JText::_('GURU_AUTHOR_MY_COURSE').'
						</a>
					</li>';
				}
				
				if($teacher_menu_students == 0){	
					$return .= '
					<li id="g_my_students" '.$g_my_students.'>
						<a href="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents").'">
							<i class="fa fa-user"></i>&nbsp;'.JText::_('GURU_AUTHOR_MY_STUDENTS').'
						</a>
					</li>';
				}
				
				if($teacher_menu_projects == 0){	
					$return .= '
					<li id="g_projects" '.$g_projects.'>
						<a href="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=projects&layout=projects").'">
							<i class="fa fa-file-archive-o"></i>&nbsp;'.JText::_('GURU_PROJECTS').'
						</a>
					</li>';
				}

				if($teacher_menu_scorm == 0){	
					$return .= '
					<li id="g_scorm" '.$g_scorm.'>
						<a href="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=scorm&layout=scorm").'">
							<i class="fa fa-file-code-o"></i>&nbsp;'.JText::_('GURU_SCORM').'
						</a>
					</li>';
				}

				if($teacher_menu_quizzes == 0){
					$return .= '
					<li id="g_my_quizzes" '.$g_my_quizzes.'>
						<a href="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes").'">
							<i class="fa fa-question-circle"></i>&nbsp;'.JText::_('GURU_AUTHOR_MY_QUIZZES').'
						</a>
					</li>';
				}

				if($teacher_menu_media == 0){
					$return .= '
						<li id="g_my_media" '.$g_my_media.'>
							<a href="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormymedia&layout=authormymedia").'">
								<i class="fa fa-picture-o"></i>&nbsp;'.JText::_('GURU_AUTHOR_MY_MEDIA').'
							</a>
						</li>
					';
				}

				if($teacher_menu_commissions == 0){
					$return .= '
						<li id="g_my_media" '.$g_my_commissions.'>
							<a href="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorcommissions&layout=authorcommissions").'">
								<i class="fa fa-money"></i>&nbsp;'.JText::_('GURU_COMMISSIONS').'
							</a>
						</li>
					';
				}
				
				if($teacher_menu_grade == 0){	
					$count_essays = $this->getCountEssaysForTeacher();
					$count_html = "";
					
					if(intval($count_essays) > 0){
						$count_html = '<sup id="grade-essays">'.intval($count_essays).'</sup>';
					}
					
					$return .= '
						<li id="g_my_essay" '.$g_my_essay.'>
							<a href="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=authoressays&layout=authoressays").'">
								<i class="fa fa-check"></i>&nbsp;'.JText::_('GURU_GRADE_ESSAYS').'
								'.$count_html.'
							</a>
						</li>
					';
				}
					
					$return .= ''.$log_out.'
				</ul>
			</nav>';
			$return .= '<div id="guru_menubar_mobile" class="uk-hidden-large uk-hidden-medium wk-hidden-large wk-hidden-medium">
							<select name="menu_bar" onchange="document.location.href = this.value">
								<option '.$menu_g_my_profile_active.' value="'.$link.'">'.JText::_("GURU_AUTHOR_PROFILE").'</option>';
								
							if($teacher_menu_courses == 0){
								$return .= '
								<option '.$menu_g_my_courses_active.' value="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses").'">'.JText::_("GURU_AUTHOR_MY_COURSE").'</option>';
							}
							
							if($teacher_menu_students == 0){
								$return .= '	
								<option '.$menu_g_my_students.' value="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents").'">'.JText::_("GURU_AUTHOR_STUDENTS").'</option>';
							}
							
							if($teacher_menu_projects == 0){
								$return .= '	
								<option '.$menu_g_projects_active.' value="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=projects&layout=projects").'">'.JText::_("GURU_PROJECTS").'</option>';
							}

							if($teacher_menu_scorm == 0){
								$return .= '	
								<option '.$menu_g_scorm_active.' value="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=scorm&layout=scorm").'">'.JText::_("GURU_SCORM").'</option>';
							}
							
							if($teacher_menu_quizzes == 0){	
								$return .= '
								<option '.$menu_g_my_quizzes.' value="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes").'">'.JText::_("GURU_AUTHOR_MY_QUIZZES").'</option>';
							}
							
							if($teacher_menu_media == 0){	
								$return .= '	
								<option '.$menu_g_my_media.' value="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormymedia&layout=authormymedia").'">'.JText::_("GURU_AUTHOR_MY_MEDIA").'</option>';
							}
							
							if($teacher_menu_commissions == 0){	
								$return .= '	
								<option '.$menu_g_my_commissions.' value="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorcommissions&layout=authorcommissions").'">'.JText::_("GURU_COMMISSIONS").'</option>';
							}

							if($teacher_menu_grade == 0){	
								$return .= '	
								<option '.$menu_g_my_commissions.' value="'.JRoute::_("index.php?option=com_guru&view=guruauthor&task=authoressays&layout=authoressays").'">'.JText::_("GURU_GRADE_ESSAYS").'</option>';
							}

							$return .= '
							</select>
						</div>';
		return $return;
	}
	
	function getCountEssaysForTeacher(){
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		
		//-------------------------------------------------- quiz change
		$quizzes_ids = array("0");

		$sql = "select `id` from #__guru_program where `author`='" . intval($user->id) . "' OR `author` like '%|" . $user->id . "|%'";
		$db->setQuery($sql);
		$authors_courses = $db->loadColumn();
		foreach ($authors_courses as $course_id) {
			$query = "SELECT question_ids FROM `#__guru_quiz_question_taken_v3` WHERE pid = " . $db->q($course_id);
			$results = $db->setQuery($query)->loadColumn();
			if (!$results) {
				continue;
			}

			foreach ($results as $value) {
				if (!trim($value)) {
					continue;
				}

				$ids = explode(',', $value);
				foreach ($ids as $id) {
					if (in_array($id, $quizzes_ids)) {
						continue;
					}
					
					$quizzes_ids[] = $id;
				}
			}
		}

		if(!isset($authors_courses) || count($authors_courses) == 0){
			$authors_courses = array("0");
		}
		
		$authors_courses = implode(',', $authors_courses);
		$quizzes_ids = implode(',', $quizzes_ids);
		$query = 
			"SELECT 
				COUNT(qt3.id)
			FROM `#__guru_quiz_taken_v3` qt3
			INNER JOIN `#__guru_questions_v3` q3 ON q3.qid = qt3.quiz_id AND q3.id = qt3.question_id
			INNER JOIN `#__users` u ON u.id = qt3.user_id
			INNER JOIN `#__guru_customer` c ON c.id = qt3.user_id
			INNER JOIN `#__guru_quiz` q ON q.id = qt3.quiz_id
			LEFT JOIN `#__guru_quiz_essay_mark` qem ON qem.question_id = qt3.question_id AND qem.user_id = qt3.user_id
			WHERE 1=1
			AND q3.`type` = 'essay'
			AND qt3.question_id IN ($quizzes_ids)
			AND qt3.pid IN ($authors_courses)
			AND q.author = {$db->q($user->id)}
			AND qem.grade IS NULL";

		$count = $db->setQuery($query)->loadResult();

		return $count;
	}
	
	function isTeacherOrNot(){
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$sql = "SELECT count(id) FROM #__guru_authors where userid = ".intval($user->id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return $result["0"];
	}
	
	function getCourseName($id){
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_program where id = ".intval($id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return $result["0"];
	}
	
	function selectedCoursesforFree(){
		$program = $this->get('Program');
		$db = JFactory::getDBO();
		$sql = "SELECT chb_free_courses  FROM #__guru_program where id = ".$program->id;
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	
	function getStepAccessCourses(){
		$program = $this->get('Program');
		$db = JFactory::getDBO();
		$sql = "SELECT step_access_courses  FROM #__guru_program where id = ".$program->id;
		$db->setQuery($sql);
		$result=$db->loadResult();
		return $result;	
	}
	
	function existing_ids ($ids){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = ".$ids." AND type='pmed' ");
		$existing_ids = $db->loadObjectList();
		return $existing_ids;
	}
	
	function preq_existing_ids ($ids){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = ".$ids." AND type='preq' ");
		$existing_ids = $db->loadObjectList();
		return $existing_ids;
	}
	
	function getConfigs(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getConfigsObject() {
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		$result = $db->loadObject();	
		return $result;
	}
	
	function getProgram($id){
		$database = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_program WHERE id = ".intval($id);
		$database->setQuery($sql);
		$result = $database->loadAssocList();
		return @$result["0"];
	}
	
	function getProgramName ($dayid){
		$database = JFactory::getDBO();
		$sql = " SELECT name FROM #__guru_program WHERE id = (SELECT pid FROM #__guru_days WHERE id = ".$dayid." )"; 
		$database->setQuery($sql);
		$result = $database->loadResult();
		return $result;	
	}
	
	function getIDTasksForDay ($dayid){			
		$order_column = "ORDER BY ordering ASC";
		
		$database = JFactory::getDBO();
		$sql = "SELECT distinct media_id 
				FROM #__guru_mediarel lm
				LEFT JOIN #__guru_task lt
				ON lm.media_id=lt.id
				WHERE type='dtask' AND type_id = ".$dayid." ".$order_column;
		$database->setQuery($sql);
		$result = $database->loadColumn();
		return $result;
	}
	
	function getTask ($taskid){
		$database = JFactory::getDBO();
		$sql = " SELECT * FROM #__guru_task WHERE id = ".$taskid; 
		$database->setQuery($sql);
		$result = $database->loadObject();
		return $result;	
	}
	
	function select_layout ($pid){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = ".intval($pid)." AND type='scr_l' ");
		$layout_id = $db->loadResult();
		return $layout_id;
	}
	
	function getTaskType ($taskid) {
		$database = JFactory::getDBO();
		$sql = " SELECT type FROM #__guru_media WHERE id = (SELECT media_id FROM #__guru_mediarel WHERE type='task' AND type_id = ".intval($taskid)." )"; 
		$database->setQuery($sql);
		$result = $database->loadResult();
		return $result;		
	}
	
	function newModule($tpl = null){
		parent::display($tpl);
	}
	
	function editForm($tpl = null) {
		$app = JFactory::getApplication("site");

		$db = JFactory::getDBO();
		$program = $this->get('day'); 

		$this->program = $program;
		parent::display($tpl);
	}
	
	function getMediaType($id){
		$db = JFactory::getDBO();
		$sql = "select type from #__guru_media where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	
	function getMediaName($id){
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_media WHERE id=".intval($id)." LIMIT 1";
		$db->setQuery($sql);
		$existing_ids = $db->loadResult();
		return $existing_ids;
	}
	
	function vimeo($tpl = null) {
        $id = JFactory::getApplication()->input->get('id', '0');
        $this->id = $id;
        parent::display($tpl);
    }
	
	function editLessonForm($tpl = null) {
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
		
		//JToolBarHelper::title(JText::_('GURU_TASK').":<small>[".$text."]</small>");
		//JToolBarHelper::save();
		if ($isNew) {
			//JToolBarHelper::cancel();
			$program->published=1;
		} else {
			//JToolBarHelper::apply();
			//JToolBarHelper::cancel ('cancel', JText::_('GURU_CLOSE_TASK_BTN'));
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_media as b WHERE a.media_id=b.id AND a.mainmedia=1 AND a.type_id=".$program->id);
			$mainmedia = $db->loadObjectList();
			
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_media as b WHERE a.type='task' AND a.media_id=b.id AND a.mainmedia=0 AND a.type_id=".$program->id);
			$mmediam = $db->loadObjectList();
			
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_quiz as b WHERE a.type='tquiz' AND a.media_id=b.id AND a.mainmedia=1 AND a.type_id=".$program->id);
			$mainquiz = $db->loadObjectList();	
			
			$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type='scrnl' AND type_id=".$program->id);
			$the_layout = $db->loadResult();		
		}
		
		$lists=array();
		
		$published = '<input type="hidden" name="published" value="0">';
		if($program->published == 1){
			$published .= '<input type="checkbox" checked="checked" value="1" class="ace-switch ace-switch-5" name="published">';
		}
		else{
			$published .= '<input type="checkbox" value="1" class="ace-switch ace-switch-5" name="published">';
		}
		$published .= '<span class="lbl"></span>';
		
		$lists['published'] = $published;
		
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
		
	  	$lists['difficulty']  =  JHTML::_( 'select.genericlist', $dificulty, 'difficultylevel', 'class="inputbox" size="1"'.$javascript,'value', 'option', $the_difficultylevel);
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
	  	$lists['addmedias']  =  JHTML::_( 'select.genericlist', $medias, 'addmedia', 'multiple="multiple" class="inputbox" size="10"'.$javascript,'id', 'name', $program->difficultylevel);	
		/*---------------------------------------------*/
		$medias[] = JHTML::_('select.option',  "0", JText::_('Add Main Media'), 'id', 'name' );
		$medias 	= array_merge( $medias, $allmedia );
		$javascript = ' style="margin-top: 3px; margin-bottom: 3px;"';
	  	$lists['addmainmedias']  =  JHTML::_( 'select.genericlist', $medias, 'addmainmedia', 'class="inputbox" size="1"'.$javascript,'id', 'name', $program->difficultylevel);
	  	/*---------------------------------------------*/
		$quiz[] = JHTML::_('select.option',  "0", JText::_('Add Quiz'), 'id', 'name' );
		$quiz 	= array_merge( $quiz, $allquiz );
		$javascript = ' style="margin-top: 3px; margin-bottom: 3px;"';
	  	$lists['addquiz']  =  JHTML::_( 'select.genericlist', $medias, 'addquiz', 'class="inputbox" size="1"'.$javascript,'id', 'name', $program->difficultylevel);		
		//$javascript = ' style="display:none;"';
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$temp_lays = $registry->get('temp_lays', "");
		
		if(isset($temp_lays)&&($temp_lays=='yes')){
			$db->setQuery("SELECT * FROM #__guru_media_templay WHERE ip='".ip2long($_SERVER['REMOTE_ADDR'])."'");
			$tem_lay = $db->loadObjectList();	
		} else { 
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
	
	function addmedia ($tpl =  null ) { 
		$model = $this->getModel("guruAuthor");
		$medias = $this->get('listaddmedia');
		$data_get = JFactory::getApplication()->input->get->getArray();
		
		if(isset($data_get['type'])&&($data_get['type']=='audio')){
			foreach($medias as $element){
				$element->prevw = $model->parse_audio($element->id);
			}
		}
		$types = $this->get('distincttypes');
		$this->types = $types;
		$this->medias = $medias;
		
 		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}

	function addproject($tpl =  null ) {
		$model = $this->getModel("guruAuthor");
		$projects = $model->getlistProjects();
		$this->projects = $projects;
		
 		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}

	function addscorm($tpl =  null ) {
		$model = $this->getModel("guruAuthor");
		$scorm = $model->getlistScorm();
		$this->scorm = $scorm;
		
 		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}
	
	function addexercise($tpl =  null ) { 
		$db = JFactory::getDBO();
		$user = JFActory::getUser();
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		$sql = "SELECT * FROM #__guru_media WHERE type in ('docs', 'file') AND author=".intval($user->id);
		if(isset($data_post['search_text']) && $data_post['search_text']!="")
			$sql = $sql." AND name LIKE '%".$data_post['search_text']."%' " ;
		if(isset($data_post['media_select']) && $data_post['media_select']!='all')
			$sql = $sql." AND type='".$data_post['media_select']."' ";	
		$db->setQuery($sql);
		$medias = $db->loadObjectList();
		$this->medias = $medias;

		parent::display($tpl);
	}
	
	function addQuiz ($tpl =  null ) { 
		$quiz = $this->get('listQuiz');
		$this->quiz = $quiz;
		
 		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}
	
	function getAllMediaCategs(){
		$db = JFactory::getDBO();
		$sql = "select id, name from #__guru_media_categories";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function get_asoc_file_for_media($media_id)	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_media WHERE id=".$media_id;
		$db->setQuery($sql);
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
	
	function getMediaCategoriesName(){
		$db = JFactory::getDBO();
		$sql = "select id, name from #__guru_media_categories";
		$db->setQuery($sql);
		$result = $db->loadAssocList("id");
		return $result;
	}
	
	function real_quiz_id($media_id)	{
		$db = JFactory::getDBO();
		$sql = "SELECT source FROM #__guru_media WHERE id=".$media_id;
		$db->setQuery($sql);
		$the_media = $db->loadResult();		
		
		return 	$the_media;			
	}
	
	function select_media ($pid, $media_no, $layout=0){
		// m_type = scr_m for media
		$db = JFactory::getDBO();
		if($pid !=""){
			$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = ".$pid." AND type='scr_m' AND mainmedia='".$media_no."' AND layout=".$layout." ");
			$media_id = $db->loadColumn();
		}
		return @$media_id[0];
	}
	
	function select_text ($pid, $text_no = NULL, $layout=0){
		$db = JFactory::getDBO();
		if(isset($text_no)) { $cond=" AND text_no = '".intval($text_no)."'";} else { $cond=NULL;}
		
		$db->setQuery("SELECT media_id,mainmedia FROM #__guru_mediarel WHERE type_id = ".intval($pid)." AND type='scr_t' ".$cond." AND layout=".$layout);
		$media_obj = $db->loadObject();
		
		if(isset($media_obj))
			$media_id = @$media_obj->media_id.'$$$$$'.@$media_obj->mainmedia;
		else
			$media_id = 0;
		return $media_id;
	}
	
	function parse_media ($id, $layout_id){
		$model = $this->getModel("guruAuthor");
		$media = $model->parse_media($id, $layout_id);
		return $media;
	}
	
	function parse_txt($id){
		$model = $this->getModel("guruAuthor");
		$media = $model->parse_txt($id);
		return $media;
	}
	
	function parse_audio($id){
		$model = $this->getModel("guruAuthor");
		$media = $model->parse_audio($id);
		return $media;
	}
	
	function preview($tpl =  null){
		$media = $this->get("mainMedia");
		$this->media = $media;
		
		$config =  $this->get('Config');
		$this->config = $config;
		
		parent::display($tpl);
	}
	
	function editMediaForm($tpl =  null){
		$app = JFactory::getApplication('site');
		$db = JFactory::getDBO();
		
		$data = JFactory::getApplication()->input->post->getArray();
		$this->data = $data;
		
		$media = $this->get('file');
		$this->media = $media;
		
		$config = $this->get('config');
		$this->config = $config;
		
		parent::display($tpl);
	}

	function editProjectForm($tpl =  null){
		$app = JFactory::getApplication('site');
		$db = JFactory::getDBO();
		
		$data = JFactory::getApplication()->input->post->getArray();
		$this->data = $data;
		
		$projectDetail = $this->get('project');

		if(!isset($projectDetail->id)){
			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
			$jnow = new JDate('now');
			$jnow->setTimezone($timezone);
			$start = $jnow->toSQL(true);
			
			$projectDetail = array("id"=>0, "course_id"=>"0", "title"=>"", "description"=>"", "published"=>"0", "start"=>$start, "end"=>"");
			$projectDetail = (object)$projectDetail;
		}

		$this->projectDetail = $projectDetail;
		
		$my_courses = $this->getMyCourses();
		$this->my_courses = $my_courses;
		
		$config = $this->get('config');
		$this->config = $config;
		
		parent::display($tpl);
	}

	function editScormForm($tpl =  null){
		$app = JFactory::getApplication('site');
		$db = JFactory::getDBO();
		
		$data = JFactory::getApplication()->input->post->getArray();
		$this->data = $data;
		
		$scormDetail = $this->get('scorm');

		if(!isset($scormDetail->id)){
			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
			$jnow = new JDate('now');
			$jnow->setTimezone($timezone);
			$start = $jnow->toSQL(true);
			
			$scormDetail = array("id"=>"0", "course_id"=>"0", "author_id"=>"0", "title"=>"", "description"=>"", "file"=>"", "created"=>$start, "updated"=>"", "start"=>$start, "end"=>"", "layout"=>"", "published"=>"0");
			$scormDetail = (object)$scormDetail;
		}

		$this->scormDetail = $scormDetail;
		
		$my_courses = $this->getMyCourses();
		$this->my_courses = $my_courses;
		
		$config = $this->get('config');
		$this->config = $config;
		
		parent::display($tpl);
	}
	
	function authormymediacategories($tpl =  null){
		$mymediacat = $this->get('AuthorMyMediaCategories');
		$this->mymediacat = $mymediacat;
		
		$config =  $this->get('Config');
		$this->config = $config;
	
		$this->pagination = $this->get('Pagination');
	
		parent::display($tpl);
	}
	
	function authorcommissions($tpl =  null){
		$config =  $this->get('Config');
		$this->config = $config;
		
		$authcomm =  $this->get('Authorcommissions');
		$this->authcomm = $authcomm;
		
		$authcommpending =  $this->get('AuthorcommissionsPending');
		$this->authcommpending = $authcommpending;
		
		$authcommpaid =  $this->get('AuthorcommissionsPaid');
		$this->authcommpaid = $authcommpaid;
		
		$authpaymetoption =  $this->get('AuthorPaymetOption');
		$this->authpaymetoption = $authpaymetoption;
	
		$this->pagination = $this->get('Pagination');
	
		parent::display($tpl);
	}
	
	function now_selected_media ($mediaid){
		$db = JFactory::getDBO();
		if((isset($mediaid)) && ($mediaid != "")){
			$sql = "SELECT local FROM #__guru_media WHERE id = ".$mediaid;
			$db->setQuery($sql);
			$now_selected = $db->loadColumn();	
		}
		else{
			$now_selected = "";
		}	
		return @$now_selected["0"];
	}
	
	function parentCategory($categ_id){
		if($categ_id == NULL){
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$category_id = $registry->get('category_id', "");
		
			if(isset($category_id) && $category_id != ""){
				$categ_id = $category_id;
			}
		}
		$model = $this->getModel();
		$categ_db = $model->getAllRows(0, 0);
				
		$return = '';
		if(is_array($categ_db) && count($categ_db) == 0){
			$return .= '<select name="category_id">';
			$return .= 		'<option value="0">'.JText::_("GURU_GENERAL").'</option>';		
			$return .= '</select>';
		}
		else{
			$return .= '<select name="category_id">';
			foreach($categ_db as $key=>$val){
				$val = (object)$val;
				$id = $val->id;
				$name = $val->name;
				$line = "";
				for($i=0; $i<$val->level; $i++){
					$line .= "&#151;";
				}
				$selected = "";
				if($categ_id == $id){
					$selected = 'selected="selected"';
				}
				$return .= '<option value="'.$id.'" '.$selected.'>'.$line."(".$val->level.") ".$name.'</option>';
			}
			$return .= '</select>';
		}
		return $return;
	}
	
	function displayArticleguru($id, $guruartname){
		$value = "";
		$document = JFactory::getDocument();
		$html = "";	
		$article = JTable::getInstance('content');

		if($id != "0"){
			$value = $id;
		}	
		else{
			$value = NULL;
		}

		if($value){
			$article->load($value);
		}							
		else{
			if($id != 0){				
				$article->title = $guruartname;
			}
			else{ 
				$article->title = '';
			}	
		}

		$name = 'article';
		$link = JURI::root().'administrator/index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;layout=modal&function=SelectArticleg"';
		JHTML::_('behavior.modal', 'a.modal');
		
		$html = "\n".'<div class="fltlft"><div id="updateTextAfterDelete"><input type="text" size="75" id="'.$name.'_name" value="'.htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" style="display:block;" title="'.JText::_('GURU_MEDIATYPEARTICLE2').'" href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('GURU_SELECT_CHANGE').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'id" name="'.$name.'id" value="'.(int)$value.'" />';
		
		echo $html;
	}
	
	function QuestionNo($id){
		$db = JFactory::getDBO();
		
		$sql = "select is_final from #__guru_quiz where id=".intval($id);
		$db->setQuery($sql);
		$is_final = $db->loadResult();
		$is_final = @$is_final["0"];
		
		if($is_final == 1){ // final exam
			$sql = "select nb_quiz_select_up from #__guru_quiz where id=".intval($id);
			$db->setQuery($sql);
			$tmp = $db->loadResult();
			return $tmp;
		}
		else{
			$sql = "select count(id) from #__guru_questions_v3 where qid=".$id;
			$db->setQuery($sql);
			$tmp = $db->loadResult();
			return $tmp;
		}
	}
	
	function editQuiz($tpl = null) { 
		$app = JFactory::getApplication('site');		
		$db = JFactory::getDBO();		
		$program = $this->get('quiz'); 
		$value_option = JFactory::getApplication()->input->get("v");
		$media = $this->get('Media');
		$this->max_reo = $media->max_reo;
		$this->min_reo = $media->min_reo;
	   	$this->mmediam = $media->mmediam;
		$this->mainmedia = $media->mainmedia;
		$this->program = $program;
		
		parent::display($tpl);
	}
	function editQuizFE($tpl = null) { 
		$app = JFactory::getApplication('site');		
		$db = JFactory::getDBO();
		$program = $this->get('quiz'); 
		$value_option = JFactory::getApplication()->input->get("v");
		$media = $this->get('Media');
		
		$this->media = $media;
		$this->max_reo = $media->max_reo;
		$this->min_reo = $media->min_reo;
	   	$this->mmediam = $media->mmediam;
		$this->mainmedia = $media->mainmedia;
		$this->program = $program;
		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		$gurudateformat = $this->get('DateFormat');
		$this->gurudateformat = $gurudateformat;
		parent::display($tpl);
	}
	
	function addQuestion ($tpl =  null ) { 
		$db = JFactory::getDBO();
		$qid = JFactory::getApplication()->input->get("qid", "0", "raw");
		$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
		
		if(!is_array($cid)){
			$cid = array($cid);
		}
		
		$db->setQuery("SELECT * FROM #__guru_questions_v3 WHERE id=".intval($qid)." AND qid=".intval($cid[0]));
		$medias = $db->loadObject();
		$this->medias = $medias;
		
		$db->setQuery("SELECT * FROM #__guru_question_answers WHERE question_id=".intval($qid)." order by id");
		$medians = $db->loadObjectList();
		$this->medians = $medians;
		
		parent::display($tpl);
	}
	
	function id_for_last_question(){
		$db = JFactory::getDBO();
		$sql = "SELECT max(id) FROM #__guru_questions_v3 ";
		$db->setQuery($sql);
		$id = $db->loadResult();
		return $id;	
	}
	
	function editQuestion ($tpl =  null ) { 
		$data_get = JFactory::getApplication()->input->get->getArray();
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__guru_questions_v3 WHERE id = ".$data_get['qid']." AND qid =".$data_get['cid']);
		$medias = $db->loadObject();
		$this->medias = $medias;
		parent::display($tpl);
	}
	
	function jumpbts($tpl = null){
		$data_get = JFactory::getApplication()->input->get->getArray();
		$days = $this->get('listDaysJumps');
		$this->days = $days;
		
		if(isset($data_get['id'])){
			$current = $this->get('CurrentJump');
		}
		else{
			$current = NULL;
		}
		$this->current = $current;
		parent::display($tpl);
	}
	
	function getIDTasksForDayJump($dayid){
		$database = JFactory::getDBO();
		$sql = "SELECT distinct(media_id) FROM #__guru_mediarel WHERE type='dtask' AND type_id = ".$dayid." ORDER BY id ASC ";
		$database->setQuery($sql);
		$result = $database->loadColumn();
		return $result;	
	}
	
	function getTask2($taskid){
		$database = JFactory::getDBO();
		$sql = " SELECT * FROM #__guru_task WHERE id = ".$taskid; 
		$database->setQuery($sql);
		$result = $database->loadObject();
		return $result;
	}
	
	function courseStats($tpl = null){
		$this->course_name = $this->get("CourseName");
		$this->total_students = $this->get("TotalStudentsByCourseId");
		$this->student_complete = $this->get("StudentCompleteByCourseId");
		$this->quizzes = $this->get("QuizzesByCourseId");
		$this->score = $this->get("ScoreByCourseId");
		$this->final_exam = $this->get("FinalExamByCourseId");
		$this->pass = $this->get("PassByCourseId");
		
		parent::display($tpl);
	}
	
	public static function getAmountQuestions($id){
		$db =Jfactory::getDBO();
		$query="select count(id) from #__guru_questions_v3 where qid=".intval($id);
		$db->setQuery($query);
		$result=$db->loadResult();
		return $result;
	
	}
	
	
	public static function getAmountQuizzes($id){
		$db =Jfactory::getDBO();
		$sql = "SELECT 	quizzes_ids FROM #__guru_quizzes_final WHERE qid=".$id;
		$db->setQuery($sql);
		$result=$db->loadColumn();	
		@$result_qids = explode(",",trim($result["0"],","));
		if(isset($result_qids[0]) && $result_qids[0]!=""){
			$query="select count(id) from #__guru_questions_v3 where qid IN (".implode(",", $result_qids).")";
			$db->setQuery($query);
			$result=$db->loadResult();
			return $result;
		}
	}	
	
	function getMyCourses(){
		$db = JFactory::getDBO();
		$user = JFActory::getUser();
		$sql = "SELECT * FROM #__guru_program WHERE author=".intval($user->id)." OR author like '%|".intval($user->id)."|%'";
		$db->setQuery($sql);
		$courses = $db->loadAssocList();
		return $courses;
	}
	
	function quizzStats($tpl = null){
		$this->quizz_name = $this->get("QuizzName");
		$this->total_students = $this->get("TotalStudentsByQuizzId");
		$this->score_to_pass = $this->get("ScoreToPassByQuizzId");
		$this->avg_score = $this->get("AvgScoreByQuizzId");
		$this->students_pass = $this->get("StudentsPassByQuizzId");
		$this->students_failed = $this->get("StudentsFailedByQuizzId");
		
		parent::display($tpl);
	}
	function addQuizzes ($tpl =  null ) { 
		$db = JFactory::getDBO();		
		$user = JFActory::getUser();
		$search_text = JFactory::getApplication()->input->get('search_text', "");
		$sql = "SELECT id, name FROM #__guru_quiz";
		if($search_text!=""){
			$sql = $sql." where name LIKE '%".$search_text."%' and is_final <> 1 and author=".intval($user->id);
		}
		else{
			$sql = $sql." where is_final <> 1 and author=".intval($user->id);
		}
		$db->setQuery($sql);
		$list_quizzes=$db->loadAssocList();	
		$this->list_quizzes = $list_quizzes;
		parent::display($tpl);
	}
	
	function studentdetails($tpl =  null){
		$this->details = $this->get("StudentDetails");
		parent::display($tpl);
	}
	
	function studentdetailslesson($tpl =  null){
		$app = JFactory::getApplication('site');
		$pid = $app->getUserStateFromRequest("pid", "pid", "0");
		$userid = JFactory::getApplication()->input->get("userid", "0");
		$db = JFactory::getDBO();
		
		$ads = $this->get('listDays');
		$this->ads = $ads;
		
		$all_lessons = $this->getAllViewedLessonsArray($pid, $userid);
		$this->all_lessons = $all_lessons;
		
		parent::display($tpl);
	}

	function studentdetailscert($tpl =  null){
		$my_courses = $this->get('StudentCertificateCourses');
		$this->my_courses = $my_courses;
		
		parent::display($tpl);
	}
	
	function studentquizes($tpl =  null){
		$this->quizes = $this->get("StudentQuizes");
		parent::display($tpl);
	}
	
	function quizdetails($tpl =  null){
		$this->ads = $this->get("StudentQuizCompleted");
		parent::display($tpl);
	}
	
	function student_quizdetails($tpl =  null){
		$this->ads = $this->get("StudentQuizCompleted");
		parent::display($tpl);
	}
	
	function getQuizName($quiz_id){
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_quiz WHERE id=".intval($quiz_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	
	function getScoreQuiz($quiz_id, $user_id, $pid){
		$db = JFactory::getDBO();
		
		$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($pid)." order by id desc limit 0,1";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		
		return @$result["0"];
	}
	
	function getAnsGived($user_id, $quiz_id, $pid){
		$db = JFactory::getDBO();
		
		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$isfinal=$db->loadResult();
		if($isfinal == 0){
			$sql = "SELECT q.id, answers_gived FROM #__guru_quiz_question_taken_v3 qq, #__guru_questions_v3 q  WHERE qq.question_id=q.id and user_id=".intval($user_id)." and show_result_quiz_id = (SELECT id FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($pid)." order by id desc limit 0,1) ORDER BY q.reorder";
		}
		else{
			$sql = "SELECT q.id, qq.answers_gived FROM #__guru_quiz_question_taken_v3 qq INNER JOIN #__guru_questions_v3 q ON( qq.question_id = q.id) WHERE qq.user_id=".intval($user_id)." and qq.show_result_quiz_id= (SELECT id FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($pid)." order by id desc limit 0,1) order by qq.question_order_no";
		}
		$db->setQuery($sql);
		$result_ansgived = $db->loadObjectList("id");	
		return $result_ansgived;		
	}
	
	function getAnsRight($user_id, $quiz_id, $pid){
		$db = JFactory::getDBO();
		
		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$isfinal=$db->loadResult();
		if($isfinal == 0){
			$sql = "SELECT id, answers FROM #__guru_questions_v3 WHERE qid=".intval($quiz_id). " ORDER BY reorder ";
		}
		else{
			$sql = "SELECT w1.id, w1.answers FROM #__guru_questions_v3 w1 INNER JOIN #__guru_quiz_question_taken_v3 w2 ON (w1.id= w2.question_id)  WHERE w2.show_result_quiz_id= (SELECT id FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($pid)." order by id desc limit 0,1) order by question_order_no";
		}
		$db->setQuery($sql);
		$result_ansright = $db->loadObjectList("id");
		return $result_ansright;
	}
	
	function getQuestionName($user_id, $pid, $quiz_id){
		$db = JFactory::getDBO();
		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$isfinal=$db->loadResult();
		
		$sql = "SELECT show_nb_quiz_select_up from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		@$show_nb_quiz_select_up=$db->loadResult();
		
		if($isfinal == 0 && @$show_nb_quiz_select_up ==1){
			$sql = "SELECT id, text FROM #__guru_questions_v3 WHERE qid=".intval($quiz_id)." ORDER BY reorder";
		}
		elseif($isfinal == 0 && @$show_nb_quiz_select_up ==0){
			$sql = "SELECT  q1.id, q1.text, q2.question_order_no  FROM #__guru_questions_v3 q1 INNER JOIN #__guru_quiz_question_taken_v3 q2 ON (q1.id = q2.question_id)  WHERE q1.qid=".intval($quiz_id)." and q2.show_result_quiz_id=(SELECT id FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($pid)." order by id desc limit 0,1) order by q2.question_order_no";
		}
		else{
			$sql = "SELECT q1.id, q1.text FROM #__guru_questions_v3 q1 INNER JOIN #__guru_quiz_question_taken_v3 q2 ON (q1.id = q2.question_id)  WHERE  q2.show_result_quiz_id= (SELECT id FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($pid)." order by id desc limit 0,1) order by q2.question_order_no";
		}
		$db->setQuery($sql);
		$result_question = $db->loadObjectList("id");
	    return $result_question; 
	}
	
	function getAllAns($quiz_id, $user_id, $pid){
		$db = JFactory::getDBO();
		$sql = "SELECT is_final from #__guru_quiz where id=".intval($quiz_id);
		$db->setQuery($sql);
		$isfinal = $db->loadColumn();
		$isfinal = $isfinal[0];
		if($isfinal == 1){
			$sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 WHERE show_result_quiz_id=(SELECT id FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($pid)." order by id desc limit 0,1) order by question_order_no";
		}
		else{
			$sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 qq, #__guru_questions_v3 q  WHERE qq.question_id=q.id and show_result_quiz_id=(SELECT id FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($pid)." order by id desc limit 0,1) ORDER BY reorder"; 
		}	
		$db->setQuery($sql);
		$result=$db->loadObjectList();
		$i = 0;
		$result_allans = array();
		
		foreach($result as $key=>$value){
			if($isfinal == 0){
				$sql = "SELECT a1, a2,a3,a4,a5,a6,a7,a8,a9,a10 FROM #__guru_questions_v3 WHERE qid=".intval($quiz_id)." and id=".$value->question_id;
			}
			else{
				$sql = "SELECT a1, a2,a3,a4,a5,a6,a7,a8,a9,a10 FROM #__guru_questions_v3 WHERE id=".$value->question_id;
			}
			$db->setQuery($sql);
			$choices = $db->loadAssocList();
			$correct_ans = "";
			
			if(!isset($choices["0"])){
				continue;
			}
			
			if($choices[0]['a1'] != "") 
			{
				$correct_ans .= "1a|||";
			}
			if($choices[0]['a2'] != "") 
			{
				$correct_ans .= "2a|||";
			}
			if($choices[0]['a3'] != "") 
			{
				$correct_ans .= "3a|||";
			}
			if($choices[0]['a4'] != "") 
			{
				$correct_ans .= "4a|||";
			}
			if($choices[0]['a5'] != "") 
			{
				$correct_ans .= "5a|||";
			}
			if($choices[0]['a6'] != "") 
			{
				$correct_ans .= "6a|||";
			}
			if($choices[0]['a7'] != "") 
			{
				$correct_ans .= "7a|||";
			}
			if($choices[0]['a8'] != "") 
			{
				$correct_ans .= "8a|||";
			}
			if($choices[0]['a9'] != "") 
			{
				$correct_ans .= "9a|||";
			}
			if($choices[0]['a10'] != "") 
			{
				$correct_ans .= "10a|||";
			}
			$result_allans[$value->question_id] = $correct_ans;
		}
		return $result_allans;	
	}
	
	function getAllAnsText($quiz_id, $user_id, $pid){
		$db =JFactory::getDBO();
		$sql = "SELECT is_final from #__guru_quiz where id=".intval($quiz_id);
		$db->setQuery($sql);
		$isfinal = $db->loadColumn();
		$isfinal = $isfinal[0];
		
		if($isfinal == 0){
			$sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 qq, #__guru_questions_v3 q  WHERE qq.question_id=q.id and show_result_quiz_id=(SELECT id FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($pid)." order by id desc limit 0,1) ORDER BY reorder";
		}
		else{
			$sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 WHERE show_result_quiz_id=(SELECT id FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($pid)." order by id desc limit 0,1) order by question_order_no";
		}
		$db->setQuery($sql);
		$result=$db->loadObjectList();
		$i = 0;
		$result_allans = array();
		
		foreach($result as $key=>$value){
			if($isfinal == 0){
				$sql = "SELECT a1, a2,a3,a4,a5,a6,a7,a8,a9,a10 FROM #__guru_questions_v3 WHERE qid=".intval($quiz_id)." and id=".$value->question_id;
			}
			else{
				$sql = "SELECT a1, a2,a3,a4,a5,a6,a7,a8,a9,a10 FROM #__guru_questions_v3 WHERE id=".$value->question_id;
			
			}
			$db->setQuery($sql);
			$choices = $db->loadAssocList();
			$correct_ans = "";
			
			if(!isset($choices["0"])){
				continue;
			}
			
			if($choices[0]['a1'] != "") 
			{
				$correct_ans .= $choices[0]['a1']."|||";
			}
			if($choices[0]['a2'] != "") 
			{
				$correct_ans .= $choices[0]['a2']."|||";
			}
			if($choices[0]['a3'] != "") 
			{
				$correct_ans .= $choices[0]['a3']."|||";
			}
			if($choices[0]['a4'] != "") 
			{
				$correct_ans .= $choices[0]['a4']."|||";
			}
			if($choices[0]['a5'] != "") 
			{
				$correct_ans .= $choices[0]['a5']."|||";
			}
			if($choices[0]['a6'] != "") 
			{
				$correct_ans .= $choices[0]['a6']."|||";
			}
			if($choices[0]['a7'] != "") 
			{
				$correct_ans .= $choices[0]['a7']."|||";
			}
			if($choices[0]['a8'] != "") 
			{
				$correct_ans .= $choices[0]['a8']."|||";
			}
			if($choices[0]['a9'] != "") 
			{
				$correct_ans .= $choices[0]['a9']."|||";
			}
			if($choices[0]['a10'] != "") 
			{
				$correct_ans .= $choices[0]['a10']."|||";
			}
			$result_allans[$value->question_id] = $correct_ans;
		}
		return $result_allans;	
	}
	
	function terms($tpl = null) {
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_config";
		$db->setQuery($sql);
		$configs = $db->loadAssocList();
		$this->configs = $configs;
		
		parent::display($tpl);
	}
	
	function userName($id){
		$name = "";
		$db = JFactory::getDBO();
		$sql = "select firstname, lastname from #__guru_customer where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		if(isset($result) && count($result) > 0){
			$name = $result["0"]["firstname"]." ".$result["0"]["lastname"];
		}
		return $name;
	}
	
	function userImage($id){
		$image = "";
		$db = JFactory::getDBO();
		$sql = "select image from #__guru_customer where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		if(isset($result) && count($result) > 0){
			$image = $result["0"]["image"];
		}
		return $image;
	}
	
	function userEmail($id){
		$name = "";
		$db = JFactory::getDBO();
		$sql = "select email from #__users where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadcolumn();
		$result = @$result["0"];
		return $result;
	}
	function getDetailsPagination(){
		$pagination = $this->get('Pagination');
		return $pagination;
	}
	
	function authorcommissions_details_paid($tpl =  null){
		$details_paid =  $this->get('PendingDetails');
		$this->details_paid = $details_paid;
		
		$config = $this->get('Config');
		$this->config = $config;
		
		parent::display($tpl);
	}
	
	function getAllLessons($courseid){
		$db = JFactory::getDBO();
		$sql = "select distinct(m.media_id) from #__guru_mediarel m, #__guru_days d where d.id=m.type_id and m.type='dtask' and d.pid=".intval($courseid);
		$db->setQuery($sql);
		$media_id = $db->loadColumn();
		$media_id = array_filter($media_id);
		
		if(isset($media_id) && count($media_id) > 0){
			$sql = "select m.type_id from #__guru_mediarel m where m.type='scr_m' and m.type_id in (".implode(", ", $media_id).") and m.layout='12'";
			$db->setQuery($sql);
			$quizzes_lessons = $db->loadColumn();
			
			$diff = array_diff($media_id, $quizzes_lessons);
			
			if(isset($diff) && count($diff) > 0){
				return count($diff);
			}
		}
		
		return "0";
	}
	
	function getAllViewedLessons($courseid, $userid){
		$db = JFactory::getDBO();
		$sql = "select distinct(m.media_id) from #__guru_mediarel m, #__guru_days d where d.id=m.type_id and m.type='dtask' and d.pid=".intval($courseid);
		$db->setQuery($sql);
		$media_id = $db->loadColumn();
		
		if(isset($media_id) && count($media_id) > 0){
			$sql = "select distinct(m.type_id) from #__guru_mediarel m where m.type='scr_m' and m.type_id in (".implode(", ", $media_id).") and m.layout='12'";
			$db->setQuery($sql);
			$quizzes_lessons = $db->loadColumn();
			
			$lessons = array_diff($media_id, $quizzes_lessons);
			
			$sql = "select distinct(lesson_id) from #__guru_viewed_lesson where pid=".intval($courseid)." and user_id=".intval($userid);
			$db->setQuery($sql);
			$viewed_lessons = $db->loadColumn();
			$viewed_lessons = @$viewed_lessons["0"];
			
			if(isset($viewed_lessons) && trim($viewed_lessons) != ""){
				$viewed_lessons = str_replace("||", ",", $viewed_lessons);
				$viewed_lessons = str_replace("|", "", $viewed_lessons);
				$viewed_lessons = explode(",", $viewed_lessons);
				
				$intersesc = array_intersect($lessons, $viewed_lessons);
				
				if(isset($intersesc) && count($intersesc) > 0){
					return count($intersesc);
				}
			}
		}
		
		return "0";
	}
	
	function getAllViewedLessonsArray($courseid, $userid){
		$db = JFactory::getDBO();
		$sql = "select m.media_id from #__guru_mediarel m, #__guru_days d where d.id=m.type_id and m.type='dtask' and d.pid=".intval($courseid);
		$db->setQuery($sql);
		$media_id = $db->loadColumn();
		
		if(isset($media_id) && count($media_id) > 0){
			$sql = "select m.type_id from #__guru_mediarel m where m.type='scr_m' and m.type_id in (".implode(", ", $media_id).") and m.layout='12'";
			$db->setQuery($sql);
			$quizzes_lessons = $db->loadColumn();
			
			$lessons = array_diff($media_id, $quizzes_lessons);
			
			$sql = "select lesson_id from #__guru_viewed_lesson where pid=".intval($courseid)." and user_id=".intval($userid);
			$db->setQuery($sql);
			$viewed_lessons = $db->loadColumn();
			$viewed_lessons = @$viewed_lessons["0"];
			
			if(isset($viewed_lessons) && trim($viewed_lessons) != ""){
				$viewed_lessons = str_replace("||", ",", $viewed_lessons);
				$viewed_lessons = str_replace("|", "", $viewed_lessons);
				$viewed_lessons = explode(",", $viewed_lessons);
				
				$intersesc = array_intersect($lessons, $viewed_lessons);
				
				return $intersesc;
			}
		}
		
		return array();
	}
	
	function getNeedMark($course_id){
		$db = JFactory::getDBO();
		$sql = "select question_ids, user_id from #__guru_quiz_question_taken_v3 where pid=".intval($course_id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				if(trim($value["question_ids"]) != ""){
					$sql = "select q.id from #__guru_questions_v3 q where q.id in (".$value["question_ids"].") and q.type='essay'";
					$db->setQuery($sql);
					$essay = $db->loadColumn();
					
					if(isset($essay) && count($essay) > 0){
						foreach($essay as $key_essay=>$value_essay){
							$sql = "select count(*) from #__guru_quiz_essay_mark where question_id=".intval($value_essay)." and user_id=".$value["user_id"];
							$db->setQuery($sql);
							$marked = $db->loadColumn();
							$marked = @$marked["0"];
							
							if(intval($marked) == 0){
								return true;
							}
						}
					}
				}
				
			}
		}
		return FALSE;
	}
	
	function getAuthorNrCourses($author){
		$model = $this->getModel();
		$nr_courses = $model->getAuthorNrCourses($author);
		
		return $nr_courses;
	}
	
	function getUserListCourses($value_selected){
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		
		//-------------------------------------------------- quiz change
		$quizzes_ids = array("0");

		$sql = "select `id` from #__guru_program where `author`='".intval($user->id)."' OR `author` like '%|".$user->id."|%'";
		$db->setQuery($sql);
		$authors_courses = $db->loadColumn();

		if(isset($authors_courses) && count($authors_courses) > 0){
			$sql = "select distinct(m.media_id) from #__guru_mediarel m, #__guru_days d where d.id=m.type_id and m.type='dtask' and d.pid in (".implode(",", $authors_courses).")";
			$db->setQuery($sql);
			$media_id = $db->loadColumn();
			$media_id = array_filter($media_id);

			if(isset($media_id) && count($media_id) > 0){
				$sql = "select m.type_id from #__guru_mediarel m where m.type='scr_m' and m.type_id in (".implode(", ", $media_id).") and m.layout='12'";
				$db->setQuery($sql);
				$quizzes_lessons = $db->loadColumn();
				
				if(isset($quizzes_lessons) && count($quizzes_lessons) > 0){
					$sql = "select `media_id` from #__guru_mediarel m where m.type='scr_m' and type_id in (".implode(", ", $quizzes_lessons).") and m.layout='12'";
					$db->setQuery($sql);
					$quizzes_ids = $db->loadColumn();
				}
			}
		}
		else{
			$authors_courses = array("0");
		}

		$sql = "select q.id, q.question_content from #__guru_questions_v3 q, #__guru_quiz quiz where q.qid=quiz.id and q.type='essay' and quiz.`id` in (".implode(",", $quizzes_ids).")";
		//--------------------------------------------------
		$db->setQuery($sql);
		$essays_questions = $db->loadColumn();
	
		if(!isset($essays_questions) || count($essays_questions) == 0){
			$essays_questions = array("0");
		}

		if(!isset($authors_courses) || count($authors_courses) == 0){
			$authors_courses = array("0");
		}
	
		$sql = "select q.question_content, q.points, u.email, c.id as user_id, c.firstname, c.lastname, c.image, A.question_id, A.answers_given, em.grade, em.feedback_quiz_results, A.pid from #__users u, #__guru_questions_v3 q, #__guru_customer c, (select user_id, question_id, answers_given, pid from #__guru_quiz_taken_v3 where question_id in (".implode(",", $essays_questions).") and id IN ( SELECT MAX(id) FROM #__guru_quiz_taken_v3 GROUP BY user_id, question_id) and pid in (".implode(",", $authors_courses).") ) as A left outer join #__guru_quiz_essay_mark em on em.question_id=A.question_id and em.user_id=A.user_id where u.id=c.id and q.id=A.question_id and c.id=A.user_id";
		$db->setQuery($sql);
		$items = $db->loadAssocList();
		
		$courses_ids = array("0");
		
		if(isset($items) && count($items) > 0){
			foreach($items as $key=>$item){
				if(!in_array($item["pid"], $courses_ids)){
					$courses_ids[] = intval($item["pid"]);
				}
			}
		}
		
		$sql = "select id, name from #__guru_program where id in (".implode(",", $courses_ids).")";
		$db->setQuery($sql);
		$courses = $db->loadAssocList();
		
		$return  = '<select name="filter_courses" style="width:100%;" onchange="javascript:document.adminForm.submit();">';
		$return .= 		'<option value="0">'.JText::_("GURU_ALL_COURSES").'</option>';
		
		if(isset($courses) && count($courses) > 0){
			foreach($courses as $key=>$course){
				$selected = "";
				
				if(intval($value_selected) == intval($course["id"])){
					$selected = 'selected="selected"';
				}
				
				$return .= '<option value="'.intval($course["id"]).'" '.$selected.' >'.$course["name"].'</option>';
			}
		}
		
		$return .= '</select>';
		
		return $return;
	}
	
	function getUserListEssays($value_selected){
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		
		//-------------------------------------------------- quiz change
		$quizzes_ids = array("0");

		$sql = "select `id` from #__guru_program where `author`='".intval($user->id)."' OR `author` like '%|".$user->id."|%'";
		$db->setQuery($sql);
		$authors_courses = $db->loadColumn();

		if(isset($authors_courses) && count($authors_courses) > 0){
			$sql = "select distinct(m.media_id) from #__guru_mediarel m, #__guru_days d where d.id=m.type_id and m.type='dtask' and d.pid in (".implode(",", $authors_courses).")";
			$db->setQuery($sql);
			$media_id = $db->loadColumn();
			$media_id = array_filter($media_id);

			if(isset($media_id) && count($media_id) > 0){
				$sql = "select m.type_id from #__guru_mediarel m where m.type='scr_m' and m.type_id in (".implode(", ", $media_id).") and m.layout='12'";
				$db->setQuery($sql);
				$quizzes_lessons = $db->loadColumn();
				
				if(isset($quizzes_lessons) && count($quizzes_lessons) > 0){
					$sql = "select `media_id` from #__guru_mediarel m where m.type='scr_m' and type_id in (".implode(", ", $quizzes_lessons).") and m.layout='12'";
					$db->setQuery($sql);
					$quizzes_ids = $db->loadColumn();
				}
			}
		}
		else{
			$authors_courses = array("0");
		}

		$sql = "select q.id, q.question_content from #__guru_questions_v3 q, #__guru_quiz quiz where q.qid=quiz.id and q.type='essay' and quiz.`id` in (".implode(",", $quizzes_ids).")";
		//--------------------------------------------------
		$db->setQuery($sql);
		$essays = $db->loadAssocList();
		
		$return  = '<select name="filter_essays" style="width:100%;" onchange="javascript:document.adminForm.submit();">';
		$return .= 		'<option value="0">'.JText::_("GURU_ALL_ESSAYS").'</option>';
		
		if(isset($essays) && count($essays) > 0){
			foreach($essays as $key=>$essay){
				$selected = "";
				
				if(intval($value_selected) == intval($essay["id"])){
					$selected = 'selected="selected"';
				}
				
				$return .= '<option value="'.intval($essay["id"]).'" '.$selected.' >'.$essay["question_content"].'</option>';
			}
		}
		
		$return .= '</select>';
		
		return $return;
	}
	
	function getUserEssayStatus($value_selected){
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		
		$pending_status_selected = 'selected="selected"';
		$graded_status_selected = "";
		
		if($value_selected == "1"){
			$pending_status_selected = "";
			$graded_status_selected = 'selected="selected"';
		}
		
		$return  = '<select id="filter_status" name="filter_status" style="width:100%;" onchange="javascript:document.adminForm.submit();">';
		$return .= 		'<option value="0" '.$pending_status_selected.' >'.JText::_("GURU_PENDING").'</option>';
		$return .= 		'<option value="1" '.$graded_status_selected.' >'.JText::_("GURU_GRADED").'</option>';
		$return .= '</select>';
		
		return $return;
	}

	function projects($tpl=null){
		$document = JFactory::getDocument();
        $document->setTitle(JText::_('GURU_TEACHER_PROJECTS'));
        $mainframe = JFactory::getApplication();
        $jinput= $mainframe->input;
        
		$user = JFactory::getUser();
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_guru/models');
		$model = JModelLegacy::getInstance('guruProjects','guruModel');

		$filter['author_id'] = $user->id;
		$filter['course_id'] = $jinput->get->get('filter_course_id', '', "raw");
		$filter['keyword'] =  $jinput->get->get('filter_keyword', '', "raw");
		
		$filter['limitstart'] = $jinput->get->get('limitstart', 0, '', 'int');
		$filter['limit'] = $jinput->get->get('limit',  $mainframe->getCfg('list_limit'),  'int');

		$listProjects   = $model->getListProjects($filter);
		$my_courses = $this->getMyCourses();

		$this->filter_keyword = $filter['keyword'];
		$this->filter_course_id = $filter['course_id'];
		$this->my_courses = $my_courses;
		$this->listProjects = $listProjects;
		$pagination = $model->getPagination();
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}

	function scorm($tpl=null){
		$document = JFactory::getDocument();
        $document->setTitle(JText::_('GURU_TEACHER_SCORM'));
        $mainframe = JFactory::getApplication();
        $jinput= $mainframe->input;
        
		$user = JFactory::getUser();
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_guru/models');
		$model = JModelLegacy::getInstance('guruScorm','guruModel');

		$filter['author_id'] = $user->id;
		$filter['course_id'] = $jinput->get->get('filter_course_id', '', "raw");
		$filter['keyword'] =  $jinput->get->get('filter_keyword', '', "raw");
		
		$filter['limitstart'] = $jinput->get->get('limitstart', 0, '', 'int');
		$filter['limit'] = $jinput->get->get('limit',  $mainframe->getCfg('list_limit'),  'int');

		$listScorm   = $model->getListScorm($filter);
		$my_courses = $this->getMyCourses();

		$this->filter_keyword = $filter['keyword'];
		$this->filter_course_id = $filter['course_id'];
		$this->my_courses = $my_courses;
		$this->listScorm = $listScorm;
		$pagination = $model->getPagination();
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}

	/**
	* project result
	*
	*/
	function projectResult($tpl=null){
		$document = JFactory::getDocument();
        $document->setTitle(JText::_('GURU_TEACHER_PROJECT_RESULTS'));
        $mainframe = JFactory::getApplication();

        $jinput= $mainframe->input;
        $id = JFactory::getApplication()->input->get('id', 0, 'raw');

        $user = JFactory::getUser();
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_guru/models');
		$model = JModelLegacy::getInstance('guruProjects','guruModel');

		// project detail
		require_once(JPATH_BASE.'/components/com_guru/tables/guruproject.php');
	    $projectDetail =  new TableguruProject();
		$where = array('id'=>$id);
	    $projectDetail->load($where);
	    
	    if($projectDetail->author_id != $user->id){
	    	$mainframe->redirect( JUri::root() . 'index.php?option=com_guru&view=guruauthor&task=projects&layout=projects', $message, 'success');
	    	return false;
	    }

		$filter['limitstart'] = $jinput->get->get('limitstart', 0, '', 'int');
		$filter['limit'] = $jinput->get->get('limit',  $mainframe->getCfg('list_limit'),  'int');
		$filter['id'] = $id;

		$listProjectResults = $model->getListProjectResults($filter);
		
		$this->listProjectResults = $listProjectResults;
		$this->projectDetail = $projectDetail;

		parent::display($tpl);
	}

	/**
	* view for project form edit and add
	*
	*/
	function projectForm($tpl = null){
		require_once(JPATH_BASE.'/components/com_guru/tables/guruproject.php');
	    $projectTable =  new TableguruProject();
	        
		$mainframe = JFactory::getApplication();
        $id = JFactory::getApplication()->input->get("id", "0", "raw");

        // get detail if Id exist
        if($id){
        	$where = array('id'=>$id);
	        $projectTable->load($where);
	        $projectDetail = $projectTable;
        }
        $projectDetail = $projectTable;
        $my_courses = $this->getMyCourses();
        
		$this->projectDetail = $projectDetail;
		$this->my_courses = $my_courses;
		parent::display($tpl);
	}

	/**
	* view for scorm form edit and add
	*
	*/
	function scormForm($tpl = null){
		require_once(JPATH_BASE.'/components/com_guru/tables/guruscorm.php');
	    $scormTable =  new TableguruScorm();
	        
		$mainframe = JFactory::getApplication();
        $id = JFactory::getApplication()->input->get("id", "0", "raw");

        // get detail if Id exist
        if($id){
        	$where = array('id'=>$id);
	        $scormTable->load($where);
	        $scormDetail = $scormTable;
        }
        $scormDetail = $scormTable;
        $my_courses = $this->getMyCourses();
        
		$this->scormDetail = $scormDetail;
		$this->my_courses = $my_courses;
		parent::display($tpl);
	}

	function projectCourse($id){
		$model = $this->getModel();
		$course = $model->projectCourse($id);
		return $course;
	}

	function scormCourse($id){
		$model = $this->getModel();
		$course = $model->scormCourse($id);
		return $course;
	}

	function getStudentCertificates($userid, $type){
		$model = $this->getModel();
		$student_certificates = $model->getStudentCertificates($userid, $type);
		return $student_certificates;
	}

	function getConfigSettings(){
		$model = $this->getModel();
		$configs = $model->getConfigSettings();

		return $configs;
	}

	function getCustomFieldsGroups(){
		$db = JFactory::getDbo();
		
		$sql = "select * from #__guru_groups where `published`='1' ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$groups = $db->loadAssocList();

		return $groups;
	}
	
	function getCustomFields($group_id){
		$db = JFactory::getDbo();
		
		$sql = "select `teacher_custom_fields` from #__guru_config";
		$db->setQuery($sql);
		$teacher_custom_fields = $db->loadColumn();

		if(isset($teacher_custom_fields["0"]) && trim($teacher_custom_fields["0"]) != ""){
			$teacher_custom_fields = json_decode($teacher_custom_fields["0"], true);

			if(is_array($teacher_custom_fields) && count($teacher_custom_fields) > 0){
				$teacher_custom_fields = implode(",", $teacher_custom_fields);
			}
			else{
				$teacher_custom_fields = "0";
			}
		}
		else{
			$teacher_custom_fields = "0";
		}

		$sql = "select * from #__guru_fields where `group_id`=".intval($group_id)." and `published`='1' and `id` in (".$teacher_custom_fields.") ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$fields = $db->loadAssocList();

		return $fields;
	}

	function isJSInstalled(){
		$db = JFactory::getDbo();
		$sql = "select `extension_id` from #__extensions where `type`='component' and `element`='com_community'";
		$db->setQuery($sql);
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
		$groups = $db->loadAssocList();

		return $groups;
	}

	function getTeacherRedirectLogoutUrl(){
		$db = JFactory::getDbo();

		$sql = "select `st_authorpage` from #__guru_config";
		$db->setQuery($sql);
		$st_authorpage = $db->loadColumn();
		$st_authorpage = @$st_authorpage["0"];
		$st_authorpage = json_decode($st_authorpage, true);

		if(isset($st_authorpage["teacher_redirect_logout"]) && trim($st_authorpage["teacher_redirect_logout"]) != ""){
			return trim($st_authorpage["teacher_redirect_logout"]);
		}
		else{
			return "";
		}
	}
};
	
?>