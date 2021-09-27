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
jimport('joomla.html.html.grid');
$teacher_name = $this->teacher_name;
$commissions_to_pay = $this->commissions_to_pay;
$n = count($commissions_to_pay);
$config = $this->config;
$currencypos = $config->currencypos;
$doc =JFactory::getDocument();
$total_sum_per_currency = array();
$guruAdminModelguruCommissions = new guruAdminModelguruCommissions();
$doc->addScript(JURI::root().'/components/com_guru/js/sorttable.js');
$i = "";
$data_post = JFactory::getApplication()->input->post->getArray();
?>
<span id="message_lib" class="alert" style="display:none; margin-top: 5px;">
    <a href='http://www.ijoomla.com/redirect/guru/mpdf.htm' target="_blank">
        <?php echo "1. ".JText::_("GURU_DOWNLOAD_MPDF1"); ?>
    </a>
    <br />
    <?php echo "2. ".JText::_("GURU_DOWNLOAD_MPDF2"); ?>
    <br />
    <?php echo "3. ".JText::_("GURU_DOWNLOAD_MPDF3"); ?>
</span>
<script>
	function guruReloadPage(){
		window.location.href="index.php?option=com_guru&controller=guruCommissions&task=pending";
	}
	
	function valthisform()
	{
		var checkboxs=document.getElementsByName("cid[]");
		var okay=false;
		var currency = "";
		
		for(var i=0,l=checkboxs.length;i<l;i++){
			if(checkboxs[i].checked){
				okay=true;
				if(currency == ""){
					currency = document.getElementById("currency_cb"+i).value;
				}
				else{
					if(currency != document.getElementById("currency_cb"+i).value){
						alert("<?php echo JText::_("GURU_MULTIPLE_CURRENCIES"); ?>");
						return false;
					}
				}
			}
		}
		
		if(okay){
			if(confirm('<?php echo JText::_("GURU_SURE_PAID_COMMISSION"); ?>')){
				document.getElementById('task').value='make_paid';
				document.getElementById('export').value='csv_mass';
				timeoutID = window.setTimeout(guruReloadPage, 1000);
				document.adminForm.submit();
			} 
			else{
				return false;
			}
		}
		else alert("<?php echo JText::_("GURU_PROGRAM_MAKESEL_JAVAMSG"); ?>");
	}
	
	var first = false;
	
	function showContent1(href){
		first = true;
		jQuery( '#myModal1 .modal-bodyc iframe').attr('src', href);
		screen_height = window.innerHeight;
		document.getElementById('myModal1').style.height = (screen_height -110)+'px';
		document.getElementById('pending_commissions').style.height = (screen_height -150)+'px';
	}
	jQuery('body').click(function () {
		if(!first){
			jQuery('#myModal1 .modal-bodyc iframe').attr('src', '');
		}
		else{
			first = false;
		}
	});
	function markTheCheckbox(elm){
		jQuery(elm).parent().parent().find('input:checkbox:first').attr('checked', 'checked');
	}
	
	
	function hideMessage(){
		document.getElementById("alert-message").style.display = "none";
		document.getElementById("read-this").style.display = "block";
	}
	
	function displayMessage(){
		document.getElementById("alert-message").style.display = "block";
		document.getElementById("read-this").style.display = "none";
	}
</script>

<style>
	div.modal {
			z-index: 9999;
			margin-left:-45%;
			top:6%;
			padding:10px;
			width:90%;
	}
	.modal-backdrop, .modal-backdrop.fade.in {
		opacity: 0.4 !important;
	}
	div.modal-header{
		padding :5px;
	}
</style>

<div id="read-this" style="display:block;">
	<div style="float:right; margin-bottom: 10px;">
    	<a data-action="collapse" href="#" onclick="javascript:displayMessage();" style="color:#FF0000; font-size:16px; text-decoration:none; margin-bottom:10px;">
        	<?php echo JText::_("GURU_READ_THIS"); ?>
            <i class="js-icon-chevron-down"></i>
        </a>
    </div>
</div>
<div id="alert-message" style="display:none;">
    <div class="alert alert-info">
        <?php echo "- "."<b>".JText::_("GURU_MAKE_PAID")."</b>".JText::_('GURU_DETAILS_TABLE1')."<br/>"."- "."<b>".JText::_("GURU_PAYPAL_PAYMENT")."</b>".JText::_('GURU_DETAILS_TABLE2')."<br/><br/>".JText::_('GURU_NOTE')."<br/>".JText::_('GURU_NOTE_EXPORT_PAYMENT')."<br/>".JText::_('GURU_NO_PAYPAL_ACCOUNT')."<br/>".JText::_('GURU_NO_PAYPAL_ACCOUNT1')."<br/>".JText::_("GURU_MULTIPLE_CURRENCIES");?>
    </div>
    <div style="float: right; position: absolute; right: 25px; top:75px;">
        <a data-action="collapse" href="#" onclick="javascript:hideMessage();" style="color:#FF0000; text-decoration:none; font-size:16px;">
            <?php echo JText::_("GURU_CLOSE_TASK_BTN"); ?>
            <i class="js-icon-chevron-up"></i>
        </a>
    </div>
