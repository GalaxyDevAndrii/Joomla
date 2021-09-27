  // export

  $('.btn-action[data-action="custom.addcolor"]').on('click', function() {
    $('.t4-custom-colors-wrap .btn-action.active').removeClass('active');
    $(this).addClass('active');
    doAdd();
  });

  $('.btn-action[data-action="custom.savecolor"]').on('click', function() {
    doSave();
  });
  $('.btn-action[data-action="custom.cancel"]').on('click', function() {
    $('.t4-custom-color-wrap .btn-action.active').removeClass('active');
    $('.custom-colors-form').hide()
  });
  $('body').on('click', '.colors-actions-list .color-del', function() {
    doRemove(this);
  });
  $(document).on('click', '.custom-color-list .control-group', function(e) {
    if (!$(e.target).closest('.can-edit').length) {
      makefocus();
    }
  });
  // $('.custom-color-list').sortable({
  // 	placeholder: "ui-state-highlight",
  // 	forcePlaceholderSize: true,
  // 	handle: ".color-move",
  // 	axis: 'y',
  // 	opacity: 0.8,
  // 	tolerance: 'pointer',
  // 	stop: function(event, ui){
  // 		updateColorOders();
  // 	},
  // }).disableSelection();
  $('body').on('click', '.custom-color-list .can-edit label', function() {
    var $parentGroup = $(this).closest('.control-group'),
      $colorname = $parentGroup.data('name');
    makefocus();
    $parentGroup.addClass('editting');
    $(this).closest('.control-label').find('label').hide();
    $(this).closest('.control-label').find('.edit-label').show();
    $(this).closest('.control-label').find('.edit-label').find('input').val($colorname);
    $('.edit-label input').focus();
  });
  $('body').on('click', '.edit-label .color-cancel', function() {
    $(this).closest('.control-label').find('label').show();
    $(this).closest('.control-label').find('.edit-label').hide();
    $(this).closest('.control-group').removeClass('editting');
  });
  var makefocus = function(btn) {
    $('.control-group.editting').find('.control-label').find('label').show();
    $('.control-group.editting').find('.edit-label').hide();
    $('.control-group.editting').removeClass('editting');
  }
  $('body').on('click', '.edit-label .color-save', function() {
    doEdit(this);
  });
  $('body').on('change', '.custom-color-item', function() {
    updateColors(this);
  });

  var doAdd = function() {
    // show addon form
    $('.custom-colors-form').slideDown('slow');
    $('.custom-input').val('');
    $('#color-name').focus();
  }
  var allNameColor = function() {
    var nameColor = [];
    $('.custom-color-list').find('.control-group').each(function() {
      if (nameColor.indexOf($(this).data('name')) == -1) {
        nameColor.push($(this).data('name'));
      }
    });
    return nameColor;
  }
  var doSave = function() {
    var allName = allNameColor() || [];
    var colors = {
      name: $('#color-name').val().trim()
    };
    if (!colors.name) {
      T4Admin.Messages(T4Admin.langs.colorNameEmptyFieldWaring, 'warning');
      return false;
    }
    var color = $('#custom-color').val().trim();
    if (color) {
      colors.color = color;
    } else {
      T4Admin.Messages(T4Admin.langs.colorEmptyFieldWaring, 'warning');
      return false;
    }
    var nameReplace = colors.name.replace(/\s+/g, '_').toLowerCase();
    if (allName.indexOf(nameReplace) == -1) {
      // add new addon into list
      var $li = '';
      $li += '<div class="control-group ' + nameReplace + '" data-name="' + colors.name + '" data-class="' + nameReplace + '" data-color="' + colors.color + '">';
      $li += '<div class="control-label can-edit">';
      $li += '<label id="jform_params_styles_color_' + nameReplace + '-lbl" for="jform_params_styles_color_' + nameReplace + '">' + colors.name;
      $li += '</label>';
      $li += '<div class="edit-label hide">';
      $li += '<input type="text" name="edit_' + nameReplace + '" value="' + colors.name + '"/>';
      $li += '<div class="edit-actions">';
      $li += '<span class="color-save"><i class="fal fa-check"></i></span>';
      $li += '<span class="color-cancel"><i class="fal fa-times"></i></span>';
      $li += '</div>';
      $li += '</div>';
      $li += '<div class="colors-actions">';
      $li += '<ul class="colors-actions-list">';
      $li += '<li><a class="color-move" href="#" data-tooltip="Move"><i class="fal fa-arrows fa-fw"></i></a></li>';
      $li += '<li><a class="color-del" href="#" data-tooltip="Delete"><i class="fal fa-trash-alt fa-fw"></i></a></li>';
      $li += '</ul>';
      $li += '</div>';
      $li += '</div>';
      $li += '<div class="controls">';
      $li += '<input type="text" class="custom-color-item minicolors rgba" name="' + nameReplace + '"  value="' + colors.color + '" />';
      $li += '</div>';
      $li += '</div>';
      $('.custom-color-list').append($li);
      // T4Admin.initMinicolors($('.control-group.' + nameReplace).find('input.minicolors'));
       $('.t4-color-picker').each(function() {
		    $(this).minicolors();
		    $(this).minicolors({
		      control: $(this).attr('data-control') || 'hue',
		      defaultValue: $(this).attr('data-color') || '',
		      format: $(this).attr('data-format') || 'hex',
		      keywords: $(this).attr('data-keywords') || '',
		      inline: $(this).attr('data-inline') === 'true',
		      letterCase: $(this).attr('data-letterCase') || 'lowercase',
		      opacity: $(this).attr('data-opacity'),
		      position: $(this).attr('data-position') || 'bottom',
		      swatches: data_swatches ? data_swatches.split('|') : [],
		      change: function(hex, opacity) {
		        var log;
		        try {
		          log = hex ? hex : 'transparent';
		          if (opacity) log += ', ' + opacity;
		          console.log(log);
		        } catch (e) {}
		      },
		      theme: 'default'
		    });
		  });
      colors.class = nameReplace;
      // hide form
      $('.custom-colors-form').slideUp('slow');
      saveColorFile(colors);
      $('.t4-custom-color-wrap .btn-action.active').removeClass('active');
    } else {
      T4Admin.Messages(T4Admin.langs.customColordaplicateWaring, 'warning');
    }
  }
  var doEdit = function(btn) {
    var $btn = $(btn),
      $parentGroup = $btn.closest('.control-group');
    var allName = allNameColor() || [];
    var $oldname = $parentGroup.data('name');
    var editLabel = $btn.closest('.edit-label').find('input').val();
    if (editLabel == null || editLabel == "") {
      T4Admin.Messages(T4Admin.langs.colorNameNoneWarning, 'warning');
    } else if ($oldname != editLabel && allName.indexOf(editLabel) !== -1) {
      T4Admin.Messages(T4Admin.langs.customColordaplicateWaring, 'warning');
    } else {
      $parentGroup.data('name', editLabel);

      $parentGroup.find('.control-label label').html(editLabel);
      $parentGroup.removeClass('editting');
      $btn.closest('.control-label').find('label').show();
      $btn.closest('.control-label').find('.edit-label').hide();
      // save data 
      var $data = {};
      $data.class = $parentGroup.data('class');
      $data.name = $parentGroup.data('name');
      saveColorFile($data);
      updateInputColor($data);
    }
  }
  var updateInputColor = function($data) {
    var $input_color = $(document).find('.t4-input-color[data-val="' + $data.class.replace(/_/g, " ") + '"]');
    if ($input_color.length) {
      $input_color.val($data.name).trigger('change');
    }
  }
  var saveColorFile = function($data) {
    // $data = JSON.parse($('.t4-custom-colors').val());
    var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Customcolors&id=' + tempId;
    $.post(url, { task: 'save', data: $data }).then(function(response) {
      if (response.ok) {
        // update status
        if (response.status == 'add') {
          T4Admin.Messages(T4Admin.langs.customColorHasSaved, 'message');
        }
      } else {
        T4Admin.Messages(response.error, 'error');
      }
    })
  }
  // var updateColor = function(){}
  var updateColorOders = function() {
    var dataColors = {};
    $('.custom-color-list').find('.control-group').each(function(index) {
      if (!dataColors.hasOwnProperty($(this).data('class'))) {
        var data = {};
        data.class = $(this).data('class');
        if (data.class == '' || data.class == 'undefined') data.class = data.name.replace(/\s+/g, "_");
        dataColors[index] = data.class.toLowerCase();
      }
    });

    saveColorFile(dataColors);
  }
  var updateColors = function(btn) {
    //init color on input select that custom color
    var $btn = $(btn),
      value = $btn.val(),
      nameColors = $btn.attr('name');
    $(document).find('.t4-input-color').each(function() {
      var $input = $(this),
        $val = $(this).data('val');
      if ($val == nameColors.replace(/_/g, " ")) {
        $input.closest('.color-preview').find('.preview-icon').data('bgcolor', value);
        $input.closest('.color-preview').find('.preview-icon').css({ 'background-color': value });
      }
    });
    // init palette select custom color
    var $palettes = $(document).find('.pattern-list').find('.pattern');
    $palettes.each(function() {
      if ($(this).data('background_color') == nameColors) {
        $(this).find('span.background_color').css({ background: value });
      }
      if ($(this).data('text_color') == nameColors) {
        $(this).find('span.text_color').css({ background: value });
      }
      if ($(this).data('link_color') == nameColors) {
        $(this).find('span.link_color').css({ background: value });
      }
      if ($(this).data('link_hover_color') == nameColors) {
        $(this).find('span.link_hover_color').css({ background: value });
      }
    });
    $btn.closest('.control-group').data('color', value);
  }
  var doRemove = function(btn) {
    var $btn = $(btn),
      url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Customcolors&id=' + tempId,
      name = $btn.closest('.control-group').data('class'),
      type = $btn.data('status') ? $btn.data('status') : '';
    if (!name) return;
    T4Admin.Confirm(T4Admin.langs.customColorRemoveConfirm, function(ans) {
      if (ans) {
        $.post(url, { task: 'remove', name: name, type: type }).then(function(response) {
          if (response.ok && response.status) {
            var input_selected_color = $(document).find('.t4-input-color');
            input_selected_color.each(function() {
              var $input = $(this),
                $name_color = $(this).data('val');
              if ($name_color.replace(/ /g, '_') == name) {
                $input.data('val', 'none');
                $input.closest('.color-preview').find('.preview-icon').css({ background: 'inherit' });
                $input.val('none').trigger('change');
              }
            });
            // init palette select custom color
            var $palettes = $(document).find('.pattern-list').find('.pattern');
            $palettes.each(function() {
              if ($(this).data('background_color') == name) {
                $(this).find('span.background_color').css({ background: 'inherit' });
              }
              if ($(this).data('text_color') == name) {
                $(this).find('span.text_color').css({ background: 'inherit' });
              }
              if ($(this).data('link_color') == name) {
                $(this).find('span.link_color').css({ background: 'inherit' });
              }
              if ($(this).data('link_hover_color') == name) {
                $(this).find('span.link_hover_color').css({ background: 'inherit' });
              }
            });
            if (response.status == 'loc') {
              $btn.closest('.control-group').slideUp(500, function() {
                $(this).remove();
                $(document).trigger('user-colors-update');
              })
            } else {
              $btn.closest('.control-group').data('color', response.color);
            }
            // update status
            T4Admin.Messages(T4Admin.langs.customColorDeleted, 'message');
          } else {
            T4Admin.Messages(response.error, 'error');
          }
        });

      } else {
        return false;
      }
    }, '');
  }
})