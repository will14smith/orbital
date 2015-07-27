var options = require('./config');
if(options.production) { require('newrelic'); }

var fs = require('fs');

var port = options.port || 3000;

var app;
if (options.ssl) {
    var httpOptions = {
        key: fs.readFileSync(options.ssl.key),
        cert: fs.readFileSync(options.ssl.cert),
        ca: fs.readFileSync(options.ssl.ca)
    };

    app = require('https').createServer(httpOptions);
} else {
    app = require('http').createServer();
}

var io = require('socket.io').listen(app);

app.listen(port, function () {
    console.log("Listening on port", port)
});

io.sockets.on('connect', function (socket) {
    console.log('connected client ' + socket.id);

    require('./scoring')(io, socket);
    require('./competition')(io, socket);

    // misc handlers
    socket.on('disconnect', function () {
        console.log('disconnect ' + socket.id);
    });
    socket.on('error', function () {
        console.error('[SOCKET.IO]', arguments);
    });
});
