jQuery(function($){
    "use strict";
    // move from script.js
    var $paneAct = 'pane2';
    $('#attrib-themeConfig').addClass('themeConfig');
    $('#attrib-themeConfig').addClass('t4-pane');
    //append themeconfigModal to body
    $('body').append('<div class="themeConfigModal"></div>');
    var $T4message = $('<div />').addClass('t4-message-container');
    $('.themeConfigModal').prepend($T4message);
    $('body.admin').addClass('j'+jversion);
    var paneElement = '.t4-pane';
    T4Admin.initrenderForm();

    // group index for legend
    var $legends = $('div.legend'),
        group = 0;
    $legends.each (function () {

        var $legend = $(this),
            $legendGroup = $legend.closest('.control-group'),
            isSub = $legend.is('.sub-legend');
        // add legend class
        $legendGroup.addClass(isSub ? 'sub-legend-group' : 'top-legend-group');
        var $params = $legendGroup.nextUntil (function() {
                var $next = $(this),
                    $nextIsLegend = $next.has('div.legend').length,
                    $nextIsSubLegend = $nextIsLegend && $next.find('div.legend').is('.sub-legend');
                if (!isSub && $nextIsLegend && $nextIsSubLegend) {
                    $next.find('div.legend').data('top-legend', $legend);
                }

                return !$next.is('.control-group') || ($nextIsLegend && (isSub || !$nextIsSubLegend));
            });

        // store its legend
        $params.data('legend', $legend);
        $legend.data('params', $params);
    });

    // grouping legend and params
    $(paneElement).each(function(){
        var $pane = $(this),
            tempStyle = $('#jform_title').val(),
            $t4PreviewBar = $('<div class="t4-sidebar-preview" />'),
            $curStyle = '<div class="t4-current-style"><h1 class="temp_title">'+tempStyle+'</h1>'+allTempl+'</div>',
            $toolbarDevice = '<span class="reload-preview"><i class="fal fa-redo-alt"></i>Reload Preview</span><ul class="t4-toolbar toolbar-devices t4-responsive"><li class="active"><span class="default">All Devices</span></li><li ><span class="btn desktop" data-tooltip="Desktop"><i class="fal fa-desktop"></i></span></li><li><span class="btn tablet" data-tooltip="Tablet"><i class="fal fa-tablet"></i></span></li><li><span class="btn mobile" data-tooltip="Mobile"><i class="fal fa-mobile"></i></span></li></ul>',
            $toolbarAction = '<div class="t4-toolbar toolbar-save"><div class="t4-admin-save"><span class="t4-btn btn-md btn-success btn-save" type="button" data-tooltip="Save"><i class="fal fa-save"></i><span class="btn-text">Save</span></span><span class="t4-btn btn-md btn-success dropdown-toggle" type="button" data-toggle="dropdown"><span class="caret"></span>&nbsp;</span><ul class="dropdown-menu"><li id="t4-admin-btn-save-close"><a href="#">Save &amp; Close</a></li><li id="t4-admin-btn-save-clone"><a href="#">Save as Copy</a></li></ul></div><a href="'+t4_site_root_url+'" title="View site" class="t4-btn btn-md" target="_blank"><i class="fal fa-external-link"></i><span class="btn-text">View site</span></a><span class="t4-btn btn-md btn-icon btn-close" type="button" data-tooltip="Close" title="Close"><i class="fal fa-times"></i></span></div>',

            $t4Sidebar = $('<div />').addClass('t4-sidebar'),
            $t4Switch = $('<div class="t4-switch-theme"/>').append('<ul><li><label for="switch-style"><input id="switch-style" class="t4-input" type="checkbox" value="0" name="t4-switch" /><span>Dark Mode</span></label></li><li><a href="#"><img src="'+t4_site_root_url+'plugins/system/t4/admin/assets/images/t4-logo.png" /></a></li></ul>'),
            $ul = $('<ul />').addClass('t4-sidebar-nav').attr('data-level','1'),
            $t4views = $('<div />').addClass('t4-pn-views').appendTo($pane),
            $topLegends = $pane.find('.top-legend-group');
            $t4PreviewBar.append( $curStyle,$toolbarDevice,$toolbarAction);

            var $i = 1;
        // $t4header.appendTo($t4views);
        $ul.appendTo($t4views);
        $t4Switch.appendTo($t4views);
        $t4views.appendTo($t4Sidebar);
        $pane.append($t4PreviewBar);
        $pane.append($t4Sidebar);

        $topLegends.each(function(){
            var $this = $(this),
                $li = $('<li />').addClass('t4-sidebar-action nav-pane').attr('data-target','pane'+$i);
                $li.appendTo($ul);
                $li.append($this);
            var $legend = $this.find('div.legend'),
                $legend_title = $this.find('span.item-desc'),
                $params = $legend.data('params'),
                $subLegends = $params.filter('.sub-legend-group'),
                $t4PageHeader = $('<div class="t4-block-header" />').append('<h4>'+$legend_title.text()+'</h4>'),
                $topGroup = $('<div />');

            $topGroup.addClass('top-group t4-pn-views-container pane').appendTo($t4Sidebar);
            $topGroup.attr({'data-level':'2','data-name':'pane'+$i});
            var attrClass = $legend.attr('class'),t4Class = (attrClass.match(/(^|\s)t4-\S+/g) || []).join(' ');
            if(t4Class){
                $li.addClass(t4Class);
                $topGroup.addClass(t4Class);
            }
            $topGroup.append($t4PageHeader);
            var $subGroupDirect = $('<div />').addClass('sub-group sub-group-direct open').appendTo($topGroup).append(
                    $('<div />').addClass('sub-group-inner').append($params)
                );
            if(['t4-megamenu','t4-layout-builder','t4-advanced'].indexOf(t4Class.trim()) == -1){
                $params.each(function (){
                    var $ctrlLabel = $(this).find('.control-label'),
                        $param = $(this),
                        $label = $(this).find('label'),
                        $reset = $('<span />').addClass('t4-param-reset').append('<i class="fal fa-undo"></i>');
                    if($label.length){
                        $label.removeClass('hasPopover');
                        var $content = $label.data('content');
                        if($content != undefined){
                            $param.append($('<div />').addClass('control-helper').append($content));
                        }
                        $label.removeAttr('data-content');
                    }
                    $reset.hide();
                    /*if(!$(this).find('.t4-checkbox').length){
                        $ctrlLabel.append($reset);
                    }*/
                    if(t4Class.trim() == 't4-general'){
                        $ctrlLabel.append($reset);
                    }

                    var $getIcon = $param.children('.control-label').find('label').first().text().trim();
                    if($getIcon){
                        var $strIcon = String($getIcon.replace(/\s+/g,"-")).toLowerCase();
                        $param.addClass('t4-'+$strIcon);
                    }
                });

            }
            $subLegends.each(function() {
                var $subLegendGroup = $(this),
                    $subLegend = $subLegendGroup.find('.legend'),
                    $params = $subLegend.data('params'),
                    subLegendClass = $subLegend.attr('class'),groupClass = (subLegendClass.match(/(^|\s)group_\S+/g) || []).join(' '),
                    $font_param = groupClass ? groupClass : '';
                $('<div />').addClass('sub-group').appendTo($topGroup).append(
                    $('<div />').addClass('sub-group-inner').append($subLegendGroup).append(
                            $('<div />').addClass('sub-group-params').append($params)
                        )
                );
                var $checkBoxs = $params.find('div.t4-checkbox');
                $checkBoxs.each(function(){
                    var $checkbox = $(this).parents('.control-group'),
                    $groupClass = $(this).attr('class').split(" "),
                    $clasesCheckbox = $groupClass[$groupClass.length - 1],
                    $groupFind = ($groupClass.length && $groupClass[$groupClass.length - 1] != '') ? '.checkbox-group.'+$clasesCheckbox  : '';
                    $checkbox.addClass('t4-checkbox');
                    if($($groupFind).length){
                        var $paramsCheckBox = $($groupFind).parents('.control-group');
                        var $subCheckbox = $('<div />').addClass('sub-group-params-checkbox '+$clasesCheckbox );
                        // $params.append();
                        $subCheckbox.append($paramsCheckBox);
                        $subCheckbox.insertAfter($checkbox);

                    }

                });
            });

            // remove empty group
            if (!$subGroupDirect.find('.sub-group-inner').children().length) $subGroupDirect.remove();


            // Move group toggle to title
            var $toggle = $this.find('.t4-group-toggle');
            if ($toggle.length) {
                $toggle.appendTo($t4PageHeader) //.find('input').trigger('change');
            }


            // store for later use
            $(this).data('top-group', $topGroup);
            $i++;
        });

        //move title to Detail group
        var $t4Title = $('#style-form').find('div:first'),
            $titleTemplate = $t4Title.find('.control-group').addClass('t4-tpl-title'),
            $detailGroup = $('.t4-detail').find('.sub-group-inner:first');

            $t4Title.remove();
            $detailGroup.prepend($('#details .span3').find('.control-group').addClass('t4-tpl-default-style'));
            $detailGroup.prepend($titleTemplate);
            $('.tpl-preview').parents('.control-group').addClass('t4-tpl-detail');
            $('#jform_template-lbl').parents('.control-group').hide();
            //$('#details').appendTo($('#custom-style-preview'));
        $('#assignment').appendTo($('.t4-pn-views-container.t4-assignment'));
        $t4Sidebar.find('#assignment').addClass('t4-assignment').removeClass('tab-pane');
    });

    $('body').on('click', '.t4-sidebar .nav-pane', function(e) {
        var $a = $(this),
            $parentPane = $a.closest('.t4-sidebar-nav'),
            $sidepane = $parentPane.closest('.t4-sidebar'),
            target = $a.data('target'),
            $targetPane = $sidepane.find('[data-name="' + target + '"]'),
            level = $targetPane.data('level');
            $('.nav-pane').removeClass('active');
            $a.addClass('active');
        $sidepane.find('.pane[data-level="' + level + '"]').not($targetPane).css({visibility: 'hidden', 'z-index': 0});
        $targetPane.css({visibility: 'visible', 'z-index': 11});
        $sidepane.removeClass('level-' + (level - 1)).addClass('level-' + level).data('level', level);


        // store prev
        var prev = $sidepane.data('prev') || [];
        prev.push($parentPane);
        $sidepane.data('prev', prev);
        // adjust sidepane width
        $sidepane.width($targetPane.outerWidth()+$('.t4-pn-views').outerWidth());
        var t4sidebarWidth = $targetPane.outerWidth() + $('.t4-pn-views').outerWidth();
        if($a.hasClass('t4-assignment')){
            $('#custom-style-preview').addClass('hide');
            $sidepane.width('100%');
        }else{
            $('#custom-style-preview').removeClass('hide');
            $sidepane.width(t4sidebarWidth);
            var ourW = $sidepane.outerWidth()+4;var lefts = $sidepane.outerWidth() + 2;
            // $('#custom-style-preview').width("calc( 100% - "+ourW+"px)");
            if(document.dir == 'rtl'){
                $('#custom-style-preview').css({'right':lefts+'px'});
            }else{
                $('#custom-style-preview').css({'left':lefts+'px'});
            }
        }
        if (localStorage) {
            localStorage.setItem('last_active_group','[data-target="' + target + '"]');
            // trigger panel switch
            $(document).trigger('panel-group-switch', {target: $targetPane, originalEvent: e});
        }

        if(!$targetPane.find('.sub-group-open').length){
            $targetPane.find('.sub-group').first().find('.sub-legend').first().trigger('click');
        }


    });
    // last active
    var $lastActiveGroup,$lastActiveSubGroup;
    if (localStorage && localStorage.getItem('last_active_group')) {
        $lastActiveGroup = $(localStorage.getItem('last_active_group'));

    }else{
        if(typeof $lastActiveGroup == 'undefined' || $lastActiveGroup == ''){
            $lastActiveGroup = $('[data-target="pane1"]');
        }
    }
    if (localStorage && localStorage.getItem('last_active_sub_group')) {
        $lastActiveSubGroup = $(localStorage.getItem('last_active_sub_group'));

    }else{
        if(typeof $lastActiveSubGroup == 'undefined' || $lastActiveSubGroup == ''){
            $lastActiveSubGroup = $('.active-group');
        }
    }

    setTimeout(function(){
        $lastActiveSubGroup.closest('.sub-group').addClass('sub-group-open');
        $lastActiveSubGroup.closest('.sub-group').find('.sub-group-params').slideDown( "slow" );
        $lastActiveGroup.trigger('click');
    }, 200);

    $('.sub-group-params').hide();
    $('body').on('click', '.sub-legend', function(){
        var fontBtn = $(this),$parentSub = $(this).parents('.top-group'),
        fontParam = fontBtn.closest('.sub-group').find('.sub-group-params');
        //1 paramer not use function
        if(fontParam.length == 0) return false;
        $parentSub.find('.sub-group').removeClass('sub-group-open');
        if(fontParam.is(':hidden')){
             $parentSub.find('.sub-group-params').slideUp("slow");
            fontBtn.closest('.sub-group').addClass('sub-group-open');
            fontParam.slideDown( "slow" );
        }else{
            fontParam.slideUp("slow");
            fontBtn.closest('.sub-group').removeClass('sub-group-open');
        }
        if (localStorage) {
            localStorage.setItem('last_active_sub_group',"."+$(this).attr('class').replace(/\s+/g,"."));
        }
    });
    $('body').on('click', '.t4-sidebar .back', function() {
        var $a = $(this),
            $sidepane = $a.closest('.t4-sidebar'),
            level = $sidepane.data('level');
        if (level > 1) {
            $sidepane.removeClass('level-' + level).addClass('level-' + (level - 1)).data('level', level-1);
            // update prev
            var prev = $sidepane.data('prev'),
              $prev = prev.pop();

            $sidepane.width($prev.width());
            $sidepane.data('prev', prev);
        }
    });

    var $inputs = $('#myTabContent').find('input, textarea, select'),
        tplhelperValue = $('#tplhelper').val(),
        tplhelper = null;
        if(!$inputs.length) $inputs = $('#myTab').find('input, textarea, select');
    try {
        tplhelper = JSON.parse (tplhelperValue);
    } catch (e) {
        tplhelper = {};
    }

    // get origin value
    $inputs.each (function() {
        var $input = $(this),$val = $input.attr('type') == 'radio' ? $input.closest('fieldset').find('input:checked').val() : $input.val();
        if($input.attr('type') == 'checkbox') {
            $val = $input.prop('checked');
        }
        $input.data('org-value', $val);
        if($input.is('#fonts')){
           var $fontType = $input.data('fontType');
            $input.data('fontType', $fontType);
        }
        //add focus input on click
        $input.on('focus',function(){
            $input.parents('.control-group').addClass('is-focus');
        });
        $input.on('blur',function(){
            $input.parents('.control-group').removeClass('is-focus');
        });
    });
 
    var custom_styles = {},
        $all_inputs = $('.t4-sidebar').find('input, textarea, select'),
        $custom_colors = $all_inputs.filter('.minicolors'),$all_global_inputs = $('.t4-sidebar').find('input[name^="jform[param"], textarea[name^="jform[param"], select[name^="jform[param"], #jform_title, #jform_home');
    custom_styles.baseUrl = window.t4_site_root_url;
    custom_styles.previewMode = true;

    $all_global_inputs.on('change', function(e) {

        var $org_value = $(this).data('org-value'),$curValue = $(this).val(),$action = false;
        if($(this).attr('type') == 'checkbox') $curValue = $(this).prop('checked');
        tplhelper.changed = false;

        if($org_value !== $curValue){
            tplhelper.changed = true;
            $action = true;
        }
        addClassBtnSave($action);
    });
    var addClassBtnSave = function($action){
        var $btn = $('.t4-admin-save .btn-save');
        if ($action) {
             $btn.addClass('t4-save-active');
        } else {
            $btn.removeClass('t4-save-active');
        }
    }
    
    setTimeout (function() {
        // switch format for minicolor to rgb when opacity less than 1
        // T4Admin.initMinicolors($custom_colors);
    }, 500);

    // move to top panel parent
    var $preview = $('#custom-style-preview');
    $preview.closest('.control-group').hide();
    $('#tplhelper').closest('.control-group').hide();
    $preview.appendTo ($preview.closest(paneElement));
    $('.themeConfig').show();
    $('.themeConfig').append('<div class="preventDefault"></div>');
    $('.themeConfig').find('.t4-sidebar').css({'z-index':'11'});
    $('.navbar-fixed-top, .navbar-fixed-bottom').css({'z-index': '0'});
    $('body').css({'overflow': 'hidden'});

    $('body').on('click','.btn-close',function(){
        var tplhelper = jQuery('#tplhelper').val() ? JSON.parse(jQuery('#tplhelper').val()) : {};
        if(tplhelper.changed == 1){
            T4Admin.Confirm(T4Admin.langs.butonCloseConfirm, function(ans){
                if (ans) {
                   Joomla.submitbutton('style.cancel');
                    $('body').css({'overflow': 'auto'});
                    $('#tplhelper').val(JSON.stringify({}));
                }else {
                 return false;
                }
            });
        }else{
            Joomla.submitbutton('style.cancel');
            $('body').css({'overflow': 'auto'});
        }
    });
    $('body').on('click','.t4-admin-save .btn-save',function(e){
        e.preventDefault();
        $('#tplhelper').val(JSON.stringify({}));

        // const saveAll = new Promise((resolve, reject) => {
        //     console.log($('.typelist'));
        //     reject('error.');  
        // });

        // saveAll.then((message) => {  
        //     console.log(message);
        // }).catch(function(error){
        //     console.log(error);
        // });


        Joomla.submitbutton('style.apply');
        if($('#system-message-container').length){
            if($('#system-message-container').find('.alert').hasClass('alert-error') || $('#system-message-container').find('.alert').hasClass('alert-danger'))
            $('.t4-message-container').html($('#system-message-container').html());
        }
    });
    $(window).ready(function(){
        if($('#system-message-container').find('.alert').length){
            $('.t4-message-container').html($('#system-message-container').html());
            window.setTimeout(function() {
                $(".t4-message-container .alert").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove();
                });
            }, 4000);
        }
    });
    $('body').on('click','#t4-admin-btn-save-close',function(e){
        e.preventDefault();
        $('#tplhelper').val(JSON.stringify({}));
        Joomla.submitbutton('style.save');
        if($('#system-message-container').length){
            if($('#system-message-container').find('.alert').hasClass('alert-error') || $('#system-message-container').find('.alert').hasClass('alert-danger'))
            $('.t4-message-container').html($('#system-message-container').html());
        }
    });
    $('body').on('click','#t4-admin-btn-save-clone',function(e){
        e.preventDefault();
        $('#tplhelper').val(JSON.stringify({}));
        Joomla.submitbutton('style.save2copy');
        if($('#system-message-container').length){
            if($('#system-message-container').find('.alert').hasClass('alert-error') || $('#system-message-container').find('.alert').hasClass('alert-danger'))
            $('.t4-message-container').html($('#system-message-container').html());
        }
    });
    //end move script.js

    //responsive iframe
    $('body').on('click', '.t4-responsive li span', function(){
        var $btn = $(this),
            $style = '',
            $sizeScreen = $btn.attr('class').split(' ')[1];
            $('.t4-responsive li').removeClass('active');
            $btn.parents('li').addClass('active');
            switch($sizeScreen) {
                case 'desktop':
                    $style = {'width':'1366px'};
                    break;
                case 'tablet':
                    $style = {'width':'768px'};
                    break;
                case 'mobile':
                    $style = {'width':'375px'};
                    break;
                default:
                    $style = {'width':'100%'};
                    break;
            }
            $('#custom-style-preview iframe').css($style);

    });
    $(document).on('shown.bs.modal','.modal', function(){
        $('body').addClass('t4-modal-open');
        var id = $(this).attr('id'),modal = $(this).hasClass('joomla-modal');
        if(modal){
            $('.modal-backdrop').insertBefore($(this));
        }else{
            $(this).appendTo($('body'));
        }
    });
    $(document).on('change',"#typelist_site_dont_use_google_font",function(e){
        e.preventDefault();
        e.stopPropagation();
        var fontOnOff = $(this).prop('checked');
        $(this).prop('checked',false);
        if(fontOnOff == false) {
            T4Admin.disGgFont('enb');
            return;   
        }
        T4Admin.Confirm(T4Admin.langs.T4loadGoogleFontConfirm,function(ans){
            if(ans){
                $('.google-font-input').val('inherit');
                T4Admin.disGgFont('dis');
                $('#typelist_site_dont_use_google_font').prop('checked',true);
            }else{
                T4Admin.disGgFont('enb');
                $('#typelist_site_dont_use_google_font').prop('checked',false);
            }
        },"");
    });
    $(document).on('hidden.bs.modal','.modal', function(){
        $('body').removeClass('t4-modal-open');
    });

    $(document).on('click','.action-t4-modal-confirm-close', function(e) {
        $(".t4-confirm-settings").hide();
    });

});
var T4Admin = window.T4Admin || {};
!function ($) {
    $.extend(T4Admin, {
        //ajax save data to file
        t4Ajax: function($data,$task){
            $.ajax({
                type: 'POST',
                url: t4_ajax_url+'index.php?option=com_ajax&plugin=t4&format=json&t4do='+$task+'&id='+tempId,
                data: JSON.stringify($data),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    //$(document).trigger('reload-preview');
                },
                failure: function(errMsg) {
                    console.log(errMsg);
                }
            })
        },
        disGgFont : function(action){
            if(action == 'dis'){
                if($('.tab-google-font').length) $('.tab-google-font').css({display:"none"});
                if($('.tab-custom-font').length) $('.tab-custom-font').trigger('click');
            }else{

                if($('.tab-google-font').length){
                    $('.tab-google-font').css({display:"block"});
                    $('.tab-google-font').trigger('click');
                }
                    
            }
        },
        generateColorJson: function () {
            var colorPattern = [];
            $('.pattern-list').find('.pattern').each(function(index){
                var dataColorPt = $(this).data();
                colorPattern[dataColorPt.class] = dataColorPt;
            });
            return colorPattern;
        },

        initrenderForm: function(){
            $('.control-group').each(function(){
                var attrClass = $(this).find('input').attr('class');
                var field = "";
                if(!attrClass){
                    attrClass = $(this).find('.control-label').find('.t4-spacer').attr('class');
                    field = "spacer";
                }
                if(attrClass && attrClass.search(/(before-)|(after-)/gi) != -1){
                    var insert = attrClass.split(' '),
                    $lastClass = insert[insert.length - 1],
                    $itemFlagArr = $lastClass.split('-'),
                    $itemFlag = $itemFlagArr[$itemFlagArr.length -1];
                    if(field) $itemFlag += '-lbl';

                    var $fieldFlag;
                        if ($('#typelist_site_'+$itemFlag+'').length) {
                             $fieldFlag = $('#typelist_site_'+$itemFlag+'');
                        }else if ($('#typelist_theme_'+$itemFlag+'').length) {
                             $fieldFlag = $('#typelist_theme_'+$itemFlag+'');
                        }else if ($('#typelist_navigation_'+$itemFlag+'').length) {
                             $fieldFlag = $('#typelist_navigation_'+$itemFlag+'');
                        }else{
                            $fieldFlag = $('#jform_params_'+$itemFlag+'');
                        }

                    if(insert.length == 1){
                        $(this).find('input').removeAttr('class');
                    }else{
                        $(this).find('input').removeClass($lastClass);
                    }
                    if($itemFlagArr[0] == 'before'){
                        $(this).insertBefore($fieldFlag.closest('.control-group'));
                    }else if($itemFlagArr[0] == 'after') {
                         $(this).insertAfter($fieldFlag.closest('.control-group'));
                    }
                }

            })
        },
        initMinicolors: function($elem){
            $elem.minicolors('settings', {
                control: 'hue',
                opacity: true,
                position: 'bottom',
                theme: 'bootstrap',
                change: function(value, opacity){
                    if (opacity >= 1) {
                        var settings = $(this).data('minicolors-settings') || {};
                        settings.format = 'hex';
                        $(this).data('minicolors-settings', settings);
                    } else {
                        var settings = $(this).data('minicolors-settings') || {};
                        settings.format = 'rgb';
                        $(this).data('minicolors-settings', settings);
                    }
                }
            });
        },
        initSwitchTheme: function () {
            // init toggle value
            var $Switchthemetoggle = $('#switch-style'),
                switchState = localStorage.getItem('switch_theme');
                if(switchState == 1){
                    $('.t4-pane.themeConfig').addClass('dark');
                }
            $Switchthemetoggle.prop('checked', switchState).on('change', function () {
                localStorage.setItem('switch_theme', $(this).prop('checked') ? 1 : '');
                $('.t4-pane.themeConfig').toggleClass('dark');
            });
        },
        Messages: function(mesg,type){
            var $cls_mesg = '', time;
            switch(type) {
                case 'warning':
                    $cls_mesg = ' alert-warning';
                    break;
                case 'error':
                    $cls_mesg = ' alert-error alert-danger';
                    break;
                case 'status':
                    $cls_mesg = ' alert-info';
                    break;
                default:
                    $cls_mesg = ' alert-success';
            }
            var $mesgEl = '<div class="alert '+$cls_mesg+'"><button type="button" data-dismiss="alert" class="t4-btn btn-icon close"><i class="fal fa-times"></i></button><div>'+mesg+'</div></div>';
            if(type != 'error'){
                $('.t4-message-container').prepend($mesgEl);
                time = window.setTimeout(function() {
                    $(".t4-message-container .alert").not('.alert-error').fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove();
                    });
                }, 4000);
            }else if(type == 'error'){
                if(typeof time != 'undefined') window.clearTimeout(time); 
                $(".t4-message-container .alert").remove();
                $('.t4-message-container').prepend($mesgEl);
            }

        },
        Alert: function(mesg,type){
           return alert(mesg);
        },
        Confirm: function(message, handler,$type){
            if(typeof handler != 'function') return;
            if(typeof $type == 'undefined') $type = '';
            if($type == 'Save'){
                 $("#t4-confirm").find('.btn-no').text('No');
                 $("#t4-confirm").find('.btn-yes').text('Save');
            }else{
                $("#t4-confirm").find('.btn-no').text('No');
                 $("#t4-confirm").find('.btn-yes').text('Yes');
            }
          $('.t4-confirm-action').find('.msg').text(message);
          //Trigger the modal
          $(".t4-confirm-settings").show();

           //Pass true to a callback function
           $(".btn-yes").off('click').on('click',function () {
               handler(true);
               $(".t4-confirm-settings").hide();
           });

           //Pass false to callback function
           $(".btn-no").off('click').on('click',function () {
               handler(false);
               $(".t4-confirm-settings").hide();
           });
        },
        initConfirmBox: function(){
            var $message = '<div class="t4-confirm-settings" style="display:none;"><div class="t4-modal-confirm-overlay"></div><div class="t4-modal t4-confirm-action no-title" id="t4-confirm" role="dialog">';
            $message += '<div class="t4-modal-header"><span class="t4-modal-header-title"></span>';
            $message += '<a href="#" class="action-t4-modal-confirm-close"><span class="fal fa-times"></span></a>';
            $message += '</div><div class="t4-modal-content"><p class="msg"></p></div>';
            $message += '<div class="t4-modal-footer"><a href="#" class="btn btn-default btn-no" title="No">No</a><a href="#" class="btn btn-primary btn-yes" title="Yes">Yes</a></div>';
            $message += '</div></div>';
            $('.themeConfigModal').prepend($message);
        },

    });

    $(document).ready(function(){
        // setInputValue Callback Function
        $.fn.setInputValue = function(options){
            if ($(this).attr('type') == 'checkbox') {
                if (options.field == '1') {
                    $(this).prop('checked',true);
                    $(this).val('1');
                }else{
                    $(this).prop('checked',false);
                    $(this).val('0');
                }
            }else if(this.hasClass('input-select')){
                this.val( options.field );
                this.trigger('liszt:updated');
                this.trigger('chosen:updated');
            }else if(this.hasClass('input-media')){
                if(options.field){
                    $imgParent = this.parent('.media');
                    $imgParent.find('img.media-preview').each(function() {
                        $(this).attr('src',layoutbuilder_base+options.field);
                    });
                }
                this.val( options.field );
            }else{
                this.val( options.field );
            }

            if (this.data('attrname') == 'column_type'){
                if (this.val() == 'component') {
                    $('.form-group.name').hide();
                }
            }
        }
        // callback function, return checkbox value
        $.fn.getInputValue = function(){
            if (this.attr('type') == 'checkbox') {
                return $(this).prop('checked');
            }else{
                return this.val();
            }
        }
        // Create the function. check html special chars

        $.fn.htmlspecialchars = function(string) {
            if(typeof string != 'string') return string;
            // A collection of special characters and their entities.
            var specialchars = [
                [ '&', '&amp;' ],
                [ '<', '&lt;' ],
                [ '>', '&gt;' ],
                [ '"', '&quot;' ]
            ];
          // Our finalized string will start out as a copy of the initial string.
          var escapedString = string;
          // For each of the special characters,
          var len = specialchars.length;
          for (var x = 0; x < len; x++) {
            // Replace all instances of the special character with its entity.
            escapedString = escapedString.replace(
              new RegExp(specialchars[x][0], 'g'),
              specialchars[x][1]
            );
          }
          // Return the escaped string.
          return escapedString;
        };
        // Create the function.
        $.fn.htmlspecialchars_decode = function(string) {
            if(typeof string != 'string') return string;
            var specialchars = [
              [ '"', '&quot;' ],
              [ '>', '&gt;' ],
              [ '<', '&lt;' ],
              [ '&', '&amp;' ]
            ];
            // Our finalized string will start out as a copy of the initial string.
            var unescapedString = string;

            // For each of the special characters,
            var len = specialchars.length;
            for (var x = 0; x < len; x++) {
                // Replace all instances of the entity with the special character.
                unescapedString = unescapedString.replace(
                    new RegExp(specialchars[x][1], 'g'),
                    specialchars[x][0]
                );
            }

            // Return the unescaped string.
            return unescapedString;
        };


        // Sortable
        $.fn.rowSortable = function($type){
            $(this).sortable({
                placeholder: "ui-state-highlight",
                forcePlaceholderSize: true,
                axis: 'x',
                opacity: 0.8,
                tolerance: 'pointer',

                start: function(event, ui) {
                    $( ".t4-layout-section .row" ).find('.ui-state-highlight').addClass( $(ui.item).attr('class') );
                    $( ".t4-layout-section .row" ).find('.ui-state-highlight').css( 'height', $(ui.item).outerHeight() );
                },
                stop: function(event, ui){
                    if($type == 'layout'){
                        T4Layout.layoutApply();
                    }else if($type == 'mega'){
                        T4AdminMegamenu.megaApply();
                    }
                },

            }).disableSelection();
        };
        T4Admin.initSwitchTheme();
        T4Admin.initConfirmBox();

        //update render theme color
        $('.t4-spacer').each(function(e){
            var cls = $(this).attr('class').split(' ');
            var regex = /(.*?)-spacer/gm;
            var groupArr = regex.exec(cls[1]);
            if(groupArr){
                var Sp_cls = groupArr[1] +'-group t4-spacer-heading';
                $(this).closest('.control-group').addClass(Sp_cls);
                $('.'+groupArr[1]+'-color').closest('.control-group').insertAfter($('.'+ groupArr[1] +'-group'));
            }
            
        });
        // $('.user-spacer').closest('.control-group').appendTo($('.t4-spacer').closest(".sub-group-params"));
        // $('.user-spacer').closest('.control-group').addClass("user-group t4-spacer-heading");
        // $('.user-color').closest('.control-group').insertAfter($('.user-group'));
        // $('.col-left').closest('.control-group').addClass('col-left');
        // $('.col-right').closest('.control-group').addClass('col-right');
        $('.t4-custom-color-spec').each(function(el){
            var clsCol = $(this).attr('class');
             var regexCol = /col-\w+/gm;
             var groupColArr = regexCol.exec(clsCol);
            if(groupColArr[0]){
                $(this).removeClass(groupColArr[0]);
                $(this).closest('.control-group').addClass(groupColArr[0]);

            }
        });
        
        $(document).trigger('t4.ready');
    });

}(jQuery);
