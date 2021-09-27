INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES('registration_form', '0');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES('redirect_url', '');

CREATE TABLE IF NOT EXISTS `#__rsform_registration` (
  `form_id` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `action` tinyint(1) NOT NULL DEFAULT '1',
  `action_field` varchar(255) NOT NULL,
  `vars` text NOT NULL,
  `joomla_fields` text,
  `profile_fields` text,
  `groups` varchar(255) NOT NULL,
  `activation` tinyint(1) NOT NULL,
  `cbactivation` tinyint(1) NOT NULL,
  `defer_admin_email` tinyint(1) NOT NULL,
  `user_activation_action` tinyint(1) NOT NULL,
  `admin_activation_action` tinyint(1) NOT NULL,
  `user_activation_url` text NOT NULL,
  `admin_activation_url` text NOT NULL,
  `user_activation_text` mediumtext NOT NULL,
  `admin_activation_text` mediumtext NOT NULL,
  `password_strength` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`form_id`)
) DEFAULT CHARSET=utf8;