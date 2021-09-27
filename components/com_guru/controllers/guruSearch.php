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

class guruControllerguruSearch extends guruController {
	
	function __construct(){
		parent::__construct();
		$this->registerTask("","search");
		$this->registerTask("search", "search");
		$this->_model = $this->getModel("guruSearch");
	}
	
	function search(){
		JFactory::getApplication()->input->set('view', 'guruSearch');
		parent::display();
	}
};

?>