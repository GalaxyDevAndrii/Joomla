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

$k = 0;
$n = count($this->categs);
$categs = $this->categs;
$config = $this->getConfigSettings;
$document = JFactory::getDocument();
$document->setTitle(JText::_("GURU_PROGRAM_CATEGORIES"));

require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."generate_display.php");

$categs_config = json_decode($config->ctgspage);
$style_categs = json_decode($config->st_ctgspage);
$layout = $categs_config->ctgslayout;
$cols = $categs_config->ctgscols;

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

// Tree categories layout
function generateTreeCategoriesList($parent_id, $level){
    $db = JFactory::getDBO();
    $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");
	
	$helper = new guruHelper();
	$itemid_seo = $helper->getSeoItemid();
	$itemid_seo = @$itemid_seo["gurupcategs"];
	
	if(intval($itemid_seo) > 0){
		$item_id = intval($itemid_seo);
	}

    $lang = JFactory::getApplication()->input->get("lang", "", "raw");

    if(strpos($lang, "-") !== false){
        $lang = explode("-", $lang);
        $lang = $lang["0"];
    }
	
    $sql = "select c.id, c.name, c.groups from #__guru_category c, #__guru_categoryrel cr where c.id=cr.child_id and cr.parent_id=".intval($parent_id)." and c.published=1 AND (c.language='' OR c.language='*' OR c.language='".$lang."')";
    $db->setQuery($sql);
    $db->execute();
    $childrens = $db->loadAssocList();

    $i = 1;
    if(isset($childrens) && is_array($childrens) && count($childrens) > 0){
        echo '<ul class="guru-list">';
        $level = $level == 1 ? 2 : 1;
        $user = JFactory::getUser();

        foreach($childrens as $key=>$value){
            $cat_id = $value["id"] == "0" ? "-1" : $value["id"];
            $sql = "select count(*) from #__guru_program where catid=".($cat_id)." and published='1'";
            $db->setQuery($sql);
            $db->execute();
            $result = $db->loadColumn();
            $add_category_to_tree = false;

            if($result["0"] != "0"){
                if(isset($value["groups"]) && trim($value["groups"]) != ""){
                    if(intval($user->id) == 0){
                        $acl_groups = json_decode(trim($value["groups"]), true);
                        if(in_array("1", $acl_groups) || in_array("9", $acl_groups)){ // Public or Guest
                            $add_category_to_tree = true;
                        }
                    }
                    else{
                        // user logged and category ACL added
                        $user_groups = $user->groups;
                        $acl_groups = json_decode(trim($value["groups"]), true);
                        $intersect = array_intersect($user_groups, $acl_groups);

                        if(isset($intersect) && is_array($intersect) && count($intersect) > 0){
                            $add_category_to_tree = true;
                        }
                    }
                }
                else{
                    $add_category_to_tree = true;
                }

                if($add_category_to_tree){
                    echo '<li class="guru_level'.$level.'">';
                    if(!isset($next_nr)){
                        $next_nr = "";
                    }

                    $helper = new guruHelper();
                    $itemid_menu = $helper->getCategMenuItem(intval($value["id"]));
                    $item_id_categ = $item_id;

                    if(intval($itemid_menu) > 0){
                        $item_id_categ = intval($itemid_menu);
                    }

    				$categ_url = JRoute::_('index.php?option=com_guru&view=gurupcategs&task=view&cid='.$value["id"]."-".JFilterOutput::stringURLSafe(trim($value["name"])).'&Itemid='.intval($item_id_categ));
    				
                    echo $next_nr." ".'<a href="'.$categ_url.'">'.trim($value["name"])." (".$result["0"].")".'</a>';

                    generateTreeCategoriesList($value["id"], $level);
                    echo '</li>';
                }
            }
            $i++;
        }
        echo "</ul>";
    }
}

