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
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgSystemiJoomlaGuruDiscussBox extends JPlugin{

	public function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		$this->mainframe = JFactory::getApplication();
	}
}	