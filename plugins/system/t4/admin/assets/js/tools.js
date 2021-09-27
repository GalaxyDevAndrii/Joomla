/*	export
 - site setting
 - navigation
 - theme setting
 	- default {brand color, typo, page. headding}
 	- css custom
 	- font custom
 - layout setting
 	- bock
 	- config
*/

jQuery(document).ready(function($) {
	var editor, editorVariabes, editorVarCustom;
	// export
	$('.btn-action[data-action="tool.export"]').click(function() {
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Export&id=' + tempId;
		var groups = '';
		// get groups to export
		if ($('#tool-export-groups').val()) {
			var groups = $('.tool-export-groups-wrap input:checked').map(function() {return this.value}).toArray();
			if (!groups.length) {
				alert(T4Admin.langs.toolExportNoSelectedGroupsWarning);
				return;
			}
			groups =  groups.join(',');
		}
		// location.href = url;
		$.post(url, {task:'export',groups:groups}).done(function(response){
			if(response.data){
				finishExport();
				window.location.href = response.data;
			}else if(response.error){
				T4Admin.Messages(response.error,'error');
			}
		});
	})
	var finishExport = function(){
		T4Admin.Messages(T4Admin.langs.ExportDataSuccessfuly,'message');
		clearForm('export');
	}
	$('#tool-export-groups').on('change', function() {
		var val = $(this).val();
		if (val) {
			// selected, show groups with uncheck all
			$('.tool-export-groups-wrap').show().find('input').prop('checked', false);
		} else {
			$('.tool-export-groups-wrap').hide();
		}
	})
	var proccessImportAjax = function(data,url){
		var i = 0;
        $.ajax({
            url: url,
            data: data,
            type: 'post',
            processData: false,
            cache: false,
            contentType: false,
            xhr: function () {
				var xhr = new window.XMLHttpRequest();

				// Upload progress
				 xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        if (i == 0) {
                            i = 1;
                            var percentComplete = 0;
                            var id = setInterval(frame, 10);

                            function frame() {
                                if (percentComplete >= 100) {
                                    clearInterval(id);
                                    i = 0;
                                } else {
									percentComplete++;
                                }
                            }
                        }
                    }
                }, false);

                return xhr;
			}
        })
        .done(function (res) {
			proccessImportData(res);
		})
		.fail(function (error) {
            console.log(error);
        });
	}

	// handle file selected for import
	$('input[name="tool-import-file"]').on('change', function (e) {
		
		var files = e.originalEvent.target.files || e.originalEvent.dataTransfer.files;
        if (!files.length) {
            return;
        }
		var file = files[0];
		
        var data = new FormData;
        data.append('package', file);
		data.append('installtype', 'upload');
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Import&task=Import&id=' + tempId;
		proccessImportAjax(data,url);
	})

	// modal import bundle 
	var $importModal = $('#t4-tool-import-modal');
	if (!$importModal.parent().is('.themeConfigModal')) $importModal.appendTo($('.themeConfigModal'));
	$('.btn-action[data-action="tool.importModal"]').click(function() {
		$importModal.show();
	});

	var proccessImportData = function (data) {
		if (!data.dir) {
			clearImport();
			alert(T4Admin.langs.toolImportDataFileError);
			return;
		}
		var setting = data.setting;
		// var setting = ['site','theme','layout'];
		var params = data.params;
		// find all group and check if group data available
		var count = 0;
		$('.tool-import-form [type="checkbox"]').each(function(){
			var $group = $(this),
				group = $group.val(),
				available = false;
				group_name = group.replace('typelist-','');
			for(var a=0; a < setting.length; a++) {
				var name = setting[a];
				if (name == group_name) {
					available = true;
					count++;
					break;
				}
			}

			if (available) {
				$group.prop('checked', true).prop('disabled', false).closest('li').removeClass('disabled');
			} else {
				$group.prop('checked', false).prop('disabled', true).closest('li').addClass('disabled');
			}
		})

		if (count) {
			$('.tool-import-form').show();

			// bind action event
			$('.t4-btn[data-action="tool.import"]').off('click').on('click', function() {
				doImport(data);
			})
		} else {
			alert(T4Admin.langs.toolImportDataFileEmptyWarning);
			$('.tool-import-form').hide();
			return;
		}
	}

	var doImport = function (data) {
		var groups = $('.tool-import-form [type="checkbox"]').filter(':enabled:checked').map(function(){return this.value});
		var groups_data = [];
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=import&task=importing&id=' + tempId;
		for(var a=0;a < groups.length;a++){groups_data.push(groups[a])}
		console.log(groups_data);
		$.post(url,{data,groups:groups_data}).done(function(res){
			console.log(res);
			if((res.message && !res.success) || (res.error && res.error.length)){
				if(res.error && res.error.length) res.message = res.error.toString();
				alert(res.message);
				return false;
			}
			var params = res.data.params;
			for(var i=0; i<groups.length; i++) {
				var group = groups[i];
				for(var name in params) {
					if (name == group || name.startsWith(group + '_')) {
						updateValue(name, params[name]);
					}
				}
			}
			// clear form
			finishImport();
		});

	}

	var updateValue = function (name, val) {
		$('[name="jform[params][' + name + ']"]').val(val);
		if(name.startsWith('typelist-')){
			$('[name="jform[params]['+name+']"]').val(val).trigger('liszt:updated').trigger('change');
		}
	}

	var finishImport = function () {
		
		clearForm('import');
		alert(T4Admin.langs.toolImportDataDone);
		$('.t4-admin-save .btn-save').click();
	}

	var clearForm = function (task) {
		if(task == 'import'){
			$('input[name="tool-import-file"]').val('');
			$('.tool-import-form').hide();
		}
		if(task == 'export'){
			$('#tool-export-groups').val('').trigger('change');
		}
	}

	var initCssEditor = function(editor,data){
		if(!editor){
			editor = CodeMirror.fromTextArea(data,{
				lineNumbers: true,
				mode: "css",
				autofocus: true,
				tabsize: 2,
				firstLineNumber: 1
			});
		}else{
			editor.getDoc().setValue(data);
		}
		setTimeout(function() {
		   editor.refresh();
		},1);
	}


	// Edit custom css
	var $cssmodal = $('.t4-css-editor-modal');
	if (!$cssmodal.parent().is('.themeConfigModal')) $cssmodal.appendTo($('.themeConfigModal'));
	$('.t4-btn[data-action="tool.css"]').click(function() {
		// load current custom css
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=getcss&id=' + tempId;
		$.ajax(url).then(function(css) {
			// Show edit popup with current css
			$('body').addClass('t4-modal-open');
			$('#t4_code_css').text(css);
			var textArea = $('#t4_code_css').get(0);
			if(!editor){
				editor = CodeMirror.fromTextArea(textArea,{
					lineNumbers: true,
					mode: "css",
					autofocus: true,
					tabsize: 2,
					direction: (document.dir == 'rtl')  ? "rtl" : "ltr",
					firstLineNumber: 1
				});
			}else{
				editor.getDoc().setValue(css);
			}
			setTimeout(function() {
			   editor.refresh();
			},1);

			$cssmodal.show();

		})
	})
	$('body').on('click','.t4-css-editor-apply', function(e) {
	    var css = editor.getDoc().getValue("\n");
	    saveCss(css);
	});
	var saveCss = function (css) {
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=savecss&id=' + tempId;
		$.post(url, {css: css}).then(function() {
			// Save done, show message and reload preview
			$cssmodal.hide();
			$('body').removeClass('t4-modal-open');
			$(document).trigger('reload-preview');
			T4Admin.Messages(T4Admin.langs.customCssSaved,'message');
		})
	}


	// SCSS TOOLS
	var $scssmodal = $('#t4-tool-scss-modal');
	$scssmodal.appendTo($('.themeConfigModal'));
	$('.t4-btn[data-action="tool.scss"]').click(function() {
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=scss&task=load&id=' + tempId;
		$.ajax(url).then(function(data) {
			$('#t4-scss-editor-variables').text(data.variables);
			$('#t4-scss-editor-custom').text(data.custom);
			if(!editorVariabes){
				editorVariabes = CodeMirror.fromTextArea($('#t4-scss-editor-variables').get(0),{
					lineNumbers: true,
					mode: "css",
					autofocus: true,
					tabsize: 2,
					direction: (document.dir == 'rtl')  ? "rtl" : "ltr",
					firstLineNumber: 1
				});
			}else{
				editorVariabes.getDoc().setValue(data.variables);
			}
			setTimeout(function() {
			   editorVariabes.refresh();
				},1);
			if(!editorVarCustom){
				editorVarCustom = CodeMirror.fromTextArea($('#t4-scss-editor-custom').get(0),{
					lineNumbers: true,
					mode: "css",
					autofocus: true,
					tabsize: 2,
					direction: (document.dir == 'rtl')  ? "rtl" : "ltr",
					firstLineNumber: 1
				});
			}else{
				editorVarCustom.getDoc().setValue(data.custom);
			}
			setTimeout(function() {
			   editorVarCustom.refresh();
			},1);
			$scssmodal.show();
		});
	})

	$scssmodal.on('click', '.btn[data-action="apply"]', function(e) {
		T4Admin.Messages('Saving & Compiling ...', 'status');
		var scssVar = editorVariabes.getDoc().getValue("\n");
		var scssCustom = editorVarCustom.getDoc().getValue("\n");
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=scss&task=save&id=' + tempId;
		$.post(url, {
			variables: scssVar,
			custom: scssCustom
		}).then(function(resp) {
			if (resp.success == false) {
				T4Admin.Messages(resp.message, 'error');
			} else {
				T4Admin.Messages('Save & compile successfully!');
				// $scssmodal.hide();
			}
		})
	})
	$scssmodal.on('click','.nav-tabs li', function(e) {
		if(!$scssmodal.is(":hidden")){
			setTimeout(function() {
			   editorVariabes.refresh();
			},1);
			setTimeout(function() {
			   editorVarCustom.refresh();
			},1);
		}
	});
	$scssmodal.on('click', '.btn[data-action="clean"]', function(e) {
		T4Admin.Messages('Removing Local css ...', 'status');
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=scss&task=clean&id=' + tempId;
		$.post(url, {}).then(function(resp) {
			if (resp.error) {
				T4Admin.Messages(resp.error, 'error');
			} else {
				T4Admin.Messages('Remove successfully!')
			}
		})
	})


})
