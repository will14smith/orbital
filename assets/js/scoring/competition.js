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

            controller.socket.emit('comp_session_select', controller.sessionId, controller.selectedBoss, controller.selectedTarget);
        }
    }

    function processArrows(arrows) {
        return arrows.map(function (arrow) {
            return {value: arrow};
        });
    }

    // session_id = '...'
    // urls = { 'socket.io': '...' };
    // rounds = [{ id: round }]
    // targets = [{ boss: [{ target: roundId }] }]
    function Controller(options) {
        this.sessionId = options.sessionId;
        this.bufferSize = options.bufferSize || 12;

        this.rounds = options.rounds;
        this.targets = options.targets;
        this.targetBuffers = setupTargetBuffers(this.targets);

        this.selectedBoss = null;
        this.selectedTarget = null;

        this.input = new scoring.input({
            submitBuffer: this.submitBuffer.bind(this),
            keyboard: true
        });

        this.setupSocketIO(options);
    }

    competition.controller = Controller;

    Controller.prototype.setupSocketIO = function (options) {
        this.socket = io(options.urls['socket.io']);

        var _this = this;
        this.socket.on('comp_session_update', function (data) {
            _this.handleUpdate(data);
        });
        this.socket.on('comp_session', function (data) {
            _this.handle(data);
        });

        this.socket.emit('comp_session_sub', this.sessionId);
    };

    Controller.prototype.submitBuffer = function (buffer) {
        this.socket.emit('comp_session_add', this.sessionId, this.selectedBoss, this.selectedTarget, buffer);

        buffer.splice(0, buffer.length);
    };
    Controller.prototype.flushBuffers = function () {
        var buffers = this.targetBuffers;
        this.socket.emit('comp_session_clear', this.sessionId);

        //TODO ajax the buffers to save them
    };

    Controller.prototype.handleUpdate = function (data) {
        if (data.sessionId !== this.sessionId) {
            return;
        }

        // currently only processing target buffers
        if (!('targets' in data)) {
            return;
        }

        m.startComputation();

        var buffers = this.targetBuffers;

        for (var boss in data.targets) {
            if (!data.targets.hasOwnProperty(boss)) {
                continue;
            }

            var bossTargets = data.targets[boss];

            for (var target in bossTargets) {
                if (!bossTargets.hasOwnProperty(target)) {
                    continue;
                }

                buffers[boss][target] = processArrows(bossTargets[target]);
            }
        }

        m.endComputation();
    };
    Controller.prototype.handle = function (data) {
        if (data.sessionId !== this.sessionId) {
            return;
        }

        m.startComputation();

        // currently only processing target buffers
        this.targetBuffers = setupTargetBuffers(this.targets);
        this.handleUpdate(data);

        m.endComputation();
    };

    // view
    competition.view = function (controller) {
        var children = [];

        children.push(competition.viewFlush(controller));

        for (var bossNumber in controller.targets) {
            if (!controller.targets.hasOwnProperty(bossNumber)) {
                continue;
            }

            var boss = controller.targets[bossNumber];
            children.push(competition.viewBoss(controller, boss, +bossNumber));
        }

        return m('div', {'class': 'scoresheet'}, children);
    };
    competition.viewFlush = function (controller) {
        return m("div", {'class': 'flush'}, [
            m('button', {
                onclick: function () {
                    controller.flushBuffers();
                }
            }, 'Flush arrow buffer')
        ]);
    };
    competition.viewBoss = function (controller, boss, bossNumber) {
        var children = [];

        for (var targetNumber in boss) {
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