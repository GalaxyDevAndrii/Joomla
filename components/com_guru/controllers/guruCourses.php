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

class guruControllerguruCourses extends guruController {
	var $_model = null;
	
	function __construct () {

		parent::__construct();

		$this->registerTask ("", "view");
		$this->_model = $this->getModel("guruCourses");	
	}
	
	function view () {
		$view = $this->getView("guruCourses", "html");
		//$view->setLayout("defa");
		$view->setModel($this->_model, true);	
		$view->display();
	}
};

?>