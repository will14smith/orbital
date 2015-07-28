'use strict';

var mysql = require('mysql');
var yaml = require('js-yaml');
var fs = require('fs');
var Promise = require('promise');

var configPath = '../../app/config/parameters.yml';
var config = yaml.safeLoad(fs.readFileSync(configPath)).parameters;

if (config.database_driver != 'pdo_mysql') {
    throw "Unsupported DB config";
}

var connection = mysql.createPool({
    connectionLimit: 5,

    host: config.database_host,
    port: config.database_port,
    database: config.database_name,
    user: config.database_user,
    password: config.database_password
});

connection.on('error', function (err) {
    console.error('[MYSQL]', err);
});

function convertArrow(arrow) {
    // mirror format in AppBundle\Services\ScoringListener

    return {
        'id': arrow['id'],
        'score_id': arrow['score_id'],
        'number': arrow['number'],
        'value': arrow['value']
    };
}

function loadArrows(score_id) {
    return new Promise(function (resolve, reject) {
        connection.query('SELECT * FROM score_arrow WHERE score_id = ?', [score_id], function (err, results) {
            if (err) return reject(err);

            resolve(results.map(convertArrow));
        });
    });
}

function loadArrow(score_id, arrow_number) {
    return new Promise(function (resolve, reject) {
        connection.query('SELECT * FROM score_arrow WHERE score_id = ? AND number = ?', [score_id, arrow_number], function (err, results) {
            if (err) return reject(err);
            if (results.length != 1)  return reject("Expected 1 arrow, actually found " + results.length);

            resolve(results.map(convertArrow)[0]);
        });
    });
}

module.exports = {
    'loadArrows': loadArrows,
    'loadArrow': loadArrow
};
