window.orbital = window.orbital || {};
window.orbital.scoring = window.orbital.scoring || {};

(function (scoring) {
    'use strict';

    var zoneData = {
        'metric': ['X', 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 'M']
    };

    scoring.input = {};

    function setupKeyboard() {
        var addArrowToBuffer = function (score) {
            scoring.vm.addToBuffer(score);
        };

        window.addEventListener('keydown', function (e) {
            m.startComputation();

            if (e.keyCode >= 49 && e.keyCode <= 57) {
                addArrowToBuffer(e.keyCode - 48);
            } else if (e.keyCode == 48) {
                addArrowToBuffer(10);
            } else if (e.keyCode >= 97 && e.keyCode <= 105) {
                addArrowToBuffer(e.keyCode - 96);
            } else if (e.keyCode == 96) {
                addArrowToBuffer(10);
            } else if (e.keyCode == 77) {
                addArrowToBuffer('M');
            } else if (e.keyCode == 88) {
                addArrowToBuffer('X');
            } else if (e.keyCode == 8) {
                if (scoring.vm.arrowBuffer.length) {
                    e.preventDefault();

                    scoring.vm.removeFromBuffer();
                }
            } else if (e.keyCode == 13) {
                if (scoring.vm.arrowBuffer.length) {
                    scoring.vm.submitBuffer();
                }
            } else {
                console.log(e.keyCode);
            }

            m.endComputation();
        });
    }

    scoring.input.init = function() {
        if(scoring.vm.input) {
            setupKeyboard();
        }
    }

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