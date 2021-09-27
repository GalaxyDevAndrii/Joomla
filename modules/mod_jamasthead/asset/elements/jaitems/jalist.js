(function ($){
    var JAList = function (element) {
        var $element = this.$element = $(element);

        // bind click event for button
        $element.bindActions ('.action', this);

        // make textarea auto height
        $element.elasticTextarea('delete_row clone_row updated');

        // make all field as ignore save
        $element.find ('input, textarea, select').not('.acm-object').data('ignoresave', 1);

        // reset index
        //$element.data('index', 0);

        // trigger updated event for element after built
        setTimeout(function(){$element.trigger('updated')}, 100);
    };

    // Actions
    JAList.prototype.delete_row = function (btn) {
        var $btn = $(btn),
            $row = $btn.parents('tr').first();
        if (!$row.hasClass('first')) {
            $row.remove();
        }
    };

    JAList.prototype.clone_row = function (btn) {
        var $btn = $(btn),
            $row = $btn.parents('tr').first(),
            idx = this.$element.data('index');
        this.$element.data('index', ++idx);
        jaTools.fixCloneObject($row.jaclone(idx), true);
		jQuery('.field-media-wrapper').fieldMedia();
		jQuery(".hasTooltip").tooltip({"html": true,"container": "body"});
    };

    function Plugin() {
        return new JAList(this);
    }

    $.fn.jalist             = Plugin;
    $.fn.jalist.Constructor = JAList;

})(jQuery);


