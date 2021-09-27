(function($) {
	var Typelists = window.Typelists || {};

	var Theme = function($el){
		Theme.$container = $el;
	}

	Theme.prototype.init = function () {

	}

	Theme.prototype.get = function () {
		// find all fields and get value
		var $inputs = Theme.$container.find('[name^="typelist-theme["]');
		var result = {};
		$inputs.each(function(i, el){
			var $input = $(el),
			name = el.name.match(/typelist-theme\[(.*)\]/)[1],
			value = $input.val();
			if(name == 'custom_colors'){
				value = JSON.stringify(Theme.prototype.generatedUserColor());
			}
			if($input.hasClass('t4-input-color')){
				value = $input.data('val');
				value = value.replace(/_/g,' ');
			}
			if ($input.hasClass('t4-custom-color-spec') && !value) {
				value = "inherit";
			}
			if($input.attr('type') == 'checkbox'){
				value = $input.prop('checked');
			}
			result[name] = value;

		})
		return result;
	}

	Theme.prototype.set = function (value) {
		var $inputs = Theme.$container.find('[name^="typelist-theme["]');

		$inputs.each( function(i, el) {
			var $input = $(el), name = el.name.match(/typelist-theme\[(.*)\]/)[1];

			if (!value[name]) value[name] = '';
			if(name == 'styles_palettes' && value[name]){
				Theme.prototype.renderPalettes(JSON.parse(value[name]));
			}
			if(name && name.match(/custom_colors/)){
				Theme.prototype.updateUserColor(value[name]);
			}
			else if ($input.hasClass('t4-input-color')) {
				var valColor,valName,dataVal;
				if(value[name] != 'none'){
					$input.closest('.t4-select-color').find('li').each(function(){
						if($(this).data('val') == value[name].replace(/\s+/g,'_')){
							valColor = $(this).data('color');
							valName = $(this).data('name');
							dataVal = value[name];
						}
					});
				}else{
					dataVal = 'none';
					valName = 'none';
					valColor = 'inherit';
				}
				if(!valColor || typeof valColor == 'undefined'){
					dataVal = 'none';
					valName = 'none';
					valColor = 'inherit';
				}
				$input.val(valName);
				$input.data('val',dataVal);
				$input.closest('.color-preview').find('.preview-icon').css('background-color',valColor);
				$input.trigger('change');
			}else if($input.attr('type') == 'checkbox'){
				$input.val(value[name]).prop('checked',value[name]);
			}else if ($input.hasClass('t4-custom-color-spec')) {
				$input.val(value[name] ? value[name] : $input.val()).trigger('change');
			}
			else {
				$input.val(value[name] ? value[name] : '').trigger('change');
			}
			if($input.is('select')){
				$input.chosen({with:'100%',disable_search:true});
				$input.trigger('liszt:updated');
			}
			if($input.hasClass('t4-input-media')){
				if($input.closest('.field-media-wrapper').find('.add-on.field-media-preview').length){
						$input.closest('.field-media-wrapper').data('fieldMedia').setValue(value[name]);
				}
			}
		})
	}
	Theme.prototype.updateUserColor = function(value){
		if(value){
			var baseColor = JSON.parse($('#typelist_theme_custom_colors').val());
			var colors = JSON.parse(value);
			for (var nameColor in baseColor) {
				var colorData = colors[nameColor];
				if(!colorData) colorData = baseColor[nameColor];
				$('.custom-color-list').find("input[name='"+nameColor+"']").minicolors('value',colorData.color);
			}
		}else{
			return true;
		}
	}
	Theme.prototype.generatedUserColor = function(){
		var data = {};
		$('.custom-color-list').find('.control-group').each(function(){
			var colorData = $(this).data();
			delete colorData.sortableItem;
			data[colorData.class] = colorData;

		});
		return data;
	}
	Theme.prototype.renderPalettes = function($data){
		Object.keys($data).forEach( function($key) {
			var palette = $('.t4-theme-palettes').find('.pattern.'+$data[$key]['class']);
            for(var name in $data[$key]){
            	var val = $data[$key][name];
            	if(name == 'title'){
                    palette.find('.pattern-title').text(val);
                }else{
                    palette.find('span.'+name).css({background:val});
                }
                palette.data(name,val);
            }
        });
	}
	Theme.prototype.compatibleUsercolor = function (value) {
		let custom_colors = JSON.parse(value['custom_colors']);
		let compare = {};
		let i = 1;
		for (var nameColor in custom_colors) {
			var colorData = custom_colors[nameColor];
			compare[nameColor] = "user_color_"+i;
			value["user_color_"+i] = colorData.color;
			i++;
		}
		Object.keys(value).forEach(function(val){
			if(val.match(/_color$/)){
				let oldVal = value[val].replace(/\s/g,'_');
				if(compare[oldVal]){
					value[val] = compare[oldVal].replace(/_/g,' ');
				}
			}
		});
		return value;
	}
	Typelists['theme'] = Theme;
	window.Typelists = Typelists;
})(jQuery);
