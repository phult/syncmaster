/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 *
 * @author Phult
 * May 13, 2015 5:02:26 PM
 */
var http = require("http");

var HttpServer = function () {
    var self = this;
    this.listeners = [];
    this.server = http.createServer(function (req, res) {
        for (var i = 0; i < self.listeners.length; i++) {
            self.listeners[i].onConnection(req, res);
        }
    });
};

/**
 * 
 * @param {int} port Action listening port
 */
HttpServer.prototype.start = function (port) {
    this.server.listen(port);
};
HttpServer.prototype.getServer = function () {
    return this.server;
};
/**
 * 
 * @param {Object} listener Object contain onConnection(req, res);
 * @returns {Boolean}
 */
HttpServer.prototype.addConnectionListener = function (listener) {
    var retval = false;
    var isExisted = false;
    for (var i = 0; i < this.listeners.length; i++) {
        if (this.listeners[i] == listener) {
            isExisted = true;
            break;
        }
    }
    if (!isExisted) {
        this.listeners.push(listener);
        retval = true;
    }
    return retval;
};
/**
 * 
 * @param {Object} listener Object contain onConnection(req, res);
 * @returns {Boolean}
 */
HttpServer.prototype.removeConnectionListener = function (listener) {
    var retval = false;
    var itemIdx = -1;
    for (var i = 0; i < this.listeners.length; i++) {
        if (this.listeners[i] == listener) {
            itemIdx = i;
            break;
        }
    }
    if (itemIdx > -1) {
        this.listeners.splice(itemIdx, 1);
        retval = true;
    }
    return retval;
};
HttpServer.prototype.get = function (host, path, callbackFunc) {
    http.get({
        host: host,
        path: path
    }, function (response) {
        var body = '';
        response.on('data', function (trunk) {
            body += trunk;
        });
        response.on('end', function () {
            callbackFunc(JSON.parse(body));
        });
    });
};
module.exports = new HttpServer();
