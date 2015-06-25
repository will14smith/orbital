window.orbital = window.orbital || {};
window.orbital.scoring = window.orbital.scoring || {};

(function (scoring) {
    'use strict';

    scoring.view = function () {
        var targets = scoring.vm.round.targets;
        var children = [];

        var arrowOffset = 0;
        targets.forEach(function (element) {
            var target = scoring.viewTarget(element, arrowOffset);
            children.push(target.view);

            arrowOffset = target.arrowEndOffset;
        });

        var currentArrowCount = scoring.vm.arrows.length;
        if(scoring.vm.input && currentArrowCount >= arrowOffset) {
            children.push(scoring.input.view(scoring.vm.inputController));
        }

        return m("div", {'class': 'scoresheet'}, children);
    };

    // render score
    scoring.viewTarget = function (target, arrowStartOffset) {
        var headerText = target.distance().value + target.distance().unit + ' - ' +
            target.target().value + target.target().unit;

        var header = m("div", {'class': 'header'}, headerText);
        var ends = [];
        var endTotals = [];

        var arrowSubOffset = 0;
        var arrowTotal = target.arrowCount();

        var stats = {
            'hits': 0,
            'golds': 0,
            'total': 0
        };

        while (arrowSubOffset < arrowTotal) {
            var end = scoring.viewEnd(target, arrowStartOffset + arrowSubOffset);

            ends.push(end.endView);
            endTotals.push(end.totalView);

            stats.hits += end.stats.hits;
            stats.golds += end.stats.golds;
            stats.total += end.stats.total;

            arrowSubOffset += target.endSize();
        }

        var footer = m("div", {'class': 'footer'}, [
            m("div", [m("strong", "Hits"), m("span", stats.hits)]),
            m("div", [m("strong", "Golds"), m("span", stats.golds)]),
            m("div", [m("strong", "Total"), m("span", stats.total)])
        ]);

        var children = [
            header,
            m("div", {'class': 'scores'}, [
                m("div", {'class': 'ends'}, ends),
                m("div", {'class': 'endTotals'}, endTotals)
            ]),
            footer
        ];

        var currentArrowCount = scoring.vm.arrows.length;
        if(scoring.vm.input && arrowStartOffset <= currentArrowCount && currentArrowCount < arrowStartOffset + arrowSubOffset) {
            children.push(scoring.input.view(scoring.vm.inputController));
        }

        return {
            'view': m("div", {class: 'target'}, children),
            'arrowEndOffset': arrowStartOffset + arrowSubOffset
        };
    };

    scoring.viewEnd = function (target, arrowStartOffset) {
        var endSize = target.endSize();

        var arrows = [];
        var stats = {
            'hits': 0,
            'golds': 0,
            'total': 0
        };

        for (var arrowSubOffset = 0; arrowSubOffset < endSize; arrowSubOffset++) {
            var arrow = scoring.viewArrow(target, arrowStartOffset + arrowSubOffset, stats);

            arrows.push(arrow);
        }

        return {
            'endView': m("div", {'class': 'end'}, arrows),
            'totalView': m("div", {'class': 'endTotal'}, stats.total),
            'stats': stats
        };
    };

    scoring.viewArrow = function (target, arrowIndex, stats) {
        var arrow = scoring.vm.getArrow(arrowIndex);
        var zones = target.scoringZones();

        var text, cls;
        if (arrow) {
            var value = arrow.value;

            var score = scoring.zones.getValue(zones, value);
            var gold = scoring.zones.isGold(zones, value);
            var hit = scoring.zones.isHit(zones, value);
            cls = scoring.zones.cssClass(zones, value);

            stats.total += score;
            stats.golds += gold ? 1 : 0;
            stats.hits += hit ? 1 : 0;

            text = value;
        } else {
            text = "-";
            cls = "blank"
        }

        return m("div", {'class': 'arrow ' + cls}, text);
    };
})(window.orbital.scoring);