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
set_time_limit(60);
jimport("joomla.aplication.component.model");
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class guruAdminModelguruinstall extends JModelLegacy
{

    function __construct()
    {
        parent::__construct();
    }

    function startDatabaseInstall()
    {
        $db = JFactory::getDBO();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_authors` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `userid` int(21) NOT NULL DEFAULT '0',
                  `gid` int(11) NOT NULL DEFAULT '0',
                  `full_bio` longtext NOT NULL,
                  `images` varchar(255) NOT NULL DEFAULT '',
                  `emaillink` int(2) NOT NULL DEFAULT '0',
                  `website` varchar(255) NOT NULL DEFAULT '',
                  `blog` varchar(255) NOT NULL DEFAULT '',
                  `facebook` varchar(255) NOT NULL DEFAULT '',
                  `twitter` varchar(255) NOT NULL DEFAULT '',
                  `show_email` tinyint(1) NOT NULL DEFAULT '1',
                  `show_website` tinyint(1) NOT NULL DEFAULT '1',
                  `show_blog` tinyint(1) NOT NULL DEFAULT '1',
                  `show_facebook` tinyint(1) NOT NULL DEFAULT '1',
                  `show_twitter` tinyint(1) NOT NULL DEFAULT '1',
                  `author_title` varchar(255) NOT NULL,
                  `ordering` int(11) NOT NULL DEFAULT '0',
                  `forum_kunena_generated` tinyint(4) NOT NULL DEFAULT '0',
                  `enabled` tinyint(4) NOT NULL DEFAULT '0',
                  `commission_id` int(11) NOT NULL,
                  `paypal_email` varchar(100),
                  `paypal_other_information` text,
                  `paypal_option` tinyint(1) NOT NULL DEFAULT '1',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_authors_commissions` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `author_id` int(11) NOT NULL,
                  `course_id` int(11) NOT NULL,
                  `plan_id` int(11) NOT NULL,
                  `order_id` int(11) DEFAULT NULL,
                  `customer_id` int(11) NOT NULL,
                  `commission_id` int(11) DEFAULT NULL,
                  `price` FLOAT(11) NOT NULL,
                  `price_paid` FLOAT(11) NOT NULL,
                  `amount_paid_author` FLOAT(11) NOT NULL,
                  `promocode_id` int(11) NOT NULL,
                  `status_payment` text NOT NULL,
                  `payment_method` text NOT NULL,
                  `history` int(11) NOT NULL,
                  `data` datetime NOT NULL,
                  `currency` varchar(10) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_authors_commissions_history` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `author_id` int(11) NOT NULL,
                  `total` float NOT NULL,
                  `order_auth_ids` text,
                  `data_paid` datetime NOT NULL,
                  `count_payments` int(11) NOT NULL,
                  `coin` varchar(10) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_buy_courses` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `userid` int(11) NOT NULL DEFAULT '0',
                  `order_id` int(11) NOT NULL DEFAULT '0',
                  `course_id` int(11) NOT NULL,
                  `price` float NOT NULL,
                  `buy_date` datetime NOT NULL,
                  `expired_date` datetime NOT NULL,
                  `plan_id` varchar(255) NOT NULL,
                  `email_send` int(3) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_category` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) DEFAULT NULL,
                  `alias` varchar(255) NOT NULL,
                  `published` tinyint(1) NOT NULL DEFAULT '1',
                  `description` text,
                  `image` varchar(255) DEFAULT NULL,
                  `ordering` int(11) DEFAULT NULL,
                  `icon` varchar(255) NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_categoryrel` (
                  `parent_id` int(11) NOT NULL DEFAULT '1',
                  `child_id` int(11) NOT NULL DEFAULT '1'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_certificates` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `general_settings` varchar(5) DEFAULT NULL,
                  `design_background` varchar(255) DEFAULT NULL,
                  `design_background_color` varchar(11) DEFAULT 'ACE0F6',
                  `design_text_color` varchar(11) NOT NULL DEFAULT '333333',
                  `avg_cert` int(11) NOT NULL DEFAULT '70',
                  `templates1` text NOT NULL,
                  `templates2` text NOT NULL,
                  `templates3` text NOT NULL,
                  `templates4` text NOT NULL,
                  `subjectt3` text NOT NULL,
                  `subjectt4` text NOT NULL,
                  `font_certificate` text NOT NULL,
                  `library_pdf` tinyint(2) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_commissions` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `default_commission` tinyint(4) NOT NULL DEFAULT '0',
                  `commission_plan` varchar(255) NOT NULL DEFAULT '',
                  `teacher_earnings` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_config` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `currency` varchar(255) NOT NULL DEFAULT 'USD',
                  `datetype` varchar(255) NOT NULL DEFAULT '0',
                  `dificulty` varchar(255) DEFAULT NULL,
                  `influence` tinyint(1) NOT NULL,
                  `display_tasks` tinyint(1) NOT NULL DEFAULT '0',
                  `groupe_tasks` tinyint(1) NOT NULL DEFAULT '0',
                  `display_media` tinyint(1) NOT NULL DEFAULT '0',
                  `show_unpubl` tinyint(1) NOT NULL DEFAULT '0',
                  `btnback` tinyint(1) NOT NULL DEFAULT '1',
                  `btnhome` tinyint(1) NOT NULL DEFAULT '1',
                  `btnnext` tinyint(1) NOT NULL DEFAULT '1',
                  `dofirst` tinyint(1) NOT NULL DEFAULT '0',
                  `imagesin` varchar(255) NOT NULL DEFAULT 'images/stories',
                  `videoin` varchar(255) NOT NULL DEFAULT 'media/videos',
                  `audioin` varchar(255) NOT NULL DEFAULT 'media/audio',
                  `docsin` varchar(255) NOT NULL DEFAULT 'media/documents',
                  `filesin` varchar(255) NOT NULL DEFAULT 'media/files',
                  `certificatein` varchar(255) NOT NULL DEFAULT 'images/stories/guru/certificates',
                  `certificatein1` varchar(255) NOT NULL DEFAULT 'images/stories/guru/certificates',
                  `fromname` varchar(255) DEFAULT NULL,
                  `fromemail` varchar(255) DEFAULT NULL,
                  `regemail` varchar(255) DEFAULT NULL,
                  `orderemail` varchar(255) DEFAULT NULL,
                  `ctgpage` text,
                  `st_ctgpage` text NOT NULL,
                  `ctgspage` text,
                  `st_ctgspage` text NOT NULL,
                  `psgspage` text NOT NULL,
                  `st_psgspage` text NOT NULL,
                  `psgpage` text NOT NULL,
                  `st_psgpage` text NOT NULL,
                  `authorspage` text NOT NULL,
                  `st_authorspage` text NOT NULL,
                  `authorpage` text NOT NULL,
                  `st_authorpage` text NOT NULL,
                  `video_display` tinyint(1) NOT NULL,
                  `audio_display` tinyint(1) NOT NULL,
                  `content_selling` text NOT NULL,
                  `open_target` int(1) NOT NULL DEFAULT '0',
                  `st_donecolor` varchar(10) NOT NULL,
                  `st_notdonecolor` varchar(10) NOT NULL,
                  `st_txtcolor` varchar(10) NOT NULL,
                  `st_width` varchar(10) NOT NULL,
                  `st_height` varchar(10) NOT NULL,
                  `progress_bar` tinyint(4) NOT NULL,
                  `lesson_window_size` varchar(255) NOT NULL,
                  `default_video_size` varchar(255) NOT NULL,
                  `lesson_window_size_back` varchar(255) NOT NULL,
                  `last_check_date` datetime,
                  `hour_format` int(11) NOT NULL,
                  `back_size_type` int(3) NOT NULL,
                  `notification` int(2) NOT NULL,
                  `show_bradcrumbs` tinyint(4) NOT NULL DEFAULT '0',
                  `show_powerd` tinyint(4) NOT NULL DEFAULT '1',
                  `qct_alignment` tinyint(4) NOT NULL DEFAULT '1',
                  `qct_border_color` varchar(10) NOT NULL DEFAULT 'cccccc',
                  `qct_minsec` varchar(10) NOT NULL DEFAULT 'cccccc',
                  `qct_title_color` varchar(10) NOT NULL DEFAULT 'FFFFFF',
                  `qct_bg_color` varchar(10) NOT NULL DEFAULT 'f7f7f7',
                  `qct_font` text,
                  `qct_width` varchar(10) NOT NULL DEFAULT '200',
                  `qct_height` varchar(10) NOT NULL DEFAULT '60',
                  `qct_font_nb` varchar(10) NOT NULL DEFAULT '22',
                  `qct_font_words` varchar(10) NOT NULL DEFAULT '14',
                  `currencypos` tinyint(4) NOT NULL DEFAULT '0',
                  `guru_ignore_ijseo` tinyint(4) NOT NULL DEFAULT '0',
                  `course_lesson_release` tinyint(4) NOT NULL DEFAULT '0',
                  `student_group` int(10) NOT NULL DEFAULT '2',
                  `guru_turnoffjq` tinyint(4) NOT NULL DEFAULT '1',
                  `show_bootstrap` tinyint(4) NOT NULL DEFAULT '0',
                  `guru_turnoffbootstrap` tinyint(4) NOT NULL DEFAULT '1',
                  `gurujomsocialregstudent` tinyint(4) NOT NULL DEFAULT '0',
                  `gurujomsocialregteacher` tinyint(4) NOT NULL DEFAULT '0',
                  `gurujomsocialprofilestudent` tinyint(4) NOT NULL DEFAULT '0',
                  `gurujomsocialprofileteacher` tinyint(4) NOT NULL DEFAULT '0',
                  `gurujomsocialregstudentmprof` tinyint(4) NOT NULL DEFAULT '0',
                  `gurujomsocialregteachermprof` tinyint(4) NOT NULL DEFAULT '0',
                  `installed_plugin_user` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `template_emails` text NOT NULL,
                  `terms_cond_student` int(3) NOT NULL DEFAULT '0',
                  `terms_cond_teacher` int(3) NOT NULL DEFAULT '0',
                  `terms_cond_student_content` text,
                  `terms_cond_teacher_content` text,
                  `course_is_free_show` tinyint(2) NOT NULL DEFAULT '0',
                  `admin_email` varchar(255) NOT NULL DEFAULT '455',
                  `invoice_issued_by` text,
                  `indicate_quiz` int(1) NOT NULL DEFAULT '0',
                  `mailchimp_teacher_api` varchar(255),
                  `mailchimp_teacher_list_id` varchar(255) NOT NULL DEFAULT '',
                  `mailchimp_teacher_auto` int(3) NOT NULL DEFAULT '1',
                  `mailchimp_student_api` varchar(255) NOT NULL DEFAULT '',
                  `mailchimp_student_list_id` varchar(255) NOT NULL DEFAULT '',
                  `mailchimp_student_auto` int(3) NOT NULL DEFAULT '1',
                  `seo` text,
                  `captcha` int(3) NOT NULL DEFAULT '1',
                  `auto_approve` int(3) NOT NULL DEFAULT '1',
                  `youtube_key` varchar(255) NOT NULL DEFAULT '',
                  `course_certificate` int(3) NOT NULL DEFAULT '1',
                  `course_exercises` int(3) NOT NULL DEFAULT '1',
                  `rtl` int(3) NOT NULL DEFAULT '0',
                  `thousands_separator` int(3) NOT NULL DEFAULT '1',
                    `decimals_separator` int(3) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_currencies` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `plugname` varchar(30) NOT NULL DEFAULT '',
                  `currency_name` varchar(20) NOT NULL DEFAULT '',
                  `currency_full` varchar(50) NOT NULL DEFAULT '',
                  `sign` varchar(10) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_customer` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `company` varchar(255) NOT NULL DEFAULT '',
                  `firstname` varchar(255) NOT NULL DEFAULT '',
                  `lastname` varchar(255) NOT NULL DEFAULT '',
                  `image` varchar(255) NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_days` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `pid` int(11) NOT NULL,
                  `title` varchar(255) DEFAULT NULL,
                  `alias` varchar(255) NOT NULL,
                  `description` text,
                  `image` varchar(255) DEFAULT NULL,
                  `published` tinyint(1) NOT NULL DEFAULT '1',
                  `startpublish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `endpublish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `metatitle` varchar(255) DEFAULT NULL,
                  `metakwd` varchar(255) DEFAULT NULL,
                  `metadesc` text,
                  `afterfinish` tinyint(1) NOT NULL DEFAULT '1',
                  `url` varchar(255) DEFAULT NULL,
                  `pagetitle` varchar(255) DEFAULT NULL,
                  `pagecontent` text,
                  `ordering` int(3) NOT NULL DEFAULT '0',
                  `locked` tinyint(1) NOT NULL DEFAULT '0',
                  `media_id` int(9) NOT NULL,
                  `access` int(3) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_emails` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `description` text,
                  `type` varchar(255) DEFAULT NULL,
                  `trigger` varchar(255) DEFAULT NULL,
                  `sendtime` tinyint(2) DEFAULT NULL,
                  `sendday` tinyint(2) DEFAULT NULL,
                  `reminder` varchar(255) DEFAULT NULL,
                  `published` tinyint(2) NOT NULL DEFAULT '0',
                  `subject` varchar(255) DEFAULT NULL,
                  `body` text,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_emails_pending` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `sending_time` int(11) NOT NULL,
                  `mail_id` int(11) NOT NULL,
                  `mail_subj` varchar(255) NOT NULL,
                  `mail_body` text NOT NULL,
                  `user_id` int(11),
                  `pid` int(11) NOT NULL,
                  `type` enum('T','R') NOT NULL,
                  `send` tinyint(1) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_emails_refr_time` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `last_trigger_time` int(11) NOT NULL,
                  `last_reminder_time` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_jomsocialstream` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `userid` int(21) NOT NULL DEFAULT '0',
                  `params` text NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_jump` (
                  `id` int(15) NOT NULL AUTO_INCREMENT,
                  `button` int(2) NOT NULL,
                  `text` varchar(255) NOT NULL,
                  `jump_step` int(15) NOT NULL,
                  `module_id1` int(10) NOT NULL,
                  `type_selected` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_kunena_courseslinkage` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `idcourse` int(11) DEFAULT NULL,
                  `coursename` varchar(255) DEFAULT NULL,
                  `catidkunena` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_kunena_forum` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `forumboardcourse` tinyint(4) NOT NULL DEFAULT '0',
                  `forumboardlesson` tinyint(4) NOT NULL DEFAULT '0',
                  `forumboardteacher` tinyint(4) NOT NULL DEFAULT '0',
                  `deleted_boards` tinyint(4) NOT NULL DEFAULT '0',
                  `allow_stud` tinyint(4) NOT NULL DEFAULT '0',
                  `allow_edit` tinyint(4) NOT NULL DEFAULT '0',
                  `allow_delete` tinyint(4) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_kunena_lessonslinkage` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `idlesson` int(11) DEFAULT NULL,
                  `lessonname` varchar(255) DEFAULT NULL,
                  `catidkunena` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_media` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) DEFAULT NULL,
                  `instructions` text,
                  `type` varchar(10) DEFAULT NULL,
                  `source` varchar(5) DEFAULT NULL,
                  `uploaded` enum('0','1') NOT NULL DEFAULT '0',
                  `code` MEDIUMTEXT NULL,
                  `url` varchar(255) DEFAULT NULL,
                  `local` varchar(255) DEFAULT NULL,
                  `width` int(11) NOT NULL DEFAULT '32',
                  `height` int(11) NOT NULL DEFAULT '32',
                  `published` tinyint(1) NOT NULL DEFAULT '1',
                  `option_video_size` int(3) NOT NULL,
                  `category_id` int(10) NOT NULL,
                  `auto_play` int(3) NOT NULL,
                  `show_instruction` int(3) NOT NULL,
                  `hide_name` int(3) NOT NULL DEFAULT '1',
                  `author` int(11) NOT NULL,
                  `image` text,
                  `description` text,
                  `uploaded_tab` int(3) NOT NULL DEFAULT '-1',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_mediarel` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `type` varchar(5) DEFAULT NULL,
                  `type_id` int(11) DEFAULT NULL,
                  `media_id` int(11) DEFAULT NULL,
                  `mainmedia` tinyint(1) NOT NULL DEFAULT '0',
                  `text_no` int(4) NOT NULL DEFAULT '0',
                  `layout` tinyint(3) NOT NULL DEFAULT '0',
                  `access` int(3) NOT NULL,
                  `order` int(100) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_media_categories` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) DEFAULT NULL,
                  `parent_id` int(11) NOT NULL,
                  `child_id` int(11) NOT NULL,
                  `description` text,
                  `metatitle` varchar(255) DEFAULT NULL,
                  `metakey` varchar(255) DEFAULT NULL,
                  `metadesc` varchar(255) DEFAULT NULL,
                  `published` tinyint(1) NOT NULL DEFAULT '1',
                  `user_id` int(11),
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_media_templay` (
                  `ip` bigint(20) NOT NULL,
                  `scr_id` int(8) NOT NULL,
                  `tmp_time` datetime NOT NULL,
                  `db_lay` int(8) NOT NULL,
                  `db_med` varchar(150) NOT NULL,
                  `db_text` varchar(150) NOT NULL,
                  PRIMARY KEY (`ip`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_mycertificates` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `course_id` int(11) NOT NULL,
                  `author_id` varchar(255) NOT NULL,
                  `user_id` int(11),
                  `emailcert` tinyint(4) NOT NULL DEFAULT '0',
                  `datecertificate` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_order` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `userid` int(11) NOT NULL DEFAULT '0',
                  `order_date` datetime NOT NULL,
                  `courses` text NOT NULL,
                  `status` varchar(10) NOT NULL,
                  `amount` float NOT NULL DEFAULT '0',
                  `amount_paid` float NOT NULL DEFAULT '0',
                  `processor` varchar(100) NOT NULL,
                  `number_of_licenses` int(11) NOT NULL,
                  `currency` varchar(10) NOT NULL,
                  `promocodeid` varchar(255) NOT NULL,
                  `published` int(11) NOT NULL,
                  `form` text NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_plugins` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(40) NOT NULL DEFAULT '',
                  `classname` varchar(40) NOT NULL DEFAULT '',
                  `value` text NOT NULL,
                  `filename` varchar(40) NOT NULL DEFAULT '',
                  `type` varchar(10) NOT NULL DEFAULT 'payment',
                  `published` int(11) NOT NULL DEFAULT '0',
                  `def` varchar(30) NOT NULL DEFAULT '',
                  `sandbox` int(11) NOT NULL DEFAULT '0',
                  `reqhttps` int(11) NOT NULL DEFAULT '0',
                  `display_name` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_plugin_settings` (
                  `pluginid` int(11) NOT NULL DEFAULT '0',
                  `setting` varchar(200) NOT NULL DEFAULT '',
                  `description` text,
                  `value` text NOT NULL,
                  KEY `pluginid` (`pluginid`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_program` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `catid` int(11) DEFAULT NULL,
                  `name` varchar(255) DEFAULT NULL,
                  `alias` varchar(255) NOT NULL,
                  `description` text,
                  `introtext` text,
                  `image` varchar(255) DEFAULT NULL,
                  `image_avatar` varchar(255) DEFAULT NULL,
                  `emails` varchar(255) DEFAULT NULL,
                  `published` tinyint(1) NOT NULL DEFAULT '1',
                  `startpublish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `endpublish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `metatitle` varchar(255) DEFAULT NULL,
                  `metakwd` varchar(255) DEFAULT NULL,
                  `metadesc` text,
                  `ordering` int(5) NOT NULL,
                  `pre_req` text,
                  `pre_req_books` text,
                  `reqmts` text,
                  `author` varchar(255) NOT NULL,
                  `level` int(3) NOT NULL,
                  `priceformat` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
                  `skip_module` int(3) NOT NULL,
                  `chb_free_courses` varchar(4) NOT NULL DEFAULT '',
                  `step_access_courses` int(4) DEFAULT NULL,
                  `selected_course` varchar(255) DEFAULT NULL,
                  `course_type` tinyint(4) NOT NULL DEFAULT '0',
                  `lesson_release` tinyint(4) NOT NULL DEFAULT '0',
                  `lessons_show` tinyint(4) NOT NULL DEFAULT '1',
                  `start_release` datetime,
                  `id_final_exam` int(11),
                  `certificate_term` tinyint(4) NOT NULL DEFAULT '0',
                  `hasquiz` tinyint(4) NOT NULL DEFAULT '0',
                  `updated` tinyint(4) NOT NULL DEFAULT '0',
                  `certificate_course_msg` text,
                  `avg_certc` int(11) NOT NULL DEFAULT '70',
                  `status` int(3) NOT NULL DEFAULT '1',
                  `groups_access` TEXT,
                  `split_commissions` int(2) NOT NULL DEFAULT '1',
                  `after_hours` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),
                  KEY `index_catid` (`catid`),
                  KEY `index_namep` (`name`),
                  KEY `index_aliasp` (`alias`),
                  KEY `index_image` (`image`),
                  KEY `index_step_access_courses` (`step_access_courses`),
                  KEY `index_chb_free_courses` (`chb_free_courses`),
                  KEY `index_selected_course` (`selected_course`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_programstatus` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `userid` int(11) NOT NULL,
                  `pid` int(11) NOT NULL,
                  `days` text NOT NULL,
                  `tasks` text NOT NULL,
                  `startdate` datetime NOT NULL,
                  `enddate` datetime NOT NULL,
                  `status` enum('0','1','2','-1') NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_program_plans` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `product_id` int(11) NOT NULL,
                  `plan_id` int(11) NOT NULL,
                  `price` float NOT NULL,
                  `default` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_program_reminders` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `product_id` int(11) NOT NULL,
                  `emailreminder_id` int(11) NOT NULL,
                  `send` enum('0','1') NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_program_renewals` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `product_id` int(11) NOT NULL,
                  `plan_id` int(11) NOT NULL,
                  `price` float NOT NULL,
                  `default` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_promos` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `title` varchar(200) NOT NULL DEFAULT '',
                  `code` varchar(100) NOT NULL DEFAULT '',
                  `codelimit` int(11) NOT NULL DEFAULT '0',
                  `codeused` int(11) NOT NULL DEFAULT '0',
                  `discount` float NOT NULL DEFAULT '0',
                  `codestart` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `codeend` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `forexisting` int(11) NOT NULL DEFAULT '0',
                  `published` int(11) NOT NULL DEFAULT '0',
                  `typediscount` tinyint(2) NOT NULL DEFAULT '0',
                  `courses_ids` text,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `code` (`code`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_questions_v3` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `qid` int(11) DEFAULT NULL,
                  `type` varchar(10) NOT NULL,
                  `question_content` text,
                  `media_ids` text NOT NULL,
                  `points` int(11) NOT NULL DEFAULT '1',
                  `published` tinyint(4) NOT NULL DEFAULT '1',
                  `question_order` int(3) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_question_answers` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `answer_content_text` text DEFAULT NULL,
                  `media_ids` text,
                  `correct_answer` int(3) NOT NULL,
                  `question_id` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_quiz` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) DEFAULT NULL,
                  `description` text,
                  `image` varchar(255) DEFAULT NULL,
                  `published` tinyint(1) NOT NULL DEFAULT '1',
                  `startpublish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `endpublish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `ordering` int(10) NOT NULL DEFAULT '0',
                  `max_score` int(20) NOT NULL DEFAULT '70',
                  `pbl_max_score` tinyint(4) NOT NULL DEFAULT '0',
                  `time_quiz_taken` int(20) NOT NULL DEFAULT '1',
                  `show_nb_quiz_taken` tinyint(4) NOT NULL DEFAULT '0',
                  `final_quiz` tinyint(4) NOT NULL DEFAULT '1',
                  `nb_quiz_select_up` int(11) NOT NULL DEFAULT '10',
                  `show_nb_quiz_select_up` tinyint(4) NOT NULL DEFAULT '0',
                  `limit_time` int(11) NOT NULL DEFAULT '10',
                  `show_limit_time` tinyint(4) NOT NULL DEFAULT '0',
                  `show_countdown` tinyint(4) NOT NULL DEFAULT '0',
                  `limit_time_f` int(11) NOT NULL DEFAULT '1',
                  `show_finish_alert` tinyint(4) NOT NULL DEFAULT '1',
                  `is_final` tinyint(4) NOT NULL DEFAULT '0',
                  `student_failed_quiz` tinyint(4) NOT NULL DEFAULT '0',
                  `hide` tinyint(2) NOT NULL DEFAULT '0',
                  `author` int(11) NOT NULL,
                  `questions_per_page` int(11) NOT NULL DEFAULT '10',
                  `pass_message` text NOT NULL,
                  `fail_message` text NOT NULL,
                  `student_failed` int(2) NOT NULL DEFAULT '0',
                  `show_correct_ans` int(3) NOT NULL DEFAULT '1',
                  `pending_message` LONGTEXT,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_quizzes_final` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `quizzes_ids` varchar(255) NOT NULL,
                  `qid` int(11) NOT NULL,
                  `published` tinyint(1) NOT NULL DEFAULT '1',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_quiz_essay_mark` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `question_id` int(11) NOT NULL,
                  `user_id` int(11),
                  `grade` int(3) DEFAULT NULL,
                  `feedback` longtext,
                  `feedback_quiz_results` longtext,
                  `date` date DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_quiz_question_taken_v3` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11),
                  `question_ids` text NOT NULL,
                  `quiz_id` int(11) NOT NULL,
                  `score_quiz` int(11) NOT NULL,
                  `pid` int(11) NOT NULL,
                  `date_taken_quiz` datetime DEFAULT NULL,
                  `count_right_answer` int(3) NOT NULL,
                  `points` int(11) NOT NULL,
                  `failed` int(3) DEFAULT 0,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_quiz_taken_v3` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11),
                  `quiz_id` int(11) NOT NULL,
                  `question_id` int(11) DEFAULT NULL,
                  `answers_given` longtext,
                  `pid` int(11) NOT NULL,
                  `id_question_taken` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_subplan` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `term` tinyint(3) NOT NULL,
                  `period` varchar(255) NOT NULL,
                  `published` enum('0','1') NOT NULL DEFAULT '0',
                  `ordering` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_subremind` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `term` tinyint(3) NOT NULL,
                  `subject` varchar(255) NOT NULL,
                  `body` text NOT NULL,
                  `published` enum('0','1') NOT NULL,
                  `ordering` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_task` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) DEFAULT NULL,
                  `alias` text NOT NULL,
                  `category` int(11) DEFAULT NULL,
                  `difficultylevel` varchar(255) DEFAULT NULL,
                  `points` int(11) DEFAULT NULL,
                  `image` varchar(255) DEFAULT NULL,
                  `published` tinyint(1) NOT NULL DEFAULT '1',
                  `startpublish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `endpublish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `metatitle` varchar(255) DEFAULT NULL,
                  `metakwd` varchar(255) DEFAULT NULL,
                  `metadesc` text,
                  `time` int(11) NOT NULL DEFAULT '0',
                  `ordering` int(3) NOT NULL DEFAULT '0',
                  `step_access` int(3) NOT NULL,
                  `final_lesson` tinyint(2) NOT NULL DEFAULT '0',
                  `forum_kunena_generatedt` tinyint(4) NOT NULL DEFAULT '0',
                  `groups_access` text,
                  `duration` varchar(255) DEFAULT '',
                  `description` longtext,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_taskcategory` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) DEFAULT NULL,
                  `published` tinyint(1) NOT NULL DEFAULT '1',
                  `description` text,
                  `image` varchar(255) DEFAULT NULL,
                  `listorder` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_taskcategoryrel` (
                  `parent_id` int(11) NOT NULL DEFAULT '1',
                  `child_id` int(11) NOT NULL DEFAULT '1'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_task_kunenacomment` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `id_lesson` int(11) DEFAULT NULL,
                  `thread` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_viewed_lesson` (
                  `id` int(3) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11),
                  `lesson_id` text,
                  `module_id` text NOT NULL,
                  `completed` tinyint(2) DEFAULT NULL,
                  `date_completed` datetime NOT NULL,
                  `date_last_visit` datetime NOT NULL,
                  `pid` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_logs` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `userid` int(11) NOT NULL DEFAULT '0',
                  `productid` int(11) NOT NULL DEFAULT '0',
                  `emailname` text NOT NULL,
                  `emailid` int(11) NOT NULL,
                  `to` text NOT NULL,
                  `subject` text NOT NULL,
                  `body` text NOT NULL,
                  `buy_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `send_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `download_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `buy_type` varchar(255) NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_projects` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `course_id` int(11) NOT NULL,
                  `author_id` int(11) NOT NULL,
                  `title` varchar(255) NOT NULL,
                  `description` text,
                  `file` varchar(100) NOT NULL,
                  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated` datetime NOT NULL,
                  `start` datetime NOT NULL,
                  `end` datetime NOT NULL,
                  `layout` varchar(100) NOT NULL,
                  `published` int(11) NOT NULL,
                   PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_project_results` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `project_id` int(11) NOT NULL,
                  `student_id` int(11) NOT NULL,
                  `course_id` int(11) NOT NULL,
                  `lesson_id` int(11) NOT NULL,
                  `file` varchar(100) NOT NULL,
                  `score` varchar(10) NOT NULL,
                  `desc` text NOT NULL,
                  `created_date` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_fields` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `type` varchar(255) NOT NULL,
                    `field_code` varchar(255) NOT NULL,
                    `published` tinyint(1) NOT NULL,
                    `required` tinyint(1) NOT NULL,
                    `ordering` int(10),
                    `group_id` int(10),
                    `options` LONGTEXT NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_groups` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `published` tinyint(1) NOT NULL,
                    `ordering` int(10),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_fields_results` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `user_id` int(10),
                    `field_id` int(10) NOT NULL,
                    `value` LONGTEXT NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_viewed_lesson_time` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `user_id` int(11) NOT NULL,
                    `lesson_id` int(11) NOT NULL,
                    `viewed_time` varchar(255) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_scorm_items` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `course_id` int(11) NOT NULL,
                  `author_id` int(11) NOT NULL,
                  `title` varchar(255) NOT NULL,
                  `description` text,
                  `file` varchar(100) NOT NULL,
                  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated` datetime NOT NULL,
                  `start` datetime NOT NULL,
                  `end` datetime NOT NULL,
                  `layout` varchar(100) NOT NULL,
                  `published` int(11) NOT NULL,
                   PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_scorm` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `lesson_id` int(11) NOT NULL,
                  `package` varchar(255) NOT NULL,
                  `storage` varchar(40) NOT NULL DEFAULT 'local',
                  `scormtype` varchar(20) NOT NULL,
                  `version` varchar(20) NOT NULL,
                  `grademethod` int(20) NOT NULL,
                  `passing_score` int(20) NOT NULL,
                  `entry` int(11) NOT NULL,
                  `launch` int(20) NOT NULL,
                   PRIMARY KEY (`id`),
                      KEY `lesson_id` (`lesson_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_scorm_scoes` (
                  `id` bigint(10) NOT NULL AUTO_INCREMENT,
                  `scorm_id` bigint(10) NOT NULL DEFAULT '0',
                  `manifest` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                  `organization` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `parent` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `launch` longtext COLLATE utf8_unicode_ci NOT NULL,
                  `scormtype` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                   PRIMARY KEY (`id`),
                      KEY `scorm_id` (`scorm_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_scorm_scoes_data` (
                  `id` bigint(10) NOT NULL AUTO_INCREMENT,
                  `sco_id` bigint(10) NOT NULL DEFAULT '0',
                  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                   PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_scorm_scoes_track` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `userid` int(11) NOT NULL DEFAULT '0',
                  `scorm_id` int(11) NOT NULL DEFAULT '0',
                  `sco_id` int(11) NOT NULL,
                  `attempt` int(11) NOT NULL DEFAULT '1',
                  `element` varchar(255) NOT NULL DEFAULT '',
                  `value` text NOT NULL,
                  `timemodified` int(11) NOT NULL DEFAULT '0',
                   PRIMARY KEY (`id`),
                      KEY `scoes_track` (`userid`,`scorm_id`,`sco_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_scorm_seq_mapinfo` (
                  `id` bigint(10) NOT NULL AUTO_INCREMENT,
                  `sco_id` bigint(10) NOT NULL DEFAULT '0',
                  `objectiveid` bigint(10) NOT NULL DEFAULT '0',
                  `targetobjectiveid` bigint(10) NOT NULL DEFAULT '0',
                  `readsatisfiedstatus` tinyint(1) NOT NULL DEFAULT '1',
                  `readnormalizedmeasure` tinyint(1) NOT NULL DEFAULT '1',
                  `writesatisfiedstatus` tinyint(1) NOT NULL DEFAULT '0',
                  `writenormalizedmeasure` tinyint(1) NOT NULL DEFAULT '0',
                   PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_scorm_seq_objective` (
                  `id` bigint(10) NOT NULL AUTO_INCREMENT,
                  `sco_id` bigint(10) NOT NULL DEFAULT '0',
                  `primaryobj` tinyint(1) NOT NULL DEFAULT '0',
                  `objectiveid` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `satisfiedbymeasure` tinyint(1) NOT NULL DEFAULT '1',
                  `minnormalizedmeasure` float(11,4) NOT NULL DEFAULT '0.0000',
                   PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_scorm_seq_rolluprule` (
                  `id` bigint(10) NOT NULL AUTO_INCREMENT,
                  `sco_id` bigint(10) NOT NULL DEFAULT '0',
                  `childactivityset` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `minimumcount` bigint(10) NOT NULL DEFAULT '0',
                  `minimumpercent` float(11,4) NOT NULL DEFAULT '0.0000',
                  `conditioncombination` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'all',
                  `action` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                   PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_scorm_seq_rolluprulecond` (
                  `id` bigint(10) NOT NULL AUTO_INCREMENT,
                  `sco_id` bigint(10) NOT NULL DEFAULT '0',
                  `rollupruleid` bigint(10) NOT NULL DEFAULT '0',
                  `operator` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'noOp',
                  `cond` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                   PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_scorm_seq_rulecond` (
                  `id` bigint(10) NOT NULL AUTO_INCREMENT,
                  `sco_id` bigint(10) NOT NULL DEFAULT '0',
                  `ruleconditionsid` bigint(10) NOT NULL DEFAULT '0',
                  `referencedobjective` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `measurethreshold` float(11,4) NOT NULL DEFAULT '0.0000',
                  `operator` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'noOp',
                  `cond` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'always',
                   PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__guru_scorm_seq_ruleconds` (
                  `id` bigint(10) NOT NULL AUTO_INCREMENT,
                  `sco_id` bigint(10) NOT NULL DEFAULT '0',
                  `conditioncombination` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'all',
                  `ruletype` tinyint(2) NOT NULL DEFAULT '0',
                  `action` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                   PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->setQuery($sql);
        $db->execute();


        if (!JFolder::exists(JPATH_SITE . "/media/scorm")) {
            JFolder::create(JPATH_SITE . "/media/scorm");

            copy(JPATH_SITE . "/media/index.html", JPATH_SITE . "/media/scorm/index.html");
        }

        if (!JFolder::exists(JPATH_SITE . "/media/scorm/lessons")) {
            JFolder::create(JPATH_SITE . "/media/scorm/lessons");

            copy(JPATH_SITE . "/media/index.html", JPATH_SITE . "/media/scorm/lessons/index.html");
        }


        $sql = "ALTER TABLE `#__guru_authors_commissions` CHANGE `price` `price` FLOAT(11) NOT NULL;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "ALTER TABLE `#__guru_authors_commissions` CHANGE `price_paid` `price_paid` FLOAT(11) NOT NULL;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "ALTER TABLE `#__guru_authors_commissions` CHANGE `amount_paid_author` `amount_paid_author` FLOAT(11) NOT NULL;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "ALTER TABLE `#__guru_program` CHANGE `author` `author` VARCHAR(255) NOT NULL;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "ALTER TABLE `#__guru_mycertificates` CHANGE `author_id` `author_id` VARCHAR(255) NOT NULL;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "ALTER TABLE `#__guru_question_answers` CHANGE `answer_content_text` `answer_content_text` text NOT NULL;";
        $db->setQuery($sql);
        $db->execute();


        $sql = "SHOW COLUMNS FROM #__guru_certificates";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if (isset($result)) {
            if (!in_array("library_pdf", $result)) {
                $sql = "ALTER TABLE `#__guru_certificates` ADD `library_pdf` tinyint(2) DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("templatespdf", $result)) {
                $sql = "ALTER TABLE `#__guru_certificates` ADD `templatespdf` TEXT NOT NULL";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("course_id", $result)) {
                $sql = "ALTER TABLE `#__guru_certificates` ADD `course_id` int(10) DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_category";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if (isset($result)) {
            if (!in_array("icon", $result)) {
                $sql = "ALTER TABLE `#__guru_category` ADD `icon` varchar(255) DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("groups", $result)) {
                $sql = "ALTER TABLE `#__guru_category` ADD `groups` TEXT NOT NULL";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("language", $result)) {
                $sql = "ALTER TABLE `#__guru_category` ADD `language` varchar(255) DEFAULT '*'";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_customer";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if (isset($result)) {
            if (!in_array("image", $result)) {
                $sql = "ALTER TABLE `#__guru_customer` ADD `image` varchar(255) DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("sequential_courses", $result)) {
                $sql = "ALTER TABLE `#__guru_customer` ADD `sequential_courses` TEXT";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_jump";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if (isset($result)) {
            if (!in_array("module_id1", $result)) {
                $sql = "ALTER TABLE `#__guru_jump` ADD `module_id1` int(10) DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("type_selected", $result)) {
                $sql = "ALTER TABLE `#__guru_jump` ADD `type_selected` varchar(255) DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
        }


        $sql = "SHOW COLUMNS FROM #__guru_program";
        $db->setQuery($sql);
        $result = $db->loadColumn();
        if (isset($result)) {
            if (!in_array("image_avatar", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `image_avatar` varchar(255) DEFAULT NULL";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("status", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `status` int(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("groups_access", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `groups_access` TEXT";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("split_commissions", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `split_commissions` int(2) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("after_hours", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `after_hours` int(11) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("reset_on_renew", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `reset_on_renew` int(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("custom_page_url", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `custom_page_url` varchar(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("record_hour", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `record_hour` varchar(5) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("record_min", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `record_min` varchar(5) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("lesson_view_confirm", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `lesson_view_confirm` int(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("course_completed_term", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `course_completed_term` int(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("avg_certificate_course_term", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `avg_certificate_course_term` int(11) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("record_hour_course_term", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `record_hour_course_term` varchar(5) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("record_min_course_term", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `record_min_course_term` varchar(5) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("lessons_per_release", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `lessons_per_release` int(2) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("free_limit", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `free_limit` varchar(10) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("og_tags", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `og_tags` LONGTEXT NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("mailchimp_api", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `mailchimp_api` varchar(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("mailchimp_list_id", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `mailchimp_list_id` varchar(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("mailchimp_auto", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `mailchimp_auto` varchar(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("mail_purchase_subject", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `mail_purchase_subject` varchar(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("mail_purchase_template", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `mail_purchase_template` LONGTEXT";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("js_groups", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `js_groups` varchar(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("groups_acl", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `groups_acl` TEXT NOT NULL";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("custom_certificate", $result)) {
                $sql = "ALTER TABLE `#__guru_program` ADD `custom_certificate` int(3) DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_config";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if (isset($result)) {
            if (!in_array("license_number", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `license_number` varchar(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("gurujomsocialregstudent", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `gurujomsocialregstudent` tinyint(4) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("indicate_quiz", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `indicate_quiz` INT(2) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("gurujomsocialregteacher", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `gurujomsocialregteacher` tinyint(4) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("gurujomsocialprofilestudent", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `gurujomsocialprofilestudent` tinyint(4) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("gurujomsocialprofileteacher", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `gurujomsocialprofileteacher` tinyint(4) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("gurujomsocialregstudentmprof", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `gurujomsocialregstudentmprof` tinyint(4) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("gurujomsocialregteachermprof", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `gurujomsocialregteachermprof` tinyint(4) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("installed_plugin_user", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `installed_plugin_user` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("invoice_issued_by", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `invoice_issued_by` text";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("guru_turnoffbootstrap", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `guru_turnoffbootstrap` tinyint(4) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("template_emails", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `template_emails` text NOT NULL";
                $db->setQuery($sql);
                $db->execute();

                $templates_emails_array = array("approve_subject" => "Approved your course [COURSE_NAME]", "approve_body" => "<p>Dear [AUTHOR_NAME],</p>\r\n<p>We are happy to inform you that we've approved your course [COURSE_NAME]!</p>\r\n<p>[SITE_NAME] admin</p>", "unapprove_subject" => "Unapproved your course [COURSE_NAME]", "unapprove_body" => "<p>Dear [AUTHOR_NAME],</p>\r\n<p>We're are sorry to inform you that your course [COURSE_NAME] was unapproved.</p>\r\n<p>[SITE_NAME] admin</p>", "ask_approve_subject" => "Approve [COURSE_NAME] course", "ask_approve_body" => "<p>Dear admin,</p>\r\n<p>New course was submitted by:[AUTHOR_NAME]</p>\r\n<p>Course name: [COURSE_NAME]</p>\r\n<p>**********************</p>\r\n<p>Approve this course: [COURSE_APPROVE_URL]</p>\r\n<p>Thank you!</p>", "approved_teacher_subject" => "Approved as teacher", "approved_teacher_body" => "<p>Dear <span class=\"error\">[AUTHOR_NAME]</span>,</p>\r\n<p>Thank you for applying to be a teacher at <span class=\"error\">[SITE_NAME]</span></p>\r\n<p>Your application has been approved. You may login to our site and submit your courses.</p>\r\n<p>Best regards,</p>\r\n<p><span class=\"error\">[SITE_NAME]</span></p>", "pending_teacher_subject" => "Registration in pending.", "pending_teacher_body" => "<p>Hello [AUTHOR_NAME],</p>\r\n<p>Thank you for registering at [SITE_NAME].</p>\r\n<p>Your Teacher Application is waiting admin approval, once that's done you'll get access to the Teacher Interface. You will be notified when you're Teacher Application is approved.</p>\r\n<p>In the meantime you are registered as any other user and may login to other parts of the [SITE_NAME] site using the following username and password:</p>\r\n<p>Username: [USERNAME]<br /> Password: [PASSWORD]</p>", "ask_teacher_subject" => "You have a new teacher application", "ask_teacher_body" => "<p>Dear admin,</p>\r\n<p>You have a new teacher application:</p>\r\n<p>Name: [AUTHOR_NAME]</p>\r\n<p>Thank you!</p>", "new_teacher_subject" => "New teacher has registered", "new_teacher_body" => "<p>Dear admin,</p>\r\n<p>New teacher has registered:</p>\r\n<p>Name: [AUTHOR_NAME]</p>\r\n<p>Thank you!</p>", "new_student_subject" => "New student has registered", "new_student_body" => "<p>Dear admin,</p>\r\n<p>New student has registered:</p>\r\n<p>Name: [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME]</p>\r\n<p>Thank you!</p>", "approve_order_subject" => "Approved Order", "approve_order_body" => "<p>Hello [STUDENT_FIRST_NAME],</p>\r\n<p>Your [COURSE_NAME] order has been approved and you can access the course now.</p>\r\n<p>Login to the [SITE_NAME] to view it.</p>\r\n<p>Kindest regards,</p>\r\n<p>[SITE_NAME] administrators</p>", "pending_order_subject" => "Pending Order", "pending_order_body" => "<p>Hello,</p>\r\n<p>There is a pending order: <br /> Course name: [COURSE_NAME]<br /> Order number: [ORDER_NUMBER]<br /> Student name: [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME] </p>\r\n<p>To approve the order, please login to the list of orders: [GURU_ORDER_LIST_URL] and approve it.</p>", "new_student_enrolled_subject" => "New Student Enrolled", "new_student_enrolled_body" => "<p>Dear admin,</p>\r\n<p>A new student has enrolled to this course - [COURSE_NAME].</p>\r\n<p>Student Name: [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME]</p>\r\n<p>Thank you!</p>", "teacher_completed_course_subject" => "[STUDENT_NAME] completed [COURSE_NAME] successfully", "teacher_completed_course_body" => "<p>Dear [TEACHER_FULL_NAME],</p>\r\n<p>[STUDENT_NAME] completed [COURSE_NAME] successfully.</p>\r\n<p>Thank you,</p>\r\n<p>[SITE_NAME]</p>", "admin_completed_course_subject" => "[STUDENT_NAME] completed [COURSE_NAME] successfully", "admin_completed_course_body" => "<p>Dear [ADMIN_NAME],</p>\r\n<p>[STUDENT_NAME] completed [COURSE_NAME] successfully.</p>\r\n<p>Thank you,</p>\r\n<p>[SITE_NAME]</p>");

                $templates_emails  = json_encode($templates_emails_array);
                $sql = "UPDATE `#__guru_config` set `template_emails` ='" . addslashes($templates_emails) . "' WHERE id=1";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("terms_cond_student", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `terms_cond_student` int(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("terms_cond_teacher", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `terms_cond_teacher` int(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("terms_cond_student_content", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `terms_cond_student_content` text NOT NULL";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("terms_cond_teacher_content", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `terms_cond_teacher_content` text NOT NULL";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("course_is_free_show", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `course_is_free_show` tinyint(2) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("admin_email", $result)) {
                $sql = "select u.`id` from #__users u, #__user_usergroup_map ugm where u.`id`=ugm.`user_id` and ugm.`group_id`='8' LIMIT 0,1";
                $db->setQuery($sql);
                $db->execute();
                $default_email = $db->loadColumn();
                $default_email = @$default_email["0"];

                $sql = "ALTER TABLE `#__guru_config` ADD `admin_email` varchar(255) NOT NULL DEFAULT '" . $default_email . "'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("mailchimp_teacher_api", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `mailchimp_teacher_api` varchar(255)";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("mailchimp_teacher_list_id", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `mailchimp_teacher_list_id` varchar(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("mailchimp_teacher_auto", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `mailchimp_teacher_auto` int(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("mailchimp_student_api", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `mailchimp_student_api` varchar(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("mailchimp_student_list_id", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `mailchimp_student_list_id` varchar(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("mailchimp_student_auto", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `mailchimp_student_auto` int(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("seo", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `seo` TEXT";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("captcha", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `captcha` INT(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("auto_approve", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `auto_approve` INT(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("youtube_key", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `youtube_key` VARCHAR(255) NOT NULL";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("course_certificate", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `course_certificate` INT(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("course_exercises", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `course_exercises` INT(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("rtl", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `rtl` INT(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("thousands_separator", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `thousands_separator` INT(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("decimals_separator", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `decimals_separator` INT(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("show_decimals", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `show_decimals` INT(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("secure_key", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `secure_key` VARCHAR(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("payed_plugins", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `payed_plugins` VARCHAR(255) NOT NULL DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("guru_turnoffuikit", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `guru_turnoffuikit` INT(2) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("teacher_custom_fields", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `teacher_custom_fields` LONGTEXT NOT NULL";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("student_custom_fields", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `student_custom_fields` LONGTEXT NOT NULL";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("certificate_html", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `certificate_html` INT(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("certificate_pdf", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `certificate_pdf` INT(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("certificate_share", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `certificate_share` INT(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("certificate_email", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `certificate_email` INT(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("student_params", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `student_params` LONGTEXT";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("student_menus", $result)) {
                $sql = "ALTER TABLE `#__guru_config` ADD `student_menus` LONGTEXT";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_media_categories";
        $db->setQuery($sql);
        $result = $db->loadColumn();
        if (isset($result)) {
            if (!in_array("user_id", $result)) {
                $sql = "ALTER TABLE `#__guru_media_categories` ADD `user_id` int(11)";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_media";
        $db->setQuery($sql);
        $result = $db->loadColumn();
        if (isset($result)) {
            if (!in_array("hide_name", $result)) {
                $sql = "ALTER TABLE `#__guru_media` ADD `hide_name` int(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("author", $result)) {
                $sql = "ALTER TABLE `#__guru_media` ADD `author` int(11) NOT NULL ";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("image", $result)) {
                $sql = "ALTER TABLE `#__guru_media` ADD `image` text ";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("description", $result)) {
                $sql = "ALTER TABLE `#__guru_media` ADD `description` text ";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("uploaded_tab", $result)) {
                $sql = "ALTER TABLE `#__guru_media` ADD `uploaded_tab` int(3) NOT NULL DEFAULT '-1'";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_promos";
        $db->setQuery($sql);
        $result = $db->loadColumn();
        if (isset($result)) {
            if (!in_array("courses_ids", $result)) {
                $sql = "ALTER TABLE `#__guru_promos` ADD `courses_ids` text ";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("one_time", $result)) {
                $sql = "ALTER TABLE `#__guru_promos` ADD `one_time` int(3) NOT NULL DEFAULT '0' ";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_quiz";
        $db->setQuery($sql);
        $result = $db->loadColumn();
        if (isset($result)) {
            if (!in_array("author", $result)) {
                $sql = "ALTER TABLE `#__guru_quiz` ADD `author` int(11) NOT NULL ";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("questions_per_page", $result)) {
                $sql = "ALTER TABLE `#__guru_quiz` ADD `questions_per_page` int(11) NOT NULL DEFAULT '10'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("pass_message", $result)) {
                $sql = "ALTER TABLE `#__guru_quiz` ADD `pass_message` text NOT NULL";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("fail_message", $result)) {
                $sql = "ALTER TABLE `#__guru_quiz` ADD `fail_message` text NOT NULL";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("student_failed", $result)) {
                $sql = "ALTER TABLE `#__guru_quiz` ADD `student_failed` INT(2) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("show_correct_ans", $result)) {
                $sql = "ALTER TABLE `#__guru_quiz` ADD `show_correct_ans` INT(3) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("pending_message", $result)) {
                $sql = "ALTER TABLE `#__guru_quiz` ADD `pending_message` LONGTEXT";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("reset_time", $result)) {
                $sql = "ALTER TABLE `#__guru_quiz` ADD `reset_time` INT(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!in_array("retake_passed_quiz", $result)) {
                $sql = "ALTER TABLE `#__guru_quiz` ADD `retake_passed_quiz` INT(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_authors";
        $db->setQuery($sql);
        $result = $db->loadColumn();
        if (isset($result)) {
            if (!in_array("enabled", $result)) {
                $sql = "ALTER TABLE `#__guru_authors` ADD `enabled` tinyint(4) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
        }
        if (isset($result)) {
            if (!in_array("commission_id", $result)) {
                $sql = "ALTER TABLE `#__guru_authors` ADD `commission_id` int(11) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
        }
        if (isset($result)) {
            if (!in_array("paypal_email", $result)) {
                $sql = "ALTER TABLE `#__guru_authors` ADD `paypal_email` varchar(100)";
                $db->setQuery($sql);
                $db->execute();
            }
        }
        if (isset($result)) {
            if (!in_array("paypal_other_information", $result)) {
                $sql = "ALTER TABLE `#__guru_authors` ADD `paypal_other_information` text";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        if (isset($result)) {
            if (!in_array("paypal_option", $result)) {
                $sql = "ALTER TABLE `#__guru_authors` ADD `paypal_option`  tinyint(1) NOT NULL DEFAULT '1'";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_task";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if (isset($result)) {
            if (!in_array("groups_access", $result)) {
                $sql = "ALTER TABLE `#__guru_task` ADD `groups_access` text";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("duration", $result)) {
                $sql = "ALTER TABLE `#__guru_task` ADD `duration` varchar(255) DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("description", $result)) {
                $sql = "ALTER TABLE `#__guru_task` ADD `description` LONGTEXT";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("css", $result)) {
                $sql = "ALTER TABLE `#__guru_task` ADD `css` varchar(255) DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("show_view_status", $result)) {
                $sql = "ALTER TABLE `#__guru_task` ADD `show_view_status` INT(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_order";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if (isset($result)) {
            if (!in_array("card_digit", $result)) {
                $sql = "ALTER TABLE `#__guru_order` ADD `card_digit` varchar(255) DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!in_array("card_type", $result)) {
                $sql = "ALTER TABLE `#__guru_order` ADD `card_type` varchar(255) DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_viewed_lesson";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if (isset($result)) {
            if (!in_array("viewed_time", $result)) {
                $sql = "ALTER TABLE `#__guru_viewed_lesson` ADD `viewed_time` varchar(255) DEFAULT ''";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_kunena_forum";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if (isset($result)) {
            if (!in_array("kunena_category", $result)) {
                $sql = "ALTER TABLE `#__guru_kunena_forum` ADD `kunena_category` INT(3) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "SHOW COLUMNS FROM #__guru_mycertificates";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if (isset($result)) {
            if (!in_array("completed", $result)) {
                $sql = "ALTER TABLE `#__guru_mycertificates` ADD `completed` INT(2) NOT NULL DEFAULT '0'";
                $db->setQuery($sql);
                $db->execute();
            }
        }

        $sql = "update #__extensions set `enabled`='1' where `type`='plugin' and `element`='recaptcha' and `folder`='captcha'";
        $db->setQuery($sql);
        $db->execute();

        $sql = "update #__guru_config set `open_target`='1'";
        $db->setQuery($sql);
        $db->execute();

        echo '<script language="javascript" type="text/javascript">
                setTimeout(function(){
                    window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=index&tmpl=component";
                }, 1000);
            </script>';
    }

    public function indexDatabase()
    {
        $this->addTableIndex('#__guru_authors', array(
            'userid',
            'enabled',
            'commission_id',
            'paypal_option',
        ));
        $this->addTableIndex('#__guru_authors_commissions', array(
            'author_id',
            'course_id',
            'plan_id',
            'order_id',
            'customer_id',
            'commission_id',
        ));
        $this->addTableIndex('#__guru_authors_commissions_history', array(
            'author_id',
        ));
        $this->addTableIndex('#__guru_buy_courses', array(
            'userid',
            'order_id',
            'course_id',
        ));
        $this->addTableIndex('#__guru_category', array(
            'published',
        ));
        $this->addTableIndex('#__guru_categoryrel', array(
            'parent_id',
            'child_id',
        ));
        $this->addTableIndex('#__guru_certificates', array(
            'course_id',
        ));
        $this->addTableIndex('#__guru_days', array(
            'pid',
            'published',
        ));
        $this->addTableIndex('#__guru_emails_pending', array(
            'mail_id',
            'pid',
            'send',
        ));
        $this->addTableIndex('#__guru_fields', array(
            'group_id',
            'published',
        ));
        $this->addTableIndex('#__guru_fields_results', array(
            'user_id',
            'field_id',
        ));
        $this->addTableIndex('#__guru_groups', array(
            'published',
        ));
        $this->addTableIndex('#__guru_jomsocialstream', array(
            'userid',
        ));
        $this->addTableIndex('#__guru_kunena_courseslinkage', array(
            'idcourse',
            'catidkunena',
        ));
        $this->addTableIndex('#__guru_kunena_lessonslinkage', array(
            'idlesson',
            'catidkunena',
        ));
        $this->addTableIndex('#__guru_logs', array(
            'userid',
            'productid',
            'emailid',
        ));
        $this->addTableIndex('#__guru_media', array(
            'type',
            'uploaded',
            'author',
            'published',
        ));
        $this->addTableIndex('#__guru_mediarel', array(
            'type',
            'type_id',
            'media_id',
            'mainmedia',
            'text_no',
            'layout',
            'access',
        ));
        $this->addTableIndex('#__guru_media_categories', array(
            'parent_id',
            'child_id',
            'published',
            'user_id',
        ));
        $this->addTableIndex('#__guru_mycertificates', array(
            'course_id',
            'author_id',
            'user_id',
            'completed',
        ));
        $this->addTableIndex('#__guru_order', array(
            'userid',
            'status',
            'published',
            'order_date',
        ));
        $this->addTableIndex('#__guru_plugin_settings', array(
            'pluginid',
        ));
        $this->addTableIndex('#__guru_program', array(
            'catid',
            'published',
        ));
        $this->addTableIndex('#__guru_programstatus', array(
            'userid',
            'pid',
        ));
        $this->addTableIndex('#__guru_program_plans', array(
            'product_id',
            'plan_id',
            'default',
        ));
        $this->addTableIndex('#__guru_program_reminders', array(
            'product_id',
            'emailreminder_id',
            'send',
        ));
        $this->addTableIndex('#__guru_program_renewals', array(
            'product_id',
            'plan_id',
            'default',
        ));
        $this->addTableIndex('#__guru_projects', array(
            'course_id',
            'author_id',
            'published',
        ));
        $this->addTableIndex('#__guru_project_results', array(
            'project_id',
            'student_id',
            'course_id',
            'lesson_id',
        ));
        $this->addTableIndex('#__guru_promos', array(
            'published',
        ));
        $this->addTableIndex('#__guru_questions_v3', array(
            'qid',
            'type',
            'published',
        ));
        $this->addTableIndex('#__guru_question_answers', array(
            'question_id',
        ));
        $this->addTableIndex('#__guru_quiz', array(
            'published',
            'author',
            'is_final',
        ));
        $this->addTableIndex('#__guru_quizzes_final', array(
            'qid',
            'published',
        ));
        $this->addTableIndex('#__guru_quiz_essay_mark', array(
            'question_id',
            'user_id',
        ));
        $this->addTableIndex('#__guru_quiz_question_taken_v3', array(
            'user_id',
            'quiz_id',
            'pid',
        ));
        $this->addTableIndex('#__guru_quiz_taken_v3', array(
            'user_id',
            'quiz_id',
            'pid',
            'question_id',
            'id_question_taken',
        ));
        $this->addTableIndex('#__guru_scorm', array(
            'lesson_id',
        ));
        $this->addTableIndex('#__guru_scorm_items', array(
            'course_id',
            'author_id',
            'published',
        ));
        $this->addTableIndex('#__guru_scorm_scoes', array(
            'scorm_id',
        ));
        $this->addTableIndex('#__guru_scorm_scoes_data', array(
            'sco_id',
        ));
        $this->addTableIndex('#__guru_subplan', array(
            'published',
        ));
        $this->addTableIndex('#__guru_subremind', array(
            'published',
        ));
        $this->addTableIndex('#__guru_task', array(
            'published',
        ));
        $this->addTableIndex('#__guru_taskcategory', array(
            'published',
        ));
        $this->addTableIndex('#__guru_taskcategoryrel', array(
            'parent_id',
            'child_id',
        ));
        $this->addTableIndex('#__guru_task_kunenacomment', array(
            'id_lesson',
            'thread',
        ));
        $this->addTableIndex('#__guru_viewed_lesson', array(
            'user_id',
            'completed',
            'pid',
        ));
        $this->addTableIndex('#__guru_viewed_lesson_time', array(
            'user_id',
            'lesson_id',
        ));

        echo '<script language="javascript" type="text/javascript">
                setTimeout(function(){
                    window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=default&tmpl=component";
                }, 1000);
            </script>';
    }

    protected function addTableIndex($tableName, $indexes)
    {
        $db = JFactory::getDbo();
        $query = "SHOW INDEX FROM `$tableName`;";
        $rows = $db->setQuery($query)->loadObjectList();
        $indexed = array_map(function ($row) {
            return $row->Key_name;
        }, $rows);

        foreach ($indexes as $index) {
            if (in_array($index, $indexed)) {
                continue;
            }

            try {
                $db->setQuery("ALTER TABLE `$tableName` ADD INDEX `$index` (`$index`)");
                $db->execute();
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }

    function startDefaultValues()
    {
        $db = JFactory::getDBO();

        $sql = "select u.`id` from #__users u, #__user_usergroup_map ugm where u.`id`=ugm.`user_id` and ugm.`group_id`='8' LIMIT 0,1";
        $db->setQuery($sql);
        $db->execute();
        $default_email = $db->loadColumn();
        $default_email = @$default_email["0"];

        $sql = "SELECT `id` FROM `#__guru_config` limit 1";
        $db->setQuery($sql);
        $textafterreg = $db->loadColumn();

        if (intval($textafterreg) == 0) {
            $config = new JConfig();
            $fromemail = $config->mailfrom;
            $fromname = $config->sitename;

            $ctgspage_array = array("ctgslayout" => "1", "ctgscols" => "2", "ctgs_image_size" => "800", "ctgs_image_size_type" => "0", "ctgs_image_alignment" => "0", "ctgs_wrap_image" => "1", "ctgs_description_length" => "120", "ctgs_description_type" => "0", "ctgs_description_alignment"  => "0", "ctgs_read_more" => "1", "ctgs_read_more_align" => "0", "ctgs_show_empty_catgs" => "1", "ctgs_description_mode" => "0");

            $st_ctgspage_array = array("ctgs_page_title" => "title_guru", "ctgs_categ_name" => "name_guru", "ctgs_image" => "image_guru", "ctgs_description" =>  "description_guru", "ctgs_st_read_more" => "readon");

            $ctgpage_array = array("ctg_image_size" => "900", "ctg_image_size_type" => "0", "ctg_image_alignment" => "0", "ctg_description_length" => "250", "ctg_description_type" => "0", "ctg_description_alignment" => "0", "ctg_description_mode" => "0");

            $st_ctgpage_array = array("ctg_name" => "title_guru", "ctg_image" => "image_guru", "ctg_description" => "description_guru_bigger", "ctg_sub_title" => "sub_title_guru");

            $psgspage_array = array("courseslayout" => "1", "coursescols" => "2", "courses_image_size" => "100", "courses_image_size_type" => "0", "courses_image_alignment" => "0", "courses_wrap_image" => "1", "courses_description_length" => "120", "courses_description_type" => "0", "courses_description_alignment" => "0", "courses_read_more" => "1", "courses_read_more_align" => "0", "courses_description_mode" => "0");

            $st_psgspage_array = array("courses_page_title" => "title_guru", "courses_name" => "name_guru", "courses_image" => "image_guru", "courses_description" => "description_guru", "courses_st_read_more" => "readon");

            $psgpage_array = array("course_image_size" => "150", "course_image_size_type" => "0", "course_image_alignment" => "0", "course_wrap_image" => "1", "course_author_name_show" => "0", "course_released_date" => "0", "course_level" => "0", "course_price" => "1", "course_price_type" => "0", "course_table_contents" => "0", "course_description_show" => "0", "course_tab_price" => "0", "course_author" => "0", "course_requirements" => "0", "course_buy_button" => "0", "course_buy_button_location" => "2", "show_course_image" => "0", "show_all_cloase_all" => "0", "show_course_studentamount" => "0", "duration" => "0", "quiz_status" => "0");

            $st_psgpage_array = array("course_name" => "title_guru", "course_image" => "image_guru", "course_top_field_name" => "field_name_guru", "course_top_field_value" => "field_value_guru", "course_tabs_module_name" => "name_guru", "course_tabs_step_name" => "step_name_guru", "course_description" => "description_guru", "course_price_field_name" => "field_name_guru", "course_price_field_value" => "field_value_guru", "course_author_name" => "title_guru", "course_author_bio" => "description_guru", "course_author_image" => "image_guru", "course_req_field_name" => "field_name_guru", "course_req_field_value" => "field_value_guru", "course_other_button" => "guru_buynow", "course_other_background" => "buy_background");

            $authorspage_array = array("authorslayout" => "1", "authorscols" => "2", "authors_image_size" => "75", "authors_image_size_type" => "0", "authors_image_alignment" => "0", "authors_wrap_image" => "1", "authors_description_length" => "300", "authors_description_type" => "0", "authors_description_alignment" => "0", "authors_read_more" => "0", "authors_read_more_align" => "1", "authors_description_mode" => "0");

            $st_authorspage_array = array("authors_page_title" => "title_guru", "authors_name" => "name_guru", "authors_image" => "image_guru", "authors_description" => "description_guru", "authors_st_read_more" => "readon");

            $authorpage_array = array("author_image_size" => "150", "author_image_size_type" => "0", "author_image_alignment" => "0", "author_wrap_image" => "0", "author_description_length" => "1000", "author_description_type" => "0", "author_description_alignment" => "0");

            $st_authorpage_array = array("author_name" => "name_guru", "author_image" => "image_guru", "author_description" => "description_guru", "author_st_read_more" => "readon", "teacher_aprove" => "1", "teacher_group" => "2", "teacher_add_media" => "0", "teacher_edit_media" => "0", "teacher_add_courses" => "0", "teacher_edit_courses" => "0", "teacher_add_quizzesfe" => "0", "teacher_edit_quizzesfe" => "0", "teacher_add_students" => "0", "teacher_edit_students" => "", "teacher_approve_courses" => "1");

            $templates_emails_array = array("approve_subject" => "Approved your course [COURSE_NAME]", "approve_body" => "<p>Dear [AUTHOR_NAME],</p>\r\n<p>We are happy to inform you that we've approved your course [COURSE_NAME]!</p>\r\n<p>[SITE_NAME] admin</p>", "unapprove_subject" => "Unapproved your course [COURSE_NAME]", "unapprove_body" => "<p>Dear [AUTHOR_NAME],</p>\r\n<p>We're are sorry to inform you that your course [COURSE_NAME] was unapproved.</p>\r\n<p>[SITE_NAME] admin</p>", "ask_approve_subject" => "Approve [COURSE_NAME] course", "ask_approve_body" => "<p>Dear admin,</p>\r\n<p>New course was submitted by:[AUTHOR_NAME]</p>\r\n<p>Course name: [COURSE_NAME]</p>\r\n<p>**********************</p>\r\n<p>Approve this course: [COURSE_APPROVE_URL]</p>\r\n<p>Thank you!</p>", "approved_teacher_subject" => "Approved as teacher", "approved_teacher_body" => "<p>Dear <span class=\"error\">[AUTHOR_NAME]</span>,</p>\r\n<p>Thank you for applying to be a teacher at <span class=\"error\">[SITE_NAME]</span></p>\r\n<p>Your application has been approved. You may login to our site and submit your courses.</p>\r\n<p>Best regards,</p>\r\n<p><span class=\"error\">[SITE_NAME]</span></p>", "pending_teacher_subject" => "Registration in pending.", "pending_teacher_body" => "<p>Hello [AUTHOR_NAME],</p>\r\n<p>Thank you for registering at [SITE_NAME].</p>\r\n<p>Your Teacher Application is waiting admin approval, once that's done you'll get access to the Teacher Interface. You will be notified when you're Teacher Application is approved.</p>\r\n<p>In the meantime you are registered as any other user and may login to other parts of the [SITE_NAME] site using the following username and password:</p>\r\n<p>Username: [USERNAME]<br /> Password: [PASSWORD]</p>", "ask_teacher_subject" => "You have a new teacher application", "ask_teacher_body" => "<p>Dear admin,</p>\r\n<p>You have a new teacher application:</p>\r\n<p>Name: [AUTHOR_NAME]</p>\r\n<p>Thank you!</p>", "new_teacher_subject" => "New teacher has registered", "new_teacher_body" => "<p>Dear admin,</p>\r\n<p>New teacher has registered:</p>\r\n<p>Name: [AUTHOR_NAME]</p>\r\n<p>Thank you!</p>");

            $ctgpage = json_encode($ctgpage_array);
            $st_ctgpage = json_encode($st_ctgpage_array);
            $ctgspage = json_encode($ctgspage_array);
            $st_ctgspage = json_encode($st_ctgspage_array);
            $psgspage = json_encode($psgspage_array);
            $st_psgspage = json_encode($st_psgspage_array);
            $psgpage = json_encode($psgpage_array);
            $st_psgpage = json_encode($st_psgpage_array);
            $authorspage = json_encode($authorspage_array);
            $st_authorspage = json_encode($st_authorspage_array);
            $authorpage = json_encode($authorpage_array);
            $st_authorpage = json_encode($st_authorpage_array);
            $templates_emails  = json_encode($templates_emails_array);

            $sql = "INSERT INTO `#__guru_config` (`id`, `currency`, `datetype`, `dificulty`, `influence`, `display_tasks`, `groupe_tasks`, `display_media`, `show_unpubl`, `btnback`, `btnhome`, `btnnext`, `dofirst`, `imagesin`, `videoin`, `audioin`, `docsin`, `filesin`, `fromname`, `fromemail`, `regemail`, `orderemail`, `ctgpage`, `st_ctgpage`, `ctgspage`, `st_ctgspage`, `psgspage`, `st_psgspage`, `psgpage`, `st_psgpage`, `authorspage`, `st_authorspage`, `authorpage`, `st_authorpage`, `video_display`, `audio_display`, `content_selling`, `open_target`, `st_donecolor`, `st_notdonecolor`, `st_txtcolor`, `st_width`, `st_height`, `progress_bar`, `lesson_window_size`, `default_video_size`, `lesson_window_size_back`, `last_check_date`, `hour_format`, `back_size_type`, `notification`, `show_bradcrumbs`,`show_powerd`, `currencypos`, `guru_ignore_ijseo`,`course_lesson_release`,`student_group`,`guru_turnoffjq`,`guru_turnoffbootstrap`, `gurujomsocialregstudent`, `gurujomsocialregteacher`, `gurujomsocialprofilestudent`, `gurujomsocialprofileteacher`, `gurujomsocialregstudentmprof`, `gurujomsocialregteachermprof`, `installed_plugin_user`,`template_emails`,`terms_cond_student`, `terms_cond_teacher`, `terms_cond_student_content`,`terms_cond_teacher_content`,`course_is_free_show`,`admin_email`, `youtube_key`) 
                
                VALUES (1, 'USD', 'Y-m-d H:i:s', 'Hard', 0, 1, 1, 1, 3, 0, 0, 0, 0, 'images/stories/guru', 'media/videos', 'media/audio', 'media/documents', 'media/files', '" . addslashes(trim($fromname)) . "', '" . addslashes(trim($fromemail)) . "', '0', '0', '" . addslashes(trim($ctgpage)) . "', '" . addslashes(trim($st_ctgpage)) . "', '" . addslashes(trim($ctgspage)) . "', '" . addslashes(trim($st_ctgspage)) . "', '" . addslashes(trim($psgspage)) . "', '" . addslashes(trim($st_psgspage)) . "', '" . addslashes(trim($psgpage)) . "', '" . addslashes(trim($st_psgpage)) . "', '" . addslashes(trim($authorspage)) . "', '" . addslashes(trim($st_authorspage)) . "', '" . addslashes(trim($authorpage)) . "', '" . addslashes(trim($st_authorpage)) . "', 0, 0, '<h2>You need to be a subscriber to access this lesson/file.</h2><p>Please select a subscription plan below and click Continue </p>', 1, '#66FF33', '#FF3399', '#FF9900', '200', '20', 0, '580x750', '400x670', '550x935', now(), 12, 1 , 0, 0, 1, 0,0,0,2,1,1, 0,0,0,0,'0','0', '0000-00-00 00:00:00','" . addslashes(trim($templates_emails)) . "', 0, 0,'', '', 0, '" . addslashes(trim($default_email)) . "', '')";
            $db->setQuery($sql);
            if (!$db->execute()) {
                echo "error";
            }
        }

        // add new email templates
        $sql = "select `template_emails` from #__guru_config limit 0,1";
        $db->setQuery($sql);
        $db->execute();
        $template_emails = $db->loadColumn();
        $template_emails = $template_emails["0"];
        $template_emails = json_decode($template_emails, true);

        if (!isset($template_emails["new_student_subject"])) {
            $template_emails["new_student_subject"] = "New student has registered";
        }

        if (!isset($template_emails["new_student_body"])) {
            $template_emails["new_student_body"] = "<p>Dear admin,</p>\r\n<p>New student has registered:</p>\r\n<p>Name: [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME]</p>\r\n<p>Thank you!</p>";
        }

        if (!isset($template_emails["new_student_enrolled_subject"])) {
            $template_emails["new_student_enrolled_subject"] = "New Student Enrolled";
        }

        if (!isset($template_emails["new_student_enrolled_body"])) {
            $template_emails["new_student_enrolled_body"] = "<p>Dear teacher,</p>\r\n<p>A new student has enrolled to this course - [COURSE_NAME].</p>\r\n<p>Student Name: [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME]</p>\r\n<p>Thank you!</p>";
        }

        if (!isset($template_emails["new_student_enrolled_admin_subject"])) {
            $template_emails["new_student_enrolled_admin_subject"] = "New Student Enrolled";
        }

        if (!isset($template_emails["new_student_enrolled_admin_body"])) {
            $template_emails["new_student_enrolled_admin_body"] = "<p>Dear admin,</p>\r\n<p>A new student has enrolled to this course - [COURSE_NAME].</p>\r\n<p>Student Name: [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME]</p>\r\n<p>Thank you!</p>";
        }

        if (!isset($template_emails["teacher_completed_course_subject"])) {
            $template_emails["teacher_completed_course_subject"] = "[STUDENT_NAME] completed [COURSE_NAME] successfully";
        }

        if (!isset($template_emails["teacher_completed_course_body"])) {
            $template_emails["teacher_completed_course_body"] = "<p>Dear [TEACHER_FULL_NAME],</p>\r\n<p>[STUDENT_NAME] completed [COURSE_NAME] successfully.</p>\r\n<p>Thank you,</p>\r\n<p>[SITE_NAME]</p>";
        }

        if (!isset($template_emails["admin_completed_course_subject"])) {
            $template_emails["admin_completed_course_subject"] = "[STUDENT_NAME] completed [COURSE_NAME] successfully";
        }

        if (!isset($template_emails["admin_completed_course_body"])) {
            $template_emails["admin_completed_course_body"] = "<p>Dear [ADMIN_NAME],</p>\r\n<p>[STUDENT_NAME] completed [COURSE_NAME] successfully.</p>\r\n<p>Thank you,</p>\r\n<p>[SITE_NAME]</p>";
        }

        $template_emails = json_encode($template_emails);
        $sql = "update #__guru_config set `template_emails`='" . addslashes($template_emails) . "'";
        $db->setQuery($sql);
        $db->execute();

        $sql = "select count(*) from #__guru_certificates";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if ($result["0"] == "0") {
            $sql = "INSERT INTO `#__guru_certificates` (`id`, `general_settings`, `design_background`, `design_background_color`, `design_text_color`, `avg_cert`, `templates1`, `templates2`, `templates3`,`templates4`, `subjectt3`, `subjectt4`, `font_certificate`) VALUES ('1', '1', 'images/stories/guru/certificates/thumbs/Cert-blue-color-back-no-seal.png', 'ACE0F6', '333333', '70', '<p align=\"center\">Student Name: [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME]<br />Certificate ID: [CERTIFICATE_ID]<br />Course name: [COURSE_NAME]<br />Completion Date: [COMPLETION_DATE]<br />Site name: [SITENAME] <br />Site URL: [SITEURL]<br />Teacher: [AUTHOR_NAME]</p>', '<p>Certificate of Satisfactory Completion<br /><br />[COURSE_NAME] Awarded to [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME] on [COMPLETION_DATE]<br /><br />This is a duplicate of the official Certificate of Completion on file for [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME], who successfully and satisfactorily completed all requirements of the [SITENAME] course [COURSE_NAME] with [AUTHOR_NAME] on [COMPLETION_DATE].<br /><br /></p>', '<p>Dear [STUDENT_FIRST_NAME],</p><p>&nbsp;</p><p>Your certificate for course[COURSE_NAME] that you completed on [COMPLETION_DATE], is now available for you to download and share.</p><p>Please visit our site to get your certificate:</p><p>&nbsp;&nbsp; [SITENAME] <br />&nbsp;</p>','<p>Hello,</p><p>Message from  <span>[STUDENT_FIRST_NAME]</span> <span>[STUDENT_LAST_NAME]</span>:</p><p><span>[CERT_MESSAGE]</span></p><p>Please click on the URL below to see the certificate</p><p><span>[CERT_URL]</span></p><p><span>[SITENAME]</span></p>', 'Certificate for [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME] from [SITENAME]', 'Certificate for [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME] from [SITENAME]', 'Arial');";
            $db->setQuery($sql);
            $db->execute();
        }

        $sql = "select count(*) from #__guru_kunena_forum";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if ($result["0"] == "0") {
            $sql = "INSERT INTO `#__guru_kunena_forum` (`forumboardcourse`, `forumboardlesson`, `forumboardteacher`, `deleted_boards`,`allow_stud`,`allow_edit`,`allow_delete`) VALUES (0, 0, 0, 0,0,0,0);";
            $db->setQuery($sql);
            $db->execute();
        }

        $sql = "select count(*) from #__guru_subplan";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if ($result["0"] == "0") {
            $sql = "INSERT INTO `#__guru_subplan` (`id`, `name`, `term`, `period`, `published`, `ordering`) VALUES
                        (1, 'Unlimited', 0, 'hours', '1', 5),
                        (2, '1 Month', 1, 'months', '1', 1),
                        (3, '3 Months', 3, 'months', '1', 2),
                        (4, '6 Months', 6, 'months', '1', 4),
                        (5, '1 Year', 1, 'years', '1', 3),
                        (7, '1 hour', 1, 'hours', '1', 0);
                    ";
            $db->setQuery($sql);
            $db->execute();
        }

        $sql = "select count(*) from #__guru_commissions";
        $db->setQuery($sql);
        $result = $db->loadColumn();

        if ($result["0"] == "0") {
            $sql = "INSERT INTO `#__guru_commissions` (`id`, `default_commission`, `commission_plan`, `teacher_earnings`) VALUES
                    (1, 1, ' default', 70);
                    ";
            $db->setQuery($sql);
            $db->execute();
        }

        $sql = "SELECT count(*) FROM `#__guru_currencies`";
        $db->setQuery($sql);
        $db->execute();
        $currency = $db->loadColumn();

        if (intval($currency["0"]) <= 0) {
            $sql = "INSERT INTO `#__guru_currencies` (`id`, `plugname`, `currency_name`, `currency_full`) VALUES 
                        (1, 'paypal', 'USD', 'U.S. Dollar'),
                        (2, 'paypal', 'AUD', 'Australian Dollar'),
                        (3, 'paypal', 'CAD', 'Canadian Dollar'),
                        (4, 'paypal', 'CHF', 'Swiss Franc'),
                        (5, 'paypal', 'CZK', 'Czech Koruna'),
                        (6, 'paypal', 'DKK', 'Danish Krone'),
                        (7, 'paypal', 'EUR', 'Euro'),
                        (8, 'paypal', 'GBP', 'Pound Sterling'),
                        (9, 'paypal', 'HKD', 'Hong Kong Dollar'),
                        (10, 'paypal', 'HUF', 'Hungarian Forint'),
                        (11, 'paypal', 'JPY', 'Japanese Yen'),
                        (12, 'paypal', 'NOK', 'Norwegian Krone'),
                        (13, 'paypal', 'NZD', 'New Zealand Dollar'),
                        (14, 'paypal', 'PLN', 'Polish Zloty'),
                        (15, 'paypal', 'SEK', 'Swedish Krona'),
                        (16, 'paypal', 'SGD', 'Singapore Dollar'),
                        (17, 'paypal', 'BRL', 'Brazilian Real'),
                        (18, 'paypal', 'INR', 'Indian rupee'),
                        (20, 'paypal', 'IDR', 'Indonesian Rupiah'),
                        (21, 'paypal', 'MYR', 'Malaysian Ringgit'),
                        (22, 'paypal', 'XOF', 'African CFA Franc'),
                        (23, 'paypal', 'BGN', 'Bulgarian lev'),
                        (24, 'paypal', 'VND', 'Vietnamese Dong'),
                        (25, 'paypal', 'CNY', 'Chinese Yuan'),
                        (26, 'paypal', 'IR', 'Iranian Rial'),
                        (19, 'paypal', 'ZAR', 'South African Rand');";
            $db->setQuery($sql);
            $db->execute();
        }

        $sql = "select count(*) from `#__guru_subremind`";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();

        if ($result["0"] == "0") {
            $sql = "INSERT INTO #__guru_subremind (`id`, `name`, `term`, `subject`, `body`, `published`, `ordering`) VALUES
                    (1, 'Your subscription has expired', 0, 'Your subscription to [COURSE_NAME] has expired ', '<p>Dear [STUDENT_FIRST_NAME], <br />\r\n<br />\r\n Your [SUBSCRIPTION_TERM] subscription to [COURSE_NAME] has expired! <br />\r\n<br />\r\n Please click on the link below to renew it: <br />\r\n<br />\r\n [RENEW_URL]       <br />\r\n<br />\r\n Remember, you can always access your courses here: <br />\r\n<br />\r\n [MY_COURSES]<br />\r\n<br />\r\n Thank you! </p>', '1', 3),
                    (2, 'Your subscription will expire in 1 day', 1, 'Your subscription to [COURSE_NAME] will expire in 1 day', '<p>Dear [STUDENT_FIRST_NAME], <br />\r\n<br />\r\n Your [SUBSCRIPTION_TERM] subscription to [COURSE_NAME] will expire tomorrow!<br />\r\n<br />\r\n Please click on the link below to renew it: <br />\r\n<br />\r\n [RENEW_URL]<br />\r\n<br />\r\n Remember, you can always access your courses here: <br />\r\n<br />\r\n [MY_COURSES]<br />\r\n<br />\r\n Thank you! </p>', '1', 2),
                    (4, '3 days after expiration', 8, 'Subscription to [COURSE_NAME] has expired!', '<p>Dear [STUDENT_FIRST_NAME], <br />\r\n<br />\r\n Your [SUBSCRIPTION_TERM] subscription to [COURSE_NAME] has expired 3 days ago! <br />\r\n<br />\r\n Please click on the link below to renew it: <br />\r\n<br />\r\n [RENEW_URL]<br />\r\n<br />\r\n Remember, you can always access your courses here: <br />\r\n<br />\r\n [MY_COURSES]<br />\r\n<br />\r\n Thank you! </p>', '1', 1),
                    (5, 'On purchase email', 11, 'Thank you for purchasing [COURSE_NAME] course', '<p>Dear [STUDENT_FIRST_NAME],<br />\r\n<br />\r\n Thank you for purchasing [SUBSCRIPTION_TERM] subscription to [COURSE_NAME] <br />\r\n<br />\r\n Your subscription will expire on:\r\n[EXPIRE_DATE]<br />\r\n<br />\r\n You can access your course here:<br />\r\n<br />\r\n [COURSE_URL] <br />\r\n<br />\r\n Best Regards,<br />\r\n<br />\r\n [SITENAME] </p>', '1', 0),
                    (6, 'New Lesson', 12, 'New lesson: [LESSON_TITLE]', '<p>Dear [STUDENT_FIRST_NAME],<br />\r\n<br />\r\n A new lesson is available for [COURSE_NAME]: <br />\r\n<br />\r\n [LESSON_TITLE]<br />\r\n<br />\r\n Click below to access this lesson:<br />\r\n<br />\r\n [LESSON_URL] <br />\r\n<br />\r\n Best Regards,<br />\r\n<br />\r\n [SITENAME] </p>', '1', 4);";
            $db->setQuery($sql);
            $db->execute();
        }

        if ($result == "4") {
            $sql = "INSERT INTO #__guru_subremind (`id`, `name`, `term`, `subject`, `body`, `published`, `ordering`) VALUES
                    (6, 'New Lesson', 12, 'New lesson: [LESSON_TITLE]', '<p>Dear [STUDENT_FIRST_NAME],<br />\r\n<br />\r\n A new lesson is available for [COURSE_NAME]: <br />\r\n<br />\r\n [LESSON_TITLE]<br />\r\n<br />\r\n Click below to access this lesson:<br />\r\n<br />\r\n [LESSON_URL] <br />\r\n<br />\r\n Best Regards,<br />\r\n<br />\r\n [SITENAME] </p>', '1', 4);";
            $db->setQuery($sql);
            $db->execute();
        }

        $sql = "SELECT `psgpage` FROM `#__guru_config` WHERE id =1";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        $update_no = json_decode($result["0"]);

        $psgpage_array = array("course_image_size" => "" . $update_no->course_image_size . "", "course_image_size_type" => "" . $update_no->course_image_size_type . "", "course_image_alignment" => "" . $update_no->course_image_alignment . "", "course_wrap_image" => "" . $update_no->course_wrap_image . "", "course_author_name_show" => "" . $update_no->course_author_name_show . "", "course_released_date" => "" . $update_no->course_released_date . "", "course_level" => "" . $update_no->course_level . "", "course_price" => "" . $update_no->course_price . "", "course_price_type" => "" . $update_no->course_price_type . "", "course_table_contents" => "" . $update_no->course_table_contents . "", "course_description_show" => "" . $update_no->course_description_show . "", "course_tab_price" => "" . $update_no->course_tab_price . "", "course_author" => "" . $update_no->course_author . "", "course_requirements" => "" . $update_no->course_requirements . "", "course_buy_button" => "" . $update_no->course_buy_button . "", "course_buy_button_location" => "" . $update_no->course_buy_button_location . "", "show_course_image" => "" . $update_no->show_course_image . "", "show_all_cloase_all" => "" . $update_no->show_all_cloase_all . "", "show_course_studentamount" => "" . $update_no->show_course_studentamount . "");


        $sql = "SELECT `st_authorpage` FROM `#__guru_config` WHERE id =1";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        $update_noa = json_decode($result["0"]);


        $st_authorpage_array2 = array("author_name" => "" . $update_noa->author_name . "", "author_image" => "" . $update_noa->author_image . "", "author_description" => "" . $update_noa->author_description . "", "author_st_read_more" => "" . $update_noa->author_st_read_more . "", "teacher_aprove" => "" . @$update_noa->teacher_aprove . "", "teacher_group" => "" . @$update_noa->teacher_group . "", "teacher_add_media" => "" . @$update_noa->teacher_add_media . "", "teacher_edit_media" => "" . @$update_noa->teacher_edit_media . "", "teacher_add_courses" => "" . @$update_noa->teacher_add_courses . "", "teacher_edit_courses" => "" . @$update_noa->teacher_edit_courses . "", "teacher_add_quizzesfe" => "" . @$update_noa->teacher_add_quizzesfe . "", "teacher_edit_quizzesfe" => "" . @$update_noa->teacher_edit_quizzesfe . "", "teacher_add_students" => "" . @$update_noa->teacher_add_students . "", "teacher_edit_students" => "" . @$update_noa->teacher_edit_students . "", "teacher_approve_courses" => "" . @$update_noa->teacher_approve_courses . "");
        $st_authorpage2 = json_encode($st_authorpage_array2);


        $sql = "SELECT `ctgspage` FROM `#__guru_config` WHERE id =1";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        $update_noctgs = json_decode($result["0"]);

        $ctgspage_array2 = array("ctgslayout" => "" . $update_noctgs->ctgslayout . "", "ctgscols" => "" . $update_noctgs->ctgscols . "", "ctgs_image_size" => "" . $update_noctgs->ctgs_image_size . "", "ctgs_image_size_type" => "" . $update_noctgs->ctgs_image_size_type . "", "ctgs_image_alignment" => "" . $update_noctgs->ctgs_image_alignment . "", "ctgs_wrap_image" => "" . $update_noctgs->ctgs_wrap_image . "", "ctgs_description_length" => "" . $update_noctgs->ctgs_description_length . "", "ctgs_description_type" => "" . $update_noctgs->ctgs_description_type . "", "ctgs_description_alignment"  => "" . $update_noctgs->ctgs_description_alignment . "", "ctgs_read_more" => "" . $update_noctgs->ctgs_read_more . "", "ctgs_read_more_align" => "" . $update_noctgs->ctgs_read_more_align . "", "ctgs_show_empty_catgs" => "" . $update_noctgs->ctgs_show_empty_catgs . "", "ctgs_description_mode" => "" . $update_noctgs->ctgs_description_mode . "");

        $sql = "SELECT `ctgpage` FROM `#__guru_config` WHERE id =1";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        $update_noctg = json_decode($result["0"]);

        $ctgpage_array2 = array("ctg_image_size" => "" . $update_noctg->ctg_image_size . "", "ctg_image_size_type" => "" . $update_noctg->ctg_image_size_type . "", "ctg_image_alignment" => "" . $update_noctg->ctg_image_alignment . "", "ctg_description_length" => "" . $update_noctg->ctg_description_length . "", "ctg_description_type" => "" . $update_noctg->ctg_description_type . "", "ctg_description_alignment" => "" . $update_noctg->ctg_description_alignment . "", "ctg_description_mode" => "" . $update_noctg->ctg_description_mode . "");

        $sql = "SELECT `psgspage` FROM `#__guru_config` WHERE id =1";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        $update_nopsg = json_decode($result["0"]);

        $psgspage_array2 = array("courseslayout" => "" . $update_nopsg->courseslayout . "", "coursescols" => "" . $update_nopsg->coursescols . "", "courses_image_size" => "" . $update_nopsg->courses_image_size . "", "courses_image_size_type" => "" . $update_nopsg->courses_image_size_type . "", "courses_image_alignment" => "" . $update_nopsg->courses_image_alignment . "", "courses_wrap_image" => "" . $update_nopsg->courses_wrap_image . "", "courses_description_length" => "" . $update_nopsg->courses_description_length . "", "courses_description_type" => "" . $update_nopsg->courses_description_type . "", "courses_description_alignment" => "" . $update_nopsg->courses_description_alignment . "", "courses_read_more" => "" . $update_nopsg->courses_read_more . "", "courses_read_more_align" => "" . $update_nopsg->courses_read_more_align . "", "courses_description_mode" => "" . $update_nopsg->courses_description_mode . "");

        $sql = "SELECT `authorspage` FROM `#__guru_config` WHERE id =1";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        $update_nocapsg = json_decode($result["0"]);

        $authorspage_array2 = array("authorslayout" => "" . $update_nocapsg->authorslayout . "", "authorscols" => "" . $update_nocapsg->authorscols . "", "authors_image_size" => "" . $update_nocapsg->authors_image_size . "", "authors_image_size_type" => "" . $update_nocapsg->authors_image_size_type . "", "authors_image_alignment" => "" . $update_nocapsg->authors_image_alignment . "", "authors_wrap_image" => "" . $update_nocapsg->authors_wrap_image . "", "authors_description_length" => "" . $update_nocapsg->authors_description_length . "", "authors_description_type" => "" . $update_nocapsg->authors_description_type . "", "authors_description_alignment" => "" . $update_nocapsg->authors_description_alignment . "", "authors_read_more" => "" . $update_nocapsg->authors_read_more . "", "authors_read_more_align" => "" . $update_nocapsg->authors_read_more_align . "", "authors_description_mode" => "" . $update_nocapsg->authors_description_mode . "");

        if (!isset($update_noctgs->ctgs_description_mode)) {
            $insert_config_array = json_encode($ctgspage_array2);
            $sql = "UPDATE `#__guru_config` set `ctgspage` ='" . $insert_config_array . "' WHERE id=1";
            $db->setQuery($sql);
            $db->execute();
        }
        if (!isset($update_noctg->ctg_description_mode)) {
            $insert_config_array = json_encode($ctgpage_array2);
            $sql = "UPDATE `#__guru_config` set `ctgpage` ='" . $insert_config_array . "' WHERE id=1";
            $db->setQuery($sql);
            $db->execute();
        }
        if (!isset($update_nopsg->courses_description_mode)) {

            $insert_config_array = json_encode($psgspage_array2);
            $sql = "UPDATE `#__guru_config` set `psgspage` ='" . $insert_config_array . "' WHERE id=1";
            $db->setQuery($sql);
            $db->execute();
        }
        if (!isset($update_nocapsg->authors_description_mode)) {
            $insert_config_array = json_encode($authorspage_array2);
            $sql = "UPDATE `#__guru_config` set `authorspage` ='" . $insert_config_array . "' WHERE id=1";
            $db->setQuery($sql);
            $db->execute();
        }
        if (!isset($update_no->show_course_studentamount)) {
            $insert_config_array = json_encode($psgpage_array);
            $sql = "UPDATE `#__guru_config` set `psgpage` ='" . $insert_config_array . "' WHERE id=1";
            $db->setQuery($sql);
            $db->execute();
        }
        if (!isset($update_noa->teacher_aprove)) {
            $insert_config_array = json_encode($st_authorpage_array2);
            $sql = "UPDATE `#__guru_config` set `st_authorpage` ='" . $insert_config_array . "' WHERE id=1";
            $db->setQuery($sql);
            $db->execute();
        }

        $sql = "SELECT `template_emails` FROM `#__guru_config` WHERE id =1";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        $update_template = json_decode($result["0"]);

        if (!isset($update_template->ask_teacher_subject) || $update_template->ask_teacher_subject == '') {
            $update_template->ask_teacher_subject = "You have a new teacher application";
        }

        if (!isset($update_template->ask_teacher_body) || $update_template->ask_teacher_body == '') {
            $update_template->ask_teacher_body = "<p>Dear admin,</p>\r\n<p>You have a new teacher application:</p>\r\n<p>Name: [AUTHOR_NAME]</p>\r\n<p>Thank you!</p>";
        }

        if (!isset($update_template->new_teacher_subject) || $update_template->new_teacher_subject == '') {
            $update_template->new_teacher_subject = "New teacher has registered";
        }

        if (!isset($update_template->new_teacher_body) || $update_template->new_teacher_body == '') {
            $update_template->new_teacher_body = "<p>Dear admin,</p>\r\n<p>New teacher has registered:</p>\r\n<p>Name: [AUTHOR_NAME]</p>\r\n<p>Thank you!</p>";
        }
        if (!isset($update_template->approve_order_subject) || $update_template->approve_order_subject == '') {
            $update_template->approve_order_subject = "Approved Order";
        }

        if (!isset($update_template->approve_order_body) || $update_template->approve_order_body == '') {
            $update_template->approve_order_body = "<p>Hello [STUDENT_FIRST_NAME],</p>\r\n<p>Your [COURSE_NAME] order has been approved and you can access the course now.</p>\r\n<p>Login to the [SITE_NAME] to view it.</p>\r\n<p>Kindest regards,</p>\r\n<p>[SITE_NAME] administrators</p>";
        }
        if (!isset($update_template->pending_order_subject) || $update_template->pending_order_subject == '') {
            $update_template->pending_order_subject = "Pending Order";
        }

        if (!isset($update_template->pending_order_body) || $update_template->pending_order_body == '') {
            $update_template->pending_order_body = "<p>Hello,</p>\r\n<p>There is a pending order: <br /> Course name: [COURSE_NAME]<br /> Order number: [ORDER_NUMBER]<br /> Student name: [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME] </p>\r\n<p>To approve the order, please login to the list of orders: [GURU_ORDER_LIST_URL] and approve it.</p>";
        }

        if (!isset($update_template->new_student_subject) || $update_template->new_student_subject == '') {
            $update_template->new_student_subject = "New student has registered";
        }

        if (!isset($update_template->new_student_body) || $update_template->new_student_body == '') {
            $update_template->new_student_body = "<p>Dear admin,</p>\r\n<p>New student has registered:</p>\r\n<p>Name: [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME]</p>\r\n<p>Thank you!</p>";
        }

        if (!isset($update_template->new_student_enrolled_subject) || $update_template->new_student_enrolled_subject == '') {
            $update_template->new_student_enrolled_subject = "New Student Enrolled";
        }

        if (!isset($update_template->new_student_enrolled_body) || $update_template->new_student_enrolled_body == '') {
            $update_template->new_student_enrolled_body = "<p>Dear admin,</p>\r\n<p>A new student has enrolled to this course - [COURSE_NAME].</p>\r\n<p>Student Name: [STUDENT_FIRST_NAME] [STUDENT_LAST_NAME]</p>\r\n<p>Thank you!</p>";
        }

        if (!isset($update_template->teacher_completed_course_subject) || $update_template->teacher_completed_course_subject == '') {
            $update_template->teacher_completed_course_subject = "[STUDENT_NAME] completed [COURSE_NAME] successfully";
        }

        if (!isset($update_template->teacher_completed_course_body) || $update_template->teacher_completed_course_body == '') {
            $update_template->teacher_completed_course_body = "<p>Dear [TEACHER_FULL_NAME],</p>\r\n<p>[STUDENT_NAME] completed [COURSE_NAME] successfully.</p>\r\n<p>Thank you,</p>\r\n<p>[SITE_NAME]</p>";
        }

        if (!isset($update_template->admin_completed_course_subject) || $update_template->admin_completed_course_subject == '') {
            $update_template->admin_completed_course_subject = "[STUDENT_NAME] completed [COURSE_NAME] successfully";
        }

        if (!isset($update_template->admin_completed_course_body) || $update_template->admin_completed_course_body == '') {
            $update_template->admin_completed_course_body = "<p>Dear [ADMIN_NAME],</p>\r\n<p>[STUDENT_NAME] completed [COURSE_NAME] successfully.</p>\r\n<p>Thank you,</p>\r\n<p>[SITE_NAME]</p>";
        }

        $templates_emails2  = json_encode($update_template);
        $sql = "UPDATE `#__guru_config` set `template_emails` =" . $db->q($templates_emails2) . " WHERE id=1";
        $db->setQuery($sql);
        $db->execute();

        //-----------------------------------------------
        $sql = "select `template_emails` from #__guru_config";
        $db->setQuery($sql);
        $db->execute();
        $template_emails = $db->loadColumn();
        $template_emails = @$template_emails["0"];
        $template_emails = json_decode($template_emails, true);

        if (!isset($template_emails["chek_quiz_subject"])) {
            $template_emails["chek_quiz_subject"] = "Teacher has graded and checked the quiz results.";
            $template_emails["chek_quiz_body"] = "<p>Hello [STUDENT_FIRST_NAME],</p>\r\n<p>Your Quiz: [QUIZ_NAME] has been checked and marked by the teacher. You can go ahead and check the results here: [LINK_TO_QUIZ_RESULT]</p>\r\n<p>Regards,<br />[SITE_NAME] Administrators</p>";

            $template_emails["feedback_subject"] = "Your score for [QUIZ_NAME]";
            $template_emails["feedback_body"] = "<p>Dear [STUDENT_FIRST_NAME],</p>\r\n<p></p><p>[loop]</p>\r\n<p>The score to [ESSAY_QUESTION_TITLE] is [SCORE].</p>\r\n<p>Feedback from [TEACHER_FULL_NAME]: \"[FEEDBACK_CONTENT]\".</p>\r\n<p>[end of loop]</p>\r\n<p></p><p>This brings your score for the quiz [QUIZ_NAME] to [TOTAL_QUIZ_SCORE].</p>";

            $template_emails["review_quiz_subject"] = "Student finishes a quiz and there are questions that need to be reviewed.";
            $template_emails["review_quiz_body"] = "<p>Hello [AUTHOR_NAME],</p>\r\n<p>One of your students has taken taken the quiz: [QUIZ_NAME] and some of the answers need to be reviewed by you.</p>\r\n<p>You can see the results here: [LINK_TO_QUIZ_RESULT]</p>\r\n<p>Regards,<br />[SITE_NAME] administrators</p>";

            $template_emails  = json_encode($template_emails);

            $sql = "update #__guru_config set `template_emails`=" . $db->q($template_emails);
            $db->setQuery($sql);
            $db->execute();
        }
        //-----------------------------------------------

        $sql = "UPDATE `#__guru_media` set `uploaded_tab` ='1' WHERE `source`='local'";
        $db->setQuery($sql);
        $db->execute();

        $sql = "select id from #__users where username='ijoomla' and email='demo@ijoomla.com'";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();

        if (!isset($result["0"]) || $result["0"] == NULL) {
            $sql = "select `id` from `#__users` where `username`='ijoomla'";
            $db->setQuery($sql);
            $db->execute();
            $result = $db->loadColumn();

            if (isset($result) && isset($result["0"])) {
                $user_id = intval($result["0"]);
            } else {
                $sql = "select count(*) from #__users where `username`='ijoomla'";
                $db->setQuery($sql);
                $db->execute();
                $count_username = $db->loadColumn();
                $count_username = @$count_username["0"];

                $sql = "select count(*) from #__users where `email`='demo@ijoomla.com'";
                $db->setQuery($sql);
                $db->execute();
                $count_email = $db->loadColumn();
                $count_email = @$count_email["0"];

                if (intval($count_username) == 0 && intval($count_email) == 0) {
                    $data = array("id" => 0, "name" => "iJoomla", "username" => "ijoomla", "email" => "demo@ijoomla.com", "password" => "ijoomla");
                    $user = new JUser();

                    if (!$user->bind($data)) {
                        die($user->getError());
                    }

                    $user->gid = 18;

                    if (!$user->save()) {
                        die($user->getError());
                    }

                    $user_id = $user->id;
                    $sql = "select `id` from #__usergroups where `title`='Registered'";
                    $db->setQuery($sql);
                    $db->execute();
                    $user_group_id = $db->loadColumn();
                    $user_group_id = $user_group_id["0"];

                    if (intval($user_group_id) == 0) {
                        $params = JComponentHelper::getParams('com_users');
                        $user_group_id = $params->get('new_usertype');
                    }

                    $sql = "INSERT INTO `#__user_usergroup_map` (`user_id`, `group_id`) VALUES (" . intval($user_id) . ", " . intval($user_group_id) . ")";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }
        } else {
            $user_id = intval($result["0"]);
        }

        $sql = "select count(*) from #__guru_authors where userid=" . intval($user_id);
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();

        if ($result["0"] == "0") {
            if (intval($user_id) != 0) {
                $sql = "INSERT INTO `#__guru_authors` (`userid`, `gid`, `full_bio`, `images`, `emaillink`, `website`, `blog`, `facebook`, `twitter`, `show_email`, `show_website`, `show_blog`, `show_facebook`, `show_twitter`, `author_title`, `ordering`, `forum_kunena_generated`,`enabled`, `commission_id`) VALUES
        (" . intval($user_id) . ", 2, '<p><a href=\"http://www.ijoomla.com\" title=\"\" target=\"_blank\">iJoomla.com</a> is the winner of &quot;The Best Joomla App Development Firm&quot; award at the 2010 <strong>CMS Expo</strong>. </p>\r\n<p>At iJoomla, we combine open source with professional standards. Our\r\ndevelopers are all experienced, full-time professionals dedicated to\r\nproducing Joomla components that take Joomla sites to the next level.\r\nWhile other developers are limited to weekend coding and occasional\r\ndebugging, our staff are constantly working to improve your site. </p>\r\n<p>The quality of our work is seen in its usability, its design and its\r\nfunctionality.\r\nAll our commercial components come with first rate technical support. If\r\n you''ve got a question about one of our components, a member of our team\r\n will be back with an answer in no time at all. Ultimately, our goal is\r\nto create a new standard for the <a href=\"http://www.ijoomla.com\" title=\"\" target=\"_blank\">Joomla</a> community: top quality\r\ncomponents with professional service for a very low price. </p>', '/images/stories/guru/authors/thumbs/ijoomla2.gif', 0, 'http://www.ijoomla.com', 'http://www.ijoomla.com/blog', 'http://www.facebook.com/ijoomla', 'ijoomla', 1, 1, 1, 1, 1, '', 0, 0, 1, 1)";
                $db->setQuery($sql);
                $db->execute();
                if (!JFolder::exists(JPATH_SITE . "/images/stories/guru/authors/thumbs")) {
                    JFolder::create(JPATH_SITE . "/images/stories/guru/authors/thumbs");
                }

                $component_dir = JPATH_SITE . '/administrator/components/com_guru';
                copy($component_dir . "/images/for_install/authors/ijoomla2.gif", JPATH_SITE . "/images/stories/guru/authors/ijoomla2.gif");
                copy($component_dir . "/images/for_install/authors/thumbs/ijoomla2.gif", JPATH_SITE . "/images/stories/guru/authors/thumbs/ijoomla2.gif");
            }
        } else {
            $sql = "SELECT id FROM `#__users` WHERE block=1";
            $db->setQuery($sql);
            $db->execute();
            $result_blocked = $db->loadColumn();
            $result_blocked = implode(",", $result_blocked);
            if ($result_blocked != "") {
                $sql = "UPDATE `#__guru_authors` set enabled = 0 WHERE userid IN(" . $result_blocked . ")";
                $db->setQuery($sql);
                $db->execute();
            }
        }
        $sql = "select count(*) from #__guru_media_categories";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        if ($result["0"] == "0") {
            $sql = "INSERT INTO `#__guru_media_categories` (`id`, `name`, `parent_id`, `child_id`, `description`, `metatitle`, `metakey`, `metadesc`, `published`) VALUES
    (3, 'Guru media', 0, 0, 'media for the Guru course', '', '', '', 1)";
            $db->setQuery($sql);
            $db->execute();
        }

        $sql = "select count(*) from #__guru_media";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();

        $sql = "select id from #__users where username='ijoomla' and email='demo@ijoomla.com'";
        $db->setQuery($sql);
        $db->execute();
        $result_id = $db->loadColumn();

        if ($result["0"] == "0") {
            $sql = "INSERT INTO `#__guru_media` (`id`, `name`, `instructions`, `type`, `source`, `uploaded`, `code`, `url`, `local`, `width`, `height`, `published`, `option_video_size`, `category_id`, `auto_play`, `show_instruction`,`hide_name`,`author`,`image`,`description`,`uploaded_tab`) VALUES
    (1, 'Overview of Guru', '', 'video', 'url', '0', '', 'http://vimeo.com/27181459', '', 0, 0, 1, 0, 3, 1, 0,1,'" . $result_id["0"] . "','','','-1'),
    (2, 'Adding a promo code', '', 'video', 'url', '0', '', 'http://vimeo.com/27181476', '', 0, 0, 1, 0, 3, 1, 0,1,'" . $result_id["0"] . "','','','-1'),
    (3, 'Edit the language file', '', 'video', 'url', '0', '', 'http://vimeo.com/27181372', '', 0, 0, 1, 0, 3, 1, 0,1,'" . $result_id["0"] . "','','','-1'),
    (4, 'Adding table of content', '', 'video', 'url', '0', '', 'http://vimeo.com/27181365', '', 0, 0, 1, 0, 3, 1, 0,1,'" . $result_id["0"] . "','','','-1'),
    (5, 'Adding media', '', 'video', 'url', '0', '', 'http://vimeo.com/27181343', '', 0, 0, 1, 0, 3, 1, 0,1,'" . $result_id["0"] . "','','','-1'),
    (6, 'Adding a course on the backend', '', 'video', 'url', '0', '', 'http://vimeo.com/27181315', '', 0, 0, 1, 0, 3, 1, 0,1,'" . $result_id["0"] . "','','','-1'),
    (7, 'Adding an order on the backend', '', 'video', 'url', '0', '', 'http://vimeo.com/27181273', '', 0, 0, 1, 0, 3, 1, 0,1,'" . $result_id["0"] . "','','','-1'),
    (8, 'Adding a student', '', 'video', 'url', '0', '', 'http://vimeo.com/27181347', '', 0, 0, 1, 0, 3, 1, 0,1,'" . $result_id["0"] . "','','','-1')";
            $db->setQuery($sql);
            $db->execute();
        }

        $sql = "select count(*) from #__guru_category";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        if ($result["0"] == "0") {
            $sql = "INSERT INTO `#__guru_category` (`id`, `name`, `alias`, `published`, `description`, `image`, `ordering`) VALUES
    (1, 'iJoomla', 'ijoomla', 1, '<p>iJoomla.com is the winner of &quot;The Best Joomla App Development Firm&quot; award at the 2010 <strong>CMS Expo</strong>. </p>', 'images/stories/guru/categories/thumbs/ijoomla.png', 1)";
            $db->setQuery($sql);
            if ($db->execute()) {
                $sql = "INSERT INTO `#__guru_categoryrel` (`parent_id`, `child_id`) VALUES (0, 1)";
                $db->setQuery($sql);
                $db->execute();
            }
            if (!JFolder::exists(JPATH_SITE . "/images/stories/guru/categories/thumbs")) {
                JFolder::create(JPATH_SITE . "/images/stories/guru/categories/thumbs");
            }
            $component_dir = JPATH_SITE . '/administrator/components/com_guru';
            copy($component_dir . "/images/for_install/categories/ijoomla.png", JPATH_SITE . "/images/stories/guru/categories/ijoomla.png");
            copy($component_dir . "/images/for_install/categories/thumbs/ijoomla.png", JPATH_SITE . "/images/stories/guru/categories/thumbs/ijoomla.png");
        }

        $sql = "select count(*) from #__guru_program";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();

        if ($result["0"] == "0") {
            $jnow = new JDate('now');
            $date_today = $jnow->toSQL();
            $sql = "INSERT INTO `#__guru_program` (`id`, `catid`, `name`, `alias`, `description`, `introtext`, `image`, `emails`, `published`, `startpublish`, `endpublish`, `metatitle`, `metakwd`, `metadesc`, `ordering`, `pre_req`, `pre_req_books`, `reqmts`, `author`, `level`, `priceformat`, `skip_module`) VALUES
    (1, 1, 'How to use Guru', 'how-to-use-guru', '<p>Guru is a <a href=\"http://guru.ijoomla.com/\" title=\"\" target=\"_blank\">joomla LMS</a>, designed to help you create online training courses. On this course you will learn how to use it. </p>', '', 'images/stories/guru/courses/thumbs/guru.png', NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', '', '', '', 0, 'Basic Joomla knowledge', '', '', " . intval($user_id) . ", 0, '1', 0);";
            $db->setQuery($sql);

            if ($db->execute()) {
                $sql = "INSERT INTO `#__guru_program_plans` (`product_id`, `plan_id`, `price`, `default`) VALUES
                            (1, 3, 0.3, 0),
                            (1, 2, 0.2, 0),
                            (1, 7, 0.1, 1)";
                $db->setQuery($sql);
                $db->execute();

                $sql = "INSERT INTO `#__guru_days` (`id`, `pid`, `title`, `alias`, `description`, `image`, `published`, `startpublish`, `endpublish`, `metatitle`, `metakwd`, `metadesc`, `afterfinish`, `url`, `pagetitle`, `pagecontent`, `ordering`, `locked`, `media_id`, `access`) VALUES
    (1, 1, 'Getting started', 'getting-started', '', NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', NULL, NULL, NULL, 0, NULL, NULL, '', 1, 0, 0, 2),
    (2, 1, 'Creating your course', 'creating-your-course', NULL, NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', NULL, NULL, NULL, 0, NULL, NULL, NULL, 2, 0, 0, 2),
    (3, 1, 'Adding stuff', 'adding-stuff', NULL, NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', NULL, NULL, NULL, 0, NULL, NULL, NULL, 3, 0, 0, 0),
    (4, 1, 'Settings', 'settings', '', NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', NULL, NULL, NULL, 0, NULL, NULL, '', 4, 0, 0, 0)";
                $db->setQuery($sql);
                $db->execute();

                $sql = "INSERT INTO `#__guru_task` (`id`, `name`, `alias`, `category`, `difficultylevel`, `points`, `image`, `published`, `startpublish`, `endpublish`, `metatitle`, `metakwd`, `metadesc`, `time`, `ordering`, `step_access`, `forum_kunena_generatedt`, `duration`) VALUES
    (1, 'Overview of Guru', 'overview-of-guru', NULL, 'easy', NULL, NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', '', '', '', 0, 1, 2, 0, '9x43'),
    (2, 'Adding a course on the backend', 'adding-a-course-on-the-backend', NULL, 'easy', NULL, NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', '', '', '', 0, 3, 2, 0, '6x3'),
    (3, 'Adding table of content', 'adding-table-of-content', NULL, 'easy', NULL, NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', '', '', '', 0, 6, 0, 0, '2x59'),
    (4, 'Adding media', 'adding-media', NULL, 'easy', NULL, NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', '', '', '', 0, 10, 0, 0, '3x23'),
    (5, 'Adding a student', 'adding-a-student', NULL, 'easy', NULL, NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', '', '', '', 0, 7, 0, 0, 'x59'),
    (6, 'Adding a promo code', 'adding-a-promo-code', NULL, 'easy', NULL, NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', '', '', '', 0, 8, 0, 0, '2x53'),
    (7, 'Adding an order on the backend', 'adding-an-order-on-the-backend', NULL, 'easy', NULL, NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', '', '', '', 0, 9, 0, 0, '3x'),
    (8, 'Editing the language file', 'editing-the-language-file', NULL, 'easy', NULL, NULL, 1, '" . $date_today . "', '0000-00-00 00:00:00', '', '', '', 0, 11, 0, 0, '')";
                $db->setQuery($sql);
                $db->execute();

                $sql = "INSERT INTO `#__guru_mediarel` (`type`, `type_id`, `media_id`, `mainmedia`, `text_no`, `layout`, `access`, `order`) VALUES
                            ('scr_l', 1, 6, 0, 0, 0, 0, 0),
                            ('dtask', 1, 1, 0, 0, 0, 0, 0),
                            ('dtask', 0, 1, 0, 0, 0, 0, 0),
                            ('scr_l', 2, 6, 0, 0, 0, 0, 0),
                            ('dtask', 2, 4, 0, 0, 0, 0, 0),
                            ('scr_l', 3, 6, 0, 0, 0, 0, 0),
                            ('dtask', 2, 3, 0, 0, 0, 0, 0),
                            ('scr_l', 4, 6, 0, 0, 0, 0, 0),
                            ('dtask', 2, 2, 0, 0, 0, 0, 0),
                            ('scr_l', 5, 6, 0, 0, 0, 0, 0),
                            ('dtask', 3, 7, 0, 0, 0, 0, 0),
                            ('scr_l', 6, 6, 0, 0, 0, 0, 0),
                            ('dtask', 3, 6, 0, 0, 0, 0, 0),
                            ('scr_l', 7, 6, 0, 0, 0, 0, 0),
                            ('dtask', 3, 5, 0, 0, 0, 0, 0),
                            ('scr_m', 3, 4, 1, 0, 1, 0, 0),
                            ('scr_l', 8, 6, 0, 0, 0, 0, 0),
                            ('dtask', 4, 8, 0, 0, 0, 0, 0),
                            ('scr_m', 1, 1, 1, 0, 1, 0, 0),
                            ('scr_m', 1, 1, 1, 0, 2, 0, 0),
                            ('scr_m', 1, 1, 1, 0, 3, 0, 0),
                            ('scr_m', 1, 1, 1, 0, 4, 0, 0),
                            ('scr_m', 1, 1, 1, 0, 6, 0, 0),
                            ('scr_m', 1, 1, 1, 0, 7, 0, 0),
                            ('scr_m', 1, 1, 1, 0, 8, 0, 0),
                            ('scr_m', 1, 1, 1, 0, 9, 0, 0),
                            ('scr_m', 1, 1, 1, 0, 10, 0, 0),
                            ('scr_m', 1, 1, 1, 0, 11, 0, 0),
                            ('dtask', 0, 1, 0, 0, 0, 0, 0),
                            ('scr_m', 2, 6, 1, 0, 1, 0, 0),
                            ('scr_m', 2, 6, 1, 0, 2, 0, 0),
                            ('scr_m', 2, 6, 1, 0, 3, 0, 0),
                            ('scr_m', 2, 6, 1, 0, 4, 0, 0),
                            ('scr_m', 2, 6, 1, 0, 6, 0, 0),
                            ('scr_m', 2, 6, 1, 0, 7, 0, 0),
                            ('scr_m', 2, 6, 1, 0, 8, 0, 0),
                            ('scr_m', 2, 6, 1, 0, 9, 0, 0),
                            ('scr_m', 2, 6, 1, 0, 10, 0, 0),
                            ('scr_m', 2, 6, 1, 0, 11, 0, 0),
                            ('dtask', 0, 2, 0, 0, 0, 0, 0),
                            ('scr_m', 3, 4, 1, 0, 2, 0, 0),
                            ('scr_m', 3, 4, 1, 0, 3, 0, 0),
                            ('scr_m', 3, 4, 1, 0, 4, 0, 0),
                            ('scr_m', 3, 4, 1, 0, 6, 0, 0),
                            ('scr_m', 3, 4, 1, 0, 7, 0, 0),
                            ('scr_m', 3, 4, 1, 0, 8, 0, 0),
                            ('scr_m', 3, 4, 1, 0, 9, 0, 0),
                            ('scr_m', 3, 4, 1, 0, 10, 0, 0),
                            ('scr_m', 3, 4, 1, 0, 11, 0, 0),
                            ('dtask', 0, 3, 0, 0, 0, 0, 0),
                            ('scr_m', 4, 5, 1, 0, 1, 0, 0),
                            ('scr_m', 4, 5, 1, 0, 2, 0, 0),
                            ('scr_m', 4, 5, 1, 0, 3, 0, 0),
                            ('scr_m', 4, 5, 1, 0, 4, 0, 0),
                            ('scr_m', 4, 5, 1, 0, 6, 0, 0),
                            ('scr_m', 4, 5, 1, 0, 7, 0, 0),
                            ('scr_m', 4, 5, 1, 0, 8, 0, 0),
                            ('scr_m', 4, 5, 1, 0, 9, 0, 0),
                            ('scr_m', 4, 5, 1, 0, 10, 0, 0),
                            ('scr_m', 4, 5, 1, 0, 11, 0, 0),
                            ('dtask', 0, 4, 0, 0, 0, 0, 0),
                            ('scr_m', 5, 8, 1, 0, 1, 0, 0),
                            ('scr_m', 5, 8, 1, 0, 2, 0, 0),
                            ('scr_m', 5, 8, 1, 0, 3, 0, 0),
                            ('scr_m', 5, 8, 1, 0, 4, 0, 0),

                            ('scr_m', 5, 8, 1, 0, 6, 0, 0),
                            ('scr_m', 5, 8, 1, 0, 7, 0, 0),
                            ('scr_m', 5, 8, 1, 0, 8, 0, 0),
                            ('scr_m', 5, 8, 1, 0, 9, 0, 0),
                            ('scr_m', 5, 8, 1, 0, 10, 0, 0),
                            ('scr_m', 5, 8, 1, 0, 11, 0, 0),
                            ('dtask', 0, 5, 0, 0, 0, 0, 0),
                            ('scr_m', 6, 2, 1, 0, 1, 0, 0),
                            ('scr_m', 6, 2, 1, 0, 2, 0, 0),
                            ('scr_m', 6, 2, 1, 0, 3, 0, 0),
                            ('scr_m', 6, 2, 1, 0, 4, 0, 0),
                            ('scr_m', 6, 2, 1, 0, 6, 0, 0),
                            ('scr_m', 6, 2, 1, 0, 7, 0, 0),
                            ('scr_m', 6, 2, 1, 0, 8, 0, 0),
                            ('scr_m', 6, 2, 1, 0, 9, 0, 0),
                            ('scr_m', 6, 2, 1, 0, 10, 0, 0),
                            ('scr_m', 6, 2, 1, 0, 11, 0, 0),
                            ('dtask', 0, 6, 0, 0, 0, 0, 0),
                            ('scr_m', 7, 7, 1, 0, 1, 0, 0),
                            ('scr_m', 7, 7, 1, 0, 2, 0, 0),
                            ('scr_m', 7, 7, 1, 0, 3, 0, 0),
                            ('scr_m', 7, 7, 1, 0, 4, 0, 0),
                            ('scr_m', 7, 7, 1, 0, 6, 0, 0),
                            ('scr_m', 7, 7, 1, 0, 7, 0, 0),
                            ('scr_m', 7, 7, 1, 0, 8, 0, 0),
                            ('scr_m', 7, 7, 1, 0, 9, 0, 0),
                            ('scr_m', 7, 7, 1, 0, 10, 0, 0),
                            ('scr_m', 7, 7, 1, 0, 11, 0, 0),
                            ('dtask', 0, 7, 0, 0, 0, 0, 0)";
                $db->setQuery($sql);
                $db->execute();
            }

            if (!JFolder::exists(JPATH_SITE . "/images/stories/guru/courses/thumbs")) {
                JFolder::create(JPATH_SITE . "/images/stories/guru/courses/thumbs");
            }
            $component_dir = JPATH_SITE . '/administrator/components/com_guru';
            copy($component_dir . "/images/for_install/courses/guru.png", JPATH_SITE . "/images/stories/guru/courses/guru.png");
            copy($component_dir . "/images/for_install/courses/thumbs/guru.png", JPATH_SITE . "/images/stories/guru/courses/thumbs/guru.png");
        }

        echo '<script language="javascript" type="text/javascript">
                setTimeout(function(){
                                window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=folders&tmpl=component";
                           }, 1000);
            </script>';
    }

    function startCreateFolders()
    {
        if (!JFolder::exists(JPATH_SITE . "/media/audio")) {
            JFolder::create(JPATH_SITE . "/media/audio");
        }

        if (!JFolder::exists(JPATH_SITE . "/media/documents")) {
            JFolder::create(JPATH_SITE . "/media/documents");
        }

        if (!JFolder::exists(JPATH_SITE . "/media/files")) {
            JFolder::create(JPATH_SITE . "/media/files");
        }

        if (!JFolder::exists(JPATH_SITE . "/media/videos")) {
            JFolder::create(JPATH_SITE . "/media/videos");
        }

        if (!JFolder::exists(JPATH_SITE . "/images/stories/guru/certificates/thumbs")) {
            JFolder::create(JPATH_SITE . "/images/stories/guru/certificates/thumbs");
        }

        if (!JFolder::exists(JPATH_SITE . "/images/stories/guru/authors/thumbs")) {
            JFolder::create(JPATH_SITE . "/images/stories/guru/authors/thumbs");
        }

        if (!JFolder::exists(JPATH_SITE . "/images/stories/guru/categories/thumbs")) {
            JFolder::create(JPATH_SITE . "/images/stories/guru/categories/thumbs");
        }

        if (!JFolder::exists(JPATH_SITE . "/images/stories/guru/courses/thumbs")) {
            JFolder::create(JPATH_SITE . "/images/stories/guru/courses/thumbs");
        }

        if (!JFolder::exists(JPATH_SITE . "/images/stories/guru/customers/thumbs")) {
            JFolder::create(JPATH_SITE . "/images/stories/guru/customers/thumbs");
        }

        if (!JFolder::exists(JPATH_SITE . "/images/stories/guru/media/thumbs")) {
            JFolder::create(JPATH_SITE . "/images/stories/guru/media/thumbs");
        }

        $component_dir = JPATH_SITE . '/administrator/components/com_guru';

        //---create guru_user_custom.css only if not already created --- this will be included in administrator/components/com_guru/controller.php
        $dir_name = JPATH_SITE . "/media/files/guru/";
        $file_name = "guru_user_custom.css";
        $file_content = "/*Here yoy can place all you custom css declarations that will overwrite already existing styles or add new ones*/";
        if (!file_exists($dir_name . $file_name)) {
            if (!file_exists($dir_name)) {
                mkdir($dir_name);
            }
            file_put_contents($dir_name . $file_name, $file_content);
        }
        //--- end guru_user_custom

        copy($component_dir . "/images/for_install/authors/ijoomla2.gif", JPATH_SITE . "/images/stories/guru/authors/ijoomla2.gif");
        copy($component_dir . "/images/for_install/authors/thumbs/ijoomla2.gif", JPATH_SITE . "/images/stories/guru/authors/thumbs/ijoomla2.gif");

        copy($component_dir . "/images/for_install/categories/ijoomla.png", JPATH_SITE . "/images/stories/guru/categories/ijoomla.png");
        copy($component_dir . "/images/for_install/categories/thumbs/ijoomla.png", JPATH_SITE . "/images/stories/guru/categories/thumbs/ijoomla.png");

        copy($component_dir . "/images/for_install/courses/guru.png", JPATH_SITE . "/images/stories/guru/courses/guru.png");
        copy($component_dir . "/images/for_install/courses/thumbs/guru.png", JPATH_SITE . "/images/stories/guru/courses/thumbs/guru.png");

        copy($component_dir . "/images/for_install/certificates/viewed.png", JPATH_SITE . "/images/stories/guru/certificates/viewed.png");

        copy($component_dir . "/images/for_install/certificates/link.png", JPATH_SITE . "/images/stories/guru/certificates/link.png");

        copy($component_dir . "/images/for_install/certificates/email.png", JPATH_SITE . "/images/stories/guru/certificates/email.png");

        copy($component_dir . "/images/for_install/certificates/download.png", JPATH_SITE . "/images/stories/guru/certificates/download.png");

        copy($component_dir . "/images/for_install/certificates/Cert-blue-color-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-blue-color-back-no-seal.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-blue-color-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-blue-color-back-no-seal.png");

        copy($component_dir . "/images/for_install/certificates/Cert-Blue-seal-color-back.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-Blue-seal-color-back.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-Blue-seal-color-back.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-Blue-seal-color-back.png");

        copy($component_dir . "/images/for_install/certificates/Cert-blue-white-back.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-blue-white-back.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-blue-white-back.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-blue-white-back.png");

        copy($component_dir . "/images/for_install/certificates/Cert-blue-white-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-blue-white-back-no-seal.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-blue-white-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-blue-white-back-no-seal.png");

        copy($component_dir . "/images/for_install/certificates/Cert-green-seal-color-back.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-green-seal-color-back.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-green-seal-color-back.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-green-seal-color-back.png");

        copy($component_dir . "/images/for_install/certificates/Cert-green-seal-color-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-green-seal-color-back-no-seal.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-green-seal-color-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-green-seal-color-back-no-seal.png");

        copy($component_dir . "/images/for_install/certificates/Cert-green-seal-white-back.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-green-seal-white-back.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-green-seal-white-back.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-green-seal-white-back.png");

        copy($component_dir . "/images/for_install/certificates/Cert-green-white--back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-green-white--back-no-seal.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-green-white--back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-green-white--back-no-seal.png");

        copy($component_dir . "/images/for_install/certificates/Cert-orange--color-back.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-orange--color-back.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-orange--color-back.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-orange--color-back.png");

        copy($component_dir . "/images/for_install/certificates/Cert-orange--color-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-orange--color-back-no-seal.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-orange--color-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-orange--color-back-no-seal.png");

        copy($component_dir . "/images/for_install/certificates/Cert-orange-white-back.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-orange-white-back.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-orange-white-back.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-orange-white-back.png");

        copy($component_dir . "/images/for_install/certificates/Cert-orange-white-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/Cert-orange-white-back-no-seal.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert-orange-white-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert-orange-white-back-no-seal.png");

        copy($component_dir . "/images/for_install/certificates/Cert--Purple-color-back.png", JPATH_SITE . "/images/stories/guru/certificates/Cert--Purple-color-back.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert--Purple-color-back.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert--Purple-color-back.png");

        copy($component_dir . "/images/for_install/certificates/Cert--Purple-color-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/Cert--Purple-color-back-no-seal.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert--Purple-color-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert--Purple-color-back-no-seal.png");

        copy($component_dir . "/images/for_install/certificates/Cert--Purple-white-back.png", JPATH_SITE . "/images/stories/guru/certificates/Cert--Purple-white-back.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert--Purple-white-back.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert--Purple-white-back.png");

        copy($component_dir . "/images/for_install/certificates/Cert--Purple-white-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/Cert--Purple-white-back-no-seal.png");
        copy($component_dir . "/images/for_install/certificates/thumbs/Cert--Purple-white-back-no-seal.png", JPATH_SITE . "/images/stories/guru/certificates/thumbs/Cert--Purple-white-back-no-seal.png");

        $metadata_xml = JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "guruprofile" . DIRECTORY_SEPARATOR . "metadata.xml";
        if (JFile::exists($metadata_xml)) {
            JFile::delete($metadata_xml);
        }

        echo '<script language="javascript" type="text/javascript">
                setTimeout(function(){
                                window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=menu&tmpl=component";
                           }, 1000);
            </script>';
    }

    function startMenuItems()
    {
        $component        = JComponentHelper::getComponent('com_guru');
        $component_id    = 0;
        $db = JFactory::getDBO();

        if (is_object($component) && isset($component->id)) {
            $component_id = $component->id;
        }

        if ($component_id > 0) {
            // Update the existing menu items.
            $query     = "UPDATE #__menu SET component_id=" . intval($component_id) . " WHERE link LIKE '%option=com_guru%'";
            $db->setQuery($query);
            if (!$db->execute()) {
                return false;
            }
        }

        // start create guru categories menu
        $sql = "select count(*) from #__menu_types where `menutype`='guru-categories'";
        $db->setQuery($sql);
        $db->execute();
        $count = $db->loadColumn();
        $count = @$count["0"];

        if (intval($count) == 0) {
            $sql = "insert into #__menu_types (`menutype`, `title`, `description`) values ('guru-categories', 'Guru Categories', 'Guru Categories')";
            $db->setQuery($sql);
            $db->execute();
        }

        //$sql = "select `id`, `params` from #__menu where `menutype`='guru-categories'";
        $sql = "select `id`, `alias`, `params` from #__menu";
        $db->setQuery($sql);
        $db->execute();
        $menus = $db->loadAssocList();

        $menus_itemid = array();
        $menus_alias = array();

        if (isset($menus) && count($menus) > 0) {
            foreach ($menus as $key => $value) {
                $params = $value["params"];
                $params = json_decode($params, true);
                $menus_itemid[] = @$params["cid"];
                $menus_alias[] = $value["alias"];
            }
        }

        $sql = "select `id`, `name`, `alias` from #__guru_category";
        $db->setQuery($sql);
        $db->execute();
        $categories = $db->loadAssocList();

        if (isset($categories) && count($categories) > 0) {
            foreach ($categories as $key => $value) {
                if (!in_array($value["id"], $menus_itemid)) {
                    if (!in_array($value["alias"], $menus_alias)) {
                        $menus_alias[] = $value["alias"];

                        $sql = "insert into #__menu (`menutype`, `title`, `alias`, `path`, `link`, `type`, `published`, `component_id`, `access`, `params`, `level`, `language`, `img`) values ('guru-categories', '" . addslashes(trim($value["name"])) . "', '" . addslashes(trim($value["alias"])) . "', '" . addslashes(trim($value["alias"])) . "', 'index.php?option=com_guru&view=gurupcategs&layout=view', 'component', '1', '" . intval($component_id) . "', '1', '{\"cid\":\"" . intval($value["id"]) . "\",\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":\"\",\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', '1', '*', '')";
                        $db->setQuery($sql);
                        $db->execute();
                    }
                }
            }
        }
        // stop create guru categories menu

        echo '<script language="javascript" type="text/javascript">
                setTimeout(function(){
                                window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=plugins&tmpl=component";
                           }, 1000);
            </script>';
    }

    function startInstallPlugins()
    {
        $db = JFactory::getDBO();
        $sql = "select count(*) from #__extensions where element='ijoomlanews'";
        $db->setQuery($sql);
        $db->execute();
        $count = $db->loadColumn();
        $count = $count["0"];

        $component_dir = JPATH_SITE . '/administrator/components/com_guru/plugins';

        //----------------------------------------start jomsocial plugin
        $sql = "select count(*) from #__extensions where element='guru_user_update'";
        $db->setQuery($sql);
        $db->execute();
        $count = $db->loadColumn();
        $count = $count["0"];

        if ($count == 0) {
            $query = 'INSERT INTO #__extensions (name,type,element,folder,client_id,enabled,access,protected,manifest_cache,params,checked_out,     checked_out_time,ordering,state) VALUES ( "Guru User Update", "plugin", "guru_user_update", "user", 0, 1, 1, 0, \'{"name":"Guru User Update","type":"plugin","creationDate":"February 12, 2014","author":"iJoomla","copyright":"(C) 2014 iJoomla.com","authorEmail":"webmaster2@ijoomla.com","authorUrl":"www.ijoomla.com","version":"1.0.0","description":"","group":""}\', "{}", 0, "0000-00-00 00:00:00", 0, 0)';
            $db->setQuery($query);
            $db->execute();
        }


        $jsplug_dir = JPATH_SITE . '/plugins/user/guru_user_update';
        $component_dirn = JPATH_SITE . '/administrator/components/com_guru/plugins';
        if (!is_dir($jsplug_dir)) {
            mkdir($jsplug_dir, 0755);
        }
        $jsplug_php = 'guru_user_update.php';
        $jsplug_xml = 'guru_user_update.xml';
        $jsplug_folder = 'guru_user_update';
        @chmod($jsplug_folder, 0755);
        if (!copy($component_dirn . "/guru_user_update/" . $jsplug_xml, $jsplug_dir . "/" . $jsplug_xml)) {
            echo 'Error copying guru_user_update.xml' . "<br/>";
        }
        if (!copy($component_dirn . "/guru_user_update/" . $jsplug_php, $jsplug_dir . "/" . $jsplug_php)) {
            echo 'Error copying guru_user_update.php' . "<br/>";
        }
        //----------------------------------------stop jomsocial plugin

        //----------------------------------------start teacher events plugin 

        $sql = "select count(*) from #__extensions where element='guruteacheractions'";
        $db->setQuery($sql);
        $db->execute();
        $count = $db->loadColumn();
        $count = $count["0"];

        if ($count == 0) {
            $query = 'INSERT INTO #__extensions (name,type,element,folder,client_id,enabled,access,protected,manifest_cache,params,checked_out,     checked_out_time,ordering,state) VALUES ( "Guru Teacher Actions", "plugin", "guruteacheractions", "system", 0, 1, 1, 0, \'{"name":"Guru Teacher Actions","type":"plugin","creationDate":"July 16, 2014","author":"iJoomla","copyright":"(C) 2014 iJoomla.com","authorEmail":"webmaster2@ijoomla.com","authorUrl":"www.ijoomla.com","version":"1.0.0","description":"","group":""}\', "{}", 0, "0000-00-00 00:00:00", 0, 0)';
            $db->setQuery($query);
            $db->execute();
        }


        $jsplug_dir = JPATH_SITE . '/plugins/system/guruteacheractions';
        $component_dirn = JPATH_SITE . '/administrator/components/com_guru/plugins';
        if (!is_dir($jsplug_dir)) {
            mkdir($jsplug_dir, 0755);
        }
        $jsplug_php = 'guruteacheractions.php';
        $jsplug_xml = 'guruteacheractions.xml';
        $jsplug_folder = 'guruteacheractions';
        @chmod($jsplug_folder, 0755);
        if (!copy($component_dirn . "/guruteacheractions/" . $jsplug_xml, $jsplug_dir . "/" . $jsplug_xml)) {
            echo 'Error copying guruteacheractions.xml' . "<br/>";
        }
        if (!copy($component_dirn . "/guruteacheractions/" . $jsplug_php, $jsplug_dir . "/" . $jsplug_php)) {
            echo 'Error copying guruteacheractions.php' . "<br/>";
        }
        //----------------------------------------stop teacher events plugin 


        //----------------------------------------start jw_allvideos
        $sql = "select count(*) from #__extensions where element='jw_allvideos'";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();
        if ($result["0"] == "0") {
            $sql = "INSERT INTO #__extensions (name,type,element,folder,client_id,enabled,access,protected,manifest_cache,params,checked_out,   checked_out_time, ordering, state)"
                . "\n VALUES ('AllVideos (by JoomlaWorks)', 'plugin', 'jw_allvideos', 'content', 0, 1, 1, 0, '{}', 'vfolder=images/stories/videos\nvwidth=400\nvheight=300\ntransparency=transparent\nbackground=#010101\nbackgroundQT=black\ncontrolBarLocation=bottom\nlightboxLink=1\nlightboxWidth=800\nlightboxHeight=600\nafolder=images/stories/audio\nawidth=300\naheight=20\ndownloadLink=1\nembedForm=1\n', 0, '0000-00-00 00:00:00' ,0, 0)";
            $db->setQuery($sql);
            $db->execute();
        }

        $update_dir = JPATH_SITE . '/plugins/content';
        $source = $component_dir . "/jw_allvideos";
        $destination = $update_dir . "/jw_allvideos";
        if (!JFolder::copy($source, $destination, '', true)) {
            echo "Can't copy " . $source;
        }
        //----------------------------------------stop jw_allvideos

        $sql = "select count(*) from #__extensions where element='gurucron'";
        $db->setQuery($sql);
        $db->execute();
        $result = $db->loadColumn();

        if ($result["0"] == 0) {
            $sql = "INSERT INTO #__extensions (name,type,element,folder,client_id,enabled,access,protected,manifest_cache,params,checked_out, checked_out_time, ordering, state)"
                . "\n VALUES ('System - Guru Cron', 'plugin', 'gurucron', 'system', 0, 1, 1, 0, '{}', '', 0, '0000-00-00 00:00:00' ,0, 0)";
            $db->setQuery($sql);
            $db->execute();
        }

        //----------------------------------------start cron_GURU plugin
        $update_dir = JPATH_SITE . '/plugins/system/gurucron';
        if (!is_dir($update_dir)) {
            mkdir($update_dir);
        }

        $update_php = 'gurucron.php';
        $update_xml = 'gurucron.xml';
        @chmod($update_dir);

        if (!copy($component_dir . "/cron_GURU/" . $update_xml, $update_dir . "/" . $update_xml)) {
            echo 'Error copying gurucron.xml' . "<br/>";
        }

        if (!copy($component_dir . "/cron_GURU/" . $update_php, $update_dir . "/" . $update_php)) {
            echo 'Error copying gurucron.php' . "<br/>";
        }
        //----------------------------------------stop cron_GURU plugin

        $sql = "select element from #__extensions where element='paypaypal' and folder='gurupayment'";
        $db->setQuery($sql);
        $db->execute();
        $name = $db->loadColumn();

        if (empty($name["0"])) {
            $sql = "INSERT INTO #__extensions (name,type,element,folder,client_id,enabled,access,protected,manifest_cache,params,checked_out,   checked_out_time,ordering,state)"
                . "\n VALUES ('Payment Processor [PayPal]', 'plugin', 'paypaypal', 'gurupayment', 0, 1, 1, 0, '{}', 'paypaypal_label=PayPal Payment\npaypaypal_image=paypal1.gif\npaypaypal_lc=EN\npaypaypal_currency=USD\npaypaypal_tax=0.00\npaypaypal_ship=1\n', 0, '0000-00-00 00:00:00', 0, 0)";
            $db->setQuery($sql);
            $db->execute();
        }


        //----------------------------------------start paypaypal_GURU plugin
        $update_dir = JPATH_SITE . '/plugins/gurupayment/paypaypal';
        $source = $component_dir . "/paypaypal_GURU";
        $destination = $update_dir;
        if (!JFolder::copy($source, $destination, '', true)) {
            echo "Can't copy " . $source;
        }

        $update_dir = JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.plg_gurupayment_paypaypal.ini';
        $source = $component_dir . "/paypaypal_GURU/language/en-GB.plg_gurupayment_paypaypal.ini";
        $destination = $update_dir;

        if (!copy($source, $destination)) {
            echo "Can't copy " . $source;
        }

        $update_dir = JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.plg_gurupayment_paypaypal.sys.ini';
        $source = $component_dir . "/paypaypal_GURU/language/en-GB.plg_gurupayment_paypaypal.sys.ini";
        $destination = $update_dir;
        if (!copy($source, $destination)) {
            echo "Can't copy " . $source;
        }
        //----------------------------------------stop paypaypal_GURU plugin

        /*$modules = array();
        $sourceModules  = JPATH_ROOT . '/administrator/components/com_guru/plugins/guru_stripe_plugin';
    
        $listModules = JFolder::files($sourceModules);
        
        foreach($listModules as $row){
            $modules[] = $sourceModules."/".$row;
        }
        
        jimport('joomla.installer.installer');
        jimport('joomla.installer.helper');
    
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
    
        foreach($modules as $module){
            $package   = JInstallerHelper::unpack($module);
            $installer = JInstaller::getInstance();
    
            if(!$installer->install($package['dir'])){
                // There was an error installing the package
            }
            // Cleanup the install files
            if (!is_file($package['packagefile'])){
                $package['packagefile'] = $app->getCfg('tmp_path').'/'.$package['packagefile'];
            }
            JInstallerHelper::cleanupInstall('', $package['extractdir']);
        }*/

        /*$modules = array();
        $sourceModules  = JPATH_ROOT . '/administrator/components/com_guru/plugins/guru_payfast_plugin';
    
        $listModules = JFolder::files($sourceModules);
        
        foreach($listModules as $row){
            $modules[] = $sourceModules."/".$row;
        }
        
        jimport('joomla.installer.installer');
        jimport('joomla.installer.helper');
    
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
    
        foreach($modules as $module){
            $package   = JInstallerHelper::unpack($module);
            $installer = JInstaller::getInstance();
    
            if(!$installer->install($package['dir'])){
                // There was an error installing the package
            }
            // Cleanup the install files
            if (!is_file($package['packagefile'])){
                $package['packagefile'] = $app->getCfg('tmp_path').'/'.$package['packagefile'];
            }
            JInstallerHelper::cleanupInstall('', $package['extractdir']);
        }*/

        $modules = array();
        $sourceModules    = JPATH_ROOT . '/administrator/components/com_guru/plugins/guru_offline_plugin';

        $listModules = JFolder::files($sourceModules);

        foreach ($listModules as $row) {
            $modules[] = $sourceModules . "/" . $row;
        }

        jimport('joomla.installer.installer');
        jimport('joomla.installer.helper');

        $app = JFactory::getApplication();
        $db = JFactory::getDBO();

        foreach ($modules as $module) {
            $package   = JInstallerHelper::unpack($module);
            $installer = JInstaller::getInstance();

            if (!$installer->install($package['dir'])) {
                // There was an error installing the package
            }
            // Cleanup the install files
            if (!is_file($package['packagefile'])) {
                $package['packagefile'] = $app->getCfg('tmp_path') . '/' . $package['packagefile'];
            }
            JInstallerHelper::cleanupInstall('', $package['extractdir']);

            $sql = "update #__extensions set `enabled`=1 where `type`='plugin' and `element`='ijoomlagurudiscussbox'";
            $db->setQuery($sql);
            $db->execute();
        }

        $modules = array();
        $sourceModules    = JPATH_ROOT . '/administrator/components/com_guru/plugins/ijoomlagurudiscussbox';

        $listModules = JFolder::files($sourceModules);

        foreach ($listModules as $row) {
            $modules[] = $sourceModules . "/" . $row;
        }

        jimport('joomla.installer.installer');
        jimport('joomla.installer.helper');

        $app = JFactory::getApplication();
        $db = JFactory::getDBO();

        foreach ($modules as $module) {
            $package   = JInstallerHelper::unpack($module);
            $installer = JInstaller::getInstance();

            if (!$installer->install($package['dir'])) {
                // There was an error installing the package
            }
            // Cleanup the install files
            if (!is_file($package['packagefile'])) {
                $package['packagefile'] = $app->getCfg('tmp_path') . '/' . $package['packagefile'];
            }
            JInstallerHelper::cleanupInstall('', $package['extractdir']);
        }

        $modules = array();
        $sourceModules    = JPATH_ROOT . '/administrator/components/com_guru/plugins/mod_guru_categories';

        $listModules = JFolder::files($sourceModules);

        foreach ($listModules as $row) {
            $modules[] = $sourceModules . "/" . $row;
        }

        $sourceModules    = JPATH_ROOT . '/administrator/components/com_guru/plugins/mod_guru_search';

        $listModules = JFolder::files($sourceModules);

        foreach ($listModules as $row) {
            $modules[] = $sourceModules . "/" . $row;
        }

        jimport('joomla.installer.installer');
        jimport('joomla.installer.helper');

        $app = JFactory::getApplication();
        $db = JFactory::getDBO();

        foreach ($modules as $module) {
            $package   = JInstallerHelper::unpack($module);
            $installer = JInstaller::getInstance();

            if (!$installer->install($package['dir'])) {
                // There was an error installing the package
            }
            // Cleanup the install files
            if (!is_file($package['packagefile'])) {
                $package['packagefile'] = $app->getCfg('tmp_path') . '/' . $package['packagefile'];
            }
            JInstallerHelper::cleanupInstall('', $package['extractdir']);
        }

        echo '<script language="javascript" type="text/javascript">
                setTimeout(function(){
                                window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=questions&tmpl=component";
                           }, 1000);
            </script>';
    }

    function startInstallQuestions()
    {
        $db = JFactory::getDBO();
        $sql = "select count(*) from #__guru_questions_v3";
        $db->setQuery($sql);
        $db->execute();
        $count = $db->loadColumn();
        $count = @$count["0"];

        if (intval($count) == 0) {
            $sql = "SHOW tables";
            $db->setQuery($sql);
            $res_tables = $db->loadColumn();

            $jconfigs = new JConfig();
            $dbprefix = $jconfigs->dbprefix;
            if (!in_array($dbprefix . "guru_questions", $res_tables)) {
                echo '<script language="javascript" type="text/javascript">
                            setTimeout(function(){
                                            window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=quiz&tmpl=component";
                                       }, 1000);
                      </script>';
                return;
            }

            $sql = "select * from #__guru_questions";
            $db->setQuery($sql);
            $db->execute();
            $all_questions = $db->loadAssocList();

            $for_insert = array();
            if (isset($all_questions) && count($all_questions) > 0) {
                foreach ($all_questions as $key => $question) {
                    $qid = $question["qid"];
                    $type = "multiple";
                    $question_content = $question["text"];
                    $media_ids = "";
                    $points = "10";
                    $published = $question["published"];
                    $question_order = $question["reorder"];
                    $answers = $question["answers"];
                    $answers = explode("|||", $answers);

                    $sql = "insert into #__guru_questions_v3 (`qid`, `type`, `question_content`, `media_ids`, `points`, `published`, `question_order`) values ('" . intval($qid) . "', '" . $type . "', '" . addslashes(trim($question_content)) . "', '" . $media_ids . "', '" . $points . "', '" . $published . "', '" . $question_order . "')";
                    $db->setQuery($sql);
                    $db->execute();

                    $sql = "select max(`id`) from #__guru_questions_v3";
                    $db->setQuery($sql);
                    $db->execute();
                    $question_id = $db->loadColumn();
                    $question_id = @$question_id["0"];

                    $a1 = $question["a1"];
                    if (trim($a1) != "") {
                        $answer_content_text = trim($a1);
                        $media_ids = "";
                        $correct_answer = "0";

                        if (in_array("1a", $answers)) {
                            $correct_answer = "1";
                        }

                        $sql = "insert into #__guru_question_answers (`answer_content_text`, `media_ids`, `correct_answer`, `question_id`) values ('" . addslashes(trim($answer_content_text)) . "', '" . $media_ids . "', '" . $correct_answer . "', '" . $question_id . "')";
                        $db->setQuery($sql);
                        $db->execute();
                    }

                    $a2 = $question["a2"];
                    if (trim($a2) != "") {
                        $answer_content_text = trim($a2);
                        $media_ids = "";
                        $correct_answer = "0";

                        if (in_array("2a", $answers)) {
                            $correct_answer = "1";
                        }

                        $sql = "insert into #__guru_question_answers (`answer_content_text`, `media_ids`, `correct_answer`, `question_id`) values ('" . addslashes(trim($answer_content_text)) . "', '" . $media_ids . "', '" . $correct_answer . "', '" . $question_id . "')";
                        $db->setQuery($sql);
                        $db->execute();
                    }

                    $a3 = $question["a3"];
                    if (trim($a3) != "") {
                        $answer_content_text = trim($a3);
                        $media_ids = "";
                        $correct_answer = "0";

                        if (in_array("3a", $answers)) {
                            $correct_answer = "1";
                        }

                        $sql = "insert into #__guru_question_answers (`answer_content_text`, `media_ids`, `correct_answer`, `question_id`) values ('" . addslashes(trim($answer_content_text)) . "', '" . $media_ids . "', '" . $correct_answer . "', '" . $question_id . "')";
                        $db->setQuery($sql);
                        $db->execute();
                    }

                    $a4 = $question["a4"];
                    if (trim($a4) != "") {
                        $answer_content_text = trim($a4);
                        $media_ids = "";
                        $correct_answer = "0";

                        if (in_array("4a", $answers)) {
                            $correct_answer = "1";
                        }

                        $sql = "insert into #__guru_question_answers (`answer_content_text`, `media_ids`, `correct_answer`, `question_id`) values ('" . addslashes(trim($answer_content_text)) . "', '" . $media_ids . "', '" . $correct_answer . "', '" . $question_id . "')";
                        $db->setQuery($sql);
                        $db->execute();
                    }

                    $a5 = $question["a5"];
                    if (trim($a5) != "") {
                        $answer_content_text = trim($a5);
                        $media_ids = "";
                        $correct_answer = "0";

                        if (in_array("5a", $answers)) {
                            $correct_answer = "1";
                        }

                        $sql = "insert into #__guru_question_answers (`answer_content_text`, `media_ids`, `correct_answer`, `question_id`) values ('" . addslashes(trim($answer_content_text)) . "', '" . $media_ids . "', '" . $correct_answer . "', '" . $question_id . "')";
                        $db->setQuery($sql);
                        $db->execute();
                    }


                    $a6 = $question["a6"];
                    if (trim($a6) != "") {
                        $answer_content_text = trim($a6);
                        $media_ids = "";
                        $correct_answer = "0";

                        if (in_array("6a", $answers)) {
                            $correct_answer = "1";
                        }

                        $sql = "insert into #__guru_question_answers (`answer_content_text`, `media_ids`, `correct_answer`, `question_id`) values ('" . addslashes(trim($answer_content_text)) . "', '" . $media_ids . "', '" . $correct_answer . "', '" . $question_id . "')";
                        $db->setQuery($sql);
                        $db->execute();
                    }

                    $a7 = $question["a7"];
                    if (trim($a7) != "") {
                        $answer_content_text = trim($a7);
                        $media_ids = "";
                        $correct_answer = "0";

                        if (in_array("7a", $answers)) {
                            $correct_answer = "1";
                        }

                        $sql = "insert into #__guru_question_answers (`answer_content_text`, `media_ids`, `correct_answer`, `question_id`) values ('" . addslashes(trim($answer_content_text)) . "', '" . $media_ids . "', '" . $correct_answer . "', '" . $question_id . "')";
                        $db->setQuery($sql);
                        $db->execute();
                    }

                    $a8 = $question["a8"];
                    if (trim($a8) != "") {
                        $answer_content_text = trim($a8);
                        $media_ids = "";
                        $correct_answer = "0";

                        if (in_array("8a", $answers)) {
                            $correct_answer = "1";
                        }

                        $sql = "insert into #__guru_question_answers (`answer_content_text`, `media_ids`, `correct_answer`, `question_id`) values ('" . addslashes(trim($answer_content_text)) . "', '" . $media_ids . "', '" . $correct_answer . "', '" . $question_id . "')";
                        $db->setQuery($sql);
                        $db->execute();
                    }

                    $a9 = $question["a9"];
                    if (trim($a9) != "") {
                        $answer_content_text = trim($a9);
                        $media_ids = "";
                        $correct_answer = "0";

                        if (in_array("9a", $answers)) {
                            $correct_answer = "1";
                        }

                        $sql = "insert into #__guru_question_answers (`answer_content_text`, `media_ids`, `correct_answer`, `question_id`) values ('" . addslashes(trim($answer_content_text)) . "', '" . $media_ids . "', '" . $correct_answer . "', '" . $question_id . "')";
                        $db->setQuery($sql);
                        $db->execute();
                    }

                    $a10 = $question["a10"];
                    if (trim($a10) != "") {
                        $answer_content_text = trim($a10);
                        $media_ids = "";
                        $correct_answer = "0";

                        if (in_array("10a", $answers)) {
                            $correct_answer = "1";
                        }

                        $sql = "insert into #__guru_question_answers (`answer_content_text`, `media_ids`, `correct_answer`, `question_id`) values ('" . addslashes(trim($answer_content_text)) . "', '" . $media_ids . "', '" . $correct_answer . "', '" . $question_id . "')";
                        $db->setQuery($sql);
                        $db->execute();
                    }
                }
            }
        }

        echo '<script language="javascript" type="text/javascript">
                setTimeout(function(){
                                window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=quiz&tmpl=component";
                           }, 1000);
            </script>';
    }

    function startInstallQuiz()
    {
        $db = JFactory::getDBO();
        $limit = JFactory::getApplication()->input->get("limit", "0");
        $rows = 10;

        $sql = "SHOW tables";
        $db->setQuery($sql);
        $res_tables = $db->loadColumn();

        $jconfigs = new JConfig();
        $dbprefix = $jconfigs->dbprefix;
        if (!in_array($dbprefix . "guru_quiz_taken", $res_tables)) {
            echo '<script language="javascript" type="text/javascript">
                    setTimeout(function(){
                                window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=stop&tmpl=component";
                           }, 1000);
                  </script>';
            return;
        }

        $sql = "select count(*) from #__guru_quiz_taken_v3";
        $db->setQuery($sql);
        $db->execute();
        $count = $db->loadColumn();
        $count = @$count["0"];

        if (intval($count) != 0 && $limit == 0) {
            echo '<script language="javascript" type="text/javascript">
                    setTimeout(function(){
                                window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=stop&tmpl=component";
                           }, 1000);
                  </script>';
        } else {
            $sql = "select * from #__guru_quiz_taken limit " . $limit . ", " . $rows;
            $db->setQuery($sql);
            $db->execute();
            $guru_quiz_taken = $db->loadAssocList();

            if (isset($guru_quiz_taken) && count($guru_quiz_taken) > 0) {
                foreach ($guru_quiz_taken as $key => $quiz_taken) {
                    $sql = "select * from #__guru_quiz_question_taken where `show_result_quiz_id` = " . intval($quiz_taken["id"]);
                    $db->setQuery($sql);
                    $db->execute();
                    $guru_quiz_question_taken = $db->loadAssocList();

                    $user_id = $quiz_taken["user_id"];
                    $question_ids = array();
                    $all_questions = array();
                    $quiz_id = $quiz_taken["quiz_id"];
                    $pid = $quiz_taken["pid"];
                    $date_taken_quiz = $quiz_taken["date_taken_quiz"];

                    if (isset($guru_quiz_question_taken) && count($guru_quiz_question_taken) > 0) {
                        foreach ($guru_quiz_question_taken as $key2 => $quiz_question_taken) {
                            $question_ids[] = $quiz_question_taken["question_id"];
                            $all_questions[] = $quiz_question_taken;
                        }
                    }

                    if (count($question_ids) == 0) {
                        $question_ids = "";
                    } else {
                        $question_ids = implode(",", $question_ids);
                    }

                    $score_quiz = $quiz_taken["score_quiz"];
                    $score_quiz = explode("|", $score_quiz);
                    $done = $score_quiz["0"];
                    $from = $score_quiz["1"];
                    $score_quiz = ($done * 100) / $from;
                    $score_quiz = number_format($score_quiz, 2);

                    $count_right_answer = $done;
                    $points = $count_right_answer * 10;

                    $sql = "insert into #__guru_quiz_question_taken_v3 (`user_id`, `question_ids`, `quiz_id`, `score_quiz`, `pid`, `date_taken_quiz`, `count_right_answer`, `points`) values ('" . $user_id . "', '" . $question_ids . "', '" . $quiz_id . "', '" . $score_quiz . "', '" . $pid . "', '" . $date_taken_quiz . "', '" . $count_right_answer . "', '" . $points . "')";
                    $db->setQuery($sql);
                    $db->execute();

                    $sql = "select max(`id`) from #__guru_quiz_question_taken_v3";
                    $db->setQuery($sql);
                    $db->execute();
                    $id_question_taken = $db->loadColumn();
                    $id_question_taken = @$id_question_taken["0"];

                    if (isset($all_questions) && count($all_questions) > 0) {
                        foreach ($all_questions as $key2 => $question) {
                            $question_id = $question["question_id"];
                            $answers_given = array();
                            $answers_gived = $question["answers_gived"];
                            $answers_gived = explode("||", $answers_gived);

                            if (isset($answers_gived) && count($answers_gived) > 0) {
                                $sql = "select `id` from #__guru_question_answers where `question_id`=" . intval($question_id) . " order by `id` asc";
                                $db->setQuery($sql);
                                $db->execute();
                                $answers = $db->loadColumn();

                                foreach ($answers_gived as $key3 => $ans_giv) {
                                    $ans_giv = intval($ans_giv);
                                    if (isset($answers[$ans_giv - 1])) {
                                        $answers_given[] = $answers[$ans_giv - 1];
                                    }
                                }

                                $answers_given = implode(",", $answers_given);
                            }

                            $sql = "insert into #__guru_quiz_taken_v3 (`user_id`, `quiz_id`, `question_id`, `answers_given`, `pid`, `id_question_taken`) values ('" . $user_id . "', '" . $quiz_id . "', '" . $question_id . "', '" . $answers_given . "', '" . $pid . "', '" . $id_question_taken . "')";
                            $db->setQuery($sql);
                            $db->execute();
                        }
                    }
                }
            }

            if (count($guru_quiz_taken) < $rows) {
                echo '<script language="javascript" type="text/javascript">
                    setTimeout(function(){
                                window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=stop&tmpl=component";
                           }, 1000);
                  </script>';
            } else {
                echo '<script language="javascript" type="text/javascript">
                    setTimeout(function(){
                                window.location.href = "' . JURI::root() . 'administrator/index.php?option=com_guru&controller=guruInstall&step=quiz&limit=' . ($limit + $rows) . '&tmpl=component";
                           }, 1000);
                  </script>';
            }
        }
    }
}
