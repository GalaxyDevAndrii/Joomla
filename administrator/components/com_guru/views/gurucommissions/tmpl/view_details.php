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

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_BASE.'/../components/com_guru/helpers/helper.php');
$guruHelper = new guruHelper();

$teachers_names = $this->teachers;
$guruAdminModelguruCommissions = new guruAdminModelguruCommissions();
@$teachers_details_list = $this->teachers_details_list;
$page = JFactory::getApplication()->input->get("page", "", "raw");
$course_id = JFactory::getApplication()->input->get("course_id", "", "raw");
$author_id = JFactory::getApplication()->input->get("cid", "0", "raw");
$config = $this->config;
$date = JFactory::getApplication()->input->get("date", "", "raw");
$id = JFactory::getApplication()->input->get("id", "0", "raw");
$p = JFactory::getApplication()->input->get("p", "0", "raw");
$orders = JFactory::getApplication()->input->get("orders", "", "raw");
$datetype  = $config->datetype;
$currencyc = JFactory::getApplication()->input->get("currencyc", "", "raw");
$b = JFactory::getApplication()->input->get("block", "", "raw");
$sum_price = 0;
$sum_price_paid = 0;
$sum_commission = 0;
$model=$this->model;

$data_post = JFactory::getApplication()->input->post->getArray();

if($page == "paid"){
	$text = JText::_('GURU_O_PAID');
	$details = $model->getPaidDetails( $author_id["0"], $orders, $currencyc);
	$n = count($details);
}
elseif($page == "pending"){
	$text = JText::_('GURU_AU_PENDING');
	$course_name = $model->getCourseName($course_id);
	$details = $model->getPendingDetails($course_id, $author_id["0"], $pageto ="pending");
	$n = count($details);
}
elseif($page == "history1"){
	$text = JText::_('GURU_O_PAID');
	$details = $model->getPendingDetails($course_id, $author_id["0"], $pageto ="paid");
	$n = count($details);
}
elseif($page == "history"){
	$text = JText::_('GURU_O_PAID');
	$details = $model->getPendingDetails($course_id, $author_id["0"], $pageto ="paid");
	$n = count($details);
}
$config = $this->config;
$currencypos = $config->currencypos;
$character = "GURU_CURRENCY_".$config->currency;
$doc =JFactory::getDocument();
$doc->addScript(JURI::root().'/components/com_guru/js/sorttable.js');

$pagination = $this->getDetailsPagination();

?>
<style>
	div.modal1 {
			z-index: 10053;
			width:1000px;
	}
	.modal-backdrop, .modal-backdrop.fade.in {
		opacity: 0.4 !important;
	}
	div.modal-header{
		padding :5px;
	}
	.btn-back {
		margin-top: -24px !important;
		margin-right: 7px !important;
	}
</style>
<div style="padding:30px;">
 <span id="message_lib" class="alert" style="display:none; margin-top: 5px;">
    <a href='http://www.ijoomla.com/redirect/guru/mpdf.htm' target="_blank">
        <?php echo "1. ".JText::_("GURU_DOWNLOAD_MPDF1"); ?>
    </a>
    <br />
    <?php echo "2. ".JText::_("GURU_DOWNLOAD_MPDF2"); ?>
    <br />
    <?php echo "3. ".JText::_("GURU_DOWNLOAD_MPDF3"); ?>
</span>
<?php 
	if($page == "history1" && $p !=1){?>
        <button class="btn pull-right btn-back" onclick="window.history.back();"><?php echo JText::_("GURU_BACK"); ?></button>
<?php 
	}
