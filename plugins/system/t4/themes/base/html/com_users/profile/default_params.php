<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<?php $fields = $this->form->getFieldset('params'); ?>
<?php if (count($fields)) : ?>
	<fieldset id="users-profile-custom" class="com-users-profile__params">
		<legend><?php echo Text::_('COM_USERS_SETTINGS_FIELDSET_LABEL'); ?></legend>
		<dl class="dl-horizontal row">
			<?php foreach ($fields as $field) : ?>
				<?php if (!$field->hidden) : ?>
					<dt class="col-sm-3">
						<?php echo $field->title; ?>
					</dt>
					<dd class="col-sm-9">
						<?php if (HTMLHelper::isRegistered('users.' . $field->id)) : ?>
							<?php echo HTMLHelper::_('users.' . $field->id, $field->value); ?>
						<?php elseif (HTMLHelper::isRegistered('users.' . $field->fieldname)) : ?>
							<?php echo HTMLHelper::_('users.' . $field->fieldname, $field->value); ?>
						<?php elseif (HTMLHelper::isRegistered('users.' . $field->type)) : ?>
							<?php echo HTMLHelper::_('users.' . $field->type, $field->value); ?>
						<?php else : ?>
							<?php echo HTMLHelper::_('users.value', $field->value); ?>
						<?php endif; ?>
					</dd>
				<?php endif; ?>
			<?php endforeach; ?>
		</dl>
	</fieldset>
<?php endif; ?>

