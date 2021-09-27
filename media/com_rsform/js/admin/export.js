Joomla.submitbutton = function(task) {
	if (task === 'submissions.exporttask') {
		var isChecked = jQuery('input[id^=header]:checked').length > 0;

		if (!isChecked) {
			var messages = {"error": []};
			messages.error.push(Joomla.JText._('RSFP_EXPORT_PLEASE_SELECT'));
			Joomla.renderMessages(messages);
			return false;
		}
	}

	Joomla.submitform(task);
}

function toggleExportCheckboxes() {
	jQuery('.exportCheckbox').prop('checked', document.getElementById('checkColumns').checked);

	updateCSVPreview();
}

function updateCSVPreview() {
	var form = document.adminForm;

	if (form.ExportType.value !== 'csv') {
		return;
	}

	var headersPre = document.getElementById('headersPre');
	var rowPre = document.getElementById('rowPre');
	var delimiter = form.ExportDelimiter.value;
	var enclosure = form.ExportFieldEnclosure.value;
	var totalHeaders = jQuery('.exportCheckbox').length;

	var headers = [];
	var previewArray = [];
	var orderArray = [];

	var i, header, order;

	for (i = 1; i <= totalHeaders; i++)
	{
		if (document.getElementById('header' + i).checked)
		{
			header = document.getElementById('header' + i).value;

			order = document.getElementsByName('ExportOrder[' + header + ']')[0].value;
			orderArray.push(order + '_' + header);
		}
	}

	orderArray.sort(function (a,b) {
		a = a.split('_');
		a = a[0];
		b = b.split('_');
		b = b[0];
		return a - b;
	});

	for (i = 0; i < orderArray.length; i++)
	{
		header = orderArray[i].split('_');
		header = enclosure + header[1] + enclosure;

		headers.push(header);
	}

	headersPre.innerHTML = headers.join(delimiter);
	headersPre.style.display = form.ExportHeaders.checked ? '' : 'none';

	for (i = 1; i <= headers.length; i++)
	{
		previewArray.push(enclosure + 'Value ' + i + enclosure);
	}

	rowPre.innerHTML = previewArray.join(delimiter);
}

function exportProcess(start, limit, total) {
	var xml = buildXmlHttp();
	xml.onreadystatechange = function () {
		if (xml.readyState == 4) {
			var post = xml.responseText;
			if (post.indexOf('END') > -1) {
				document.getElementById('progressBar').style.width = document.getElementById('progressBar').innerHTML = '100%';
				document.location = 'index.php?option=com_rsform&task=submissions.export.file&ExportFile=' + document.getElementById('ExportFile').value + '&ExportType=' + document.getElementById('ExportType').value + '&formId=' + document.getElementsByName('formId')[0].value;
			}
			else if (post.indexOf('error') > -1)
			{
				Joomla.renderMessages({'error': [Joomla.JText._('COM_RSFORM_AN_ERROR_HAS_OCCURRED')]});
			}
			else
			{
				var progress = Math.ceil(start * 100 / total);
				document.getElementById('progressBar').style.width = progress + '%';
				document.getElementById('progressBar').innerHTML = progress + '%';
				start = start + limit;
				exportProcess(start, limit, total);
			}
		}
	};

	xml.open('GET', 'index.php?option=com_rsform&task=submissions.export.process&exportStart=' + start + '&exportLimit=' + limit, true);
	xml.send(null);
}