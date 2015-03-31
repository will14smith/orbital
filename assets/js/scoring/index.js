'use strict';

window['orbital'] = window['orbital'] || {};

(function (global) {
    var scoring = global['scoring'] = global['scoring'] || {};

    function Round(data) {
        this.name = m.prop(data.name);
        this.targets = data.targets.map(function (target) {
            return new RoundTarget(target);
        });

        this.total_arrows = m.prop(data.total_arrows);

        this.targetFromArrowIndex = function (index) {
            var i = 0;
            while (index >= 0 && i < this.targets.length) {
                var target = this.targets[i];
                var arrows = target.arrow_count();

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
        this.scoring_zones = m.prop(data.scoring_zones);
        this.distance = m.prop(data.distance);
        this.target = m.prop(data.target);
        this.arrow_count = m.prop(data.arrow_count);
        this.end_size = m.prop(data.end_size);
    }

    // model arrows

    scoring.vm = {
        init: function (round, score_id, input, urls) {
            scoring.vm.urls = urls;

            scoring.vm.round = new Round(round);

            scoring.vm.input = input;
            // current arrow input
            scoring.vm.arrow_index = 0;
            // hold arrows from input
            scoring.vm.arrow_buffer = [];

            scoring.vm.arrows = [];
            scoring.vm.socket = io(urls['socket.io']);
            scoring.vm.socket.on('arrows', scoring.vm.handle_arrows);
            scoring.vm.socket.on('arrow', scoring.vm.handle_arrow);

            scoring.vm.socket.emit('sub_score', score_id);
        },

        get_arrow: function (index) {
            var arrows = scoring.vm.arrows;

            if (arrows.length <= index) {
                return null;
            }

            //TODO return arrows in buffer?

            return arrows[index];
        },

        submit_buffer: function () {
            var vm = scoring.vm;

            m.request({
                'method': 'POST',
                'url': vm.urls['add'],
                'data': {
                    'index': vm.arrow_index - vm.arrow_buffer.length,
                    'arrows': vm.arrow_buffer
                }
            }).then(function (data) {
                if (data.success) {
                    vm.arrow_buffer = [];
                } else {
                    //TODO handle unsuccessful response
                    console.log(data);
                    throw "ERROR";
                }
            });
        },
        complete: function () {
            var vm = scoring.vm;

            m.request({
                'method': 'POST',
                'url': vm.urls['complete']
            }).then(function (data) {
                if (data.success) {
                    document.location = data.url;
                } else {
                    //TODO handle unsuccessful response
                    console.log(data);
                    throw "ERROR";
                }
            });
        },

        handle_arrows: function (data) {
            var arrows = data['arrows'];
            var score_id = data['score_id'];

            //TODO check score_id

            m.startComputation();
            arrows.forEach(function (arrow) {
                scoring.vm.arrows[arrow.number] = arrow;
            });
            scoring.vm.arrow_index = arrows.length;
            m.endComputation();
        },
        handle_arrow: function (data) {
            var arrow = data['arrow'];
            var score_id = data['score_id'];

            //TODO check score_id

            m.startComputation();
            scoring.vm.arrows[arrow.number] = arrow;
            m.endComputation();

        }
    };
})(window['orbital']);