window.orbital = window.orbital || {};
window.orbital.scoring = window.orbital.scoring || {};

(function (scoring) {
    'use strict';

    scoring.zones = {
        'metric': {'X': 10, 10: 10, 9: 9, 8: 8, 7: 7, 6: 6, 5: 5, 4: 4, 3: 3, 2: 2, 1: 1, 'M': 0},

        //This logic should be inline with AppBundle\Services\Scoring\ZoneManager

        getValue: function (zone, score) {
            var zoneData = scoring.zones[zone];
            if (!zoneData) {
                throw "Unsupported Zones '" + zone + "'";
            }

            return zoneData[score];
        },
        isGold: function (zone, score) {
            //TODO is this correct for compound?

            return scoring.zones.getValue(zone, score) >= 9;
        },
        isHit: function (zone, score) {
            return scoring.zones.getValue(zone, score) > 0;
        },

        cssClass: function (zone, score) {
            var value = scoring.zones.getValue(zone, score);

            if (value >= 9) {
                return 'yellow';
            }
            if (value >= 7) {
                return 'red';
            }
            if (value >= 5) {
                return 'blue';
            }
            if (value >= 3) {
                return 'black';
            }

            return 'white';
        }
    };

})(window.orbital.scoring);