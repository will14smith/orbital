(function ($) {
    function initSelectWidget($element) {
        var name = $element.attr('name');
        var value = $element.data('value');
        var items = {};
        $("li", $element).each(function () {
            var $this = $(this);

            var data = $this.data();
            data.text = $this.text();

            items[data.id] = data;
        });

        // create the hidden input field
        var $input = $("<input />")
            .attr('type', 'hidden')
            .attr('name', name)
            .val(value);
        $element.after($input);

        // create the selected item ui
        var $label = $("<div />")
            .addClass("select-widget__label");
        $element.after($label);

        updateValue();

        // bind events
        $label.click(open);
        $('li', $element).click(function () {
            updateValue($(this).data('id'));
            close();

            return false;
        });
        $element.on('selectwidget:set', function(_, value) {
           updateValue(value);
        });

        var isOpen = false;

        function open() {
            if (isOpen) return close();
            isOpen = true;

            var pos = $label.position();

            $(document).on('click', documentClose);
            $element
                .css('position', 'absolute')
                .css('zIndex', '10')
                .css('top', pos.top + $label.outerHeight())
                .css('left', pos.left)
                .show();

            return false;
        }

        function documentClose(evt) {
            if ($.contains($element[0], evt.target)) {
                return;
            }

            close();
        }

        function close() {
            isOpen = false;

            $(document).off('click', documentClose);
            $element.hide();
        }

        function updateValue(newValue) {
            value = newValue;

            updateLabel();
            updateSelected();
            $input.val(value);

            $element.trigger('selectwidget:update', [value, getDataForValue(value)]);
        }

        function updateLabel() {
            var data = getDataForValue(value);

            if (data === null) {
                $label.addClass('select-widget__label--novalue');
                $label.text('Select a value');
            } else {
                $label.removeClass('select-widget__label--novalue');
                $label.text(data.text);
            }
        }

        function updateSelected() {
            $("li", $element).removeClass('selected');

            var data = getDataForValue(value);
            if (data !== null) {
                $('li[data-id=' + data.id + ']', $element).addClass('selected');
            }
        }

        function getDataForValue(value) {
            if (value in items) {
                return items[value];
            }

            return null;
        }
    }

    $.fn.selectWidget = function (method) {
        var args = arguments;

        this.each(function () {
            var $this = $(this);

            if (method === void 0) {
                initSelectWidget($this);
            } else {
                $this.trigger('selectwidget:set', [args[1]]);
            }
        });

        return this;
    };
})(jQuery);
