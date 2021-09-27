if (typeof RSFormPro != 'object') {
	var RSFormPro = {};
}

RSFormPro.isJ4 = 'Event' in Joomla;

RSFormPro.$ = jQuery;

function initRSFormPro() {
	jQuery("#properties").click(function () {
		jQuery("#rsform_properties_tab").removeClass('rsfp-hidden');
		jQuery("#rsform_components_tab").addClass('rsfp-hidden');
		jQuery("#components").removeClass('btn-primary active').addClass('btn-outline-secondary');
		jQuery("#properties").addClass('btn-primary active').removeClass('btn-outline-secondary');
	});

	jQuery("#components").click(function () {
		jQuery("#rsform_components_tab").removeClass('rsfp-hidden');
		jQuery("#rsform_properties_tab").addClass('rsfp-hidden');
		jQuery("#properties").removeClass('btn-primary active').addClass('btn-outline-secondary');
		jQuery("#components").addClass('btn-primary active').removeClass('btn-outline-secondary');

		jQuery('#componentscontent').trigger('components.shown');
	});

	jQuery('[data-placeholders]').rsplaceholder();

	initMappingsOrdering(false);
	initCalculationsOrdering(false);

	jQuery(document).on('renderedMappings', function(){
		jQuery('[data-placeholders]').rsplaceholder();
	});

	jQuery(document).on('renderedRsfpmappingWhere', function(event, element){
		jQuery('#'+element).find('[data-placeholders]').rsplaceholder();
	});

	jQuery(document).on('renderedSilentPostField', function($event, $field_one, $field_two){
		jQuery($field_one).find('input').rsplaceholder();
		jQuery($field_two).find('input').rsplaceholder();
	});

	jQuery(document).on('renderedCalculationsFields', function($event){
		jQuery('#calculationsContents [data-placeholders]').rsplaceholder();
	});
}

function initMappingsOrdering(update_php) {
	jQuery('#mappingTable tbody').tableDnD({
		onDragClass: 'rsform_dragged',
		onDragStop : function () {
			if (typeof update_php === 'undefined') {
				update_php = false;
			}

			stateLoading();

			var params = [];
			var orders = document.getElementsByName('mporder[]');
			var cids = document.getElementsByName('mpid[]');
			var newValue = 0,
				oldValue = 0;

			for (var i = 0; i < orders.length; i++) {
				newValue = parseInt(i + 1);
				oldValue = parseInt(orders[i].value);

				if (oldValue !== newValue) {
					update_php = true;
				}

				orders[i].value = newValue;

				params.push('cid[' + cids[i].value + ']' + '=' + newValue);
			}

			if (update_php) {
				var xml = buildXmlHttp();
				xml.open("POST", 'index.php?option=com_rsform&task=mappings.saveordering', true);

				params = params.join('&');

				//Send the proper header information along with the request
				xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

				xml.send(params);
				xml.onreadystatechange = function () {
					if (xml.readyState === 4) {
						stateDone();
					}
				}
			} else {
				stateDone();
			}
		}
	});
}

function initCalculationsOrdering(update_php) {
	jQuery('#calculationsTable').tableDnD({
		onDragClass: 'rsform_dragged',
		onDragStop : function () {
			if (typeof update_php === 'undefined') {
				update_php = false;
			}

			stateLoading();

			var orders = document.getElementsByName('calcorder[]');
			var cids = document.getElementsByName('calcid[]');
			var formId = document.getElementById('formId').value;
			var params = ['formId=' + formId];
			var newValue = 0,
				oldValue = 0;

			for (var i = 0; i < orders.length; i++) {
				newValue = parseInt(i + 1);
				oldValue = parseInt(orders[i].value);

				if (oldValue !== newValue) {
					update_php = true;
				}

				orders[i].value = newValue;

				params.push('cid[' + cids[i].value + ']' + '=' + newValue);
			}

			if (update_php) {
				var xml = buildXmlHttp();

				var url = 'index.php?option=com_rsform&task=calculations.saveOrdering';
				xml.open("POST", url, true);

				params = params.join('&');

				//Send the proper header information along with the request
				xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

				xml.send(params);
				xml.onreadystatechange = function () {
					if (xml.readyState === 4) {
						stateDone();
					}
				}
			} else {
				stateDone();
			}
		}
	});
}

function buildXmlHttp() {
	return new XMLHttpRequest();
}

