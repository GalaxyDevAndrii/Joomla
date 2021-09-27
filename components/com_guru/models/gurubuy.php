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
use Joomla\Event\Dispatcher;

require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

class guruModelguruBuy extends JModelLegacy {
	var $_plugins;
	var $_plugin;
	var $plugin_instances = array();
	var $_id = null;
	var $allowed_types = array("payment", "encoding");
	var $req_methods = array("getFEData", "getBEData");	
	var $_installpath = ""; 
	var $plugins_loaded = 0;
	
	function __construct () {
		parent::__construct();
		$this->_installpath = JPATH_COMPONENT_ADMINISTRATOR .DIRECTORY_SEPARATOR. "plugins" . DS;
		$this->loadPlugins();
	}
	
	function getUserCourses(){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$sql = "select bc.course_id from #__guru_buy_courses bc, #__guru_order o where bc.userid=".intval($user_id)." and bc.order_id=o.id and o.status='Paid'";
		$db->setQuery($sql);
		$result = $db->loadAssocList("course_id");
		return $result;
	}
	
	function getCourseDetails($course_id){
		$db = JFactory::getDBO();
		$sql = "select p.name from #__guru_program p where p.id=".intval($course_id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		
		if($result == ""){
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$courses_from_cart = $registry->get('courses_from_cart', "");
			$renew_courses_from_cart = $registry->get('renew_courses_from_cart', "");
			
			if(isset($courses_from_cart[$course_id])){
				unset($courses_from_cart[$course_id]);
				$registry->set('courses_from_cart', $courses_from_cart);
			}
			
			if(isset($renew_courses_from_cart[$course_id])){
				unset($renew_courses_from_cart[$course_id]);
				$registry->set('renew_courses_from_cart', $renew_courses_from_cart);
			}
		}
		
		return $result;
	}
	
	function getCoursePlans($course_id, $plan){
		$db = JFactory::getDBO();
		$sql = "";
		$action = JFactory::getApplication()->input->get("action", "", "raw");

		if(trim($plan) == "renew"){
			$action = "renew";
		}

		if($action == ""){
			$sql = "select sp.name, pp.default, pp.plan_id, pp.price from #__guru_program p, #__guru_program_plans pp, #__guru_subplan sp where p.id=".intval($course_id)." and p.id=pp.product_id and pp.plan_id=sp.id order by sp.ordering asc";
		}
		else{
			$sql = "select sp.name, pp.default, pp.plan_id, pp.price from #__guru_program p, #__guru_program_renewals pp, #__guru_subplan sp where p.id=".intval($course_id)." and p.id=pp.product_id and pp.plan_id=sp.id order by sp.ordering asc";
		}
		
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		if(isset($result) && count($result) == 0){
			$sql = "select sp.name, pp.default, pp.plan_id, pp.price from #__guru_program p, #__guru_program_plans pp, #__guru_subplan sp where p.id=".intval($course_id)." and p.id=pp.product_id and pp.plan_id=sp.id order by sp.ordering asc";			
			$db->setQuery($sql);
			$result = $db->loadAssocList();
		}		
		return $result;
	}
	
	function getPluginList(){
		if(!empty($this->plugins) && is_array($this->plugins)){
			return $this->plugins;
		}
		$plugins = JPluginHelper::getPlugin('gurupayment');

		return $plugins;
	}
	
	function getCustomer(){
		$user = JFactory::getUser();
		$user_id = $user->id;
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_customer where id=".intval($user_id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getJoomlaUserF($id){
		$db = JFactory::getDBO();
		$sql = "select name, username, email from #__users where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function checkProfileCompletion($customer){		
		if(empty($customer)){
			return -1;
		}
		
		$user_email = "";

		if(isset($customer["0"]["id"])){
			$user = JFactory::getUser($customer["0"]["id"]);
			$user_email = $user->email;
		}

		if(!isset($customer["0"]["id"]) || ((int)$customer["0"]["id"] <= 0) || strlen(trim($user_email)) < 1 ){
			return -1;
		}
		return 1;
	}
	
	function getConfigs(){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_config";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getPromo(){
		$promocode = JFactory::getApplication()->input->get("promocode", "", "raw");	
		$promo = "";
		$db = JFactory::getDBO();
		
		if(trim($promocode) == ""){
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$promocode = $registry->get('promo_code', "");
		}
		
		if(trim($promocode) != ""){
			$sql = "select * from #__guru_promos where code='".$db->escape(trim($promocode))."'";
			$db->setQuery($sql);
			$promo = $db->loadObjectList();			
			$promo = @$promo["0"];
		}
		else{
			$promo =  $this->getTable("guruPromos");
		}
		return $promo;
	}
	
	function loadCustomer( $sid ){
		$db = JFactory::getDBO();
		$sql = "select transaction_details from #__guru_session where sid=".$sid;
		$db->setQuery($sql);
		$prof = $db->loadResult();
		return unserialize(base64_decode($prof));
	}
	
	function saveNewOrder($total_prices){
		$user = JFactory::getUser();
		$user_id = $user->id;	
		$all_plans = $this->getPlanExpiration();
	
		if(intval($user_id) != 0){
			$db = JFactory::getDBO();
			//$jnow = new JDate('now');
			//$date = $jnow->toSQL();
			
			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
			$jnow = new JDate('now');
			$jnow->setTimezone($timezone);
			$date = $jnow->toSQL(true);
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$procesor = $registry->get('processor', "");
			
			$config = $this->getConfigs();			
			$courses = array();
			$plans = array();
			$all_courses = array();
			$action = JFactory::getApplication()->input->get("action", "", "raw");
			
			if(trim($action) == "renew"){
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$all_courses = $registry->get('renew_courses_from_cart', "");				
			}
			else{
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$all_courses = $registry->get('courses_from_cart', "");
			}
			
			if(isset($all_courses) && count($all_courses) > 0){
				foreach($all_courses as $key=>$value){
					$price = trim($value["value"]);
					
					if($value["plan"] == "buy"){
						$sql = "select p.plan_id from #__guru_program_plans p where p.product_id = ".intval($value["course_id"])." and price like '".$price."'";
						$db->setQuery($sql);
						$plan_id = intval($db->loadResult());
					}
					else{
						$sql = "select p.plan_id from #__guru_program_renewals p where p.product_id = ".intval($value["course_id"])." and price like '".$price."'";
						$db->setQuery($sql);
						$plan_id = intval($db->loadResult());
						
						if(!isset($plan_id) || $plan_id == NULL || $plan_id == "0"){
							$sql = "select p.plan_id from #__guru_program_plans p where p.product_id = ".intval($value["course_id"])." and price like '".$price."'";
							$db->setQuery($sql);
							$plan_id = intval($db->loadResult());
						}
					}	
									
					$courses[] = $value["course_id"]."-".$price."-".$plan_id;
					$plans[$value["course_id"]] = $plan_id;
				}
			}
			else{
				$courses[] = $all_courses;
			}
			
			$promo_code_id = "0";
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$promo_code = $registry->get('promo_code', "");
			
			if(isset($promo_code) && trim($promo_code) != ""){
				$sql = "select id from #__guru_promos where code='".$db->escape(trim($promo_code))."'";
				$db->setQuery($sql);
				$promo_code_id = $db->loadColumn();
				$promo_code_id = $promo_code_id["0"];
			}
			$sql = "INSERT INTO #__guru_order (userid, order_date, courses, status, amount, amount_paid, processor, number_of_licenses, currency, promocodeid, published, form) VALUES (".$user_id.", '".$date."', '".implode("|", $courses)."', 'Pending', '".$total_prices."', '-1', '".addslashes(trim($procesor))."', 0, '".$config["0"]["currency"]."', '".intval($promo_code_id)."' ,0, '')";
			$db->setQuery($sql);
			
			if($db->execute()){
				$sql = "select max(id) from #__guru_order where userid=".intval($user_id);
				$db->setQuery($sql);
				$order_id = $db->loadResult();
				$buy_type = "new";
				
				if(isset($order_id)){
					foreach($all_courses as $key=>$value){
						if(!$this->wasBuy($key, $user_id)){
							//----------- set expiration courses
							$order_expiration = "";					
							$order_date_int = $this->getCurrentDate(strtotime($date), $key, $user_id);
							
							if($all_plans[$plans[$key]]["period"] == "hours" && $all_plans[$plans[$key]]["term"] != "0"){
								$order_expiration = strtotime("+".$all_plans[$plans[$key]]["term"]." hours", $order_date_int);
								$order_expiration = date('Y-m-d H:i:s', $order_expiration);
							}
							elseif($all_plans[$plans[$key]]["period"] == "months" && $all_plans[$plans[$key]]["term"] != "0"){
								$order_expiration = strtotime("+".$all_plans[$plans[$key]]["term"]." month", $order_date_int);
								$order_expiration = date('Y-m-d H:i:s', $order_expiration);
							}
							elseif($all_plans[$plans[$key]]["period"] == "years" && $all_plans[$plans[$key]]["term"] != "0"){
								$order_expiration = strtotime("+".$all_plans[$plans[$key]]["term"]." year", $order_date_int);
								$order_expiration = date('Y-m-d H:i:s', $order_expiration);
							}
							elseif($all_plans[$plans[$key]]["period"] == "days" && $all_plans[$plans[$key]]["term"] != "0"){
								$order_expiration = strtotime("+".$all_plans[$plans[$key]]["term"]." days", $order_date_int);
								$order_expiration = date('Y-m-d H:i:s', $order_expiration);
							}
							elseif($all_plans[$plans[$key]]["period"] == "weeks" && $all_plans[$plans[$key]]["term"] != "0"){
									$order_expiration = strtotime("+".$all_plans[$plans[$key]]["term"]." weeks", $order_date_int);
									$order_expiration = date('Y-m-d H:i:s', $order_expiration);
							} else if (isset($all_plans[$plans[$key]]["term"]) && $all_plans[$plans[$key]]["term"] == '0') {
								$order_expiration = "0000-00-00 00:00:00";
							} else {
								throw new Exception("Plan not found", 404);
							}
							//---------------set expiration course	
							
							if($procesor == 'offline'){
								$sql  = "insert into #__guru_buy_courses (userid, order_id, course_id, price, buy_date, expired_date, plan_id, email_send) values ";
								$sql .= "(".$user_id.", ".$order_id.", ".$key.", '".$value["value"]."', '".$date."', '".$order_expiration."', '".$plans[$key]."', 0)";
								$db->setQuery($sql);
								$db->execute();
							}
							else{
								$sql  = "insert into #__guru_buy_courses (userid, order_id, course_id, price, buy_date, expired_date, plan_id, email_send) values ";
								$sql .= "(".$user_id.", ".$order_id.", ".$key.", '".$value["value"]."', '".$date."', '0000-00-00 00:00:00', '".$plans[$key]."', 0)";
								$db->setQuery($sql);
								$db->execute();
							}
						}
						else{
							$buy_type = "renew";
							$sql = 'update #__guru_buy_courses set plan_id=CONCAT(plan_id, "|", '.$plans[$key].') where userid='.$user_id." and course_id=".$key;
							$db->setQuery($sql);
							$db->execute();
						}
					}
				}
				
				if($procesor == 'offline' && $total_prices > 0){
					// start  sent email to admin to let him know that there are orders in pending
					$template_emails = $config["0"]["template_emails"];
					$template_emails = json_decode($template_emails, true);
					$subject_procesed = $template_emails["pending_order_subject"];
					$body_procesed = $template_emails["pending_order_body"];
					
					$firstnamelastname = "SELECT firstname, lastname FROM #__guru_customer WHERE id=".intval($user_id);
					$db->setQuery($firstnamelastname);
					$firstnamelastname = $db->loadAssocList();
					
					$sql= "Select courses from #__guru_order WHERE id=".intval($order_id)." and userid=".intval($user_id)."";
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
					$configss = JFactory::getConfig();
					$from = $configss->get("mailfrom");
					$fromname = $configss->get("fromname");
						
					$order_url_list = '<a href="'.JURI::root().'administrator/index.php?option=com_guru&controller=guruOrders" target="_blank">'.$fromname.'</a>';
					
					$body_procesed = str_replace("[COURSE_NAME]", $coursename, $body_procesed);
					$body_procesed = str_replace("[STUDENT_LAST_NAME]", $firstnamelastname[0]["lastname"], $body_procesed);
					$body_procesed = str_replace("[STUDENT_FIRST_NAME]", $firstnamelastname[0]["firstname"], $body_procesed);
					$body_procesed = str_replace("[ORDER_NUMBER]", $order_id, $body_procesed);
					$body_procesed = str_replace("[GURU_ORDER_LIST_URL]", $order_url_list, $body_procesed);
					
					$sql = "select u.email from #__users u, #__user_usergroup_map ugm where u.id=ugm.user_id and ugm.group_id='8' and u.id IN (".$config["0"]["admin_email"].")";
					$db->setQuery($sql);
					$email = $db->loadColumn();

					for($i=0; $i<count($email); $i++){
						$send_admin_email_pending_order = isset($template_emails["send_admin_email_pending_order"]) ? $template_emails["send_admin_email_pending_order"] : 1;

						if($send_admin_email_pending_order){
							$site_root = JURI::root();

							if(strpos(" ".$site_root, "localhost") === false){
								JFactory::getMailer()->sendMail($from, $fromname, $email[$i], $subject_procesed, $body_procesed, 1);
							}
						}
						
						if(isset($idss) && count($idss) > 0){
							foreach($idss as $key=>$id){
								$db = JFactory::getDbo();
								$query = $db->getQuery(true);
								$query->clear();
								
								$query->insert('#__guru_logs');
								$query->columns(array($db->quoteName('userid'), $db->quoteName('productid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type') ));
								$query->values(intval($user_id) . ',' . intval($id) . ',' . $db->quote('buy-offline') . ',' . '0' . ',' . $db->quote(trim($email[$i])) . ',' . $db->quote(trim($subject_procesed)) . ',' . $db->quote(trim($body_procesed)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim($buy_type)) );
								$db->setQuery($query);
								$db->execute();
							}
						}
					}
					// end  sent email to admin to let him know that there are orders in pending
				}
				return $order_id;
			}
			return "0";
		}
		return "0";
	}
	
	function wasBuy($course_id, $user_id){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_buy_courses where userid=".intval($user_id)." and course_id=".intval($course_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result == "0"){
			return false;
		}
		return true;
	}
	
