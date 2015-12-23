

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
var ROUTE_PATH = "/stream";
var request = require("request");
var restAPI = require("../modules/rest-api.js");
var sourceAnalyzer = require("../modules/source-analyzer.js");
var util = require("util");
var Streamer = function () {
};

Streamer.prototype.start = function (httpServer) {
    var self = this;
    restAPI.start(httpServer);
    restAPI.get(ROUTE_PATH, function (req, res, url) {
        var sourceId = url.replace(ROUTE_PATH + "/", "");
        sourceAnalyzer.getSourceUrl(sourceId, function (result, sourceUrl) {
            if (result) {
                self.writeStream(res, sourceUrl);
            } else {
                res.end();
            }
        });
    });
};
Streamer.prototype.writeStream = function (response, sourceURL) {
    try {
        response.writeHead(200, {
            "Content-Type": "audio/mpeg"
        });
        var readStream = request(sourceURL);
        // Opt1: Tranfer by request module
        readStream.pipe(response, function (error, response, body) {
            console.log(error);
        });
        // Opt2: Tranfer by util module
        /*
         util.pump(readStream, response); 
         */
        // Opt3: Manual Tranfer
        /*
         var readStream = fileSystem.createReadStream("E:/abc.mp3");
         readStream.on("data", function (data) {
         var flushed = response.write(data);
         // Pause the read stream when the write stream gets saturated
         if (!flushed)
         readStream.pause();
         });
         
         response.on("drain", function () {
         // Resume the read stream when the write stream gets hungry 
         readStream.resume();
         });
         
         readStream.on("end", function () {
         response.end();
         });
         */
    } catch (err) {
        console.log("On error: " + err.toString());
    }
};
module.exports = new Streamer();