</div>

<div class="clearfix"></div>

<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post">
    <div id="myModal1" class="modal hide" style="">
        <div class="modal-header">
            <button type="button" id="close" class="close" data-dismiss="modal" aria-hidden="true">x</button>
         </div>
         <div class="modal-bodyc" style="background-color:#FFFFFF;" >
            <iframe style="" id="pending_commissions" width="100%" frameborder="0"></iframe>
        </div>
    </div>

<table style="width: 100%;" cellpadding="5" cellspacing="5" bgcolor="#FFFFFF">
    <tr>
        <td>
            <input type="text" name="search_text" value="<?php if(isset($data_post['search_text'])) echo $data_post['search_text'];?>" />
            <input class="btn btn-primary" type="submit" onclick="document.getElementById('export').value=''" name="submit_search" value="<?php echo JText::_('GURU_SEARCHTXT');?>" />
        </td>
        <td>
        	<?php
            	$filter_teacher = JFactory::getApplication()->input->get("filter_teacher", "0");
				$teachers = $this->getAllTeachers();
			?>
            <select name="filter_teacher" class="inputbox" onchange="document.getElementById('export').value=''; document.adminForm.submit();">
                <option value="0" <?php if($filter_teacher == "0"){ echo 'selected="selected"'; }?>><?php  echo JText::_("GURU_SELECT_TEACHER"); ?></option>
                <?php
                	if(isset($teachers) && count($teachers) > 0){
						foreach($teachers as $key=>$teacher){
				?>
                			<option value="<?php echo $teacher["id"]; ?>" <?php if($teacher["id"] == $filter_teacher){echo 'selected="selected"';} ?> ><?php echo $teacher["name"]; ?></option>
                <?php
						}
					}
				?>
            </select>
        </td>
         <td>
        	<?php
            	$filter_course = JFactory::getApplication()->input->get("filter_course", "0");
				$course = $this->getAllCourses();
			?>
            <select name="filter_course" class="inputbox" onchange="document.getElementById('export').value=''; document.adminForm.submit();">
                <option value="0" <?php if($filter_course == "0"){ echo 'selected="selected"'; }?>><?php  echo JText::_("GURU_SELECT_COURSE"); ?></option>
                <?php
                	if(isset($course) && count($course) > 0){
						foreach($course as $key=>$courses){
				?>
                			<option value="<?php echo $courses["id"]; ?>" <?php if($courses["id"] == $filter_course){echo 'selected="selected"';} ?> ><?php echo $courses["name"]; ?></option>
                <?php
						}
					}
				?>
            </select>
        </td>
         <td>
        	<?php
            	$filter_payment = JFactory::getApplication()->input->get("filter_payment", "0");
			?>
            <select name="filter_payment" class="inputbox" onchange="document.getElementById('export').value=''; document.adminForm.submit();">
                <option value="0" <?php if($filter_payment == "0"){ echo 'selected="selected"'; }?>><?php  echo JText::_("GURU_PAYMENT_OPTION"); ?></option>
                <option value="1" <?php if($filter_payment == "1"){ echo 'selected="selected"'; }?>><?php  echo JText::_("GURU_PAYMENT_OPTION1"); ?></option>
                <option value="2" <?php if($filter_payment == "2"){ echo 'selected="selected"'; }?>><?php  echo JText::_("GURU_PAYMENT_OPTION2"); ?></option>
            </select>
        </td>
    <tr>
