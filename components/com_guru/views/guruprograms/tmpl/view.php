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
$guruModelguruProgram = new guruModelguruProgram();
// --------------------------- unload ijseo_plugin
jimport( 'joomla.plugin.helper' );
class iJoomlaPlugin extends JPluginHelper{
    function unloadFromPlugin($type, $name){
        $plugins = JPluginHelper::getPlugin("content");
        $plugins = JPluginHelper::$plugins;
        
        if(isset($plugins) && count($plugins) > 0){
            foreach($plugins as $key=>$value){
                if($value->name == $name && $value->type == $type){
                    array_splice($plugins, $key, 1);
                    JPluginHelper::$plugins = $plugins;
                    break;
                }
            }
        }
    }
}

$db = JFactory::getDBO();
$sql = "SELECT guru_ignore_ijseo from #__guru_config where id=1";
$db->setQuery($sql);
$db->execute();
$res = $db->loadResult();
$preview = JFactory::getApplication()->input->get("preview", "", "raw");

if($res == 0){
    $iJoomlaPlugin = new iJoomlaPlugin();
    $iJoomlaPlugin->unloadFromPlugin("content", "ijseo_plugin");
}
// --------------------------- unload ijseo_plugin


require_once(JPATH_BASE . "/components/com_guru/helpers/Mobile_Detect.php");

$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
    
$document = JFactory::getDocument();
$guruHelper = new guruHelper ();

$db = JFactory::getDBO();
$sql = "SELECT guru_turnoffjq  FROM  #__guru_config WHERE id=1";
$db->setQuery($sql);
$db->execute();
$guru_turnoffjq = $db->loadResult();

