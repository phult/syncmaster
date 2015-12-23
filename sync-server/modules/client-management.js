var ClientManagement = function () {
    this.messageListeners = [];
    this.connectionListeners = [];
    this.sessions = [];
    this.io = require("socket.io");
    this.countUsers = function () {
        var uniqueUsers = [];
        for (var i = 0; i < this.sessions.length; i++) {
            var existedUser = false;
            for (var j = 0; j < uniqueUsers.length; j++) {
                if (uniqueUsers[j].userId == this.sessions[i].userId) {
                    existedUser = true;
                    break;
                }
            }
            if (!existedUser) {
                uniqueUsers.push(this.sessions[i]);
            }
        }
        return uniqueUsers.length;
    };
    this.getClientSessions = function (id) {
        var reval = [];
        for (var i = 0; i < this.sessions.length; i++) {
            if (this.sessions[i].userId == id) {
                reval.push(this.sessions[i]);
            }
        }
        return reval;
    };
    this.getUserBySocket = function (socket) {
        var retVal;
        for (var i = 0; i < this.sessions.length; i++) {
            if (this.sessions[i].socket == socket) {
                retVal = this.sessions[i];
                break;
            }
        }
        return retVal;
    };
    /**
     * Add A messsage listener
     * @param {interface[onClientMessage]} listener
     * @returns {bool}
     */
    this.addMessageListener = function (listener) {
        this.messageListeners.push(listener);
    };
    /**
     * Add A messsage listener
     * @param {interface[onConnectionEvent]} listener
     * @returns {bool}
     */
    this.addConnectionListener = function (listener) {
        this.connectionListeners.push(listener);
    };
    this.onMessage = function (data, session) {
        if (data.type === "free") {
            this.sendMessage(data.toUserId, data.eventType, data.body, null);
            return;
        }
        for (var i = 0; i < this.messageListeners.length; i++) {
            try {
                this.messageListeners[i].onClientMessage(data, session);
            } catch (exc) {

            }
        }
    };
    this.onConnectionEvent = function (type, data) {
        for (var i = 0; i < this.connectionListeners.length; i++) {
            try {
                this.connectionListeners[i].onConnectionEvent(type, data);
            } catch (exc) {

            }
        }
    };
    this.sendMessage = function (toUserId, type, message, ignoredClientSession) {
        var users = this.getClientSessions(toUserId);
        for (var i = 0; i < users.length; i++) {
            if (ignoredClientSession != null && users[i].socket === ignoredClientSession.socket) {
                continue;
            }
            users[i].socket.emit(type, message);
        }
    };
    this.sendMessageToSession = function (session, type, message) {
        session.socket.emit(type, message);
    };
    this.broadcastMessage = function (type, message) {
        var users = this.sessions;
        for (var i = 0; i < users.length; i++) {
            users[i].socket.emit(type, message);
        }
    };
    this.start = function (httpServer) {
        var self = this;
        var socketIO = self.io(httpServer.getServer());
        socketIO.sockets.on("connection", function (socket) {
//            setTimeout(function () {
//                socket.emit('ping', {beat: 1});
//            }, 25000);
            // Initialize session
            var userId = socket.handshake.query.userId;
            var session = new Object();
            session.userId = userId;
            session.socket = socket;
            if (socket.handshake.query.extra != null) {
                var params = socket.handshake.query.extra.split(",");
                params.forEach(function (param) {
                    session[param] = socket.handshake.query[param];
                });
            }
            self.sessions.push(session);
            // Fire connection event
            console.log("count user: " + self.countUsers());
            self.onConnectionEvent("connection", session);
            // Receive a message from the client
            socket.on("message", function (data) {
                self.onMessage(data, session);
            });
            socket.on("disconnect", function () {
                // Remove from sessions
                for (var i = 0; i < self.sessions.length; i++) {
                    if (self.sessions[i].socket == socket) {
                        self.sessions.splice(i, 1);
                        break;
                    }
                }
                self.onConnectionEvent("disconnect", session);
            });
        });
    };
};
module.exports = new ClientManagement();