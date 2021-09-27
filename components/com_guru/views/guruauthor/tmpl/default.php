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

// get connection....
$doc = JFactory::getDocument();
require_once(JPATH_BASE . "/components/com_guru/helpers/Mobile_Detect.php");
require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

$guruHelper = new guruHelper();
$rmore = "";

$authors = $this->authors;
$config  = $this->config;
$style_authors = json_decode($config->st_authorspage);
$config_authors = json_decode($config->authorspage);
$cols = $config_authors->authorscols;

$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

if($deviceType =="phone"){
    $cols = 1;
}

$layout = $config_authors->authorslayout;

$doc->setTitle(JText::_("GURU_AUTHORS_LIST"));

//---start cutBio function to dispaly teacher description by admin settings---

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

//---end cutBio function to dispaly teacher description by admin settings---
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div id="teacherList" class="gru-listofteachers">
    <h2 class="gru-page-title">
        <?php echo JText::_('GURU_AUTHORS_LIST'); ?>
    </h2>

    <?php
    if($layout == "1"){
        $authors_array = array();
       
	    for($i=0; $i<count($authors); $i++){
            $author	=$authors[$i];
            $type = $config_authors->authors_image_size_type == "0" ? "w" : "h";
            $return = "";
            $layout = $config_authors->authorslayout;
            $wrap = $config_authors->authors_wrap_image; //0-yes, 1-no
            $img_align = $config_authors->authors_image_alignment; //0-left, 1-right
            $read_more = $config_authors->authors_read_more; //0-yes 1-no
            $read_align = $config_authors->authors_read_more_align == "0" ? "left" : "right";
            $bio_align = $config_authors->authors_description_alignment == "0" ? "left" : "right";
            $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");
			
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruprograms"];
			
			if(intval($itemid_seo) > 0){
				$item_id = intval($itemid_seo);
			}
			
			$nr_courses = $this->getAuthorNrCourses($author);
            $detect = new Mobile_Detect;
            $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

			$class_display = "display:table-cell;";
            
			if($layout == "1"){
                //mini profile
                if(trim($author->images) == ""){
                    $author->images = "/components/com_guru/images/thumbs/no_image.gif";
                    $author->imageName = "no_image.gif";
                    $guruHelper->createThumb($author->imageName, "components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."images", $config_authors->authors_image_size, $type);
                }
                else{
                    $guruHelper->createThumb($author->imageName, $config->imagesin."/authors", $config_authors->authors_image_size, $type);
                }
                
                $image = "";

                $itemid_menu = $helper->getTeacherMenuItem(intval($author->id));

                if(intval($itemid_menu) > 0){
                    $item_id = intval($itemid_menu);
                }
				
                if(trim($author->images) != ""){
                    $site_home = JURI::root();

                    if (substr($site_home, -1) == '/'){
                        $site_home = substr($site_home, 0, -1);
                    }

                    $image = '<img src="'.$site_home.$author->images.'" />';

                    if($img_align == 0){//left
						$image = '<a class="thumbnail pull-left" href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.$image.'</a>';
					}
					else{
						$image = '<a class="thumbnail pull-right" href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.$image.'</a>';
					}
                    
                }
				
                $bio = cutBio($author->full_bio, $config_authors->authors_description_length, $config_authors->authors_description_type, $config_authors->authors_description_mode);

                // start the layout---
                // load contents: name, image, description (not responsive)

				if($wrap == "1"){//no wrap
                    if($img_align == "0"){// left
                        $return .= "<div class='guru-teacher-box uk-panel uk-panel-box wk-panel wk-panel-box'>";
                        $return .= 		'<div class="uk-clearfix wk-clearfix">';
                        $return .= 			'<div class="'.$style_authors->authors_name.'">
												<a href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'"><h4>'.$author->name.'</h4></a>
											</div>';
                        if(trim($image) != ""){
                            $return .= 			'<div class="'.$style_authors->authors_image.'">'.$image.'</div>';
                        }
						if($read_more == "0"){
                            $rmore = '<a style="text-align:'.$read_align.'" class="uk-button uk-button-primary wk-button wk-button-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';
                        }
                        $return .= 			'<div class="'.$style_authors->authors_description.'" style="text-align:'.$bio_align.'; '.$class_display.'"><p>'.$bio.'</p>'.$rmore.'</div>';
                        $return .=          "<div class='guru-teacher-box-footer'><i class='uk-icon-file-text-o wk-icon-file-text-o'></i> ".JText::_("GURU_PROGRAM_PROGRAMS").": ".$nr_courses."</div>";
                        $return .= 		"</div>";
                        $return .= "</div>";
                    }elseif($img_align == "1"){// right
                        $return .= "<div class='guru-teacher-box uk-panel uk-panel-box wk-panel wk-panel-box'>";
                        $return .= 		'<div class="uk-clearfix wk-clearfix">';
                        $return .= 			'<div class="'.$style_authors->authors_name.'">
												<a href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'"><h4>'.$author->name.'</h4></a>
											</div>';
                        if(trim($image) != ""){
                            $return .= 			'<div class="'.$style_authors->authors_image.' float:right;">'.$image.'</div>';
                        }
						 if($read_more == "0"){
                            $rmore = '<a style="text-align:'.$read_align.'" class="uk-button uk-button-primary wk-button wk-button-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';
                        }
                        $return .= 			'<div class="'.$style_authors->authors_description.'" style="text-align:'.$bio_align.'; '.$class_display.'"><p>'.$bio.'</p>'.$rmore.'</div>';
                        $return .=          "<div class='guru-teacher-box-footer'><i class='uk-icon-file-text-o wk-icon-file-text-o'></i> ".JText::_("GURU_PROGRAM_PROGRAMS").": ".$nr_courses."</div>";
                       
                        $return .= 		"</div>";
                        $return .= "</div>";
                    }
                }
                elseif($wrap == "0"){//wrap
                    if($img_align == "0"){// left
                        $return .= "<div class='guru-teacher-box uk-panel uk-panel-box wk-panel wk-panel-box'>";
                        $return .= 		'<div class="uk-clearfix wk-clearfix">';
                        $return .= 			'<div class="'.$style_authors->authors_name.'">
												<a href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'"><h4>'.$author->name.'</h4></a>
											</div>';
                        if(trim($image) != ""){
                            $return .= 			'<div class="'.$style_authors->authors_image.'">'.$image.'</div>';
                        }
						 if($read_more == "0"){
                            $rmore = '<a style="text-align:'.$read_align.'" class="uk-button uk-button-primary wk-button wk-button-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';
                        }
                        $return .= 			'<div class="'.$style_authors->authors_description.'" style="text-align:'.$bio_align.';"><p>'.$bio.'</p>'.$rmore.'</div>';
                        $return .=          "<div class='guru-teacher-box-footer'><i class='uk-icon-file-text-o wk-icon-file-text-o'></i> ".JText::_("GURU_PROGRAM_PROGRAMS").": ".$nr_courses."</div>";
                       
                        $return .= 		"</div>";
                        $return .= "</div>";
                    }
                    elseif($img_align == "1"){// right
                        $return .= "<div class='guru-teacher-box uk-panel uk-panel-box wk-panel wk-panel-box'>";
                        $return .= 		'<div class="uk-clearfix wk-clearfix">';
                        $return .= 			'<div class="'.$style_authors->authors_name.'">
												<a href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'"><h4>'.$author->name.'</h4></a>
											 </div>';
                        if(trim($image) != ""){
                            $return .= 			'<div class="'.$style_authors->authors_image.' float:right;">'.$image.'</div>';
                        }
						if($read_more == "0"){
                            $rmore = '<a class="uk-button uk-button-primary wk-button wk-button-primary" href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';
                        }
                        $return .= 			'<div class="'.$style_authors->authors_description.'" style="text-align:'.$bio_align.';"><p>'.$bio.'</p>'.$rmore.'</div>';
                        $return .=          "<div class='guru-teacher-box-footer'><i class='uk-icon-file-text-o wk-icon-file-text-o'></i> ".JText::_("GURU_PROGRAM_PROGRAMS").": ".$nr_courses."</div>";
                        $return .= 		"</div>";
                        $return .= "</div>";
                    }
                }
            }//if mini profile
            $authors_array[] = $return ;
        }
        //end the calculation of teacher name, image, description---
        //start te display of teacher main div that contain $authors_array content calculated above
        $i = 0;

        // counting the row (responsive)
        // contains: row and collumns
        while(isset($authors_array[$i])){
            $row = "";
            $row .= '<div class="uk-grid uk-grid-match wk-grid wk-grid-match" data-uk-grid-match=\'{target:".uk-panel"}\'>';
            if(count($authors_array) == 1) {
                $span = "uk-width-large-1-1 wk-width-large-1-1";
            }
            else{
                $span = "uk-width-large-1-".$cols." wk-width-large-1-".$cols;
            }

            // counting the collumn
            for($j=0; $j<$cols; $j++){
                if(isset($authors_array[$i])){
                    $row .= '<div class="'.$span.' uk-width-small-1-1 uk-width-medium-1-2 wk-width-small-1-1 wk-width-medium-1-2">'.$authors_array[$i++]."</div>";
                }
            }
            $row .= '</div>';
            echo $row;
        }
    }
    else{
        $result = "<ul>";
        for($i=0; $i<count($authors); $i++){
            $result .= "<li>".'<a href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$authors[$i]->id."-".JFilterOutput::stringURLSafe($authors[$i]->name)).'">'.$authors[$i]->name.'</a></li>';
        }
        $result .= "</ul>";
        echo $result;
    }
    //end te display of teacher main div that contain $authors_array content calculated above
    ?>
</div>
