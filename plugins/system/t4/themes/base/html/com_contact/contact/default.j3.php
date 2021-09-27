<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;


$cparams = ComponentHelper::getParams('com_media');
$tparams = $this->item->params;
?>

<div class="contact<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Person">
	<?php if ($tparams->get('show_page_heading')) : ?>
		<h1>
			<?php echo $this->escape($tparams->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

	<?php if ($this->contact->name && $tparams->get('show_name')) : ?>
		<div class="page-header">
			<h2>
				<?php if ($this->item->published == 0) : ?>
					<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
				<?php endif; ?>
				<span class="contact-name" itemprop="name"><?php echo $this->contact->name; ?></span>
			</h2>
		</div>
	<?php endif; ?>

	<?php $show_contact_category = $tparams->get('show_contact_category'); ?>

	<?php if ($show_contact_category === 'show_no_link') : ?>
		<h3>
			<span class="contact-category"><?php echo $this->contact->category_title; ?></span>
		</h3>
	<?php elseif ($show_contact_category === 'show_with_link') : ?>
		<?php $contactLink = ContactHelperRoute::getCategoryRoute($this->contact->catid); ?>
		<h3>
			<span class="contact-category"><a href="<?php echo $contactLink; ?>">
				<?php echo $this->escape($this->contact->category_title); ?></a>
			</span>
		</h3>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayTitle; ?>

	<?php if ($tparams->get('show_contact_list') && count($this->contacts) > 1) : ?>
		<form action="#" method="get" name="selectForm" id="selectForm">
			<label for="select_contact"><?php echo JText::_('COM_CONTACT_SELECT_CONTACT'); ?></label>
			<?php echo JHtml::_('select.genericlist', $this->contacts, 'select_contact', 'class="inputbox" onchange="document.location.href = this.value"', 'link', 'name', $this->contact->link); ?>
		</form>
	<?php endif; ?>

	<?php if ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
		<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
		<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
	<?php endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php $presentation_style = $tparams->get('presentation_style'); ?>
	<?php $accordionStarted = false; ?>
	<?php $tabSetStarted = false; ?>


	<!-- SLIDERS STYLE -->
	<?php if ($presentation_style === 'sliders') : ?>
		<?php if ($this->params->get('show_info', 1)) : ?>

			<?php echo JHtml::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'basic-details')); ?>
			<?php $accordionStarted = true; ?>
			<?php echo JHtml::_('bootstrap.addSlide', 'slide-contact', JText::_('COM_CONTACT_DETAILS'), 'basic-details'); ?>

			<?php if ($this->contact->image && $tparams->get('show_image')) : ?>
			<div class="thumbnail contact-thumb mb-4">
				<?php echo JHtml::_('image', $this->contact->image, htmlspecialchars($this->contact->name,  ENT_QUOTES, 'UTF-8'), array('itemprop' => 'image')); ?>
			</div>
			<?php endif; ?>

			<?php if ($this->contact->con_position && $tparams->get('show_position')) : ?>
				<dl class="contact-position dl-horizontal">
					<dt><?php echo JText::_('COM_CONTACT_POSITION'); ?>:</dt>
					<dd itemprop="jobTitle">
						<?php echo $this->contact->con_position; ?>
					</dd>
				</dl>
			<?php endif; ?>

			<?php echo $this->loadTemplate('address'); ?>

			<?php if ($tparams->get('allow_vcard')) : ?>
				<?php echo JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?>
				<a href="<?php echo JRoute::_('index.php?option=com_contact&amp;view=contact&amp;id=' . $this->contact->id . '&amp;format=vcf'); ?>">
				<?php echo JText::_('COM_CONTACT_VCARD'); ?></a>
			<?php endif; ?>

			<?php echo JHtml::_('bootstrap.endSlide'); ?>

		<?php endif; ?>
		<!-- // Show info -->

		<!-- Show email -->
		<?php if ($tparams->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>
			<?php if (!$accordionStarted) {
				echo JHtml::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'display-form'));
				$accordionStarted = true;
			} ?>
			<?php echo JHtml::_('bootstrap.addSlide', 'slide-contact', JText::_('COM_CONTACT_EMAIL_FORM'), 'display-form'); ?>

			<?php echo $this->loadTemplate('form'); ?>
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php endif; ?>
		<!-- // Show email -->

		<!-- Show links -->
		<?php if ($tparams->get('show_links')) : ?>
			<?php if (!$accordionStarted) : ?>
				<?php echo JHtml::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'display-links')); ?>
				<?php $accordionStarted = true; ?>
				<?php echo $this->loadTemplate('links'); ?>
			<?php endif; ?>
		<?php endif; ?>
		<!-- // Show links -->

		<!-- Show articles -->
		<?php if ($tparams->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>
			<?php if (!$accordionStarted)
			{
				echo JHtml::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'display-articles'));
				$accordionStarted = true;
			}
			?>
			<?php echo JHtml::_('bootstrap.addSlide', 'slide-contact', JText::_('JGLOBAL_ARTICLES'), 'display-articles'); ?>
			<?php echo $this->loadTemplate('articles'); ?>
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php endif; ?>
		<!-- // Show articles -->

		<!-- Show profile -->
		<?php if ($tparams->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?>
			<?php if (!$accordionStarted)
			{
				echo JHtml::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'display-profile'));
				$accordionStarted = true;
			}
			?>
			<?php echo JHtml::_('bootstrap.addSlide', 'slide-contact', JText::_('COM_CONTACT_PROFILE'), 'display-profile'); ?>
			<?php echo $this->loadTemplate('profile'); ?>
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php endif; ?>
		<!-- // Show profile -->

		<!-- Custom field -->
		<?php if ($tparams->get('show_user_custom_fields') && $this->contactUser) : ?>
			<?php echo $this->loadTemplate('user_custom_fields'); ?>
		<?php endif; ?>
		<!-- // Custom field -->

		<!-- Misc -->
		<?php if ($this->contact->misc && $tparams->get('show_misc')) : ?>
			<?php if (!$accordionStarted)
			{
				echo JHtml::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'display-misc'));
				$accordionStarted = true;
			}
			?>
			<?php echo JHtml::_('bootstrap.addSlide', 'slide-contact', JText::_('COM_CONTACT_OTHER_INFORMATION'), 'display-misc'); ?>
			<div class="contact-miscinfo">
				<dl class="dl-horizontal">
					<dt>
						<span class="<?php echo $tparams->get('marker_class'); ?>">
						<?php echo $tparams->get('marker_misc'); ?>
						</span>
					</dt>
					<dd>
						<span class="contact-misc">
							<?php echo $this->contact->misc; ?>
						</span>
					</dd>
				</dl>
			</div>
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php endif; ?>
		<!-- // Misc -->

		<?php if ($accordionStarted) : ?>
			<?php echo JHtml::_('bootstrap.endAccordion'); ?>
		<?php endif; ?>

	<?php endif; ?>
	<!-- // SLIDERS STYLE -->

	<!-- TABS STYLE -->
	<?php if ($presentation_style === 'tabs') : ?>
		<?php if ($this->params->get('show_info', 1)) : ?>
			<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'basic-details')); ?>
			<?php $tabSetStarted = true; ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'basic-details', JText::_('COM_CONTACT_DETAILS')); ?>

			<?php if ($this->contact->image && $tparams->get('show_image')) : ?>
			<div class="thumbnail pull-right">
				<?php echo JHtml::_('image', $this->contact->image, htmlspecialchars($this->contact->name,  ENT_QUOTES, 'UTF-8'), array('itemprop' => 'image')); ?>
			</div>
			<?php endif; ?>

			<?php if ($this->contact->con_position && $tparams->get('show_position')) : ?>
				<dl class="contact-position dl-horizontal">
					<dt><?php echo JText::_('COM_CONTACT_POSITION'); ?>:</dt>
					<dd itemprop="jobTitle">
						<?php echo $this->contact->con_position; ?>
					</dd>
				</dl>
			<?php endif; ?>

			<?php echo $this->loadTemplate('address'); ?>

			<?php if ($tparams->get('allow_vcard')) : ?>
				<?php echo JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?>
				<a href="<?php echo JRoute::_('index.php?option=com_contact&amp;view=contact&amp;id=' . $this->contact->id . '&amp;format=vcf'); ?>">
				<?php echo JText::_('COM_CONTACT_VCARD'); ?></a>
			<?php endif; ?>

			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>
			<!-- // Show info -->

		<!-- Show email -->
		<?php if ($tparams->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>
			<?php if (!$tabSetStarted)
			{
				echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'display-form'));
				$tabSetStarted = true;
			}
			?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'display-form', JText::_('COM_CONTACT_EMAIL_FORM')); ?>

			<?php echo $this->loadTemplate('form'); ?>

			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>
		<!-- // Show email -->

		<!-- Show links -->
		<?php if ($tparams->get('show_links')) : ?>
			<?php if (!$tabSetStarted) : ?>
				<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'display-links')); ?>
				<?php $tabSetStarted = true; ?>
			<?php endif; ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>
		<!-- // Show links -->

		<!-- Show articles -->
		<?php if ($tparams->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>
			<?php if (!$tabSetStarted)
			{
				echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'display-articles'));
				$tabSetStarted = true;
			}
			?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'display-articles', JText::_('JGLOBAL_ARTICLES')); ?>
			<?php echo $this->loadTemplate('articles'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>
		<!-- // Show articles -->

		<!-- Show profile -->
		<?php if ($tparams->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?>
			<?php if (!$tabSetStarted)
			{
				echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'display-profile'));
				$tabSetStarted = true;
			}
			?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'display-profile', JText::_('COM_CONTACT_PROFILE')); ?>
			<?php echo $this->loadTemplate('profile'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>
		<!-- // Show profile -->

		<!-- Custom field -->
		<?php if ($tparams->get('show_user_custom_fields') && $this->contactUser) : ?>
			<?php echo $this->loadTemplate('user_custom_fields'); ?>
		<?php endif; ?>
		<!-- // Custom field -->

		<!-- Misc -->
		<?php if ($this->contact->misc && $tparams->get('show_misc')) : ?>
			<?php if (!$tabSetStarted)
			{
				echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'display-misc'));
				$tabSetStarted = true;
			}
			?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'display-misc', JText::_('COM_CONTACT_OTHER_INFORMATION')); ?>
			<div class="contact-miscinfo">
				<dl class="dl-horizontal">
					<dt>
						<span class="<?php echo $tparams->get('marker_class'); ?>">
						<?php echo $tparams->get('marker_misc'); ?>
						</span>
					</dt>
					<dd>
						<span class="contact-misc">
							<?php echo $this->contact->misc; ?>
						</span>
					</dd>
				</dl>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>
		<!-- // Misc -->

		<?php if ($tabSetStarted) : ?>
			<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<?php endif; ?>

	<?php endif; ?>
	<!-- // TABS STYLE -->

	<!-- PLAIN STYLE -->
	<?php if ($presentation_style === 'plain') : ?>
	<div class="plain-style">
		<div class="row">
			<div class="col-12 col-md-7">
				<!-- // Show email -->
				<?php if ($tparams->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>
					<?php echo '<h3>' . JText::_('COM_CONTACT_EMAIL_FORM') . '</h3>'; ?>
					<?php echo $this->loadTemplate('form'); ?>
				<?php endif; ?>
				<!-- // Show email -->
			</div>

			<div class="col-12 col-md-5">
				<?php if ($this->params->get('show_info', 1)) : ?>
					<?php echo '<h3>' . JText::_('COM_CONTACT_DETAILS') . '</h3>'; ?>

					<?php if ($this->contact->image && $tparams->get('show_image')) : ?>
					<div class="contact-image">
						<?php echo JHtml::_('image', $this->contact->image, htmlspecialchars($this->contact->name,  ENT_QUOTES, 'UTF-8'), array('itemprop' => 'image')); ?>
					</div>
					<?php endif; ?>

					<?php if ($this->contact->con_position && $tparams->get('show_position')) : ?>
						<div class="contact-position">
							<dl class="contact-position dl-horizontal">
								<dt><?php echo JText::_('COM_CONTACT_POSITION'); ?>:</dt>
								<dd itemprop="jobTitle">
									<?php echo $this->contact->con_position; ?>
								</dd>
							</dl>
						</div>
					<?php endif; ?>

					<?php echo $this->loadTemplate('address'); ?>

					<?php if ($tparams->get('allow_vcard')) : ?>
						<?php echo JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?>
						<a href="<?php echo JRoute::_('index.php?option=com_contact&amp;view=contact&amp;id=' . $this->contact->id . '&amp;format=vcf'); ?>">
						<?php echo JText::_('COM_CONTACT_VCARD'); ?></a>
					<?php endif; ?>
				<?php endif; ?>
				<!-- // Show info -->

				<!-- Show links -->
				<?php if ($tparams->get('show_links')) : ?>
					<div class="contact-link">
						<?php echo $this->loadTemplate('links'); ?>
					</div>
				<?php endif; ?>
				<!-- // Show links -->

				<!-- Show articles -->
				<?php if ($tparams->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>
					<div class="contact-articles">
						<?php echo '<h3>' . JText::_('JGLOBAL_ARTICLES') . '</h3>'; ?>
						<?php echo $this->loadTemplate('articles'); ?>
					</div>
				<?php endif; ?>
				<!-- // Show articles -->

				<!-- Show profile -->
				<?php if ($tparams->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?>
					<div class="contact-profile">
						<?php echo '<h3>' . JText::_('COM_CONTACT_PROFILE') . '</h3>'; ?>
						<?php echo $this->loadTemplate('profile'); ?>
					</div>
				<?php endif; ?>
				<!-- // Show profile -->

				<!-- Custom field -->
				<?php if ($tparams->get('show_user_custom_fields') && $this->contactUser) : ?>
					<div class="contact-custom-field">
						<?php echo $this->loadTemplate('user_custom_fields'); ?>
					</div>
				<?php endif; ?>
				<!-- // Custom field -->

				<!-- Misc -->
				<?php if ($this->contact->misc && $tparams->get('show_misc')) : ?>
					<div class="contact-miscinfo">
						<?php echo '<h3>' . JText::_('COM_CONTACT_OTHER_INFORMATION') . '</h3>'; ?>
						<span class="<?php echo $tparams->get('marker_class'); ?>">
							<?php echo $tparams->get('marker_misc'); ?>
						</span>

						<div class="contact-misc">
							<?php echo $this->contact->misc; ?>
						</div>
				</div>
				<?php endif; ?>
				<!-- // Misc -->
			</div>
		</div>
	</div>
	<?php endif; ?>
	<!-- // PLAIN STYLE -->

	<?php echo $this->item->event->afterDisplayContent; ?>
</div>
