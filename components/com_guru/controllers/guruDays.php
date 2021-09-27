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

class guruControllerguruDays extends guruController {
	var $_model = null;
	
	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listDays");
		$this->_model = $this->getModel("guruDays");
		$this->registerTask ("unpublish", "publish");	
	}
	
	function view () {
		$view = $this->getView("guruDays", "html");
		$view->setLayout("view");
		$view->setModel($this->_model, true);	
		$view->show();
	}		

	function listDays() {
		$view = $this->getView("guruDays", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function edit () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruDays", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
	
		$model = $this->getModel("guruDays");
		$view->setModel($model);
		$view->editForm();

	}



	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('AD_REMOVE_ERR');
		} else {
		 	$msg = JText::_('AD_REMOVED');
		}
		
		$link = "index.php?option=com_guru&view=guruDays";
		$this->setRedirect($link, $msg);
		
	}

	
	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('AD_PUB_ERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('AD_UNPUBLISHED');
		} elseif ($res == 1) {
			$msg = JText::_('AD_PUBLISHED');
		} else {
                 	$msg = JText::_('AD_PUB_ERR');
		}
		
		$link = "index.php?option=com_guru&view=guruDays";
		$this->setRedirect($link, $msg);


	}
	function approve () {
		$res = $this->_model->approve();
		if (!$res) {
			$msg = JText::_('AD_UNPUB_ERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('AD_UNPUBLISHED');
		} elseif ($res == 1) {
			$msg = JText::_('AD_PUBLISHED');
		} else {
	       	$msg = JText::_('AD_UNPUB_ERR');
		}
			
	$link = "index.php?option=com_guru&view=guruDays";
		$this->setRedirect($link, $msg);
	}
		
	function unapprove () {
			$res = $this->_model->unapprove();
			if (!$res) {
				$msg = JText::_('AD_UNPUB_ERR');
			} elseif ($res == -1) {
			 	$msg = JText::_('AD_UNPUBLISHED');
			} elseif ($res == 1) {
				$msg = JText::_('AD_PUBLISHED');
			} else {
	 			$msg = JText::_('AD_UNPUB_ERR');
			}
				
			$link = "index.php?option=com_guru&view=guruDays";
		$this->setRedirect($link, $msg);
		
		
	}
	
	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('AD_CMP_SAVED');
		} else {
			$msg = JText::_('AD_CMP_NOT_SAVED');
		}
		$link = "index.php?option=com_guru&view=guruDays";
		$this->setRedirect($link, $msg);

	}
	
	function cancel () {
	 	$msg = JText::_('AD_OP_CANCELED');
		$link = "index.php?option=com_guru&view=guruDays";
		$this->setRedirect($link, $msg);
	}

	function preview () {
	
		JFactory::getApplication()->input->set ("hidemainmenu", 1); 
		$view = $this->getView("guruDays", "html");
		$view->setLayout("preview");
		$view->setModel($this->_model, true); 
		$view->preview();
		
		//$view = $this->getView("guruDays", "html");
		//$view->setLayout("view");
		//$view->setModel($this->_model, true);	
				
	}		

};

?>