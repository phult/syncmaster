/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 *
 * @author PhuLuong
 * SEP 26, 2015 5:14:40 PM
 */
var play = angular.module("syncmaster", ["ngSanitize"]);
play.controller("SyncMasterController", SyncMasterController);
/**
 * 
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 */
function SyncMasterController($scope, $http, $rootScope, $document) {
    //--------------------------------------------------------------------------
    //  Members
    $scope.controllerName = "SyncMasterController";
    $scope.user = user;
    $scope.contacts = [];
    $scope.messages = [];
    $scope.smsForm = {
        errors: []
    };
    $scope.contactSugestions = [];
    $scope.sms = null;
    var self = this;
    var socket = null;
    this.__proto__ = new BaseController($scope, $http, $rootScope);
    //--------------------------------------------------------------------------
    //  Initialize    
    this.initialize = function () {
        var self = this;
        this.connect();
        this.findMessages(null, function (messages) {
            $scope.messages = messages;
        });
        this.findContacts(null, function (contacts) {
            $scope.contacts = contacts;
        });
    };
    //--------------------------------------------------------------------------
    // Method binding
    $scope.getContactDispByMessage = function (message) {
        var retval;
        var contact = $scope.getByField($scope.contacts, "id", message.contact_id);
        if (contact == null) {
            retval = $scope.formatPhone(message.phone);
        } else {
            retval = contact.name;
        }
        return retval;
    };
    $scope.getMessageIcon = function (message) {
        var retval;
        switch (message.type) {
            case "in_sms":
            {
                retval = "sms_in";
                break;
            }
            case "out_sms":
            {
                retval = "sms";
                break;
            }
            case "in_call":
            {
                retval = "call";
                break;
            }
            case "out_call":
            {
                retval = "call";
                break;
            }
        }
        return retval;
    };
    $scope.showSMSForm = function (message, contact) {
        self.resetSMSForm();
        if (message != null) {
            $scope.sms = message;
            $scope.sms.contact = $scope.getByField($scope.contacts, "id", message.contact_id);
        } else {
            $scope.sms = {
                type: "out_sms"
            };
        }
        if (contact != null) {
            $scope.sms.contact = contact;
            $scope.sms.phone = contact.phone;
        }
        jQuery('#AddListItem').fadeIn("slow");
        jQuery('#backgroundummy').fadeIn("slow");
    };
    $scope.sendSMS = function () {
        if (self.validateSMS()) {
            self.send("message", self.buildSMSData());
            self.hidePopup();
        }
    };
    $scope.suggestContacts = function (keyword) {
        var retval = [];
        keyword = $scope.toFriendlyString(keyword);
        $scope.contacts.forEach(function (item) {
            if ($scope.toFriendlyString(item.name).indexOf(keyword) > -1
                    || $scope.toFriendlyString(item.phone).indexOf(keyword) > -1
                    || $scope.toFriendlyString(item.email).indexOf(keyword) > -1) {
                retval.push(item);
            }
        });
        return retval;
    };
    $scope.selectContactSuggestion = function (contact) {
        $scope.sms.contact = contact;
        $scope.sms.phone = contact.phone;
        $scope.contactSugestions = [];
    };
    //--------------------------------------------------------------------------
    // Utils
    this.connect = function () {
        var self = this;
        socket = io.connect(serverURL, {query: 'userId=' + $scope.user.id + "&extra=type&type=web-app"});
        socket.on("connect_error", function (error) {
            self.onConnectionEvent("connect_error");
        });
        socket.on("reconnect_failed", function () {
            self.onConnectionEvent("reconnect_failed");
        });
        socket.on("connect", function () {
            self.onConnectionEvent("connect");
        });
        socket.on("reconnect", function () {
            self.onConnectionEvent("reconnect");
        });
        socket.on("message", function (message) {
            $scope.onMessage(message);
        });
    };
    this.onConnectionEvent = function (type) {
        if (type == "connect_error") {
//            jQuery('#processing-popup').fadeIn("slow");
//            jQuery('#backgroundummy').fadeIn("slow");
        } else if (type == "connect") {
            jQuery('#processing-popup').fadeOut("fast");
            jQuery('#backgroundummy').fadeOut("fast");
        }
    };
    $scope.onMessage = function (message) {
        console.log(message);
        if (message.contact_id == null || message.contact_id <= 0) {
            var contact = $scope.getByField($scope.contacts, "phone", message.phone);
            if (contact != null) {
                message.contact_id = contact.id;
            }
        }
        // push to list
        $scope.$apply(function () {
            $scope.messages.unshift(message);
        });
        // notify
        if (message.type.indexOf("in") > -1) {
            var notificationTitle = "";
            var notificationMessage = $scope.getContactDispByMessage(message);
            var notificationIcon = null;
            if (message.type.indexOf("call") > -1) {
                notificationTitle = "Incoming Call";
                notificationIcon = "/syncmaster/images/call.png";
            } else if (message.type.indexOf("sms") > -1) {
                notificationTitle = "Incoming SMS";
                notificationIcon = "/syncmaster/images/sms.png";
            }
            self.notify(notificationTitle, notificationMessage, notificationIcon);
        }
    };
    this.buildSMSData = function () {
        var retval = $scope.sms;
        if (retval.contact != null) {
            retval.contact_id = retval.contact.id;
        }
        retval.type = "out_sms";
        return retval;
    };
    this.findMessages = function (filter, callbackFn) {
        $http.post("/find-messages", filter).success(function (data) {
            if (data.status == "successful") {
                callbackFn(data.result);
            }
        });
    };
    this.findContacts = function (keyword, callbackFn) {
        $http.post("/find-contacts/" + keyword, null).success(function (data) {
            if (data.status == "successful") {
                callbackFn(data.result);
            }
        });
    };
    this.send = function (channel, data) {
        socket.emit(channel, data);
    };
    this.resetSMSForm = function () {
        $scope.smsForm.errors = [];
        $scope.contactSugestions = [];
        $scope.sms = null;
    };
    this.hidePopup = function () {
        jQuery('.popup').fadeOut("fast");
        jQuery('#backgroundummy').fadeOut("fast");
    };
    this.notify = function (title, message, icon) {
        if (!Notification) {
            alert('Desktop notifications not available in your browser. Try Chromium.');
            return;
        }
        if (Notification.permission !== "granted")
            Notification.requestPermission();
        else {
            if (icon == null) {
                icon = "/syncmaster/images/sms.png";
            }
            var notification = new Notification(title, {
                icon: icon,
                body: message
            });

            notification.onclick = function () {
                window.focus();
            };
        }

    };
    this.validateSMS = function () {
        var errors = [];
        if ($scope.sms.phone == null || $scope.sms.phone == "") {
            errors.push("A phone number or contact is required!");
        }
        if ($scope.sms.data == null || $scope.sms.data == "") {
            errors.push("Cannot send a empty message!");
        }
        $scope.smsForm.errors = errors;
        return errors.length > 0 ? false : true;
    };
    this.initialize();
}


