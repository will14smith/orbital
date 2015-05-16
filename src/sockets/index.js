var sql = require('./sql');
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

    // client apis
    socket.on('sub_score', function (id) {
        id = +id;
        socket.join('score-' + id);

        sql.loadArrows(id).then(function (data) {
            socket.emit('arrows', {
                'score': id,
                'arrows': data
            });
        });
    });

    // server apis
    socket.on('arrow_added', function (arrow) {
        io.to('score-' + arrow.score_id)
            .emit('arrow', {
                'score': arrow.score_id,
                'arrow': arrow
            });
    });

    // misc handlers
    socket.on('disconnect', function () {
        console.log('disconnect ' + socket.id);
    });
    socket.on('error', function () {
        console.error('[SOCKET.IO]', arguments);
    });
});
