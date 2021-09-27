<?php
defined('_JEXEC') or die('Restricted access');

class ModGuruSearch{
    public $child_categories = array();

    function getAllChilds($parent){
        $db = JFactory::getDbo();
        $this->child_categories[] = intval($parent);

        $sql = "SELECT `child_id` FROM #__guru_categoryrel WHERE parent_id=".intval($parent);
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        
        if(isset($result) && is_array($result) && isset($result["0"])){
            $this->child_categories[] = intval($result["0"]);

            if(intval($result["0"]) > 0){
                $this->getAllChilds($result["0"]);
            }
        }

        return $this->child_categories;
    }

    function getCourses($params){
        $db = JFactory::getDbo();
        $sortby = $params->get("sortby", "0");
        $category = $params->get("category", "0");
        
        $and = "";

        if(intval($category) > 0){
            $categories_list = $this->getAllChilds(intval($category));

            if(is_array($categories_list) && count($categories_list) > 0){
                $and .= " AND `catid` in (".implode(",", $categories_list).")";
            }
        }

        switch($sortby){
            case "0" : { // most popular
                //$sql = "select  p.* from `#__guru_program` p, `#__guru_order` o  where o.courses like p.id=bc.course_id GROUP BY bc.course_id LIMIT 0,".$params->get("howManyC")."";
                $sql = "select * from `#__guru_program` where `published`='1' and `status`='1' and `startpublish` <= now() ".$and;
                $db->setQuery($sql);
                $db->execute();
                $courses = $db->loadAssocList();
                if(isset($courses) && count($courses) > 0){
                    $courses_temp = array();
                    foreach($courses as $key=>$course){
                        $nr = self::getStudentsNumber($course, null);
                        if(count($courses_temp) == 0){
                            $courses_temp[] = array($course['id']=>$nr);
                        }
                        else{                           
                            foreach($courses_temp as $key=>$c_id_nr){
                                if(current($c_id_nr) <= $nr){
                                    array_splice($courses_temp, $key, 0, array(array($course['id']=>$nr)));
                                    break;
                                }
                                elseif(!isset($courses_temp[$key + 1])){
                                    array_splice($courses_temp, $key+1, 0, array(array($course['id']=>$nr)));
                                    break;
                                }
                            }
                        }
                    }
                    $courses_temp = array_slice($courses_temp, 0, $params->get("howManyC"));
                    $courses = array();
                    foreach($courses_temp as $key=>$c_id_nr){
                        $sql = "select * from `#__guru_program` where `id`=".intval(key($c_id_nr));
                        $db->setQuery($sql);
                        $db->execute();
                        $course_temp = $db->loadAssocList();
                        $courses = array_merge($courses, $course_temp);
                    }
                }

                return $courses;
                break;
            }
            case "1" : { // most recent
                $and .= " ORDER BY `startpublish` DESC LIMIT 0,".$params->get("howManyC")."";
                break;
            }
            case "2" : { // random
                $and .= " ORDER BY RAND() LIMIT 0,".$params->get("howManyC")."";
                break;
            }
        }

        $sql = "select * from `#__guru_program` where 1=1 and published=1 and status=1 and `startpublish` <= now() ".$and;
        $db->setQuery($sql);
        $db->execute();
        $courses = $db->loadAssocList();

        return $courses;
    }
    
    function showCourseImage($params){
        if($params->get("showthumb", "1") == 1){
            return true;
        }
        return false;
    }
    
    function createThumb($course, $params){
        return $course["image"];
    }
    
    function getStudentsNumber($course, $params){
        $db = JFactory::getDbo();
        $sql = "SELECT count(distinct bc.`userid`) FROM #__guru_buy_courses bc, #__users u , #__guru_customer c, #__guru_order o WHERE c.`id`=bc.`userid` and bc.`userid`=u.`id` and bc.`course_id`=".intval($course["id"])." and o.`userid`=c.`id` and o.`userid`=bc.`userid` and o.`status`='Paid'";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        
        return @$result["0"];
    }
    
    function getDescription($course, $params){
        $return = "";
        $audio_p_desc_length = $params->get("desclength");
        $audio_p_desc_length_type = $params->get("desclengthtype");
        $description = strip_tags($course["description"]);
        
        if($audio_p_desc_length != '' && trim($description) != ""){
             $new_description = "";
             if($audio_p_desc_length_type == 0){
                //words
                $desc_array = explode(" ", $description);
                $desc = array();
                if(count($desc_array) > $audio_p_desc_length){
                    foreach($desc_array as $key => $val){                                   
                        if($key < $audio_p_desc_length){
                            $desc[] = $val;
                        }                                   
                     }
                    $new_description = implode(" ", $desc)."...";                               
                }
                else{
                    $new_description = $description;
                }                            
             }
             elseif($audio_p_desc_length_type == 1){
                //characters                            
                $descr_nr = strlen($description);
                if($descr_nr > $audio_p_desc_length){
                    $new_description = substr(trim($description), 0, $audio_p_desc_length)."...";
                }
                else{
                    $new_description = $description;
                }
             }
             $return = $new_description;
        }
        return $return;
    }
    