?>                    
<h2 class="pull-left"><?php echo JText::_('GURU_COMMISSIONS')." ".">"." ".$text." ".">"." ".$teachers_names; ?></h2>
<br/><br/><br/>
<?php
	if($page == "pending"){
	?>
		<h4><?php echo $course_name[0]; ?></h4>
	<?php
	}
	elseif($page == "history"){
	?>
    	<?php 
			if($config->hour_format == 12){
			$format = " Y-m-d h:i:s A ";
				switch($datetype){
					case "d-m-Y H:i:s": $format = "d-m-Y h:i:s A";
						  break;
					case "d.m.Y H:i": $format = "d.m.Y h:i A"; 
					  	break;
					case "d/m/Y H:i:s": $format = "d/m/Y h:i:s A"; 
						  break;
					case "m-d-Y H:i:s": $format = "m-d-Y h:i:s A"; 
						  break;
					case "m/d/Y H:i:s": $format = "m/d/Y h:i:s A"; 
						  break;
					case "Y-m-d H:i:s": $format = "Y-m-d h:i:s A"; 
						  break;
					case "Y/m/d H:i:s": $format = "Y/m/d h:i:s A"; 
						  break;
					case "d-m-Y": $format = "d-m-Y"; 
						  break;
					case "d/m/Y": $format = "d/m/Y"; 
						  break;
					case "m-d-Y": $format = "m-d-Y"; 
						  break;
					case "m/d/Y": $format = "m/d/Y"; 
						  break;
					case "Y-m-d": $format = "Y-m-d"; 
						  break;
					case "Y/m/d": $format = "Y/m/d";	
						  break;	  	  	  	  	  	  	  	  	  	  
				}
				$date_int = strtotime($date);
				$date_string = JHTML::_('date', $date_int, $format );
			}
			else{
				$date_int = strtotime($date);
				//$date_string = date("Y-m-d H:i:s", $date_int);
				$format = "Y-m-d H:M:S";
				switch($datetype){
					case "d-m-Y H:i:s": $format = "d-m-Y H:i:s";
						  break;
					case "d.m.Y H:i": $format = "d.m.Y H:i"; 
					  break;
					case "d/m/Y H:i:s": $format = "d/m/Y H:i:s"; 
						  break;
					case "m-d-Y H:i:s": $format = "m-d-Y H:i:s"; 
						  break;
					case "m/d/Y H:i:s": $format = "m/d/Y H:i:s"; 
						  break;
					case "Y-m-d H:i:s": $format = "Y-m-d H:i:s"; 
						  break;
					case "Y/m/d H:i:s": $format = "Y/m/d H:i:s"; 
						  break;
					case "d-m-Y": $format = "d-m-Y"; 
						  break;
					case "d/m/Y": $format = "d/m/Y"; 
						  break;
					case "m-d-Y": $format = "m-d-Y"; 
						  break;
					case "m/d/Y": $format = "m/d/Y"; 
						  break;
					case "Y-m-d": $format = "Y-m-d"; 
						  break;
					case "Y/m/d": $format = "Y/m/d";		
						  break;  	  	  	  	  	  	  	  	  	  
				}
				$date_string = JHTML::_('date', $date_int, $format);
			}	
		
		?>
    	<h4><?php echo JText::_('GURU_PAID_ON')." ".$date_string; ?></h4>
    <?php
	}
	elseif($page == "paid"){
	?>
    	<h4><?php echo JText::_('GURU_PAYEMENT_H'); ?></h4>
        <table align="right" style="margin-top:-40px; text-align: right;">
        	<tr>
            	<td>
            		<div class="btn-group">
                    <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="border-width:0px !important;"><?php echo JText::_("GURU_EXPORT"); ?>
                        <span class="caret" style="margin-left: 0; margin-top: 10px;"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" onclick="document.getElementById('message_lib').style.display='none'; document.adminForm.export.value='csv'; document.adminForm.submit();">CSV</a>
                        </li>
                    </ul>
                </div>
                </td>
            </tr>
        </table>
    <?php
	}
