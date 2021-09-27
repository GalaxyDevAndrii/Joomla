<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;
?>
<div class="container-fluid">
	<div class="<?php echo RSFormProAdapterGrid::row(); ?> text-center">
		<div class="rsfp-column-option <?php echo RSFormProAdapterGrid::column(3); ?>">
			<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
				<div class="<?php echo RSFormProAdapterGrid::column(12); ?>"><div class="rsfp-column-color"></div></div>
			</div>
			<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
				<div class="<?php echo RSFormProAdapterGrid::column(12); ?>">
					<p><button class="btn btn-secondary" onclick="RSFormPro.gridModal.save([12]);" type="button"><?php echo JText::_('RSFP_GRID_ONE_COLUMN'); ?></button></p>
				</div>
			</div>
		</div>
		
		<div class="rsfp-column-option <?php echo RSFormProAdapterGrid::column(3); ?>">
			<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
				<div class="<?php echo RSFormProAdapterGrid::column(6); ?>"><div class="rsfp-column-color"></div></div>
				<div class="<?php echo RSFormProAdapterGrid::column(6); ?>"><div class="rsfp-column-color"></div></div>
			</div>
			<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
				<div class="<?php echo RSFormProAdapterGrid::column(12); ?>">
					<p><button class="btn btn-secondary" onclick="RSFormPro.gridModal.save([6,6]);" type="button"><?php echo JText::_('RSFP_GRID_TWO_COLUMNS'); ?></button></p>
				</div>
			</div>
		</div>
		
		<div class="rsfp-column-option <?php echo RSFormProAdapterGrid::column(3); ?>">
			<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
				<div class="<?php echo RSFormProAdapterGrid::column(4); ?>"><div class="rsfp-column-color"></div></div>
				<div class="<?php echo RSFormProAdapterGrid::column(4); ?>"><div class="rsfp-column-color"></div></div>
				<div class="<?php echo RSFormProAdapterGrid::column(4); ?>"><div class="rsfp-column-color"></div></div>
			</div>
			<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
				<div class="<?php echo RSFormProAdapterGrid::column(12); ?>">
					<p><button class="btn btn-secondary" onclick="RSFormPro.gridModal.save([4,4,4]);" type="button"><?php echo JText::_('RSFP_GRID_THREE_COLUMNS'); ?></button></p>
				</div>
			</div>
		</div>
		
		<div class="rsfp-column-option <?php echo RSFormProAdapterGrid::column(3); ?>">
			<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
				<div class="<?php echo RSFormProAdapterGrid::column(3); ?>"><div class="rsfp-column-color"></div></div>
				<div class="<?php echo RSFormProAdapterGrid::column(3); ?>"><div class="rsfp-column-color"></div></div>
				<div class="<?php echo RSFormProAdapterGrid::column(3); ?>"><div class="rsfp-column-color"></div></div>
				<div class="<?php echo RSFormProAdapterGrid::column(3); ?>"><div class="rsfp-column-color"></div></div>
			</div>
			<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
				<div class="<?php echo RSFormProAdapterGrid::column(12); ?>">
					<p><button class="btn btn-secondary" onclick="RSFormPro.gridModal.save([3,3,3,3]);" type="button"><?php echo JText::_('RSFP_GRID_FOUR_COLUMNS'); ?></button></p>
				</div>
			</div>
		</div>
	</div>
</div>
