var path = require('path');
var express = require('express');
var logger = require('morgan');
var sql = require('./sql');

var app = express();

app.set('port', process.env.PORT || 3000);
app.use(logger('dev'));
console.log('Server has started on port: ' + app.get('port'));

var io = require('socket.io').listen(app.listen(app.get('port')));

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
    //TODO arrow_updated, arrow_removed

    // misc handlers
    socket.on('disconnect', function () {
        console.log('disconnect ' + socket.id);
    });
    socket.on('error', function () {
        console.error('[SOCKET.IO]', arguments);
    });
});
