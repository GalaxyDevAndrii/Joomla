(function($) {
	var Typelists = window.Typelists || {};

	var Site = function($el){
		Site.$container = $el;
	}

	Site.prototype.init = function () {
	}

	Site.prototype.get = function () {
		// find all fields and get value
		var $inputs = Site.$container.find('[name^="typelist-site["]');
		var result = {};
		$inputs.each(function(i, el){
			var $input = $(el),
			name = el.name.match(/typelist-site\[(.*)\]/)[1],
			value = $input.val();
			if($input.hasClass('google-font-input')){
				var $dataWeight = $input.data('loadweights'),$typelistName = name.replace("_family","");
				if(typeof $dataWeight == 'undefined') $dataWeight = '';
				result[$typelistName+'_load_weights'] = $dataWeight;
			}
			if ($input.hasClass('t4-custom-color-spec') && !value) {
				value = "inherit";
			}
			if(name && name.match(/_font_weight/)){
				if(value && value.match(/i/)){
					value = value.replace('i','');
					result[name.replace('_weight','_style')] = 'italic';
				}else if(value && !value.match(/i/)){
					result[name.replace('_weight','_style')] = 'normal';
				}else{
					result[name.replace('_weight','_style')] = 'inherit';
				}
			}
			if($input.attr('type') == 'checkbox'){
				value = $input.prop('checked');
			}
			result[name] = value;

		})
		return result;
	}

	Site.prototype.set = function (value) {
		var $inputs = Site.$container.find('[name^="typelist-site["]');

		$inputs.each( function(i, el) {
			var $input = $(el), name = el.name.match(/typelist-site\[(.*)\]/)[1];

			if (!value[name]) value[name] = '';
			if(name && name.match(/_font_weight/)){
				if(value[name.replace('_weight','_style')] == 'italic'){
					value[name] = value[name]+'i';
				}
				$input.val(value[name]);
			 	$input.trigger('change');
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
			if($input.hasClass('google-font-input')){
				var $typelistName = name.replace("_family","");
				if(typeof value[$typelistName+'_load_weights'] == 'undefined') value[$typelistName+'_load_weights'] = "";
				var $fontWeightEl = $("<div class='t4-font-weight-preview control-helper' data-fontname='"+value[name]+"' data-loadweights='"+JSON.stringify(value[$typelistName+'_load_weights'].split(','))+"' />");
				$fontWeightEl.append('<span class="font-weight">Font weight: <small>'+value[$typelistName+'_load_weights'].replace(/,/g,"</small><small>")+'</small></span>');
				if(value[$typelistName+'_load_weights']){
					if($input.closest('.control-group').find('.t4-font-weight-preview').length){
						$input.closest('.control-group').find('.t4-font-weight-preview').html($fontWeightEl);
					}else{
						$input.closest('.control-group').append($fontWeightEl);
					}
				}
				$input.data('loadweights',value[$typelistName+'_load_weights']);
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
	Site.prototype.updateFontWeight = function($selector,$value,$styles){

		var current_fontWeight = [];
        var all_fonts = site_all_fonts;
        var fonts = all_fonts.filter(function(font){
            if(font.name == $value){
                return font;
            }
        })[0];
        if(typeof fonts == 'undefined') fonts = {};
        if(!fonts.hasOwnProperty('styles')) fonts.styles = '';
        if(!$styles.length || $styles[0] == '') $styles = fonts.styles;
        var fontWeight = $styles;
        var remove_w = [];
        // merge with current fonts
        var removed_fontsWeight = $(current_fontWeight).not(fontWeight).get(),
            added_fontnames = $(fontWeight).not(current_fontWeight).get();
            removed_fontsWeight.forEach(function(w){
                return remove_w.push($.fn.fontWeight_TexttoNumber(w));
            });
        // remove fonts weigth
        if (removed_fontsWeight.length)
            $selector.find('option').filter(function(){
                return $.inArray(this.value, remove_w) > -1 ? true : false}).remove();

        if (added_fontnames.length) {
            for (var i = 0; i < added_fontnames.length; i++) {
                $('<option>', {value: $.fn.fontWeight_TexttoNumber(added_fontnames[i]), text: $.fn.fontWeight_NumbertoText(added_fontnames[i])}).appendTo ($selector);
            }
        }
        // update selected for first time
        if (!current_fontWeight.length) {
            $selector.each (function() {
                var $elem = $(this);
                $elem.val ($elem.data('value'));

            })
        }
        if(remove_w.length){
            if(remove_w.findIndex(function(w) {if(w == $selector.data('value')) return w;}) > -1){
                $selector.val ('400');
            }else{
                $selector.val ($selector.data('value'));
            }
            $selector.trigger('change');
        }
        current_fontWeight = fontWeight;

        // update for chosen select
        $selector.trigger('liszt:updated');
        $selector.trigger('chosen:updated');
	}
	
	Typelists['site'] = Site;
	window.Typelists = Typelists;
})(jQuery);
