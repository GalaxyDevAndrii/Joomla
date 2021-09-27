<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');

JText::script('COM_RSFORM_ARE_YOU_SURE_YOU_WANT_TO_CLEAR');
?>

<form action="index.php?option=com_rsform&amp;view=forms" method="post" name="adminForm" id="adminForm">
	<?php
	echo RSFormProAdapterGrid::sidebar();

	echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));

	if (empty($this->items)) { ?>
	<div class="alert alert-info">
		<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo JText::_('INFO'); ?></span>
		<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
	<?php } else { ?>
	<table class="table table-striped table-align-middle">
		<caption id="captionTable" class="sr-only">
			<?php echo JText::_('COM_RSFORM_FORMS_TABLE_CAPTION'); ?>,
			<span id="orderedBy"><?php echo JText::_('JGLOBAL_SORTED_BY'); ?> </span>,
			<span id="filteredBy"><?php echo JText::_('JGLOBAL_FILTERED_BY'); ?></span>
		</caption>
		<thead>
		<tr>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('#'); ?></th>
			<th style="width:1%" class="text-center">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th class="title"><?php echo JHtml::_('searchtools.sort', JText::_('RSFP_FORM_TITLE'), 'FormTitle', $this->sortOrder, $this->sortColumn, 'forms.manage'); ?></th>
			<th class="title"><?php echo JHtml::_('searchtools.sort', JText::_('RSFP_FORM_NAME'), 'FormName', $this->sortOrder, $this->sortColumn, 'forms.manage'); ?></th>
			<th width="1%" nowrap="nowrap" class="title"><?php echo JHtml::_('searchtools.sort', JText::_('RSFP_PUBLISHED'), 'Published', $this->sortOrder, $this->sortColumn, 'forms.manage'); ?></th>
            <?php if ($this->user->authorise('submissions.manage', 'com_rsform')) { ?>
			<th width="1%" nowrap="nowrap" class="title"><?php echo JText::_('RSFP_SUBMISSIONS'); ?></th>
            <?php } ?>
			<th class="title"><?php echo JText::_('RSFP_TOOLS'); ?></th>
			<?php if (!$this->disable_multilanguage) { ?>
			<th class="title" width="1%" nowrap="nowrap"><?php echo JText::_('RSFP_LAST_LANGUAGE'); ?></th>
			<?php } ?>
			<th width="1%" nowrap="nowrap" class="title"><?php echo JHtml::_('searchtools.sort', JText::_('RSFP_FORM_ID'), 'FormId', $this->sortOrder, $this->sortColumn, 'forms.manage'); ?></th>
		</tr>
		</thead>
	<?php
	$i = 0;
	foreach ($this->items as $row)
	{
		$row->published = $row->Published;
		$row->FormTitle = strip_tags($row->FormTitle);
		?>
		<tr>
			<td width="1%" nowrap="nowrap"><?php echo $this->pagination->getRowOffset($i); ?></td>
			<td width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.id', $i, $row->FormId); ?></td>
			<td><a href="index.php?option=com_rsform&amp;view=forms&amp;layout=edit&amp;formId=<?php echo $row->FormId; ?>"><?php echo !empty($row->FormTitle) ? $row->FormTitle : '<em>'.JText::_('RSFP_FORM_DEFAULT_TITLE').'</em>'; ?></a></td>
			<td><?php echo $this->escape($row->FormName); ?></td>
			<td width="1%" nowrap="nowrap" align="center"><?php echo JHtml::_('jgrid.published', $row->published, $i, 'forms.'); ?></td>
            <?php if ($this->user->authorise('submissions.manage', 'com_rsform')) { ?>
			<td width="1%" nowrap="nowrap">
				<span class="hasTooltip" title="<?php echo JText::sprintf('RSFP_TODAY_SUBMISSIONS', $row->_todaySubmissions); ?>"><a href="index.php?option=com_rsform&amp;view=submissions&amp;formId=<?php echo $row->FormId; ?>&amp;filter[dateFrom]=<?php echo $this->today; ?>"><i class="rsficon rsficon-calendar"></i> <?php echo $row->_todaySubmissions; ?></a></span>
				<span class="hasTooltip" title="<?php echo JText::sprintf('RSFP_MONTH_SUBMISSIONS', $row->_monthSubmissions); ?>"><a href="index.php?option=com_rsform&amp;view=submissions&amp;formId=<?php echo $row->FormId; ?>&amp;filter[dateFrom]=<?php echo $this->month; ?>"><i class="rsficon rsficon-calendar"></i> <?php echo $row->_monthSubmissions; ?></a></span>
				<span class="hasTooltip" title="<?php echo JText::sprintf('RSFP_ALL_SUBMISSIONS', $row->_allSubmissions); ?>"><a href="index.php?option=com_rsform&amp;view=submissions&amp;formId=<?php echo $row->FormId; ?>&amp;filter[dateFrom]="><i class="rsficon rsficon-calendar"></i> <?php echo $row->_allSubmissions; ?></a></span>
			</td>
            <?php } ?>
			<td align="center" nowrap="nowrap">
				<a class="btn btn-secondary" href="<?php echo JUri::root(); ?>index.php?option=com_rsform&amp;view=rsform&amp;formId=<?php echo $row->FormId; ?>" target="_blank"><span class="rsficon rsficon-eye rsficon-green"></span> <?php echo JText::_('RSFP_PREVIEW'); ?></a>
				<div class="btn-group">
					<a class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" href="#"><?php echo JText::_('RSFP_TOOLS'); ?> <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="<?php echo JText::_('RSFP_TOOLS'); ?>">
						<li><a class="dropdown-item" href="index.php?option=com_rsform&amp;task=forms.menuadd.screen&amp;formId=<?php echo $row->FormId; ?>"><span class="rsficon rsficon-share rsficon-blue"></span> <?php echo JText::_('RSFP_LINK_TO_MENU'); ?></a></li>
						<?php if ($row->Backendmenu) { ?>
						<li><a class="dropdown-item" href="index.php?option=com_rsform&amp;task=forms.menuremove.backend&amp;formId=<?php echo $row->FormId; ?>"><span class="rsficon rsficon-minus-circle rsficon-red"></span>  <?php echo JText::_('LINK_TO_BACKEND_REMOVE_MENU'); ?></a></li>
						<?php } else { ?>
						<li><a class="dropdown-item" href="index.php?option=com_rsform&amp;task=forms.menuadd.backend&amp;formId=<?php echo $row->FormId; ?>"><span class="rsficon rsficon-plus-circle rsficon-green"></span>  <?php echo JText::_('LINK_TO_BACKEND_MENU'); ?></a></li>
						<?php } ?>
                        <?php if ($this->user->authorise('submissions.manage', 'com_rsform')) { ?>
						<li><a class="dropdown-item" href="index.php?option=com_rsform&amp;task=submissions.clear&amp;formId=<?php echo $row->FormId; ?>&amp;<?php echo JSession::getFormToken(); ?>=1" onclick="return (confirm(Joomla.JText._('COM_RSFORM_ARE_YOU_SURE_YOU_WANT_TO_CLEAR')));"><span class="rsficon rsficon-times-circle-o rsficon-red"></span>  <?php echo JText::_('RSFP_CLEAR_SUBMISSIONS'); ?></a></li>
                        <?php } ?>
					</ul>
				</div>
			</td>
			<?php if (!$this->disable_multilanguage) { ?>
			<td width="1%" nowrap="nowrap"><?php echo $this->escape(RSFormProHelper::getCurrentLanguage($row->FormId)); ?></td>
			<?php } ?>
			<td width="1%" nowrap="nowrap"><?php echo $row->FormId; ?></td>
		</tr>
	<?php
		$i++;
	}
	?>
	</table>
	<?php echo $this->pagination->getListFooter(); ?>
	<?php } ?>

	</div>
	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="" />
</form>