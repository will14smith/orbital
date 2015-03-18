'use strict';

(function($) {

    $(function() {
        function connect() {
            var $this = $(this);

            // toggle the menu on click
            $this.on('change', function() {
                updateClass()
            });
            updateClass();

            function updateClass() {
                if($this.val() != '' || $this.prop("tagName") == 'SELECT') {
                    $this.addClass('with-value')
                } else {
                    $this.removeClass('with-value')
                }
            }
        }

        $('.form-input').each(connect);

        $.fn.input_connect = function() {
            $('.form-input', this).each(connect);
            return this;
        }
    });

})(jQuery);