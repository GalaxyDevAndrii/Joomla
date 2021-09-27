// google fonts
jQuery(document).ready(function($){
    
    // Open Row settings Modal
    $(document).on('click touchstart', '.btn-fonts', function(e){
        e.preventDefault();
        var btndata = $(this).data();
        var fontCheck = $('#typelist_site_dont_use_google_font').prop('checked');
        if(fontCheck) T4Admin.disGgFont('dis');
        //init google font modal
        var $bodyfontmodal = $('.t4-google-font-modal');
        if(!$bodyfontmodal.parents().is('.themeConfigModal')) $bodyfontmodal.appendTo('.themeConfigModal');
        $('body').addClass('t4-modal-open');
        var nameFont = $('#'+btndata.name).val();
        $bodyfontmodal.find('li[data-name="'+nameFont+'"]').addClass('font-active');
        $bodyfontmodal.show();
        $bodyfontmodal.data('input',btndata.name);
        $('.custom-font-form').find('.input-select').each(function(){
            $(this).val("").trigger('liszt:updated');
        });
        $('#t4-font-filter').val('');
        $('.t4-font-filter').hide();
    });
    $('body').on('change','#custom-css', function(e) {
        $(this).closest('.control-group.custom-css').find('.message.alert').remove();
    });
    $('body').on('change','#custom-font-url', function(e) {
        $(this).parents('.control-group.custom-font-url').find('.message.alert').remove();
    });
    $(document).on('click','li .t4-font-weight-popup', function(e) {
        e.stopPropagation();
    })
    $(document).on('click','li .jub-font-container', function(e) {
        e.stopPropagation();
        e.preventDefault();
        $('.t4-font-weight-popup').appendTo($(this));
        $('.jub-font-container.top').removeClass('top');
        $('.jub-font-container.left').removeClass('left');
        $('.jub-font-container.right').removeClass('right');
        var styles = $(this).closest('.jub-font').data('styles'),
        fontActive = false,
        nameFont = $(this).closest('.jub-font').data('name'),
        dataWeight = "",
        clsInputFont = "#"+$(this).closest('.t4-google-font-modal').data('input');
        if($(this).closest('.jub-font').hasClass('font-active')){
            fontActive = true;
            dataWeight = $(clsInputFont).data('loadweights');
        }
        if(typeof dataWeight == 'undefined') dataWeight = "";
        var options = {
            styles: styles,
            name: nameFont,
            fontActive: fontActive,
            clsInputFont: clsInputFont,
            dataWeight: dataWeight.split(',')
        }
        fontWeightRenderPopup($('.t4-font-weight-popup'),options);
        var offSet = $(this).find('.jub-font-styles').offset();
        var offSetfixed = $(this).closest('.t4-google-content').offset();

        if(offSet.top - offSetfixed.top > 360) {
            $(this).addClass('top');
        }
        if(offSet.left - offSetfixed.left < 500){
            $(this).addClass('right');
        }
        $(".t4-font-weight-popup").show();
        $('li.jub-font.font-focus').removeClass('font-focus');
        $(this).closest('li.jub-font').addClass('font-focus');

    });
    $('body').on('click','li.custom-font', function(e){
        var nameFont = $(this).data('name'),$fontType = 'custom',
            elemClass = this.className.split(" "),
            $classInputFont = $(this).closest('.t4-google-font-modal').data('input');
        $('#'+$classInputFont).val(nameFont);
        $('#'+$classInputFont).data('fontType',$fontType);
        $('#'+$classInputFont).data('loadweights','');
        $('#'+$classInputFont).trigger('change');
        $('.t4-font-weight-preview').html('');
        $('body').removeClass('t4-modal-open');
        $('.font-active').removeClass('font-active');
        $('.themeConfigModal').children().not('.t4-message-container').hide();
    });
    $('#t4-font-filter').on('keyup',function() {
    	var value = $(this).val().toLowerCase();
    	var fontCount = 0;
    	var $filterKey,$filterView;
    	if(!$('#jub-google-content').is(":visible")){
    		$filterKey = 'li.custom-google-font';
            $filterView = $('#custom-google-content');
    	}else{
    		$filterKey = 'li.jub-google-font';
            $filterView = $('#jub-google-content');
    	}
	    $($filterKey).not('#custom-local').filter(function() {
	      	$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
	      	if($(this).text().toLowerCase().indexOf(value) > -1){
	      		fontCount++;
	      	}
	    });
	    $('.t4-font-filter').show();
	    if(!value) $('.t4-font-filter').hide();
	    if(!fontCount){
	    	$filterView.find('.t4-font-filter').get(0).innerHTML = '<span class="alert alert-success">no result</span>';

	    }else{
	    	$filterView.find('.t4-font-filter').get(0).innerHTML = '<span class="alert alert-success">you have '+fontCount+' result</span>';
	    }
        $('#custom-local').hide();
    });
    // export
    $('.btn-action[data-action="font.addfont"]').on('click', function() {
        $('.add-more-custom-font .btn-action.active').removeClass('active');
        $(this).addClass('active');
        // show addon form
        $('.custom-font-form').show();
        $('#custom-font-input').val("");
        $('.custom-font-url').show();
        $('.add-font-name').show();
        $('.custom-css').hide();

    });
    // export
    $('.btn-action[data-action="font.addcss"]').on('click', function() {
        $('.add-more-custom-font .btn-action.active').removeClass('active');
        $(this).addClass('active');
        // show addon form
        $('.custom-font-form').show();
        $('#custom-font-input').val("");
        $('.custom-font-url').hide();
        $('.add-font-name').hide();
        $('.custom-css').show();
    });

    $('.btn-action[data-action="fonts.save"]').on('click', function() {
        var type = $(this).data('type');
        doSave(type);
    })
    $('body').on('click', '.btn-action[data-action="fonts.remove"]', function(e) {
        e.preventDefault();e.stopPropagation();
        doRemove(this);
    })

    var doSave = function (typefont) {
        var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=customFont&task=add&id=' + tempId;
        var typeActive = $('.add-more-custom-font').find('.btn-action.active').data('type');
        var fonts = {
            type: typeActive
        };
        if (!fonts.type) {
            var mesg = $('<div class="message alert" />').append(T4Admin.langs.addonEmptyFieldTypeWaring);
            if(!$('.control-group.custom-font-type').find('.message.alert').length){
                $('.control-group.custom-font-type').append(mesg);
            }
            return;
        }
        if(fonts.type == 'css'){
            var $css = $('.'+typefont+' #custom-css').val().trim();
            if ($css) {
                fonts.css = $css.split('\n');

            }
            if (!fonts.css) {
                var mesg = $('<div class="message alert" />').append(T4Admin.langs.fontsEmptyFieldCssWaring);
                if(!$('.control-group.custom-css').find('.message.alert').length){
                    $('.control-group.custom-css').append(mesg);
                }

                return;
            }
        }else{
            var font = $('.'+typefont+' #custom-font-url').val().trim();
            if (font) {
                fonts.font = font.split('\n');
            }
            if (!fonts.font) {
                var mesg = $('<div class="message alert" />').append(T4Admin.langs.fontEmptyFieldFontFileWaring);
                if(!$('.control-group.custom-font-url').find('.message.alert').length){
                    $('.control-group.custom-font-url').append(mesg);
                }
                return;
            }
        }
        $.post(url, {fonts:fonts}).then(function(response) {
            if (response.ok && response.fonts) {
                // hide form
                if(typeof response.fonts.length != 'undefined' && response.fonts.length != 0){
                    var $font = response.fonts;
                    for (var i = 0; i < $font.length; i++) {
                        var $li = $('.'+typefont+'#custom-local').clone();
                        $li.data('name',$font[i]['name']);
                        $li.removeAttr('id').removeClass('hide');
                        $li.find('.custom-font-container').attr('title',$font[i]['name']);
                        $li.find('.custom-font-container').data('type',$font[i]['type']);
                        $li.find('.custom-font-name').html($font[i]['name']);
                        $li.insertBefore($('.'+typefont+'#custom-local')).show();
                    }
                }else{

                    // add new addon into list
                    var $li = $('.'+typefont+'#custom-local').clone();
                    $li.removeAttr('id').removeClass('hide');
                    $li.find('.custom-font-container').attr('title') = response.fonts.name;
                    $li.find('.custom-font-container').data('type',response.fonts.type);
                    $li.find('.custom-font-name').html(response.fonts.name);
                    $li.insertBefore($('.'+typefont+' #custom-local')).show();
                }
                $('.custom-font-input').val('');
                T4Admin.Messages(T4Admin.langs.T4fontCustomAdded,'message');
            } else {
                alert(response.error);
            }
        })
    }
    var doRemove = function (btn) {
        var $btn = $(btn),
            $fontName = $btn.closest('li').find('.custom-font-name').text();
        if (!$fontName) return;
        T4Admin.Confirm(T4Admin.langs.T4fontCustomRemoveConfirm, function(ans){
          if (ans) {
            var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=customFont&task=remove&id=' + tempId;
            $.post(url, {name: $fontName}).then(function(response) {
                if (response.ok) {
                    // remove addon
                    $btn.closest('li').remove();
                    T4Admin.Messages(T4Admin.langs.T4fontCustomRemoved,'message');
                } else {
                    alert(response.error);
                }
            })
          }else {
             return false;
          }
        },'');
    }
    $.fn.fontWeight_NumbertoText = function ($number) {
        var text = '';
        switch ($number) {
            case '100':
                text = 'Thin';
                break;
            case '100italic':
            case '100i':
                text = 'Thin italic';
                break;
            case '200':
                text = 'Extra-Light';
                break;
            case '200italic':
            case '200i':
                text = 'Extra-Light italic';
                break;
            case '300':
                text = 'Light';
                break;
            case '300italic':
            case '300i':
                text = 'Light italic';
                break;
            case '400':
            case 'regular':
                text = 'Regular';
                break;
            case 'italic':
            case '400italic':
            case '400i':
                text = 'Regular italic';
                break;
            case '500':
                text = 'Medium';
                break;
            case '500italic':
            case '500i':
                text = 'Medium italic';
                break;
            case '600':
                text = 'Semi-Bold';
                break;
            case '600italic':
            case '600i':
                text = 'Semi-Bold italic';
                break;
            case '700':
                text = 'Bold';
                break;
            case '700italic':
            case '700i':
                text = 'Bold italic';
                break;
            case '800':
                text = 'Extra-Bold';
                break;
            case '800italic':
            case '800i':
                text = 'Extra-Bold italic';
                break;
            case '900':
                text = 'Ultra-Bold';
                break;
            case '900italic':
            case '900i':
                text = 'Ultra-Bold italic';
                break;
            default:
                text = $number;
                break;
        }
        return text;
    }
    $.fn.fontWeight_TexttoNumber = function ($text) {
        var number = '';
        switch ($text) {
            case 'Thin':
                number = 100;
                break;
            case '100italic':
            case '100 italic':
            case 'Thin italic':
                number = '100i';
                break;
            case 'Extra-Light':
                number = 200;
                break;
            case '200italic':
            case '200 italic':
            case 'Extra-Light italic':
                number = '200i';
                break;
            case 'Light':
                number = 300;
                break;
            case '300italic':
            case '300 italic':
            case 'Light italic':
                number = '300i';
                break;
            case 'Regular':
            case 'regular':
                number = 400;
                break;
            case '400 italic':
            case 'italic':
            case 'Regular italic':
                number = '400i';
                break;
            case 'Medium':
                number = 500;
                break;
            case '500italic':
            case 'Medium italic':
                number = '500i';
                break;
            case 'Semi-Bold':
                number = 600;
                break;
            case '600italic':
            case 'Semi-Bold italic':
                number = '600i';
                break;
            case 'Bold':
                number = 700;
                break;
            case '700italic':
            case 'Bold italic':
                number = '700i';
                break;
            case 'Extra-Bold':
                number = 800;
                break;
            case '800italic':
            case 'Extra-Bold italic':
                number = '800i';
                break;
            case 'Ultra-Bold':
                number = 900;
                break;
            case '900italic':
            case 'Ultra-Bold italic':
                number = '900i';
                break;

            default:
                number = $text;
                break;
        }
        return number;
    }
    var update_fontWeight = function($options) {
        if(!$options.fontweightopt) return;
        var fontName = $options.fontname, $fontWeight_options = $('#attrib-themeConfig select.'+$options.fontweightopt ),current_fontWeight = [];
        $fontWeight_options.find('option').each(function(){
            if($(this).val()) current_fontWeight.push($(this).val());
        });
        var all_fonts = site_all_fonts;
        var fonts = all_fonts.filter(function(font){
            if(font.name == fontName){
                return font;
            }
        })[0];
        var optWeight = $options.styles;
        if(typeof fonts == 'undefined') fonts = {};
        if(!fonts.hasOwnProperty('styles')) fonts.styles = '';
        if(!optWeight){
            optWeight = fonts.styles;
        }else{
            optWeight = optWeight.split(',');
        }
        var fontWeight = optWeight;
        var remove_w = [];
        // merge with current fonts
        var removed_fontsWeight = $(current_fontWeight).not(fontWeight).get(),
            added_fontnames = $(fontWeight).not(current_fontWeight).get();
        removed_fontsWeight.forEach(function(w){
            return remove_w.push($.fn.fontWeight_TexttoNumber(w));
        });
        // remove fonts weigth
        if (removed_fontsWeight.length)
            $fontWeight_options.find('option').filter(function(){
                return $.inArray(this.value, remove_w) > -1 ? true : false}).remove();

        if (added_fontnames.length) {
            for (var i = 0; i < added_fontnames.length; i++) {
                $('<option>', {value: $.fn.fontWeight_TexttoNumber(added_fontnames[i].toString()), text: $.fn.fontWeight_NumbertoText(added_fontnames[i].toString())}).appendTo ($fontWeight_options);
            }
        }

        // update selected for first time
        if (!current_fontWeight.length) {
            $fontWeight_options.each (function() {
                var $elem = $(this);
                $elem.val ($elem.data('value'));

            })
        }
        if(remove_w.length){
            if(remove_w.findIndex(function(w) {if(w == $fontWeight_options.data('value')) return w;}) > -1){
                $fontWeight_options.val ('400');
                $fontWeight_options.trigger('change');
            }else{
                $fontWeight_options.val ($fontWeight_options.data('value'));
            }
        }
        current_fontWeight = fontWeight;

        // update for chosen select
        $fontWeight_options.trigger('liszt:updated');
        $fontWeight_options.trigger('chosen:updated');
    }
    $(document).on('change','.google-font-input', function(e) {
        var $styles = $(this).data('loadweights');
        if(typeof $styles == 'undefined') $styles = "";
        var options = {
            styles:$styles,
            fontname: $(this).val(),
            fontweightopt: $(this).data('classfontweight'),

        }
        update_fontWeight(options);
    });
    var fontWeightRenderPopup = function($selector,$data){
        var $div = "";
        //convert data to array
        var weightArr = $data.styles.split(","),dataW = $data.dataWeight;
        if(weightArr.length){
            if(dataW && typeof dataW == 'string') dataW = JSON.parse(dataW);
            $div += "<ul>";
            weightArr.forEach(function(w){
                w = $.fn.fontWeight_NumbertoText(w);
                w_number = $.fn.fontWeight_TexttoNumber(w);
                var $checked = "";
                if($data.fontActive && dataW.includes(w_number.toString())){
                    $checked = 'checked=""';
                }
                $div += "<li class='t4-form-checkbox'>";
                $div += "<label class='checkbox-label'>";
                $div += '<input type="checkbox" class="form-check-input" value="" data-value="'+w_number+'" '+$checked+'>'+w;
                $div += "</label></li>";
            });
            $div += "</ul>";
        }
        if($div){
            $div += '<div class="btn-actions"><button  type="button" data-action="btn.cancel" class="t4-btn btn-action">Cancel</button>';
            $div += '<button type="button" data-action="btn.selected" data-name_font="'+$data.name+'" data-input_font="'+$data.clsInputFont+'" class="t4-btn btn-action btn-primary">Select font</button></div>';
        }
        $selector.html($div);

    }
    $(document).on('click','.t4-btn[data-action="btn.selected"]', function(e) {
        var dataWeight = [],nameFont = $(this).data('name_font'),class_input_font = $(this).data('input_font');
        $('.t4-form-checkbox').find('.form-check-input').each(function(){
            if($(this).prop('checked')){
                dataWeight.push($(this).data('value').toString());
            }
        });
        $(class_input_font).val(nameFont);
        $(class_input_font).data('fonttype','google');
        $(class_input_font).data('loadweights',dataWeight.join(","));
        var parentELfont = $(class_input_font).closest('.control-group');
        if(dataWeight.length){
            if(parentELfont.find('.t4-font-weight-preview').length){
                parentELfont.find('.t4-font-weight-preview').html("<span class='font-weight'>Font weight: <small>"+dataWeight.join("</small><small>")+"</small></span>");
            }else{
                parentELfont.append($("<div class='t4-font-weight-preview control-helper' data-fontname='"+nameFont+"' data-loadweights='"+dataWeight.join(",")+"' />").append("<span class='font-weight'>Font weight: <small>"+dataWeight.join("</small><small>")+"</small></span>"));
            }
        } else {
            parentELfont.find('.t4-font-weight-preview').html("");
        }
        parentELfont.find('.t4-font-weight-preview').data('fontname',nameFont);
        parentELfont.find('.t4-font-weight-preview').data('loadweights',JSON.stringify(dataWeight));
        $(class_input_font).trigger('change');
        $('body').removeClass('t4-modal-open');
        $('.font-active').removeClass('font-active');
         $(".t4-font-weight-popup").hide();
        $('.themeConfigModal').children().not('.t4-message-container').hide();

    });
    $(document).on('click','.t4-btn[data-action="btn.cancel"]', function(e) {
        $(".t4-font-weight-popup").hide();
    });
    $(document).on('click', '.t4-fonts-manager', function(e) {
         $(".t4-font-weight-popup").hide();
         $('li.jub-font.font-focus').removeClass('font-focus');
    });
});
