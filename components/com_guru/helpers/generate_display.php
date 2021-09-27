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

require_once(JPATH_BASE . "/components/com_guru/helpers/Mobile_Detect.php");

require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

class GenerateDisplay{

    function generateAuthorsCells($config_authors, $style_authors, $author, $config){

        $type = $config_authors->authors_image_size_type == "0" ? "w" : "h";

        $return = "";

        $layout = $config_authors->authorslayout;

        $wrap = $config_authors->authors_wrap_image; //0-yes, 1-no

        $img_align = $config_authors->authors_image_alignment; //0-left, 1-right

        $read_more = $config_authors->authors_read_more; //0-yes 1-no

        $read_align = $config_authors->authors_read_more_align == "0" ? "left" : "right";

        $bio_align = $config_authors->authors_description_alignment == "0" ? "left" : "right";

        $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");



        $detect = new Mobile_Detect;

        $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

        if($deviceType =="phone"){

            $widthboostrapimg = 'width="40%"';

            $break = "<br/>";

        }

        else{

            $widthboostrapimg = 'width="5%"';

            $break = "";

        }



        if($layout == "1"){//mini profile

            if(trim($author->images) == ""){

                $author->images = "components/com_guru/images/thumbs/no_image.gif";

                $author->imageName = "no_image.gif";

                guruHelper::createThumb($author->imageName, "components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."images", $config_authors->authors_image_size, $type);

            }

            else{

                guruHelper::createThumb($author->imageName, $config->imagesin."/authors", $config_authors->authors_image_size, $type);

            }

            $image = "";

            if(trim($author->images) != ""){
				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["guruauthor"];
				
				if(intval($itemid_seo) > 0){
					$item_id = intval($itemid_seo);
				}
                else{
                    $itemid_menu = $helper->getTeacherMenuItem(intval($author->id));

                    if(intval($itemid_menu) > 0){
                        $item_id = intval($itemid_menu);
                    }
                }
				
                $image = '<img  src="'.JURI::root().$author->images.'" />';

                $image = '<a class="thumbnail" href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.$image.'</a>';

            }

            $bio = $this->cutBio($author->full_bio, $config_authors->authors_description_length, $config_authors->authors_description_type);



            $return .= '<div>';

            if($wrap == "1"){//no wrap
				$class_display = "display:table-cell;";

                if($img_align == "0"){// left

                    $return .= "<div class='media-body'>";

                    if(trim($image) != ""){

                        $return .= 			'<div class="pull-left"><ul class="thumbnails"><li>'.$image.'</li></ul></div>';

                    }
					
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruauthor"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}
                    else{
                        $itemid_menu = $helper->getTeacherMenuItem(intval($author->id));

                        if(intval($itemid_menu) > 0){
                            $item_id = intval($itemid_menu);
                        }
                    }
					
                    $return .= 			'<div class=" media-heading '.$style_authors->authors_name.'">

											<a href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.$author->name.'</a>

										</div>';

                    if($read_more == "0"){$rm ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';}

                    $return .= 			'<div class="media '.$style_authors->authors_description.'" style="text-align:'.$bio_align.'; '.$class_display.'">'.$bio.$rm.'</div>';

                    $return .= "</div>";
                }

                elseif($img_align == "1"){// right
					$image = '<img class="media-object '.$style_authors->authors_image.'" src="'.JURI::root().$author->images.'" />';
					
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruauthor"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}
                    else{
                        $itemid_menu = $helper->getTeacherMenuItem(intval($author->id));

                        if(intval($itemid_menu) > 0){
                            $item_id = intval($itemid_menu);
                        }
                    }
					
                    $image = '<a href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.$image.'</a>';



                    $return .= "<div class='media-body' style='margin-left:10px;'>";

                    $return .= 			'<div class=" media-heading '.$style_authors->authors_name.'">

											<a href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.$author->name.'</a></div>';

                    if($read_more == "0"){$rm ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';}

                    $return .= 			'<div class=" media '.$style_authors->authors_description.'" style="text-align:'.$bio_align.'; '.$class_display.'">'.$bio.$rm.'</div>';

                    if(trim($image) != ""){

                        $return .= 			'<div class="pull-right" style="border:1px solid #DDDDDD">'.$image.'</div>';

                    }

                    $return .= 		"</div>";

                }

            }

            elseif($wrap == "0"){//wrap

                if($img_align == "0"){// left
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruauthor"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}
                    else{
                        $itemid_menu = $helper->getTeacherMenuItem(intval($author->id));

                        if(intval($itemid_menu) > 0){
                            $item_id = intval($itemid_menu);
                        }
                    }
					
                    $return .= "<div>";

                    $return .= 		'<div>';

                    $return .= 			'<div class="'.$style_authors->authors_name.'">

											<a href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.$author->name.'</a>

										</div>';

                    if(trim($image) != ""){

                        $return .= 			'<div class="'.$style_authors->authors_image.'">'.$image.'</div>';

                    }

                    $return .= 			'<div class="'.$style_authors->authors_description.'" style="text-align:'.$bio_align.';">'.$bio.'</div>';

                    if($read_more == "0"){
						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["guruauthor"];
						
						if(intval($itemid_seo) > 0){
							$item_id = intval($itemid_seo);
						}
                        else{
                            $itemid_menu = $helper->getTeacherMenuItem(intval($author->id));

                            if(intval($itemid_menu) > 0){
                                $item_id = intval($itemid_menu);
                            }
                        }
						
                        $return .= '<div class="'.$style_authors->authors_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                    }

                    $return .= 		"</div>";

                    $return .= "</div>";

                }

                elseif($img_align == "1"){// right
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruauthor"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}
                    else{
                        $itemid_menu = $helper->getTeacherMenuItem(intval($author->id));

                        if(intval($itemid_menu) > 0){
                            $item_id = intval($itemid_menu);
                        }
                    }
					
                    $return .= "<tr>";

                    $return .= 		'<td style="vertical-align:top;">';

                    if(trim($image) != ""){

                        $return .= 			'<div class="'.$style_authors->authors_image.'" style="float:right;">'.$image.'</div>';

                    }

                    $return .= 			'<div class="'.$style_authors->authors_name.'">

											<a href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.$author->name.'</a>

										</div>';

                    $return .= 			'<div class="'.$style_authors->authors_description.'" style="text-align:'.$bio_align.';">'.$bio.'</div>';

                    if($read_more == "0"){

                        $return .= '<div class="'.$style_authors->authors_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.$author->id."-".JFilterOutput::stringURLSafe($author->name)."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                    }

                    $return .= 		"</td>";

                    $return .= "</tr>";

                }

            }



            $return .= '</div>';

        }//if mini profile



        return $return;

    }//end function



    function generateAuthorCell($config_author, $style_author, $author, $config){
		$class_display = "display:table-cell;";
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

            $author->images = "components/com_guru/images/thumbs/no_image.gif";

            $author->imageName = "no_image.gif";

            guruHelper::createThumb($author->imageName, "components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."images", $config_author->author_image_size, $type);

        }

        else{

            guruHelper::createThumb($author->imageName, $config->imagesin."/authors", $config_author->author_image_size, $type);

        }

        $image = "";

        if(trim($author->images) != ""){

            $image = '<img class=" thumbnail '.$style_author->author_image.'" src="'.JURI::root().$author->images.'" />';

        }

        $bio = $author->full_bio;



        $return .= '<table cellpadding="0" cellspacing="0">';

        if($wrap == "1"){//no wrap

            $links = $this->createAuthorLinksboots($author);



            if($img_align == "0"){// left

                $return .= "<tr>";

                $return .= 		'<td style="vertical-align:top;" colspan="2">';

                $return .= 			'<div class="'.$style_author->author_name.'"><h1>'.$author->name.'</h1></div>';

                $return .= 				$links;

                $return .= 		"</td>";

                $return .= "</tr>";

                $return .= "<tr>";

                $return .= 		'<td style="vertical-align:top;">';

                if(trim($image) != ""){

                    $return .= 			'<div class="'.$style_author->author_image.'">'.$image.'</div>';

                }

                $return .= 		"</td>";

                $return .= 		'<td style="vertical-align:top;">';

                $return .= 			'<div class="'.$style_author->author_description.'" style="text-align:'.$bio_align.'; '.$class_display.'">'.$bio.'</div>';

                $return .= 		"</td>";

                $return .= "</tr>";

            }

            elseif($img_align == "1"){// right

                $return .= "<tr>";

                $return .= 		'<td style="vertical-align:top;" colspan="2">';

                $return .= 			'<div class="'.$style_author->author_name.'"><h1>'.$author->name.'</h1></div>';

                $return .= 			$this->createAuthorLinks($author);

                $return .= 		"</td>";

                $return .= "</tr>";

                $return .= "<tr>";

                $return .= 		'<td style="vertical-align:top;">';

                $return .= 			'<div class="'.$style_author->author_description.'" style="text-align:'.$bio_align.'; '.$class_display.'">'.$bio.'</div>';

                $return .= 		"</td>";

                $return .= 		'<td style="vertical-align:top;">';

                if(trim($image) != ""){

                    $return .= 			'<div class="'.$style_author->author_image.'">'.$image.'</div>';

                }

                $return .= 		"</td>";

                $return .= "</tr>";

            }

        }

        elseif($wrap == "0"){//wrap



            $links = $this->createAuthorLinksboots($author);



            if($img_align == "0"){// left

                $return .= "<tr>";

                $return .= 		'<td style="vertical-align:top;" colspan="2">';

                $return .= 			'<div class="'.$style_author->author_name.'"><h1>'.$author->name.'</h1></div>';

                $return .= 			$links;

                $return .= 		"</td>";

                $return .= "</tr>";

                $return .= "<tr>";

                $return .= 		'<td style="vertical-align:top;">';

                if(trim($image) != ""){

                    $return .= 			'<div class="'.$style_author->author_image.'">'.$image.'</div>';

                }

                $return .= 			'<div class="'.$style_author->author_description.'" style="text-align:'.$bio_align.';">'.$bio.'</div>';

                $return .= 		"</td>";

                $return .= "</tr>";

            }

            elseif($img_align == "1"){// right

                $return .= "<tr>";

                $return .= 		'<td style="vertical-align:top;" colspan="2">';

                $return .= 			'<div class="'.$style_author->author_name.'"><h1>'.$author->name.'</h1></div>';

                $return .= 			$this->createAuthorLinks($author);

                $return .= 		"</td>";

                $return .= "</tr>";

                $return .= "<tr>";

                $return .= 		'<td style="vertical-align:top;">';

                if(trim($image) != ""){

                    $return .= 			'<div class="'.$style_author->author_image.'" style="float:right;">'.$image.'</div>';

                }

                $return .= 			'<div class="'.$style_author->author_description.'" style="text-align:'.$bio_align.';">'.$bio.'</div>';

                $return .= 		"</td>";

                $return .= "</tr>";

            }

        }



        $return .= '</table>';



        return $return;

    }//end function





    function generateCoursesCellsB($config_courses, $style_courses, $course, $config){

        $detect = new Mobile_Detect;

        $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

        $type = $config_courses->courses_image_size_type == "0" ? "w" : "h";

        $return = "";

        $layout = $config_courses->courseslayout;

        $wrap = $config_courses->courses_wrap_image; //0-yes, 1-no

        $img_align = $config_courses->courses_image_alignment; //0-left, 1-right

        $read_more = $config_courses->courses_read_more; //0-yes 1-no

        $read_align = $config_courses->courses_read_more_align == "0" ? "left" : "right";

        $description_align = $config_courses->courses_description_alignment == "0" ? "left" : "right";

        $edit_read_more = $config_courses->courses_read_more;

        $alias = trim($course->alias) == "" ? JFilterOutput::stringURLSafe($course->name) : trim($course->alias);

        $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");

        $rt = "";

        $style_d = "padding-right:10px;";



        if($layout == "1"){//mini profile

            $image_name = explode("/", $course->image);

            $image_name = $image_name[count($image_name)-1];



            if(trim($course->image) == ""){

                $course->image = "components/com_guru/images/thumbs/no_image.gif";

                guruHelper::createThumb($image_name, "components/com_guru/images", $config_courses->courses_image_size, $type);

            }

            else{

                guruHelper::createThumb($image_name, $config->imagesin."/courses", $config_courses->courses_image_size, $type);

            }

            $image = "";

            if(trim($course->image) != ""){
                $image = '<img  src="'.JURI::root().$course->image.'" />';

				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["guruprograms"];
				
				if(intval($itemid_seo) > 0){
					$item_id = intval($itemid_seo);
				}
                else{
                    $helper = new guruHelper();
                    $itemid_menu = $helper->getCourseMenuItem(intval($course->id));

                    if(intval($itemid_menu) > 0){
                        $item_id = intval($itemid_menu);
                    }
                }

                $image = '<a class="thumbnail"  href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.$image.'</a>';

            }

            $description = $this->cutBio($course->description, $config_courses->courses_description_length, $config_courses->courses_description_type);



            $return .= '<div class="row-fluid">';



            if($wrap == "1"){//no wrap

                if($img_align == "0"){// left

                    $return .= "<div class='media-body'>";

                    if(trim($image) != ""){

                        $return .= 			'<div class="pull-left"><ul class="thumbnails"><li>'.$image.'</li></ul></div>';

                    }
					
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruprograms"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}
                    else{
                        $helper = new guruHelper();
                        $itemid_menu = $helper->getCourseMenuItem(intval($course->id));

                        if(intval($itemid_menu) > 0){
                            $item_id = intval($itemid_menu);
                        }
                    }
					
                    $return .= 			'<div  class=" media-heading '.$style_courses->courses_name.'">

														<a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.$course->name.'</a>

													</div>';

                    if($read_more == "0"&& $edit_read_more == "0"){
						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["guruprograms"];
						
						if(intval($itemid_seo) > 0){
							$item_id = intval($itemid_seo);
						}
                        else{
                            $helper = new guruHelper();
                            $itemid_menu = $helper->getCourseMenuItem(intval($course->id));

                            if(intval($itemid_menu) > 0){
                                $item_id = intval($itemid_menu);
                            }
                        }
						
                        $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';

                    }

                    elseif($read_more == "1" && $edit_read_more == "0"){
						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["guruprograms"];
						
						if(intval($itemid_seo) > 0){
							$item_id = intval($itemid_seo);
						}
						else{
                            $helper = new guruHelper();
                            $itemid_menu = $helper->getCourseMenuItem(intval($course->id));

                            if(intval($itemid_menu) > 0){
                                $item_id = intval($itemid_menu);
                            }
                        }

                        $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';

                    }

                    $return .= 			'<div class="media '.$style_courses->courses_description.'" style="text-align:'.$description_align.' '.$style_d.';">'.$description.$rt.'</div>';

                    $return .= "</div>";



                }

                elseif($img_align == "1"){// right
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruprograms"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}
                    else{
                        $helper = new guruHelper();
                        $itemid_menu = $helper->getCourseMenuItem(intval($course->id));

                        if(intval($itemid_menu) > 0){
                            $item_id = intval($itemid_menu);
                        }
                    }
					
                    $return .= "<div class='media-body'>";

                    $return .= 			'<div class=" media-heading  '.$style_courses->courses_name.'">

												<a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.$course->name.'</a>

											</div>';

                    if($read_more == "0"&& $edit_read_more == "0"){
						
                        $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';

                    }

                    elseif($read_more == "1" && $edit_read_more == "0"){

                        $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';

                    }

                    $return .= 			'<div class=" media '.$style_courses->courses_description.'" style="text-align:'.$description_align.';">'.$description.$rt.'</div>';





                    if(trim($image) != ""){

                        $return .= 			'<div class="pull-right" ><ul class="thumbnails"><li>'.$image.'</li></ul></div>';

                    }

                    $return .= "</div>";



                }

            }

            elseif($wrap == "0"){//wrap
				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["guruprograms"];
				
				if(intval($itemid_seo) > 0){
					$item_id = intval($itemid_seo);
				}
                else{
                    $helper = new guruHelper();
                    $itemid_menu = $helper->getCourseMenuItem(intval($course->id));

                    if(intval($itemid_menu) > 0){
                        $item_id = intval($itemid_menu);
                    }
                }
				
                if($img_align == "0"){// left

                    $return .= 		'<div class="media-body">';

                    $return .= 			'<div class=" media-heading '.$style_courses->courses_name.'">

												<a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.$course->name.'</a>

											</div>';

                    if(trim($image) != ""){

                        $return .= 	'<div class="pull-left" ><ul class="thumbnails"><li>'.$image.'</li></ul></div>';

                    }

                    if($read_more == "0"&& $edit_read_more == "0"){

                        $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';

                    }

                    elseif($read_more == "1" && $edit_read_more == "0"){

                        $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';

                    }

                    $return .= 			'<div class="media '.$style_courses->courses_description.'" style="text-align:'.$description_align.';">'.$description.$rt.'</div>';



                    $return .= "</div>";

                }

                elseif($img_align == "1"){// right
                    $return .= "<div class='media-body'>";

                    if(trim($image) != ""){

                        $return .= 			'<div class="pull-right" ><ul class="thumbnails"><li >'.$image.'</li></ul></div>';

                    }
					
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruprograms"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}
                    else{
                        $helper = new guruHelper();
                        $itemid_menu = $helper->getCourseMenuItem(intval($course->id));

                        if(intval($itemid_menu) > 0){
                            $item_id = intval($itemid_menu);
                        }
                    }
					
                    $return .= 			'<div class=" media-heading '.$style_courses->courses_name.'">

												<a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.$course->name.'</a>

											</div>';

                    if($read_more == "0"&& $edit_read_more == "0"){

                        $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';

                    }

                    elseif($read_more == "1" && $edit_read_more == "0"){

                        $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a>';

                    }

                    $return .= 			'<div class=" media '.$style_courses->courses_description.'" style="text-align:'.$description_align.';">'.$description.$rt.'</div>';



                    $return .= "</div>";

                }

            }



            $return .= '</div>';

        }//if mini profile



        return $return;

    }









    function generateCoursesCells($config_courses, $style_courses, $course, $config){



        $detect = new Mobile_Detect;

        $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

        if($deviceType =="phone"){

            $widthboostrapimg = 'width="50%"';

            $break = "<br/>";

        }

        else{

            $widthboostrapimg = 'width="35%"';

            $break = "";

        }



        $type = $config_courses->courses_image_size_type == "0" ? "w" : "h";

        $return = "";

        $layout = $config_courses->courseslayout;

        $wrap = $config_courses->courses_wrap_image; //0-yes, 1-no

        $img_align = $config_courses->courses_image_alignment; //0-left, 1-right

        $read_more = $config_courses->courses_read_more; //0-yes 1-no

        $read_align = $config_courses->courses_read_more_align == "0" ? "left" : "right";

        $description_align = $config_courses->courses_description_alignment == "0" ? "left" : "right";

        $edit_read_more = $config_courses->courses_read_more;

        $alias = trim($course->alias) == "" ? JFilterOutput::stringURLSafe($course->name) : trim($course->alias);

        $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");



        if($layout == "1"){//mini profile

            $image_name = explode("/", $course->image);

            $image_name = $image_name[count($image_name)-1];



            if(trim($course->image) == ""){

                $course->image = "components/com_guru/images/thumbs/no_image.gif";

                guruHelper::createThumb($image_name, "components/com_guru/images", $config_courses->courses_image_size, $type);

            }

            else{

                guruHelper::createThumb($image_name, $config->imagesin."/courses", $config_courses->courses_image_size, $type);

            }

            $image = "";

            if(trim($course->image) != ""){
				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["guruprograms"];
				
				if(intval($itemid_seo) > 0){
					$item_id = intval($itemid_seo);
				}
                else{
                    $helper = new guruHelper();
                    $itemid_menu = $helper->getCourseMenuItem(intval($course->id));

                    if(intval($itemid_menu) > 0){
                        $item_id = intval($itemid_menu);
                    }
                }
				
                $image = '<img class="'.$style_courses->courses_image.'" src="'.JURI::root().$course->image.'" />';

                $image = '<a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.$image.'</a>';

            }

            $description = $this->cutBio($course->description, $config_courses->courses_description_length, $config_courses->courses_description_type);



            $return .= '<div class="row-fluid">';



            if($wrap == "1"){//no wrap

                if($img_align == "0"){// left
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruprograms"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}
                    else{
                        $helper = new guruHelper();
                        $itemid_menu = $helper->getCourseMenuItem(intval($course->id));

                        if(intval($itemid_menu) > 0){
                            $item_id = intval($itemid_menu);
                        }
                    }
					
                    $return .= "<div class='row-fluid'>";

                    $return .= 		'<div class="span6">';

                    if(trim($image) != ""){

                        $return .= 			'<div class="thumbnail '.$style_courses->courses_image.'">'.$image.'</div>';

                    }

                    $return .= 		"</div>";

                    $return .= 		'<div class="span6">';

                    $return .= 			'<div class="'.$style_courses->courses_name.'">

												<a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.$course->name.'</a>

											</div>';

                    $return .= 			'<div class="'.$style_courses->courses_description.'" style="text-align:'.$description_align.';">'.$description.'</div>';

                    if($read_more == "0" && $edit_read_more == "0"){

                        $return .= '<div class="'.$style_courses->courses_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                    }

                    elseif($read_more == "1" && $edit_read_more == "0"){

                        $return .= '<div class="'.$style_courses->courses_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                    }

                    $return .= 		"</div>";

                    $return .= "</div>";

                }

                elseif($img_align == "1"){// right
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruprograms"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}
                    else{
                        $helper = new guruHelper();
                        $itemid_menu = $helper->getCourseMenuItem(intval($course->id));

                        if(intval($itemid_menu) > 0){
                            $item_id = intval($itemid_menu);
                        }
                    }
				
                    $return .= "<div class='row-fluid'>";

                    $return .= 		'<div class="span6">';

                    $return .= 			'<div class="'.$style_courses->courses_name.'">

												<a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.$course->name.'</a>

											</div>';

                    $return .= 			'<div class="'.$style_courses->courses_description.'" style="text-align:'.$description_align.';">'.$description.'</div>';

                    if($read_more == "0" && $edit_read_more == "0"){

                        $return .= '<div class="'.$style_courses->courses_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                    }

                    elseif($read_more == "1" && $edit_read_more == "0"){

                        $return .= '<div class="'.$style_courses->courses_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                    }

                    $return .= 		"</div>";

                    $return .= 		'<div class="span6">';

                    if(trim($image) != ""){

                        $return .= 			'<div class="'.$style_courses->courses_image.'">'.$image.'</div>';

                    }

                    $return .= 		"</div>";

                    $return .= "</div>";

                }

            }

            elseif($wrap == "0"){//wrap
				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["guruprograms"];
				
				if(intval($itemid_seo) > 0){
					$item_id = intval($itemid_seo);
				}
                else{
                    $helper = new guruHelper();
                    $itemid_menu = $helper->getCourseMenuItem(intval($course->id));

                    if(intval($itemid_menu) > 0){
                        $item_id = intval($itemid_menu);
                    }
                }
				
                if($img_align == "0"){// left

                    $return .= "<div class='row-fluid'>";

                    $return .= 		'<div class="span6">';

                    $return .= 			'<div class="'.$style_courses->courses_name.'">

												<a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.$course->name.'</a>

											</div>';

                    if(trim($image) != ""){

                        $return .= 			'<div class="'.$style_courses->courses_image.'">'.$image.'</div>';

                    }

                    $return .= 			'<div class="'.$style_courses->courses_description.'" style="text-align:'.$description_align.';">'.$description.'</div>';

                    if($read_more == "0" && $edit_read_more == "0"){

                        $return .= '<div class="'.$style_courses->courses_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                    }

                    elseif($read_more == "1" && $edit_read_more == "0"){

                        $return .= '<div class="'.$style_courses->courses_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                    }

                    $return .= 		"</div>";

                    $return .= "</div>";

                }

                elseif($img_align == "1"){// right

                    $return .= "<div class='row-fluid'>";

                    $return .= 		'<div class="span6">';

                    if(trim($image) != ""){

                        $return .= 			'<div class="'.$style_courses->courses_image.'" style="float:right;">'.$image.'</div>';

                    }

                    $return .= 			'<div class="'.$style_courses->courses_name.'">

												<a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.$course->name.'</a>

											</div>';

                    $return .= 			'<div class="'.$style_courses->courses_description.'" style="text-align:'.$description_align.';">'.$description.'</div>';

                    if($read_more == "0" && $edit_read_more == "0"){

                        $return .= '<div class="'.$style_courses->courses_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                    }

                    elseif($read_more == "1" && $edit_read_more == "0"){

                        $return .= '<div class="'.$style_courses->courses_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                    }

                    $return .= 		"</div>";

                    $return .= "</div>";

                }

            }



            $return .= '</div>';

        }//if mini profile



        return $return;

    }//end function



    function countCoursesNumber($cat_id){

        $db = JFactory::getDBO();

        $sql = "select count(*) from #__guru_program where catid=".intval($cat_id)." and published=1";

        $db->setQuery($sql);

        $db->execute();

        $single_result = $db->loadResult();

        $result = 0;



        $sql = "select child_id from #__guru_categoryrel where parent_id=".intval($cat_id);

        $db->setQuery($sql);

        $db->execute();

        $ids = $db->loadResultArray();

        if(isset($ids) && count($ids) > 0){

            $sql = "select count(*) from #__guru_program where catid in (".implode(", ", $ids).") and published=1";

            $db->setQuery($sql);

            $db->execute();

            $result = $db->loadResult();

        }

        return (int)$single_result + (int)$result;

    }



    function countSubcategsNumber($cat_id){

        $db = JFactory::getDBO();

        $sql = "select count(*) from #__guru_categoryrel c, #__guru_category ca where c.parent_id=".intval($cat_id)." and c.child_id = ca.id and published=1";

        $db->setQuery($sql);

        $db->execute();

        $result = $db->loadResult();

        return $result;

    }



    function generateCategsCells($config_categs, $style_categs, $course, $config){
        $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");

        $type = $config_categs->ctgs_image_size_type == "0" ? "w" : "h";

        $return = "";

        $layout = $config_categs->ctgslayout;

        $wrap = $config_categs->ctgs_wrap_image; //0-yes, 1-no

        $img_align = $config_categs->ctgs_image_alignment; //0-left, 1-right

        $read_more = $config_categs->ctgs_read_more; //0-yes 1-no

        $read_align = $config_categs->ctgs_read_more_align == "0" ? "left" : "right";

        $description_align = $config_categs->ctgs_description_alignment == "0" ? "left" : "right";

        $edit_read_more = $config_categs->ctgs_read_more;

        $courses_number = $this->countCoursesNumber($course->id);

        $sub_categs_number = $this->countSubcategsNumber($course->id);

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

        if(isset($course->alias) && $course->alias == ""){

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



        if($show === true){

            if($layout == "1"){//mini profile

                if(trim($course->image) == ""){

                    $course->image = "components/com_guru/images/thumbs/no_image.gif";

                    $course->imageName = "no_image.gif";

                    guruHelper::createThumb($course->imageName, "components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."images", $config_categs->ctgs_image_size, $type);

                }

                else{

                    guruHelper::createThumb($course->imageName, $config->imagesin."/categories", $config_categs->ctgs_image_size, $type);

                }

                $image = "";

                if(trim($course->image) != ""){
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["gurupcategs"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}

                    $helper = new guruHelper();
                    $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                    $item_id_categ = $item_id;

                    if(intval($itemid_menu) > 0){
                        $item_id_categ = intval($itemid_menu);
                    }
					
					$image = '<img alt="Category Image" class="'.$style_categs->ctgs_image.'" src="'.JURI::root().$course->image.'" />';
					$image = '<a href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.$image.'</a>';
                }

                $description = $this->cutBio($course->description, $config_categs->ctgs_description_length, $config_categs->ctgs_description_type);



                $return .= '<div>';

                if($wrap == "1"){//no wrap

                    if($img_align == "0"){// left

                        $return .= "<div class='media-body'>";

                        if(trim($image) != ""){

                            $return .= 			'<div class="'.$style_categs->ctgs_image.'">'.$image.'</div>';

                        }
						
						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["gurupcategs"];
						
						if(intval($itemid_seo) > 0){
							$item_id = intval($itemid_seo);
						}
						
                        $helper = new guruHelper();
                        $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                        $item_id_categ = $item_id;

                        if(intval($itemid_menu) > 0){
                            $item_id_categ = intval($itemid_menu);
                        }

                        $return .= 			'<div class=" media-heading '.$style_categs->ctgs_categ_name.'">

													<a href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.$course->name.$edit_sum.'</a>

												</div>';

                        if($read_more == "0" && $edit_read_more == "0"){$rc ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a>';}

                        $return .= 			'<div class="media '.$style_categs->ctgs_description.'" style="text-align:'.$description_align.';">'.$description.$rc.'</div>';

                        $return .= "</div>";
                    }

                    elseif($img_align == "1"){// right
						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["gurupcategs"];
						
						if(intval($itemid_seo) > 0){
							$item_id = intval($itemid_seo);
						}
						
                        $helper = new guruHelper();
                        $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                        $item_id_categ = $item_id;

                        if(intval($itemid_menu) > 0){
                            $item_id_categ = intval($itemid_menu);
                        }
						
                        $return .= "<tr>";

                        $return .= 		'<td style="vertical-align:top;">';

                        $return .= 			'<div class="'.$style_categs->ctgs_categ_name.'">

													<a href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.$course->name.$edit_sum.'</a>

												</div>';

                        $return .= 			'<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.';">'.$description.'</div>';

                        if($read_more == "0" && $edit_read_more == "0"){

                            $return .= '<div class="'.$style_categs->ctgs_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        elseif($read_more == "1" && $edit_read_more == "0"){

                            $return .= '<div class="'.$style_categs->ctgs_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        $return .= 		"</td>";

                        $return .= 		'<td style="vertical-align:top;" width="5%">';

                        if(trim($image) != ""){

                            $return .= 			'<div class="'.$style_categs->ctgs_image.'">'.$image.'</div>';

                        }

                        $return .= 		"</td>";

                        $return .= "</tr>";

                    }

                }

                elseif($wrap == "0"){//wrap
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["gurupcategs"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}

                    $helper = new guruHelper();
                    $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                    $item_id_categ = $item_id;

                    if(intval($itemid_menu) > 0){
                        $item_id_categ = intval($itemid_menu);
                    }
					
                    if($img_align == "0"){// left

                        $return .= "<tr>";

                        $return .= 		'<td style="vertical-align:top;">';

                        $return .= 			'<div class="'.$style_categs->ctgs_categ_name.'">

													<a href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.$course->name.$edit_sum.'</a>

												</div>';

                        if(trim($image) != ""){

                            $return .= 			'<div class="'.$style_categs->ctgs_image.'">'.$image.'</div>';

                        }

                        $return .= 			'<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.';">'.$description.'</div>';

                        if($read_more == "0" && $edit_read_more == "0"){

                            $return .= '<div class="'.$style_categs->ctgs_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        elseif($read_more == "1" && $edit_read_more == "0"){

                            $return .= '<div class="'.$style_categs->ctgs_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        $return .= 		"</td>";

                        $return .= "</tr>";

                    }

                    elseif($img_align == "1"){// right
						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["gurupcategs"];
						
						if(intval($itemid_seo) > 0){
							$item_id = intval($itemid_seo);
						}

                        $helper = new guruHelper();
                        $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                        $item_id_categ = $item_id;

                        if(intval($itemid_menu) > 0){
                            $item_id_categ = intval($itemid_menu);
                        }
						
                        $return .= "<tr>";

                        $return .= 		'<td style="vertical-align:top;" width="5%">';

                        if(trim($image) != ""){

                            $return .= 			'<div class="'.$style_categs->ctgs_image.'" style="float:right;">'.$image.'</div>';

                        }

                        $return .= 			'<div class="'.$style_categs->ctgs_categ_name.'">

													<a href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.$course->name.$edit_sum.'</a>

												</div>';

                        $return .= 			'<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.';">'.$description.'</div>';

                        if($read_more == "0" && $edit_read_more == "0"){

                            $return .= '<div class="'.$style_categs->ctgs_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        elseif($read_more == "1" && $edit_read_more == "0"){

                            $return .= '<div class="'.$style_categs->ctgs_st_read_more.'" style="text-align:'.$read_align.'">'.'<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a></div>';

                        }

                        $return .= 		"</td>";

                        $return .= "</tr>";

                    }

                }

                $return .= '</div>';

            }//if mini profile

        }//if show

        return $return;

    }



    //boostrap function for categories----begin//

    function generateCategsCellsB($config_categs, $style_categs, $course, $config){

        $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");

        $type = $config_categs->ctgs_image_size_type == "0" ? "w" : "h";

        $return = "";

        $layout = $config_categs->ctgslayout;

        $wrap = $config_categs->ctgs_wrap_image; //0-yes, 1-no

        $img_align = $config_categs->ctgs_image_alignment; //0-left, 1-right

        $read_more = $config_categs->ctgs_read_more; //0-yes 1-no

        $read_align = $config_categs->ctgs_read_more_align == "0" ? "left" : "right";

        $description_align = $config_categs->ctgs_description_alignment == "0" ? "left" : "right";

        $edit_read_more = $config_categs->ctgs_read_more;

        $courses_number = $this->countCoursesNumber($course->id);

        $sub_categs_number = $this->countSubcategsNumber($course->id);

        $show_empty_categs = $config_categs->ctgs_show_empty_catgs;

        $show = true;

        $rt = "";

        $detect = new Mobile_Detect;

        $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

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

        if(isset($course->alias) && $course->alias == ""){

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
				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["gurupcategs"];
				
				if(intval($itemid_seo) > 0){
					$item_id = intval($itemid_seo);
				}

                $helper = new guruHelper();
                $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                $item_id_categ = $item_id;

                if(intval($itemid_menu) > 0){
                    $item_id_categ = intval($itemid_menu);
                }
				
                if(trim($course->image) == ""){
                    $course->image = "components/com_guru/images/thumbs/no_image.gif";
                    $course->imageName = "no_image.gif";
					$guru_helper = new guruHelper();
                    $guru_helper->createThumb($course->imageName, "components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."images", $config_categs->ctgs_image_size, $type);
                }
                else{
					$helper = new guruHelper();
                    $helper->createThumb($course->imageName, $config->imagesin."/categories", $config_categs->ctgs_image_size, $type);
                }

                $image = "";

                if(trim($course->image) != ""){

                    $image = '<img alt="Category Image" src="'.JURI::root().$course->image.'" />';

                    $image = '<a class="thumbnail" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.$image.'</a>';

                }

                $description = $this->cutBio($course->description, $config_categs->ctgs_description_length, $config_categs->ctgs_description_type);


                if($wrap == "1"){//no wrap
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["gurupcategs"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}

                    $helper = new guruHelper();
                    $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                    $item_id_categ = $item_id;

                    if(intval($itemid_menu) > 0){
                        $item_id_categ = intval($itemid_menu);
                    }
					
					$class_display = "display:table-cell;";
                    
                    if($img_align == "0"){// left

                        $return .= "<div>";

                        if(trim($image) != ""){

							$return .= 			'<div class="'.$style_categs->ctgs_image.'">'.$image.'</div>';
                        }

                        $return .= 			'<div class="'.$style_categs->ctgs_categ_name.'">

														<a style="'.$style_d.'" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id.'-'.$alias.'&Itemid='.$item_id_categ).'">'.$nameandnumb.'</a>

													</div>';

                        if($read_more == "0"&& $edit_read_more == "0"){

                            $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a>';

                        }

                        elseif($read_more == "1" && $edit_read_more == "0"){

                            $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a>';

                        }

                        $return .= 			'<div class="'.$style_categs->ctgs_description.' '.$class_display.'" style="text-align:'.$description_align.'; '.$style_d.'">'.$description.$rt.'</div>';

                        $return .= "</div>";

                    }

                    elseif($img_align == "1"){// right
						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["gurupcategs"];
						
						if(intval($itemid_seo) > 0){
							$item_id = intval($itemid_seo);
						}
					
                        $return .= "<div class='media-body'>";

                        if(trim($image) != ""){

                            $return .= 			'<div class="pull-right"><ul class="thumbnails"><li >'.$image.'</li></ul></div>';

                        }

                        $return .= 			'<div class=" media-heading '.$style_categs->ctgs_categ_name.'">

														<a style="'.$style_m.'" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id.'-'.$alias.'&Itemid='.$item_id_categ).'">'.$nameandnumb.'</a>

													</div>';

                        if($read_more == "0"&& $edit_read_more == "0"){
                            $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a>';

                        }

                        elseif($read_more == "1" && $edit_read_more == "0"){

                            $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a>';

                        }

                        $return .= 			'<div class="media '.$style_categs->ctgs_description.' '.$class_display.'" style="text-align:'.$description_align.'; '.$style_m.'">'.$description.$rt.'</div>';

                        $return .= "</div>";

                    }

                }

                elseif($wrap == "0"){//wrap
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["gurupcategs"];
					
					if(intval($itemid_seo) > 0){
						$item_id = intval($itemid_seo);
					}

                    $helper = new guruHelper();
                    $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                    $item_id_categ = $item_id;

                    if(intval($itemid_menu) > 0){
                        $item_id_categ = intval($itemid_menu);
                    }
					 
					if($img_align == "0"){// left
						$return .= "<div>";
						$return .= 		'<div>';
						$return .= 			'<div class="'.$style_categs->ctgs_categ_name.'">
												<a href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.$course->name.$edit_sum.'</a>
											</div>';
	
						if(trim($image) != ""){
							$return .= 			'<div class="'.$style_categs->ctgs_image.'">'.$image.'</div>';
						}
						$return .= 			'<div class="'.$style_categs->ctgs_description.'" style="text-align:'.$description_align.';">'.$description.'</div>';
	
						if($read_more == "0" && $edit_read_more == "0"){
							$return .= '<div class="'.$style_categs->ctgs_st_read_more.'" style="text-align:'.$read_align.'">'.'<a href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a></div>';
						}
						elseif($read_more == "1" && $edit_read_more == "0"){
							$return .= '<div class="'.$style_categs->ctgs_st_read_more.'" style="text-align:'.$read_align.'">'.'<a href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a></div>';
						}	
						$return .= 		"</div>";
						$return .= "</div>";
	
					}

                    elseif($img_align == "1"){// right

                        $return .= "<div class='media-body'>";

                        if(trim($image) != ""){

                            $return .= 			'<div class="pull-right"><ul class="thumbnails"><li >'.$image.'</li></ul></div>';

                        }

                        $return .= 			'<div class=" media-heading '.$style_categs->ctgs_categ_name.'">

														<a style="'.$style_m.'" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id.'-'.$alias.'&Itemid='.$item_id).'">'.$nameandnumb.'</a>

													</div>';

                        if($read_more == "0"&& $edit_read_more == "0"){

                            $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a>';

                        }

                        elseif($read_more == "1" && $edit_read_more == "0"){

                            $rt ='<a class="btn readmore" href="'.JRoute::_('index.php?option=com_guru&view=guruPcategs&task=view&cid='.$course->id."-".$alias."&Itemid=".$item_id_categ).'">'.JText::_("GURU_READ_MORE").'</a>';

                        }

                        $return .= 			'<div class="media '.$style_categs->ctgs_description.'" style="text-align:'.$description_align.'; '.$style_m.'">'.$description.$rt.'</div>';

                        $return .= "</div>";

                    }

                }

                $return .= '</div>';

            }//if mini profile

        }//if show

        return $return;

    }

    //boostrap function for categories----end//









    function generateTreeCategoriesList($parent_id, $level){

        $db = JFactory::getDBO();

        $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");

		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["gurupcategs"];
		
		if(intval($itemid_seo) > 0){
			$item_id = intval($itemid_seo);
		}

        $sql = "select c.id, c.name from #__guru_category c, #__guru_categoryrel cr where c.id=cr.child_id and cr.parent_id=".intval($parent_id)." and c.published=1";

        $db->setQuery($sql);

        $db->execute();

        $childrens = $db->loadAssocList();



        $i = 1;

        if(isset($childrens) && is_array($childrens) && count($childrens) > 0){

            echo '<div id="categoryList"><ul>';

            $level = $level == 1 ? 2 : 1;

            foreach($childrens as $key=>$value){

                $cat_id = $value["id"] == "0" ? "-1" : $value["id"];

                $sql = "select count(*) from #__guru_program p where p.catid=".($cat_id)." and p.status='1'";
				
                $db->setQuery($sql);

                $db->execute();

                $result = $db->loadResult();

                if($result != "0"){

                    echo '<li class="guru_level'.$level.'">';

                    if(!isset($next_nr)){

                        $next_nr = "";

                    }

                    $helper = new guruHelper();
                    $itemid_menu = $helper->getCategMenuItem(intval($course->id));
                    $item_id_categ = $item_id;

                    if(intval($itemid_menu) > 0){
                        $item_id_categ = intval($itemid_menu);
                    }

                    echo $next_nr." ".'<a href="index.php?option=com_guru&view=guruPcategs&task=view&cid='.$value["id"]."-".JFilterOutput::stringURLSafe(trim($value["name"])).'&Itemid='.intval($item_id_categ).'">'.trim($value["name"])." (".$result.")".'</a>';

                    $this->generateTreeCategoriesList($value["id"], $level);

                    echo '</li>';

                }

                $i++;

            }

            echo "</ul></div>";

        }

    }



    function cutBio($full_bio, $description_length, $description_type){

        $full_bio = strip_tags($full_bio);

        if($description_length == "" || strlen($full_bio) <= $description_length){

            return $full_bio;

        }

        else{

            if($description_type == "0"){

                $return = substr($full_bio, 0, $description_length);

                return $return."...";

            }

            elseif($description_type == "1"){

                $words = explode(" ", $full_bio);

                $return = "";

                $words = array_slice($words, 0, $description_length);

                $return = implode(" ", $words);

                return $return."...";

            }

        }

    }



    function createAuthorLinksboots($author){

        $table = '<div  style="margin-left:2px;">

						<div class="well teacher_links">	

							<div class="guru_teacher_email">';



        if($author->show_email==1 && trim($author->email)!=""){

            $table .= '<span class="teacher_email_guru">

								<a href="mailto:'.$author->email.'">'.

                JText::_("GURU_EMAIL").'

								</a>

							</span>';

        }



        if($author->show_website==1 && trim($author->website)!="http://" && trim($author->website)!=""){

            $table .= '<span class="guru_teacher_site">

								<a href="'.$author->website.'" target="_blank">'.

                JText::_("GURU_WEBSITE").'

								</a>

							</span>';

        }



        if($author->show_blog==1 && trim($author->blog)!="http://" && trim($author->blog)!=""){

            $table .= '<span class="guru_teacher_blog">

								<a href="'.$author->blog.'" target="_blank">'.

                JText::_("GURU_BLOG").'

								</a>

							</span>';

        }



        if($author->show_twitter==1 && trim($author->twitter)!=""){

            $table .= '<span class="guru_teacher_twitter">

								<a href="http://www.twitter.com/'.$author->twitter.'" target="_blank">'.

                JText::_("GURU_TWITTER").'

								</a>

							</span>';

        }



        if($author->show_facebook==1 && trim($author->facebook)!="http://" && trim($author->facebook)!=""){

            $table .= '<span class="guru_teacher_facebook">

								<a href="'.$author->facebook.'" target="_blank">'.

                JText::_("GURU_FACEBOOK").'

								</a>

							</span>';

        }

        $table .= 			'</div>

								</div>

						</div>';

        return $table;

    }



};

?>

