(function($){
    "use strict";
    $(document).ready(function() {
        var initColor = function(els){
            // Show edit popup with current palettes
            var $plmodal = $('.t4-palettes-modal');
            if (!$plmodal.parent().is('.themeConfigModal')) $plmodal.appendTo($('.themeConfigModal'));
            var $parentColor = "";
            if($(els).data('action') =='edit'){
                $plmodal.data('action','edit');
                $parentColor = $(els).parents('.pattern');
               
            }else{
                $plmodal.data('action','create');
                $parentColor = $(els).parents('.add-more-palettes').find('.pattern');
            }
            $('.pattern.active').removeClass('active');
            if(!$parentColor.length) return;
             $parentColor.addClass('active');   
             var $data = $parentColor.data(),$data_color = {};
            $plmodal.find('.config_pattern').find('.t4-pattern').each(function(){
                $(this).parents('.controls').find('li').removeClass('active');
                var data = $(this).data();
                var attrName = $(this).data('attrname'),$value = "";
                if(attrName != 'title'){
                    if($data[attrName].search('#') != -1){
                        $data_color[attrName] = $data[attrName];
                        $value = $data[attrName];
                    }
                    $(this).val($value);
                    $(this).spectrum('set',$value);

                }
                if(attrName == 'title') $(this).val($data[attrName]);
                $(this).data('val',$data[attrName]);
            });
            if(typeof $data_color == 'object'){
               Object.keys($data_color).forEach( function(cls, index) {
                    initPalettePreview(cls,$data_color);
                });
            }
            $plmodal.show();
        }
        var createPalette = function(data){
            let clone = data.clone();
            let dataColor = data.data();
            clone.removeClass('pattern-clone');
            clone.removeClass('hidden');
            $('.t4-palettes-modal').find('.config_pattern').find('input.t4-pattern').each(function(){
                // var $data = $(this).data(), $attrName = $data.attrname,$val = $(this).val(),$dataColor = '';
                var name = $(this).data('attrname'),val = $(this).val();
                if(name == 'title'){
                    if(clone.data('class') == ''){
                        var $classes = val.replace(/\s+/g,"_").toLowerCase();
                        clone.data('class',$classes);
                    }
                    clone.find('.pattern-title').text(val);
                }else{
                    clone.find('span.'+name).css({background:val});
                }
                clone.data(name,val);
            });
            clone.appendTo($('.pattern-list'));
            //update status action
            var action_del = clone.find('.pt-color-del');
            if(action_del.is(":hidden")){
                action_del.parents('li').removeClass('hidden');
            }
        }
        var generatedPalettes = function(trigger){
            var colorPattern = {},$pattern;
            if(trigger){
                $pattern = '.pattern';
            }else{
                $pattern = '.pattern.active';
            }
            $('.group_palette').find($pattern).not('.pattern-clone').each(function(index){
                var dataColorPt = $(this).data();
                colorPattern[dataColorPt.class] = dataColorPt;
            });
            return colorPattern;
        }
        var validationData = function() {
            if(!$('.t4-pattern.title').val()){
                $('.t4-palette-error').show();
                return false;
            }else{
                $('.t4-palette-error').hide();
                return true;
            }
        }
        var updateToJson = function(trigger){
            var AllData = generatedPalettes(true);
            $('#typelist_theme_styles_palettes').val(JSON.stringify(AllData));
            $('#typelist_theme_styles_palettes').trigger('change');
            if(trigger){
                doSave();
            }
        }
        var doSave = function(trigger){
            var dataSave = generatedPalettes(trigger);
            var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Palettes&id=' + tempId;
            $.post(url, {task: 'save',all:trigger,value: dataSave}).then(function(response) {
                if (response.ok) {
                    T4Admin.Messages(T4Admin.langs.palettesUpdated,'message');
                } else {
                    T4Admin.Messages(response.error,'error');
                }
            });
        }
        var doRemove = function(el){
            var plEl = $(el).closest('.pattern'),plName = plEl.data('class'),plDel = $(this).parents('li');
            var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Palettes&id=' + tempId;
            $.post(url, {task: 'remove',name: plName}).then(function(response) {
                if (response.ok) {
                    var resData = response.data;
                    var resDataColor = response.datacolor;
                    if(!resData){
                        plEl.remove();
                    }else{
                        var plObj = plEl.find('li');
                        plObj.each(function(){
                            var elClass = $(this).find('span').attr('class');
                            if(elClass){
                                plEl.data(elClass,resDataColor[elClass]);
                                if(!resDataColor[elClass]) resDataColor[elClass] = '';
                                $(el).find('span').css({'background':resDataColor[elClass]})
                            }
                        });
                        plDel.addClass('hidden');
                    }
                    T4Admin.Messages(T4Admin.langs[$message],'message');
                } else {
                    T4Admin.Messages(response.error,'error');
                }
            })
        }
        var initPalettePreview = function(name,data){
        	if(name == 'background_color'){
	             $('.pattern-preview').css({background:data.background_color});
	         }else{

	            $('.pattern-preview .'+name).data(name,data[name]);
	            $('.pattern-preview .'+name).css({color:data[name]});
	            if(name.match(/_hover/)){
	                $('.pattern-preview .'+name.replace(/_hover/,"")).data(name,data[name]);
	                $('.pattern-preview .'+name.replace(/_hover/,"")).hover(function(){
	                	var colorArr = $(this).data();
	                    $(this).css("color", colorArr[name]);
	                }, function(){
	                		var colorArr = $(this).data();
	                    $(this).css("color", colorArr[name.replace(/_hover/,"")]);
	                });
	            }

	         }
        }
	    $(document).on('click','.pt-color-edit', function(e) {
            e.preventDefault();
            // Show edit popup with current palettes
            var $plmodal = $('.t4-palettes-modal');
            if (!$plmodal.parent().is('.themeConfigModal')) $plmodal.appendTo($('.themeConfigModal'));
            $plmodal.data('action','edit');
            //init user color to chose color
           $('.pattern.active').removeClass('active');
           var $parentColor = $(this).closest('.pattern');
           console.log($parentColor)
             $parentColor.addClass('active');   
             var $data = $parentColor.data(),$data_color = {};
            $plmodal.find('.config_pattern').find('.t4-pattern').each(function(){
                $(this).parents('.controls').find('li').removeClass('active');
                var data = $(this).data();
                var attrName = $(this).data('attrname'),$value = "";
                if(attrName != 'title'){
                    if($data[attrName].search('#') != -1){
                        $data_color[attrName] = $data[attrName];
                        $value = $data[attrName];
                    }
                    $(this).val($value);
                    $(this).spectrum('set',$value);

                }
                if(attrName == 'title') $(this).val($data[attrName]);
                $(this).data('val',$data[attrName]);
            });
            if(typeof $data_color == 'object'){
               Object.keys($data_color).forEach( function(cls, index) {
                    initPalettePreview(cls,$data_color);
                });
            }

            $plmodal.show();
        });
        $(document).on('change','.t4-pattern', function(e) {
            var $data = $(this).data(),attrName = $data.attrname,$val = $data.val,$color = $(this).val();
            var data_color = {}
            data_color[attrName] = $color;
            initPalettePreview(attrName,data_color);
        });

    	$(document).on('click','.pt-color-cancel', function(e) {
    	    e.preventDefault();
            $('.pattern.active').removeClass('active');
    	    $('.config_pattern').slideUp('slow');
    	});
    	$(document).on('click','.pt-color-create', function(e) {
    	    e.preventDefault();
            //init user color to chose color
            initColor(this);
            
    	});
        $(document).on('click','.pt-color-clone', function(e) {
            e.preventDefault();
            var $parentColor = $(this).closest('.pattern'), $patternClone = $parentColor.clone(true), $data = $patternClone.data();
            var $val = $(this).val(), $name_palette = [];
            $('.pattern-list').find('.pattern').each(function(){
                $name_palette.push($(this).data('title'));
            });
            $patternClone.removeClass($data.class);
            var random = '', newTitle = '', title = $data.title;
            for (var i = 0; i < 100; i++) {
                random = ' copy '+i;
                if(i==0) random = ' copy';
               newTitle = title + random;
               if($name_palette.indexOf(newTitle) == -1){
                break;
               }
            }
            $patternClone.addClass($data.class+random.replace(/\s/g,'_'));
            $patternClone.addClass('clone');
            $patternClone.data('class','');
            $patternClone.data('status','loc');
            $patternClone.find('.pattern-title').text(newTitle);
            $patternClone.data('title',newTitle);
            //update status action
            var action_del = $patternClone.find('.pt-color-del');
            if(action_del.is(":hidden")){
                action_del.parents('li').removeClass('hidden');
            }
            action_del.parents('li').html('<a class="pt-color-del" href="#" data-tooltip="Delete"><i class="fal fa-trash-alt fa-fw"></i></a>');
            $patternClone.insertAfter($parentColor);
            $patternClone.find('.pt-color-edit').trigger('click');
        });
    	$('body').on('click','.t4-patterns-apply', function(e) {
    	    e.preventDefault();
            if(!validationData()){
                return false;
            }
            var savetofile = false;
            var $plmodal = $('.t4-palettes-modal');
            if($plmodal.data('action') == 'create'){
                let clone = $('.pattern.active').clone();
                let dataColor = $('.pattern.active').data();
                clone.removeClass('pattern-clone');
                clone.removeClass('hidden');
                $('.pattern.active').removeClass('active');
                clone.appendTo($('.pattern-list'));
                clone.addClass('active');
                clone.data('class','');
                //update status action
                var action_del = clone.find('.pt-color-del');
                if(action_del.is(":hidden")){
                    action_del.parents('li').removeClass('hidden');
                }
                savetofile = true;
            }
            var patternActive = $('.pattern.active');
            if(patternActive.hasClass('clone')){
                patternActive.removeClass('clone');
                savetofile = true;
            }

            $plmodal.find('.config_pattern').find('input.t4-pattern').each(function(){
                // var $data = $(this).data(), $attrName = $data.attrname,$val = $(this).val(),$dataColor = '';
                var name = $(this).data('attrname'), val = $(this).val();

                if(name == 'title'){
                    if(patternActive.data('class') == ''){
                        var $classes = val.replace(/\s+/g,"_").toLowerCase();
                        patternActive.data('class',$classes);
                    }
                    patternActive.find('.pattern-title').text(val);
                }else{
                    patternActive.find('span.'+name).css({background:val});
                }
                patternActive.data(name,val);
            });
            $(this).closest('.t4-palettes-modal').hide();
            updateToJson(savetofile);
    	});
        $('body').on('click','.t4-patterns-cancel', function(e) {
            $(this).closest('.t4-palettes-modal').hide();
            var $pattern = $('.pattern.active'),dataClass = $pattern.data('class');
            if($pattern.hasClass('clone')){
                $pattern.remove();
                $(document).find('.pattern[data-class="'+dataClass+'"]').addClass('active');
            }
            // $('.t4-row-settings').show();
        });
        $(document).on('click','.pattern-actions-list', function(e) {
             e.stopPropagation();
        });
        // $(document).find('.config_pattern').find('input.t4-pattern').on('change',function(){
        //     let name = $(this).data('attrname'),val = $(this).val();
        //     $('.pattern.active').data(name,val);
        // });
		$('body').on('click','.pt-color-del', function(e) {
            e.stopPropagation();
    	    e.preventDefault();
            var action = $(this).data('action');
            var $langs = 'colorPalettesConfirm'+$(this).data('tooltip');
            var $message = 'colorPalettes'+$(this).data('tooltip');
            var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Palettes&id=' + tempId;
            var plEl = $(this).closest('.pattern'),plName = plEl.data('class'),plDel = $(this).parents('li');

            if(plEl.hasClass('clone')){
                plEl.remove();
                T4Admin.Messages(T4Admin.langs.palettesRemnoveClone,'message');
                return true;
            }
            T4Admin.Confirm(T4Admin.langs[$langs],function(ans){
              if (ans) {
                $.post(url, {task: 'remove',name: plName}).then(function(response) {
                    if (response.ok) {
                        var resData = response.data;
                        var resDataColor = response.datacolor;
                        if(!resData){
                            plEl.remove();
                        }else{
                            var plObj = plEl.find('li');
                            plObj.each(function(){
                                var elClass = $(this).find('span').attr('class');
                                if(elClass){
                                    plEl.data(elClass,resData[elClass]);
                                    if(!resDataColor[elClass]) resDataColor[elClass] = '';
                                    $(this).find('span').css({'background':resDataColor[elClass]})
                                }
                            });
                            plDel.addClass('hidden');
                        }
                        updateToJson();
                        T4Admin.Messages(T4Admin.langs[$message],'message');
                    } else {
                        T4Admin.Messages(response.error,'error');
                    }
                })
              }else {
                 return false;
              }
            },'');
            
    	});
        $('body').on('click','.t4-pattern, .fa-angle-down', function(e) {
            $('.choose-color-pattern').addClass('hidden');
            $(this).closest('.controls').find('.choose-color-pattern').removeClass('hidden');
        });
        $('body').on('click','.t4-input-color', function(e) {
            $('.choose-color-pattern').addClass('hidden').removeClass('is-focus');
            //init user color to chose color
            var $colorActive = $(this).data('val');
            $(this).closest('.controls').find('.choose-color-pattern').find('.t4-select-pattern[data-val="'+$colorActive.replace(/\s+/g,'_')+'"]').addClass('active');
            $(this).closest('.controls').find('.choose-color-pattern').removeClass('hidden').addClass('is-focus');
        });
         $(document).on('click',function(e){
            if(!$('.t4-pattern-row').is(":hidden")){
                if(($('.fa-angle-down').index(e.target) == -1) && ($('.t4-pattern').index(e.target) == -1) && ($('.choose-color-pattern').index(e.target) == -1)){
                    $('.choose-color-pattern').addClass('hidden');
                }
            }else{
                if($('.choose-color-pattern').hasClass("is-focus")){
                    if(($('.t4-input-color').index(e.target) == -1) && ($('.choose-color-pattern').index(e.target) == -1)){
                        $('.choose-color-pattern').addClass('hidden').removeClass('is-focus');
                    }
                }
            }
        });

        $('body').on('click','.t4-select-pattern', function(e) {
            var $val = $(this).data('val'),$input = $(this).closest('.controls').find('input.t4-pattern');
            if($input.length == 0) $input = $(this).closest('.controls').find('input.t4-input-color');
            $(this).closest('.controls').find('li').removeClass('active');
            $(this).addClass('active');
            var $value = $val.replace(/_/g," ");
            var dataName = $(this).data('name');
            $input.val(dataName);
            $input.data('val',$val);
            $input.prev().css({'background-color':$(this).data('color')});
            $input.data('color',$(this).data('color'));
            $input.trigger('change');
            $(this).closest('.controls').find('.choose-color-pattern').addClass('hidden');
         });
        

        //trigger update palettes color to JSON
        $(document).on('palettestoJson', updateToJson);
	});

})(jQuery);