function displayTemplate(componentTypeId, componentId) {
	RSFormPro.editModal.display(componentTypeId, componentId);
}

function removeComponent(componentId) {
	stateLoading();

	var formId = document.getElementById('formId').value;

	// Build URL to post to
	var url = 'index.php?option=com_rsform&task=components.remove';

	// Build data array
	var data = {
		'ajax'  : 1,
		'cid[]' : componentId,
		'formId': formId
	};

	jQuery.post(url, data, function (response, status, jqXHR) {

		RSFormPro.Grid.deleteField(componentId);

		if (!response.submit) {
			jQuery('#rsform_submit_button_msg').show();
		}

		stateDone();
	}, 'json');
}

function processComponent(componentType) {
	RSFormPro.editModal.disableButton();

	jQuery('#rsformerror0').hide();
	jQuery('#rsformerror1').hide();
	jQuery('#rsformerror2').hide();
	jQuery('#rsformerror3').hide();

	stateLoading();

	// Build URL to post to
	var url = 'index.php?option=com_rsform&task=components.validatename';

	// Build data array
	var data = {
		'componentName'     : jQuery('#NAME').val(),
		'formId'            : jQuery('#formId').val(),
		'currentComponentId': jQuery('#componentIdToEdit').val(),
		'componentType'     : componentType
	};

	if (componentType == 9) {
		data['destination'] = jQuery('#DESTINATION').val();
	}

	jQuery.post(url, data, function (response, status, jqXHR) {
		if (response.result == false) {
			// Switch to tab
			jQuery('[href="#rsfptab' + response.tab + '"]').click();

			// Show error message
			jQuery('#rsformerror' + response.tab).text(response.message).show();

			stateDone();

			RSFormPro.editModal.enableButton();
		} else {
			Joomla.submitbutton('components.save');
		}
	}, 'json');
}

function autoGenerateLayout()
{
    if (jQuery('[name=FormLayoutAutogenerate]:checked').val() === '1')
    {
        generateLayout(false);
    }
}

function changeFormLayoutFlow()
{
    stateLoading();

    // Build URL to post to
    var url = 'index.php?option=com_rsform&task=forms.changeFormLayoutFlow';

    // Build data array
    var data = {
        'status': jQuery('[name=FormLayoutFlow]').val(),
        'formId': document.getElementById('formId').value
    };

    jQuery.post(url, data, function (response, status, jqXHR) {
        stateDone();

        autoGenerateLayout();
    }, 'json');
}

function changeFormAutoGenerateLayout(value) {
	var formId = document.getElementById('formId').value;

	stateLoading();

	// Build URL to post to
	var url = 'index.php?option=com_rsform&task=forms.changeAutoGenerateLayout';

	// Build data array
	var data = {
		'formLayoutName': jQuery('[name=FormLayoutName]:checked').val(),
		'formId'        : formId,
		'status'        : value
	};

	jQuery.post(url, data, function (response, status, jqXHR) {
		var hasCodeMirror = typeof Joomla.editors.instances['formLayout'] != 'undefined';

		if (value === '0') {
			Joomla.renderMessages({'warning': [Joomla.JText._('RSFP_AUTOGENERATE_LAYOUT_DISABLED')]}, '#componentsMessages');
		} else {
			Joomla.removeMessages(document.getElementById('componentsMessages'));
		}

		jQuery('#formLayout').prop('readonly', value === '1');

		if (hasCodeMirror) {
			Joomla.editors.instances['formLayout'].setOption('readOnly', value === '1');
		}

		stateDone();
	}, 'json');
}

function generateLayout(alert) {
	if (alert && !confirm(Joomla.JText._('RSFP_AUTOGENERATE_LAYOUT_WARNING_SURE'))) {
		return;
	}

	stateLoading();

	// Build URL to post to
	var url = 'index.php?option=com_rsform&task=layouts.generate';
	var formId = document.getElementById('formId').value;

	// Build data array
	var data = {
		'layoutName': jQuery('[name=FormLayoutName]:checked').val(),
		'formId'    : formId
	};

	jQuery.post(url, data, function (response, status, jqXHR) {
		var hasCodeMirror = typeof Joomla.editors.instances['formLayout'] != 'undefined';

		jQuery('#formLayout').val(response);
		if (hasCodeMirror)
		{
			Joomla.editors.instances['formLayout'].setValue(response);
		}

		stateDone();
	}, 'text');
}