    function getAuthor($course, $params){
        $db = JFactory::getDbo();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$return = array();
		$item_id = JRequest::getVar("Itemid", "0");
		require_once(JPATH_SITE.DS."components".DS."com_guru".DS.'helpers'.DS.'helper.php');
			
		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruprofile"];
		
		if(intval($itemid_seo) > 0){
			$item_id = intval($itemid_seo);
			
			$sql = "select `access` from #__menu where `id`=".intval($item_id);
			$db->setQuery($sql);
			$db->execute();
			$access = $db->loadColumn();
			$access = @$access["0"];
			
			if(intval($access) == 3){
				// special
				$user_groups = $user->get("groups");
				if(!in_array(8, $user_groups)){
					$item_id = JRequest::getVar("Itemid", "0");
				}
			}
		}
		
		if(intval($item_id) > 0 && intval($user_id) == 0){
			$sql = "select `access` from #__menu where `id`=".intval($item_id);
			$db->setQuery($sql);
			$db->execute();
			$access = $db->loadColumn();
			$access = @$access["0"];
			
			if(intval($access) != "1"){
				$item_id = 0;
			}
		}
		
		$authors = explode("|", $course["author"]);
		$authors = array_filter($authors);
		
        $sql = "SELECT `id`, `name` from #__users where `id` in (".implode(",", $authors).")";
        $db->setQuery($sql);
        $db->execute();
        $authors_details = $db->loadAssocList();
        
		if(isset($authors_details) && count($authors_details) > 0){
			foreach($authors_details as $key=>$value){
				$sql = "SELECT `id` from #__guru_authors where `userid`=".intval($value["id"]);
				$db->setQuery($sql);
				$db->execute();
				$teacher_id = $db->loadColumn();
				$teacher_id = @$teacher_id["0"];
				
				$url = '<a href="'.JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid='.intval($teacher_id)."-".JFilterOutput::stringURLSafe($value["name"])."&Itemid=".intval($item_id)).'">'.$value["name"]."</a>";
				$return[] = $url;
			}
		}
		
		return $return;
    }
	
    function getAuthorID($course, $params){
        $db = JFactory::getDbo();
        $authorname = "SELECT id from #__guru_authors where userid=".intval($course["author"]);
        $db->setQuery($authorname);
        $db->execute();
        $authorname = $db->loadColumn();
        return $authorname["0"];
    }
	
    function create_module_thumbnails($images, $width, $height, $width_old, $height_old){
            $image_path = JURI::root().$images;
            if(strpos($images, "http") !== FALSE){
                $image_path = $images;
            }
            $thumb_src = "modules/mod_guru_courses/createthumbs.php?src=".$image_path."&amp;w=".$width."&amp;h=".$height;//."&zc=1";
            return $thumb_src;
        }
        
    function getAudioDescription($audio, $params){
        $return = "";
        $audio_p_desc_length = $params->get("desclength");
        $audio_p_desc_length_type = $params->get("desclengthtype");
        $description = $audio["description"];
        
        if($audio_p_desc_length != '' && trim($description) != ""){
             $new_description = "";
             if($audio_p_desc_length_type == 0){
                //words
                $desc_array = explode(" ", $description);
                $desc = array();
                if(count($desc_array) > $audio_p_desc_length){
                    foreach($desc_array as $key => $val){                                   
                        if($key < $audio_p_desc_length){
                            $desc[] = $val;
                        }                                   
                     }
                    $new_description = implode(" ", $desc)."...";                               
                }
                else{
                    $new_description = $description;
                }                            
             }
             elseif($audio_p_desc_length_type == 1){
                //characters                            
                $descr_nr = strlen($description);
                if($descr_nr > $audio_p_desc_length){
                    $new_description = substr($description, 0, $audio_p_desc_length)."...";
                }
                else{
                    $new_description = $description;
                }
             }
             $return = $new_description;
        }
        return $return;
    }
    function getCourseLevel($course, $params){
        switch($course["level"]){
            case "0" : { 
                $return = JText::_("GURU_BEGINNERS");
                break;
            }
            case "1" : { 
                $return = JText::_("GURU_INTERMEDIATE");
                break;
            }
            case "2" : { 
                $return = JText::_("GURU_ADVANCED");
                break;
            }
        }
        return $return;
    
    }
};
?>
