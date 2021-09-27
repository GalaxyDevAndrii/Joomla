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

    $document = JFactory::getDocument();
    require_once(JPATH_BASE . "/components/com_guru/helpers/Mobile_Detect.php");

    require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');
    $guruHelper = new guruHelper();
    
    $document->setTitle(JText::_("GURU_PROGRAM_CATEGORIES"));
    $display = $this->display;
    $k = 0;
    $categ = $this->categ;  
    $subcateg = $this->subcateg;
    $programs = $this->programs;
    $k = count($programs);
    $n = count($subcateg);
    $config = $this->getConfigSettings;
    $config_category = json_decode($config->ctgpage);
    $config_category_style = json_decode($config->st_ctgpage);
    $type = $config_category->ctg_image_size_type == "0" ? "w" : "h";
    $category_layout = "";

    $lang = JFactory::getApplication()->input->get("lang", "", "raw");

    if(strpos($lang, "-") !== false){
        $lang = explode("-", $lang);
        $lang = $lang["0"];
    }
    
    if($categ->language != "" && $categ->language != "*"){
        if($categ->language != $lang){
            $app = JFactory::getApplication();
            $app->redirect(JURI::root());
            die();
        }
    }

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

        $sql = "select c.id, c.name from #__guru_category c, #__guru_categoryrel cr where c.id=cr.child_id and cr.parent_id=".intval($parent_id)." and c.published=1 AND (c.language='' OR c.language='*' OR c.language='".$lang."')";

        $db->setQuery($sql);

        $db->execute();

        $childrens = $db->loadAssocList();



        $i = 1;

        if(isset($childrens) && is_array($childrens) && count($childrens) > 0){

            echo '<div id="categoryList"><ul>';

            $level = $level == 1 ? 2 : 1;

            foreach($childrens as $key=>$value){
                $cat_id = $value["id"] == "0" ? "-1" : $value["id"];

                $sql = "select count(*) from #__guru_program where catid=".($cat_id)." and `status`='1'";

                $db->setQuery($sql);

                $db->execute();

                $result = $db->loadColumn();

                if($result["0"] != "0"){

                    echo '<li class="guru_level'.$level.'">';

                    if(!isset($next_nr)){

                        $next_nr = "";

                    }

                    echo $next_nr." ".'<a href="index.php?option=com_guru&view=guruPcategs&task=view&cid='.$value["id"]."-".JFilterOutput::stringURLSafe(trim($value["name"])).'&Itemid='.intval($item_id).'">'.trim($value["name"])." (".$result["0"].")".'</a>';
                    
                    //generateTreeCategoriesList($value["id"], $level);

                    echo '</li>';

                }

                $i++;

            }

            echo "</ul></div>";

        }

    }

    function countCoursesNumber($cat_id){
        $db = JFactory::getDBO();

        $sql = "select count(*) from #__guru_program where catid=".intval($cat_id)." and published=1 and `status`='1'";
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
            $sql = "select count(*) from #__guru_program where catid in (".implode(", ", $ids).") and published=1 and `status`='1'";
            $db->setQuery($sql);
            $db->execute();
            $result = $db->loadColumn();
        }

        return (int)$single_result[0] + (int)$result[0];
    }



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

    
function generateCategsCellsC($config_categs, $style_categs, $course, $config){
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
                $image = '<img alt="Category Image" class="uk-border-rounded '.$style_categs->ctgs_image.'" src="'.JURI::root().$course->image.'" />';

                // Generate image link
                $image_link = JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id);
            }

            $description = cutBio($course->description, $config_categs->ctgs_description_length, $config_categs->ctgs_description_type, $config_categs->ctgs_description_mode);

            // Category box structure
            $return .= '<div class="uk-panel uk-panel-box clearfix">';

            if ($img_align == 0) { // Centered image

                if(trim($image) != ""){
                    $course->image = str_replace("thumbs/", "", $course->image);
                
                    $return .= '<div class="uk-panel-teaser gru-panel-teaser" style="background-image: url('.JURI::root().$course->image.');">
                        <a href="'.$image_link.'"></a>
                    </div>';
                }

                //$return .= '<div class="uk-panel-badge uk-badge uk-badge-notification uk-badge-warning">'.$edit_sum.'</div>';

                $return .= '<h3 class="uk-panel-title '.$style_categs->ctgs_categ_name.'">
                                <a href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.$course->name.'</a>
                            </h3>';

                if($read_more == "0" && $edit_read_more == "0"){
                    $rmore = '<a style="float:'.$read_align.'" class="uk-button" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a>';
                }

                else{
                    $rmore ="";
                }

                $return .= '<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.';"><p>'.$description.'</p>'.$rmore.'</div>';

            } elseif ($img_align == 1) { // Left image alignment

                $return .= '<div class="uk-grid uk-grid-small">';

                $return .= '<div class="uk-badge uk-panel-badge uk-badge-warning">'.$edit_sum.'</div>';

                $return .= '<div class="uk-width-1-1">
                                <div class="uk-panel-header">
                                    <h3 class="uk-panel-title '.$style_categs->ctgs_categ_name.'">
                                        <a href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.$course->name.'</a>
                                    </h3>
                                </div>
                            </div>';

                if(trim($image) != ""){
                    $return .= '<div class="uk-width-1-1 uk-width-medium-1-3 '.$style_categs->ctgs_image.'">'.$image.'</div>';
                }

                $return .= '<div class="uk-width-1-1 uk-width-medium-2-3">';

                    if($read_more == "0" && $edit_read_more == "0"){
                        $rmore = '<a style="float:'.$read_align.'" class="uk-button" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a>';
                    }

                    else{
                        $rmore ="";
                    }

                    $return .= '<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.';"><p>'.$description.'</p>'.$rmore.'</div>';

                $return .= '</div>';

                $return .= '</div>';

            } elseif ($img_align == 2) { // Right image alignment

                $return .= '<div class="uk-grid uk-grid-small">';

                $return .= '<div class="uk-badge uk-panel-badge uk-badge-warning">'.$edit_sum.'</div>';

                $return .= '<div class="uk-width-1-1">
                                <div class="uk-panel-header">
                                    <h3 class="uk-panel-title '.$style_categs->ctgs_categ_name.'">
                                        <a href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.$course->name.'</a>
                                    </h3>
                                </div>
                            </div>';

                $return .= '<div class="uk-width-1-1 uk-width-medium-2-3">';

                    if($read_more == "0" && $edit_read_more == "0"){
                        $rmore = '<a style="float:'.$read_align.'" class="uk-button" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a>';
                    }

                    else{
                        $rmore ="";
                    }

                    $return .= '<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.';"><p>'.$description.'</p>'.$rmore.'</div>';

                $return .= '</div>';

                if(trim($image) != ""){
                    $return .= '<div class="uk-width-1-1 uk-width-medium-1-3 '.$style_categs->ctgs_image.'">'.$image.'</div>';
                }

                $return .= '</div>';
            }

            $return .= '</div>';
        }//if mini profile
    }//if show
    return $return;
}

    
// start the category and sub-category  generation function 
     function generateCategsCellsB($config_categs, $style_categs, $course, $config){
        $guruHelper = new guruHelper();

        $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");
        
        $helper = new guruHelper();
        $itemid_seo = $helper->getSeoItemid();
        $itemid_seo = @$itemid_seo["gurupcategs"];
        
        if(intval($itemid_seo) > 0){
            $item_id = intval($itemid_seo);
        }
        
        $type = $config_categs->ctgs_image_size_type == "0" ? "w" : "h";

        $return = "";

        $layout = $config_categs->ctgslayout;

        $img_align = $config_categs->ctgs_image_alignment; //0-left, 1-right

        $read_more = $config_categs->ctgs_read_more; //0-yes 1-no

        $read_align = $config_categs->ctgs_read_more_align == "0" ? "left" : "right";

        $description_align = $config_categs->ctgs_description_alignment == "0" ? "left" : "right";

        $edit_read_more = $config_categs->ctgs_read_more;

        $courses_number = countCoursesNumber($course->id);

        $sub_categs_number = countSubcategsNumber($course->id);

        $show_empty_categs = $config_categs->ctgs_show_empty_catgs;

        $show = true;

        $rt = "";

        $detect = new Mobile_Detect;

        $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');



         if(isset($course->alias) && $course->alias != ""){

            $alias = trim($course->alias);

        }

        else{

            $alias =  JFilterOutput::stringURLSafe($course->name);

        }

        //$alias = isset($course->alias) == "" ? trim($course->alias) : JFilterOutput::stringURLSafe($course->name);



        if($show_empty_categs == "0"){

            $show = true;

        }

        elseif($show_empty_categs == "1"){

            if(intval($sub_categs_number) > 0 || intval($courses_number) > 0){

                $show = true;

            }

            else{

                $show = false;

            }

        }



        $edit_sum = "";

        $edit_sum_array = array();

        if($sub_categs_number > 0){

            if($sub_categs_number == 1){

                $edit_sum_array[] = $sub_categs_number." ".JText::_("GURU_NUMBER_CATEGORY");

            }

            else{

                $edit_sum_array[] = $sub_categs_number." ".JText::_("GURU_NUMBER_CATEGORIES");

            }

        }

        if($courses_number > 0){

            if($courses_number == 1){

                $edit_sum_array[] = $courses_number." ".JText::_("GURU_NUMBER_COURSE");

            }

            else{

                $edit_sum_array[] = $courses_number." ".JText::_("GURU_NUMBER_COURSES");

            }

        }

        $edit_sum = "";

        if(count($edit_sum_array) > 0){

            $edit_sum = " (".implode(" / ", $edit_sum_array).") ";

        }



        if($deviceType =="phone"){

            $nameandnumb = $course->name."<br/>".$edit_sum;

            $style_m = "padding-left:20px;";

        }

        else{

            $nameandnumb = $course->name.$edit_sum;

            $style_d = "";

        }



        if($show === true){

            if($layout == "1"){//mini profile

                if(trim($course->image) == ""){

                    $course->image = "components/com_guru/images/thumbs/no_image.gif";

                    $course->imageName = "no_image.gif";

                   $guruHelper->createThumb($course->imageName, "components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."images", $config_categs->ctgs_image_size, $type);

                }

                else{

                    $guruHelper->createThumb($course->imageName, $config->imagesin."/categories", $config_categs->ctgs_image_size, $type);

                }

                $image = "";

                if(trim($course->image) != ""){

                    $image = '<img alt="Category Image" src="'.JURI::root().$course->image.'" />';

                    $image_left = '<a class="thumbnail pull-left" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.$image.'</a>';
                    $image_right = '<a class="thumbnail pull-right" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.$image.'</a>';

                }

                $description = cutBio($course->description, $config_categs->ctgs_description_length, $config_categs->ctgs_description_type, $config_categs->ctgs_description_mode);

                if($wrap == "1"){//no wrap
                    $class_display = "display:table-cell;";

                    if($img_align == "0"){// left

                        $return .= "<div class='image_guru'>";

                        if(trim($image) != ""){

                            $return .=  $image_left;
                        }

                        $return .=          '<div class="'.$style_categs->ctgs_categ_name.'">

                                                        <a style="'.$style_d.'" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id.'-'.$alias.'&Itemid='.intval($item_id)).'">'.$nameandnumb.'</a>

                                                    </div>';

                        if($read_more == "0"&& $edit_read_more == "0"){

                            $rt ='<div class="readon"><a class="btn btn-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        elseif($read_more == "1" && $edit_read_more == "0"){

                            $rt ='<div class="readon"><a class="btn btn-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        $return .=          '<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.'; '.$style_d.' '.$class_display.'"><p>'.$description.'</p>'.$rt.'</div>';

                        $return .= "</div>";

                    }

                    elseif($img_align == "1"){// right

                        $return .= "<div class='image_guru'>";

                        if(trim($image) != ""){

                            $return .=  $image_right;
                        }

                        $return .=          '<div class="'.$style_categs->ctgs_categ_name.'">

                                                        <a style="'.$style_d.'" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id.'-'.$alias.'&Itemid='.intval($item_id)).'">'.$nameandnumb.'</a>

                                                    </div>';

                        if($read_more == "0"&& $edit_read_more == "0"){

                            $rt ='<div class="readon"><a class="btn btn-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        elseif($read_more == "1" && $edit_read_more == "0"){

                            $rt ='<div class="readon"><a class="btn btn-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        $return .=          '<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.'; '.$style_d.' '.$class_display.'"><p>'.$description.'</p>'.$rt.'</div>';

                        $return .= "</div>";
                    }

                }

                elseif($wrap == "0"){//wrap
                     if($img_align == "0"){// left
                        $return .= "<div class='image_guru'>";

                        if(trim($image) != ""){

                            $return .=  $image_left;
                        }

                        $return .=          '<div class="'.$style_categs->ctgs_categ_name.'">

                                                        <a style="'.$style_d.'" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id.'-'.$alias.'&Itemid='.intval($item_id)).'">'.$nameandnumb.'</a>

                                                    </div>';

                        if($read_more == "0"&& $edit_read_more == "0"){

                            $rt ='<div style="text-align:'.$read_align.'" class="readon">'.'<a class="btn btn-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        elseif($read_more == "1" && $edit_read_more == "0"){

                            $rt ='<div style="text-align:'.$read_align.'" class="readon">'.'<a class="btn btn-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        $return .=          '<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.'; '.$style_d.'"><p>'.$description.'</p>'.$rt.'</div>';

                        $return .= "</div>";
                    }

                    elseif($img_align == "1"){// right

                        $return .= "<div class='image_guru'>";

                        if(trim($image) != ""){

                            $return .=  $image_right;
                        }

                        $return .=          '<div class="'.$style_categs->ctgs_categ_name.'">

                                                        <a style="'.$style_d.'" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id.'-'.$alias.'&Itemid='.intval($item_id)).'">'.$nameandnumb.'</a>

                                                    </div>';

                        if($read_more == "0"&& $edit_read_more == "0"){

                            $rt ='<div class="readon"><a class="btn btn-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        elseif($read_more == "1" && $edit_read_more == "0"){

                            $rt ='<div class="readon"><a class="btn btn-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        $return .=          '<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.'; '.$style_d.'"><p>'.$description.'</p>'.$rt.'</div>';

                        $return .= "</div>";
                    }

                }


            }//if mini profile

        }//if show
        return $return;

    }


// start the category and sub-category  generation function 



// start course in the category (Courses in this category:)generation function

function generateCoursesCellsB($config_courses, $style_courses, $course, $config) {
    $guruHelper = new guruHelper();
    $detect = new Mobile_Detect;

    $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

    $type = $config_courses->courses_image_size_type == "0" ? "w" : "h";

    $return = "";

    $layout = $config_courses->courseslayout;

    $img_align = $config_courses->courses_image_alignment; //0-left, 1-right

    $read_more = $config_courses->courses_read_more; //0-yes 1-no

    $read_align = $config_courses->courses_read_more_align == "0" ? "left" : "right";

    $description_align = $config_courses->courses_description_alignment == "0" ? "left" : "right";

    $edit_read_more = $config_courses->courses_read_more;

    $alias = trim($course->alias) == "" ? JFilterOutput::stringURLSafe($course->name) : trim($course->alias);

    $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");
    
    $helper = new guruHelper();
    $itemid_seo = $helper->getSeoItemid();
    $itemid_seo = @$itemid_seo["gurupcategs"];
    
    if(intval($itemid_seo) > 0){
        $item_id = intval($itemid_seo);
    }
    
    $rt = "";

    $style_d = "";

    $cols = $config_courses->coursescols;



    if($layout == "1"){//mini profile
        $image_name = explode("/", $course->image_avatar);
        $image_name = $image_name[count($image_name)-1];

        $image_size = 0;

        if ($cols == "1") {
            $image_size = "900";
        }

        else {
            $image_size = "700";
        }

        if(trim($course->image_avatar) == ""){
            $course->image_avatar = "components/com_guru/images/thumbs/no_image.gif";
            $guruHelper->createThumb($image_name, "components/com_guru/images", $image_size, 0);
        }
        else{
            $guruHelper->createThumb($image_name, $config->imagesin."/courses", $image_size, 0);
        }

        $image_avatar = "";

        if (trim($course->image_avatar) != ""){
             $helper = new guruHelper();
            $itemid_seo = $helper->getSeoItemid();
            $itemid_seo = @$itemid_seo["guruprograms"];
            
            if(intval($itemid_seo) > 0){
                $item_id = intval($itemid_seo);
            }
            
            $image = '<img src="'.JURI::root().$course->image_avatar.'" />';
            $image_url = JURI::root().$course->image_avatar;
            $image_link = JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id));
            $image_rounded = '<img class="uk-border-rounded" src="'.JURI::root().$course->image_avatar.'" />';
            $image_left = '<a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.$image_rounded.'</a>';
            $image_right = '<a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.$image_rounded.'</a>';
        }

        $description = cutBio($course->description, $config_courses->courses_description_length, $config_courses->courses_description_type, $config_courses->courses_description_mode);
        
        $guruModelguruPcateg = new guruModelguruPcateg();
        $authors_urls = $guruModelguruPcateg->getCourseAuthors($course->author);
        
        $config_category = json_decode($config->ctgpage);
        $count_courses_text = "";
        $count_courses_text_tip = "";
        
        if(!isset($config_category->ctg_students_number)){
            $config_category->ctg_students_number = 0; // YES default
        }

        if($config_category->ctg_students_number == 0){
            $count_courses = $guruModelguruPcateg->getCourseStudentsCount($course->id);
            
            if($count_courses == 1){
                $count_courses_text = "1";
                $count_courses_text_tip = JText::_("GURU_AUTHOR_MY_STUDENT");
            }
            else{
                $count_courses_text = $count_courses;
                $count_courses_text_tip = JText::_("GURU_AUTHOR_MY_STUDENTS");
            }
        }

        // : Course box item, under category
        $return .= '<div class="uk-panel uk-panel-box uk-panel-box-secondary uk-category-view">';

        // : Centered image
        if ($img_align == "0") {

            if (trim($image) != "") {
                $image_url = str_replace("thumbs/", "", $image_url);
            
                $return .= '<div class="uk-panel-teaser gru-panel-teaser uk-cover-background" style="background-image: url(\''.$image_url.'\');">
                                <a href="'.$image_link.'"></a>
                            </div>';
            }
            
                $price = "";

                $price_array = explode("-", $course->price);
                sort($price_array);
                
                if(count($price_array) > 1){
                    if($config->currencypos == 0){
                        $price = JText::_("GURU_CURRENCY_".$config->currency)." ".$guruHelper->displayPrice($price_array["0"])." - ".JText::_("GURU_CURRENCY_".$config->currency)." ".$guruHelper->displayPrice($price_array[count($price_array)-1]);
                    }
                    else{
                        $price = $guruHelper->displayPrice($price_array["0"])." ".JText::_("GURU_CURRENCY_".$config->currency)." - ".$guruHelper->displayPrice($price_array[count($price_array)-1])." ".JText::_("GURU_CURRENCY_".$config->currency);
                    }
                }
                else{
                    if($config->currencypos == 0){
                        $price = JText::_("GURU_CURRENCY_".$config->currency)." ".$guruHelper->displayPrice($price_array["0"]);
                    }
                    else{
                        $price = $guruHelper->displayPrice($price_array["0"])." ".JText::_("GURU_CURRENCY_".$config->currency);
                    }
                }
                
                $course_config = json_decode($config->psgpage, true);
                $show_price = $course_config["course_price"];
                
                if(trim($course->price) == ""){
                    $price = JText::_("GURU_FREE");
                }
                
                
                
                $helper = new guruHelper();
                $itemid_seo = $helper->getSeoItemid();
                $itemid_seo = @$itemid_seo["guruprograms"];
                
                if(intval($itemid_seo) > 0){
                    $item_id = intval($itemid_seo);
                }
                
                $return .= '<div class="author">'.$authors_urls.'</div><div class="'.$style_courses->courses_name.'">
                                <h3 class="uk-panel-title"><a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.$course->name.'</a></h3>
                            </div>';

                if ($read_more == "0" && $edit_read_more == "0") {
                    // : Show `Read more` button depending on admin settings
                    $rt =  '<a class="pull-right" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a>';
                }

                $description = JHtml::_('content.prepare', $description);

                $return .= '<div class="'.$style_courses->courses_description.' " style="text-align:'.$description_align.' '.$style_d.';"><p>'.$description.'</p></div>';
                $return .= '<div class="student-info"><i class="fa fa-user"></i>'.$count_courses_text.' '.JText::_('TPL_AMOUNT_STUDENTS_FRONT').'</div>';

                $return .= '<div class="course-footer clearfix"><span class="price pull-left">'.$price.'</span><span class="readmore">'.$rt.'</span></div>';
        }

        // : Left alignment
        elseif ($img_align == "1") {
            
            $price = "";
                
            if($config->currencypos == 0){
                $price = JText::_("GURU_CURRENCY_".$config->currency).$course->price;
            }
            else{
                $price = $course->price.JText::_("GURU_CURRENCY_".$config->currency);
            }
            
            if(trim($course->price) == ""){
                $price = JText::_("GURU_FREE");
            }
            
            $return .= "<div class='uk-grid uk-grid-small'>";
            
            if($show_price == 0){
                $return .= '<div class="uk-panel-badge uk-badge uk-badge-success uk-badge-notification">'.$price.'</div>';
            }
            
            $helper = new guruHelper();
            $itemid_seo = $helper->getSeoItemid();
            $itemid_seo = @$itemid_seo["guruprograms"];
            
            if(intval($itemid_seo) > 0){
                $item_id = intval($itemid_seo);
            }
            
            $return .=     '<div class="uk-width-1-1 uk-margin-bottom '.$style_courses->courses_name.'">
                                <h3 class="uk-panel-title"><a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.$course->name.'</a></h3>
                                <ul class="uk-list gru-course-box-details">
                                    <li class="uk-display-inline-block"><i class="uk-icon-user"></i> '.$authors_urls.' </li>';

            if(trim($count_courses_text) != ""){
                $return .=     '
                                    <li class="uk-display-inline-block" title="'.$count_courses_text_tip.'" data-uk-tooltip><i class="uk-icon-graduation-cap"></i> '.$count_courses_text.'</li>';
            }

            $return .=     '
                                </ul>
                            </div>';

            if (trim($image) != "") {
                $return .= '<div class="uk-width-1-1 uk-width-medium-1-3">'.$image_left.'</div>';
            }

            $return .=     '<div class="uk-width-medium-2-3">';

                if ($read_more == "0" && $edit_read_more == "0") {
                    $helper = new guruHelper();
                    $itemid_seo = $helper->getSeoItemid();
                    $itemid_seo = @$itemid_seo["guruprograms"];
                    
                    if(intval($itemid_seo) > 0){
                        $item_id = intval($itemid_seo);
                    }
                    
                        // : Show `Read more` button depending on admin settings
                        $rt =  '<div class="readon"><a class="uk-button" style="float:'.$read_align.'" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a></div>';
                }

                $description = JHtml::_('content.prepare', $description);

                $return .=     '<div class="'.$style_courses->courses_description.' " style="text-align:'.$description_align.' '.$style_d.';"><p>'.$description.'</p>'.$rt.'</div>';
                $return .= "</div>";

            $return .= "</div>";
        }

        // : Right alignment
        elseif ($img_align == "2") {
            
            $price = "";
                
            if($config->currencypos == 0){
                $price = JText::_("GURU_CURRENCY_".$config->currency).$course->price;
            }
            else{
                $price = $course->price.JText::_("GURU_CURRENCY_".$config->currency);
            }
            
            if(trim($course->price) == ""){
                $price = JText::_("GURU_FREE");
            }
            
            $return .= "<div class='uk-grid uk-grid-small'>";

            if($show_price == 0){
                $return .= '<div class="uk-panel-badge uk-badge uk-badge-success uk-badge-notification">'.$price.'</div>';
            }
            
            $helper = new guruHelper();
            $itemid_seo = $helper->getSeoItemid();
            $itemid_seo = @$itemid_seo["guruprograms"];
            
            if(intval($itemid_seo) > 0){
                $item_id = intval($itemid_seo);
            }
            
            $return .=     '<div class="uk-width-1-1 uk-margin-bottom '.$style_courses->courses_name.'">
                                <h3 class="uk-panel-title"><a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.$course->name.'</a></h3>
                                <ul class="uk-list gru-course-box-details">
                                    <li class="uk-display-inline-block"><i class="uk-icon-user"></i> '.$authors_urls.' </li>';
            if(trim($count_courses_text) != ""){
                $return .=     '
                                    <li class="uk-display-inline-block" title="'.$count_courses_text_tip.'" data-uk-tooltip><i class="uk-icon-graduation-cap"></i> '.$count_courses_text.'</li>';
            }

            $return .=     '
                                </ul>
                            </div>';

            $return .=     '<div class="uk-width-medium-2-3">';

                if ($read_more == "0" && $edit_read_more == "0") {
                    $helper = new guruHelper();
                    $itemid_seo = $helper->getSeoItemid();
                    $itemid_seo = @$itemid_seo["guruprograms"];
                    
                    if(intval($itemid_seo) > 0){
                        $item_id = intval($itemid_seo);
                    }    
                        
                        // : Show `Read more` button depending on admin settings
                        $rt =  '<div class="readon"><a class="uk-button" style="float:'.$read_align.'" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".intval($item_id)).'">'.JText::_("GURU_READ_MORE").'</a></div>';
                }

                $description = JHtml::_('content.prepare', $description);

                $return .=     '<div class="'.$style_courses->courses_description.' " style="text-align:'.$description_align.' '.$style_d.';"><p>'.$description.'</p>'.$rt.'</div>';

                $return .= "</div>";

            if (trim($image) != "") {
                $return .= '<div class="uk-width-1-1 uk-width-medium-1-3">'.$image_right.'</div>';
            }

            $return .= "</div>";
        }

        $return .= '</div>';
    }

    // : Return mini-profile layout
    return $return;
}


