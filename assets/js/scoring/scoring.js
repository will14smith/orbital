window.orbital = window.orbital || {};
window.orbital.scoring = window.orbital.scoring || {};

(function (scoring) {
    'use strict';

    function setupSocketIO(urls, scoreId) {
        scoring.vm.socket = io(urls['socket.io']);
        scoring.vm.socket.on('arrows', scoring.vm.handleArrows);
        scoring.vm.socket.on('arrow', scoring.vm.handleArrow);

        scoring.vm.socket.emit('sub_score', scoreId);
    }

    scoring.vm = {
        init: function (round, scoreId, input, urls) {
            scoring.vm.urls = urls;

            scoring.vm.round = new scoring.Round(round);

            scoring.vm.input = input;
            scoring.vm.arrowIndex = 0;
            scoring.vm.arrowBuffer = [];

            scoring.vm.arrows = [];

            scoring.vm.inputController = new scoring.input({
                buffer: scoring.vm.arrowBuffer,
                submitBuffer: scoring.vm.submitBuffer,
                keyboard: true
            });

            setupSocketIO(urls, scoreId);
        },

        getArrow: function (index) {
            var arrows = scoring.vm.arrows;

            if (arrows.length <= index) {
                return null;
            }

            return arrows[index];
        },

        submitBuffer: function (buffer) {
            var vm = scoring.vm;

            m.request({
                'method': 'POST',
                'url': vm.urls.add,
                'data': {
                    'index': vm.arrowIndex,
                    'arrows': buffer
                }
            }).then(function (data) {
                if (data.success) {
                    buffer.splice(0, buffer.length);
                } else {
                    throw "ERROR";
                }
            });
        },
        complete: function () {
            var vm = scoring.vm;

            m.request({
                'method': 'POST',
                'url': vm.urls.complete
            }).then(function (data) {
                if (data.success) {
                    document.location = data.url;
                } else {
                    throw "ERROR";
                }
            });
        },

        handleArrows: function (data) {
            var arrows = data.arrows;
            /*jshint camelcase: false */
            var score_id = data.score_id;
            /*jshint camelcase: true */

            m.startComputation();
            arrows.forEach(function (arrow) {
                scoring.vm.arrows[arrow.number] = arrow;
            });
            scoring.vm.arrowIndex = arrows.length;
            m.endComputation();
        },
        handleArrow: function (data) {
            var arrow = data.arrow;
            /*jshint camelcase: false */
            var score_id = data.score_id;
            /*jshint camelcase: true */

            m.startComputation();
            scoring.vm.arrows[arrow.number] = arrow;
            m.endComputation();
        }
    };
})(window.orbital.scoring);
