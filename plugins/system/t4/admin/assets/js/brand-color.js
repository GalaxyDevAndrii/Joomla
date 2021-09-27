jQuery(function($) {
  initMinicolors();

  $('body').on('subform-row-add', initMinicolors);

  function initMinicolors(event, container) {
    container = container || document;
    $(".themeConfigModal").append($('<div class="t4-theme-color"></div>'));
    $(container).find('.t4-custom-color-spec').each(function() {
      var $this = $(this);
      $this.spectrum({
        type: "color",
        showPalette: false,
        showInput: true,
        allowEmpty:false,
        showInitial: true,
        color:true,
        appendTo: ".t4-theme-color",
        preferredFormat: "hex6",
        palette: [],
        hide: function(color){
          $this.trigger('change');
        },
        beforeShow: function(color){
          if($this.hasClass('t4-palette-color-spec')){
            return false;
          }
          if($('.t4-theme-color').is(":hidden")){
            $('.t4-theme-color').show();
          }
        }
      });
      $this.on("dragstop.spectrum", function(e, color) {
          $this.trigger('change'); // #ff0000
      });
    });
  }
});