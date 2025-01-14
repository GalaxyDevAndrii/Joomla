;(function ($) {

	var RSPlaceholder = function ($el, options) {

		this._defaults = {};

		this.element = $el;

		this._options = $.extend(true, {}, this._defaults, options);

		this.options = function (options) {
			return (options) ?
				$.extend(true, this._options, options) :
				this._options;
		};

		this.addDropdown = function (element) {
			var $plugin = this;

			var $html = '',
				$filter_src,
				$action,
				$skip,
				$array,
				$el = $(element);

			$action = $el.attr('data-filter-type') ? $el.attr('data-filter-type') : '';
			$filter_src = $el.attr('data-filter') ? $el.attr('data-filter').split(',') : [];
			$skip = $el.attr('data-skip') ? $el.attr('data-skip').split(',') : [];

			var $filter = {
				action: $action,
				filter: $filter_src,
				skip  : $skip
			};

			$array = $plugin.filterPlaceholders($filter, RSFormPro.Placeholders);

			$.each($array, function () {
				$html += '<li><a href="javascript:void(0)" class="rsfp-dropdown-placeholder" data-value="' + this + '">' + this + '</a></li>'
			});

			var classes = ['placeholders-input-append', 'btn', 'btn-primary'];
			if (!$el.hasClass('form-control'))
			{
				classes.push('btn-sm');
			}

			$el.after('<div class="rsfp-dropdown-list-container"><button class="' + classes.join(' ') + '" id="' + $el.attr('id') + '-list" type="button">&#9660;</button><ul class="rsfp-dropdown-list" data-target="' + $el.attr('id') + '-list">' + $html + '</ul></div>');

			this.initDropdowns($('#' + $el.attr('id') + '-list'));
		};

		this.initDropdowns = function (element) {

			var $id = $(element).attr('id'),
				$dropdown = $('[data-target="' + $id + '"]');

			$(element).on('click', function (e) {
				e.preventDefault();
				$('.rsfp-dropdown-list').hide();
				$dropdown.toggle();
			});

			$dropdown.find('li > a').on('click', function () {
				var $inputField = $(this).parents('.rsfp-dropdown-list-container').siblings('input');
				var $val = $inputField.val();
				$val += $inputField.val() ? $inputField.attr('data-delimiter') + $(this).attr('data-value') : $(this).attr('data-value');
				$inputField.val($val)
			});
		};

		this.filterPlaceholders = function (filter, placeholders) {
			$.each(filter.skip, function (e, value) {
				var $filter = value;
				placeholders = $.grep(placeholders, function (value, index) {
					return !String(value).match($filter);
				});
			});

			switch (filter.action) {
				case 'include':
					var $newArray = [];
					$.each(filter.filter, function () {
						var $filter = this;
						$.each(placeholders, function (e, value) {
							if (String(value).match($filter)) {
								$newArray.push(this);
							}
						});
					});
					placeholders = $newArray;
					break;
				case 'exclude':
					$.each(filter.filter, function (e, value) {
						var $filter = value;
						placeholders = $.grep(placeholders, function (value, index) {
							return !String(value).match($filter);
						});
					});
					break;
			}

			return placeholders;
		};

	};

	$.fn.rsplaceholder = function (methodOrOptions) {

		var method = (typeof methodOrOptions === 'string') ? methodOrOptions : undefined;

		if (method) {
			var rsplaceholders = [];

			function getRSPlaceholders() {
				var $el = $(this);
				var rsplaceholder = $el.data('rsplaceholder');

				rsplaceholders.push(rsplaceholder);
			}

			this.each(getRSPlaceholders);

			var args = (arguments.length > 1) ? Array.prototype.slice.call(arguments, 1) : undefined;
			var results = [];

			function applyMethod(index) {
				var rsplaceholder = rsplaceholders[index];

				if (!rsplaceholder) {
					console.warn('$.rsplaceholder not instantiated yet');
					console.info(this);
					results.push(undefined);
					return;
				}

				if (typeof rsplaceholder[method] === 'function') {
					var result = rsplaceholder[method].apply(rsplaceholder, args);
					results.push(result);
				} else {
					console.warn('Method \'' + method + '\' not defined in $.rsplaceholder');
				}
			}

			this.each(applyMethod);

			return (results.length > 1) ? results : results[0];
		} else {
			var options = (typeof methodOrOptions === 'object') ? methodOrOptions : undefined;

			function init() {
				var $el = $(this);
				var rsplaceholder = new RSPlaceholder($el, options);

				$el.data('rsplaceholder', rsplaceholder);
				rsplaceholder.addDropdown($el);
			}
			
			$('html').click(function (e) {
				if (!e.target.classList.contains('placeholders-input-append')) {
					$('.rsfp-dropdown-list').hide();
				}
			});

			return this.each(init);
		}

	};

})(jQuery);