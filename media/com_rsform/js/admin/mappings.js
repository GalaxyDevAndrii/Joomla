Joomla.submitbutton = function(task) {
	var method = parseInt(jQuery('[name="jform[method]"]').val()),
		$inputs;

	// Delete
	if (method === 2)
	{
		$inputs = jQuery('#rsfpmappingWhere').find('input[type="text"], textarea');
	}
	else
	{
		$inputs = jQuery('#rsfpmappingColumns').find('input[type="text"], textarea');
	}

    var hasValues = false;

	$inputs.each(function(){
		if (jQuery(this).val().length > 0) {
			hasValues = true;
		}
	});

	if (!hasValues) {
		Joomla.renderMessages({'error': [Joomla.JText._('COM_RSFORM_PLEASE_ADD_SOME_DATA_TO_YOUR_COLUMNS_BEFORE_SAVING')]});
		return false;
	}

	jQuery('input, select').prop('disabled', false);

    Joomla.submitform(task);
}

function mappingConnectionDetails() {
    var $connectionDetails = jQuery('#connectionDetails'),
        $connectionFields = $connectionDetails.find('input, select'),
        initial = $connectionFields.prop('disabled'),
        params;

    // We remove the disabled attribute so we can get the values
    $connectionFields.prop('disabled', false);

    // Get the values
    params = $connectionFields.serialize();

    $connectionFields.prop('disabled', initial);

    return params;
}

function getMappingTable()
{
    return jQuery('[name="jform[table]"]');
}

function mappingConnect() {
    var $loader = jQuery('#mappingloader'),
        $connectionDetails = jQuery('#connectionDetails'),
        $connectionFields = $connectionDetails.find('input, select'),
        $connectButton = jQuery('#connectBtn'),
        xmlHttp = new XMLHttpRequest(),
        params = mappingConnectionDetails();

    $loader.show();

    xmlHttp.open("POST", 'index.php?option=com_rsform&task=mappings.gettables', true);

    // Send the proper header information along with the request
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState === 4) {
            var response = JSON.parse(xmlHttp.responseText);

            $loader.hide();

            if (typeof response.message !== 'undefined')
            {
                Joomla.renderMessages({'error': [response.message]});
            }
            else
            {
                var $table = getMappingTable();

                $table.prop('disabled', false);

                // Create the 'Please select' option
                var option = document.createElement('option');
                option.value = '';
                option.text = Joomla.JText._('RSFP_FORM_MAPPINGS_SELECT_TABLE');

                // Remove options
                $table.empty();

                // Append the default
                $table.append(option);

                // Append tables
                if (typeof response.tables === 'object')
                {
                    for (var i = 0; i < response.tables.length; i++)
                    {
                        option = document.createElement('option');
                        option.value = option.text = response.tables[i];

                        $table.append(option);
                    }
                }

                // We don't need this button anymore
                $connectButton.hide();

                // Everything is now disabled
                $connectionFields.prop('disabled', true);
            }
        }
    };

    xmlHttp.send(params);
}

function mappingColumns(table) {
    var params = mappingConnectionDetails(),
        $loader = jQuery('#mappingloader2'),
        $saveButton = jQuery('#saveBtn'),
        xmlHttp = new XMLHttpRequest(),
        method = parseInt(jQuery('[name="jform[method]"]').val());

    params += '&' + encodeURIComponent('jform[table]') + '=' + encodeURIComponent(table);
    params += '&type=set';
    params += '&cid=' + jQuery('#mappingid').val();

    $loader.show();

    xmlHttp.open("POST", 'index.php?option=com_rsform&task=mappings.getcolumns', true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState === 4) {
            $loader.hide();

            // DELETE doesn't have a 'SET'
            if (method !== 2)
            {
                jQuery('#rsfpmappingColumns').html(xmlHttp.responseText);
            }

            // 'WHERE' is used only for UPDATE and DELETE
            if ([1,2].indexOf(method) > -1)
            {
                mappingWhere(table);
            }

            jQuery(document).trigger('renderedMappings');

            $saveButton.show();
        }
    };

    xmlHttp.send(params);
}

function mappingWhere(table) {
    var params = mappingConnectionDetails(),
        $loader = jQuery('#mappingloader2'),
        xmlHttp = new XMLHttpRequest();

    params += '&' + encodeURIComponent('jform[table]') + '=' + encodeURIComponent(table);
    params += '&table=' + table;
    params += '&type=where';
    params += '&cid=' + jQuery('#mappingid').val();

    $loader.show();

    xmlHttp.open("POST", 'index.php?option=com_rsform&task=mappings.getcolumns', true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState === 4) {
            jQuery('#rsfpmappingWhere').html(xmlHttp.responseText);

            $loader.hide();

            jQuery(document).trigger('renderedRsfpmappingWhere', 'rsfpmappingWhere');
        }
    };
    xmlHttp.send(params);
}