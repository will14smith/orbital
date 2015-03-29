'use strict';

window['orbital'] = window['orbital'] || {};

(function (global) {
    var scoring = {};

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

    // socket.io <- data
    // ajax -> data

    // render app
    scoring.view = function () {
        var targets = scoring.vm.round.targets;
        var rendered_targets = [];

        var arrow_offset = 0;
        targets.forEach(function (element) {
            var target = scoring.view_target(element, arrow_offset);
            arrow_offset = target['arrow_end'];
            rendered_targets.push(target['view']);
        });

        return m("div", {'class': 'scoresheet'}, rendered_targets);
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
        var arrow = null;
        var scoring_zones = target.scoring_zones();

        var text;
        if (arrow) {
            throw "TODO";
        } else {
            text = "-";
        }

        return m("div", {'class': 'arrow'}, text);
    };

    // render input

    global['scoring'] = scoring;
})(window['orbital']);