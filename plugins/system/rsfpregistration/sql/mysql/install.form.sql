INSERT INTO `#__rsform_forms` (`FormName`, `FormLayout`, `GridLayout`, `FormLayoutName`, `LoadFormLayoutFramework`, `FormLayoutAutogenerate`, `FormLayoutFlow`, `DisableSubmitButton`, `RemoveCaptchaLogged`, `CSS`, `JS`, `FormTitle`, `ShowFormTitle`, `Published`, `Lang`, `ReturnUrl`, `ShowSystemMessage`, `ShowThankyou`, `ScrollToThankYou`, `ThankYouMessagePopUp`, `Thankyou`, `ShowContinue`, `UserEmailText`, `UserEmailTo`, `UserEmailCC`, `UserEmailBCC`, `UserEmailFrom`, `UserEmailReplyTo`, `UserEmailReplyToName`, `UserEmailFromName`, `UserEmailSubject`, `UserEmailMode`, `UserEmailAttach`, `UserEmailAttachFile`, `AdminEmailText`, `AdminEmailTo`, `AdminEmailCC`, `AdminEmailBCC`, `AdminEmailFrom`, `AdminEmailReplyTo`, `AdminEmailReplyToName`, `AdminEmailFromName`, `AdminEmailSubject`, `AdminEmailMode`, `DeletionEmailText`, `DeletionEmailTo`, `DeletionEmailCC`, `DeletionEmailBCC`, `DeletionEmailFrom`, `DeletionEmailReplyTo`, `DeletionEmailReplyToName`, `DeletionEmailFromName`, `DeletionEmailSubject`, `DeletionEmailMode`, `ScriptProcess`, `ScriptProcess2`, `ScriptBeforeDisplay`, `ScriptBeforeValidation`, `ScriptDisplay`, `UserEmailScript`, `AdminEmailScript`, `AdditionalEmailsScript`, `MetaTitle`, `MetaDesc`, `MetaKeywords`, `Required`, `ErrorMessage`, `MultipleSeparator`, `TextareaNewLines`, `CSSClass`, `CSSId`, `CSSName`, `CSSAction`, `CSSAdditionalAttributes`, `AjaxValidation`, `ScrollToError`, `Keepdata`, `KeepIP`, `DeleteSubmissionsAfter`, `Backendmenu`, `ConfirmSubmission`, `ConfirmSubmissionUrl`, `Access`, `LimitSubmissions`) VALUES ('rsform-pro-registration-form', '<h2>{global:formtitle}</h2>\n{error}\n<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->\n<fieldset class=\"formContainer formHorizontal\" id=\"rsform_{global:formid}_page_0\">\n	<div class=\"formRow\">\n		<div class=\"formSpan12\">\n			<div class=\"rsform-block rsform-block-name{name:errorClass}\">\n				<label class=\"formControlLabel\" for=\"name\">{name:caption}<strong class=\"formRequired\">(*)</strong></label>\n				<div class=\"formControls\">\n					<div class=\"formBody\">\n						{name:body}\n						<span class=\"formValidation\">{name:validation}</span>\n						<p class=\"formDescription\">{name:description}</p>\n					</div>\n				</div>\n			</div>\n			<div class=\"rsform-block rsform-block-username{username:errorClass}\">\n				<label class=\"formControlLabel\" for=\"username\">{username:caption}<strong class=\"formRequired\">(*)</strong></label>\n				<div class=\"formControls\">\n					<div class=\"formBody\">\n						{username:body}\n						<span class=\"formValidation\">{username:validation}</span>\n						<p class=\"formDescription\">{username:description}</p>\n					</div>\n				</div>\n			</div>\n			<div class=\"rsform-block rsform-block-email{email:errorClass}\">\n				<label class=\"formControlLabel\" for=\"email\">{email:caption}<strong class=\"formRequired\">(*)</strong></label>\n				<div class=\"formControls\">\n					<div class=\"formBody\">\n						{email:body}\n						<span class=\"formValidation\">{email:validation}</span>\n						<p class=\"formDescription\">{email:description}</p>\n					</div>\n				</div>\n			</div>\n			<div class=\"rsform-block rsform-block-verifyemail{verifyemail:errorClass}\">\n				<label class=\"formControlLabel\" for=\"verifyemail\">{verifyemail:caption}<strong class=\"formRequired\">(*)</strong></label>\n				<div class=\"formControls\">\n					<div class=\"formBody\">\n						{verifyemail:body}\n						<span class=\"formValidation\">{verifyemail:validation}</span>\n						<p class=\"formDescription\">{verifyemail:description}</p>\n					</div>\n				</div>\n			</div>\n			<div class=\"rsform-block rsform-block-password{password:errorClass}\">\n				<label class=\"formControlLabel\" for=\"password\">{password:caption}<strong class=\"formRequired\">(*)</strong></label>\n				<div class=\"formControls\">\n					<div class=\"formBody\">\n						{password:body}\n						<span class=\"formValidation\">{password:validation}</span>\n						<p class=\"formDescription\">{password:description}</p>\n					</div>\n				</div>\n			</div>\n			<div class=\"rsform-block rsform-block-verifypassword{verifypassword:errorClass}\">\n				<label class=\"formControlLabel\" for=\"verifypassword\">{verifypassword:caption}<strong class=\"formRequired\">(*)</strong></label>\n				<div class=\"formControls\">\n					<div class=\"formBody\">\n						{verifypassword:body}\n						<span class=\"formValidation\">{verifypassword:validation}</span>\n						<p class=\"formDescription\">{verifypassword:description}</p>\n					</div>\n				</div>\n			</div>\n			<div class=\"rsform-block rsform-block-register{register:errorClass}\">\n				<label class=\"formControlLabel\" for=\"register\">{register:caption}</label>\n				<div class=\"formControls\">\n					<div class=\"formBody\">\n						{register:body}\n						<span class=\"formValidation\">{register:validation}</span>\n						<p class=\"formDescription\">{register:description}</p>\n					</div>\n				</div>\n			</div>\n		</div>\n	</div>\n</fieldset>', '[[{\"columns\":[[\"37\",\"38\",\"39\",\"40\",\"41\",\"42\",\"43\"]],\"sizes\":[\"12\"]}],[]]', 'responsive', 1, 1, 0, 0, 0, '', '', 'RSForm! Pro Registration Form', 1, 1, 'en-GB', '', 1, 1, 0, 0, '<p>Thank you for your submission! We will contact you as soon as possible.</p>', 1, '', '', '', '', '{global:mailfrom}', '', '', '{global:fromname}', '', 1, 0, '', '', '', '', '', '', '', '', '', '', 1, '', '', '', '', '', '', '', '', '', 1, '', '', '', '', '', '', '', '', 0, '', '', '(*)', '<p class=\"formRed\">Please complete all required fields!</p>', '', 1, '', 'userForm', '', '', '', 0, 0, 1, 1, 0, 0, 0, '', '', 0);