// Count courses number
function countCoursesNumber($cat_id){
    $db = JFactory::getDBO();
    $sql = "select count(*) from #__guru_program where catid=".intval($cat_id)." and published=1 and status='1'";
    $db->setQuery($sql);
    $db->execute();
    $single_result = $db->loadColumn();
    $result = 0;

    $lang = JFactory::getApplication()->input->get("lang", "", "raw");

    if(strpos($lang, "-") !== false){
        $lang = explode("-", $lang);
        $lang = $lang["0"];
    }

    $sql = "select child_id from #__guru_categoryrel where parent_id=".intval($cat_id);
    $db->setQuery($sql);
    $db->execute();
    $ids = $db->loadColumn();
    if(isset($ids) && count($ids) > 0){
        $sql = "select count(*) from #__guru_program where catid in (".implode(", ", $ids).") and published=1 and status='1'";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        $result = $result["0"];
    }
    return (int)$single_result["0"] + (int)$result;
}

// Count subcategories number
function countSubcategsNumber($cat_id){
    $db = JFactory::getDBO();

    $lang = JFactory::getApplication()->input->get("lang", "", "raw");

    if(strpos($lang, "-") !== false){
        $lang = explode("-", $lang);
        $lang = $lang["0"];
    }

    $sql = "select count(*) from #__guru_categoryrel c, #__guru_category ca where c.parent_id=".intval($cat_id)." and c.child_id = ca.id and published=1 AND (ca.language='' OR ca.language='*' OR ca.language='".$lang."')";
    $db->setQuery($sql);
    $db->execute();
    $result = $db->loadColumn();
    return $result["0"];
}

// Ignore html code
function ignoreHtml($html, $maxLength=100){
        $printedLength = 0;
        $position = 0;
        $tags = array();
        $newContent = '';
    
        $html = $content = preg_replace("/<img[^>]+\>/i", "", $html);
    
        while ($printedLength < $maxLength && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position))
        {
            list($tag, $tagPosition) = $match[0];
            // Print text leading up to the tag.
            $str = substr($html, $position, $tagPosition - $position);
            if ($printedLength + strlen($str) > $maxLength){
                $newstr = substr($str, 0, $maxLength - $printedLength);
                $newstr = preg_replace('~\s+\S+$~', '', $newstr);  
                $newContent .= $newstr;
                $printedLength = $maxLength;
                break;
            }
            $newContent .= $str;
            $printedLength += strlen($str);
            if ($tag[0] == '&') {
                // Handle the entity.
                $newContent .= $tag;
                $printedLength++;
            } else {
                // Handle the tag.
                $tagName = $match[1][0];
                if ($tag[1] == '/') {
                  // This is a closing tag.
                  $openingTag = array_pop($tags);
                  assert($openingTag == $tagName); // check that tags are properly nested.
                  $newContent .= $tag;
                } else if ($tag[strlen($tag) - 2] == '/'){
              // Self-closing tag.
                $newContent .= $tag;
            } else {
              // Opening tag.
              $newContent .= $tag;
              $tags[] = $tagName;
            }
          }
    
          // Continue after the tag.
          $position = $tagPosition + strlen($tag);
        }
    
        // Print any remaining text.
        if ($printedLength < $maxLength && $position < strlen($html))
          {
            $newstr = substr($html, $position, $maxLength - $printedLength);
            $newstr = preg_replace('~\s+\S+$~', '', $newstr);
            $newContent .= $newstr;
          }
    
        // Close any open tags.
        while (!empty($tags))
          {
            $newContent .= sprintf('</%s>', array_pop($tags));
          }
    
        return $newContent."...";
    }

