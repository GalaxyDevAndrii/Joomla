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

defined('_JEXEC') or die('Restricted access');
$input = JFactory::getApplication()->input;
$menu = JFactory::getApplication()->getMenu()->getActive();
if ($menu && $menu->params->get('guru_show_search_form', 1)) {
    $showForm = true;
} else {
    $showForm = false;
}
?>
<?php if ($showForm) : ?>
    <div class="guru-view-search">
        <form class="guru-view-search-form uk-search uk-search-default" name="guru-view-search-form" action="<?php echo JRoute::_("index.php?option=com_guru&view=gurusearch") ?>" method="get">
            <div class="guru-control-group" style="display: flex;">
                <input class="uk-search-input" type="search" placeholder="<?php echo JText::_("GURU_SEARCH"); ?>" name="search" value="<?php echo $input->getString('search'); ?>" />
                <button type="submit" class="uk-search-icon-flip uk-search-icon uk-icon" uk-search-icon="">
                    <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle>
                        <path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path>
                    </svg>
                </button>
            </div>

            <?php if ($input->getInt('module_id', 0)) : ?>
                <input type="hidden" name="module_id" value="<?php echo $input->getInt('module_id', 0) ?>" />
            <?php endif ?>
        </form>
    </div>
    <br>
<?php endif ?>
<?php
$courses = $this->courses;

if (isset($courses) && count($courses) > 0) {
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root() . 'modules/mod_guru_search/mod_guru_search.css');

    $item_id = JFactory::getApplication()->input->getInt("Itemid", "0");
    $module_id = JFactory::getApplication()->input->getInt("module_id");
    $moduleTable = JTable::getInstance('module');
    $moduleTable->load($module_id);
    $params = new JRegistry;
    $params->loadString($moduleTable->params);

    $lang = JFactory::getLanguage();
    $extension = 'mod_guru_search';
    $base_dir = JPATH_SITE;
    $language_tag = '*';
    $lang->load($extension, $base_dir, $language_tag, true);

    echo '<ul class="uk-list uk-list-line wk-list wk-list-line">';

    foreach ($courses as $key => $course) {
        $helper = new guruHelper();
        $itemid_menu = $helper->getCourseMenuItem(intval($course["id"]));
        $itemid_course = $item_id;

        if (intval($itemid_menu) > 0) {
            $itemid_course = intval($itemid_menu);
        }
?>
        <li class="guru-mod-search_item">
            <?php
            // course thumbnail
            if ($this->showCourseImage($params)) {
                $image_url = $course["image_avatar"];
                $image_url = str_replace("thumbs/", "", $image_url);
                $img_size = array();
                $host = $_SERVER['HTTP_HOST'];
                $myImg = str_replace("http://", "", $image_url);
                $myImg = str_replace($host, "", $myImg);

                if ($myImg != $image_url) {
                    $myImg = str_replace("/", DS . "", $myImg);
                    $img_size = @getimagesize(JPATH_SITE . DS . $myImg);
                } else {
                    $img_size = @getimagesize(urldecode($image_url));
                }

                $width_old = $img_size["0"];
                $height_old = $img_size["1"];

                $width_th = "0";
                $height_th = "0";

                if ($params->get("thumbsizetype", "1") == 0 && isset($img_size)) {
                    if ($width_old > $params->get("thumbsize", "0") && $params->get("thumbsize", "0") > 0) {
                        //proportional by width
                        $raport = $width_old / $height_old;
                        $width_th = $params->get("thumbsize", "0");
                        $height_th = intval($params->get("thumbsize", "0") / $raport);
                        $width_bullet_margin = $params->get("thumbsize", "0");
                    } else {
                        $width_th = $width_old;
                        $height_th = $height_old;
                    }
                } else {
                    if ($height_old > $params->get("thumbsize", "0") && $params->get("thumbsize", "0") > 0) {
                        //proportional by height            
                        $raport = $height_old / $width_old;
                        $height_th = $params->get("thumbsize", "0");
                        $width_th  = intval($params->get("thumbsize", "0") / $raport);
                        $width_bullet_margin = intval($params->get("thumbsize", "0") / $raport);
                    } else {
                        $width_th = $width_old;
                        $height_th = $height_old;
                    }
                }

                if (trim($course["image_avatar"])) {
                    $src =  $this->create_module_thumbnails($image_url, 400, 150, 400, 150);
                    echo '<a class="guru-mod-search_item-thumb" href="' . JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid=' . $course["id"] . "-" . $course["alias"] . "&Itemid=" . intval($itemid_course)) . '"><img src="' . $src . '" alt="" title=""></a>';
                } else {
                    echo '';
                }
            }

            // course title
            echo '<a class="guru-mod-search_item-title" href="' . JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid=' . $course["id"] . "-" . $course["alias"] . "&Itemid=" . intval($itemid_course)) . '">' . $course["name"] . '</a>';

            // course details
            if (($params->get("teachername", "1") == 1) || ($params->get("showamountstud", "1") == 1)) {
                echo '<div class="guru-mod-search_item-details">';
                if ($params->get("teachername", "1") == 1) {
                    $authors_urls = $this->getAuthor($course, $params);

                    echo "<span><i class='uk-icon-user wk-icon-user'></i> " . implode(", ", $authors_urls) . "</span>";
                }

                if ($params->get("showamountstud", "1") == 1) {
                    $nr_students = $this->getStudentsNumber($course, $params);

                    echo "<span><i class='uk-icon-users wk-icon-users'></i>" . " " . $nr_students . " " . JText::_('GURU_MODULE_AMOUNT_STUDENTS_FRONT') . "</span>";
                }
                echo '</div>';
            }

            // course description
            if (($params->get("showdescription", "1") == 1)) {
                echo '<div class="guru-mod-search_item-desc">';
                if ($params->get("showdescription", "1") == 1) {
                    $description = $this->getDescription($course, $params);
                    echo '<p>' . $description . '</p>';
                } else {
                    echo '';
                }
                echo '</div>';
            }
            ?>
        </li>
    <?php
    }

    echo '</ul>';
} else {
    ?>
    <div class="uk-alert-primary uk-alert wk-alert-primary wk-alert" style="text-align: center;"><?php echo JText::_("GURU_NO_RESULTS"); ?></div>
<?php
}
?>