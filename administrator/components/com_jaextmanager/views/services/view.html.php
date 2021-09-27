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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JaextmanagerViewServices extends JAEMView
{


	function display($tpl = null)
	{
		// Display menu
		if (!JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR."/views/default/tmpl/menu_header.php";
			if (@file_exists($file))
				require_once ($file);
		}
		
		switch ($this->getLayout()) {
			case 'form':
				$this->edit();
				break;
			case 'config':
				$this->config();
				break;
			case 'response':
				$this->response();
				break;
			default:
				$this->displayList();
				break;
		}
		parent::display($tpl);
		
		// Display footer
		if (!JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR."/views/default/tmpl/menu_footer.php";
			if (@file_exists($file))
				require_once ($file);
		}
	
	}


	function displayList()
	{
		$model =  JAEMModel::getInstance('services', 'JaextmanagerModel');
		
		$lists = $model->_getVars_admin();
		
		$lists['create_date'] = JRequest::getVar('createdate', NULL);
		
		$total = $model->getTotal('');
		//limit
		

		if ($lists['limit'] > $total) {
			$lists['limitstart'] = 0;
		}
		if ($lists['limit'] == 0) {
			$limit = $total;
		} else {
			$limit = $lists['limit'];
		}
		
		jimport('joomla.html.pagination');
		
		$pageNav = new JPagination($total, $lists['limitstart'], $lists['limit']);
		
		//$services = $model->getList ('', 't.ws_name ASC', $lists ['limitstart'], $lists ['limit'] );
		$services = $model->getList('', 't.id asc', $lists['limitstart'], $limit);
		
		$this->assign('services', $services);
		
		$this->assign('lists', $lists);
		
		$this->assign('pageNav', $pageNav);
	}


	function edit($item = null)
	{
		$model = $this->getModel('services');
		
		if (!$item) {
			
			$item = $this->get('row');
			
			$postback = JRequest::getVar('postback');
			
			if (!$postback) {
				
				$post = JRequest::get('request', JREQUEST_ALLOWHTML);
				
				$item->bind($post);
			}
		}
		
		$number = JRequest::getVar('number', 0);
		
		$listMode = JHtml::_('select.radiolist', $model->getListServiceMode(), 'ws_mode', 'class="inputbox"', 'value', 'text', $item->ws_mode);
		
		$isDefault = ($item->ws_default == 1) ? 1 : 0;
		$ws_default = JHtml::_('select.booleanlist', 'ws_default', 'class="inputbox"', $isDefault);
		
		$this->assignRef('listMode', $listMode);
		$this->assignRef('ws_default', $ws_default);
		$this->assignRef('item', $item);
		$this->assignRef('number', $number);
	}


	function config($item = null)
	{
		$model = $this->getModel('services');
		
		if (!$item) {
			
			$item = $this->get('row');
			
			$postback = JRequest::getVar('postback');
			
			if (!$postback) {
				
				$post = JRequest::get('request', JREQUEST_ALLOWHTML);
				
				$item->bind($post);
			}
		}
		
		$number = JRequest::getVar('number', 0);
		
		$listMode = JHtml::_('select.radiolist', $model->getListServiceMode(), 'ws_mode', 'class="inputbox"', 'value', 'text', $item->ws_mode);
		
		$isDefault = ($item->ws_default == 1) ? 1 : 0;
		$ws_default = JHtml::_('select.booleanlist', 'ws_default', 'class="inputbox"', $isDefault);
		
		$this->assignRef('listMode', $listMode);
		$this->assignRef('ws_default', $ws_default);
		$this->assignRef('item', $item);
		$this->assignRef('number', $number);
	}


	function response()
	{
		$model = JAEMModel::getInstance('services', 'JaextmanagerModel');
		$type = JRequest::getVar('type', 'admin_response');
		if (!isset($item)) {
			$item = $model->getAdmin_response();
		}
		
		$cid[0] = $item->item_id;
		$row = $model->getItem($cid);
		$item->item_title = $row ? $row->title : '';
		$response = JFactory::getUser($item->user_id);
		$item->responsename = $response ? $response->username : '';
		$this->assign('item', $item);
		$this->assign('type', $type);
	}
}