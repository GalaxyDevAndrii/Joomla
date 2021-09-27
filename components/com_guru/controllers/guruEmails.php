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

class guruControllerguruEmails extends guruController {
	var $_model = null;
	
	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listEmails");
		$this->_model = $this->getModel("guruEmails");
		$this->registerTask ("unpublish", "publish");	
	}

	function listEmails() {
		$view = $this->getView("guruEmails", "html");
		$view->setModel($this->_model, true);
		$view->display();


	}


	function edit () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruEmails", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
	
		$view->editForm();

	}
	

	function save () {
		if ($this->_model->store() ) {

			$msg = JText::_('AD_ADSAVED');
		} else {
			$msg = JText::_('AD_ADSAVEFAIL');
		}
		$link = "index.php?option=com_guru&view=guruEmails";
		$this->setRedirect($link, $msg);

	}


	function cancel () {
	 	$msg = JText::_('AD_SAVECANCEL');
		$link = "index.php?option=com_guru&view=guruEmails";
		$this->setRedirect($link, $msg);


	}
		function publish () { 
			$res = $this->_model->publish();

			if (!$res) {
				$msg = JText::_('GURU_EPUBERR');
			} elseif ($res == 1) {
				$msg = JText::_('GURU_PUB');
			} elseif ($res == -1) {
				$msg = JText::_('GURU_UNPUB');
			}
			
			$link = "index.php?option=com_guru&view=guruEmails";
			$this->setRedirect($link, $msg);

		}
		
		function unpublish () {
			$res = $this->_model->unpublish();
	
			if (!$res) {
				$msg = JText::_('GURU_EUNPERR');
			} else { //if ($res == -1) {
				$msg = JText::_('GURU_UNPUB');
			}
			
			$link = "index.php?option=com_guru&view=guruEmails";
			$this->setRedirect($link, $msg);
		}

	

};

?>