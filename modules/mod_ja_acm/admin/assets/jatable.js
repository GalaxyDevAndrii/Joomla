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
    var JATable = function (element) {
        var $element = this.$element = $(element);
        // bind click event for button
        // bindActions();
        $element.bindActions('.action', this);

        // make textarea auto height
        $element.elasticTextarea('updated clone_col delete_col clone_row');

        // make all field as ignore save
        $element.find('input, textarea, select').not('.acm-object').data('ignoresave', 1);

        // reset index
        $element.data('index', 0);

        // build Form
        this.bindData();

        // store
        $element.find('.acm-object').data('acm-object', this);

        // trigger updated event for element after built
        setTimeout(function () {
            $element.trigger('updated')
        }, 100);
    };

    JATable.prototype.getData = function () {
        var result = {},
            $element = this.$element;
        // get fixed row data
        var $items = $element.find('table.jatable > thead > tr > :nth-child(2)').find('input, textarea, select');

        $items.each(function () {
            var $this = $(this),
                name = $this.data('name');
            result[name] = jaTools.getVal(this, $element);
        });

        // get dynamic data
        result['data'] = [];
        $element.find('table.jatable > tbody > tr').each(function (i, row) {
            result['data'][i] = [];
            $(row).children().not(':last').each(function (j, cell) {
                var val = $(cell).data('type');
                if (val == 'text') {
                    val = 't' + $(cell).find('textarea').val();
                } else if (!val) {
                    val = $(cell).find('textarea').val();
                }
                result['data'][i][j] = val;
            });
        });

        result['rows'] = $element.find('table.jatable > tbody > tr').length;
        result['cols'] = $element.find('table.jatable > tbody > tr:first > td').length - 2;
        result['type'] = 'table';

        return result;
    };

    JATable.prototype.bindData = function (fieldname, alldata) {
        var jatable = this;
        // delete added cols/rows
        this.$element.find('table.jatable > thead .btn-delete-col').slice(1).each(function () {
            jatable.delete_col (this);
        });
        this.$element.find('table.jatable > tbody .btn-delete-row').slice(1).each(function () {
            jatable.delete_row (this);
        });

        if (!alldata) return;

        var cols = 1,
            names = [],
            $items = this.$element.find('table.jatable > thead > tr > :nth-child(2)');

        $items.each(function (i, cell) {
            var $cell = $(cell),
                $field = $cell.find('input, textarea, select'),
                name = $field.data('name');
            names[i] = name;
        });

        var data = alldata[fieldname] ? alldata[fieldname] : {};

        // compatible with old version
        // try to detect old data - compatible with old version
        if ($.isEmptyObject(data)) {
            var group = fieldname.replace(/^[^\[]*\[/, '['),
                prefix = fieldname.replace(group, '');
            // try to detect old data - compatible with old version
            for (var i = 0; i < names.length; i++) {
            	var name = names[i];
            	var fname = name.replace(group, '');
                if (alldata[fname]) data[name] = alldata[fname];
            }
        }
        // end compatible

        // find number cols/rows
        for (var i = 0; i < names.length; i++) {
        	var name = names[i];
        	 if (data[name] && data[name].length > cols) cols = data[name].length;
        }

        // compatible with pricing table - detect dynamic data
        if ($.isEmptyObject(data['data'])) {
            var dyndata = [],
                feature_name = prefix + '[pricing-row-name]',
                feature_value = prefix + '[pricing-row-supportfor]';
            if (alldata[feature_name]) {
                for (var i = 0; i < alldata[feature_name].length; i++) {
                    dyndata[i] = [];
                    dyndata[i][0] = alldata[feature_name][i];
                    if (alldata[feature_value] && alldata[feature_value][i]) {
                        var value = alldata[feature_value][i];
                        for (var j = 0; j < cols; j++) {
                            dyndata[i][j + 1] = (value & Math.pow(2, j)) ? 'b1' : 'b0';
                        }
                    }
                }
            }
            data['data'] = dyndata;
        }
        // end compatible
        // blank data, just quit
        if ($.isEmptyObject(data)) return;

        var btn = this.$element.find('table.jatable > thead .btn-clone-col')[0];
        for (var i = 0; i < cols - 1; i++) {
            // actions['clone-col'](btn);
            this.clone_col (btn);
        }

        var $rows = this.$element.find('table.jatable > thead > tr');
        for (var i = 0; i < names.length; i++) {
        	var name = names[i];
        	if (data[name] && data[name].length) {
        		var vals = data[name];
        		for (var x = 0; x < vals.length; x++) {
        			var val = vals[x];
        			var $cell = $rows.eq(i).children().eq(x + 1);
                    jaTools.setVal($cell.find('input, textarea, select'), val);
        		}
            }
        }

        // build dynamic row
        if (!data['data']) return;
        var rows = data['data'].length,
            btn = this.$element.find('table.jatable > tbody .btn-clone-row')[0];
        for (var i = 0; i < rows - 1; i++) {
            // actions['clone-row'](btn);
            this.clone_row (btn);
        }

        // update data
        var $rows = this.$element.find('table.jatable > tbody > tr');
        for (var i = 0; i < rows; i++) {
            var cells = $rows.eq(i).children();
            for (var j = 0; j < cols + 1; j++) {
                var $cell = cells.eq(j),
                    celldata = data['data'][i][j],
                    type = celldata[0];
                if (data['data'][i] && data['data'][i][j]) {
                    if (j == 0) {
                        $cell.find('textarea').val(celldata);
                    } else {
                        if (type == 't') {
                            // text type
                            $cell.find('textarea').val(celldata.substr(1)).show();
                            $cell.find('.jatable-cell').hide();
                        } else if ($cell.find('[data-type="' + celldata + '"]').length) {
                            // actions['change-type']($cell.find('[data-type="' + celldata + '"]'));
                            this.change_type ($cell.find('[data-type="' + celldata + '"]'));
                            $cell.find('.jatable-cell').show();
                            $cell.find('.jatable-cell-text').hide();
                        } else {
                            $cell.find('textarea').val(celldata).show();
                            $cell.find('.jatable-cell').hide();
                        }
                    }
                }
            }
        }

    };


    JATable.prototype.delete_row = function (btn) {
        var $btn = $(btn),
            $row = $btn.parents('tr').first();

        if (!$row.hasClass('first')) {
            $row.remove();
        }
    }

    JATable.prototype.clone_row = function (btn) {
        var $btn = $(btn),
            $row = $btn.parents('tr').first(),
            $newrow = $row.clone(true, true).removeClass('first').insertAfter($row);
    }

    JATable.prototype.delete_col = function (btn) {
        var $btn = $(btn),
            $col = $btn.parents('th').first(),
            colidx = $col.index();

        if (!$col.hasClass('first')) {
            $col.remove();
            // remove other cell in rows
            this.$element.find('tr').each(function () {
                var $this = $(this);
                if ($this.hasClass('title')) return;
                $this.children().eq(colidx).remove();
            });
        }
    }

    JATable.prototype.clone_col = function (btn) {
        var $btn = $(btn),
            $col = $btn.parents('th').first(),
            colidx = $col.index(),
            idx = this.$element.data('index');

        this.$element.data('index', ++idx);
        // clone first cell
        jaTools.fixCloneObject($col.jaclone(idx).removeClass('first').insertAfter($col), true);
        // insert other cell
        this.$element.find('tr').each(function () {
            var $this = $(this);
            if ($this.hasClass('title')) return;
            var $col = $this.children().eq(colidx);
            jaTools.fixCloneObject($col.jaclone(idx).insertAfter($col), true);
        });
    }

    JATable.prototype.change_type = function (btn) {
        var $btn = $(btn),
            $cell = $btn.parents('td').first(),
            $cellval = $cell.find('.jatable-cell'),
            $celltext = $cell.find('.jatable-cell-text');

        $cell.data('type', $btn.data('type'));

        if ($btn.data('type') == 'text') {
            $cellval.hide();
            $celltext.show().focus();
        } else {
            $celltext.hide();
            $cellval.show().removeClass().addClass('jatable-cell').addClass($btn.data('type')).html($btn.html());
        }
    };

    function Plugin() {
        return new JATable(this);
    }

    $.fn.jatable = Plugin;
    $.fn.jatable.constructor = JATable;

})(jQuery);
