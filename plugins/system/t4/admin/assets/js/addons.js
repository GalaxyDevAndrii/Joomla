jQuery(document).ready(function($) {
	// export
	$('.btn-action[data-action="addons.addasset"]').on('click', function() {
		$('.t4-addons-wrap .btn-action.active').removeClass('active');
		$(this).addClass('active');
		doAdd();
	});
	
	$('.btn-action[data-action="addons.save"]').on('click', function() {
		doSave();
	})

	$('.btn-action[data-action="addons.cancel"]').on('click', function() {
		$('.t4-addons-wrap .btn-action.active').removeClass('active');
		$('.btn-action[data-action="addons.addasset"]').show();
		$('li.editting').removeClass('editting');
		$('.addons-form').slideUp();
	})
	$('.addons-items').off('click', '.btn-action[data-action="addons.remove"]').on('click', '.btn-action[data-action="addons.remove"]', function() {
		var that = this;
		T4Admin.Confirm(T4Admin.langs.addonRemoveConfirm,function(ans){
		  if (ans) {
				$('.addons-form').insertAfter($('.add-more-addons'));
				doRemove(that);
		  }else {
		     return false;
		  }
	  },'');
	});
	$(document).on('click','.btn-action[data-action="addons.edit"]', function(e) {
	    var $li = $(this).closest('.addon-local'),$aname = $li.data('name'),$css = '',$js = '';
	    $('.btn-action[data-action="addons.addasset"]').hide();
	    $li.addClass('editting');
	    $('.addons-form').appendTo($li);
	    $('.addons-form').data('doaction','update');
	    $('.addons-form').find('#addons-name').val(T4Admin.addons[$aname].name);
	    if(T4Admin.addons[$aname].css) $css = T4Admin.addons[$aname].css.join("\n");
	    if(T4Admin.addons[$aname].js) $js = T4Admin.addons[$aname].js.join("\n");
	    $('.addons-form').find('#addons-css').val($css);
	    $('.addons-form').find('#addons-js').val($js);
	    $('.addons-form').slideDown();
	});
	var doAdd = function () {
		// show addon form
		$('.addons-form').insertAfter($('.add-more-addons'));
		$('.addons-form').data('doaction','add');
		$('.addons-form').slideDown();
		$('.addons-input').val('');
		$('#addons-name').focus();
	}
	var doSave = function () {
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=addAddon&id=' + tempId;
		var addonsForm = $('.addons-form'),$action = addonsForm.data('doaction');
		var asset = {
			name: addonsForm.find('#addons-name').val().trim()
		};
		var css = addonsForm.find('#addons-css').val().trim();
		if (css) {
			asset.css = css.split('\n');
		}
		var js = addonsForm.find('#addons-js').val().trim();
		if (js) {
			asset.js = js.split('\n');
		}
		$oldname = addonsForm.closest('.addon-local').data('name');
		var options = {
			asset: asset,
			action: $action,
			oldname: $oldname
		}/*
		asset.action = $action;
		asset.oldname = $oldname;*/
		if (!asset.name) {
			T4Admin.Messages(T4Admin.langs.addonEmptyFieldWaring,'error');
			return;
		}
		if((!asset.js && !asset.css)){
			T4Admin.Messages(T4Admin.langs.addonEmptyFieldCssOrJSWaring,'error');
			return;
		}
		if(asset.name != $oldname && T4Admin.addons.hasOwnProperty(asset.name)){
			T4Admin.Messages(T4Admin.langs.addonNameDuplicated,'error');
			return;
		}
		$.post(url, {asset:options}).then(function(response) {
			if (response.ok && response.asset) {
				// hide form
				addonsForm.hide();
				if(response.action == 'update'){
					if($oldname != response.asset.name ){
						addonsForm.closest('.addon-local').find('label[for^="jform_params_system_addons"]').html(response.asset.name);
						addonsForm.closest('.addon-local').data('name',response.asset.name);
						delete T4Admin.addons[$oldname];
					}
					T4Admin.Messages(T4Admin.langs.T4AddonsHasUpdated,'message');
				}else if(response.action == 'add'){
					// add new addon into list
					var $li = $('#addons-ghost').clone();
					var $input = $li.removeAttr('id').find('input');
					$input.attr('id', $input.data('id') + ($('.addons-items li').length -1));
					$input.val(response.asset.name);
					$input.closest('.addon-local').find('label').html(response.asset.name);
					$li.data('name',response.asset.name);
					$li.insertBefore($('#addons-ghost')).show();
					$('.t4-addons-wrap .btn-action.active').removeClass('active');
					T4Admin.Messages(T4Admin.langs.T4AddonsHasAdded,'message');
				}
				$('.btn-action[data-action="addons.addasset"]').show();
				$('li.editting').removeClass('editting');
				T4Admin.addons[response.asset.name] = response.asset;
			} else {
				T4Admin.Messages(response.error,'error');
			}
		})
	}

	var doRemove = function (btn) {
		var $btn = $(btn),
			addon = $btn.closest('li').find('input').val();
		if (!addon){
			T4Admin.Messages('T4_ADDON_NOT_FOUND','error');
			return false;
		}
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=removeAddon&id=' + tempId;
		$.post(url, {name: addon}).then(function(response) {
			if (response.ok) {
				// remove addon
				$btn.closest('li').remove();
				$('.btn-action[data-action="addons.addasset"]').show();
				T4Admin.Messages(T4Admin.langs.addonRemoveDeleted,'message');
			} else {
				T4Admin.Messages(response.error,'error');
			}
		})
	}
})