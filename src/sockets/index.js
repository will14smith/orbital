var options = require('./config');
if(options.production) { require('newrelic'); }

var fs = require('fs');
var express = require('express');

var port = options.port || 3000;
var host = options.host;

var app = express();
var http;

if (options.ssl) {
    var httpOptions = {
        key: fs.readFileSync(options.ssl.key),
        cert: fs.readFileSync(options.ssl.cert),
        ca: fs.readFileSync(options.ssl.ca)
    };

    http = require('https').createServer(httpOptions, app);
} else {
    http = require('http').createServer(app);
}

var io = require('socket.io').listen(http);

app.get('/', function(req, res){
    res.send('<h1>orbital-node</h1><p>You have probably reached here by mistake!</p>');
});

http.listen(port, host, function () {
    var addr = http.address();

    console.info("Listening on " + addr.address + ":" + addr.port)
});

io.sockets.on('connect', function (socket) {
    console.debug('connected client ' + socket.id);

    require('./scoring')(io, socket);
    require('./competition')(io, socket);

    // misc handlers
    socket.on('disconnect', function () {
        console.debug('disconnect ' + socket.id);
    });
    socket.on('error', function () {
        console.error('[socket.io]', arguments);
    });
});
