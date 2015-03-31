'use strict';

window['orbital'] = window['orbital'] || {};

(function (global) {
    var scoring = global['scoring'] = global['scoring'] || {};

    scoring.zones = {
        'metric': {'X': 10, 10: 10, 9: 9, 8: 8, 7: 7, 6: 6, 5: 5, 4: 4, 3: 3, 2: 2, 1: 1, 'M': 0},

        //This logic should be inline with AppBundle\Services\Scoring\ZoneManager

        get_value: function (zone, score) {
            var zone_data = scoring.zones[zone];
            if(!zone_data) {
                throw "Unsupported Zones '" + zone + "'";
            }

            return zone_data[score];
        },
        is_gold: function(zone, score) {
            //TODO is this correct for compound?

            return scoring.zones.get_value(zone, score) >= 9;
        },
        is_hit: function(zone, score) {
            return scoring.zones.get_value(zone, score) > 0;
        }
    };

})(window['orbital']);