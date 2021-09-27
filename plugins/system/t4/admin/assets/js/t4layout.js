jQuery(function($) {
    var block_editors, block_css_editor;
    var isDarkMode = $('#attrib-themeConfig.dark').length ? true : false;
    /******
    add events on elements
    ***/
    // Open Row settings Modal
    $(document).on('click touchstart', '.t4-row-options', function(event) {
        event.preventDefault();
        var $rowSettings = $('.t4-row-settings'),
            $dataCols = [];
        if (!$rowSettings.parents().is('.themeConfigModal')) $rowSettings.appendTo('.themeConfigModal');
        $('body').addClass('t4-modal-open');
        $rowSettings.find('.t4_padding,.t4_margin').hide();
        $rowSettings.find('.t4_extra_class,.t4_layout_sticky').show();
        $rowSettings.show();
        $('.row-active').removeClass('row-active');
        $parent = $(this).closest('.t4-layout-section');
        /*
        var ptColorActive = $('.row-active').data('color_pattern');
        ptColorActive = ptColorActive ? ptColorActive : 'none';
        $rowSettings.find('.pattern.active').removeClass('active');
        if (ptColorActive) $rowSettings.find('.' + ptColorActive).trigger('click');*/
        $parent.addClass('row-active');
        var $dataSection = $parent.data();
        T4Layout.initDataPaddingandMargin($dataSection);
        $parent.find('.t4-layout-col').each(function() {
            $dataCols.push($(this).data());
        });
        var $clone = $('.t4-row-setting');
        $clone.find('.config-section').html(T4Layout.configSection($parent, 'def'));
        var $divlayout = '<ul class="t4-admin-layout">';
        $divlayout += '<li><span class="t4-admin-layout-structur active" title="Layout Default" data-tooltip="Layout Default">Layout Default</span></li>';
        $divlayout += '<li><span class="t4-admin-responsive" title="Layout responsive" data-tooltip="Layout responsive">responsive</span></li>';
        $divlayout += '</ul>';
        $clone.find('.config-section').prepend($divlayout);
        $clone.find('.t4-col-remove').each(function() { $(this).removeClass('hidden'); });
        $clone.find('.config-section').removeClass('md sm xs lg xl def');
        $clone.find('.config-section').addClass('def');
        $clone.find('.tab-pane').removeClass('active show').addClass('fade');
        $clone.find('.nav-tabs .nav-item').removeClass('active');
        $clone.find('.nav-tabs .nav-item a').removeClass('active');
        $clone.find('#general').removeClass('fade').addClass('show active');
        $clone.find('.t4-group-general').addClass('active');
        $clone.find('.t4-input-cols').data('layout', $dataSection.layout);
        var cols = $clone.find('.t4-layout-col').length;
        if ($clone.find('.alert.alert-warning').length) {
            $clone.find('.alert.alert-warning').remove();
            $clone.find(".t4-modal-footer").removeClass('disabled');
        }
        $clone.find('.t4-input-color').each(function() {
            if (!$(this).hasClass('hasinit')) {
                $(this).addClass('minicolors hasinit');
            }
        });
        var oldLayout = '';
        $clone.find('.t4-layout-col').each(function(idx) {
            var $idx = $(this).find('.t4-admin-layout-vis').data('idx');
            oldLayout += $(this).data('col');
            if (idx + 1 < cols) {
                oldLayout += '+';
            }
            var $that = $(this);
            $.each($dataCols[$idx], function(index, val) {
                if (index != 'uiresizable') {
                    $that.data(index, val);
                }
            });

        });
        var $container = $('.row-active').data('container');
        $('.fuildwidth .active').removeClass('active');
        if (typeof $container == 'undefined' || !isNaN($container)) {
            $('[data-container="container"]').addClass('active');
            $('.t4-layout-container').val('container');
        } else {
            $('[data-container="' + $container + '"]').addClass('active');
            $('.t4-layout-container').val($container);

        }

        var ptColorActive = $('.row-active').data('color_pattern');
        ptColorActive = ptColorActive ? ptColorActive : 'none';
        $clone.find('.pattern.active').removeClass('active');
        $clone.find('.' + ptColorActive).trigger('click');

        var countCol = $clone.find('.t4-layout-col').length,
            countPos = $('.t4-admin-layout-hiddenpos').find('.hide').length;
        if (countCol != countPos) {
            $('.t4-admin-layout-hiddenpos').addClass('haspos');
        }
        $clone.find('.fuildwidth .btn').each(function() {
            T4Layout.t4Container($(this));
        });
        T4Layout.initOverlay();
        $('.fuildwidth .active').click();
        $clone.find('.t4-layout').each(function() {
            var $that = $(this),
                attrName = $that.data('attrname'),
                attrValue = $parent.data(attrName);
            if (attrName == 'background_image') {
                if ($that.hasClass('t4-input-media') || $that.hasClass('field-media-input')) {
                    if ($that.closest('.field-media-wrapper').find('.add-on.field-media-preview').length) {
                        $that.closest('.field-media-wrapper').data('fieldMedia').setValue(attrValue);
                    } else {
                        if (attrValue) {
                            var image = $('<img src="' + t4_site_root_url + '/' + attrValue + '" alt="" />');
                            $that.closest('.field-media-wrapper').find('.field-media-preview').html(image);
                        }
                    }
                }
            }
            if (attrName == 'background_color' || attrName == 'link_color' || attrName == 'text_color' || attrName == 'link_hover_color') {
                valueColor = attrValue;
                if (typeof attrValue == 'undefined') valueColor = '';
                $that.minicolors({
                    control: 'hue',
                    position: 'bottom',
                    theme: 'bootstrap'
                });
                $that.minicolors('value', valueColor);
            }
            if ($that.is('select')) {
                $that.chosen({ width: '100%' });
            }
            $that.setInputValue({ field: attrValue });

        });
        $('.t4-admin-dv-def').click();
        T4Layout.resizeElement();
    });

    // Open Column settings Modal
    $(document).on('click touchstart', '.t4-column-options', function(event) {
        event.preventDefault();
        var $colSettings = $('.t4-cols-settings');
        if (!$colSettings.parents().is($('.themeConfigModal'))) $colSettings.appendTo($('.themeConfigModal'));
        $('body').addClass('t4-modal-open');
        $colSettings.show()
        $('.t4-layout-col').removeClass('column-active');
        $parent = $(this).closest('.t4-layout-col');
        $parent.addClass('column-active');

        $('.t4-cols-inner').find('select.t4-layout').each(function() {
            $(this).chosen('destroy');
        });

        var $clone = $('.t4-cols-inner');
        $clone.find('.tab-pane').removeClass('active show').addClass('fade');
        $clone.find('#general').removeClass('fade').addClass('show active');
        $clone.find('.t4-layout').each(function() {
            var $that = $(this),
                attrValue = $parent.data($that.data('attrname'));
            if ($that.data('attrname') == 'name') {
                if (typeof $parent.data('title') != 'undefined') $that.data('title', $parent.data('title'));
                if (typeof $parent.data('modname') != 'undefined') $that.data('modname', $parent.data('modname'));
                if ($parent.data('type') == 'component') {
                    $parent.data('name', 'Component');
                }
            }
            attrValue = $parent.data($that.data('attrname'));
            $that.setInputValue({ field: attrValue });
        });

        $clone.find('select.t4-layout').each(function() {
            var $input = $(this);
            $input.chosen({ width: 'auto' });
            var typeName = $('.column-active').data('type'),
                nameActive = $('.column-active').data('name');
            $('.name_type').not($('.' + typeName)).hide();
            $('.' + typeName).show();
            $('.style').hide();
            $('.element_type').hide();
            if (typeName == 'module' || typeName == 'positions') {
                $('.style').show();
            } else if (typeName == 'element') {
                $('.element_type').show();
            }
            var $typeAct = $('.' + typeName).find('select.t4-layout');
            $typeAct.val(nameActive).trigger("chosen:updated");

        });
        $('.name_type').find('select.t4-layout').on('change', function() {
            var $select = $(this),
                value = $select.val();
            if ($select.data('attrname') == 'module') {
                var modname = this.options[this.selectedIndex].getAttribute('data-modname');
                $('[data-attrname="name"]').data('modname', modname);
                $('[data-attrname="name"]').data('title', this.options[this.selectedIndex].text);
            }
            $('[data-attrname="name"]').val(value);
        });
        $clone.find('[data-attrname="type"]').on('change', function(e) {
            var $this = $(this),
                valInput = $this.val(),
                $attrName = $this.data('attrname');
            $('.name_type').hide();
            $('.' + valInput).show();
            $('.style').hide();
            $('.element_type').hide();
            if (valInput == 'module' || valInput == 'positions') {
                $('.style').show();
            }
            if (valInput == 'element') {
                $('.element_type').show();
            }
            if (valInput == 'component') {
                $('[data-attrname="name"]').val('component');
            }
        });
    });
    //select number columns events
    $(document).on('click', '.t4-layout-column .btn', function(e) {
        var cols = $('.t4-layout-xresize').find('.t4-layout-col').length;
        if ($(this).hasClass('t4-col-add') && (cols < 12)) {
            cols = cols + 1;
        }
        if ($(this).hasClass('t4-col-remove') && cols > 1) {
            cols = cols - 1;
        }
        var $newLayout = T4Layout.layoutBuilder(cols);

        $('.config-section').find('.t4-layout-xresize').html($newLayout);

        $('.config-section').find('.t4-column-options').remove();
        $('.config-section').find('.ui-resizable-handle').remove();
        $('.config-section').find('.t4-layout-col').removeAttr('data-sortableitem');
        $('.config-section').find('.t4-layout-col').addClass('t4-layout-unit');
        var $jposHide = '';
        $('.config-section').find('.t4-layout-col').each(function(index) {
            $(this).find('.t4-column-title').text($(this).data('col'));
            $('.config-section').find('.t4-admin-layout-hiddenpos').find('.pos-hidden').remove();
            $jposHide += '<span class="pos-hidden hide" data-item_vis="' + index + '" title="Click here to show this position on current device layout">' + $(this).data('name') + '</span>';
        });
        $('.config-section').find('.t4-admin-layout-hiddenpos').append($jposHide);
        $('.t4-input-cols').val(cols);
        T4Layout.resizeElement();
    });

    // Save Row Column Settings
    $(document).on('click', '.t4-settings-apply', function(event) {
        event.preventDefault();
        var tplhelperValue = $('#tplhelper').val();
        var flag = $(this).data('flag');
        switch (flag) {
            case 'row-setting':
                $('.t4-modal-row').find('.t4-layout').each(function() {
                    var $this = $(this),
                        cols = '',
                        $parent = $('.row-active'),
                        $attrname = $this.data('attrname');
                    $parent.removeData($attrname);

                    if ($attrname == 'name') {
                        var nameVal = $this.val();

                        if (nameVal == '' || nameVal == null) {
                            $('.row-active .t4-section-title').text('Section');
                        } else {
                            $('.row-active .t4-section-title').text($this.val());
                        }
                    }
                    if ($attrname == 'cols') {
                        cols = $this.val();
                        $parent.data('layout', $this.data('layout'));
                        var $options = {
                            classes: 't4-layout-col',
                            layout: 'column'
                        }
                        T4Layout.layoutArr($options);
                    }
                    if ($attrname == 'padding') {
                        var padding_xl = $this.data('padding_xl') || "",
                            padding_lg = $this.data('padding_lg') || "",
                            padding_md = $this.data('padding_md') || "",
                            padding_sm = $this.data('padding_sm') || "",
                            padding_xs = $this.data('padding_xs') || "";
                        $parent.data('padding_xl', padding_xl);
                        $parent.data('padding_lg', padding_lg);
                        $parent.data('padding_md', padding_md);
                        $parent.data('padding_sm', padding_sm);
                        $parent.data('padding_xs', padding_xs);
                    }
                    if ($attrname == 'margin') {
                        var margin_xl = $this.data('margin_xl') || "",
                            margin_lg = $this.data('margin_lg') || "",
                            margin_md = $this.data('margin_md') || "",
                            margin_sm = $this.data('margin_sm') || "",
                            margin_xs = $this.data('margin_xs') || "";
                        $parent.data('margin_xl', margin_xl);
                        $parent.data('margin_lg', margin_lg);
                        $parent.data('margin_md', margin_md);
                        $parent.data('margin_sm', margin_sm);
                        $parent.data('margin_xs', margin_xs);
                    }
                    $parent.data($attrname, $this.getInputValue());
                });
                break;

            case 'column-setting':
                var component = false;
                $('.t4-modal-col').find('.t4-layout').each(function() {

                    var $this = $(this),
                        $parent = $('.column-active'),
                        $attrname = $this.data('attrname'),
                        dataVal = $this.val();
                    $parent.removeData($attrname);
                    switch ($attrname) {
                        case 'type':
                            if (dataVal == 'component') {
                                dataVal = 'Component';
                            }
                            $('.column-active .t4-column-title').text(dataVal);

                            break;
                        case 'name':
                            if (dataVal == '' || dataVal == undefined) {
                                dataVal = 'none';
                            }
                            $parent.data('title', $(this).data('title'));
                            $parent.data('modname', $(this).data('modname'));
                            $('.column-active .t4-column-title').text(dataVal);

                            break;
                        case 'extra_class':
                            if (dataVal !== '' || dataVal !== undefined) {
                                $parent.addClass(dataVal);
                            }

                            break;
                    }
                    $parent.data($attrname, $this.getInputValue());

                });
                break;

            default:
                T4Admin.messages('You are doing somethings wrongs. Try again', 'error');
        }
        $('.t4-col').removeClass('t4-layout-unit');
        $('.t4-admin-layout-vis').hide();
        $('.t4-col-remove').addClass('hidden');
        $('.row-active').removeClass('row-active');
        $('.column-active').removeClass('column-active');
        // $('.t4-modal-overlay').remove();
        $('.themeConfigModal').children().not('.t4-message-container').hide();
        $('body').removeClass('t4-modal-open');
        var $dataLayout = T4Layout.getGeneratedLayout();
        $('input.t4-layouts').val(JSON.stringify($dataLayout)).trigger('change');
        // T4Admin.t4Ajax($dataLayout,'SaveLayout');
    });
    // Cancel Modal
    $(document).on('click', '.t4-settings-cancel, .action-t4-modal-close', function(event) {
        event.preventDefault();
        // $('.t4-modal-overlay').remove();
        $('.themeConfigModal').children().not('.t4-message-container').hide();
        $('.row-active').removeClass('row-active');
        $('.column-active').removeClass('column-active');
        $('body').removeClass('t4-modal-open');
        $('.row-active').removeClass('row-active');
    });

    // add row
    $(document).on('click', '.t4-add-row', function(event) {
        event.preventDefault();

        var $parent = $(this).closest('.t4-layout-section'),
            $rowClone = $('#t4-layout-section').clone(true);

        $rowClone.addClass('t4-layout-section').removeAttr('id');
        $($rowClone).insertAfter($parent);

        T4Layout.jqueryUiLayout();
    });

    // Remove Row
    $(document).on('click', '.t4-remove-row', function(event) {
        event.preventDefault();
        var $that = $(this);
        T4Admin.Confirm(T4Admin.langs.t4LayoutRowConfirmDel, function(ans) {
            if (ans) {
                $that.closest('.t4-layout-section').slideUp(500, function() {
                    var countSection = $('.t4-layout-builder').find('.t4-layout-section').length;
                    if (countSection == 2) {
                        $that.closest('.t4-layout-section').find('.t4-add-row').trigger('click');
                    }
                    $that.closest('.t4-layout-section').remove();
                    T4Layout.layoutApply();
                    T4Admin.Messages(T4Admin.langs.t4LayoutRowDeleted, 'message');
                });
            } else {
                return false;
            }
        }, '');
    });

    /*Option Group*/
    $(document).on('click', '.nav-item.t4-group', function(event) {
        event.preventDefault();
        var tabContent = $(this).find('a').attr('href'),
            $parents = $(this).closest('.nav-tabs').next('.tab-content');
        $parents.find('.tab-pane.active').removeClass('show active').addClass('fade');
        $parents.find(tabContent).removeClass('fade').addClass('show active');
        $(this).closest('.nav-tabs').find('.nav-item.active').removeClass('active');
        $(this).addClass('active');
    });
    $(document).on('click', '.t4-layout-styles-settings', function(e) {
        $('#imageModal_t4layout_layout_media').appendTo('body');
        $('#imageModal_t4layout_layout_media').css({ 'z-index': '3000' });
    });
    // //init on change media background image
    // $(document).on('click','.button-save-selected', function(e) {
    // 	alert(3);
    //     console.log(this);
    // });
    $(document).on('click', '.t4-admin-layout-vis', function(e) {
        var $device = T4Layout.getDeviceActive();
        var jPos = $('.t4-input-cols').data('pos_' + $device) | '';
        var $datashow = $(this).data('idx');
        $('.row-active').find('.t4-layout-col').each(function(index) {
            if (index == $datashow) {
                $(this).data('hidden_' + $device, true);
            }
        });
        $(this).parents('.t4-layout-col').data('hidden_' + $device, true);
        if (jPos) {
            jPos += ',' + $datashow;
        } else {
            jPos = $datashow;
        }
        var $col = $(this).parents('.t4-layout-col');
        $col.addClass('pos-hidden-' + $device);
        $('.config-section').find('[data-item_vis="' + $datashow + '"]').removeClass('hide');
        $('.t4-input-cols').data('pos_' + $device, jPos);
        if (!$('.t4-admin-layout-hiddenpos').hasClass('haspos')) {
            $('.t4-admin-layout-hiddenpos').addClass('haspos');
        }
    });
    $(document).on('click', '.t4-admin-layout-hiddenpos .pos-hidden', function() {
        var idx = $(this).data('item_vis');
        var $device = T4Layout.getDeviceActive();

        $(this).addClass('hide');
        $('.config-section').find('[data-idx="' + idx + '"]').parents('.t4-layout-col').removeClass('pos-hidden-' + $device);
        var jPos = $('.config-section').find('[data-idx="' + idx + '"]').parents('.t4-layout-col').data('hidden_' + $device) | '';
        var $jPosShow = [];
        isNaN(jPos) ? $jPosShow.concat(jPos.split(',')) : $jPosShow.push(jPos);
        if ($jPosShow.indexOf(idx)) {
            var dataPos = $jPosShow.filter(function(value) { return value != idx });
            if (isNaN(dataPos)) {
                var dataPosTostr = dataPos.join(',');
            } else {
                dataPosTostr = dataPos;
            }
        }
        $('.config-section').find('[data-idx="' + idx + '"]').parents('.t4-layout-col').data('hidden_' + $device, '');
        $('.row-active').find('.t4-layout-col').each(function(index) {
            if (idx == index) {
                $(this).data('hidden_' + $device, '');
            }
        });
        var countCol = $('.config-section').find('.t4-layout-col').length,
            countPos = $('.t4-admin-layout-hiddenpos').find('.hide').length;
        if (countCol == countPos) {
            $('.t4-admin-layout-hiddenpos').removeClass('haspos');
        }
    });
    $(document).on('click', '.t4-admin-dv-auto', function(e) {
        $device = T4Layout.getDeviceActive();
        if ($device != 'def') {
            $('.config-section').find('.t4-layout-col').each(function() {
                $(this).data($device, 'auto');
            });
        } else {
            $('.config-section').find('.t4-layout-col').each(function() {
                $(this).data('col', 'auto');
            });
        }
        $('.config-section').find('.t4-admin-layout-devices').find('[data-device="' + $device + '"]').click();
    });
    $(document).on('click', '.t4-admin-dv-reset', function(e) {
        $device = T4Layout.getDeviceActive();
        if ($device != 'def') {
            $('.config-section').find('.t4-layout-col').each(function() {
                $(this).data($device, $(this).data('col'));
            });
        } else {
            var old_layout = $('.t4-input-cols').data('old_layout');
            if (typeof old_layout != 'undefined') {
                old_layout = old_layout.split('+');
            }
            $('.config-section').find('.t4-layout-col').each(function(idx) {
                $(this).data('col', old_layout[idx]);
            });
        }
        $('.config-section').find('.t4-admin-layout-devices').find('[data-device="' + $device + '"]').click();

    });
    $(document).on('click', '.t4-admin-dv-clear, .t4-admin-dv-none', function(e) {
        $device = T4Layout.getDeviceActive();
        if ($device != 'def') {
            $('.config-section').find('.t4-layout-col').each(function() {
                $(this).data($device, 'none');
            });
            $('.config-section').find('.t4-admin-layout-devices').find('[data-device="' + $device + '"]').click();
        }
    });
    $('body').on('click', '.t4-layout-styles-settings', function() {
        $('.t4-modal.t4-row-setting').addClass('hidden');
    });
    $('body').on('hidden.bs.modal', '.modal', function() {
        if ($('.t4-modal.t4-row-setting').hasClass('hidden')) {
            $('.t4-modal.t4-row-setting').removeClass('hidden');
        }

    });
    $('body').on('click', '.t4-col-remove', function(e) {
        var parentsCol = $(this).parents('.t4-layout-xresize');
        var lastCol = parentsCol.find('.t4-layout-col').length;
        if (lastCol == 1) {
            T4Admin.Messages('cant remove last column!!!!', 'error');
        } else {
            var elemChange = '';
            var removeColClass = function(el) {
                el.removeClass(function(index, cName) {
                    return (cName.match(/(^|\s)col-\S+/g) || []).join(' ');
                });
                el.removeClass('col');
            };
            var hasPoss = parentsCol.next('.t4-admin-layout-hiddenpos');
            var checkColIdx = parentsCol.find('.t4-layout-col').toArray();
            var colIdx = checkColIdx.indexOf($(this).parents('.t4-layout-col')[0]);
            if (checkColIdx.length - 1 == colIdx) {
                elemChange = $(checkColIdx[colIdx - 1]);
            } else {
                elemChange = $(checkColIdx[colIdx + 1]);
            }
            if (checkColIdx.length == 2) {
                elemChange.find('.t4-col-remove').addClass('hidden');
            }
            removeColClass(elemChange);
            elemChange.addClass('col-md');
            elemChange.data('col', 'auto');
            elemChange.find('.t4-column-title').text('auto');
            var posIdx = $(this).parents('.t4-layout-col').data('idx');
            hasPoss.find('[data-item_vis="' + posIdx + '"]').remove();
            $(this).parents('.t4-layout-col').remove();
        }
    });
    $(document).on('click', '.t4-admin-layout-structur', function(e) {
        $(this).addClass('active');
        $('.t4-admin-responsive').removeClass('active');
        $('.t4-row-settings').find('.t4_padding,.t4_margin').hide();
        $('.t4-row-settings').find('.t4_extra_class,.t4_layout_sticky').show();
        $('.t4-layout-column').show();
        $('.t4-layout-xresize .t4-col-remove').removeClass('hidden');
        $('.t4-admin-layout-devices , .t4-admin-dv-clear,.t4-admin-layout-vis').hide();
        $('.t4-admin-dv-def').click();

    });
    $(document).on('click', '.t4-admin-responsive', function(e) {
        $(this).addClass('active');
        $('.t4-admin-layout-structur').removeClass('active');
        $('.t4-layout-column').hide();
        $('.t4-col-remove').addClass('hidden');
        $('.t4-admin-layout-devices , .t4-admin-dv-clear').show();
        $('.config-section').find('.t4-admin-layout-vis').show();
        $('.t4-row-settings').find('.t4_padding,.t4_margin').show();
        $('.t4-row-settings').find('.t4_extra_class,.t4_layout_sticky').hide();
        $('.t4-admin-dv-xl').click();
        var device = T4Layout.getDeviceActive();
        T4Layout.initPaddingMarginValue(device);

    });
    // update padding of Device responsive
    $(document).on('keyup', "#t4layout_padding", function(e) {
        var device = T4Layout.getDeviceActive();
        if (device == 'def') device = 'xl';
        var paddingData = $(this).val();
        $('#t4layout_padding').data('padding_' + device, paddingData);
    })
    // update margin of Device responsive
    $(document).on('keyup', "#t4layout_margin", function(e) {
        var device = T4Layout.getDeviceActive();
        if (device == 'def') device = 'xl';
        var marginData = $(this).val();
        $('#t4layout_margin').data('margin_' + device, marginData);
    })
    //init opacity
    $(document).on('change', 'input#opacityVal', function(e) {
        var $valueOp = $(this).val(),
            $flex = $valueOp * 100;
        if (0 <= parseInt($valueOp) && parseInt($valueOp) <= 1) {
            $('#t4layout_opacity').val($(this).val());
            $('.slider-bg-lower').css({ 'width': $flex + '%' });
        }
    });
    $(document).on('change mousemove', '#t4layout_opacity', function(e) {
        $('#opacityVal').val($(this).val());
        var $valueOp = $(this).val(),
            $flex = $valueOp * 100;
        $('.slider-bg-lower').css({ 'width': $flex + '%' });
    });
    //init overlay type change
    $(document).on('change', '.overlay_type', function(e) {
        var $overlay_type = $(this).find('.t4-layout').getInputValue();
        $('body').find('.control-group.image_type,.control-group.video_type,.control-group.file_type').hide();
        if ($overlay_type != '') {
            $('.control-group.' + $overlay_type + '_type').show();
            $('.opacity').show();
        }
        T4Layout.initOverlayReadonly($overlay_type);
    });
    $(document).on('change', '.video_type input, .image_type .field-media-input, .file_type input', function(e) {
        var $overlay_type = $('.overlay_type').find('.t4-layout').getInputValue();
        T4Layout.initOverlayReadonly($overlay_type);
    });

    //edit block
    $('.t4-btn[data-action="block.edit"]').click(function() {
        $('.t4-edit-block-remove').show();
        $('.t4-block-name').hide();
        // load current block
        var blockname = $('[name="t4layoutcol[block]"]').val();
        if (!blockname) {
            T4Admin.Messages('You have not select block to edit!~!', 'error');
            return false;
        }
        $('.t4-block-name').find('input').val(blockname);
        var url = location.pathname + '?option=com_ajax&plugin=t4&format=html&t4do=getblock&id=' + tempId;
        $.post(url, { 'block': blockname }).then(function(data) {
            // Show edit popup with current block
            var $data = data['data'],
                $posLocal = data['pos'];
            $('#t4layoutcol_edit_block').text($data);
            var textArea = $('#t4layoutcol_edit_block').get(0);
            var theme = $('#attrib-themeConfig.dark').length ? 'monokai' : 'default';
            
            if (!block_editors) {
                block_editors = CodeMirror.fromTextArea(textArea, {
                    lineNumbers: true,
                    mode: "htmlmixed",
                    extraKeys: { "Ctrl-Space": "autocomplete" },
                    autoBeautify: true,
                    styleActiveLine: true,
                    matchBrackets: true,
                    theme: theme,
                    highlightNonStandardPropertyKeywords: true,
                    autofocus: true,
                    tabsize: 2,
                    direction: (document.dir == 'rtl') ? "rtl" : "ltr",
                    firstLineNumber: 1
                });
            } else {
                block_editors.setOption("theme", theme);
                block_editors.getDoc().setValue($data);
            }
            setTimeout(function() {
                block_editors.refresh();
            }, 1);
            if ($posLocal == 'ovr') {
                $('.t4-edit-block-remove').html('<span class="fal fa-undo"></span> Restore Default');
                $('.t4-edit-block-remove').data('local', 'ovr');
            } else if ($posLocal == 'loc') {
                $('.t4-edit-block-remove').html('<span class="fal fa-trash-alt"></span>Delete Block');
                $('.t4-edit-block-remove').data('local', 'loc');
            } else {
                $('.t4-edit-block-remove').hide();
            }
            hideColModalSettings('edit');
        })
    });
    // action add block
    $('.t4-btn[data-action="block.add"]').click(function() {
        if ($('.control-group.t4-block-name').find('.alert.alert-warning').length) {
            $('.control-group.t4-block-name').find('.alert.alert-warning').remove();
            $('.t4-edit-block-footer').removeClass('disabled');
        }
        $('.t4-edit-block-remove').hide();
        $('.t4-block-name').find('input').val('');
        $('.t4-block-name').show();
        $('#t4layoutcol_edit_block').text('');
        var textArea = $('#t4layoutcol_edit_block').get(0);
        var theme = $('#attrib-themeConfig.dark').length ? 'monokai' : 'default';
        
        if (!block_editors) {
            block_editors = CodeMirror.fromTextArea(textArea, {
                lineNumbers: true,
                mode: "htmlmixed",
                extraKeys: { "Ctrl-Space": "autocomplete" },
                autoBeautify: true,
                styleActiveLine: true,
                matchBrackets: true,
                theme: theme,
                highlightNonStandardPropertyKeywords: true,
                autofocus: true,
                tabsize: 2,
                direction: (document.dir == 'rtl') ? "rtl" : "ltr",
                firstLineNumber: 1
            });
        }
        block_editors.setOption("theme", theme);
        block_editors.getDoc().setValue('<!-- html in here -->');
        setTimeout(function() {
            block_editors.refresh();
        }, 1);
        hideColModalSettings('add');
    });
    var hideColModalSettings = function($action) {
        if ($action == 'edit') {
            $('.t4-cols-setting').find('.t4-edit-block-title').html('<i class="fal fa-cog"></i> Edit block').show();
        } else if ($action == 'add') {
            $('.t4-cols-setting').find('.t4-edit-block-title').html('<i class="fal fa-cog"></i> Add new block').show();
        }
        var tab_editblock = $('.t4-modal-col').find('#editblock');
        $('.t4-cols-setting').find('.t4-modal-footer, .t4-modal-header-title, .action-t4-modal-close').hide();
        $('.t4-modal-col').find('#general').removeClass('show active').addClass('fade');
        tab_editblock.removeClass('fade').addClass('show active');
        $('.t4-cols-setting').find('.t4-edit-block-footer, .t4-edit-block-title, .t4-modal-block-close').show();
    }
    var showColModalSettings = function() {
        $('.t4-modal-col').find('#editblock').removeClass('show active').addClass('fade');
        $('.t4-modal-col').find('#general').removeClass('fade').addClass('show active');
        $('.t4-cols-setting').find('.t4-modal-footer, .t4-modal-header-title, .action-t4-modal-close').show();
        $('.t4-cols-setting').find('.t4-edit-block-footer, .t4-edit-block-title, .t4-modal-block-close').hide();
    }
    //cancel edit block
    $('body').on('click', '.t4-edit-block-cancel', function(e) {
        showColModalSettings();
        if ($('.control-group.t4-block-name').find('.alert.alert-warning').length) {
            $('.control-group.t4-block-name').find('.alert.alert-warning').remove();
            $('.t4-edit-block-footer').removeClass('disabled');
        }
    });
    //save edit or new block
    $('body').on('click', '.t4-edit-block-save', function(e) {
        e.preventDefault();
        var blockname = $("#t4layoutcol_new_block").val(),
            blName = blockname.replace(/\s+/g, '_').toLowerCase();
        if (!blockname) {
            T4Admin.Messages(T4Admin.langs.T4BlockNameNone, 'error');
            return false;
        }
        var block = block_editors.getDoc().getValue('\n');
        T4Layout.saveBlock(blName, block);
    });
    // remove block
    $('body').on('click', '.t4-edit-block-remove', function(e) {
        var localFile = $('.t4-edit-block-remove').data('local');
        e.preventDefault();
        T4Admin.Confirm('Are you sure?', function(ans) {
            if (ans) {
                var deleteFile = location.pathname + '?option=com_ajax&plugin=t4&format=html&t4do=removeblock&id=' + tempId;
                var blockname = $("#t4layoutcol_new_block").val();
                $.post(deleteFile, { 'block': blockname }).then(function(data) {
                    if (localFile == 'loc') {
                        var $selectBox = document.getElementById('t4layoutcol_block'),
                            $selected = $selectBox.selectedIndex;
                        $selectBox.remove($selected);
                        $('#t4layoutcol_block').val('').trigger('liszt:updated');
                        $('.t4-modal-col').find('#editblock').removeClass('show active').addClass('fade');
                        $('.t4-modal-col').find('#general').removeClass('fade').addClass('show active');
                        $('.t4-cols-setting').find('.t4-modal-footer, .t4-modal-header-title, .action-t4-modal-close').show();
                        $('.t4-cols-setting').find('.t4-edit-block-footer,.t4-edit-block-title').hide();
                    } else {
                        var $data = data['data'];
                        block_editors.getDoc().setValue($data);
                        setTimeout(function() {
                            block_editors.refresh();
                        }, 1);
                        $('.t4-edit-block-remove').hide();
                    }
                });
            } else {
                return false;
            }
        }, '');
    });
    //close edit or new block
    $(document).on('click', '.t4-modal-block-close', function(e) {
        $('.t4-edit-block-cancel').trigger('click');
    });
    //check exist name block for create new block
    $(document).on('change', "#t4layoutcol_new_block", function(e) {
        var newName = $(this).val(),
            $checkName = $('#t4layoutcol_block').find('option[value="' + newName + '"]').length;
        if ($checkName) {
            $('.control-group.t4-block-name').find('.control-group-inner').append('<div class="alert alert-warning">The name block has exist!</div>');
            $('.t4-edit-block-footer').addClass('disabled');
        } else {
            if ($('.control-group.t4-block-name').find('.alert.alert-warning').length) {
                $('.control-group.t4-block-name').find('.alert.alert-warning').remove();
                $('.t4-edit-block-footer').removeClass('disabled');
            }
        }
    });

    //check exist name section
    $(document).on('change', '#t4layout_name', function(e) {
        var $val = $(this).val(),
            $name_section = [];
        var $btn = $(this);
        $('.t4-layout-builder').find('.t4-layout-section').each(function() {
            $name_section.push($(this).data('name'));
        });
        if ($name_section.indexOf($val) != '-1') {
            $(this).closest('.t4-modal').find(".t4-modal-footer").addClass('disabled');
            $(this).closest('.control-group-inner').append('<div class="alert alert-warning">The name section has exist!</div>');
        } else {
            $(this).parents('.control-group-inner').find('.alert-warning').remove();
            $(this).closest('.t4-modal').find(".t4-modal-footer").removeClass('disabled');
        }
    });
    // select palettes on section
    $(document).on('click', '.t4-layout-palettes .pattern', function(e) {
        e.preventDefault();
        e.stopPropagation();
        T4Layout.updatePaletteColor();
        $('.pattern.active').removeClass('active');
        $(this).addClass('active');
        var $data = $(this).data();
        if($(this).hasClass('none')){
            $data.background_color = $('#typelist_theme_body_bg_color').val();
            $data.text_color = $('#typelist_theme_body_text_color').val();
            $data.link_color = $('#typelist_theme_body_link_color').val();
            $data.link_hover_color = $('#typelist_theme_body_link_hover_color').val();
        }
        $('.t4-input-color_pattern').val($data.class);
        //init preview
        var prewviewEl = $('.pl-preview__input');
        var palettePreEl = $('.pattern-preview');
        Object.keys($data).forEach( function(name, index) {
            if(name == 'background_color'){
             $('.pattern-preview').css({background:$data.background_color});
             }else{
                $('.pattern-preview .'+name).data(name,$data[name]);
                $('.pattern-preview .'+name).css({color:$data[name]});
                if(name.match(/_hover/)){
                    $('.pattern-preview .'+name.replace(/_hover/,"")).data(name,$data[name]);
                    $('.pattern-preview .'+name.replace(/_hover/,"")).hover(function(){
                        var colorArr = $(this).data();
                        $(this).css("color", colorArr[name]);
                    }, function(){
                            var colorArr = $(this).data();
                        $(this).css("color", colorArr[name.replace(/_hover/,"")]);
                    });
                }
             }
        });
        prewviewEl.find('.t4-palette-color-spec').each(function(){
            var name = $(this).attr('name');
            $(this).val($data[name]);
            $(this).trigger('change');
        });
    });

    $(document).on('click', '.t4-admin-layout-devices .btn', function(e) {
        var $elem = $(this);
        $('.t4-admin-layout-devices .active').removeClass('active');
        $elem.addClass('active');
        var $device = $elem.data('device');
        $('.t4-layout-reset .btn').hide();
        T4Layout.initPaddingMarginValue($device);
        if ($device != 'def') {
            $('.config-section').find('.t4-admin-layout-vis').show();
            $('.config-section').find('.t4-layout-col').each(function(idx) {
                T4Layout.posHide($(this), idx, $device);
            })
        } else {
            $('.config-section').find('.t4-admin-layout-vis').hide();
            $('.config-section').find('.t4-admin-layout-hiddenpos .pos-hidden').addClass('hide');
        }
        if ($device == 'xs') {
            $('.t4-admin-dv-auto').show();
            $('.t4-admin-dv-none').show();
        } else if ($device == 'xl' || $device == 'lg' || $device == 'md') {
            $('.t4-admin-dv-reset').show();
            $('.t4-admin-dv-auto').show();
        } else {
            $('.t4-admin-dv-auto').show();
        }
        $elem.parents('.config-section').removeClass('md def xl lg sm xs').addClass($device);
        $elem.parents('.config-section').find('.t4-layout-col').data('device', $device);
        $elem.parents('.config-section').find('.t4-layout-col').each(function(idx) {
            var $t4Col = $(this),
                classCol = 'col-' + $device,
                $colLayout = $t4Col.data($device);

            if (typeof $colLayout == 'undefined') $colLayout = 'auto';

            if ($device == 'xs') classCol = 'col';

            if (($device == 'sm' || $device == 'xs') && $colLayout == '') {
                $colLayout = 'auto';
            }
            if ($device == 'lg' || $device == 'xl' || $device == 'md') {
                if ($colLayout == '') {
                    $colLayout = $t4Col.data('col');
                }
            }
            if ($colLayout && $colLayout != 'none' && $colLayout != 'auto') {
                classCol = 'col-' + $device + '-' + $colLayout;
                if ($device == 'xs') classCol = 'col-' + $colLayout;
            }
            $t4Col.removeClass(function(index, className) {
                return (className.match(/(^|\s)col-\S+/g) || []).join(' ');
            });
            $t4Col.removeClass('col');
            if ($device == 'def') {
                if ($device == 'def') $deviceDefault = 'md';
                var dataCol = $t4Col.data('col');
                if (dataCol == 'auto') {
                    $t4Col.addClass('col-' + $deviceDefault);
                } else {
                    $t4Col.addClass('col-' + $deviceDefault + '-' + dataCol);
                }
                $t4Col.find('.t4-column-title').text(dataCol);
            } else {
                $t4Col.addClass(classCol);
                if ($colLayout && $colLayout != 'none') {
                    $t4Col.find('.t4-column-title').text($colLayout);
                } else if ($colLayout == '') {
                    $t4Col.find('.t4-column-title').text('auto');
                } else {
                    $t4Col.find('.t4-column-title').text('none');
                }
            }

        });
        var countCol = $('.config-section').find('.t4-layout-col').length,
            countPos = $('.t4-admin-layout-hiddenpos').find('.hide').length;
        if (countCol != countPos) {
            $('.t4-admin-layout-hiddenpos').addClass('haspos');
        } else {
            $('.t4-admin-layout-hiddenpos').removeClass('haspos');
        }
    });
    // block custom css
    $(document).on('click', '.t4-settings-block-css', function(e) {
        var theme = $('#attrib-themeConfig.dark').length ? 'monokai' : 'default';
        var $cssModal = $(document).find('.t4-block-css-modal');
        if ($cssModal.length && !$cssModal.parents().is('.themeConfigModal')) $cssModal.appendTo('.themeConfigModal');
        var blockName = $('#t4layout_name').val().replace(/\s/g, '-').toLowerCase();
        // load current custom css
        var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=blockcss&id=' + tempId;
        $.post(url, { task: 'getcss', name: blockName }).then(function(css) {
            // Show edit popup with current css
            $('body').addClass('t4-modal-open');
            var $rowSettings = $('.t4-row-settings');
            $rowSettings.hide();
            $('#t4_block_css').text(css);
            var textArea = $('#t4_block_css').get(0);
            if (!block_css_editor) {
                block_css_editor = CodeMirror.fromTextArea(textArea, {
                    lineNumbers: true,
                    mode: "css",
                    extraKeys: { "Ctrl-Space": "autocomplete" },
                    autoBeautify: true,
                    styleActiveLine: true,
                    matchBrackets: true,
                    theme: theme,
                    highlightNonStandardPropertyKeywords: true,
                    autofocus: true,
                    tabsize: 2,
                    direction: (document.dir == 'rtl') ? "rtl" : "ltr",
                    firstLineNumber: 1
                });
            } else {
                block_css_editor.setOption("theme", theme);
                block_css_editor.getDoc().setValue(css);
            }
            setTimeout(function() {
                block_css_editor.refresh();
            }, 1);

            $cssModal.show();

        })

    });
    $(document).on('click', '.block-css-editor-apply', function(e) {
        e.preventDefault();
        var scss = block_css_editor.getDoc().getValue("\n");
        var blockName = $('#t4layout_name').val().replace(/\s/g, '-').toLowerCase();
        T4Admin.Messages('Saving & Compiling ...', 'status');
        // load current custom css
        var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=blockcss&id=' + tempId;
        $.post(url, { task: 'save', name: blockName, blockcss: scss }).then(function(data) {
            if (data.error) {
                T4Admin.Messages(data.error, 'error');
            } else {
                $('.t4-block-css-modal').hide();
                $('.t4-row-settings').show();
                T4Admin.Messages('Save & compile successfully!');

            }
        })


    });
    $(document).on('click', '.t4-block-css-cancel', function(e) {
        e.preventDefault();
        $('.t4-block-css-modal').hide();
        $('.t4-row-settings').show();
    });
});
var T4Layout = window.t4Layout || {};
! function($) {
    $.extend(T4Layout, {
        //init padding or margin
        initPaddingMarginValue: function(device) {

            if (device == 'def') device = 'xl';
            // update title padding
            var paddingTitle = $('.t4_padding').find("#t4layout_padding-lbl").text();
            var tooltip = '<span class="hasTooltip fal fa-question-circle" data-original-title="" title=""></span>';
            paddingTitle.trim();
            paddingTitle = paddingTitle.replace(/(\(xl\)|\(lg\)|\(md\)|\(xs\)|\(sm\))/g, "").trim();
            $('.t4_padding').find("#t4layout_padding-lbl").html(paddingTitle + '(' + device + ') ' + tooltip);

            // update title margin
            var marginTitle = $('.t4_margin').find("#t4layout_margin-lbl").text();
            var tooltip = '<span class="hasTooltip fal fa-question-circle" data-original-title="" title=""></span>';
            marginTitle.trim();
            marginTitle = marginTitle.replace(/(\(xl\)|\(lg\)|\(md\)|\(xs\)|\(sm\))/g, "").trim();
            $('.t4_margin').find("#t4layout_margin-lbl").html(marginTitle + '(' + device + ') ' + tooltip);
            // update value margin
            var paddingData = $("#t4layout_padding").data('padding_' + device) || "";
            $("#t4layout_padding").val(paddingData);
            // update value margin
            var marginData = $("#t4layout_margin").data('margin_' + device) || "";
            $("#t4layout_margin").val(marginData);
        },
        initDataPaddingandMargin(dataSection) {
            $('#t4layout_padding').data('padding_xl', dataSection['padding_xl'] ? dataSection['padding_xl'] : "");
            $('#t4layout_padding').data('padding_lg', dataSection['padding_lg'] ? dataSection['padding_lg'] : "");
            $('#t4layout_padding').data('padding_md', dataSection['padding_md'] ? dataSection['padding_md'] : "");
            $('#t4layout_padding').data('padding_sm', dataSection['padding_sm'] ? dataSection['padding_sm'] : "");
            $('#t4layout_padding').data('padding_xs', dataSection['padding_xs'] ? dataSection['padding_xs'] : "");
            $('#t4layout_margin').data('margin_xl', dataSection['margin_xl'] ? dataSection['margin_xl'] : "");
            $('#t4layout_margin').data('margin_lg', dataSection['margin_lg'] ? dataSection['margin_lg'] : "");
            $('#t4layout_margin').data('margin_md', dataSection['margin_md'] ? dataSection['margin_md'] : "");
            $('#t4layout_margin').data('margin_sm', dataSection['margin_sm'] ? dataSection['margin_sm'] : "");
            $('#t4layout_margin').data('margin_xs', dataSection['margin_xs'] ? dataSection['margin_xs'] : "");
        },
        // Column Layout Arrange
        layoutArr: function(options) {
            var col = [],
                colNew = [],
                $gparent = $('.t4-layout-section.row-active'),
                colAttr = [],
                newLayout = [];
            $gparent.find('.' + options.classes).each(function(i, val) {
                col[i] = $(this);

            });
            $('.config-section').find('.' + options.classes).each(function(i, val) {
                newLayout.push($(this).data('col'));
                var colData = $(this).data();
                if (typeof colData == 'object') {
                    colAttr[i] = colData;
                } else {
                    colAttr[i] = {};
                }
                var idx = $(this).find('.t4-admin-layout-vis').data('idx');
                colNew.push(idx);
            });
            var new_item = '';
            for (var i = 0; i < newLayout.length; i++) {
                if (typeof colAttr[i] != 'object') {
                    colAttr[i] = {
                        col: newLayout[i],
                        type: 'row',
                        name: 'none'
                    }
                } else {
                    colAttr[i].col = newLayout[i];
                }
                var dataAttr = '';
                $.each(colAttr[i], function(index, value) {
                    dataAttr += ' data-' + index + '="' + $.fn.htmlspecialchars(value) + '"';
                });
                if (newLayout[i] == 'auto') {
                    $cls = 'col-md';
                } else {
                    $cls = 'col-md-' + newLayout[i];
                }
                new_item += '<div class="t4-col ' + options.classes + ' ' + $cls + '" ' + dataAttr + '>';
                if (!colAttr[i].name) colAttr[i].name = 'none';
                if (colAttr[i].type == 'component') colAttr[i].name = 'Component';
                new_item += '<div class="col-inner clearfix">';
                new_item += '<span class="t4-column-title">' + colAttr[i].name + '</span>';
                new_item += '<span class="t4-col-remove" title="Remove column" data-content="Remove column"><i class="fal fa-minus"></i> </span>';
                new_item += '<span class="t4-admin-layout-vis" data-idx="' + i + '" title="Click here to hide this position on current device layout" style="display:none;"><i class="fal fa-eye"></i></span>';
                new_item += '<a class="t4-' + options.layout + '-options " href="#"><i class="fal fa-cog fa-fw"></i></a>';
                new_item += '</div>';
                new_item += '</div>';
            }
            $old_column = $gparent.find('.' + options.classes);
            $old_column.remove();
            $gparent.find('.row.ui-sortable').append(new_item);
            $gparent.data('layout', newLayout.join('+'));
            T4Layout.jqueryUiLayout();

        },
        saveBlock: function(block, data, task) {
            var url = location.pathname + '?option=com_ajax&plugin=t4&format=html&t4do=saveblock&id=' + tempId;
            $.post(url, { name: block, data: data }).then(function() {
                // Save done, show message and reload preview
                //check edit block or add new block
                var $checkName = $('#t4layoutcol_block').find('option[value="' + block + '"]').length;
                if (!$checkName) {
                    var $option = "<option value='" + block + "'>" + block + "</option>";
                    $('#t4layoutcol_block').append($option);
                    $('#t4layoutcol_block').val(block).trigger("liszt:updated");
                    $('#t4layoutcol_block').trigger("change");
                }
                $('.t4-modal-col').find('#editblock').removeClass('show active').addClass('fade');
                $('.t4-modal-col').find('#general').removeClass('fade').addClass('show active');
                $('.t4-cols-setting').find('.t4-modal-footer, .t4-modal-header-title, .action-t4-modal-close').show();
                $('.t4-cols-setting').find('.t4-edit-block-footer, .t4-edit-block-title, .t4-modal-block-close').hide();
                T4Admin.Messages(T4Admin.langs.T4LayoutSaveBlock, 'message');
            })
        },
        // Column Layout Arrange
        layoutBuilder: function(cols) {

            var col = [],
                $cls = '',
                $gparent = $('.config-section');
            colAttr = [];
            $gparent.find('.t4-layout-col').each(function(i, val) {

                col[i] = $(this).html();
                var colData = $(this).data();

                if (typeof colData == 'object') {
                    colAttr[i] = $(this).data();
                } else {
                    colAttr[i] = '';
                }
            });

            var new_item = '';
            for (var i = 0; i < cols; i++) {
                var dataAttr = '';
                if (typeof colAttr[i] != 'object') {
                    colAttr[i] = {
                        col: 'auto',
                        type: 'row',
                        name: 'none',
                        idx: i
                    }
                } else {
                    colAttr[i].col = 'auto';
                }
                $.each(colAttr[i], function(index, value) {
                    dataAttr += ' data-' + index + '="' + value + '"';
                });

                new_item += '<div class="t4-col t4-layout-col col-md" ' + dataAttr + '>';
                if (col[i]) {
                    new_item += col[i];
                } else {
                    new_item += '<div class="col-inner clearfix">';
                    new_item += '<span class="t4-column-title">none</span>';

                    new_item += '<span class="t4-col-remove" title="Remove column" data-content="Remove column"><i class="fal fa-minus"></i> </span>';
                    new_item += '<span class="t4-admin-layout-vis" data-idx="' + i + '" title="Click here to hide this position on current device layout" style="display:none;"><i class="fal fa-eye"></i></span>';
                    new_item += '<a class="t4-column-options" href="#"><i class="fal fa-cogs"></i></a>';
                    new_item += '</div>';
                    var pos_hide = $("<span class='pos-hidden hide' data-item_vis='" + i + "' title='Click here to show this position on current device layout'/>").append('none');
                    $('.t4-admin-layout-hiddenpos').append(pos_hide);
                }
                new_item += '</div>';
            }
            return new_item;
        },

        layoutApply: function() {
            var $dataLayout = T4Layout.getGeneratedLayout();
            $('.t4-layouts').val(JSON.stringify($dataLayout)).trigger('change');
            // T4Admin.t4Ajax($dataLayout,'SaveLayout');
        },
        jqueryUiLayout: function() {
            $("#t4-layout-builder").sortable({
                placeholder: "ui-state-highlight",
                forcePlaceholderSize: true,
                axis: 'y',
                opacity: 0.8,
                tolerance: 'pointer',
                stop: function(event, ui) {
                    T4Layout.layoutApply();
                },
            }).disableSelection();

            $('.t4-layout-section').find('.row').rowSortable('layout');
        },
        // Generate Layout JSON
        getGeneratedLayout: function() {
            var item = [];
            $('#t4-layout-builder').find('.t4-layout-section').each(function(index) {
                var $row = $(this),
                    rowIndex = index,
                    rowObj = $row.data();
                var padding_responsive = (typeof rowObj.padding_responsive == 'object') ? JSON.stringify(rowObj.padding_responsive) : rowObj.padding_responsive;
                var margin_responsive = (typeof rowObj.margin_responsive == 'object') ? JSON.stringify(rowObj.margin_responsive) : rowObj.margin_responsive;
                item[rowIndex] = $.extend({
                    'type': 'row',
                    'contents': [],
                }, rowObj);
                delete item[rowIndex].sortableItem;
                delete item[rowIndex].uiresizable;
                // Find Column Elements
                $row.find('.t4-layout-col').each(function(index) {
                    var $column = $(this),
                        colIndex = index,
                        colObj = $column.data();
                    delete colObj.sortableitem;
                    delete colObj.idx;
                    $column.data('idx', colIndex);
                    item[rowIndex].contents[colIndex] = $.extend({
                        'idx': colIndex,
                        'type': colObj.type,
                        'name': colObj.name,
                        'col': colObj.col,
                        'xl': colObj.xl,
                        'lg': colObj.lg,
                        'md': colObj.md,
                        'sm': colObj.sm,
                        'xs': colObj.xs,
                        'hidden_lg': colObj.hidden_lg,
                        'hidden_xl': colObj.hidden_xl,
                        'hidden_md': colObj.hidden_md,
                        'hidden_sm': colObj.hidden_sm,
                        'hidden_xs': colObj.hidden_xs,
                        'style': colObj.style,
                        'extra_class': colObj.extra_class,
                        'extra_params': $.fn.htmlspecialchars_decode(colObj.extra_params),
                    }, colObj);
                    if (colObj.type == 'module') {
                        item[rowIndex].contents[colIndex].title = colObj.title;
                        item[rowIndex].contents[colIndex].modname = colObj.modname;
                    }
                    delete item[rowIndex].contents[colIndex].sortableItem;
                    delete item[rowIndex].contents[colIndex].sortableitem;
                    delete item[rowIndex].contents[colIndex].uiresizable;


                });
            });
            var layout = { 'sections': item, 'settings': { "assets": {}, "fonts": {} } };
            return layout;
        },
        getDeviceActive: function() {
            var $device = $('.t4-admin-layout-devices .active').data('device');
            if (['xl', 'lg', 'md', 'sm', 'xs'].indexOf($device) != '-1') {
                return $device;
            }
            return 'def';
        },
        posHide: function($t4Col, index, $device) {
            var $colData = $t4Col.data(),
                hideDevice = 'hidden_' + $device;
            var pos_col = $colData[hideDevice];
            if (typeof pos_col == 'undefined') {
                $t4Col.data('hidden_' + $device, '');
                pos_col = '';
            }
            if (pos_col) {
                $t4Col.addClass('pos-hidden-' + $device);
                $('.config-section').find('[data-item_vis="' + index + '"]').removeClass('hide');
            } else {
                $('.config-section').find('[data-item_vis="' + index + '"]').addClass('hide');

            }
        },
        t4Container: function($element) {
            $element.on('click', function() {
                $('.fuildwidth .active').removeClass('active');
                $(this).addClass('active');
                var $container = $(this).data('container');
                $('.t4-layout-container').val($container);
            });

        },
        //resizableEl
        resizeElement: function() {
            //resizable clone section
            var resizableEl = $('.t4-layout-col.t4-layout-unit'),
                device = T4Layout.getDeviceActive(),
                columns = 12,
                SecLayout = '',
                fullWidth = resizableEl.parent().width(),
                sibTotalWidth,
                columnWidth = fullWidth / columns,
                totalCol, // this is filled by start event handler
                updateClass = function(el, col, dv) {
                    if (dv == 'def' || dv == 'xs') {
                        el.addClass('col-' + col);
                    }
                    el.addClass('col-' + dv + '-' + col);
                    var $idx = el.data('idx');
                    var layoutCol = $('.config-section').find('.t4-layout-col');
                    layoutCol.each(function(idx) {
                        if ($idx == idx) {
                            if (dv != 'def') {
                                $(this).data(dv, col);
                            } else {
                                $(this).data('col', col);
                            }
                        }
                    });
                    if (dv != 'def') {
                        el.data(dv, col);
                    } else {
                        el.data('col', col);
                    }
                },
                removeCol = function(el) {
                    el.removeClass(function(index, cName) {
                        return (cName.match(/(^|\s)col-\S+/g) || []).join(' ');
                    });
                    el.removeClass('col');
                };
            // jQuery UI Resizable
            var dir = (document.dir == 'rtl') ? "w" : 'e';
            resizableEl.each(function() {
                $(this).resizable({
                    handles: dir,
                    start: function(event, ui) {
                        var
                            target = ui.element,
                            next = target.next(),
                            targetCol = Math.round(target.width() / columnWidth),
                            nextCol = Math.round(next.width() / columnWidth);
                        sibTotalWidth = target.width() + next.width();
                        removeCol(target);
                        //removeCol(next);
                        // set totalColumns globally
                        totalCol = 12;
                        target.resizable('option', 'minWidth', columnWidth);
                        target.resizable('option', 'maxWidth', ((totalCol) * columnWidth));
                    },
                    stop: function(event, ui) {
                        var
                            target = ui.element,
                            next = target.next(),
                            $device = T4Layout.getDeviceActive(),
                            targetW = ui.size.width,
                            nextW = sibTotalWidth - targetW;
                        targetColumnCount = Math.round(targetW / columnWidth),
                            nextColumnCount = Math.round(nextW / columnWidth),
                            targetSet = targetColumnCount,
                            nextSet = totalCol - targetColumnCount;
                        updateClass(target, targetSet, $device);
                        //updateClass(next, nextSet);
                        ui.element.removeAttr('style'); // remove width, our class already has it
                        ui.element.next().removeAttr('style'); // remove width, our class already has it

                    },
                    resize: function(event, ui) {
                        var
                            target = ui.element,
                            next = target.next(),
                            targetW = ui.size.width,
                            nextW = sibTotalWidth - targetW;
                        targetColumnCount = Math.round(targetW / columnWidth),
                            nextSet = totalCol - targetColumnCount;
                        //ui.originalElement.next().width(nextW);
                        target.find('.t4-column-title').text(targetColumnCount);
                        //next.find('.t4-column-title').text(nextSet);

                    }
                });
            });

        },
        initPreview: function() {
            // init toggle value
            var $previewtoggle = $('#t4-preview-layout'),
                previewState = localStorage.getItem('layout_preview');
            $previewtoggle.prop('checked', previewState).on('change', function() {
                localStorage.setItem('layout_preview', $(this).prop('checked') ? 1 : '');
                // 	$(document).trigger('reload-preview');
            });
            $(document).on('panel-group-switch', function(e, data) {
                if (data.originalEvent.isTrigger) return;
                var $pane = data.target;

                if ($previewtoggle.closest($pane).length) {
                    // do nothing
                } else {
                    // disable layout preview
                    if ($previewtoggle.prop('checked')) $previewtoggle.prop('checked', false).trigger('change');
                }
            })

        },
        renderColorPattern: function() {
            var colorPtClone = $('.pattern-list').clone(true),
                layoutPattern = '';
            colorPtClone.find('.pattern-actions').remove();
            layoutPattern += '<div data-type="default" class="pattern none" data-background_color="" data-link_color="" data-link_hover_color="" data-title="none" data-class="none" data-text_color="">';
            layoutPattern += '<div class="pattern-inner">';
            layoutPattern += '<div class="pattern-header"><h4 class="pattern-title">No palette</h4></div>';
            layoutPattern += '<p>Use color defaults</p>';
            layoutPattern += '</div>';
            layoutPattern += '</div>';
            colorPtClone.find('.pattern').each(function() {
                var $dataClone = $(this).data();
                var $dataColor = {};
                for (var name in $dataClone) {
                    if (name != 'type' && name != 'class' && name != 'title') {
                        if ($dataClone[name].search('#') != -1) {
                            $dataColor[name] = $dataClone[name];
                        } else {
                            $dataColor[name] = $('li[data-val="' + $dataClone[name] + '"]').data('color');
                        }
                    }
                }
                layoutPattern += '<div data-type="default" class="pattern ' + $dataClone.class + '" data-background_color="' + $dataColor.background_color + '" data-link_color="' + $dataClone.link_color + '" data-link_hover_color="' + $dataClone.link_color + '" data-title="' + $dataClone.link_color + '" data-class="' + $dataClone.class + '" data-text_color="' + $dataClone.text_color + '">';
                layoutPattern += '<div class="pattern-inner">';
                layoutPattern += '<div class="pattern-header"><h4 class="pattern-title">' + $dataClone.title + '</h4></div>';
                layoutPattern += '<ul class="color-list">';
                layoutPattern += '<li><span class="background_color" data-title="Background color" style="background:' + $dataColor.background_color + ';">&nbsp;</span></li>';
                layoutPattern += '<li><span class="text_color" data-title="Text color" style="background:' + $dataColor.text_color + ';">&nbsp;</span></li>';
                layoutPattern += '<li><span class="link_color" data-title="Link color" style="background: ' + $dataColor.link_color + ';">&nbsp;</span></li>';
                layoutPattern += '<li><span class="link_hover_color" data-title="Link hover color" style="background: ' + $dataColor.link_hover_color + ';">&nbsp;</span></li>';
                layoutPattern += '</ul>';
                layoutPattern += '</div>';
                layoutPattern += '</div>';

            });
            return layoutPattern;
        },
        colorPatternClick: function() {
            $('.t4-color-pattern').find('.pattern').each(function() {
                $(this).on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $('.t4-color-pattern').find('.active').removeClass('active');
                    $(this).addClass('active');
                    var $data = $(this).data();
                    $('.t4-input-color_pattern').val($data.class);
                });
            });
        },
        initOverlay: function() {
            var $over_type = $('.row-active').data('overlay_type') || '';
            var $opacity = $('.row-active').data('opacity') || 0;
            $('body').find('.control-group.image_type,.control-group.video_type,.control-group.file_type, .control-group.opacity').hide();
            $('.opacity').find('.t4-layout').val($opacity).trigger('mousemove');
            if ($over_type != '') {
                $('body').find('.control-group.' + $over_type + '_type').show();
                $('.opacity').show();
            }

        },
        configSection: function($elem, $device) {
            var cols = $elem.data('cols'),
                $section = $elem.find('.t4-row-container'),
                $clone = $section.clone(true);
            $clone.removeClass('t4-row-container').addClass('t4-content');
            $clone.find('.t4-column-options').remove();
            $clone.find('.row').addClass('t4-layout-xresize');
            $clone.find('.t4-layout-col').addClass('t4-layout-unit');
            var $colHide = '',
                dataCol = [];
            $section.find('.t4-layout-col').each(function() {
                dataCol.push($(this).data());
            });
            $clone.find('.t4-layout-col').each(function(index) {
                if (typeof dataCol[index].col == 'undefined') dataCol[index].col = 'auto';
                $(this).find('.t4-column-title').text(dataCol[index].col);
                $colHide += '<span class="pos-hidden hide" data-item_vis="' + index + '" data-hidden_sm="' + dataCol[index].hidden_sm + '" data-hidden_xs="' + dataCol[index].hidden_xs + '" title="Click here to show this position on current device layout">' + dataCol[index].name + '</span>';
            });
            if ($clone.find('.t4-layout-col').length) {

                var $gridClass = 't4-layout-devices';
                var $configGrid = '<div class="t4-admin-layout-devices btn-group" style="display:none">';
                $configGrid += '<button style="display:none;" class="btn t4-admin-dv-def active" data-device="def" title="Layout def" data-tooltip="Layout def"><i class="fal fa-desktop"></i></button>';
                $configGrid += '<button class="btn t4-admin-dv-xl active" data-device="xl" title="Layout xl" data-tooltip="Layout xl"><i class="fal fa-desktop"></i></button>';
                $configGrid += '<button class="btn t4-admin-dv-lg" data-device="lg" title="Layout Desktop" data-tooltip="Layout Desktop"><i class="fal fa-desktop"></i></button>';
                $configGrid += '<button class="btn t4-admin-dv-md" data-device="md" title="Layout laptop" data-tooltip="layout laptop"><i class="fal fa-laptop"></i></button>';
                $configGrid += '<button class="btn t4-admin-dv-sm" data-device="sm" title="Tablets" data-tooltip="Tablets"><i class="fal fa-tablet-alt"></i></button>';
                $configGrid += '<button class="btn t4-admin-dv-xs" data-device="xs" title="Phone" data-tooltip="Phone"><i class="fal fa-mobile-alt"></i></button>';
                $configGrid += '</div>';
                var resetAll = '<div class="t4-layout-reset">';
                resetAll += '<span class="btn t4-admin-dv-auto" title="Auto layout" data-tooltip="Auto layout">Set auto</span>';
                resetAll += '<span class="btn t4-admin-dv-clear" style="display:none;" title="Clear layout" data-tooltip="Clear layout">Clear</span>';
                resetAll += '<span class="btn t4-admin-dv-reset" style="display:none;" title="Reset layout" data-tooltip="Reset layout">Reset</span>';
                resetAll += '<span class="btn t4-admin-dv-none" style="display:none;" title="Set none layout" data-tooltip="Set none layout">Set none</span>';
                resetAll += '</div>';
                var addColumn = '<div class="t4-layout-column">';
                addColumn += '<span class="btn t4-col-add" title="Add column" data-content="Add column"><i class="fal fa-plus"></i>Add Column</span>';
                addColumn += '</div>';

                var admAction = $('<div />').addClass('t4-admin-layout-action').append(resetAll, $configGrid, addColumn);
                $clone.prepend(admAction);
                var jcolHide = '<div class="t4-admin-layout-hiddenpos" title="Currently hidden positions">' + $colHide + '</div>';
                $clone.append(jcolHide);
            }
            return $clone.html();
        },
        initOverlayReadonly: function($over_type) {
            var $value = '';
            switch ($over_type) {
                case 'video':
                    $value = $('[data-attrname="video_id"]').val();
                    break;
                case 'image':
                    $value = $('[data-attrname="background_image"]').val();
                    break;
                case 'file':
                    $file_cover = $('[data-attrname="file_cover"]').val();
                    $file_mp4 = $('[data-attrname="file_mp4"]').val();
                    $file_webm = $('[data-attrname="file_webm"]').val();
                    $file_ogg = $('[data-attrname="file_ogg"]').val();
                    if (!$file_cover || !$file_mp4 || !$file_webm || !$file_ogg) {
                        $value = true;
                    }

                    break;
                default:
                    $('.opacity').hide();
                    break;
            }
            if ($value) {
                $('.opacity').removeClass('disabled').prop('readonly', false);
            } else {
                $('.opacity').addClass('disabled').prop('readonly', true);
            }
        },
        updatePaletteColor(){
            var colorPattern = {},$pattern = '.pattern';
            var plLayoutNum = $('.t4-layout-palettes').find('.pattern').not('.none');
            var plThemeNameArr = [];
            var plLayoutNameArr = [];
            plLayoutNum.each(function(pl){
                plLayoutNameArr.push($(plLayoutNum[pl]).data('class'))
            });
            $('.group_palette').find($pattern).not('.pattern-clone').each(function(index){
                var dataColorPt = $(this).data();
                colorPattern[dataColorPt.class] = dataColorPt;
                var layoutPl = $('.t4-layout-palettes').find('.pattern.'+dataColorPt.class);

                if(!layoutPl.length){
                    var plNew = $(`
                            <div class="pattern ${dataColorPt.class}" data-background_color="${dataColorPt.background_color}" data-heading_color="${dataColorPt.heading_color}" data-heading_hover_color="${dataColorPt.heading_hover_color}" data-text_color="${dataColorPt.text_color}" data-link_color="${dataColorPt.link_color}" data-link_hover_color="${dataColorPt.link_hover_color}" data-title="${dataColorPt.title}" data-status="${dataColorPt.status}" data-class="${dataColorPt.class}">
                              <div class="pattern-inner" style="background-color: ${dataColorPt.background_color};">
                                <div class="pattern-header">
                                  <h4 class="pattern-title" style="color: ${dataColorPt.text_color};">${dataColorPt.title}</h4>
                                </div>
                              </div>
                            </div>
                        `);
                    plNew.appendTo($('.t4-layout-palettes'));
                }else{
                    layoutPl.find('.pattern-inner').css({"background-color":dataColorPt.background_color});
                    layoutPl.find('.pattern-title').css({"color":dataColorPt.text_color});
                    Object.keys(dataColorPt).forEach(function(name, index) {
                       layoutPl.data(name, dataColorPt[name]);
                    });
                }
                plThemeNameArr.push(dataColorPt.class);
            });
            plLayoutNameArr.forEach(function(val,idx){
                if(plThemeNameArr.indexOf(val) == -1){
                    $('.t4-layout-palettes').find('.pattern.'+val).remove();
                }
            });
        }
    });
    $(document).ready(function() {
        T4Layout.initPreview();
    });
}(jQuery);