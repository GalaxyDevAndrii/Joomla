/**
 * ------------------------------------------------------------------------
 * JA Masthead Module 
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

var JADependForm = function () {
	var self = this;
	self.initialized = false;
	self.depends = {};
	self.controls = {};

	self.register = function (to, depend) {
		var controls = self.controls;
		
		if (!controls[to]) {
			controls[to] = [];

			self.elmsFrom(to).on('change', function (e) {
				self.change(this);
			});
		}

		if (controls[to].indexOf(depend) == -1) {
			controls[to].push(depend);
		}
	};

	self.compareVersions = function (a, b) {
		var v1 = a.split('.');
		var v2 = b.split('.');
		var maxLen = Math.min(v1.length, v2.length);
		for (var i = 0; i < maxLen; i++) {
			var res = parseInt(v1[i]) - parseInt(v2[i]);
			if (res != 0) {
				return res;
			}
		}
		return 0;
	};

	self.change = function (ctrlelm) {
		var controls = self.controls,
				depends = self.depends,
				ctrls = controls[ctrlelm.name];

		if (!ctrls) {
			ctrls = controls[ctrlelm.name.substr(0, ctrlelm.name.length - 2)];
		}

		if (!ctrls) {
			return false;
		}
		
		for (var i = 0; i < ctrls.length; i++) {
			var dpd = ctrls[i];
			var showup = true;
			
			for (var ctrl in depends[dpd]) {
				var cvals = depends[dpd][ctrl];

				if (showup) {
					var celms = self.elmsFrom(ctrl);
					showup = showup && !self.closest(celms, '.control-group').data('disabled');
					if (showup) {
						showup = showup && self.valuesFrom(celms).some(function (val) {
							return (cvals.indexOf(val) > -1 );
						});
					}
				}
			};

			self.elmsFrom(dpd).each(function (i, delm) {
				if (showup) {
					self.enable(delm);
				} else {
					self.disable(delm);
				}
			}, this);

			if (controls[dpd] && controls[dpd] != dpd) {
				self.elmsFrom(dpd).trigger('change');
			}

		};
	};

	self.add = function (control, info) {
		var depends = self.depends,
			name = info.group + '[' + control + ']';

		info = jQuery.extend({
			group: 'params',
			hiderow: true,
			control: name
		}, info);
		
		info.hiderow = !!info.hiderow;
		
		var arr = info.elms.split(',');
		for (var i = 0; i < arr.length; i++) {
			var elm = info.group + '[' + arr[i].trim() + ']';
			if (!depends[elm]) {
				depends[elm] = {};
			}

			if (!depends[elm][name]) {
				depends[elm][name] = [];
			}

			depends[elm][name].push(info.val);

			self.register(name, elm);
		}
	};

	self.start = function () {
		jQuery('h4.block-head').each(function (i, el) {
			self.closest(el, 'div.control-group').addClass('segment');
		}, this);

		jQuery('.hideanchor').each(function (i, el) {
			var ctr = self.closest(el, '.control-group');
			if (ctr.length) {
				ctr.addClass('hide');
			}
		}, this);

		self.update();
		self.initialized = true;
	};

	self.update = function () {
		for (var k in self.controls) {
			self.elmsFrom(k).trigger('change');
		}
	};

	self.enable = function (el) {
		var dur = self.initialized ? 300 : 0;
		self.closest(el, '.control-group').show(dur).data('disabled', false);
	};
	
	self.disable = function (el) {
		var dur = self.initialized ? 300 : 0;
		self.closest(el, '.control-group').hide(dur).data('disabled', true);
	};

	self.elmsFrom = function (name) {
		var multipe = name + '[]';
		var elm = jQuery('[name="'+name+'"]');
		if (!elm.length) {
			elm = jQuery('[name="'+multipe+'"]');
		}
		return elm;
	};

	self.valuesFrom = function (els) {
		var vals = [];
		els.each(function(i, e) {
			var el = jQuery(e),
				val = el.val();
				
			if (!val) {
				return;
			}
			
			if (el.is('select')) {
				vals = Array.isArray(val) ? val : [val];
			} else if((el.is(':radio') || el.is(':checkbox')) && el.is(':checked')){
				vals.push(val);
			}
		});

		return vals;
	};

	self.closest = function (elm, sel) {
		return jQuery(elm).parents(sel);
	};

	self.segment = function (seg) {
		if (jQuery('#'+seg).hasClass('close')) {
			self.showseg(seg);
		} else {
			self.hideseg(seg);
		}
	};

	self.showseg = function (seg) {

		var segelm = jQuery('#' + seg),
			snext = self.closest(segelm, '.control-group').next();
		while (snext.length && !snext.hasClass('segment')) {
			if (!snext.hasClass('hide') && !snext.data('disabled') ) {
				snext.show(200);
			}
			snext = snext.next();
		}

		segelm.removeClass('close').addClass('open');
	};

	self.hideseg = function (seg) {
		var segelm = jQuery('#' + seg),
			snext = self.closest(segelm, '.control-group').next();

		while (snext.length && !snext.hasClass('segment')) {
			if (!snext.hasClass('hide') && !snext.data('disabled')) {
				snext.hide(200);
			}
			snext = snext.next();
		}

		segelm.removeClass('open').addClass('close');
	};
};

var JADepend = window.JADepend || {};
JADepend.inst = new JADependForm();