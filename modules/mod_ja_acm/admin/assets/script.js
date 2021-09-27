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
(function ($) {
    $.fn.bindActions = function (btn, object) {
        var $element = $(this),
            $confirmbox = new jBox ('Confirm', {id: 'amc-confirm'});
        $element.find(btn).on('click', function () {
            var $btn = $(this),
                action = $btn.data('action'),
                func = object[action] ? action : action.replace('-', '_');

            if (typeof object[func] == 'function') {
                if ($btn.data('confirm')) {
                    $confirmbox.options.confirm = function () {
                        object[func]($btn);
                        setTimeout(function () {
                            $element.trigger(action)
                        }, 100);
                    }
                    $confirmbox.open();
                } else {
                    object[func]($btn);
                    setTimeout(function () {
                        $element.trigger(action)
                    }, 100);
                }
            }
        })
    };

    $.fn.elasticTextarea = function (events) {
        var $element = $(this);
        var autoheightResize = function (box, reset) {
            var $box = $(box),
                padding = $box.outerHeight() - $box.height();
            if (reset) {
                $box.height(20);
            } else if ($box.data('scrollheight') == box.scrollHeight) {
                // not reset & scrollheight not change
                return;
            }
            // update height
            $box.height(box.scrollHeight - padding);
            // store current scroll height
            $box.data('scrollheight', box.scrollHeight);
        };

        $element.find('textarea').addClass('autoheight').on("keyup change", function () {
            autoheightResize(this);
        }).on("focus blur resize update", function () {
            autoheightResize(this, true);
        });

        $element.on(events, function () {
            $element.find('textarea').trigger('update');
        })
    };

    $.fn.jaclone = function (idx) {
        var $item = $(this),
            $newitem = $item.clone(true, true),
            atags = $newitem.find('a');

        $newitem.find('input, select, textarea').each(function () {
            // update id, name
            var newid = this.id + '_' + idx,
                oldid = this.id;
            // update label for
            $newitem.find('[for="' + oldid + '"]').attr('for', newid).attr('id', newid + '-lbl');
            this.id = newid;
            this.name = $(this).data('name') + '[' + idx + ']';

            // find a tag and update id
            atags.each(function (i, a) {
                if (a.href) a.href = a.href.replace('fieldid=' + oldid + '&', 'fieldid=' + newid + '&');
                if ($(a).attr('onclick')) $(a).attr('onclick', $(a).attr('onclick').replace('\'' + oldid + '\'', '\'' + newid + '\''));
            });

            // update image preview tips
            var regex = new RegExp('"' + oldid + '_preview', 'gm'),
                oldtips = $item.find('.hasTipPreview'),
                newtips = $newitem.find('.hasTipPreview');
            oldtips.each(function (i, tip) {
                if (tip.retrieve && tip.retrieve('tip:title') && tip.retrieve('tip:text') && tip.retrieve('tip:text').match(regex)) {
                    newtips[i].store('tip:title', tip.retrieve('tip:title'));
                    newtips[i].store('tip:text', tip.retrieve('tip:text').replace(regex, '"' + newid + '_preview'));
                } else if (tip.title.match(regex)) {
                    newtips[i].title = tip.title.replace(new RegExp('"' + oldid + '_preview', 'gm'), '"' + newid + '_preview');
                }
            });

            // update button
            var $button = $newitem.find('#' + oldid + '_img');
            if ($button.length) {
                $button.attr('id', newid + '_img');
            }
        });

        return $newitem;
    };

})(jQuery);


