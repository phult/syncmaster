/**
 * Copyright (C) 2015, MEGAADS, JSC - All Rights Reserved -
 * http://www.megaads.vn
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 *
 * @author THOQ LUONG
 * Mar 25, 2015 6:17:45 PM
 */

system.controller("HeaderController", HeaderController);
/**
 * 
 * @param {type} $scope
 * @param {type} $rootScope
 * @param {type} $interval
 * @param {type} $http
 * @param {type} $document
 * @param {type} $timeout
 * @returns {undefined}
 */
function HeaderController($scope, $rootScope,$interval, $http, $document, $timeout) {
    //--------------------------------------------------------------------------
    //  Members
    var ticksCount = 0;
    var TICK_INTERVAl = 5000;
    var TOUCH_INTERVAL = 2 * 60000;//2 minute
    var lastTickTimestamp = 0;

    $scope.controllerName = "HeaderController";
    $scope.onlineStaffs = [];
    $scope.otherOnlineStaffs = [];
    $scope.otherOnlineStaffsSummary = "";
    $scope.otherOnlineStaffsDetail = "";

    $scope.isShowLoading = false;
    $scope.isShowShortcutMenu = false;
    $scope.ticketsCount = 0;
    $scope.messagesCount = 0;

    $scope.serverDate = null;
    $scope.serverTime = null;
    var deltaTimestamp = 0;//between server and browser
    var socket = null;

    $scope.notifications = [];

    $scope.liveUpdate = {
        enable: false,
        status: null //'online' or 'offline'
    };

    //--------------------------------------------------------------------------
    //  Initialize
    this.__proto__ = new BaseController($scope, $http);

    this.initialize = function ( ) {
        if (initClockValue.showDate || initClockValue.showTime) {
            initializeClock();
        }
        touch();
        tick();
        $interval(tick, TICK_INTERVAl);
        $document.on("click", function ( ) {
            $scope.$apply(function (  ) {
                if ($scope.isShowShortcutMenu) {
                    $scope.isShowShortcutMenu = false;
                }
            });
        });
    };

    function initializeClock() {
        var serverDateTime = new Date($scope.vietnameseDateToTimestamp(initClockValue.date) * 1000);
        var timeParts = initClockValue.time.split(":");
        serverDateTime.setHours(timeParts[0]);
        serverDateTime.setMinutes(timeParts[1]);
        serverDateTime.setSeconds(timeParts[2]);
        //client DateTime
        var clientDateTime = new Date();
        deltaTimestamp = serverDateTime.getTime() - clientDateTime.getTime();
        if (window.liveUpdateServerUrl != null) {
            initializeLiveUpdate();
            $scope.liveUpdate.enable = true;
        } else {
            $scope.liveUpdate.enable = false;
        }
    }

    function initializeLiveUpdate() {
        if (window.io != null) {
            socket = io(liveUpdateServerUrl);
            socket.on("connect_error", onConnectFail);
            socket.on("reconnect_failed", onConnectFail);
            socket.on("connect", onConnectionSuccessful);
            socket.on("reconnect", onConnectionSuccessful);
            socket.on("live-update", onLiveUpdateMessage);
        }else{
            $scope.liveUpdate.status = 'offline';
        }
    }

    $scope.$on("header.showLoading", function () {
        $scope.isShowLoading = true;
    });

    $scope.$on("header.hideLoading", function () {
        $scope.isShowLoading = false;
    });

    $scope.$on("header.hideShortcutMenu", function () {
        $scope.isShowShortcutMenu = false;
    });

    $scope.$on("header.touch", function () {
        touch();
    });

    /**
     * Notification data: {type,message}
     * type: create, update, delete
     */
    $scope.$on("header.notify", function (event, data) {
        data.timestamp = (new Date()).getTime();
        $scope.notifications.splice(0, 0, data);
    });

    //--------------------------------------------------------------------------
    // Method binding
    //--------------------------------------------------------------------------
    // Utils    

    /**
     * Tick every 1 minute except the first tick
     * @returns {undefined}
     */
    function tick() {
        lastTickTimestamp = (new Date()).getTime();
        ticksCount++;
        //touch
        if (ticksCount * TICK_INTERVAl % TOUCH_INTERVAL == 0) {
            touch();
        }
        //update clock
        if (initClockValue.showDate || initClockValue.showTime) {
            if (($scope.serverDate == null && $scope.serverTime == null) || (new Date()).getSeconds() < 10) {
                updateClock();
            }
        }
        //remove notify
        if ($scope.notifications.length > 0) {
            var oldestNotify = $scope.notifications[$scope.notifications.length - 1];
            if (lastTickTimestamp - oldestNotify.timestamp >= TICK_INTERVAl) {
                $scope.notifications.splice($scope.notifications.length - 1, 1);
            }
        }
    }

    function touch() {
        $http.post("/service/action/touch").success(function (data) {
            if (data.status == 'successful') {
                var onlineStaffs = data.result.onlineStaffs;
                var otherOnlineStaffs = [];
                if (onlineStaffs.length > 1) {
                    onlineStaffs.forEach(function (staff) {
                        if (staff.id != currentStaffId) {
                            otherOnlineStaffs.push(staff);
                        }
                    });
                }
                //calculate otherOnlineStaffsSummary
                var otherOnlineStaffsSummary = "";
                if (otherOnlineStaffs.length > 0) {
                    otherOnlineStaffsSummary += "và " + otherOnlineStaffs[0].username;
                    if (otherOnlineStaffs.length > 1) {
                        otherOnlineStaffsSummary += " + " + (otherOnlineStaffs.length - 1);
                    }
                }
                //calculate otherOnlineStaffsDetail
                var otherOnlineStaffsDetail = "";
                otherOnlineStaffs.forEach(function (staff) {
                    if (otherOnlineStaffsDetail.length > 0) {
                        otherOnlineStaffsDetail += ", ";
                    }
                    otherOnlineStaffsDetail += staff.username;
                });
                //assign
                $scope.otherOnlineStaffsSummary = otherOnlineStaffsSummary;
                $scope.otherOnlineStaffsDetail = otherOnlineStaffsDetail;
                $scope.otherOnlineStaffs = otherOnlineStaffs;
                //warning ticket
                $scope.ticketsCount = data.result.ticketsCount;
                $scope.messagesCount = data.result.messagesCount;
                if (ticksCount == 1 && thresholdToAlertFullMessage > 0 && $scope.messagesCount >= thresholdToAlertFullMessage) {
                    var message = "CHÚ Ý:\n"
                            + "Bạn có quá nhiều tin nhắn chưa đọc, trong đó có thể có tin nhắn rất QUAN TRỌNG với bạn!\n";
                    $timeout(function () {
                        alert(message);
                    });
                }
            }
        });
    }

    function updateClock() {
        var now = new Date((new Date()).getTime() + deltaTimestamp);
        if (initClockValue.showDate) {
            $scope.serverDate = (now.getDate() < 10 ? "0" : "") + now.getDate() + "/"
                    + (now.getMonth() < 10 ? "0" : "") + (now.getMonth() + 1);
        }
        if (initClockValue.showTime) {
            $scope.serverTime = (now.getHours() < 10 ? "0" : "") + now.getHours() + ":" +
                    (now.getMinutes() < 10 ? "0" : "") + now.getMinutes();
        }
    }

    function onConnectFail() {
        $scope.$apply(function () {
            $scope.liveUpdate.status = 'offline';
        });
        console.log("Open connection fail");
    }

    function onConnectionSuccessful() {
        $scope.$apply(function () {
            $scope.liveUpdate.status = 'online';
        });
        console.log("Open connection successfully");
    }

    function onLiveUpdateMessage(message) {
        $rootScope.$broadcast("header.live-update", message);
        console.log("Receive live update message: " + message);
    }

    this.initialize( );
}