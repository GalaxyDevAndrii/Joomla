<?php
/**
 * ------------------------------------------------------------------------
 * JA ACM Module
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
$field = $displayData['field'];
$value = htmlspecialchars($field->value, ENT_COMPAT, 'UTF-8');
$id = $field->id;
$baseon = $field->element['baseon'];
?>

<div id="<?php echo $id ?>" class="jachecklist <?php echo $id ?>" data-baseon="<?php echo $baseon ?>">
	<input id="<?php echo $id ?>-val" class="<?php echo $id ?>-val" type="hidden" name="<?php echo $field->name ?>"
				 value="<?php echo $value ?>"/>
	<ul class="jachecklist-list">
		<li class="jachecklist-item">
			<span class="jachecklist-item-titel">Item Title</span>
		</li>
	</ul>
</div>

<script>
	(function ($) {
		var $elem = $('#<?php echo $id ?>'),
			baseon = $elem.data('baseon'),
			$container = $elem.parents('.jatools-layout-config');

		$container.on('change', function () {
			// get baseon
			var $baseons = $container.find('input, textarea, select').filter(function () {
					return this.name.indexOf('[' + baseon + ']') > -1
				}),
				list = $baseons.map(function () {
					if (this.type == 'checkbox' || this.type == 'radio') {
						if (this.checked) return this.value;
					} else {
						return $(this).val();
					}
				}).get();
			$container.find('.<?php echo $id ?>').each(function () {
				var $list = $(this).find('.jachecklist-list'),
					$olds = $list.find('.jachecklist-item'),
					$tmp = $olds.first(),
					value = $(this).find('.<?php echo $id ?>-val').val(),
					newval = 0;

				$.each(list, function (i, title) {
					var $newitem = $tmp.clone(true, true);
					$newitem.find('.jachecklist-item-titel').html(title);
					if (value & Math.pow(2, i)) {
						newval += Math.pow(2, i);
						$newitem.addClass('on').removeClass('off');
					} else {
						$newitem.addClass('off').removeClass('on');
					}
					$newitem.appendTo($list);
				});
				$olds.remove();

				// update newval back
				$(this).find('.<?php echo $id ?>-val').val(newval);
			});
		});

		// toggle for item
		$container.find('.<?php echo $id ?>').find('.jachecklist-item').on('click', function () {
			var $this = $(this),
				$parent = $this.parents('.jachecklist').first();
			if ($this.hasClass('on')) {
				$this.addClass('off').removeClass('on');
			} else {
				$this.addClass('on').removeClass('off');
			}
			// update value
			var newval = 0;
			$parent.find('.jachecklist-item').each(function (i, item) {
				if ($(item).hasClass('on')) newval += Math.pow(2, i);
			});
			$parent.find('.<?php echo $id ?>-val').val(newval);
		})
	})(jQuery);
</script>
