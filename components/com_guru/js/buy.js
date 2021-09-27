function formatNumberSeparator(price){
	var thousands_separator = document.getElementById("thousands-separator").value;
	var decimals_separator = document.getElementById("decimals-separator").value;

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

function update_cart(course_id, price_id, all_ids, simbol){
	price = 0;
	show_decimals = document.getElementById("show-decimals").value;
	
	if(eval(document.getElementById("plan_selected_value_"+price_id+"_"+course_id).value)){
		price = document.getElementById("plan_selected_value_"+price_id+"_"+course_id).value;
	}

	if(price < 0){
		price = 0.00;	
	}

	if(show_decimals == 1){
		document.getElementById("cart_item_price"+course_id).innerHTML = simbol+" "+formatNumberSeparator(parseFloat(price).toFixed(0));
		document.getElementById("cart_item_total"+course_id).innerHTML = simbol+" "+formatNumberSeparator(parseFloat(price).toFixed(0));
	}
	else{
		document.getElementById("cart_item_price"+course_id).innerHTML = simbol+" "+formatNumberSeparator(parseFloat(price).toFixed(2));
		document.getElementById("cart_item_total"+course_id).innerHTML = simbol+" "+formatNumberSeparator(parseFloat(price).toFixed(2));
	}
	
	setMaxTotal(simbol, all_ids);
}

function removeCourse(course_id, all_ids, action, cart_empty, link_redirect, click_here, simbol, site_url){
	var id = "row_" + course_id;
	var table = document.getElementById("g_table_cart");
	var row = document.getElementById(id);	
	table.deleteRow(row.rowIndex);
	jQuery.ajax({
			async: true,
			url: site_url + "index.php?option=com_guru&controller=guruBuy&task=deletefromsession&course_id="+course_id+"&action="+action,
			method: 'get',
			data: { 'do' : '1' },
			success: function(response){		
			}
		});
	
	setMaxTotal(simbol, all_ids, cart_empty, link_redirect, click_here, simbol);
}
//start bootstrap
function update_cartb(course_id, price, all_ids, simbol){
	if(price < 0){
		price = 0.00;	
	}
	
	show_decimals = document.getElementById("show-decimals").value;
	
	total_id = "cart_item_totalb"+course_id;

	if(show_decimals == 1){
		document.getElementById(total_id).innerHTML = simbol+" "+parseFloat(price).toFixed(0);
		document.getElementById("max_totalb1").innerHTML = simbol+" "+parseFloat(price).toFixed(0);
		document.getElementById("max_totalb").innerHTML = simbol+" "+parseFloat(price).toFixed(0);
	}
	else{
		document.getElementById(total_id).innerHTML = simbol+" "+parseFloat(price).toFixed(2);
		document.getElementById("max_totalb1").innerHTML = simbol+" "+parseFloat(price).toFixed(2);
		document.getElementById("max_totalb").innerHTML = simbol+" "+parseFloat(price).toFixed(2);
	}

	setMaxTotalB(simbol, all_ids);
}
function removeCourseB(course_id, all_ids, action, cart_empty, link_redirect, click_here, simbol){
	var id = "row1_" + course_id;
	var table = document.getElementById("divuniq");
	var row = document.getElementById(id);	
	table.removeChild(row);
	var url = document.location.toString() ; //url
	var e_url = '' ; //edited url
	var p = 0 ; //position
	var p2 = 0 ;//position 2
	p = url.indexOf("//") ;
	e_url = url.substring(p+2) ;
	p2 = e_url.indexOf("/") ;
	var root_url = url.substring(0,p+p2+3);
	
	jQuery.ajax({
		async: true,
		url: root_url+"index.php?option=com_guru&controller=guruBuy&task=deletefromsession&course_id="+course_id+"&action="+action,
		method: 'get',
		data: { 'do' : '1' },
		success: function(response){	
		}
	});
	
	setMaxTotalB(simbol, all_ids, cart_empty, link_redirect, click_here, simbol);
}

function trim(str) {
	return str.replace(/^\s+|\s+$/g,"");
}

function setMaxTotalB(simbol, all_ids, cart_empty, link_redirect, click_here){
	var ids_array = new Array();
	ids_array = all_ids.split(',');
	 
	max_total = 0.0;
	nr_courses_after_delete = 0;
	var show_decimals = document.getElementById("show-decimals").value;
	
	for(var i=0; i<ids_array.length; i++){
		if(document.getElementById("cart_item_totalb"+ids_array[i])){
			element_id = "cart_item_totalb"+ids_array[i];
			element = document.getElementById(element_id);
			div_content = element.innerHTML + "";
			div_content = trim(div_content);
			
			div_content = div_content.replace(simbol, "");
			div_content = div_content.replace(" ", "");
			div_content = parseFloat(div_content);
			
			max_total += div_content;
			
			nr_courses_after_delete ++;
		}
	}
		
	if(nr_courses_after_delete == 0){
		document.getElementById("guru_cart1").innerHTML = cart_empty+", <a href=\""+link_redirect+"\">"+click_here+"</a>";
	}
	else{
		if(show_decimals == 1){
			document.getElementById("max_totalb").innerHTML = simbol+" " + max_total.toFixed(0);
			document.getElementById("max_totalb1").innerHTML = simbol+" " + max_total.toFixed(0);
		}
		else{
			document.getElementById("max_totalb").innerHTML = simbol+" " + max_total.toFixed(2);
			document.getElementById("max_totalb1").innerHTML = simbol+" " + max_total.toFixed(2);
		}
	}
}
//end bootstrap
function setMaxTotal(simbol, all_ids, cart_empty, link_redirect, click_here){
	var ids_array = new Array();
	ids_array = all_ids.split(',');
	
	max_total = 0.0;
	nr_courses_after_delete = 0;

	var thousands_separator = document.getElementById("thousands-separator").value;
	var decimals_separator = document.getElementById("decimals-separator").value;
	var show_decimals = document.getElementById("show-decimals").value;

	for(var i=0; i<ids_array.length; i++){
		if(document.getElementById("cart_item_total"+ids_array[i])){
			value = document.getElementById("cart_item_total"+ids_array[i]).innerHTML + "";
			value = value.replace(simbol, "");
			value = value.replace(" ", "");

			if(thousands_separator == "0" && decimals_separator == "1"){
				value = value.replace(".", "");
				value = value.replace(",", ".");
			}
			else if(thousands_separator == "1" && decimals_separator == "0"){
				value = value.replace(",", "");
			}
			else if(thousands_separator == "2" && decimals_separator == "0"){
				value = value.replace(" ", "");
			}
			else if(thousands_separator == "2" && decimals_separator == "1"){
				value = value.replace(" ", "");
				value = value.replace(",", ".");
			}
			else if(thousands_separator == "0" && decimals_separator == "2"){
				value = value.replace(".", "");
				value = value.replace(" ", ".");
			}
			else if(thousands_separator == "1" && decimals_separator == "2"){
				value = value.replace(",", "");
				value = value.replace(" ", ".");
			}

			value = parseFloat(value);
			
			max_total += value;
			nr_courses_after_delete ++;
		}
	}
	
	if(nr_courses_after_delete == 0){
		document.getElementById("guru_cart").innerHTML = cart_empty+", <a href=\""+link_redirect+"\">"+click_here+"</a>";
	}
	else{
		if(show_decimals == 1){
			document.getElementById("max_total").innerHTML = simbol+" " + formatNumberSeparator(max_total.toFixed(0));
		}
		else{
			document.getElementById("max_total").innerHTML = simbol+" " + formatNumberSeparator(max_total.toFixed(2));
		}
	}
}

function digiChangePayment($payment){
	if($payment == "stripe"){
		jQuery("#stripe-form").show();
		jQuery("#paypalpro-form").hide();
		jQuery("#mobilpay-form").hide();
	}
	else if($payment == "paypalpro"){
		jQuery("#stripe-form").hide();
		jQuery("#paypalpro-form").show();
		jQuery("#mobilpay-form").hide();
	}
	else if($payment == "mobilpay"){
		jQuery("#stripe-form").hide();
		jQuery("#paypalpro-form").hide();
		jQuery("#mobilpay-form").show();
	}
	else{
		jQuery("#stripe-form").hide();
		jQuery("#paypalpro-form").hide();
		jQuery("#mobilpay-form").hide();
	}
}

jQuery( document ).ready(function() {
	jQuery("#stripe-form").insertAfter(".uk-alert.uk-margin-top.guru-plugin-filter");
	jQuery("#paypalpro-form").insertAfter(".uk-alert.uk-margin-top.guru-plugin-filter");
	jQuery("#mobilpay-form").insertAfter(".uk-alert.uk-margin-top.guru-plugin-filter");
	
	var processor = jQuery("#processor").val();

	if(processor == "stripe"){
		jQuery("#stripe-form").show();
		jQuery("#paypalpro-form").hide();
		jQuery("#mobilpay-form").hide();
	}
	else if(processor == "paypalpro"){
		jQuery("#stripe-form").hide();
		jQuery("#paypalpro-form").show();
		jQuery("#mobilpay-form").hide();
	}
	else if(processor == "mobilpay"){
		jQuery("#stripe-form").hide();
		jQuery("#paypalpro-form").hide();
		jQuery("#mobilpay-form").show();
	}
	else{
		jQuery("#stripe-form").hide();
		jQuery("#paypalpro-form").hide();
		jQuery("#mobilpay-form").hide();
	}
});

function validateCardCart(){
	var current_total = parseFloat(jQuery("#current-total").val());
	var task = jQuery("#guru-task").val();

	if(task == "updatecart"){
		document.adminForm.submit();
		return true;
	}

	if(jQuery("#processor").val() != "stripe" && jQuery("#processor").val() != "paypalpro" && jQuery("#processor").val() != "mobilpay"){
		document.adminForm.submit();
		return true;
	}
	else if(jQuery("#processor").val() == "stripe"){
		if(jQuery(".stripe_first_name").length == 0 && current_total > 0){
			alert($("#log-in-first").val());
			return false
		}

		if(jQuery(".stripe_first_name").val() == "" && current_total > 0){
			alert(jQuery("#lang-first-name").val());
			return false;
		}

		if(jQuery(".stripe_last_name").val() == "" && current_total > 0){
			alert(jQuery("#lang-last-name").val());
			return false;
		}

		if(jQuery(".card-number").val() == "" && current_total > 0){
			alert(jQuery("#lang-card").val());
			return false;
		}

		if(jQuery(".card_cvc").val() == "" && current_total > 0){
			alert(jQuery("#lang-card-code").val());
			return false;
		}

		if(current_total <= 0){
			document.adminForm.submit();
			return;
		}

		jQuery(".payment-errors").html("");
		jQuery(".stripe-loading").show();
		var public_key = jQuery("#public-key").val();
		Stripe.setPublishableKey(public_key);

		var returnCheck = Stripe.createToken({
			number: jQuery('.card-number').val(),
			cvc: jQuery('.card-cvc').val(),
			exp_month: jQuery('.card-expiry-month').val(),
			exp_year: jQuery('.card-expiry-year').val()
		}, stripeResponseHandler);
	}
	else if(jQuery("#processor").val() == "paypalpro"){
		if(jQuery("#card_number").length == 0 && current_total > 0){
			alert($("#log-in-first").val());
			return false
		}

		if(current_total <= 0){
			document.adminForm.submit();
			return;
		}

		if (cardFormValidate()) {
			document.adminForm.submit();
        }
	}
	else if(jQuery("#processor").val() == "mobilpay"){
		document.adminForm.submit();
		return;
	}
}