if(isset($categ->groups) && trim($categ->groups) != ""){
    $user = JFactory::getUser();
    $user_groups = $user->groups;
    $acl_groups = json_decode(trim($categ->groups), true);
    $intersect = array_intersect($user_groups, $acl_groups);

    if(in_array(1, $acl_groups) || in_array(9, $acl_groups) || count($intersect) > 0){
        // Public or Guest, access to courses/categories
    }
    elseif(!isset($intersect) || count($intersect) == 0){
        $app = JFactory::getApplication();
        $app->redirect(JURI::root());
        die();
    }
}

// end course in the category generation function
    $guruHelper = new guruHelper(); 
    $detect = new Mobile_Detect;
    $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
    
    if($categ->published == 1){
        $var_inc = 0;

        if(trim($categ->image) == ""){
            $categ->image = "components/com_guru/images/thumbs/no_image.gif";
            $categ->imageName = "no_image.gif";
            $guruHelper->createThumb($categ->imageName, "components/com_guru/images", $config_category->ctg_image_size, $type);
        }

        else{
            $guruHelper->createThumb($categ->imageName, $config->imagesin."/categories", $config_category->ctg_image_size, $type);
        }
    
        if(trim($categ->image) != ""){
            if ($config_category->ctg_image_alignment == "1") {
                $categ_image = '<img border="0" alt="" class="uk-border-rounded" src="'.JURI::root().$categ->image.'" />';
            }

            elseif ($config_category->ctg_image_alignment == "2") {
                $categ_image = '<img border="0" alt="" class="uk-border-rounded" src="'.JURI::root().$categ->image.'" />';
            }

            else {
                $categ_image = '<img border="0" alt="" src="'.JURI::root().$categ->image.'" />';
                $categ_image_url = JURI::root().str_replace("thumbs/", "", $categ->image);
            }
        }

        else{ 
            $categ_image = "";
            $categ_image_url = "";
        }

        $guruModelguruPcateg = new guruModelguruPcateg();
        $no_programs = $guruModelguruPcateg->getnoprograms($categ->id); 
        $return_value = $this->categlist($categ->id);   
        
        $desc_align = $config_category->ctg_description_alignment == "0" ? "left" : "right";
        $desc = $this->cutDescription($categ->description, $config_category->ctg_description_length, $config_category->ctg_description_type, $config_category->ctg_description_mode);
            
            ?>
            <div class="gru-courses">
            <?php
                $category_layout .= 
                '<div id="g_cath_detail_top" class="cat_level_wrap cat_level_'.$var_inc.' g_sect clearfix">';
                    $category_layout .= 
                       '<div class="uk-panel uk-panel-box">';

                            if ($config_category->ctg_image_alignment == "0") { 
                                if($categ_image != ""){
                                    $category_layout .= '<div class="uk-panel-teaser uk-cover-background gru-category-teaser" style="background-image: url('.$categ_image_url.');">
                                        <figure class="uk-overlay">
                                            <figcaption class="uk-overlay-panel uk-overlay-background uk-overlay-bottom"><h3>' . $categ->name . '</h3></figcaption>
                                        </figure>
                                    </div>'; //main category image
                                }

                                $category_layout .=     '<div style="text-align:'.$desc_align.';">'.$desc.'</div>';                                             
                            }

                            elseif ($config_category->ctg_image_alignment == "1") {
                                $category_layout .= '<div class="uk-grid">';
                                if($categ_image != ""){
                                    $category_layout .= '<div class="uk-width-1-1 uk-width-medium-1-3">'.$categ_image.'</div>'; //main category image                                                           
                                }

                                $category_layout .=   '<div class="uk-width-1-1 uk-width-medium-2-3">';
                                $category_layout .=     '<h2>' . $categ->name . '</h2>';
                                $category_layout .=     '<div style="text-align:'.$desc_align.';">'.$desc.'</div>';
                                $category_layout .=   '</div>';
                                $category_layout .= '</div>';
                            }

                            elseif ($config_category->ctg_image_alignment == "2") {
                                $category_layout .= '<div class="uk-grid">';
                                $category_layout .=   '<div class="uk-width-1-1 uk-width-medium-2-3">';
                                $category_layout .=     '<h2>' . $categ->name . '</h2>';
                                $category_layout .=     '<div style="text-align:'.$desc_align.';">'.$desc.'</div>';
                                $category_layout .=   '</div>';

                                if($categ_image != ""){
                                    $category_layout .= '<div class="uk-width-1-1 uk-width-medium-1-3">'.$categ_image.'</div>'; //main category image                                                           
                                }

                                $category_layout .= '</div>';
                            }
                                                
                    $category_layout .= '
                       </div>
                    ';
        
        
            
                // start sub categories------------------------------------
                    
                    if(isset($subcateg) && count($subcateg) > 0){
                        $categs_config = json_decode($config->ctgspage);
                        $cols = $categs_config->ctgscols;
                        $style_categs = json_decode($config->st_ctgspage);
                        $layout = $categs_config->ctgslayout;
                        
                        if($deviceType =="phone"){
                            $style = 'style="width:'.(float)(100/1).'%"';
                        }
                        else{
                            $style = 'style="width:'.(float)(100/$cols).'%"';
                            if(count($subcateg) == 1){
                                $span = "12";
                            }
                            else{
                                $span=12/$cols;
                            }
                        }
                        
                        if($layout == "0"){
                            //generateTreeCategoriesList(0, 2);
                        }
                        else{
                            $categs_array = array();    
                            for($i=0; $i<count($subcateg); $i++){
                                $added_element = generateCategsCellsC($categs_config, $style_categs, $subcateg[$i], $config);
                                
                                if(trim($added_element) != ""){
                                    $categs_array[] = $added_element;
                                }   
                            }
                            
                            $i = 0;
                            $var_inc ++;
                            $category_layout .= '<div class="cat_level_wrap cat_level_'.$var_inc.'" >
                                                        <div class="g_sect">';

                            if(count($categs_array) == "1"){
                                $cols = 1;
                                $category_layout .= '<div class="'.$config_category_style->ctg_name.' page_title"><h3>'.JText::_("GURU_PROGRAM_SUBCATEGORIES").':</h3></div>';
                            }
                            elseif(count($categs_array) != "0"){
                                $category_layout .= '<div class="'.$config_category_style->ctg_name.' page_title"><h3>'.JText::_("GURU_PROGRAM_SUBCATEGORIES").':</h3></div>';
                            }
                            
                            while(isset($categs_array[$i])){
                                $row = "";
                                $row .= '<div class="cont_detail_guru uk-grid" >';
        
                                $j = 0;
                                while($j < $cols){
                                    if(!isset($categs_array[$i])){
                                        $j = $cols;
                                        $i++;
                                    }
                                    elseif(isset($categs_array[$i]) && trim($categs_array[$i]) != ""){  
                                        $row .= '<div class="course_cell_guru g_cell uk-width-large-1-'.$cols.' uk-width-medium-1-'.$cols.'">
                                                    <div><div>'.$categs_array[$i]."</div></div>
                                                 </div>";
                                        $j++;
                                    }
                                    $i++;
                                   
                                }
                                $row .= '</div>';
                    
                                $category_layout .= $row;
                            }
                            $category_layout .='</div>
                                                    </div>';        
                        }
                }               
                // end sub categories------------------------------------
                
                $courses_config = json_decode($config->psgspage);
                $style_courses = json_decode($config->st_psgspage);
                $layout = $courses_config->courseslayout;
                $cols = $courses_config->coursescols;   
                
                if($deviceType =="phone"){
                    $style = 'style="width:'.(float)(100/1).'%"';
                }
                else{
                    $style = 'style="width:'.(float)(100/$cols).'%"';
                    if(is_array($cols)){
                        if(count($cols) <= 1){
                             $span = "12";
                        }
                        else{
                            $span=12/$cols;
                        }
                    }
                }

                if($layout == "0"){
                    if(count($programs) > 0){
                        $cid = JFactory::getApplication()->input->get("cid", "0");
                        $Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
                        $form_url = "index.php?option=com_guru&view=guruPcategs&task=view&cid=".$cid."&Itemid=".$Itemid;
                    
                        $category_layout .= '<form name="adminForm" id="adminForm" action="'.$form_url.'" method="post">';

                        $category_layout .= "<div class='uk-panel uk-panel-box uk-panel-box-secondary uk-margin-top'><ul class='guru-list'>";

                        foreach($programs as $order){
                            if(isset($order->name)){
                                $course_url = JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$order->id);
                                $category_layout .= '<li><a href="'.$course_url.'">'.$order->name.'</a></li>';
                            } 
                        }
                        $category_layout .= "</ul></div>";

                        $category_layout .= '<div class="uk-grid"><div class="uk-width-large-1-1">';
                        $pages = $this->pagination->getPagesLinks();
                        include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
                        $helper = new guruHelper();
                        $pages = $helper->transformPagination($pages);

                        $category_layout .= '<div style="margin:10px 0px;">'.$pages.'</div></div></div>';

                        $category_layout .= '</form>';
                    }
                }
                else{
                    $courses_array = array();   
                    $generate = new GenerateDisplay();
                    
                    for($i=0; $i<count($programs); $i++){   
                        $courses_array[] = generateCoursesCellsB($courses_config, $style_courses, $programs[$i], $config);  
                    }
                    
                    $i = 0;
                    
                    $cid = JFactory::getApplication()->input->get("cid", "0");
                    $Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
                    $guru_search = JFactory::getApplication()->input->get("guru_search", "");
                    
                    $form_url = "index.php?option=com_guru&view=guruPcategs&task=view&cid=".$cid."&Itemid=".$Itemid;
                    
                    $category_layout .= '<form name="adminForm" id="adminForm" action="'.$form_url.'" method="post">';
                    
                    $category_layout .= '<div class="cat_list_wrap g_sect clearfix">';
                    if(count($courses_array) > 0){
                        $category_layout .= '<div class="uk-grid">
                                                <div class="uk-width-1-1">
                                                    <h4 class="gru-page-subtitle uk-margin-top">'.JText::_("GURU_COURSES_IN_CATEGORY").':</h4>
                                                </div>
                                            </div>';
                    }
                    
                    $limit_pag = $this->pagination->getLimitBox();
                    $limit_pag = preg_replace('/class="(.*)"/msU', 'class="uk-margin-small-top uk-form-width-mini"', $limit_pag);
                    
                    $category_layout .= '<div class="gru-page-filters uk-margin">';
                    $category_layout .=     '<div class="gru-filter-item"><input type="text" placeholder="'.JText::_("GURU_SEARCH").'" name="guru_search" value="'.trim($guru_search).'" class="form-control uk-margin-remove uk-form-width-small" /> <input type="submit" name="search_button" value="'.JText::_("GURU_SEARCH").'" class="uk-button uk-button-primary" style="margin:0px;"></div>';
                    
                    if(count($courses_array) > 5){
                        $category_layout .= '<div class="gru-filter-item item-right">'.$limit_pag.'</div>';
                    }
                    
                    $category_layout .= '</div><div class="clearfix"></div>';
                    
                    $category_layout .= '<div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:\'.uk-panel\'}">';
                    while(isset($courses_array[$i])){
                        $row = "";
                        for($j=0; $j<$cols; $j++){
                            if(count($courses_array) == 1){             
                                if(isset($courses_array[$i])){
                                    $row .= '<div class="uk-width-large-1-1"><div>'.$courses_array[$i++]."</div></div>";
                                }
                            }
                            else{
                                if(isset($courses_array[$i])){
                                        $row .= '<div class="uk-width-large-1-'.$cols.' uk-width-medium-1-2"><div>'.$courses_array[$i++]."</div></div>";
                                }
                            }
                        }
                        $category_layout .= $row;
                    }
                    
                    $category_layout .= '<div class="uk-grid"><div class="uk-width-large-1-1">';
                    $pages = $this->pagination->getPagesLinks();
                    include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
                    $helper = new guruHelper();
                    $pages = $helper->transformPagination($pages);
                    $category_layout .= $pages.'</div></div>';
                    
                    $category_layout .= '</form>';
                        
                    $category_layout .= '</div></div>';
                }
                
                $category_layout .= '</div>';
            
                echo $category_layout;
            ?>
    </div>
    

    <?php   
    }
    else{
        echo JText::_("GURU_NO_CATH");//display the message from GURU_NO_CATH when the category is unpublised 
    }   
?>
<script type="text/javascript" src="components/com_guru/js/jquery.height_equal.js"></script>
<script>
    window.onload = equalHeight('course_cell_guru');
</script>