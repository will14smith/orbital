'use strict';

(function($) {

    $(function() {
        $('[icac-toggle]').each(function() {
            var $this = $(this);

            // get the target menu
            var targetSelector = $this.attr('icac-toggle');
            var $target = $(targetSelector);

            // toggle the menu on click
            $this.on('click', function() {
                $target.slideToggle();
            });

            // show / hide when media query changes
            var mqChangeHandler = function(mq) {
                if(mq.matches) {
                    $target.hide();
                } else {
                    $target.css('display', 'flex');
                }
            };

            var mq = window.matchMedia('(max-width: 960px)');
            mq.addListener(mqChangeHandler);
            mqChangeHandler(mq);
        });
    });

})(jQuery);