	function getCourseId($course_name){
		$db = JFactory::getDBO();
		$sql = "select id from #__guru_program where name like '".addslashes(trim($course_name))."'";
		$db->setQuery($sql);
		$id = $db->loadResult();
		return $id;
	}
	
	function getlistPlugins(){
		if(empty($this->_plugins)){
			$sql = "select * from #__guru_plugins";
			$this->_plugins = $this->_getList($sql);			
		}
		return $this->_plugins;
	}
	
	function registerPlugin($filename, $classname){
		$install_path = $this->_installpath; 
		if (!file_exists($install_path.$filename)) {
			return 0;//_NO_PLUGIN_FILE_EXISTS;	
		}
		require_once ($install_path.$filename);
		$plugin = new $classname;//new $this->plugins[$classname];	// 
		if(!is_object($plugin)){
			return 0;
		}
		foreach($this->req_methods as $method){
			if(!method_exists($plugin, $method)){
				return 0;
			}
		}
		if(isset($this->_plugins[$classname])){
			$this->_plugins[$classname]->instance =& $plugin;
		}
		else{
			$this->_plugins[$classname] = new stdClass;
			$this->_plugins[$classname]->instance =& $plugin;
		}
		return $plugin;
	}
	
	function loadPlugins(){
		if($this->plugins_loaded == 1){
			return;
		}
		$plugins = $this->getlistPlugins();

		foreach($plugins as $plugin){
			$this->registerPlugin($plugin->filename, $plugin->classname);
		    if($plugin->published == '1'){
	        	if($plugin->type == 'payment'){
					$this->payment_plugins[$plugin->name] = $plugin;
					if($plugin->def == 'default'){
						$this->default_payment = $plugin;
					}
				}
				if($plugin->type == 'encoding'){
					$this->encoding_plugins[$plugin->name] = $plugin;
				}
			}
		}
		$this->plugins_loaded = 1;
		return;
	}

