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
jimport( 'joomla.html.parameter' );

class guruViewguruBuy extends JViewLegacy {

	function display($tpl = null){
		parent::display($tpl);
	}	
	
	function getPlugins(){
		$return = "";
		$plugin_items = $this->get('PluginList');
		
        $plugins = array();
        foreach($plugin_items as $plugin_item){
			$plugin_params = new JRegistry($plugin_item->params);
			$pluginname = $plugin_params->get($plugin_item->name.'_label');
			$plugins[] = JHTML::_('select.option', $plugin_item->name, $pluginname);
        }
		
		$processor = '';

		if(isset($plan_details['processor'])){
			$processor = $plan_details['processor'];
		}	
		
		if(!empty($plugins)){
			$return = JText::_("GURU_BUY_PAYMENT_METH").': ' . JHTML::_('select.genericlist',  $plugins, 'processor', 'size="1" ', 'value', 'text', $processor);
		} 
		else{
			$return = JText::_('Payment plugins not installed');
		}
		return $return;
	}
	function getPluginsB(){
		$return = "";
		$plugin_items = $this->get('PluginList');
		
        $plugins = array();
        foreach($plugin_items as $plugin_item){
			$plugin_params = new JRegistry($plugin_item->params);
			$pluginname = $plugin_params->get($plugin_item->name.'_label');
			$plugins[] = JHTML::_('select.option', '0', JText::_('GURU_SELEECT_PAYM_GATEWAY'));
			$plugins[] = JHTML::_('select.option', $plugin_item->name, $pluginname);
        }
		$processor = '';
		if(isset($plan_details['processor'])){
			$processor = $plan_details['processor'];
		}	
		if(!empty($plugins)){
			$return = JHTML::_('select.genericlist',  $plugins, 'processor', 'class="inputbox" size="1" ', 'value', 'text', $processor);
		} 
		else{
			$return = JText::_('Payment plugins not installed');
		}
		return $return;
	}
	
	function refreshCoursesFromCart($all_product){
		$user_courses = $this->get("UserCourses");
		
		if(isset($all_product) && isset($user_courses)){
			foreach($all_product as $key=>$value){
				if(isset($value["course_id"]) && isset($user_courses[$value["course_id"]])){
					if($all_product[$key]["plan"] == "buy"){
						$all_product[$key]["value"] = "";
					}
					$all_product[$key]["plan"] = "renew";
				}
			}
		}
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$registry->set('courses_from_cart', $all_product);
		
		return $all_product;
	}
	
	function setPromoTest($total, $counter){
		$old_total = $total;
		$model = $this->getModel("guruBuy");
		$promo_details = $model->getPromo();
		$configs = $model->getConfigs();
		$currency = $configs["0"]["currency"];	
		$currencypos = $configs["0"]["currencypos"];					
		$character = JText::_("GURU_CURRENCY_".$currency);
		
		if(@$promo_details->typediscount == '0') {//use absolute values					
			$difference = $total - (float)$promo_details->discount;
			if($difference < 0){
				$total = 0;
			}
			else{
				$total = $difference;
			}					
			$model = $this->getModel('gurubuy');
			
			if($currencypos == 0){					
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('discount_value', $character." ".($promo_details->discount*$counter));
			}
			else{
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('discount_value', ($promo_details->discount*$counter)." ".$character);
			}

		}
		else{//use percentage
			@$total = ($promo_details->discount / 100)*$total;
			$difference = $old_total - $total;	
			if($difference < 0){
				
				if($currencypos == 0){					
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$registry->set('discount_value', $character." "."0");
				}
				else{
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$registry->set('discount_value', "0"." ".$character);
				}
					
			}
			else{
				$discount = $old_total - $difference;
				
				if($currencypos == 0){		
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$registry->set('discount_value', $character." ".(float)($discount*$counter));
				}
				else{
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$registry->set('discount_value', (float)($discount*$counter)." ".$character);
				}
				
				$total = (float)$old_total - (float)$discount;
			}
		}
		return $total;
	}
	
	function getPromoDiscountCourse($total){
		$old_total = $total;
		$model = $this->getModel("guruBuy");
		$promo_details = $model->getPromo();
		$configs = $model->getConfigs();
		$currency = $configs["0"]["currency"];	
		$currencypos = $configs["0"]["currencypos"];					
		$character = JText::_("GURU_CURRENCY_".$currency);
		$value_to_display = "";
		
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
			@$total = ($promo_details->discount / 100)*$total;
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
}

?>