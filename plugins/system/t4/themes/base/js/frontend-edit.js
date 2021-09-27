/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.isis
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.0
 */

jQuery(function($)
{
	'use strict';

	var $w = $(window);

	$(document.body)
		// add color classes to chosen field based on value
		.on('liszt:ready', 'select[class^="chzn-color"], select[class*=" chzn-color"]', function() {
			var $select = $(this);
			var cls = this.className.replace(/^.(chzn-color[a-z0-9-_]*)$.*/, '$1');
			var $container = $select.next('.chzn-container').find('.chzn-single');

			$container.addClass(cls).attr('rel', 'value_' + $select.val());
			$select.on('change click', function() {
				$container.attr('rel', 'value_' + $select.val());
			});
		})
		// Handle changes to (radio) button groups
		.on('change', '.btn-group input:radio', function () {
			var $this = $(this);
			var $group = $this.closest('.btn-group');
			var name = $this.prop('name');
			var reversed = $group.hasClass('btn-group-reversed');

			$group.find('input:radio[name="' + name + '"]').each(function () {
				var $input = $(this);
				// Get the enclosing label
				var $label = $input.closest('label');
				var inputId = $input.attr('id');
				var inputVal = $input.val();
				var btnClass = 'primary';

				// Include any additional labels for this control
				if (inputId) {
					$label = $label.add($('label[for="' + inputId + '"]'));
				}

				if ($input.prop('checked')) {
					if (inputVal != '') {
						btnClass = (inputVal == 0 ? !reversed : reversed) ? 'danger' : 'success';
					}

					$label.addClass('active btn-' + btnClass);
				} else {
					$label.removeClass('active btn-success btn-danger btn-primary');
				}
			})
		})
		.on('subform-row-add', initTemplate);

	initTemplate();

	// Called once on domready, again when a subform row is added
	function initTemplate(event, container)
	{
		var $container = $(container || document);

		// Create tooltips
		$container.find('*[rel=tooltip]').tooltip();

		// Turn radios into btn-group
		$container.find('.radio.btn-group label').addClass('btn');

		// Handle disabled, prevent clicks on the container, and add disabled style to each button
		$container.find('fieldset.btn-group:disabled').each(function() {
			$(this).css('pointer-events', 'none').off('click').find('.btn').addClass('disabled');
		});

		// Setup coloring for buttons
		$container.find('.btn-group input:checked').each(function() {
			var $input  = $(this);
			var $label = $('label[for=' + $input.attr('id') + ']');
			var btnClass = 'primary';

			if ($input.val() != '')
			{
				var reversed = $input.parent().hasClass('btn-group-reversed');
				btnClass = ($input.val() == 0 ? !reversed : reversed) ? 'danger' : 'success';
			}

			$label.addClass('active btn-' + btnClass);
		});
	}

/*
	//initChosen();
	//$("body").on("subform-row-add", initChosen);

	function initChosen(event, container)
	{
		container = container || document;
		$(container).find("select").chosen({"disable_search_threshold":10,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Type or select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	}
*/

});
