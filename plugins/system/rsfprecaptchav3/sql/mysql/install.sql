DELETE FROM `#__rsform_component_types` WHERE `ComponentTypeId` IN (2423);

INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`, `CanBeDuplicated`) VALUES
(2423, 'recaptchav3', 0);

INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES
('recaptchav3.sitekey', ''),
('recaptchav3.secretkey', ''),
('recaptchav3.allpages', '1'),
('recaptchav3.threshold', '0.5');

DELETE FROM `#__rsform_component_type_fields` WHERE ComponentTypeId = 2423;

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Properties`, `Ordering`) VALUES
(2423, 'NAME', 'textbox', '', '', 0),
(2423, 'RECAPTCHAACTION', 'textbox', 'contactform', 'alphanumeric', 1),
(2423, 'COMPONENTTYPE', 'hidden', '2423', '', 2);