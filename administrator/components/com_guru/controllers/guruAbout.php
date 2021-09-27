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

class guruAdminControllerguruAbout extends guruAdminController{
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listCategories");
		$this->registerTask ("unpublish", "publish");	
		$this->_model = $this->getModel("guruabout");
	}

	function listCategories() {
       	JFactory::getApplication()->input->set("view", "guruabout");
		$view = $this->getView("guruabout", "html");
		$view->setModel($this->_model, true);
		parent::display();
	}
	function cancel () {
	 	$link = "index.php?option=com_guru";
		$this->setRedirect($link, $msg);
	}
	
	 function vimeo() {
   		JFactory::getApplication()->input->set('view', 'guruAbout');
		JFactory::getApplication()->input->set('layout', 'vimeo');
        $view = $this->getView("guruAbout", "html");
		$view->setLayout("vimeo");
        $view->vimeo();
        die();
    }
	function youtube() {
   		JFactory::getApplication()->input->set('view', 'guruAbout');
		JFactory::getApplication()->input->set('layout', 'youtube');
        $view = $this->getView("guruAbout", "html");
		$view->setLayout("youtube");
        $view->youtube();
        die();
    }
	
};

?>