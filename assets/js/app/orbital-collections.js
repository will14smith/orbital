(function ($) {
    'use strict';

    $.fn.collection = function (name, options) {
        options = $.extend({
            addCallback: function() { }
        }, options);

        var $collectionHolder = this;



        // setup an "add a tag" link
        var $addLink = $('<a href="#">Add a ' + name + '</a>');
        var $newLinkDiv = $('<div />').append($addLink);

        // add the "add a tag" anchor and li to the tags ul
        $collectionHolder.append($newLinkDiv);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        $addLink.on('click', function (e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new tag form (see next code block)
            addTagForm($collectionHolder, $newLinkDiv);
        });


        function addTagForm($collectionHolder, $newLinkLi) {
            // Get the data-prototype explained earlier
            var prototype = $collectionHolder.data('prototype');

            // get the new index
            var index = $collectionHolder.data('index');

            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have
            var newForm = prototype
                .replace(/__name__label__/g, name + ' #' + index)
                .replace(/__name__/g, index);

            // increase the index with one for the next item
            $collectionHolder.data('index', index + 1);

            // Display the form in the page in an li, before the "Add a tag" link li
            var $newFormDiv = $('<div />').append(newForm).inputConnect();
            $newLinkLi.before($newFormDiv);

            options.addCallback($newFormDiv);
        }
    };
})(jQuery);