	function getCartItems(){
		$db = JFactory::getDBO();
		$items = array();
		$action = JFactory::getApplication()->input->get("action", "", "raw");
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		
		if(trim($action) == "renew"){
			$items = $registry->get('renew_courses_from_cart', "");
		}
		else{
			$items = $registry->get('courses_from_cart', "");
		}
		
		if(isset($items) && count($items) > 0){			
			foreach($items as  $key=>$value){
				$plan_id = "0";
				if($value["plan"] == "buy"){
					$sql = "select p.plan_id from #__guru_program_plans p where p.product_id = ".intval($value["course_id"])." and price like '".$value["value"]."'";
					$db->setQuery($sql);
					$plan_id = intval($db->loadResult());					
				}
				else{
					$sql = "select p.plan_id from #__guru_program_renewals p where p.product_id = ".intval($value["course_id"])." and price like '".$value["value"]."'";
					$db->setQuery($sql);
					$plan_id = intval($db->loadResult());
					if(!isset($plan_id) || $plan_id == NULL || $plan_id == "0"){
						$sql = "select p.plan_id from #__guru_program_plans p where p.product_id = ".intval($value["course_id"])." and price like '".$value["value"]."'";
						$db->setQuery($sql);
						$plan_id = intval($db->loadResult());
					}
				}
				$sql = "select s.name from #__guru_subplan s where id=".intval($plan_id);				
				$db->setQuery($sql);
				$plan_name = $db->loadResult();
				$items[$key]["name"] .= " - ".$plan_name;
			}	
		}		
		return $items;
	}
	
