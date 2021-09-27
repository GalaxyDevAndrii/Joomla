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

jimport('joomla.application.component.modellist');
jimport('joomla.utilities.date');
use Joomla\Event\Dispatcher;

class guruAdminModelguruOrder extends JModelLegacy {
	var $_orders;
	var $_order;
	var $_id = null;
	var $_total = 0;
	var $total = 0;
	var $_pagination = null;
	protected $_context = 'com_guru.guruOrder';

	function __construct () {
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");
		$this->setId((int)$cids[0]);
		$mainframe =JFactory::getApplication();

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
	
    function getPagination() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getItems();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	function getItems(){
		$startdate = JFactory::getApplication()->input->get("startdate", "", "raw");
		$enddate = JFactory::getApplication()->input->get("enddate", "", "raw");
		$search = JFactory::getApplication()->input->get("search", "", "raw");
		$filter_status = JFactory::getApplication()->input->get("filter_status", "-", "raw");
		$filter_payement = JFactory::getApplication()->input->get("filter_payement", "-", "raw");
		$filter_course = JFactory::getApplication()->input->get("filter_course", "", "raw");
		$filter_teacher = JFactory::getApplication()->input->get("filter_teacher", "", "raw");
		$db = JFactory::getDBO();
		
		$and = "";
		
		if($startdate != ""){
			$startdate = $startdate." 00:00:00";
		}
		
		if($enddate != ""){
			$enddate = date('Y-m-d', strtotime($enddate . '+1 day'))." 00:00:00";
		}
		
		if($startdate != "" && $enddate == ""){
			$and .= " and o.order_date >= '".$startdate."'";
		}
		elseif($startdate == "" && $enddate != ""){
			$and .= " and o.order_date <= '".$enddate."'";
		}
		elseif($startdate != "" && $enddate != ""){
			$and .= " and o.order_date >= '".$startdate."' and o.order_date <= '".$enddate."'";
		}
		
		if($filter_status != "-"){
			$and .= " and o.status='".$filter_status."'";
		}
		
		if($filter_payement != "-"){
			$and .= " and o.processor ='".$filter_payement."'";
		}
		if(intval($filter_teacher) != 0){
			$courses = array("0");
			if(intval($filter_course) == 0){
				$sql = "select id from #__guru_program where (author=".intval($filter_teacher).' OR  author like \'%|'.intval($filter_teacher).'|%\')';
			}
			else{
				$sql = "select id from #__guru_program where (author=".intval($filter_teacher).' OR  author like \'%|'.intval($filter_teacher).'|%\') and id ='.intval($filter_course);
			}
			
			$db->setQuery($sql);
			$courses_ids = $db->loadColumn();
			if(isset($courses_ids) && count($courses_ids) > 0){
				$courses = $courses_ids;
			}
			
			$or = array();
			foreach($courses as $key=>$id){
				$or[] = "o.courses like '".$id."-%' OR o.courses like '%|".$id."-%'";
			}
			$and .= " and (".implode(" OR ", $or).")";
		}
		
		if(trim($search) != ""){
			$sql = "select `id` from #__guru_promos where (`title` like '%".$db->escape(trim($search))."%' OR `code` like '%".$db->escape(trim($search))."%')";
			$db->setQuery($sql);
			$promos_ids = $db->loadColumn();
			$and_promos = "";

			if(isset($promos_ids) && is_array($promos_ids) && count($promos_ids) > 0){
				$and_promos = " OR o.`promocodeid` in (".implode(",", $promos_ids).")";
			}

			$and .= " and (c.firstname like '%".$db->escape(trim($search))."%' or c.lastname like '%".$db->escape(trim($search))."%' or u.username like '".$db->escape(trim($search))."' or u.name like '".$db->escape(trim($search))."' or CONCAT(c.firstname, ' ', c.lastname) like '%".$db->escape(trim($search))."%' ".$and_promos." )";
		}
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$registry->set('search_order', trim($search));
		
		$sql = "SELECT o.*, u.email, u.username, c.firstname, c.lastname 
			FROM `#__guru_order` o 
			LEFT JOIN `#__users` u ON u.id = o.userid 
			LEFT JOIN `#__guru_customer` c ON c.id = u.id 
			WHERE 1 = 1 
			$and
			ORDER BY o.order_date DESC ";
		
		$export = JFactory::getApplication()->input->get("export", "", "raw");
		if($export != ""){
			$this->exportOrders($sql);
		}
		
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
			
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		} else {
			$limit_cond = NULL;
		}
		
		$result = $this->_getList($sql.$limit_cond);
		$this->_total = $this->_getListCount($sql);
		return $result;
	}
	
