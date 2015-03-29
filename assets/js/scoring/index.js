'use strict';

window['orbital'] = window['orbital'] || {};

(function (global) {
    var scoring = global['scoring'] = global['scoring'] || {};

    function Round(data) {
        this.name = m.prop(data.name);
        this.targets = data.targets.map(function (target) {
            return new RoundTarget(target);
        });

        this.targetFromArrowIndex = function (index) {
            var i = 0;
            while (index >= 0 && i < this.targets.length) {
                var target = this.targets[i];
                var arrows = target.arrow_count();

                if(arrows > index) {
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
            scoring.vm.arrow_index = 0;
            // hold arrows from input
            scoring.vm.arrow_buffer = [];

            scoring.vm.arrows = [];

            //TODO connect to socket.io
            //  load arrows
            //  sub to score
        },
        submit_buffer: function() {
            var vm = scoring.vm;

            m.request({
                'method': 'POST',
                'url': vm.urls['add'],
                'data': {
                    'index': vm.arrow_index - vm.arrow_buffer.length,
                    'arrows': vm.arrow_buffer
                }
            }).then(function() {
                vm.arrow_buffer = [];
            });
        }
    };
})(window['orbital']);