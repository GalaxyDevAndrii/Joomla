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

JHTML::_('behavior.tooltip');
JHtml::_('behavior.framework');

$username = $this->getOrderDetails();
$user_id = JFactory::getApplication()->input->get("userid", "0");

$config = guruAdminModelguruOrder::getConfig();
$currency = $config->currency;
$currencypos = $config->currencypos;
$lang = JFactory::getLanguage();
$lang->load('plg_gurupayment_offline', JPATH_ADMINISTRATOR);
$character = JText::_("GURU_CURRENCY_".$currency);
$plugins = guruAdminModelguruOrder::getPlugins();
$doc =JFactory::getDocument();
?>
<style>
	div.modal {
		width:830px !important;
	}
</style>
<script language="javascript" type="text/javascript">	
	function showGuruCoursesList(){
			var url = "index.php?option=com_guru&controller=guruOrders&task=productitem&userid=<?php echo $user_id; ?>&tmpl=component&format=raw";
			jQuery.ajax({ url: url,
				method: 'get',
				asynchronous: 'true',
				success: function(transport) {
					jQuery('.product_items').append(transport);
				}
			});
	}
	
	function showContentSelect(href){
		jQuery( '#myModal .modal-body iframe').attr('src', href);
	}
	
	function remove_product(id){
		old_length = document.getElementById("nr_licenses").value;
		new_length = old_length;
		if(old_length != 0){
			var element_id = "licences_"+id;
			if(document.getElementById(element_id).style.display == "none"){				
			}
			else{
				new_length = old_length-1;
			}
		}
		document.getElementById("nr_licenses").value = new_length;
		$('#course_item_'+id).remove();
		
		removePromo(id);
		changeAmount(id);
		
    }
	
	function show_licences_renew(gen_number){
		c_id = "course_id"+gen_number;
		opt_id = "subscr_type_select"+gen_number;
		course_id = document.getElementById(c_id).value;
		update = '#div_licences_select_'+gen_number;
		option = document.getElementById(opt_id).value;
		var url = "index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=setrenew&course_id="+course_id+"&gen_number="+gen_number+"&option_req="+option;
		
		var req = jQuery.ajax({
			method: 'get',
			url: url,
			data: { 'do' : '1' },
			update: $(update),
			success: function(response){
				$('#div_licences_select_'+gen_number).html(response);
				changeAmount(gen_number);
			}
		})
	}
	
	function changeAmount(generate_number){
		lic_id_select = "licences_select"+generate_number;
		if((typeof(document.getElementById(lic_id_select)) != "unsigned") && (document.getElementById(lic_id_select) !== null)){
			var selected = document.getElementById(lic_id_select);
			var lic_id_select_length = selected.length;
			var selectedIndex = selected.selectedIndex; 
			var new_value = (lic_id_select_length > 1) 
				? selected[selectedIndex].dataset.price 
				: selected.value;

			var hidd_price = "hidden_licenses_"+generate_number;
			document.getElementById(hidd_price).value = new_value;

			var hidd_plan = 'hidden_plan_' + generate_number;
			document.getElementById(hidd_plan).value = selected.value;
		}
		var licences_select = document.adminForm.licences_select;
		var length = (licences_select) ? licences_select.length : 0;
		var total = getTotalForAllSelectedCourses(licences_select, length);

		document.getElementById("total").value = total;
		document.getElementById("amount").innerHTML = "<?php if($currencypos == 0){ echo trim($character);} ?>"+formatNumberSeparator(parseFloat(total).toFixed(2))+"<?php if($currencypos == 1){ echo trim($character);}?>";
		document.getElementById("amount_hidden").value = parseFloat(total).toFixed(2);
		document.getElementById("total_column").innerHTML = "<?php if($currencypos == 0){ echo trim($character);}?>"+formatNumberSeparator(total)+"<?php if($currencypos == 1){ echo trim($character);}?>";
	
		var promo_code = document.getElementById("promocode").value;
		setPromo(promo_code);	
	}

	function formatNumberSeparator(price){
		var thousands_separator = '<?php echo $config->thousands_separator; ?>';
		var decimals_separator = '<?php echo $config->decimals_separator; ?>';

		if(thousands_separator == "1" && decimals_separator == "0"){
			var parts = price.toString().split(".");
	  		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	  		return parts.join(".");
		}
		else if(thousands_separator == "0" && decimals_separator == "1"){
			var parts = price.toString().split(".");
	  		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	  		return parts.join(",");
		}
		else if(thousands_separator == "1" && decimals_separator == "2"){
			var parts = price.toString().split(".");
	  		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	  		return parts.join(" ");
		}
		else if(thousands_separator == "2" && decimals_separator == "1"){
			var parts = price.toString().split(".");
	  		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
	  		return parts.join(",");
		}
		else if(thousands_separator == "0" && decimals_separator == "2"){
			var parts = price.toString().split(".");
	  		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	  		return parts.join(" ");
		}
		else if(thousands_separator == "2" && decimals_separator == "0"){
			var parts = price.toString().split(".");
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
	  		return parts.join(".");
		}
	}

	function getTotalForAllSelectedCourses(licences_select, length){

		var total = 0;
		for(var i=0; i<length; i++){
			if(licences_select[i][0]){
				if(licences_select[i][1]){
					var licences_select_nodeList_length = licences_select[i].length;
					for(var n=0; n<licences_select_nodeList_length; n++){
						if(licences_select[i][n].selected== true){
							if(!isNaN(licences_select[i][n].value)){
								total += parseFloat(licences_select[i][n].dataset.price);
							}
						}
					}
				}
				else{
					if(licences_select[i][0].selected== true){
						if(!isNaN(licences_select[i].value)){
							total += parseFloat(licences_select[i][0].dataset.price);
						}
					}
				} 
			}
			else{
				
				if(licences_select[i].selected== true){
					if(!isNaN(licences_select[i].value)){
						total += parseFloat(licences_select[i].dataset.price);
					}
				}
			}
		}
		return total;
	}
	
	function removePromo(id){
		var promo_code = '|'+id.replace("licences_select","");
		//console.log(promo_code);
		var value_gnerated_code =  document.getElementById("courses_ids_code_generate").value;
		str = document.getElementById("courses_ids_code_generate").value;
		var temp = str.split(promo_code);
		var tempLength = temp.length;
		var courses_ids_code_generate_value='';
		for(var i=0; i<tempLength; i++){				
			if(temp[i] !== promo_code){
				courses_ids_code_generate_value +=temp[i];
			}
		}

		document.getElementById("courses_ids_code_generate").value = courses_ids_code_generate_value;
		//console.log(document.getElementById("courses_ids_code_generate").value);
		
	}

	function setPromo(option){
		var promo_code = option;
		var value = document.getElementById("amount_hidden").value;
		if(value == 0){
			value = document.getElementById("total").value;
		}
		var value_gnerated_code =  document.getElementById("courses_ids_code_generate").value;
		value_gnerated_code_item = value_gnerated_code.split("|");
		var n = value_gnerated_code_item.length;
		
		var and = "";
		for(c=0; c<n; c++){
			if(value_gnerated_code_item[c] != ""){
				price_id = "hidden_licenses_"+value_gnerated_code_item[c];
				price = document.getElementById(price_id).value;
				course_id ="course_id"+value_gnerated_code_item[c];
				id_course = document.getElementById(course_id).value;
				and += "&s"+c+"="+id_course+"-"+price;
			}
		}
		
		var url = "index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=setpromo&promocode="+promo_code+"&value="+value+"&count="+n+and;
		jQuery.ajax({ url: url,
			method: 'get',
			asynchronous: 'true',
			success: function(html) {
				html = html;
				if(parseFloat(html) < 0){
					document.getElementById("total").value = parseFloat((value)+"").toFixed(2);
					document.getElementById("total_column").innerHTML = "<?php if($currencypos == 0){ echo trim($character);}?>"+formatNumberSeparator(parseFloat((value)+"")).toFixed(2)+"<?php if($currencypos == 1){ echo trim($character);}?>";
					document.getElementById("discount_collumn").innerHTML = "";
				}
				else if(parseFloat(html) > 0){
					value = parseFloat((value-html)+"").toFixed(2);
					response = parseFloat(html+"").toFixed(2);
					if(value < 0){
						document.getElementById("total").value = "0";
						document.getElementById("total_column").innerHTML = "<?php if($currencypos == 0){ echo trim($character);}?>"+" 0"+"<?php if($currencypos == 1){ echo trim($character);}?>";
						document.getElementById("discount_collumn").innerHTML = "<?php if($currencypos == 0){ echo trim($character);}?>"+formatNumberSeparator(html)+"<?php if($currencypos == 1){ echo trim($character);}?>";						
					}
					else{
						document.getElementById("total").value = value;
						document.getElementById("total_column").innerHTML = "<?php if($currencypos == 0){ echo trim($character);}?>"+formatNumberSeparator(value)+"<?php if($currencypos == 1){ echo trim($character);}?>";
						document.getElementById("discount_collumn").innerHTML = "<?php if($currencypos == 0){ echo trim($character);}?>"+formatNumberSeparator(html)+"<?php if($currencypos == 1){ echo trim($character);}?>";
					}
				}
				else{
					document.getElementById("total").value = value;
					document.getElementById("total_column").innerHTML = "<?php if($currencypos == 0){ echo trim($character);}?>"+formatNumberSeparator(value)+"<?php if($currencypos == 1){ echo trim($character);}?>";
					document.getElementById("discount_collumn").innerHTML = "<?php if($currencypos == 0){ echo trim($character);}?>"+formatNumberSeparator(html)+"<?php if($currencypos == 1){ echo trim($character);}?>";
				}
			}
		});
	}
	
