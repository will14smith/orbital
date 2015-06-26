window.orbital = window.orbital || {};
window.orbital.scoring = window.orbital.scoring || {};
window.orbital.competition = window.orbital.competition || {};

(function (scoring, competition) {
    'use strict';

    function setupTargetBuffers(targets) {
        var buffers = [];

        for (var bossNumber in targets) {
            if (!targets.hasOwnProperty(bossNumber)) {
                continue;
            }

            buffers[bossNumber] = [];

            var bossTargets = targets[bossNumber];
            for (var targetNumber in bossTargets) {
                if (!bossTargets.hasOwnProperty(targetNumber)) {
                    continue;
                }

                var roundId = bossTargets[targetNumber];
                if (roundId === null) {
                    buffers[bossNumber][targetNumber] = null;
                } else {
                    buffers[bossNumber][targetNumber] = [];
                }

            }
        }

        return buffers;
    }

    function selectBossFactory(controller, bossNumber, targetNumber) {
        return function () {
            if (bossNumber === controller.selectedBoss && targetNumber === controller.selectedTarget) {
                controller.selectedBoss = null;
                controller.selectedTarget = null;
            } else {
                controller.selectedBoss = bossNumber;
                controller.selectedTarget = targetNumber;
            }
        }
    }

    // rounds = [{ id: round }]
    // targets = [{ boss: [{ target: roundId }] }]
    competition.controller = function (options) {
        this.rounds = options.rounds;
        this.targets = options.targets;
        this.bufferSize = options.bufferSize || 12;

        this.targetBuffers = setupTargetBuffers(this.targets);

        this.submitBuffer = function (buffer) {
            //TODO push to socketio
            for (var i in buffer) {
                this.targetBuffers[this.selectedBoss][this.selectedTarget].push({
                    value: buffer[i]
                });
            }

            buffer.splice(0, buffer.length);
        };

        this.selectedBoss = null;
        this.selectedTarget = null;

        this.buffer = [];
        this.input = new scoring.input({
            buffer: this.buffer,
            submitBuffer: this.submitBuffer.bind(this),
            keyboard: true
        });

        //TODO bind to socketio
    };
    competition.view = function (controller) {
        var children = [];

        for (var bossNumber in controller.targets) {
            if (!controller.targets.hasOwnProperty(bossNumber)) {
                continue;
            }

            var boss = controller.targets[bossNumber];
            children.push(competition.viewBoss(controller, boss, +bossNumber));
        }

        return m('div', {'class': 'scoresheet'}, children);
    };
    competition.viewBoss = function (controller, boss, bossNumber) {
        var children = [];

        for(var targetNumber in boss) {
            if (!boss.hasOwnProperty(targetNumber)) {
                continue;
            }

            var roundId = boss[targetNumber];
            var round = controller.rounds[roundId];

            children.push(competition.viewTarget(controller, bossNumber, +targetNumber, round));
        }

        if (bossNumber === controller.selectedBoss) {
            children.push(scoring.input.view(controller.input));
        }

        return m('div', {'class': 'target'}, children);
    };
    competition.viewTarget = function (controller, bossNumber, targetNumber, round) {
        var targetLetter = String.fromCharCode('A'.charCodeAt(0) + targetNumber - 1);
        var targetBuffer = controller.targetBuffers[bossNumber][targetNumber];
        if (targetBuffer === null) {
            return null;
        }

        var stats = {
            'hits': 0,
            'golds': 0,
            'total': 0
        };

        var arrows = [];
        for (var i = 0; i < controller.bufferSize; i++) {
            arrows.push(scoring.viewArrow(targetBuffer[i], 'metric', stats));
        }

        var isActive = bossNumber === controller.selectedBoss && targetNumber === controller.selectedTarget;
        var activeClass = isActive ? ' active' : '';

        var name = m("div", {class: 'endTotal' + activeClass}, bossNumber + targetLetter);
        var buffer = m('div', {'class': 'ends'}, m("div", {'class': 'end' + activeClass}, arrows));
        var total = m("div", {'class': 'endTotal' + activeClass}, stats.total);

        return m('div', {
            'class': 'scores',
            'onclick': selectBossFactory(controller, bossNumber, targetNumber)
        }, [name, buffer, total]);
    }
})(window.orbital.scoring, window.orbital.competition);