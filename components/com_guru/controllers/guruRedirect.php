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

class guruControllerguruRedirect extends guruController {
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("checkout", "checkout");
	}
	
	function checkout(){
		$link = "index.php?option=com_guru&view=guruBuy&task=checkout&from=redirect";
		$this->setRedirect(JRoute::_($link, false), "");
	}
};

?>