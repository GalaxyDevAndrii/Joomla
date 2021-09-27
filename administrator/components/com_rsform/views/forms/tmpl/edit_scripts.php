<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
	<div class="<?php echo RSFormProAdapterGrid::column(12); ?>">
		<fieldset>
			<?php
			foreach ($this->jform->getFieldset('scripts') as $field)
			{
				?>
				<legend class="rsfp-legend"><?php echo $field->title; ?></legend>
				<div class="alert alert-info"><?php echo JText::_($field->description); ?></div>
				<?php
				echo $field->input;
			}
			?>
		</fieldset>
	</div>
</div>