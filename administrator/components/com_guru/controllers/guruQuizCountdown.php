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

class guruAdminControllerguruQuizCountdown extends guruAdminController {

	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("", "listQuizSettings");
		$this->_model = $this->getModel("guruQuizCountdown");
	}
	
	function listQuizSettings() {
		$view = $this->getView("guruQuizCountdown", "html");
		//$view->setModel($this->_model, true);
		$view->display();
	}
	function save(){ 
		if($this->_model->store()){
			$msg = JText::_('GURU_QCOUNTDOWN_SETTINGS_OK');
		}
		else{
			$msg = JText::_('GURU_QCOUNTDOWN_SETTINGS_FAIL');
		}
		$link = "index.php?option=com_guru";
		$this->setRedirect($link, $msg);
	}
	function apply(){ 
		if($this->_model->store()){
			$msg = JText::_('GURU_PROMAPPLY');
		}
		else{
			$msg = JText::_('GURU_PROMAPPLYFAILED');
		}
		$link = "index.php?option=com_guru&controller=guruQuizCountdown";
		$this->setRedirect($link, $msg);
	}	
	function cancel(){
	 	$msg = JText::_('GURU_PROMCANCEL');
		$link = "index.php?option=com_guru";
		$this->setRedirect($link, $msg);
	}
};

?>