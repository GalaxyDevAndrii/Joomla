<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

?>
<div class="com-users-registration-complete registration-complete <?php echo $this->pageclass_sfx;?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>
</div>
