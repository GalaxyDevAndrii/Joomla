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

class JFormFieldCategories extends JFormField{
	protected $type = 'categories';
	public $return_array = array();

	function getAllRows($parent, $level){
		$db = JFactory::getDbo();

		$sql = "SELECT id, description, name, child_id as cid, parent_id as pid, ordering, published FROM #__guru_category, #__guru_categoryrel WHERE #__guru_category.id = #__guru_categoryrel.child_id and #__guru_categoryrel.parent_id=".intval($parent)." ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		
		if(isset($result) && is_array($result) && count($result) > 0){
			$level ++;
			foreach($result as $key=>$value){
				$value["level"] = $level;
				$this->return_array[] = $value;
				$this->getAllRows($value["id"], $level);
			}
		}

		return $this->return_array;
	}
	
	protected function getInput(){
		$params = $this->form->getValue('params');
		$category = "0";
		$db = JFactory::getDBO();
		
		if(isset($params->category)){
			$category = $params->category;
		}

		$categories = $this->getAllRows(0, 0);

		$return  = '<select id="jform_params_category" name="jform[params][category]">';
		$return .= 		'<option value="0">-- All Categories --</option>';
		
		if(isset($categories) && is_array($categories) && count($categories) > 0){
			foreach($categories as $key=>$categ){
				$categ_name = $categ["name"];

				for($i=1; $i<=$categ["level"]; $i++){
					$categ_name = " - ".$categ_name;
				}

				$selected = "";

				if(intval($categ["id"]) == intval($category)){
					$selected = 'selected="selected"';
				}

				$return .= '<option value="'.intval($categ["id"]).'" '.$selected.'>'.$categ_name.'</option>';
			}
		}
				
		$return .= '</select>';
		
		return $return;
	}
}
?>
