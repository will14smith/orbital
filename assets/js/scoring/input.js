'use strict';

window['orbital'] = window['orbital'] || {};

(function (global) {
    var scoring = global['scoring'] = global['scoring'] || {};

    var scoring_zone_data = {
        'metric': ['X', 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 'M']
    };

    scoring.input = {};

    var score_click_factory = function (score) {
        return function () {
            if (scoring.vm.arrow_index >= scoring.vm.round.total_arrows()) {
                return;
            }

            scoring.vm.arrow_buffer.push(score);
            scoring.vm.arrow_index++;
        }
    };
    var undo_click = function () {
        scoring.vm.arrow_buffer.pop();
        scoring.vm.arrow_index--;
    };
    var save_click = function () {
        scoring.vm.submit_buffer();
    };
    var complete_click = function () {
        scoring.vm.complete();
    };

    scoring.input.view = function () {
        var children = [];

        if (scoring.vm.arrow_buffer.length > 0) {
            children.push(scoring.input.view_buffer());
        }
        children.push(scoring.input.view_buttons());

        return m("div", {'class': 'input'}, children);
    };
    scoring.input.view_buttons = function () {
        var complete = scoring.vm.arrow_index >= scoring.vm.round.total_arrows();

        if (complete) {
            if (scoring.vm.arrow_buffer.length) {
                return;
            } else {
                return scoring.input.view_accept();
            }
        }

        var target = scoring.vm.round.targetFromArrowIndex(scoring.vm.arrow_index);
        var scoring_zones = target.scoring_zones();

        if (!scoring_zone_data[scoring_zones]) {
            throw "Unsupported scoring_zone: " + scoring_zones;
        }


        var buttons = scoring_zone_data[scoring_zones].map(function (score) {
            return m("button", {onclick: score_click_factory(score)}, score);
        });

        return m("div", {'class': 'buttons'}, buttons);
    };
    scoring.input.view_accept = function () {
        return m("div", {'class': 'accept'}, [
            m('button', {onclick: complete_click}, 'Sign & Complete')
        ]);
    }
    scoring.input.view_buffer = function () {
        var buffer = scoring.vm.arrow_buffer.map(function (score) {
            return m("div", score);
        });

        var undo = m("button", {onclick: undo_click}, "Undo");
        var save = m("button", {onclick: save_click}, "Save");

        return m("div", {'class': 'buffer'}, buffer.concat([undo, save]));
    };

})(window['orbital']);