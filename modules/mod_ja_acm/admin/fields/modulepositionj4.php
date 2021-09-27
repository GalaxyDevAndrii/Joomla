<?php
/**
 * ------------------------------------------------------------------------
 * JA ACM Module
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// Ensure this file is being included by a parent file
// namespace Joomla\CMS\Form\Field;

defined('JPATH_PLATFORM') or die;

// use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
// use Joomla\CMS\Form\Form;
// use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\ModulepositionField;

// JLoader::register('ModulesHelper', JPATH_ADMINISTRATOR . '/components/com_modules/helpers/modules.php');
JLoader::register('JHtmlModules', JPATH_ADMINISTRATOR . '/components/com_modules/helpers/html/modules.php');

defined('_JEXEC') or die( 'Restricted access' );

/**
 * List of checkbox base on other fields
 *
 * @since      Class available since Release 1.2.0
 */
class JFormFieldModulePositionj4 extends ModulepositionField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'modulepositionj4';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	function getInput()
	{
		// modal type. WIP.
// 		$linkEdit = '../index.php?option=com_ajax&module=ja_acm&method=positionj4&format=raw&function=jSelectPosition_spotlight_data__position&client_id=0';
// 		$html = '<!-- Modal -->
// 		<input type="text" value="'.$this->value.'" id="" />
// 
// 		<!-- Button trigger modal -->
// 		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
// 		  Launch demo modal
// 		</button>
// 
// 		<!-- Modal -->
// 		<div class="joomla-modal modal fade" 
// 		data-url="'.$linkEdit.'" 
// 		data-iframe="<iframe class=&quot;iframe&quot; src=&quot;'.$linkEdit.'&quot;></iframe>"
// 		id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
// 		  <div class="modal-dialog modal-lg jviewport-width80" role="document">
// 			<div class="modal-content">
// 			  <div class="modal-header">
// 				<h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
// 				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
// 				  <span aria-hidden="true">&times;</span>
// 				</button>
// 			  </div>
// 			  <div class="modal-body jviewport-height70">
// 			  	
// 			  </div>
// 			  <div class="modal-footer">
// 				<button type="button" class="btn btn-secondary" data-dismiss="modal">'.JText::_('JCANCEL').'</button>
// 				
// 			  </div>
// 			</div>
// 		  </div>
// 		</div>';
		
		$clientId = Factory::getApplication()->input->get('client_id', 0, 'int');
		$options = JHtmlModules::positions($clientId);
		$html = \JHtml::_(
			'select.groupedlist', $options, $this->name,
			array('id' => $this->id, 'group.id' => 'id', 'list.attr' => '', 'list.select' => $this->value)
		);
		return $html;
	}
}