	function proccessAuthorizeFail($controller, $result){
		$msg = "Fail payment";
		if(isset($result['msg']) && !empty($result['msg'])){
			$msg .= " :" . $result['msg'];
		}
		if($result["processor"] == "payauthorize"){
			$sid = $result["sid"];
			$return_url = "index.php?option=com_guru&view=guruBuy&action2=submit&order_id=".intval($sid);
		}
		$controller->setRedirect(JRoute::_($return_url), $msg);
	}
	
	function proccessFail($controller, $result){
		global $Itemid;
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$order_id = $result["order_id"];
		$page_itemid = $result["Itemid"];
		
		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["gurupcategs"];
		
		if(intval($itemid_seo) > 0){
			$page_itemid = intval($itemid_seo);
		}
		
		if($page_itemid != '0'){
			$Itemid = "&Itemid=".$page_itemid;
		}
		else{
			$Itemid = NULL;
		}
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		
		$registry->set('courses_from_cart', "");
		$registry->set('renew_courses_from_cart', "");
		$registry->set('promo_code', "");
		$registry->set('max_total', "");
		$registry->set('order_id', "");
		$registry->set('promocode', "");
		$registry->set('processor', "");
		$registry->set('discount_value', "");
		
		$sql = "select courses from #__guru_order where id=".intval($order_id);
		$db->setQuery($sql);
		$courses_string = $db->loadResult();
		if(isset($courses_string) && trim($courses_string) != ""){
			$all_courses_string = explode("|", $courses_string);
			if(isset($all_courses_string) && count($all_courses_string) > 0){
				foreach($all_courses_string as $key=>$value){
					$temp = explode("-", $value);
					$course_id = $temp["0"];
					$sql = "select plan_id from #__guru_buy_courses where userid=".intval($user_id)." and course_id=".intval($course_id);
					$db->setQuery($sql);
					$plan_id = $db->loadResult();
					if(strpos($plan_id, "|")){
						$temp = explode("|", $plan_id);
						$plan_id = $temp["0"];
						$sql = "update #__guru_buy_courses set plan_id='".$plan_id."'where userid=".intval($user_id)." and course_id=".intval($course_id);
						$db->setQuery($sql);
						$db->execute();
					}
				}
			}
		}
		
		$sql = "delete from #__guru_order where id=".intval($order_id);
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete from #__guru_buy_courses where order_id=".intval($order_id);
		$db->setQuery($sql);
		$db->execute();
		
        $failed_url = JURI::base()."index.php?option=com_guru&view=gurupcategs".$Itemid;
		$failed_url = str_replace ("https://", "http://", $failed_url);

		$this->setError(JText::_('GURU_FAIL_PAY'));
		$app->redirect(JRoute::_($failed_url));
	}
	