</script>		
<div id="myModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	 <div class="modal-dialog modal-lg">
	 	<div class="modal-content">
		    <div class="modal-header">
		   		<button type="button" id="close" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		    </div>
		    <div class="modal-body">
		    	<iframe width="800"  height="600" frameborder="0">
		        </iframe>
		    </div>
		</div>
	</div>
</div>
<form action="" method="post" name="adminForm" id="adminForm">	
	<fieldset class="adminform">
		<div class="well"><?php echo JText::_("GURU_NEW_ORDER"); ?></div>
			<table width="100%">
				<tr>
					<td width="30%"><?php echo JText::_("GURU_USERNAME"); ?></td>
					<td><?php echo $username; ?></td>
					<td>
						<!-- <a href="index.php?option=com_digistore&amp;controller=digistoreOrders&amp;task=newCreateCustomer&amp;username=fffgfdgddhd">Change</a> -->
					</td>
				</tr>				
				<tr>
					<td style="background-color:#F6F6F6; height:25px;" colspan="3">
						<b><?php echo JText::_("GURU_ADD_COURSES"); ?></b>
					</td>
				</tr>				
				<tr>
					<td style="border-top: 1px solid rgb(204, 204, 204); padding-top: 5px;"></td>
					<td style="border-top: 1px solid rgb(204, 204, 204); padding-top: 5px;">
						<input onclick="showGuruCoursesList();" type="button" class="btn" name="buttonaddproduct" id="buttonaddproduct" value="<?php echo JText::_("GURU_ADD_COURS_BUTTON"); ?>">
					</td>
					<td style="border-top: 1px solid rgb(204, 204, 204); padding-top: 5px;"></td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="product_items" id="product_items">
						</div>						
					</td>
				</tr>
				<tr>
					<td style="border-top: 1px solid rgb(204, 204, 204); padding-top: 5px;"><?php echo JText::_("GURU_ORDPAYMENTMETHOD"); ?></td>
					<td id="payment_method" style="border-top: 1px solid rgb(204, 204, 204); padding-top: 5px;">
						<select size="1" class="inputbox" id="processor" name="processor">
							<?php
								if(isset($plugins) && count($plugins) > 0){
									foreach($plugins as $key=>$value){
										$value["name"] = JText::_($value["name"]);
										echo '<option value="'.$value["element"].'">'.$value["name"].'</option>';
									}
								}
							?>
						</select>
						<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_ORDPAYMENTMETHOD"); ?>" >
							<img border="0" src="components/com_guru/images/icons/tooltip.png">
						</span>
					</td>
					<td style="border-top: 1px solid rgb(204, 204, 204); padding-top: 5px;"></td>
				</tr>
				<tr>
					<td><?php echo JTExt::_("GURU_PROMOCODE"); ?></td>
					<td>
						<?php
							$promos = guruAdminModelguruOrder::getAllPromos();							
						?>
						<select size="1" class="inputbox" id="promocode" name="promocode" onchange="javascript:setPromo(this.value);">
							<?php
								if(isset($promos) && is_array($promos) && count($promos) > 0){
									foreach($promos as $key=>$value){
							?>
										<option value="<?php echo $value["code"]; ?>"><?php echo $value["title"]; ?></option>
							<?php
									}
								}
							?>
							<option selected="selected" value="none"><?php echo JText::_("GURU_NONE"); ?></option>
						</select>
						<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PROMOCODE"); ?>" >
							<img border="0" src="components/com_guru/images/icons/tooltip.png">
						</span>
					</td>
					<td></td>
				</tr>
				<tr>
					<td><?php echo JText::_("GURU_ORDAMOUNT"); ?></td>
					<td id="amount"></td>
					<td></td>
				</tr>
				<tr>
					<td><?php echo JText::_("GURU_DISCOUNT"); ?></td>
					<td id="discount_collumn"></td>
					<td></td>
				</tr>
				<tr>
					<td><?php echo JText::_("GURU_TOTAL"); ?></td>
					<td id="total_column"></td>
					<td></td>
				</tr>
				<tr>
					<td><?php echo JText::_("VIEWORDERSAMOUNTPAID"); ?></td>
					<td>
                    <?php if($currencypos == 0){?>
						<span id="currency_amount_paid"><?php echo $character; ?></span>
                     <?php }?>   
						<span><input type="text" value="" name="amount_paid" id="amount_paid"></span>
                        <?php if($currencypos == 1){?>
						<span id="currency_amount_paid"><?php echo $character; ?></span>
                     <?php }?> 
						<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_VIEWORDERSAMOUNTPAID"); ?>" >
							<img border="0" src="components/com_guru/images/icons/tooltip.png">
						</span>
					</td>
					<td></td>
				</tr>
			</table>			

			<div style="border-top: 1px solid rgb(204, 204, 204); padding-top: 5px;">
				<input type="button" class="btn" value="<?php echo JText::_("GURU_SAVE_PROGRAM_BTN"); ?>" name="button" onclick="document.adminForm.task.value='saveorder'; submitform();">
			</div>
	</fieldset>
	
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="guruOrders" />
	<input type="hidden" name="userid" value="<?php echo $user_id; ?>" />
	<input type="hidden" name="username" value="<?php echo $username; ?>" />
	<input type="hidden" id="nr_licenses" name="nr_licenses" value="0" />
	<input type="hidden" name="total" id="total" value="0" />
	<input type="hidden" name="amount_hidden" id="amount_hidden" value="0" />
    <input type="hidden" name="id_selected" id="id_selected" value="" />
    <input type="hidden" name="courses_ids_code_generate" id="courses_ids_code_generate" value="" />
</form>
