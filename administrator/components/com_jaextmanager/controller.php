<?php
/**
 * ------------------------------------------------------------------------
 * JA Extension Manager Component for J3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JaextmanagerController extends JAEMController
{
	public function display($cachable = false, $urlparams = false)
	{
		$view = JRequest::getVar("view");
		if (empty($view)) {
			JRequest::setVar("view", "default");
		}
		parent::display();
	}


	public function getLink()
	{
		return "index.php?option=com_jaextmanager";
	}
}
