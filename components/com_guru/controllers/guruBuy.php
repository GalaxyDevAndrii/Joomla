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

jimport ('joomla.application.component.controller');
JPluginHelper::importPlugin('gurupayment');
use Joomla\Event\Event;
use Joomla\Event\Dispatcher;

class guruControllerguruBuy extends guruController {
	
	function __construct(){
		parent::__construct();
		$this->registerTask("","view");
		$this->registerTask("checkout", "checkout");
		$this->registerTask("payment", "payment");
		$this->registerTask("return", "return_action");
		$this->registerTask("cancelreturn", "cancelreturn");
		$this->registerTask("updatecart", "updatecart");
		$this->registerTask("deletefromsession", "deleteFromSession");
		$this->registerTask("failPayment", "failPayment");
		$this->registerTask("apiurl", "apiUrl");
		$this->_model = $this->getModel("guruBuy");
	}
	
	function view(){
		JFactory::getApplication()->input->set('view', 'guruBuy');	
		parent::display();
	}
	
	function getPromoDiscountCoursee($total, $promo_id){
		$old_total = $total;
		$value_to_display = "";
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_promos where id='".intval($promo_id)."'";
		$db->setQuery($sql);
		$db->execute();
		$promo = $db->loadObjectList();			
		$promo_details = @$promo["0"];
		
		if(@$promo_details->typediscount == '0') {//use absolute values					
			$difference = $total - (float)$promo_details->discount;
			if($difference < 0){
				$total = 0;
			}
			else{
				$total = $difference;
			}					
		}
		else{//use percentage
			$total = (@$promo_details->discount / 100)*$total;
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
	
	function updatecart(){
		$all_courses = array();
		$discount_code = JFactory::getApplication()->input->get("promocode", "", "raw");
		$msg2 = "";
		$msg = "";
		$msg_one_time = "";
		
		if(trim($discount_code) != ""){
			$msg2 = JText::_("GURU_PROMO_FOR_STUD");
			$msg = JText::_("GURU_PROMO_NOT_AVAILABLE");
			$msg_one_time = JText::_("GURU_PROMO_ONE_TIME_USED");
		}
		
		$action = JFactory::getApplication()->input->get("action", "", "raw");
		$counter = 0;
		$db = JFactory::getDBO();
		
		if(trim($action) == ""){
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$all_courses = $registry->get('courses_from_cart', "");
		}
		else{
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$all_courses = $registry->get('renew_courses_from_cart', "");
		}
		
		$prices = JFactory::getApplication()->input->get("plan_id", array(), "raw");
		$prices = $this->getPricesValues($prices);

		if(!isset($prices) || count($prices) == 0){
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruBuy",false));
			return false; // aici
		}
		
		$total = 0.0;
		$first_total = 0;
		$sum_discount = 0;
		
		$sql = "select courses_ids from #__guru_promos where code='".$db->escape($discount_code)."'";
		$db->setQuery($sql);
		$db->execute();
		$courses_ids_list = $db->loadColumn();
		$courses_ids_list2 = implode(",",$courses_ids_list);
		$courses_ids_list3 = explode("||",$courses_ids_list2);
		$courses_ids_list3 = array_filter($courses_ids_list3);
		
		if(isset($all_courses) && is_array($all_courses) && count($all_courses) > 0){
			$quantity = JFactory::getApplication()->input->get("quantity", array());
			$selected_customers = JFactory::getApplication()->input->get("selected_customers", array());
			
			foreach($all_courses as $key=>$value){	
				$first_total += $prices[$key];

				if($prices[$key] <= 0){
					if(isset($all_courses[$key])){
						$first_total = $all_courses[$key]["value"];

						$plan_id = JRequest::getVar("plan_id", array(), "post", "array");

						if(!isset($plan_id) || count($plan_id) == 0){
							$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruBuy",false));
							return false; // aici
						}

						if(isset($plan_id[$key])){
							$plan_id[$key] = $first_total;
						}

						JRequest::setVar("plan_id", $plan_id);
						$prices = JFactory::getApplication()->input->get("plan_id", array(), "raw");
						$prices = $this->getPricesValues($prices);
					}
				}
				
				if(count($courses_ids_list3) > 0 && in_array($value["course_id"], $courses_ids_list3)){
					$counter += 1;
					$model = $this->getModel("guruBuy");
					$promo_details = $model->getPromo();
					
					if($promo_details->typediscount == '0') {//use absolute values		
						$difference = $prices[$key] - (float)$promo_details->discount;
						$total += $difference;
						$sum_discount += (float)$promo_details->discount;
					}
					else{//use percentage
						$total_temp = ($promo_details->discount / 100)*$prices[$key];
						$difference = $prices[$key] - $total_temp;
						$discount = $prices[$key] - $difference;
						$total += (float)$prices[$key] - (float)$discount;
						$sum_discount += (float)$discount;
					}
					$all_courses[$key]["value"] = $prices[$key];
				}
				elseif(count($courses_ids_list3) <= 0){
					$counter += 1;
					$model = $this->getModel("guruBuy");
					$promo_details = $model->getPromo();
					
					if($promo_details->typediscount == '0') {//use absolute values		
						$difference = $prices[$key] - (float)$promo_details->discount;
						$total += $difference;
						$sum_discount += (float)$promo_details->discount;
					}
					else{//use percentage
						$total_temp = ($promo_details->discount / 100)*$prices[$key];
						$difference = $prices[$key] - $total_temp;
						$discount = $prices[$key] - $difference;
						$total += (float)$prices[$key] - (float)$discount;
						$sum_discount += (float)$discount;
					}
					$all_courses[$key]["value"] = $prices[$key];
				}
				else{
					$all_courses[$key]["value"] = $prices[$key];
					$total += $prices[$key];
				}
				
				if(isset($quantity) && isset($quantity[$value["course_id"]])){
					$all_courses[$key]["quantity"] = $quantity[$value["course_id"]];
				}
				
				if(isset($selected_customers) && isset($selected_customers[$value["course_id"]])){
					$all_courses[$key]["selected_customers"] = $selected_customers[$value["course_id"]];
				}
			}
					
			if(trim($action) == ""){
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('courses_from_cart', $all_courses);
			}
			else{
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('renew_courses_from_cart', $all_courses);
			}
		}

		$old_total = $first_total;
	
		$sql = "select id, published from #__guru_promos where code='".$db->escape($discount_code)."'";
		$db->setQuery($sql);
		$db->execute();
		$result_pubpromo = $db->loadAssocList();
		$pubpromo = @$result_pubpromo["0"]["published"];
		$promocodeid = @$result_pubpromo["0"]["id"];

		$jnow = new JDate('now');
		$date = $jnow->toSQL(true);
		$date = strtotime($date);
		
		$sql = "select codeused, codelimit, codeend, codestart, forexisting, one_time from #__guru_promos where id=".intval($promocodeid);
		$db->setQuery($sql);
		$db->execute();
		$result_codeused = $db->loadAssocList();
		$codeused = @$result_codeused["0"]["codeused"];
		$codelimit = @$result_codeused["0"]["codelimit"];
		$codeend = @$result_codeused["0"]["codeend"];
		$codestart = strtotime(@$result_codeused["0"]["codestart"]);
		$forexisting = @$result_codeused["0"]["forexisting"];
		$one_time = @$result_codeused["0"]["one_time"];
		
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$sql = "select count(id) from #__guru_customer where id=".intval($user_id);
		$db->setQuery($sql);
		$db->execute();
		$isstudent = $db->loadColumn();
		$isstudent = $isstudent["0"];
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		
		if($codeend =='0000-00-00 00:00:00'){
			$never = 1;
		}
		else{
			$never = 0;
		}

		$codeend = strtotime($codeend);
		
		if($pubpromo == 0){
			$discount_code ="";
			$registry->set('msg', $msg);
		}
		
		if($codeused >= $codelimit && $never == 0 && $codelimit >0){
			$discount_code ="";
			$registry->set('msg', $msg);
		}
		
		if($date < $codestart || ($date > $codeend && $never == 0)){
			$discount_code ="";
			$registry->set('msg', $msg);
		}
		
		if($forexisting == 1 && $isstudent == 0){
			$discount_code = "";
			$registry->set('msg', $msg2);
		}

		if(intval($one_time) > 0){
			if(intval($user_id) == 0){
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=checkout", false));
				return true;
			}

			$sql = "select count(*) from #__guru_order where `promocodeid`=".intval($promocodeid)." and `userid`=".intval($user_id)." and `status`='Paid'";
			$db->setQuery($sql);
			$db->execute();
			$count_used_promo = $db->loadColumn();
			$count_used_promo = @$count_used_promo["0"];

			if(intval($count_used_promo) > 0){
				$discount_code = "";
				$registry->set('msg', $msg_one_time);
			}
		}
		
		if(trim($discount_code) != ""){
			$registry->set('promo_code', $discount_code);
			
			$model = $this->getModel("guruBuy");
			$promo_details = $model->getPromo();
			
			if(!isset($promo_details)){// promo expired
				$registry->set('promo_code', "");
				$registry->set('discount_value', "");
			}
			else{
				$set_discount = false;			
				if(trim($promo_details->codelimit) != 0){
					if(trim($promo_details->codelimit) > trim($promo_details->codeused)){
						$set_discount = true;
					}
				}
				else{
					$set_discount = true;
				}
				
				if($set_discount === TRUE){
					$configs = $model->getConfigs();
					$currency = $configs["0"]["currency"];
					$currencypos = $configs["0"]["currencypos"];					
					$character = JText::_("GURU_CURRENCY_".$currency);
					
					if($promo_details->typediscount == '0') {//use absolute values		
						//$difference = $total - (float)$promo_details->discount;
						$difference = $total;
						
						if($difference < 0){
							$total = 0;
							$counter = $counter -1;
						}
						$model = $this->getModel('gurubuy');
						
						if($currencypos == 0){
							$registry->set('discount_value', $character." ".$sum_discount);
						}
						else{
							$registry->set('discount_value', $sum_discount." ".$character);
						}
					}
					else{//use percentage
						$difference = $old_total - $total;
						
						if($difference < 0){
							if($currencypos == 0){
								$registry->set('discount_value', $character." "."0");
							}
							else{
								$registry->set('discount_value', "0"." ".$character);
							}								
						}
						else{
							$discount = $difference;
							if($currencypos == 0){
								$registry->set('discount_value', $character." ".$sum_discount);
							}
							else{
								$registry->set('discount_value', $sum_discount." ".$character);
							}	
							
							$total = (float)$old_total - $sum_discount;
						}
						
					}
					
					$registry->set('msg', "");		
				}
				else{
					$registry->set('promo_code', "");
					$registry->set('discount_value', "");
					$registry->set('msg', $msg);
				}
			}// if promo is not expired
		}
		else{		
			$registry->set('promo_code', "");
			$registry->set('discount_value', "");
		}
		
		$point_poz = strpos($total, ".");		
		$total = substr($total, 0, $point_poz+3);
		$registry->set('max_total', $total);
		
		$lang = JFactory::getApplication()->input->get("lang", "", "raw");
		$lang_url = "";

		if(isset($lang) && trim($lang) != ""){
			$lang_url = "&lang=".trim($lang);
		}

		if(trim($action) != ""){
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruBuy&action=".trim($action).$lang_url, false));
		}
		else{
			$msg = $registry->get('msg', "");
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruBuy".$lang_url, false), $msg, 'warning');
		}
	}
	
	function checkout(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$db = JFactory::getDBO();
		$from = JFactory::getApplication()->input->get("from", "", "raw");
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		
		if(trim($from) == ""){
			$promocode = JFactory::getApplication()->input->get("promocode", "");
			$procesor = JFactory::getApplication()->input->get("processor", "");
			
			$registry->set('promocode', trim($promocode));
			$registry->set('processor', trim($procesor));
			
			$this->updatecart();
		}

		$promocode_session = $registry->get('promocode', "");
		
		$sql = "select courses_ids from #__guru_promos where code='".addslashes(trim($promocode_session))."'";
		$db->setQuery($sql);
		$courses_for_promo = $db->loadResult();
		
		$sql = "select id from #__guru_promos where code='".addslashes(trim($promocode_session))."'";
		$db->setQuery($sql);
		$promo_id_f = $db->loadResult();
		
		$list_courses_promo = explode("||", $courses_for_promo);
		
		$order_id = "";
		$_Itemid = $Itemid;
		$cart = $this->getModel('gurubuy');		
		$plugins_enabled = $cart->getPluginList();		
		$user = JFactory::getUser();
		$user_id = $user->id;

		// Check Login
		if($user_id == "0"){
			// sincronize courses from session with courses from request;
			$courses_request = JFactory::getApplication()->input->get("plan_id", array(), "raw");
			$courses_request = $this->getPricesValues($courses_request);
			$courses_session = $registry->get('courses_from_cart', "");
			
			if(!isset($courses_request) || count($courses_request) == 0){
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruBuy",false));
				return false; // aici
			}

			if(isset($courses_request) && count($courses_request) > 0){
				foreach($courses_request as $key=>$value){
					$courses_session[$key]["value"] = $value;
				}
				
				$registry->set('courses_from_cart', $courses_session);
			}
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=checkout", false));
			return true;
		}
		
		$customer = $cart->getCustomer();
		
		// Check Payment Plugin installed
		if(empty($plugins_enabled)) {
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurubuy"];
			
			if(intval($itemid_seo) > 0){
				$_Itemid = intval($itemid_seo);
			}
		
			$msg = JText::_('Payment plugins not installed');
			$this->setRedirect(JRoute::_("index.php?option=com_guru&controller=guruBuy&Itemid=".$_Itemid, false), $msg);
			return;
		}

		$res = $cart->checkProfileCompletion($customer);
		$details_user =$cart->getJoomlaUserF($user_id);
		$username = $details_user["0"]["username"];
		$email = $details_user["0"]["email"];
		$name = $details_user["0"]["name"];

		$temp = explode(" ", $name);
		if(isset($temp) && count($temp) > 1){		
			$last_name = $temp[count($temp) - 1];	
			unset($temp[count($temp) - 1]);
			$first_name = implode(" ", $temp); 
		}
		else{
			if(count($temp) == 1){
				$first_name = $name;
				$last_name  = $name;
			}
		}
		
		if($res < 1){
			$db = JFactory::getDBO();
			$sql = "insert into #__guru_customer(id, firstname, lastname) values (".intval($user_id).", '".addslashes(trim($first_name))."', '".addslashes(trim($last_name))."')";
			$db->setQuery($sql);
			$db->execute();
			
		}
		$total = 0;

		$configs = $cart->getConfigs();
		$items = $cart->getCartItems($customer, $configs);

		$params['user_id'] = $customer["0"]["id"];
		
		if ( isset($this->_customer) && isset($this->_customer->_customer) ) {
			$params['customer'] = ($this->_customer->_customer);
			// get email from user and set to customer
			$user = JFactory::getUser();
			$params['customer']->email = $user->get('email');
		}
		
		$processor_session = $registry->get('processor', "");
		
		$params['products'] = $items; // array of products
		$params['config'] = $configs;
		$params['processor'] = $processor_session;
		$gataways = JPluginHelper::getPlugin('gurupayment', $params['processor']);

		if(is_array($gataways)){
			foreach($gataways as $gw){
				if($gw->name == $prosessor){
					$params['params'] = $gw->params;
					break;
				}
			}
		}
		else{
			$params['params'] = $gataways->params;
		}

		$character = JText::_("GURU_CURRENCY_".$configs["0"]["currency"]);
		$discount_value = $registry->get('discount_value', "");
		
		$discount_value = str_replace($character, "", $discount_value);
		$discount_value = trim($discount_value);
		
		//check if the price is 0(zero), to not redirect to paypal
		$total_prices = 0;

		// check if cart products have prices selected in cart
		$plan_id_from_cart = JFactory::getApplication()->input->get("plan_id", array(), "raw");

		foreach($items as $key=>$value){
			if(!isset($plan_id_from_cart[$key]) || count($plan_id_from_cart) == 0){
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruBuy",false));
				return false; // aici
			}
		}

		foreach($items as $key=>$value){
			$plan_selected = JFactory::getApplication()->input->get("plan_selected", array());
			$plan_id = JFactory::getApplication()->input->get("plan_id", array(), "raw");
			$plan_id = $this->getPricesValues($plan_id);

			if(!isset($plan_id) || count($plan_id) == 0){
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruBuy",false));
				return false; // aici
			}
			
			$course_plans = $cart->getCoursePlans($value["course_id"], $value["plan"]);
			
			if(in_array($value["course_id"], $list_courses_promo) || (count($list_courses_promo) == 1 && intval($list_courses_promo["0"]) == 0)){
				$value["value"] = $this->getPromoDiscountCoursee($value["value"], $promo_id_f);
			}

			$total_prices += $value["value"];
		}

		$order_id = $cart->saveNewOrder($total_prices);
		$model = $this->getModel("guruBuy");
		$promo_details = $model->getPromo();

		if($total_prices == "0" || $total_prices == "0.00"){
			$model = $this->getModel("guruBuy");
			$submit_array = array("customer_id"=>$customer["0"]["id"], "order_id"=>$order_id, "price"=>"0");
			
			$registry->set('courses_from_cart', "");
			$registry->set('renew_courses_from_cart', "");
			$registry->set('promo_code', "");
			$registry->set('max_total', "");
			$registry->set('order_id', "");
			$registry->set('promocode', "");
			$registry->set('processor', "");
			$registry->set('discount_value', "");
			
			$model->proccessSuccess("guruBuy", $submit_array, false);
		}

		$params['order_id'] = $order_id;
		$params['sid'] = $order_id;
		$params['option'] = 'com_guru';
		$params['controller'] = 'guruBuy';
		$params['task'] = 'payment';
		$params['order_amount'] = $discount_value;
		$params['order_currency'] = $configs["0"]['currency'];
		$params['Itemid'] = JFactory::getApplication()->input->get('Itemid', "0", "raw");
		$params["customer_id"] = $customer["0"]["id"];

		
		if(!class_exists('JDispatcher')){
			$dispatcher = new Dispatcher;
			$event = new Event('onSendPayment');
			$event = $event->setArgument('result', $params);
			$result = $dispatcher->addEvent($event);
			$config = JFactory::getConfig();

			switch ($procesor) {
				case 'paypaypal': {
					$plgGurupaymentPaypaypal = new plgGurupaymentPaypaypal($dispatcher, $config);
					$result = $plgGurupaymentPaypaypal->onSendPayment($params);
					
					break;
				}
				case 'offline': {
					$plgGurupaymentOffline = new plgGurupaymentOffline($dispatcher, $config);
					$result = $plgGurupaymentOffline->onSendPayment($params);

					break;
				}
				case 'payauthorize': {
					$plgGurupaymentPayauthorize = new plgGurupaymentPayauthorize($dispatcher, $config);
					$result = $plgGurupaymentPayauthorize->onSendPayment($params);

					break;
				}
				case 'dotpay': {
					$plgGurupaymentDotPay = new plgGurupaymentDotPay($dispatcher, $config);
					$result = $plgGurupaymentDotPay->onSendPayment($params);
					
					break;
				}
				case 'payfast': {
					$plgGurupaymentPayFast = new plgGurupaymentPayFast($dispatcher, $config);
					$result = $plgGurupaymentPayFast->onSendPayment($params);
					
					break;
				}
				case 'stripe': {
					$plgGurupaymentStripe = new plgGurupaymentStripe($dispatcher, $config);
					$result = $plgGurupaymentStripe->onSendPayment($params);
					
					break;
				}
				case 'paypalpro': {
					$plgGurupaymentPayPalPro = new plgGurupaymentPayPalPro($dispatcher, $config);
					$result = $plgGurupaymentPayPalPro->onSendPayment($params);
					
					break;
				}
				case 'mobilpay': {
					$plgGurupaymentMobilPay = new plgGurupaymentMobilPay($dispatcher, $config);
					$result = $plgGurupaymentMobilPay->onSendPayment($params);
					
					break;
				}
				case '2checkout': {
					$plgGurupayment2checkout = new plgGurupayment2checkout($dispatcher, $config);
					$result = $plgGurupayment2checkout->onSendPayment($params);
					
					break;
				}
			}
		}
		else{
			$dispatcher = JDispatcher::getInstance();
			$result = $dispatcher->trigger('onSendPayment', array(&$params));
		}

		if(is_array($result)){
			foreach($result as $key=>$value){
				if(isset($value) && trim($value) != ""){
					$form_created = $value;
					break;
				}
			}

		}
		else{
			$form_created = trim($result);
		}
		
		
		$db = JFactory::getDBO();
		$sql = "update #__guru_order set form='".trim(addslashes($form_created))."' where id=".intval($order_id);
		$db->setQuery($sql);
		$db->execute();
		
		//for https ---------------------------------------
		$processor = $registry->get('processor', "");
		
		
		if($processor == "payauthorize"){
			$page_url = $this->getPageURL();
			$reqhttps = "1";
			if(is_file(JPATH_SITE.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."gurupayment".DIRECTORY_SEPARATOR."payauthorize".DIRECTORY_SEPARATOR."install")){
				$content = JFile::read(JPATH_SITE.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."gurupayment".DIRECTORY_SEPARATOR."payauthorize".DIRECTORY_SEPARATOR."install");
				$reqhttps = $this->getReqhttps($content);
				if($reqhttps == "1"){//https
					if(strpos($page_url, "https") === FALSE){
						$site = JURI::root();
						$site = str_replace("http", "https", $site);
						
						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["gurubuy"];
						
						if(intval($itemid_seo) > 0){
							$Itemid = intval($itemid_seo);
						}
						
						$page_url = $site."index.php?option=com_guru&view=guruBuy&action2=submit&order_id=".$order_id."&Itemid=".intval($Itemid);
						$this->setRedirect(JRoute::_($page_url, false));
						return true;
					}
				}
			}
		}

		$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruBuy&action2=submit&order_id=".$order_id."&Itemid=".intval($Itemid), false));
		
		//for https ---------------------------------------
	}
	
	function getPageURL(){
		$pageURL = 'http';
		if($_SERVER["HTTPS"] == "on"){
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if($_SERVER["SERVER_PORT"] != "80"){
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	function getReqhttps($content){
		$reqhttps = "1";
		if(trim($content) != ""){
			$by_n = explode("\n", $content);
			if(isset($by_n)){
				foreach($by_n as $key=>$value){
					$by_equal = explode("=", $value);
					if(is_array($by_equal) && count($by_equal) > 0){
						if($by_equal["0"] == "reqhttps"){
							$reqhttps = trim($by_equal["1"]);
						}
					}
				}
			}
		}
		return $reqhttps;
	}
	
	function payment(){
		$model = $this->getModel("guruBuy");
		
		if(JFactory::getApplication()->input->get('processor', '', "raw") == ''){
			return false;
		}

		$session = JFactory::getSession();
		$registry = $session->get('registry');
		
		$registry->set('creditCardNumber', JFactory::getApplication()->input->get("creditCardNumber", ""));
		$registry->set('expDateMonth', JFactory::getApplication()->input->get("expDateMonth", ""));
		$registry->set('expDateYear', JFactory::getApplication()->input->get("expDateYear", ""));
		$registry->set('cvv2Number', JFactory::getApplication()->input->get("cvv2Number", ""));
		
		JPluginHelper::importPlugin('gurupayment');
		$params = JPluginHelper::getPlugin('gurupayment', JFactory::getApplication()->input->get('processor'))->params;
		
		$param = array_merge(JFactory::getApplication()->input->request->getArray(), array('params' => $params));
		$param['handle'] = &$this;

		if(!class_exists('JDispatcher')){
			$dispatcher = new Dispatcher;
			$event = new Event('onReceivePayment');
			$event = $event->setArgument('result', $param);
			$result = $dispatcher->addEvent($event);
			$config = JFactory::getConfig();
			$procesor = JFactory::getApplication()->input->get('processor', '', "raw");

			switch ($procesor) {
				case 'paypaypal': {
					$plgGurupaymentPaypaypal = new plgGurupaymentPaypaypal($dispatcher, $config);
					$results_plugins = $plgGurupaymentPaypaypal->onReceivePayment($param);
					
					break;
				}
				case 'offline': {
					$plgGurupaymentOffline = new plgGurupaymentOffline($dispatcher, $config);
					$results_plugins = $plgGurupaymentOffline->onReceivePayment($param);

					break;
				}
				case 'payauthorize': {
					$plgGurupaymentPayauthorize = new plgGurupaymentPayauthorize($dispatcher, $config);
					$results_plugins = $plgGurupaymentPayauthorize->onReceivePayment($param);

					break;
				}
				case 'dotpay': {
					$plgGurupaymentDotPay = new plgGurupaymentDotPay($dispatcher, $config);
					$results_plugins = $plgGurupaymentDotPay->onReceivePayment($param);
					
					break;
				}
				case 'payfast': {
					$plgGurupaymentPayFast = new plgGurupaymentPayFast($dispatcher, $config);
					$results_plugins = $plgGurupaymentPayFast->onReceivePayment($param);
					
					break;
				}
				case 'stripe': {
					$plgGurupaymentStripe = new plgGurupaymentStripe($dispatcher, $config);
					$results_plugins = $plgGurupaymentStripe->onReceivePayment($param);
					
					break;
				}
				case 'paypalpro': {
					$plgGurupaymentPayPalPro = new plgGurupaymentPayPalPro($dispatcher, $config);
					$results_plugins = $plgGurupaymentPayPalPro->onReceivePayment($params);
					
					break;
				}
				case 'mobilpay': {
					$plgGurupaymentMobilPay = new plgGurupaymentMobilPay($dispatcher, $config);
					$results_plugins = $plgGurupaymentMobilPay->onReceivePayment($params);
					
					break;
				}
				case '2checkout': {
					$plgGurupayment2checkout = new plgGurupayment2checkout($dispatcher, $config);
					$results_plugins = $plgGurupayment2checkout->onReceivePayment($params);
					
					break;
				}
			}
		}
		else{
			$dispatcher = JDispatcher::getInstance();
			$results_plugins = $dispatcher->trigger('onReceivePayment', array(&$param));
		}

		$result = array();

		if(isset($results_plugins) && count($results_plugins) > 0){
			foreach($results_plugins as $key_result=>$result_value){
				if(is_array($result_value)){
					$result = $result_value;
				}
			}
		}
		else{
			$result = $results_plugins;
		}

		if(is_array($result) && empty($result['sid'])){
			$result['sid'] = -1;
		}	

		if(is_array($result) && empty($result['pay'])){
			$result['pay'] = 'fail';
		}	

		if(isset($result) && !empty($result)){
			// set sid if empty 
			if((!isset($result['sid']) || empty($result['sid'])) && !empty($result['order_id'])){
				$result['sid'] = $result['order_id'];
			}

			switch($result['pay']){
				case 'success':
					$model->proccessSuccess($this, $result);
					break;
				case 'ipn':
					$model->proccessIPN($this, $result);
					break;
				case 'wait':
					$model->proccessWait($this, $result);
					break;
				case 'fail':
					if($result["processor"] == "paypaypal" || $result["processor"] == "offline"){
						$model->proccessFail($this, $result);
					}
					else{
						$model->proccessAuthorizeFail($this, $result);
					}
					break;
				default:
					break;
			}
		}
		else{
			$Itemid_orders = JFactory::getApplication()->input->get("Itemid", "0", "raw");
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_("index.php?option=com_guru&view=guruorders&layout=myorders&Itemid=".intval($Itemid_orders), false));
		}
	}
	
	function cancelreturn(){
		$msg = JText::_("GURU_OPERATION_CANCELED");
		$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruBuy", false), $msg);
	}
	
	function deleteFromSession(){
		$course_id = JFactory::getApplication()->input->get("course_id");
		$action = JFactory::getApplication()->input->get("action", "");
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		
		if(trim($action) == "buy"){
			$all_courses = $registry->get('courses_from_cart', "");
			unset($all_courses[$course_id]);
			$registry->set('courses_from_cart', $all_courses);
		}
		else{
			$all_courses = $registry->get('renew_courses_from_cart', "");
			unset($all_courses[$course_id]);
			$registry->set('renew_courses_from_cart', $all_courses);
		}
		
		die();
	}

	function getPricesValues($prices){
		$db = JFactory::getDbo();
		$return_prices = array();

		if(isset($prices) && count($prices) > 0){
			foreach($prices as $course_id=>$plan_id){
				$sql = "select `price` from #__guru_program_plans where `product_id`=".intval($course_id)." and `plan_id`=".intval($plan_id);

				$plan_selected_renew = JFactory::getApplication()->input->get("plan_selected_renew_".intval($course_id), "", "raw");

				if($plan_selected_renew == "renew"){
					$sql = "select `price` from #__guru_program_renewals where `product_id`=".intval($course_id)." and `plan_id`=".intval($plan_id);
				}

				$db->setQuery($sql);
				$db->execute();
				$price = $db->loadColumn();

				if(isset($price["0"])){
					$return_prices[$course_id] = trim($price["0"]);
				}
				else{
					$sql = "select `price` from #__guru_program_plans where `product_id`=".intval($course_id)." and `plan_id`=".intval($plan_id);
					$db->setQuery($sql);
					$db->execute();
					$price = $db->loadColumn();

					if(isset($price["0"])){
						$return_prices[$course_id] = trim($price["0"]);
					}
					else{
						return array();
					}
				}
			}
		}

		return $return_prices;
	}

	function apiUrl(){
		$model = $this->getModel("guruBuy");
		$configs = $model->getConfigs();
		$saved_key = "";
		$saved_payed_plugins = "";
		$request_key = JFactory::getApplication()->input->get("key", "", "raw");

		if(isset($configs["0"]["secure_key"])){
			$saved_key = trim($configs["0"]["secure_key"]);
		}

		if(isset($configs["0"]["payed_plugins"])){
			$saved_payed_plugins = trim($configs["0"]["payed_plugins"]);
		}

		if($saved_key == ""){
			$return = array("Error"=>JText::_("GURU_NO_SECURE_KEY"));
			die(json_encode($return));
		}
		elseif(trim($request_key) != trim($saved_key)){
			$return = array("Error"=>JText::_("GURU_INVALID_SECURE_KEY"));
			die(json_encode($return));
		}
		elseif(trim($request_key) == trim($saved_key)){
			$db = JFactory::getDbo();
			$and = "";

			$sql = "select `id`, `name` from #__guru_program";
			$db->setQuery($sql);
			$db->execute();
			$courses = $db->loadAssocList("id");

			if(trim($saved_payed_plugins) != ""){
				$and = " and `processor`='".trim($saved_payed_plugins)."'";
			}

			$sql = "select o.*, bc.`expired_date`, c.`firstname`, c.`lastname` from #__guru_order o, #__guru_buy_courses bc, #__guru_customer c where o.`id`=bc.`order_id` and o.`status`='Paid' and o.`userid`=c.`id`".$and;
			$db->setQuery($sql);
			$db->execute();
			$orders = $db->loadAssocList();

			$return = array();

			if(isset($orders) && count($orders) > 0){
				foreach ($orders as $key => $order) {
					$courses_display = array();
					$name = $order["firstname"]." ".$order["lastname"];
					$price = $order["amount_paid"];
					$card_name = $order["card_type"];
					$last_digit = $order["card_digit"];
					$exp_date = $order["expired_date"];

					if($exp_date == "0000-00-00 00:00:00"){
						$exp_date = JText::_("GURU_NEVER");
					}
					else{
						$exp_date = date("Y-m-d", strtotime($exp_date));
					}

					if($price == -1){
						$price = $order["amount"];
					}

					if(isset($order["courses"]) && trim($order["courses"]) != ""){
						$order_courses = trim($order["courses"]);
						$order_courses = explode("|", trim($order_courses));

						if(isset($order_courses) && count($order_courses) > 0){
							foreach ($order_courses as $key => $order_course) {
								if(trim($order_course) != ""){
									$order_course = explode("-", trim($order_course));

									if(isset($courses[$order_course["0"]])){
										$courses_display[] = trim($courses[$order_course["0"]]["name"]);
									}
								}
							}
						}
					}

					$temp_array = array(
						"Name" => $name,
						"Course" => implode(" | ", $courses_display),
						"Price" => $price,
						"Card" => $card_name,
						"Last 4 digit card" => $last_digit,
						"Expiration Date" => $exp_date
					);

					$return[] = $temp_array;
				}
			}

			die(json_encode($return));
		}

		die();
	}

};

?>