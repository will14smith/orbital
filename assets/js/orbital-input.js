'use strict';

(function($) {

    $(function() {
        function connect() {
            var $this = $(this);

            if($this.prop("tagName") == 'SELECT'
                || $this.attr('type') == 'file') {
                $this.addClass('with-value');
                return;
            }

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
        }

        $('.form-input').each(connect);

        $.fn.input_connect = function() {
            $('.form-input', this).each(connect);
            return this;
        }
    });

})(jQuery);