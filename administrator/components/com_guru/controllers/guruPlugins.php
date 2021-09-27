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

class guruAdminControllerguruPlugins extends guruAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("", "listPlugins");
		$this->_model = $this->getModel("guruPlugin");
		$this->registerTask ("publish", "publish");	
		$this->registerTask ("unpublish", "publish");	
	}

	function listPlugins() {
		$view = $this->getView("guruPlugins", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function publish(){
		$res = $this->_model->publish();
		if(!$res){
			$msg = JText::_('GURU_PLUGPUBERR');
		} 
		elseif($res == -1){
		 	$msg = JText::_('GURU_PLUGUNPUB');
		} 
		elseif($res == 1){
			$msg = JText::_('GURU_PLUGPUB');
		} 
		else{
            $msg = JText::_('GURU_PLUGUNSPEC');
		}		
		$link = "index.php?option=com_guru&controller=guruPlugins";
		$this->setRedirect($link, $msg);
	}

};

?>