(function ($) {
    'use strict';

    function connect() {
        /*jshint validthis: true */
        var $this = $(this);

        if ($this.prop("tagName") === 'SELECT'
            || $this.prop("tagName") === 'DIV'
            || $this.attr('type') === 'file') {

            $this.addClass('with-value');
            return;
        }

        // toggle the menu on click
        $this.on('change', function () {
            updateClass();
        });
        updateClass();

        function updateClass() {
            if ($this.val()) {
                $this.addClass('with-value');
            } else {
                $this.removeClass('with-value');
            }
        }
    }

    $.fn.inputConnect = function () {
        $('.form-input', this).each(connect);
        return this;
    };

    $(function () {
        $('.form-input').each(connect);
    });

})(jQuery);
