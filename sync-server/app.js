var httpServer = require('./modules/http-server');
var clientManagement = require('./modules/client-management');
var restAPI = require('./modules/rest-api');
var utils = require('./modules/utils');
var request = require('request');
var config = {
    port: 2307,
    restServiceURL: "http://launcher.megaads.vn"
};

var SyncServer = function () {
    this.start = function (config) {
        restAPI.start(httpServer);
        clientManagement.addConnectionListener(this);
        clientManagement.addMessageListener(this);
        clientManagement.start(httpServer);
        httpServer.start(config.port);
        utils.log("=============================================================");
        utils.log("Server started!");
        utils.log("-------------------------------------------------------------");
        utils.log("- time: " + utils.now());
        utils.log("- port: " + config.port);
        utils.log("=============================================================");
    };
    this.onConnectionEvent = function (type, session) {
        var self = this;
        utils.log("on connection event: " + type + ", from client: " + session.userId, {timeDisp: true});
        if (type === "connection") {
        }
    };
    this.onClientMessage = function (message, session) {
        var self = this;
        utils.log("on client message: " + message.type + ", from client: " + session.userId, {timeDisp: true});
        message.create_time = utils.now();
        if (message.type === "out_sms") {
            // save to database
            request(config.restServiceURL + "/service/syncmaster/message/create?" + utils.parseObjectToRequestURL(message), function (error, response, body) {                
                // send to just one device-app
                clientManagement.getClientSessions(session.userId).forEach(function (item) {
                    if (item.type == "device-app") {
                        clientManagement.sendMessageToSession(item, "message", message);
                        return;
                    }
                });
                // broadcast to all web-apps
                clientManagement.getClientSessions(session.userId).forEach(function (item) {
                    if (item.type == "web-app") {
                        clientManagement.sendMessageToSession(item, "message", message);
                    }
                });
            });
        } else if (message.type === "in_sms" || message.type === "in_call") {
            // broadcast to all web-apps
            clientManagement.getClientSessions(session.userId).forEach(function (item) {
                if (item.type == "web-app") {
                    clientManagement.sendMessageToSession(item, "message", message);
                }
            });
            // save to databse
            request(config.restServiceURL + "/service/syncmaster/message/create?" + utils.parseObjectToRequestURL(message), function (error, response, body) {
            });
        }
    };
};
(new SyncServer()).start(config);


process.on('uncaughtException', function (err) {
    console.error('uncaughtException: ' + err.message);
    console.error(err.stack);
});