function saveLayoutName(layoutName) {
	var formId = document.getElementById('formId').value;

	stateLoading();
	var xml = buildXmlHttp();
	xml.open('GET', 'index.php?option=com_rsform&task=layouts.savename&formId=' + formId + '&formLayoutName=' + layoutName, true);
	xml.send(null);
	xml.onreadystatechange = function () {
		if (xml.readyState === 4) {
			autoGenerateLayout();

			stateDone();
		}
	};
}

function stateLoading() {
	document.getElementById('state').style.display = '';
}

function stateDone() {
	document.getElementById('state').style.display = 'none';
}

function number_format(number, decimals, dec_point, thousands_sep) {
	var n = number, prec = decimals;
	n = !isFinite(+n) ? 0 : +n;
	prec = !isFinite(+prec) ? 0 : Math.abs(prec);
	var sep = (typeof thousands_sep == "undefined") ? ',' : thousands_sep;
	var dec = (typeof dec_point == "undefined") ? '.' : dec_point;

	var s = (prec > 0) ? n.toFixed(prec) : Math.round(n).toFixed(prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;

	var abs = Math.abs(n).toFixed(prec);
	var _, i;

	if (abs >= 1000) {
		_ = abs.split(/\D/);
		i = _[0].length % 3 || 3;

		_[0] = s.slice(0, i + (n < 0)) +
			_[0].slice(i).replace(/(\d{3})/g, sep + '$1');

		s = _.join(dec);
	} else {
		s = s.replace('.', dec);
	}

	return s;
}

function changeValidation(elem) {
	if (elem == null) return;
	if (elem.id == 'VALIDATIONRULE') {
		if (document.getElementById('idVALIDATIONEXTRA')) {
			if (elem.value == 'regex') {
                theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRAREGEX');
			} else if (elem.value == 'sameas') {
                theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRASAMEAS');
			} else {
				theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRA');
			}
			document.getElementById('captionVALIDATIONEXTRA').innerHTML = theText;

			if (elem.value == 'custom' || elem.value == 'numeric' || elem.value == 'alphanumeric' || elem.value == 'alpha' || elem.value == 'regex' || elem.value == 'sameas')
				document.getElementById('idVALIDATIONEXTRA').className = 'showVALIDATIONEXTRA control-group';
			else
				document.getElementById('idVALIDATIONEXTRA').className = 'hideVALIDATIONEXTRA control-group';
		}
		
		var multipleRulesField = document.getElementById('idVALIDATIONMULTIPLE');
		if (elem.value == 'multiplerules') {
			multipleRulesField.style.display = 'block';
			changeValidation(document.getElementById('VALIDATIONMULTIPLE'));
		} else {
			multipleRulesField.style.display = 'none';
			document.getElementById('VALIDATIONEXTRA').name='param[VALIDATIONEXTRA]';
			
			// if the saved extra value of the multiple rule exist in the current rule selection keep it, if no leave it as it is
			var savedExtra = document.getElementById('VALIDATIONEXTRA').value;
			try {
				eval('var savedExtraObject='+savedExtra);
			} catch(e) {
				var savedExtraObject = {};
			}
			
			if (typeof savedExtraObject == 'object' && typeof savedExtraObject[elem.value] != 'undefined') {
				document.getElementById('VALIDATIONEXTRA').value = savedExtraObject[elem.value];
			}
			
			// remove previous created extra validations for the multiple validation
			var previousExtras = document.querySelectorAll('.mValidation');
			for (i = 0; i < previousExtras.length; i++) {
				previousExtras[i].parentNode.removeChild(previousExtras[i]);
			} 
		}
	} else if (elem.id == 'VALIDATIONMULTIPLE') {
		var selectedValues = [];
		for (i = 0; i < elem.length; i++) {
			if (elem[i].selected && (elem[i].value == 'custom' || elem[i].value == 'numeric' || elem[i].value == 'alphanumeric' || elem[i].value == 'alpha' || elem[i].value == 'regex' || elem[i].value == 'sameas')) {
				selectedValues.push(elem[i].value);
			}
		}
		
		// remove previous created extra validations
		var previousExtras = document.querySelectorAll('.mValidation');
		for (i = 0; i < previousExtras.length; i++) {
			previousExtras[i].parentNode.removeChild(previousExtras[i]);
		} 
		
		// set the name of the normal validation to 'empty'
		document.getElementById('VALIDATIONEXTRA').name='';
		
		// the default validation extra value if already saved
		var savedExtra = document.getElementById('VALIDATIONEXTRA').value;
		try {
			eval('var savedExtraObject='+savedExtra);
		} catch(e) {
			var savedExtraObject = {};
		}
		
		var clonedElement = document.getElementById('idVALIDATIONEXTRA').cloneNode(true);
		clonedElement.removeAttribute('id');
		jQuery(clonedElement).removeClass('hideVALIDATIONEXTRA');
		
		var afterElement = document.getElementById('idVALIDATIONMULTIPLE');
		
		for (var i = 0; i < selectedValues.length; i++) {
			var newclonedElement = clonedElement.cloneNode(true);
			jQuery(newclonedElement).addClass('mValidation '+selectedValues[i]);
			
			var captionElement = newclonedElement.querySelector('#captionVALIDATIONEXTRA');
			var validationElement = newclonedElement.querySelector('#VALIDATIONEXTRA');
			
			captionElement.id='captionValidation'+selectedValues[i];
			validationElement.id='Validation'+selectedValues[i];
			validationElement.name="param[VALIDATIONEXTRA]["+selectedValues[i]+"]";
			if (typeof savedExtraObject[selectedValues[i]] != 'undefined') {
				validationElement.value = savedExtraObject[selectedValues[i]];
			} else {
				validationElement.value = '';
			}
			
			if (selectedValues[i] == 'regex') {
                theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRAREGEX');
			} else if (selectedValues[i] == 'sameas') {
                theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRASAMEAS');
			} else {
				theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRA');
			}
			
			jQuery(document.getElementById('VALIDATIONRULE').options).each(function(){
				if (this.value == selectedValues[i])
				{
					theText = this.text + ' - ' + theText;
				}
			});
			
			captionElement.innerHTML = theText;
			
			afterElement.parentNode.insertBefore(newclonedElement, afterElement.nextSibling);
		}
		
	}
}

function toggleQuickAdd() {
	jQuery('.QuickAdd').toggle();
}

function removeEmail(id, type) {
	stateLoading();

	var formId = document.getElementById('formId').value;
	var params = [
		'cid=' + id,
		'formId=' + formId,
		'type=' + type
	];
	var xmlHttp = buildXmlHttp();

	xmlHttp.open("POST", 'index.php?option=com_rsform&task=emails.remove', true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState === 4) {
			stateDone();

			document.getElementById('emailsContent').innerHTML = xmlHttp.responseText;
		}
	};
	xmlHttp.send(params.join('&'));
}

