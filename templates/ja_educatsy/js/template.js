jQuery(document).ready(function() {
    jQuery(document).on("scroll", onScroll);
    onepageNavLinks = jQuery('.onepage .t4-navbar .nav-link');

    function onScroll(event){
        var scrollPos = jQuery(document).scrollTop();
        onepageNavLinks.each(function () {
            var currLink = jQuery(this);
            var refElement = jQuery(currLink.attr("href"));
            if (refElement.position().top <= scrollPos /* && refElement.position().top + refElement.height() > scrollPos*/ ) {
                onepageNavLinks.removeClass("active");
                currLink.addClass("active");
            }
            else{
                currLink.removeClass("active");
            }
        });
    }
});
