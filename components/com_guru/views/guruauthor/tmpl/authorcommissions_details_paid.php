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
$doc =JFactory::getDocument();
$doc->addScript(JURI::root().'/components/com_guru/js/sorttable.js');
$guruModelguruAuthor = new guruModelguruAuthor();
$details = $this->details_paid;
$n = count($details);
$config = $this->config;
$datetype  = $config->datetype;
$currencypos = $config->currencypos;
$character = "GURU_CURRENCY_".$config->currency;
$block = JFactory::getApplication()->input->get("block", "0");
$date_url = $guruModelguruAuthor->getDateFormat2(JFactory::getApplication()->input->get("date", ""));
$total_ammount = $guruModelguruAuthor->getPendingDetailsTotal();
$pagination = $this->getDetailsPagination();
$course_id_request = JFactory::getApplication()->input->get("course_id","0");

$course_nam_top =  $guruModelguruAuthor->getCourseName1($course_id_request);

$sum_price = $guruModelguruAuthor->getPendingDetailsTotalPrice();
$sum_price_paid = $guruModelguruAuthor->getPendingDetailsTotalPricePaid();

//-----------------------------------------------------------------------------------
$tot = "";
if(isset($total_ammount) && count($total_ammount) > 0){
	$temp = array();
	foreach($total_ammount as $key=>$value){
		if(isset($temp[$value["currency"]])){
			$temp[$value["currency"]] += $value["amount_paid_author"];
		}
		else{
			$temp[$value["currency"]] = $value["amount_paid_author"];
		}
	}
	
	if($currencypos == 0){
		foreach($temp as $currency=>$value){
			$temp[$currency] = JText::_("GURU_CURRENCY_".$currency)." ".number_format($value, 2);
		}
		$tot = '<b>'.implode("&nbsp;&nbsp;&nbsp;", $temp).'</b>';
	}
	else{
		foreach($temp as $currency=>$value){
			$temp[$currency] = number_format($value, 2)." ".JText::_("GURU_CURRENCY_".$currency);
		}
		$tot = '<b>'.implode("&nbsp;&nbsp;&nbsp;", $temp).'</b>';
	}
}
//-----------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------
$sum_price1 = "";
if(isset($sum_price) && count($sum_price) > 0){
	$temp = array();
	foreach($sum_price as $key=>$value){
		if(isset($temp[$value["currency"]])){
			$temp[$value["currency"]] += $value["price"];
		}
		else{
			$temp[$value["currency"]] = $value["price"];
		}
	}
	
	if($currencypos == 0){
		foreach($temp as $currency=>$value){
			$temp[$currency] = JText::_("GURU_CURRENCY_".$currency)." ".number_format($value, 2);
		}
		$sum_price1 = '<b>'.implode("&nbsp;&nbsp;&nbsp;", $temp).'</b>';
	}
	else{
		foreach($temp as $currency=>$value){
			$temp[$currency] = number_format($value, 2)." ".JText::_("GURU_CURRENCY_".$currency);
		}
		$sum_price1 = '<b>'.implode("&nbsp;&nbsp;&nbsp;", $temp).'</b>';
	}
}
//-----------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------
$sum_price_paid1 = "";
if(isset($sum_price_paid) && count($sum_price_paid) > 0){
	$temp = array();
	foreach($sum_price_paid as $key=>$value){
		if(isset($temp[$value["currency"]])){
			$temp[$value["currency"]] += $value["price_paid"];
		}
		else{
			$temp[$value["currency"]] = $value["price_paid"];
		}
	}
	
	if($currencypos == 0){
		foreach($temp as $currency=>$value){
			$temp[$currency] = JText::_("GURU_CURRENCY_".$currency)." ".number_format($value, 2);
		}
		$sum_price_paid1 = '<b>'.implode("&nbsp;&nbsp;&nbsp;", $temp).'</b>';
	}
	else{
		foreach($temp as $currency=>$value){
			$temp[$currency] = number_format($value, 2)." ".JText::_("GURU_CURRENCY_".$currency);
		}
		$sum_price_paid1 = '<b>'.implode("&nbsp;&nbsp;&nbsp;", $temp).'</b>';
	}
}
//-----------------------------------------------------------------------------------
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<style>
	.component, .contentpane {
		background-color:#FFFFFF;
	}
</style>

<div>
	<button class="btn btn-primary pull-right" onclick="window.location.href = '<?php echo JUri::root() ?>index.php?option=com_guru&view=guruauthor&task=paid_commission&tmpl=component';"><?php echo JText::_("GURU_BACK"); ?></button>
    <h2><?php echo JText::_('GURU_COMMISSIONS_RECEIVED')." ".">"." ".JText::_('GURU_VIEW_DETAILS')." > ".JText::_('GURU_ID')." ".$block; ?></h2>
    <div><?php echo JText::_('GURU_COURSE_NAME').":"." ".'<b>'.$course_nam_top.'</b>'; ?></div>
    <div><?php echo JText::_('GURU_PAID_ON')." ".'<b>'.$date_url.'</b>'; ?></div>
