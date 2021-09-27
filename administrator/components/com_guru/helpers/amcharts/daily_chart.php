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

$db = JFactory::getDBO();
$sql = "SELECT amount, amount_paid, currency, order_date FROM #__guru_order WHERE status='Paid' order by order_date ";
$db->setQuery($sql);
$db->execute();
$result = $db->loadAssocList();

if(!isset($result) || count($result) == 0){
	$sql = "select currency from #__guru_config";
	$db->setQuery($sql);
	$db->execute();
	$currency = $db->loadColumn();
	$currency = @$currency["0"];
	$result = array("0"=>array("amount"=>"0.1", "amount_paid"=>"-1", "currency"=>$currency, "order_date"=>date("Y-m-d H:i:s")));
}

if(isset($result) && count($result) > 0){
	$total_values = array();
	foreach($result as $key=>$value){
		if($value["order_date"] != "0000-00-00 00:00:00"){
			$price = $value["amount"];
			if($value["amount_paid"] != -1){
				$price = $value["amount_paid"];
			}
			
			if(isset($total_values[$value["currency"]][strtotime($value["order_date"])."000"])){
				$total_values[$value["currency"]][strtotime($value["order_date"])."000"] += $price;
			}
			else{
				$total_values[$value["currency"]][strtotime($value["order_date"])."000"] = $price;
			}
		}
	}
	
	if(isset($total_values) && count($total_values) > 0){
		echo '<script type="text/javascript" language="javascript">'."\n";
		echo '$(function() {'."\n";
		$k = 1;
		$params_array = array();
		
		foreach($total_values as $key=>$value){
			$temp_array = array();
			foreach($value as $time=>$price){
				$temp_array[] = '['.$time.','.$price.']';
			}
			$params_array[] = '{ data: variable_'.$k.', label: "Price in '.$key.'" }';
			echo 'var variable_'.$k.' = ['.implode(", ", $temp_array).'];'."\n";
		 	
			$k ++;
		}
		
		echo 'function euroFormatter(v, axis) {
				return v.toFixed(axis.tickDecimals) + "&euro;";
			  }'."\n";
		
		echo 'function doPlot(position) {
					jQuery.plot("#placeholder", [
						'.implode(", \n", $params_array).'
					], {
						xaxes: [ { mode: "time" } ],
						yaxes: [ { min: 0 }, {
							// align if we are to the right
							alignTicksWithAxis: position == "right" ? 1 : null,
							position: position,
							tickFormatter: euroFormatter
						} ],
						legend: { position: "sw" }
					});
				}
				
				doPlot("right");';	  
			  
		echo '});'."\n";
		echo '</script>';
	}
}
?>