// Start cutBio function to dispaly teacher description by admin settings
function cutBio($full_bio, $description_length, $description_type, $description_mode){
    if(intval($description_length) == 0){
		return "";
	}
	
	
	$original_text = $full_bio;
    $full_bio = strip_tags($full_bio);
    
    if($description_mode == 0){
        // Text
        $original_text = strip_tags($original_text);
    }
    
    if($description_length == "" || strlen($full_bio) <= $description_length){
        return $original_text;
    }
    else{
        if($description_type == "0"){
            $return = ignoreHtml($original_text, $description_length);
            return $return;
        }
        elseif($description_type == "1"){
            $return = "";
            
            $full_bio = str_replace("\r\n", " ", $full_bio);
            $full_bio = str_replace("\r", " ", $full_bio);
            $full_bio = str_replace("\n", " ", $full_bio);
            $full_bio = str_replace("  ", " ", $full_bio);
            
            $words = explode(" ", $full_bio);
            $words = array_slice($words, 0, $description_length);
            $return = implode(" ", $words);
            
            $new_length = strlen($return);
            $return = ignoreHtml($original_text, $new_length + ($description_length - 1));
            return $return;
        }
    }
}

// Mini Profile layout - Start generate category list view function
function generateCategsCells($config_categs, $style_categs, $course, $config){
    $guruHelper = new guruHelper();
    $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");
    
	$helper = new guruHelper();
	$itemid_seo = $helper->getSeoItemid();
	$itemid_seo = @$itemid_seo["gurupcategs"];
	
	if(intval($itemid_seo) > 0){
		$item_id = intval($itemid_seo);
	}
	
	$return = "";
    $img_align = $config_categs->ctgs_image_alignment; //0-center, 1-left, 2-right
    $layout = $config_categs->ctgslayout;
    $cols = $config_categs->ctgscols;
    $read_more = $config_categs->ctgs_read_more; //0-yes 1-no
    $read_align = $config_categs->ctgs_read_more_align == "0" ? "left" : "right";
    $description_align = $config_categs->ctgs_description_alignment == "0" ? "left" : "right";
    $edit_read_more = $config_categs->ctgs_read_more;
    $courses_number = countCoursesNumber($course->id);
    $sub_categs_number = countSubcategsNumber($course->id);
    $show_empty_categs = $config_categs->ctgs_show_empty_catgs;
    $show = true;

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

    if (isset($course->alias) && $course->alias != "") {
        $alias = trim($course->alias);
    }

    else {
        $alias =  JFilterOutput::stringURLSafe($course->name);
    }

    //$alias = isset($course->alias) == "" ? trim($course->alias) : JFilterOutput::stringURLSafe($course->name);
    if ($show_empty_categs == "0") {
        $show = true;
    }

    elseif ($show_empty_categs == "1") {
        if (intval($sub_categs_number) > 0 || intval($courses_number) > 0) {
            $show = true;
        }

        else {
            $show = false;
        }
    }

    $edit_sum = "";
    $edit_sum_array = array();

    if ($sub_categs_number > 0) {
        if ($sub_categs_number == 1) {
            $edit_sum_array[] = $sub_categs_number." ".JText::_("GURU_NUMBER_CATEGORY");
        }

        else {
            $edit_sum_array[] = $sub_categs_number." ".JText::_("GURU_NUMBER_CATEGORIES");
        }
    }

    if ($courses_number > 0) {
        if ($courses_number == 1) {
            $edit_sum_array[] = $courses_number." ".JText::_("GURU_NUMBER_COURSE");
        }

        else {
            $edit_sum_array[] = $courses_number." ".JText::_("GURU_NUMBER_COURSES");
        }
    }

    $edit_sum = "";
    if(count($edit_sum_array) > 0){
        $edit_sum = "".implode(" / ", $edit_sum_array)."";
    }

    if ($show === true) {
        // If layout is Mini Profile
        if ($layout == "1") {

            $image_size = 0;

            if ($cols == "1") {
                $image_size = "900";
            }

            else {
                $image_size = "700";
            }

            if (trim($course->image) == "") {
                $course->image = "components/com_guru/images/thumbs/no_image.gif";
                $course->imageName = "no_image.gif";
                $guruHelper->createThumb($course->imageName, "components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."images", $image_size, 0);
            }

            else {
                $guruHelper->createThumb($course->imageName, $config->imagesin."/categories", $image_size, 0);
            }

            // Empty image as default
            $image = "";

            if(trim($course->image) != ""){
                $image = '<img alt="Category Image" class="uk-border-rounded wk-border-rounded '.$style_categs->ctgs_image.'" src="'.JURI::root().$course->image.'" />';

                $helper = new guruHelper();
                $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                $item_id_categ = $item_id;

                if(intval($itemid_menu) > 0){
                    $item_id_categ = intval($itemid_menu);
                }

                // Generate image link
                $image_link = JRoute::_('index.php?option=com_guru&view=gurupcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ);
            }

            $description = cutBio($course->description, $config_categs->ctgs_description_length, $config_categs->ctgs_description_type, $config_categs->ctgs_description_mode);

            // Category box structure
            $return .= '<div class="uk-panel uk-panel-box wk-panel wk-panel-box clearfix">';

            if ($img_align == 0) { // Centered image

                if(trim($image) != ""){
					$course->image = str_replace("thumbs/", "", $course->image);
                    $return .= '<div class="uk-panel-teaser wk-panel-teaser gru-panel-teaser" style="background-image: url(\''.JURI::root().$course->image.'\');">
                        <a href="'.$image_link.'"></a>
                    </div>';
                }

                $return .= '<div class="uk-panel-badge uk-badge uk-badge-notification uk-badge-warning wk-panel-badge wk-badge wk-badge-notification wk-badge-warning">'.$edit_sum.'</div>';

                $helper = new guruHelper();
                $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                $item_id_categ = $item_id;

                if(intval($itemid_menu) > 0){
                    $item_id_categ = intval($itemid_menu);
                }

                $return .= '<h3 class="uk-panel-title wk-panel-title '.$style_categs->ctgs_categ_name.'">
                                <a href="'.JRoute::_('index.php?option=com_guru&view=gurupcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.$course->name.'</a>
                            </h3>';

                if($read_more == "0" && $edit_read_more == "0"){
                    $rmore = '<a style="float:'.$read_align.'" class="uk-button wk-button" href="'.JRoute::_('index.php?option=com_guru&view=gurupcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a>';
                }

                else{
                    $rmore ="";
                }

                $return .= '<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.';">'.$description.$rmore.'</div>';

            } elseif ($img_align == 1) { // Left image alignment

                $return .= '<div class="uk-grid uk-grid-small wk-grid wk-grid-small">';

                $return .= '<div class="uk-badge uk-panel-badge uk-badge-warning wk-badge wk-panel-badge wk-badge-warning">'.$edit_sum.'</div>';

                $helper = new guruHelper();
                $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                $item_id_categ = $item_id;

                if(intval($itemid_menu) > 0){
                    $item_id_categ = intval($itemid_menu);
                }

                $return .= '<div class="uk-width-1-1 wk-width-1-1">
                                <div class="uk-panel-header wk-panel-header">
                                    <h3 class="uk-panel-title wk-panel-title '.$style_categs->ctgs_categ_name.'">
                                        <a href="'.JRoute::_('index.php?option=com_guru&view=gurupcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.$course->name.'</a>
                                    </h3>
                                </div>
                            </div>';

                if(trim($image) != ""){
                    $return .= '<div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3 '.$style_categs->ctgs_image.'">'.$image.'</div>';
                }

                $return .= '<div class="uk-width-1-1 uk-width-medium-2-3 wk-width-1-1 wk-width-medium-2-3">';

                    if($read_more == "0" && $edit_read_more == "0"){
                        $rmore = '<a style="float:'.$read_align.'" class="uk-button wk-button" href="'.JRoute::_('index.php?option=com_guru&view=gurupcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a>';
                    }

                    else{
                        $rmore ="";
                    }

                    $return .= '<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.';">'.$description.$rmore.'</div>';

                $return .= '</div>';

                $return .= '</div>';

            } elseif ($img_align == 2) { // Right image alignment

                $return .= '<div class="uk-grid uk-grid-small wk-grid wk-grid-small">';

                $return .= '<div class="uk-badge uk-panel-badge uk-badge-warning wk-badge wk-panel-badge wk-badge-warning">'.$edit_sum.'</div>';

                $helper = new guruHelper();
                $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                $item_id_categ = $item_id;

                if(intval($itemid_menu) > 0){
                    $item_id_categ = intval($itemid_menu);
                }

                $return .= '<div class="uk-width-1-1 wk-width-1-1">
                                <div class="uk-panel-header wk-panel-header">
                                    <h3 class="uk-panel-title wk-panel-title '.$style_categs->ctgs_categ_name.'">
                                        <a href="'.JRoute::_('index.php?option=com_guru&view=gurupcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.$course->name.'</a>
                                    </h3>
                                </div>
                            </div>';

                $return .= '<div class="uk-width-1-1 uk-width-medium-2-3 wk-width-1-1 wk-width-medium-2-3">';

                    if($read_more == "0" && $edit_read_more == "0"){
                        $rmore = '<a style="float:'.$read_align.'" class="uk-button wk-button" href="'.JRoute::_('index.php?option=com_guru&view=gurupcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a>';
                    }

                    else{
                        $rmore ="";
                    }

                    $return .= '<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.';">'.$description.$rmore.'</div>';

                $return .= '</div>';

                if(trim($image) != ""){
                    $return .= '<div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3 '.$style_categs->ctgs_image.'">'.$image.'</div>';
                }

                $return .= '</div>';
            }

            $return .= '</div>';
        }//if mini profile
    }//if show
    return $return;
}
//end generate category list view function
?>
<div class="gru-list-categories">
    <div class="uk-grid wk-grid" data-uk-grid-margin>
        <div class="uk-width-1-1 wk-width-1-1">
            <h2 class='gru-page-title'>
                <?php echo JText::_('GURU_PROGRAM_CATEGORIES'); ?>
            </h2>
        </div>
    </div>
    <?php
    $grid_style = "";

    if ($cols == '4') {
        $grid_style = "uk-grid-small wk-grid-small";
    }

    if($layout == "0"){
        echo '<div class="uk-panel uk-panel-box uk-panel-box-secondary uk-margin-top wk-panel wk-panel-box wk-panel-box-secondary wk-margin-top">';
		generateTreeCategoriesList(0, 2);
        echo '</div>';
    }
    else{
        $categs_array = array();
        for($i=0; $i<count($categs); $i++){
            $categs_array[] = generateCategsCells($categs_config, $style_categs, $categs[$i], $config); //here is the result from function generateCategsCells called above
        }
        $i = 0;
        if(count($categs_array) == "1"){
            $cols = 1;
            $span = 12;
        }
        else{
            $span=12/$cols;
        }
        while(isset($categs_array[$i])){
            $row = "";
            $row .= "<div class='uk-grid wk-grid ".$grid_style." uk-grid-match wk-grid-match' data-uk-grid-match=\"{target:'.uk-panel'}\">";
            $j = 0;
            
            while($j<$cols){
                if(!isset($categs_array[$i])){
                    $j = $cols;
                    $i++;
                }
                elseif(isset($categs_array[$i]) && trim($categs_array[$i]) != ""){
                    $row .= '<div class="uk-width-1-1 wk-width-1-1 uk-width-medium-1-'.$cols.' wk-width-medium-1-'.$cols.'">'.$categs_array[$i]."</div>";
                    $j++;
                }
                $i++;
            }
            
            $row .= '</div>';
            echo $row;
        }
    }
    ?>
</div>
<script type="text/javascript" src="components/com_guru/js/jquery.height_equal.js"></script>
<script>
    window.onload = equalHeight('course_cell_guru');
</script>