SET @formId = LAST_INSERT_ID();

/* the name field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 1, 1, 1);

SET @componentIdName = LAST_INSERT_ID();

/* the username field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 1, 2, 1);

SET @componentIdUsername = LAST_INSERT_ID();

/* the email field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 1, 3, 1);

SET @componentIdEmail = LAST_INSERT_ID();

/* the verifyemail field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 1, 4, 1);

SET @componentIdVerifyEmail = LAST_INSERT_ID();

/* the password field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 14, 5, 1);

SET @componentIdPassword = LAST_INSERT_ID();

/* the verifypassword field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 14, 6, 1);

SET @componentIdVerifyPassword = LAST_INSERT_ID();

/* the register submit button */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 13, 7, 1);

SET @componentIdRegister = LAST_INSERT_ID();

INSERT INTO `#__rsform_properties` (`ComponentId`, `PropertyName`, `PropertyValue`) VALUES
(@componentIdName, 'NAME', 'name'),
(@componentIdName, 'CAPTION', 'Name'),
(@componentIdName, 'DEFAULTVALUE', ''),
(@componentIdName, 'DESCRIPTION', ''),
(@componentIdName, 'REQUIRED', 'YES'),
(@componentIdName, 'VALIDATIONRULE', 'none'),
(@componentIdName, 'VALIDATIONEXTRA', ''),
(@componentIdName, 'VALIDATIONMESSAGE', 'Please enter the name!'),
(@componentIdName, 'INPUTTYPE', 'text'),
(@componentIdName, 'SIZE', '20'),
(@componentIdName, 'MAXSIZE', ''),
(@componentIdName, 'PLACEHOLDER', ''),
(@componentIdName, 'ADDITIONALATTRIBUTES', ''),
(@componentIdName, 'EMAILATTACH', ''),
(@componentIdUsername, 'NAME', 'username'),
(@componentIdUsername, 'CAPTION', 'Username'),
(@componentIdUsername, 'DEFAULTVALUE', ''),
(@componentIdUsername, 'DESCRIPTION', ''),
(@componentIdUsername, 'REQUIRED', 'YES'),
(@componentIdUsername, 'VALIDATIONRULE', 'none'),
(@componentIdUsername, 'VALIDATIONEXTRA', ''),
(@componentIdUsername, 'VALIDATIONMESSAGE', 'Please provide an username!'),
(@componentIdUsername, 'INPUTTYPE', 'text'),
(@componentIdUsername, 'SIZE', '20'),
(@componentIdUsername, 'MAXSIZE', ''),
(@componentIdUsername, 'PLACEHOLDER', ''),
(@componentIdUsername, 'ADDITIONALATTRIBUTES', ''),
(@componentIdUsername, 'EMAILATTACH', ''),
(@componentIdEmail, 'NAME', 'email'),
(@componentIdEmail, 'CAPTION', 'E-mail'),
(@componentIdEmail, 'DEFAULTVALUE', ''),
(@componentIdEmail, 'DESCRIPTION', ''),
(@componentIdEmail, 'REQUIRED', 'YES'),
(@componentIdEmail, 'VALIDATIONRULE', 'email'),
(@componentIdEmail, 'VALIDATIONEXTRA', ''),
(@componentIdEmail, 'VALIDATIONMESSAGE', 'Please provide a valid e-mail!'),
(@componentIdEmail, 'INPUTTYPE', 'email'),
(@componentIdEmail, 'SIZE', '20'),
(@componentIdEmail, 'MAXSIZE', ''),
(@componentIdEmail, 'PLACEHOLDER', ''),
(@componentIdEmail, 'ADDITIONALATTRIBUTES', ''),
(@componentIdEmail, 'EMAILATTACH', ''),
(@componentIdVerifyEmail, 'NAME', 'verifyemail'),
(@componentIdVerifyEmail, 'CAPTION', 'Verify E-mail'),
(@componentIdVerifyEmail, 'DEFAULTVALUE', ''),
(@componentIdVerifyEmail, 'DESCRIPTION', ''),
(@componentIdVerifyEmail, 'REQUIRED', 'YES'),
(@componentIdVerifyEmail, 'VALIDATIONRULE', 'email'),
(@componentIdVerifyEmail, 'VALIDATIONEXTRA', ''),
(@componentIdVerifyEmail, 'VALIDATIONMESSAGE', 'Retype the e-mail!'),
(@componentIdVerifyEmail, 'INPUTTYPE', 'email'),
(@componentIdVerifyEmail, 'SIZE', '20'),
(@componentIdVerifyEmail, 'MAXSIZE', ''),
(@componentIdVerifyEmail, 'PLACEHOLDER', ''),
(@componentIdVerifyEmail, 'ADDITIONALATTRIBUTES', ''),
(@componentIdVerifyEmail, 'EMAILATTACH', ''),
(@componentIdPassword, 'NAME', 'password'),
(@componentIdPassword, 'CAPTION', 'Password'),
(@componentIdPassword, 'DEFAULTVALUE', ''),
(@componentIdPassword, 'DESCRIPTION', ''),
(@componentIdPassword, 'REQUIRED', 'YES'),
(@componentIdPassword, 'VALIDATIONEXTRA', ''),
(@componentIdPassword, 'VALIDATIONRULE', 'none'),
(@componentIdPassword, 'VALIDATIONMESSAGE', 'Please enter a password!'),
(@componentIdPassword, 'SIZE', ''),
(@componentIdPassword, 'MAXSIZE', ''),
(@componentIdPassword, 'PLACEHOLDER', ''),
(@componentIdPassword, 'ADDITIONALATTRIBUTES', ''),
(@componentIdPassword, 'EMAILATTACH', ''),
(@componentIdVerifyPassword, 'NAME', 'verifypassword'),
(@componentIdVerifyPassword, 'CAPTION', 'Verify Password'),
(@componentIdVerifyPassword, 'DEFAULTVALUE', ''),
(@componentIdVerifyPassword, 'DESCRIPTION', ''),
(@componentIdVerifyPassword, 'REQUIRED', 'YES'),
(@componentIdVerifyPassword, 'VALIDATIONEXTRA', 'password'),
(@componentIdVerifyPassword, 'VALIDATIONRULE', 'sameas'),
(@componentIdVerifyPassword, 'VALIDATIONMESSAGE', 'Retype the password!'),
(@componentIdVerifyPassword, 'SIZE', ''),
(@componentIdVerifyPassword, 'MAXSIZE', ''),
(@componentIdVerifyPassword, 'PLACEHOLDER', ''),
(@componentIdVerifyPassword, 'ADDITIONALATTRIBUTES', ''),
(@componentIdVerifyPassword, 'EMAILATTACH', ''),
(@componentIdRegister, 'NAME', 'register'),
(@componentIdRegister, 'LABEL', 'Register'),
(@componentIdRegister, 'CAPTION', ''),
(@componentIdRegister, 'RESET', 'NO'),
(@componentIdRegister, 'RESETLABEL', ''),
(@componentIdRegister, 'DISPLAYPROGRESSMSG', '<div>\r\n <p><em>Page <strong>{page}</strong> of {total}</em></p>\r\n <div class="rsformProgressContainer">\r\n  <div class="rsformProgressBar" style="width: {percent}%;"></div>\r\n </div>\r\n</div>'),
(@componentIdRegister, 'PREVBUTTON', 'PREV'),
(@componentIdRegister, 'DISPLAYPROGRESS', 'NO'),
(@componentIdRegister, 'BUTTONTYPE', 'TYPEINPUT'),
(@componentIdRegister, 'ADDITIONALATTRIBUTES', ''),
(@componentIdRegister, 'EMAILATTACH', '');

INSERT INTO `#__rsform_registration` (`form_id`, `itemid`, `action`, `action_field`, `vars`, `groups`, `joomla_fields`, `profile_fields`, `activation`, `cbactivation`, `defer_admin_email`, `user_activation_action`, `admin_activation_action`, `user_activation_url`, `admin_activation_url`, `user_activation_text`, `admin_activation_text`, `password_strength`,  `published`) VALUES
(@formId, 0, 1, '', 'a:6:{s:4:"name";s:4:"name";s:8:"username";s:8:"username";s:6:"email1";s:5:"email";s:6:"email2";s:11:"verifyemail";s:9:"password1";s:8:"password";s:9:"password2";s:14:"verifypassword";}', '2', '', '', 0, 1, 0, 0, 0, '', '', '', '', 1, 1);

UPDATE `#__rsform_config` SET `SettingValue` = @formId WHERE `#__rsform_config`.`SettingName` = 'registration_form';