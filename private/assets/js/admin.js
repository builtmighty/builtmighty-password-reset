/* admin.js */
jQuery(document).ready(function($) {
    
    // On nav click.
    $('span.builtpass-nav').on('click', function() {
        // Disable all active.
        $('span.tab-active').removeClass('tab-active');
        $('.builtpass-form-tab').hide();
        // Add active to current.
        $(this).addClass('tab-active');
        $('#' + $(this).data('id')).show();
    });

});