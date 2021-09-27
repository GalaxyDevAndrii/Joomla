DELETE FROM `#__rsform_component_types` WHERE ComponentTypeId = 2423;
DELETE FROM `#__rsform_component_type_fields` WHERE ComponentTypeId = 2423;

DELETE FROM `#__rsform_config` WHERE `SettingName` LIKE 'recaptchav3.%';