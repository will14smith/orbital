window.orbital = window.orbital || {};
window.orbital.scoring = window.orbital.scoring || {};
window.orbital.competition = window.orbital.competition || {};

(function (scoring, competition) {
    'use strict';

    // rounds = [{ id: round }]
    // targets = [{ boss: [{ target: roundId }] }]
    competition.controller = function(options) {
        this.rounds = options.rounds;
        this.targets = options.targets;

        this.submitBuffer = function(buffer) {
            console.log(buffer);
        };

        this.buffer = [];
        this.input = new scoring.input({
            buffer: this.buffer,
            submitBuffer: this.submitBuffer.bind(this),
            keyboard: true
        });
    };
    competition.view = function(controller) {
        return m('div', { 'class': 'competition-scoring' },
            controller.targets.map(function(boss, bossNumber) {
                return competition.viewBoss(controller, boss, bossNumber);
            }));
    };
    competition.viewBoss = function(controller, boss, bossNumber) {
        return m('div', { 'class': 'boss' },
            boss.map(function(roundIndex, targetNumber) {
                var round = controller.rounds[roundIndex];

                return competition.viewTarget(controller, bossNumber, targetNumber, round);
            }));
    };
    competition.viewTarget = function(controller, bossNumber, targetNumber, round) {
        var targetLetter = String.fromCharCode('a'.charCodeAt(0) + targetNumber - 1);

        return m('div', bossNumber + targetLetter);
    }
})(window.orbital.scoring, window.orbital.competition);