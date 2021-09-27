<?php 
                // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
defined('DS') or define("DS", DIRECTORY_SEPARATOR);
require_once(JPATH_SITE.DS."modules".DS."mod_guru_courses".DS."helper.php");    

    $class = new ModGuruCourses();
    $courses = $class->getCourses($params);
    $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");
    $item_id_home = $class->getHomeMenuItem();
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'modules/mod_guru_courses/mod_guru_courses.css' );

    $modShow = $params->get('show-link');
    $modCat = $params->get('title-category');
    $modMenu = $params->get('link-category');

    $lang = JFactory::getLanguage();
    $dir = $lang->get('rtl');

?>

<?php if($modShow) :?>
<div class="category-action">
    <div class="container">
        <a class="link" href="<?php  echo JRoute::_("index.php?Itemid={$modMenu}"); ?>" title="View More">
            <?php echo $modCat ;?>
            <span class="icon ion-ios-arrow-round-forward"></span>
        </a>
    </div>
</div>
<?php endif ;?>

<div class="guru-mod--courses-wrap">
    <div class="container">
        <div id="guru-mod--courses-<?php echo $module->id; ?>" class="guru-mod--courses courses-grid">
            <div class="uk-list uk-list-line row">
                <?php
                if(isset($courses) && count($courses) > 0){
                    foreach($courses as $key=>$course){
                        $item_id_course = $class->getCourseMenuItem($course["id"]);

                        if(intval($item_id_course) == 0){
                            $item_id_course = $item_id_home;
                        }

                        ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="guru-mod--courses__item">
                                <?php
                                // course thumbnail
                                if($class->showCourseImage($params)){
                                    $image_url = $course["image_avatar"];
                                    $image_url = str_replace("thumbs/", "", $image_url);
                                    $img_size = array();
                                    $host = $_SERVER['HTTP_HOST'];
                                    $myImg = str_replace("http://", "", $image_url);
                                    $myImg = str_replace($host, "", $myImg);
                                    if($myImg != $image_url){
                                        $myImg =str_replace("/", DS."", $myImg);            
                                        $img_size = @getimagesize(JPATH_SITE.DS.$myImg);                    
                                    }
                                    else{
                                        $img_size = @getimagesize(urldecode($image_url));
                                    }

                                    $width_old = $img_size["0"];
                                    $height_old = $img_size["1"];

                                    $width_th = "0";
                                    $height_th = "0";

                                    if($params->get("thumbsizetype", "1") == 0 && isset($img_size)){
                                        if($width_old > $params->get("thumbsize", "0") && $params->get("thumbsize", "0") > 0){
                                    //proportional by width
                                            $raport = $width_old/$height_old;
                                            $width_th = $params->get("thumbsize", "0");
                                            $height_th = intval($params->get("thumbsize", "0") / $raport);
                                            $width_bullet_margin = $params->get("thumbsize", "0");                  
                                        }
                                        else{
                                            $width_th = $width_old;
                                            $height_th = $height_old;                   
                                        }
                                    }
                                    else{
                                        if($height_old > $params->get("thumbsize", "0") && $params->get("thumbsize", "0") > 0){
                                    //proportional by height            
                                            $raport = $height_old/$width_old;
                                            $height_th = $params->get("thumbsize", "0");                        
                                            $width_th  = intval($params->get("thumbsize", "0") / $raport);
                                            $width_bullet_margin = intval($params->get("thumbsize", "0") / $raport);                    
                                        }
                                        else{
                                            $width_th = $width_old;
                                            $height_th = $height_old;                   
                                        }
                                    }

                                    if(trim($course["image_avatar"])){
                                        $src =  $class->create_module_thumbnails($image_url, 400, 150, 400, 150);
                                        echo '<a class="guru-mod--courses__item-thumb" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course["id"]."-".$course["alias"]."&Itemid=".intval($item_id_course)).'"><img src="'.$image_url.'" alt="" title=""></a>';
                                    }

                                    else{
                                        echo '';
                                    }
                                }

                                if($params->get("showcateg", "1") == 1){
                                    $categories_urls = $class->getCategories($course, $params);

                                    echo "<div class='category-course'>".implode(", ", $categories_urls)."</div>";
                                }

                                echo '<a class="guru-mod--courses__item-title h4 heading-link" href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course["id"]."-".$course["alias"]."&Itemid=".intval($item_id_course)).'">'.$course["name"].'</a>';

                                // course details
                                if(($params->get("teachername", "1") == 1)){
                                    echo '<div class="guru-mod--courses__item-details">';
                                        if($params->get("teachername", "1") == 1){
                                            $authors_urls = $class->getAuthor($course, $params);
                                            
                                            if($params->get("showteacherthumb", "1") == 1){
                                                echo "<span>".implode(", ", $authors_urls)."</span>";
                                            }
                                            else{
                                                echo "<span><i class='uk-icon-user'></i> ".implode(", ", $authors_urls)."</span>";
                                            }
                                        }

                                    echo '</div>';
                                }

                                // course description
                                if(($params->get("showdescription", "1") == 1)){
                                    echo '<div class="guru-mod--courses__item-desc">';
                                    if($params->get("showdescription", "1") == 1){
                                        $description = $class->getDescription($course, $params);
                                        echo '<p>'.$description.'</p>';
                                    }
                                    else{
                                        echo '';
                                    }
                                    echo'</div>';
                                }

                                echo '<div class="guru-mod--footer">';
                                    if($params->get("showamountstud", "1") == 1){
                                        $nr_students = $class->getStudentsNumber($course, $params);

                                        echo "<div class='student-info'><i class='fa fa-user'></i>"." ".$nr_students." ".JText::_('GURU_MODULE_AMOUNT_STUDENTS_FRONT')."</div>";
                                    }

                                    echo '<div class="readmore-info"><a href="'.JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$course["id"]."-".$course["alias"]."&Itemid=".intval($item_id_course)).'">'.Jtext::_('TPL_CONTENT_READ_MORE_TITLE').'</a></div>';
                                echo '</div>';
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
