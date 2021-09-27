<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<fieldset id="com-users-profile__core users-profile-core">
	<legend>
		<?php echo Text::_('COM_USERS_PROFILE_CORE_LEGEND'); ?>
	</legend>
	<dl class="dl-horizontal row">
		<dt class="col-sm-3">
			<?php echo Text::_('COM_USERS_PROFILE_NAME_LABEL'); ?>
		</dt>
		<dd class="col-sm-9">
			<?php echo $this->escape($this->data->name); ?>
		</dd>
		<dt class="col-sm-3">
			<?php echo Text::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>
		</dt>
		<dd class="col-sm-9">
			<?php echo htmlspecialchars($this->data->username, ENT_COMPAT, 'UTF-8'); ?>
		</dd>
		<dt class="col-sm-3">
			<?php echo Text::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
		</dt>
		<dd class="col-sm-9">
			<?php echo HTMLHelper::_('date', $this->data->registerDate, Text::_('DATE_FORMAT_LC1')); ?>
		</dd>
		<dt class="col-sm-3">
			<?php echo Text::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>
		</dt>
		<?php if ($this->data->lastvisitDate != $this->db->getNullDate()) : ?>
			<dd class="col-sm-9">
				<?php echo HTMLHelper::_('date', $this->data->lastvisitDate, Text::_('DATE_FORMAT_LC1')); ?>
			</dd>
		<?php else : ?>
			<dd class="col-sm-9">
				<?php echo Text::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
			</dd>
		<?php endif; ?>
	</dl>
</fieldset>
