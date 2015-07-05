function SessionBuffer(io, id) {
    this.io = io;
    this.id = id;

    this.targets = {};
    this.selected = {};
}

SessionBuffer.prototype.add = function(boss, target, buffer) {
    if(!(boss in this.targets)) {
        this.targets[boss] = {};
    }

    if(!(target in this.targets[boss])) {
        this.targets[boss][target] = [];
    }

    this.targets[boss][target] = this.targets[boss][target].concat(buffer);

    var data = { targets: {  } };
    data.targets[boss] = { };
    data.targets[boss][target] = this.targets[boss][target];
    this.broadcast(data);
};

SessionBuffer.prototype.setSelected = function(clientId, selectedBoss, selectedTarget) {
    this.selected[clientId] = { boss: selectedBoss, target: selectedTarget };

    var data = { selected: {  } };
    data.selected[clientId] = this.selected[clientId];
    this.broadcast(data);
};

SessionBuffer.prototype.removeClient = function(clientId) {
    if(!(clientId in this.selected)) {
        return;
    }

    delete this.selected[clientId];

    var data = { selected: {  } };
    data.selected[clientId] = null;
    this.broadcast(data)
};

SessionBuffer.prototype.clear = function() {
    this.targets = {};
    this.selected = {};

    this.broadcast();
};

SessionBuffer.prototype.send = function(target, data) {
    if(data) {
        if(!('sessionId' in data)) {
            data.sessionId = this.id;
        }

        target.emit('comp_session_update', data);
    } else {
        target.emit('comp_session', {
            sessionId: this.id,
            targets: this.targets,
            selected: this.selected
        });
    }
};
SessionBuffer.prototype.broadcast = function(data) {
    var target = this.io.to('comp-session-' + this.id);
    this.send(target, data);
};

var buffers = {};
module.exports = function(io, socket) {

    // join session
    socket.on('comp_session_sub', function(sessionId) {
        socket.join('comp-session-' + sessionId);

        // setup buffer
        if(!buffers[sessionId]) {
            buffers[sessionId] = new SessionBuffer(io, sessionId);
        }

        buffers[sessionId].send(socket);
    });

    // monitor selected targets
    socket.on('comp_session_select', function(sessionId, selectedBoss, selectedTarget) {
        buffers[sessionId].setSelected(socket.id, selectedBoss, selectedTarget);
    });

    // add arrows to buffer
    socket.on('comp_session_add', function(sessionId, boss, target, buffer) {
        buffers[sessionId].add(boss, target, buffer);
    });
    // clear buffer
    socket.on('comp_session_clear', function(sessionId) {
        buffers[sessionId].clear();
    });

    socket.on('disconnect', function () {
        for(var i in buffers) {
            buffers[i].removeClient(socket.id);
        }
    });
};