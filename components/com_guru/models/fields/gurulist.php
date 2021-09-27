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
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldGurulist extends JFormField{	
	protected $type = 'gurulist';
	
	protected function getInput(){
		$params = $this->form->getValue('params');
		$display = "0";
		$db = JFactory::getDBO();
		
		if(isset($params->display)){
			$display = $params->display;
		}
		
		$return  = '<select id="jform_params_display" name="jform[params][display]">';				
		if($display == "tree"){
			$return .= '<option value="tree" selected="selected">Tree</option>';
			$return .= '<option value="listing">Listing</option>';	
		}
		else{
			$return .= '<option value="tree">Tree</option>';
			$return .= '<option value="listing" selected="selected">Listing</option>';	
		}					
		$return .= '</select>';
		
		return $return;
	}
}
?>
