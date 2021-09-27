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

class guruControllerguruConfigs extends guruController {

	var $_model = null;
	
	function __construct () {
		parent::__construct();
		require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php' );
		$this->registerTask ("", "listConfigs");
		$this->registerTask("general", "listConfigs");
		$this->registerTask("payments", "listConfigs");
		$this->registerTask("content", "listConfigs");
		$this->_model = $this->getModel("guruConfig");

	}
	function listConfigs() {
		$view = $this->getView("guruConfigs", "html");
		$view->setModel($this->_model, true);
		
		$view->display();
	}


	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('GURU_CONFIGSAVED');
		} else {
			$msg = JText::_('');
		}
		$link = "index.php?option=com_guru";
		$this->setRedirect($link, $msg);

	}

};

?>