function updateEmails(type) {
	stateLoading();

	var formId = document.getElementById('formId').value;
	var params = [
		'formId=' + formId,
		'type=' + type,
	];
	var xmlHttp = buildXmlHttp();

	xmlHttp.open("POST", 'index.php?option=com_rsform&task=emails.update', true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState === 4) {
			stateDone();

			document.getElementById('emailsContent').innerHTML = xmlHttp.responseText;
		}
	};
	xmlHttp.send(params.join('&'));
}

function conditionDelete(cid) {
	stateLoading();

	var formId = document.getElementById('formId').value;
	var params = [
		'formId=' + formId,
		'cid=' + cid,
	];
	var xmlHttp = buildXmlHttp();

	xmlHttp.open("POST", 'index.php?option=com_rsform&task=conditions.remove', true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState === 4) {
			stateDone();

			document.getElementById('conditionsContent').innerHTML = xmlHttp.responseText;
		}
	};
	xmlHttp.send(params.join('&'));
}

function showConditions() {
	stateLoading();

	var formId = document.getElementById('formId').value;
	var params = [
		'formId=' + formId
	];
	var xmlHttp = buildXmlHttp();

	xmlHttp.open("POST", 'index.php?option=com_rsform&task=conditions.showconditions', true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState === 4) {
			stateDone();

			document.getElementById('conditionsContent').innerHTML = xmlHttp.responseText;
		}
	};
	xmlHttp.send(params.join('&'));
}

function openRSModal(href, type, size) {
	if (!type)
		type = 'Richtext';
	if (!size)
		size = '600x500';
	size = size.split('x');

	window.open(href, type, 'width=' + size[0] + ', height=' + size[1] + ',scrollbars=1');
}

