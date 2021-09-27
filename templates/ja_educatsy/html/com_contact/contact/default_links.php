<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

if ($this->params->get('presentation_style')=='sliders'):?>
<div class="card card-default">
	<div class="card-header">
		<h4 class="card-title">
			<a class="btn collapsed" data-toggle="collapse" data-parent="#slide-contact" href="#display-links">
			<?php echo JText::_('COM_CONTACT_LINKS');?>
			</a>
		</h4>
	</div>
	<div id="display-links" class="card-collapse collapse">
		<div class="card-body">
<?php endif; ?>
<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'display-links', JText::_('COM_CONTACT_LINKS')); ?>
<?php endif; ?>

<?php if ($this->params->get('presentation_style') === 'plain') : ?>
	<div class="title-link">
		<?php echo '<h3><span>' . JText::_('COM_CONTACT_LINKS') . '</span></h3>'; ?>
	</div>
<?php endif; ?>

			<div class="contact-links">
				<ul class="nav nav-stacked">
					<?php
					foreach (range('a', 'e') as $char) :// letters 'a' to 'e'
						$link = $this->contact->params->get('link'.$char);
						$label = $this->contact->params->get('link'.$char.'_name');

						if (!$link) :
							continue;
						endif;

						// Add 'http://' if not present
						$link = (0 === strpos($link, 'http')) ? $link : 'http://'.$link;

						// If no label is present, take the link
						$label = ($label) ? $label : $link;
						?>
						<li class="<?php echo JApplication::stringURLSafe(strtolower($label)) ?>">
							<a href="<?php echo $link; ?>">
								<?php if($label): ?>
									<span class="fab fa-<?php echo JApplication::stringURLSafe(strtolower($label)) ?>"></span>
									<?php echo $label ?>
								<?php else: ?>
									<?php echo $link ?>
								<?php endif; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

<?php if ($this->params->get('presentation_style')=='sliders'):?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
</div>
<?php endif; ?>
