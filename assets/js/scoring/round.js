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

    scoring.Round = Round;
    scoring.RoundTarget = RoundTarget;
})(window.orbital.scoring);