function showCalculations() {
	var formId = document.getElementById('formId').value,
		params = [
			'formId=' + formId
		],
		xmlHttp = buildXmlHttp();

	stateLoading();

	xmlHttp.open("POST", 'index.php?option=com_rsform&task=calculations.show', true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState === 4) {
			stateDone();

			document.getElementById('calculationsContents').innerHTML = xmlHttp.responseText;
			initCalculationsOrdering(false);
			jQuery(document).trigger('renderedCalculationsFields');
		}
	};
	xmlHttp.send(params.join('&'));
}

function removeCalculation(id) {
	stateLoading();

	var formId = document.getElementById('formId').value,
		params = [
			'formId=' + formId,
			'id=' + id
		],
		xmlHttp = buildXmlHttp();

	xmlHttp.open('POST', 'index.php?option=com_rsform&task=calculations.remove', true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState === 4) {
			stateDone();

			document.getElementById('calculationsContents').innerHTML = xmlHttp.responseText;
			initCalculationsOrdering(false);
			jQuery(document).trigger('renderedCalculationsFields');
		}
	};
	xmlHttp.send(params.join('&'));
}

function mappingDelete(mid) {
	stateLoading();

	var formId = document.getElementById('formId').value;
	var params = [
		'formId=' + formId,
		'mid=' + mid
	];
	var xmlHttp = buildXmlHttp();

	xmlHttp.open("POST", 'index.php?option=com_rsform&task=mappings.remove', true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState === 4) {
			stateDone();

			document.getElementById('mappingsContents').innerHTML = xmlHttp.responseText;
			initMappingsOrdering(true);
		}
	};
	xmlHttp.send(params.join('&'));
}

function mappingsShow() {
	stateLoading();

	var formId = document.getElementById('formId').value;
	var params = [
		'formId=' + formId
	];
	var xmlHttp = buildXmlHttp();

	xmlHttp.open("POST", 'index.php?option=com_rsform&task=mappings.showmappings', true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState === 4) {
			stateDone();

			document.getElementById('mappingsContents').innerHTML = xmlHttp.responseText;
			initMappingsOrdering(true);
		}
	};
	xmlHttp.send(params.join('&'));
}

function validateEmailFields() {
    var fields = [
        'UserEmailFrom', 'UserEmailTo', 'UserEmailReplyTo', 'UserEmailCC', 'UserEmailBCC',
        'AdminEmailFrom', 'AdminEmailTo', 'AdminEmailReplyTo', 'AdminEmailCC', 'AdminEmailBCC',
        'DeletionEmailFrom', 'DeletionEmailTo', 'DeletionEmailReplyTo', 'DeletionEmailCC', 'DeletionEmailBCC'
    ];

    var result = true;
    var fieldName, field, fieldValue, values, value, match;
    var pattern = /{.*?}/g;

    var hasPlaceholder, wrongPlaceholder, notAnEmail, wrongDelimiter;

    for (var i = 0; i < fields.length; i++) {
        // Grab field name from array
        fieldName 	= fields[i];
        field 		= document.getElementById(fieldName);
        // Grab value
        fieldValue 	= field.value;

        jQuery(field).removeClass('rs_error_field');

        // Something's been typed in
        if (fieldValue.length > 0) {
            // Check for multiple values
            values = fieldValue.split(',');

            for (var v = 0; v < values.length; v++) {
                value = values[v].replace(/^\s+|\s+$/gm,'');

                // Has placeholder
                hasPlaceholder = value.indexOf('{') > -1 && value.indexOf('}') > -1;

                // Defaults to false, the code below will actually check the placeholder
                wrongPlaceholder = false;

                // Let's take into account multiple placeholders
                if (hasPlaceholder) {
                    do {
                        match = pattern.exec(value);
                        if (match && typeof match[0] !== 'undefined') {
                            // Wrong placeholder
                            if (RSFormPro.Placeholders.indexOf(match[0]) === -1) {
                                wrongPlaceholder = true;
                            }
                        }
                    } while (match);
                }

                // Not an email
                notAnEmail = !hasPlaceholder && value.indexOf('@') === -1;
                // A situation where we have a wrong delimiter thus ending up in multiple @ addresses
                wrongDelimiter = !hasPlaceholder && (value.match(/@/g) || []).length > 1;

                if (wrongPlaceholder || notAnEmail || wrongDelimiter) {
                    // Switch to the correct tab only on the first error
                    if (result === true) {
                    	if (wrongPlaceholder)
						{
							Joomla.renderMessages({'error': [Joomla.JText._('COM_RSFORM_EMAIL_FIELD_ERROR_WRONG_PLACEHOLDER').replace('%s', fieldName)]});
						}
                    	if (notAnEmail)
						{
							Joomla.renderMessages({'error': [Joomla.JText._('COM_RSFORM_EMAIL_FIELD_ERROR_NOT_AN_EMAIL').replace('%s', fieldName)]});
						}
                    	if (wrongDelimiter)
						{
							Joomla.renderMessages({'error': [Joomla.JText._('COM_RSFORM_EMAIL_FIELD_ERROR_WRONG_DELIMITER').replace('%s', fieldName)]});
						}

                        jQuery('#properties').click();
                        if (fieldName.indexOf('User') > -1) {
							jQuery('#useremails').click();
						} else if (fieldName.indexOf('Admin') > -1) {
							jQuery('#adminemails').click();
                        } else if (fieldName.indexOf('Deletion') > -1) {
                            jQuery('#deletionemail').click();
                        }
                    }
                    jQuery(field).addClass('rs_error_field');
                    result = false;
                }
            }
        }
    }

    return result;
}

