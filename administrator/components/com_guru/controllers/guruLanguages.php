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

class guruAdminControllerguruLanguages extends guruAdminController {
	var $_model = null;
	
	function __construct () {
		$app = JFactory::getApplication();
		$app->redirect(JURI::root()."administrator/index.php?option=com_guru");
		return true;

		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listLanguages"); 
		$this->_model = $this->getModel("guruLanguages");
	}

	function edit () {
		$view = $this->getView("guruLanguages", "html");
		$view->setLayout("editForm");
		$view->editForm();
	}

	function save () {
		
	}
	
	function apply () {
		
	}	

	function upload () {
		$msg = $this->_model->upload();
		$link = "index.php?option=com_guru&controller=guruLanguages";
		$this->setRedirect($link, $msg);
	}
	
	function cancel () {
	 	$msg = JText::_('GURU_LANG_CANCEL');
		$link = "index.php?option=com_guru&controller=guruLanguages";
		$this->setRedirect($link, $msg);
	}
};

?>