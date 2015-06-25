window.orbital = window.orbital || {};
window.orbital.scoring = window.orbital.scoring || {};

(function (scoring) {
    'use strict';

    var zoneData = {
        'metric': ['X', 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 'M']
    };

    scoring.input = function(options) {
        if(typeof options.submitBuffer !== 'function') {
            throw "must supply a submitBuffer";
        }

        this.buffer = options.buffer = (options.buffer || []);

        this.addArrow = function(score) { this.buffer.push(score); };
        this.removeArrow = function() { this.buffer.pop(); };
        this.hasArrowsInBuffer = function() { return this.buffer.length > 0; };
        this.submitBuffer = function() { options.submitBuffer(this.buffer); };

        if(options.keyboard) {
            setupKeyboard(this);
        }
    };

    function setupKeyboard(controller) {
        var addArrowToBuffer = function (score) {
            controller.addArrow(score);
        };

        window.addEventListener('keydown', function (e) {
            m.startComputation();

            if (e.keyCode >= 49 && e.keyCode <= 57) { addArrowToBuffer(e.keyCode - 48); }
            else if (e.keyCode == 48) { addArrowToBuffer(10); }
            else if (e.keyCode >= 97 && e.keyCode <= 105) { addArrowToBuffer(e.keyCode - 96); }
            else if (e.keyCode == 96) { addArrowToBuffer(10); }
            else if (e.keyCode == 77) { addArrowToBuffer('M'); }
            else if (e.keyCode == 88) { addArrowToBuffer('X'); }
            else if (e.keyCode == 8 && controller.hasArrowsInBuffer()) {
                e.preventDefault();

                controller.removeArrow();
            } else if (e.keyCode == 13 && controller.hasArrowsInBuffer()) {
                controller.submitBuffer();
            }

            m.endComputation();
        });
    }

    var scoreClickFactory = function (controller, score) {
        return function () {
            controller.addArrow(score);
        };
    };
    var undoClick = function (controller) {
        controller.removeArrow();
    };
    var saveClick = function (controller) {
        controller.submitBuffer();
    };
    var completeClick = function (controller) {
        controller.complete();
    };

    scoring.input.view = function (controller) {
        var children = [];

        if (controller.hasArrowsInBuffer()) {
            children.push(scoring.input.viewBuffer(controller));
        }
        children.push(scoring.input.viewButtons(controller));

        return m("div", {'class': 'input'}, children);
    };
    scoring.input.viewButtons = function (controller) {
        var buttons = zoneData['metric'].map(function (score) {
            return m("button", {onclick: scoreClickFactory(controller, score)}, score);
        });

        return m("div", {'class': 'buttons btn-group'}, buttons);
    };
    scoring.input.viewAccept = function (controller) {
        return m("div", {'class': 'accept'}, [
            m('button', {onclick: function() { completeClick(controller); }}, 'Sign & Complete')
        ]);
    };
    scoring.input.viewBuffer = function (controller) {
        var buffer = controller.buffer.map(function (score) {
            return m("div", score);
        });

        var undo = m("button", {onclick: function() { undoClick(controller); }}, "Undo");
        var save = m("button", {onclick: function() { saveClick(controller); }}, "Save");

        return m("div", {'class': 'buffer'}, buffer.concat([
            m('div', { 'class': 'btn-group' }, [undo, save])])
        );
    };

})(window.orbital.scoring);