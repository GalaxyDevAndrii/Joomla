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

class JFormFieldGurucourse extends JFormField{	
	protected $type = 'gurucourse';
	
	protected function getInput(){
		$params = $this->form->getValue('params');
		$cid = "0";
		$db = JFactory::getDBO();
		
		if(isset($params->cid)){
			$cid = $params->cid;
		}	
		
		$return  = '<select id="jform_params_cid" name="jform[params][cid]">';
		
		//$sql = "SELECT CONCAT_WS(':', id, alias) as cid, name FROM #__guru_program";	
		$sql = "SELECT id as cid, name FROM #__guru_program";	
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		if(is_array($result) && count($result) > 0){		
			foreach($result as $key => $values){						
				$course_id = $values["cid"];
				$course_name = $values["name"];			
				
				if($cid == $course_id){
					$selected = ' selected="selected" ';
				}
				else{
					$selected = '';
				}				
				$return .= '<option value="'.$course_id.'" '.$selected.'>'.$course_name.'</option>';			
			}			
		}
				
		$return .= '</select>';
		
		return $return;
	}
}
?>
