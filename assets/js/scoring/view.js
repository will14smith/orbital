'use strict';

window['orbital'] = window['orbital'] || {};

(function (global) {
    var scoring = global['scoring'] = global['scoring'] || {};

    scoring.view = function () {
        var targets = scoring.vm.round.targets;
        var children = [];

        var arrow_offset = 0;
        targets.forEach(function (element) {
            var target = scoring.view_target(element, arrow_offset);
            arrow_offset = target['arrow_end'];
            children.push(target['view']);
        });

        if (scoring.vm.input) {
            children.push(scoring.input.view());
        }

        return m("div", {'class': 'scoresheet'}, children);
    };

    // render score
    scoring.view_target = function (target, arrow_start) {
        var headerText = target.distance().value + target.distance().unit + ' - ' +
            target.target().value + target.target().unit;

        var header = m("div", {'class': 'header'}, headerText);
        var ends = [];
        var endTotals = [];

        var arrow_offset = 0;
        var arrow_total = target.arrow_count();

        var stats = {
            'hits': 0,
            'golds': 0,
            'total': 0
        };

        while (arrow_offset < arrow_total) {
            var end = scoring.view_end(target, arrow_start + arrow_offset);

            ends.push(end['end']);
            endTotals.push(end['total']);

            stats['hits'] += end['stats']['hits'];
            stats['golds'] += end['stats']['golds'];
            stats['total'] += end['stats']['total'];

            arrow_offset += target.end_size();
        }

        var footer = m("div", {'class': 'footer'}, [
            m("div", [m("strong", "Hits"), m("span", stats['hits'])]),
            m("div", [m("strong", "Golds"), m("span", stats['golds'])]),
            m("div", [m("strong", "Total"), m("span", stats['total'])])
        ]);


        return {
            'view': m("div", {class: 'target'}, [
                header,
                m("div", {'class': 'scores'}, [
                    m("div", {'class': 'ends'}, ends),
                    m("div", {'class': 'endTotals'}, endTotals)
                ]),
                footer
            ]),
            'arrow_end': arrow_start + arrow_offset
        };
    };

    scoring.view_end = function (target, arrow_start) {
        var end_size = target.end_size();

        var arrows = [];
        var stats = {
            'hits': 0,
            'golds': 0,
            'total': 0
        };

        for (var arrow_offset = 0; arrow_offset < end_size; arrow_offset++) {
            var arrow = scoring.view_arrow(target, arrow_start + arrow_offset, stats);

            arrows.push(arrow);
        }

        return {
            'end': m("div", {'class': 'end'}, arrows),
            'total': m("div", {'class': 'endTotal'}, stats['total']),
            'stats': stats
        };
    };

    scoring.view_arrow = function (target, arrow_index, stats) {
        var arrow = scoring.vm.get_arrow(arrow_index);
        var zones = target.scoring_zones();

        var text;
        if (arrow) {
            var value = arrow['value'];

            var score = scoring.zones.get_value(zones, value);
            var gold = scoring.zones.get_value(zones, value);
            var hit = scoring.zones.get_value(zones, value);

            stats['total'] += score;
            stats['golds'] += gold ? 1 : 0;
            stats['hits'] += hit ? 1 : 0;

            text = value;
        } else {
            text = "-";
        }

        return m("div", {'class': 'arrow'}, text);
    };
})(window['orbital']);