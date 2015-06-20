window.orbital = window.orbital || {};
window.orbital.scoring = window.orbital.scoring || {};

(function (scoring) {
    'use strict';

    var zoneData = {
        'metric': ['X', 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 'M']
    };

    scoring.input = {};

    var scoreClickFactory = function (score) {
        return function () {
            scoring.vm.addToBuffer(score);
        };
    };
    var undoClick = function () {
        scoring.vm.removeFromBuffer();
    };
    var saveClick = function () {
        scoring.vm.submitBuffer();
    };
    var completeClick = function () {
        scoring.vm.complete();
    };

    scoring.input.view = function () {
        var children = [];

        if (scoring.vm.arrowBuffer.length > 0) {
            children.push(scoring.input.viewBuffer());
        }
        children.push(scoring.input.viewButtons());

        return m("div", {'class': 'input'}, children);
    };
    scoring.input.viewButtons = function () {
        var complete = scoring.vm.arrowIndex >= scoring.vm.round.totalArrows();

        if (complete) {
            if (scoring.vm.arrowBuffer.length) {
                return;
            } else {
                return scoring.input.viewAccept();
            }
        }

        var target = scoring.vm.round.targetFromArrowIndex(scoring.vm.arrowIndex);
        var scoringZones = target.scoringZones();

        if (!zoneData[scoringZones]) {
            throw "Unsupported scoring_zone: " + scoringZones;
        }

        var buttons = zoneData[scoringZones].map(function (score) {
            return m("button", {onclick: scoreClickFactory(score)}, score);
        });

        return m("div", {'class': 'buttons btn-group'}, buttons);
    };
    scoring.input.viewAccept = function () {
        return m("div", {'class': 'accept'}, [
            m('button', {onclick: completeClick}, 'Sign & Complete')
        ]);
    };
    scoring.input.viewBuffer = function () {
        var buffer = scoring.vm.arrowBuffer.map(function (score) {
            return m("div", score);
        });

        var undo = m("button", {onclick: undoClick}, "Undo");
        var save = m("button", {onclick: saveClick}, "Save");

        return m("div", {'class': 'buffer'}, buffer.concat([
            m('div', { 'class': 'btn-group' }, [undo, save])])
        );
    };

})(window.orbital.scoring);