	function getTotalSum(){
		$startdate = JFactory::getApplication()->input->get("startdate", "", "raw");
		$enddate = JFactory::getApplication()->input->get("enddate", "", "raw");
		$search = JFactory::getApplication()->input->get("search", "", "raw");
		$filter_status = JFactory::getApplication()->input->get("filter_status", "-", "raw");
		$filter_payement = JFactory::getApplication()->input->get("filter_payement", "-", "raw");
		$filter_teacher = JFactory::getApplication()->input->get("filter_teacher", "", "raw");
		$db = JFactory::getDBO();
		$courses = array();
		
		$and = "";
		
		if($startdate != "" && $enddate == ""){
			$and .= " and o.order_date >= '".$startdate."'";
		}
		elseif($startdate == "" && $enddate != ""){
			$and .= " and o.order_date <= '".$enddate."'";
		}
		elseif($startdate != "" && $enddate != ""){
			$and .= " and o.order_date >= '".$startdate."' and o.order_date <= '".$enddate."'";
		}
		
		if($filter_status != "-"){
			$and .= " and o.status='".$filter_status."'";
		}
		
		if($filter_payement != "-"){
			$and .= " and o.processor ='".$filter_payement."'";
		}
		
		if(intval($filter_teacher) != 0){
			$courses = array("0");
			$sql = "select id from #__guru_program where author=".intval($filter_teacher)." OR author like '%|".intval($filter_teacher)."|%'";
			$db->setQuery($sql);
			$courses_ids = $db->loadColumn();
			if(isset($courses_ids) && count($courses_ids) > 0){
				$courses = $courses_ids;
			}
			
			$or = array();
			foreach($courses as $key=>$id){
				$or[] = "o.courses like '".$id."-%' OR o.courses like '%|".$id."-%'";
			}
			$and .= " and (".implode(" OR ", $or).")";
		}
		
		if(trim($search) != ""){			
			$and .= " and (c.firstname like '%".addslashes(trim($search))."%' or c.lastname like '%".addslashes(trim($search))."%' or u.username like '".addslashes(trim($search))."')";
		}
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$registry->set('search_order', trim($search));
		
		$sql = "SELECT o.courses, o.amount_paid, o.currency, o.amount
			FROM `#__guru_order` o
			LEFT JOIN `#__users` u ON u.id = o.userid
			LEFT JOIN `#__guru_customer` c ON c.id = o.userid
			WHERE 1 = 1
			$and";

		$db->setQuery($sql);
		$result = $db->loadAssocList();
		
		$return = 0.00;
		$currency = "USD";
		
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				if(isset($value["courses"]) && trim($value["courses"]) != ""){
					$list = explode("|", $value["courses"]);
					
					if(isset($list) && count($list) > 0){
						$filter_by_teacher = false;
						$is_teacher_course = false;
						
						foreach($list as $nr=>$element){
							$temp = explode("-", $element);
							$course_id = intval($temp["0"]);
							
							if(isset($courses) && count($courses) > 0){// filter about teachers
								$filter_by_teacher = true;
								
								if(in_array($course_id, $courses)){ // if this course is from our filter
									$is_teacher_course = true;
								}
							}
						}
						
						if($filter_by_teacher){
							if($is_teacher_course){
								if($value["amount_paid"] == -1){
									$currency = $value["currency"];
									$return += $value["amount"];
								}
								else{
									$currency = $value["currency"];
									$return += $value["amount_paid"];
								}
							}
						}
						else{
							if($value["amount_paid"] == -1){
								$currency = $value["currency"];
								$return += $value["amount"];
							}
							else{
								$currency = $value["currency"];
								$return += $value["amount_paid"];
							}
						}
						
					}
				}
			}
		}
		
		$character = "GURU_CURRENCY_".$currency;
		return JText::_($character).' '. number_format($return, 2, '.', ',');
	}

	function exportOrders($sql){
		$db = JFactory::getDBO();
		$export = JFactory::getApplication()->input->get("export", "", "raw");
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		$sql = "select `id`, `code` from #__guru_promos";
		$db->setQuery($sql);
		$promos = $db->loadAssocList("id");

		if($export == "csv"){
			$users = $this->getUsers();
			$courses = $this->getAllCourses();
			
			$data = "";
			$header = array("Date", "Price","Email", "Username", "First Name", "Last Name", "Student", "Status", "Course(s)", "Payment Method", "Promo code");
			
			$data .= implode(",", $header);
			$data .= "\n";
			
			if(isset($result) && count($result) > 0){
				foreach($result as $key=>$value){
					$data .= $value["order_date"].",";
					if($value["amount_paid"] == "-1"){
						$data .= $value["amount"].",";
					}
					else{
						$data .= $value["amount_paid"].",";
					}
					
					$data .= $value["email"].",";
					$data .= @$users[$value["userid"]]["username"].",";
					$data .= $value["firstname"].",";
					$data .= $value["lastname"].",";
					$data .= @$users[$value["userid"]]["name"].",";
					$data .= $value["status"].",";
					
					if(isset($value["courses"]) && trim($value["courses"]) != ""){
						$list = explode("|", $value["courses"]);
						if(isset($list) && count($list) > 0){
							$list_courses_name = array();
							foreach($list as $nr=>$element){
								$temp = explode("-", $element);
								if(isset($temp["0"]) && trim($temp["0"]) != ""){
									$list_courses_name[] = @$courses[$temp["0"]]["name"];
								}
							}
							$data .= implode(" | ", $list_courses_name).",";
						}
						else{
							$data .= ",";
						}
					}
					else{
						$data .= ",";
					}
					
					$data .= $value["processor"].",";

					if(isset($promos[$value["promocodeid"]])){
						$data .= $promos[$value["promocodeid"]]["code"]."\n";
					}
					else{
						$data .= ""."\n";
					}
				}
			}

			$csv_filename = "orders.csv";
			header("Content-Type: application/x-msdownload");
			header("Content-Disposition: attachment; filename=".$csv_filename);
			header("Pragma: no-cache");
			header("Expires: 0");
			echo $data;
			exit();
		}
		elseif($export == "pdf"){
			$users = $this->getUsers();
			$courses = $this->getAllCourses();
			$file_content = "<style>
								td{
									border:1px solid #000000;
								}
								table{
									border-collapse: collapse
								}
							 </style>";
			
			$file_content .= '<table>';
			$file_content .= '	<tr>';
			$file_content .= '		<td>Date</td>';
			$file_content .= '		<td>Price</td>';
			$file_content .= '		<td>Email</td>';
			$file_content .= '		<td>Username</td>';
			$file_content .= '		<td>First Name</td>';
			$file_content .= '		<td>Last Name</td>';
			$file_content .= '		<td>Student</td>';
			$file_content .= '		<td>Status</td>';
			$file_content .= '		<td>Course(s)</td>';
			$file_content .= '		<td>Payment Method</td>';
			$file_content .= '		<td>Promo code</td>';
			$file_content .= '	</tr>';
			
			if(isset($result) && count($result) > 0){
				foreach($result as $key=>$value){
					$file_content .= '<tr>';
					
					$file_content .= '<td>'.$value["order_date"].'</td>';
					
					if($value["amount_paid"] == "-1"){
						$file_content .= '<td>'.$value["amount"].'</td>';
					}
					else{
						$file_content .= '<td>'.$value["amount_paid"].'</td>';
					}
					
					$file_content .= '<td>'.$value["email"].'</td>';
					$file_content .= '<td>'.$users[$value["userid"]]["username"].'</td>';
					$file_content .= '<td>'.$value["firstname"].'</td>';
					$file_content .= '<td>'.$value["lastname"].'</td>';
					$file_content .= '<td>'.$users[$value["userid"]]["name"].'</td>';
					$file_content .= '<td>'.$value["status"].'</td>';
					
					if(isset($value["courses"]) && trim($value["courses"]) != ""){
						$list = explode("|", $value["courses"]);
						if(isset($list) && count($list) > 0){
							$list_courses_name = array();
							foreach($list as $nr=>$element){
								$temp = explode("-", $element);
								if(isset($temp["0"]) && trim($temp["0"]) != ""){
									$list_courses_name[] = $courses[$temp["0"]]["name"];
								}
							}
							$file_content .= '<td>'.implode("</br>", $list_courses_name).'</td>';
						}
						else{
							$file_content .= '<td></td>';
						}
					}
					else{
						$file_content .= '<td></td>';
					}
					
					$file_content .= '<td>'.$value["processor"].'</td>';

					if(isset($promos[$value["promocodeid"]])){
						$file_content .= '<td>'.$promos[$value["promocodeid"]]["code"].'</td>';
					}
					else{
						$file_content .= '<td></td>';
					}

					$file_content .= '</tr>';
				}
			}
			
			$file_content .= '</table>';

			error_reporting(0);
			ini_set('display_errors','Off');
			
			require_once JPATH_ROOT . "/components/com_guru/helpers/MPDF/mpdf.php";
			$pdf = new mPDF('utf-8','A4-L');
			//set up a page
			$pdf->AddPage('L');
			$pdf->SetXY(100,50);
			$pdf->SetDisplayMode('fullpage');  
			$pdf->WriteHTML($file_content);
			$pdf->Output('orders.pdf','D'); 
			exit;
		}
	}
	
	function getUsers(){
		$db = JFactory::getDBO();
		$sql = "select u.id, u.name, u.username from #__users u, #__guru_customer g where u.id=g.id";
		$db->setQuery($sql);
		$result = $db->loadAssocList("id");
		return $result;
	}
	
	function getAllCourses(){
		$db = JFactory::getDBO();
		$sql = "select id, name from #__guru_program";
		$db->setQuery($sql);
		$result = $db->loadAssocList("id");
		return $result;
	}

	function setId($id) {
		$this->_id = $id;

		$this->_order = null;
	}
	
	public static function getAllPromos(){
		$db = JFactory::getDBO();
		$jnow = new JDate('now');
		$date = $jnow->toSQL();
		$sql = "select title, code from #__guru_promos where published=1 and codestart <= '".$date."' and (codeend >= '".$date."' or codeend='0000-00-00 00:00:00')";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}

	public static function getJoomlaUser($id){
		$db = JFactory::getDBO();
		$sql = "select name, username, email from #__users where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}

	function getDiscountDetails($promocode){
		$db = JFactory::getDBO();
		$sql = "select discount, typediscount from #__guru_promos where id = '".addslashes(trim($promocode))."'";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function publish(){
		$db = JFactory::getDBO();		
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw"); 
		$task = JFactory::getApplication()->input->get('task', '', "raw");
		$item = $this->getTable('guruTasks');
		if ($task == 'publish'){
			$sql = "update #__guru_order set published='1' where oid in ('".implode("','", $cids)."')";
			$ret = 1;
		} else {
			$sql = "update #__guru_order set published='0' where oid in ('".implode("','", $cids)."')";
			$ret = -1;	
		}
		
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
	
		return $ret;
	}
	
	function getOrder() {
		
		if (empty ($this->_order)) {
			$this->_order = $this->getTable("guruOrder");
			$this->_order->load($this->_id);
		}
		return $this->_order;
	}
	
	function getSigns(){
		$db = JFactory::getDBO();
		$sql="SELECT currency_name AS name, sign FROM #__guru_currencies";
		$db->setQuery($sql);
		$result=$db->loadObjectList();
		return $result;
	}

	function saveCustomer(){
		$db = JFactory::getDBO();
		$user_id = $this->saveJoomlaUser();
		return $user_id;
	}	
	
	function saveJoomlaUser(){
		$db = JFactory::getDBO();
		
		$user_id = JFactory::getApplication()->input->get("userid", "", "raw");
		$password = JFactory::getApplication()->input->get("password", "", "raw");
		$password = $this->encriptPassword($password);
		$name = JFactory::getApplication()->input->get("firstname", "", "raw");
		$username = JFactory::getApplication()->input->get("username", "", "raw");
		$email = JFactory::getApplication()->input->get("email", "", "raw");
		
		$usertype = "Registered";
		$block = "0";
		$sendEmail = "0";
		$jnow = new JDate('now');
		$registerDate = $jnow->toSQL();
		$lastvisitDate = "0000-00-00 00:00:00";
				
		if($user_id == ""){
			$sql = "insert into #__users(name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params) values ('".addslashes(trim($name))."', '".addslashes(trim($username))."', '".addslashes(trim($email))."', '".$password."', 0, 0, '".$registerDate."', '".$lastvisitDate."', '', '')";
			$db->setQuery($sql);
			
			if($db->execute()){
				$sql = "select id from #__users where name='".addslashes(trim($name))."' and username='".addslashes(trim($username))."' and email='".addslashes(trim($email))."'";
				$db->setQuery($sql);
				$user_id = $db->loadResult();
			}			
			if($user_id != ""){
				$query = "select id from #__usergroups where title='Registered'";
				$db->setQuery($query);
				$group_id = $db->loadResult();

				if(intval($group_id) == 0){
					$group_id = 2;
				}
				
				$query = "insert into #__user_usergroup_map(user_id, group_id) values('".$user_id."', '".$group_id."')";
				$db->setQuery($query);
				$group_id = $db->loadResult();			
			}
		}
		
		$company = JFactory::getApplication()->input->get("company", "", "raw");
		$firstname = JFactory::getApplication()->input->get("firstname", "", "raw");
		$lastname = JFactory::getApplication()->input->get("lastname", "", "raw");
		
		$sql = "insert into #__guru_customer(id, company, firstname, lastname) values (".$user_id.", '".addslashes(trim($company))."', '".addslashes(trim($firstname))."', '".addslashes(trim($lastname))."')";		
		$db->setquery($sql);
		$db->execute();
		
		$return = $user_id;
		return $return;
	}
	
	function encriptPassword($password){
		$salt = "";
		for($i=0; $i<=32; $i++) {
			$d = rand(1,30)%2;
		  	$salt .= $d ? chr(rand(65,90)) : chr(rand(48,57));
	   	}
		$hashed = md5($password.$salt);
		$encrypted = $hashed.':'.$salt;
		return $encrypted;
	}

	public static function getPlanExpiration(){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_subplan";
		$db->setQuery($sql);
		$result = $db->loadAssocList("id");
		return $result;
	}

	function courseIsAlreadyBuy($course_id, $user_id){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_buy_courses where userid=".intval($user_id)." and course_id=".intval($course_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result == "0"){
			return false;
		}
		return true;
	}
	
	function getCurrentDate($today_date, $course_id, $user_id){
		$db = JFactory::getDBO();
		$sql = "select expired_date from #__guru_buy_courses where course_id=".intval($course_id)." and userid=".intval($user_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result != NULL && trim($result) != ""){
			$date_int = strtotime(trim($result));
			if($today_date > $date_int){
				return $today_date;
			}
			elseif($today_date <= $date_int){
				return $date_int;
			}
		}
		else{
			return $today_date;
		}
	}

	function saveOrder(){		
		$db = JFactory::getDBO();
		$jnow = new JDate('now');
		$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
		$jnow->setTimezone($timezone);
		$datetime = $jnow->toSQL(true);
		$input = JFactory::getApplication()->input;
		$user_id = $input->get("userid", "0", "raw");
		$course_id = $input->get("course_id", "", "raw");
		$hidden_plan = $input->get('hidden_plan', array(), 'array');
		$all_price = $input->get("hidden_licenses", array(), "raw");
		$subscr_type_select = $input->get("subscr_type_select", array(), "raw");
		$amount_paid = $input->get("amount_paid", "", "raw") == "" ? "-1":$input->get("amount_paid", "", "raw");
		$courses = "";
		$all_plans = $this->getPlanExpiration();
		$courses_list_expiration = array();
		$courses_list_plans = array();
		$courses_list_price = array();
		
		$sql = "select currency from #__guru_config where id=1";
		$db->setQuery($sql);
		$currency = $db->loadResult();
			
		if(isset($course_id) && is_array($course_id) && count($course_id) > 0){
			$course_price = array();
			foreach($course_id as $key=>$value){
				$price = trim($all_price[$key]);
				$plan = $hidden_plan[$key];
				$sql = "";
				if($subscr_type_select[$key] == "renewal"){
					$sql = "select p.plan_id from #__guru_program_renewals p where p.product_id = ".$value." and p.plan_id = '".trim($plan)."'";
				}
				else{
					$sql = "select p.plan_id from #__guru_program_plans p where p.product_id = ".$value." and p.plan_id = '".trim($plan)."'";
				}
				$db->setQuery($sql);
				$plan_id = intval($db->loadResult());
				
				if(intval($plan_id) == "0"){
					$sql = "select p.plan_id from #__guru_program_plans p where p.product_id = ".$value." and price like '".trim($price)."'";
					$db->setQuery($sql);
					$plan_id = intval($db->loadResult());
				}
				
				$course_price[] = $value."-".$price."-".$plan_id;
				$courses_list_plans[$value] = $plan_id;
				$courses_list_price[$value] = $price;
				//-------------set list with expiration date
				$order_expiration = "";
				$order_date_int = $this->getCurrentDate(strtotime($datetime), $value, $user_id);
				
				if($all_plans[$plan_id]["period"] == "hours" && $all_plans[$plan_id]["term"] != "0"){
					$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." hours", $order_date_int);
					$courses_list_expiration[$value] = date('Y-m-d H:i:s', $order_expiration);
				}
				elseif($all_plans[$plan_id]["period"] == "months" && $all_plans[$plan_id]["term"] != "0"){
					$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." month", $order_date_int);
					$courses_list_expiration[$value] = date('Y-m-d H:i:s', $order_expiration);
				}
				elseif($all_plans[$plan_id]["period"] == "years" && $all_plans[$plan_id]["term"] != "0"){
					$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." year", $order_date_int);
					$courses_list_expiration[$value] = date('Y-m-d H:i:s', $order_expiration);
				}
				elseif($all_plans[$plan_id]["period"] == "days" && $all_plans[$plan_id]["term"] != "0"){
					$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." days", $order_date_int);
					$courses_list_expiration[$value] = date('Y-m-d H:i:s', $order_expiration);
				}
				elseif($all_plans[$plan_id]["period"] == "weeks" && $all_plans[$plan_id]["term"] != "0"){
					$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." weeks", $order_date_int);
					$courses_list_expiration[$value] = date('Y-m-d H:i:s', $order_expiration);
				}
				else{
					$courses_list_expiration[$value] = "0000-00-00 00:00:00";	
				}
				//-------------set list with expiration date
			}
			$courses = implode("|", $course_price);
		}
		$amount = JFactory::getApplication()->input->get("total", "", "raw");
		$processor = JFactory::getApplication()->input->get("processor", "", "raw");
		$number_of_licenses = JFactory::getApplication()->input->get("nr_licenses", "", "raw");
		$promocode_code = JFactory::getApplication()->input->get("promocode", "", "raw");
		$promocodeid = "0";
		
		if($promocode_code != "none"){		
			$sql = "select id from #__guru_promos where code = '".addslashes(trim($promocode_code))."'";
			$db->setQuery($sql);
			$result = $db->loadResult();
			$promocodeid = intval($result);
		}		
		
		$sql = "insert into #__guru_order (userid, order_date, courses, status, amount, amount_paid, processor, number_of_licenses, currency, promocodeid, published, form) values (".$user_id.", '".$datetime."', '".$courses."', 'Paid', ".trim($amount).", ".$amount_paid.", '".addslashes(trim($processor))."', ".intval($number_of_licenses).", '".$currency."', '".addslashes(trim($promocodeid))."', 0, '')";
		$db->setQuery($sql);
		if($db->execute()){
			$sql = "select max(id) from #__guru_order";
			$db->setQuery($sql);
			$order_id = $db->loadResult();
			if(isset($courses_list_expiration) && count($courses_list_expiration) > 0){
				foreach($courses_list_expiration as $course_id=>$date_expiration){
					$sql = "";
					if($this->courseIsAlreadyBuy($course_id, $user_id)){
						$sql = "update #__guru_buy_courses set expired_date = '".$date_expiration."', plan_id = '".$courses_list_plans[$course_id]."', email_send=0, order_id=".intval($order_id)."  where userid=".intval($user_id)." and course_id=".intval($course_id);
					}
					else{
						$sql  = "insert into #__guru_buy_courses(userid, order_id, course_id, price, buy_date, expired_date, plan_id, email_send) values ";
						$sql .= "(".intval($user_id).", ".intval($order_id).", ".intval($course_id).", '".$courses_list_price[$course_id]."', '".$datetime."', '".$date_expiration."', '".$courses_list_plans[$course_id]."', 0)";
 					}
					$db->setQuery($sql);
					$db->execute();
					
					// start teacher commission calculation 
					$sql = "select author from #__guru_program where id=".intval($course_id);
					$db->setQuery($sql);
					$authors = $db->loadResult();
					$authors = explode("|", $authors);
					$authors = array_filter($authors);
					
					if(isset($authors) && count($authors) > 0){
						foreach($authors as $key=>$author){
							$sql = "select commission_id from #__guru_authors where userid=".intval($author);
							$db->setQuery($sql);
							$commission_id = $db->loadResult();
							
							$sql = "select teacher_earnings from #__guru_commissions where id=".intval($commission_id);
							$db->setQuery($sql);
							$teacher_earnings = $db->loadResult(); 
							
							if($promocodeid !=0){
								$price = $this->getPromoDiscountCourse($courses_list_price[$course_id],$promocodeid);
							}
							else{
								$price = $courses_list_price[$course_id];
							}
							
							//split_commissions
							$amount_paid_author = (($teacher_earnings * $price)/100) / count($authors);
							
							$sql = "INSERT INTO #__guru_authors_commissions (author_id, course_id, plan_id, order_id, customer_id, commission_id, price, price_paid, amount_paid_author, promocode_id, status_payment, payment_method, data, currency, history) VALUES('".intval($author)."', ".intval($course_id).", ".$courses_list_plans[$course_id].", '".intval($order_id)."', '".intval($user_id)."', '".$commission_id."', '".$courses_list_price[$course_id]."', '".$price."', '".$amount_paid_author."', '".$promocodeid."', 'pending', '".$processor."', '".$datetime."','".$currency."', 0)";
							$db->setQuery($sql);
							$db->execute();
						}
					}
					// end teacher commission calculation 
				}
			}
		}
		
		$sql = "select codeused from #__guru_promos where id=".intval($promocodeid);
		$db->setQuery($sql);
		$result = $db->loadResult();
		
		if(isset($result)){
			$new_val = 	intval($result) + 1;
		}
		else{
			$new_val = 	intval($result) + 0;
		}

		$sql = "update #__guru_promos set codeused=".$new_val." where id=".intval($promocodeid);
		$db->setQuery($sql);
		$db->execute();
		$this->sendEmailOnPurcaseAdmin($course_id, $order_id ,$order_expiration, $plan_id);
		
		//-------------------
		JEventDispatcher::getInstance()->trigger('onAfterGuruCourseBuy', array("customer_id"=>$user_id, "order_id"=>$order_id));
		//-------------------

		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$registry->set('orderok', "1");
		
		return true;
	}
	
	function getEmailProgramReminders(){
		$course_id = JFactory::getApplication()->input->get("course_id", "", "raw");
		$db = JFactory::getDBO();
		$sql = "select 1 from #__guru_program_reminders re, #__guru_subremind se where re.product_id =".intval($course_id)." and re.emailreminder_id = se.id and se.term=11"; 
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result != "0"){
			return true;
		}
		else{
			return false;
		}
	}
	
	function sendEmailOnPurcaseAdmin($course_id, $order_id ,$order_expiration, $plan_id){
		if($this->getEmailProgramReminders()){
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cronjobs.php');
			$order_expiration = date("Y-m-d H:i:s", $order_expiration);
			sendEmailOnPurcase($course_id, $order_id, $order_expiration, $plan_id);
		}
	}
	
	function cycleStatus(){
		$id = JFactory::getApplication()->input->get("cid", "", "raw");
		
		if(isset($id) && is_array($id) && count($id) > 0){
			$id = $id["0"];
			$db = JFactory::getDBO();
			$config = JFactory::getConfig();
			
			$sql = "select status from #__guru_order where id=".intval($id);
			$db->setQuery($sql);
			$actual = $db->loadResult();
			
			$jnow = new JDate('now');
			$order_date = $jnow->toSQL();
			
			$sql = "select currency from #__guru_config where id=1";
			$db->setQuery($sql);
			$currency = $db->loadResult();
			
			if(isset($actual) && trim($actual) != "" && trim($actual) == "Paid"){				
				$sql = "update #__guru_order set status = 'Pending' where id=".intval($id);
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "DELETE FROM #__guru_authors_commissions where order_id =".intval($id);
				$db->setQuery($sql);
				$db->execute();
				
				return "1";
			}
			elseif(isset($actual) && trim($actual) != "" && trim($actual) == "Pending"){
				$sql = "select courses, userid from #__guru_order where id=".intval($id);
				$result = $db->setQuery($sql)->loadObject();
				if (!$result) {
					return;
				}

				$courses = $result->courses;
				$customer_id = $result->userid;

				$all_courses = explode("|", $courses);

				if(isset($all_courses) && count($all_courses) > 0){
					foreach($all_courses as $key => $value){
						$temp1 = explode("-", $value);
						$course_id = intval($temp1["0"]);
						$plan_id = intval($temp1["2"]);

						$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
						$jnow = new JDate('now');
						$jnow->setTimezone($timezone);
						$current_date = $jnow->toSQL(true);
						$current_date_int = strtotime($current_date);
						$order_expiration = "";

						$sql = "select * from #__guru_subplan where `id`=".intval($plan_id);
						$db->setQuery($sql);
						$plan_details = $db->loadAssocList();

						if(isset($plan_details["0"])){
							$plan_details = $plan_details["0"];
						}

						if($plan_details["period"] == "hours" && $plan_details["term"] != "0"){
							$order_expiration = strtotime("+".$plan_details["term"]." hours", $current_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($plan_details["period"] == "months" && $plan_details["term"] != "0"){
							$order_expiration = strtotime("+".$plan_details["term"]." month", $current_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($plan_details["period"] == "years" && $plan_details["term"] != "0"){
							$order_expiration = strtotime("+".$plan_details["term"]." year", $current_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($plan_details["period"] == "days" && $plan_details["term"] != "0"){
							$order_expiration = strtotime("+".$plan_details["term"]." days", $current_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($plan_details["period"] == "weeks" && $plan_details["term"] != "0"){
							$order_expiration = strtotime("+".$plan_details["term"]." weeks", $current_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						else{//for unlimited
							$order_expiration = "0000-00-00 00:00:00";
						}

						$sql = "SELECT COUNT(*) FROM `#__guru_buy_courses` WHERE `userid`=".intval($customer_id)." AND `course_id`=".intval($course_id);
						$count = $db->setQuery($sql)->loadResult();

						if ($count) {
							$sql = "update #__guru_buy_courses set `expired_date` = '".$order_expiration."', `order_id` = '$id' where `userid`=".intval($customer_id)." and `course_id`=".intval($course_id);
							$db->setQuery($sql);
							$db->execute();
						} else {
							$data = new stdClass;
							$data->userid = $customer_id;
							$data->order_id = $id;
							$data->course_id = $course_id;
							$data->buy_date = $current_date;
							$data->expired_date = $order_expiration;
							$data->plan_id = $plan_id;
							$db->insertObject('#__guru_buy_courses', $data);
						}

						// check if need to reset the course for this student
						$sql = "select `reset_on_renew` from #__guru_program where `id`=".intval($course_id);
						$db->setQuery($sql);
						$reset_on_renew = $db->loadResult();

						if(intval($reset_on_renew) == 1){
							$sql = "delete from #__guru_viewed_lesson where `user_id`=".intval($customer_id)." and `pid`=".intval($course_id);
							$db->setQuery($sql);
							$db->execute();

							$sql = "delete from #__guru_quiz_taken_v3 where `user_id`=".intval($customer_id)." and `pid`=".intval($course_id);
							$db->setQuery($sql);
							$db->execute();

							$sql = "delete from #__guru_quiz_question_taken_v3 where `user_id`=".intval($customer_id)." and `pid`=".intval($course_id);
							$db->setQuery($sql);
							$db->execute();
						}
					}
				}
		
				$sql = "update #__guru_order set status = 'Paid' where id=".intval($id);
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "select count(id) from #__guru_authors_commissions where order_id=".intval($id);
				$db->setQuery($sql);
				$count = $db->loadResult();
				
				$sql = "select processor from #__guru_order where id=".intval($id);
				$db->setQuery($sql);
				$processor_payment = $db->loadResult();
				
				if($count <= 0 ){
					$sql = "select courses from #__guru_order where id=".intval($id);
					$db->setQuery($sql);
					$courses = $db->loadResult();
						
					if(isset($courses) && trim($courses) != ""){
						$all_courses = explode("|", $courses);
						if(isset($all_courses) && is_array($all_courses) && count($all_courses) > 0){
							foreach($all_courses as $key => $value){
								$temp1 = explode("-", $value);
								$course_id = $temp1["0"];
								$plan_id = $temp1["2"];
								$allpricecourse = $temp1["1"];
								
								// start teacher commission calculation 
								$sql = "select author from #__guru_program where id=".intval($course_id);
								$db->setQuery($sql);
								$authors = $db->loadResult();
								
								$authors = explode("|", $authors);
								$authors = array_filter($authors);
								
								if(is_array($authors) && count($authors) > 0){
									foreach($authors as $key=>$author){
										if(intval($author) == 0){
											continue;
										}
										
										$sql = "select commission_id from #__guru_authors where userid=".intval($author);
										$db->setQuery($sql);
										$commission_id = $db->loadResult();
										
										$sql = "select teacher_earnings from #__guru_commissions where id=".intval($commission_id);
										$db->setQuery($sql);
										$teacher_earnings = $db->loadResult(); 
										
										$sql = "select userid from #__guru_order where id=".intval($id);
										$db->setQuery($sql);
										$userid = $db->loadResult();
										
										$sql = "select promocodeid from #__guru_order where id=".intval($id);
										$db->setQuery($sql);
										$promocodeid = $db->loadResult(); 
										if($promocodeid !=0){
											$price = $this->getPromoDiscountCourse($allpricecourse,$promocodeid);
										}
										else{
											$price = $allpricecourse;
										}
										
										//split_commissions
										$amount_paid_author = (($teacher_earnings * $price)/100) / count($authors);
										
										$sql = "INSERT INTO #__guru_authors_commissions (author_id, course_id, plan_id, order_id, customer_id, commission_id, price, price_paid, amount_paid_author, promocode_id, status_payment, payment_method, data, currency, history) VALUES('".intval($author)."', ".intval($course_id).", ".$plan_id.", '".intval($id)."', '".intval($userid)."', '".$commission_id."', '".$allpricecourse."', '".$price."', '".$amount_paid_author."', '".$promocodeid."', 'pending', '".$processor_payment."', '".$order_date."','".$currency."', '0')";
										$db->setQuery($sql);
										$db->execute();
									}
								}
								// end teacher commission calculation
							}	
						}
					}
				}		
				
				$sql = "select userid, courses from #__guru_order where id=".intval($id);
				$db->setQuery($sql);
				$order_detailss = $db->loadAssocList();
				
				$sql = "SELECT email from #__users where id=".intval($order_detailss["0"]["userid"]);
				$db->setQuery($sql);
				$email = $db->loadResult();
				$email_list = array($email);
				
				$sql = "SELECT firstname from #__guru_customer where id=".intval($order_detailss["0"]["userid"]);
				$db->setQuery($sql);
				$firstname = $db->loadResult();
				
				$sql = "select template_emails from #__guru_config";
				$db->setQuery($sql);
				$configs = $db->loadAssocList();
				$template_emails = $configs["0"]["template_emails"];
				$template_emails = json_decode($template_emails, true);
				
				$subject_procesed = $template_emails["approve_order_subject"];
				$body_procesed = $template_emails["approve_order_body"];
				$app = JFactory::getApplication();
				$site_name = $app->getCfg('sitename');
				$site_name = "<a target='_blank' href='".JURI::root()."'>".$site_name."</a>";
				$body_procesed = str_replace("[SITE_NAME]", $site_name, $body_procesed);
				$body_procesed = str_replace("[STUDENT_FIRST_NAME]", $firstname, $body_procesed);
				
				$sql= "Select courses from #__guru_order WHERE id=".intval($id)." and userid=".intval($order_detailss["0"]["userid"])."";
				$db->setQuery($sql);
				$courselist = $db->loadResult();
				$idss = array();
				
				if($courselist){
					$temp1 = explode("|", trim($courselist["0"]));
					if(is_array($temp1) && count($temp1) > 0){
						foreach($temp1 as $key=>$value){
							$temp2 = explode("-", $value);
							$idss[] = trim($temp2["0"]);
						}
					}
				}
		
				$list_of_coursesids = implode(",", $idss);	
								
				$sql = "Select name from #__guru_program where id in (".$list_of_coursesids.")";
				$db->setQuery($sql);
				$coursename = $db->loadColumn();
				$coursename = implode(", ", $coursename);
				$coursename = "<a target='_blank' href='".JURI::root()."index.php?option=com_guru&view=guruorders&layout=mycourses'>".$coursename."</a>";
				$body_procesed = str_replace("[COURSE_NAME]", "''".$coursename."''", $body_procesed);
				$from = $config->get("mailfrom");
				$fromname = $config->get("fromname");

				$send_student_email_order_approved = isset($template_emails["send_student_email_order_approved"]) ? $template_emails["send_student_email_order_approved"] : 1;

				if($send_student_email_order_approved){
					JFactory::getMailer()->sendMail($from, $fromname, $email_list, $subject_procesed, $body_procesed, 1);
				}

				$this->addStudentToMailchimp($idss, $order_detailss);
				$this->addStudentToJsGroups($idss, $order_detailss);

				$sql = "insert into #__guru_logs (`userid`, `productid`, `emailname`, `emailid`, `to`, `subject`, `body`, `buy_date`, `send_date`, `buy_type`) values ('".intval($order_detailss["0"]["userid"])."', ".intval($course_id).", '', '0', '".trim($email)."', '".addslashes(trim($subject_procesed))."', '".addslashes(trim($body_procesed))."', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', 'approved-order')";
				$db->setQuery($sql);
				$db->execute();
				
				return "2";
			}
		}
	}
	function getPromoDiscountCourse($total, $promo_id){
		$old_total = $total;
		$value_to_display = "";
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_promos where id='".intval($promo_id)."'";
		$db->setQuery($sql);
		$promo_details = $db->loadObject();			
		
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
	
	function approveOrder(){
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		
		if(isset($cids) && count($cids) > 0){
			$db = JFactory::getDBO();
			$config = JFactory::getConfig();
			$jnow = new JDate('now');
			$order_date = $jnow->toSQL();
			$sql = "select currency from #__guru_config where id=1";
			$db->setQuery($sql);
			$currency = $db->loadResult();
			
			foreach($cids as $key=>$id){
				// start change expiration date if processor is offline
				$sql = "select `processor` from #__guru_order where `id`=".intval($id);
				$db->setQuery($sql);
				$offline_processor = $db->loadResult();

				if($offline_processor == "offline"){
					$sql = "select courses from #__guru_order where id=".intval($id);
					$db->setQuery($sql);
					$courses = $db->loadResult();

					$sql = "select userid from #__guru_order where id=".intval($id);
					$db->setQuery($sql);
					$customer_id = $db->loadResult();

					$all_courses = explode("|", $courses);

					if(isset($all_courses) && count($all_courses) > 0){
						foreach($all_courses as $key_course => $value){
							$temp1 = explode("-", $value);
							$course_id = intval($temp1["0"]);
							$plan_id = intval($temp1["2"]);

							$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
							$jnow = new JDate('now');
							$jnow->setTimezone($timezone);
							$current_date = $jnow->toSQL(true);
							$current_date_int = strtotime($current_date);
							$order_expiration = "";

							$sql = "select * from #__guru_subplan where `id`=".intval($plan_id);
							$db->setQuery($sql);
							$plan_details = $db->loadAssocList();

							if(isset($plan_details["0"])){
								$plan_details = $plan_details["0"];
							}

							if($plan_details["period"] == "hours" && $plan_details["term"] != "0"){
								$order_expiration = strtotime("+".$plan_details["term"]." hours", $current_date_int);
								$order_expiration = date('Y-m-d H:i:s', $order_expiration);
							}
							elseif($plan_details["period"] == "months" && $plan_details["term"] != "0"){
								$order_expiration = strtotime("+".$plan_details["term"]." month", $current_date_int);
								$order_expiration = date('Y-m-d H:i:s', $order_expiration);
							}
							elseif($plan_details["period"] == "years" && $plan_details["term"] != "0"){
								$order_expiration = strtotime("+".$plan_details["term"]." year", $current_date_int);
								$order_expiration = date('Y-m-d H:i:s', $order_expiration);
							}
							elseif($plan_details["period"] == "days" && $plan_details["term"] != "0"){
								$order_expiration = strtotime("+".$plan_details["term"]." days", $current_date_int);
								$order_expiration = date('Y-m-d H:i:s', $order_expiration);
							}
							elseif($plan_details["period"] == "weeks" && $plan_details["term"] != "0"){
								$order_expiration = strtotime("+".$plan_details["term"]." weeks", $current_date_int);
								$order_expiration = date('Y-m-d H:i:s', $order_expiration);
							}
							else{//for unlimited
								$order_expiration = "0000-00-00 00:00:00";
							}

							$sql = "update #__guru_buy_courses set `expired_date` = '".$order_expiration."' where `order_id`=".intval($id)." and `course_id`=".intval($course_id);
							$db->setQuery($sql);
							$db->execute();

							// check if need to reset the course for this student
							$sql = "select `reset_on_renew` from #__guru_program where `id`=".intval($course_id);
							$db->setQuery($sql);
							$reset_on_renew = $db->loadResult();

							if(intval($reset_on_renew) == 1){
								$sql = "delete from #__guru_viewed_lesson where `user_id`=".intval($customer_id)." and `pid`=".intval($course_id);
								$db->setQuery($sql);
								$db->execute();

								$sql = "delete from #__guru_quiz_taken_v3 where `user_id`=".intval($customer_id)." and `pid`=".intval($course_id);
								$db->setQuery($sql);
								$db->execute();

								$sql = "delete from #__guru_quiz_question_taken_v3 where `user_id`=".intval($customer_id)." and `pid`=".intval($course_id);
								$db->setQuery($sql);
								$db->execute();
							}
						}
					}
				}
				// stop change expiration date if processor is offline

				$sql = "select status from #__guru_order where id=".intval($id);
				$db->setQuery($sql);
				$actual = $db->loadResult();
				
				$sql = "select promocodeid from #__guru_order where id=".intval($id);
				$db->setQuery($sql);
				$promocodeid = $db->loadResult();
				
				$sql = "select processor from #__guru_order where id=".intval($id);
				$db->setQuery($sql);
				$processor_payment = $db->loadResult();
				
				if(isset($actual) && trim($actual) != "" && trim($actual) == "Pending"){
					$sql = "update #__guru_order set status = 'Paid' where id=".intval($id);
					$db->setQuery($sql);
					$db->execute();
					$sql = "select count(id) from #__guru_authors_commissions where order_id=".intval($id);
					$db->setQuery($sql);
					$count = $db->loadResult();
					
					if($count <= 0){
						$sql = "select courses from #__guru_order where id=".intval($id);
						$db->setQuery($sql);
						$courses = $db->loadResult();
						
						if(isset($courses) && trim($courses) != ""){
							$all_courses = explode("|", $courses);
							if(isset($all_courses) && is_array($all_courses) && count($all_courses) > 0){
								foreach($all_courses as $key => $value){
									$temp1 = explode("-", $value);
									$course_id = $temp1["0"];
									$plan_id = $temp1["2"];
									$allpricecourse = $temp1["1"];
									
									// start teacher commission calculation 
									$sql = "select author from #__guru_program where id=".intval($course_id);
									$db->setQuery($sql);
									$authors = $db->loadAssocList();
									$authors = $authors["0"]["author"];
									
									$authors = explode("|", $authors);
									$authors = array_filter($authors);
									
									if(is_array($authors) && count($authors) > 0){
										foreach($authors as $key=>$author){
											if(intval($author) == 0){
												continue;
											}
											
											$sql = "select commission_id from #__guru_authors where userid=".intval($author);
											$db->setQuery($sql);
											$commission_id = $db->loadResult();
											
											$sql = "select teacher_earnings from #__guru_commissions where id=".intval($commission_id);
											$db->setQuery($sql);
											$teacher_earnings = $db->loadResult(); 
											
											$sql = "select userid from #__guru_order where id=".intval($id);
											$db->setQuery($sql);
											$userid = $db->loadResult(); 
											
											$sql = "select promocodeid from #__guru_order where id=".intval($id);
											$db->setQuery($sql);
											$promocodeid = $db->loadResult(); 
			
											if($promocodeid !=0){
												$price = $this->getPromoDiscountCourse($allpricecourse,$promocodeid);
											}
											else{
												$price = $allpricecourse;
											}							
																				
											$amount_paid_author = (($teacher_earnings * $price)/100) / count($authors);
											
											$sql = "INSERT INTO #__guru_authors_commissions (author_id, course_id, plan_id, order_id, customer_id, commission_id, price, price_paid, amount_paid_author, promocode_id, status_payment, payment_method, data, currency) VALUES('".intval($author)."', ".intval($course_id).", ".$plan_id.", '".intval($id)."', '".intval($userid)."', '".$commission_id."', '".$allpricecourse."', '".$price."', '".$amount_paid_author."', '".$promocodeid."', 'pending', '".$processor_payment."', '".$order_date."', '".$currency."' )";
											$db->setQuery($sql);
											$db->execute();
										}
									}
									// end teacher commission calculation
								}	
							}
						}	
					}
					
					$sql = "select userid, courses from #__guru_order where id=".intval($id);
					$db->setQuery($sql);
					$order_detailss = $db->loadAssocList();
					
					$sql = "SELECT email from #__users where id=".intval($order_detailss["0"]["userid"]);
					$db->setQuery($sql);
					$email = $db->loadColumn();
					$email = $email["0"];
					$email_list = array("0"=>$email);
					
					$sql = "SELECT firstname from #__guru_customer where id=".intval($order_detailss["0"]["userid"]);
					$db->setQuery($sql);
					$firstname = $db->loadColumn();
					$firstname = $firstname["0"];
					
					$sql = "select template_emails from #__guru_config";
					$db->setQuery($sql);
					$configs = $db->loadAssocList();
					$template_emails = $configs["0"]["template_emails"];
					$template_emails = json_decode($template_emails, true);
					
					$subject_procesed = $template_emails["approve_order_subject"];
					$body_procesed = $template_emails["approve_order_body"];
					$app = JFactory::getApplication();
					$site_name = $app->getCfg('sitename');

					$site_name = "<a target='_blank' href='".JURI::root()."'>".$site_name."</a>";
					$body_procesed = str_replace("[SITE_NAME]", $site_name, $body_procesed);
					$body_procesed = str_replace("[STUDENT_FIRST_NAME]", $firstname, $body_procesed);
					
					$sql= "Select courses from #__guru_order WHERE id=".intval($id)." and userid=".intval($order_detailss["0"]["userid"])."";
					$db->setQuery($sql);
					$courselist = $db->loadColumn();
					$idss = array();
					
					if(trim($courselist["0"]) != ""){
						$temp1 = explode("|", trim($courselist["0"]));
						if(is_array($temp1) && count($temp1) > 0){
							foreach($temp1 as $key=>$value){
								$temp2 = explode("-", $value);
								$idss[] = trim($temp2["0"]);
							}
						}
					}

					$list_of_coursesids = implode(",", $idss);	
									
					$sql = "Select name from #__guru_program where id in (".$list_of_coursesids.")";
					$db->setQuery($sql);
					$coursename = $db->loadColumn();
					$coursename = implode(", ", $coursename);
					$coursename = "<a target='_blank' href='".JURI::root()."index.php?option=com_guru&view=guruorders&layout=mycourses'>".$coursename."</a>";
					$body_procesed = str_replace("[COURSE_NAME]", "''".$coursename."''", $body_procesed);
					$from = $config->get("mailfrom");
					$fromname = $config->get("fromname");

					$send_student_email_order_approved = isset($template_emails["send_student_email_order_approved"]) ? $template_emails["send_student_email_order_approved"] : 1;

					if($send_student_email_order_approved){
						JFactory::getMailer()->sendMail($from, $fromname, $email_list, $subject_procesed, $body_procesed, 1);
					}

					$this->addStudentToMailchimp($idss, $order_detailss);
					$this->addStudentToJsGroups($idss, $order_detailss);
					
					$sql = "insert into #__guru_logs (`userid`, `productid`, `emailname`, `emailid`, `to`, `subject`, `body`, `buy_date`, `send_date`, `buy_type`) values ('".intval($order_detailss["0"]["userid"])."', ".intval($course_id).", '', '0', '".trim($email)."', '".addslashes(trim($subject_procesed))."', '".addslashes(trim($body_procesed))."', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', 'approved-order')";
					$db->setQuery($sql);
					$db->execute();
				}
			}	
			return "2";
		}
	}
	function makePending(){
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		if(isset($cids) && count($cids) > 0){
			$db = JFactory::getDBO();
			$config = JFactory::getConfig();
			foreach($cids as $key=>$id){
				$sql = "select status from #__guru_order where id=".intval($id);
				$db->setQuery($sql);
				$actual = $db->loadResult();
				
				if(isset($actual) && trim($actual) != "" && trim($actual) == "Paid"){				
					$sql = "update #__guru_order set status = 'Pending' where id=".intval($id);
					$db->setQuery($sql);
					$db->execute();
					
					$sql = "DELETE FROM #__guru_authors_commissions where order_id =".intval($id);
					$db->setQuery($sql);
					$db->execute();
				}
			}
			return "1";
		}
	}
	
	function getUserId($username){
		$db = JFactory::getDBO();
		$sql = "select id from #__users where username='".addslashes(trim($username))."'";
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	
	function checkUsername(){
		$db = JFactory::getDBO();
		$username = JFactory::getApplication()->input->get("username", "", "raw");
		if(trim($username) != ""){
			$sql = "select count(*) from #__users where username='".addslashes(trim($username))."'";
			$db->setQuery($sql);
			$result = $db->loadResult();
			if($result != "0"){
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

	function getOrderDetails(){
		$db = JFactory::getDBO();
		$user_id = JFactory::getApplication()->input->get("userid", "0", "raw");
		$sql = "select username from #__users where id=".intval($user_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	
	function getOrderFromOrders(){
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("cid", "0", "raw");
		$sql = "select * from #__guru_order where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
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
		$promo = $promo["0"];
		
		return $promo;
	}
	
	function getPromoDiscountCourses($total, $promo_id){
		$old_total = $total;
		$value_to_display = "";
		$promo_details = $this->getPromo($promo_id);
		
		if($promo_details->typediscount == '0') {//use absolute values					
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
			$total = ($promo_details->discount / 100)*$total;
			$difference = $old_total - $total;	
			if($difference < 0){
				$value_to_display =  "0";
			}
			else{
				$discount = $old_total - $difference;
				$value_to_display =  (float)$discount;
			}
		}
		return $value_to_display;
	}

	function delete () {
		$cids = JFactory::getApplication()->input->get('cid', array("0"), "raw");
		
		$db = JFactory::getDBO();
		$course_id = "";
		
		foreach($cids as $cid){
			$sql = "select * FROM #__guru_order WHERE id = ".$cid;
			$db->setQuery($sql);
			$order_details = $db->loadAssocList();
			
			$student_id = @$order_details["0"]["userid"];
			$courses = @$order_details["0"]["courses"];
			$courses = explode("|", $courses);
			
			if(is_array($courses)){
				foreach($courses as $key=>$value){
					$value = explode("-", $value);
					$course_id = $value["0"];
					$plan_id = $value["2"];
					//check if we can delete for all that course and order OR just delete from valability date but not all course
					$sql = "select count(*) FROM #__guru_order where (courses like '".$course_id."-%' OR courses like '%|".$course_id."-%') and userid=".intval($student_id);
					$db->setQuery($sql);
					$count = $db->loadResult();
					
					if($count == "1"){//delete all course
						$sql = "delete from #__guru_buy_courses where userid=".intval($student_id)." and course_id=".intval($course_id);
						$db->setQuery($sql);
						$db->execute();
					}
					else{ //just delete from valability
						$sql = "select bc.expired_date, bc.buy_date, s.term, s.period FROM #__guru_buy_courses bc, #__guru_subplan s where bc.userid=".intval($student_id)." and bc.course_id=".intval($course_id)." and s.id=".intval($plan_id);
						$db->setQuery($sql);
						$result = $db->loadAssocList();
						
						$expiration_string = $result["0"]["expired_date"];
						$buy_string =  $result["0"]["buy_date"];
						$term = $result["0"]["term"];
						$period = $result["0"]["period"];
						$expiration_int = strtotime($expiration_string);
						
						if($period == "hours" && $term != "0"){
							$expiration_string = strtotime("-".$term." hours", $expiration_int);
							$expiration_string = date('Y-m-d H:i:s', $expiration_string);
						}
						elseif($period == "months" && $term != "0"){
							$expiration_string = strtotime("-".$term." month", $expiration_int);
							$expiration_string = date('Y-m-d H:i:s', $expiration_string);
						}
						elseif($period == "years" && $term != "0"){
							$expiration_string = strtotime("-".$term." year", $expiration_int);
							$expiration_string = date('Y-m-d H:i:s', $expiration_string);
						}
						elseif($period == "days" && $term != "0"){
							$expiration_string = strtotime("-".$term." days", $expiration_int);
							$expiration_string = date('Y-m-d H:i:s', $expiration_string);
						}
						elseif($period == "weeks" && $term != "0"){
							$expiration_string = strtotime("-".$term." weeks", $expiration_int);
							$expiration_string = date('Y-m-d H:i:s', $expiration_string);
						}
						elseif($term == "0"){//if date is 0000-00-00 00:00:00 for unlimited
							$expiration_string = $this->getOldDate($course_id, $cid, $buy_string);
						}
					}
				}
				// $sql = "select courses FROM #__guru_order where id = (select max(id) from  #__guru_order where (courses like '".$course_id."-%' OR courses like '%|".$course_id."-%') and id <> ".intval($cid).")";
				// $db->setQuery($sql);
				// $find_courses = $db->loadAssocList();

				/*if(isset($find_courses) && count($find_courses) > 0){
					$find_courses = explode("|", $find_courses["0"]["courses"]);
					foreach($find_courses as $findkey=>$find_value){
						$find_value = explode("-", $find_value);
						$find_plan_id = $find_value["2"];
						$find_course_id = $find_value["0"];
						if($find_course_id == $course_id){
							$sql = "update #__guru_buy_courses set expired_date='".trim($expiration_string)."', plan_id='".$find_plan_id."' where userid=".intval($student_id)." and course_id=".intval($course_id);
							$db->setQuery($sql);
							$db->execute();
							
							//set old order_id
							$sql = "select max(id) from  #__guru_order where (courses like '".$course_id."-%' OR courses like '%|".$course_id."-%') and id <> ".intval($cid)." and userid=".intval($student_id);
							$db->setQuery($sql);
							$db->execute();
							$next_order = $db->loadResult();
							if(isset($next_order)){
								$sql = "update #__guru_buy_courses set order_id=".intval($next_order)." where userid=".intval($student_id)." and course_id=".intval($course_id);
								$db->setQuery($sql);
								$db->execute();
							}
						}
					}
				}*/
			}
			
			$delsql = "DELETE FROM #__guru_order WHERE id = ".$cid;
			$db->setQuery($delsql);
			$db->execute();
			
			$sql = "DELETE FROM #__guru_authors_commissions where order_id =".intval($cid);
			$db->setQuery($sql);
			$db->execute();
		}
		return true;
	}
	
	function getOldDate($course_id, $order_id, $buy_string){
		$db = JFactory::getDBO();
		$sql = "select courses FROM #__guru_order where (courses like '".$course_id."-%' OR courses like '%|".$course_id."-%') and id <> ".intval($order_id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		$return = "";
		
		$buy_int = strtotime($buy_string);
		
		if(isset($result) && count($result) > 0){
			foreach($result as $key => $course){
				$course = explode("|", $course["courses"]);
				foreach($course as $key=>$value){
					if(trim($value) != ""){
						$value = explode("-", $value);
						$plan_id = $value["2"];
						$courseid = $value["0"];
						if($courseid == $course_id){
							$sql = "select s.term, s.period FROM #__guru_subplan s where s.id=".intval($plan_id);
							$db->setQuery($sql);
							$plans = $db->loadAssocList();
							$term = $plans["0"]["term"];
							$period = $plans["0"]["period"];
							if($period == "hours" && $term != "0"){
								$return = strtotime("+".$term." hours", $buy_int);
								$return = date('Y-m-d H:i:s', $return);
								$buy_int = strtotime($return);
							}
							elseif($period == "months" && $term != "0"){
								$return = strtotime("+".$term." month", $buy_int);
								$return = date('Y-m-d H:i:s', $return);
								$buy_int = strtotime($return);
							}
							elseif($period == "years" && $term != "0"){
								$return = strtotime("+".$term." year", $buy_int);
								$return = date('Y-m-d H:i:s', $return);
								$buy_int = strtotime($return);
							}
							elseif($period == "days" && $term != "0"){
								$return = strtotime("+".$term." days", $buy_int);
								$return = date('Y-m-d H:i:s', $return);
								$buy_int = strtotime($return);
							}
							elseif($period == "weeks" && $term != "0"){
								$return = strtotime("+".$term." weeks", $buy_int);
								$return = date('Y-m-d H:i:s', $return);
								$buy_int = strtotime($return);
							}
						}	
					}	
				}
			}	
		}
		return $return;
	}
	
	function remove () {
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$item = $this->getTable('adagencyOrder'); 
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				//$this->setError($item->getErrorMsg());
				return false;

			}
		}

		return true;
	}
	function getProgramPrice($id){
		$db = JFactory::getDBO();
		$sql = "SELECT price FROM #__guru_program WHERE id = ".$id;
		$db->setQuery($sql);
		$price = $db->loadResult();
		return $price;
	}
	
	public static function getConfig(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config WHERE id = '1' ";
		$db->setQuery($sql);
		$price = $db->loadObject();
		return $price;
	}
	
	public static function getCourses($ids){
		$db = JFactory::getDBO();
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
	
	public static function getPlugins(){
		$db = JFactory::getDBO();
		$sql = "select * from #__extensions where folder='gurupayment'";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	function getDateType(){
		$db = JFactory::getDBO();
		$sql = "select datetype from #__guru_config WHERE id = '1'";
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	
	function getAllTeachers(){
		$db = JFactory::getDBO();
		$sql = "select u.id, u.name from #__users u, #__guru_authors a where a.userid=u.id";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}

	function addStudentToJsGroups($courses, $order_details){
		$db = JFactory::getDbo();

		$sql = "select `extension_id` from #__extensions where `type`='component' and `element`='com_community'";
		$db->setQuery($sql);
		$jsid = $db->loadColumn();

		$customer_id = intval($order_details["0"]["userid"]);

		if(isset($jsid["0"]) && intval($jsid["0"]) > 0){
			if(isset($courses) && count($courses) > 0){
				foreach($courses as $key=>$course_details_id){
					$sql = "select `js_groups` from #__guru_program where `id`=".intval($course_details_id);
					$db->setQuery($sql);
					$saved_js_groups = $db->loadColumn();
					$saved_js_groups = @$saved_js_groups["0"];

					if(intval($course_details_id) > 0 && intval($saved_js_groups) > 0){
						$sql = "select `userid`, `groups` from #__community_users where `userid`=".intval($customer_id);
						$db->setQuery($sql);
						$js_user = $db->loadAssocList();
						
						if(isset($js_user["0"]) && intval($js_user["0"]["userid"]) > 0){
							// user already exist
							$user_js_grops = $js_user["0"]["groups"];
							$user_js_grops_array = explode(",", $user_js_grops);
							$add_to_group = false;

							if(count($user_js_grops_array) > 0 && trim($user_js_grops) != ""){
								if(!in_array($saved_js_groups, $user_js_grops_array)){
									$user_js_grops = $user_js_grops.",".$saved_js_groups;
								}
							}
							else{
								$user_js_grops = $saved_js_groups;
							}

							$sql = "update `#__community_users` set `groups`='".$user_js_grops."' where `userid`=".intval($customer_id);
							$db->setQuery($sql);
							$db->execute();

							$sql = "select count(*) from #__community_groups_members where `groupid`=".intval($saved_js_groups)." and `memberid`=".intval($customer_id);
							$db->setQuery($sql);
							$already_assigned = $db->loadColumn();
							$already_assigned = @$already_assigned["0"];

							if(intval($already_assigned) == 0){
								$sql = "insert into #__community_groups_members (`groupid`, `memberid`,	`approved`,	`permissions`) values ('".intval($saved_js_groups)."', '".intval($customer_id)."', '1', '1')";
								$db->setQuery($sql);
								$db->execute();
							}
						}
						else{
							// user not exist
							$sql = "INSERT INTO `#__community_users` (`userid`, `status`, `status_access`, `points`, `posted_on`, `avatar`, `thumb`, `cover`, `invite`, `params`, `view`, `friends`, `groups`, `events`, `friendcount`, `alias`, `latitude`, `longitude`, `profile_id`, `storage`, `watermark_hash`, `search_email`, `default_avatar`, `usergroup`) VALUES (".intval($customer_id).", '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, '{\"notifyEmailSystem\":1,\"privacyPrivateMessage\":10,\"privacyFriendRequest\":10,\"privacyProfileView\":0,\"privacyPhotoView\":0,\"privacyFriendsView\":0,\"privacyGroupsView\":\"\",\"privacyVideoView\":0,\"notifyEmailMessage\":1,\"notifyEmailApps\":1,\"notifyWallComment\":0,\"invitorId\":0,\"notif_groups_notify_admin\":1,\"etype_groups_notify_admin\":1,\"notif_user_profile_delete\":1,\"etype_user_profile_delete\":1,\"notif_system_reports_threshold\":1,\"etype_system_reports_threshold\":1,\"notif_profile_activity_add_comment\":1,\"etype_profile_activity_add_comment\":1,\"notif_profile_activity_reply_comment\":1,\"etype_profile_activity_reply_comment\":1,\"notif_profile_status_update\":1,\"etype_profile_status_update\":1,\"notif_profile_like\":1,\"etype_profile_like\":1,\"notif_profile_stream_like\":1,\"etype_profile_stream_like\":1,\"notif_profle_digest_email\":0,\"etype_profle_digest_email\":1,\"notif_friends_request_connection\":1,\"etype_friends_request_connection\":1,\"notif_friends_create_connection\":1,\"etype_friends_create_connection\":1,\"notif_inbox_create_message\":1,\"etype_inbox_create_message\":1,\"notif_users_tagged\":1,\"etype_users_tagged\":1,\"notif_groups_invite\":1,\"etype_groups_invite\":1,\"notif_groups_discussion_reply\":1,\"etype_groups_discussion_reply\":1,\"notif_groups_wall_create\":1,\"etype_groups_wall_create\":1,\"notif_groups_create_discussion\":1,\"etype_groups_create_discussion\":1,\"notif_groups_create_news\":1,\"etype_groups_create_news\":1,\"notif_groups_create_album\":1,\"etype_groups_create_album\":1,\"notif_groups_create_video\":1,\"etype_groups_create_video\":1,\"notif_groups_create_event\":1,\"etype_groups_create_event\":1,\"notif_groups_sendmail\":1,\"etype_groups_sendmail\":1,\"notif_groups_member_approved\":1,\"etype_groups_member_approved\":1,\"notif_groups_member_join\":1,\"etype_groups_member_join\":1,\"notif_groups_notify_creator\":1,\"etype_groups_notify_creator\":1,\"notif_groups_discussion_newfile\":1,\"etype_groups_discussion_newfile\":1,\"notif_groups_activity_add_comment\":1,\"etype_groups_activity_add_comment\":1,\"notif_events_notify_admin\":1,\"etype_events_notify_admin\":1,\"notif_events_invite\":1,\"etype_events_invite\":1,\"notif_events_invitation_approved\":1,\"etype_events_invitation_approved\":1,\"notif_events_sendmail\":1,\"etype_events_sendmail\":1,\"notif_event_notify_creator\":0,\"etype_event_notify_creator\":0,\"notif_event_join_request\":1,\"etype_event_join_request\":1,\"notif_events_activity_add_comment\":0,\"etype_events_activity_add_comment\":0,\"notif_events_wall_create\":1,\"etype_events_wall_create\":1,\"notif_videos_submit_wall\":1,\"etype_videos_submit_wall\":1,\"notif_videos_reply_wall\":1,\"etype_videos_reply_wall\":1,\"notif_videos_tagging\":1,\"etype_videos_tagging\":1,\"notif_videos_like\":1,\"etype_videos_like\":1,\"notif_videos_convert_success\":1,\"etype_videos_convert_success\":1,\"notif_photos_submit_wall\":1,\"etype_photos_submit_wall\":1,\"notif_photos_reply_wall\":1,\"etype_photos_reply_wall\":1,\"notif_photos_tagging\":1,\"etype_photos_tagging\":1,\"notif_photos_like\":1,\"etype_photos_like\":1,\"notif_system_messaging\":1,\"etype_system_messaging\":1}', 0, '', '".intval($saved_js_groups)."', '', 0, '', 255.000000, 255.000000, 0, 'file', '', 1, 1, NULL);";

							$db->setQuery($sql);
							$db->execute();

							$sql = "insert into #__community_groups_members (`groupid`, `memberid`,	`approved`,	`permissions`) values ('".intval($saved_js_groups)."', '".intval($customer_id)."', '1', '1')";
							$db->setQuery($sql);
							$db->execute();
						}
					}
				}
			}
		}
	}

	function addStudentToMailchimp($courses, $order_details){
		if(isset($courses) && count($courses) > 0){
			$db = JFactory::getDbo();

			$sql = "select `email` from #__users where `id`=".intval($order_details["0"]["userid"]);
			$db->setQuery($sql);
			$email = $db->loadResult();

			$sql = "select `firstname`, `lastname` from #__guru_customer where `id`=".intval($order_details["0"]["userid"]);
			$db->setQuery($sql);
			$customer_details = $db->loadAssocList();
			$first_name = $customer_details["0"]["firstname"];
			$last_name = $customer_details["0"]["lastname"];

			foreach($courses as $key=>$course_id){
				$sql = "select `mailchimp_api`, `mailchimp_list_id`, `mailchimp_auto` from #__guru_program where `id`=".intval($course_id);
				$db->setQuery($sql);
				$mailchimp_details = $db->loadAssocList();

				if(isset($mailchimp_details) && count($mailchimp_details) > 0){
					$mailchimp_api = $mailchimp_details["0"]["mailchimp_api"];
					$mailchimp_list_id = $mailchimp_details["0"]["mailchimp_list_id"];
					$mailchimp_auto = $mailchimp_details["0"]["mailchimp_auto"];
				
					if(trim($mailchimp_api) != "" && trim($mailchimp_list_id) != ""){
						require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."MCAPI2.class.php");

						//-----------------------------------------------------
						$api_key = $mailchimp_api;
						$list_id = $mailchimp_list_id;

						$mc_autoregister = FALSE;

						if(intval($mailchimp_auto) == 0){
							$mc_autoregister = TRUE;
						}

						$Mailchimp = new Mailchimp( $api_key );
						$Mailchimp_Lists = new Mailchimp_Lists( $Mailchimp );

						try
						{
						    $subscriber = $Mailchimp_Lists->subscribe(
						        $list_id,
						        array('email' => $email),      // Specify the e-mail address you want to add to the list.
						        array('FNAME' => $first_name, 'LNAME' => $last_name),   // Set the first name and last name for the new subscriber.
						        'text',    // Specify the e-mail message type: 'html' or 'text'
						        $mc_autoregister,     // Set double opt-in: If this is set to TRUE, the user receives a message to confirm they want to be added to the list.
						        TRUE       // Set update_existing: If this is set to TRUE, existing subscribers are updated in the list. If this is set to FALSE, trying to add an existing subscriber causes an error.
						    );
						} 
						catch (Exception $e) 
						{
						    //echo "Caught exception: " . $e;
						}
						//-----------------------------------------------------
					}
				}
			}
		}
	}
};
