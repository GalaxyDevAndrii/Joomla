(function($) {
	var Typelists = window.Typelists || {};

	var Layout = function($el){
		Layout.$container = $el;
		Layout.$input = $el.closest('.typelist').find('.typelist-input');
		Layout.$tpl = $el.closest('.typelist').find('.layout-title');
	}

	Layout.prototype.init = function () {
		//init show name layout 
		Layout.$input.on('change', function(e) {
		    var $layoutTpl = $(this).val();
		    Layout.$tpl.html($layoutTpl+' layout');
		});
	}

	Layout.prototype.get = function () {
		// find all fields and get value
		var $inputs = Layout.$container.find('[name^="typelist-layout["]');
		var result = {};
		$inputs.each(function(i, el) {
			var $input = $(el),
				name = el.name.match(/typelist-layout\[(.*)\]/)[1],
				value = $input.val();
				if(name == 'layout'){
					value = Layout.prototype.generatedLayout();
				}
			result[name] = value;
		})
		return result;
	}

	Layout.prototype.set = function (value) {
		if(typeof value == 'undefined') return false;
		var $inputs = Layout.$container.find('[name^="typelist-layout["]');
		$inputs.each( function(i, el) {
			var $input = $(el),
				name = el.name.match(/typelist-layout\[(.*)\]/)[1];
			if(name == 'layout'){
				Layout.prototype.updateLayout(value[name]);
				value[name] = JSON.stringify(value[name]);
			}
			if($input.hasClass('minicolors')){
				$input.minicolors('value',value[name]);
			}
			if($input.is('select')){
				$input.chosen({with:'100%',disable_search:true});
				$input.trigger('liszt:updated');
			}
			if($input.hasClass('t4-input-media')){
				if($input.closest('.field-media-wrapper').find('.add-on.field-media-preview').length){
						$input.closest('.field-media-wrapper').data('fieldMedia').setValue(value[name]);
				}
			}
			var val = value[name] ? value[name] : '';
			if ($input.val() != val) $input.val(val).trigger('change');
		})
	}
	//start update layout section when change on select input;
	Layout.prototype.updateLayout = function($data){
		if ($data === undefined || $data.length == 0) return false;
		var $layoutBuider = Layout.$container.find('#t4-layout-builder'),
		$section = $data.sections,$html = '';
		if ($section === undefined || $section.length == 0) return false;
		for (var i = 0; i < $section.length; i++) {
			$html += this.sectionRender($section[i]);
		}
		$layoutBuider.html($html);
		T4Layout.jqueryUiLayout();
	}
	Layout.prototype.renderAttrs = function($data,$content){
		var $dataAttr = '';
		$.each($data,function(index,value){
			if(index != 'contents'){
				$dataAttr += ' data-'+index+'="'+$.fn.htmlspecialchars(value)+'"';
			}
		});
		return $dataAttr;
	}
	Layout.prototype.sectionRender = function($section){
		var dataAttr = this.renderAttrs($section,false);
		var $layout_raw = '';
		if(typeof $section.name == 'undefined' || $section.name == '') $section.name = 'Section';
		$layout_raw += '<div class="t4-layout-section" '+dataAttr+'>';
		$layout_raw += '<div class="t4-section-settings clearfix">';
		$layout_raw += '<div class="pull-left"><strong class="t4-section-title">'+$section.name+'</strong></div>';
		$layout_raw += '<div class="pull-right"><ul class="t4-row-option-list">';
		$layout_raw += '<li><a class="t4-move-row" href="#" data-tooltip="Move"><i class="fal fa-arrows-alt"></i></a></li>';
		$layout_raw += '<li><a class="t4-row-options" href="#" data-tooltip="Configure"><i class="fal fa-cog fa-fw"></i></a></li>';
		$layout_raw += '<li><a class="t4-remove-row" href="#" data-tooltip="Remove"><i class="fal fa-trash-alt fa-fw"></i></a></li>';
		$layout_raw += '</ul></div></div>';
		$layout_raw += '<div class="t4-row-container"><div class="row">';
		for (var i = 0; i < $section['contents'].length; i++) {
			var $col_cls = 'col-md-'+$section['contents'][i].col;
			var $attrCol = this.renderAttrs($section['contents'][i],true);
			var extra_class = $section['contents'][i].extra_class || "";
			if($section['contents'][i].col == 'auto') $col_cls = "col-md";
			$layout_raw += '<div class="t4-col t4-layout-col '+$col_cls + " "+ extra_class +'" '+$attrCol+'>';
			$layout_raw += '<div class="col-inner clearfix">';
			if($section['contents'][i].type == 'component') $section['contents'][i].name = 'Component';
			$layout_raw += '<span class="t4-column-title">'+ $section['contents'][i].name+'</span>';
			$layout_raw += '<span class="t4-col-remove hidden" title="Remove column" data-content="Remove column"><i class="fal fa-minus"></i> </span>';
			$layout_raw += '<span class="t4-admin-layout-vis" title="Click here to hide this position on current device layout" style="display:none;" data-idx="'+i+'"><i class="fal fa-eye"></i></span>';
			$layout_raw += '<a class="t4-column-options" href="#"><i class="fal fa-cog fa-fw"></i></a>';
			$layout_raw += '</div></div>';
		}
		$layout_raw += '</div></div>';
		$layout_raw += '<a class="t4-add-row" href="#"><i class="fal fa-plus"></i><span>Add Row</span></a></div>';


		return $layout_raw;
	}
	//end update;
	// Generate Layout JSON
	Layout.prototype.generatedLayout =  function(){
		var item = [];
		Layout.$container.find('#t4-layout-builder').find('.t4-layout-section').each(function(index){
			var $row 		= $(this),
				rowIndex 	= index,
				rowObj 		= $row.data();
			var padding_responsive = (typeof rowObj.padding_responsive == 'object') ? JSON.stringify(rowObj.padding_responsive) : rowObj.padding_responsive;
			var margin_responsive = (typeof rowObj.margin_responsive == 'object') ? JSON.stringify(rowObj.margin_responsive) : rowObj.margin_responsive;

			item[rowIndex] = $.extend({
				'contents'				: []
			},rowObj);
			delete item[rowIndex].sortableItem;
			delete item[rowIndex].uiresizable;
			// Find Column Elements
			$row.find('.t4-layout-col').each(function(index) {
				var $column 	= $(this),
					colIndex 	= index,
					colObj 		= $column.data();
				item[rowIndex].contents[colIndex] = $.extend({
					'type'			: colObj.type,
					'name'			: colObj.name || '',
					'col'			: colObj.col || '',
					'xl'			: colObj.xl || '',
					'lg'			: colObj.lg || '',
					'md'			: colObj.md || '',
					'sm'			: colObj.sm || '',
					'xs'			: colObj.xs || '',
					'hidden_lg'		: colObj.hidden_lg || '',
					'hidden_xl'		: colObj.hidden_xl || '',
					'hidden_md'		: colObj.hidden_md || '',
					'hidden_sm'		: colObj.hidden_sm || '',
					'hidden_xs'		: colObj.hidden_xs || '',
					'style'			: colObj.style || '',
					'extra_class'	: colObj.extra_class || '',
					'extra_params'	: $.fn.htmlspecialchars_decode(colObj.extra_params) || '',
					},colObj);
					if(colObj.type == 'module'){
						item[rowIndex].contents[colIndex].title = colObj.title;
						item[rowIndex].contents[colIndex].modname = colObj.modname;
					}
				delete item[rowIndex].contents[colIndex].sortableItem;
				delete item[rowIndex].contents[colIndex].uiresizable;


			});
		});
		var layout = {'sections':item,'settings':{"assets": {},"fonts": {}}};
		return layout;
	}
	
	Typelists['layout'] = Layout;
	window.Typelists = Typelists;
})(jQuery);