</table>
<table class="sortable table table-striped adminlist table-bordered">
    <thead>
        <tr>
            <th class="sorttable_nosort" width="2%">
                <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
                <span class="lbl"></span>
            </th>
            <th>
                <?php echo JText::_('GURU_AUTHOR');?><i class="icon-menu-2"></i>
            </th>
            <th>
                <?php echo JText::_('GURU_COURSE')."(s)";?><i class="icon-menu-2"></i>
            </th>
            <th> <?php echo JText::_('GURU_TREEORDERS');?><i class="icon-menu-2"></i>
            </th>
            <th class="sorttable_numeric">
                <?php echo JText::_('GURU_COMM_PENDING');?><i class="icon-menu-2"></i>
            </th>
            <th>
                <?php echo JText::_('GURU_VIEW_DETAILS');?>
            </th>
            <th>
                <?php echo JText::_('GURU_MAKE_PAID');?>
            </th> 
            <th>
                <?php echo JText::_('GURU_PAYPAL_PAYMENT');?>
            </th>    
        </tr>
    </thead>
    
    <tbody>
    
    <?php
	$sum_orders = 0;
	$total_commission = 0;
	if(count($commissions_to_pay) == 0){
	?>
        	<tr>
                <td>
                </td>
            	<td width="100%">
                	 <?php echo JText::_('GURU_ALL_MARKED_PAID');?>
                </td>
                <td>
                </td>

                <td>
                </td>

                <td>
                </td>

                <td>
                </td>
                
                <td>
                </td>
                
                <td>
                </td>

            </tr>
	<?php
	}
	else{
		$i = 0;
		
		foreach($commissions_to_pay as $key=>$value){
			$teachername = $guruAdminModelguruCommissions->getTeacherName($value["author_id"]);
			$course_name = $guruAdminModelguruCommissions->getCourseName($value["course_id"]);
			$paypal_or_not = $guruAdminModelguruCommissions->getPaymentOption($value["author_id"]);
			$paypal_email = $guruAdminModelguruCommissions->getPaymentPaypalEmail($value["author_id"]);
			$count_orders = $value["orders"];
			$amount_pending = $value["amount_paid_author"];
			
			if($amount_pending == 0){
				//continue; /** commented this to show all orders even price or commision is 0 **/
			}
			
			$sum_orders += $count_orders;
			$checked = JHTML::_('grid.id', $i, $value["author_id"]."-".$value["course_id"]."-".$value["id"]."-".$value["currency"]);
			
			if(isset($total_sum_per_currency[$value["currency"]])){
				$total_sum_per_currency[$value["currency"]] += $value["amount_paid_author"];
			}
			else{
				$total_sum_per_currency[$value["currency"]] = $value["amount_paid_author"];
			}
			?>
		<tr> 
			<td>
				<?php echo $checked;?>
				<span class="lbl"></span>
			</td>	
		   <td>
				<?php echo '<a href="index.php?option=com_guru&controller=guruAuthor&task=edit&id='.$value["author_id"].'">'.@$teachername["0"].'</a>';?>
		   </td>
		   <td>
				<?php echo '<a href="index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]='.$value["course_id"].'">'.@$course_name["0"].'</a>';?>
		   </td>
		   <td>
				<?php echo '<a href="index.php?option=com_guru&controller=guruOrders&filter_teacher='.$value["author_id"].'&filter_course= '.$value["course_id"].'">'.intval($count_orders).'</a>'; ?>
		   </td>
		   <td>
			   <?php 
					if($currencypos == 0){
						echo JText::_("GURU_CURRENCY_".$value["currency"])." ".number_format($amount_pending,2);
					}
					else{
						echo number_format($amount_pending,2)." ".JText::_("GURU_CURRENCY_".$value["currency"]);
					}
			   ?>
               <input type="hidden" id="currency_cb<?php echo $i; ?>" value="<?php echo $value["currency"]; ?>">
		   </td>
		   <td>
				<a class="g_btn" data-toggle="modal" data-target="#myModal1" onClick = "showContent1('index.php?option=com_guru&controller=guruCommissions&task=details&page=pending&tmpl=component&course_id=<?php echo $value["course_id"];?>&cid[]=<?php echo $value["author_id"];?>&currency=<?php echo $value["currency"];?>');" href="#"><?php echo JText::_("GURU_DETAILS"); ?></a>
		   </td>
            <td>
                <a class="g_btn-warning"  onClick = "markTheCheckbox(this);document.adminForm.course_second_table.value='<?php echo $value["author_id"]."-".$value["course_id"]."-".$value["id"]."-".$value["currency"];?>'; document.getElementById('currency_row').value='<?php echo $value["currency"];?>'; document.adminForm.task.value='make_paid_top'; document.adminForm.submit();"><?php echo JText::_("GURU_MAKE_PAID"); ?></a>
           </td>
            <td>
            	<?php 
					if($paypal_or_not == 0){
						$list_pay = array("AUD","BRL","CAD","CZK","DKK","EUR","HKD","HUF","ILS","JPY","MYR","MXN","NOK","NZD","PHP","PLN","GBP","SGD","SEK","CHF","TWD", "THB", "TRY", "USD");

						if(in_array($value["currency"],$list_pay)){				
					?>
						<a class="g_btn-warning"  onClick = "markTheCheckbox(this);document.PayPalPayment.business.value='<?php echo $paypal_email;?>'; document.PayPalPayment.currency_code.value='<?php echo $value["currency"];?>'; document.PayPalPayment.currency_code.value='<?php echo $value["currency"];?>'; document.PayPalPayment.business.value='<?php echo $paypal_email;?>'; document.PayPalPayment.item_name_1.value='<?php echo @$teachername["0"];?>'; document.PayPalPayment.amount_1.value='<?php echo number_format($amount_pending,2);?>'; document.PayPalPayment.custom.value='<?php echo $value["author_id"]."-".$value["course_id"]."-".$value["id"];?>'; document.PayPalPayment.submit();"><?php echo JText::_("GURU_PAY_WITH_PAYPAL"); ?></a>
					<?php
					  }
				  }
				  else{
				   echo '&nbsp;';
				  }?>
           </td>
		</tr>
	<?php 
			$i++;
		}
	}
