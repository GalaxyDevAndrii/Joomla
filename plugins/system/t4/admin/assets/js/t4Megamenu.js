/** 
 *------------------------------------------------------------------------------
 * @package       t4 Framework for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2013 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github 
 *                & Google group to become co-author)
 * @Google group: https://groups.google.com/forum/#!forum/t4fw
 * @Link:         http://t4-framework.org 
 *------------------------------------------------------------------------------
 */
var T4AdminMegamenu = window.T4AdminMegamenu || {};
! function($) {
    $.extend(T4AdminMegamenu, {
        // put megamenu admin panel into right place

        initMegaMenu: function() {
            var ChoiseMenuType = '';
            T4AdminMegamenu.changeMenuType($('.menu_type'));
            T4AdminMegamenu.duration = $('#jform_params_navigation_mega_animate').val();
        },
        changeMenuType: function(menu) {
            var $menuType = menu.val();
            $('.menu_items').children().hide();
            $('.t4-menu-layout-builder').children().hide();
            var $lastMenuItemActive = '';
            if (localStorage) {
                $lastMenuItemActive = localStorage.getItem('last_menu_item_active');
            }
            if ($menuType) {
                if ($lastMenuItemActive == '' || $lastMenuItemActive == null) {
                    $lastMenuItemActive = $('.menu_items .' + $menuType).find('.menu-item:first-child').data('name');
                }
                $('.item-active').removeClass('item-active');
                var $itemActive = $('.menu_items .' + $menuType).find('[data-name="' + $lastMenuItemActive + '"]');
                if (!$itemActive.length) {
                    $itemActive = $('.menu_items .' + $menuType).find('.menu-item:first-child');
                }
                $itemActive.addClass('item-active');
                $itemActive.trigger('click');
                $('.t4-megamenu-builder').show();
                $('.menu_items .' + $menuType).show();
                $('.t4-' + $menuType).show();
                ChoiseMenuType = $menuType;
                var $menuList = all_menu_item[ChoiseMenuType];
                var $itemParent = $('.menu_items .' + $menuType).find('[data-name="' + $lastMenuItemActive + '"]').data('itemid');
                T4AdminMegamenu.settingItemList($menuList, $itemParent);
            } else {
                $('.t4-megamenu-builder').hide();
            }
        },
        settingMegaItems: function($item) {
            if (!$item.find('.t4-mega-section').length) {
                var $menuList = all_menu_item[ChoiseMenuType];
                var itemId = $('.item-active').data('itemid');
                var $listItem = $menuList.filter(function(e) {
                    if (e.parent == itemId) {
                        return true;
                    }
                });
                if ($listItem.length) {
                    var $itemIdArr = [];
                    $listItem.forEach(function(el) {
                        $itemIdArr.push(el.id);
                        if (el.id in all_menu_item) {
                            console.log(all_menu_item[el.id]);
                        }
                    });
                    $item.find('.t4-mega-col').data('item', $itemIdArr.join(','));
                    $item.find('.t4-mega-col').data('type', 'item');
                    $item.find('.t4-mega-col').data('name', 'All Items');
                    $item.find('.t4-column-title').text('All Items');
                }
            }
        },
        settingItemList: function($menuList, $itemParent) {
            if (typeof $menuList == 'undefined') {
                $('.control-group.items').find('#megaCol_items').remove();
                return true;
            }

            var $listItem = [],
                $html = '';
            $html += "<div class='t4-choise-menu-item t4-layout control-group' data-attrname='items' data-items=''>";
            $html += "<ul>";
            if ($itemParent in all_menu_item) {
                $html += "<li class='menu-item-checkbox allitem' data-item='all'>";
                $html += "<label class=''><input type='checkbox' class='t4-checkbox' name='item' value='all' >Select all items</label>";
                $html += "</li>";
                $html += T4AdminMegamenu.renderMenuItem(all_menu_item[$itemParent], $itemParent);
            } else {
                $html += "Menu item has not child item";
            }
            $html += "</ul>";
            $html += "</div>";
            // $('.control-group.items').find('#megaCol_items').remove();
            $('.control-group.items').find('.controls').html($html);
        },
        renderMenuItem: function($list, $parent) {
            var $html = '';

            $list.forEach(function(el) {
                if (el.parent == $parent) {
                    $html += "<li class='menu-item-checkbox level-" + el.level + "' data-item='" + el.id + "'>";
                    $html += "<label class=''><input type='checkbox' class='t4-checkbox' name='item' value='" + el.id + "' >" + el.title + "</label>";
                    if (el.id in all_menu_item) {
                        $html += '<ul>';
                        $html += T4AdminMegamenu.renderMenuItem(all_menu_item[el.id], el.id);
                        $html += '</ul>';
                    }
                }

            });
            return $html;
        },
        //resizableEl
        resizeElement: function() {
            //resizable clone section
            var resizableEl = $('.t4-mega-col.t4-layout-unit'),
                columns = 12,
                setLayout = '',
                fullWidth = resizableEl.parent().width(),
                sibTotalWidth,
                columnWidth = fullWidth / columns,
                totalCol, // this is filled by start event handler
                updateClass = function(el, col) {
                    el.addClass('col-md-' + col);
                    el.data('col', col);
                },
                removeCol = function(el) {
                    el.removeClass(function(index, cName) {
                        return (cName.match(/(^|\s)col-\S+/g) || []).join(' ');
                    });
                };
                console.log(fullWidth)
            // jQuery UI Resizable
            resizableEl.each(function() {
                $(this).resizable({
                    handles: 'e',
                    start: function(event, ui) {
                        var
                            target = ui.element,
                            next = target.next(),
                            targetCol = Math.round(target.width() / columnWidth),
                            nextCol = Math.round(next.width() / columnWidth);
                        sibTotalWidth = target.width() + next.width();
                        removeCol(target);
                        removeCol(next);
                        // set totalColumns globally
                        totalCol = 12;
                        target.resizable('option', 'minWidth', columnWidth);
                        target.resizable('option', 'maxWidth', ((totalCol) * columnWidth));
                    },
                    stop: function(event, ui) {
                        var
                            target = ui.element,
                            next = target.next(),
                            targetW = ui.size.width,
                            nextW = sibTotalWidth - targetW;
                        targetColumnCount = Math.round(targetW / columnWidth),
                            nextColumnCount = Math.round(nextW / columnWidth),
                            targetSet = targetColumnCount,
                            nextSet = totalCol - targetColumnCount;
                        updateClass(target, targetSet);
                        updateClass(next, nextSet);
                        ui.element.removeAttr('style'); // remove width, our class already has it
                        ui.element.next().removeAttr('style'); // remove width, our class already has it
                        SecLayout = '';
                        $('.t4-layout-unit').each(function(index) {
                            SecLayout += $(this).data('col');
                            if ((index + 1) < $('.t4-layout-unit').length) {
                                SecLayout += '+';
                            }
                            return SecLayout;
                        });
                        $('.t4-input-mega-cols').data('layout', SecLayout);
                    },
                    resize: function(event, ui) {
                        var
                            target = ui.element,
                            next = target.next(),
                            targetW = ui.size.width,
                            nextW = sibTotalWidth - targetW;
                        targetColumnCount = Math.round(targetW / columnWidth),
                            nextSet = totalCol - targetColumnCount;
                        ui.originalElement.next().width(nextW);
                        target.find('.t4-column-title').text(targetColumnCount);
                        next.find('.t4-column-title').text(nextSet);

                    }
                })
            });
        },

        GeneratedJSON: function() {
            var t4item = {},
                megamenuType = $('.menu_type').val();
            $('.t4-menu-layout-builder').find('.t4-megamenu').each(function(index) {
                // var $megamenu = {};
                var menutypes = $(this);
                var $itemType = menutypes.data('type');
                // if($itemType == megamenuType){
                t4item[$itemType] = {};
                // Find menutypes Elements
                if (menutypes.find('.t4-menu-items').length) {
                    menutypes.find('.t4-menu-items').each(function(index) {
                        var $itemsMenu = {};
                        var type = $(this);
                        var typeObj = type.data();
                        // if(type.find('.t4-input-check-mega').getInputValue() == 1){
                        var $itemsId = typeObj.itemid;
                        t4item[$itemType][$itemsId] = {};
                        var itemsIndex = index;
                        type.find('.t4-menu-item').each(function(index) {
                            var itemsIndex = index,
                                items = $(this),
                                dataItems = items.data();
                            if (typeof dataItems.align == 'undefined') {
                                dataItems.align = 'left';
                            }
                            t4item[$itemType][$itemsId] = $.extend({

                                'mega_extra': dataItems.mega_extra,
                                'extra': dataItems.extra,
                                'icons': dataItems.icons,
                                'caption': dataItems.caption,
                                'width': dataItems.width,
                                'megabuild': dataItems.megabuild,
                                'align': dataItems.align,
                                'settings': []
                            }, dataItems);
                            delete t4item[$itemType][$itemsId].sortableitem;
                            delete t4item[$itemType][$itemsId].sortableItem;
                            delete t4item[$itemType][$itemsId].uiSortable;
                            delete t4item[$itemType][$itemsId].uisortable;
                            items.find('.t4-mega-section').each(function(index) {
                                var itemIndex = index,
                                    item = $(this),
                                    itemObj = item.data();
                                t4item[$itemType][$itemsId].settings[itemIndex] = {
                                    'contents': [],
                                };
                                item.find('.t4-mega-col').each(function(index) {
                                    var dataItem = $(this).data();
                                    var dataIndex = index;
                                    if (dataItem.name != 'none') {
                                        t4item[$itemType][$itemsId].settings[itemIndex].contents[dataIndex] = $.extend({
                                            "name": dataItem.name,
                                            "type": dataItem.type,
                                            "style": dataItem.style,
                                            "title": dataItem.title,
                                            "col": dataItem.col,
                                        }, dataItem);
                                        if (dataItem.type == 'module') {
                                            t4item[$itemType][$itemsId].settings[itemIndex].contents[dataIndex].modname = dataItem.modname;
                                            t4item[$itemType][$itemsId].settings[itemIndex].contents[dataIndex].module_id = dataItem.module_id;
                                        } else if (dataItem.type == 'position') {
                                            t4item[$itemType][$itemsId].settings[itemIndex].contents[dataIndex].position = dataItem.position;
                                        } else if (dataItem.type == 'items') {
                                            t4item[$itemType][$itemsId].settings[itemIndex].contents[dataIndex].items = dataItem.items;
                                        }
                                        delete t4item[$itemType][$itemsId].settings[itemIndex].contents[dataIndex].sortableItem;
                                        delete t4item[$itemType][$itemsId].settings[itemIndex].contents[dataIndex].sortableitem;
                                        delete t4item[$itemType][$itemsId].settings[itemIndex].contents[dataIndex].uiSortable;
                                        delete t4item[$itemType][$itemsId].settings[itemIndex].contents[dataIndex].uisortable;
                                    }
                                });
                            });
                        });
                        // }
                    });
                }
                // }

            });
            console.log(t4item);
            return t4item;
        },
        settingApply: function() {
            //apply setting
            $(document).on('click', '.t4-menu-settings-apply', function() {
                event.preventDefault();
                var tplhelperValue = $('#tplhelper').val();
                var flag = $(this).data('flag');
                switch (flag) {
                    case 'mega-setting':
                        $('.t4-mega-inner').find('.t4-layout').each(function() {
                            var $thisEl = $(this),
                                cols = '',
                                $parent = $('.t4-mega-section.row-active'),
                                $attrname = $thisEl.data('attrname');
                            $parent.removeData($attrname);
                            if ($attrname == 'mega-cols') {
                                cols = $thisEl.val();
                                T4AdminMegamenu.layoutArr();
                            }
                            $parent.data($attrname, $thisEl.getInputValue());
                        });

                        $('.themeConfigModal').children().not('.t4-message-container').hide();
                        $('.t4-col-remove').addClass('hidden');
                        $('body').removeClass('t4-modal-open');
                        break;

                    case 'item-setting':
                        var component = false;
                        $('.t4-mega-item-inner').find('.t4-layout').each(function() {
                            var $thisEl = $(this),
                                $parent = $('.column-active'),
                                $attrname = $thisEl.data('attrname'),
                                dataVal = $thisEl.val();

                            $parent.removeData($attrname);
                            if ($attrname == 'name' && component != true) {
                                if (dataVal == '' || dataVal == undefined) {
                                    dataVal = 'none';
                                }
                                $parent.data('module_id', $(this).data('module_id'));
                                $parent.data('modname', $(this).data('modname'));
                                $('.column-active .t4-column-title').text(dataVal);
                            }
                            if ($attrname == 'items') {
                                dataVal = $thisEl.data('items') ? $thisEl.data('items') : '';
                            }
                            $parent.data($attrname, dataVal);

                        });
                        $('.themeConfigModal').children().not('.t4-message-container').hide();
                        $('body').removeClass('t4-modal-open');
                        break;

                    default:
                        alert('You are doing somethings wrongs. Try again');
                }
                $('.row-active').removeClass('row-active');
                $('.column-active').removeClass('column-active');
                tplhelper['t4megamenu'] = 1;
                $('#tplhelper').val(JSON.stringify(tplhelper));
                var $dataMegaMenu = T4AdminMegamenu.GeneratedJSON();
                $('.t4-navigation').val(JSON.stringify($dataMegaMenu)).trigger('change');
            });
        },
        // Column Layout Arrange
        layoutArr: function(options) {
            var col = [],
                $gparent = $('.t4-mega-section.row-active'),
                colAttr = [],
                newLayout = [];
            $gparent.find('.t4-mega-col').each(function(i, val) {
                col[i] = $(this);

            });
            $('.config-section').find('.t4-mega-col').each(function(i, val) {
                newLayout.push($(this).data('col'));
                var colData = $(this).data();
                if (typeof colData == 'object') {
                    colAttr[i] = $(this).data();
                } else {
                    colAttr[i] = '';
                }
            });
            var new_item = '';
            for (var i = 0; i < newLayout.length; i++) {
                var dataAttr = '';

                if (typeof colAttr[i] != 'object') {
                    colAttr[i] = {
                        col: newLayout[i],
                        type: 'row',
                        name: 'none'
                    }
                } else {
                    colAttr[i].col = newLayout[i];
                }
                $.each(colAttr[i], function(index, value) {
                    if (index != 'sortableitem' && index != 'uiresizable') {
                        dataAttr += ' data-' + index + '="' + value + '"';
                    }
                });
                if (newLayout[i] == 'auto') {
                    $cls = 'col-md';
                } else {
                    $cls = 'col-md-' + newLayout[i];
                }
                new_item += '<div class="t4-col t4-mega-col ' + $cls + '" ' + dataAttr + '>';
                if (col[i]) {
                    var that = col[i];
                    if (!colAttr[i].name) colAttr[i].name = 'none';
                    that.find('.t4-column-title').text(colAttr[i].name);
                    new_item += that.html();
                } else {
                    new_item += '<div class="col-inner clearfix">';
                    new_item += '<span class="t4-column-title">none</span>';
                    new_item += '<span class="t4-col-remove hidden" title="Remove column" data-content="Remove column"><i class="fal fa-minus"></i> </span>';
                    new_item += '<a class="t4-item-options " href="#"><i class="fal fa-cog fa-fw"></i></a>';
                    new_item += '</div>';
                }
                new_item += '</div>';
            }
            $old_column = $gparent.find('.t4-mega-col').remove();
            $gparent.find('.row.ui-sortable').append(new_item);
            T4AdminMegamenu.MegaMenuUiLayout();
        },
        itemConfig: function() {

            $('.t4-menu-item').each(function() {
                var $data = $(this).data();
                $itemMenu = $(this).parents('.t4-menu-items').data('itemid');
                var $itemEl = $('[data-name="itemid-' + $itemMenu + '"]');
                var $valAttr = $data['megabuild'];
                if ($valAttr == 1) {
                    $(this).show();
                    $(this).parents('.t4-menu-items').find('.t4-menu-add-row').show();
                    $(this).parents('.t4-menu-items').find('.item-mega-config').show();
                    $(this).parents('t4-menu-items').find('input.t4-input-check-mega').setInputValue({ field: 1 });

                } else {
                    $(this).hide();
                    $(this).parents('.t4-menu-items').find('.t4-menu-add-row').hide();
                    $(this).parents('.t4-menu-items').find('.item-mega-config').hide();
                    $(this).parents('t4-menu-items').find('input.t4-input-check-mega').setInputValue({ field: 0 });
                }
            });

            var $itemidActive = $('.item-active').data('name');
            var $dataItemConfig = $('.' + $itemidActive).find('.t4-menu-item').data();
            if (typeof $dataItemConfig == 'undefined') {
                $dataItemConfig = [];
            }
            $('.t4-mainmenu').children().hide();
            $('.' + $itemidActive).slideDown('slow');
            $('.t4-item').each(function() {
                var $attrName = $(this).attr('id');
                $(this).val($dataItemConfig[$attrName]);
            });
            if ($dataItemConfig['align']) {
                $('.t4-item-action').each(function() {
                    $(this).removeClass('active');
                    if ($(this).data('align') == $dataItemConfig['align']) {
                        $(this).addClass('active');
                    }
                });


            }
            //check mega on/off
            $('.menu_items').find('.menu-item').each(function() {
                // console.log($(this).data('itemid'));
            });
        },
        ChangeItems: function(data) {
            $('.t4-menu-layout-builder').find('.t4-megamenu').children().hide();
            var dataName = data.data('name'),
                ChoiseMenuType = $('.menu_type').val(),
                $menuList = all_menu_item[ChoiseMenuType],
                $itemParent = data.data('itemid');
            T4AdminMegamenu.settingItemList($menuList, $itemParent);
            var $itemsetData = $('.' + dataName).find('.t4-menu-item').data();
            $('.t4-menu-items.' + dataName).show();
            $('#extra').val($itemsetData.extra);
            $('#icons').val($itemsetData.icons);
            $('#caption').val($itemsetData.caption);
            $('.t4-item-width').val($itemsetData.width);
            $('.t4-mega-extra-class').val($itemsetData.mega_extra);
            $('.item-mega-align').find('.btn').removeClass('active');
            $('.item-mega-align').find('[data-align="' + $itemsetData.align + '"]').addClass('active');
            if ($itemsetData.megabuild == 1) {
                $('.' + dataName + ' #megamenu').setInputValue({ field: 1 });
                $('.' + dataName).find('.t4-menu-item').find('.t4-mega-col').each(function() {
                    var $dataCol = $(this).data();
                    $(this).find('.t4-column-title').text($dataCol.name);
                });
            } else {
                $('.' + dataName + ' #megamenu').setInputValue({ field: 0 });
            }

        },
        MegaMenuUiLayout: function() {
            $(".t4-menu-item").sortable({
                placeholder: "ui-state-highlight",
                forcePlaceholderSize: true,
                axis: 'y',
                opacity: 0.8,
                tolerance: 'pointer',
                stop: function(event, ui) {
                    T4AdminMegamenu.megaApply();
                },
            }).disableSelection();

            $('.t4-mega-section').find('.row').rowSortable('mega');
        },
        initEvents: function() {
            $(document).on('change', '.menu_type', function() {
                T4AdminMegamenu.changeMenuType($(this));
            });
            $('body').on('click', '.menu-item', function() {
                $('.menu-item').removeClass('item-active');
                $(this).addClass('item-active');
                if (localStorage) {
                    localStorage.setItem('last_menu_item_active', $(this).data('name'));
                }
                T4AdminMegamenu.ChangeItems($(this));
            });
            $(document).on('change', '.menu-item-checkbox', function() {
                var data = $(this).find('input').val();

                if (data == 'all') {
                    var enabled = $(this).prop('checked');
                    if (enabled) {
                        $(this).prop('checked', false);
                        $('.menu-item-checkbox input').prop('checked', false);
                        $('.name').find('input').val('None');
                        $('.t4-choise-menu-item').data('items', '');
                    } else {
                        $(this).prop('checked', true);
                        $('.menu-item-checkbox input').prop('checked', true);
                        $('.name').find('input').val(T4Admin.langs.megamenuSectionAllItems);
                        $('.t4-choise-menu-item').data('items', 'all');
                    }

                } else {
                    var allChecked = $('.t4-choise-menu-item').find('[data-item="all"]').find('input').attr('checked');
                    if (allChecked == 'checked') {
                        $('.t4-choise-menu-item').find('[data-item="all"]').find('input').attr('checked', false);
                    }
                    $('.name').find('input').val(T4Admin.langs.megamenuSectionSelectItems);
                    var dataItemMega = [];
                    $('.menu-item-checkbox input').each(function() {
                        var checked = $(this).prop('checked');
                        if (checked && $(this).val() != 'all') {
                            dataItemMega.push($(this).val());
                        }
                    });
                    $('.t4-choise-menu-item').data('items', dataItemMega.join(','));
                }

            });
            $(document).on('click', '.t4-meganeu-row-options', function() {
                event.preventDefault();
                var $megaModal = $('.t4-mega-row-modal');
                if (!$megaModal.parents().is('.themeConfigModal')) $megaModal.appendTo($('.themeConfigModal'));
                $('body').addClass('t4-modal-open');
                $megaModal.show();
                $('.row-active').removeClass('row-active');
                $parent = $(this).closest('.t4-mega-section');
                $parent.addClass('row-active');
                $('.t4-modal.t4-mega-row').find('.tab-pane').removeClass('fade').addClass('active');
                $('.t4-mega-inner').find('select.t4-layout').each(function() {
                    $(this).chosen('destroy');
                });

                var $clone = $('.t4-mega-inner');

                $clone.find('select.t4-layout').each(function() {
                    $(this).chosen({ width: '100%' });
                });
                $clone.find('.config-section').html(T4AdminMegamenu.configSection($parent));
                $clone.find('.t4-item-options').remove();
                $clone.find('.t4-mega-col').addClass('t4-layout-unit');
                $clone.find('.t4-layout').each(function() {
                    var $that = $(this),
                        attrValue = $parent.data($that.data('attrname'));
                    $that.setInputValue({ field: attrValue });
                });
                var cols = $clone.find('.t4-mega-col').length;

                $clone.find('.t4-layout-ncolumns .btn').each(function() {
                    $(this).removeClass('active');
                    if (cols == $(this).data('col')) {
                        $(this).addClass('active');
                    }
                });
                $clone.find('.t4-col-remove').removeClass('hidden');
                //set mega layout
                $clone.find('.t4-input-mega-cols').data('mega_layout', $parent.data('mega-cols'));
                T4AdminMegamenu.resizeElement();

            });
            // Remove Row
            $(document).on('click', '.t4-remove-row-mega', function(event) {
                event.preventDefault();
                var $that = $(this);
                T4Admin.Confirm(T4Admin.langs.t4LayoutRowConfirmDel, function(ans) {
                    if (ans) {
                        $that.closest('.t4-mega-section').slideUp(500, function() {
                            $that.closest('.t4-mega-section').remove();
                            var $megaData = T4AdminMegamenu.GeneratedJSON();
                            $('.t4-navigation').val(JSON.stringify($megaData)).trigger('change');
                        });
                    } else {
                        return false;
                    }
                }, '');
            });
            // Open Item settings Modal
            $(document).on('click', '.t4-item-options', function(event) {
                event.preventDefault();
                var $megaColSettings = $('.t4-mega-item-modal');
                if (!$megaColSettings.parents().is('.themeConfigModal')) $megaColSettings.appendTo($('.themeConfigModal'));
                $('body').addClass('t4-modal-open');
                $megaColSettings.show();
                $('.t4-mega-col').removeClass('column-active');
                $parent = $(this).closest('.t4-mega-col');
                $parent.addClass('column-active');
                $('.t4-modal.t4-mega-item').find('.tab-pane').removeClass('fade').addClass('show active');
                var $itemId = $parent.data('items');
                $('.t4-mega-item-inner').find('select.t4-layout').each(function() {
                    $(this).chosen('destroy');
                });
                var $clone = $('.t4-mega-item-inner');
                $clone.find('.tab-pane').removeClass('active show').addClass('fade');
                $clone.find('#general').removeClass('fade').addClass('show active');
                if ($itemId != '' && typeof $itemId != 'undefined') {
                    $clone.find('.t4-choise-menu-item').data('items', $itemId);
                    var $itemIdArrs = [],
                        $itemIdArr;
                    if (isNaN($itemId)) {
                        $itemIdArrs = $itemIdArrs.concat($itemId.split(','))
                    } else {
                        $itemIdArrs.push($itemId.toString())
                    }
                    if (($itemIdArrs.length + 1) == ($clone.find('.menu-item-checkbox input').length)) {
                        $('.menu-item-checkbox[data-item="all"]').find('input').prop('checked', true);
                    }
                    $clone.find('.items.name_type').find('.menu-item-checkbox input').each(function() {
                        var $optVal = $(this).val();
                        if ($itemId == 'all') {
                            $(this).prop('checked', true);
                        } else {
                            if ($itemIdArrs.indexOf($optVal) != -1) {
                                $(this).prop('checked', true);
                            } else {
                                $(this).prop('checked', false);
                            }
                        }

                    });
                } else {
                    $('.menu-item-checkbox').find('input').prop('checked', false);
                }
                $clone.find('.t4-layout').each(function() {
                    var $that = $(this),
                        attrValue = $parent.data($that.data('attrname'));
                    if ($that.data('attrname') == 'name') {
                        if (typeof $parent.data('modname') != 'undefined') $that.data('modname', $parent.data('modname'));
                        if (typeof $parent.data('module_id') != 'undefined') $that.data('module_id', $parent.data('module_id'));
                    }
                    $that.setInputValue({ field: attrValue });
                });
                $clone.find('select.t4-layout').each(function() {
                    var $input = $(this);
                    $input.chosen({ width: '100%' });
                    var typeName = $('.column-active').data('type'),
                        nameActive = $('.column-active').data('name');
                    if (typeof typeName != 'undefined' && typeName != '') {
                        $('.name_type').not($('.' + typeName)).hide();
                        $('.' + typeName).show();

                        if (typeName == 'module' || typeName == 'position') {
                            $('.style').show();
                        } else {
                            $('.style').hide();
                        }
                        $typeAct = $('.' + typeName).find('select.t4-layout');
                        $typeAct.val(nameActive);
                        $typeAct.trigger("chosen:updated");

                    } else {
                        $('.name_type').hide();
                    }
                    $input.on('change', function(e) {
                        var $thisEl = $(this),
                            valInput = $thisEl.val(),
                            $AttrName = $thisEl.data('attrname');
                        if ($AttrName == 'type') {
                            $('.name_type').hide();
                            $('.' + valInput).show();
                            if (valInput == 'module' || valInput == 'position') {
                                $('.style').show();
                            } else {
                                $('.style').hide();
                            }
                        }
                    });
                    $('.name_type').find('select.t4-layout').on('change', function() {
                        var $select = $(this),
                            value = $select.val();
                        if ($select.data('attrname') == 'module') {
                            var modname = this.options[this.selectedIndex].getAttribute('data-modname');
                            var modId = this.options[this.selectedIndex].getAttribute('data-id');
                            $('[data-attrname="name"]').data('modname', modname);
                            $('[data-attrname="name"]').data('module_id', modId);
                        }
                        $('[data-attrname="name"]').val(value);
                    });
                });
            });
            // add row to config
            $(document).on('click', '.t4-menu-add-row a', function(event) {
                event.preventDefault();
                var $parents = $(this).closest('.t4-menu-items'),
                    $parent = $parents.find('.t4-menu-item'),
                    $rowClone = $('#t4-mega-section').clone(true);

                $rowClone.addClass('t4-mega-section').removeAttr('id');
                $($rowClone).appendTo($parent);

                T4AdminMegamenu.MegaMenuUiLayout();
            });
            //select number columns events
            $(document).on('click', '.t4-layout-ncolumns .btn', function(e) {
                $('.t4-layout-ncolumns .active').removeClass('active');
                $(this).addClass('active');
                var cols = $(this).text(),
                    $newLayout = T4AdminMegamenu.layoutBuilder(cols);

                $('.t4-mega-xresize').html($newLayout);
                $('.t4-mega-xresize').find('.t4-col-remove').removeClass('hidden');
                $('.config-section').find('.t4-column-options').remove();
                $('.config-section').find('.t4-item-options').remove();
                $('.config-section').find('.t4-col').removeAttr('data-sortableitem');
                $('.config-section').find('.t4-col').addClass('t4-layout-unit');
                var $jposHide = '';
                $('.config-section').find('.t4-col').each(function(index) {
                    $(this).find('.t4-column-title').text($(this).data('col'));
                    $('.config-section').find('.t4-admin-layout-hiddenpos').find('.pos-hidden').remove();
                    $jposHide += '<span class="pos-hidden hide" data-item_vis="' + index + '" title="Click here to show this position on current device layout">' + $(this).data('name') + '</span>';
                });
                $('.t4-input-mega-cols').val(cols);
                T4AdminMegamenu.resizeElement();
            });
            //remove columns
            $('body').on('click', '.t4-col-remove', function(e) {
                var parentsCol = $(this).parents('.t4-mega-xresize');
                var lastCol = parentsCol.find('.t4-mega-col').length;
                if (lastCol == 1) {
                    alert(T4Admin.langs.RemoveColConfirm);
                } else {
                    $(this).parents('.t4-mega-col').remove();
                }
            });
            //mouse hover to btn of section config
            $(document).on('mouseover', ".t4-layout-ncolumns .btn", function() {
                var cols = $(this).text();
                for (i = 0; i < cols; i++) {
                    var select = $('.t4-layout-ncolumns .btn').get(i);
                    $(select).addClass('selected');
                }
            });
            $(document).on('mouseout', ".t4-layout-ncolumns .btn", function() {
                $('.t4-layout-ncolumns .btn').removeClass('selected');
            });
            /*			$(document).on('change', '.t4-input-check-mega',function(){
            				var $item = $('.item-active'),
            					$itemsetData = $('.'+$item.data('name')).find('.t4-menu-item');
            				if($(this).prop('checked')){
            					$itemsetData.data('megabuild','1');
            					T4AdminMegamenu.settingMegaItems($itemsetData);
            					$itemsetData.show();
            					$('.'+$item.data('name')).find('.t4-menu-add-row').show();
            					$('.'+$item.data('name')).find('.item-mega-config').show();
            					$(this).setInputValue({field:'1'});
            	  		}else{
            	  			$(this).prop('checked',false);
            					$itemsetData.data('megabuild','0');
            					console.log($itemsetData.data());
            					$itemsetData.hide();
            					$('.'+$item.data('name')).find('.t4-menu-add-row').hide();
            					$('.'+$item.data('name')).find('.item-mega-config').hide();
            					$(this).setInputValue({field:'0'});
            		  	}
            			});*/
            // init onchange value input
            $(document).on('change', '.t4-item', function() {
                var $value = $(this).val(),
                    $config_item = $(this).attr('id'),
                    $orgVal = $(this).data($config_item),
                    $item = $('.item-active'),
                    $itemsetData = $('.' + $item.data('name')).find('.t4-menu-item');
                if ($(this).attr('name') == 'megabuild') {
                    if ($(this).prop('checked')) {
                        $itemsetData.data('megabuild', '1');
                        T4AdminMegamenu.settingMegaItems($itemsetData);
                        $itemsetData.show();
                        $('.' + $item.data('name')).find('.t4-menu-add-row').show();
                        $('.' + $item.data('name')).find('.item-mega-config').show();
                        $(this).setInputValue({ field: '1' });
                        $value = 1;
                    } else {
                        $(this).prop('checked', false);
                        $itemsetData.data('megabuild', '0');
                        $itemsetData.hide();
                        $('.' + $item.data('name')).find('.t4-menu-add-row').hide();
                        $('.' + $item.data('name')).find('.item-mega-config').hide();
                        $(this).setInputValue({ field: '0' });
                        $value = 0;
                    }
                } else {
                    $(this).data($config_item, $value);
                }
                $itemsetData.data($config_item, $value);
                var $dataMegaMenu = T4AdminMegamenu.GeneratedJSON();
                $('.t4-navigation').val(JSON.stringify($dataMegaMenu)).trigger('change');
            });
            //action Alignment
            $(document).on('click', '.item-mega-align .t4-item-action', function(e) {
                var $align = $(this).data('align'),
                    $item = $('.item-active'),
                    $itemsetData = $('.' + $item.data('name')).find('.t4-menu-item');
                $('.t4-item-action.active').removeClass('active');
                $(this).addClass('active');
                $itemsetData.data('align', $align);
                var $dataMegaMenu = T4AdminMegamenu.GeneratedJSON();
                $('.t4-navigation').val(JSON.stringify($dataMegaMenu)).trigger('change');
            });
            //select number columns events
            $(document).on('click', '.t4-mega-column .btn', function(e) {
                var cols = $('.t4-mega-xresize').find('.t4-mega-col').length;
                if ($(this).hasClass('t4-mega-col-add') && (cols < 12)) {
                    cols = cols + 1;
                }
                if ($(this).hasClass('t4-col-remove') && cols > 1) {
                    cols = cols - 1;
                }
                var $newLayout = T4AdminMegamenu.layoutBuilder(cols);

                $('.config-section').find('.t4-mega-xresize').html($newLayout);

                $('.config-section').find('.t4-item-options').remove();
                $('.config-section').find('.t4-col-remove').removeClass('hidden');
                $('.config-section').find('.ui-resizable-handle').remove();
                $('.config-section').find('.t4-mega-col').removeAttr('data-sortableitem');
                $('.config-section').find('.t4-mega-col').addClass('t4-layout-unit');
                $('.config-section').find('.t4-mega-col').each(function(index) {
                    $(this).find('.t4-column-title').text($(this).data('col'));
                });
                T4AdminMegamenu.resizeElement();
            });

        },
        //clone Section to config this
        configSection: function($elem) {
            var cols = $elem.data('cols'),
                $section = $elem.find('.t4-row-container'),
                $clone = $section.clone(true);
            $clone.removeClass('t4-row-container').addClass('t4-content');
            $clone.find('.t4-column-options').remove();
            $clone.find('.t4-item-options').remove();
            $clone.find('.row').addClass('t4-mega-xresize');
            $clone.find('.t4-col').addClass('t4-layout-unit');
            var $colHide = '';
            $clone.find('.t4-col').each(function(index) {
                var dataCol = $(this).data();
                $(this).find('.t4-column-title').text(dataCol.col);
                $colHide += '<span class="pos-hidden hide" data-item_vis="' + index + '" data-hidden_sm="' + dataCol.hidden_sm + '" data-hidden_xs="' + dataCol.hidden_xs + '" title="Click here to show this position on current device layout">' + dataCol.name + '</span>';
            });
            var $gridClass = 't4-layout-devices';
            var addColumn = '<div class="t4-mega-column">';
            addColumn += '<span class="btn t4-mega-col-add"><i class="fal fa-plus"></i> Add column</span>';
            addColumn += '</div>';

            var admAction = $('<div />').addClass('t4-admin-layout-action').append(addColumn);
            $clone.prepend(admAction);
            var jcolHide = '<div class="t4-admin-layout-hiddenpos" title="Currently hidden positions">' + $colHide + '</div>';
            $clone.append(jcolHide);

            return $clone;
        },
        // Column Layout Arrange
        layoutBuilder: function(cols) {

            var col = [],
                $cls = '',
                $gparent = $('.row-active');
            colAttr = [];
            $gparent.find('.t4-mega-col').each(function(i, val) {

                col[i] = $(this);
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
                        name: 'none'
                    }
                } else {
                    colAttr[i].col = 'auto';
                }
                $.each(colAttr[i], function(index, value) {
                    if (index != 'sortableitem' && index != 'uiresizable') {
                        dataAttr += ' data-' + index + '="' + value + '"';
                    }
                });

                new_item += '<div class="t4-col t4-mega-col col-md" ' + dataAttr + '>';
                if (col[i]) {
                    var that = col[i];
                    if (!colAttr[i].name) colAttr[i].name = 'none';
                    that.find('.t4-column-title').text(colAttr[i].name);
                    new_item += that.html();
                } else {
                    new_item += '<div class="col-inner clearfix">';
                    new_item += '<span class="t4-column-title">none</span>';
                    new_item += '<span class="t4-col-remove" title="Remove column" data-content="Remove column"><i class="fal fa-minus"></i> </span>';

                    new_item += '<a class="t4-item-options" href="#"><i class="fal fa-cogs"></i></a>';
                    new_item += '</div>';
                }
                new_item += '</div>';
            }
            return new_item;
        },
        megaApply: function() {
            var $dataMegaMenu = T4AdminMegamenu.GeneratedJSON();
            T4Admin.t4Ajax($dataMegaMenu, 'SaveMegamenu');

        },
        updateMegamenu: function() {
            var $megaBuilder = $('.t4-menu-layout-builder'),
                $megaVal = $('[name="jform[params][navigation_mega_settings]"]').val();
            $data = JSON.parse($megaVal);
            var $menuType = Object.keys($data);
            var $html = T4AdminMegamenu.renderMegaMenu($data);
            $megaBuilder.find('.t4-' + $menuType).html($html);
            if (typeof $lastMenuItemActive != 'undefined') {
                $lastMenuItemActive.trigger('click');
            }
            T4AdminMegamenu.MegaMenuUiLayout();
        },
        renderAttrs: function($data) {
            var $dataAttr = '';
            $.each($data, function(index, value) {
                if (index != 'settings') {
                    $dataAttr += ' data-' + index + '="' + value + '"';
                }
            });
            return $dataAttr;
        }, //$mega
        renderMegaMenu: function($mega) {
            var alignData = ['left', 'right', 'center', 'justify'];
            var $menuType = Object.keys($mega),
                $layoutMega = '',
                $items = Object.keys($mega[$menuType]);

            if ($items.length) {
                for (var i = 0; i < $items.length; i++) {
                    var $dataItem = $mega[$menuType][$items[i]],
                        $megabuild = $dataItem.megabuild ? $dataItem.megabuild : "0",
                        $style = "style='display:none;'",
                        $checked = '';

                    if (typeof $megabuild != 'undefined' && $megabuild != '0') {
                        $style = "style='display:block;'";
                        $checked = "checked='checked'";
                    }
                    $layoutMega += '<div class="t4-menu-items itemid-' + $items[i] + '" data-itemid="' + $items[i] + '" ' + $style + '>';
                    $layoutMega += '<div class="enablemega ' + $items[i] + ' t4-home">';
                    $layoutMega += '<label for="megamenu">Build Mega Menu</label>';
                    $layoutMega += '<input id="megamenu" class="t4-item t4-input t4-input-check-mega" type="checkbox" name="megabuild" data-attrname="megabuild" value="' + $megabuild + '" ' + $checked + '></div>';
                    $layoutMega += '<div class="item-mega-config" ' + $style + '>';
                    $layoutMega += '<div class="item-mega-width"><label class="item-width" for="width">' + T4Admin.langs.megamenuSubmenuWidth + '</label>';
                    $layoutMega += '<input id="width" type="text" placeholder="300px" class="t4-item t4-item-width" name="item-width" value="' + $dataItem.width + '"></div>';
                    $layoutMega += '<div id="mega-extra" class="mega-extra-class">';
                    $layoutMega += '<label for="megaextra">' + T4Admin.langs.megamenuExtraClass + '</label>';
                    $layoutMega += '<input id="mega_extra" type="text" name="mega_extra" value="' + $dataItem.extra + '" class="t4-item t4-mega-extra-class" aria-invalid="false"></div>';
                    $layoutMega += '<div class="item-mega-align"><label class="item-align">' + T4Admin.langs.megamenuAlignment + '</label>';
                    $layoutMega += '<div class="t4-item btn-group">';
                    for (var n = 0; n < alignData.length; n++) {
                        var classes = (alignData[n] == $dataItem.align) ? 'active' : '';
                        $layoutMega += '<a class="btn t4-item-align-' + alignData[n] + ' t4-item-action ' + classes + '" href="#" data-action="alignment" data-align="' + alignData[n] + '" title="' + alignData[n] + '"><i class="fal fa-align-' + alignData[n] + '"></i></a>';
                    }
                    $layoutMega += '</div></div></div>';
                    var $dataAttrs = T4AdminMegamenu.renderAttrs($dataItem);
                    $layoutMega += '<div class="t4-menu-item" ' + $style + ' ' + $dataAttrs + '>';
                    var $dataSection = $mega[$menuType][$items[i]].settings;
                    for (var j = 0; j < $dataSection.length; j++) {

                        $layoutMega += '<div class="t4-mega-section" data-layout="12" data-cols="1">';
                        $layoutMega += '<div class="t4-meganeu-settings clearfix">';
                        $layoutMega += '<div class="pull-right"><ul class="t4-row-option-list">';
                        $layoutMega += '<li><a class="t4-move-row" href="#"><i class="fal fa-arrows-alt"></i></a></li>';
                        $layoutMega += '<li><a class="t4-meganeu-row-options" href="#"><i class="fal fa-cog fa-fw"></i></a></li>';
                        $layoutMega += '<li><a class="t4-remove-row-mega" href="#"><i class="fal fa-trash-alt fa-fw"></i></a></li>';
                        $layoutMega += '</ul></div></div>';
                        $layoutMega += '<div class="t4-row-container"><div class="row">';
                        var $dataContents = $dataSection[j].contents;
                        for (var k = 0; k < $dataContents.length; k++) {
                            var $dataAttr = T4AdminMegamenu.renderAttrs($dataContents[k]);
                            var nameTitle = $dataContents[k].name ? $dataContents[k].name : "none";
                            $layoutMega += '<div class="t4-col t4-mega-col col-md" ' + $dataAttr + '>';
                            $layoutMega += '<div class="col-inner item-build clearfix"><span class="t4-column-title">' + nameTitle + '</span><a class="t4-item-options" href="#"><i class="fal fa-cog fa-fw"></i></a></div>';
                            $layoutMega += '</div>';
                        }
                        $layoutMega += '</div></div></div>';
                    }
                    $layoutMega += '</div>';
                    $layoutMega += '<div class="t4-menu-add-row" ' + $style + '><a class="" href="#"><i class="fal fa-plus-circle"></i><span>Add Row</span></a></div></div>';
                }
            }
            return $layoutMega;
        },
    });
    $(document).ready(function() {
        T4AdminMegamenu.initMegaMenu();
        T4AdminMegamenu.initEvents();
        T4AdminMegamenu.MegaMenuUiLayout();
        T4AdminMegamenu.settingApply();
        T4AdminMegamenu.itemConfig();
    });

}(jQuery);