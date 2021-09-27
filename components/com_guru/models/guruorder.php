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


class guruModelguruOrder extends JModelLegacy {
	var $_orders;
	var $_order;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct () {
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");
		$this->setId((int)$cids);
		global $option;
		
		// Get the pagination request variables
		/*$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);*/
	}

	function getCourseName($id){
		$db = JFactory::getDBO();
		$sql = "select name from #__guru_program where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	function getCoursesPromo($id){
		$db = JFactory::getDBO();
		$sql = "select courses_ids from #__guru_promos where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return $result;
	}
	function getPromo($id){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_promos where id='".intval($id)."'";
		$db->setQuery($sql);
		$promo = $db->loadObjectList();			
		$promo = @$promo["0"];
		
		return $promo;
	}
	function getPromoDiscountCourse($total, $promo_id){
		$old_total = $total;
		$value_to_display = "";
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_promos where id='".intval($promo_id)."'";
		$db->setQuery($sql);
		$promo = $db->loadObjectList();			
		$promo_details = $promo["0"];
		
		if($promo_details->typediscount == '0') {//use absolute values					
			$difference = $total - (float)$promo_details->discount;
			if($difference < 0){
				$total = 0;
			}
			else{
				$total = $difference;
			}					
		}
		else{//use percentage
			$total = ($promo_details->discount / 100)*$total;
			$difference = $old_total - $total;	
			if($difference < 0){
				$total =  "0";
			}
			else{
				$total = $difference;
			}
		}
		
		return $total;
	}
	
