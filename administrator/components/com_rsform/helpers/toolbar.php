<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

abstract class RSFormProToolbarHelper
{	
	public static function addToolbar($view = '')
	{
		$user = JFactory::getUser();

		// load language file (.sys because the toolbar has the same options as the components dropdown)
		JFactory::getLanguage()->load('com_rsform.sys', JPATH_ADMINISTRATOR);
		
		// Add toolbar entries
		JHtmlSidebar::addEntry(JText::_('COM_RSFORM_DASHBOARD'), 'index.php?option=com_rsform', $view == '' || $view == 'rsform');

        if ($user->authorise('forms.manage', 'com_rsform'))
        {
	        JHtmlSidebar::addEntry(JText::_('COM_RSFORM_MANAGE_FORMS'), 'index.php?option=com_rsform&view=forms', $view == 'forms');
        }
        if ($user->authorise('submissions.manage', 'com_rsform'))
        {
	        JHtmlSidebar::addEntry(JText::_('COM_RSFORM_MANAGE_SUBMISSIONS'), 'index.php?option=com_rsform&view=submissions', $view == 'submissions');
        }
        if ($user->authorise('directory.manage', 'com_rsform'))
        {
	        JHtmlSidebar::addEntry(JText::_('COM_RSFORM_MANAGE_DIRECTORY_SUBMISSIONS'), 'index.php?option=com_rsform&view=directory', $view == 'directory');
        }
        if ($user->authorise('core.admin', 'com_rsform'))
        {
	        JHtmlSidebar::addEntry(JText::_('COM_RSFORM_CONFIGURATION'), 'index.php?option=com_rsform&view=configuration', $view == 'configuration');
        }
        if ($user->authorise('backuprestore.manage', 'com_rsform'))
        {
	        JHtmlSidebar::addEntry(JText::_('COM_RSFORM_BACKUP_SCREEN'), 'index.php?option=com_rsform&view=backupscreen', $view == 'backupscreen');
	        JHtmlSidebar::addEntry(JText::_('COM_RSFORM_RESTORE_SCREEN'), 'index.php?option=com_rsform&view=restorescreen', $view == 'restorescreen');
        }
	}
	
	public static function addFilter($text, $key, $options, $noDefault = false)
	{
		JHtmlSidebar::addFilter($text, $key, $options, $noDefault);
	}
	
	public static function render()
	{
		return JHtmlSidebar::render();
	}
}