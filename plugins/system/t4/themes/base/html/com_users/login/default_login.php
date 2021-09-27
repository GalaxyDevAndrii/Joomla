<?php
/**
T4 Overide
 */


defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

$usersConfig = ComponentHelper::getParams('com_users');

?>
<div class="login-wrap">
	<div class="frm-wrap login">
		<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
		<?php endif; ?>

		<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
		<div class="login-description">
		<?php endif; ?>

			<?php if ($this->params->get('logindescription_show') == 1) : ?>
				<?php echo $this->params->get('login_description'); ?>
			<?php endif; ?>

			<?php if ($this->params->get('login_image') != '') : ?>
				<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="com-users-login__image login-image" alt="<?php echo Text::_('COM_USERS_LOGIN_IMAGE_ALT'); ?>">
			<?php endif; ?>

		<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
		</div>
		<?php endif; ?>

		<form action="<?php echo Route::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="frm-login-form form-validate">

			<fieldset>
				<?php echo $this->form->renderFieldset('credentials', ['class' => 'login__input']); ?>
				<?php if ($this->tfa) : ?>
					<?php echo $this->form->renderField('secretkey','','', ['class' => 'login__secretkey']); ?>
				<?php endif; ?>

				<?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
					<div  class="login-remember">
						<label for="remember">
							<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes">	
							<?php echo Text::_('COM_USERS_LOGIN_REMEMBER_ME'); ?>
						</label>
					</div>
				<?php endif; ?>

				<div class="login-submit control-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary">
							<?php echo Text::_('JLOGIN'); ?>
						</button>
					</div>
				</div>

				<?php $return = $this->form->getValue('return', '', $this->params->get('login_redirect_url', $this->params->get('login_redirect_menuitem'))); ?>
				<input type="hidden" name="return" value="<?php echo base64_encode($return); ?>">
				<?php echo HTMLHelper::_('form.token'); ?>
			</fieldset>
		</form>
	</div>

	<div class="other-links">
		<ul>
			<li><a href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
				<?php echo Text::_('COM_USERS_LOGIN_RESET'); ?>
			</a></li>
			<li><a href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
				<?php echo Text::_('COM_USERS_LOGIN_REMIND'); ?>
			</a></li>
			<?php if ($usersConfig->get('allowUserRegistration')) : ?>
				<li><a href="<?php echo Route::_('index.php?option=com_users&view=registration'); ?>">
					<?php echo Text::_('COM_USERS_LOGIN_REGISTER'); ?>
				</a></li>
			<?php endif; ?>
			</ul>
	</div>
</div>