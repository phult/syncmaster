/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 *
 * @author Phult
 * May 13, 2015 3:11:37 PM
 */
var http = require("http");
var RestAPI = function () {
    this.postAPIs = [];
    this.getAPIs = [];
};
RestAPI.prototype.start = function (httpServer) {
    httpServer.addConnectionListener(this);
};
RestAPI.prototype.onConnection = function (req, res) {
    var self = this;
    var url = req.url;
    if (req.method == "GET") {
        //console.log("Get:" + url);
        var callback = self.getCallback("GET", url);
        if (callback != null) {
            callback(req, res, url);
        }
    } else if (req.method == "POST") {
        //console.log("Post:" + url);
        var body = "";
        req.on("data", function (data) {
            body += data;
            // Too much POST data, kill the connection!
            if (body.length > 1e6)
                req.connection.destroy();
        });
        req.on("end", function () {
            var callback = self.getCallback("POST", url);
            if (callback != null) {
                callback(req, res, body);
            }
        });
    }
//    res.end("ok");
};
RestAPI.prototype.get = function (url, callback) {
    this.getAPIs[url] = callback;
};
RestAPI.prototype.post = function (url, callback) {
    this.postAPIs[url] = callback;
};
RestAPI.prototype.getCallback = function (type, url) {
    var retval;
    if (type == "GET") {
        var urls = Object.keys(this.getAPIs);
        for (var i = 0; i < urls.length; i++) {
            if (url.indexOf(urls[i]) == 0) {
                retval = this.getAPIs[urls[i]];
                break;
            }
        }
    } else if (type == "POST") {
        retval = this.postAPIs[url];
    }
    return retval;
};
module.exports = new RestAPI();