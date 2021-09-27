<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php echo RSFormProAdapterGrid::sidebar(); ?>
		<div id="dashboard-left">
			<?php
			$rows = array_chunk($this->buttons, 4);
			foreach ($rows as $buttons)
			{
				?>
				<div class="dashboard-container">
					<?php
					foreach ($buttons as $button)
					{
						if ($button['access'])
						{
							?>
							<div class="dashboard-info dashboard-button">
								<a <?php if ($button['target']) { ?> target="<?php echo $this->escape($button['target']); ?>"<?php } ?> href="<?php echo $button['link']; ?>">
									<i class="dashboard-icon rsficon rsficon-<?php echo $button['icon']; ?>"></i>
									<span class="dashboard-title"><?php echo $button['text']; ?></span>
								</a>
							</div>
						<?php
						}
					}
					?>
				</div>
				<?php
			}
			?>
		</div>
		<div id="dashboard-right" class="hidden-phone hidden-tablet">
			<div class="dashboard-container">
				<div class="dashboard-info">
					<?php echo JHtml::_('image', 'com_rsform/admin/rsformpro.png', JText::_('COM_RSFORM'), null, true); ?>
					<table class="dashboard-table">
						<tr>
							<td nowrap="nowrap" class="text-right"><strong><?php echo JText::_('COM_RSFORM_PRODUCT_VERSION') ?>: </strong></td>
							<td nowrap="nowrap">RSForm! Pro <?php echo $this->version; ?></td>
						</tr>
						<tr>
							<td nowrap="nowrap" class="text-right"><strong><?php echo JText::_('COM_RSFORM_COPYRIGHT_NAME') ?>: </strong></td>
							<td nowrap="nowrap">&copy; 2007 - <?php echo gmdate('Y'); ?> <a href="https://www.rsjoomla.com" target="_blank">RSJoomla!</a></td>
						</tr>
						<tr>
							<td nowrap="nowrap" class="text-right"><strong><?php echo JText::_('COM_RSFORM_LICENSE_NAME') ?>: </strong></td>
							<td nowrap="nowrap"><a href="https://www.gnu.org/licenses/gpl.html" target="_blank">GNU/GPL</a> <?php echo JText::_('COM_RSFORM_COMMERCIAL'); ?></td>
						</tr>
						<tr>
							<td nowrap="nowrap" class="text-right"><strong><?php echo JText::_('COM_RSFORM_UPDATE_CODE') ?>: </strong></td>
							<?php if (strlen($this->code) == 20) { ?>
								<td nowrap="nowrap" class="correct-code"><?php echo $this->escape($this->code); ?></td>
							<?php } elseif ($this->code) { ?>
								<td nowrap="nowrap" class="incorrect-code"><?php echo $this->escape($this->code); ?>
									<br />
									<strong><a href="https://www.rsjoomla.com/support/documentation/general-faq/where-do-i-find-my-license-code-.html" target="_blank"><?php echo JText::_('COM_RSFORM_WHERE_DO_I_FIND_THIS'); ?></a></strong>
								</td>
							<?php } else { ?>
								<td nowrap="nowrap" class="missing-code"><a href="<?php echo JRoute::_('index.php?option=com_rsform&view=configuration'); ?>"><?php echo JText::_('COM_RSFORM_PLEASE_ENTER_YOUR_CODE_IN_THE_CONFIGURATION'); ?></a>
									<br />
									<strong><a href="https://www.rsjoomla.com/support/documentation/general-faq/where-do-i-find-my-license-code-.html" target="_blank"><?php echo JText::_('COM_RSFORM_WHERE_DO_I_FIND_THIS'); ?></a></strong>
								</td>
							<?php } ?>
						</tr>
					</table>
				</div>
			</div>
			<p class="text-center center"><a href="https://www.rsjoomla.com/joomla-components/joomla-security.html?utm_source=rsform&amp;utm_medium=banner_approved&amp;utm_campaign=rsfirewall" target="_blank"><?php echo JHtml::_('image', 'com_rsform/admin/rsfirewall-approved.png', JText::_('COM_RSFORM_RSFIREWALL_APPROVED'), null, true); ?></a></p>
		</div>
	</div>
	
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="" />
</form>