$document->addScriptDeclaration('
    jQuery.noConflict();
    jQuery(function(){
        jQuery(".subcat").find("hr:last").css("display","block"); 
    });
');


$program            = $this->program;   
$config             = $this->getConfigSettings;
$pdays              = $this->pdays; 
$author             = $this->author;
$courses            = $this->courses;
$programContent     = $this->programContent;
$exercise           = $this->exercise;
$requirements       = $this->requirements;
$number_of_days_per_program = count($pdays);

if($program->published == 0 && $preview == ""){
    $app = JFactory::getApplication();
    $app->redirect(JURI::root());
    die();
}

if($program->status == 0 && $preview == ""){
    //course accesible only for students of this course
    $user = JFactory::getUser();

    if(intval($user->id) > 0){
        // user logged
        $is_customerfor_course = $guruModelguruProgram->isCustomerForCourse($program);
        
        if(!$is_customerfor_course){
            $app = JFactory::getApplication();
            $app->redirect(JURI::root());
            die();
        }
    }
    else{
        // user not logged, no access
        $app = JFactory::getApplication();
        $app->redirect(JURI::root());
        die();
    }
}

$sql = "select `groups` from #__guru_category where `id`=".intval($program->catid);
$db->setQuery($sql);
$db->execute();
$categ_groups = $db->loadColumn();

if(isset($program->groups_acl) && trim($program->groups_acl) != "" && $preview == ""){
    $user = JFactory::getUser();
    $user_groups = $user->groups;
    $acl_groups = json_decode(trim($program->groups_acl), true);
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
elseif(isset($categ_groups["0"]) && trim($categ_groups["0"]) != "" && $preview == ""){
    $user = JFactory::getUser();
    $user_groups = $user->groups;
    $acl_groups = json_decode(trim($categ_groups["0"]), true);
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

if($program->endpublish != "0000-00-00 00:00:00"){
    if(strtotime(date("Y-m-d")) > strtotime($program->endpublish)){
        $app = JFactory::getApplication();
        $app->redirect(JURI::root());
        die();
    }
}

if($preview == "true"){
	$user = JFactory::getUser();

	if(intval($user->id) == 0){
		$app = JFactory::getApplication();
        $app->redirect(JURI::root()."index.php?option=com_users");
        die();
	}
	else{
    	$isAuthor = false;
        foreach ($author as $value) {
            $db = JFactory::getDbo();
            $query ="SELECT id FROM `#__guru_authors` WHERE id = '{$value->id}' AND userid = '{$user->id}' LIMIT 1";
            $result = $db->setQuery($query)->loadResult();
            if ($result) {
                $isAuthor = true;
                break;
            }
        }

        if(!$isAuthor && !in_array(8, $user->groups)){
    		// not Super User
    		$app = JFactory::getApplication();
	        $app->redirect(JURI::root());
	        die();
    	}
	}
}

if(isset($program->og_tags) && trim($program->og_tags) != ""){
    $og_tags = json_decode(trim($program->og_tags), true);

    if(isset($og_tags["og_title"]) && trim($og_tags["og_title"]) != ""){
        $meta_tag = '<meta property="og:title" content="'.trim($og_tags["og_title"]).'" />';
        $document->addCustomTag($meta_tag);
    }

    if(isset($og_tags["og_type"]) && trim($og_tags["og_type"]) != ""){
        $meta_tag = '<meta property="og:type" content="'.trim($og_tags["og_type"]).'" />';
        $document->addCustomTag($meta_tag);
    }

    if(isset($og_tags["og_image"]) && trim($og_tags["og_image"]) != ""){
        $meta_tag = '<meta property="og:image" content="'.trim($og_tags["og_image"]).'" />';
        $document->addCustomTag($meta_tag);
    }

    if(isset($og_tags["og_url"]) && trim($og_tags["og_url"]) != ""){
        $meta_tag = '<meta property="og:url" content="'.trim($og_tags["og_url"]).'" />';
        $document->addCustomTag($meta_tag);
    }

    if(isset($og_tags["og_desc"]) && trim($og_tags["og_desc"]) != ""){
        $meta_tag = '<meta property="og:description" content="'.trim($og_tags["og_desc"]).'" />';
        $document->addCustomTag($meta_tag);
    }
}

// how many points and how much time has a program
//$getsum_points_and_time = $this->getsum_points_and_time;

$my = JFactory::getUser();

$document   = JFactory::getDocument();
$document->setTitle($program->metatitle);
$document->setMetaData('keywords', $program->metakwd); 
$document->setMetaData('description', $program->metadesc); 

$db = JFactory::getDBO();
$sql = "SELECT chb_free_courses, step_access_courses, selected_course  FROM #__guru_program where id = ".intval($program->id);
$db->setQuery($sql);
$db->execute();
$result= $db->loadAssocList();
$chb_free_courses = $result["0"]["chb_free_courses"];
$step_access_courses = $result["0"]["step_access_courses"];
$selected_course = $result["0"]["selected_course"];

$itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
$selected_course_final = explode('|', $selected_course);

if(implode(", ", $selected_course_final) != ''){
    foreach($selected_course_final as $key=>$value){
        if(trim($value) == ""){
            unset($selected_course_final[$key]);
        }
    }
    
    
    $sql = "select name, id from #__guru_program where id in (".implode(", ", $selected_course_final).")";
    
    $db->setQuery($sql);
    $db->execute();
    $result = $db->loadAssocList();
    $all_title = array();
    
    if(isset($result) && count($result) > 0){
        foreach($result as $key=>$course){
            $all_title[] = $course["name"];
        }
    }
    $all_title = implode(", ", $all_title);
}
$sql = "select certificate_term from #__guru_program  where id = ".intval($program->id);
$db->setQuery($sql);
$db->execute();
$certificate_term = $db->loadResult();

$sql = "select avg_certc from #__guru_program where id = ".intval($program->id);
$db->setQuery($sql);
$db->execute();
$avg_cert = $db->loadResult();

$sql = "SELECT max_score FROM #__guru_quiz WHERE is_final= 1 LIMIT 1";
$db->setQuery($sql);
$result_maxs = $db->loadResult();


if($config->display_media == 1){
    $the_media = $guruModelguruProgram->find_intro_media($program->id);
    $no_plugin_for_code = 0;
    $aheight = 0; 
    $awidth = 0; 
    $vheight = 0; 
    $vwidth = 0;
    if(isset($the_media)){
        $the_media->code = stripslashes($the_media->code);
        if($the_media->type == 'video'){
            if($the_media->source == 'url' || $the_media->source == 'local'){
                if ($the_media->width == 0 || $the_media->height == 0){
                    $vheight = 300; 
                    $vwidth = 400;
                }
                else{
                    $vheight=$the_media->height; $vwidth=$the_media->width;
                }       
            }
            elseif ($the_media->source=='code'){
                if ($the_media->width == 0 || $the_media->height == 0){
                    $begin_tag = strpos($the_media->code, 'width="');
                    if($begin_tag !== false){
                        $remaining_code = substr($the_media->code, $begin_tag+7, strlen($the_media->code));
                        $end_tag = strpos($remaining_code, '"');
                        $vwidth = substr($remaining_code, 0, $end_tag);
                                    
                        $begin_tag = strpos($the_media->code, 'height="');
                        if($begin_tag !== false){
                            $remaining_code = substr($the_media->code, $begin_tag+8, strlen($the_media->code));
                            $end_tag = strpos($remaining_code, '"');
                            $vheight = substr($remaining_code, 0, $end_tag);
                            $no_plugin_for_code = 1;
                        }
                        else{
                            $vheight = 300; 
                            $vwidth = 400;
                        }   
                    }
                    else{
                        $vheight = 300; 
                        $vwidth = 400;
                    }   
                }
                else{
                    $replace_with = 'width="'.$the_media->width.'"';
                    $the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
                    $replace_with = 'height="'.$the_media->height.'"';
                    $the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);
                    $vheight=$the_media->height; $vwidth=$the_media->width;                     
                }
            }   
        }
        elseif($the_media->type == 'audio'){
            if ($the_media->source == 'url' || $the_media->source == 'local'){  
                if ($the_media->width == 0 || $the_media->height == 0){
                    $aheight=20;
                    $awidth=300;
                }
                else{
                    $aheight=$the_media->height; 
                    $awidth=$the_media->width;
                }
            }       
            elseif ($the_media->source=='code'){
                if ($the_media->width == 0 || $the_media->height == 0){
                    $begin_tag = strpos($the_media->code, 'width="');
                    if ($begin_tag !== false){
                        $remaining_code = substr($the_media->code, $begin_tag+7, strlen($the_media->code));
                        $end_tag = strpos($remaining_code, '"');
                        $awidth = substr($remaining_code, 0, $end_tag); 
                        $begin_tag = strpos($the_media->code, 'height="');
                        if ($begin_tag !== false){
                            $remaining_code = substr($the_media->code, $begin_tag+8, strlen($the_media->code));
                            $end_tag = strpos($remaining_code, '"');
                            $aheight = substr($remaining_code, 0, $end_tag);
                            $no_plugin_for_code = 1;
                        }
                        else{
                            $aheight = 20;
                            $awidth = 300;
                        }   
                    }
                    else{
                        $aheight = 20;
                        $awidth = 300;
                    }                           
                }
                else{                   
                    $replace_with = 'width="'.$the_media->width.'"';
                    $the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
                    $replace_with = 'height="'.$the_media->height.'"';
                    $the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);
                    $aheight=$the_media->height; $awidth=$the_media->width;
                }
            }   
        }   
        if ($no_plugin_for_code == 0){
            $media = $guruHelper->create_media_using_plugin($the_media, $config, $aheight, $awidth, $vheight, $vwidth);
        }
        else{
            $media = $the_media->code;
        }
    }
    else{
        $media = '';
    }
}
elseif($config->display_media == 0){
    $media = '';
}   

$public_data = $program->startpublish;
$int_date    = strtotime($public_data);
$data        = date($config->datetype,$int_date);

$view = $this->view->getView("guruPrograms", "html");
$view->setLayout("tabs");
$view->show();

$course_config = json_decode($config->psgpage);
$course_style = json_decode($config->st_psgpage);

$wrap = $course_config->course_wrap_image; //0-yes, 1-no
$img_align = $course_config->course_image_alignment; //0-left, 1-right
$type = $course_config->course_image_size_type == "0" ? "w" : "h";

if(trim($program->image) != ""){
    $array = explode("/", $program->image);
    if(isset($array) && count($array) > 0){
        $program->imageN = $array[count($array)-1];
    }
}

if(trim($program->image) == "" || $program->image == NULL ){
    $program->image = "components/com_guru/images/no_image.gif";
    $guruHelper->createThumb($program->image, "components/com_guru/images", $course_config->course_image_size, $type);
}
else{
    $guruHelper->createThumb($program->imageN, $config->imagesin."/courses", $course_config->course_image_size, $type);
    $program->image = str_replace("thumbs/", "", $program->image);
}

$img_size = getimagesize(JPATH_SITE.DIRECTORY_SEPARATOR.$program->image);
$height = "auto";
if(isset($img_size) && isset($img_size["1"])){
    $height = $img_size["1"];
}
$db = JFactory::getDBO();
$sql = "SELECT currency, currencypos, course_lesson_release  from #__guru_config where id =1";
$db->setQuery($sql);
$db->execute();
$res = $db->loadAssoc();
$amount_students = $guruModelguruProgram->getStudentAmount($program->id);
$class_cover ='guru-cover-details';
$class_wrap = "";
$text = "";

$style = "";
$show_course_image = "0"; //isset($course_config->show_course_image) ? $course_config->show_course_image : "0";

if(trim($program->image != "") && $show_course_image == 0){
    $style = 'style="background-image:url(\''.JURI::root().$program->image.'\'); height: 280px;"';
}

$show_course_price = "";
$show_course_release = "";
$show_course_cert = "";

if ($course_config->course_price == "0") {
    $curent_currency = "GURU_CURRENCY_".$res["currency"];

    if ($course_config->course_price_type == 0 ) {
        $prices = $guruModelguruProgram->getOnlyPricesR($program->id);
    }
    else{
        $prices = $guruModelguruProgram->getOnlyPrices($program->id);
    }


    if(isset($chb_free_courses) && $chb_free_courses == 1) {
		if ($step_access_courses == 2) {

			$text = JText::_("GURU_FREE_GUEST_PRICE");
		}
		elseif ($step_access_courses == 1) {
			if (isset($prices)) {
				if ($res["currencypos"] == '0') {
					if(strpos($prices, "-") !== false){
						$prices = str_replace("-", " - ".JText::_($curent_currency)." ", $prices);
						$text = JText::_($curent_currency)." ".$prices;
					}
					else{
						$text = JText::_($curent_currency)." ".$prices;
					}
				}
				else{
					if(strpos($prices, "-") !== false){
						$prices = str_replace("-", " ".JText::_($curent_currency)." - ", $prices);
						$text = $prices." ".JText::_($curent_currency);
					}
					else{
						$text = $prices." ".JText::_($curent_currency);
					}
				}
			}
		}
		elseif ($step_access_courses == 0 && $selected_course == -1) {
			if (isset($prices)) {
				if ($res["currencypos"] == '0') {
					if(strpos($prices, "-") !== false){
						$prices = str_replace("-", " - ".JText::_($curent_currency)." ", $prices);
						$text = JText::_($curent_currency)." ".$prices." ".JText::_("GURU_FREE_FOR_STUDENTS_AC_PRICE2");
					}
					else{
						$text = JText::_($curent_currency)." ".$prices." ".JText::_("GURU_FREE_FOR_STUDENTS_AC_PRICE2");
					}
				}
				else{
					if(strpos($prices, "-") !== false){
						$prices = str_replace("-", " ".JText::_($curent_currency)." - ", $prices);
						$text = $prices." ".JText::_($curent_currency)." ".JText::_("GURU_FREE_FOR_STUDENTS_AC_PRICE2");
					}
					else{
						$text = $prices." ".JText::_($curent_currency)." ".JText::_("GURU_FREE_FOR_STUDENTS_AC_PRICE2");
					}
				}
			}
		}
		elseif($step_access_courses == 0 && $selected_course != -1) {
			$text = JText::_("GURU_FREE_FOR_STUDENTS_SC_PRICE")." ".$all_title;
			
			if (isset($prices)) {
				if ($res["currencypos"] == '0') {
					if(strpos($prices, "-") !== false){
						$prices = str_replace("-", " - ".JText::_($curent_currency)." ", $prices);
						$text = JText::_($curent_currency)." ".$prices." (".$text.")";
					}
					else{
						$text = JText::_($curent_currency)." ".$prices." (".$text.")";
					}
				}
				else{
					if(strpos($prices, "-") !== false){
						$prices = str_replace("-", " ".JText::_($curent_currency)." - ", $prices);
						$text = $prices." ".JText::_($curent_currency)." (".$text.")";
					}
					else{
						$text = $prices." ".JText::_($curent_currency)." (".$text.")";
					}
				}
			}
			
		}

        if($prices == 0){
            $text = JText::_("GURU_FREE");
        }

        $show_course_price .= '<li><i class="uk-icon-usd wk-icon-usd"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_BUY_PRICE").':</span> '.$text.'</li>';
    }
	else{
        if (isset($prices)) {
            if ($res["currencypos"] == '0') {
                if(strpos($prices, "-") !== false){
					$prices = JText::_($curent_currency)." ".str_replace("-", " - ".JText::_($curent_currency)." ", $prices);
				}
				else{
					$prices = JText::_($curent_currency)." ".$prices;
				}
				
				$show_course_price .= '<li><i class="uk-icon-usd wk-icon-usd"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_BUY_PRICE").':</span> '.$prices."</li>";
            }
            else{
				if(strpos($prices, "-") !== false){
					$prices = str_replace("-", " ".JText::_($curent_currency)." - ", $prices)." ".JText::_($curent_currency);
				}
				else{
					$prices = $prices." ".JText::_($curent_currency);
				}
			
                $show_course_price .= '<li><i class="uk-icon-usd wk-icon-usd"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_BUY_PRICE").':</span> '.$prices."</li>";
            }
        }
    }
}

$res_progr =$guruModelguruProgram->getLessonReleaseType($program->id);
    
if($res["course_lesson_release"] == '0'){
    if($res_progr["course_type"] == 1 && $res_progr["lesson_release"] == 0){
        $show_course_release .= '<li><i class="uk-icon-calendar wk-icon-calendar"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_RELEASED_DATE").':</span> '.JText::_("GURU_ALL_AT_ONCE").'</li>';
    }
    elseif($res_progr["course_type"] == 1 && $res_progr["lesson_release"] == 1){
        $show_course_release .= '<li><i class="uk-icon-calendar wk-icon-calendar"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_RELEASED_DATE").':</span> '.JText::_("GURU_ONE_PER_DAY").'</li>';
    }
    elseif($res_progr["course_type"] == 1 && $res_progr["lesson_release"] == 2){
        $show_course_release .= '<li><i class="uk-icon-calendar wk-icon-calendar"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_RELEASED_DATE").':</span> '.JText::_("GURU_ONE_PER_W").'</li>';
    }
    elseif($res_progr["course_type"] == 1 && $res_progr["lesson_release"] == 3){
        $show_course_release .= '<li><i class="uk-icon-calendar wk-icon-calendar"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_RELEASED_DATE").':</span> '.JText::_("GURU_ONE_PER_M").'</li>';
    }
}

if(isset($certificate_term) && $certificate_term != 0){
    if($certificate_term == 1){
        $show_course_cert .= '<p>'.JText::_("GURU_NO_CERT_GIVEN").'</p>';
    }
    elseif($certificate_term == 2){
        $show_course_cert .= '<p>'.JText::_("GURU_MUST_COLMP_ALL_LESS").'</p>';
    }
    elseif($certificate_term == 3){
        //$show_course_cert .= '<p>'.JText::_("GURU_MUST_PASS_FE")." ".$result_maxs."%</p>";

        $course_id = intval(JFactory::getApplication()->input->get("cid", 0));
        $db = JFactory::getDbo();
        $sql = "select `max_score` from #__guru_quiz where `id`= (select `id_final_exam` from #__guru_program where `id`=".intval($course_id).")";
        $db->setQuery($sql);
        $db->execute();
        $finale_max_score = $db->loadColumn();
        $finale_max_score = @$finale_max_score["0"];

        $show_course_cert .= '<p>'.JText::_("GURU_MUST_PASS_FE")." ".$finale_max_score."%</p>";
    }
    elseif($certificate_term == 4){
        $show_course_cert .= '<p>'.JText::_("GURU_MUST_PASS_QAVG")." ".$avg_cert."%</p>";
    }
    elseif($certificate_term == 5){
        $show_course_cert .= '<p>'.JText::_("GURU_CERT_TERM_FALFE").'</p>';
    }
    elseif($certificate_term == 6){
        $show_course_cert .= '<p>'.JText::_("GURU_CERT_TERM_FALPQAVG")." ".$avg_cert."%</p>";
    }
}
?>

<?php
	$session = JFactory::getSession();
	$registry = $session->get('registry');
	$joomlamessage = $registry->get('joomlamessage', NULL);

    if (isset($joomlamessage)) {
?>
        <div class="uk-alert uk-alert-danger wk-alert wk-alert-danger" id="joomlamessage">
            <?php echo $joomlamessage; ?>
        </div>
<?php 
    }
	
	$registry->set('joomlamessage', NULL);
?>

<!-- Course cover area -->
<div class="uk-cover-background wk-cover-background gru-cover" <?php echo $style; ?> >
    <figure class="uk-overlay uk-width-1-1 wk-overlay wk-width-1-1" style="height: 100%">
        
        <!-- Course title -->
        <figcaption class="uk-overlay-panel uk-overlay-top wk-overlay-panel wk-overlay-top">
        	<?php
            	$program->name = JHtml::_('content.prepare', $program->name);
			?>
            <h2 class="gru-cover-title <?php echo $course_style->course_name; ?>"><?php echo $program->name; ?></h2>
        </figcaption>
        <!-- Course details -->
        <figcaption class="uk-overlay-panel uk-overlay-background uk-overlay-bottom wk-overlay-panel wk-overlay-background wk-overlay-bottom">
            <div class="uk-grid wk-grid gru-cover-details">
                <div class="uk-width-1-1 uk-width-medium-2-3 wk-width-1-1 wk-width-medium-2-3">
                    <ul class="uk-list uk-padding-remove wk-list wk-padding-remove">
                        <?php
                        if ($course_config->show_course_studentamount == "0") {
                            echo '<li><i class="uk-icon-graduation-cap wk-icon-graduation-cap"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_AMOUNT_STUDENTS").':</span> '.$amount_students.'</li>';
                        }

                        if ($course_config->course_author_name_show == "0") {
                            $list_author_name = array();

                            if (isset($author) && count($author) > 0) {
                                foreach ($author as $key=>$value) {
                                    $list_author_name[] = $value->name;
                                }
                            }

                            if (isset($list_author_name) && count($list_author_name) > 0) {
                                echo '<li><i class="uk-icon-user wk-icon-user"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_AUTHOR").':</span> '.implode(", ", $list_author_name).'</li>';
                            }
                        }

                        if($course_config->course_released_date == "0"){
                            echo '<li><i class="uk-icon-calendar wk-icon-calendar"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_RELEASED").':</span> '.$data.'</li>';
                        }
                        
                        if($course_config->course_level == "0"){
                            echo '<li><i class="uk-icon-signal wk-icon-signal"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_LEVEL").':</span> '.$program->level."</li>";
                        }

                        if(isset($course_config->duration) && $course_config->duration == 0){
                            echo '<li><i class="uk-icon-clock-o wk-icon-clock-o"></i> <span class="uk-text-bold wk-text-bold">'.JText::_("GURU_DURATION").':</span> <span id="total-duration-1"></span></li>';
                        }

                        // Show course price
                        echo $show_course_price;

                        // Show course release
                        echo $show_course_release;
                        ?>
                    </ul>
                </div>
                <div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3">
                    <?php
                    	if ($config->course_certificate == "0") {
					?>
                    		<i class="uk-icon-file-text wk-icon-file-text"></i>
                    		<span class="uk-text-bold wk-text-bold"><?php echo JText::_("GURU_CERTIFICATE_COL"); ?>:</span>
                    		<div class="uk-clearfix wk-clearfix"><?php echo $show_course_cert; ?></div>
					<?php
                    	}
					?>
                    
                    <?php
						$user = JFactory::getUser();
                    	$course_id = intval(JFactory::getApplication()->input->get("cid", 0));
						$need_enroll = false;
						$need_buy = true;
						$need_login = false;
						$not_show_button = false;
						
						$sql = "select chb_free_courses, step_access_courses, groups_access, selected_course, free_limit from #__guru_program where id=".intval($course_id);
						$db->setQuery($sql);
						$db->execute();
						$course_access_details = $db->loadAssocList();

                        if($course_access_details["0"]["chb_free_courses"] == 1){// free for
                            $free_limit = $course_access_details["0"]["free_limit"];

                            if(intval($free_limit) > 0){
                                $sql = "select count(*) from #__guru_buy_courses bc, #__guru_order o where o.`status`='Paid' and o.`id`=bc.`order_id` and (bc.`expired_date` >= now() OR bc.`expired_date`='0000-00-00 00:00:00') and bc.`course_id`=".intval($course_id);
                                $db->setQuery($sql);
                                $db->execute();
                                $count_orders = $db->loadColumn();
                                $count_orders = @$count_orders["0"];

                                if(intval($count_orders) >= intval($free_limit)){
                                    $course_access_details["0"]["chb_free_courses"] = 0;
                                }
                            }
                        }
						
						if($course_access_details["0"]["chb_free_courses"] == "1"){
							if($course_access_details["0"]["step_access_courses"] == "1"){
								// members
								if($user->id == 0){
									// not logged
									$need_enroll = false;
									$need_buy = false;
									$need_login = true;
									$not_show_button = false;
								}
								else{
									$groups_access = $course_access_details["0"]["groups_access"];
									$in_groups = $guruModelguruProgram->userInGroups($groups_access);
									
									if(trim($groups_access) == ""){
										$need_enroll = true;
										$need_buy = false;
										$need_login = false;
										$not_show_button = false;
									}
									else{
										if($in_groups){
											$need_enroll = true;
											$need_buy = false;
											$need_login = false;
											$not_show_button = false;
										}
									}
								}
							}
							elseif($course_access_details["0"]["step_access_courses"] == "0"){
								// students
								if($course_access_details["0"]["selected_course"] == "-1"){
									// any course
									if($user->id == 0){
										// not logged
										$need_enroll = false;
										$need_buy = false;
										$need_login = true;
										$not_show_button = false;
									}
									else{
										// user logged
										$is_customer = $guruModelguruProgram->isCustomer();
										$course_temp = array("id"=>$course_id);
										$is_customerfor_course = $guruModelguruProgram->isCustomerForCourse($course_temp);
										
										if($is_customer && !$is_customerfor_course){
											$need_enroll = true;
											$need_buy = false;
											$need_login = false;
											$not_show_button = false;
										}
										elseif($is_customer && $is_customerfor_course){
											$need_enroll = false;
											$need_buy = true;
											$need_login = false;
											$not_show_button = false;
										}
									}
								}
								else{
									// selected courses
									if($user->id == 0){
										// not logged
										$need_enroll = false;
										$need_buy = false;
										$need_login = true;
										$not_show_button = false;
									}
									else{
										$user_courses = $guruModelguruProgram->getUserCourses();
										$selected_course = $course_access_details["0"]["selected_course"];
										$selected_course = explode("|", $selected_course);
										$selected_course = array_filter($selected_course);
										
										if(isset($user_courses) && count($user_courses) > 0){
											$exist = false;
											foreach($user_courses as $key=>$value){
												if(in_array($key, $selected_course)){
													$exist = true;
													break;
												}
											}
											
											if($exist){
												$need_enroll = true;
												$need_buy = false;
												$need_login = false;
												$not_show_button = false;
											}
											else{
												$need_enroll = false;
												$need_buy = true;
												$need_login = false;
												$not_show_button = false;
											}
										}
										else{
											$need_enroll = false;
											$need_buy = true;
											$need_login = false;
											$not_show_button = false;
										}
									}
								}
							}
							elseif($course_access_details["0"]["step_access_courses"] == "2"){
								// free for gues
								if($user->id == 0){
									// not logged
									$need_enroll = false;
									$need_buy = false;
									$need_login = false;
									$not_show_button = true;
								}
								else{
									// logged, need enrol
									$need_enroll = true;
									$need_buy = false;
									$need_login = false;
									$not_show_button = false;
								}
							}
						}
						
						$course_temp = array("id"=>$course_id);
						$is_customerfor_course = $guruModelguruProgram->isCustomerForCourse($course_temp);
						
						if($is_customerfor_course){
							$need_enroll = false;
							$need_buy = false;
							$need_login = false;
							$not_show_button = true;
						}
					?>

                    <div class="gru-cover-purchase-btn">
                    	<?php
                        	$sql = "SELECT chb_free_courses, step_access_courses, selected_course, groups_access FROM `#__guru_program` where id = ".intval($course_id);
							$db->setQuery($sql);
							$db->execute();
							$result= $db->loadAssocList();
							$chb_free_courses = $result["0"]["chb_free_courses"];
							$step_access_courses = $result["0"]["step_access_courses"];
							$selected_course = $result["0"]["selected_course"];
							$members_groups = $result["0"]["groups_access"];
							
							if($chb_free_courses == 1){// free for
								if($step_access_courses == "0"){// students
									if($selected_course == "-1"){// any course
										$user_logged = JFactory::getUser();

										if(!hasAtLeastOneCourse()){
											$sql = "select `price` from #__guru_program_plans where `product_id`=".intval($course_id);
											$db->setQuery($sql);
											$db->execute();
											$prices = $db->loadAssocList();
	
											if(intval($user_logged->id) == 0){
												$need_enroll = false;
												$need_buy = false;
												$need_login = true;
											}
											else{
												$need_enroll = false;
												$need_buy = false;
												$need_login = false;
												
												if(isset($prices) && count($prices) > 0){
													foreach($prices as $key=>$value_price){
														if(intval($value_price["price"]) != 0){
															$need_buy = true;
														}
													}
												}
											}
										}
									}
									elseif(!buySelectedCourse($selected_course)){
										$user_logged = JFactory::getUser();
										
										if(!hasAtLeastOneCourse()){
											$sql = "select `price` from #__guru_program_plans where `product_id`=".intval($course_id);
											$db->setQuery($sql);
											$db->execute();
											$prices = $db->loadAssocList();
	
											if(intval($user_logged->id) == 0){
												$need_enroll = false;
												$need_buy = false;
												$need_login = true;
											}
											else{
												$need_enroll = false;
												$need_buy = false;
												$need_login = false;
												
												if(isset($prices) && count($prices) > 0){
													foreach($prices as $key=>$value_price){
														if(intval($value_price["price"]) != 0){
															$need_buy = true;
														}
													}
												}
											}
										}
									}
								}
								elseif($step_access_courses){// members
									$sql = "select `price` from #__guru_program_plans where `product_id`=".intval($course_id);
									$db->setQuery($sql);
									$db->execute();
									$prices = $db->loadAssocList();
									
									if(isset($members_groups) && trim($members_groups) != ""){
										// selected some groups
										$members_groups_array = explode(",", $members_groups);
										$user_logged = JFactory::getUser();
										
										if(intval($user_logged->id) != 0){
											$intersect_groups = array_intersect($user_logged->groups, $members_groups_array);
											
											if(is_array($intersect_groups) && count($intersect_groups) <= 0){
												$need_enroll = false;
												$need_buy = false;
												$need_login = false;
												
												if(isset($prices) && count($prices) > 0){
													foreach($prices as $key=>$value_price){
														if(intval($value_price["price"]) != 0){
															$need_buy = true;
														}
													}
												}
												
												if(buySelectedCourse($course_id."|")){
													$need_buy = false;
												}
											}
										}
									}
								}
							}
						?>
                    
                        <?php
                        	if($need_enroll){
						?>
                        		<a class="uk-button uk-button-success uk-button-large wk-button wk-button-success wk-button-large" href="#" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruprograms&task=enroll&action=enroll&cid=".$course_id); ?>'; return false;">
                                    <i class="uk-icon-check wk-icon-check"></i>
                                    <?php echo JText::_("GURU_TAKE_COURSE"); ?>
                                </a>
                        <?php
							}
							elseif($need_buy){
						?>
                                <a class="uk-button uk-button-success uk-button-large wk-button wk-button-success wk-button-large" href="<?php echo JURI::root()."index.php?option=com_guru&controller=guruPrograms&task=buy_action&course_id=".$course_id; ?>">
                                    <i class="uk-icon-check wk-icon-check"></i>
                                    <?php echo JText::_("GURU_TAKE_COURSE"); ?>
                                </a>
                        <?php
                        	}
							elseif($need_login){
								$course_lessons = $guruModelguruProgram->find_program_tasks(intval($course_id));
								$lesson_id = 0;
								
								if(isset($course_lessons) && count($course_lessons) > 0){
									$lesson_id = $course_lessons["0"]->id;
								}
						?>
                                <a class="uk-button uk-button-success uk-button-large wk-button wk-button-success wk-button-large" href="#" onclick="openMyModal('0', '0', '<?php echo JURI::root().'index.php?option=com_guru&view=guruLogin&tmpl=component&returnpage=open_lesson&lesson_id='.intval($lesson_id); ?>'); return false;">
                                    <i class="uk-icon-check wk-icon-check"></i>
                                    <?php echo JText::_("GURU_TAKE_COURSE"); ?>
                                </a>
                        <?php
                        	}
							elseif($not_show_button){
								// not show that button
							}
						?>
                    </div>
                </div>
            </div>
        </figcaption>
    </figure>
</div>

<form name="adminForm" id="adminForm" >
    
	<?php
		$show_buy_button =  $course_config->course_buy_button;
    	$buy_button_location =  $course_config->course_buy_button_location;
		$st_psgpage = json_decode($config->st_psgpage);
		$buy_background = $st_psgpage->course_other_background;
		$course_id = JFactory::getApplication()->input->get("cid", "0");
		$buy_class = $st_psgpage->course_other_button;
	
		if($show_buy_button == "0" && ($buy_button_location == "0" || $buy_button_location == "2")){
			$button = createButton($buy_background, $course_id, $buy_class, $program, $programContent);
			
			if(is_array($button)){
				echo $button["0"];
			}
			else{
				echo $button;
			}
		}
	?>
	
	<?php
        ob_start();

        createTabs($program, $author, $programContent, $exercise, $requirements, $courses, $config, $course_config);
        $tabsContent = ob_get_contents();

        ob_end_clean();

        // Show tabs content
        echo $tabsContent;
    ?>
	
    <?php
        if($show_buy_button == "0" && ($buy_button_location == "1" || $buy_button_location == "2")){
            $button = createButton($buy_background, $course_id, $buy_class, $program, $programContent);
			
			if(is_array($button)){
				echo $button["0"];
			}
			else{
				echo $button;
			}
		}

        $lang = JFactory::getLanguage()->getTag();
        $lang = explode("-", $lang);
        $lang = @$lang["0"];
	?>
    
    <input type="hidden" id="course_id" name="course_id" value="<?php echo intval($course_id); ?>" />
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="controller" value="guruPrograms" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" id="modal-lang" value="<?php echo $lang; ?>" />
</form>

<script type="text/javascript">
    function openFullImageGuru(image){
        document.getElementById("full-image").src = image.src;
        document.getElementById("guru-full-image-modal-background").style.display = "block";
        document.getElementById("guru-full-image-modal").style.display = "block";
    }

    function closeFullImageGuru(){
        document.getElementById("guru-full-image-modal-background").style.display = "none";
        document.getElementById("guru-full-image-modal").style.display = "none";
    }
</script>

<style>
    #guru-full-image-modal-background{
        position: fixed;
        top: 0px;
        left: 0px;
        height: 100%;
        width: 100%;
        background-color: #000;
        z-index: 3900000000;
        opacity: 0.5;
        display: none;
    }

    #guru-full-image-modal{
        position: fixed;
        top: 0px;
        right: 0px;
        left: 0px;
        bottom: 0px;
        background-color: #fff;
        z-index: 4900000000;
        padding: 10px;
        text-align: center;
        display: none;
    }
</style>

<div id="guru-full-image-modal-background"></div>
<div id="guru-full-image-modal">
    <div class="guru-lesson-header" style="text-align: right;">
        <a href="#" onclick="closeFullImageGuru();" class="guru-lesson-home" style="line-height: 45px; padding-right: 10px;"><i class="fontello-cancel"></i></a>
    </div>

    <img src="" id="full-image" style="max-width: 100%; max-height: 100%; padding-top: 60px;" />
</div>