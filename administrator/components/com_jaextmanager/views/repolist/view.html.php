<?php
/**
 * @version		$Id: view.html.php 17858 2010-06-23 17:54:28Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * HTML View class for the Media component
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since 1.0
 */
class JaextmanagerViewRepolist extends JAEMView
{


	function display($tpl = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		
		// Do not allow cache
		JFactory::getApplication()->allowCache(false);
		
		//$style = $app->getUserStateFromRequest('media.list.layout', 'layout', 'details', 'word');
		$style = "details";
		
		JHtml::_('behavior.framework', true);
		
		$document = JFactory::getDocument();
		$document->addStyleSheet('components/com_jaextmanager/assets/repo_manager/repolist-' . $style . '.css');
		
		$images 	= $this->get('images');
		$documents 	= $this->get('documents');
		$folders	= $this->get('folders');
		$state		= $this->get('state');
		$this->assign('baseURL', JURI::root());
		$this->assignRef('images', $images);
		$this->assignRef('documents', $documents);
		$this->assignRef('folders', $folders);
		$this->assignRef('state', $state);
		
		parent::display($tpl);
	}


	function setFolder($index = 0)
	{
		if (isset($this->folders[$index])) {
			$this->_tmp_folder = &$this->folders[$index];
		} else {
			$this->_tmp_folder = new JObject();
		}
	}


	function setImage($index = 0)
	{
		if (isset($this->images[$index])) {
			$this->_tmp_img = &$this->images[$index];
		} else {
			$this->_tmp_img = new JObject();
		}
	}


	function setDoc($index = 0)
	{
		if (isset($this->documents[$index])) {
			$this->_tmp_doc = &$this->documents[$index];
		} else {
			$this->_tmp_doc = new JObject();
		}
	}
}
