Joomla.submitbutton = function(task) {
	if (task === 'apply' || task === 'save')
	{
		if (document.getElementById('component_id').value === '')
		{
			Joomla.renderMessages({'error': [Joomla.JText._('COM_RSFORM_CONDITION_PLEASE_SELECT_AT_LEAST_ONE_FIELD')]});

			return false;
		}

		if (document.getElementsByName('detail_component_id[]').length === 0)
		{
			Joomla.renderMessages({'error': [Joomla.JText._('COM_RSFORM_CONDITION_PLEASE_ADD_AT_LEAST_ONE_CONDITION')]});

			return false;
		}
	}

	Joomla.submitform(task);
}

function conditionChangeField() {
	var children = this.parentNode.childNodes;

	for (var i = 0; i < children.length; i++) {
		if (children[i].nodeName === 'SELECT' && children[i].getAttribute('name') === 'value[]') {
			children[i].options.length = 0;

			var selected_values = getConditionValues(this.value);
			if (selected_values !== false) {
				for (var j = 0; j < selected_values.length; j++) {
					var option = document.createElement('option');
					option.value = selected_values[j].value;
					option.text = selected_values[j].label;
					children[i].options.add(option);
				}
			}

			break;
		}
	}
}

function addCondition() {
	var optionFields = getConditionOptionFields();
	if (optionFields.length === 0) {
		Joomla.renderMessages({'error': [Joomla.JText._('RSFP_CONDITION_PLEASE_ADD_OPTIONS')]});
		return false;
	}

	var newCondition = document.createElement('p');

	var spacer = document.createElement('span');
	spacer.setAttribute('class', 'rsform_spacer');
	spacer.innerHTML = '&nbsp;&nbsp;&nbsp;';

	var spacer2 = document.createElement('span');
	spacer2.setAttribute('class', 'rsform_spacer');
	spacer2.innerHTML = '&nbsp;&nbsp;&nbsp;';

	var spacer3 = document.createElement('span');
	spacer3.setAttribute('class', 'rsform_spacer');
	spacer3.innerHTML = '&nbsp;&nbsp;&nbsp;';

	// fields
	var fields = document.createElement('select');
	fields.name = 'detail_component_id[]';
	fields.setAttribute('name', 'detail_component_id[]');
	fields.onchange = conditionChangeField;

	var option;
	for (var componentId in optionFields) {
		if (optionFields.hasOwnProperty(componentId)) {
			option 		    = document.createElement('option');
			option.value 	= componentId;
			option.text 	= optionFields[componentId].name;
			fields.options.add(option);
		}
	}

	// operator
	var operator = document.createElement('select');
	operator.setAttribute('class', 'input-small');
	operator.name = 'operator[]';

	option 		    = document.createElement('option');
	option.value 	= 'is';
	option.text 	= Joomla.JText._('RSFP_CONDITION_IS');
	operator.options.add(option);

	option 		    = document.createElement('option');
	option.value 	= 'is_not';
	option.text 	= Joomla.JText._('RSFP_CONDITION_IS_NOT');
	operator.options.add(option);

	// values
	var selected_values = getConditionValues(fields.value);
	var values = document.createElement('select');
	values.name = 'value[]';
	if (selected_values !== false)
	{
		for (var i=0; i<selected_values.length; i++)
		{
			option 		    = document.createElement('option');
			option.value	= selected_values[i].value;
			option.text		= selected_values[i].label;
			values.options.add(option);
		}
	}

	// remove button
	var removeBtn = document.createElement('button');
	removeBtn.setAttribute('type', 'button');
	removeBtn.setAttribute('class', 'btn btn-danger btn-mini');
	removeBtn.onclick = function() {
		this.parentNode.parentNode.removeChild(this.parentNode);
	};

	var removeIcon = document.createElement('i');
	removeIcon.setAttribute('class', 'rsficon rsficon-remove');

	removeBtn.appendChild(removeIcon);

	// Append all elements
	newCondition.appendChild(fields);
	newCondition.appendChild(spacer);
	newCondition.appendChild(operator);
	newCondition.appendChild(spacer2);
	newCondition.appendChild(values);
	newCondition.appendChild(spacer3);
	newCondition.appendChild(removeBtn);

	document.getElementById('conditionsContainer').appendChild(newCondition);
}

function getConditionValues(id) {
	var fields = getConditionOptionFields();

	if (typeof fields[id] === 'undefined' || typeof fields[id].items === 'undefined')
	{
		return false;
	}

	return fields[id].items;
}

window.addEventListener('DOMContentLoaded', function() {
	var detail_component_ids = document.getElementsByName('detail_component_id[]');
	for (var i = 0; i < detail_component_ids.length; i++) {
		detail_component_ids[i].onchange = conditionChangeField;
	}
});