jQuery(document).ready(function($) {
    $('#publish').click(function(event){
        var customLinkVal = $('#_custom_link').val();
        if (!customLinkVal) {
            event.preventDefault();
            alert('Live Preview link is mandatory.');
            $('#_custom_link').focus();
        }
    });
});
