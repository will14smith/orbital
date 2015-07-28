var sql = require('./sql');

module.exports = function (io, socket) {
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
};