RSFormPro.Post = {};

RSFormPro.Post.addField = function () {
	var $table = jQuery('#com-rsform-post-fields tbody');
	var $row = jQuery('<tr>');

	var $inputName = jQuery('<td><input type="text" id="form_post_name'+ Math.floor((Math.random() * 100000) + 1) +'" data-delimiter=" " data-placeholders="display" name="form_post[name][]" placeholder="' + Joomla.JText._('RSFP_POST_NAME_PLACEHOLDER') + '" class="rs_inp rs_80"></td>');
	var $inputValue = jQuery('<td><input type="text" id="form_post_value'+ Math.floor((Math.random() * 100000) + 1) +'" data-delimiter=" " data-placeholders="display" data-filter-type="include" data-filter="value,global" name="form_post[value][]" placeholder="' + Joomla.JText._('RSFP_POST_VALUE_PLACEHOLDER') + '" class="rs_inp rs_80"></td>');
	var $deleteBtn = jQuery('<td>').append(jQuery('<button type="button" class="btn btn-danger btn-mini"><i class="rsficon rsficon-remove"></i></button>').click(RSFormPro.Post.deleteField));

	$row.append($inputName, $inputValue, $deleteBtn);
	$table.append($row);
	var $object = [$inputName, $inputValue];
	jQuery(document).trigger('renderedSilentPostField', $object);
};

RSFormPro.Post.deleteField = function () {
    if (confirm(Joomla.JText._('RSFP_POST_ARE_YOU_SURE_DELETE_THIS_FIELD'))) {
        jQuery(this).parents('tr').remove();
    }
};

RSFormPro.Post.addHeader = function () {
    var $table = jQuery('#com-rsform-post-headers tbody');
    var $row = jQuery('<tr>');

    var $inputName = jQuery('<td><input type="text" id="form_post_headers_name'+ Math.floor((Math.random() * 100000) + 1) +'" data-delimiter=" " data-placeholders="display" name="form_post[headers_name][]" placeholder="' + Joomla.JText._('RSFP_POST_HEADERS_NAME_PLACEHOLDER') + '" class="rs_inp rs_80"></td>');
    var $inputValue = jQuery('<td><input type="text" id="form_post_headers_value'+ Math.floor((Math.random() * 100000) + 1) +'" data-delimiter=" " data-placeholders="display" data-filter-type="include" data-filter="value,global" name="form_post[headers_value][]" placeholder="' + Joomla.JText._('RSFP_POST_HEADERS_VALUE_PLACEHOLDER') + '" class="rs_inp rs_80"></td>');
    var $deleteBtn = jQuery('<td>').append(jQuery('<button type="button" class="btn btn-danger btn-mini"><i class="rsficon rsficon-remove"></i></button>').click(RSFormPro.Post.deleteHeader));

    $row.append($inputName, $inputValue, $deleteBtn);
    $table.append($row);
    var $object = [$inputName, $inputValue];
    jQuery(document).trigger('renderedSilentPostField', $object);
};

RSFormPro.Post.deleteHeader = function () {
	if (confirm(Joomla.JText._('RSFP_POST_ARE_YOU_SURE_DELETE_THIS_HEADER'))) {
        jQuery(this).parents('tr').remove();
	}
};

RSFormPro.removeFile = function(button) {
	if (button.parentNode)
	{
		button.parentNode.parentNode.removeChild(button.parentNode);
	}
};

jQuery(document).ready(initRSFormPro);