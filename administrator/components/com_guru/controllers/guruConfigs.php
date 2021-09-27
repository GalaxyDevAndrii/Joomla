<?php
/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport ('joomla.application.component.controller');

class guruAdminControllerguruConfigs extends guruAdminController {

	var $_model = null;
	
	function __construct () {
		parent::__construct();
		require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php' );
		$this->registerTask ("", "listConfigs");
		$this->registerTask("general", "listConfigs");
		$this->registerTask("payments", "listConfigs");
		$this->registerTask("content", "listConfigs");
		$this->registerTask("guru_file_uploader", "guruFileUploader");
		$this->_model = $this->getModel("guruConfig");
	}
	
	function listConfigs() {
		$view = $this->getView("guruConfigs", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function save(){
		if ($this->_model->store() ) {
			$msg = JText::_('GURU_CONFIGSAVED');
			$notice="";
		}
		else{
			$msg = JText::_('GURU_CONFIGNOTSAVED');
			$notice="Warning";
		}
		$link = "index.php?option=com_guru";
		$this->setRedirect($link, $msg, $notice);
	}
	
	function cancel(){
		$msg = JText::_('GURU_MEDIACANCEL');	
		$link = "index.php?option=com_guru";
		$this->setRedirect($link, $msg);
	}
	
	function apply(){
		$tab = JFactory::getApplication()->input->get("tab", "0", "raw");
		$active_tab = JFactory::getApplication()->input->get("active_tab", "categories_active", "raw");
		
		if($this->_model->store()){
			$msg = JText::_('GURU_CONFIG_APPLY');
			$notice="";
		}
		else{
			$msg = JText::_('GURU_CONFIG_APPLYFAILED')." ". JText::_('GURU_INVALID_VALUE_PROVIDED');
			$notice="Warning";
		}
		$link = "index.php?option=com_guru&controller=guruConfigs&tab=".intval($tab)."&active_tab=".$active_tab;
		$this->setRedirect($link, $msg, $notice);
	}
	
	function guruFileUploader(){
		include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."fileuploader.php");
  		die();
	}
};

?>