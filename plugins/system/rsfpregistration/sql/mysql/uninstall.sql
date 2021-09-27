DROP TABLE IF EXISTS `#__rsform_registration`;

DELETE FROM #__rsform_config WHERE SettingName = 'registration_form';
DELETE FROM #__rsform_config WHERE SettingName = 'redirect_url';