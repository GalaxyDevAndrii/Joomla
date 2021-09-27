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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php');

jimport("joomla.application.component.view");
jimport('joomla.html.parameter');

class guruViewguruSearch extends JViewLegacy
{

    function display($tpl = null)
    {
        $model = $this->getModel("guruSearch");
        $this->courses = $model->getCourses();

        parent::display($tpl);
    }

    function showCourseImage($params)
    {
        if ($params->get("showthumb", "1") == 1) {
            return true;
        }
        return false;
    }

    function getAuthor($course, $params)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $user_id = $user->id;
        $return = array();
        $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");
        require_once(JPATH_SITE . DS . "components" . DS . "com_guru" . DS . 'helpers' . DS . 'helper.php');

        $helper = new guruHelper();
        $itemid_seo = $helper->getSeoItemid();
        $itemid_seo = @$itemid_seo["guruprofile"];

        if (intval($itemid_seo) > 0) {
            $item_id = intval($itemid_seo);

            $sql = "select `access` from #__menu where `id`=" . intval($item_id);
            $db->setQuery($sql);
            $db->execute();
            $access = $db->loadColumn();
            $access = @$access["0"];

            if (intval($access) == 3) {
                // special
                $user_groups = $user->get("groups");
                if (!in_array(8, $user_groups)) {
                    $item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");
                }
            }
        }

        if (intval($item_id) > 0 && intval($user_id) == 0) {
            $sql = "select `access` from #__menu where `id`=" . intval($item_id);
            $db->setQuery($sql);
            $db->execute();
            $access = $db->loadColumn();
            $access = @$access["0"];

            if (intval($access) != "1") {
                $item_id = 0;
            }
        }

        $authors = explode("|", $course["author"]);
        $authors = array_filter($authors);

        $sql = "SELECT `id`, `name` from #__users where `id` in (" . implode(",", $authors) . ")";
        $db->setQuery($sql);
        $db->execute();
        $authors_details = $db->loadAssocList();

        if (isset($authors_details) && count($authors_details) > 0) {
            foreach ($authors_details as $key => $value) {
                $sql = "SELECT `id` from #__guru_authors where `userid`=" . intval($value["id"]);
                $db->setQuery($sql);
                $db->execute();
                $teacher_id = $db->loadColumn();
                $teacher_id = @$teacher_id["0"];

                $item_id_author = $item_id;

                $itemid_menu = $helper->getTeacherMenuItem(intval($teacher_id));

                if (intval($itemid_menu) > 0) {
                    $item_id_author = intval($itemid_menu);
                }

                $url = '<a href="' . JRoute::_('index.php?option=com_guru&view=guruauthor&layout=view&cid=' . intval($teacher_id) . "-" . JFilterOutput::stringURLSafe($value["name"]) . "&Itemid=" . intval($item_id_author)) . '">' . $value["name"] . "</a>";
                $return[] = $url;
            }
        }

        return $return;
    }

    function getStudentsNumber($course, $params)
    {
        $db = JFactory::getDbo();
        $sql = "SELECT count(distinct bc.`userid`) FROM #__guru_buy_courses bc, #__users u , #__guru_customer c, #__guru_order o WHERE c.`id`=bc.`userid` and bc.`userid`=u.`id` and bc.`course_id`=" . intval($course["id"]) . " and o.`userid`=c.`id` and o.`userid`=bc.`userid` and o.`status`='Paid'";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();

        return @$result["0"];
    }

    function getDescription($course, $params)
    {
        $return = "";
        $audio_p_desc_length = $params->get("desclength");
        $audio_p_desc_length_type = $params->get("desclengthtype");
        $description = strip_tags($course["description"]);

        if ($audio_p_desc_length != '' && trim($description) != "") {
            $new_description = "";
            if ($audio_p_desc_length_type == 0) {
                //words
                $desc_array = explode(" ", $description);
                $desc = array();
                if (count($desc_array) > $audio_p_desc_length) {
                    foreach ($desc_array as $key => $val) {
                        if ($key < $audio_p_desc_length) {
                            $desc[] = $val;
                        }
                    }
                    $new_description = implode(" ", $desc) . "...";
                } else {
                    $new_description = $description;
                }
            } elseif ($audio_p_desc_length_type == 1) {
                //characters                            
                $descr_nr = strlen($description);
                if ($descr_nr > $audio_p_desc_length) {
                    $new_description = substr(trim($description), 0, $audio_p_desc_length) . "...";
                } else {
                    $new_description = $description;
                }
            }
            $return = $new_description;
        }
        return $return;
    }

    function create_module_thumbnails($images, $width, $height, $width_old, $height_old)
    {
        $image_path = JURI::root() . $images;

        if (strpos($images, "http") !== FALSE) {
            $image_path = $images;
        }

        $thumb_src = "modules/mod_guru_search/createthumbs.php?src=" . $image_path . "&amp;w=" . $width . "&amp;h=" . $height; //."&zc=1";

        return $thumb_src;
    }
}
