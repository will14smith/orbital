'use strict';

(function($) {

    $(function() {
        $('.form-input').each(function() {
            var $this = $(this);

            // toggle the menu on click
            $this.on('change', function() {
                updateClass()
            });
            updateClass();

            function updateClass() {
                if($this.val() != '') {
                    $this.addClass('with-value')
                } else {
                    $this.removeClass('with-value')
                }
            }
        });
    });

})(jQuery);