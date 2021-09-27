(function ($) {
    $.fn.bindActions = function (btn, object) {
        var $element = $(this);
        $element.find(btn).on('click', function () {
            var action = $(this).data('action'),
                func = object[action] ? action : action.replace('-', '_');
            if (typeof object[func] == 'function') {
                object[func](this);
                setTimeout(function () {
                    $element.trigger(action)
                }, 100);
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

        var calendar_ids = [];
        $newitem.find('div, img').each(function () {
            // update id, name
            if($(this).attr('id')) {
                var newid = this.id.replace(/_([0-9]+)(_[a-z_\-]+)*$/, '_' + idx + '$2');//last number is index
                this.id = newid;
            }
        });
        $newitem.find('input, select, textarea').each(function () {
            // update id, name
            if (this.id == '') return;
            var newid = this.id.replace(/_([0-9]+)(_[a-z_\-]+)*$/, '_' + idx+'$2');//last number is index
            var oldid = this.id;
			
            if(newid == oldid) {
                if(!newid.test(/_[0-9]+/)) {
                    newid += '_' + idx;
                }
            }
            // update label for
            $newitem.find('[for="' + oldid + '"]').attr('for', newid).attr('id', newid + '-lbl');
            if($(this).attr('id')) {
                this.id = newid;
            }
            if($(this).attr('name')) {
                this.name = $(this).attr('name').replace(/\[[0-9]+\](\[\])?$/, '[' + idx + ']$1');// + '[' + idx + ']';
            }
            if($(this).data('id')) {
                var dataid = $(this).data('id');
                $(this).data('id', dataid.replace(/_([0-9]+)(_[a-z_\-]+)*$/, '_' + idx+'$2'));
            }

            // find a tag and update id
            if($(this).attr('id')) {

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
            }

            // update button
            var $button = $newitem.find('#' + oldid + '_img');
            if ($button.length) {
                $button.attr('id', newid + '_img');
            }

            //update calendar
            if($(this).hasClass('type-calendar')) {

                if(typeof(Calendar) == 'function' || typeof(Calendar) == 'object') {
                    calendar_ids.push(newid);
                }
            }

            // update chosen
            if (jQuery(this).next().hasClass('chzn-container')) {
            	jQuery(this).val(jQuery('#'+oldid).val());
            }

            // update media field.
//             if (jQuery(this).hasClass('field-media-input')) {
// 				$newtd = jQuery(this).parents('td');
// 				$oldtd = jQuery('#'+oldid).parents('td');
// 				$clone = $oldtd.clone(false, false);
// 				$clone.insertAfter($newtd);
// 				$newtd.remove();
//             }
        });

        $newitem.removeClass ('first');
        $newitem.insertAfter($(this));

        if(calendar_ids.length) {
            for(var i=0; i<calendar_ids.length; i++) {
                Calendar.setup({
                    inputField: calendar_ids[i],
                    ifFormat: "%Y-%m-%d %H:%M:%S",
                    // Trigger for the calendar (button ID)
                    button: calendar_ids[i]+"_img"});
            }
        }

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
        	// SqueezeBox cause error in joomla 3.7 and Tips do not cause any effect.
            // enable modal
//             SqueezeBox.assign($newitem.find('a').filter('.modal').get(), {
//                 parse: 'rel'
//             });
            // init new tips
//             new Tips($newitem.find('.hasTip').get(), {maxTitleChars: 50, fixed: false});
//             new Tips($newitem.find('.hasTipPreview').get(), {
//                 maxTitleChars: 50,
//                 fixed: false
//             });
        }
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
