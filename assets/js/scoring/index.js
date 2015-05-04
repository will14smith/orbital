window.orbital = window.orbital || {};
window.orbital.scoring = window.orbital.scoring || {};

(function (scoring) {
    'use strict';

    function Round(data) {
        this.name = m.prop(data.name);
        this.targets = data.targets.map(function (target) {
            return new RoundTarget(target);
        });

        /*jshint camelcase: false */
        this.totalArrows = m.prop(data.total_arrows);
        /*jshint camelcase: true */

        this.targetFromArrowIndex = function (index) {
            var i = 0;
            while (index >= 0 && i < this.targets.length) {
                var target = this.targets[i];
                var arrows = target.arrowCount();

                if (arrows > index) {
                    return target;
                }

                index -= arrows;
                i++;
            }

            return this.targets[i];
        };
    }

    function RoundTarget(data) {
        /*jshint camelcase: false */

        this.scoringZones = m.prop(data.scoring_zones);
        this.distance = m.prop(data.distance);
        this.target = m.prop(data.target);
        this.arrowCount = m.prop(data.arrow_count);
        this.endSize = m.prop(data.end_size);

        /*jshint camelcase: true */
    }

    // model arrows

    scoring.vm = {
        init: function (round, scoreId, input, urls) {
            scoring.vm.urls = urls;

            scoring.vm.round = new Round(round);

            scoring.vm.input = input;
            // current arrow input
            scoring.vm.arrowIndex = 0;
            // hold arrows from input
            scoring.vm.arrowBuffer = [];

            scoring.vm.arrows = [];
            scoring.vm.socket = io(urls['socket.io']);
            scoring.vm.socket.on('arrows', scoring.vm.handleArrows);
            scoring.vm.socket.on('arrow', scoring.vm.handleArrow);

            scoring.vm.socket.emit('sub_score', scoreId);
        },

        getArrow: function (index) {
            var arrows = scoring.vm.arrows;

            if (arrows.length <= index) {
                return null;
            }

            return arrows[index];
        },

        submitBuffer: function () {
            var vm = scoring.vm;

            m.request({
                'method': 'POST',
                'url': vm.urls.add,
                'data': {
                    'index': vm.arrowIndex - vm.arrowBuffer.length,
                    'arrows': vm.arrowBuffer
                }
            }).then(function (data) {
                if (data.success) {
                    vm.arrowBuffer = [];
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
