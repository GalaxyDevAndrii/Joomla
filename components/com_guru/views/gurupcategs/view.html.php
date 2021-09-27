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
jimport ("joomla.application.component.view");
require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."generate_display.php");

class guruViewguruPCategs extends JViewLegacy {

	function display($tpl = null){
		$db = JFactory::getDBO();
		
		$categs = $this->get('listPcategs');	
		$this->categs = $categs;

		$getConfigSettings = $this->get('ConfigSettings');
		$this->getConfigSettings = $getConfigSettings;
		
		parent::display($tpl);
	}
	
	function show ($tpl =  null ) {
		$db = JFactory::getDBO();

		$categ = $this->get('categ');
		$this->categ = $categ;
		
		$subcateg = $this->get('children');
		$this->subcateg = $subcateg;

		$get_sub_categs = $this->get('listPcategs');
		$this->get_sub_categs = $get_sub_categs;
		
		$programs = $this->get('programs'); 
		$this->programs = $programs;
		
		$this->pagination = $this->get('Pagination');
		
		$getConfigSettings = $this->get('ConfigSettings');
		$this->getConfigSettings = $getConfigSettings;
		
		$display = $this->get('Display');
		$this->display = $display;
		
		parent::display($tpl);
	}
	
	function categlist($cid='0', $level='0', $selected_categories=Array()){
		$tree = '';
		$level++;
		
		$db = JFactory::getDBO();
		$lang = JFactory::getApplication()->input->get("lang", "", "raw");

		if(strpos($lang, "-") !== false){
			$lang = explode("-", $lang);
			$lang = $lang["0"];
		}

		$q = "SELECT id, child_id, ordering, published, name,image, description FROM #__guru_category, #__guru_categoryrel ";
		$q .= "WHERE #__guru_categoryrel.parent_id='$cid' ";
		$q .= "AND #__guru_category.id=#__guru_categoryrel.child_id AND (#__guru_category.language='' OR #__guru_category.language='*' OR #__guru_category.language='".$lang."')";
		$q .= "ORDER BY #__guru_category.ordering ASC";
		$db->setQuery($q);
		$allresults = $db->loadObjectList();
	
		$ask = "SELECT * FROM #__guru_config LIMIT 1 ";
		$db->setQuery( $ask );
		$result = $db->loadObject();
		$config_category = json_decode($result->ctgpage);
		$config_category_style = json_decode($result->st_ctgpage);		
		$cols = "2";
		global $i;
		$i = 0;
		$config = $this->get('ConfigSettings');
		//$categ_config = json_decode($config->ctgspage);

		$categs_config = json_decode($config->ctgspage);
		$style_categs = json_decode($config->st_ctgspage);
		$layout = $categs_config->ctgslayout;
		$cols = $categs_config->ctgscols;
		$return_result = "";

		//----------------------------------------
		$itemid_req = JFactory::getApplication()->input->get("Itemid", "", "raw");

		if(intval($itemid_req) != 0){
		    $db = JFactory::getDbo();
		    $sql = "select `params` from #__menu where `id`=".intval($itemid_req);
		    $db->setQuery($sql);
		    $db->execute();
		    $menu_params = $db->loadColumn();
		    $menu_params = @$menu_params["0"];

		    if(isset($menu_params) && trim($menu_params) != ""){
		        $menu_params = json_decode($menu_params, true);

		        if(isset($menu_params["display"]) && ($menu_params["display"] == "tree" || $menu_params["display"] == "listing")){
		            if($menu_params["display"] == "tree"){
		                $layout = "0";
		            }
		            elseif($menu_params["display"] == "listing"){
		                $layout = "1";
		            }
		        }
		    }
		}
		//----------------------------------------
		
		if($layout == "0"){		
			$generate = new GenerateDisplay();
			//$generate->generateTreeCategoriesList(0, 2);
		}
		else{
			$categs_array = array();	
			$generate = new GenerateDisplay();
			for($i=0; $i<count($allresults); $i++){
				$image = $allresults[$i]->image;
				if(trim($image) != ""){
					$image = explode("/", $image);
					$image_name = $image[count($image)-1];
					$allresults[$i]->imageName = $image_name;
				}
				$categs_array[] = $generate->generateCategsCellsB($categs_config, $style_categs, $allresults[$i], $config);	
			}
			if(count($categs_array) == 1){
                $span = "12";
            }
            else{
                $span=12/$cols;
            }
			$i = 0;
			while(isset($categs_array[$i])){
				$row = "";
				$row .= '<div class="course_row_guru uk-grid wk-grid clearfix">';
				for($j=0; $j<$cols; $j++){
					$row .= '<div class="course_cell_guru g_cell uk-width-large-1-'.$cols.' wk-width-large-1-'.$cols.' uk-width-medium-1-'.$cols.' wk-width-medium-1-'.$cols.'">'.@$categs_array[$i++]."</div>";
				}
				$row .= '</div>';
				$return_result .= $row; 
			}		
		}
		return $return_result;
	}
	
