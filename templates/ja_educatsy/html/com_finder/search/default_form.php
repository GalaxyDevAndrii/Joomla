<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

/*
* This segment of code sets up the autocompleter.
*/
$doc = JFactory::getDocument();
if ($this->params->get('show_advanced', 1) || $this->params->get('show_autosuggest', 1))
{
	JHtml::_('jquery.framework');

	$script = "
jQuery(function() {";

	if ($this->params->get('show_advanced', 1))
	{
		/*
		* This segment of code disables select boxes that have no value when the
		* form is submitted so that the URL doesn't get blown up with null values.
		*/
		$script .= "
	jQuery('#finder-search').on('submit', function(e){
		e.stopPropagation();
		// Disable select boxes with no value selected.
		jQuery('#advancedSearch').find('select').each(function(index, el) {
			var el = jQuery(el);
			if(!el.val()){
				el.attr('disabled', 'disabled');
			}
		});
	});";
	}

	/*
	* This segment of code sets up the autocompleter.
	*/
	if ($this->params->get('show_autosuggest', 1))
	{
		// enable jquery.ui
		$wam = \T4\Helper\Asset::getWebAssetManager();
		$wam->enableAsset('jquery.autocomplete');
		$script .= "
		jQuery('.input-group-append a.btn').on('click',function(e){
			e.preventDefault();
			e.stopPropagation();
			var target = jQuery(this).data('target');jQuery(target).slideToggle();
		});
	var suggest = jQuery('#q').autocomplete({
		serviceUrl: '" . JRoute::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component') . "',
		paramName: 'q',
		minChars: 1,
		maxHeight: 400,
		width: 300,
		zIndex: 9999,
		deferRequestBy: 500
	});";
	}

	$script .= "
});";

	$doc->addScriptDeclaration($script);
}


?>

<form action="<?php echo JRoute::_($this->query->toUri()); ?>" method="get" class="js-finder-searchform">
	<?php echo $this->getFields(); ?>

	<?php //DISABLED UNTIL WEIRD VALUES CAN BE TRACKED DOWN. ?>
	<?php if (false && $this->state->get('list.ordering') !== 'relevance_dsc') : ?>
		<input type="hidden" name="o" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>">
	<?php endif; ?>
	<fieldset class="com-finder__search word mb-3">
		<div class="form-inline">
			<label for="q" class="mr-2">
				<?php echo JText::_('COM_FINDER_SEARCH_TERMS'); ?>
			</label>
			<div class="input-group">
				<input type="text" name="q" id="q" class="js-finder-search-query form-control" autocomplete="off" value="<?php echo $this->escape($this->query->input); ?>">
				<span class="input-group-append">
				<?php if ($this->escape($this->query->input) != '' || $this->params->get('allow_empty_query')) : ?>
					<button name="Search" type="submit" class="btn btn-primary">
						<span class="fa fa-search icon-white" aria-hidden="true"></span>
						<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
					</button>
				<?php else : ?>
					<button name="Search" type="submit" class="btn btn-primary disabled">
						<span class="fa fa-search icon-white" aria-hidden="true"></span>
						<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
					</button>
				<?php endif; ?>
				<?php if ($this->params->get('show_advanced', 1)) : ?>
					<a href="#advancedSearch" data-toggle="collapse" class="btn btn-secondary" data-target="#advancedSearch" aria-hidden="true" role="button" aria-expanded="false" aria-controls="advancedSearch">
						<span class="fa fa-search-plus" aria-hidden="true"></span>
						<?php echo JText::_('COM_FINDER_ADVANCED_SEARCH_TOGGLE'); ?></a>
				<?php endif; ?>
				</span>
			</div>
		</div>
	</fieldset>

	<?php if ($this->params->get('show_advanced', 1)) : ?>
		<div id="advancedSearch" class="com-finder__advanced js-finder-advanced collapse<?php if ($this->params->get('expand_advanced', 0)) echo ' show'; ?>">
			<?php if ($this->params->get('show_advanced_tips', 1)) : ?>
				<div class="com-finder__tips card card-outline-secondary mb-3">
					<div class="card-body">
						<?php echo JText::_('COM_FINDER_ADVANCED_TIPS'); ?>
					</div>
				</div>
			<?php endif; ?>
			<div id="finder-filter-window" class="com-finder__filter">
				<?php echo JHtml::_('filter.select', $this->query, $this->params); ?>
			</div>
		</div>
	<?php endif; ?>
</form>
