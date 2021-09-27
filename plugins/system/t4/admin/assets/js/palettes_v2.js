(function($){
    "use strict";
     var initPalettes = function(){
        // Show edit popup with current palettes
        var $plmodal = $('.t4-palettes-modal');
        if (!$plmodal.parent().is('.themeConfigModal')) $plmodal.appendTo($('.themeConfigModal'));
        var $plEdit = $('.pattern.active'),$data = $plEdit.data(),$data_color = {};
        $plmodal.find('.config_pattern').find('.t4-pattern').each(function(){
            $(this).parents('.controls').find('li').removeClass('active');
            var data = $(this).data();
            var attrName = $(this).data('attrname'), $value = "";
            if(attrName != 'title'){
                $data_color[attrName] = $data[attrName];
                $value = $data[attrName];
                if(!$value) $value = "#FFF";
                $(this).val($value);
                $(this).spectrum("set",$value);
            }
            if(attrName == 'title') $(this).val($data[attrName]);
            $(this).data('val',$data[attrName]);
        });
        if(typeof $data_color == 'object'){
            Object.keys($data_color).forEach( function(cls, index) {
                initPalettePreview(cls,$data_color);
            });
        }
        $('.error-name-exist').hide();
        $('.error-title-null').hide();
        // modal show
        $plmodal.show();
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
        var allPalette = generatedPalettes(true);

        var $return = true;
        if(!$('.t4-pattern.title').val()){
            $('.error-title-null').show();
            $return = false;
        }else{
            $('.error-title-null').hide();
            var title = $('.t4-pattern.title').val();
            var $cls = title.replace(/\s+/g,"_").toLowerCase();
            Object.keys(allPalette).forEach(function(cls, idx){
                if (cls == $cls || allPalette[cls]['title'] == title) {
                    $('.error-name-exist').show();
                    $return = false;
                }
            });
        }
        return $return;
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
    var doRemove = function(el){
        var $message = 'colorPalettes'+$(el).data('tooltip'), action = $(el).data('action'), plEl = $(el).closest('.pattern'),plName = plEl.data('class'),plDel = $(el).parents('li');
        var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Palettes&id=' + tempId;
        $.post(url, {task: 'remove',name: plName}).then(function(response) {
            if (response.ok) {
                var resData = response.data;
                var resDataColor = response.datacolor;
                if(action == 'remove'){
                    plEl.remove();
                }else{
                    console.log(plEl)
                    var plObj = plEl.find('li');
                    plObj.each(function(){
                        var elClass = $(this).find('span').attr('class');
                        if(elClass){
                            plEl.data(elClass,resDataColor[elClass]);
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
    }
    $(document).ready(function() {
       
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
	    $(document).on('click','.pt-color-edit', function(e) {
            e.preventDefault();
            var paletteEdit = $(this).closest('.pattern');
            $('.pattern.active').removeClass('active');
            paletteEdit.addClass('active');
            $('.t4-palettes-modal').data('action','edit');
            initPalettes();
        });
        $(document).on('change','.t4-pattern', function(e) {
            var $data = $(this).data(),attrName = $data.attrname,$val = $data.val,$color = $(this).val();
            var data_color = {}
            data_color[attrName] = $color;
            initPalettePreview(attrName,data_color);
        });

    	$(document).on('click','.pt-color-create', function(e) {

    	    e.preventDefault();
            var palettes = $('.pattern-list.t4-theme-palettes');
            //init user color to chose color
            var $parentColor = $(this).parents('.add-more-palettes').find('.pattern').clone(true);
            //update status action
            var action_del = $parentColor.find('.pt-color-del');
            if(action_del.is(":hidden")){
                action_del.parents('li').removeClass('hidden');
            }
            action_del.parents('li').html('<a class="pt-color-del" href="#" data-tooltip="Delete" data-action="remove"><i class="fal fa-trash-alt fa-fw"></i></a>');
            $('.pattern.active').removeClass('active');
            $parentColor.addClass('hidden active clone');
            $parentColor.data('class','');
            $parentColor.appendTo(palettes);
            $('.t4-palettes-modal').data('action','create');
            initPalettes();
            
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
            action_del.parents('li').html('<a class="pt-color-del" href="#" data-tooltip="Delete"  data-action="remove"><i class="fal fa-trash-alt fa-fw"></i></a>');
            $patternClone.insertAfter($parentColor);
            $patternClone.find('.pt-color-edit').trigger('click');
            $('.t4-palettes-modal').data('action','clone');
        });
    	$('body').on('click','.t4-patterns-apply', function(e) {
    	    e.preventDefault();
            var $plmodal = $('.t4-palettes-modal');

            if(!validationData() && $plmodal.data('action') == 'create'){
                return false;
            }
            var savetofile = false;
            var patternActive = $('.pattern.active');
            if(patternActive.hasClass('clone')){
                patternActive.removeClass('clone');
                savetofile = true;
            }
            console.log($plmodal.data('action'))
            if(patternActive.hasClass('hidden')){
                patternActive.removeClass('hidden');
                patternActive.removeClass('pattern-clone');
            }
            var status = patternActive.data('status');
            if(['ovr','org'].indexOf(status) > -1){
                patternActive.data('status','ovr');
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
        $('body').on('click','.t4-patterns-cancel, .action-t4-modal-close', function(e) {
            $(this).closest('.t4-palettes-modal').hide();
            var $pattern = $('.pattern.active'),dataClass = $pattern.data('class');
            $('.t4-palettes-modal').data('action','');
            if($pattern.hasClass('clone')){
                $pattern.remove();
                $(document).find('.pattern[data-class="'+dataClass+'"]').addClass('active');
            }
        });
		$('body').on('click','.pt-color-del', function(e) {
            e.stopPropagation();
    	    e.preventDefault();
            var that = this;
            var $langs = 'colorPalettesConfirm'+$(this).data('tooltip');
            T4Admin.Confirm(T4Admin.langs[$langs],function(ans){
              if (ans) {
                doRemove(that);
              }else {
                 return false;
              }
            },'');
    	});

        // dont trigger action when click on action group
        $(document).on('click','.pattern-actions-list', function(e) {
             e.stopPropagation();
        });
	});

})(jQuery);
