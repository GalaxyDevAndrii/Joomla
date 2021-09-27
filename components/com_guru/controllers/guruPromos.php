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

class guruControllerguruPromos extends guruController {
	var $_model = null;
	
	function __construct () {

		parent::__construct();

		$this->registerTask ("", "listPromos");
		$this->_model = $this->getModel("guruPromos");
	
	}

	function listPromos() {
		$view = $this->getView("guruPromos", "html");
		$view->setModel($this->_model, true);
		//$model =& $this->getModel("adagencyConfig");
		//$view->setModel($model);
		$view->display();


	}


	function edit () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruPromos", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		//$model =& $this->getModel("adagencyConfig");
		//$view->setModel($model);
		$view->editForm();

	}


	function save () { 
		if ($this->_model->store() ) {

			$msg = JText::_('PACKAGESAVED');
		} else {
			$msg = JText::_('PACKAGEFAILED');
		}
		$link = "index.php?option=com_guru&view=guruPromos";
		$this->setRedirect($link, $msg);

	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('PACKAGEREMERR');
		} else {
		 	$msg = JText::_('PACKAGEREMSUCC');
		}
		
		$link = "index.php?option=com_guru&view=guruPromos";
		$this->setRedirect($link, $msg);
		
	}

	function cancel () {
	 	$msg = JText::_('PACKAGECANCEL');
		$link = "index.php?option=com_guru&view=guruPromos";
		$this->setRedirect($link, $msg);


	}

	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('PACKAGEBLOCKERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('PACKAGEUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('PACKAGEPUB');
		} else {
                 	$msg = JText::_('PACKAGEUNSPEC');
		}
		
		$link = "index.php?option=com_guru&view=guruPromos";
		$this->setRedirect($link, $msg);


	}
	
	function unpublish () {
		$res = $this->_model->unpublish();
		if (!$res) {
			$msg = JText::_('PACKAGEBLOCKERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('PACKAGEUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('PACKAGEPUB');
		} else {
                 	$msg = JText::_('PACKAGEUNSPEC');
		}
		
		$link = "index.php?option=com_guru&view=guruPromos";
		$this->setRedirect($link, $msg);


	}

};

?>