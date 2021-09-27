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

$database = JFactory::getDBO();
$task = JFactory::getApplication()->input->get("task", "");

switch($task){
	case "getplans" : getPlainsByCourseIDSelectHTML(); 
						break;
	case "getcoursecost" : getCourseCost();
						break;
	case "setpromo" : setPromo();
						break;
	case "setrenew" : setRenew();
						break;	
	case "checkExistingUser" : {
		checkExistingUser();
		break;
	}
	case "checkCommissionPlan" : {
		checkCommissionPlan();
		break;
	}													
}

function checkExistingUser(){
	$database = JFactory::getDBO();;
	$username = JFactory::getApplication()->input->get("username", "");
	$email = JFactory::getApplication()->input->get("email", "");
	$id = JFactory::getApplication()->input->get("id", "0");
	
	if(intval($id) == 0){// new user
		$sql = "select count(*) from #__users where email='".addslashes(trim($email))."'";
		$database->setQuery($sql);
		$result = $database->loadResult();
		if($result > 0){
			echo "111";
			return true;
		}
		
		$sql = "select count(*) from #__users where username='".addslashes(trim($username))."'";
		$database->setQuery($sql);
		$result = $database->loadResult();
		if($result > 0){
			echo "222";
			return true;
		}
		die();
	}
	elseif(intval($id) != 0){
		$sql = "select username, email from #__users where id=".intval($id);
		$database->setQuery($sql);
		$result = $database->loadAssocList();
		$old_username = $result["0"]["username"];
		$old_email = $result["0"]["email"];
		
		if($username != $old_username){
			$sql = "select count(*) from #__users where username='".addslashes(trim($username))."'";
			$database->setQuery($sql);
			$result = $database->loadResult();
			if($result > 0){
				echo "222";
				return true;
			}
			die();
		}
		
		if($email != $old_email){
			$sql = "select count(*) from #__users where email='".addslashes(trim($email))."'";
			$database->setQuery($sql);
			$result = $database->loadResult();
			if($result > 0){
				echo "111";
				return true;
			}
			die();
		}
	}
}

function checkCommissionPlan(){
	$db = JFactory::getDBO();
	$id = JFactory::getApplication()->input->get("id", "0");
	$new_plan = JFactory::getApplication()->input->get("new_plan", "0");
	$old_plan = JFactory::getApplication()->input->get("old_plan", "0");
	
	$sql = "select author from #__guru_program where (author=".intval($id)." OR author like '%|".intval($id)."|%')";
	$db->setQuery($sql);
	$list = $db->loadColumn();
	
	if(isset($list) && count($list) > 0){
		foreach($list as $key=>$value){
			$authors = explode("|", $value);
			$authors = array_filter($authors);
			
			if(isset($authors) && count($authors) > 0){
				foreach($authors as $key_author=>$author_id){
					if($author_id != $id){
						$sql = "select commission_id from #__guru_authors where userid=".intval($author_id);
						$db->setQuery($sql);
						$commission_id = $db->loadColumn();
						$commission_id = @$commission_id["0"];
						
						if($commission_id != $new_plan){
							echo '000';
							return true;
						}
					}
				}
			}
		}
		echo '111';
		return true;
	}
	echo '111';
	return true;
}

function setRenew(){
	require_once(JPATH_BASE.'/../components/com_guru/helpers/helper.php');
	$guruHelper = new guruHelper();

	$db = JFactory::getDBO();
	$gen_code = JFactory::getApplication()->input->get("gen_number");
	$course_id = JFactory::getApplication()->input->get("course_id");
	$option = JFactory::getApplication()->input->get("option_req");
	
	$sql = "select sb.name, pr.plan_id, pr.price, pr.default from #__guru_program_renewals pr, #__guru_subplan sb where sb.id=pr.plan_id and pr.product_id=".intval($course_id);
	$db->setQuery($sql);
	$plans = $db->loadAssocList();

	$component_configs = getComponentConfigs();
	$currency = $component_configs["0"]["currency"];
	$character = (trim(getCharacterCurrency($currency)));

	if((!isset($plans) || count($plans) <= 0) || $option == "new"){
		$sql = "select sb.name, pr.plan_id, pr.price, pr.default from #__guru_program_plans pr, #__guru_subplan sb where sb.id=pr.plan_id and pr.product_id=".intval($course_id);
		$db->setQuery($sql);
		$plans = $db->loadAssocList();
	}
	
	$hidden_value = "0.00";
	
	$select = '<select name="licences_select" id="licences_select'.$gen_code.'" class="inputbox" size="1" onchange="javascript:changeAmount(\''.$gen_code.'\');">';
	foreach($plans as $key=>$value){
		$selected = '';
		if($value["default"] == "1"){
			$selected = 'selected="selected"';
			$hidden_value = $value["price"];
		}
		$select .= '<option data-price="'.$value['price'].'" value="'.$value["plan_id"].'" '.$selected.'>'.$value["name"]." - ".$character.$guruHelper->displayPrice($value["price"]).'</option>';
	}
	$select .= "</select>";
	$select .= '<input type="hidden" id="hidden_licenses_'.$gen_code.'" name="hidden_licenses['.$gen_code.']" value="'.$hidden_value.'" />';
	$select .= '<input type="hidden" id="hidden_plan_'.$gen_code.'" name="hidden_plan['.$gen_code.']" value="'.$value['plan_id'].'" />';
	echo $select;
}