	function proccessIPN($controller, $result){
		$this->proccessSuccess($controller, $result, true);
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
	
	function proccessSuccess($controller, $result, $stop = false){
		global $Itemid;
		$app = JFactory::getApplication("site");
		require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cronjobs.php');
		$db = JFactory::getDbo();
		
		$customer_id = isset($result["customer_id"])? $result["customer_id"] : $result["user_id"];
		$order_id = isset($result["order_id"]) ? $result["order_id"] : $result["sid"];
		$price = $result["price"];
		$card_digit = "";
		$card_type = "";

		if(isset($result["card_digit"])){
			$card_digit = $result["card_digit"];
		}

		if(isset($result["card_type"])){
			$card_type = $result["card_type"];
		}
		
		$sql = "select * from #__users where `id`=".intval($customer_id);
		$db->setQuery($sql);
		$user = $db->loadObject();
	
		$sql = "select status from #__guru_order where id=".intval($order_id);
		$db->setQuery($sql);
		$orderstatus = $db->loadResult();
		
		$sql = "select processor from #__guru_order where id=".intval($order_id);
		$db->setQuery($sql);
		$processor_payment = $db->loadResult();
		
		$sql = "select promocodeid from #__guru_order where id=".intval($order_id);
		$db->setQuery($sql);
		$promocodeid = $db->loadResult();
		
		if(intval($promocodeid) != 0){
			$sql = "update #__guru_promos set codeused = codeused + 1 where id='".intval($promocodeid)."'";
			$db->setQuery($sql);
			$db->execute();
		}
		
		$sql = "select currency from #__guru_config where id=1";
		$db->setQuery($sql);
		$currency = $db->loadResult();
		
		if(isset($orderstatus) && trim($orderstatus) == "Pending"){
			//----------- set expiration courses
			$all_plans = $this->getPlanExpiration();
			//$jnow = new JDate('now');
			//$order_date = $jnow->toSQL();
			
			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
			$jnow = new JDate('now');
			$jnow->setTimezone($timezone);
			$order_date = $jnow->toSQL(true);
			
			$sql = "select courses from #__guru_order where id=".intval($order_id);
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
						
						$sql = "select plan_id from #__guru_buy_courses where userid=".intval($customer_id)." and course_id=".intval($course_id);
						$db->setQuery($sql);
						$old_plan_id = $db->loadResult();
						
						if(strpos($old_plan_id, "|") !== FALSE){
							$temp = explode("|", $old_plan_id);
							$plan_id = $temp["1"];
						}
						
						$order_expiration = "";					
						$order_date_int = $this->getCurrentDate(strtotime($order_date), $course_id, $customer_id);
						
						if($all_plans[$plan_id]["period"] == "hours" && $all_plans[$plan_id]["term"] != "0"){
							$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." hours", $order_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($all_plans[$plan_id]["period"] == "months" && $all_plans[$plan_id]["term"] != "0"){
							$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." month", $order_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($all_plans[$plan_id]["period"] == "years" && $all_plans[$plan_id]["term"] != "0"){
							$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." year", $order_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($all_plans[$plan_id]["period"] == "days" && $all_plans[$plan_id]["term"] != "0"){
							$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." days", $order_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($all_plans[$plan_id]["period"] == "weeks" && $all_plans[$plan_id]["term"] != "0"){
								$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." weeks", $order_date_int);
								$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						else{//for unlimited
							$order_expiration = "0000-00-00 00:00:00";
						}
						
						$sql = "update #__guru_buy_courses set buy_date='".$order_date."', expired_date='".$order_expiration."', plan_id=".$plan_id.", email_send=0, order_id=".intval($order_id)." where userid=".intval($customer_id)." and course_id=".intval($course_id);
						$db->setQuery($sql);
						$db->execute();

						// check if need to reset the course for this student
						$sql = "select `reset_on_renew` from #__guru_program where `id`=".intval($course_id);
						$db->setQuery($sql);
						$reset_on_renew = $db->loadColumn();
						$reset_on_renew = @$reset_on_renew["0"];

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
						
						//-----------------send email
						require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cronjobs.php');
						sendEmailOnPurcase($course_id, $order_id, $order_expiration, $plan_id);
						//-----------------send email

						// start teacher commission calculation 
						$sql = "select author from #__guru_program where id=".intval($course_id);
						$db->setQuery($sql);
						$authors = $db->loadAssocList();
						$authors = $authors["0"]["author"];
						
						// send email to teacher for new student enroll ---------------------------------
						$sql = "select `firstname`, `lastname` from #__guru_customer where `id`=".intval($customer_id);
						$db->setQuery($sql);
						$customer_details = $db->loadAssocList();
						$first_name = $customer_details["0"]["firstname"];
						$last_name = $customer_details["0"]["lastname"];

						$sql = "select `name` from #__guru_program where `id`=".intval($course_id);
						$db->setQuery($sql);
						$course_name = $db->loadColumn();
						$course_name = @$course_name["0"];

						include_once(JPATH_SITE.DS."components".DS."com_guru".DS."models".DS."guruprogram.php");
						$modelProgram = new guruModelguruProgram();
						$modelProgram->emailForNewStudentEnrolled($course_id, $course_name, $authors, $first_name, $last_name, $user);
						// send email to teacher for new student enroll ----------------------------------

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
								
								//split_commissions
								$amount_paid_author = (($teacher_earnings * $price)/100) / count($authors);
								$history = 0;
								$sql = "INSERT INTO #__guru_authors_commissions (author_id, course_id, plan_id, order_id, customer_id, commission_id, price, price_paid, amount_paid_author, promocode_id, status_payment, payment_method, data, currency, history) VALUES('".intval($author)."', ".intval($course_id).", ".$plan_id.", '".intval($order_id)."', '".intval($customer_id)."', '".$commission_id."', '".$allpricecourse."', '".$price."', '".$amount_paid_author."', '".$promocodeid."', 'pending', '".$processor_payment."', '".$order_date."', '".$currency."', '".$history."')";
								$db->setQuery($sql);
								$db->execute();
							}
						}
						// end teacher commission calculation 

						// start MailChimp integration
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
								        array('email' => $user->email),      // Specify the e-mail address you want to add to the list.
								        array('FNAME' => $first_name, 'LNAME' => $last_name),   // Set the first name and last name for the new subscriber.
								        'text',    // Specify the e-mail message type: 'html' or 'text'
								        $mc_autoregister,     // Set double opt-in: If this is set to TRUE, the user receives a message to confirm they want to be added to the list.
								        TRUE       // Set update_existing: If this is set to TRUE, existing subscribers are updated in the list. If this is set to FALSE, trying to add an existing subscriber causes an error.
								    );
								} 
								catch (Exception $e) 
								{
								    //echo "Caught exception: " . $e;
								    JFactory::getApplication()->enqueueMessage('Mailchimp subscribe failed. Error: ' . $e->getMessage(),'error');
								}
								//-----------------------------------------------------
							}
						}
						// end MailChimp integration

						// start JomSocial Groups integration
						$sql = "select `extension_id` from #__extensions where `type`='component' and `element`='com_community'";
						$db->setQuery($sql);
						$jsid = $db->loadColumn();

						if(isset($jsid["0"]) && intval($jsid["0"]) > 0){
							$sql = "select `courses` from #__guru_order where `id`=".intval($order_id);
							$db->setQuery($sql);
							$order_courses_result = $db->loadAssocList();
							$order_courses = $order_courses_result["0"]["courses"];
							$order_courses = explode("|", $order_courses);

							if(isset($order_courses) && count($order_courses) > 0){
								foreach($order_courses as $key=>$course_details){
									$course_details = explode("-", $course_details);
									$course_details_id = $course_details["0"];

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
												$sql = "insert into #__community_groups_members (`groupid`, `memberid`,	`approved`,	`permissions`) values ('".intval($saved_js_groups)."', '".intval($customer_id)."', '1', '0')";
												$db->setQuery($sql);
												$db->execute();
											}
										}
										else{
											// user not exist
											$sql = "INSERT INTO `#__community_users` (`userid`, `status`, `status_access`, `points`, `posted_on`, `avatar`, `thumb`, `cover`, `invite`, `params`, `view`, `friends`, `groups`, `events`, `friendcount`, `alias`, `latitude`, `longitude`, `profile_id`, `storage`, `watermark_hash`, `search_email`, `default_avatar`, `usergroup`) VALUES (".intval($customer_id).", '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, '{\"notifyEmailSystem\":1,\"privacyPrivateMessage\":10,\"privacyFriendRequest\":10,\"privacyProfileView\":0,\"privacyPhotoView\":0,\"privacyFriendsView\":0,\"privacyGroupsView\":\"\",\"privacyVideoView\":0,\"notifyEmailMessage\":1,\"notifyEmailApps\":1,\"notifyWallComment\":0,\"invitorId\":0,\"notif_groups_notify_admin\":1,\"etype_groups_notify_admin\":1,\"notif_user_profile_delete\":1,\"etype_user_profile_delete\":1,\"notif_system_reports_threshold\":1,\"etype_system_reports_threshold\":1,\"notif_profile_activity_add_comment\":1,\"etype_profile_activity_add_comment\":1,\"notif_profile_activity_reply_comment\":1,\"etype_profile_activity_reply_comment\":1,\"notif_profile_status_update\":1,\"etype_profile_status_update\":1,\"notif_profile_like\":1,\"etype_profile_like\":1,\"notif_profile_stream_like\":1,\"etype_profile_stream_like\":1,\"notif_profle_digest_email\":0,\"etype_profle_digest_email\":1,\"notif_friends_request_connection\":1,\"etype_friends_request_connection\":1,\"notif_friends_create_connection\":1,\"etype_friends_create_connection\":1,\"notif_inbox_create_message\":1,\"etype_inbox_create_message\":1,\"notif_users_tagged\":1,\"etype_users_tagged\":1,\"notif_groups_invite\":1,\"etype_groups_invite\":1,\"notif_groups_discussion_reply\":1,\"etype_groups_discussion_reply\":1,\"notif_groups_wall_create\":1,\"etype_groups_wall_create\":1,\"notif_groups_create_discussion\":1,\"etype_groups_create_discussion\":1,\"notif_groups_create_news\":1,\"etype_groups_create_news\":1,\"notif_groups_create_album\":1,\"etype_groups_create_album\":1,\"notif_groups_create_video\":1,\"etype_groups_create_video\":1,\"notif_groups_create_event\":1,\"etype_groups_create_event\":1,\"notif_groups_sendmail\":1,\"etype_groups_sendmail\":1,\"notif_groups_member_approved\":1,\"etype_groups_member_approved\":1,\"notif_groups_member_join\":1,\"etype_groups_member_join\":1,\"notif_groups_notify_creator\":1,\"etype_groups_notify_creator\":1,\"notif_groups_discussion_newfile\":1,\"etype_groups_discussion_newfile\":1,\"notif_groups_activity_add_comment\":1,\"etype_groups_activity_add_comment\":1,\"notif_events_notify_admin\":1,\"etype_events_notify_admin\":1,\"notif_events_invite\":1,\"etype_events_invite\":1,\"notif_events_invitation_approved\":1,\"etype_events_invitation_approved\":1,\"notif_events_sendmail\":1,\"etype_events_sendmail\":1,\"notif_event_notify_creator\":0,\"etype_event_notify_creator\":0,\"notif_event_join_request\":1,\"etype_event_join_request\":1,\"notif_events_activity_add_comment\":0,\"etype_events_activity_add_comment\":0,\"notif_events_wall_create\":1,\"etype_events_wall_create\":1,\"notif_videos_submit_wall\":1,\"etype_videos_submit_wall\":1,\"notif_videos_reply_wall\":1,\"etype_videos_reply_wall\":1,\"notif_videos_tagging\":1,\"etype_videos_tagging\":1,\"notif_videos_like\":1,\"etype_videos_like\":1,\"notif_videos_convert_success\":1,\"etype_videos_convert_success\":1,\"notif_photos_submit_wall\":1,\"etype_photos_submit_wall\":1,\"notif_photos_reply_wall\":1,\"etype_photos_reply_wall\":1,\"notif_photos_tagging\":1,\"etype_photos_tagging\":1,\"notif_photos_like\":1,\"etype_photos_like\":1,\"notif_system_messaging\":1,\"etype_system_messaging\":1}', 0, '', '".intval($saved_js_groups)."', '', 0, '', 255.000000, 255.000000, 0, 'file', '', 1, 1, NULL);";

											$db->setQuery($sql);
											$db->execute();

											$sql = "insert into #__community_groups_members (`groupid`, `memberid`,	`approved`,	`permissions`) values ('".intval($saved_js_groups)."', '".intval($customer_id)."', '1', '0')";
											$db->setQuery($sql);
											$db->execute();
										}
									}
								}
							}
						}
						// stop JomSocial Groups integration
					}
				}
			}
			
			$sql = "update #__guru_order set order_date='".$order_date."', status='Paid', amount='".$price."', amount_paid='".$price."', form='', card_digit='".$card_digit."', card_type='".$card_type."' where id=".$order_id;
			$db->setQuery($sql);
			$db->execute();
			
			//-------------------
			$dispatcher = new Dispatcher;
			$dispatcher->triggerEvent('onCustomSuccessPayment', array(array("customer_id"=>$customer_id, "order_id"=>$order_id)));

			JEventDispatcher::getInstance()->trigger('onAfterGuruCourseBuy', array("customer_id"=>$customer_id, "order_id"=>$order_id));
			//-------------------

			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('courses_from_cart', "");
			$registry->set('renew_courses_from_cart', "");
			$registry->set('promo_code', "");
			$registry->set('max_total', "");
			$registry->set('order_id', "");
			$registry->set('promocode', "");
			$registry->set('processor', "");
		}
		
		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruorders"];
		
		if(intval($itemid_seo) > 0){
			$item_id = intval($itemid_seo);
		}

		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$registry->set('courses_from_cart', "");
		$registry->set('renew_courses_from_cart', "");
		$registry->set('promo_code', "");
		$registry->set('max_total', "");
		$registry->set('order_id', "");
		$registry->set('promocode', "");
		$registry->set('processor', "");

		JFactory::getApplication()->enqueueMessage(JText::_("GURU_PAYMENT_SUCCESSFULLY"),'success');
		$app->redirect(JRoute::_("index.php?option=com_guru&view=guruorders&layout=myorders&Itemid=".intval($item_id)));
		return true;
	}
	
	function proccessWait( $controller, $result ){
		global $Itemid;
		$app = JFactory::getApplication("site");
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		
		$var_processor = $registry->get('processor', "");
		$promo_code = $registry->get('promo_code', "");
		
		$registry->set('courses_from_cart', "");
		$registry->set('renew_courses_from_cart', "");
		$registry->set('promo_code', "");
		$registry->set('max_total', "");
		$registry->set('order_id', "");
		$registry->set('promocode', "");
		$registry->set('processor', "");
		
		if($var_processor == 'offline'){
			$db = JFactory::getDBO();
			$order_id = $result["order_id"];
			//$jnow = new JDate('now');
			//$order_date = $jnow->toSQL();
			
			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
			$jnow = new JDate('now');
			$jnow->setTimezone($timezone);
			$order_date = $jnow->toSQL(true);
			
			$all_plans = $this->getPlanExpiration();
			
			if(trim($promo_code) != ""){
				$sql = "update #__guru_promos set codeused = codeused + 1 where code='".$db->escape(trim($promo_code))."'";
				$db->setQuery($sql);
				$db->execute();
			}
			
			$sql = "select userid, courses from #__guru_order where id=".intval($order_id);
			$db->setQuery($sql);
			$result = $db->loadAssocList();
			$courses = $result["0"]["courses"];
			$customer_id = $result["0"]["userid"];
			
			if(isset($courses) && trim($courses) != ""){
				$all_courses = explode("|", $courses);
				if(isset($all_courses) && is_array($all_courses) && count($all_courses) > 0){
					foreach($all_courses as $key => $value){
						$temp1 = explode("-", $value);
						$course_id = $temp1["0"];
						$plan_id = $temp1["2"];
						$allpricecourse = $temp1["1"];
						
						$sql = "select plan_id from #__guru_buy_courses where userid=".intval($customer_id)." and course_id=".intval($course_id);
						$db->setQuery($sql);
						$old_plan_id = $db->loadResult();
						if(strpos($old_plan_id, "|") !== FALSE){
							$temp = explode("|", $old_plan_id);
							$plan_id = $temp["1"];
						}
						
						$order_expiration = "";					
						$order_date_int = $this->getCurrentDate(strtotime($order_date), $course_id, $customer_id);
						
						if($all_plans[$plan_id]["period"] == "hours" && $all_plans[$plan_id]["term"] != "0"){
							$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." hours", $order_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($all_plans[$plan_id]["period"] == "months" && $all_plans[$plan_id]["term"] != "0"){
							$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." month", $order_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($all_plans[$plan_id]["period"] == "years" && $all_plans[$plan_id]["term"] != "0"){
							$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." year", $order_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($all_plans[$plan_id]["period"] == "days" && $all_plans[$plan_id]["term"] != "0"){
							$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." days", $order_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						elseif($all_plans[$plan_id]["period"] == "weeks" && $all_plans[$plan_id]["term"] != "0"){
							$order_expiration = strtotime("+".$all_plans[$plan_id]["term"]." weeks", $order_date_int);
							$order_expiration = date('Y-m-d H:i:s', $order_expiration);
						}
						else{//for unlimited
							$order_expiration = "0000-00-00 00:00:00";
						}

						//$sql = "update #__guru_buy_courses set buy_date='".$order_date."', expired_date='".$order_expiration."', plan_id=".$plan_id.", email_send=0, order_id=".intval($order_id)." where userid=".intval($customer_id)." and course_id=".intval($course_id);

						$sql = "update #__guru_buy_courses set plan_id=".$plan_id.", email_send=0, order_id=".intval($order_id)." where userid=".intval($customer_id)." and course_id=".intval($course_id);
						$db->setQuery($sql);
						$db->execute();
						//-----------------send email
						require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cronjobs.php');
						sendEmailOnPurcase($course_id, $order_id, $order_expiration, $plan_id);
						//-----------------send email
					}
				}
			}
			
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruorders"];
			
			if(intval($itemid_seo) > 0){
				$item_id = intval($itemid_seo);
			}
			
			$app->redirect(JRoute::_("index.php?option=com_guru&view=guruorders&layout=myorders&Itemid=".intval($item_id), false));
		}
		else{
			$app->redirect(JRoute::_("index.php?option=com_guru&view=guruorders&layout=mycourses&Itemid=".intval($item_id), false));
		}
		return true;
	}
	
	function getPlanExpiration(){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_subplan";
		$db->setQuery($sql);
		$result = $db->loadAssocList("id");
		return $result;
	}
};

?>