<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

$params             = $this->item->params;
$presentation_style = $params->get('presentation_style');

$displayGroups      = $params->get('show_user_custom_fields');
$userFieldGroups    = array();
?>
  
<?php 
// Alkis - We don't want custom fields in the Contact Us page, therefore everything gets commented out
/*
<?php if (!$displayGroups || !$this->contactUser) : ?>
	<?php return; ?>
<?php endif; ?>

<?php foreach ($this->contactUser->jcfields as $field) : ?>
	<?php if (!in_array('-1', $displayGroups) && (!$field->group_id || !in_array($field->group_id, $displayGroups))) : ?>
		<?php continue; ?>
	<?php endif; ?>
	<?php if (!key_exists($field->group_title, $userFieldGroups)) : ?>
		<?php $userFieldGroups[$field->group_title] = array(); ?>
	<?php endif; ?>
	<?php $userFieldGroups[$field->group_title][] = $field; ?>
<?php endforeach; ?>

<?php foreach ($userFieldGroups as $groupTitle => $fields) : ?>
	<?php $id = JApplicationHelper::stringURLSafe($groupTitle); ?>
	
	<!-- Slider -->
	<?php if ($presentation_style == 'sliders') : ?>
		<div class="card">
			<div class="card-header">
			<h4 class="card-title">
				<a class="btn" data-toggle="collapse" data-parent="#slide-contact" href="#<?php echo 'display-' . $id; ?>">
				<?php echo JText::_('COM_CONTACT_USER_FIELDS');?>
				</a>
			</h4>
			</div>
			<div id="<?php echo 'display-' . $id; ?>" class="card-collapse collapse">
				<div class="card-body">
					<div class="contact-fields" id="user-custom-fields-<?php echo $id; ?>">
						<dl class="dl-horizontal">
						<?php foreach ($fields as $field) : ?>
							<?php if (!$field->value) : ?>
								<?php continue; ?>
							<?php endif; ?>

							<?php if ($field->params->get('showlabel')) : ?>
								<?php echo '<dt>' . JText::_($field->label) . '</dt>'; ?>
							<?php endif; ?>

							<?php echo '<dd>' . $field->value . '</dd>'; ?>
						<?php endforeach; ?>
						</dl>
					</div>

				</div>
			</div>
		</div>
	<?php endif; ?>
	<!-- // Slider -->

	<!-- Tabs -->
	<?php if ($presentation_style == 'tabs') : ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'user-custom-fields', $groupTitle ?: JText::_('COM_CONTACT_USER_FIELDS')); ?>
			<div class="contact-fields" id="user-custom-fields-<?php echo $id; ?>">
				<dl class="dl-horizontal">
				<?php foreach ($fields as $field) : ?>
					<?php if (!$field->value) : ?>
						<?php continue; ?>
					<?php endif; ?>

					<?php if ($field->params->get('showlabel')) : ?>
						<?php echo '<dt>' . JText::_($field->label) . '</dt>'; ?>
					<?php endif; ?>

					<?php echo '<dd>' . $field->value . '</dd>'; ?>
				<?php endforeach; ?>
				</dl>
			</div>

		<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>
	<!-- // Tabs -->

	<!-- Plain -->
	<?php if ($presentation_style == 'plain') : ?>
		<?php echo '<h3>' . ($groupTitle ?: JText::_('COM_CONTACT_USER_FIELDS')) . '</h3>'; ?>
		<div class="contact-fields" id="user-custom-fields-<?php echo $id; ?>">
			<dl class="dl-horizontal">
			<?php foreach ($fields as $field) : ?>
				<?php if (!$field->value) : ?>
					<?php continue; ?>
				<?php endif; ?>

			<?php if ($field->params->get('showlabel')) : ?>
				<?php echo '<dt>' . JText::_($field->label) . '</dt>'; ?>
			<?php endif; ?>
				<?php echo '<dd>' . $field->value . '</dd>'; ?>
			<?php endforeach; ?>
			</dl>
		</div>
	<?php endif; ?>
	<!-- // Plain -->

<?php endforeach; ?>
*/