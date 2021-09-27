Joomla.submitbutton = function(task)
{
	var messages = {"error": []};

	if (task === 'submissions.importtask')
	{
		var headers = document.getElementsByName('header[]');
		var selectedAValue = false;

		main_loop:
			for (var i = 0; i < headers.length; i++)
			{
				if (headers[i].value.length > 0)
				{
					selectedAValue = true;

					for (var j = 0; j < headers.length; j++)
					{
						if (i !== j && headers[i].value === headers[j].value)
						{
							messages.error.push(Joomla.JText._('COM_RSFORM_YOU_HAVE_SELECTED_MULTIPLE_FIELDS').replace('%s', headers[i].value));
							break main_loop;
						}
					}
				}
			}

		if (!selectedAValue)
		{
			messages.error.push(Joomla.JText._('COM_RSFORM_PLEASE_MAP_AT_LEAST_A_FIELD_FROM_THE_DROPDOWN'));
		}
	}

	if (messages.error.length > 0)
	{
		Joomla.renderMessages(messages);
		return false;
	}

	Joomla.submitform(task);
}

function importProcess(start, limit, total)
{
	var xml = buildXmlHttp();
	var formId = document.getElementById('formId').value;
	xml.onreadystatechange = function ()
	{
		if (xml.readyState == 4)
		{
			var post = xml.responseText;
			if (post.indexOf('END') > -1)
			{
				document.getElementById('progressBar').style.width = document.getElementById('progressBar').innerHTML = '100%';

				Joomla.renderMessages({'message': [Joomla.JText._('COM_RSFORM_IMPORT_HAS_FINISHED')]});
			}
			else if (post.indexOf('ERROR') > -1)
			{
				Joomla.renderMessages({'error': [Joomla.JText._('COM_RSFORM_AN_ERROR_HAS_OCCURRED_DURING_IMPORT')]});
			}
			else
			{
				var start = post;

				document.getElementById('progressBar').style.width = Math.ceil(start * 100 / total) + '%';
				document.getElementById('progressBar').innerHTML = Math.ceil(start * 100 / total) + '%';

				importProcess(start, limit, total);
			}
		}
	};

	xml.open('GET', 'index.php?option=com_rsform&task=submissions.importprocess&formId=' + formId + '&importStart=' + start + '&importLimit=' + limit, true);
	xml.send(null);
}