var fs = require('fs');

var port = process.env.PORT || 3000;

var app;
var options = {};
if (process.env.SSL) {
    options = {
        key: fs.readFileSync('/etc/apache2/ssl/orbital.toxon.co.uk.key'),
        cert: fs.readFileSync('/etc/apache2/ssl/orbital.toxon.co.uk.crt'),
        ca: fs.readFileSync('/etc/apache2/ssl/sub.class1.server.ca.pem')
    };

    app = require('https').createServer(options);
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
