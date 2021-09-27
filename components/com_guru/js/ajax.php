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
define( '_JEXEC', 1 );
defined( '_JEXEC' ) or die( 'Restricted access' );
define('JPATH_BASE', substr(substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "components")),0,-1));


define( 'DS', DIRECTORY_SEPARATOR );
$parts = explode(DS, JPATH_BASE);
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'defines.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'framework.php' );
//require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'methods.php');
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'configuration.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'adapter.php');
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'database.php');
//require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'mysql.php');
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');

$config = new JConfig();


//$database = new JoomlaDatabase($options);
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
	case "checkExistingUserU" : {
		checkExistingUserU();
		break;
	}
	case "checkExistingUserE" : {
		checkExistingUserE();
		break;
	}															
}

function checkExistingUserU(){
	global $database;
	$username = JFactory::getApplication()->input->get("username", "");
	$email = JFactory::getApplication()->input->get("email", "");
	$id = JFactory::getApplication()->input->get("id", "0");
	if(intval($id) == 0){// new user
		$sql = "select count(*) from #__users where username='".addslashes(trim($username))."'";
		$database->setQuery($sql);
		$database->execute();
		$result = $database->loadResult();

		if($result > 0){
			echo "222";
			return true;
		}
		else{
			echo "333";
			return true;
		}
		die();
	}
	elseif(intval($id) != 0){
		$sql = "select username, email from #__users where id=".intval($id);
		$database->setQuery($sql);
		$database->execute();
		$result = $database->loadAssocList();
		$old_username = $result["0"]["username"];
		$old_email = $result["0"]["email"];
		if($username != $old_username){
			$sql = "select count(*) from #__users where username='".addslashes(trim($username))."'";
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadResult();
			if($result > 0){
				echo "222";
				return true;
			}
			die();
		}
	}
}
function checkExistingUserE(){	
	global $database;
	$username = JFactory::getApplication()->input->get("username", "");
	$email = JFactory::getApplication()->input->get("email", "");
	$id = JFactory::getApplication()->input->get("id", "0");
	if(intval($id) == 0){// new user
		$sql = "select count(*) from #__users where email='".addslashes(trim($email))."'";
		$database->setQuery($sql);
		$database->execute();
		$result = $database->loadResult();
		if($result > 0){
			echo "111";
			return true;
		}
		else{
			echo "222";
			return true;
		}
		
	}
	elseif(intval($id) != 0){
		$sql = "select username, email from #__users where id=".intval($id);
		$database->setQuery($sql);
		$database->execute();
		$result = $database->loadAssocList();
		$old_username = $result["0"]["username"];
		$old_email = $result["0"]["email"];
		if($email != $old_email){
			$sql = "select count(*) from #__users where email='".addslashes(trim($email))."'";
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadResult();
			if($result > 0){
				echo "111";
				return true;
			}
			die();
		}
	}
}


function setRenew(){
	$db = JFactory::getDBO();
	$gen_code = JFactory::getApplication()->input->get("gen_number");
	$course_id = JFactory::getApplication()->input->get("course_id");
	$option = JFactory::getApplication()->input->get("option");
	
	$sql = "select sb.name, pr.price, pr.default from #__guru_program_renewals pr, #__guru_subplan sb where sb.id=pr.plan_id and pr.product_id=".intval($course_id);
	$db->setQuery($sql);
	$db->execute();
	$plans = $db->loadAssocList();

	$component_configs = getComponentConfigs();
	$currency = $component_configs["0"]["currency"];
	$character = utf8_encode(trim(getCharacterCurrency($currency)));

	if((!isset($plans) || count($plans) <= 0) || $option == "new"){
		$sql = "select sb.name, pr.price, pr.default from #__guru_program_plans pr, #__guru_subplan sb where sb.id=pr.plan_id and pr.product_id=".intval($course_id);
		$db->setQuery($sql);
		$db->execute();
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
		$select .= '<option value="'.$value["price"].'" '.$selected.'>'.$value["name"]." - ".$character.$value["price"].'</option>';
	}
	$select .= "</select>";
	$select .= '<input type="hidden" id="hidden_licenses_'.$gen_code.'" name="hidden_licenses['.$gen_code.']" value="'.$hidden_value.'" />';
	echo $select;
}

function setPromo(){
	$promo_code = JFactory::getApplication()->input->get("promocode", "");
	$value = JFactory::getApplication()->input->get("value");
	$value = floatval($value);
	if($promo_code == "none" || $promo_code == ""){
		echo "-1";
	}
	else{
		global $database;		
		$sql = "select * from #__guru_promos where code='".addslashes(trim($promo_code))."'";
		$database->setQuery($sql);
		$database->execute();
		$result = $database->loadAssocList();
		if(isset($result) && count($result) > 0){
			$discount = $result["0"]["discount"];
			$type = $result["0"]["typediscount"];
			if($type == "0"){
				$value = $discount;
			}
			else{
				$value = floatval($discount/100)*$value;
			}
		}
		else{
			echo "-1";
		}		
	}
	echo $value;
	return true;
}

function getCourseCost(){
	global $database;
	$course_id = JFactory::getApplication()->input->get("course_id");
	$sql = "SELECT pp.price FROM #__guru_program_plans pp, #__guru_subplan s WHERE pp.product_id = ".$course_id." and pp.plan_id=s.id and pp.default=1";
	$database->setQuery($sql);
	$database->execute();
	$result = $database->loadResult();
	echo $result;
}

function getComponentConfigs(){
	global $database;
	$sql = "select * from #__guru_config";
	$database->setQuery($sql);
	$database->execute();
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
	global $database;
	$course_id = JFactory::getApplication()->input->get("course_id");
	$gen_number = JFactory::getApplication()->input->get("gen_id");
	$sql = "SELECT pp.plan_id, pp.price, pp.default, s.name FROM #__guru_program_plans pp, #__guru_subplan s WHERE pp.product_id = ".$course_id." and pp.plan_id=s.id order by s.ordering";
	$database->setQuery($sql);
	$database->execute();
	$result = $database->loadAssocList();
	
	$hidden_value = "0.00";
	$currencyposition = "";
	
	$component_configs = getComponentConfigs();
	$currency = $component_configs["0"]["currency"];
	$currencypos = $component_configs["0"]["currencypos"];
	
	$character = utf8_encode(trim(getCharacterCurrency($currency)));
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
			$html .= '<option value="'.$value["price"].'" '.$selected.' >'.$value["name"]." - ".@$currencyposition1." ".$value["price"].@$currencyposition2.'</option>';
		}
	}
	else{
		$html .= '<option value="none">none</option>';
	}
	$html .= '</select>';
	$html .= '<input type="hidden" id="hidden_licenses_'.$gen_number.'" name="hidden_licenses['.$gen_number.']" value="'.$hidden_value.'" />';
	echo $html;
}
?>	