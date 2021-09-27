function enableImportUpload()
{
    var input = document.getElementsByName('import[file]')[0];
    var message = document.getElementById('importError');
    if (input.value.length === 0)
    {
        message.style.display = 'block';
        message.innerText = Joomla.JText._('COM_RSFORM_PLEASE_UPLOAD_A_FILE');
        return false;
    }

    var ext = input.value.substring(input.value.lastIndexOf('.') + 1).toLowerCase();

    if (ext !== 'csv')
    {
        message.style.display = 'block';
        message.innerText = Joomla.JText._('COM_RSFORM_PLEASE_UPLOAD_ONLY_CSV_FILES');
        return false;
    }

    message.style.display = 'none';
    document.getElementById('adminForm').setAttribute('enctype', 'multipart/form-data');
    document.getElementById('importFileButton').removeAttribute('disabled');
}

function toggleCheckColumns()
{
    var i;
    var tocheck = document.getElementById('checkColumns').checked;
    var staticcolumns = document.getElementsByName('staticcolumns[]');
    for (i = 0; i < staticcolumns.length; i++)
    {
        staticcolumns[i].checked = tocheck;
    }

    var columns = document.getElementsByName('columns[]');
    for (i = 0; i < columns.length; i++)
    {
        columns[i].checked = tocheck;
    }
}