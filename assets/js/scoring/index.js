'use strict';

window['orbital'] = window['orbital'] || {};

(function (global) {
    var scoring = global['scoring'] = global['scoring'] || {};

    function Round(data) {
        this.name = m.prop(data.name);
        this.targets = data.targets.map(function (target) {
            return new RoundTarget(target);
        });
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
        init: function (round, score_id, input) {
            scoring.vm.round = new Round(round);

            //TODO score, etc...
        }
    };

    global['scoring'] = scoring;
})(window['orbital']);