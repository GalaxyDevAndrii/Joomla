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

require_once(JPATH_BASE.'/../components/com_guru/helpers/helper.php');
$guruHelper = new guruHelper();

$order = $this->order["0"];
$promocodeid = $order["promocodeid"];
$discount_details = array();
$config = guruAdminModelguruOrder::getConfig();
$model_order = new guruAdminModelguruOrder();
$currency = $config->currency;
$currencypos = $config->currencypos;
$character = "GURU_CURRENCY_".$currency;
$subplans = guruAdminModelguruOrder::getPlanExpiration();
$total_final = "";
$totall_discount = 0;

if($currencypos == 0){
	$discount = JText::_($character)." 0.00";
}
else{
	$discount = "0.00 ".JText::_($character);
}
$price = 0;

$courses_list_promo = array("0");

if($promocodeid != "0"){
	
	$courses_list_promo =  $model_order->getCoursesPromo($promocodeid);
}

$courses_array = explode("|",$courses_list_promo["0"]);
$courses_array = array_values(array_filter($courses_array));


?>

<form  id="adminForm" method="post" name="adminForm" action="<?php echo JUri::root() ?>administrator/index.php">
	<div id="contentpane">
		<table cellspacing="0" cellpadding="3" bordercolor="#cccccc" border="1" width="100%" style="border-collapse: collapse;" class="adminlist">
			<caption class="componentheading"><?php echo JText::_("GURU_MYORDERS_ORDER_NUMBER"); ?><?php echo $order["id"] ?>: <?php echo $order["status"]; ?></caption>
		</table>
	<span align="left"><b> <?php echo JText::_("GURU_DATE"); ?> 
		<?php
			if($config->hour_format == 12){
				$date_int = strtotime($order["order_date"]);
				//$date_string = date("Y-m-d h:i:s A", $date_int);
				$date_string = JHTML::_('date', $date_int, 'Y-m-d h:m:s');
				echo $date_string;
			}
			else{
				$date_int = strtotime($order["order_date"]);
				//$date_string = date("Y-m-d H:i:s", $date_int);
				$date_string = JHTML::_('date', $date_int, 'Y-m-d h:m:s');
				echo $date_string;
			}
			
		?></b>
	</span>
	<br><br>
	<table cellspacing="0" cellpadding="3" bordercolor="#cccccc" border="0" width="100%" style="border-collapse: collapse;" class="adminlist">
		<thead style="text-align:left;">	
			<tr>
				<th class="sectiontableheader"></th>
				<th class="sectiontableheader"><?php echo JText::_("GURU_COURSE_NAME"); ?></th>
				<th class="sectiontableheader"><?php echo JText::_("VIEWORDERSQNTY"); ?></th>		
				<th class="sectiontableheader"><?php echo JText::_("GURU_PRICE"); ?></th>
				<th class="sectiontableheader"><?php echo JText::_("GURU_DISCOUNTPROMO"); ?></th>
				<th class="sectiontableheader"><?php echo JText::_("GURU_SUBS_PLAN"); ?></th>
				<th class="sectiontableheader"><?php echo JText::_("GURU_SUB_TOTAL"); ?></th>	
			</tr>
		</thead>
		<tbody>
	<?php
		$ids = "0";
		$id_price = array();
		$course_id_plan = array();
		$id = array();
		$total_courses_price = 0.00;
		if(trim($order["courses"]) != ""){
			$temp1 = explode("|", trim($order["courses"]));
			if(is_array($temp1) && count($temp1) > 0){
				foreach($temp1 as $key=>$value){
					$temp2 = explode("-", $value);
					$id[] = trim($temp2["0"]);
					$course_id_plan[$temp2["0"]] = $temp2["2"];
					$id_price[trim($temp2["0"])]["price"] = trim($temp2["1"]);
				}
			}
		}
		
		$courses = "";
		if(isset($id) && count($id) > 0){
			$courses = guruAdminModelguruOrder::getCourses(implode(",", $id));
		}	
		
		
		if(isset($courses) && is_array($courses) && count($courses) > 0){
			$i = 0;
			$k = 1;			
			foreach($courses as $key=>$value){
				if($promocodeid != "0"){
					$discount_details = $model_order->getDiscountDetails($promocodeid);
					if($discount_details["0"]["typediscount"] == "0"){
						$discount = JText::_($character)." ".$discount_details["0"]["discount"];
					}
					else{
						$discount = $discount_details["0"]["discount"]."%";
					}
				}
			
				$price = $id_price[$value["id"]]["price"];
				$total_courses_price += (float)$price;
				
				
	?>			
			<tr class="<?php echo "row".$i; ?>">
				<td><?php echo $k; ?></td>
				<td><?php echo $value["name"]; ?></td>
				<td>1</td>						
				<td><?php 
						if($currencypos == 0){
							echo JText::_($character)." ".$guruHelper->displayPrice(round(((float)$price), 2));
						}
						else{
							echo $guruHelper->displayPrice(round(((float)$price), 2))." ".JText::_($character); 
						} ?>
                </td>
				<td><?php
						if(in_array($value["id"], $courses_array)){
							$promo_discount_percourse = $model_order->getPromoDiscountCourses($price, $promocodeid );
							
							if($currencypos == 0){
								echo JText::_($character)." ".$guruHelper->displayPrice($promo_discount_percourse)." (".$discount.")";
							}
							else{
								echo $guruHelper->displayPrice($promo_discount_percourse)." ".JText::_($character)." (".$discount.")";
							} 
							
						}
						else{
							if($currencypos == 0){
								echo JText::_($character)." "."0 (0"."%".")"; 
							}
							else{
								echo "0 (0"."%".")"." ".JText::_($character); 
							} 
						}
					?>
                </td>
				<td><?php if(isset($subplans[$course_id_plan[$value["id"]]]) && $subplans[$course_id_plan[$value["id"]]] > 0){ echo $subplans[$course_id_plan[$value["id"]]]["name"]; }?></td>
				<td><?php
					if($promocodeid != "0"){
						if(in_array($value["id"], $courses_array) || count($courses_array) == 0){
							$model_orders = new guruAdminModelguruOrder();
							$total_new = $model_orders->getPromoDiscountCourse($price,$promocodeid);
							$promo_discount_percourse = $model_orders->getPromoDiscountCourses($price, $promocodeid );
							$totall_discount += $promo_discount_percourse;
						}
						else{
							$total_new = $price;
						}	
					}
					else{
						$total_new = $price;
						if(isset($discount_details["0"]) && $discount_details["0"]["typediscount"] == "0"){
							$totall_discount = JText::_($character)." "."0";
						}
						else{
							$totall_discount = "0"."%";
						}
					}
						
					if($currencypos == 0){
						echo JText::_($character)." ".$guruHelper->displayPrice(round(((float)$total_new)), 2); 
					}
					else{
						echo $guruHelper->displayPrice(round(((float)$total_new), 2))." ".JText::_($character); 
					}
					
					@$total_final += $total_new;
				
				 ?></td>
			</tr>			
			<?php
					$i = 1-$i;
					$k++;
				}//foreach
			}//if
			?>	
			<tr style="border-style: none;">
				<td colspan="7" style="border-style: none;"><hr></td>
			</tr>
			
			<tr>
				<td colspan="5"></td>
				<td style="font-weight: bold; text-align:right;"><?php echo JText::_("GURU_TOTAL"); ?></td>
				<td><?php 
				if($currencypos == 0){
					echo JText::_($character)." ".$guruHelper->displayPrice($total_courses_price);
				}
				else{
					echo $guruHelper->displayPrice($total_courses_price)." ".JText::_($character);
				}
				//JText::_($character)." ".$order["amount"]; ?></td>
			</tr>
			<tr>
				<td colspan="5"></td>
				<td style="font-weight: bold; text-align:right;"><?php echo JText::_("GURU_DISCOUNTPROMO"); ?></td>
				<td>
					<?php 
						if($currencypos == 0){
							echo JText::_($character)." ".$guruHelper->displayPrice($totall_discount);
						}
						else{
							echo $guruHelper->displayPrice($totall_discount)." ".JText::_($character);
						}
					?>
            </td>
			</tr>
			<tr>
				<td colspan="5"></td>
					<td style="font-weight: bold; text-align:right;"><?php echo JText::_("GURU_FINAL_TOTAL"); ?></td>
				<td>
					<?php
						if($currencypos == 0){
							echo JText::_($character)." ".$guruHelper->displayPrice($total_final);
						}
						else{
							echo $guruHelper->displayPrice($total_final)." ".JText::_($character);
						}
					?>
				</td>
			</tr>
		</tbody>			
	</table>	
	</div>
	
	<input type="hidden" value="com_guru" name="option" />
	<input type="hidden" value="" name="task" />
	<input type="hidden" value="0" name="boxchecked" />
	<input type="hidden" value="guruOrders" name="controller" />
</form>