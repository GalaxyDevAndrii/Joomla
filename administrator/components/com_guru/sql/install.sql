CREATE TABLE IF NOT EXISTS `#__guru_authors` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(21) NOT NULL default '0',
  `gid` int(11) NOT NULL default '0',
  `full_bio` longtext NOT NULL,
  `images` varchar(255) NOT NULL default '',
  `emaillink` int(2) NOT NULL default '0',
  `website` varchar(255) NOT NULL default '',
  `blog` varchar(255) NOT NULL default '',
  `facebook` varchar(255) NOT NULL default '',
  `twitter` varchar(255) NOT NULL default '',
  `show_email` tinyint(1) NOT NULL default '1',
  `show_website` tinyint(1) NOT NULL default '1',
  `show_blog` tinyint(1) NOT NULL default '1',
  `show_facebook` tinyint(1) NOT NULL default '1',
  `show_twitter` tinyint(1) NOT NULL default '1',
  `author_title` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `forum_kunena_generated` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_buy_courses` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `order_id` int(11) NOT NULL default '0',
  `course_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `buy_date` datetime NOT NULL,
  `expired_date` datetime NOT NULL,
  `plan_id` varchar(255) NOT NULL,
  `email_send` int(3) NOT NULL,
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_category` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `alias` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL default '1',
  `description` text,
  `image` varchar(255) default NULL,
  `ordering` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_categoryrel` (
  `parent_id` int(11) NOT NULL default '1',
  `child_id` int(11) NOT NULL default '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_config` (
  `id` int(11) NOT NULL auto_increment,
  `currency` varchar(255) NOT NULL default 'USD',
  `datetype` varchar(255) NOT NULL default '0',
  `dificulty` varchar(255) default NULL,
  `influence` tinyint(1) NOT NULL,
  `display_tasks` tinyint(1) NOT NULL default '0',
  `groupe_tasks` tinyint(1) NOT NULL default '0',
  `display_media` tinyint(1) NOT NULL default '0',
  `show_unpubl` tinyint(1) NOT NULL default '0',
  `btnback` tinyint(1) NOT NULL default '1',
  `btnhome` tinyint(1) NOT NULL default '1',
  `btnnext` tinyint(1) NOT NULL default '1',
  `dofirst` tinyint(1) NOT NULL default '0',
  `imagesin` varchar(255) NOT NULL default 'images/stories',
  `videoin` varchar(255) NOT NULL default 'media/videos',
  `audioin` varchar(255) NOT NULL default 'media/audio',
  `docsin` varchar(255) NOT NULL default 'media/documents',
  `filesin` varchar(255) NOT NULL default 'media/files',
  `fromname` varchar(255) default NULL,
  `fromemail` varchar(255) default NULL,
  `regemail` varchar(255) default NULL,
  `orderemail` varchar(255) default NULL,
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
  `open_target` int(1) NOT NULL default '0',
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
  `show_bradcrumbs` tinyint(4) NOT NULL default '0',
  `show_powerd` tinyint(4) NOT NULL default '1',
  `qct_alignment` tinyint(4) NOT NULL DEFAULT '1',
  `qct_border_color` varchar(10) NOT NULL DEFAULT '000000',
  `qct_minsec` varchar(10) NOT NULL DEFAULT '000000',
  `qct_title_color` varchar(10) NOT NULL DEFAULT 'FFFFFF',
  `qct_bg_color` varchar(10) NOT NULL DEFAULT 'FFFFCC',
  `qct_font` text,
  `qct_width` varchar(10) NOT NULL DEFAULT '200',
  `qct_height` varchar(10) NOT NULL DEFAULT '90',
  `qct_font_nb` varchar(10) NOT NULL DEFAULT '28',
  `qct_font_words` varchar(10) NOT NULL DEFAULT '18',
  `currencypos` tinyint(4) NOT NULL default '0',
  `guru_ignore_ijseo` tinyint(4) NOT NULL default '0',
  `course_lesson_release` tinyint(4) NOT NULL default '0',
  `student_group` int(10) NOT NULL DEFAULT '2',	
  `guru_turnoffjq` tinyint(4) NOT NULL default '1',
  `thousands_separator` int(3) NOT NULL DEFAULT '1',
  `decimals_separator` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_currencies` (
  `id` int(11) NOT NULL auto_increment,
  `plugname` varchar(30) NOT NULL default '',
  `currency_name` varchar(20) NOT NULL default '',
  `currency_full` varchar(50) NOT NULL default '',
  `sign` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_customer` (
  `id` int(11) NOT NULL auto_increment,
  `company` varchar(100) NOT NULL default '',
  `firstname` varchar(50) NOT NULL default '',
  `lastname` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_days` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL,
  `title` varchar(255) default NULL,
  `alias` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) default NULL,
  `published` tinyint(1) NOT NULL default '1',
  `startpublish` datetime NOT NULL default '0000-00-00 00:00:00',
  `endpublish` datetime NOT NULL default '0000-00-00 00:00:00',
  `metatitle` varchar(255) default NULL,
  `metakwd` varchar(255) default NULL,
  `metadesc` text,
  `afterfinish` tinyint(1) NOT NULL default '1',
  `url` varchar(255) default NULL,
  `pagetitle` varchar(255) default NULL,
  `pagecontent` text,
  `ordering` int(3) NOT NULL default '0',
  `locked` tinyint(1) NOT NULL default '0',
  `media_id` int(9) NOT NULL,
  `access` int(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_emails` (
  `id` int(11) NOT NULL auto_increment,
  `description` text,
  `type` varchar(255) default NULL,
  `trigger` varchar(255) default NULL,
  `sendtime` tinyint(2) default NULL,
  `sendday` tinyint(2) default NULL,
  `reminder` varchar(255) default NULL,
  `published` tinyint(2) NOT NULL default '0',
  `subject` varchar(255) default NULL,
  `body` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_emails_pending` (
  `id` int(11) NOT NULL auto_increment,
  `sending_time` int(11) NOT NULL,
  `mail_id` int(11) NOT NULL,
  `mail_subj` varchar(255) NOT NULL,
  `mail_body` text NOT NULL,
  `user_id` int(11),
  `pid` int(11) NOT NULL,
  `type` enum('T','R') NOT NULL,
  `send` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_emails_refr_time` (
  `id` int(11) NOT NULL auto_increment,
  `last_trigger_time` int(11) NOT NULL,
  `last_reminder_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_jump` (
  `id` int(15) NOT NULL auto_increment,
  `button` int(2) NOT NULL,
  `text` varchar(255) NOT NULL,
  `jump_step` int(15) NOT NULL,
  `module_id` int(10) NOT NULL,
  `type_selected` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_media` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `instructions` text,
  `type` varchar(10) default NULL,
  `source` varchar(5) default NULL,
  `uploaded` enum('0','1') NOT NULL default '0',
  `code` mediumtext null,
  `url` varchar(255) default NULL,
  `local` varchar(255) default NULL,
  `width` int(11) NOT NULL default '32',
  `height` int(11) NOT NULL default '32',
  `published` tinyint(1) NOT NULL default '1',
  `option_video_size` int(3) NOT NULL,
  `category_id` int(10) NOT NULL,
  `auto_play` int(3) NOT NULL,
  `show_instruction` int(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_mediarel` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(5) default NULL,
  `type_id` int(11) default NULL,
  `media_id` int(11) default NULL,
  `mainmedia` tinyint(1) NOT NULL default '0',
  `text_no` int(4) NOT NULL default '0',
  `layout` tinyint(3) NOT NULL default '0',
  `access` int(3) NOT NULL,
  `order` int(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_media_categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `description` text,
  `metatitle` varchar(255) default NULL,
  `metakey` varchar(255) default NULL,
  `metadesc` varchar(255) default NULL,
  `published` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

CREATE TABLE IF NOT EXISTS `#__guru_media_templay` (
  `ip` bigint(20) NOT NULL,
  `scr_id` int(8) NOT NULL,
  `tmp_time` datetime NOT NULL,
  `db_lay` int(8) NOT NULL,
  `db_med` varchar(150) NOT NULL,
  `db_text` varchar(150) NOT NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_order` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `order_date` datetime NOT NULL,
  `courses` text NOT NULL,
  `status` varchar(10) NOT NULL,
  `amount` float NOT NULL default '0',
  `amount_paid` float NOT NULL default '0',
  `processor` varchar(100) NOT NULL,
  `number_of_licenses` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `promocodeid` varchar(255) NOT NULL,
  `published` int(11) NOT NULL,
  `form` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_plugins` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(40) NOT NULL default '',
  `classname` varchar(40) NOT NULL default '',
  `value` text NOT NULL,
  `filename` varchar(40) NOT NULL default '',
  `type` varchar(10) NOT NULL default 'payment',
  `published` int(11) NOT NULL default '0',
  `def` varchar(30) NOT NULL default '',
  `sandbox` int(11) NOT NULL default '0',
  `reqhttps` int(11) NOT NULL default '0',
  `display_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_plugin_settings` (
  `pluginid` int(11) NOT NULL default '0',
  `setting` varchar(200) NOT NULL default '',
  `description` text,
  `value` text NOT NULL,
  KEY `pluginid` (`pluginid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_program` (
  `id` int(11) NOT NULL auto_increment,
  `catid` int(11) default NULL,
  `name` varchar(255) default NULL,
  `alias` varchar(255) NOT NULL,
  `description` text,
  `introtext` text,
  `image` varchar(255) default NULL,
  `emails` varchar(255) default NULL,
  `published` tinyint(1) NOT NULL default '1',
  `startpublish` datetime NOT NULL default '0000-00-00 00:00:00',
  `endpublish` datetime NOT NULL default '0000-00-00 00:00:00',
  `metatitle` varchar(255) default NULL,
  `metakwd` varchar(255) default NULL,
  `metadesc` text,
  `ordering` int(5) NOT NULL,
  `pre_req` text,
  `pre_req_books` text,
  `reqmts` text,
  `author` int(3) NOT NULL,
  `level` int(3) NOT NULL,
  `priceformat` enum('1','2','3','4','5') NOT NULL default '1',
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
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_programstatus` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `days` text NOT NULL,
  `tasks` text NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `status` enum('0','1','2','-1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_program_plans` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `default` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_program_reminders` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `emailreminder_id` int(11) NOT NULL,
  `send` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_program_renewals` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `default` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_promos` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL default '',
  `code` varchar(100) NOT NULL default '',
  `codelimit` int(11) NOT NULL default '0',
  `codeused` int(11) NOT NULL default '0',
  `discount` float NOT NULL default '0',
  `codestart` datetime NOT NULL default '0000-00-00 00:00:00',
  `codeend` datetime NOT NULL default '0000-00-00 00:00:00',
  `forexisting` int(11) NOT NULL default '0',
  `published` int(11) NOT NULL default '0',
  `typediscount` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_questions` (
  `id` int(11) NOT NULL auto_increment,
  `qid` int(11) default NULL,
  `text` text,
  `a1` varchar(255) default NULL,
  `a2` varchar(255) default NULL,
  `a3` varchar(255) default NULL,
  `a4` varchar(255) default NULL,
  `a5` varchar(255) default NULL,
  `a6` varchar(255) default NULL,
  `a7` varchar(255) default NULL,
  `a8` varchar(255) default NULL,
  `a9` varchar(255) default NULL,
  `a10` varchar(255) default NULL,
  `answers` varchar(255) default NULL,
  `reorder` int(4) NOT NULL default '0',
  `published` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_quizzes_final` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quizzes_ids` varchar(255) NOT NULL,
  `qid` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_quiz` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_subplan` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `term` tinyint(3) NOT NULL,
  `period` varchar(255) NOT NULL,
  `published` enum('0','1') NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_subremind` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `term` tinyint(3) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `published` enum('0','1') NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_task` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `alias` text NOT NULL,
  `category` int(11) default NULL,
  `difficultylevel` varchar(255) default NULL,
  `points` int(11) default NULL,
  `image` varchar(255) default NULL,
  `published` tinyint(1) NOT NULL default '1',
  `startpublish` datetime NOT NULL default '0000-00-00 00:00:00',
  `endpublish` datetime NOT NULL default '0000-00-00 00:00:00',
  `metatitle` varchar(255) default NULL,
  `metakwd` varchar(255) default NULL,
  `metadesc` text,
  `time` int(11) NOT NULL default '0',
  `ordering` int(3) NOT NULL default '0',
  `step_access` int(3) NOT NULL,
  `final_lesson` tinyint(2) NOT NULL DEFAULT '0',
  `forum_kunena_generatedt` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_taskcategory` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `published` tinyint(1) NOT NULL default '1',
  `description` text,
  `image` varchar(255) default NULL,
  `listorder` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_taskcategoryrel` (
  `parent_id` int(11) NOT NULL default '1',
  `child_id` int(11) NOT NULL default '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__guru_viewed_lesson` (
  `user_id` int(11),
  `lesson_id` text,
  `module_id` text NOT NULL,
  `completed` tinyint(2) DEFAULT NULL,
  `date_completed` date NOT NULL,
  `date_last_visit` date NOT NULL,
  `pid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__guru_quiz_taken` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11),
  `quiz_id` int(11) NOT NULL,
  `score_quiz` text,
  `date_taken_quiz` datetime DEFAULT NULL,
  `pid` int(11) NOT NULL,
  `time_quiz_taken_per_user` int(20) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__guru_quiz_question_taken` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11),
  `show_result_quiz_id` int(11) NOT NULL,
  `answers_gived` varchar(255) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question_order_no` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_certificates` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_mycertificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `user_id` int(11),
  `emailcert` tinyint(4) NOT NULL DEFAULT '0',
  `datecertificate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100;


CREATE TABLE IF NOT EXISTS `#__guru_kunena_courseslinkage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcourse` int(11) DEFAULT NULL,
  `coursename` varchar(255) DEFAULT NULL,
  `catidkunena` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_kunena_forum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forumboardcourse` tinyint(4) NOT NULL DEFAULT '0',
  `forumboardlesson` tinyint(4) NOT NULL DEFAULT '0',
  `forumboardteacher` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_boards` tinyint(4) NOT NULL DEFAULT '0',
  `allow_stud` tinyint(4) NOT NULL DEFAULT '0',
  `allow_edit` tinyint(4) NOT NULL DEFAULT '0',
  `allow_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_kunena_lessonslinkage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idlesson` int(11) DEFAULT NULL,
  `lessonname` varchar(255) DEFAULT NULL,
  `catidkunena` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__guru_task_kunenacomment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_lesson` int(11) DEFAULT NULL,
  `thread` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_projects` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__guru_project_results` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