	function categlist_home($cid='0', $level='0', $selected_categories=Array() ) {
		$tree = '';
		$level++;	
		$db = JFactory::getDBO();

		$lang = JFactory::getApplication()->input->get("lang", "", "raw");

		if(strpos($lang, "-") !== false){
			$lang = explode("-", $lang);
			$lang = $lang["0"];
		}

		$q = "SELECT id, child_id, ordering, published, name FROM #__guru_category, #__guru_categoryrel ";
		$q .= "WHERE #__guru_categoryrel.parent_id='$cid' ";
		$q .= "AND #__guru_category.id=#__guru_categoryrel.child_id AND (#__guru_category.language='' OR #__guru_category.language='*' OR #__guru_category.language='".$lang."')";
		$q .= "ORDER BY #__guru_category.ordering ASC";
		$db->setQuery($q);
		$allresults = $db->loadObjectList();

		$ask = "SELECT * FROM #__guru_config LIMIT 1 ";
		$db->setQuery( $ask );
		$result = $db->loadObject();
		$cols = $result->ctgscols;
		global $i;
		$i = 0;
		
		foreach ($allresults as $child) {
			$id = $child->id;
			$no_programs = guruModelguruPcateg::getnoprograms($id);
			$no_programs2 = guruModelguruPcateg::no_of_programs_for_category_recursive($id);
		
			if($result->show_empty_categ=='1' || ($result->show_empty_categ=='0' && $no_programs>0) || ($result->show_empty_categ=='0' && $no_programs2>0)){ 

				$item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");
				$helper = new guruHelper();
                $itemid_menu = $helper->getCategMenuItem(intval($id));
                $item_id_categ = $item_id;

                if(intval($itemid_menu) > 0){
                    $item_id_categ = intval($itemid_menu);
                }

				$link = JRoute::_("index.php?option=com_guru&view=guruPcategs&task=view&cid=".$id."&Itemid=".intval($item_id_categ));		
				$child_id = $child->id;
				if ($child_id != $cid) {
					$tree = $tree."<li><a href='".$link."'>";
				}

				$tree = $tree.$child->name . " (".$no_programs.")</a>";
			
				$no_of_sub_categories = 0;
				// we extract how many subcategories has a subcategory
				$sub_category = guruModelguruPcateg::getchildren_of_subcategory($id);
				$no_of_sub_categories = count($sub_category);
				if ($no_of_sub_categories>0){
					$tree = $tree.'<ul>'.$this->categlist_home($child_id, $level, $selected_categories).'</ul>';
				}
			} 	
			$i++;
		}
		return $tree;
	}
	
	function ignoreHtml($text, $length = 100, $ending = '&hellip;', $exact = false, $considerHtml = true){
		$text = JHtml::_('content.prepare', $text);
		$text = preg_replace("/<img(.*)>/msU", "", $text);
		
		if ($considerHtml){
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length){
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$open_tags = array();
			$total_length = 0;
			$truncate = '';
			
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])){
						// do nothing
					// if tag is a closing tag
					}
					elseif(preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)){
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if($pos !== false){
							unset($open_tags[$pos]);
						}
					// if tag is an opening tag
					}
					elseif(preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)){
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				
				if(intval($total_length) + intval($content_length) > intval($length)){
					// the number of characters which are left
					$left = intval($length) - intval($total_length);
					$entities_length = 0;
					// search for html entities
					if(preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach($entities["0"] as $entity){
							if($entity[1] + 1 - $entities_length <= $left){
								$left--;
								$entities_length += strlen($entity["0"]);
							}
							else{
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left + $entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				}
				else{
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if($total_length >= $length) {
					break;
				}
			}
		}
		else{
			if(strlen($text) <= $length){
				return $text;
			}
			else{
				$truncate = substr($text, 0, $length);
			}
		}
		// if the words shouldn't be cut in the middle...
		if(!$exact && $length > 10){
			$spacepos = strrpos($truncate, ' ');
			if(isset($spacepos)){
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		// close all unclosed html-tags
		if($considerHtml){
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}
	
	function cutDescription($description, $description_length, $description_type, $description_mode){
		if(intval($description_length) == 0){
			return "";
		}
	
		$original_text = $description;
		$description = strip_tags($description);
		
		if($description_mode == 0){
			$original_text = strip_tags($original_text);
		}
		
		if($description_length == "" || strlen($description) <= $description_length){
			return $original_text;
		}
		else{
			if($description_type == "0"){
				$return = $this->ignoreHtml($original_text, $description_length);
				return $return;
			}
			elseif($description_type == "1"){
				$return = "";
				
				$description = str_replace("\r\n", " ", $description);
				$description = str_replace("\r", " ", $description);
				$description = str_replace("\n", " ", $description);
				$description = str_replace("  ", " ", $description);
				
				$words = explode(" ", $description);
				$words = array_slice($words, 0, $description_length);
				$return = implode(" ", $words);
				
				$new_length = strlen($return);
				$return = ignoreHtml($original_text, $new_length + ($description_length - 1));
				return $return;
			}
		}
	}
}

?>