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

class guruAdminControllerguruLogs extends guruAdminController{
	var $model = null;
	
	function __construct(){
		parent::__construct();
		$this->registerTask("emails", "emails");
		$this->registerTask("purchases", "purchases");
		$this->registerTask("editEmail", "editEmail");
		$this->_model = $this->getModel("guruLogs");
	}
	
	function emails(){
		$view = $this->getView("guruLogs", "html");
		$view->setLayout("emails");
		$view->setModel($this->_model, true);
		$view->emails();
	}
	
	function purchases(){
		$view = $this->getView("guruLogs", "html");
		$view->setLayout("purchases");
		$view->setModel($this->_model, true);
		$view->purchases();
	}
	
	function editEmail(){
		$view = $this->getView("guruLogs", "html");
		$view->setLayout("edit");
		$view->setModel($this->_model, true);
		$view->edit();
	}
};

?>