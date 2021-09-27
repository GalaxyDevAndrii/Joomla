<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelRsform extends JModelLegacy
{
	protected $config;

	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->config = RSFormProConfig::getInstance();
	}
	
	public function getCode() {
		return $this->config->get('global.register.code');
	}
	
	public function getButtons() {
		JFactory::getLanguage()->load('com_rsfirewall.sys', JPATH_ADMINISTRATOR);
		
		/* $button = array(
				'access', 'id', 'link', 'target', 'onclick', 'title', 'icon', 'alt', 'text'
			); */

		$user = JFactory::getUser();

		$buttons = array(
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=forms'),
				'icon'      => 'clipboard',
				'text' 		=> JText::_('RSFP_MANAGE_FORMS'),
				'access' 	=> $user->authorise('forms.manage', 'com_rsform'),
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=submissions'),
				'icon'      => 'database2',
				'text' 		=> JText::_('RSFP_MANAGE_SUBMISSIONS'),
                'access' 	=> $user->authorise('submissions.manage', 'com_rsform'),
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=directory'),
				'icon'      => 'folder-open',
				'text' 		=> JText::_('RSFP_MANAGE_DIRECTORY_SUBMISSIONS'),
                'access' 	=> $user->authorise('directory.manage', 'com_rsform'),
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=backupscreen'),
				'icon'      => 'file-zip-o',
				'text' 		=> JText::_('RSFP_BACKUP_SCREEN'),
                'access' 	=> $user->authorise('backuprestore.manage', 'com_rsform'),
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=restorescreen'),
				'icon' 	    => 'upload',
				'text' 		=> JText::_('RSFP_RESTORE_SCREEN'),
				'access' 	=> $user->authorise('backuprestore.manage', 'com_rsform'),
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=configuration'),
				'icon' 	    => 'cogs',
				'text' 		=> JText::_('RSFP_CONFIGURATION'),
                'access' 	=> $user->authorise('core.admin', 'com_rsform'),
				'target' 	=> ''
			),
			array(
				'link' 		=> 'https://www.rsjoomla.com/support/documentation/rsform-pro/plugins-and-modules.html',
				'icon' 	    => 'power-cord',
				'text' 		=> JText::_('RSFP_PLUGINS'),
				'access' 	=> true,
				'target' 	=> '_blank'
			),
			array(
				'link' 		=> 'https://www.rsjoomla.com/support/documentation/rsform-pro.html',
				'icon' 	    => 'books',
				'text' 		=> JText::_('RSFP_USER_GUIDE'),
				'access' 	=> true,
				'target' 	=> '_blank'
			),
			array(
				'link' 		=> 'https://www.rsjoomla.com/support.html',
				'icon' 	    => 'life-ring',
				'text' 		=> JText::_('RSFP_SUPPORT'),
				'access' 	=> true,
				'target' 	=> '_blank'
			),
		);
		
		return $buttons;
	}
}