(function($){
    "use strict";

    var init = function (group) {
    	// find toggle field, then base on the value to enable/disable params in group
    	var name = 'jform[params][toggle-' + group + ']',
    		$toggle = $('input[name="' + name + '"]');
    	if (!$toggle.length) return;
    	toggle(group, $toggle.prop('checked'));
    };

    var toggle = function ($toggle) {
        if ($toggle.closest('.sub-group').length) return toggleSubgroup($toggle);
        if ($toggle.closest('.top-group').length) return toggleTopgroup($toggle);
    }

    var toggleTopgroup = function ($toggle) {

    	var enabled = $toggle.prop('checked'),
    		$topgroup = $toggle.closest('.top-group'),
    		$subgroups = $topgroup.find('.sub-group').filter(function(){return !$(this).find('.legend.tools_group').length});

    	// standard
    	if (enabled) {
			$subgroups.removeClass('disabled').find(':input').prop('readonly', false);
            $('#font-filter-heading-filter,#font-filter-body-filter').prop('readonly', false);
            $subgroups.find('.sub-group-params').off('click')
		} else {
			$subgroups.addClass('disabled').find(':input').prop('readonly', true);

            $subgroups.find('.sub-group-params').on('click', function (e) {
                if (!e.isTrigger && $toggle.attr('type') == 'checkbox') {
                    if (!$toggle.data('popover')) {
                        $toggle.popover( {
                            placement: 'bottom',
                            trigger: 'manual',
                        } );
                    }
                    $toggle.data('popover').show();
                }
            }).on('mouseleave', function() {
                var popover = $toggle.data('popover');
                if (popover) popover.hide();
            })
		}
    }

    var toggleSubgroup = function ($toggle) {

        var enabled = $toggle.prop('checked'),
            $subgroup = $toggle.closest('.sub-group'),
            $subgroupParams = $subgroup.find('.sub-group-params'),
            $controls = $subgroupParams.find('.control-group').not('.subgroup-toggle');

        // move subgroup toggle into params
        if ($toggle.closest('.sub-legend-group').length) {
            $('<div class="control-group subgroup-toggle">').append($toggle.closest('.controls')).prependTo($subgroupParams);
        }

        // standard
        if (enabled) {
            $controls.removeClass('disabled').find(':input').prop('readonly', false);
            $subgroupParams.off('click').off('mouseleave');

            var popover = $toggle.data('popover');
            if (popover) popover.hide();
        } else {
            $controls.addClass('disabled').find(':input').prop('readonly', true);

            $subgroupParams.on('click', function (e) {
                if (!e.isTrigger && $toggle.attr('type') == 'checkbox') {
                    if (!$toggle.data('popover')) {
                        $toggle.popover( {
                            placement: 'bottom',
                            trigger: 'manual',
                        } );
                    }
                    $toggle.data('popover').show();
                }
            }).on('mouseleave', function() {
                var popover = $toggle.data('popover');
                if (popover) popover.hide();
            })
        }
    }

    $(document).ready(function() {
	    $('[name^="jform[params][toggle-"]').each(function () {

            var $toggle = $(this);

            $toggle.on('click', function(e) {
                var enabled = $toggle.prop('checked');
                if(!enabled && $toggle.data('group') != 'system'){
                    T4Admin.Confirm(T4Admin.langs.OverRideConfirm, function(conf){
                        if(conf){
                            $toggle.prop('checked', false);
                            toggle($toggle);
                        }else{
                            return false;
                        }
                    });
                    return false;
                }else{
                    toggle($toggle);
                }
            });
	    }).on('change', function() {
			toggle($(this));
	    })//.trigger('change')

	}).on('t4.ready', function() {
        $('[name^="jform[params][toggle-"]').trigger('change');
    })

})(jQuery);