var jaTools = {};
(function ($) {
    jaTools.fixCloneObject = function ($newitem, bindEvents) {
        // fix for jQuery Chosen
        if ($newitem.find('select').next().hasClass('chzn-container')) {
            // remove chosen if found and recreate it
            $newitem.find('.chzn-container').remove();
            $newitem.find('select').data('chosen', null).chosen();
        }

        // rebind events for image button & tips
        if (bindEvents) {
            // enable modal
            SqueezeBox.assign($newitem.find('a').filter('.modal').get(), {
                parse: 'rel'
            });
            // init new tips
            new Tips($newitem.find('.hasTip').get(), {maxTitleChars: 50, fixed: false});
            new Tips($newitem.find('.hasTipPreview').get(), {
                maxTitleChars: 50,
                fixed: false,
                onShow: window.jMediaRefreshPreviewTip ? jMediaRefreshPreviewTip : function(){}
            });
        }
		// fix calendar after clone.
		if ($newitem.find('.field-calendar input').length) {
			(typeof Calendar !== 'undefined') && Calendar.setup({
				inputField: $newitem.find('.field-calendar input').attr('id'),
				ifFormat: "%Y-%m-%d",
				button: $newitem.find('.field-calendar input').attr('id')+"_img",
				align: "Tl",
				singleClick: true,
				firstDay: 0
			});
		}
		// end fix calendar after clone.

		// fix media field joomla 3.7
        $newitem.find('.field-media-wrapper').each(function() {
            $media = $(this).clone(false, false);
            $td = $(this).parent();
            $(this).remove();
            $td.append($media);
            // we could not use $(this) here
            $newitem.find('.field-media-wrapper').fieldMedia();
            $newitem.find(".hasTooltip").tooltip({"html": true,"container": "body"});
            // need to attach data-name again after refresh new row.
            $newitem.find('.field-media-wrapper input').each(function () {
                // get ignoresave from first item.
                let igs = jQuery('.field-media-wrapper input').first().data("ignoresave");
                $(this).not('.acm-object').data('ignoresave', igs);
                if ($(this).data('name') == undefined && $(this).attr('name') != undefined) {
                    $(this).data('name', $(this).attr('name').replace(/\[\d+\]$/, ''));
                }
            });
        });
		// end fix media field joomla 3.7

		// fix user field joomla 3+
        $newitem.find('.field-user-wrapper').each(function() {
            $media = $(this).clone(false, false);
            $td = $(this).parent();
            $(this).remove();
            $td.append($media);
            $newitem.find('.field-user-wrapper').fieldUser();
            $newitem.find(".hasTooltip").tooltip({"html": true,"container": "body"});
            // need to attach data-name again after refresh new row.
            $newitem.find('.field-user-wrapper input').each(function () {
                // get ignoresave from first item.
                let igs = jQuery('.field-user-wrapper input').first().data("ignoresave");
                $(this).not('.acm-object').data('ignoresave', igs);
                if ($(this).data('name') == undefined && $(this).attr('name') != undefined) {
                    $(this).data('name', $(this).attr('name').replace(/\[\d+\]$/, ''));
                    if ($(this).data('name') == "undefined") $(this).data('name', "");
                }
            });
        });
		// end fix user field joomla 3+
    };

    jaTools.getVal = function (elem, $parent) {
        var $elem = $(elem),
            name = $elem.data('name'),
            type = $elem.attr('type'),
            $fields = $parent.find($elem.prop('tagName')).filter(function () {
                return $(this).data("name") == name
            });

        if (type == 'checkbox') {

        }
        if (type == 'radio') $fields = $fields.filter(':checked');

        return $fields.map(function () {
            return type == 'checkbox' ? $(this).prop('checked') : $(this).val()
        }).get();
    };

    jaTools.setVal = function ($elem, value) {
        if (!$elem.length) return;
        var type = $elem.attr('type'),
            tag = $elem.prop('tagName');
        if (type == 'radio') {
            $elem.removeAttr('checked').filter('[value="' + value + '"]').prop('checked', true);
        } else if (type == 'checkbox') {
            $elem.prop('checked', value);
        } else {
            $elem.val(value);
        }
    };
})(jQuery);

