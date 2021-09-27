jQuery(document).ready(function($) {
	var orgvalue;

	var Typelist = function($el) {
		this.$container = $el;

		this.$forms = this.$container.find('.typelist-form');
		this.$formedit = this.$forms.filter('[data-name="edit"]');
		this.$formclone = this.$forms.filter('[data-name="clone"]');
		this.$actions = this.$container.find('.typelist-action');
		this.$input = this.$container.find('.typelist-input');
		//this.$inputEdit = this.$container.find('input.t4-typelist-edit');
		this.$btnedit = this.$container.find('[data-action="edit"]');
		this.$presetcontent = this.$container.find('.preset-content');
		this.type = this.$container.data('type');

		this.editting = false;

		if (Typelists[this.type]) {
			this.script = new Typelists[this.type](this.$formedit);
		} else {
			this.script = new DefaultType(this.$formedit, this.type);
		}
	}

	Typelist.prototype.init = function() {
		var self = this;
		// init action
		this.$container.on('click', '.btn-action', function(e) {
			var $btn = $(e.target);
			var action = 'do' + $btn.data('action');

			if (self[action]) {
				self[action]($btn);
			}
		})
		this.$input.chosen({'with':'100%',disable_search:true});
		this.$input.on('change', function() {
			self.doload(1);
		})

		// first init form then load value
		this.script.init();
		this.doload();
	}

	Typelist.prototype.doload = function(trigger) {
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=typelist&id=' + tempId;
		var name = this.$input.val();
		var self = this;
		$.get(url, {task: 'load', type: this.type, name: name}).then(function(response) {
			if (response.value) {
				// init form with value
				//this.value = response.value;
				//this.script.set(response.value);
				self.val(response.value);

				//check t4dev Mode
				/*if(T4Admin.t4devmode){
					self.t4devMode();
				}else{

					// show readonly form
					self.makeReadonly();

				}*/
				// show readonly form
				self.makeReadonly();
				// self.$container.find('input[name^="typelist-"]').first().trigger('change');
				if (trigger) {
					$(document).trigger('typelist.load', [self]);
				}
			} else {
				T4Admin.Messages(response.error,'error');
			}
		})
	}

	Typelist.prototype.makeReadonly = function () {
		this.editting = false;
		this.$forms.hide();
		this.$formedit.show().find('.form-action').hide();
		this.$container.find('.form-edit-action').hide();

		this.$presetcontent.removeClass('editting');
		//this.$inputEdit.prop('checked',false);
		this.$btnedit.show();
		// disable all field
		this.$actions.show();
		this.$input.parent().css('pointer-events', '').removeClass('disabled');
		//disabled action from panel
		$('.t4-sidebar-preview, .t4-pn-views').css('pointer-events', '').removeClass('disabled');
		if(!$('.custom-colors-form').is(":hidden")){
			$('.btn-action[data-action="custom.cancel"]').trigger('click');
		}

		// show delete/restore action
		var status = this.$input.find('option:selected').data('status');
		this.$actions.find('.btn-action[data-action="delete"]').hide().filter('[data-status="' + status + '"]').show();
		var selfbtnedit = this.$btnedit;
		// // popover when click on form
		this.$formedit.find('.sub-group-params, .sub-group-params-one').addClass('disabled').on('click', function(e) {
           if(!e.isTrigger) {
                if (!selfbtnedit.data('popover')) {
                    selfbtnedit.popover( {
                        placement: 'bottom',
                        content: selfbtnedit.data('tooltip'),
                        trigger: 'manual',
                    } );
                }
                var popover = selfbtnedit.data('popover');
                if(!popover) popover = selfbtnedit.data('bs.popover');
                popover.show();
            }
        }).on('mouseleave', function(){
        	 var popover = selfbtnedit.data('popover');
            if(!popover) popover = selfbtnedit.data('bs.popover');
            if (popover) popover.hide();
        })
	}
	Typelist.prototype.makeEditable = function () {
		this.editting = true;
		this.$forms.hide();
		this.$formedit.show().removeClass('disabled').find('.form-action').show();
		this.$container.find('.form-edit-action').show();
		this.$presetcontent.addClass('editting');
		//this.$inputEdit.prop('checked',true);
		// disable all field
		this.$input.parent().css('pointer-events', 'none').addClass('disabled');
		//disabled action from panel
		$('.t4-sidebar-preview, .t4-pn-views').css('pointer-events', 'none').addClass('disabled');

		// disable popover
		this.$formedit.find('.sub-group-params,.sub-group-params-one').removeClass('disabled').off('click').off('mouseleave');
	}
	Typelist.prototype.t4devMode = function () {
		this.editting = true;
		this.$forms.hide();
		this.$actions.hide();
		this.$formedit.show().removeClass('disabled').find('.form-action').show();
		this.$container.find('.form-edit-action').hide();
		this.$presetcontent.addClass('editting');
		//this.$inputEdit.prop('checked',true);
		// disable all field
		// this.$input.parent().css('pointer-events', 'none').addClass('disabled');
		//disabled action from panel
		// $('.t4-sidebar-preview, .t4-pn-views').css('pointer-events', 'none').addClass('disabled');

		// disable popover
		this.$formedit.find('.sub-group-params,.sub-group-params-one').removeClass('disabled').off('click').off('mouseleave');
	}

	Typelist.prototype.doedit = function($btn) {
		// this.$forms.hide().filter('[data-name="edit"]').show();
		orgvalue = this.script.get();
		this.makeEditable();
		this.$actions.hide();

		/*
		if(this.$inputEdit.prop('checked') === true){
			orgvalue = this.script.get();
			this.makeEditable();
			this.$actions.hide();
		}else{
			if(JSON.stringify(orgvalue) != JSON.stringify(this.script.get())){
				this.$inputEdit.prop('checked',true);
				T4Admin.Confirm(T4Admin.langs['typelistConfirmEdit'+this.type],function(ans){
				  if (ans) {
				     this.$container.find('[data-action="save"]').trigger('click');
				  }else {
				     this.$container.find('[data-action="cancel"]').trigger('click');
				  }
			  });
			}else{
				this.$container.find('[data-action="cancel"]').trigger('click');
			}
		}
		*/
	}

	Typelist.prototype.doclone = function($btn) {
		this.$forms.hide().filter('[data-name="clone"]').show();
		this.$forms.find('#layout-new-name').val('');
		this.$btnedit.hide();
		this.$actions.hide();
	}

	Typelist.prototype.dodelete = function($btn) {
		var self = this;
		var $langs = 'typelistconfirm'+this.type + $btn.data('tooltip');
		// confirm then allow delete
		T4Admin.Confirm(T4Admin.langs[$langs],function(ans){
		  if (ans) {
		    var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=typelist&id=' + tempId;
				var name = self.$input.val();
				$.post(url, {task: 'delete', type: self.type, name: name}).then(function(response) {
					if (response.ok) {
						// Remove from input
						if (response.status == 'del') {
							self.$input.find('option[value="' + name + '"]').remove();
							self.$input.trigger('liszt:updated');
						} else {
							// update status
							self.$input.find('option[value="' + name + '"]').data('status', response.status);
						}

						T4Admin.Messages(T4Admin.langs[$langs+'d'],'success');
						// hide form
						self.makeReadonly();
						self.doload(1);
					} else {
						T4Admin.Messages(response.error,'error');
					}
				})
		  }else{
		  	return false;
		  }
	  },'');
	}

	Typelist.prototype.docancel = function($btn) {
		//check cancel clone
		if($btn.data('type') == 'clone'){
			// hide form and show action
			this.makeReadonly();
			return;
		}
		// confirm before cancel
		var self = this;
		if(JSON.stringify(orgvalue) != JSON.stringify(self.script.get())){
			T4Admin.Confirm(T4Admin.langs['typelistConfirmEdit'+self.type], function(ans){
			  if (ans) {
			     self.$container.find('[data-action="save"]').trigger('click');
			  }else {
			     self.val(orgvalue);
			     self.makeReadonly();
			     self.doload();
			  }
		  },T4Admin.langs.t4save);
		} else {
			// hide form and show action
			this.makeReadonly();
		}

	}

	Typelist.prototype.dosave = function($btn) {
		// get Value
		var value = this.script.get();
		if (typeof value == 'object') value = JSON.stringify(value);
		if (JSON.stringify(orgvalue) != JSON.stringify(value)) {
			// data change, then save
			var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=typelist&id=' + tempId;
			var name = this.$input.val();
			var self = this;
			$.post(url, {task: 'save', type: this.type, name: name, value: value}).then(function(response) {
				if (response.ok) {
					// update status
					self.$input.find('option[value="' + name + '"]').data('status', response.status);
					// hide form
					self.makeReadonly();
					self.doload(1);
					T4Admin.Messages(T4Admin.langs.T4TypeListSaved,'message');
				} else {
					T4Admin.Messages(response.error,'error');
				}
			})
		} else {
			// just quit
			this.makeReadonly();
		}
	}

	Typelist.prototype.dosaveclone = function($btn) {
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=typelist&id=' + tempId;
		var name = this.$input.val();
		var newname = this.$formclone.find('input[name="newname"]').val();
		if (!newname) {
			T4Admin.Messages('Enter new name','error');
			return;
		}
		var self = this;
		$.post(url, {task: 'clone', type: this.type, name: name, newname: newname}).then(function(response) {
			if (response.ok) {
				// Add to list
				$('<option value="' + newname + '">' + newname + '</option>').appendTo(self.$input).data('status', response.status);
				self.$input.val(newname);
				self.$input.trigger('liszt:updated');
				self.$input.trigger('chosen:updated');
				// hide form
				self.makeReadonly();
				T4Admin.Messages(T4Admin.langs.typelistCloneSaved,'message');
			} else {
				T4Admin.Messages(response.error,'error');
			}
		})
	}


	Typelist.prototype.val = function(value) {
		if (value === undefined) {
			// get value
			return this.script.get();
		}
		var oval = this.script.get();
		if (JSON.stringify(oval) != JSON.stringify(value)) {
			this.script.set(value);
		}
	}



	// Default type
	var DefaultType = function($el, type){
		this.$container = $el;
		this.type = type;
	}

	DefaultType.prototype.init = function () {

	}

	DefaultType.prototype.get = function () {
		// find all fields and get value
		var $inputs = this.$container.find('[name^="typelist-' + this.type + '["]');
		var result = {};
		$inputs.each(function(i, el){
			var $input = $(el),
				name = el.name.match(/typelist-[^\[]*\[(.*)\]/)[1],
				value = $input.val();

			if ($input.is('[type="checkbox"]')) {
				value = $input.is(':checked') ? value : '';
			}
			result[name] = value;

		})
		return result;
	}

	DefaultType.prototype.set = function (value) {
		var $inputs = this.$container.find('[name^="typelist-' + this.type + '["]');
		$inputs.each( function(i, el) {
			var $input = $(el), name = el.name.match(/typelist-[^\[]*\[(.*)\]/)[1];
			if (!value[name]) value[name] = '';

			if ($input.is('[type="checkbox"]')) {
				// for checkbox
				$input.prop('checked', value[name] ? true : false);
			} else {
				$input.val(value[name] ? value[name] : '');
			}
			if($input.hasClass('t4-input-color')){
				var valColor =  $input.closest('.t4-select-color').find('li[data-val="'+value[name].replace(/\s/g,'_')+'"]').data('color');
				$input.closest('.color-preview').find('.preview-icon').css({background:valColor});
			}
			if($input.hasClass('minicolors')){
				$input.minicolors('value',value[name]);
			}
			if($input.is('select')){
				$input.chosen({with:'100%'});
				$input.trigger('liszt:updated');
				$input.trigger('chosen:updated');
			}
			if($input.hasClass('t4-input-media') || $input.hasClass('field-media-input')){
				if($input.closest('.field-media-wrapper').find('.add-on.field-media-preview').length){
					$input.closest('.field-media-wrapper').data('fieldMedia').setValue(value[name]);
				}else{
					if(value[name]){
						var image = $('<img src="'+t4_site_root_url+'/'+value[name]+'" alt="" />');
						$input.closest('.field-media-wrapper').find('.field-media-preview').html(image);
					}
				}
			}
		})
	}



	// init with all typelist element
	$('.typelist').each(function() {
		var $el = $(this);
		var typelist = new Typelist($el);
		$el.data('typelist', typelist);
		typelist.init();
	})


})
