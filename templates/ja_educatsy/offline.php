<?php
/**
 *------------------------------------------------------------------------------
 * @package       JA Educatsy - Joomla LMS template for education and online learning
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt
 *------------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');

require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';

$twofactormethods = UsersHelper::getTwoFactorMethods();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />

	<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet"> 

	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template ?>/css/offline.css" type="text/css" />
</head>

<body>
<jdoc:include type="message" />
	<div id="frame" class="outline">
		<?php if ($app->get('offline_image') && file_exists($app->get('offline_image'))) : ?>
			<img src="<?php echo $app->get('offline_image'); ?>" alt="<?php echo htmlspecialchars($app->get('sitename'), ENT_COMPAT, 'UTF-8'); ?>" />
		<?php endif; ?>
		<h1>
			<?php echo htmlspecialchars($app->get('sitename'), ENT_COMPAT, 'UTF-8'); ?>
		</h1>
	<?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
		<p class="message">
			<?php echo $app->get('offline_message'); ?>
		</p>
	<?php elseif ($app->get('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != '') : ?>
		<p class="message">
			<?php echo JText::_('JOFFLINE_MESSAGE'); ?>
		</p>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
	<fieldset class="input">
		<div id="form-login-username" class="form-group">
			<label for="username"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label>
			<input name="username" id="username" type="text" class="inputbox" alt="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" autocomplete="off" autocapitalize="none" />
		</div>
		<div id="form-login-password" class="form-group">
			<label for="passwd"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
			<input type="password" name="password" class="inputbox" alt="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" id="passwd" />
		</div>
		<?php if (count($twofactormethods) > 1) : ?>
			<div id="form-login-secretkey" class="form-group">
				<label for="secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY'); ?></label>
				<input type="text" name="secretkey" class="inputbox" alt="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" id="secretkey" />
			</div>
		<?php endif; ?>
		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<div id="form-login-remember" class="form-group">
			<input type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" id="remember" />
			<label for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
		</div>
		<?php  endif; ?>
		<div id="submit-buton">
			<input type="submit" name="Submit" class="button btn btn-primary btn-block login" value="<?php echo JText::_('JLOGIN'); ?>" />
		</div>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo base64_encode(JUri::base()); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
	</form>
	</div>
</body>
</html>
