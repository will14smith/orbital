(function ($) {
    'use strict';

    $.fn.collection = function (name, options) {
        options = $.extend({}, {
            allowAdd: true,
            allowRemove: false
        }, options);

        var $collectionHolder = this;

        if (options.allowAdd) {
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
        }

        if (options.allowRemove) {
            $collectionHolder.find('div div.form-row').each(function () {
                addTagFormDeleteLink($(this));
            });
        }

        function addTagForm($collectionHolder, $newLink) {
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
            $newLink.before($newFormDiv);

            if (options.allowRemove) {
                addTagFormDeleteLink($newFormDiv);
            }
        }

        function addTagFormDeleteLink($el) {
            var $removeLink = $('<a href="#">Remove</a>');

            $removeLink.on('click', function(e) {
                e.preventDefault();

                // remove the li for the tag form
                $el.remove();
                $removeLink.remove();
            });

            $el.after($("<div />").append($removeLink));
        }
    };
})(jQuery);