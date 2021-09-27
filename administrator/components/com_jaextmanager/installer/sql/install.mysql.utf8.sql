--
-- Table structure for table `#__jaem_log`
--

DROP TABLE IF EXISTS `#__jaem_log`;
CREATE TABLE IF NOT EXISTS `#__jaem_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_id` varchar(50) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `check_date` datetime DEFAULT NULL,
  `check_info` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ext_id` (`ext_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;