?>
<form  id="adminForm" name="adminForm" method="post">
<?php
	if($page != "paid"){
?>
        <table style="width: 100%;" cellpadding="5" cellspacing="5" bgcolor="#FFFFFF">
            <tr>
                <td>
                    <input type="text" name="search_text" value="<?php if(isset($data_post['search_text'])) echo $data_post['search_text'];?>" />
                    <input class="btn btn-primary" onclick="document.getElementById('export').value=''" type="submit" name="submit_search" value="<?php echo JText::_('GURU_SEARCHTXT');?>" />
                </td>
                <td>
                    <?php
                        $filter_promocode = JFactory::getApplication()->input->get("filter_promocode", "0");
                        $promos = $this->getAllPromos();
                    ?>
                    <select name="filter_promocode" class="inputbox" onchange="document.getElementById('export').value=''; document.adminForm.submit();">
                        <option value="0" <?php if($filter_promocode == "0"){ echo 'selected="selected"'; }?>><?php  echo JText::_("GURU_SELECT_PROMO"); ?></option>
                        <?php
                            if(isset($promos) && count($promos) > 0){
                                foreach($promos as $key=>$promo){
                        ?>
                                    <option value="<?php echo $promo["id"]; ?>" <?php if($promo["id"] == $filter_promocode){echo 'selected="selected"';} ?> ><?php echo $promo["code"]; ?></option>
                        <?php
                                }
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <?php
                        $filter_paymentmethod = JFactory::getApplication()->input->get("filter_paymentmethod", "0");
                    ?>
                    <select name="filter_paymentmethod" class="inputbox" onchange="document.getElementById('export').value=''; document.adminForm.submit();">
                        <option value="" <?php if($filter_paymentmethod == ""){ echo 'selected="selected"'; }?>><?php  echo JText::_("GURU_SELECT_PAYEMET_METHOD"); ?></option>
                        <option value="payauthorize"  <?php if ($filter_paymentmethod == "payauthorize"){ echo ' selected="selected" '; }?>><?php  echo JText::_("GURU_PAYAUTHORIZE"); ?></option>
                        <option value="paypaypal"  <?php if ($filter_paymentmethod == "paypaypal"){ echo ' selected="selected" '; }?>><?php echo JText::_("GURU_PAYPAL"); ?></option>
                        <option value="offline"  <?php if ($filter_paymentmethod == "offline"){ echo ' selected="selected" '; }?>><?php echo JText::_("GURU_OFFLINE"); ?></option>
                    </select>
                </td>
                <td>
                    <?php
                        $filter_commission_plan = JFactory::getApplication()->input->get("filter_commission_plan", "0");
                        $commissions_plans = $this->getAllCommissions();
                    ?>
                    <select name="filter_commission_plan" class="inputbox" onchange="document.getElementById('export').value=''; document.adminForm.submit();">
                        <option value="0" <?php if($filter_commission_plan == "0"){ echo 'selected="selected"'; }?>><?php  echo JText::_("GURU_SELECT_COMMISSION_PLAN"); ?></option>
                        <?php
                            if(isset($commissions_plans) && count($commissions_plans) > 0){
                                foreach($commissions_plans as $key=>$commissions_plan){
                        ?>
                                    <option value="<?php echo $commissions_plan["id"]; ?>" <?php if($commissions_plan["id"] == $filter_commission_plan){echo 'selected="selected"';} ?> ><?php echo $commissions_plan["commission_plan"]; ?></option>
                        <?php
                                }
                            }
                        ?>
                    </select>
                </td>
                <td class="text-right">
                   <div class="btn-group">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="border-width:0px !important;"><?php echo JText::_("GURU_EXPORT"); ?>
                            <span class="caret" style="margin-left: 0; margin-top: 10px;"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" onclick="document.getElementById('message_lib').style.display='none'; document.adminForm.export.value='csv'; document.adminForm.submit();">CSV</a>
                            </li>
                        </ul>
                    </div>
                </td>
            <tr>
        </table>
<?php 
}
?>        
<table class="sortable table table-striped adminlist table-bordered">
    <thead>
        <tr>
            <th>
            	#
            </th>
            <th>
            	 <?php echo JText::_('GURU_ID');?><i class="icon-menu-2"></i>
            </th>
            <th>
            	 <?php echo JText::_('GURU_DATE');?><i class="icon-menu-2"></i>
            </th>
            <?php if($page != "paid"){
			?>
                    <th class="sorttable_numeric">
                    <?php if($page != "paid"){
                            echo JText::_('GURU_PRICE');
                          }
                          else{
                            echo JText::_('VIEWORDERSAMOUNTPAID');
                          }
                    ?><i class="icon-menu-2"></i>
                    </th class="sorttable_numeric">
                    
                    <th>
                         <?php echo JText::_('GURU_O_PAID');?><i class="icon-menu-2"></i>
                    </th>
                    <th>
                         <?php echo JText::_('GURU_TREECUSTOMERS');?><i class="icon-menu-2"></i>
                    </th>
                    <th>
                         <?php echo JText::_('GURU_ORDPAYMENTMETHOD');?><i class="icon-menu-2"></i>
                    </th>
                    <th>
                         <?php echo JText::_('GURU_PROMOCODE')."(% /".JText::_('GURU_VALUE').")";?><i class="icon-menu-2"></i>
                    </th>
                    <th>
                         <?php echo JText::_('GURU_COMMISSION_PLAN');?><i class="icon-menu-2"></i>
                    </th>
               <?php }?> 
                <th class="sorttable_numeric">
                	<?php if($page != "paid"){
						echo JText::_('GURU_COMMISSIONS');
					  }
					  else{
					  	echo JText::_('VIEWORDERSAMOUNTPAID');
					  }
					?><i class="icon-menu-2"></i>
                </th> 
                <?php if($page == "paid"){
				?>  
                    <th>
                        <?php echo JText::_('GURU_VIEW_DETAILS');?>
                    </th> 
                   <?php 
                   }
			   ?>        
        </tr>
    </thead>
    
    <tbody>
    <?php
		if($page == 'paid'){
			$inc = 1;
			$total_sum_per_currency = array();
			
			foreach($details as $key=>$value){
				if(isset($total_sum_per_currency_final[$value["currency"]])){
					$total_sum_per_currency_final[$value["currency"]] += $value["amount_paid_author"];
				}
				else{
					$total_sum_per_currency_final[$value["currency"]] = $value["amount_paid_author"];
				}
			?>
            	<tr> 
				<td>
					<?php echo $inc; ?>
				</td>
				<td>
					<?php echo $value["id"];?>
				</td>	
                <td>
                	<?php 
					if($config->hour_format == 12){
					$format = " Y-m-d h:i:s A ";
						switch($datetype){
							case "d-m-Y H:i:s": $format = "d-m-Y h:i:s A";
								  break;
							case "d.m.Y H:i": $format = "d.m.Y h:i A"; 
					  			break;
							case "d/m/Y H:i:s": $format = "d/m/Y h:i:s A"; 
								  break;
							case "m-d-Y H:i:s": $format = "m-d-Y h:i:s A"; 
								  break;
							case "m/d/Y H:i:s": $format = "m/d/Y h:i:s A"; 
								  break;
							case "Y-m-d H:i:s": $format = "Y-m-d h:i:s A"; 
								  break;
							case "Y/m/d H:i:s": $format = "Y/m/d h:i:s A"; 
								  break;
							case "d-m-Y": $format = "d-m-Y"; 
								  break;
							case "d/m/Y": $format = "d/m/Y"; 
								  break;
							case "m-d-Y": $format = "m-d-Y"; 
								  break;
							case "m/d/Y": $format = "m/d/Y"; 
								  break;
							case "Y-m-d": $format = "Y-m-d"; 
								  break;
							case "Y/m/d": $format = "Y/m/d";	
								  break;	  	  	  	  	  	  	  	  	  	  
						}
						$date_int = strtotime($value["data"]);
						$date_string = JHTML::_('date', $date_int, $format );
					}
					else{
						$date_int = strtotime($value["data"]);
						//$date_string = date("Y-m-d H:i:s", $date_int);
						$format = "Y-m-d H:M:S";
						switch($datetype){
							case "d-m-Y H:i:s": $format = "d-m-Y H:i:s";
								  break;
							case "d.m.Y H:i": $format = "d.m.Y H:i"; 
					  			break;
							case "d/m/Y H:i:s": $format = "d/m/Y H:i:s"; 
								  break;
							case "m-d-Y H:i:s": $format = "m-d-Y H:i:s"; 
								  break;
							case "m/d/Y H:i:s": $format = "m/d/Y H:i:s"; 
								  break;
							case "Y-m-d H:i:s": $format = "Y-m-d H:i:s"; 
								  break;
							case "Y/m/d H:i:s": $format = "Y/m/d H:i:s"; 
								  break;
							case "d-m-Y": $format = "d-m-Y"; 
								  break;
							case "d/m/Y": $format = "d/m/Y"; 
								  break;
							case "m-d-Y": $format = "m-d-Y"; 
								  break;
							case "m/d/Y": $format = "m/d/Y"; 
								  break;
							case "Y-m-d": $format = "Y-m-d"; 
								  break;
							case "Y/m/d": $format = "Y/m/d";		
								  break;  	  	  	  	  	  	  	  	  	  
						}
						$date_string = JHTML::_('date', $date_int, $format);
					}	
				
				?>
					<?php echo $date_string;?>
				
                   <td>
                        <?php
                            if($currencypos == 0){
                                echo JText::_("GURU_CURRENCY_".$value["currency"])." ".$guruHelper->displayPrice(number_format($value["amount_paid_author"],2));
                            }
                            else{
                                echo $guruHelper->displayPrice(number_format($value["amount_paid_author"],2))." ".JText::_("GURU_CURRENCY_".$value["currency"]);
                            }
                        ?>
                   </td>
                    <td>
                        <a class="btn btn-primary" href ="index.php?option=com_guru&controller=guruCommissions&task=details&page=history1&tmpl=component&course_id=<?php echo $value["course_id"];?>&cid[]=<?php echo $value["author_id"];?>&block=<?php echo $value["id"];?>"><?php echo JText::_("GURU_DETAILS"); ?></a>
                    </td> 
			</tr>
            <?php			
				$inc ++;
			}
		
		}
		else{
			$k = $pagination->limitstart;
			$total_sum_per_currency_price = array();
			$total_sum_per_currency_paid = array();
			$total_sum_per_currency_final = array();
			
			for($i=0; $i<$n; $i++):
				$student_name = $guruAdminModelguruCommissions->getStudentName($details[$i]["customer_id"]); 
				$commission_plan = $guruAdminModelguruCommissions->getCommissionsDetails($details[$i]["commission_id"]);
				$promo_code = $guruAdminModelguruCommissions->getPromoDetails($details[$i]["promocode_id"],$details[$i]["price"],$details[$i]["currency"]);
				
				if(isset($total_sum_per_currency_price[$details[$i]["currency"]])){
					$total_sum_per_currency_price[$details[$i]["currency"]] += $details[$i]["price"];
				}
				else{
					$total_sum_per_currency_price[$details[$i]["currency"]] = $details[$i]["price"];
				}
				
				if(isset($total_sum_per_currency_paid[$details[$i]["currency"]])){
					$total_sum_per_currency_paid[$details[$i]["currency"]] += $details[$i]["price_paid"];
				}
				else{
					$total_sum_per_currency_paid[$details[$i]["currency"]] = $details[$i]["price_paid"];
				}
				
				if(isset($total_sum_per_currency_final[$details[$i]["currency"]])){
					$total_sum_per_currency_final[$details[$i]["currency"]] += $details[$i]["amount_paid_author"];
				}
				else{
					$total_sum_per_currency_final[$details[$i]["currency"]] = $details[$i]["amount_paid_author"];
				}
			?>
				<tr> 
					<td>
						<?php echo $k+1; ?>
					</td>
					<td>
						<?php echo $details[$i]["id"];?>
					</td>	
					<td>
						<?php 
						if($config->hour_format == 12){
						$format = " Y-m-d h:i:s A ";
							switch($datetype){
								case "d-m-Y H:i:s": $format = "d-m-Y h:i:s A";
									  break;
								case "d.m.Y H:i": $format = "d.m.Y h:i A"; 
					  					break;
								case "d/m/Y H:i:s": $format = "d/m/Y h:i:s A"; 
									  break;
								case "m-d-Y H:i:s": $format = "m-d-Y h:i:s A"; 
									  break;
								case "m/d/Y H:i:s": $format = "m/d/Y h:i:s A"; 
									  break;
								case "Y-m-d H:i:s": $format = "Y-m-d h:i:s A"; 
									  break;
								case "Y/m/d H:i:s": $format = "Y/m/d h:i:s A"; 
									  break;
								case "d-m-Y": $format = "d-m-Y"; 
									  break;
								case "d/m/Y": $format = "d/m/Y"; 
									  break;
								case "m-d-Y": $format = "m-d-Y"; 
									  break;
								case "m/d/Y": $format = "m/d/Y"; 
									  break;
								case "Y-m-d": $format = "Y-m-d"; 
									  break;
								case "Y/m/d": $format = "Y/m/d";	
									  break;	  	  	  	  	  	  	  	  	  	  
							}
							$date_int = strtotime($details[$i]["data"]);
							$date_string = JHTML::_('date', $date_int, $format );
						}
						else{
							$date_int = strtotime($details[$i]["data"]);
							//$date_string = date("Y-m-d H:i:s", $date_int);
							$format = "Y-m-d H:M:S";
							switch($datetype){
								case "d-m-Y H:i:s": $format = "d-m-Y H:i:s";
									  break;
								case "d.m.Y H:i": $format = "d.m.Y H:i"; 
					  				break;
								case "d/m/Y H:i:s": $format = "d/m/Y H:i:s"; 
									  break;
								case "m-d-Y H:i:s": $format = "m-d-Y H:i:s"; 
									  break;
								case "m/d/Y H:i:s": $format = "m/d/Y H:i:s"; 
									  break;
								case "Y-m-d H:i:s": $format = "Y-m-d H:i:s"; 
									  break;
								case "Y/m/d H:i:s": $format = "Y/m/d H:i:s"; 
									  break;
								case "d-m-Y": $format = "d-m-Y"; 
									  break;
								case "d/m/Y": $format = "d/m/Y"; 
									  break;
								case "m-d-Y": $format = "m-d-Y"; 
									  break;
								case "m/d/Y": $format = "m/d/Y"; 
									  break;
								case "Y-m-d": $format = "Y-m-d"; 
									  break;
								case "Y/m/d": $format = "Y/m/d";		
									  break;  	  	  	  	  	  	  	  	  	  
							}
							$date_string = JHTML::_('date', $date_int, $format);
						}	
					
					?>
						<?php echo $date_string;?>
					</td>
					<td>
						<?php
							if($currencypos == 0){
								echo JText::_("GURU_CURRENCY_".$details[$i]["currency"])." ".$guruHelper->displayPrice($details[$i]["price"]);
							}
							else{
								echo $guruHelper->displayPrice($details[$i]["price"])." ".JText::_("GURU_CURRENCY_".$details[$i]["currency"]);
							}
						?>
				   </td>
				   <td>
						<?php
							if($currencypos == 0){
								echo JText::_("GURU_CURRENCY_".$details[$i]["currency"])." ".$guruHelper->displayPrice($details[$i]["price_paid"]);
							}
							else{
								echo $guruHelper->displayPrice($details[$i]["price_paid"])." ".JText::_("GURU_CURRENCY_".$details[$i]["currency"]);
							}
						?>
				   </td>
				   <td>
						<?php echo $student_name[0]; ?>
				   </td>
				   <td>
						<?php echo $details[$i]["payment_method"];?>
				   </td>
				   <td>
					   <?php 
					   if(isset($promo_code)){
						echo $promo_code;
					   }
					   else{
						echo "-";
					   }
					   ?>
				   </td>
				   <td>
					   <?php echo $commission_plan["0"]["commission_plan"]." (".$commission_plan["0"]["teacher_earnings"]."%)";?>
				   </td>
				   <td>
						<?php 
							if($currencypos == 0){
								echo JText::_("GURU_CURRENCY_".$details[$i]["currency"])." ".$guruHelper->displayPrice(number_format($details[$i]["amount_paid_author"],2));
							}
							else{
								echo $guruHelper->displayPrice(number_format($details[$i]["amount_paid_author"],2))." ".JText::_("GURU_CURRENCY_".$details[$i]["currency"]);
							}
                        ?>
				   </td>
				   
				</tr>
			<?php
				$k++;
			endfor;
			}
		?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10">
                <div class="btn-group pull-left hidden-phone">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                    <?php
                    	$select_limit = $pagination->getLimitBox();
						$select_limit = str_replace('Joomla.submitform()', "document.getElementById('export').value=''; Joomla.submitform()", $select_limit);
						echo $select_limit;
					?>
                </div>
                <?php
                	$pages = $pagination->getListFooter();
					$pages = str_replace('Joomla.submitform()', "document.getElementById('export').value=''; Joomla.submitform()", $pages);
					echo $pages;
				?>
                
            </td>
        </tr>
    </tfoot>
</table>

<table class="table table-striped table-bordered">
	<tr>
        <th>
        </th>
        <?php
        	if($page != "paid"){
		?>
            <th>
            <?php 
             	echo JText::_('GURU_PRICE');
            ?>
            </th>
          
            <th>
                <?php
                    echo JText::_('GURU_O_PAID');
                ?>
            </th>
            <th>
               <?php
               		echo JText::_('GURU_COMMISSION');
                ?>
            </th>
        <?php
		 }
		 else{
		 ?>
         	<th>
			 <?php
                echo JText::_('VIEWORDERSAMOUNTPAID');
			 ?>
            </th>
            <?php 	
		  }
		?>    
    </tr>
    <tr>
        <td>
           <b><?php echo JText::_('GURU_SUMMARY');?></b>
        </td>
        <?php if($page != "paid"){?>
                <td>
                   <b><?php
                        
                            foreach($total_sum_per_currency_price as $currency=>$value){
								if($currencypos == 0){
									echo JText::_("GURU_CURRENCY_".$currency)." ".$guruHelper->displayPrice(number_format($value,2));
								}
								else{
									echo $guruHelper->displayPrice(number_format($value,2))." ".JText::_("GURU_CURRENCY_".$currency);
								}
								echo"<br/>";	
							}
                     ?>
                   </b>
                </td>
                <td>
                    <b><?php
                            foreach($total_sum_per_currency_paid as $currency=>$value){
								if($currencypos == 0){
									echo JText::_("GURU_CURRENCY_".$currency)." ".$guruHelper->displayPrice(number_format($value,2));
								}
								else{
									echo $guruHelper->displayPrice(number_format($value,2))." ".JText::_("GURU_CURRENCY_".$currency);
								}
								echo"<br/>";	
							}
                     ?>
                   </b>
                </td>
        <?php }?>        
        <td>
        	<b>
        	<?php
				foreach($total_sum_per_currency_final as $currency=>$value){
					if($currencypos == 0){
						echo JText::_("GURU_CURRENCY_".$currency)." ".$guruHelper->displayPrice(number_format($value,2));
					}
					else{
						echo $guruHelper->displayPrice(number_format($value,2))." ".JText::_("GURU_CURRENCY_".$currency);
					}
					echo"<br/>";	
				}
			 ?>
           </b>  
        </td>
    </tr>
</table>

<input type="hidden" name="option" value="com_guru" />
<input type="hidden" id="export" name="export" value="" />
<input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get("task", "");?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="guruCommissions" />
<input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
</form>
</div>