function setPromo(){
	$promo_code = JFactory::getApplication()->input->get("promocode", "");
	$value = JFactory::getApplication()->input->get("value");
	$value = floatval($value);
	$count_s = JFactory::getApplication()->input->get("count", "0");
	$total_discount = 0;

	if($promo_code == "none" || $promo_code == ""){
		echo "0";
		return;
	}
	else{
		$database = JFactory::getDBO();;		
		$sql = "select * from #__guru_promos where code='".addslashes(trim($promo_code))."'";
		$database->setQuery($sql);
		$result = $database->loadAssocList();
		
		$sql1 = "SELECT courses_ids FROM #__guru_promos where code='".addslashes(trim($promo_code))."'";
		$database->setQuery($sql1);
		$courses = $database->loadColumn();
		$courses_array = explode("||",$courses["0"]);
		
		for ($i =1; $i<$count_s; $i++){
			$s = JFactory::getApplication()->input->get("s".$i, "");
			$course_id_price = explode("-",$s);
			$course_id = $course_id_price[0];
			$price = $course_id_price[1];

			if(in_array($course_id, $courses_array)){
				$discount = $result["0"]["discount"];
				$type = $result["0"]["typediscount"];
				
				if($type == '0') {//use absolute values		
					$value = $price - (float)$discount;
					if ($value < 0) {
						$value = $price;
					}
				}
				else{//use percentage
					$value = ($discount / 100)*$price;
				}

				$total_discount += $value;
			}
		}
	}

	echo $total_discount;

	return true;
}

function getCourseCost(){
	$database = JFactory::getDBO();
	$course_id = JFactory::getApplication()->input->get("course_id");
	$sql = "SELECT pp.price FROM #__guru_program_plans pp, #__guru_subplan s WHERE pp.product_id = ".$course_id." and pp.plan_id=s.id and pp.default=1";
	$database->setQuery($sql);
	$result = $database->loadResult();
	echo $result;
}

function getComponentConfigs(){
	$database = JFactory::getDBO();;
	$sql = "select * from #__guru_config";
	$database->setQuery($sql);
	$result = $database->loadAssocList();
	return $result;
}

function getCharacterCurrency($currency){
	$character = "$";
	switch($currency){
		case "INR" : {
			$character = "INR";
			break;
		}
		case "MXN" : {
			$character = "$";
			break;
		}
		case "USD" : {
			$character = "$";
			break;
		}
		case "AUD" : {
			$character = "$";
			break;
		}
		case "CAD" : {
			$character = "$";
			break;
		}
		case "CHF" : {
			$character = "Fr";
			break;
		}
		case "CZK" : {
			$character = "Kc";
			break;
		}
		case "DKK" : {
			$character = "kr";
			break;
		}
		case "EUR" : {
			$character = "€";
			break;
		}
		case "GBP" : {
			$character = "£";
			break;
		}
		case "HKD" : {
			$character = "$";
			break;
		}
		case "HUF" : {
			$character = "Ft";
			break;
		}
		case "JPY" : {
			$character = "¥";
			break;
		}
		case "NOK" : {
			$character = "kr";
			break;
		}
		case "HZD" : {
			$character = "$";
			break;
		}
		case "PLN" : {
			$character = "zl";
			break;
		}
		case "SEK" : {
			$character = "kr";
			break;
		}
		case "SGD" : {
			$character = "$";
			break;
		}
		case "BRL" : {
			$character = "R$";
			break;
		}
	}
	return $character;
}

function getPlainsByCourseIDSelectHTML(){
	require_once(JPATH_BASE.'/../components/com_guru/helpers/helper.php');
	$guruHelper = new guruHelper();

	$database = JFactory::getDBO();
	$course_id = JFactory::getApplication()->input->get("course_id");
	$gen_number = JFactory::getApplication()->input->get("gen_id");
	$sql = "SELECT pp.plan_id, pp.price, pp.default, s.name FROM #__guru_program_plans pp, #__guru_subplan s WHERE pp.product_id = ".$course_id." and pp.plan_id=s.id order by s.ordering";
	$database->setQuery($sql);
	$result = $database->loadAssocList();
	
	$hidden_value = "0.00";
	$currencyposition = "";
	
	$component_configs = getComponentConfigs();
	$currency = $component_configs["0"]["currency"];
	$currencypos = $component_configs["0"]["currencypos"];
	
	$character = (trim(getCharacterCurrency($currency)));
	if($currencypos == 0){
		$currencyposition1 = $character ;
	}
	else{
		$currencyposition2 = $character ;
	}

	$html = '<select onchange="javascript:changeAmount(\''.$gen_number.'\');" size="1" class="inputbox" id="licences_select'.$gen_number.'" name="licences_select">';
	if(isset($result) && is_array($result) && count($result) > 0){
		foreach($result as $key=>$value){
			$selected = "";
			if($value["default"] == "1"){
				$selected = 'selected="selected"';
				$hidden_value = $value["price"];
			}
			$html .= '<option data-price="'.$value['price'].'" value="'.$value["plan_id"].'" '.$selected.' >'.$value["name"]." - ".@$currencyposition1." ".$guruHelper->displayPrice($value["price"]).@$currencyposition2.'</option>';
		}
	}
	else{
		$html .= '<option value="none">none</option>';
	}
	$html .= '</select>';
	$html .= '<input type="hidden" id="hidden_licenses_'.$gen_number.'" name="hidden_licenses['.$gen_number.']" value="'.$hidden_value.'" />';
	$html .= '<input type="hidden" id="hidden_plan_'.$gen_number.'" name="hidden_plan['.$gen_number.']" value="'.$value['plan_id'].'" />';
	echo $html;
}
?>	