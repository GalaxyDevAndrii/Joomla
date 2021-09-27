CREATE TABLE IF NOT EXISTS `#__rsform_component_types` (
  `ComponentTypeId` int(11) NOT NULL auto_increment,
  `ComponentTypeName` text NOT NULL,
  `CanBeDuplicated` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`ComponentTypeId`)
) DEFAULT CHARSET=utf8;