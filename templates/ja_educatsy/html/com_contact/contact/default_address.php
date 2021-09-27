<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

/**
 * marker_class: Class based on the selection of text, none, or icons
 */
?>
<dl class="contact-address dl-horizontal" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
	<?php if (($this->params->get('address_check') > 0) &&
		($this->contact->address || $this->contact->suburb  || $this->contact->state || $this->contact->country || $this->contact->postcode)) : ?>
		<?php if ($this->params->get('address_check') > 0) : ?>
			<dt class="<?php echo "show-".$this->params->get('contact_icons') ?>">
				<span class="<?php echo $this->params->get('marker_class'); ?>" >
					<?php echo $this->params->get('marker_address'); ?>
				</span>
			</dt>
		<?php endif; ?>

		<?php if ($this->contact->address && $this->params->get('show_street_address')) : ?>
			<dd class="<?php echo "show-".$this->params->get('contact_icons') ?>">
				<span class="contact-street" itemprop="streetAddress">
					<span class="fa fa-map-marker"> </span><?php echo $this->contact->address; ?>
				</span>
			</dd>
		<?php endif; ?>

		<?php if ($this->contact->suburb && $this->params->get('show_suburb')) : ?>
			<dd class="<?php echo "show-".$this->params->get('contact_icons') ?>">
				<span class="contact-suburb" itemprop="addressLocality">
					<span class="fa fa-location-arrow"></span><?php echo $this->contact->suburb .'<br/>'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->contact->state && $this->params->get('show_state')) : ?>
			<dd class="<?php echo "show-".$this->params->get('contact_icons') ?>">
				<span class="contact-state" itemprop="addressRegion">
					<span class="fa fa-location-arrow"></span><?php echo $this->contact->state . '<br/>'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->contact->postcode && $this->params->get('show_postcode')) : ?>
			<dd class="<?php echo "show-".$this->params->get('contact_icons') ?>">
				<span class="contact-postcode" itemprop="postalCode">
					<span class="fa fa-magic"></span><?php echo $this->contact->postcode .'<br/>'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->contact->country && $this->params->get('show_country')) : ?>
		<dd class="<?php echo "show-".$this->params->get('contact_icons') ?>">
			<span class="contact-country" itemprop="addressCountry">
				<span class="fa fa-building-o"></span><?php echo $this->contact->country .'<br/>'; ?>
			</span>
		</dd>
		<?php endif; ?>
	<?php endif; ?>

<?php if ($this->contact->email_to && $this->params->get('show_email')) : ?>
	<dt class="<?php echo "show-".$this->params->get('contact_icons') ?>">
		<span class="<?php echo $this->params->get('marker_class'); ?>" itemprop="email">
			<?php echo nl2br($this->params->get('marker_email')); ?>
		</span>
	</dt>
	<dd class="<?php echo "show-".$this->params->get('contact_icons') ?>">
		<span class="contact-emailto">
			<span class="fa fa-envelope-o"></span><?php echo $this->contact->email_to; ?>
		</span>
	</dd>
<?php endif; ?>

<?php if ($this->contact->telephone && $this->params->get('show_telephone')) : ?>
	<dt class="<?php echo "show-".$this->params->get('contact_icons') ?>">
		<span class="<?php echo $this->params->get('marker_class'); ?>">
			<?php echo $this->params->get('marker_telephone'); ?>
		</span>
	</dt>
	<dd class="<?php echo "show-".$this->params->get('contact_icons') ?>">
		<span class="contact-telephone" itemprop="telephone">
			<span class="fa fa-phone"></span><?php echo nl2br($this->contact->telephone); ?>
		</span>
	</dd>
<?php endif; ?>
<?php if ($this->contact->fax && $this->params->get('show_fax')) : ?>
	<dt class="<?php echo "show-".$this->params->get('contact_icons') ?>">
		<span class="<?php echo $this->params->get('marker_class'); ?>">
			<?php echo $this->params->get('marker_fax'); ?>
		</span>
	</dt>
	<dd class="<?php echo "show-".$this->params->get('contact_icons') ?>">
		<span class="contact-fax" itemprop="faxNumber">
			<span class="fa fa-print"></span><?php echo nl2br($this->contact->fax); ?>
		</span>
	</dd>
<?php endif; ?>
<?php if ($this->contact->mobile && $this->params->get('show_mobile')) :?>
	<dt class="<?php echo "show-".$this->params->get('contact_icons') ?>">
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_mobile'); ?>
		</span>
	</dt>
	<dd class="<?php echo "show-".$this->params->get('contact_icons') ?>">
		<span class="contact-mobile" itemprop="telephone">
			<span class="fa fa-phone-square"></span><?php echo nl2br($this->contact->mobile); ?>
		</span>
	</dd>
<?php endif; ?>
<?php if ($this->contact->webpage && $this->params->get('show_webpage')) : ?>
	<dt class="<?php echo "show-".$this->params->get('contact_icons') ?>">
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo JText::_('TPL_CONTACT_WEBSITE');?>
		</span>
	</dt>
	<dd class="<?php echo "show-".$this->params->get('contact_icons') ?>">
		<span class="contact-webpage">
			<span class="fa fa-globe"></span><a href="<?php echo $this->contact->webpage; ?>" target="_blank" itemprop="url">
			<?php echo $this->contact->webpage; ?></a>
		</span>
	</dd>
<?php endif; ?>
</dl>
