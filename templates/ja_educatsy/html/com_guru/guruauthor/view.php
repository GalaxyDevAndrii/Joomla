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

$doc= JFactory::getDocument();
$doc->addStyleSheet(JURI::root()."components/com_guru/css/tabs_css.css");
$author = $this->author;
$config = $this->config;
$guruHelper = new guruHelper();

$style_author = json_decode($config->st_authorpage);
$config_author = json_decode($config->authorpage);
$author->imageName = $author->images;

if(trim($author->images) != ""){
    $array = explode("/", $author->images);
    if(isset($array) && count($array) > 0){
        $author->imageName = $array[count($array)-1];
    }
}

$type = $config_author->author_image_size_type == "0" ? "w" : "h";
$return = "";
$wrap = $config_author->author_wrap_image; //0-yes, 1-no
$img_align = $config_author->author_image_alignment; //0-left, 1-right
$bio_align = $config_author->author_description_alignment == "0" ? "left" : "right";
$item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");

if(trim($author->images) == ""){
    $author->images = "/components/com_guru/images/thumbs/no_image.gif";
    $author->imageName = "no_image.gif";
    $guruHelper->createThumb($author->imageName, "components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."images", $config_author->author_image_size, $type);
}
else{
    $guruHelper->createThumb($author->imageName, $config->imagesin."/authors", $config_author->author_image_size, $type);
}

$image = "";

if(trim($author->images) != ""){
    $site_home = JURI::root();

    if (substr($site_home, -1) == '/'){
        $site_home = substr($site_home, 0, -1);
    }

	if($img_align == 0){
    	$image = '<img class="author-image thumbnail" src="'.$site_home.$author->images.'" />';
	}
	else{
		$image = '<img class="author-image thumbnail" src="'.$site_home.$author->images.'" />';
	}
}

$bio = $author->full_bio;

// start the calculation for image, description and top links
// count layout

if($wrap == "1"){//no wrap
	$class_display = "display:table-cell;";
    $links = createAuthorLinksboots($author);

    if($img_align == "0"){// left
        $return .= "<div class = 'weblinks'>";
        $return .= 		'<div>';
        $return .= 				$links;
        $return .= 		"</div>";
        $return .= "</div>";
		
        $return .= "<div>";
        $return .= 		'<div>';

        if(trim($image) != ""){
            $return .= 			'<div class="'.$style_author->author_image.'">'.$image.'</div>';
        }

        $return .= 		"</div>";
        $return .= 		'<div>';
        $return .= 			'<div class="'.$style_author->author_description.'" style="text-align:'.$bio_align.'; '.$class_display.'">'.$bio.'</div>';
        $return .= 		"</div>";
        $return .= "</div>";
    }
    elseif($img_align == "1"){// right
		$links = createAuthorLinksboots($author);
        $return .= "<div class = 'weblinks'>";
        $return .= 		'<div>';
        $return .= 			$links;
        $return .= 		"</div>";
        $return .= "</div>";

        $return .= "<div>";
        $return .= 		'<div>';
		
		if(trim($image) != ""){
            $return .= 			'<div class="'.$style_author->author_image.'">'.$image.'</div>';
        }

        $return .= 			'<div class="'.$style_author->author_description.'" style="text-align:'.$bio_align.'; '.$class_display.' ">'.$bio.'</div>';
        $return .= 		"</div>";
        $return .= 		'<div>';
        $return .= 		"</div>";
        $return .= "</div>";
    }
}
elseif($wrap == "0"){//wrap
    $links = createAuthorLinksboots($author);

    if($img_align == "0"){// left
        $return .= "<div class = 'weblinks'>";
        $return .= 		'<div>';
        $return .= 			$links;
        $return .= 		"</div>";
        $return .= "</div>";
        $return .= "<div>";
        $return .= 		'<div>';

        // if(trim($image) != ""){
        //     $return .= 			'<div class="'.$style_author->author_image.'">'.$image.'</div>';
        // }

        $return .= 			'<div class="'.$style_author->author_description.'" style="text-align:'.$bio_align.';">'.$bio.'</div>';
        $return .= 		"</div>";
        $return .= "</div>";
    }
    elseif($img_align == "1"){// right
        $return .= "<div class = 'weblinks'>";
        $return .= 		'<div>';
        $return .= 			$links;
        $return .= 		"</div>";
        $return .= "</div>";

        $return .= "<div>";
        $return .= 		'<div>';

        // if(trim($image) != ""){
        //     $return .= 			'<div class="'.$style_author->author_image.'" style="float:right;">'.$image.'</div>';
        // }

        $return .= 			'<div class="'.$style_author->author_description.'" style="text-align:'.$bio_align.';">'.$bio.'</div>';
        $return .= 		"</div>";
        $return .= "</div>";
    }
}

$teacher_table = $return;

// end the calculation for image, description and top links
//start teacher links function (email, website, blog, twitter, facebook)

function createAuthorLinksboots($author){
    $table  = '<div class="teacher_links clearfix g_toolbar">';
	
    if($author->show_email==1 && trim($author->email)!=""){
        $table .= '<div class="teacher_links-item">
						<span class="teacher_email_guru">
					   		<a href="mailto:'.$author->email.'"> <i class="fa fa-envelope"></i> '.JText::_("GURU_EMAIL").'</a>
				   		</span>
					</div>';
    }

    if($author->show_website==1 && trim($author->website)!="http://" && trim($author->website)!=""){
        $table .= '<div class="teacher_links-item">
						<span class="guru_teacher_site">
					   		<a href="'.$author->website.'" target="_blank"> <i class="fa fa-globe"></i> '.JText::_("GURU_WEBSITE").'</a>
				  		</span>
					</div>';
    }

    if($author->show_blog==1 && trim($author->blog)!="http://" && trim($author->blog)!=""){
        $table .= '<div class="teacher_links-item">
						<span class="guru_teacher_blog">
							<a href="'.$author->blog.'" target="_blank"> <i class="fa fa-pencil"></i> '.JText::_("GURU_BLOG").'</a>
				  		</span>
					</div>';
    }

    if($author->show_twitter==1 && trim($author->twitter)!=""){
        $table .= '<div class="teacher_links-item">
						<span class="guru_teacher_twitter">
							<a href="http://www.twitter.com/'.$author->twitter.'" target="_blank"> <i class="fa fa-twitter"></i> '.JText::_("GURU_TWITTER").'</a>
				  		</span>
					</div>';
    }

    if($author->show_facebook==1 && trim($author->facebook)!="http://" && trim($author->facebook)!=""){
        $table .= '<div class="teacher_links-item">
						<span class="guru_teacher_facebook">
					   		<a href="'.$author->facebook.'" target="_blank"> <i class="fa fa-facebook"></i> '.JText::_("GURU_FACEBOOK").'</a>
				  		</span>
					</div>';
    }
    $table .= '</div>';
    return $table;
} 

//end teacher links function (email, website, blog, twitter, facebook)
// start the display for image, description and top links (created above in $teacher_table = $return)
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div class="gru-authorlayout">
    <div class="gru-author-wrap">
        <div class="gru-author-avatar">
            <?php echo $image ;?>
        </div>

        <div class="gru-author-info">
            <h2 class="gru-page-title"><?php echo $author->name; ?></h2>
            <?php if($author->author_title) :?>
                <div class=""><?php echo $author->author_title; ?></div>
            <?php endif ;?>
            <?php 
                $row = "";
            $row .= '<div class="teacher_row_guru uk-clearfix wk-clearfix" >';
            $row .=     '<div class="teacher_cell_guru">'.$teacher_table."</div>";
            $row .= '</div>';
        
            echo $row;

            ;?>
        </div>
    </div>
	
    <?php
		$doc->setTitle($author->name." ".JText::_("GURU_TEACHER_PROFILE"));
		@$teacher_bio = substr($teacher_bio, 0, 200);
		$doc->setMetaData('description', $teacher_bio);
		$itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		
		if(isset($author->courses) && !empty($author->courses)){
	?>
    		<h2 class="teacher_courses_heading_guru"><?php echo JText::_("GURU_TAB_AUTHOR_COURSES"); ?></h2>
            <table class="uk-table uk-table-striped wk-table wk-table-striped">
                <tr>
                    <th><?php echo JText::_("GURU_TAB_AUTHOR_COURSES_NAME"); ?></th>
                    <th class="uk-text-center wk-text-center"><?php echo JText::_("GURU_TAB_AUTHOR_COURSES_LEVEL"); ?></th>
                    <th class="hidden-phone uk-text-center wk-text-center"><?php echo JText::_("GURU_TAB_AUTHOR_COURSES_RELEASE");?></th>
                </tr>
                <?php
                    for($i=0; $i<count($author->courses); $i++){
                        $class = "odd";
                        if(@$k%2 != 0){
                            $class = "even";
                        }

                        $helper = new guruHelper();
                        $itemid_menu = $helper->getCourseMenuItem(intval($author->courses[$i]->id));
                        $itemid_course = $itemid;

                        if(intval($itemid_menu) > 0){
                            $itemid_course = intval($itemid_menu);
                        }
                ?>
                    <tr class="<?php echo $class; ?>">			
                        <td>
                                
                                <a href="<?php echo JRoute::_('index.php?option=com_guru&view=guruPrograms&layout=view&cid='.$author->courses[$i]->id."-".$author->courses[$i]->alias."&Itemid=".$itemid_course); ?>" >
    
                        <?php echo $author->courses[$i]->name; ?>
    
                    </a>
                        </td>
                        <td class="uk-text-center wk-text-center">
                            <?php
    
                    switch($author->courses[$i]->level){
    
                        case "0":
    
                            $img="beginner_level.png";
    
                            break;
    
                        case "1":
    
                            $img="intermediate_level.png";
    
                            break;
    
                        case "2":
    
                            $img="advanced_level.png";
    
                            break;
    
                    }
    
                    ?>
    
                    <img src="<?php echo JURI::root()."components/com_guru/images/".$img; ?>" />
                        </td>
                        
                        <td class="hidden-phone uk-text-center wk-text-center">
                        <?php
    
                    $int_date 	 = strtotime($author->courses[$i]->startpublish);
    
                    $date 		 = date($config->datetype,$int_date);
    
                    echo $date;
    
                    ?>
                        </td>
                    </tr>
                <?php
                    @$k++;
                    }
                ?>
        </table>
    <?php
		}
	?>
</div>