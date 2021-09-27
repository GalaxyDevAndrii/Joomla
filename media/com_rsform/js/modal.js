window.addEventListener('DOMContentLoaded', function(){
    var elements = document.querySelectorAll('.rsform-simple-image-modal > img');

    if (elements.length > 0) {
        for (var i = 0; i < elements.length; i++) {
            elements[i].onclick = function() {
                RSFormPro.openImageModal(this, 'href');
            }
        }
    }
});