</div>
<div class="g_margin_bottom clearfix">
    <div class="btn-group pull-right">
        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="border-width:0px !important;"><?php echo JText::_("GURU_EXPORT"); ?>
            <span class="caret" style="margin-left: 0; margin-top: 10px;"></span>
        </button>
        <ul class="dropdown-menu">
            <li>
            	<div onclick="document.adminForm.export.value='csv'; document.adminForm.submit();" style="width: 100%; padding: 2px 10px; cursor: pointer;"><?php echo JText::_("GURU_CSV");?></div>
            </li>
        </ul>
    </div>
    <div class="g_margin_bottom"><?php echo JText::_('GURU_TABLE_SUMMARY')."<br/>".JText::_('GURU_PRICE').":".$sum_price1. "&nbsp;&nbsp;&nbsp;&nbsp;".JText::_('GURU_O_PAID').":".$sum_price_paid1."&nbsp;&nbsp;&nbsp;&nbsp;".JText::_('GURU_COMM_EARNED')." ".$tot ;?></div>
</div>
<form  id="adminForm" name="adminForm" method="post">
    <div class="gru-page-filters">
    	<div class="gru-filter-item"  style="line-height: 30px;">
			<?php  echo JText::_("GURU_FILTER_BY"); ?>
		</div>
    	<div class="gru-filter-item">
			<?php
                $filter_promocode = JFactory::getApplication()->input->get("filter_promocode", "0");
				$promos_filter = $guruModelguruAuthor->getAllPromos();
            ?>
        
            <select name="filter_promocode" class="inputbox" onchange="document.getElementById('export').value=''; document.adminForm.submit();">
                <option value="0" <?php if($filter_promocode == "0"){ echo 'selected="selected"'; }?>><?php  echo JText::_("GURU_BUY_PROMO"); ?></option>
                <?php
                    if(isset($promos_filter) && count($promos_filter) > 0){
                        foreach($promos_filter as $key=>$promo){
                ?>
                            <option value="<?php echo $promo["id"]; ?>" <?php if($promo["id"] == $filter_promocode){echo 'selected="selected"';} ?> ><?php echo $promo["code"]; ?></option>
                <?php
                        }
                    }
                ?>
            </select>
		</div>
    </div>
    
    <div class="clearfix"></div>
    
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
					 <?php echo JText::_('GURU_MYORDERS_ORDER_DATE');?><i class="icon-menu-2"></i>
                </th>
                <th class="sorttable_numeric">
                <?php echo JText::_('GURU_PRICE');?><i class="icon-menu-2"></i>
                </th>
                <th>
                     <?php echo JText::_('GURU_O_PAID');?><i class="icon-menu-2"></i>
                </th>
                <th>
                     <?php echo JText::_('GURU_COU_STUDENTS');?><i class="icon-menu-2"></i>
                </th>
                <th>
                     <?php echo JText::_('GURU_PROMOCODE')."(% / ".JText::_($character).")";?><i class="icon-menu-2"></i>
                </th>
                <th class="sorttable_numeric">
                    <?php echo JText::_('GURU_COMMISSIONS'); ?><i class="icon-menu-2"></i>
                </th> 
            </tr>
        </thead>
        
        <tbody>
        <?php
       		$k = $pagination->limitstart;
			for($i=0; $i<$n; $i++){
				$student_name = $guruModelguruAuthor->getStudentName($details[$i]["customer_id"]); 
				$promo_code = $guruModelguruAuthor->getPromoDetails($details[$i]["promocode_id"],$details[$i]["price"]);
				$character = "GURU_CURRENCY_".$details[$i]["currency"];
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
								echo JText::_($character)." ".$details[$i]["price"];
							}
							else{
								echo $details[$i]["price"]." ".JText::_($character);
							}
						?>
				   </td>
				   <td>
						<?php
							if($currencypos == 0){
								echo JText::_($character)." ".$details[$i]["price_paid"];
							}
							else{
								echo $details[$i]["price_paid"]." ".JText::_($character);
							}
						?>
				   </td>
				   <td>
						<?php echo $student_name[0]; ?>
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
						<?php
							if($currencypos == 0){
								echo JText::_($character)." ".number_format($details[$i]["amount_paid_author"],2);
							}
							else{
								echo number_format($details[$i]["amount_paid_author"],2)." ".JText::_($character);
							}
						?>
				   </td>
				   
				</tr>
			<?php
				$k++;
		}
	?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10">
                <div class="btn-group pull-left">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                    <?php
                    	$select_limit = $pagination->getLimitBox();
						$select_limit = str_replace('this.form.submit()', "document.getElementById('export').value=''; Joomla.submitform()", $select_limit);
						echo $select_limit;
					?>
                </div>
                <?php
                	$pages = $pagination->getListFooter();
					include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
					$helper = new guruHelper();
					$pages = str_replace('name="limitstart"', 'name="temp_limitstart"', $pages);
					$pages = $helper->transformPagination($pages);
					$pages = str_replace('Joomla.submitform()', "document.getElementById('export').value=''; Joomla.submitform()", $pages);
					echo $pages;
				?>
                
            </td>
        </tr>
    </tfoot>
</table> 
	<input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get("task", "authorcommissions_paid");?>" />
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="controller" value="guruAuthor" />
    <input type="hidden" name="boxchecked" value="" />
    <input type="hidden" id="export" name="export" value="" />
    <input type="hidden" name="old_limit" value="<?php echo intval($pagination->limit); ?>" />
</form>
