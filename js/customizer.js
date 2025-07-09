/**
 * Customizer Live Preview
 */

(function($) {
    'use strict';
    
    // Primary Color
    wp.customize('movieflix_primary_color', function(value) {
        value.bind(function(newval) {
            $(':root').css('--primary-color', newval);
        });
    });
    
    // Footer Text
    wp.customize('movieflix_footer_text', function(value) {
        value.bind(function(newval) {
            $('.footer-content p').text(newval);
        });
    });
    
})(jQuery);