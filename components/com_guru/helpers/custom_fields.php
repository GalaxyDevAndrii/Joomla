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

class guruCustomFields{

	function renderCustomField($field){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$html = "";
		$required_html = "";

		$field_value = "";

		if(isset($user) && isset($user->id) && intval($user->id) > 0){
			$sql = "select `value` from #__guru_fields_results where `user_id`=".intval($user->id)." and `field_id`=".intval($field["id"]);
			$db->setQuery($sql);
			$db->execute();
			$field_value = $db->loadColumn();
			$field_value = @$field_value["0"];
		}

		$app = JFactory::getApplication();
    	
    	if($app->isAdmin()){
    		$cid = JFactory::getApplication()->input->get("cid", array("0"), "raw");

    		if(intval($cid["0"]) == 0){
    			$cid["0"] = JFactory::getApplication()->input->get("id", "0", "raw");
    		}

    		$user = JFactory::getUser(intval($cid["0"]));

    		$field_value = "";

			if(isset($user) && isset($user->id) && intval($user->id) > 0){
				$sql = "select `value` from #__guru_fields_results where `user_id`=".intval($user->id)." and `field_id`=".intval($field["id"]);
				$db->setQuery($sql);
				$db->execute();
				$field_value = $db->loadColumn();
				$field_value = @$field_value["0"];
			}
    	}

		if($field["required"] == 1){
			$required_html = '
				<span class="uk-text-danger wk-text-danger">*</span>
				<input type="hidden" data-name="'.$field["name"].'" data-type="'.$field["type"].'" data-id="'.$field["id"].'" class="custom_fields_required" />
			';
		} 

		if($field["type"] == "text"){
			$value = "";

			if(isset($field_value) && trim($field_value) != ""){
				$value = trim($field_value);
			}

			$html .= '
				<div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label">
                        '.$field["name"].$required_html.'
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <input type="text" value="'.trim($value).'" name="fields['.$field["id"].']" id="'.$field["field_code"].'" />
                    </div>
                </div>';
		}
		elseif($field["type"] == "textarea"){
			$value = "";

			if(isset($field_value) && trim($field_value) != ""){
				$value = trim($field_value);
			}

			$html .= '
				<div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label">
                        '.$field["name"].$required_html.'
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <textarea name="fields['.$field["id"].']" id="'.$field["field_code"].'">'.trim($value).'</textarea>
                    </div>
                </div>';
		}
		elseif($field["type"] == "select"){
			$value = "";

			if(isset($field_value) && trim($field_value) != ""){
				$value = $field_value;
			}

			$html .= '
				<div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label">
                        '.$field["name"].$required_html.'
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <select name="fields['.$field["id"].']" id="'.$field["field_code"].'">';

			            if(isset($field["options"]) && trim($field["options"]) != ""){
			            	$options = explode("\n", $field["options"]);

			            	if(isset($options) && is_array($options) && count($options) > 0){
			            		foreach($options as $key=>$option){
			            			$selected = "";

			            			if(trim($option) == trim($value)){
			            				$selected = 'selected="selected"';
			            			}

			            			$html .= '<option value="'.trim($option).'" '.$selected.'>'.trim($option).'</option>';
			            		}
			            	}
			            }

            $html .= '
                        </select>
                    </div>
                </div>';
		}
		elseif($field["type"] == "radio"){
			$value = "";

			if(isset($field_value) && trim($field_value) != ""){
				$field_value = json_decode($field_value, true);

				if(is_array($field_value) && isset($field_value["0"])){
					$value = trim($field_value["0"]);
				}
			}

			$html .= '
				<div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label">
                        '.$field["name"].$required_html.'
                    </label>
                    <div class="uk-form-controls wk-form-controls">';

			            if(isset($field["options"]) && trim($field["options"]) != ""){
			            	$options = explode("\n", $field["options"]);

			            	if(isset($options) && is_array($options) && count($options) > 0){
			            		foreach($options as $key=>$option){
			            			$checked = "";

			            			if(trim($option) == trim($value)){
			            				$checked = 'checked="checked"';
			            			}

			            			$html .= '<input type="radio" name="fields['.$field["id"].'][]" id="'.$field["field_code"].'" value="'.trim($option).'" '.$checked.'><span class="lbl"></span><div class="custom-label">'.trim($option)."</div>";
			            		}
			            	}
			            }

            $html .= '
                    </div>
                </div>';
		}
		elseif($field["type"] == "checkbox"){
			$value = "";

			if(isset($field_value) && trim($field_value) != ""){
				$value = json_decode($field_value, true);
				$value = array_map('trim', $value);
			}

			$html .= '
				<div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label">
                        '.$field["name"].$required_html.'
                    </label>
                    <div class="uk-form-controls wk-form-controls">';

			            if(isset($field["options"]) && trim($field["options"]) != ""){
			            	$options = explode("\n", $field["options"]);

			            	if(isset($options) && is_array($options) && count($options) > 0){
			            		foreach($options as $key=>$option){
			            			$checked = "";

			            			if(is_array($value) && in_array(trim($option), $value)){
			            				$checked = 'checked="checked"';
			            			}

			            			$html .= '<input type="checkbox" name="fields['.$field["id"].'][]" id="'.$field["field_code"].'" value="'.trim($option).'" '.$checked.'><span class="lbl"></span><div class="custom-label">'.trim($option)."</div>";
			            		}
			            	}
			            }

            $html .= '
                    </div>
                </div>';
		}
		elseif($field["type"] == "url"){
			$prot = "";
			$url = "";

			if(isset($field_value) && trim($field_value) != ""){
				$value = json_decode($field_value, true);
				$value = array_map('trim', $value);

				if(isset($value["prot"])){
					$prot = trim($value["prot"]);
				}

				if(isset($value["url"])){
					$url = trim($value["url"]);
				}
			}

			$html .= '
				<div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label">
                        '.$field["name"].$required_html.'
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                    	<select class="prot-select" name="fields['.$field["id"].'][prot]" id="'.$field["field_code"].'">
                    		<option value="https://" '.($prot == "https://" ? 'selected="selected"' : '').' >https://</option>
                    		<option value="http://" '.($prot == "http://" ? 'selected="selected"' : '').' >http://</option>
                    	</select>
                    	<input type="text" value="'.$db->escape(trim($url)).'" name="fields['.$field["id"].'][url]" id="'.$field["field_code"].'" />
                    </div>
                </div>';
		}

		return $html;
	}
}

?>