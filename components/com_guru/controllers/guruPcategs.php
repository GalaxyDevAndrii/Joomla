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

class guruControllerguruPcategs extends guruController {
	var $_model = null;
	
	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listZones");
		$this->_model = $this->getModel("guruPcateg");
		$this->registerTask ("unpublish", "publish");
	}

	function listZones() {
		$view = $this->getView("guruPcategs", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function view () {
		$view = $this->getView("guruPcategs", "html");
		$view->setLayout("view");
		$view->setModel($this->_model, true);	
		$view->show();
	}

	function edit () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruPcategs", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);	
	}


	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('ZONESAVED');
		} else {
			$msg = JText::_('ZONEFAILED');
		}
		$link = "index.php?option=com_guru&view=guruPcategs";
		$this->setRedirect($link, $msg);

	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('ZONEREMERR');
		} else {
		 	$msg = JText::_('ZONEREMSUCC');
		}
		
		$link = "index.php?option=com_guru&view=guruPcategs";
		$this->setRedirect($link, $msg);
		
	}

	function cancel () {
	 	$msg = JText::_('ZONECANCEL');
		$link = "index.php?option=com_guru&view=guruPcategs";
		$this->setRedirect($link, $msg);


	}

	function publish () { 
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('ZONEBLOCKERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('ZONEUNPUBSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('ZONEPPUBSUCC');
		} else {
                 	$msg = JText::_('ZONEUNSPEC');
		}
		
		$link = "index.php?option=com_adagency&view=adagencyZones";
		$this->setRedirect($link, $msg);


	}

};

?>