	function getPromoDiscountCourses($total, $promo_id){
		$old_total = $total;
		$value_to_display = "";
		$promo_details = self:: getPromo($promo_id);
		
		if(@$promo_details->typediscount == '0') {//use absolute values					
			$difference = $total - (float)$promo_details->discount;
			if($difference < 0){
				$total = 0;
			}
			else{
				$total = $difference;
			}					
			$value_to_display = $promo_details->discount;
		}
		else{//use percentage
			@$total = (@$promo_details->discount / 100)*$total;
			@$difference = $old_total - $total;	
			
			if($difference < 0){
				$value_to_display =  "0";
			}
			else{
				@$discount = $old_total - $difference;
				$value_to_display =  (float)$discount;
			}
		}
		return $value_to_display;
	}

	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) @$this->getListOrders();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function setId($id) {
		$this->_id = $id;

		$this->_order = null;
	}

	function getMyOrders(){
		$db = JFactory::getDBO();
		$search = JFactory::getApplication()->input->get("search", "", "raw"); // view only orders for one course
		$course = JFactory::getApplication()->input->get("course", "", "raw"); // view only orders for one course
		
		$and = "";
		if($search !=""){
			$course_id  ="SELECT id FROM #__guru_program where name like '%".$search."%' LIMIT 0,1";
			$db->setQuery($course_id);
			$course_id = $db->loadResult();
			
			if(intval($course_id) != "0"){
				$and .= " and (courses like '".intval($course_id)."-%' OR courses like '%|".intval($course_id)."-%') ";
			}	
		}
		
		if(intval($course) != 0){
			$and .= " and (courses like '".intval($course)."-%' OR courses like '%|".intval($course)."-%') ";
		}
				
		$my = JFactory::getUser();
		
		$sql = "SELECT * FROM #__guru_order
				WHERE userid = ".$my->id." ".$and." order by order_date desc";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getMyCourses(){
		$search = JFactory::getApplication()->input->get("search_course", "", "raw");
		$and = "";
		if($search !=""){
			$and .= " and p.name like '%".addslashes(trim($search))."%'";	
		}
		$my = JFactory::getUser();
		$db = JFactory::getDBO();		
		$user_id = $my->id;
		
		$sql = "SELECT
					o.*,
					bc.*,
					s.name as plan_name,
					p.name as course_name,
					p.id as course_id,
					p.certificate_term as certerm,
					c.`name` as category_name
				FROM
					`#__guru_order` o,
					`#__guru_buy_courses` bc
					left outer join `#__guru_subplan` s on bc.plan_id = s.id,
					`#__guru_program` p,
					`#__guru_category` c
				WHERE
					c.`id` = p.`catid`
					and o.status = 'Paid'
					and o.id = bc.order_id
					AND bc.userid = ".intval($user_id)." ".$and." AND bc.course_id = p.id
					and p.published = 1
					and o.userid = bc.userid
				ORDER BY
					o.id DESC";
		
		$db->setQuery($sql);
		$result = $db->loadAssocList();	
		return $result;
	}
	
	
	function getCertificateId($uid,$course_id){
		$db = JFactory::getDBO();
		$sql = "SELECT id FROM #__guru_mycertificates
				WHERE user_id = ".intval($uid)." and course_id=". intval($course_id) ;
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	
	function getCertificate(){ 
		$db = JFactory::getDBO();
		$sql = "SELECT general_settings  FROM #__guru_certificates
				WHERE id = 1";
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	
	function getAuthorName($id){ 
		$my = JFactory::getUser();
		$db = JFactory::getDBO();
		
		$sql = "SELECT author FROM #__guru_program WHERE id=".intval($id);
		$db->setQuery($sql);
		$authors = $db->loadAssocList();
		$authors = $authors["0"]["author"];
		$authors = explode("|", $authors);
		$authors = array_filter($authors);
		
		if(count($authors) == 0){
			$authors = array("0"=>"0");
		}
		
		$sql = "SELECT name FROM #__users
				WHERE id in (".implode(",", $authors).")";
		$db->setQuery($sql);
		$result = $db->loadResult();
		$result = implode(", ", $result);
		
		return $result;
	}
	
	function getConfigSettings(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config WHERE id=1";
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}
	function checkCustomerProfile($user_id){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_customer where id=".intval($user_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getDiscountDetails($promocode){
		$db = JFactory::getDBO();
		$sql = "select discount, typediscount from #__guru_promos where id = '".addslashes(trim($promocode))."'";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getOrderFromOrders(){
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("orderid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		if(intval($user_id) == 0){
			$user_id = JFactory::getApplication()->input->get("user_reques", "0", "raw");
		}
		
		$orders_is_for_user =  "select id from #__guru_order where userid=".intval($user_id);
		$db->setQuery($orders_is_for_user);
		$orders_is_for_user = $db->loadColumn();
		
		if(in_array($id, $orders_is_for_user)){
			$sql = "select * from #__guru_order where id=".intval($id);
			$db->setQuery($sql);
			$result = $db->loadAssocList();
			return $result;
		}
		else{
			return array();
		}
	}
	
	function getCourses($ids){
		$db =  JFactory::getDBO();
		if(trim($ids) != ""){
			$sql = "SELECT * FROM #__guru_program WHERE id in (".$ids.")";
			$db->setQuery($sql);
			$result = $db->loadAssocList();
			return $result;
		}
		else{
			return array();
		}
	}
	
	function getCustomerDetails($id){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_customer where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getPlans(){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_subplan";
		$db->setQuery($sql);
		$result = $db->loadAssocList("id");
		return $result;
	}
	
	function countCourseOrders($id){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$sql = "select count(*) from #__guru_order where (courses like '".intval($id)."-%' OR courses like '%|".intval($id)."-%') and status='Paid' and userid=".intval($user_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	function getDateType(){
		$db = JFactory::getDBO();
		$sql = "select datetype from #__guru_config WHERE id = '1'";
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	
	}
	
	function courseCompleted($user_id, $id){
		$db = JFactory::getDBO();
		$sql = "SELECT completed from #__guru_viewed_lesson WHERE user_id =".intval($user_id)." and pid=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		$result = @$result["0"];
		
		if($result == 1){
			return true;	
		}
		else{
			return false;
		}
	}
	
	function dateCourseCompleted($user_id, $id){
		$db = JFactory::getDBO();
		$sql = "SELECT date_completed from #__guru_viewed_lesson WHERE user_id =".intval($user_id)." and pid=".$id;
		$db->setQuery($sql);
		$result = $db->loadResult();
		if(isset($result) && $result!= '0000-00-00' ){
			return $result;
		}
		else{
			return "";
		}
	}
	function getTermCourse($id){
		$db = JFactory::getDBO();
		$sql = "SELECT certificate_term FROM #__guru_program
				WHERE id = ".intval($id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	
	}
	function getCourseidsList($userid){
		$db = JFactory::getDBO();
		$sql = "SELECT course_id FROM #__guru_mycertificates
				WHERE user_id = ".intval($userid);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return $result;
	}
	
	function getProgressPercentage($user_id, $id){
		$db = JFactory::getDbo();
		$return = 0;

		$sql = "SELECT lesson_id, module_id from #__guru_viewed_lesson WHERE user_id =".intval($user_id)." and pid=".$id;
		$db->setQuery($sql);
		$result = $db->loadObjectList();

		if(isset($result) && isset($result["0"])){
			$viewed_lessons = $result["0"]->lesson_id;
			$viewed_lessons = explode("|", $viewed_lessons);

			$sql = "SELECT id FROM #__guru_task WHERE id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (SELECT id FROM #__guru_days WHERE pid =".intval($id).")) order by `ordering` asc, `id` desc";
			$db->setQuery($sql);
			$course_lessons = $db->loadColumn();

			if(isset($course_lessons) && count($course_lessons) > 0){
				$nr_all_lessons = 0;
				$nr_viewed_lessons = 0;

				foreach($course_lessons as $key => $lesson_id){
					$nr_all_lessons ++;
					if(in_array($lesson_id, $viewed_lessons)){
						$nr_viewed_lessons ++;
					}
				}

				$return = ($nr_viewed_lessons * 100) / $nr_all_lessons;
			}
		}

		return number_format($return, 2);
	}

	function getLastViewedLessandMod($user_id, $id){
		$db = JFactory::getDBO();
		$sql = "SELECT lesson_id, module_id from #__guru_viewed_lesson WHERE user_id =".intval($user_id)." and pid=".$id;
		$db->setQuery($sql);
		$result = $db->loadObjectList();

		if(@$result[0]->lesson_id != ""){
			$sql = "SELECT course_type FROM #__guru_program WHERE id=". intval($id);
			$db->setQuery($sql);
			$course_type = $db->loadResult();
			
			$sql = "SELECT id FROM #__guru_task WHERE id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (SELECT id FROM #__guru_days WHERE pid =".intval($id).")) order by `ordering` asc, `id` desc";
			$db->setQuery($sql);
			$course_lessons = $db->loadColumn();

			@$result_lesson = $result[0]->lesson_id;
			$result_lesson = explode('||', trim($result_lesson, "||"));
			$result_lesson1 = end($result_lesson);
			@$result_module = $result[0]->module_id;
			$result_module = explode('||', trim($result_module, "||"));
			$result_module1 = end($result_module);
			
			if(isset($course_lessons) && count($course_lessons) > 0){
				$bottom_lesson_from_course = 0;

				foreach($course_lessons as $key=>$lesson_id){
					if(intval($lesson_id) > 0){
						if(in_array(intval($lesson_id), $result_lesson)){
							$bottom_lesson_from_course = intval($lesson_id);
						}
					}
				}

				if(intval($bottom_lesson_from_course) > 0){
					$result_lesson1 = intval($bottom_lesson_from_course);

					$sql = "select type_id from #__guru_mediarel where type='dtask' and media_id=".intval($result_lesson1)." and type_id <> '0'";
					$db->setQuery($sql);
					$module_id = $db->loadColumn();
					$result_module1 = @$module_id["0"];
				}
			}
			
			$sql = "SELECT name FROM #__guru_task WHERE id=". intval($result_lesson1);
			$db->setQuery($sql);
			$result_lesson = $db->loadResult();
			
			
			$sql = "SELECT title FROM #__guru_days WHERE id=".intval($result_module1);
			$db->setQuery($sql);
			$result_module = $db->loadResult();
			
			$sql = "SELECT id FROM #__guru_days WHERE pid=".$id." ORDER BY ordering";
			$db->setQuery($sql);
			$result_module_id = $db->loadColumn();
			
			$sql = "SELECT id FROM #__guru_task t WHERE id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (".$result_module1.")) ORDER BY t.ordering";
			$db->setQuery($sql);
			$result_lesson_id = $db->loadColumn();
			
			$module_nb = array_search ($result_module1 , $result_module_id );
			$module_nb += 1;
			/*if($module_nb ==0){
				$module_nb +=1;
			}*/
			
			@$lesson_nb = array_search ($result_lesson1 , $result_lesson_id );
			$lesson_nb +=1; 

			if($result_module!=""){
				if($course_type == 1){
					$result = JText::_('GURU_PROGRAM_DETAILS_DAY')." ".$module_nb.":"." ".$result_module. "<br/>".JText::_('GURU_PROGRES_LESSON')." ".$lesson_nb.":"." ".$result_lesson ;	
				}
				else{
					$result = JText::_('GURU_PROGRAM_DETAILS_DAY')." ".$module_nb.":"." ".$result_module. "<br/>".JText::_('GURU_PROGRES_LESSON')." ".$lesson_nb.":"." ".$result_lesson ;	
				}		
			}
			else{
				$result = "";
			}
		}
		else{
			$result = "";
		}

		return $result;
	}
	
	function dateLastVisit($user_id, $id){
		$db = JFactory::getDBO();
		$sql = "SELECT date_last_visit from #__guru_viewed_lesson WHERE user_id =".intval($user_id)." and pid=".$id;
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;	
	}	
	
	function getlistQuizTakenStudF(){
		$db = JFactory::getDBO();
		$user_id = JFactory::getApplication()->input->get('cid',"", "raw");
		$sql = "SELECT * FROM #__guru_quiz_taken_v3 WHERE user_id=".$user_id." and pid=".$pid;
		$db->setQuery($sql);
		$result=$db->loadObjectList();
		return $result;
	}
	function countQuizzTakenF($user_id, $id){
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) from #__guru_quiz_taken_v3 WHERE user_id =".intval($user_id)." and pid=".$id;
		$db->setQuery($sql);
		$result = $db->loadResult();	
		return $result;	
	}
	
	function getlistQuizTakenStud($pid){
		$db = JFactory::getDBO();
		$user_id = JFactory::getApplication()->input->get('cid',"", "raw");
		$sql = "SELECT * FROM #__guru_quiz_taken_v3 WHERE user_id=".$user_id." and pid=".$pid;
		$db->setQuery($sql);
		$result=$db->loadObjectList();
		return $result;
	}
	function getQuizNameF($quiz_id){
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_quiz WHERE id=".$quiz_id;
		$db->setQuery($sql);
		$result=$db->loadResult();		
		return $result;	
	}
	
	function getStudNameF($user_id){
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__users WHERE id=".$user_id;
		$db->setQuery($sql);
		$result=$db->loadResult();		
		return $result;	
	}
	function getCourseNameF($pid){
		$database = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_program WHERE id=".$pid;
		$database->setQuery($sql);
		$course_name = $database->loadResult();
		return $course_name;
	}
	function getScoreQuizF($quiz_id,$user_id,$id){
		$db = JFactory::getDBO();
		$sql = "SELECT 	score_quiz FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and id=".intval($id);
		$db->setQuery($sql);
		$result=$db->loadResult();		
		return $result;		
	}
		function getAnsGivedF($user_id, $id){
		$db = JFactory::getDBO();	
		$sql = "SELECT answers_gived FROM #__guru_quiz_question_taken_v3 qq, #__guru_questions_v3 q  WHERE qq.question_id=q.id and user_id=".intval($user_id)." and show_result_quiz_id=".intval($id)." ORDER BY q.reorder";
		$db->setQuery($sql);
		$result_ansgived = $db->loadObjectList();	
		return $result_ansgived;		
	}
	
	function getAnsGivedFr($user_id, $id, $quiz_id){
		$db = JFactory::getDBO();	

		$sql = "SELECT show_nb_quiz_select_up from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		@$show_nb_quiz_select_up=$db->loadColumn();
		@$show_nb_quiz_select_up = @$show_nb_quiz_select_up[0];
		
		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$isfinal= $db->loadColumn();
		$isfinal = $isfinal[0];
		
		
		if($isfinal == 0 && @$show_nb_quiz_select_up == 1){
			$sql = "SELECT qq.answers_gived FROM #__guru_quiz_question_taken_v3 qq INNER JOIN #__guru_questions_v3 q ON( qq.question_id = q.id) WHERE qq.user_id=".intval($user_id)." and qq.show_result_quiz_id=".intval($id)." order by qq.question_order_no";
		}
		elseif($isfinal == 0 && @$show_nb_quiz_select_up == 0){
			$sql = "SELECT qq.answers_gived FROM #__guru_quiz_question_taken_v3 qq INNER JOIN #__guru_questions_v3 q ON( qq.question_id = q.id) WHERE qq.user_id=".intval($user_id)." and qq.show_result_quiz_id=".intval($id)." order by qq.question_order_no";
		}
		elseif($isfinal == 1 && @$show_nb_quiz_select_up == 0){
			$sql = "SELECT qq.answers_gived FROM #__guru_quiz_question_taken_v3 qq INNER JOIN #__guru_questions_v3 q ON( qq.question_id = q.id) WHERE qq.user_id=".intval($user_id)." and qq.show_result_quiz_id=".intval($id)." order by qq.question_order_no";
		}
		elseif($isfinal == 1 && @$show_nb_quiz_select_up == 1){
			$sql = "SELECT qq.answers_gived FROM #__guru_quiz_question_taken_v3 qq INNER JOIN #__guru_questions_v3 q ON( qq.question_id = q.id) WHERE qq.user_id=".intval($user_id)." and qq.show_result_quiz_id=".intval($id)." order by qq.question_order_no";
		}
		$db->setQuery($sql);
		$result_ansgived = $db->loadObjectList();	
		return $result_ansgived;
	}
	function getAnsRightF($quiz_id){
		$db = JFactory::getDBO();
		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$isfinal=$db->loadResult();
		
		if($isfinal == 0){
			$sql = "SELECT answers FROM #__guru_questions_v3 WHERE qid=".intval($quiz_id). " ORDER BY reorder ";
		}
		else{
			$sql = "SELECT 	quizzes_ids FROM #__guru_quizzes_final WHERE qid=".$quiz_id;
			$db->setQuery($sql);
			$result=$db->loadResult();	
			$result_qids = explode(",",trim($result,","));
		
			if(count($result_qids) == 0 || $result_qids["0"] == ""){
					$result_qids["0"] = 0;
			}
			
			$sql = "SELECT answers FROM #__guru_questions_v3 WHERE qid IN (".implode(",", $result_qids).")  ORDER BY reorder ";
		}
		$db->setQuery($sql);
		$result_ansright = $db->loadObjectList();	
		return $result_ansright;	
	
	}
	function getAnsRightFr($quiz_id, $id ){
		$db = JFactory::getDBO();
		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$isfinal=$db->loadResult();
		@$show_nb_quiz_select_up = "";
		$sql = "SELECT show_nb_quiz_select_up from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		@$show_nb_quiz_select_up=$db->loadResult();
		
		if($isfinal == 0 && @$show_nb_quiz_select_up == 1){
			$sql = "SELECT w1.answers FROM #__guru_questions_v3 w1 INNER JOIN #__guru_quiz_question_taken_v3 w2 ON (w1.id= w2.question_id)  WHERE qid=".intval($quiz_id). " and w2.show_result_quiz_id=".intval($id)." order by question_order_no";
		}
		elseif($isfinal == 0 && @$show_nb_quiz_select_up == 0){
			$sql = "SELECT w1.answers FROM #__guru_questions_v3 w1 INNER JOIN #__guru_quiz_question_taken_v3 w2 ON (w1.id= w2.question_id)  WHERE qid=".intval($quiz_id). " and w2.show_result_quiz_id=".intval($id)." order by question_order_no";
		}
		elseif($isfinal == 1 && @$show_nb_quiz_select_up == 0){
			$sql = "SELECT w1.answers FROM #__guru_questions_v3 w1 INNER JOIN #__guru_quiz_question_taken_v3 w2 ON (w1.id= w2.question_id)  WHERE w2.show_result_quiz_id=".intval($id)." order by question_order_no";
		}
		elseif($isfinal == 1 && @$show_nb_quiz_select_up == 1){
			$sql = "SELECT w1.answers FROM #__guru_questions_v3 w1 INNER JOIN #__guru_quiz_question_taken_v3 w2 ON (w1.id= w2.question_id)  WHERE w2.show_result_quiz_id=".intval($id)." order by question_order_no";
		}
		$db->setQuery($sql);
		$result_ansright = $db->loadObjectList();	
		return $result_ansright;
	}
	
	
		function getAllAnsF($quiz_id, $id){
		$db = JFactory::getDBO();
		
		$sql = "SELECT show_nb_quiz_select_up from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		@$show_nb_quiz_select_up = $db->loadColumn();
		@$show_nb_quiz_select_up = @$show_nb_quiz_select_up[0];
		
		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$isfinal = $db->loadColumn();
		$isfinal = $isfinal[0];
		
		if($isfinal == 0 && @$show_nb_quiz_select_up == 1){
		 $sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 WHERE show_result_quiz_id=".intval($id)." order by question_order_no";
		 $db->setQuery($sql);
		 $result=$db->loadObjectList();
		}
		elseif($isfinal == 0 && @$show_nb_quiz_select_up == 0){
		 $sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 WHERE show_result_quiz_id=".intval($id)." order by question_order_no";
		 $db->setQuery($sql);
		 $result=$db->loadObjectList();
		}
		elseif($isfinal == 1 && @$show_nb_quiz_select_up == 0){
		 $sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 WHERE show_result_quiz_id=".intval($id)." order by question_order_no";
		 $db->setQuery($sql);
		 $result=$db->loadObjectList();	
		}
		else{
			$sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 qq, #__guru_questions_v3 q  WHERE qq.question_id=q.id and show_result_quiz_id=".$id." ORDER BY reorder"; 
			$db->setQuery($sql);
			$result=$db->loadObjectList();
		}
		//$result_allans = new Array();
		$i = 0;
		foreach($result as $key=>$value){
		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$isfinal = $db->loadColumn();
		
		if($isfinal[0] == 0){
			$sql = "SELECT a1, a2,a3,a4,a5,a6,a7,a8,a9,a10 FROM #__guru_questions_v3 WHERE qid=".intval($quiz_id)." and id=".$value->question_id;
		}
		else{
			$sql = "SELECT a1, a2,a3,a4,a5,a6,a7,a8,a9,a10 FROM #__guru_questions_v3 WHERE id=".$value->question_id;
		}
			$db->setQuery($sql);
			$choices = $db->loadAssocList();
			$correct_ans = "";
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
			$result_allans[$i++] = $correct_ans;
		}
		return @$result_allans;	
	}
	function getAllAnsTextF($quiz_id, $id){
		$db = JFactory::getDBO();
		$sql1 = "SELECT  nb_quiz_select_up, show_nb_quiz_select_up FROM #__guru_quiz WHERE id=".intval($quiz_id);
		$db->setQuery($sql1);
		$result1 = $db->loadAssocList();
		
		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$isfinal=$db->loadResult();
		
		if($isfinal == 0 && $result1[0]["show_nb_quiz_select_up"] == 1){
		 $sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 WHERE show_result_quiz_id=".intval($id)." order by question_order_no";
		 $db->setQuery($sql);
		 $result=$db->loadObjectList();
		}
		elseif($isfinal == 0 && $result1[0]["show_nb_quiz_select_up"] == 0){
		 $sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 WHERE show_result_quiz_id=".intval($id)." order by question_order_no";
		 $db->setQuery($sql);
		 $result=$db->loadObjectList();
		}
		elseif($isfinal == 1 && $result1[0]["show_nb_quiz_select_up"] == 0){
		 $sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 WHERE show_result_quiz_id=".intval($id)." order by question_order_no";
		 $db->setQuery($sql);
		 $result=$db->loadObjectList();	
		}
		elseif(isset($result1[0]["nb_quiz_select_up"]) && $result1[0]["nb_quiz_select_up"] !="" && $result1[0]["show_nb_quiz_select_up"] == 0 ){
			$sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 qq, #__guru_questions_v3 q  WHERE qq.question_id=q.id and show_result_quiz_id=".$id." ORDER BY qq.id DESC LIMIT 0, ".$result1[0]["nb_quiz_select_up"]."";
			$db->setQuery($sql);
			$result=$db->loadObjectList();
		}
		else{
			$sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 qq, #__guru_questions_v3 q  WHERE qq.question_id=q.id and show_result_quiz_id=".$id." ORDER BY question_order LIMIT 0, ".$result1[0]["nb_quiz_select_up"].""; 
			$db->setQuery($sql);
			$result=$db->loadObjectList();
		}
		//$result_allans = new Array();
		$i = 0;
		foreach($result as $key=>$value){
		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$isfinal=$db->loadResult();
		
		if($isfinal == 0){
			$sql = "SELECT a1, a2,a3,a4,a5,a6,a7,a8,a9,a10 FROM #__guru_questions_v3 WHERE qid=".intval($quiz_id)." and id=".$value->question_id;
		}
		else{
			$sql = "SELECT a1, a2,a3,a4,a5,a6,a7,a8,a9,a10 FROM #__guru_questions_v3 WHERE id=".$value->question_id;
		
		}
			$db->setQuery($sql);
			$choices = $db->loadAssocList();
			$correct_ans = "";
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
			$result_allans[$i++] = $correct_ans;

		}
		return @$result_allans;	
	}


	 function getQuestionNameF($id,$quiz_id){
		$db = JFactory::getDBO();
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$questions_ids = $registry->get('questionsids', "");
		
		$questions_ids = explode(",",$questions_ids );

		$sql = "SELECT is_final from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		$isfinal=$db->loadResult();
		
		$sql = "SELECT show_nb_quiz_select_up from #__guru_quiz where id=".$quiz_id;
		$db->setQuery($sql);
		@$show_nb_quiz_select_up=$db->loadResult();
		
		if($isfinal == 0 && @$show_nb_quiz_select_up ==1){
			$sql = "SELECT text FROM #__guru_questions_v3 WHERE qid=".intval($quiz_id)." ORDER BY reorder";
		}
		elseif($isfinal == 0 && @$show_nb_quiz_select_up ==0){
			$sql = "SELECT q1.text, q2.question_order_no  FROM #__guru_questions_v3 q1 INNER JOIN #__guru_quiz_question_taken_v3 q2 ON (q1.id = q2.question_id)  WHERE q1.qid=".intval($quiz_id)." and q2.show_result_quiz_id=".intval($id)." order by q2.question_order_no";
		}
		else{
			$sql = "SELECT q1.text FROM #__guru_questions_v3 q1 INNER JOIN #__guru_quiz_question_taken_v3 q2 ON (q1.id = q2.question_id)  WHERE  q2.show_result_quiz_id=".intval($id)." order by q2.question_order_no";
		}
		
		$db->setQuery($sql);
		$result_question=$db->loadObjectList();
	    return $result_question; 
	}

	function dateCourseRecordTime($user_id, $course_id){
		$db = JFactory::getDbo();
		$return = array("show_time"=>false, "time"=>"00:00:00");

		$sql = "select `certificate_term` from #__guru_program where `id`=".intval($course_id);
		$db->setQuery($sql);
		$certificate_term = $db->loadColumn();
		$certificate_term = @$certificate_term["0"];

		if(intval($certificate_term) == 7){
			$return["show_time"] = true;
		}

		$sql = "select `viewed_time` from #__guru_viewed_lesson where `pid`=".intval($course_id)." and `user_id`=".intval($user_id);
		$db->setQuery($sql);
		$viewed_time = $db->loadColumn();
		$viewed_time = @$viewed_time["0"];

		if(isset($viewed_time) && trim($viewed_time) != ""){
			$return["time"] = trim($viewed_time);
		}

		return $return;
	}
};
?>