jaToolsInit = function ($) {

    var $allElems, activeType, activeLayout, $activeType, advancedForm, configs = null;
    
    var initConfigForm = function (bindEvent) {
        // Update config data
        if (!configs) {
            configs = getJSon(decodeHtml($('#jatools-config').val()));
        }
        // display correct form
        displayConfig(bindEvent);

    };

    var displayConfig = function (rebind) {
        var $jaacmadmin = $('#ja-acm-admin'),
            tmp = $jaacmadmin.data('activetype').split(':');
        activeType = tmp.length == 1 ? tmp[0].trim() : tmp[1].trim();
        $activeType = $('#jatools-' + activeType);
        activeLayout = $jaacmadmin.data('activelayout');

        // update active form
        updateActiveForm(rebind);

        // fix chozen
        jaTools.fixCloneObject($activeType);
    };

    var updateVal = function (fname) {
        var $elem = $allElems.filter(function () {
            return $(this).data("name") == fname
        }).first();
        if ($elem.data('ignoresave')) return;
        // check if this fields in a hide group - used for other style
        if ($elem.parents('.control-group').first().hasClass('hide')) return;
        if ($elem.parents('.jatools-group').first().hasClass('hide')) return;

        var val = $elem.data('acm-object') ? $elem.data('acm-object').getData() : jaTools.getVal($elem, $activeType),
            layout = $elem.parents('.jatools-layout-config');
        configs[activeType][fname] = val;
    };

    var getJSon = function (str) {
        var result = {};
        try {
            result = JSON.parse(str.trim());
        } catch (e) {
            return {};
        }
        return $.isPlainObject(result) ? result : {};
    };

    var encodeHtml = function (str) {
        return String(str)
            .replace(/</g, '((')
            .replace(/>/g, '))');
    };

    var decodeHtml = function (str) {
        return String(str)
            .replace(/\(\(/g, '<')
            .replace(/\)\)/g, '>');
    };

    var updateActiveForm = function (rebind) {
        // update value to form
        if (!activeType || !$activeType) return;
				// check sample data
				if (!configs[activeType]) {
					configs[activeType] = null;
					var sampledata = getJSon(decodeHtml($activeType.find('[name="jatools-sample-data"]').val()));
					configs[activeType] = sampledata && sampledata[activeType] ? sampledata[activeType] : sampledata;
				}
        var data = configs[activeType];				
        if (!data) return;

        $.each(data, function (field_name, value) {
            var $elem = $activeType.find('[name="' + field_name + '"]'),
                field_data = data[field_name],
                group = $elem.parents('.jatools-group');
            if (!$elem.length) return;

            if ($elem.data('acm-object')) {
                $elem.data('acm-object').bindData(field_name, data);
            } else if ($.isArray(field_data) && group.hasClass('jatools-multiple')) {
                // find this field
                var $rows = group.find('.jatools-row');
                if ($rows.length && field_data.length > $rows.length) {
                    var $lastrow = $rows.last();
                    for (var i = $rows.length; i < field_data.length; i++) {
                        // clone row
                        $lastrow = $rows.first().jaclone(i).insertAfter($lastrow);
                        jaTools.fixCloneObject($lastrow, rebind);
                    }
                }
                $rows = group.find('.jatools-row');
                // check & update data
                // rows = group.find  ('.jatools-row');
                field_data.each(function (val, i) {
                    var $elem = $($rows[i]).find('input, select, textarea').filter(function () {
                        return $(this).data('name') == field_name
                    });
                    jaTools.setVal($elem, val);
                });
            } else {
				$elem.attr('data-alt-value', $.isArray(field_data) && $elem.prop('name').substr(-2) != '[]' ? field_data[0] : field_data); // fix for calendar field.
                jaTools.setVal($elem, $.isArray(field_data) && $elem.prop('name').substr(-2) != '[]' ? field_data[0] : field_data);
            }
        });

        // compatible with old version
        $activeType.find('.acm-object').each(function () {
            var $this = $(this),
                field_name = this.name;
            if (!data[field_name]) {
                $this.data('acm-object').bindData(field_name, data);
            }
        });
        // end compatible

        // get all form elements
        $allElems = $('.jatools-layout-config').find('input, select, textarea');

        $activeType.trigger('change');
    };

    var getData = function () {
        configs = {};
        configs[':type'] = $('#ja-acm-admin').data('activetype');
        configs[activeType] = {};
        configs[activeType]['jatools-layout-' + activeType] = activeLayout;

        var $elems = $activeType.find('input, select, textarea'),
            names = $elems.map(function () {
                return $(this).data('name')
            }).get();
        names.each(function (fname, i) {
            updateVal(fname);
        });

        return encodeHtml(JSON.stringify(configs));
    };

    var updateData = function () {
        // close dialog
        // update data to form
        var advancedvalue = decodeHtml($("#acm-advanced-input").val()),
        	_config = getJSon(advancedvalue);
        
        if (!_config[':type']) return;

        var newType         = _config[':type'],
			tmp             = newType.split(':'),
			newSelectedType    = tmp.length == 1 ? tmp[0].trim() : tmp[1].trim(),
			newLayout       = _config[newSelectedType]['jatools-layout-' + newSelectedType];

		if (!newLayout) return;
		
        if ((newType != activeType && newSelectedType != activeType) || newLayout != activeLayout) {
			// store temporary value in cookie, and reload form
			var expire = new Date();
			expire.setTime(expire.getTime() + 3600000);
			document.cookie = "advancedvalue=" + advancedvalue + "; expires=" + expire.toGMTString() + "; path=/";
			window.location.reload(true);
		} else {
			configs = _config;
            initConfigForm(true);
		}
    };

    var advancedFormInit = function () {
        var $button = $('#toolbar-advanced').children();
        $button.attr('onclick', '');
        new jBox('Confirm', {
            attach: $button,
            title: 'ACM Data',
            content: $('#acm-advanced-form'),
            width: 550,
            height: 300,
            confirmButton: 'Update',
            cancelButton: 'Close',
            confirm: function() {
                updateData();
            },
            onOpen: function() {
                $("#acm-advanced-input").val(getData());
            }
        });
        return;
    };

    // get all form elements
    $allElems = $('.jatools-layout-config').find('input, select, textarea');

    $allElems.each(function () {
        var $this = $(this);
        if ($this.hasClass('required')) {
        	// fix do not check for required.
            $this.data('required', 1);
                //.removeClass('required');
        }
        $this.data('name', this.name);
    });

    initConfigForm(false);

    // bind submit event for form
    document.adminForm.onsubmit = function () {
        $('#jatools-config').val(getData());
    };

    // bind event for btn-add, btn-del
    $('.jatools-btn-add').on('click', function () {
        var $rows = $(this).parents('.jatools-group').find('.jatools-row');
        jaTools.fixCloneObject($rows.first().jaclone($rows.length).insertAfter($rows.last()), true);
        // update $allElems
        $allElems = $('.jatools-layout-config').find('input, select, textarea');
    });

    $('.jatools-btn-del').on('click', function () {
        var $this = $(this),
            $row = $this.parent(),
            $fieldset = $row.parent();
        // move this button out
        $this.appendTo($fieldset);
        $row.remove();
        // update $allElems
        $allElems = $('.jatools-layout-config').find('input, select, textarea');
    });

    // hover event for row
    $('.jatools-row').on('mouseenter', function () {
        if ($(this).is($(this).parent().find('.jatools-row').first())) return;
        // check if this is the last row, do nothing
        if ($(this).parent().find('.jatools-row').length < 2) return;
        $(this).parent().find('.jatools-btn-del').appendTo($(this));
    }).on('mouseleave', function () {
        $(this).find('.jatools-btn-del').appendTo($(this).parent());
    });

    // build done, fire change events
    $(document).ready(function () {
        $('.jatools-layout-config').trigger('change');
    });
    $(window).on('load', function() {
        // fix user field empty onload
        if (jQuery('.field-user-wrapper').length) {
            let uIdx=0;
            jQuery('.field-user-wrapper').each(function() {
                // add value to user field after loaded.
                $(this).find("input[type=\"text\"]").attr("name", "");
                $(this).find("input[type=\"text\"]").data("name", "");
                if (configs[configs[":type"].split(":")[1]][""] !== undefined) {
                    jQuery(this).find("input[type=\"text\"]").val(configs[configs[":type"].split(":")[1]][""][uIdx]);
                    uIdx++;
                }
            });
        }

        // we must update variable and some data if media,user field showup.
        if (jQuery('.field-user-wrapper').length || jQuery('.field-media-wrapper').length) {
            $allElems = $('.jatools-layout-config').find('input, select, textarea');
            $(".jaacm-list").find('input, textarea, select').not('.acm-object').data('ignoresave', 1);
        }
    });

    // store this
    $.data(document, 'jaToolsACM', this);

    // bind the advanced button
    advancedFormInit();

    // add expand panel button
    var $fsbtn = $('<div class="toggle-fullscreen"><i class="fa fa-expand icon-expand"></i><i class="fa fa-compress icon-contract"></i></div>'),
        $acmadmin = $('.ja-acm-admin');
    if ($acmadmin.hasClass('joomla2')) {
        var $parent = $('body');
        $parent.find('.panel').first().addClass('acm-panel');
        $fsbtn.appendTo('#basic-options').click(function (e) {
            e.stopPropagation();
            if ($parent.hasClass ('full-screen')) {
                $parent.removeClass('full-screen');
            } else {
                $parent.addClass('full-screen');
            }
        });
    } else {
        $fsbtn.appendTo('#general > .row-fluid > .span9 > h3').click(function (e) {
            e.stopPropagation();
            var $panel = $('body');
            if ($panel.hasClass ('full-screen')) {
                $panel.removeClass('full-screen');
            } else {
                $panel.addClass('full-screen');
            }
        });
    }
};
