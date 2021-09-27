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
jimport("joomla.aplication.component.model");

require_once(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php');

class guruModelguruProgram extends JModelLegacy
{
    var $_attributes;
    var $_attribute;
    var $_id = null;

    function __construct()
    {
        parent::__construct();
        $cids = JFactory::getApplication()->input->get('cid', 0, "raw");
        $itemid = JFactory::getApplication()->input->get('Itemid', 0, "raw");

        if (!is_array($cids)) {
            $cids = array("0" => intval($cids));
        }

        if (($cids["0"] == 0) && (isset($itemid) && ($itemid != 0))) {
            $db = JFactory::getDBO();
            $sql = "SELECT params from #__menu where id=" . intval($itemid);
            $db->setQuery($sql);
            $params = $db->loadResult();
            $params = json_decode($params);

            if (isset($params->cid) && ($params->cid != "")) {
                $cids[0] = $params->cid;
                JFactory::getApplication()->input->set("cid", $cids[0]);
            }
        }

        $this->setId((int)$cids[0]);
    }

    function setId($id)
    {
        $this->_id = $id;
        $this->_attribute = null;
    }

    function getlistPrograms()
    {
        $catId  = JFactory::getApplication()->input->get("cid", "0", "raw");
        $jnow   = new JDate('now');
        $date   = $jnow->toSQL(true);
        $db     = JFactory::getDBO();
        $result = array();

        if ($catId > 0) {
            $sql = "SELECT name from #__guru_category where id = '" . $catId . "'";
            $db->setQuery($sql);
            $res = $db->loadObject();
            $result['catName'] = $res->name;

            $sql = "SELECT * from #__guru_program where catid = '" . $catId . "'
                    AND published = 1 and startpublish <= '" . $date . "' and (endpublish> '" . $date . "' or endpublish='0000-00-00')";
            $db->setQuery($sql);
            $res = $db->loadObjectList();
            $result['courses'] = $res;
        }
        return $result;
    }

    function getpdays()
    {
        if (isset($_REQUEST['cid'])) {
            $sql = "SELECT * FROM #__guru_days WHERE pid='" . intval($_REQUEST['cid']) . "' ORDER BY ordering ASC";
            $pdays = $this->_getList($sql);
            return $pdays;
        }
        return NULL;
    }

    function get_a_day_by_id($day_id)
    {
        $database = JFactory::getDBO();
        $sql = "SELECT * FROM #__guru_days WHERE id='" . $day_id . "' ";
        $database->setQuery($sql);
        $aday = $database->loadObject();

        return $aday;
    }

    function getProgram()
    {
        $database = JFactory::getDBo();

        if (empty($this->_attribute)) {
            $this->_attribute = $this->getTable("guruPrograms");
            $this->_attribute->load($this->_id);
        }

        $data = JFactory::getApplication()->input->post->getArray();

        if (!$this->_attribute->bind($data)) {
            $this->setError($item->getErrorMsg());
            return false;
        }
        if (!$this->_attribute->check()) {
            $this->setError($item->getErrorMsg());
            return false;
        }

        $sql = "SELECT sum(lt.time) AS course_time 
              FROM #__guru_task as lt 
              LEFT JOIN #__guru_mediarel lm on lt.id=lm.media_id 
              LEFT JOIN #__guru_days as ld on lm.type_id=ld.id
              WHERE type='dtask' and ld.pid=" . $this->_id;

        $database->setQuery($sql);
        $result = $database->loadResult();
        $hours = intval($result / 60);
        $minutes = $result % 60;
        $this->_attribute->duration = $hours . ":" . $minutes;

        switch ($this->_attribute->level) {
            case "0":
                $this->_attribute->level = JText::_('GURU_LEVEL_BEGINER');
                break;
            case "1":
                $this->_attribute->level = JText::_('GURU_LEVEL_INTERMEDIATE');
                break;
            case "2":
                $this->_attribute->level = JText::_('GURU_LEVEL_ADVANCED');
                break;
        }
        return $this->_attribute;
    }

    function getConfigSettings()
    {
        $sql = "SELECT * FROM #__guru_config WHERE id=1";
        $ConfigSettings = $this->_getList($sql);
        return $ConfigSettings[0];
    }

    function getsum_points_and_time()
    {
        $db = JFactory::getDBO();
        $sql = "SELECT id FROM #__guru_days WHERE pid=" . intval(JFactory::getApplication()->input->get("cid", "", "raw"));
        $db->setQuery($sql);
        $temp = $db->loadColumn();

        $sql = "SELECT media_id FROM #__guru_mediarel WHERE type='dtask' AND type_id in (" . implode(',', $temp) . ")";
        $db->setQuery($sql);
        $temp = $db->loadColumn();

        $sql = "SELECT sum(points) as s_points, sum(time) as s_time FROM #__guru_task WHERE id in (" . implode(',', $temp) . ")";
        $sum_points_time = $this->_getList($sql);

        return $sum_points_time;
    }

    function getmyprograms()
    {
        $my = JFactory::getUser();
        $sql = "SELECT ord.*,prog.*,stat.* FROM #__guru_programstatus as stat, #__guru_program as prog
            LEFT JOIN #__guru_order as ord on ord.programid = prog.id
            WHERE (prog.id = stat.pid AND stat.userid = " . $my->id . ")
            AND ord.userid = " . $my->id . "
            GROUP BY prog.id
            ORDER BY prog.id ASC
            ";
        $my_programs = $this->_getList($sql);

        return $my_programs;
    }

    function find_id_for_first_uncompleted_day($day_array)
    {
        $search_id_for_first_day_uncompleted = -1;
        foreach ($day_array as $day_array_key => $day_array_value) {
            if (strpos($day_array_value, ',0') !== FALSE || strpos($day_array_value, ',1') !== FALSE) {
                $search_id_for_first_day_uncompleted = $day_array_key;
                break;
            }
        }

        $search_array_id_for_first_day_uncompleted = explode(',', $day_array[$search_id_for_first_day_uncompleted]);
        $id_for_first_day_uncompleted = $search_array_id_for_first_day_uncompleted[0];

        return $id_for_first_day_uncompleted . ',' . ($search_id_for_first_day_uncompleted + 1);
    }


    function find_id_for_first_uncompleted_task($task_array)
    {
        $search_id_for_first_task_uncompleted = 0;

        foreach ($task_array as $task_array_key => $task_array_value) {
            if (strpos($task_array_value, ',0') !== FALSE || strpos($task_array_value, ',1') !== FALSE) {
                $search_id_for_first_task_uncompleted = $task_array_key;
                break;
            }
        }

        $search_array_id_for_first_task_uncompleted = explode(',', $task_array[$search_id_for_first_task_uncompleted]);
        $id_for_first_task_uncompleted = $search_array_id_for_first_task_uncompleted[0];
        return $id_for_first_task_uncompleted . ',' . ($search_id_for_first_task_uncompleted + 1);
    }

    function find_day_status($userid, $progid, $dayid)
    {
        $database = JFactory::getDBO();
        $sql = "SELECT days FROM #__guru_programstatus WHERE pid =" . $progid . " AND userid = " . $userid;
        $database->setQuery($sql);
        $result = $database->loadObjectList();

        if (isset($result[0])) {
            $day_array = $result[0]->days;
            $day_array = explode(';', $day_array);
        }
        $status = 0;
        if (isset($result[0]))
            foreach ($day_array as $day_value) {
                $day_value_array = explode(',', $day_value);
                if ($day_value_array[0] == $dayid)
                    $status = $day_value_array[1];
            }
        return $status;
    }

    function find_program_tasks($progid)
    {
        $database = JFactory::getDBO();
        $sql = "SELECT * FROM #__guru_task WHERE id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (SELECT id FROM #__guru_days WHERE pid =" . $progid . ") ) ";
        $database->setQuery($sql);
        $result = $database->loadObjectList();
        return $result;
    }


    function find_program_days($progid)
    {
        $database = JFactory::getDBO();
        $sql = "SELECT id, ordering,title FROM #__guru_days WHERE pid =" . $progid . " ORDER BY ordering ASC";
        $database->setQuery($sql);
        $result = $database->loadObjectList();
        return $result;
    }

    function find_day_tasks($dayid)
    {
        $database = JFactory::getDBO();
        $sql = "SELECT * FROM #__guru_task WHERE id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id = " . intval($dayid) . ")";
        $database->setQuery($sql);
        $result = $database->loadObjectList();
        return $result;
    }

    function find_intro_media($progid)
    {
        $database = JFactory::getDBO();
        $sql = "SELECT * FROM #__guru_media WHERE id = (SELECT media_id FROM #__guru_mediarel WHERE type = 'pmed' AND type_id = " . intval($progid) . " LIMIT 1)";
        $database->setQuery($sql);
        $result = $database->loadObject();
        return $result;
    }


    function find_if_rogram_was_bought($userid, $progid)
    {
        // returns 1 if the program is already bought
        // return 0 if the program is TRIAL or wasn't bought
        $database = JFactory::getDBO();
        $sql = "SELECT payment FROM #__guru_order WHERE userid = '" . $userid . "' AND programid = " . $progid;
        $database->setQuery($sql);
        $result = $database->loadResult();
        if (strtolower($result) == 'trial' || !isset($result))
            return 0;
        else return 1;
    }

    function find_status_line_for_program($userid, $progid)
    {
        $database = JFactory::getDBO();
        $sql = "SELECT days,tasks FROM #__guru_programstatus WHERE userid = '" . $userid . "' AND pid = " . $progid;
        $database->setQuery($sql);
        $result = $database->loadObject();
        return $result;
    }

    function find_link_text_for_day_resume_button($day_array, $task_array, $status)
    {
        //$day_array = $status_line->days;
        $day_array = explode(';', $day_array);
        $how_many_days = count($day_array) - 1;
        $day_id_to_get_started_array = explode(',', $day_array[0]);

        //$task_array = $status_line->tasks;
        $task_array = explode(';', $task_array);
        $task_id_array = explode('-', $task_array[0]);
        $task_id_to_get_started_array = explode(',', $task_id_array[0]);

        // we find the id for the first day who isn't completed


        if ($status == '1') {
            $first_day_uncompleted = guruModelguruProgram::find_id_for_first_uncompleted_day($day_array);
            $first_day_uncompleted = explode(',', $first_day_uncompleted);
            $id_for_first_day_uncompleted = $first_day_uncompleted[0];
            $ordering_for_first_day_uncompleted = $first_day_uncompleted[1];

            $first_task_uncompleted = guruModelguruProgram::find_id_for_first_uncompleted_task(explode('-', $task_array[($ordering_for_first_day_uncompleted - 1)]));
            $first_task_uncompleted = explode(',', $first_task_uncompleted);
            $id_for_first_task_uncompleted = $first_task_uncompleted[0];
            $ordering_for_first_task_uncompleted = $first_task_uncompleted[1];
        }

        if ($status == '0' && isset($task_id_to_get_started_array[0]) && $task_id_to_get_started_array[0] > 0 && isset($day_id_to_get_started_array[0]) && $day_id_to_get_started_array[0] > 0) {
            $link_for_resume =  'index.php?option=com_guru&view=guruTasks&task=view&cid=1&pid=' . $day_id_to_get_started_array[0];
            $text_for_resume = JText::_('GURU_MYPROGRAMS_ACTION_GETSTARTED');
        }

        if ($status == '1' && isset($id_for_first_task_uncompleted) && $id_for_first_task_uncompleted > 0 && isset($id_for_first_day_uncompleted) && $id_for_first_day_uncompleted > 0) {
            $link_for_resume =  'index.php?option=com_guru&view=guruTasks&task=view&cid=' . $ordering_for_first_task_uncompleted . '&pid=' . $id_for_first_day_uncompleted;
            $text_for_resume = JText::_('GURU_DAYS_RESUME_BUTTON');
        }
        if ($status == '2' && isset($task_id_to_get_started_array[0]) && $task_id_to_get_started_array[0] > 0 && isset($day_id_to_get_started_array[0]) && $day_id_to_get_started_array[0] > 0) {
            $link_for_resume = 'index.php?option=com_guru&view=guruTasks&task=view&cid=1&pid=' . $day_id_to_get_started_array[0] . '&s=0';
            $text_for_resume = JText::_('GURU_MYPROGRAMS_ACTION_STARTAGAIN');
        }
        if ($status == '-1') {
            $the_day_id = $day_id_to_get_started_array[0];

            $db = JFactory::getDBO();
            $sql = "SELECT pid FROM #__guru_days 
                WHERE id = " . $the_day_id;
            $db->setQuery($sql);
            $result = $db->loadResult();

            $link_for_resume = 'index.php?option=com_guru&view=guruProfile&task=buy&cid=' . $result;
            $text_for_resume = JText::_('GURU_MYPROGRAMS_ACTION_BUYAGAIN');
        }

        return $link_for_resume . '$$$$$' . $text_for_resume;
    }

    function program_status($userid, $progid)
    {
        $database = JFactory::getDBO();
        $sql = "SELECT status FROM #__guru_programstatus WHERE userid = '" . $userid . "' AND pid = " . $progid;
        $database->setQuery($sql);
        $result = $database->loadResult();
        return $result;
    }

    function getSubCategory($id)
    {
        $db = JFactory::getDbo();
        $userid = JFactory::getUser()->id;
        $now = new JDate('now', JFactory::getConfig()->get('offset'));
        $date = $now->format('Y-m-d') . " 23:59:59";

        $sql = "SELECT 
                    t.name, 
                    t.alias, 
                    t.time, 
                    t.id, 
                    t.groups_access, 
                    t.duration, 
                    t.ordering, 
                    t.step_access, 
                    t.difficultylevel, 
                    t.show_view_status, 
                    p.chb_free_courses, 
                    p.step_access_courses, 
                    p.lessons_show, 
                    p.selected_course, 
                    lt.viewed_time
                FROM `#__guru_task` t 
                LEFT JOIN `#__guru_mediarel` m ON m.media_id=t.id
                LEFT JOIN `#__guru_viewed_lesson_time` lt ON t.id = lt.lesson_id AND lt.user_id= '$userid'
                LEFT JOIN `#__guru_days` d ON m.type_id=d.id
                LEFT JOIN `#__guru_program` p ON d.pid = p.id
                WHERE d.id= '$id' 
                AND m.type='dtask'
                AND t.published=1 
                AND t.startpublish <= '$date'  
                AND (t.endpublish = '0000-00-00 00:00:00' OR t.endpublish >= '$date') 
                GROUP BY t.id
                ORDER BY t.ordering ASC ";

        $db->setQuery($sql);
        $rows = $db->loadAssocList();

        return $rows;
    }

    function getProgramContent()
    {
        $database = JFactory::getDBO();
        $sql = "select id, title, alias from #__guru_days 
                WHERE pid=" . intval($this->_attribute->id) . " ORDER BY ordering";

        $database->setQuery($sql);
        $rows = $database->loadAssocList();
        return $rows;
    }



    function getReqCourses()
    {
        $result = array();
        $database = JFactory::getDBO();
        $sql = "SELECT lp.id, name from #__guru_program as lp
                LEFT JOIN #__guru_mediarel as lm 
                on lp.id=lm.media_id
                where type_id=" . intval($this->_attribute->id) . " and lm.type='preq'";
        $database->setQuery($sql);
        $rows = $database->loadAssocList();

        if (isset($rows) && count($rows) > 0) {
            foreach ($rows as $key => $value) {
                $result[] = '<a href="index.php?option=com_guru&view=guruPrograms&task=view&cid=' . $value["id"] . '">' . $value["name"] . "</a>";
            }
        }
        return $result;
    }

    function getAuthor()
    {
        if (empty($this->_attribute)) {
            $this->_attribute = $this->getTable("guruPrograms");
            $this->_attribute->load($this->_id);
        }

        $authors = explode("|", $this->_attribute->author);
        $authors = array_filter($authors);
        if (count($authors) <= 0) {
            $authors = array("0" => "0");
        }

        $db = JFactory::getDBO();
        $sql = "SELECT * FROM #__users u LEFT JOIN #__guru_authors a ON u.id=a.userid WHERE a.userid in (" . implode(",", $authors) . ")";
        $db->setQuery($sql);
        $author = $db->loadObjectList();

        if (isset($author->images) && trim($author->images) != "") {
            $path = explode("/", $author->images);
            $author->imageName = $path[count($path) - 1];
        }

        return $author;
    }

    function getExercise()
    {
        $db = JFactory::getDBO();
        $sql = "SELECT lm.*, lmr.access as access,type_id
                FROM #__guru_media as lm
                LEFT JOIN #__guru_mediarel as lmr
                ON lm.id=lmr.media_id
                WHERE lmr.type_id =" . intval($this->_attribute->id) . "
                AND lm.published = 1 and lmr.type='pmed' order by lmr.order ";
        $db->setQuery($sql);
        $result = $db->loadObjectList();

        for ($i = 0; $i < count($result); $i++) {
            switch ($result[$i]->type) {
                case "video":
                    if ($result[$i]->width == 0 && $result[$i]->height == 0) {
                        $result[$i]->width = 400;
                        $result[$i]->height = 300;
                    } else {
                        $result[$i]->width += 50;
                        $result[$i]->height += 50;
                    }
                    break;
                case "audio":
                    if ($result[$i]->width == 0) {
                        $result[$i]->width = 400;
                    } else $result[$i]->width += 50;
                    $result[$i]->height = 300;
                    break;
                case "docs":
                    if ($result[$i]->width == 0 && $result[$i]->height == 0) {
                        $result[$i]->width = 400;
                        $result[$i]->height = 300;
                    } else {
                        $result[$i]->width += 50;
                        $result[$i]->height += 50;
                    }
                    break;
                case "image":
                    $img_size = @getimagesize(JPATH_SITE . DIRECTORY_SEPARATOR . $config->imagesin . '/' . $result[$i]->local);
                    if ($result[$i]->height > 0) {
                        $result[$i]->width = $img_size[0] + 50;
                    } else {
                        $result[$i]->height = $img_size[1] + 50;
                    }
                    break;
                default:
                    $result[$i]->width = 400;
                    $result[$i]->height = 300;
                    break;
            }
        }

        return $result;
    }

    function getAuthorCourses()
    {
        $db = JFactory::getDBO();

        $jnow   = new JDate('now');
        $date   = $jnow->toSQL(true);

        $sql = "SELECT lp.id, lp.name, lp.startpublish, sum(lt.time) AS course_time,
            CASE lp.level WHEN 0 THEN 'beginner_level'
                       WHEN 1 THEN 'intermediate_level'
                       WHEN 2 THEN 'advanced_level'
            END 
            AS level
            FROM #__guru_program as lp
            LEFT JOIN #__guru_days as ld on lp.id=ld.pid  
            LEFT JOIN #__guru_mediarel as lm on ld.id=lm.type_id 
            LEFT JOIN #__guru_task as lt on lt.id=lm.media_id 
            WHERE lp.author=" . intval($this->_attribute->author) . "  
            AND lp.published=1 
            AND lp.startpublish<='" . $date . "' 
            AND (lp.endpublish>'" . $date . "' or lp.endpublish='0000-00-00 00:00:00')
            GROUP BY lp.id, lp.name, lp.startpublish, lp.level";
        $db->setQuery($sql);
        $courses = $db->loadObjectList();
        return $courses;
    }

    function getAuthorCoursesById($author)
    {
        $db = JFactory::getDBO();
        $jnow   = new JDate('now');
        $date   = $jnow->toSQL(true);

        $sql = "SELECT lp.id, lp.name, lp.startpublish, sum(lt.time) AS course_time,
            CASE lp.level WHEN 0 THEN 'beginner_level'
                       WHEN 1 THEN 'intermediate_level'
                       WHEN 2 THEN 'advanced_level'
            END 
            AS level
            FROM #__guru_program as lp
            LEFT JOIN #__guru_days as ld on lp.id=ld.pid  
            LEFT JOIN #__guru_mediarel as lm on ld.id=lm.type_id 
            LEFT JOIN #__guru_task as lt on lt.id=lm.media_id 
            WHERE (lp.author=" . intval($author->userid) . " OR lp.author like '%|" . intval($author->userid) . "|%')
            AND lp.published=1 
            AND lp.startpublish<='" . $date . "' 
            AND (lp.endpublish>'" . $date . "' or lp.endpublish='0000-00-00 00:00:00')
            GROUP BY lp.id, lp.name, lp.startpublish, lp.level";

        $db->setQuery($sql);
        $courses = $db->loadObjectList();
        return $courses;
    }

    function getFreeForGroups($id)
    {
        $db = JFactory::getDBO();
        $sql = "select name from #__core_acl_aro_groups where id=" . intval($id);
        $db->setQuery($sql);
        $result = $db->loadResult();
        return $result;
    }

    function getPrices($id)
    {
        $db = JFactory::getDBO();
        $sql = "select s.name, pp.price from #__guru_program p left join #__guru_program_plans pp on p.id=pp.product_id left join #__guru_subplan s on s.id=pp.plan_id where p.id=" . intval($id) . " order by s.ordering asc";
        $db->setQuery($sql);
        $result = $db->loadAssocList();
        return $result;
    }

    function getUserCourses()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $user_id = $user->id;
        $sql = "select bc.course_id from #__guru_buy_courses bc inner join #__guru_order o on bc.order_id=o.id where bc.userid=" . intval($user_id) . " and o.status='Paid' and (bc.`expired_date` >= now() or bc.`expired_date`='0000-00-00 00:00:00')";
        $db->setQuery($sql);
        $result = $db->loadAssocList("course_id");

        return $result;
    }

    function isCustomer()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $user_id = $user->id;

        $sql = "select count(*) from #__guru_buy_courses bc, #__guru_customer c, #__guru_order o where bc.userid=" . intval($user_id) . " and c.id=" . intval($user_id) . " and o.`id`=bc.`order_id` and o.`status`='Paid'";
        $db->setQuery($sql);
        $result = $db->loadResult();
        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }

    function isCustomerForCourse($program)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $user_id = $user->id;
        $program = (array)$program;

        if (intval($user_id) > 0) {
            $sql = "select count(*) from #__guru_buy_courses bc inner join #__guru_order o on bc.order_id=o.id where bc.userid=" . intval($user_id) . " and bc.course_id=" . intval($program["id"]) . " and o.status='Paid' and (bc.expired_date = '0000-00-00 00:00:00' OR (bc.expired_date <> '0000-00-00 00:00:00' and bc.expired_date >= now()) )";
            $db->setQuery($sql);
            $result = $db->loadResult();

            if ($result > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function hasAtLeastOneCourse()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $user_id = $user->id;
        $course_id = intval(JFactory::getApplication()->input->get("cid", 0, "raw"));
        $sql = "SELECT count(*) FROM #__guru_buy_courses where userid=" . intval($user_id) . " and course_id <>" . $course_id;
        $db->setQuery($sql);
        $result = $db->loadResult();
        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getOnlyPrices($id)
    {
        $db = JFactory::getDBO();
        $sql = "select min(pp.price) as min_price, max(pp.price) as max_price from #__guru_program p, #__guru_program_plans pp WHERE p.id=pp.product_id and pp.product_id=" . $id;
        $db->setQuery($sql);
        $result = $db->loadAssocList();
        $price = 0;

        $min = number_format($result["0"]["min_price"], 2);
        $max = number_format($result["0"]["max_price"], 2);

        $min_array = explode(".", $min);
        $max_array = explode(".", $max);

        if (isset($min_array["1"]) && $min_array["1"] == "00") {
            $min = $min_array["0"];
        }

        if (isset($max_array["1"]) && $max_array["1"] == "00") {
            $max = $max_array["0"];
        }

        $guruHelper = new guruHelper();
        $min = $guruHelper->displayPrice($min);
        $max = $guruHelper->displayPrice($max);

        if ($min != $max) {
            $price = $min . "-" . $max;
        } else {
            $price = $min;
        }

        return $price;
    }

    function getOnlyPricesR($id)
    {
        $db = JFactory::getDBO();
        $sql = "select min(pp.price) from #__guru_program p, #__guru_program_plans pp WHERE p.id=pp.product_id and pp.product_id=" . intval($id);
        $db->setQuery($sql);
        $result = $db->loadResult();
        //$result = number_format($result, 2);
        $result_array = explode(".", $result);

        if (isset($result_array["1"]) && ($result_array["1"] == "0" || $result_array["1"] == "00")) {
            $result = $result_array["0"];
        }

        $guruHelper = new guruHelper();
        $result = $guruHelper->displayPrice($result);
        return $result;
    }

    function getLessonReleaseType($id)
    {
        $db = JFactory::getDBO();
        $sql = "select lesson_release, course_type  from #__guru_program where id=" . $id;
        $db->setQuery($sql);
        $result = $db->loadAssoc();
        return $result;
    }
    function getStudentAmount($id)
    {
        $db = JFactory::getDBO();
        $sql = "SELECT count(distinct bc.userid) FROM #__guru_buy_courses bc, #__users u , #__guru_customer c, #__guru_order o WHERE c.id=bc.userid and bc.userid=u.id and bc.course_id=" . intval($id) . " and o.userid=c.id and o.userid=bc.userid and o.status='Paid'";
        $db->setQuery($sql);
        return $db->loadResult();
    }

    public static function getCourseTypeDetails($id)
    {
        $db = JFactory::getDBO();
        $sql = "select DATE_FORMAT(p.start_release, '%Y-%m-%d') as start_release, p.course_type, p.lesson_release,  p.lessons_show, p.after_hours from #__guru_program p WHERE p.id=" . intval($id);
        $db->setQuery($sql);
        $result = $db->loadAssocList();
        return $result;
    }

    function enroll()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $user_id = $user->id;
        $course_id = intval(JFactory::getApplication()->input->get("cid", "0", "raw"));
        if ($course_id == 0) {
            $course_id = intval(JFactory::getApplication()->input->get("course_id", "0", "raw"));
        }
        $courses = intval($course_id) . "-0.0-1";
        $amount = 0;
        //$buy_date = date("Y-m-d H:i:s");

        $timezone = new DateTimeZone(JFactory::getConfig()->get('offset'));
        $jnow = new JDate('now');
        $jnow->setTimezone($timezone);
        $buy_date = $jnow->toSQL(true);

        $plan_id = "1";
        $order_expiration = "0000-00-00 00:00:00";
        $jnow = new JDate('now');
        $current_date_string = $jnow->toSQL(true);

        $sql = "select count(*) from #__guru_customer  where id=" . intval($user_id);
        $db->setQuery($sql);
        $result = $db->loadResult();

        if ($result > 0) {
            $iscustomer =  true;
        } else {
            $iscustomer = false;
        }

        $sql = "SELECT name, author, chb_free_courses, step_access_courses, selected_course 
            FROM `#__guru_program` 
            where id = " . intval($course_id);
        $db->setQuery($sql);
        $result = $db->loadAssocList();

        $course_name = $result["0"]["name"];
        $course_authors = $result["0"]["author"];
        $chb_free_courses = $result["0"]["chb_free_courses"];
        $step_access_courses = $result["0"]["step_access_courses"];
        $selected_course = $result["0"]["selected_course"];

        if ($chb_free_courses == 1) {
            if ($step_access_courses == 1) {
                $iscustomer =  true;
            } elseif ($step_access_courses == 0 && $selected_course == -1) {
                if ($this->hasAtLeastOneCourse() && $this->isCustomer()) {
                    $iscustomer =  true;
                } else {
                    $iscustomer =  false;
                }
            }
        }

        $temp = explode(" ", $user->name);
        if (isset($temp) && count($temp) > 1) {
            $last_name = $temp[count($temp) - 1];
            unset($temp[count($temp) - 1]);
            $first_name = implode(" ", $temp);
        } else {
            if (count($temp) == 1) {
                $first_name = $user->name;
                $last_name  = $user->name;
            }
        }

        $sql = "SELECT count(id) FROM #__guru_customer where id=" . intval($user_id);
        $db->setQuery($sql);
        $count = $db->loadResult();

        if ($count == 0) {
            $sql = "INSERT INTO #__guru_customer(id,company, firstname, lastname) VALUES ('" . intval($user_id) . "','','" . addslashes(trim($first_name)) . "','" . addslashes(trim($last_name)) . "')";
            $db->setQuery($sql);
            $db->execute();
        } else {
            $sql = "select `firstname`, `lastname` from #__guru_customer where `id`=" . intval($user_id);
            $db->setQuery($sql);
            $fn_ln = $db->loadAssocList();

            if (isset($fn_ln["0"]["firstname"]) && trim($fn_ln["0"]["firstname"]) != "") {
                $first_name = trim($fn_ln["0"]["firstname"]);
            }

            if (isset($fn_ln["0"]["lastname"]) && trim($fn_ln["0"]["lastname"]) != "") {
                $last_name = trim($fn_ln["0"]["lastname"]);
            }
        }

        //$sql = "select count(*) from #__guru_buy_courses where order_id <> 0 and userid=".intval($user_id)." and course_id=".intval($course_id)." and expired_date < '".$current_date_string."'";
        $sql = "select count(*) from #__guru_buy_courses bc, #__guru_order o where bc.order_id <> 0 and bc.userid=" . intval($user_id) . " and bc.course_id=" . intval($course_id) . " and bc.expired_date < '" . $current_date_string . "' and bc.order_id=o.`id` and o.`status`='Paid'";
        $db->setQuery($sql);
        $result = $db->loadResult();

        if (($result == 0) && $iscustomer == true) { // add a new license
            if (isset($selected_course)) {
                $final_max_expire_date = "";
                $final_plan_id = "";
                $selected_course_array = explode("|", $selected_course);

                if (isset($selected_course_array) && is_array($selected_course_array)) {
                    foreach ($selected_course_array as $key_selected => $id_selected) {
                        if (intval($id_selected) > 0) {
                            $sql = "select max(`expired_date`) from #__guru_buy_courses bc, #__guru_order o where bc.order_id <> 0 and bc.userid=" . intval($user_id) . " and bc.course_id=" . intval($id_selected) . " and bc.order_id=o.`id` and o.`status`='Paid'";
                            $db->setQuery($sql);
                            $max_expired_date = $db->loadResult();

                            if ($final_max_expire_date == "" && trim($max_expired_date) != "") {
                                $final_max_expire_date = $max_expired_date;

                                $sql = "select bc.`plan_id` from #__guru_buy_courses bc where bc.order_id <> 0 and bc.userid=" . intval($user_id) . " and bc.course_id=" . intval($id_selected) . " and bc.`expired_date`='" . $final_max_expire_date . "'";
                                $db->setQuery($sql);
                                $max_plan_id = $db->loadResult();

                                if (intval($max_plan_id) > 0) {
                                    $final_plan_id = intval($max_plan_id);
                                }
                            } elseif (trim($max_expired_date) != "" && strtotime($max_expired_date) > strtotime($final_max_expire_date)) {
                                $final_max_expire_date = $max_expired_date;

                                $sql = "select bc.`plan_id` from #__guru_buy_courses bc where bc.order_id <> 0 and bc.userid=" . intval($user_id) . " and bc.course_id=" . intval($id_selected) . " and bc.`expired_date`='" . $final_max_expire_date . "'";
                                $db->setQuery($sql);
                                $max_plan_id = $db->loadResult();

                                if (intval($max_plan_id) > 0) {
                                    $final_plan_id = intval($max_plan_id);
                                }
                            }
                        }
                    }
                }

                if (trim($final_max_expire_date) != "") {
                    $order_expiration = $final_max_expire_date;
                }

                if (intval($final_plan_id) > 0) {
                    $plan_id = $final_plan_id;
                }
            }

            $sql = "select currency from #__guru_config where id=1";
            $db->setQuery($sql);
            $currency = $db->loadResult();

            $sql = "insert into #__guru_order (userid, order_date, courses, status, amount, amount_paid, processor, number_of_licenses, currency, promocodeid, published, form) values (" . intval($user_id) . ", '" . $buy_date . "', '" . intval($course_id) . "-0-1', 'Paid', '0', '-1','paypaypal','0','" . $currency . "','0','1', '')";
            $db->setQuery($sql);
            $db->execute();

            $sql = "select MAX(id) from #__guru_order";
            $db->setQuery($sql);
            $max_id = $db->loadResult();

            $sql = "insert into #__guru_buy_courses (userid, order_id, course_id, price, buy_date, expired_date, plan_id, email_send) values (" . $user_id . ", " . $max_id . " , " . $course_id . ", '" . $amount . "', '" . $buy_date . "', '" . $order_expiration . "', '" . $plan_id . "', 0)";
            $db->setQuery($sql);
            $db->execute();

            // send teacher and admin email for new student enrolled
            $this->emailForNewStudentEnrolled($course_id, $course_name, $course_authors, $first_name, $last_name, $user);

            //-----------------send email on purchase
            require_once(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cronjobs.php');
            sendEmailOnPurcase($course_id, $max_id, $order_expiration, $plan_id);
            //-----------------send email on purchase

            return 'now';
        } else {
            if ($iscustomer) {
                $sql = "update #__guru_buy_courses set expired_date = '" . $order_expiration . "', plan_id = 1 where userid=" . intval($user_id) . " and course_id=" . intval($course_id) . " and order_id=0 ";
                $db->setQuery($sql);
                $db->execute();

                return 'old';
            }
        }
    }

    function emailForNewStudentEnrolled($course_id, $course_name, $course_authors, $first_name, $last_name, $user)
    {
        $db = JFactory::getDbo();
        $email = $user->email;
        $itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

        $helper = new guruHelper();
        $itemid_seo = $helper->getSeoItemid();
        $itemid_seo = @$itemid_seo["guruprograms"];

        if (intval($itemid_seo) > 0) {
            $itemid = intval($itemid_seo);
        } else {
            $helper = new guruHelper();
            $itemid_menu = $helper->getCourseMenuItem(intval($course_id));

            if (intval($itemid_menu) > 0) {
                $itemid = intval($itemid_menu);
            }
        }

        $sql = "select template_emails from #__guru_config limit 0, 1";
        $db->setQuery($sql);
        $template_emails = $db->loadResult();
        $template_emails = json_decode($template_emails, true);

        $new_student_enrolled_subject = $template_emails["new_student_enrolled_subject"];
        $new_student_enrolled_body = $template_emails["new_student_enrolled_body"];

        $course_link = '<a href="' . JRoute::_("index.php?option=com_guru&view=guruPrograms&task=view&cid=" . intval($course_id) . "&Itemid=" . intval($itemid), true, -1) . '">' . $course_name . '</a>';

        $new_student_enrolled_subject = str_replace('[STUDENT_FIRST_NAME]', $first_name, $new_student_enrolled_subject);
        $new_student_enrolled_subject = str_replace('[STUDENT_LAST_NAME]', $last_name, $new_student_enrolled_subject);
        $new_student_enrolled_subject = str_replace('[STUDENT_EMAIL]', $email, $new_student_enrolled_subject);
        $new_student_enrolled_subject = str_replace('[COURSE_NAME]', $course_link, $new_student_enrolled_subject);

        $new_student_enrolled_body = str_replace('[STUDENT_FIRST_NAME]', $first_name, $new_student_enrolled_body);
        $new_student_enrolled_body = str_replace('[STUDENT_LAST_NAME]', $last_name, $new_student_enrolled_body);
        $new_student_enrolled_body = str_replace('[STUDENT_EMAIL]', $email, $new_student_enrolled_body);
        $new_student_enrolled_body = str_replace('[COURSE_NAME]', $course_link, $new_student_enrolled_body);

        $query = "SELECT * FROM `#__guru_fields` WHERE published = 1";
        $fields = $db->setQuery($query)->loadObjectList();
        $studentData = array();
        foreach ($fields as $field) {
            $query = "SELECT `value` 
                FROM `#__guru_fields_results` 
                WHERE user_id = {$db->q($user->id)}
                AND field_id = {$db->q($field->id)}";

            $value = $db->setQuery($query)->loadResult();
            $item = new stdClass;
            $item->field_code = $field->field_code;
            $item->field_value = $value;
            $studentData[] = $item;
        }
        
        foreach ($studentData as $item) {
            $new_student_enrolled_body = str_replace("[{$item->field_code}]", $item->field_value, $new_student_enrolled_body);
        }

        $config = new JConfig();
        $from = $config->mailfrom;
        $fromname = $config->fromname;

        // send email to authors
        $authors = explode("|", $course_authors);
        if (isset($authors) && count($authors) > 0) {
            foreach ($authors as $key => $author_id) {
                if (intval($author_id) != 0) {
                    $sql = "select email from #__users where id=" . intval($author_id);
                    $db->setQuery($sql);
                    $authorEmail = $db->loadResult();
                    $recipient = array($authorEmail);

                    $send_teacher_email_student_enrolled = isset($template_emails["send_teacher_email_student_enrolled"]) ? $template_emails["send_teacher_email_student_enrolled"] : 1;

                    if ($send_teacher_email_student_enrolled) {
                        JFactory::getMailer()->sendMail($from, $fromname, $recipient, $new_student_enrolled_subject, $new_student_enrolled_body, true);
                    }

                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $query->clear();
                    $query->insert('#__guru_logs');
                    $query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type')));
                    $query->values(intval($author_id) . ',' . $db->quote('to-author-student-enrolled') . ',' . '0' . ',' . $db->quote(trim($email)) . ',' . $db->quote(trim($new_student_enrolled_subject)) . ',' . $db->quote(trim($new_student_enrolled_body)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''");
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }

        // send email to admins
        $new_student_enrolled_admin_subject = $template_emails["new_student_enrolled_admin_subject"];
        $new_student_enrolled_admin_body = $template_emails["new_student_enrolled_admin_body"];

        $new_student_enrolled_admin_subject = str_replace('[STUDENT_FIRST_NAME]', $first_name, $new_student_enrolled_admin_subject);
        $new_student_enrolled_admin_subject = str_replace('[STUDENT_LAST_NAME]', $last_name, $new_student_enrolled_admin_subject);
        $new_student_enrolled_admin_subject = str_replace('[STUDENT_EMAIL]', $email, $new_student_enrolled_admin_subject);
        $new_student_enrolled_admin_subject = str_replace('[COURSE_NAME]', $course_link, $new_student_enrolled_admin_subject);

        $new_student_enrolled_admin_body = str_replace('[STUDENT_FIRST_NAME]', $first_name, $new_student_enrolled_admin_body);
        $new_student_enrolled_admin_body = str_replace('[STUDENT_LAST_NAME]', $last_name, $new_student_enrolled_admin_body);
        $new_student_enrolled_admin_body = str_replace('[STUDENT_EMAIL]', $email, $new_student_enrolled_admin_body);
        $new_student_enrolled_admin_body = str_replace('[COURSE_NAME]', $course_link, $new_student_enrolled_admin_body);

        foreach ($studentData as $item) {
            $new_student_enrolled_admin_body = str_replace("[{$item->field_code}]", $item->field_value, $new_student_enrolled_admin_body);
        }

        $sql = "select admin_email from #__guru_config limit 0, 1";
        $db->setQuery($sql);
        $admin_email = $db->loadResult();

        $admin_email = explode(",", $admin_email);
        if (isset($admin_email) && count($admin_email) > 0) {
            foreach ($admin_email as $key => $admin_id) {
                if (intval($admin_id) != 0) {
                    $sql = "select email from #__users where id=" . intval($admin_id);
                    $db->setQuery($sql);
                    $adminEmail = $db->loadResult();
                    $recipient = array($adminEmail);

                    $send_admin_email_student_enrolled = isset($template_emails["send_admin_email_student_enrolled"]) ? $template_emails["send_admin_email_student_enrolled"] : 1;

                    if ($send_admin_email_student_enrolled) {
                        JFactory::getMailer()->sendMail($from, $fromname, $recipient, $new_student_enrolled_admin_subject, $new_student_enrolled_admin_body, true);
                    }

                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $query->clear();
                    $query->insert('#__guru_logs');
                    $query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type')));
                    $query->values(intval($admin_id) . ',' . $db->quote('to-admin-student-enrolled') . ',' . '0' . ',' . $db->quote(trim($email)) . ',' . $db->quote(trim($new_student_enrolled_admin_subject)) . ',' . $db->quote(trim($new_student_enrolled_admin_body)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''");
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }

        return true;
    }

    function changeCompleted()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $userid = $user->id;
        $id = JFactory::getApplication()->input->get("cid", "0", "raw");
        $id = intval($id);

        $sql = "SELECT lesson_id from #__guru_viewed_lesson WHERE user_id =" . intval($userid) . " and pid=" . $id;
        $db->setQuery($sql);
        $result1 = $db->loadResult();
        $viewed = explode('||', trim($result1, "||"));

        $sql = "SELECT id FROM #__guru_days WHERE pid =" . $id;
        $db->setQuery($sql);
        $temp = $db->loadColumn();

        if (count($temp) > 0) {
            $sql = "SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (" . implode(',', $temp) . ")";
            $db->setQuery($sql);
            $temp = $db->loadColumn();

            if (!isset($temp) || !is_array($temp) || count($temp) == 0) {
                $temp = array("0" => "0");
            }

            $sql = "SELECT id FROM #__guru_task WHERE id IN (" . implode(',', $temp) . ") and `published`='1'";
            $db->setQuery($sql);
            $all_lessons = $db->loadColumn();

            $diff = array_diff($all_lessons, $viewed);
            if (isset($diff) && count($diff) > 0) {
                $sql = "update #__guru_viewed_lesson set completed=0 WHERE user_id =" . intval($userid) . " and pid=" . intval($id);
                $db->setQuery($sql);
            }
        }
    }

    function getCount($module, $lesson)
    {
        $db = JFactory::getDBO();
        $sql = "select media_id from #__guru_mediarel where type='scr_m' and type_id=" . intval($lesson) . " and layout='12'";
        $db->setQuery($sql);
        $media_id = $db->loadResult();

        return intval($media_id);
    }

    function userInGroups($groups)
    {
        $user = JFactory::getUser();
        $user_groups = $user->groups;

        if (!is_array($groups)) {
            $groups = explode(",", $groups);
        }

        if (is_array($user_groups) && count($user_groups) == 0) {
            $db = JFactory::getDbo();
            $sql = "select group_id from #__user_usergroup_map where user_id=" . intval($user->id);
            $db->setQuery($sql);
            $user_groups = $db->loadColumn();
        }

        $intersect = array_intersect($user_groups, $groups);

        if (isset($intersect) && count($intersect) > 0) {
            return true;
        }
        return false;
    }
    // start changes for lessons per release
    function getLessonDetails($program, $author, $lesson, $diff_date, $diff_start, $step_less, $start_date, $config, $lesson_details_for_quiz, $poz, $nr_lesson, $lessons_per_release)
    {
        $user = JFactory::getUser();
        $db = JFactory::getDbo();

        $preview = JFactory::getApplication()->input->get("preview", "", "raw");

        if ($preview == "true") {
            $user = JFactory::getUser();
            $view_course = false;

            if (intval($user->id) > 0) {
                $sql = "select `author` from #__guru_program where `id`=" . intval($program->id);
                $db->setQuery($sql);
                $author = $db->loadResult();

                if (isset($author) && trim($author) != "") {
                    $author = explode("|", $author);
                }

                if (in_array(intval($user->id), $author)) {
                    // user logged is course author
                    $view_course = true;
                } else {
                    // check if user logged is super user
                    if (in_array(8, $user->groups)) {
                        $view_course = true;
                    }
                }

                if ($view_course) {
                    $lesson["can_open_lesson"] = "1";
                    return $lesson;
                }
            }

            $app = JFactory::getApplication();
            $app->redirect(JURI::root());
        }

        if ($program->chb_free_courses == 1) { // free for
            $free_limit = $program->free_limit;

            if (intval($free_limit) > 0) {
                $sql = "select count(*) from #__guru_buy_courses bc, #__guru_order o where o.`status`='Paid' and o.`id`=bc.`order_id` and (bc.`expired_date` >= now() OR bc.`expired_date`='0000-00-00 00:00:00') and bc.`course_id`=" . intval($program->id);
                $db->setQuery($sql);
                $count_orders = $db->loadResult();

                if (intval($count_orders) >= intval($free_limit)) {
                    $program->chb_free_courses = 0;
                }
            }
        }

        if (intval($user->id) == 0) { // not logged user
            if ($program->chb_free_courses == 0) { // this course is not free
                if ($lesson["step_access"] == 2) { // lesson access is guest
                    $lesson["can_open_lesson"] = "1";
                } else { // lesson access students or members and is not logged anyone
                    if ($lesson["step_access"] == 1) { // members
                        $lesson["can_open_lesson"] = "0";
                        $lesson["need_registration"] = "1";
                    } else {
                        $lesson["can_open_lesson"] = "0";
                    }
                }
            } elseif ($program->chb_free_courses == 1) { // this course is free
                if ($program->step_access_courses == 2) { // free for guests
                    $lesson["can_open_lesson"] = "1";
                } else { // free for students or members and is not logged anyone
                    $lesson["can_open_lesson"] = "0";
                    $lesson["need_registration"] = "1";
                }
            }

            if ($lesson["step_access"] == "2") {
                $lesson["can_open_lesson"] = "1";
                $lesson["need_registration"] = "0";
            }
        }

        if (intval($user->id) != 0) { // logged user
            $sql = "select bc.expired_date from #__guru_buy_courses bc inner join #__guru_order o on bc.order_id=o.id where bc.course_id=" . intval($program->id) . " and bc.userid=" . intval($user->id) . " and o.status='Paid'";
            $db->setQuery($sql);

            $expired_date = $db->loadResult();
            $license_expired = false;

            if (isset($expired_date) && $expired_date != "0000-00-00 00:00:00") {
                $today = time();
                $course_expired_date = strtotime($expired_date);
                if ($today > $course_expired_date) {
                    $license_expired = true;
                }
            }

            // check if before this lesson is a quiz that is not finished and can't go on to next lessons
            if (isset($lesson_details_for_quiz) && count($lesson_details_for_quiz) > 0) {
                for ($i = count($lesson_details_for_quiz) - 2; $i >= 0; $i--) {
                    $previous_lesson = $lesson_details_for_quiz[$i];
                    if (isset($previous_lesson["lesson_contains_quiz"]) && $previous_lesson["lesson_quiz_student_failed"] == '1' && $previous_lesson["lesson_quiz_student_go_on"] == 'false') {
                        $lesson["can_open_lesson"] = "0";
                        $lesson["lesson_quiz_student_go_on"] = "0";
                        return $lesson;
                    }
                }
            }
            // check if before this lesson is a quiz that is not finished and can't go on to next lessons

            if (!$this->finishedAllRequiredCourses($program)) { // not finished required courses
                $lesson["can_open_lesson"] = "0";
                $lesson["finish_required_courses"] = "1";
            } elseif ($program->chb_free_courses == 0) { // this course is not free
                if ($lesson["step_access"] == 2) { // lesson access is guest
                    $lesson["can_open_lesson"] = "1";
                } elseif ($lesson["step_access"] == 1) { // lesson access is members
                    $groups_access = $lesson["groups_access"];

                    if (!is_array($groups_access)) {
                        $groups_access = explode(",", $groups_access);
                    }

                    if (!is_array($groups_access) || count($groups_access) == 0) {
                        $lesson["can_open_lesson"] = "1";
                    } elseif ($this->userInGroups($groups_access) && is_array($groups_access) && count($groups_access) > 0) {
                        $lesson["can_open_lesson"] = "1";
                    } else {
                        $lesson["can_open_lesson"] = "0";
                    }

                    if (isset($expired_date) && $license_expired === false) {
                        // has an order, and is not expired
                        $lesson["can_open_lesson"] = "1";
                    }
                } elseif ($lesson["step_access"] == 0) { // lesson access is students
                    if ($license_expired) {
                        $lesson["can_open_lesson"] = "0";
                    }

                    if ($this->isCustomerForCourse($program)) {
                        $lesson["can_open_lesson"] = "1";
                    } else {
                        $lesson["can_open_lesson"] = "0";
                    }

                    if (isset($expired_date) && $license_expired === false) {
                        // has an order, and is not expired
                        $lesson["can_open_lesson"] = "1";
                    }
                }
            } elseif ($program->chb_free_courses == 1) { // this course is free
                if ($lesson["step_access"] == 2) { // lesson access is guest
                    $lesson["can_open_lesson"] = "1";
                } elseif ($lesson["step_access"] == 1) { // lesson access is members
                    $groups_access = $lesson["groups_access"];

                    if (!is_array($groups_access)) {
                        $groups_access = explode(",", $groups_access);
                    }

                    if (!is_array($groups_access) || count($groups_access) == 0) {
                        $lesson["can_open_lesson"] = "1";
                    } elseif ($this->userInGroups($groups_access) && is_array($groups_access) && count($groups_access) > 0) {
                        $lesson["can_open_lesson"] = "1";
                    } else {
                        $lesson["can_open_lesson"] = "0";
                    }

                    if (isset($expired_date) && $license_expired === false) {
                        // has an order, and is not expired
                        $lesson["can_open_lesson"] = "1";
                    }
                } elseif ($lesson["step_access"] == 0) { // lesson access is students
                    // check course access details --------------------------------------------
                    if ($program->step_access_courses == 2) { // free for guests
                        $lesson["can_open_lesson"] = "1";
                    } elseif ($program->step_access_courses == 1) { // free for members
                        $groups_access = $program->groups_access;
                        $user_courses = $this->getUserCourses();

                        if (isset($user_courses[$program->id])) { // user is enrolleed to this curse
                            if ($this->userInGroups($groups_access) && trim($groups_access) != "") {
                                $lesson["can_open_lesson"] = "1";
                            } elseif (trim($groups_access) == "") {
                                $lesson["can_open_lesson"] = "1";
                            } else {
                                $lesson["can_open_lesson"] = "0";

                                // if user is not from that user groups but bought this course
                                if ($this->isCustomerForCourse($program)) {
                                    $lesson["can_open_lesson"] = "1";
                                }
                            }
                        } else { // user is not enrolled
                            if (trim($groups_access) == "") {
                                if ($this->isCustomerForCourse($program)) {
                                    $lesson["can_open_lesson"] = "1";
                                } else {
                                    $lesson["can_open_lesson"] = "0";
                                    $lesson["need_enroll"] = "1";
                                }
                            } else {
                                if ($this->userInGroups($groups_access) && trim($groups_access) != "") {
                                    $lesson["can_open_lesson"] = "0";
                                    $lesson["need_enroll"] = "1";
                                } else {
                                    $lesson["can_open_lesson"] = "0";
                                }
                            }

                            // if user is not from that user groups but bought this course
                            if ($this->isCustomerForCourse($program)) {
                                $lesson["can_open_lesson"] = "1";
                            }
                        }
                    } elseif ($program->step_access_courses == 0) { // free for students
                        if ($program->selected_course == -1) { // free for students of any courses
                            if ($this->isCustomer()) {
                                $lesson["can_open_lesson"] = "1";

                                if (!$this->isCustomerForCourse($program)) {
                                    $lesson["can_open_lesson"] = "0";
                                    $lesson["enroll_to_course"] = "1";
                                }
                            } else {
                                $lesson["can_open_lesson"] = "0";
                            }
                        } else { // free for students of selected courses
                            $selected_courses = $program->selected_course;
                            $selected_courses = explode("|", $selected_courses);
                            $selected_courses = array_filter($selected_courses);
                            $user_courses = $this->getUserCourses();

                            if (isset($user_courses) && count($user_courses) > 0) {
                                $exist = false;
                                foreach ($user_courses as $key => $value) {
                                    if (in_array($key, $selected_courses)) {
                                        $exist = true;
                                        break;
                                    }
                                }

                                if ($exist) {
                                    if ($license_expired) {
                                        $lesson["can_open_lesson"] = "0";
                                        $lesson["enroll_to_course"] = "0";
                                    } else {
                                        $lesson["can_open_lesson"] = "0";
                                        $lesson["enroll_to_course"] = "1";
                                    }
                                } else {
                                    $lesson["can_open_lesson"] = "0";
                                }

                                // if user courses exist in list of selected courses, no need for enroll, already enrolled
                                $cid = JFactory::getApplication()->input->get("cid", "", "raw");
                                if (intval($cid) == $program->id && isset($user_courses[$program->id])) {
                                    $lesson["can_open_lesson"] = "1";
                                    $lesson["enroll_to_course"] = "0";
                                }
                            } else {
                                $lesson["can_open_lesson"] = "0";
                            }
                        }
                    }
                    // check course access details --------------------------------------------
                }
            }

            if ($program->course_type == 1 && intval($program->lesson_release) != 0 && $lesson["step_access"] != "2") { // Sequential course, bot not all at once
                if ($step_less != "" && $start_date != "") {
                    $date_to_display = "";
                    $serial_order = false;

                    if ($program->lesson_release == 1) {
                        if ($nr_lesson / $lessons_per_release <= 1) {
                            if (strpos($start_date, "-") === FALSE && strpos($start_date, ":") === FALSE) {
                                $start_date = date("Y-m-d H:i:s", $start_date);
                            }

                            $date_to_display = strtotime($start_date);
                        } else {
                            $date_to_display = strtotime('+' . (ceil($nr_lesson / $lessons_per_release) - 1) . ' day', $start_date);
                        }
                    } elseif ($program->lesson_release == 2) {
                        if ($nr_lesson / $lessons_per_release <= 1) {
                            if (strpos($start_date, "-") === FALSE && strpos($start_date, ":") === FALSE) {
                                $start_date = date("Y-m-d H:i:s", $start_date);
                            }

                            $date_to_display = strtotime($start_date);
                        } else {
                            $date_to_display = strtotime('+' . (ceil($nr_lesson / $lessons_per_release) - 1) . ' week', $start_date);
                        }
                    } elseif ($program->lesson_release == 3) {
                        if ($nr_lesson / $lessons_per_release <= 1) {
                            if (strpos($start_date, "-") === FALSE && strpos($start_date, ":") === FALSE) {
                                $start_date = date("Y-m-d H:i:s", $start_date);
                            }

                            $date_to_display = strtotime($start_date);
                        } else {
                            $date_to_display = strtotime('+' . (ceil($nr_lesson / $lessons_per_release) - 1) . ' month', $start_date);
                        }
                    } elseif ($program->lesson_release == 4) {
                        $date_to_display = strtotime('+' . (($nr_lesson - 1) * $program->after_hours) . ' hour', $start_date);
                    } elseif ($program->lesson_release == 5) {
                        $serial_order = true;
                    }

                    $available_div = '';

                    $timezone = new DateTimeZone(JFactory::getConfig()->get('offset'));
                    $jnow = new JDate('now');
                    $jnow->setTimezone($timezone);
                    $jnow = $jnow->toSQL(true);

                    if ($lesson["can_open_lesson"] && $serial_order) {
                        $db = JFactory::getDbo();
                        $sql = "SELECT `id` FROM `#__guru_days` WHERE `pid`=" . intval($program->id);
                        $db->setQuery($sql);
                        $modules = $db->loadColumn();
                        $lesson_access = false;

                        if (!isset($modules) || count($modules) == 0) {
                            $modules = array("0");
                        }

                        $sql = "SELECT DISTINCT `media_id` FROM `#__guru_mediarel` WHERE type='dtask' AND `type_id` in (" . implode(',', $modules) . ")";
                        $db->setQuery($sql);
                        $lessons = $db->loadColumn();

                        if (isset($lessons) && count($lessons) > 0) {
                            $sql = "SELECT `id` FROM `#__guru_task` WHERE `id` in (" . implode(',', $lessons) . ") and `published`='1' and `startpublish` <= now() and (`endpublish` >= now() OR `endpublish`='0000-00-00 00:00:00') order by `ordering` ASC";
                            $db->setQuery($sql);
                            $lessons = $db->loadColumn();

                            if (isset($lessons) && count($lessons) > 0) {
                                foreach ($lessons as $key => $lesson_id) {
                                    if ($lesson_id == $lesson["id"] && $key == 0) {
                                        // first lesson, should be available
                                        $lesson_access = true;
                                    } else {
                                        $preview_lesson_id = 0;

                                        if ($lesson_id == $lesson["id"]) {
                                            if (isset($lessons[$key - 1])) {
                                                $preview_lesson_id = $lessons[$key - 1];
                                            }
                                        }

                                        if (intval($preview_lesson_id) > 0) {
                                            $sql = "select count(1) from #__guru_viewed_lesson where `lesson_id` like '%|" . intval($preview_lesson_id) . "|%' and `user_id`=" . intval($user->id) . " and `pid`=" . intval($program->id);
                                            $db->setQuery($sql);
                                            $count_view_preview = (int) $db->loadResult();

                                            if ($count_view_preview > 0) {
                                                $lesson_access = true;
                                            }
                                        }
                                    }
                                }
                            } else {
                                $lesson_access = false;
                            }
                        } else {
                            $lesson_access = false;
                        }

                        if ($lesson_access && !$license_expired) {
                            $lesson["can_open_lesson"] = "1";

                            if ($program->lessons_show == 1) {
                                $available_div = '<div class="available_lesson replace_class available pull-left">
                                    <span class="hidden-phone">' . JText::_("GURU_AVAILABLE") . '</span>
                                    <i class="uk-icon-check uk-text-success wk-icon-check wk-text-success" title="' . JText::_("GURU_AVAILABLE") . '" data-uk-tooltip></i>
                                </div>';
                                $lesson["available_div"] = $available_div;
                            }
                        } else {
                            //$available_div = '<div class="available_lesson replace_class available pull-left">'.JText::_("GURU_VIEW_PREVIOUS_LESSON").'</div>';

                            $available_div = '<div class="not-available_lesson replace_class available pull-left">
                                    <span class="hidden-phone">' . JText::_("GURU_VIEW_PREVIOUS_LESSON") . '</span>
                                    <i class="uk-icon-clock-o wk-icon-clock-o hidden-desktop" title="' . JText::_("GURU_VIEW_PREVIOUS_LESSON") . '" data-uk-tooltip></i>
                                </div>';

                            $lesson["can_open_lesson"] = "0";
                            $lesson["available_div"] = $available_div;

                            if ($program->lessons_show == 2) { // do not show unvailable lessons
                                $lesson["not_show_lesson"] = "1";
                            }
                        }
                    } elseif (strtotime($jnow) < $date_to_display) {
                        $date_to_display = date($config->datetype, $date_to_display);
                        $available_div = '<div class="available_lesson replace_class available pull-left">
                            <span class="hidden-phone">' . $date_to_display . '</span>
                            <i class="uk-icon-clock-o wk-icon-clock-o" title="' . $date_to_display . '" data-uk-tooltip></i>
                        </div>';
                        $lesson["can_open_lesson"] = "0";
                        $lesson["available_div"] = $available_div;

                        if ($program->lessons_show == 2) { // do not show unvailable lessons
                            $lesson["not_show_lesson"] = "1";
                        }
                    } else {
                        if (!$license_expired && isset($expired_date)) {
                            $lesson["can_open_lesson"] = "1";

                            if ($program->lessons_show == 1) {
                                $available_div = '<div class="available_lesson replace_class available pull-left">
                                    <span class="hidden-phone">' . JText::_("GURU_AVAILABLE") . '</span>
                                    <i class="uk-icon-check uk-text-success wk-icon-check wk-text-success" title="' . JText::_("GURU_AVAILABLE") . '" data-uk-tooltip></i>
                                </div>';
                                $lesson["available_div"] = $available_div;
                            }
                        }
                    }

                    /*if user can see all sequential lessons*/
                    if ($this->canSeeSequentialLessons($user->id, $program->id)) {
                        if (!$license_expired && isset($expired_date)) {
                            $lesson["can_open_lesson"] = "1";

                            if ($program->lessons_show == 1) {
                                $available_div = '<div class="available_lesson replace_class available pull-left">
                                    <span class="hidden-phone">' . JText::_("GURU_AVAILABLE") . '</span>
                                    <i class="uk-icon-check uk-text-success wk-icon-check wk-text-success" title="' . JText::_("GURU_AVAILABLE") . '" data-uk-tooltip></i>
                                </div>';
                                $lesson["available_div"] = $available_div;
                            }
                        }
                    }
                } else {
                    $lesson["can_open_lesson"] = "0";
                }
            }
        }

        return $lesson;
    }
    // end changes for lessons per release
    function canSeeSequentialLessons($user_id, $program_id)
    {
        $db = JFactory::getDbo();

        $sql = "select `sequential_courses` from #__guru_customer where `id`=" . intval($user_id);
        $db->setQuery($sql);
        $sequential_courses = $db->loadResult();

        if (trim($sequential_courses) != "") {
            $sequential_courses = json_decode($sequential_courses, true);

            if (in_array(intval($program_id), $sequential_courses)) {
                return true;
            }
        }

        return false;
    }

    function finishedAllRequiredCourses($program)
    {
        $return = true;
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $sql = "SELECT a.*, b.* FROM `#__guru_mediarel` as a INNER JOIN `#__guru_program` as b ON a.media_id=b.id WHERE a.type='preq' AND a.mainmedia=0 AND a.type_id=" . intval($program->id);
        $db->setQuery($sql);
        $required_courses = $db->loadAssocList();

        if (isset($required_courses) && count($required_courses) > 0) {
            $sql = "select pid, completed from #__guru_viewed_lesson where user_id=" . intval($user->id);
            $db->setQuery($sql);
            $viewed_lessons = $db->loadAssocList("pid");

            foreach ($required_courses as $key => $value) {
                if (!isset($viewed_lessons[$value["id"]])) {
                    $return = false;
                    break;
                } elseif (isset($viewed_lessons[$value["id"]]) && $viewed_lessons[$value["id"]]["completed"] == 0) {
                    $return = false;
                    break;
                }
            }
        }

        return $return;
    }

    function getExpiredLicense($program)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $expired = false;

        $sql = "select expired_date from #__guru_buy_courses where course_id=" . intval($program->id) . " and userid=" . intval($user->id);
        $db->setQuery($sql);
        $expired_date = $db->loadResult();
        $license_expired = false;

        if ($expired_date != "0000-00-00 00:00:00") {
            $today = time();
            $course_expired_date = strtotime($expired_date);
            if ($today > $course_expired_date) {
                $license_expired = true;
            }
        }

        return $license_expired;
    }

    function checkLessonQuiz($lesson, $program)
    {
        $db = JFactory::getDbo();
        $user_id = JFactory::getUser();
        $user_id = $user_id->id;

        $sql = "select media_id from #__guru_mediarel where type='scr_m' and type_id=" . intval($lesson["id"]) . " and layout='12'";
        $db->setQuery($sql);
        $media_id = $db->loadResult();

        if (intval($media_id) > 0) {
            $lesson["lesson_contains_quiz"] = "true";

            $sql = "select max_score, student_failed from #__guru_quiz where id=" . intval($media_id);
            $db->setQuery($sql);
            $result = $db->loadAssocList();
            $max_score = @$result["0"]["max_score"];
            $student_failed = @$result["0"]["student_failed"];

            $lesson["lesson_quiz_student_failed"] = intval($student_failed);

            $essay_unmarcked = false;

            $sql = "select question_ids from #__guru_quiz_question_taken_v3 where user_id=" . intval($user_id) . " and quiz_id=" . intval($media_id) . " and pid=" . intval($program->id) . " order by id desc limit 0,1";
            $db->setQuery($sql);
            $question_ids = $db->loadResult();

            if (trim($question_ids) != "") {
                $sql = "select id, type from #__guru_questions_v3 where id in (" . trim($question_ids) . ") and type='essay'";
                $db->setQuery($sql);
                $questions = $db->loadAssocList();

                if (isset($questions) && count($questions) > 0) {
                    foreach ($questions as $key => $question) {
                        $sql = "select count(*) from #__guru_quiz_essay_mark where question_id=" . intval($question["id"]) . " and user_id=" . intval($user_id);
                        $db->setQuery($sql);
                        $count = $db->loadResult();

                        if (intval($count) == 0) {
                            $essay_unmarcked = true;
                            break;
                        }
                    }
                }
            }

            if (intval($student_failed) == '1') {
                // don't let student continue
                $sql = "select score_quiz from #__guru_quiz_question_taken_v3 where user_id=" . intval($user_id) . " and quiz_id=" . intval($media_id) . " and pid=" . intval($program->id) . " order by id desc limit 0,1";
                $db->setQuery($sql);
                $score_quiz = $db->loadResult();

                if ($essay_unmarcked) {
                    $lesson["lesson_quiz_student_go_on"] = "false";
                    $lesson["quiz_passed"] = "2"; // pending assessment
                } elseif (!isset($score_quiz)) {
                    $lesson["lesson_quiz_student_go_on"] = "false";
                    $lesson["quiz_passed"] = "-1"; // pending
                } elseif ($score_quiz >= $max_score) {
                    $lesson["lesson_quiz_student_go_on"] = "true";
                    $lesson["quiz_passed"] = "1"; // passed
                } elseif ($score_quiz < $max_score) {
                    $lesson["lesson_quiz_student_go_on"] = "false";
                    $lesson["quiz_passed"] = "0"; // failed
                }
            } else {
                $user_id = JFactory::getUser();
                $user_id = $user_id->id;

                $sql = "select score_quiz from #__guru_quiz_question_taken_v3 where user_id=" . intval($user_id) . " and quiz_id=" . intval($media_id) . " and pid=" . intval($program->id) . " order by id desc limit 0,1";
                $db->setQuery($sql);
                $score_quiz = $db->loadResult();

                if ($essay_unmarcked) {
                    $lesson["quiz_passed"] = "2"; // pending assessment
                } elseif (!isset($score_quiz)) {
                    $lesson["quiz_passed"] = "-1"; // pending
                } elseif ($score_quiz >= $max_score) {
                    $lesson["quiz_passed"] = "1"; // passed
                } elseif ($score_quiz < $max_score) {
                    $lesson["quiz_passed"] = "0"; // failed
                }
            }
        }

        return $lesson;
    }
};
