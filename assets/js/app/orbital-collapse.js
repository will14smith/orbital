(function($) {
    'use strict';

    $.fn.collapse = function(handle) {
        var $this = this;

        $this.hide();

        var $handle;
        if(typeof handle === 'string') {
            $handle = $("<a />")
                .attr('href', '#')
                .text('Expand ' + handle);
            $this.after($handle);
        } else {
            $handle = $(handle);
        }

        $handle.click(function (e) {
            e.preventDefault();

            $handle.hide();
            $this.slideDown();
        });
    };

    $(function() {
        $("[orbital-collapse]").each(function() {
            var $this = $(this);

            $this.collapse($this.attr('orbital-collapse'));
        });
    });

})(jQuery);