?>
</tbody>
    <tfoot>
        <tr>
            <td colspan="10">
                <div class="btn-group pull-left hidden-phone">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                    <?php
                        $select_limit = $this->pagination1->getLimitBox();
                        $select_limit = str_replace('Joomla.submitform()', "document.getElementById('export').value=''; document.adminForm.submit()", $select_limit);
                        echo $select_limit;
                    ?>
                </div>
                <?php
                    $pages = $this->pagination1->getListFooter();
                    $pages = str_replace('Joomla.submitform()', "document.getElementById('export').value=''; document.adminForm.submit()", $pages);
                    echo $pages;
                ?>
            </td>
        </tr>
    </tfoot>
    <tr>
    	<td colspan="10">
    		<button id="mass_button" type="button" onclick="valthisform();" class="btn btn-success"><?php echo JText::_("GURU_BATCH_PAY"); ?> </button>
        </td>
    </tr>        
</table>

<table class="table table-striped table-bordered">
	<tr>
        <th>
        </th>
        <th>
            <?php echo JText::_('GURU_TREEORDERS');?>
        </th>
        <th>
            <?php echo JText::_('GURU_COMM_PENDING');?>
        </th>
    </tr>
    <tr>
        <td>
           <b><?php echo JText::_('GURU_SUMMARY');?></b>
        </td>
        <td>
            <b><?php echo $sum_orders;?></b>
        </td>
        <td>
            <b><?php
				if(count($total_sum_per_currency)>0){
					foreach($total_sum_per_currency as $currency=>$value){
						if($currencypos == 0){
							echo JText::_("GURU_CURRENCY_".$currency)." ".number_format($value,2);
						}
						else{
							echo number_format($value,2)." ".JText::_("GURU_CURRENCY_".$currency);
						}
						echo"<br/>";	
					}
				}
				else{
					if($currencypos == 0){
							echo JText::_("GURU_CURRENCY_". $config->currency)." "."0.00";
						}
						else{
							echo "0.00"." ".JText::_("GURU_CURRENCY_". $config->currency);
						}
				}

             ?>
           </b>
        </td>
    </tr>
</table>


<input type="hidden" name="option" value="com_guru" />
<input type="hidden" id="export" name="export" value="" />
<input type="hidden" id="task" name="task" value="<?php echo JFactory::getApplication()->input->get("task", "pending");?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="guruCommissions" />
<input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
<input type="hidden" name="course_second_table" value="" />
<input type="hidden"  id="currency_row" name="currency_row" value="" />
</form>

<!-- start paypal form -->

<form action='https://www.paypal.com/us/cgi-bin/webscr' name='PayPalPayment' id="PayPalPayment"  method='post'>
	<input type='hidden' id='upload' name='upload' value='1'>
	<input type='hidden' id='no_note' name='no_note' value='1'>
	<input type='hidden' id='notify_url' name='notify_url' value='<?php echo JURI::root();?>administrator/index.php?option=com_guru&controller=guruCommissions&task=pending'>
	<input type='hidden' id='return' name='return' value='<?php echo JURI::root();?>administrator/index.php?option=com_guru&controller=guruCommissions&task=pending_ok'>
	<input type='hidden' id='cancel_return' name='cancel_return' value='<?php echo JURI::root();?>administrator/index.php?option=com_guru&controller=guruCommissions&task=pending'>
	<input type='hidden' id='lc' name='lc' value='EN'>
	<input type='hidden' id='no_shipping' name='no_shipping' value='1'>
	<input type='hidden' id='cmd' name='cmd' value='_cart'>
	<input type='hidden' id='rm' name='rm' value='2'>
	<input type='hidden' id='business' name='business' value=''>
	<input type='hidden' id='currency_code' name='currency_code' value=''>
	<input type='hidden' id='item_name_1' name='item_name_1' value=''>
	<input type='hidden' id='amount_1' name='amount_1' value=''>
	<input type='hidden' id='quantity_1' name='quantity_1' value='1'>
	<input type='hidden' id='custom' name='custom' value=''>
	<INPUT TYPE="hidden" name="charset" value="utf-8">
</form>
<!-- end paypal form-->